import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload, X, AlertOctagon } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

const impugnacaoSchema = z.object({
  eleicaoId: z.string().min(1, 'Selecione uma eleicao'),
  chapaId: z.string().min(1, 'Selecione uma chapa para impugnar'),
  motivo: z.string().min(10, 'Motivo deve ter no minimo 10 caracteres'),
  fundamentacao: z.string().min(100, 'Fundamentacao deve ter no minimo 100 caracteres'),
  impugnanteNome: z.string().min(3, 'Nome e obrigatorio'),
  impugnanteCpf: z.string().min(11, 'CPF invalido'),
  impugnanteEmail: z.string().email('Email invalido'),
  impugnanteTelefone: z.string().optional(),
})

type ImpugnacaoFormData = z.infer<typeof impugnacaoSchema>

interface Eleicao {
  id: string
  nome: string
  ano: number
}

interface Chapa {
  id: string
  nome: string
  numero: number
  eleicaoId: string
}

const motivosImpugnacao = [
  'Irregularidade na documentacao',
  'Inelegibilidade de membro',
  'Descumprimento de prazo',
  'Fraude no registro',
  'Ausencia de requisitos',
  'Irregularidade na composicao',
  'Outro motivo',
]

export function ImpugnacaoFormPage() {
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [anexos, setAnexos] = useState<File[]>([])

  const { data: eleicoes } = useQuery({
    queryKey: ['eleicoes-ativas'],
    queryFn: async () => {
      const response = await api.get<Eleicao[]>('/eleicao/ativas')
      return response.data
    },
  })

  // Mock chapas - em producao viria da API
  const [chapas] = useState<Chapa[]>([
    { id: '1', nome: 'Chapa Renovacao', numero: 1, eleicaoId: '1' },
    { id: '2', nome: 'Chapa Unidade', numero: 2, eleicaoId: '1' },
    { id: '3', nome: 'Chapa Progresso', numero: 3, eleicaoId: '1' },
  ])

  const {
    register,
    handleSubmit,
    watch,
    formState: { errors },
  } = useForm<ImpugnacaoFormData>({
    resolver: zodResolver(impugnacaoSchema),
  })

  const selectedEleicaoId = watch('eleicaoId')
  const filteredChapas = chapas.filter((c) => c.eleicaoId === selectedEleicaoId)

  const createMutation = useMutation({
    mutationFn: async (data: ImpugnacaoFormData) => {
      const formData = new FormData()
      Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          formData.append(key, String(value))
        }
      })
      anexos.forEach((file) => {
        formData.append('anexos', file)
      })
      const response = await api.post('/impugnacao', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacoes'] })
      toast({
        title: 'Impugnacao registrada com sucesso!',
        description: 'A impugnacao sera analisada pela Comissao Eleitoral.',
      })
      navigate('/impugnacoes')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar impugnacao',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: ImpugnacaoFormData) => {
    createMutation.mutate(data)
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || [])
    setAnexos((prev) => [...prev, ...files])
  }

  const handleRemoveAnexo = (index: number) => {
    setAnexos((prev) => prev.filter((_, i) => i !== index))
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/impugnacoes">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Nova Impugnacao</h1>
          <p className="text-gray-600">Registre uma impugnacao de chapa</p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          {/* Selecao da Chapa */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertOctagon className="h-5 w-5 text-red-500" />
                Chapa a ser Impugnada
              </CardTitle>
              <CardDescription>Selecione a eleicao e a chapa que deseja impugnar</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="eleicaoId">Eleicao *</Label>
                  <select
                    id="eleicaoId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('eleicaoId')}
                  >
                    <option value="">Selecione uma eleicao</option>
                    {eleicoes?.map((eleicao) => (
                      <option key={eleicao.id} value={eleicao.id}>
                        {eleicao.nome} ({eleicao.ano})
                      </option>
                    ))}
                  </select>
                  {errors.eleicaoId && (
                    <p className="text-sm text-red-500">{errors.eleicaoId.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="chapaId">Chapa *</Label>
                  <select
                    id="chapaId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('chapaId')}
                    disabled={!selectedEleicaoId}
                  >
                    <option value="">Selecione uma chapa</option>
                    {filteredChapas.map((chapa) => (
                      <option key={chapa.id} value={chapa.id}>
                        {chapa.numero}. {chapa.nome}
                      </option>
                    ))}
                  </select>
                  {errors.chapaId && (
                    <p className="text-sm text-red-500">{errors.chapaId.message}</p>
                  )}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Motivo e Fundamentacao */}
          <Card>
            <CardHeader>
              <CardTitle>Motivo da Impugnacao</CardTitle>
              <CardDescription>Descreva os motivos e fundamentos da impugnacao</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="motivo">Motivo *</Label>
                <select
                  id="motivo"
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  {...register('motivo')}
                >
                  <option value="">Selecione um motivo</option>
                  {motivosImpugnacao.map((motivo) => (
                    <option key={motivo} value={motivo}>
                      {motivo}
                    </option>
                  ))}
                </select>
                {errors.motivo && <p className="text-sm text-red-500">{errors.motivo.message}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="fundamentacao">Fundamentacao *</Label>
                <textarea
                  id="fundamentacao"
                  className="flex min-h-[200px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Descreva detalhadamente os fundamentos de fato e de direito para a impugnacao. Cite os artigos do regimento eleitoral ou legislacao aplicavel que fundamentam seu pedido..."
                  {...register('fundamentacao')}
                />
                {errors.fundamentacao && (
                  <p className="text-sm text-red-500">{errors.fundamentacao.message}</p>
                )}
                <p className="text-xs text-gray-500">
                  Minimo de 100 caracteres. Seja o mais detalhado possivel.
                </p>
              </div>
            </CardContent>
          </Card>

          {/* Dados do Impugnante */}
          <Card>
            <CardHeader>
              <CardTitle>Dados do Impugnante</CardTitle>
              <CardDescription>Suas informacoes para identificacao</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="impugnanteNome">Nome Completo *</Label>
                  <Input
                    id="impugnanteNome"
                    placeholder="Seu nome completo"
                    {...register('impugnanteNome')}
                  />
                  {errors.impugnanteNome && (
                    <p className="text-sm text-red-500">{errors.impugnanteNome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="impugnanteCpf">CPF *</Label>
                  <Input
                    id="impugnanteCpf"
                    placeholder="000.000.000-00"
                    {...register('impugnanteCpf')}
                  />
                  {errors.impugnanteCpf && (
                    <p className="text-sm text-red-500">{errors.impugnanteCpf.message}</p>
                  )}
                </div>
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="impugnanteEmail">Email *</Label>
                  <Input
                    id="impugnanteEmail"
                    type="email"
                    placeholder="seu@email.com"
                    {...register('impugnanteEmail')}
                  />
                  {errors.impugnanteEmail && (
                    <p className="text-sm text-red-500">{errors.impugnanteEmail.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="impugnanteTelefone">Telefone</Label>
                  <Input
                    id="impugnanteTelefone"
                    placeholder="(00) 00000-0000"
                    {...register('impugnanteTelefone')}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Anexos */}
          <Card>
            <CardHeader>
              <CardTitle>Documentos Comprobatorios</CardTitle>
              <CardDescription>Anexe documentos que comprovem suas alegacoes</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                  <input
                    type="file"
                    id="anexos"
                    multiple
                    className="hidden"
                    onChange={handleFileChange}
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                  />
                  <label htmlFor="anexos" className="cursor-pointer">
                    <Upload className="h-8 w-8 mx-auto text-gray-400 mb-2" />
                    <p className="text-sm text-gray-500">
                      Clique para selecionar ou arraste arquivos
                    </p>
                    <p className="text-xs text-gray-400 mt-1">
                      PDF, DOC, JPG, PNG (max. 10MB cada)
                    </p>
                  </label>
                </div>

                {anexos.length > 0 && (
                  <div className="space-y-2">
                    {anexos.map((file, index) => (
                      <div
                        key={index}
                        className="flex items-center justify-between rounded-lg border p-3"
                      >
                        <span className="text-sm">{file.name}</span>
                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          onClick={() => handleRemoveAnexo(index)}
                        >
                          <X className="h-4 w-4 text-red-500" />
                        </Button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Alerta */}
          <Card className="border-yellow-200 bg-yellow-50">
            <CardContent className="pt-6">
              <div className="flex gap-3">
                <AlertOctagon className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                <div>
                  <h4 className="font-medium text-yellow-800">Importante</h4>
                  <p className="text-sm text-yellow-700 mt-1">
                    A impugnacao deve ser apresentada dentro do prazo estabelecido pelo calendario
                    eleitoral. Impugnacoes intempestivas nao serao conhecidas. Voce sera notificado
                    por email sobre o andamento do processo.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-4">
            <Link to="/impugnacoes">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={createMutation.isPending}>
              {createMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              Registrar Impugnacao
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
