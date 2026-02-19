import { useState, useEffect, useCallback } from 'react'
import { Link } from 'react-router-dom'
import {
  Bell,
  Vote,
  AlertTriangle,
  Info,
  CheckCircle,
  Trash2,
  Check,
  ChevronRight,
  ChevronLeft,
  Filter,
  Loader2,
  AlertCircle,
  RefreshCw,
  Settings,
} from 'lucide-react'
import api, { extractApiError } from '../../services/api'

// Backend enums
enum TipoNotificacao {
  Info = 0,
  Sucesso = 1,
  Alerta = 2,
  Erro = 3,
  Sistema = 4,
}

// Types matching the backend DTOs
interface NotificacaoDto {
  id: string
  usuarioId: string
  titulo: string
  mensagem: string
  tipo: TipoNotificacao
  prioridade: number
  lida: boolean
  dataLeitura: string | null
  link: string | null
  icone: string | null
  dados: Record<string, string> | null
  createdAt: string
}

interface PagedResult<T> {
  items: T[]
  totalCount: number
  page: number
  pageSize: number
  totalPages: number
  hasNextPage: boolean
  hasPreviousPage: boolean
}

interface ContagemNotificacoesDto {
  total: number
  naoLidas: number
  altaPrioridade: number
}

const tipoConfig: Record<number, { icon: typeof Bell; color: string; bg: string }> = {
  [TipoNotificacao.Info]: { icon: Info, color: 'text-blue-600', bg: 'bg-blue-100' },
  [TipoNotificacao.Sucesso]: { icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
  [TipoNotificacao.Alerta]: { icon: AlertTriangle, color: 'text-yellow-600', bg: 'bg-yellow-100' },
  [TipoNotificacao.Erro]: { icon: AlertCircle, color: 'text-red-600', bg: 'bg-red-100' },
  [TipoNotificacao.Sistema]: { icon: Settings, color: 'text-gray-600', bg: 'bg-gray-100' },
}

const defaultTipoConfig = { icon: Vote, color: 'text-primary', bg: 'bg-primary/10' }

const PAGE_SIZE = 20

export function NotificacoesPage() {
  const [notificacoes, setNotificacoes] = useState<NotificacaoDto[]>([])
  const [filter, setFilter] = useState<'todas' | 'nao_lidas'>('todas')
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(0)
  const [totalCount, setTotalCount] = useState(0)
  const [hasNextPage, setHasNextPage] = useState(false)
  const [hasPreviousPage, setHasPreviousPage] = useState(false)
  const [unreadCount, setUnreadCount] = useState(0)
  const [actionLoading, setActionLoading] = useState<string | null>(null)

  const fetchNotificacoes = useCallback(async () => {
    setIsLoading(true)
    setError(null)
    try {
      const params: Record<string, string | number | boolean> = {
        page,
        pageSize: PAGE_SIZE,
      }
      if (filter === 'nao_lidas') {
        params.apenasNaoLidas = true
      }
      const response = await api.get<PagedResult<NotificacaoDto>>('/notificacao', { params })
      const data = response.data
      setNotificacoes(data.items ?? [])
      setTotalPages(data.totalPages)
      setTotalCount(data.totalCount)
      setHasNextPage(data.hasNextPage)
      setHasPreviousPage(data.hasPreviousPage)
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message)
      setNotificacoes([])
    } finally {
      setIsLoading(false)
    }
  }, [page, filter])

  const fetchUnreadCount = useCallback(async () => {
    try {
      const response = await api.get<ContagemNotificacoesDto>('/notificacao/nao-lidas/count')
      setUnreadCount(response.data.naoLidas)
    } catch {
      // Silently fail for unread count - not critical
    }
  }, [])

  useEffect(() => {
    fetchNotificacoes()
  }, [fetchNotificacoes])

  useEffect(() => {
    fetchUnreadCount()
  }, [fetchUnreadCount])

  // Reset to page 1 when filter changes
  useEffect(() => {
    setPage(1)
  }, [filter])

  const markAsRead = async (id: string) => {
    setActionLoading(id)
    try {
      await api.post(`/notificacao/${id}/marcar-lida`)
      setNotificacoes(prev =>
        prev.map(n => n.id === id ? { ...n, lida: true, dataLeitura: new Date().toISOString() } : n)
      )
      setUnreadCount(prev => Math.max(0, prev - 1))
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message)
    } finally {
      setActionLoading(null)
    }
  }

  const markAllAsRead = async () => {
    setActionLoading('all')
    try {
      await api.post('/notificacao/marcar-todas-lidas')
      setNotificacoes(prev =>
        prev.map(n => ({ ...n, lida: true, dataLeitura: n.dataLeitura ?? new Date().toISOString() }))
      )
      setUnreadCount(0)
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message)
    } finally {
      setActionLoading(null)
    }
  }

  const deleteNotification = async (id: string) => {
    setActionLoading(id)
    try {
      await api.delete(`/notificacao/${id}`)
      const deleted = notificacoes.find(n => n.id === id)
      setNotificacoes(prev => prev.filter(n => n.id !== id))
      if (deleted && !deleted.lida) {
        setUnreadCount(prev => Math.max(0, prev - 1))
      }
      // If the page is now empty and we're not on page 1, go back one page
      if (notificacoes.length === 1 && page > 1) {
        setPage(prev => prev - 1)
      } else {
        // Refresh to fill in from next page
        fetchNotificacoes()
      }
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message)
    } finally {
      setActionLoading(null)
    }
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    const now = new Date()
    const diff = now.getTime() - date.getTime()
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))

    if (days === 0) {
      return `Hoje as ${date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`
    } else if (days === 1) {
      return 'Ontem'
    } else if (days < 7) {
      return `${days} dias atras`
    } else {
      return date.toLocaleDateString('pt-BR')
    }
  }

  const getConfig = (tipo: TipoNotificacao) => {
    return tipoConfig[tipo] ?? defaultTipoConfig
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Notificações</h1>
          <p className="text-gray-600 mt-1">
            {unreadCount > 0 ? `${unreadCount} notificacao(es) nao lida(s)` : 'Todas as notificacoes foram lidas'}
          </p>
        </div>

        {unreadCount > 0 && (
          <button
            onClick={markAllAsRead}
            disabled={actionLoading === 'all'}
            className="inline-flex items-center gap-2 px-4 py-2 text-sm text-primary hover:bg-primary/10 rounded-lg disabled:opacity-50"
          >
            {actionLoading === 'all' ? (
              <Loader2 className="h-4 w-4 animate-spin" />
            ) : (
              <Check className="h-4 w-4" />
            )}
            Marcar todas como lidas
          </button>
        )}
      </div>

      {/* Filters */}
      <div className="flex items-center gap-2">
        <Filter className="h-4 w-4 text-gray-500" />
        <button
          onClick={() => setFilter('todas')}
          className={`px-3 py-1 rounded-full text-sm font-medium transition-colors ${
            filter === 'todas'
              ? 'bg-primary text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          Todas
        </button>
        <button
          onClick={() => setFilter('nao_lidas')}
          className={`px-3 py-1 rounded-full text-sm font-medium transition-colors ${
            filter === 'nao_lidas'
              ? 'bg-primary text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          Nao lidas {unreadCount > 0 && `(${unreadCount})`}
        </button>
      </div>

      {/* Error State */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-center gap-3">
            <AlertCircle className="h-5 w-5 text-red-600 flex-shrink-0" />
            <div className="flex-1">
              <p className="text-sm text-red-800">{error}</p>
            </div>
            <button
              onClick={() => {
                setError(null)
                fetchNotificacoes()
                fetchUnreadCount()
              }}
              className="inline-flex items-center gap-1 text-sm text-red-700 hover:text-red-900 font-medium"
            >
              <RefreshCw className="h-4 w-4" />
              Tentar novamente
            </button>
          </div>
        </div>
      )}

      {/* Notifications List */}
      {isLoading ? (
        <div className="flex items-center justify-center py-12">
          <Loader2 className="h-8 w-8 animate-spin text-primary" />
        </div>
      ) : notificacoes.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Bell className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">
            {filter === 'nao_lidas'
              ? 'Nenhuma notificacao nao lida'
              : 'Nenhuma notificacao encontrada'}
          </p>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm border divide-y overflow-hidden">
          {notificacoes.map(notificacao => {
            const config = getConfig(notificacao.tipo)
            const Icon = config.icon
            const isActionLoading = actionLoading === notificacao.id

            return (
              <div
                key={notificacao.id}
                className={`p-4 sm:p-6 transition-colors ${
                  !notificacao.lida ? 'bg-primary/5' : 'hover:bg-gray-50'
                }`}
              >
                <div className="flex gap-4">
                  {/* Icon */}
                  <div className={`p-2 ${config.bg} rounded-lg h-fit flex-shrink-0`}>
                    <Icon className={`h-5 w-5 ${config.color}`} />
                  </div>

                  {/* Content */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-4">
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <h3 className={`font-semibold ${!notificacao.lida ? 'text-gray-900' : 'text-gray-700'}`}>
                            {notificacao.titulo}
                          </h3>
                          {!notificacao.lida && (
                            <span className="w-2 h-2 bg-primary rounded-full flex-shrink-0" />
                          )}
                        </div>
                        <p className="text-gray-600 text-sm mt-1">{notificacao.mensagem}</p>
                        <p className="text-gray-400 text-xs mt-2">{formatDate(notificacao.createdAt)}</p>
                      </div>

                      {/* Actions */}
                      <div className="flex items-center gap-1 flex-shrink-0">
                        {!notificacao.lida && (
                          <button
                            onClick={() => markAsRead(notificacao.id)}
                            disabled={isActionLoading}
                            className="p-2 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-lg disabled:opacity-50"
                            title="Marcar como lida"
                          >
                            {isActionLoading ? (
                              <Loader2 className="h-4 w-4 animate-spin" />
                            ) : (
                              <Check className="h-4 w-4" />
                            )}
                          </button>
                        )}
                        <button
                          onClick={() => deleteNotification(notificacao.id)}
                          disabled={isActionLoading}
                          className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg disabled:opacity-50"
                          title="Excluir"
                        >
                          {isActionLoading && notificacao.lida ? (
                            <Loader2 className="h-4 w-4 animate-spin" />
                          ) : (
                            <Trash2 className="h-4 w-4" />
                          )}
                        </button>
                      </div>
                    </div>

                    {/* Link */}
                    {notificacao.link && (
                      <Link
                        to={notificacao.link}
                        onClick={() => {
                          if (!notificacao.lida) {
                            markAsRead(notificacao.id)
                          }
                        }}
                        className="inline-flex items-center gap-1 mt-3 text-sm text-primary font-medium hover:underline"
                      >
                        Ver detalhes
                        <ChevronRight className="h-4 w-4" />
                      </Link>
                    )}
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      )}

      {/* Pagination */}
      {totalPages > 1 && (
        <div className="flex items-center justify-between">
          <p className="text-sm text-gray-600">
            Pagina {page} de {totalPages} ({totalCount} notificacoes)
          </p>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setPage(prev => prev - 1)}
              disabled={!hasPreviousPage || isLoading}
              className="inline-flex items-center gap-1 px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft className="h-4 w-4" />
              Anterior
            </button>
            <button
              onClick={() => setPage(prev => prev + 1)}
              disabled={!hasNextPage || isLoading}
              className="inline-flex items-center gap-1 px-3 py-2 text-sm border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Proxima
              <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        </div>
      )}

      {/* Info */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Sobre as notificações</p>
            <p className="text-blue-700">
              Voce recebe notificacoes sobre eleicoes, prazos e atualizacoes importantes.
              Configure suas preferencias de notificacao no seu perfil.
            </p>
            <Link
              to="/eleitor/perfil"
              className="inline-flex items-center gap-1 mt-2 text-blue-800 font-medium hover:underline"
            >
              Gerenciar preferencias
              <ChevronRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}
