import { useState, useEffect, useCallback } from 'react'
import { Link } from 'react-router-dom'
import {
  Vote,
  CheckCircle,
  Download,
  Eye,
  Calendar,
  Clock,
  Shield,
  Loader2,
  History,
  AlertCircle,
  RefreshCw,
} from 'lucide-react'
import { votacaoService } from '../../services/votacao'
import { extractApiError } from '../../services/api'

// Types matching HistoricoVotoDto from the backend
interface HistoricoVoto {
  eleicaoId: string
  eleicaoNome: string
  anoEleicao: number
  dataVoto: string
  hashComprovante: string
}

export function MeusVotosPage() {
  const [votos, setVotos] = useState<HistoricoVoto[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [actionError, setActionError] = useState<string | null>(null)
  const [isVerifying, setIsVerifying] = useState(false)
  const [verificationCode, setVerificationCode] = useState('')
  const [verificationResult, setVerificationResult] = useState<'success' | 'error' | null>(null)
  const [verificationMessage, setVerificationMessage] = useState('')
  const [downloadingId, setDownloadingId] = useState<string | null>(null)

  const fetchHistorico = useCallback(async () => {
    setLoading(true)
    setError(null)
    setActionError(null)
    try {
      const data = await votacaoService.getHistoricoVotos()
      // Map the response to our interface (backend returns camelCase via JSON serialization)
      const mapped: HistoricoVoto[] = (data as unknown as HistoricoVoto[]).map((item) => ({
        eleicaoId: item.eleicaoId,
        eleicaoNome: item.eleicaoNome,
        anoEleicao: item.anoEleicao ?? new Date(item.dataVoto).getFullYear(),
        dataVoto: item.dataVoto,
        hashComprovante: item.hashComprovante,
      }))
      setVotos(mapped)
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message)
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    fetchHistorico()
  }, [fetchHistorico])

  const handleVerify = async () => {
    if (!verificationCode.trim()) return

    setIsVerifying(true)
    setVerificationResult(null)
    setVerificationMessage('')
    try {
      const result = await votacaoService.validarComprovante(verificationCode, verificationCode)
      if (result.valido) {
        setVerificationResult('success')
        setVerificationMessage(result.mensagem || 'Seu voto foi registrado com sucesso no sistema.')
      } else {
        setVerificationResult('error')
        setVerificationMessage(result.mensagem || 'Codigo nao encontrado. Verifique se foi digitado corretamente.')
      }
    } catch (err) {
      const apiError = extractApiError(err)
      setVerificationResult('error')
      setVerificationMessage(apiError.message || 'Nao foi possivel validar o comprovante agora.')
    } finally {
      setIsVerifying(false)
    }
  }

  const handleDownloadComprovante = async (voto: HistoricoVoto) => {
    setDownloadingId(voto.eleicaoId)
    setActionError(null)
    try {
      const blob = await votacaoService.downloadComprovante(voto.eleicaoId)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `comprovante-${voto.eleicaoNome.replace(/\s+/g, '-').toLowerCase()}.pdf`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch {
      setActionError('Nao foi possivel baixar o comprovante agora. Tente novamente em instantes.')
    } finally {
      setDownloadingId(null)
    }
  }

  const currentYear = new Date().getFullYear()
  const currentYearVotes = votos.filter(v => {
    const voteYear = v.anoEleicao || new Date(v.dataVoto).getFullYear()
    return voteYear === currentYear
  })
  const previousVotes = votos.filter(v => {
    const voteYear = v.anoEleicao || new Date(v.dataVoto).getFullYear()
    return voteYear < currentYear
  })

  const oldestYear = votos.length > 0
    ? Math.min(...votos.map(v => v.anoEleicao || new Date(v.dataVoto).getFullYear()))
    : null

  // Loading state
  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Votos</h1>
          <p className="text-gray-600 mt-1">Historico de participacao nas eleicoes</p>
        </div>
        <div className="flex items-center justify-center py-16">
          <div className="flex flex-col items-center gap-3">
            <Loader2 className="h-8 w-8 animate-spin text-primary" />
            <p className="text-gray-500">Carregando historico de votos...</p>
          </div>
        </div>
      </div>
    )
  }

  // Error state
  if (error) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Votos</h1>
          <p className="text-gray-600 mt-1">Historico de participacao nas eleicoes</p>
        </div>
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <AlertCircle className="h-12 w-12 text-red-400 mx-auto mb-4" />
          <p className="text-gray-700 font-medium mb-2">Erro ao carregar historico</p>
          <p className="text-gray-500 mb-6 text-sm">{error}</p>
          <button
            onClick={fetchHistorico}
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
          >
            <RefreshCw className="h-4 w-4" />
            Tentar Novamente
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Votos</h1>
        <p className="text-gray-600 mt-1">Historico de participacao nas eleicoes</p>
      </div>

      {actionError && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
          <AlertCircle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
          <p className="text-sm text-red-700">{actionError}</p>
        </div>
      )}

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-primary/10 rounded-lg">
              <Vote className="h-5 w-5 text-primary" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{votos.length}</p>
              <p className="text-sm text-gray-500">Total de Votos</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <CheckCircle className="h-5 w-5 text-green-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{currentYearVotes.length}</p>
              <p className="text-sm text-gray-500">Este Ano</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <History className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{previousVotes.length}</p>
              <p className="text-sm text-gray-500">Anos Anteriores</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-purple-100 rounded-lg">
              <Calendar className="h-5 w-5 text-purple-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{oldestYear ?? '-'}</p>
              <p className="text-sm text-gray-500">Primeira Votacao</p>
            </div>
          </div>
        </div>
      </div>

      {/* Verification Section */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Shield className="h-5 w-5 text-primary" />
          <h2 className="text-lg font-semibold text-gray-900">Verificar Voto</h2>
        </div>
        <p className="text-gray-600 text-sm mb-4">
          Digite o codigo de verificacao do seu comprovante para confirmar que seu voto foi registrado corretamente.
        </p>

        <div className="flex gap-3">
          <input
            type="text"
            value={verificationCode}
            onChange={(e) => {
              setVerificationCode(e.target.value.toUpperCase())
              setVerificationResult(null)
            }}
            placeholder="Ex: CAU-2024-XYZ12345"
            className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary uppercase font-mono"
          />
          <button
            onClick={handleVerify}
            disabled={isVerifying || !verificationCode.trim()}
            className="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 flex items-center gap-2"
          >
            {isVerifying ? (
              <>
                <Loader2 className="h-4 w-4 animate-spin" />
                Verificando...
              </>
            ) : (
              'Verificar'
            )}
          </button>
        </div>

        {verificationResult === 'success' && (
          <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <CheckCircle className="h-5 w-5 text-green-600 flex-shrink-0" />
            <div>
              <p className="font-medium text-green-800">Voto verificado!</p>
              <p className="text-sm text-green-700">{verificationMessage}</p>
            </div>
          </div>
        )}

        {verificationResult === 'error' && (
          <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
            <Shield className="h-5 w-5 text-red-600 flex-shrink-0" />
            <div>
              <p className="font-medium text-red-800">Codigo nao encontrado</p>
              <p className="text-sm text-red-700">{verificationMessage}</p>
            </div>
          </div>
        )}
      </div>

      {/* Current Year Votes */}
      {currentYearVotes.length > 0 && (
        <div>
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Eleicoes {currentYear}</h2>
          <div className="space-y-4">
            {currentYearVotes.map(voto => (
              <VotoCard
                key={voto.eleicaoId}
                voto={voto}
                onDownload={handleDownloadComprovante}
                isDownloading={downloadingId === voto.eleicaoId}
              />
            ))}
          </div>
        </div>
      )}

      {/* Previous Votes */}
      {previousVotes.length > 0 && (
        <div>
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Eleicoes Anteriores</h2>
          <div className="space-y-4">
            {previousVotes.map(voto => (
              <VotoCard
                key={voto.eleicaoId}
                voto={voto}
                onDownload={handleDownloadComprovante}
                isDownloading={downloadingId === voto.eleicaoId}
              />
            ))}
          </div>
        </div>
      )}

      {/* Empty State */}
      {votos.length === 0 && (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Vote className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Voce ainda nao participou de nenhuma eleicao</p>
          <Link
            to="/eleitor/votacao"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
          >
            Ver Eleicoes Disponiveis
          </Link>
        </div>
      )}
    </div>
  )
}

