// import { useState } from 'react'
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
  FileText,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

const julgamentoSchema = z.object({
  decisao: z.enum(['deferida', 'indeferida', 'arquivada'], {
    required_error: 'Selecione uma decisao',
  }),
  fundamentacao: z.string().min(50, 'A fundamentacao deve ter no minimo 50 caracteres'),
  penalidade: z.string().optional(),
  prazoRecurso: z.number().min(0).optional(),
  notificarPartes: z.boolean().default(true),
})

type JulgamentoFormData = z.infer<typeof julgamentoSchema>

interface Denuncia {
  id: string
  protocolo: string
  titulo: string
  descricao: string
  tipo: string
  status: string
  prioridade: string
  denuncianteNome?: string
  denunciadoNome: string
  chapaNome?: string
  eleicaoNome: string
  dataRegistro: string
}

const penalidades = [
  { value: 'advertencia', label: 'Advertencia' },
  { value: 'multa', label: 'Multa' },
  { value: 'suspensao_campanha', label: 'Suspensao de Campanha' },
  { value: 'cassacao_registro', label: 'Cassacao do Registro' },
  { value: 'inelegibilidade', label: 'Inelegibilidade' },
]

export function DenunciaJulgamentoPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Mock denuncia - em producao viria da API
  const { data: denuncia, isLoading: isLoadingDenuncia } = useQuery({
    queryKey: ['denuncia', id],
    queryFn: async () => {
      // const response = await api.get<Denuncia>(`/denuncia/${id}`)
      // return response.data
      return {
        id: '1',
        protocolo: 'DEN-2024-00001',
        titulo: 'Irregularidade na campanha eleitoral',
        descricao:
          'Foi observada distribuicao de material de campanha fora do periodo permitido pelo calendario eleitoral.',
        tipo: 'propaganda_irregular',
        status: 'em_analise',
        prioridade: 'alta',
        denuncianteNome: 'Joao Carlos Silva',
        denunciadoNome: 'Chapa Renovacao',
        chapaNome: 'Chapa Renovacao',
        eleicaoNome: 'Eleicao CAU/SP 2024',
        dataRegistro: '2024-02-16T10:30:00',
      } as Denuncia
    },
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
      const response = await api.post(`/denuncia/${id}/julgar`, data)
      return response.data
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
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar julgamento',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: JulgamentoFormData) => {
    julgamentoMutation.mutate(data)
  }

  const getDecisaoIcon = (decisaoValue: string) => {
    switch (decisaoValue) {
      case 'deferida':
        return <CheckCircle className="h-5 w-5 text-green-500" />
      case 'indeferida':
        return <XCircle className="h-5 w-5 text-red-500" />
      case 'arquivada':
        return <Archive className="h-5 w-5 text-gray-500" />
      default:
        return null
    }
  }

  // Use the function to avoid unused warning
  void getDecisaoIcon

  if (isLoadingDenuncia) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!denuncia) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Denuncia nao encontrada.</p>
      </div>
    )
  }

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
              <dt className="text-sm font-medium text-gray-500">Denunciado</dt>
              <dd className="mt-1 text-sm text-gray-900">{denuncia.denunciadoNome}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Eleicao</dt>
              <dd className="mt-1 text-sm text-gray-900">{denuncia.eleicaoNome}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Data do Registro</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(denuncia.dataRegistro).toLocaleDateString('pt-BR')}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Descricao</dt>
              <dd className="mt-1 text-sm text-gray-900">{denuncia.descricao}</dd>
            </div>
          </CardContent>
        </Card>

        {/* Formulario de Julgamento */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Gavel className="h-5 w-5" />
              Decisao
            </CardTitle>
            <CardDescription>Registre o julgamento da denuncia</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
              {/* Decisao */}
              <div className="space-y-4">
                <Label>Decisao *</Label>
                <div className="grid gap-4 sm:grid-cols-3">
                  <label
                    className={`flex items-center gap-3 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'deferida' ? 'border-green-500 bg-green-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="deferida"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <CheckCircle
                      className={`h-5 w-5 ${
                        decisao === 'deferida' ? 'text-green-500' : 'text-gray-400'
                      }`}
                    />
                    <div>
                      <p className="font-medium">Deferida</p>
                      <p className="text-xs text-gray-500">Aceitar a denuncia</p>
                    </div>
                  </label>
                  <label
                    className={`flex items-center gap-3 rounded-lg border p-4 cursor-pointer transition-colors ${
                      decisao === 'indeferida' ? 'border-red-500 bg-red-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value="indeferida"
                      {...register('decisao')}
                      className="sr-only"
                    />
                    <XCircle
                      className={`h-5 w-5 ${
                        decisao === 'indeferida' ? 'text-red-500' : 'text-gray-400'
                      }`}
                    />
                    <div>
                      <p className="font-medium">Indeferida</p>
                      <p className="text-xs text-gray-500">Rejeitar a denuncia</p>
                    </div>
                  </label>
                  <label
                    className={`flex items-center gap-3 rounded-lg border p-4 cursor-pointer transition-colors ${
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
                      className={`h-5 w-5 ${
                        decisao === 'arquivada' ? 'text-gray-500' : 'text-gray-400'
                      }`}
                    />
                    <div>
                      <p className="font-medium">Arquivada</p>
                      <p className="text-xs text-gray-500">Arquivar sem julgamento</p>
                    </div>
                  </label>
                </div>
                {errors.decisao && (
                  <p className="text-sm text-red-500">{errors.decisao.message}</p>
                )}
              </div>

              {/* Fundamentacao */}
              <div className="space-y-2">
                <Label htmlFor="fundamentacao">Fundamentacao *</Label>
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

              {/* Penalidade (apenas se deferida) */}
              {decisao === 'deferida' && (
                <div className="space-y-2">
                  <Label htmlFor="penalidade">Penalidade Aplicada</Label>
                  <select
                    id="penalidade"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('penalidade')}
                  >
                    <option value="">Selecione uma penalidade</option>
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
