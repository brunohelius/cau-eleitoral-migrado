import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
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
  Activity,
  UserX,
  UserCheck,
  AlertTriangle,
  Calendar,
  MapPin,
  Key,
  Send,
  Ban,
  Trash2,
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
  StatusUsuario,
  LogAtividade,
} from '@/services/usuarios'

// Helper to get status info
const getStatusInfo = (status: StatusUsuario) => {
  const statusMap: Record<StatusUsuario, { label: string; color: string; icon: typeof CheckCircle }> = {
    [StatusUsuario.ATIVO]: { label: 'Ativo', color: 'bg-green-100 text-green-800', icon: CheckCircle },
    [StatusUsuario.INATIVO]: { label: 'Inativo', color: 'bg-gray-100 text-gray-800', icon: XCircle },
    [StatusUsuario.PENDENTE]: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800', icon: AlertTriangle },
    [StatusUsuario.BLOQUEADO]: { label: 'Bloqueado', color: 'bg-red-100 text-red-800', icon: Ban },
    [StatusUsuario.SUSPENSO]: { label: 'Suspenso', color: 'bg-orange-100 text-orange-800', icon: AlertTriangle },
  }
  return statusMap[status] || { label: 'Desconhecido', color: 'bg-gray-100 text-gray-800', icon: XCircle }
}

// Helper to get tipo info
const getTipoInfo = (tipo: TipoUsuario) => {
  const tipoMap: Record<TipoUsuario, { label: string; color: string }> = {
    [TipoUsuario.ADMINISTRADOR]: { label: 'Administrador', color: 'bg-purple-100 text-purple-800' },
    [TipoUsuario.COMISSAO]: { label: 'Comissao Eleitoral', color: 'bg-blue-100 text-blue-800' },
    [TipoUsuario.FISCAL]: { label: 'Fiscal', color: 'bg-teal-100 text-teal-800' },
    [TipoUsuario.ANALISTA]: { label: 'Analista', color: 'bg-indigo-100 text-indigo-800' },
    [TipoUsuario.AUDITOR]: { label: 'Auditor', color: 'bg-orange-100 text-orange-800' },
    [TipoUsuario.OPERADOR]: { label: 'Operador', color: 'bg-cyan-100 text-cyan-800' },
  }
  return tipoMap[tipo] || { label: 'Outro', color: 'bg-gray-100 text-gray-800' }
}

