import { useState } from 'react'
import { Link } from 'react-router-dom'
import {
  ArrowLeft,
  Search,
  Clock,
  CheckCircle,
  AlertCircle,
  Loader2,
  FileText,
  Calendar,
  RefreshCw,
} from 'lucide-react'
import api, { extractApiError } from '@/services/api'

interface ConsultaResultado {
  protocolo: string
  status: string
  dataEnvio: string
  ultimaAtualizacao: string
}

// Status configuration for visual feedback
const statusConfig: Record<string, { label: string; color: string; icon: typeof CheckCircle }> = {
  Recebida: { label: 'Recebida', color: 'bg-blue-100 text-blue-800', icon: Clock },
  EmAnalise: { label: 'Em Análise', color: 'bg-yellow-100 text-yellow-800', icon: Search },
  AdmissibilidadeAceita: { label: 'Admissibilidade Aceita', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  AdmissibilidadeRejeitada: { label: 'Admissibilidade Rejeitada', color: 'bg-red-100 text-red-800', icon: AlertCircle },
  AguardandoDefesa: { label: 'Aguardando Defesa', color: 'bg-purple-100 text-purple-800', icon: Clock },
  DefesaApresentada: { label: 'Defesa Apresentada', color: 'bg-indigo-100 text-indigo-800', icon: FileText },
  AguardandoJulgamento: { label: 'Aguardando Julgamento', color: 'bg-orange-100 text-orange-800', icon: Clock },
  Julgada: { label: 'Julgada', color: 'bg-gray-100 text-gray-800', icon: CheckCircle },
  Procedente: { label: 'Procedente', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  Improcedente: { label: 'Improcedente', color: 'bg-gray-100 text-gray-800', icon: AlertCircle },
  ParcialmenteProcedente: { label: 'Parcialmente Procedente', color: 'bg-yellow-100 text-yellow-800', icon: CheckCircle },
  Arquivada: { label: 'Arquivada', color: 'bg-gray-100 text-gray-800', icon: FileText },
  AguardandoRecurso: { label: 'Aguardando Recurso', color: 'bg-orange-100 text-orange-800', icon: Clock },
  RecursoApresentado: { label: 'Recurso Apresentado', color: 'bg-indigo-100 text-indigo-800', icon: FileText },
  RecursoJulgado: { label: 'Recurso Julgado', color: 'bg-gray-100 text-gray-800', icon: CheckCircle },
}

export function ConsultaProtocoloPage() {
  const [protocolo, setProtocolo] = useState('')
  const [resultado, setResultado] = useState<ConsultaResultado | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [isLoading, setIsLoading] = useState(false)
  const [searched, setSearched] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!protocolo.trim()) {
      setError('Digite o numero do protocolo')
      return
    }

    setIsLoading(true)
    setError(null)
    setResultado(null)

    try {
      const response = await api.get<ConsultaResultado>(`/public/denuncias/protocolo/${protocolo.trim()}`)
      setResultado(response.data)
      setSearched(true)
    } catch (err) {
      const apiError = extractApiError(err)
      if (apiError.message.includes('nao encontrado')) {
        setError('Protocolo nao encontrado. Verifique o numero e tente novamente.')
      } else {
        setError(apiError.message)
      }
      setSearched(true)
    } finally {
      setIsLoading(false)
    }
  }

  const getStatusInfo = (status: string) => {
    return statusConfig[status] || { label: status, color: 'bg-gray-100 text-gray-800', icon: Clock }
  }

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  return (
    <div className="max-w-2xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <Link
          to="/"
          className="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
          Consultar Denuncia
        </h1>
        <p className="text-gray-600 mt-2">
          Digite o numero do protocolo para consultar o andamento da sua denuncia.
        </p>
      </div>

      {/* Search Form */}
      <div className="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="protocolo" className="block text-sm font-medium text-gray-700 mb-2">
              Numero do Protocolo
            </label>
            <div className="flex gap-3">
              <input
                type="text"
                id="protocolo"
                value={protocolo}
                onChange={(e) => {
                  setProtocolo(e.target.value.toUpperCase())
                  setError(null)
                }}
                placeholder="Ex: DEN-2024-00001"
                className={`flex-1 px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-mono ${
                  error ? 'border-red-500 bg-red-50' : 'border-gray-300'
                }`}
              />
              <button
                type="submit"
                disabled={isLoading}
                className="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isLoading ? (
                  <Loader2 className="h-5 w-5 animate-spin" />
                ) : (
                  <Search className="h-5 w-5" />
                )}
                Consultar
              </button>
            </div>
            {error && (
              <p className="mt-2 text-sm text-red-600">{error}</p>
            )}
          </div>
        </form>
      </div>

      {/* Result */}
      {searched && resultado && (
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="flex items-start justify-between mb-6">
            <div>
              <p className="text-sm text-gray-500 mb-1">Protocolo</p>
              <p className="text-xl font-mono font-bold text-gray-900">
                {resultado.protocolo}
              </p>
            </div>
            <button
              onClick={handleSubmit}
              className="p-2 text-gray-400 hover:text-gray-600 transition-colors"
              title="Atualizar status"
            >
              <RefreshCw className="h-5 w-5" />
            </button>
          </div>

          {/* Status */}
          <div className="mb-6">
            <p className="text-sm text-gray-500 mb-2">Status Atual</p>
            {(() => {
              const statusInfo = getStatusInfo(resultado.status)
              const StatusIcon = statusInfo.icon
              return (
                <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full ${statusInfo.color}`}>
                  <StatusIcon className="h-5 w-5" />
                  <span className="font-medium">{statusInfo.label}</span>
                </div>
              )
            })()}
          </div>

          {/* Dates */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-gray-100 rounded-lg">
                <Calendar className="h-5 w-5 text-gray-600" />
              </div>
              <div>
                <p className="text-sm text-gray-500">Data de Envio</p>
                <p className="font-medium text-gray-900">{formatDate(resultado.dataEnvio)}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <div className="p-2 bg-gray-100 rounded-lg">
                <Clock className="h-5 w-5 text-gray-600" />
              </div>
              <div>
                <p className="text-sm text-gray-500">Ultima Atualização</p>
                <p className="font-medium text-gray-900">{formatDate(resultado.ultimaAtualizacao)}</p>
              </div>
            </div>
          </div>

          {/* Info Note */}
          <div className="mt-6 p-4 bg-blue-50 rounded-lg">
            <p className="text-sm text-blue-700">
              <strong>Nota:</strong> O status da denuncia e atualizado conforme o andamento do
              processo de analise pela Comissao Eleitoral. Para mais informacoes, entre em
              contato atraves do suporte.
            </p>
          </div>
        </div>
      )}

      {/* Not Found Message */}
      {searched && !resultado && !isLoading && error && (
        <div className="bg-white rounded-lg shadow-sm border p-8 text-center">
          <AlertCircle className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhum resultado encontrado</p>
          <p className="text-sm text-gray-400 mt-2">
            Verifique se o numero do protocolo esta correto e tente novamente.
          </p>
        </div>
      )}

      {/* Quick Links */}
      <div className="mt-8 text-center">
        <p className="text-gray-600 mb-4">Ainda não registrou sua denúncia?</p>
        <Link
          to="/denuncias/nova"
          className="inline-flex items-center gap-2 text-primary font-medium hover:underline"
        >
          <FileText className="h-5 w-5" />
          Registrar Nova Denuncia
        </Link>
      </div>
    </div>
  )
}
