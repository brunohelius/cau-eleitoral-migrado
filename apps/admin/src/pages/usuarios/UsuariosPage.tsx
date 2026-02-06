import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  Search,
  Plus,
  Filter,
  MoreHorizontal,
  Eye,
  Edit,
  Lock,
  UserX,
  UserCheck,
  ChevronLeft,
  ChevronRight,
  Loader2,
  Users,
  Download,
  Upload,
  RefreshCw,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  usuariosService,
  Usuario,
  TipoUsuario,
  StatusUsuario,
  UsuarioListParams,
} from '@/services/usuarios'

// Helper to get status info
const getStatusInfo = (status: StatusUsuario) => {
  const statusMap: Record<StatusUsuario, { label: string; color: string }> = {
    [StatusUsuario.ATIVO]: { label: 'Ativo', color: 'bg-green-100 text-green-800' },
    [StatusUsuario.INATIVO]: { label: 'Inativo', color: 'bg-gray-100 text-gray-800' },
    [StatusUsuario.PENDENTE]: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800' },
    [StatusUsuario.BLOQUEADO]: { label: 'Bloqueado', color: 'bg-red-100 text-red-800' },
    [StatusUsuario.SUSPENSO]: { label: 'Suspenso', color: 'bg-orange-100 text-orange-800' },
  }
  return statusMap[status] || { label: 'Desconhecido', color: 'bg-gray-100 text-gray-800' }
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

// Format CPF for display
const formatCPF = (cpf?: string) => {
  if (!cpf) return '-'
  const cleaned = cpf.replace(/\D/g, '')
  if (cleaned.length !== 11) return cpf
  return `${cleaned.slice(0, 3)}.${cleaned.slice(3, 6)}.${cleaned.slice(6, 9)}-${cleaned.slice(9)}`
}

// Format date for display
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

export function UsuariosPage() {
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Filter states
  const [search, setSearch] = useState('')
  const [tipoFilter, setTipoFilter] = useState<TipoUsuario | ''>('')
  const [statusFilter, setStatusFilter] = useState<StatusUsuario | ''>('')
  const [page, setPage] = useState(1)
  const [pageSize] = useState(10)
  const [showFilters, setShowFilters] = useState(false)
  const [actionMenuOpen, setActionMenuOpen] = useState<string | null>(null)

  // Build query params
  const queryParams: UsuarioListParams = {
    page,
    pageSize,
    ...(search && { search }),
    ...(tipoFilter !== '' && { tipo: tipoFilter }),
    ...(statusFilter !== '' && { status: statusFilter }),
  }

  // Fetch usuarios
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['usuarios', queryParams],
    queryFn: () => usuariosService.getAll(queryParams),
  })

  // Ativar mutation
  const ativarMutation = useMutation({
    mutationFn: (id: string) => usuariosService.ativar(id),
    onSuccess: () => {
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
    mutationFn: (id: string) => usuariosService.inativar(id),
    onSuccess: () => {
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

  // Reset senha mutation
  const resetSenhaMutation = useMutation({
    mutationFn: (id: string) => usuariosService.resetarSenha(id, { enviarEmail: true }),
    onSuccess: () => {
      toast({
        title: 'Email enviado',
        description: 'Um email de redefinicao de senha foi enviado ao usuario.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel enviar o email de redefinicao.',
      })
    },
  })

  // Export mutation
  const exportMutation = useMutation({
    mutationFn: () => usuariosService.exportar({ ...queryParams, formato: 'xlsx' }),
    onSuccess: (blob) => {
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `usuarios-${new Date().toISOString().split('T')[0]}.xlsx`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
      toast({
        title: 'Exportacao concluida',
        description: 'O arquivo foi baixado com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Nao foi possivel exportar os usuarios.',
      })
    },
  })

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    setPage(1)
    refetch()
  }

  const clearFilters = () => {
    setSearch('')
    setTipoFilter('')
    setStatusFilter('')
    setPage(1)
  }

  const toggleActionMenu = (id: string) => {
    setActionMenuOpen(actionMenuOpen === id ? null : id)
  }

  const usuarios = data?.data || []
  const total = data?.total || 0
  const totalPages = data?.totalPages || 1

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Usuarios</h1>
          <p className="text-gray-600">Gerencie os usuarios do sistema</p>
        </div>
        <div className="flex gap-2">
          <Button
            variant="outline"
            onClick={() => exportMutation.mutate()}
            disabled={exportMutation.isPending}
          >
            {exportMutation.isPending ? (
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            ) : (
              <Download className="mr-2 h-4 w-4" />
            )}
            Exportar
          </Button>
          <Link to="/usuarios/novo">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Novo Usuario
            </Button>
          </Link>
        </div>
      </div>

      {/* Search and Filters */}
      <Card>
        <CardContent className="pt-6">
          <form onSubmit={handleSearch} className="space-y-4">
            <div className="flex flex-col gap-4 sm:flex-row">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  placeholder="Buscar por nome, email ou CPF..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-10"
                />
              </div>
              <div className="flex gap-2">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setShowFilters(!showFilters)}
                >
                  <Filter className="mr-2 h-4 w-4" />
                  Filtros
                </Button>
                <Button type="submit">Buscar</Button>
              </div>
            </div>

            {showFilters && (
              <div className="grid gap-4 pt-4 border-t sm:grid-cols-3">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Usuario
                  </label>
                  <select
                    value={tipoFilter}
                    onChange={(e) => setTipoFilter(e.target.value as TipoUsuario | '')}
                    className="w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                  >
                    <option value="">Todos</option>
                    <option value={TipoUsuario.ADMINISTRADOR}>Administrador</option>
                    <option value={TipoUsuario.COMISSAO}>Comissao Eleitoral</option>
                    <option value={TipoUsuario.FISCAL}>Fiscal</option>
                    <option value={TipoUsuario.ANALISTA}>Analista</option>
                    <option value={TipoUsuario.AUDITOR}>Auditor</option>
                    <option value={TipoUsuario.OPERADOR}>Operador</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Status
                  </label>
                  <select
                    value={statusFilter}
                    onChange={(e) => setStatusFilter(e.target.value as StatusUsuario | '')}
                    className="w-full h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                  >
                    <option value="">Todos</option>
                    <option value={StatusUsuario.ATIVO}>Ativo</option>
                    <option value={StatusUsuario.INATIVO}>Inativo</option>
                    <option value={StatusUsuario.PENDENTE}>Pendente</option>
                    <option value={StatusUsuario.BLOQUEADO}>Bloqueado</option>
                    <option value={StatusUsuario.SUSPENSO}>Suspenso</option>
                  </select>
                </div>
                <div className="flex items-end">
                  <Button
                    type="button"
                    variant="ghost"
                    onClick={clearFilters}
                    className="w-full"
                  >
                    <RefreshCw className="mr-2 h-4 w-4" />
                    Limpar Filtros
                  </Button>
                </div>
              </div>
            )}
          </form>
        </CardContent>
      </Card>

      {/* Users Table */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Users className="h-5 w-5" />
            Lista de Usuarios
            {!isLoading && (
              <span className="text-sm font-normal text-gray-500">
                ({total} {total === 1 ? 'usuario' : 'usuarios'})
              </span>
            )}
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : isError ? (
            <div className="text-center py-12">
              <p className="text-gray-500">Erro ao carregar usuarios.</p>
              <Button variant="outline" onClick={() => refetch()} className="mt-4">
                Tentar novamente
              </Button>
            </div>
          ) : usuarios.length === 0 ? (
            <div className="text-center py-12">
              <Users className="mx-auto h-12 w-12 text-gray-400" />
              <h3 className="mt-2 text-sm font-medium text-gray-900">
                Nenhum usuario encontrado
              </h3>
              <p className="mt-1 text-sm text-gray-500">
                Tente ajustar os filtros ou crie um novo usuario.
              </p>
              <div className="mt-6">
                <Link to="/usuarios/novo">
                  <Button>
                    <Plus className="mr-2 h-4 w-4" />
                    Novo Usuario
                  </Button>
                </Link>
              </div>
            </div>
          ) : (
            <>
              {/* Desktop Table */}
              <div className="hidden md:block overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b text-left text-sm text-gray-500">
                      <th className="pb-3 font-medium">Nome</th>
                      <th className="pb-3 font-medium">Email</th>
                      <th className="pb-3 font-medium">CPF</th>
                      <th className="pb-3 font-medium">Tipo</th>
                      <th className="pb-3 font-medium">Status</th>
                      <th className="pb-3 font-medium">Ultimo Acesso</th>
                      <th className="pb-3 font-medium text-right">Acoes</th>
                    </tr>
                  </thead>
                  <tbody>
                    {usuarios.map((usuario) => {
                      const statusInfo = getStatusInfo(usuario.status)
                      const tipoInfo = getTipoInfo(usuario.tipo)

                      return (
                        <tr key={usuario.id} className="border-b last:border-0">
                          <td className="py-4">
                            <div className="flex items-center gap-3">
                              <div className="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                {usuario.avatarUrl ? (
                                  <img
                                    src={usuario.avatarUrl}
                                    alt={usuario.nome}
                                    className="h-10 w-10 rounded-full object-cover"
                                  />
                                ) : (
                                  <span className="text-sm font-medium text-gray-600">
                                    {usuario.nome.charAt(0).toUpperCase()}
                                  </span>
                                )}
                              </div>
                              <div>
                                <p className="font-medium text-gray-900">{usuario.nome}</p>
                                {usuario.registroCAU && (
                                  <p className="text-xs text-gray-500">{usuario.registroCAU}</p>
                                )}
                              </div>
                            </div>
                          </td>
                          <td className="py-4">
                            <span className="text-sm text-gray-600">{usuario.email}</span>
                          </td>
                          <td className="py-4">
                            <span className="text-sm text-gray-600">{formatCPF(usuario.cpf)}</span>
                          </td>
                          <td className="py-4">
                            <span
                              className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${tipoInfo.color}`}
                            >
                              {tipoInfo.label}
                            </span>
                          </td>
                          <td className="py-4">
                            <span
                              className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
                            >
                              {statusInfo.label}
                            </span>
                          </td>
                          <td className="py-4">
                            <span className="text-sm text-gray-500">
                              {formatDate(usuario.ultimoAcesso)}
                            </span>
                          </td>
                          <td className="py-4">
                            <div className="flex justify-end gap-1">
                              <Link to={`/usuarios/${usuario.id}`}>
                                <Button variant="ghost" size="icon" title="Visualizar">
                                  <Eye className="h-4 w-4" />
                                </Button>
                              </Link>
                              <Link to={`/usuarios/${usuario.id}/editar`}>
                                <Button variant="ghost" size="icon" title="Editar">
                                  <Edit className="h-4 w-4" />
                                </Button>
                              </Link>
                              <div className="relative">
                                <Button
                                  variant="ghost"
                                  size="icon"
                                  title="Mais acoes"
                                  onClick={() => toggleActionMenu(usuario.id)}
                                >
                                  <MoreHorizontal className="h-4 w-4" />
                                </Button>
                                {actionMenuOpen === usuario.id && (
                                  <>
                                    <div
                                      className="fixed inset-0 z-10"
                                      onClick={() => setActionMenuOpen(null)}
                                    />
                                    <div className="absolute right-0 top-full z-20 mt-1 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                                      <button
                                        className="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        onClick={() => {
                                          resetSenhaMutation.mutate(usuario.id)
                                          setActionMenuOpen(null)
                                        }}
                                      >
                                        <Lock className="mr-3 h-4 w-4 text-gray-400" />
                                        Redefinir Senha
                                      </button>
                                      {usuario.status === StatusUsuario.ATIVO ? (
                                        <button
                                          className="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                          onClick={() => {
                                            inativarMutation.mutate(usuario.id)
                                            setActionMenuOpen(null)
                                          }}
                                        >
                                          <UserX className="mr-3 h-4 w-4" />
                                          Inativar Usuario
                                        </button>
                                      ) : (
                                        <button
                                          className="flex w-full items-center px-4 py-2 text-sm text-green-600 hover:bg-gray-100"
                                          onClick={() => {
                                            ativarMutation.mutate(usuario.id)
                                            setActionMenuOpen(null)
                                          }}
                                        >
                                          <UserCheck className="mr-3 h-4 w-4" />
                                          Ativar Usuario
                                        </button>
                                      )}
                                    </div>
                                  </>
                                )}
                              </div>
                            </div>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>

              {/* Mobile Cards */}
              <div className="md:hidden space-y-4">
                {usuarios.map((usuario) => {
                  const statusInfo = getStatusInfo(usuario.status)
                  const tipoInfo = getTipoInfo(usuario.tipo)

                  return (
                    <div key={usuario.id} className="border rounded-lg p-4 space-y-3">
                      <div className="flex items-start justify-between">
                        <div className="flex items-center gap-3">
                          <div className="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                            {usuario.avatarUrl ? (
                              <img
                                src={usuario.avatarUrl}
                                alt={usuario.nome}
                                className="h-12 w-12 rounded-full object-cover"
                              />
                            ) : (
                              <span className="text-lg font-medium text-gray-600">
                                {usuario.nome.charAt(0).toUpperCase()}
                              </span>
                            )}
                          </div>
                          <div>
                            <p className="font-medium text-gray-900">{usuario.nome}</p>
                            <p className="text-sm text-gray-500">{usuario.email}</p>
                          </div>
                        </div>
                        <span
                          className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
                        >
                          {statusInfo.label}
                        </span>
                      </div>
                      <div className="grid grid-cols-2 gap-2 text-sm">
                        <div>
                          <span className="text-gray-500">CPF: </span>
                          <span className="text-gray-900">{formatCPF(usuario.cpf)}</span>
                        </div>
                        <div>
                          <span
                            className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${tipoInfo.color}`}
                          >
                            {tipoInfo.label}
                          </span>
                        </div>
                      </div>
                      <div className="text-sm text-gray-500">
                        Ultimo acesso: {formatDate(usuario.ultimoAcesso)}
                      </div>
                      <div className="flex gap-2 pt-2 border-t">
                        <Link to={`/usuarios/${usuario.id}`} className="flex-1">
                          <Button variant="outline" size="sm" className="w-full">
                            <Eye className="mr-2 h-4 w-4" />
                            Ver
                          </Button>
                        </Link>
                        <Link to={`/usuarios/${usuario.id}/editar`} className="flex-1">
                          <Button variant="outline" size="sm" className="w-full">
                            <Edit className="mr-2 h-4 w-4" />
                            Editar
                          </Button>
                        </Link>
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => resetSenhaMutation.mutate(usuario.id)}
                        >
                          <Lock className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  )
                })}
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex items-center justify-between pt-4 border-t mt-4">
                  <p className="text-sm text-gray-500">
                    Pagina {page} de {totalPages}
                  </p>
                  <div className="flex gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setPage((p) => Math.max(1, p - 1))}
                      disabled={page === 1}
                    >
                      <ChevronLeft className="h-4 w-4 mr-1" />
                      Anterior
                    </Button>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                      disabled={page === totalPages}
                    >
                      Proximo
                      <ChevronRight className="h-4 w-4 ml-1" />
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
