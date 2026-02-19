import { useState, useEffect, useCallback } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Vote,
  User,
  Check,
  AlertTriangle,
  Loader2,
  Info,
  ChevronDown,
  ChevronUp,
  Clock,
  X,
  Ban,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { useVotacaoStore, ChapaOpcao } from '@/stores/votacao'
import { votacaoService, ChapaVotacao } from '@/services/votacao'
import { extractApiError } from '@/services/api'

// Color configurations for different chapa numbers
const colorConfig: Record<number, { bg: string; light: string; text: string; border: string; hover: string; ring: string }> = {
  1: { bg: 'bg-blue-600', light: 'bg-blue-100', text: 'text-blue-600', border: 'border-blue-600', hover: 'hover:border-blue-400', ring: 'ring-blue-500' },
  2: { bg: 'bg-green-600', light: 'bg-green-100', text: 'text-green-600', border: 'border-green-600', hover: 'hover:border-green-400', ring: 'ring-green-500' },
  3: { bg: 'bg-purple-600', light: 'bg-purple-100', text: 'text-purple-600', border: 'border-purple-600', hover: 'hover:border-purple-400', ring: 'ring-purple-500' },
  4: { bg: 'bg-orange-600', light: 'bg-orange-100', text: 'text-orange-600', border: 'border-orange-600', hover: 'hover:border-orange-400', ring: 'ring-orange-500' },
  5: { bg: 'bg-red-600', light: 'bg-red-100', text: 'text-red-600', border: 'border-red-600', hover: 'hover:border-red-400', ring: 'ring-red-500' },
  6: { bg: 'bg-teal-600', light: 'bg-teal-100', text: 'text-teal-600', border: 'border-teal-600', hover: 'hover:border-teal-400', ring: 'ring-teal-500' },
  7: { bg: 'bg-indigo-600', light: 'bg-indigo-100', text: 'text-indigo-600', border: 'border-indigo-600', hover: 'hover:border-indigo-400', ring: 'ring-indigo-500' },
  8: { bg: 'bg-pink-600', light: 'bg-pink-100', text: 'text-pink-600', border: 'border-pink-600', hover: 'hover:border-pink-400', ring: 'ring-pink-500' },
}

const getColorConfig = (numero: number) => {
  return colorConfig[numero] || colorConfig[1]
}