// Voto Card Component
interface VotoCardProps {
  voto: HistoricoVoto
  onDownload: (voto: HistoricoVoto) => void
  isDownloading: boolean
}

function VotoCard({ voto, onDownload, isDownloading }: VotoCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        {/* Icon */}
        <div className="p-3 bg-green-100 rounded-lg w-fit">
          <CheckCircle className="h-6 w-6 text-green-600" />
        </div>

        {/* Info */}
        <div className="flex-1">
          <h3 className="font-semibold text-gray-900">{voto.eleicaoNome}</h3>
          <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
            <span className="flex items-center gap-1">
              <Calendar className="h-4 w-4" />
              {new Date(voto.dataVoto).toLocaleDateString('pt-BR')}
            </span>
            <span className="flex items-center gap-1">
              <Clock className="h-4 w-4" />
              {new Date(voto.dataVoto).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
            </span>
          </div>
          {voto.hashComprovante && (
            <p className="mt-2 text-sm">
              <span className="text-gray-500">Codigo: </span>
              <span className="font-mono font-medium text-gray-900">{voto.hashComprovante}</span>
            </p>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center gap-2">
          <Link
            to={`/eleitor/votacao/${voto.eleicaoId}/comprovante`}
            className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
            title="Ver Comprovante"
          >
            <Eye className="h-5 w-5" />
          </Link>
          <button
            onClick={() => onDownload(voto)}
            disabled={isDownloading}
            className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors disabled:opacity-50"
            title="Baixar Comprovante"
          >
            {isDownloading ? (
              <Loader2 className="h-5 w-5 animate-spin" />
            ) : (
              <Download className="h-5 w-5" />
            )}
          </button>
        </div>
      </div>
    </div>
  )
}
