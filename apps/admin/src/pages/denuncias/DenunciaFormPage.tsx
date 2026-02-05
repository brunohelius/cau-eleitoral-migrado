import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload, X, AlertTriangle } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

const denunciaSchema = z.object({
  titulo: z.string().min(10, 'Titulo deve ter no minimo 10 caracteres'),
  descricao: z.string().min(50, 'Descricao deve ter no minimo 50 caracteres'),
  tipo: z.string().min(1, 'Selecione um tipo de denuncia'),
  prioridade: z.string().min(1, 'Selecione uma prioridade'),
  eleicaoId: z.string().min(1, 'Selecione uma eleicao'),
  denunciadoNome: z.string().min(3, 'Nome do denunciado e obrigatorio'),
  denunciadoCpf: z.string().optional(),
  chapaId: z.string().optional(),
  denuncianteNome: z.string().optional(),
  denuncianteEmail: z.string().email('Email invalido').optional().or(z.literal('')),
  denuncianteTelefone: z.string().optional(),
  anonimo: z.boolean().default(false),
})

type DenunciaFormData = z.infer<typeof denunciaSchema>

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

const tiposDenuncia = [
  { value: 'propaganda_irregular', label: 'Propaganda Irregular' },
  { value: 'abuso_poder', label: 'Abuso de Poder' },
  { value: 'fraude', label: 'Fraude' },
  { value: 'coacao', label: 'Coacao de Eleitores' },
  { value: 'compra_votos', label: 'Compra de Votos' },
  { value: 'uso_recursos', label: 'Uso Indevido de Recursos' },
  { value: 'irregularidade_candidatura', label: 'Irregularidade na Candidatura' },
  { value: 'outros', label: 'Outros' },
]

const prioridades = [
  { value: 'baixa', label: 'Baixa' },
  { value: 'media', label: 'Media' },
  { value: 'alta', label: 'Alta' },
  { value: 'urgente', label: 'Urgente' },
]