export function VotacaoCedulaPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const navigate = useNavigate()

  // Stores
  const { voter, isAuthenticated } = useVoterStore()
  const {
    votoSelecionado,
    selecionarChapa,
    selecionarBranco,
    selecionarNulo,
    limparSelecao,
    tempoRestante,
    timerAtivo,
    decrementarTempo,
    setTempoRestante,
    setTimerAtivo,
    setError: setStoreError,
    error: storeError,
  } = useVotacaoStore()

  // Local state
  const [chapas, setChapas] = useState<ChapaVotacao[]>([])
  const [eleicaoNome, setEleicaoNome] = useState(voter?.eleicaoNome || 'Eleicao')
  const [expandedChapa, setExpandedChapa] = useState<string | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [loadError, setLoadError] = useState<string | null>(null)

  // Check authentication
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/votacao')
    }
  }, [isAuthenticated, navigate])

  const loadChapas = useCallback(async () => {
    if (!eleicaoId) return

    setIsLoading(true)
    setLoadError(null)
    setStoreError(null)

    try {
      const response = await votacaoService.getChapasVotacao(eleicaoId)
      setChapas(response)

      if (!response.length) {
        const message = 'Nao existem chapas disponiveis para esta eleicao no momento.'
        setLoadError(message)
        setStoreError(message)
      }

      try {
        const status = await votacaoService.getStatus(eleicaoId)
        if (status.tempoRestante) {
          setTempoRestante(status.tempoRestante)
          setTimerAtivo(true)
        } else {
          setTempoRestante(15 * 60)
          setTimerAtivo(true)
        }
      } catch {
        setTempoRestante(15 * 60)
        setTimerAtivo(true)
      }
    } catch (err) {
      const apiError = extractApiError(err)
      const message = apiError.message || 'Nao foi possivel carregar a cedula de votacao.'
      setLoadError(message)
      setStoreError(message)
      setChapas([])
      setTimerAtivo(false)
    } finally {
      setIsLoading(false)
    }
  }, [eleicaoId, setStoreError, setTempoRestante, setTimerAtivo])

  // Load chapas
  useEffect(() => {
    loadChapas()
  }, [loadChapas])

  // Timer countdown
  useEffect(() => {
    if (!timerAtivo || tempoRestante === null) return

    const interval = setInterval(() => {
      decrementarTempo()
    }, 1000)

    return () => clearInterval(interval)
  }, [timerAtivo, tempoRestante, decrementarTempo])

  // Check if session expired
  useEffect(() => {
    if (tempoRestante !== null && tempoRestante <= 0) {
      setTimerAtivo(false)
      setStoreError('Tempo de sessao expirado. Por favor, reinicie o processo de votacao.')
    }
  }, [tempoRestante, setTimerAtivo, setStoreError])

  // Prevent back navigation
  useEffect(() => {
    const handlePopState = (e: PopStateEvent) => {
      // Push current state again to prevent back navigation
      window.history.pushState(null, '', window.location.href)
    }

    window.history.pushState(null, '', window.location.href)
    window.addEventListener('popstate', handlePopState)

    return () => {
      window.removeEventListener('popstate', handlePopState)
    }
  }, [])

  const handleSelectChapa = (chapa: ChapaVotacao) => {
    selecionarChapa({
      id: chapa.id,
      numero: chapa.numero,
      nome: chapa.nome,
      sigla: chapa.sigla,
      logoUrl: chapa.logoUrl,
    })
  }

  const handleVotoBranco = () => {
    selecionarBranco()
  }

  const handleVotoNulo = () => {
    selecionarNulo()
  }

  const handleConfirmar = () => {
    if (votoSelecionado) {
      navigate(`/eleitor/votacao/${eleicaoId}/confirmacao`, {
        state: {
          eleicaoNome,
        },
      })
    }
  }

  const toggleExpand = (chapaId: string) => {
    setExpandedChapa(expandedChapa === chapaId ? null : chapaId)
  }

  const formatTempo = (segundos: number | null) => {
    if (segundos === null) return '--:--'
    const min = Math.floor(segundos / 60)
    const sec = segundos % 60
    return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`
  }

  const isSessionExpired = tempoRestante !== null && tempoRestante <= 0

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin text-primary mx-auto mb-4" />
          <p className="text-gray-500">Carregando cedula de votacao...</p>
        </div>
      </div>
    )
  }

  if (loadError) {
    return (
      <div className="max-w-2xl mx-auto">
        <div className="bg-red-50 border-2 border-red-300 rounded-lg p-8 text-center">
          <AlertTriangle className="h-16 w-16 text-red-500 mx-auto mb-4" />
          <h1 className="text-2xl font-bold text-red-800 mb-2">Falha ao carregar cedula</h1>
          <p className="text-red-600 mb-6">{loadError}</p>
          <button
            onClick={() => loadChapas()}
            className="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90"
          >
            Tentar novamente
          </button>
        </div>
      </div>
    )
  }

  if (isSessionExpired) {
    return (
      <div className="max-w-2xl mx-auto">
        <div className="bg-red-50 border-2 border-red-300 rounded-lg p-8 text-center">
          <AlertTriangle className="h-16 w-16 text-red-500 mx-auto mb-4" />
          <h1 className="text-2xl font-bold text-red-800 mb-2">Sessao Expirada</h1>
          <p className="text-red-600 mb-6">
            O tempo para votacao expirou. Por favor, reinicie o processo.
          </p>
          <Link
            to="/eleitor/votacao"
            className="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90"
          >
            <ArrowLeft className="h-5 w-5" />
            Voltar para eleicoes
          </Link>
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between gap-4">
        <div className="flex items-center gap-4">
          <Link
            to="/eleitor/votacao"
            className="p-2 hover:bg-gray-100 rounded-lg"
          >
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Cedula de Votacao</h1>
            <p className="text-gray-600">{eleicaoNome}</p>
          </div>
        </div>

        {/* Timer */}
        {tempoRestante !== null && (
          <div className={`flex items-center gap-2 px-4 py-2 rounded-lg ${
            tempoRestante < 60 ? 'bg-red-100 text-red-800' :
            tempoRestante < 300 ? 'bg-yellow-100 text-yellow-800' :
            'bg-gray-100 text-gray-800'
          }`}>
            <Clock className="h-5 w-5" />
            <span className="font-mono font-bold text-lg">{formatTempo(tempoRestante)}</span>
          </div>
        )}
      </div>

      {/* Instructions */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Instrucoes</p>
            <ul className="text-blue-700 mt-1 space-y-1">
              <li>Selecione uma chapa para votar, ou escolha Voto em Branco ou Voto Nulo.</li>
              <li>Clique em "Ver composicao" para ver os membros de cada chapa.</li>
              <li>Revise sua escolha com atencao antes de confirmar.</li>
              <li>Apos confirmar, seu voto nao podera ser alterado.</li>
            </ul>
          </div>
        </div>
      </div>

      {storeError && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
            <p className="text-sm text-red-700">{storeError}</p>
          </div>
        </div>
      )}

      {/* Chapas */}
      <div className="space-y-4">
        <h2 className="text-lg font-semibold text-gray-900">Escolha sua opcao:</h2>

        {chapas.map((chapa) => {
          const colors = getColorConfig(chapa.numero)
          const isSelected = votoSelecionado?.tipo === 'chapa' && votoSelecionado.chapaId === chapa.id
          const isExpanded = expandedChapa === chapa.id

          return (
            <div
              key={chapa.id}
              className={`bg-white rounded-lg shadow-sm border-2 transition-all ${
                isSelected
                  ? `${colors.border} ring-2 ring-offset-2 ${colors.ring}`
                  : `border-gray-200 ${colors.hover}`
              }`}
            >
              {/* Main Content */}
              <button
                onClick={() => handleSelectChapa(chapa)}
                className="w-full p-4 sm:p-6 text-left"
              >
                <div className="flex items-center gap-4">
                  {/* Number Badge */}
                  <div className={`w-14 h-14 ${colors.light} rounded-lg flex items-center justify-center flex-shrink-0`}>
                    <span className={`text-2xl font-bold ${colors.text}`}>{chapa.numero}</span>
                  </div>

                  {/* Info */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <h3 className="text-lg font-bold text-gray-900">{chapa.nome}</h3>
                      {chapa.sigla && (
                        <span className={`text-xs font-medium px-2 py-0.5 rounded ${colors.light} ${colors.text}`}>
                          {chapa.sigla}
                        </span>
                      )}
                    </div>
                    {chapa.slogan && (
                      <p className="text-gray-600 text-sm italic">"{chapa.slogan}"</p>
                    )}
                    <p className="text-gray-500 text-sm mt-1">
                      Presidente: {chapa.presidente}
                    </p>
                  </div>

                  {/* Selection Indicator */}
                  <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${
                    isSelected
                      ? `${colors.bg} border-transparent`
                      : 'border-gray-300'
                  }`}>
                    {isSelected && <Check className="h-4 w-4 text-white" />}
                  </div>
                </div>
              </button>

              {/* Expand Button */}
              <button
                onClick={(e) => {
                  e.stopPropagation()
                  toggleExpand(chapa.id)
                }}
                className="w-full px-4 sm:px-6 py-2 border-t flex items-center justify-center gap-2 text-sm text-gray-500 hover:bg-gray-50"
              >
                {isExpanded ? (
                  <>
                    <ChevronUp className="h-4 w-4" />
                    Ocultar composicao
                  </>
                ) : (
                  <>
                    <ChevronDown className="h-4 w-4" />
                    Ver composicao da chapa
                  </>
                )}
              </button>

              {/* Expanded Content */}
              {isExpanded && (
                <div className="px-4 sm:px-6 pb-4 border-t">
                  <div className="pt-4 space-y-3">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                        <User className="h-4 w-4 text-gray-400" />
                        <div>
                          <p className="text-xs text-gray-500">Presidente</p>
                          <p className="text-sm font-medium">{chapa.presidente}</p>
                        </div>
                      </div>
                      {chapa.vicePresidente && (
                        <div className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                          <User className="h-4 w-4 text-gray-400" />
                          <div>
                            <p className="text-xs text-gray-500">Vice-Presidente</p>
                            <p className="text-sm font-medium">{chapa.vicePresidente}</p>
                          </div>
                        </div>
                      )}
                    </div>
                    {chapa.membros && chapa.membros.length > 0 && (
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {chapa.membros.map((membro, idx) => (
                          <div key={idx} className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                            <User className="h-4 w-4 text-gray-400" />
                            <div>
                              <p className="text-xs text-gray-500">{membro.cargo}</p>
                              <p className="text-sm font-medium">{membro.nome}</p>
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          )
        })}

        {/* Voto em Branco */}
        <div
          className={`bg-white rounded-lg shadow-sm border-2 transition-all cursor-pointer ${
            votoSelecionado?.tipo === 'branco'
              ? 'border-gray-600 ring-2 ring-offset-2 ring-gray-500'
              : 'border-gray-200 hover:border-gray-400'
          }`}
          onClick={handleVotoBranco}
        >
          <div className="p-4 sm:p-6">
            <div className="flex items-center gap-4">
              <div className="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span className="text-xl font-bold text-gray-400">B</span>
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-bold text-gray-500">Voto em Branco</h3>
                <p className="text-gray-400 text-sm">
                  Votar em branco significa nao escolher nenhuma chapa
                </p>
              </div>
              <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${
                votoSelecionado?.tipo === 'branco'
                  ? 'bg-gray-600 border-transparent'
                  : 'border-gray-300'
              }`}>
                {votoSelecionado?.tipo === 'branco' && <Check className="h-4 w-4 text-white" />}
              </div>
            </div>
          </div>
        </div>

        {/* Voto Nulo */}
        <div
          className={`bg-white rounded-lg shadow-sm border-2 transition-all cursor-pointer ${
            votoSelecionado?.tipo === 'nulo'
              ? 'border-red-600 ring-2 ring-offset-2 ring-red-500'
              : 'border-gray-200 hover:border-red-300'
          }`}
          onClick={handleVotoNulo}
        >
          <div className="p-4 sm:p-6">
            <div className="flex items-center gap-4">
              <div className="w-14 h-14 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <Ban className="h-6 w-6 text-red-400" />
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-bold text-red-500">Voto Nulo</h3>
                <p className="text-gray-400 text-sm">
                  Anular seu voto intencionalmente
                </p>
              </div>
              <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${
                votoSelecionado?.tipo === 'nulo'
                  ? 'bg-red-600 border-transparent'
                  : 'border-gray-300'
              }`}>
                {votoSelecionado?.tipo === 'nulo' && <Check className="h-4 w-4 text-white" />}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Warning */}
      {votoSelecionado && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
            <div className="text-sm">
              <p className="font-medium text-yellow-800">Atencao</p>
              <p className="text-yellow-700">
                Apos confirmar, seu voto nao podera ser alterado. Certifique-se de que sua escolha esta correta.
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Selection Summary */}
      {votoSelecionado && (
        <div className="bg-gray-50 border rounded-lg p-4">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Sua selecao atual:</p>
              <p className="font-bold text-gray-900">
                {votoSelecionado.tipo === 'chapa' && votoSelecionado.chapaNome}
                {votoSelecionado.tipo === 'branco' && 'Voto em Branco'}
                {votoSelecionado.tipo === 'nulo' && 'Voto Nulo'}
              </p>
            </div>
            <button
              onClick={limparSelecao}
              className="text-sm text-red-600 hover:text-red-800 flex items-center gap-1"
            >
              <X className="h-4 w-4" />
              Limpar
            </button>
          </div>
        </div>
      )}

      {/* Actions */}
      <div className="flex flex-col sm:flex-row gap-3 pt-4">
        <Link
          to="/eleitor/votacao"
          className="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 text-center"
        >
          Cancelar
        </Link>
        <button
          onClick={handleConfirmar}
          disabled={!votoSelecionado}
          className="flex-1 py-3 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <Vote className="h-5 w-5" />
          Confirmar Escolha
        </button>
      </div>
    </div>
  )
}
