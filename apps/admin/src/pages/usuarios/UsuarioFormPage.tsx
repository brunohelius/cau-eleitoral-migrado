import { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, User, Shield, Eye, EyeOff } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

const usuarioSchema = z.object({
  nome: z.string().min(3, 'Nome deve ter no minimo 3 caracteres'),
  email: z.string().email('Email invalido'),
  cpf: z.string().min(11, 'CPF invalido'),
  telefone: z.string().optional(),
  cargo: z.string().optional(),
  departamento: z.string().optional(),
  perfil: z.string().min(1, 'Selecione um perfil'),
  senha: z.string().min(6, 'Senha deve ter no minimo 6 caracteres').optional(),
  confirmarSenha: z.string().optional(),
  ativo: z.boolean().default(true),
}).refine((data) => {
  if (data.senha && data.confirmarSenha) {
    return data.senha === data.confirmarSenha
  }
  return true
}, {
  message: 'As senhas nao conferem',
  path: ['confirmarSenha'],
})

type UsuarioFormData = z.infer<typeof usuarioSchema>

interface Usuario {
  id: string
  nome: string
  email: string
  cpf: string
  telefone?: string
  cargo?: string
  departamento?: string
  perfil: string
  ativo: boolean
}

const perfis = [
  { value: 'admin', label: 'Administrador', descricao: 'Acesso total ao sistema' },
  { value: 'comissao', label: 'Comissao Eleitoral', descricao: 'Gerencia eleicoes e julgamentos' },
  { value: 'fiscal', label: 'Fiscal', descricao: 'Acompanha o processo eleitoral' },
  { value: 'operador', label: 'Operador', descricao: 'Operacoes basicas do sistema' },
  { value: 'auditor', label: 'Auditor', descricao: 'Acesso aos logs e relatorios' },
]

