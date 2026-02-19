import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import {
  Vote,
  Calendar,
  Users,
  ChevronRight,
  CheckCircle,
  Clock,
  AlertTriangle,
  Loader2,
  Info,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { useVotacaoStore } from '@/stores/votacao'
import api, { extractApiError, setTokenType } from '@/services/api'

// Types
interface EleicaoDisponivel {
  id: string
  nome: string
  descricao: string
  dataInicioVotacao: string
  dataFimVotacao: string
  totalChapas: number
  emAndamento: boolean
  jaVotou: boolean
}

const statusFromApi = (e: EleicaoDisponivel): 'disponivel' | 'votado' | 'encerrada' | 'nao_iniciada' => {
  if (e.jaVotou) return 'votado'
  if (e.emAndamento) return 'disponivel'
  const now = new Date()
  const fim = new Date(e.dataFimVotacao)
  if (fim < now) return 'encerrada'
  return 'nao_iniciada'
}

const statusConfig = {
  disponivel: {
    label: 'Votação Aberta',
    color: 'bg-green-100 text-green-800',
    icon: Vote,
    action: 'Votar Agora',
  },
  votado: {
    label: 'Voto Registrado',
    color: 'bg-blue-100 text-blue-800',
    icon: CheckCircle,
    action: 'Ver Comprovante',
  },
  encerrada: {
    label: 'Encerrada',
    color: 'bg-gray-100 text-gray-800',
    icon: Clock,
    action: 'Ver Resultado',
  },
  nao_iniciada: {
    label: 'Em Breve',
    color: 'bg-yellow-100 text-yellow-800',
    icon: Clock,
    action: 'Ver Detalhes',
  },
}

export function VotacaoEleicaoPage() {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [eleicoes, setEleicoes] = useState<(EleicaoDisponivel & { status: ReturnType<typeof statusFromApi> })[]>([])

  // Get voter from store
  const { voter, isAuthenticated } = useVoterStore()
  const { resetVotacao } = useVotacaoStore()

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/votacao')
    }
  }, [isAuthenticated, navigate])

  // Reset voting state when viewing election list
  useEffect(() => {
    resetVotacao()
  }, [resetVotacao])

  // Fetch elections from API
  useEffect(() => {
    const fetchEleicoes = async () => {
      setIsLoading(true)
      setError(null)
      try {
        setTokenType('voter')
        const response = await api.get<EleicaoDisponivel[]>('/votacao/eleicoes-disponiveis')
        const mapped = response.data.map(e => ({
          ...e,
          status: statusFromApi(e),
        }))
        setEleicoes(mapped)
      } catch (err) {
        const apiError = extractApiError(err)
        setEleicoes([])
        setError(apiError.message || 'Nao foi possivel carregar as eleicoes disponiveis.')
      } finally {
        setIsLoading(false)
      }
    }

    if (isAuthenticated) {
      fetchEleicoes()
    }
  }, [isAuthenticated])

  // Get user info from voter store
  const user = {
    nome: voter?.nome || 'Eleitor',
    cau: voter?.registroCAU || 'A*****-*',
  }

  const handleVotar = (eleicao: typeof eleicoes[0]) => {
    if (eleicao.status === 'disponivel') {
      navigate(`/eleitor/votacao/${eleicao.id}/cedula`)
    } else if (eleicao.status === 'votado') {
      navigate(`/eleitor/votacao/${eleicao.id}/comprovante`)
    } else if (eleicao.status === 'encerrada') {
      navigate(`/eleicoes/${eleicao.id}/resultados`)
    } else {
      navigate(`/eleicoes/${eleicao.id}`)
    }
  }

  const getTimeRemaining = (dataFim: string) => {
    const end = new Date(dataFim).getTime()
    const now = new Date().getTime()
    const diff = end - now

    if (diff <= 0) return null

    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))

    if (days > 0) return `${days} dia(s) restante(s)`
    if (hours > 0) return `${hours} hora(s) restante(s)`
    return 'Ultimo dia!'
  }

  const eleicoesDisponiveis = eleicoes.filter(e => e.status === 'disponivel')
  const eleicoesVotadas = eleicoes.filter(e => e.status === 'votado')
  const outrasEleicoes = eleicoes.filter(e => !['disponivel', 'votado'].includes(e.status))

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando eleições...</span>
      </div>
    )
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
          Selecionar Eleicao
        </h1>
        <p className="text-gray-600 mt-1">
          Escolha uma eleicao para votar ou visualizar seu comprovante
        </p>
      </div>

      {/* User Info Card */}
      <div className="bg-white p-4 rounded-lg shadow-sm border">
        <div className="flex items-center gap-4">
          <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
            <Users className="h-6 w-6 text-primary" />
          </div>
          <div>
            <p className="font-medium text-gray-900">{user.nome}</p>
            <p className="text-sm text-gray-500">CAU: {user.cau}</p>
          </div>
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
            <div>
              <h3 className="font-semibold text-red-800">Falha ao carregar eleições</h3>
              <p className="text-red-700 mt-1 text-sm">{error}</p>
            </div>
          </div>
        </div>
      )}

      {/* Eleicoes Disponiveis */}
      {eleicoesDisponiveis.length > 0 && (
        <section>
          <div className="flex items-center gap-2 mb-4">
            <Vote className="h-5 w-5 text-green-600" />
            <h2 className="text-lg font-semibold text-gray-900">
              Eleicoes Disponiveis para Votacao
            </h2>
            <span className="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">
              {eleicoesDisponiveis.length}
            </span>
          </div>

          <div className="space-y-4">
            {eleicoesDisponiveis.map(eleicao => {
              const timeRemaining = getTimeRemaining(eleicao.dataFimVotacao)
              const config = statusConfig[eleicao.status]
              const StatusIcon = config.icon

              return (
                <div
                  key={eleicao.id}
                  className="bg-white rounded-lg shadow-sm border-2 border-green-200 overflow-hidden"
                >
                  <div className="p-6">
                    <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                      <div className="flex-1">
                        <div className="flex flex-wrap items-center gap-2 mb-2">
                          <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>
                            <StatusIcon className="h-3 w-3" />
                            {config.label}
                          </span>
                          {timeRemaining && (
                            <span className="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                              <Clock className="h-3 w-3" />
                              {timeRemaining}
                            </span>
                          )}
                        </div>

                        <h3 className="text-xl font-bold text-gray-900">{eleicao.nome}</h3>
                        {eleicao.descricao && (
                          <p className="text-gray-600 mt-1">{eleicao.descricao}</p>
                        )}

                        <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                          <span className="flex items-center gap-1">
                            <Calendar className="h-4 w-4" />
                            Ate {new Date(eleicao.dataFimVotacao).toLocaleDateString('pt-BR')} as {new Date(eleicao.dataFimVotacao).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
                          </span>
                          {eleicao.totalChapas > 0 && (
                            <span className="flex items-center gap-1">
                              <Users className="h-4 w-4" />
                              {eleicao.totalChapas} chapas
                            </span>
                          )}
                        </div>
                      </div>

                      <button
                        onClick={() => handleVotar(eleicao)}
                        className="bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 flex items-center gap-2 whitespace-nowrap"
                      >
                        <Vote className="h-5 w-5" />
                        {config.action}
                        <ChevronRight className="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        </section>
      )}

      {/* Alert for no available elections */}
      {!error && eleicoesDisponiveis.length === 0 && eleicoesVotadas.length === 0 && outrasEleicoes.length === 0 && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-6 w-6 text-yellow-600 flex-shrink-0" />
            <div>
              <h3 className="font-semibold text-yellow-800">Nenhuma eleição disponível</h3>
              <p className="text-yellow-700 mt-1">
                No momento nao ha eleicoes abertas para votacao. Verifique o calendario eleitoral para saber as proximas datas.
              </p>
              <Link
                to="/calendario"
                className="inline-flex items-center gap-1 mt-3 text-yellow-800 font-medium hover:underline"
              >
                Ver calendario eleitoral
                <ChevronRight className="h-4 w-4" />
              </Link>
            </div>
          </div>
        </div>
      )}

      {/* Eleicoes ja votadas */}
      {eleicoesVotadas.length > 0 && (
        <section>
          <div className="flex items-center gap-2 mb-4">
            <CheckCircle className="h-5 w-5 text-blue-600" />
            <h2 className="text-lg font-semibold text-gray-900">
              Votos Registrados
            </h2>
          </div>

          <div className="space-y-4">
            {eleicoesVotadas.map(eleicao => {
              const config = statusConfig[eleicao.status]
              const StatusIcon = config.icon

              return (
                <div
                  key={eleicao.id}
                  className="bg-white rounded-lg shadow-sm border overflow-hidden"
                >
                  <div className="p-6">
                    <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                      <div className="flex-1">
                        <div className="flex flex-wrap items-center gap-2 mb-2">
                          <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>
                            <StatusIcon className="h-3 w-3" />
                            {config.label}
                          </span>
                        </div>

                        <h3 className="text-xl font-bold text-gray-900">{eleicao.nome}</h3>
                      </div>

                      <button
                        onClick={() => handleVotar(eleicao)}
                        className="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 flex items-center gap-2 whitespace-nowrap"
                      >
                        {config.action}
                        <ChevronRight className="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        </section>
      )}

      {/* Outras Eleicoes */}
      {outrasEleicoes.length > 0 && (
        <section>
          <div className="flex items-center gap-2 mb-4">
            <Calendar className="h-5 w-5 text-gray-600" />
            <h2 className="text-lg font-semibold text-gray-900">
              Outras Eleicoes
            </h2>
          </div>

          <div className="space-y-4">
            {outrasEleicoes.map(eleicao => {
              const config = statusConfig[eleicao.status]
              const StatusIcon = config.icon

              return (
                <div
                  key={eleicao.id}
                  className="bg-white rounded-lg shadow-sm border overflow-hidden opacity-75"
                >
                  <div className="p-6">
                    <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                      <div className="flex-1">
                        <div className="flex flex-wrap items-center gap-2 mb-2">
                          <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${config.color}`}>
                            <StatusIcon className="h-3 w-3" />
                            {config.label}
                          </span>
                        </div>

                        <h3 className="text-lg font-bold text-gray-900">{eleicao.nome}</h3>

                        <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                          <span className="flex items-center gap-1">
                            <Calendar className="h-4 w-4" />
                            Inicio: {new Date(eleicao.dataInicioVotacao).toLocaleDateString('pt-BR')}
                          </span>
                        </div>
                      </div>

                      <button
                        onClick={() => handleVotar(eleicao)}
                        className="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 flex items-center gap-2 whitespace-nowrap"
                      >
                        {config.action}
                        <ChevronRight className="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        </section>
      )}

      {/* Info Box */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Lembre-se</p>
            <p className="text-blue-700">
              Cada eleitor pode votar apenas uma vez por eleicao. Apos confirmar seu voto, nao sera possivel altera-lo.
              Guarde seu comprovante de votacao.
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
