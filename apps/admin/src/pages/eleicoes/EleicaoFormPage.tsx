import { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import { eleicoesService, CreateEleicaoRequest, UpdateEleicaoRequest } from '@/services/eleicoes'

const eleicaoSchema = z.object({
  nome: z.string().min(3, 'Nome deve ter no mínimo 3 caracteres'),
  descricao: z.string().optional(),
  tipo: z.number().min(0, 'Selecione um tipo'),
  ano: z.number().min(2020, 'Ano inválido').max(2100, 'Ano inválido'),
  mandato: z.number().optional(),
  dataInicio: z.string().min(1, 'Data de início é obrigatoria'),
  dataFim: z.string().min(1, 'Data de fim é obrigatoria'),
  dataVotacaoInicio: z.string().optional(),
  dataVotacaoFim: z.string().optional(),
  regionalId: z.string().optional(),
  modoVotacao: z.number().min(0, 'Selecione um modo de votação'),
  quantidadeVagas: z.number().optional(),
  quantidadeSuplentes: z.number().optional(),
})

type EleicaoFormData = z.infer<typeof eleicaoSchema>

const tiposEleicao = [
  { value: 0, label: 'Ordinaria' },
  { value: 1, label: 'Extraordinaria' },
  { value: 2, label: 'Suplementar' },
]

const modosVotacao = [
  { value: 0, label: 'Presencial' },
  { value: 1, label: 'Online' },
  { value: 2, label: 'Hibrido' },
]

export function EleicaoFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id

  const { data: eleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['eleicao', id],
    queryFn: () => eleicoesService.getById(id!),
    enabled: isEditing,
  })

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<EleicaoFormData>({
    resolver: zodResolver(eleicaoSchema),
    defaultValues: {
      tipo: 0,
      modoVotacao: 0,
      ano: new Date().getFullYear(),
    },
  })

  useEffect(() => {
    if (eleicao) {
      reset({
        nome: eleicao.nome,
        descricao: eleicao.descricao || '',
        tipo: eleicao.tipo,
        ano: eleicao.ano,
        mandato: eleicao.mandato,
        dataInicio: eleicao.dataInicio.split('T')[0],
        dataFim: eleicao.dataFim.split('T')[0],
        dataVotacaoInicio: eleicao.dataVotacaoInicio?.split('T')[0] || '',
        dataVotacaoFim: eleicao.dataVotacaoFim?.split('T')[0] || '',
        regionalId: eleicao.regionalId || '',
        modoVotacao: eleicao.modoVotacao,
        quantidadeVagas: eleicao.quantidadeVagas,
        quantidadeSuplentes: eleicao.quantidadeSuplentes,
      })
    }
  }, [eleicao, reset])

  const createMutation = useMutation({
    mutationFn: (data: CreateEleicaoRequest) => eleicoesService.create(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['eleicoes'] })
      toast({
        title: 'Eleição criada com sucesso!',
        description: 'A eleição foi cadastrada no sistema.',
      })
      navigate('/eleicoes')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao criar eleição',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const updateMutation = useMutation({
    mutationFn: (data: UpdateEleicaoRequest) => eleicoesService.update(id!, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['eleicoes'] })
      queryClient.invalidateQueries({ queryKey: ['eleicao', id] })
      toast({
        title: 'Eleição atualizada com sucesso!',
        description: 'As alterações foram salvas.',
      })
      navigate('/eleicoes')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao atualizar eleição',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: EleicaoFormData) => {
    if (isEditing) {
      updateMutation.mutate(data)
    } else {
      createMutation.mutate(data as CreateEleicaoRequest)
    }
  }

  const isSubmitting = createMutation.isPending || updateMutation.isPending

  if (isEditing && isLoadingEleicao) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/eleicoes">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {isEditing ? 'Editar Eleicao' : 'Nova Eleicao'}
          </h1>
          <p className="text-gray-600">
            {isEditing ? 'Atualize os dados da eleicao' : 'Preencha os dados para criar uma nova eleicao'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          <Card>
            <CardHeader>
              <CardTitle>Informações Basicas</CardTitle>
              <CardDescription>Dados principais da eleição</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="nome">Nome da Eleição *</Label>
                  <Input
                    id="nome"
                    placeholder="Ex: Eleicao CAU/BR 2024"
                    {...register('nome')}
                  />
                  {errors.nome && (
                    <p className="text-sm text-red-500">{errors.nome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="ano">Ano *</Label>
                  <Input
                    id="ano"
                    type="number"
                    {...register('ano', { valueAsNumber: true })}
                  />
                  {errors.ano && (
                    <p className="text-sm text-red-500">{errors.ano.message}</p>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="descricao">Descrição</Label>
                <textarea
                  id="descricao"
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  placeholder="Descricao detalhada da eleicao..."
                  {...register('descricao')}
                />
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo de Eleição *</Label>
                  <select
                    id="tipo"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    {...register('tipo', { valueAsNumber: true })}
                  >
                    {tiposEleicao.map((tipo) => (
                      <option key={tipo.value} value={tipo.value}>
                        {tipo.label}
                      </option>
                    ))}
                  </select>
                  {errors.tipo && (
                    <p className="text-sm text-red-500">{errors.tipo.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="modoVotacao">Modo de Votação *</Label>
                  <select
                    id="modoVotacao"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    {...register('modoVotacao', { valueAsNumber: true })}
                  >
                    {modosVotacao.map((modo) => (
                      <option key={modo.value} value={modo.value}>
                        {modo.label}
                      </option>
                    ))}
                  </select>
                  {errors.modoVotacao && (
                    <p className="text-sm text-red-500">{errors.modoVotacao.message}</p>
                  )}
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Datas</CardTitle>
              <CardDescription>Cronograma da eleição</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="dataInicio">Data de Início *</Label>
                  <Input
                    id="dataInicio"
                    type="date"
                    {...register('dataInicio')}
                  />
                  {errors.dataInicio && (
                    <p className="text-sm text-red-500">{errors.dataInicio.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="dataFim">Data de Fim *</Label>
                  <Input
                    id="dataFim"
                    type="date"
                    {...register('dataFim')}
                  />
                  {errors.dataFim && (
                    <p className="text-sm text-red-500">{errors.dataFim.message}</p>
                  )}
                </div>
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="dataVotacaoInicio">Início da Votação</Label>
                  <Input
                    id="dataVotacaoInicio"
                    type="date"
                    {...register('dataVotacaoInicio')}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="dataVotacaoFim">Fim da Votação</Label>
                  <Input
                    id="dataVotacaoFim"
                    type="date"
                    {...register('dataVotacaoFim')}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Configurações</CardTitle>
              <CardDescription>Parametros adicionais</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-3">
                <div className="space-y-2">
                  <Label htmlFor="mandato">Mandato (anos)</Label>
                  <Input
                    id="mandato"
                    type="number"
                    placeholder="Ex: 4"
                    {...register('mandato', { valueAsNumber: true })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="quantidadeVagas">Quantidade de Vagas</Label>
                  <Input
                    id="quantidadeVagas"
                    type="number"
                    placeholder="Ex: 10"
                    {...register('quantidadeVagas', { valueAsNumber: true })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="quantidadeSuplentes">Quantidade de Suplentes</Label>
                  <Input
                    id="quantidadeSuplentes"
                    type="number"
                    placeholder="Ex: 5"
                    {...register('quantidadeSuplentes', { valueAsNumber: true })}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-4">
            <Link to="/eleicoes">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              {isEditing ? 'Salvar Alteracoes' : 'Criar Eleicao'}
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