export function UsuarioFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id
  const [showPassword, setShowPassword] = useState(false)

  const { data: usuario, isLoading: isLoadingUsuario } = useQuery({
    queryKey: ['usuario', id],
    queryFn: async () => {
      const response = await api.get<Usuario>(`/usuario/${id}`)
      return response.data
    },
    enabled: isEditing,
  })

  const {
    register,
    handleSubmit,
    reset,
    watch,
    formState: { errors },
  } = useForm<UsuarioFormData>({
    resolver: zodResolver(usuarioSchema),
    defaultValues: {
      perfil: 'operador',
      ativo: true,
    },
  })

  useEffect(() => {
    if (usuario) {
      reset({
        nome: usuario.nome,
        email: usuario.email,
        cpf: usuario.cpf,
        telefone: usuario.telefone || '',
        cargo: usuario.cargo || '',
        departamento: usuario.departamento || '',
        perfil: usuario.perfil,
        ativo: usuario.ativo,
      })
    }
  }, [usuario, reset])

  const createMutation = useMutation({
    mutationFn: async (data: UsuarioFormData) => {
      const response = await api.post<Usuario>('/usuario', data)
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario criado com sucesso!',
        description: 'O usuario foi cadastrado no sistema.',
      })
      navigate('/usuarios')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao criar usuario',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const updateMutation = useMutation({
    mutationFn: async (data: Partial<UsuarioFormData>) => {
      const response = await api.put<Usuario>(`/usuario/${id}`, data)
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      queryClient.invalidateQueries({ queryKey: ['usuario', id] })
      toast({
        title: 'Usuario atualizado com sucesso!',
        description: 'As alteracoes foram salvas.',
      })
      navigate('/usuarios')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao atualizar usuario',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: UsuarioFormData) => {
    // Remove confirmarSenha before sending
    const { confirmarSenha, ...submitData } = data
    // Remove senha if empty (for editing)
    if (!submitData.senha) {
      delete submitData.senha
    }

    if (isEditing) {
      updateMutation.mutate(submitData)
    } else {
      createMutation.mutate(submitData as UsuarioFormData)
    }
  }

  const isSubmitting = createMutation.isPending || updateMutation.isPending
  const selectedPerfil = watch('perfil')

  if (isEditing && isLoadingUsuario) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/usuarios">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">
            {isEditing ? 'Editar Usuario' : 'Novo Usuario'}
          </h1>
          <p className="text-gray-600">
            {isEditing ? 'Atualize os dados do usuario' : 'Preencha os dados para criar um novo usuario'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <User className="h-5 w-5" />
                Dados Pessoais
              </CardTitle>
              <CardDescription>Informacoes basicas do usuario</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="nome">Nome Completo *</Label>
                  <Input
                    id="nome"
                    placeholder="Nome do usuario"
                    {...register('nome')}
                  />
                  {errors.nome && (
                    <p className="text-sm text-red-500">{errors.nome.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="cpf">CPF *</Label>
                  <Input
                    id="cpf"
                    placeholder="000.000.000-00"
                    {...register('cpf')}
                  />
                  {errors.cpf && (
                    <p className="text-sm text-red-500">{errors.cpf.message}</p>
                  )}
                </div>
              </div>

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="email">Email *</Label>
                  <Input
                    id="email"
                    type="email"
                    placeholder="usuario@email.com"
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

              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="cargo">Cargo</Label>
                  <Input
                    id="cargo"
                    placeholder="Ex: Coordenador"
                    {...register('cargo')}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="departamento">Departamento</Label>
                  <Input
                    id="departamento"
                    placeholder="Ex: TI"
                    {...register('departamento')}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Perfil de Acesso
              </CardTitle>
              <CardDescription>Define as permissoes do usuario no sistema</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-3">
                {perfis.map((perfil) => (
                  <label
                    key={perfil.value}
                    className={`flex items-center gap-4 rounded-lg border p-4 cursor-pointer transition-colors ${
                      selectedPerfil === perfil.value
                        ? 'border-blue-500 bg-blue-50'
                        : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value={perfil.value}
                      {...register('perfil')}
                      className="sr-only"
                    />
                    <div
                      className={`h-4 w-4 rounded-full border-2 ${
                        selectedPerfil === perfil.value
                          ? 'border-blue-500 bg-blue-500'
                          : 'border-gray-300'
                      }`}
                    >
                      {selectedPerfil === perfil.value && (
                        <div className="h-full w-full flex items-center justify-center">
                          <div className="h-1.5 w-1.5 rounded-full bg-white" />
                        </div>
                      )}
                    </div>
                    <div className="flex-1">
                      <p className="font-medium">{perfil.label}</p>
                      <p className="text-sm text-gray-500">{perfil.descricao}</p>
                    </div>
                  </label>
                ))}
              </div>
              {errors.perfil && (
                <p className="text-sm text-red-500">{errors.perfil.message}</p>
              )}
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Credenciais</CardTitle>
              <CardDescription>
                {isEditing
                  ? 'Deixe em branco para manter a senha atual'
                  : 'Defina a senha de acesso do usuario'}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="senha">
                    Senha {!isEditing && '*'}
                  </Label>
                  <div className="relative">
                    <Input
                      id="senha"
                      type={showPassword ? 'text' : 'password'}
                      placeholder="******"
                      {...register('senha')}
                    />
                    <Button
                      type="button"
                      variant="ghost"
                      size="icon"
                      className="absolute right-0 top-0 h-full px-3"
                      onClick={() => setShowPassword(!showPassword)}
                    >
                      {showPassword ? (
                        <EyeOff className="h-4 w-4 text-gray-400" />
                      ) : (
                        <Eye className="h-4 w-4 text-gray-400" />
                      )}
                    </Button>
                  </div>
                  {errors.senha && (
                    <p className="text-sm text-red-500">{errors.senha.message}</p>
                  )}
                </div>
                <div className="space-y-2">
                  <Label htmlFor="confirmarSenha">Confirmar Senha</Label>
                  <Input
                    id="confirmarSenha"
                    type={showPassword ? 'text' : 'password'}
                    placeholder="******"
                    {...register('confirmarSenha')}
                  />
                  {errors.confirmarSenha && (
                    <p className="text-sm text-red-500">{errors.confirmarSenha.message}</p>
                  )}
                </div>
              </div>

              <div className="flex items-center gap-2 pt-2">
                <input
                  type="checkbox"
                  id="ativo"
                  {...register('ativo')}
                  className="h-4 w-4 rounded border-gray-300"
                />
                <Label htmlFor="ativo" className="font-normal">
                  Usuario ativo (pode acessar o sistema)
                </Label>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-4">
            <Link to="/usuarios">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={isSubmitting}>
              {isSubmitting && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              {isEditing ? 'Salvar Alteracoes' : 'Criar Usuario'}
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
