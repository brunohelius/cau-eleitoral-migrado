import { useState, useEffect } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import {
  CheckCircle,
  FileText,
  Home,
  Shield,
  Clock,
  Search,
  Loader2,
  AlertTriangle,
  ExternalLink,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { votacaoService, ComprovanteVoto } from '@/services/votacao'
import { extractApiError } from '@/services/api'

export function JaVotouPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const navigate = useNavigate()

  // Stores
  const { voter, isAuthenticated, clearVoter } = useVoterStore()

  // Local state
  const [comprovante, setComprovante] = useState<ComprovanteVoto | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  // Verification state
  const [hashInput, setHashInput] = useState('')
  const [isVerifying, setIsVerifying] = useState(false)
  const [verificationResult, setVerificationResult] = useState<{
    válido: boolean
    mensagem: string
    dataVoto?: string
    eleicaoNome?: string
  } | null>(null)

  // Check authentication
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/votacao')
      return
    }
  }, [isAuthenticated, navigate])

  // Load comprovante
  useEffect(() => {
    const loadComprovante = async () => {
      if (!eleicaoId) return

      setIsLoading(true)
      try {
        const data = await votacaoService.getComprovante(eleicaoId)
        setComprovante(data)
      } catch (err) {
        const apiError = extractApiError(err)

        // If no vote found, voter hasn't voted
        if (apiError.code === 'NOT_FOUND') {
          navigate(`/eleitor/votacao/${eleicaoId}/cedula`)
          return
        }

        setError(apiError.message || 'Não foi possível carregar as informações do voto.')
      } finally {
        setIsLoading(false)
      }
    }

    loadComprovante()
  }, [eleicaoId, navigate])

  const handleVerifyVote = async () => {
    if (!hashInput.trim()) return

    setIsVerifying(true)
    setVerificationResult(null)

    try {
      const result = await votacaoService.verificarVotoHash(hashInput.trim())
      setVerificationResult(result)
    } catch (err) {
      const apiError = extractApiError(err)
      setVerificationResult({
        válido: false,
        mensagem: apiError.message || 'Não foi possível verificar o codigo.',
      })
    } finally {
      setIsVerifying(false)
    }
  }

  const handleLogout = () => {
    clearVoter()
    navigate('/votacao')
  }

  // Loading state
  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin text-primary mx-auto mb-4" />
          <p className="text-gray-500">Verificando seu voto...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Header */}
      <div className="text-center py-8">
        <div className="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
          <CheckCircle className="h-10 w-10 text-blue-600" />
        </div>
        <h1 className="text-3xl font-bold text-gray-900">Você já votou!</h1>
        <p className="text-gray-600 mt-2">
          Seu voto nesta eleição já foi registrado
        </p>
      </div>

      {/* Error Alert */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
            <div>
              <p className="font-medium text-red-800">Erro</p>
              <p className="text-red-600 text-sm mt-1">{error}</p>
            </div>
          </div>
        </div>
      )}

      {/* Vote Info Card */}
      {comprovante && (
        <div className="bg-white rounded-xl shadow-sm border overflow-hidden">
          <div className="bg-green-50 p-4 border-b border-green-100">
            <div className="flex items-center gap-3">
              <CheckCircle className="h-6 w-6 text-green-600" />
              <div>
                <p className="font-semibold text-green-800">Voto Confirmado</p>
                <p className="text-sm text-green-600">
                  Seu voto foi registrado com sucesso
                </p>
              </div>
            </div>
          </div>

          <div className="p-6 space-y-4">
            <div className="flex items-center justify-between py-2 border-b">
              <span className="text-gray-500 flex items-center gap-2">
                <FileText className="h-4 w-4" />
                Eleicao
              </span>
              <span className="font-medium text-gray-900">{comprovante.eleicaoNome}</span>
            </div>

            <div className="flex items-center justify-between py-2 border-b">
              <span className="text-gray-500 flex items-center gap-2">
                <Clock className="h-4 w-4" />
                Data/Hora do Voto
              </span>
              <span className="font-medium text-gray-900">
                {new Date(comprovante.dataHoraVoto).toLocaleDateString('pt-BR')} as{' '}
                {new Date(comprovante.dataHoraVoto).toLocaleTimeString('pt-BR')}
              </span>
            </div>

            <div className="flex items-center justify-between py-2">
              <span className="text-gray-500 flex items-center gap-2">
                <Shield className="h-4 w-4" />
                Protocolo
              </span>
              <span className="font-mono font-medium text-gray-900">{comprovante.protocolo}</span>
            </div>
          </div>

          {/* View Receipt Link */}
          <div className="border-t p-4 bg-gray-50">
            <Link
              to={`/eleitor/votacao/${eleicaoId}/comprovante`}
              className="flex items-center justify-center gap-2 text-primary hover:text-primary/80 font-medium"
            >
              <FileText className="h-4 w-4" />
              Ver comprovante completo
              <ExternalLink className="h-4 w-4" />
            </Link>
          </div>
        </div>
      )}

      {/* Verification Section */}
      <div className="bg-white rounded-xl shadow-sm border p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Verificar Voto</h2>
        <p className="text-sm text-gray-600 mb-4">
          Digite o codigo de verificacao ou hash do comprovante para confirmar que seu voto foi contabilizado.
        </p>

        <div className="flex gap-3">
          <input
            type="text"
            value={hashInput}
            onChange={(e) => {
              setHashInput(e.target.value)
              setVerificationResult(null)
            }}
            placeholder="Digite o codigo de verificacao"
            className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          />
          <button
            onClick={handleVerifyVote}
            disabled={isVerifying || !hashInput.trim()}
            className="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          >
            {isVerifying ? (
              <Loader2 className="h-4 w-4 animate-spin" />
            ) : (
              <Search className="h-4 w-4" />
            )}
            Verificar
          </button>
        </div>

        {/* Verification Result */}
        {verificationResult && (
          <div className={`mt-4 p-4 rounded-lg ${
            verificationResult.valido
              ? 'bg-green-50 border border-green-200'
              : 'bg-red-50 border border-red-200'
          }`}>
            <div className="flex items-start gap-3">
              {verificationResult.valido ? (
                <CheckCircle className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
              ) : (
                <AlertTriangle className="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
              )}
              <div>
                <p className={`font-medium ${
                  verificationResult.válido ? 'text-green-800' : 'text-red-800'
                }`}>
                  {verificationResult.valido ? 'Voto Verificado' : 'Verificacao Falhou'}
                </p>
                <p className={`text-sm mt-1 ${
                  verificationResult.válido ? 'text-green-700' : 'text-red-600'
                }`}>
                  {verificationResult.mensagem}
                </p>
                {verificationResult.valido && verificationResult.dataVoto && (
                  <p className="text-sm text-green-700 mt-1">
                    Votado em: {new Date(verificationResult.dataVoto).toLocaleString('pt-BR')}
                  </p>
                )}
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Info Box */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 className="font-semibold text-blue-800 mb-2">Informações Importantes</h3>
        <ul className="text-sm text-blue-700 space-y-1 list-disc list-inside">
          <li>Cada eleitor pode votar apenas uma vez por eleição</li>
          <li>Seu voto e sigiloso - ninguem pode saber em quem você votou</li>
          <li>Guarde seu comprovante para futuras verificacoes</li>
          <li>Os resultados serao divulgados após o encerramento da votação</li>
        </ul>
      </div>

      {/* Security Info */}
      <div className="bg-green-50 border border-green-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Shield className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-green-800">Seu voto esta seguro</p>
            <p className="text-green-700">
              O sistema utiliza criptografia de ponta a ponta. A verificacao do voto confirma
              apenas que foi contabilizado, sem revelar sua escolha.
            </p>
          </div>
        </div>
      </div>

      {/* Navigation */}
      <div className="flex flex-col sm:flex-row gap-3 pt-4">
        <Link
          to="/eleitor/votacao"
          className="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 text-center"
        >
          Ver outras eleições
        </Link>
        <button
          onClick={handleLogout}
          className="flex-1 py-3 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 flex items-center justify-center gap-2"
        >
          <Home className="h-5 w-5" />
          Encerrar Sessão
        </button>
      </div>
    </div>
  )
}
