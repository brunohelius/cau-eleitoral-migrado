import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  User,
  Mail,
  Phone,
  Shield,
  Edit,
  Lock,
  Loader2,
  CheckCircle,
  XCircle,
  AlertTriangle,
  Calendar,
  Ban,
  Trash2,
  UserCheck,
  UserX,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  usuariosService,
  StatusUsuario,
  TipoUsuario,
  type UsuarioDetail,
} from '@/services/usuarios'

const getStatusInfo = (status: StatusUsuario) => {
  const statusMap: Record<StatusUsuario, { label: string; color: string; icon: typeof CheckCircle }> = {
    [StatusUsuario.ATIVO]: { label: 'Ativo', color: 'bg-green-100 text-green-800', icon: CheckCircle },
    [StatusUsuario.INATIVO]: { label: 'Inativo', color: 'bg-gray-100 text-gray-800', icon: XCircle },
    [StatusUsuario.BLOQUEADO]: { label: 'Bloqueado', color: 'bg-red-100 text-red-800', icon: Ban },
    [StatusUsuario.PENDENTE_CADASTRO]: { label: 'Pendente (Cadastro)', color: 'bg-yellow-100 text-yellow-800', icon: AlertTriangle },
    [StatusUsuario.PENDENTE_CONFIRMACAO]: { label: 'Pendente (Confirmação)', color: 'bg-orange-100 text-orange-800', icon: AlertTriangle },
  }
  return statusMap[status] || { label: 'Desconhecido', color: 'bg-gray-100 text-gray-800', icon: XCircle }
}

const getTipoInfo = (tipo: TipoUsuario) => {
  const tipoMap: Record<TipoUsuario, { label: string; color: string }> = {
    [TipoUsuario.ADMINISTRADOR]: { label: 'Administrador', color: 'bg-purple-100 text-purple-800' },
    [TipoUsuario.COMISSAO_ELEITORAL]: { label: 'Comissão Eleitoral', color: 'bg-blue-100 text-blue-800' },
    [TipoUsuario.CONSELHEIRO]: { label: 'Conselheiro', color: 'bg-teal-100 text-teal-800' },
    [TipoUsuario.PROFISSIONAL]: { label: 'Profissional', color: 'bg-indigo-100 text-indigo-800' },
    [TipoUsuario.CANDIDATO]: { label: 'Candidato', color: 'bg-orange-100 text-orange-800' },
    [TipoUsuario.ELEITOR]: { label: 'Eleitor', color: 'bg-cyan-100 text-cyan-800' },
  }
  return tipoMap[tipo] || { label: 'Outro', color: 'bg-gray-100 text-gray-800' }
}

