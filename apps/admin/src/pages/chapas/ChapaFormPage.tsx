import { useState, useEffect, useRef } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload, X, Image } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  chapasService,
  type Chapa,
  type CreateChapaRequest,
  type UpdateChapaRequest,
} from '@/services/chapas'
import { eleicoesService } from '@/services/eleicoes'

// Validation schema
const chapaSchema = z.object({
  nome: z.string().min(3, 'Nome deve ter no mínimo 3 caracteres'),
  numero: z.number().min(1, 'Numero é obrigatório'),
  sigla: z.string().max(10, 'Sigla muito longa').optional().or(z.literal('')),
  lema: z.string().optional().or(z.literal('')),
  descricao: z.string().optional().or(z.literal('')),
  eleicaoId: z.string().min(1, 'Selecione uma eleição'),
})

type ChapaFormData = z.infer<typeof chapaSchema>

export function ChapaFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id
  const [previewImage, setPreviewImage] = useState<string | null>(null)
  const [logoFile, setLogoFile] = useState<File | null>(null)
  const fileInputRef = useRef<HTMLInputElement>(null)

  // Fetch chapa if editing
  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: () => chapasService.getById(id!),
    enabled: isEditing,
  })

  // Fetch eleicoes for dropdown
  const { data: eleicoes, isLoading: isLoadingEleicoes } = useQuery({
    queryKey: ['eleições-ativas'],
    queryFn: eleicoesService.getAtivas,
  })

  // Form setup
  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<ChapaFormData>({
    resolver: zodResolver(chapaSchema),
    defaultValues: {
      numero: 1,
      nome: '',
      sigla: '',
      lema: '',
      descricao: '',
      eleicaoId: '',
    },
  })

  // Reset form when chapa data is loaded
  useEffect(() => {
    if (chapa) {
      reset({
        nome: chapa.nome,
        numero: chapa.numero,
        sigla: chapa.sigla || '',
        lema: chapa.lema || '',
        descricao: chapa.descricao || '',
        eleicaoId: chapa.eleicaoId,
      })
      if (chapa.logoUrl) {
        setPreviewImage(chapa.logoUrl)
      }
    }
  }, [chapa, reset])

  // Create mutation
  const createMutation = useMutation({
    mutationFn: async (data: CreateChapaRequest) => {
      const createdChapa = await chapasService.create(data)
      // Upload logo if selected
      if (logoFile && createdChapa.id) {
        await chapasService.uploadLogo(createdChapa.id, logoFile)
      }
      return createdChapa
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({
        title: 'Chapa criada com sucesso!',
        description: 'A chapa foi cadastrada no sistema.',
      })
      navigate('/chapas')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao criar chapa',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Update mutation
  const updateMutation = useMutation({
    mutationFn: async (data: UpdateChapaRequest) => {
      const updatedChapa = await chapasService.update(id!, data)
      // Upload logo if changed
      if (logoFile) {
        await chapasService.uploadLogo(id!, logoFile)
      }
      return updatedChapa
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      queryClient.invalidateQueries({ queryKey: ['chapa', id] })
      toast({
        title: 'Chapa atualizada com sucesso!',
        description: 'As alterações foram salvas.',
      })
      navigate('/chapas')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao atualizar chapa',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Form submit handler
  const onSubmit = (data: ChapaFormData) => {
    if (isEditing) {
      const updateData: UpdateChapaRequest = {
        nome: data.nome,
        sigla: data.sigla || undefined,
        lema: data.lema || undefined,
        descricao: data.descricao || undefined,
      }
      updateMutation.mutate(updateData)
    } else {
      const createData: CreateChapaRequest = {
        eleicaoId: data.eleicaoId,
        numero: data.numero,
        nome: data.nome,
        sigla: data.sigla || undefined,
        lema: data.lema || undefined,
        descricao: data.descricao || undefined,
      }
      createMutation.mutate(createData)
    }
  }

  // Handle image selection
  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      // Validate file size (max 2MB)
      if (file.size > 2 * 1024 * 1024) {
        toast({
          variant: 'destructive',
          title: 'Arquivo muito grande',
          description: 'O tamanho máximo permitido e 2MB.',
        })
        return
      }

      // Validate file type
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        toast({
          variant: 'destructive',
          title: 'Formato inválido',
          description: 'Apenas arquivos JPG, PNG e WebP são permitidos.',
        })
        return
      }

      setLogoFile(file)
      const reader = new FileReader()
      reader.onloadend = () => {
        setPreviewImage(reader.result as string)
      }
      reader.readAsDataURL(file)
    }
  }

  // Remove image
  const handleRemoveImage = () => {
    setPreviewImage(null)
    setLogoFile(null)
    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  const isSubmitting = createMutation.isPending || updateMutation.isPending
  const isLoading = isEditing && (isLoadingChapa || isLoadingEleicoes)

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
        <span className="ml-2 text-gray-500">Carregando...</span>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link to="/chapas">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {isEditing ? 'Editar Chapa' : 'Nova Chapa'}
          </h1>
          <p className="text-gray-600">
            {isEditing
              ? 'Atualize os dados da chapa'
              : 'Preencha os dados para criar uma nova chapa'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          {/* Basic Info Card */}
          <Card>
            <CardHeader>
              <CardTitle>Informações Basicas</CardTitle>
              <CardDescription>Dados principais da chapa</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="nome">Nome da Chapa *</Label>
                  <Input
                    id="nome"
                    placeholder="Ex: Chapa Renovacao"
                    {...register('nome')}
                    disabled={isSubmitting}
                  />
                  {errors.nome && (
                    <p className="text-sm text-red-500">{errors.nome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="eleicaoId">Eleição *</Label>
                  <select
                    id="eleicaoId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    {...register('eleicaoId')}
                    disabled={isSubmitting || isEditing}
                  >
                    <option value="">Selecione uma eleição</option>
                    {eleicoes?.map((eleicao) => (
                      <option key={eleicao.id} value={eleicao.id}>
                        {eleicao.nome} ({eleicao.ano})
                      </option>
                    ))}
                  </select>
                  {errors.eleicaoId && (
                    <p className="text-sm text-red-500">{errors.eleicaoId.message}</p>
                  )}
                  {isEditing && (
                    <p className="text-xs text-gray-500">
                      A eleição não pode ser alterada após a criação
                    </p>
                  )}
                </div>
              </div>

              <div className="grid gap-4 sm:grid-cols-3">
                <div className="space-y-2">
                  <Label htmlFor="numero">Número *</Label>
                  <Input
                    id="numero"
                    type="number"
                    min={1}
                    max={99}
                    {...register('numero', { valueAsNumber: true })}
                    disabled={isSubmitting || isEditing}
                  />
                  {errors.numero && (
                    <p className="text-sm text-red-500">{errors.numero.message}</p>
                  )}
                  {isEditing && (
                    <p className="text-xs text-gray-500">
                      O numero não pode ser alterado
                    </p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="sigla">Sigla</Label>
                  <Input
                    id="sigla"
                    placeholder="Ex: RENOV"
                    maxLength={10}
                    {...register('sigla')}
                    disabled={isSubmitting}
                  />
                  {errors.sigla && (
                    <p className="text-sm text-red-500">{errors.sigla.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="lema">Lema</Label>
                  <Input
                    id="lema"
                    placeholder="Ex: Por uma nova gestao"
                    {...register('lema')}
                    disabled={isSubmitting}
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="descricao">Descrição</Label>
                <textarea
                  id="descricao"
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="Descricao da chapa..."
                  {...register('descricao')}
                  disabled={isSubmitting}
                />
              </div>
            </CardContent>
          </Card>

          {/* Logo Card */}
          <Card>
            <CardHeader>
              <CardTitle>Logo da Chapa</CardTitle>
              <CardDescription>Imagem de identificação visual</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex items-start gap-6">
                <div className="h-32 w-32 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 relative">
                  {previewImage ? (
                    <>
                      <img
                        src={previewImage}
                        alt="Preview"
                        className="h-full w-full object-cover"
                      />
                      <button
                        type="button"
                        onClick={handleRemoveImage}
                        className="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"
                        title="Remover imagem"
                      >
                        <X className="h-3 w-3" />
                      </button>
                    </>
                  ) : (
                    <div className="text-center">
                      <Image className="h-8 w-8 text-gray-400 mx-auto" />
                      <span className="text-xs text-gray-400 mt-1">Sem logo</span>
                    </div>
                  )}
                </div>
                <div className="space-y-2">
                  <Input
                    ref={fileInputRef}
                    id="logo"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    onChange={handleImageChange}
                    className="w-auto"
                    disabled={isSubmitting}
                  />
                  <p className="text-xs text-gray-500">
                    Formatos aceitos: JPG, PNG, WebP. Tamanho maximo: 2MB
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Actions */}
          <div className="flex justify-end gap-4">
            <Link to="/chapas">
              <Button type="button" variant="outline" disabled={isSubmitting}>
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              {isEditing ? 'Salvar Alteracoes' : 'Criar Chapa'}
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
