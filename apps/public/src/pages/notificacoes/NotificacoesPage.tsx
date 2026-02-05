import { useState } from 'react'
import { Link } from 'react-router-dom'
import {
  Bell,
  Vote,
  Calendar,
  AlertTriangle,
  Info,
  CheckCircle,
  Trash2,
  Check,
  ChevronRight,
  Filter,
  Loader2,
} from 'lucide-react'

// Types
interface Notificacao {
  id: string
  tipo: 'eleicao' | 'prazo' | 'alerta' | 'info' | 'sucesso'
  titulo: string
  mensagem: string
  data: string
  lida: boolean
  link?: string
  linkText?: string
}

// Mock data
const mockNotificacoes: Notificacao[] = [
  {
    id: '1',
    tipo: 'eleicao',
    titulo: 'Eleicao em andamento',
    mensagem: 'A votacao para a Eleicao Ordinaria CAU/SP 2024 esta aberta. Participe!',
    data: '2024-03-16T08:00:00',
    lida: false,
    link: '/eleitor/votacao',
    linkText: 'Votar agora',
  },
  {
    id: '2',
    tipo: 'prazo',
    titulo: 'Ultimo dia para votar',
    mensagem: 'Amanha e o ultimo dia para votar na Eleicao Ordinaria CAU/BR 2024. Nao deixe para depois!',
    data: '2024-03-15T10:00:00',
    lida: false,
    link: '/eleitor/votacao',
    linkText: 'Votar agora',
  },
  {
    id: '3',
    tipo: 'sucesso',
    titulo: 'Voto registrado com sucesso',
    mensagem: 'Seu voto na Eleicao Ordinaria CAU/BR 2024 foi registrado. Codigo de verificacao: CAU-2024-ABC67890',
    data: '2024-03-16T11:45:00',
    lida: true,
    link: '/eleitor/meus-votos',
    linkText: 'Ver meus votos',
  },
  {
    id: '4',
    tipo: 'info',
    titulo: 'Nova eleicao disponivel',
    mensagem: 'A Eleicao Suplementar CAU/RJ 2024 esta agendada para abril. Fique atento ao calendario.',
    data: '2024-03-10T09:00:00',
    lida: true,
    link: '/calendario',
    linkText: 'Ver calendario',
  },
  {
    id: '5',
    tipo: 'alerta',
    titulo: 'Atualizacao de dados necessaria',
    mensagem: 'Por favor, verifique se seus dados cadastrais estao atualizados para garantir seu direito ao voto.',
    data: '2024-03-05T14:30:00',
    lida: true,
    link: '/eleitor/perfil',
    linkText: 'Atualizar dados',
  },
  {
    id: '6',
    tipo: 'eleicao',
    titulo: 'Resultado divulgado',
    mensagem: 'O resultado da Eleicao Ordinaria CAU/SP 2021 foi divulgado. Confira os eleitos.',
    data: '2021-03-26T10:00:00',
    lida: true,
    link: '/eleicoes/3/resultados',
    linkText: 'Ver resultados',
  },
]

const tipoConfig = {
  eleicao: { icon: Vote, color: 'text-primary', bg: 'bg-primary/10' },
  prazo: { icon: Calendar, color: 'text-yellow-600', bg: 'bg-yellow-100' },
  alerta: { icon: AlertTriangle, color: 'text-red-600', bg: 'bg-red-100' },
  info: { icon: Info, color: 'text-blue-600', bg: 'bg-blue-100' },
  sucesso: { icon: CheckCircle, color: 'text-green-600', bg: 'bg-green-100' },
}

export function NotificacoesPage() {
  const [notificacoes, setNotificacoes] = useState<Notificacao[]>(mockNotificacoes)
  const [filter, setFilter] = useState<'todas' | 'nao_lidas'>('todas')
  const [isLoading] = useState(false)

  const filteredNotificacoes = notificacoes.filter(n =>
    filter === 'todas' ? true : !n.lida
  )

  const unreadCount = notificacoes.filter(n => !n.lida).length

  const markAsRead = (id: string) => {
    setNotificacoes(prev =>
      prev.map(n => n.id === id ? { ...n, lida: true } : n)
    )
  }

  const markAllAsRead = () => {
    setNotificacoes(prev =>
      prev.map(n => ({ ...n, lida: true }))
    )
  }

  const deleteNotification = (id: string) => {
    setNotificacoes(prev => prev.filter(n => n.id !== id))
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

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Notificacoes</h1>
          <p className="text-gray-600 mt-1">
            {unreadCount > 0 ? `${unreadCount} notificacao(es) nao lida(s)` : 'Todas as notificacoes foram lidas'}
          </p>
        </div>

        {unreadCount > 0 && (
          <button
            onClick={markAllAsRead}
            className="inline-flex items-center gap-2 px-4 py-2 text-sm text-primary hover:bg-primary/10 rounded-lg"
          >
            <Check className="h-4 w-4" />
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

      {/* Notifications List */}
      {isLoading ? (
        <div className="flex items-center justify-center py-12">
          <Loader2 className="h-8 w-8 animate-spin text-primary" />
        </div>
      ) : filteredNotificacoes.length === 0 ? (
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
          {filteredNotificacoes.map(notificacao => {
            const config = tipoConfig[notificacao.tipo]
            const Icon = config.icon

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
                        <p className="text-gray-400 text-xs mt-2">{formatDate(notificacao.data)}</p>
                      </div>

                      {/* Actions */}
                      <div className="flex items-center gap-1 flex-shrink-0">
                        {!notificacao.lida && (
                          <button
                            onClick={() => markAsRead(notificacao.id)}
                            className="p-2 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-lg"
                            title="Marcar como lida"
                          >
                            <Check className="h-4 w-4" />
                          </button>
                        )}
                        <button
                          onClick={() => deleteNotification(notificacao.id)}
                          className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                          title="Excluir"
                        >
                          <Trash2 className="h-4 w-4" />
                        </button>
                      </div>
                    </div>

                    {/* Link */}
                    {notificacao.link && (
                      <Link
                        to={notificacao.link}
                        onClick={() => markAsRead(notificacao.id)}
                        className="inline-flex items-center gap-1 mt-3 text-sm text-primary font-medium hover:underline"
                      >
                        {notificacao.linkText}
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

      {/* Info */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Sobre as notificacoes</p>
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
