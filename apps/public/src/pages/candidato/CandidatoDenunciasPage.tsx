import { useState, useEffect, useCallback } from 'react'
import { Link } from 'react-router-dom'
import {
  AlertTriangle,
  Clock,
  CheckCircle,
  XCircle,
  Eye,
  Shield,
  ChevronRight,
  Loader2,
  Info,
  RefreshCw,
} from 'lucide-react'
import { useCandidatoStore } from '../../stores/candidato'
import api, { extractApiError } from '../../services/api'
import { setTokenType } from '../../services/api'
import {
  type Denuncia,
  getStatusDenunciaLabel,
  getStatusDenunciaColor,
  getTipoDenunciaLabel,
  StatusDenuncia,
  StatusDefesa,
} from '../../services/denuncias'

export function CandidatoDenunciasPage() {
  const candidato = useCandidatoStore((s) => s.candidato)
  const [denuncias, setDenuncias] = useState<Denuncia[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchDenuncias = useCallback(async () => {
    if (!candidato?.chapaId) {
      setDenuncias([])
      setIsLoading(false)
      return
    }

    setIsLoading(true)
    setError(null)
    try {
      setTokenType('candidate')
      const response = await api.get<Denuncia[]>(`/denuncia/chapa/${candidato.chapaId}`)
      setDenuncias(response.data || [])
    } catch (err) {
      const apiErr = extractApiError(err)
      // 404 or 403 means no denuncias or endpoint not available for this role
      if (apiErr.message.includes('404') || apiErr.message.includes('403') || apiErr.message.includes('nao encontrad')) {
        setDenuncias([])
      } else {
        setError(apiErr.message)
      }
    } finally {
      setIsLoading(false)
    }
  }, [candidato?.chapaId])

  useEffect(() => {
    fetchDenuncias()
  }, [fetchDenuncias])

  const pendentes = denuncias.filter(d =>
    d.status === StatusDenuncia.AguardandoDefesa && d.statusDefesa === StatusDefesa.AguardandoDefesa
  )
  const emAndamento = denuncias.filter(d =>
    d.status === StatusDenuncia.EmAnalise ||
    d.status === StatusDenuncia.DefesaApresentada ||
    d.status === StatusDenuncia.AguardandoJulgamento ||
    d.status === StatusDenuncia.Recebida ||
    (d.status === StatusDenuncia.AguardandoDefesa && d.statusDefesa === StatusDefesa.Apresentada)
  )
  const finalizadas = denuncias.filter(d =>
    d.status === StatusDenuncia.Procedente ||
    d.status === StatusDenuncia.Improcedente ||
    d.status === StatusDenuncia.ParcialmenteProcedente ||
    d.status === StatusDenuncia.Arquivada ||
    d.status === StatusDenuncia.Julgada
  )

  const getDaysRemaining = (prazo: string) => {
    const diff = new Date(prazo).getTime() - new Date().getTime()
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
  }

  const getStatusColor = (status: StatusDenuncia): string => {
    const color = getStatusDenunciaColor(status)
    const colorMap: Record<string, string> = {
      blue: 'text-blue-600 bg-blue-100',
      yellow: 'text-yellow-600 bg-yellow-100',
      green: 'text-green-600 bg-green-100',
      red: 'text-red-600 bg-red-100',
      purple: 'text-purple-600 bg-purple-100',
      orange: 'text-orange-600 bg-orange-100',
      gray: 'text-gray-600 bg-gray-100',
    }
    return colorMap[color] || 'text-gray-600 bg-gray-100'
  }

  const getStatusIcon = (status: StatusDenuncia) => {
    if (status === StatusDenuncia.Procedente || status === StatusDenuncia.Improcedente ||
        status === StatusDenuncia.ParcialmenteProcedente || status === StatusDenuncia.Julgada ||
        status === StatusDenuncia.Arquivada) {
      return status === StatusDenuncia.Improcedente ? CheckCircle : XCircle
    }
    return Clock
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando denúncias...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64 gap-4">
        <AlertTriangle className="h-12 w-12 text-red-500" />
        <p className="text-gray-700">{error}</p>
        <button
          onClick={fetchDenuncias}
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          <RefreshCw className="h-4 w-4" />
          Tentar novamente
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Denúncias</h1>
        <p className="text-gray-600 mt-1">Acompanhe as denúncias registradas contra sua chapa</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-gray-900">{denuncias.length}</p>
          <p className="text-sm text-gray-500">Total</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">{pendentes.length}</p>
          <p className="text-sm text-gray-500">Aguardando Defesa</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-blue-600">{emAndamento.length}</p>
          <p className="text-sm text-gray-500">Em Andamento</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">{finalizadas.length}</p>
          <p className="text-sm text-gray-500">Finalizadas</p>
        </div>
      </div>

      {/* Pendentes - Alert */}
      {pendentes.length > 0 && (
        <div className="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-6 w-6 text-yellow-600 flex-shrink-0" />
            <div>
              <p className="font-semibold text-yellow-800">
                Voce tem {pendentes.length} denuncia(s) aguardando defesa!
              </p>
              <p className="text-sm text-yellow-700 mt-1">
                Envie sua defesa dentro do prazo para garantir seus direitos.
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Denuncias List */}
      {denuncias.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Shield className="h-12 w-12 text-green-500 mx-auto mb-4" />
          <p className="text-gray-500">Nenhuma denúncia registrada contra sua chapa</p>
        </div>
      ) : (
        <div className="space-y-4">
          {denuncias.map(denuncia => {
            const statusLabel = getStatusDenunciaLabel(denuncia.status)
            const statusColor = getStatusColor(denuncia.status)
            const StatusIcon = getStatusIcon(denuncia.status)
            const tipoLabel = getTipoDenunciaLabel(denuncia.tipo)
            const daysRemaining = denuncia.prazoDefesa ? getDaysRemaining(denuncia.prazoDefesa) : null
            const isAguardandoDefesa = denuncia.status === StatusDenuncia.AguardandoDefesa &&
                                       denuncia.statusDefesa === StatusDefesa.AguardandoDefesa
            const defesaEnviada = denuncia.statusDefesa === StatusDefesa.Apresentada

            return (
              <div key={denuncia.id} className="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div className="p-4 sm:p-6">
                  <div className="flex flex-col lg:flex-row lg:items-start gap-4">
                    {/* Icon */}
                    <div className="p-3 bg-gray-100 rounded-lg w-fit">
                      <AlertTriangle className="h-6 w-6 text-gray-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                          {tipoLabel}
                        </span>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${statusColor}`}>
                          <StatusIcon className="h-3 w-3" />
                          {statusLabel}
                        </span>
                        {denuncia.protocolo && (
                          <span className="text-xs text-gray-500">
                            Protocolo: {denuncia.protocolo}
                          </span>
                        )}
                      </div>

                      <h3 className="font-semibold text-gray-900">
                        {tipoLabel} - {denuncia.descricao.substring(0, 80)}
                        {denuncia.descricao.length > 80 ? '...' : ''}
                      </h3>
                      <p className="text-gray-600 text-sm mt-1 line-clamp-2">{denuncia.descricao}</p>

                      <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                        <span>Registrada em: {new Date(denuncia.createdAt).toLocaleDateString('pt-BR')}</span>

                        {daysRemaining !== null && isAguardandoDefesa && (
                          <span className={`font-medium ${daysRemaining <= 2 ? 'text-red-600' : 'text-yellow-600'}`}>
                            {daysRemaining === 0 ? 'Ultimo dia!' : `${daysRemaining} dias restantes`}
                          </span>
                        )}

                        {defesaEnviada && (
                          <span className="flex items-center gap-1 text-green-600">
                            <CheckCircle className="h-4 w-4" />
                            Defesa enviada
                          </span>
                        )}
                      </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-2">
                      <Link
                        to={`/candidato/denuncias/${denuncia.id}`}
                        className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                        title="Ver detalhes"
                      >
                        <Eye className="h-5 w-5" />
                      </Link>

                      {isAguardandoDefesa && (
                        <Link
                          to={`/candidato/defesas/nova?denuncia=${denuncia.id}`}
                          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 text-sm"
                        >
                          <Shield className="h-4 w-4" />
                          Enviar Defesa
                        </Link>
                      )}
                    </div>
                  </div>
                </div>

                {/* Result for finalized */}
                {denuncia.status === StatusDenuncia.Procedente && (
                  <div className="px-4 sm:px-6 py-3 bg-red-50 border-t">
                    <div className="flex items-center gap-2 text-sm text-red-800">
                      <XCircle className="h-4 w-4" />
                      <span>Denúncia procedente. Você pode interpor recurso.</span>
                      <Link
                        to="/candidato/recursos/novo"
                        className="ml-auto text-red-700 font-medium hover:underline flex items-center gap-1"
                      >
                        Interpor Recurso
                        <ChevronRight className="h-4 w-4" />
                      </Link>
                    </div>
                  </div>
                )}

                {denuncia.status === StatusDenuncia.Improcedente && (
                  <div className="px-4 sm:px-6 py-3 bg-green-50 border-t">
                    <div className="flex items-center gap-2 text-sm text-green-800">
                      <CheckCircle className="h-4 w-4" />
                      <span>Denúncia improcedente. Sua defesa foi aceita.</span>
                    </div>
                  </div>
                )}
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
            <p className="font-medium text-blue-800">Sobre as Denúncias</p>
            <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
              <li>Você tem direito a apresentar defesa dentro do prazo estabelecido</li>
              <li>Em caso de deferimento, e possível interpor recurso</li>
              <li>Todas as decisoes são publicadas oficialmente</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}