export function DenunciaFormPage() {
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [anexos, setAnexos] = useState<File[]>([])
  const [isAnonimo, setIsAnonimo] = useState(false)

  const { data: eleicoes } = useQuery({
    queryKey: ['eleicoes-ativas'],
    queryFn: async () => {
      const response = await api.get<Eleicao[]>('/eleicao/ativas')
      return response.data
    },
  })

  // Mock chapas - em producao viria da API com base na eleicao selecionada
  const [chapas] = useState<Chapa[]>([
    { id: '1', nome: 'Chapa Renovacao', numero: 1, eleicaoId: '1' },
    { id: '2', nome: 'Chapa Unidade', numero: 2, eleicaoId: '1' },
    { id: '3', nome: 'Chapa Progresso', numero: 3, eleicaoId: '1' },
  ])

  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors },
  } = useForm<DenunciaFormData>({
    resolver: zodResolver(denunciaSchema),
    defaultValues: {
      prioridade: 'media',
      anonimo: false,
    },
  })

  const selectedEleicaoId = watch('eleicaoId')
  const filteredChapas = chapas.filter((c) => c.eleicaoId === selectedEleicaoId)

  const createMutation = useMutation({
    mutationFn: async (data: DenunciaFormData) => {
      const formData = new FormData()
      Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== '') {
          formData.append(key, String(value))
        }
      })
      anexos.forEach((file) => {
        formData.append('anexos', file)
      })
      const response = await api.post('/denuncia', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denuncia registrada com sucesso!',
        description: 'A denuncia foi registrada e sera analisada pela Comissao Eleitoral.',
      })
      navigate('/denuncias')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar denuncia',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: DenunciaFormData) => {
    createMutation.mutate({ ...data, anonimo: isAnonimo })
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || [])
    setAnexos((prev) => [...prev, ...files])
  }

  const handleRemoveAnexo = (index: number) => {
    setAnexos((prev) => prev.filter((_, i) => i !== index))
  }

  const handleAnonimoChange = (checked: boolean) => {
    setIsAnonimo(checked)
    if (checked) {
      setValue('denuncianteNome', '')
      setValue('denuncianteEmail', '')
      setValue('denuncianteTelefone', '')
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/denuncias">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Nova Denuncia</h1>
          <p className="text-gray-600">Registre uma nova denuncia eleitoral</p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          {/* Informacoes da Denuncia */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertTriangle className="h-5 w-5 text-orange-500" />
                Informacoes da Denuncia
              </CardTitle>
              <CardDescription>Descreva detalhadamente a irregularidade</CardDescription>
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
                  <Label htmlFor="tipo">Tipo de Denuncia *</Label>
                  <select
                    id="tipo"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('tipo')}
                  >
                    <option value="">Selecione um tipo</option>
                    {tiposDenuncia.map((tipo) => (
                      <option key={tipo.value} value={tipo.value}>
                        {tipo.label}
                      </option>
                    ))}
                  </select>
                  {errors.tipo && <p className="text-sm text-red-500">{errors.tipo.message}</p>}
                </div>
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="prioridade">Prioridade *</Label>
                  <select
                    id="prioridade"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('prioridade')}
                  >
                    {prioridades.map((p) => (
                      <option key={p.value} value={p.value}>
                        {p.label}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="chapaId">Chapa Denunciada</Label>
                  <select
                    id="chapaId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('chapaId')}
                  >
                    <option value="">Selecione uma chapa (opcional)</option>
                    {filteredChapas.map((chapa) => (
                      <option key={chapa.id} value={chapa.id}>
                        {chapa.numero}. {chapa.nome}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="titulo">Titulo *</Label>
                <Input
                  id="titulo"
                  placeholder="Resumo da denuncia"
                  {...register('titulo')}
                />
                {errors.titulo && <p className="text-sm text-red-500">{errors.titulo.message}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="descricao">Descricao Detalhada *</Label>
                <textarea
                  id="descricao"
                  className="flex min-h-[150px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Descreva detalhadamente a irregularidade, incluindo datas, locais, pessoas envolvidas e quaisquer outras informacoes relevantes..."
                  {...register('descricao')}
                />
                {errors.descricao && (
                  <p className="text-sm text-red-500">{errors.descricao.message}</p>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Denunciado */}
          <Card>
            <CardHeader>
              <CardTitle>Denunciado</CardTitle>
              <CardDescription>Informacoes sobre quem esta sendo denunciado</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="denunciadoNome">Nome do Denunciado *</Label>
                  <Input
                    id="denunciadoNome"
                    placeholder="Nome completo ou da chapa"
                    {...register('denunciadoNome')}
                  />
                  {errors.denunciadoNome && (
                    <p className="text-sm text-red-500">{errors.denunciadoNome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="denunciadoCpf">CPF do Denunciado</Label>
                  <Input
                    id="denunciadoCpf"
                    placeholder="000.000.000-00 (opcional)"
                    {...register('denunciadoCpf')}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Denunciante */}
          <Card>
            <CardHeader>
              <CardTitle>Denunciante</CardTitle>
              <CardDescription>Suas informacoes de contato (opcional)</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="anonimo"
                  checked={isAnonimo}
                  onChange={(e) => handleAnonimoChange(e.target.checked)}
                  className="h-4 w-4 rounded border-gray-300"
                />
                <Label htmlFor="anonimo" className="font-normal">
                  Denuncia anonima (seus dados nao serao registrados)
                </Label>
              </div>

              {!isAnonimo && (
                <div className="grid gap-4 sm:grid-cols-3">
                  <div className="space-y-2">
                    <Label htmlFor="denuncianteNome">Nome</Label>
                    <Input
                      id="denuncianteNome"
                      placeholder="Seu nome"
                      {...register('denuncianteNome')}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="denuncianteEmail">Email</Label>
                    <Input
                      id="denuncianteEmail"
                      type="email"
                      placeholder="seu@email.com"
                      {...register('denuncianteEmail')}
                    />
                    {errors.denuncianteEmail && (
                      <p className="text-sm text-red-500">{errors.denuncianteEmail.message}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="denuncianteTelefone">Telefone</Label>
                    <Input
                      id="denuncianteTelefone"
                      placeholder="(00) 00000-0000"
                      {...register('denuncianteTelefone')}
                    />
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Anexos */}
          <Card>
            <CardHeader>
              <CardTitle>Anexos</CardTitle>
              <CardDescription>Adicione evidencias (fotos, videos, documentos)</CardDescription>
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
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mp3"
                  />
                  <label htmlFor="anexos" className="cursor-pointer">
                    <Upload className="h-8 w-8 mx-auto text-gray-400 mb-2" />
                    <p className="text-sm text-gray-500">
                      Clique para selecionar ou arraste arquivos
                    </p>
                    <p className="text-xs text-gray-400 mt-1">
                      PDF, DOC, JPG, PNG, MP4 (max. 10MB cada)
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

          <div className="flex justify-end gap-4">
            <Link to="/denuncias">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={createMutation.isPending}>
              {createMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              Registrar Denuncia
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
