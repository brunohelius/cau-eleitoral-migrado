import { useState, useEffect } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import {
  ArrowLeft,
  Loader2,
  Save,
  User,
  Shield,
  Eye,
  EyeOff,
  Mail,
  Phone,
  Building,
  Key,
  CheckCircle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  usuariosService,
  Usuario,
  TipoUsuario,
  Role,
  CreateUsuarioRequest,
  UpdateUsuarioRequest,
} from '@/services/usuarios'

// Validation schema
const usuarioSchema = z.object({
  nome: z.string().min(3, 'Nome deve ter no minimo 3 caracteres'),
  nomeCompleto: z.string().optional(),
  email: z.string().email('Email invalido'),
  cpf: z
    .string()
    .optional()
    .or(z.literal(''))
    .refine((value) => {
      if (!value) return true
      const cleaned = value.replace(/\D/g, '')
      return cleaned.length === 11
    }, 'CPF invalido'),
  telefone: z.string().optional(),
  tipo: z.nativeEnum(TipoUsuario),
  roles: z.array(z.string()).min(1, 'Selecione pelo menos um perfil'),
  password: z.string().min(8, 'Senha deve ter no minimo 8 caracteres').optional(),
  confirmarSenha: z.string().optional(),
  enviarEmailBoasVindas: z.boolean().default(true),
}).refine((data) => {
  if (data.password && data.confirmarSenha) {
    return data.password === data.confirmarSenha
  }
  return true
}, {
  message: 'As senhas nao conferem',
  path: ['confirmarSenha'],
})

type UsuarioFormData = z.infer<typeof usuarioSchema>

// Tipo de usuario options
const tiposUsuario = [
  { value: TipoUsuario.ADMINISTRADOR, label: 'Administrador', descricao: 'Acesso total ao sistema' },
  { value: TipoUsuario.COMISSAO_ELEITORAL, label: 'Comissao Eleitoral', descricao: 'Gerencia eleicoes e julgamentos' },
  { value: TipoUsuario.CONSELHEIRO, label: 'Conselheiro', descricao: 'Atua nos julgamentos e comissoes' },
  { value: TipoUsuario.PROFISSIONAL, label: 'Profissional', descricao: 'Usuario profissional registrado no CAU' },
  { value: TipoUsuario.CANDIDATO, label: 'Candidato', descricao: 'Usuario candidato em uma eleicao' },
  { value: TipoUsuario.ELEITOR, label: 'Eleitor', descricao: 'Usuario apto a votar na eleicao' },
]

// Format CPF for display
const formatCPF = (value: string) => {
  const cleaned = value.replace(/\D/g, '')
  if (cleaned.length <= 3) return cleaned
  if (cleaned.length <= 6) return `${cleaned.slice(0, 3)}.${cleaned.slice(3)}`
  if (cleaned.length <= 9) return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6)}`
  return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9, 11)}`
}

