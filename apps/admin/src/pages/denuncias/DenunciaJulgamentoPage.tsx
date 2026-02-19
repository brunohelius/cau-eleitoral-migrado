import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import {
  ArrowLeft,
  Loader2,
  Gavel,
  CheckCircle,
  XCircle,
  Archive,
  AlertTriangle,
  Scale,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  denunciasService,
  StatusDenuncia,
  statusDenunciaLabels,
  tipoDenunciaLabels,
} from '@/services/denuncias'

const julgamentoSchema = z.object({
  decisao: z.enum(['procedente', 'improcedente', 'parcialmente_procedente', 'arquivada'], {
    required_error: 'Selecione uma decisao',
  }),
  fundamentacao: z.string().min(50, 'A fundamentacao deve ter no mínimo 50 caracteres'),
  penalidade: z.string().optional(),
  prazoRecurso: z.number().min(0).optional(),
  notificarPartes: z.boolean().default(true),
})

type JulgamentoFormData = z.infer<typeof julgamentoSchema>

const penalidades = [
  { value: 'advertencia', label: 'Advertencia' },
  { value: 'multa', label: 'Multa' },
  { value: 'suspensao_campanha', label: 'Suspensao de Campanha' },
  { value: 'cassacao_registro', label: 'Cassacao do Registro' },
  { value: 'inelegibilidade', label: 'Inelegibilidade' },
]

const decisaoToStatus: Record<string, StatusDenuncia> = {
  procedente: StatusDenuncia.PROCEDENTE,
  improcedente: StatusDenuncia.IMPROCEDENTE,
  parcialmente_procedente: StatusDenuncia.PARCIALMENTE_PROCEDENTE,
  arquivada: StatusDenuncia.ARQUIVADA,
}

