import { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

const chapaSchema = z.object({
  nome: z.string().min(3, 'Nome deve ter no minimo 3 caracteres'),
  numero: z.number().min(1, 'Numero e obrigatorio'),
  sigla: z.string().min(1, 'Sigla e obrigatoria').max(10, 'Sigla muito longa'),
  slogan: z.string().optional(),
  descricao: z.string().optional(),
  eleicaoId: z.string().min(1, 'Selecione uma eleicao'),
  proposta: z.string().optional(),
  email: z.string().email('Email invalido').optional().or(z.literal('')),
  telefone: z.string().optional(),
})

type ChapaFormData = z.infer<typeof chapaSchema>

interface Eleicao {
  id: string
  nome: string
  ano: number
}

interface Chapa {
  id: string
  nome: string
  numero: number
  sigla: string
  slogan?: string
  descricao?: string
  eleicaoId: string
  proposta?: string
  email?: string
  telefone?: string
  fotoUrl?: string
  status: number
}

export function ChapaFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id
  const [previewImage, setPreviewImage] = useState<string | null>(null)

  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: async () => {
      const response = await api.get<Chapa>(`/chapa/${id}`)
      return response.data
    },
    enabled: isEditing,
  })

  const { data: eleicoes, isLoading: isLoadingEleicoes } = useQuery({
    queryKey: ['eleicoes-ativas'],
    queryFn: async () => {
      const response = await api.get<Eleicao[]>('/eleicao/ativas')
      return response.data
    },
  })

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<ChapaFormData>({
    resolver: zodResolver(chapaSchema),
    defaultValues: {
      numero: 1,
    },
  })

  useEffect(() => {
    if (chapa) {
      reset({
        nome: chapa.nome,
        numero: chapa.numero,
        sigla: chapa.sigla,
        slogan: chapa.slogan || '',
        descricao: chapa.descricao || '',
        eleicaoId: chapa.eleicaoId,
        proposta: chapa.proposta || '',
        email: chapa.email || '',
        telefone: chapa.telefone || '',
      })
      if (chapa.fotoUrl) {
        setPreviewImage(chapa.fotoUrl)
      }
    }
  }, [chapa, reset])

  const createMutation = useMutation({
    mutationFn: async (data: ChapaFormData) => {
      const response = await api.post<Chapa>('/chapa', data)
      return response.data
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

  const updateMutation = useMutation({
    mutationFn: async (data: Partial<ChapaFormData>) => {
      const response = await api.put<Chapa>(`/chapa/${id}`, data)
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      queryClient.invalidateQueries({ queryKey: ['chapa', id] })
      toast({
        title: 'Chapa atualizada com sucesso!',
        description: 'As alteracoes foram salvas.',
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

  const onSubmit = (data: ChapaFormData) => {
    if (isEditing) {
      updateMutation.mutate(data)
    } else {
      createMutation.mutate(data)
    }
  }

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onloadend = () => {
        setPreviewImage(reader.result as string)
      }
      reader.readAsDataURL(file)
    }
  }

  const isSubmitting = createMutation.isPending || updateMutation.isPending
  const isLoading = isEditing && (isLoadingChapa || isLoadingEleicoes)

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
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
            {isEditing ? 'Atualize os dados da chapa' : 'Preencha os dados para criar uma nova chapa'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Informacoes Basicas</CardTitle>
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
                  />
                  {errors.nome && (
                    <p className="text-sm text-red-500">{errors.nome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="eleicaoId">Eleicao *</Label>
                  <select
                    id="eleicaoId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
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
              </div>

              <div className="grid gap-4 sm:grid-cols-3">
                <div className="space-y-2">
                  <Label htmlFor="numero">Numero *</Label>
                  <Input
                    id="numero"
                    type="number"
                    min={1}
                    {...register('numero', { valueAsNumber: true })}
                  />
                  {errors.numero && (
                    <p className="text-sm text-red-500">{errors.numero.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="sigla">Sigla *</Label>
                  <Input
                    id="sigla"
                    placeholder="Ex: RENOV"
                    maxLength={10}
                    {...register('sigla')}
                  />
                  {errors.sigla && (
                    <p className="text-sm text-red-500">{errors.sigla.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="slogan">Slogan</Label>
                  <Input
                    id="slogan"
                    placeholder="Ex: Por uma nova gestao"
                    {...register('slogan')}
                  />
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="descricao">Descricao</Label>
                <textarea
                  id="descricao"
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="Descricao da chapa..."
                  {...register('descricao')}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Imagem da Chapa</CardTitle>
              <CardDescription>Foto ou logo da chapa</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex items-start gap-6">
                <div className="h-32 w-32 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                  {previewImage ? (
                    <img src={previewImage} alt="Preview" className="h-full w-full object-cover" />
                  ) : (
                    <Upload className="h-8 w-8 text-gray-400" />
                  )}
                </div>
                <div className="space-y-2">
                  <Input
                    id="foto"
                    type="file"
                    accept="image/*"
                    onChange={handleImageChange}
                    className="w-auto"
                  />
                  <p className="text-xs text-gray-500">
                    Formatos aceitos: JPG, PNG. Tamanho maximo: 2MB
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Proposta</CardTitle>
              <CardDescription>Proposta de trabalho da chapa</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                <textarea
                  id="proposta"
                  className="flex min-h-[200px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="Descreva a proposta de trabalho da chapa..."
                  {...register('proposta')}
                />
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Contato</CardTitle>
              <CardDescription>Informacoes de contato da chapa</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="email">Email</Label>
                  <Input
                    id="email"
                    type="email"
                    placeholder="contato@chapa.com"
                    {...register('email')}
                  />
                  {errors.email && (
                    <p className="text-sm text-red-500">{errors.email.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="telefone">Telefone</Label>
                  <Input
                    id="telefone"
                    placeholder="(00) 00000-0000"
                    {...register('telefone')}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-4">
            <Link to="/chapas">
              <Button type="button" variant="outline">
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