// Format phone for display
const formatPhone = (value: string) => {
  const cleaned = value.replace(/\D/g, '')
  if (cleaned.length <= 2) return cleaned
  if (cleaned.length <= 6) return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2)}`
  if (cleaned.length <= 10) return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`
  return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7, 11)}`
}

export function UsuarioFormPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const isEditing = !!id
  const [showPassword, setShowPassword] = useState(false)

  // Fetch roles
  const {
    data: roles = [],
    isLoading: isLoadingRoles,
    isError: isRolesError,
    error: rolesError,
    refetch: refetchRoles,
  } = useQuery({
    queryKey: ['roles'],
    queryFn: () => usuariosService.getRoles(),
  })

  // Fetch user for editing
  const { data: usuario, isLoading: isLoadingUsuario } = useQuery({
    queryKey: ['usuario', id],
    queryFn: () => usuariosService.getById(id!),
    enabled: isEditing,
  })

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors },
  } = useForm<UsuarioFormData>({
    resolver: zodResolver(usuarioSchema),
    defaultValues: {
      tipo: TipoUsuario.PROFISSIONAL,
      roles: [],
      enviarEmailBoasVindas: true,
    },
  })

  // Watch for form values
  const selectedTipo = watch('tipo')
  const selectedRoles = watch('roles')
  const cpfValue = watch('cpf')
  const telefoneValue = watch('telefone')

  // Populate form when editing
  useEffect(() => {
    if (usuario) {
      reset({
        nome: usuario.nome,
        nomeCompleto: usuario.nomeCompleto || '',
        email: usuario.email,
        cpf: usuario.cpf || '',
        telefone: usuario.telefone || '',
        tipo: usuario.tipo,
        roles: usuario.roles,
        enviarEmailBoasVindas: false,
      })
    }
  }, [usuario, reset])

  // Handle CPF formatting
  const handleCPFChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const formatted = formatCPF(e.target.value)
    setValue('cpf', formatted)
  }

  // Handle phone formatting
  const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const formatted = formatPhone(e.target.value)
    setValue('telefone', formatted)
  }

  // Handle role toggle
  const handleRoleToggle = (roleName: string) => {
    const current = selectedRoles || []
    if (current.includes(roleName)) {
      setValue('roles', current.filter((r) => r !== roleName))
    } else {
      setValue('roles', [...current, roleName])
    }
  }

  // Create mutation
  const createMutation = useMutation({
    mutationFn: (data: CreateUsuarioRequest) => usuariosService.create(data),
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

  // Update mutation
  const updateMutation = useMutation({
    mutationFn: (data: UpdateUsuarioRequest) => usuariosService.update(id!, data),
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

  const onSubmit = async (data: UsuarioFormData) => {
    // Remove confirmarSenha before sending
    const { confirmarSenha, ...submitData } = data

    // Clean CPF and phone
    const cleanedData = {
      ...submitData,
      cpf: submitData.cpf?.replace(/\D/g, ''),
      telefone: submitData.telefone?.replace(/\D/g, ''),
    }

    if (isEditing) {
      const updateData: UpdateUsuarioRequest = {
        nome: cleanedData.nome,
        nomeCompleto: cleanedData.nomeCompleto || undefined,
        telefone: cleanedData.telefone || undefined,
        tipo: cleanedData.tipo,
        roles: cleanedData.roles,
      }
      await updateMutation.mutateAsync(updateData)
    } else {
      // Create new user
      if (!cleanedData.password) {
        toast({
          variant: 'destructive',
          title: 'Senha obrigatoria',
          description: 'Informe uma senha para o novo usuario.',
        })
        return
      }
      const createData: CreateUsuarioRequest = {
        email: cleanedData.email,
        nome: cleanedData.nome,
        nomeCompleto: cleanedData.nomeCompleto || undefined,
        cpf: cleanedData.cpf || undefined,
        telefone: cleanedData.telefone || undefined,
        password: cleanedData.password,
        tipo: cleanedData.tipo,
        roles: cleanedData.roles,
        enviarEmailConfirmacao: cleanedData.enviarEmailBoasVindas,
      }
      createMutation.mutate(createData)
    }
  }

  const isSubmitting = createMutation.isPending || updateMutation.isPending

  if (isEditing && isLoadingUsuario) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
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
          {/* Dados Pessoais */}
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
                  <Label htmlFor="nome">Nome *</Label>
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
                  <Label htmlFor="nomeCompleto">Nome Completo</Label>
                  <Input
                    id="nomeCompleto"
                    placeholder="Nome completo"
                    {...register('nomeCompleto')}
                  />
                </div>
              </div>

              <div className="grid gap-4">
                <div className="space-y-2">
                  <Label htmlFor="cpf">CPF</Label>
                  <Input
                    id="cpf"
                    placeholder="000.000.000-00"
                    value={cpfValue || ''}
                    onChange={handleCPFChange}
                    maxLength={14}
                    disabled={isEditing}
                  />
                  {errors.cpf && (
                    <p className="text-sm text-red-500">{errors.cpf.message}</p>
                  )}
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Contato */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Mail className="h-5 w-5" />
                Contato
              </CardTitle>
              <CardDescription>Informacoes de contato do usuario</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="email">Email *</Label>
                  <Input
                    id="email"
                    type="email"
                    placeholder="usuario@email.com"
                    {...register('email')}
                    disabled={isEditing}
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
                    value={telefoneValue || ''}
                    onChange={handlePhoneChange}
                    maxLength={15}
                  />
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Tipo de Usuario */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Building className="h-5 w-5" />
                Tipo de Usuario
              </CardTitle>
              <CardDescription>Define a categoria do usuario no sistema</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                {tiposUsuario.map((tipo) => (
                  <label
                    key={tipo.value}
                    className={`flex items-start gap-3 rounded-lg border p-4 cursor-pointer transition-colors ${
                      selectedTipo === tipo.value
                        ? 'border-blue-500 bg-blue-50'
                        : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      value={tipo.value}
                      {...register('tipo', { valueAsNumber: true })}
                      className="mt-1"
                    />
                    <div className="flex-1">
                      <p className="font-medium">{tipo.label}</p>
                      <p className="text-sm text-gray-500">{tipo.descricao}</p>
                    </div>
                  </label>
                ))}
              </div>
              {errors.tipo && (
                <p className="text-sm text-red-500">{errors.tipo.message}</p>
              )}
            </CardContent>
          </Card>

          {/* Perfis de Acesso (Roles) */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Perfis de Acesso
              </CardTitle>
              <CardDescription>Selecione os perfis que definem as permissoes do usuario</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              {isLoadingRoles && (
                <div className="flex items-center gap-2 text-sm text-gray-500">
                  <Loader2 className="h-4 w-4 animate-spin" />
                  <span>Carregando perfis...</span>
                </div>
              )}

              {isRolesError && (
                <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                  <p className="font-medium">Erro ao carregar perfis de acesso</p>
                  <p className="mt-1 text-red-600">
                    {rolesError instanceof Error ? rolesError.message : 'Tente novamente mais tarde.'}
                  </p>
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    className="mt-3"
                    onClick={() => refetchRoles()}
                  >
                    Tentar novamente
                  </Button>
                </div>
              )}

              {!isLoadingRoles && !isRolesError && roles.length === 0 && (
                <p className="text-sm text-gray-500">Nenhum perfil encontrado.</p>
              )}

              {!isLoadingRoles && !isRolesError && roles.length > 0 && (
                <div className="grid gap-3 sm:grid-cols-2">
                  {roles.map((role) => (
                    <label
                      key={role.id}
                      className={`flex items-center gap-4 rounded-lg border p-4 cursor-pointer transition-colors ${
                        selectedRoles?.includes(role.nome)
                          ? 'border-blue-500 bg-blue-50'
                          : 'hover:bg-gray-50'
                      }`}
                    >
                      <input
                        type="checkbox"
                        checked={selectedRoles?.includes(role.nome) || false}
                        onChange={() => handleRoleToggle(role.nome)}
                        className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <p className="font-medium">{role.nome}</p>
                          {selectedRoles?.includes(role.nome) && (
                            <CheckCircle className="h-4 w-4 text-blue-500" />
                          )}
                        </div>
                        {role.descricao && (
                          <p className="text-sm text-gray-500">{role.descricao}</p>
                        )}
                      </div>
                    </label>
                  ))}
                </div>
              )}
              {errors.roles && (
                <p className="text-sm text-red-500">{errors.roles.message}</p>
              )}
            </CardContent>
          </Card>

          {/* Credenciais */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Key className="h-5 w-5" />
                Credenciais
              </CardTitle>
              <CardDescription>
                {isEditing
                  ? 'Deixe em branco para manter a senha atual'
                  : 'Defina a senha de acesso do usuario'}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="password">
                    Senha {!isEditing && '*'}
                  </Label>
                  <div className="relative">
                    <Input
                      id="password"
                      type={showPassword ? 'text' : 'password'}
                      placeholder="******"
                      {...register('password')}
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
                  {errors.password && (
                    <p className="text-sm text-red-500">{errors.password.message}</p>
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

              {!isEditing && (
                <div className="flex items-center gap-2 pt-2">
                  <input
                    type="checkbox"
                    id="enviarEmailBoasVindas"
                    {...register('enviarEmailBoasVindas')}
                    className="h-4 w-4 rounded border-gray-300"
                  />
                  <Label htmlFor="enviarEmailBoasVindas" className="font-normal">
                    Enviar email de boas-vindas com as credenciais de acesso
                  </Label>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Submit Buttons */}
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