export function DenunciaJulgamentoPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Fetch denuncia
  const { data: denuncia, isLoading: isLoadingDenuncia } = useQuery({
    queryKey: ['denuncia', id],
    queryFn: () => denunciasService.getById(id!),
    enabled: !!id,
  })

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm<JulgamentoFormData>({
    resolver: zodResolver(julgamentoSchema),
    defaultValues: {
      prazoRecurso: 5,
      notificarPartes: true,
    },
  })

  const decisao = watch('decisao')

  const julgamentoMutation = useMutation({
    mutationFn: async (data: JulgamentoFormData) => {
      return denunciasService.julgar(id!, {
        decisao: decisaoToStatus[data.decisao],
        fundamentacao: data.fundamentacao,
        penalidade: data.penalidade,
      })
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      queryClient.invalidateQueries({ queryKey: ['denuncia', id] })
      toast({
        title: 'Julgamento registrado com sucesso!',
        description: 'A decisao foi registrada e as partes serao notificadas.',
      })
      navigate(`/denuncias/${id}`)
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar julgamento',
        description: 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: JulgamentoFormData) => {
    julgamentoMutation.mutate(data)
  }

  if (isLoadingDenuncia) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!denuncia) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertTriangle className="h-12 w-12 text-gray-300 mb-4" />
        <p className="text-gray-500">Denuncia não encontrada.</p>
        <Link to="/denuncias">
          <Button variant="link" className="mt-2">
            Voltar para lista
          </Button>
        </Link>
      </div>
    )
  }

  const statusInfo = statusDenunciaLabels[denuncia.status] || {
    label: 'Desconhecido',
    color: 'bg-gray-100 text-gray-800',
  }
  const tipoLabel = tipoDenunciaLabels[denuncia.tipo] || 'Outro'

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to={`/denuncias/${id}`}>
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Julgar Denuncia</h1>
          <p className="text-gray-600">{denuncia.protocolo}</p>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Resumo da Denuncia */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <AlertTriangle className="h-5 w-5 text-orange-500" />
              Resumo
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div>
              <dt className="text-sm font-medium text-gray-500">Titulo</dt>
              <dd className="mt-1 text-sm text-gray-900">{denuncia.titulo}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Tipo</dt>
              <dd className="mt-1 text-sm text-gray-900">{tipoLabel}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Status Atual</dt>
              <dd className="mt-1">
                <span
                  className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
                >
                  {statusInfo.label}
                </span>
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Denunciado</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {denuncia.chapaNome || denuncia.membroNome || '-'}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Eleição</dt>
              <dd className="mt-1 text-sm text-gray-900">{denuncia.eleicaoNome || '-'}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Data da Denuncia</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(denuncia.dataDenuncia).toLocaleDateString('pt-BR')}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Descrição</dt>
              <dd className="mt-1 text-sm text-gray-900 line-clamp-4">{denuncia.descricao}</dd>
            </div>
          </CardContent>
        </Card>

        {/* Formulario de Julgamento */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Scale className="h-5 w-5" />
              Decisao
            </CardTitle>
            <CardDescription>Registre o julgamento da denuncia</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
              {/* Decisao */}
              <div className="space-y-4">
                <Label>Decisao *</Label>
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                  <label
                    className={`flex flex-col items-center gap-2 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'procedente' ? 'border-green-500 bg-green-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="procedente"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <CheckCircle
                      className={`h-8 w-8 ${
                        decisao === 'procedente' ? 'text-green-500' : 'text-gray-400'
                      }`}
                    />
                    <div className="text-center">
                      <p className="font-medium text-sm">Procedente</p>
                      <p className="text-xs text-gray-500">Aceitar a denuncia</p>
                    </div>
                  </label>
                  <label
                    className={`flex flex-col items-center gap-2 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'improcedente' ? 'border-red-500 bg-red-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="improcedente"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <XCircle
                      className={`h-8 w-8 ${
                        decisao === 'improcedente' ? 'text-red-500' : 'text-gray-400'
                      }`}
                    />
                    <div className="text-center">
                      <p className="font-medium text-sm">Improcedente</p>
                      <p className="text-xs text-gray-500">Rejeitar a denuncia</p>
                    </div>
                  </label>
                  <label
                    className={`flex flex-col items-center gap-2 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'parcialmente_procedente'
                        ? 'border-amber-500 bg-amber-50'
                        : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="parcialmente_procedente"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <Gavel
                      className={`h-8 w-8 ${
                        decisao === 'parcialmente_procedente' ? 'text-amber-500' : 'text-gray-400'
                      }`}
                    />
                    <div className="text-center">
                      <p className="font-medium text-sm">Parcial</p>
                      <p className="text-xs text-gray-500">Parcialmente procedente</p>
                    </div>
                  </label>
                  <label
                    className={`flex flex-col items-center gap-2 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'arquivada' ? 'border-gray-500 bg-gray-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="arquivada"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <Archive
                      className={`h-8 w-8 ${
                        decisao === 'arquivada' ? 'text-gray-600' : 'text-gray-400'
                      }`}
                    />
                    <div className="text-center">
                      <p className="font-medium text-sm">Arquivada</p>
                      <p className="text-xs text-gray-500">Arquivar sem merito</p>
                    </div>
                  </label>
                </div>
                {errors.decisao && (
                  <p className="text-sm text-red-500">{errors.decisao.message}</p>
                )}
              </div>

              {/* Fundamentacao */}
              <div className="space-y-2">
                <Label htmlFor="fundamentacao">Fundamentação *</Label>
                <textarea
                  id="fundamentacao"
                  className="flex min-h-[150px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Fundamente a decisao com base nos fatos, provas e legislacao aplicavel..."
                  {...register('fundamentacao')}
                />
                {errors.fundamentacao && (
                  <p className="text-sm text-red-500">{errors.fundamentacao.message}</p>
                )}
              </div>

              {/* Penalidade (apenas se procedente ou parcialmente procedente) */}
              {(decisao === 'procedente' || decisao === 'parcialmente_procedente') && (
                <div className="space-y-2">
                  <Label htmlFor="penalidade">Penalidade Aplicada</Label>
                  <select
                    id="penalidade"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('penalidade')}
                  >
                    <option value="">Selecione uma penalidade (opcional)</option>
                    {penalidades.map((p) => (
                      <option key={p.value} value={p.value}>
                        {p.label}
                      </option>
                    ))}
                  </select>
                </div>
              )}

              {/* Prazo de Recurso */}
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="prazoRecurso">Prazo para Recurso (dias)</Label>
                  <Input
                    id="prazoRecurso"
                    type="number"
                    min={0}
                    {...register('prazoRecurso', { valueAsNumber: true })}
                  />
                </div>
                <div className="flex items-center gap-2 pt-8">
                  <input
                    type="checkbox"
                    id="notificarPartes"
                    {...register('notificarPartes')}
                    className="h-4 w-4 rounded border-gray-300"
                  />
                  <Label htmlFor="notificarPartes" className="font-normal">
                    Notificar as partes por email
                  </Label>
                </div>
              </div>

              <div className="flex justify-end gap-4 pt-4 border-t">
                <Link to={`/denuncias/${id}`}>
                  <Button type="button" variant="outline">
                    Cancelar
                  </Button>
                </Link>
                <Button type="submit" disabled={julgamentoMutation.isPending}>
                  {julgamentoMutation.isPending && (
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  )}
                  <Gavel className="mr-2 h-4 w-4" />
                  Registrar Decisao
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