const formatCPF = (cpf?: string | null) => {
  if (!cpf) return '-'
  const cleaned = cpf.replace(/\D/g, '')
  if (cleaned.length !== 11) return cpf
  return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`
}

const formatPhone = (phone?: string | null) => {
  if (!phone) return '-'
  const cleaned = phone.replace(/\D/g, '')
  if (cleaned.length === 11) {
    return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7)}`
  }
  if (cleaned.length === 10) {
    return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`
  }
  return phone
}

const formatDate = (dateString?: string | null) => {
  if (!dateString) return 'Nunca'
  return new Date(dateString).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getRandomInt = (max: number) => {
  const bytes = new Uint32Array(1)
  if (typeof crypto !== 'undefined' && 'getRandomValues' in crypto) {
    crypto.getRandomValues(bytes)
    return bytes[0] % max
  }
  return Math.floor(Math.random() * max)
}

const generateTempPassword = (length = 12) => {
  const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ'
  const lower = 'abcdefghijkmnopqrstuvwxyz'
  const digits = '23456789'
  const special = '@$!%*?&'
  const all = upper + lower + digits + special

  const chars = [
    upper[getRandomInt(upper.length)],
    lower[getRandomInt(lower.length)],
    digits[getRandomInt(digits.length)],
    special[getRandomInt(special.length)],
  ]

  while (chars.length < length) chars.push(all[getRandomInt(all.length)])

  for (let i = chars.length - 1; i > 0; i--) {
    const j = getRandomInt(i + 1)
    ;[chars[i], chars[j]] = [chars[j], chars[i]]
  }

  return chars.join('')
}

export function UsuarioDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  const [showResetPasswordModal, setShowResetPasswordModal] = useState(false)
  const [newPassword, setNewPassword] = useState('')

  const { data: usuario, isLoading, isError } = useQuery({
    queryKey: ['usuario-detail', id],
    queryFn: () => usuariosService.getByIdDetailed(id!),
    enabled: !!id,
  })

  const ativarMutation = useMutation({
    mutationFn: () => usuariosService.ativar(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario-detail', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({ title: 'Usuario ativado', description: 'O usuario foi ativado com sucesso.' })
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível ativar o usuario.' }),
  })

  const inativarMutation = useMutation({
    mutationFn: () => usuariosService.inativar(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario-detail', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({ title: 'Usuario inativado', description: 'O usuario foi inativado com sucesso.' })
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível inativar o usuario.' }),
  })

  const bloquearMutation = useMutation({
    mutationFn: () => usuariosService.bloquear(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario-detail', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({ title: 'Usuario bloqueado', description: 'O usuario foi bloqueado com sucesso.' })
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível bloquear o usuario.' }),
  })

  const desbloquearMutation = useMutation({
    mutationFn: () => usuariosService.desbloquear(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario-detail', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({ title: 'Usuario desbloqueado', description: 'O usuario foi desbloqueado com sucesso.' })
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível desbloquear o usuario.' }),
  })

  const resetSenhaMutation = useMutation({
    mutationFn: async () => {
      const password = newPassword?.trim() || generateTempPassword()
      await usuariosService.resetarSenha(id!, password)
      return password
    },
    onSuccess: (password) => {
      setShowResetPasswordModal(false)
      setNewPassword('')
      toast({ title: 'Senha redefinida', description: `Nova senha: ${password}` })
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível redefinir a senha.' }),
  })

  const deleteMutation = useMutation({
    mutationFn: () => usuariosService.delete(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({ title: 'Usuario excluido', description: 'O usuario foi excluido com sucesso.' })
      navigate('/usuarios')
    },
    onError: () => toast({ variant: 'destructive', title: 'Erro', description: 'Não foi possível excluir o usuario.' }),
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (isError || !usuario) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Usuario não encontrado.</p>
      </div>
    )
  }

  const user = usuario as UsuarioDetail
  const statusInfo = getStatusInfo(user.status)
  const tipoInfo = getTipoInfo(user.tipo)
  const StatusIcon = statusInfo.icon

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div className="flex items-center gap-4">
          <Link to="/usuarios">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>

          <div className="flex items-center gap-4">
            <div className="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
              <span className="text-2xl font-medium text-gray-600">{user.nome.charAt(0).toUpperCase()}</span>
            </div>
            <div>
              <div className="flex items-center gap-3">
                <h1 className="text-2xl font-bold text-gray-900">{user.nome}</h1>
                <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}>
                  <StatusIcon className="h-3 w-3" />
                  {statusInfo.label}
                </span>
              </div>
              <p className="text-gray-600">{user.email}</p>
              <div className="mt-1">
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${tipoInfo.color}`}>
                  {tipoInfo.label}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div className="flex flex-wrap gap-2">
          <Button variant="outline" onClick={() => setShowResetPasswordModal(true)}>
            <Lock className="mr-2 h-4 w-4" />
            Redefinir Senha
          </Button>

          {user.status === StatusUsuario.ATIVO ? (
            <Button
              variant="outline"
              onClick={() => inativarMutation.mutate()}
              disabled={inativarMutation.isPending}
            >
              <UserX className="mr-2 h-4 w-4" />
              Inativar
            </Button>
          ) : user.status === StatusUsuario.BLOQUEADO ? (
            <Button
              variant="outline"
              onClick={() => desbloquearMutation.mutate()}
              disabled={desbloquearMutation.isPending}
            >
              <UserCheck className="mr-2 h-4 w-4" />
              Desbloquear
            </Button>
          ) : (
            <Button
              variant="outline"
              onClick={() => ativarMutation.mutate()}
              disabled={ativarMutation.isPending}
            >
              <UserCheck className="mr-2 h-4 w-4" />
              Ativar
            </Button>
          )}

          <Link to={`/usuarios/${id}/editar`}>
            <Button>
              <Edit className="mr-2 h-4 w-4" />
              Editar
            </Button>
          </Link>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Informações do Usuario
            </CardTitle>
          </CardHeader>
          <CardContent>
            <dl className="grid gap-4 sm:grid-cols-2">
              <div className="flex items-start gap-3">
                <Mail className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Email</dt>
                  <dd className="mt-1 text-sm text-gray-900 flex items-center gap-2">
                    {user.email}
                    {user.emailConfirmado ? (
                      <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700">
                        <CheckCircle className="h-3 w-3" />
                        Confirmado
                      </span>
                    ) : (
                      <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700">
                        <AlertTriangle className="h-3 w-3" />
                        Pendente
                      </span>
                    )}
                  </dd>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <Phone className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Telefone</dt>
                  <dd className="mt-1 text-sm text-gray-900">{formatPhone(user.telefone)}</dd>
                </div>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">CPF</dt>
                <dd className="mt-1 text-sm text-gray-900">{formatCPF(user.cpf)}</dd>
              </div>

              <div className="flex items-start gap-3">
                <Calendar className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Criado em</dt>
                  <dd className="mt-1 text-sm text-gray-900">{formatDate(user.createdAt)}</dd>
                </div>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">Ultimo acesso</dt>
                <dd className="mt-1 text-sm text-gray-900">{formatDate(user.ultimoAcesso)}</dd>
              </div>

              <div>
                <dt className="text-sm font-medium text-gray-500">2FA</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {user.doisFatoresHabilitado ? 'Ativo' : 'Inativo'}
                </dd>
              </div>
            </dl>
          </CardContent>
        </Card>

        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Perfis de Acesso
              </CardTitle>
              <CardDescription>
                {user.rolesDetail?.length ? `${user.rolesDetail.length} roles` : `${user.roles.length} roles`}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {(user.rolesDetail?.length ? user.rolesDetail.map((r) => r.nome) : user.roles).map((roleName) => (
                  <div key={roleName} className="rounded-lg bg-gray-50 p-3">
                    <p className="font-medium text-sm">{roleName}</p>
                  </div>
                ))}
                {(!user.rolesDetail?.length && user.roles.length === 0) && (
                  <p className="text-sm text-gray-500">Nenhum perfil atribuido</p>
                )}
              </div>
            </CardContent>
          </Card>
        </div>

        <Card className="lg:col-span-3 border-red-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-red-600">
              <AlertTriangle className="h-5 w-5" />
              Zona de Perigo
            </CardTitle>
            <CardDescription>Ações irreversiveis. Prossiga com cautela.</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="flex flex-wrap gap-4">
              {user.status !== StatusUsuario.BLOQUEADO && (
                <Button
                  variant="outline"
                  className="border-orange-300 text-orange-600 hover:bg-orange-50"
                  onClick={() => bloquearMutation.mutate()}
                  disabled={bloquearMutation.isPending}
                >
                  <Ban className="mr-2 h-4 w-4" />
                  Bloquear Usuario
                </Button>
              )}

              <Button
                variant="outline"
                className="border-red-300 text-red-600 hover:bg-red-50"
                onClick={() => {
                  if (confirm('Tem certeza que deseja excluir este usuario? Esta ação e irreversivel.')) {
                    deleteMutation.mutate()
                  }
                }}
                disabled={deleteMutation.isPending}
              >
                <Trash2 className="mr-2 h-4 w-4" />
                Excluir Usuario
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      {showResetPasswordModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black bg-opacity-50" onClick={() => setShowResetPasswordModal(false)} />
          <div className="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
            <h2 className="text-lg font-semibold mb-4">Redefinir Senha</h2>
            <div className="space-y-4">
              <div>
                <Label htmlFor="newPassword">Nova Senha</Label>
                <Input
                  id="newPassword"
                  type="text"
                  placeholder="Deixe vazio para gerar automaticamente"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  className="mt-1"
                />
                <p className="text-xs text-gray-500 mt-1">
                  Se deixar vazio, uma senha temporaria será gerada e exibida.
                </p>
              </div>
            </div>
            <div className="flex justify-end gap-3 mt-6">
              <Button variant="outline" onClick={() => setShowResetPasswordModal(false)}>
                Cancelar
              </Button>
              <Button onClick={() => resetSenhaMutation.mutate()} disabled={resetSenhaMutation.isPending}>
                {resetSenhaMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                Redefinir Senha
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