// Format CPF
const formatCPF = (cpf?: string) => {
  if (!cpf) return '-'
  const cleaned = cpf.replace(/\D/g, '')
  if (cleaned.length !== 11) return cpf
  return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`
}

// Format phone
const formatPhone = (phone?: string) => {
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

// Format date
const formatDate = (dateString?: string) => {
  if (!dateString) return 'Nunca'
  return new Date(dateString).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Mock user for development
const mockUsuario: Usuario = {
  id: '1',
  nome: 'Carlos Alberto Silva',
  nomeCompleto: 'Carlos Alberto da Silva Santos',
  email: 'carlos.silva@cau.org.br',
  cpf: '12345678900',
  telefone: '11987654321',
  registroCAU: 'A123456-SP',
  tipo: TipoUsuario.ADMINISTRADOR,
  status: StatusUsuario.ATIVO,
  roles: [
    { id: '1', nome: 'Administrador', codigo: 'admin', descricao: 'Acesso total ao sistema' },
    { id: '2', nome: 'Comissao Eleitoral', codigo: 'comissao', descricao: 'Gerencia eleicoes' },
  ],
  ultimoAcesso: '2024-02-20T15:30:00',
  emailVerificado: true,
  doisFatoresAtivo: true,
  createdAt: '2023-01-15T10:00:00',
  updatedAt: '2024-02-10T14:00:00',
  regionalNome: 'CAU/SP',
}

// Mock activities
const mockAtividades: LogAtividade[] = [
  { id: '1', usuarioId: '1', acao: 'login', descricao: 'Login realizado', ip: '192.168.1.100', createdAt: '2024-02-20T15:30:00' },
  { id: '2', usuarioId: '1', acao: 'eleicao.update', descricao: 'Eleicao editada - CAU/SP 2024', createdAt: '2024-02-20T14:00:00' },
  { id: '3', usuarioId: '1', acao: 'usuario.create', descricao: 'Usuario criado - Maria Santos', createdAt: '2024-02-19T16:45:00' },
  { id: '4', usuarioId: '1', acao: 'login', descricao: 'Login realizado', ip: '192.168.1.100', createdAt: '2024-02-19T10:30:00' },
  { id: '5', usuarioId: '1', acao: 'relatorio.export', descricao: 'Relatorio exportado - Votacao', createdAt: '2024-02-18T09:00:00' },
]

export function UsuarioDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [showResetPasswordModal, setShowResetPasswordModal] = useState(false)
  const [newPassword, setNewPassword] = useState('')
  const [sendEmail, setSendEmail] = useState(true)

  // Fetch user
  const { data: usuario, isLoading } = useQuery({
    queryKey: ['usuario', id],
    queryFn: () => usuariosService.getById(id!),
    enabled: !!id,
  })

  // Fetch activities
  const { data: atividadesData } = useQuery({
    queryKey: ['usuario-atividades', id],
    queryFn: () => usuariosService.getAtividades(id!, { pageSize: 10 }),
    enabled: !!id,
  })

  // Use mock data if API returns nothing
  const user = usuario || mockUsuario
  const atividades = atividadesData?.data || mockAtividades

  // Ativar mutation
  const ativarMutation = useMutation({
    mutationFn: () => usuariosService.ativar(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario ativado',
        description: 'O usuario foi ativado com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel ativar o usuario.',
      })
    },
  })

  // Inativar mutation
  const inativarMutation = useMutation({
    mutationFn: () => usuariosService.inativar(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario inativado',
        description: 'O usuario foi inativado com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel inativar o usuario.',
      })
    },
  })

  // Bloquear mutation
  const bloquearMutation = useMutation({
    mutationFn: () => usuariosService.bloquear(id!, 'Bloqueio administrativo'),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario bloqueado',
        description: 'O usuario foi bloqueado com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel bloquear o usuario.',
      })
    },
  })

  // Desbloquear mutation
  const desbloquearMutation = useMutation({
    mutationFn: () => usuariosService.desbloquear(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuario', id] })
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario desbloqueado',
        description: 'O usuario foi desbloqueado com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel desbloquear o usuario.',
      })
    },
  })

  // Reset senha mutation
  const resetSenhaMutation = useMutation({
    mutationFn: () => usuariosService.resetarSenha(id!, {
      novaSenha: newPassword || undefined,
      enviarEmail: sendEmail,
    }),
    onSuccess: (data) => {
      setShowResetPasswordModal(false)
      setNewPassword('')
      if (data.tempPassword) {
        toast({
          title: 'Senha redefinida',
          description: `Nova senha temporaria: ${data.tempPassword}`,
        })
      } else {
        toast({
          title: 'Email enviado',
          description: 'Um email de redefinicao de senha foi enviado ao usuario.',
        })
      }
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel redefinir a senha.',
      })
    },
  })

  // Enviar verificacao email mutation
  const enviarVerificacaoMutation = useMutation({
    mutationFn: () => usuariosService.enviarVerificacaoEmail(id!),
    onSuccess: () => {
      toast({
        title: 'Email enviado',
        description: 'Um email de verificacao foi enviado ao usuario.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel enviar o email de verificacao.',
      })
    },
  })

  // Delete mutation
  const deleteMutation = useMutation({
    mutationFn: () => usuariosService.delete(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['usuarios'] })
      toast({
        title: 'Usuario excluido',
        description: 'O usuario foi excluido com sucesso.',
      })
      navigate('/usuarios')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel excluir o usuario.',
      })
    },
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  const statusInfo = getStatusInfo(user.status)
  const tipoInfo = getTipoInfo(user.tipo)
  const StatusIcon = statusInfo.icon

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div className="flex items-center gap-4">
          <Link to="/usuarios">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div className="flex items-center gap-4">
            <div className="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
              {user.avatarUrl ? (
                <img
                  src={user.avatarUrl}
                  alt={user.nome}
                  className="h-16 w-16 rounded-full object-cover"
                />
              ) : (
                <span className="text-2xl font-medium text-gray-600">
                  {user.nome.charAt(0).toUpperCase()}
                </span>
              )}
            </div>
            <div>
              <div className="flex items-center gap-3">
                <h1 className="text-2xl font-bold text-gray-900">{user.nome}</h1>
                <span
                  className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
                >
                  <StatusIcon className="h-3 w-3" />
                  {statusInfo.label}
                </span>
              </div>
              <p className="text-gray-600">{user.email}</p>
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
        {/* Informacoes do Usuario */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Informacoes do Usuario
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
                    {user.emailVerificado ? (
                      <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700">
                        <CheckCircle className="h-3 w-3" />
                        Verificado
                      </span>
                    ) : (
                      <Button
                        variant="ghost"
                        size="sm"
                        className="h-6 text-xs"
                        onClick={() => enviarVerificacaoMutation.mutate()}
                        disabled={enviarVerificacaoMutation.isPending}
                      >
                        <Send className="h-3 w-3 mr-1" />
                        Verificar
                      </Button>
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
              <div>
                <dt className="text-sm font-medium text-gray-500">Registro CAU</dt>
                <dd className="mt-1 text-sm text-gray-900">{user.registroCAU || '-'}</dd>
              </div>
              {user.nomeCompleto && (
                <div className="sm:col-span-2">
                  <dt className="text-sm font-medium text-gray-500">Nome Completo</dt>
                  <dd className="mt-1 text-sm text-gray-900">{user.nomeCompleto}</dd>
                </div>
              )}
              <div>
                <dt className="text-sm font-medium text-gray-500">Tipo</dt>
                <dd className="mt-1">
                  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${tipoInfo.color}`}>
                    {tipoInfo.label}
                  </span>
                </dd>
              </div>
              {user.regionalNome && (
                <div className="flex items-start gap-3">
                  <MapPin className="h-5 w-5 text-gray-400 mt-0.5" />
                  <div>
                    <dt className="text-sm font-medium text-gray-500">Regional</dt>
                    <dd className="mt-1 text-sm text-gray-900">{user.regionalNome}</dd>
                  </div>
                </div>
              )}
              <div className="flex items-start gap-3">
                <Calendar className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                  <dd className="mt-1 text-sm text-gray-900">{formatDate(user.createdAt)}</dd>
                </div>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Ultimo Acesso</dt>
                <dd className="mt-1 text-sm text-gray-900">{formatDate(user.ultimoAcesso)}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>

        {/* Perfis de Acesso e Seguranca */}
        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Perfis de Acesso
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {user.roles.map((role) => (
                  <div key={role.id} className="rounded-lg bg-gray-50 p-3">
                    <p className="font-medium text-sm">{role.nome}</p>
                    {role.descricao && (
                      <p className="text-xs text-gray-500 mt-1">{role.descricao}</p>
                    )}
                  </div>
                ))}
                {user.roles.length === 0 && (
                  <p className="text-sm text-gray-500">Nenhum perfil atribuido</p>
                )}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Key className="h-5 w-5" />
                Seguranca
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="flex items-center justify-between">
                  <span className="text-sm text-gray-600">Email Verificado</span>
                  {user.emailVerificado ? (
                    <span className="inline-flex items-center gap-1 text-sm text-green-600">
                      <CheckCircle className="h-4 w-4" />
                      Sim
                    </span>
                  ) : (
                    <span className="inline-flex items-center gap-1 text-sm text-red-600">
                      <XCircle className="h-4 w-4" />
                      Nao
                    </span>
                  )}
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-gray-600">Autenticacao 2FA</span>
                  {user.doisFatoresAtivo ? (
                    <span className="inline-flex items-center gap-1 text-sm text-green-600">
                      <CheckCircle className="h-4 w-4" />
                      Ativo
                    </span>
                  ) : (
                    <span className="inline-flex items-center gap-1 text-sm text-gray-500">
                      <XCircle className="h-4 w-4" />
                      Inativo
                    </span>
                  )}
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Atividades Recentes */}
        <Card className="lg:col-span-3">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Activity className="h-5 w-5" />
              Atividades Recentes
            </CardTitle>
            <CardDescription>Ultimas acoes realizadas pelo usuario</CardDescription>
          </CardHeader>
          <CardContent>
            {atividades.length === 0 ? (
              <p className="text-sm text-gray-500 text-center py-8">
                Nenhuma atividade registrada
              </p>
            ) : (
              <div className="relative">
                <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
                <div className="space-y-6">
                  {atividades.map((atividade) => (
                    <div key={atividade.id} className="relative flex gap-4 pl-10">
                      <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                      <div className="flex-1">
                        <div className="flex items-center justify-between">
                          <span className="font-medium text-sm">{atividade.descricao}</span>
                          <span className="text-xs text-gray-500">
                            {formatDate(atividade.createdAt)}
                          </span>
                        </div>
                        {atividade.ip && (
                          <p className="text-xs text-gray-500 mt-1">IP: {atividade.ip}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Acoes Perigosas */}
        <Card className="lg:col-span-3 border-red-200">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-red-600">
              <AlertTriangle className="h-5 w-5" />
              Zona de Perigo
            </CardTitle>
            <CardDescription>Acoes irreversiveis. Prossiga com cautela.</CardDescription>
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
                  if (confirm('Tem certeza que deseja excluir este usuario? Esta acao e irreversivel.')) {
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

      {/* Reset Password Modal */}
      {showResetPasswordModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div
            className="fixed inset-0 bg-black bg-opacity-50"
            onClick={() => setShowResetPasswordModal(false)}
          />
          <div className="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
            <h2 className="text-lg font-semibold mb-4">Redefinir Senha</h2>
            <div className="space-y-4">
              <div>
                <Label htmlFor="newPassword">Nova Senha (opcional)</Label>
                <Input
                  id="newPassword"
                  type="password"
                  placeholder="Deixe vazio para gerar automaticamente"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  className="mt-1"
                />
                <p className="text-xs text-gray-500 mt-1">
                  Se deixar vazio, uma senha temporaria sera gerada.
                </p>
              </div>
              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="sendEmail"
                  checked={sendEmail}
                  onChange={(e) => setSendEmail(e.target.checked)}
                  className="h-4 w-4 rounded border-gray-300"
                />
                <Label htmlFor="sendEmail" className="font-normal">
                  Enviar nova senha por email
                </Label>
              </div>
            </div>
            <div className="flex justify-end gap-3 mt-6">
              <Button
                variant="outline"
                onClick={() => setShowResetPasswordModal(false)}
              >
                Cancelar
              </Button>
              <Button
                onClick={() => resetSenhaMutation.mutate()}
                disabled={resetSenhaMutation.isPending}
              >
                {resetSenhaMutation.isPending && (
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                )}
                Redefinir Senha
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
