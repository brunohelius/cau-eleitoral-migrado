import { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload, X, AlertTriangle, User } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  denunciasService,
  TipoDenuncia,
  PrioridadeDenuncia,
  tipoDenunciaLabels,
  prioridadeLabels,
  type CreateDenunciaRequest,
  type UpdateDenunciaRequest,
} from '@/services/denuncias'
import { eleicoesService, type Eleicao } from '@/services/eleicoes'
import { chapasService } from '@/services/chapas'

const denunciaSchema = z.object({
  titulo: z.string().min(10, 'Titulo deve ter no mínimo 10 caracteres'),
  descricao: z.string().min(50, 'Descrição deve ter no mínimo 50 caracteres'),
  fundamentacao: z.string().optional(),
  tipo: z.string().min(1, 'Selecione um tipo de denuncia'),
  prioridade: z.string().min(1, 'Selecione uma prioridade'),
  eleicaoId: z.string().min(1, 'Selecione uma eleição'),
  chapaId: z.string().optional(),
  membroId: z.string().optional(),
  denuncianteNome: z.string().optional(),
  denuncianteEmail: z.string().email('Email inválido').optional().or(z.literal('')),
  denuncianteTelefone: z.string().optional(),
  anonima: z.boolean().default(false),
})

type DenunciaFormData = z.infer<typeof denunciaSchema>

interface Chapa {
  id: string
  nome: string
  numero: number
  eleicaoId: string
}

interface Membro {
  id: string
  nome: string
  cargo: string
  chapaId: string
}

const cargoLabel: Record<number, string> = {
  0: 'Presidente',
  1: 'Vice-Presidente',
  2: 'Conselheiro',
  3: 'Diretor',
  4: 'Coordenador',
}

// Type options from enum
const tipoOptions = Object.entries(tipoDenunciaLabels).map(([value, label]) => ({
  value,
  label,
}))

// Priority options from enum
const prioridadeOptions = Object.entries(prioridadeLabels).map(([value, { label }]) => ({
  value,
  label,
}))

export function DenunciaFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id
  const [anexos, setAnexos] = useState<File[]>([])
  const [isAnonimo, setIsAnonimo] = useState(false)

  // Fetch existing denuncia if editing
  const { data: existingDenuncia, isLoading: isLoadingDenuncia } = useQuery({
    queryKey: ['denuncia', id],
    queryFn: () => denunciasService.getById(id!),
    enabled: isEditing,
  })

  // Fetch eleicoes
  const { data: eleicoes, isLoading: isLoadingEleicoes } = useQuery({
    queryKey: ['eleições'],
    queryFn: eleicoesService.getAll,
  })

  const {
    register,
    handleSubmit,
    watch,
    setValue,
    reset,
    formState: { errors },
  } = useForm<DenunciaFormData>({
    resolver: zodResolver(denunciaSchema),
    defaultValues: {
      prioridade: String(PrioridadeDenuncia.NORMAL),
      tipo: '',
      anonima: false,
    },
  })

  // Populate form when editing
  useEffect(() => {
    if (existingDenuncia) {
      reset({
        titulo: existingDenuncia.titulo,
        descricao: existingDenuncia.descricao,
        fundamentacao: existingDenuncia.fundamentacao || '',
        tipo: String(existingDenuncia.tipo),
        prioridade: String(existingDenuncia.prioridade || PrioridadeDenuncia.NORMAL),
        eleicaoId: existingDenuncia.eleicaoId,
        chapaId: existingDenuncia.chapaId || '',
        membroId: existingDenuncia.membroId || '',
        denuncianteNome: existingDenuncia.denuncianteNome || '',
        denuncianteEmail: existingDenuncia.denuncianteEmail || '',
        denuncianteTelefone: existingDenuncia.denuncianteTelefone || '',
        anonima: existingDenuncia.anonima,
      })
      setIsAnonimo(existingDenuncia.anonima)
    }
  }, [existingDenuncia, reset])

  const selectedEleicaoId = watch('eleicaoId')
  const selectedChapaId = watch('chapaId')

  const { data: chapas = [], isLoading: isLoadingChapas } = useQuery({
    queryKey: ['chapas-denuncia', selectedEleicaoId],
    queryFn: async () => {
      const result = await chapasService.getByEleicao(selectedEleicaoId!)
      return result.map((c) => ({
        id: c.id,
        nome: c.nome,
        numero: c.numero,
        eleicaoId: c.eleicaoId,
      })) as Chapa[]
    },
    enabled: !!selectedEleicaoId,
  })

  const { data: membros = [], isLoading: isLoadingMembros } = useQuery({
    queryKey: ['membros-denuncia', selectedChapaId],
    queryFn: async () => {
      const result = await chapasService.getMembros(selectedChapaId!)
      return result.map((m) => ({
        id: m.id,
        nome: m.candidatoNome,
        cargo: cargoLabel[m.cargo] || String(m.cargo),
        chapaId: m.chapaId,
      })) as Membro[]
    },
    enabled: !!selectedChapaId,
  })

  // Reset dependent selects when eleicao changes
  useEffect(() => {
    if (!isEditing) {
      setValue('chapaId', '')
      setValue('membroId', '')
    }
  }, [selectedEleicaoId, setValue, isEditing])

  // Reset dependent selects when chapa changes
  useEffect(() => {
    if (!isEditing) {
      setValue('membroId', '')
    }
  }, [selectedChapaId, setValue, isEditing])

  const createMutation = useMutation({
    mutationFn: async (data: CreateDenunciaRequest) => {
      const denuncia = await denunciasService.create(data)
      // Upload anexos if any
      for (const file of anexos) {
        await denunciasService.uploadAnexo(denuncia.id, file)
      }
      return denuncia
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denuncia registrada com sucesso!',
        description: 'A denuncia foi registrada e será analisada pela Comissão Eleitoral.',
      })
      navigate('/denuncias')
    },
    onError: (error: Error & { response?: { data?: { message?: string } } }) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar denuncia',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const updateMutation = useMutation({
    mutationFn: async (data: UpdateDenunciaRequest) => {
      return denunciasService.update(id!, data)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      queryClient.invalidateQueries({ queryKey: ['denuncia', id] })
      toast({
        title: 'Denuncia atualizada com sucesso!',
        description: 'As alterações foram salvas.',
      })
      navigate(`/denuncias/${id}`)
    },
    onError: (error: Error & { response?: { data?: { message?: string } } }) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao atualizar denuncia',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: DenunciaFormData) => {
    if (isEditing) {
      const updateData: UpdateDenunciaRequest = {
        titulo: data.titulo,
        descricao: data.descricao,
        fundamentacao: data.fundamentacao,
        tipo: Number(data.tipo) as TipoDenuncia,
        prioridade: Number(data.prioridade) as PrioridadeDenuncia,
      }
      updateMutation.mutate(updateData)
    } else {
      const createData: CreateDenunciaRequest = {
        eleicaoId: data.eleicaoId,
        chapaId: data.chapaId || undefined,
        membroId: data.membroId || undefined,
        tipo: Number(data.tipo) as TipoDenuncia,
        titulo: data.titulo,
        descricao: data.descricao,
        fundamentacao: data.fundamentacao,
        prioridade: Number(data.prioridade) as PrioridadeDenuncia,
        anonima: isAnonimo,
        denuncianteNome: isAnonimo ? undefined : data.denuncianteNome,
        denuncianteEmail: isAnonimo ? undefined : data.denuncianteEmail,
        denuncianteTelefone: isAnonimo ? undefined : data.denuncianteTelefone,
      }
      createMutation.mutate(createData)
    }
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

  const isSubmitting = createMutation.isPending || updateMutation.isPending

  if (isEditing && isLoadingDenuncia) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to={isEditing ? `/denuncias/${id}` : '/denuncias'}>
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {isEditing ? 'Editar Denuncia' : 'Nova Denuncia'}
          </h1>
          <p className="text-gray-600">
            {isEditing
              ? `Editando ${existingDenuncia?.protocolo}`
              : 'Registre uma nova denuncia eleitoral'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          {/* Informacoes da Denuncia */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertTriangle className="h-5 w-5 text-orange-500" />
                Informações da Denuncia
              </CardTitle>
              <CardDescription>Descreva detalhadamente a irregularidade</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="eleicaoId">Eleição *</Label>
                  <select
                    id="eleicaoId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('eleicaoId')}
                    disabled={isEditing || isLoadingEleicoes}
                  >
                    <option value="">Selecione uma eleição</option>
                    {eleicoes?.map((eleicao: Eleicao) => (
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
                    {tipoOptions.map((tipo) => (
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
                    {prioridadeOptions.map((p) => (
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
                    disabled={!selectedEleicaoId || isEditing || isLoadingChapas}
                  >
                    <option value="">Selecione uma chapa (opcional)</option>
                    {chapas.map((chapa) => (
                      <option key={chapa.id} value={chapa.id}>
                        {chapa.numero}. {chapa.nome}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              {selectedChapaId && !isEditing && (
                <div className="space-y-2">
                  <Label htmlFor="membroId">Membro Especifico (opcional)</Label>
                  <select
                    id="membroId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register('membroId')}
                    disabled={isLoadingMembros}
                  >
                    <option value="">Selecione um membro (opcional)</option>
                    {membros.map((membro) => (
                      <option key={membro.id} value={membro.id}>
                        {membro.nome} - {membro.cargo}
                      </option>
                    ))}
                  </select>
                </div>
              )}

              <div className="space-y-2">
                <Label htmlFor="titulo">Titulo *</Label>
                <Input id="titulo" placeholder="Resumo da denuncia" {...register('titulo')} />
                {errors.titulo && <p className="text-sm text-red-500">{errors.titulo.message}</p>}
              </div>

              <div className="space-y-2">
                <Label htmlFor="descricao">Descrição Detalhada *</Label>
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

              <div className="space-y-2">
                <Label htmlFor="fundamentacao">Fundamentação Legal (opcional)</Label>
                <textarea
                  id="fundamentacao"
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Indique os dispositivos legais ou normativos que fundamentam a denuncia..."
                  {...register('fundamentacao')}
                />
              </div>
            </CardContent>
          </Card>

          {/* Denunciante - Only show when creating */}
          {!isEditing && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Denunciante
                </CardTitle>
                <CardDescription>Suas informações de contato (opcional)</CardDescription>
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
                    Denuncia anônima (seus dados não seráo registrados)
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
          )}

          {/* Anexos - Only show when creating */}
          {!isEditing && (
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
          )}

          <div className="flex justify-end gap-4">
            <Link to={isEditing ? `/denuncias/${id}` : '/denuncias'}>
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              {isEditing ? 'Salvar Alteracoes' : 'Registrar Denuncia'}
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
