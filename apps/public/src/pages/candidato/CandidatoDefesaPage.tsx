import { useState, useEffect, useCallback } from 'react'
import { Link } from 'react-router-dom'
import {
  Shield,
  Clock,
  CheckCircle,
  FileText,
  Eye,
  Download,
  Loader2,
  Info,
  AlertTriangle,
  RefreshCw,
} from 'lucide-react'
import { useCandidatoStore } from '../../stores/candidato'
import api, { extractApiError, setTokenType } from '../../services/api'
import {
  type Impugnacao,
  getStatusImpugnacaoLabel,
  getStatusImpugnacaoColor,
  StatusImpugnacao,
} from '../../services/impugnacaoService'

// Defesa type (from impugnacao defesas endpoint)
interface Defesa {
  id: string
  impugnacaoId: string
  texto: string
  dataApresentacao: string
  status?: string
  protocolo?: string
  arquivos?: { nome: string; url: string }[]
}

// Map a color name to tailwind class pairs
const colorToClass = (color: string): string => {
  const map: Record<string, string> = {
    blue: 'text-blue-600 bg-blue-100',
    yellow: 'text-yellow-600 bg-yellow-100',
    green: 'text-green-600 bg-green-100',
    red: 'text-red-600 bg-red-100',
    purple: 'text-purple-600 bg-purple-100',
    orange: 'text-orange-600 bg-orange-100',
    gray: 'text-gray-600 bg-gray-100',
  }
  return map[color] || 'text-gray-600 bg-gray-100'
}

const getStatusIcon = (status: StatusImpugnacao) => {
  if (status === StatusImpugnacao.PROCEDENTE || status === StatusImpugnacao.IMPROCEDENTE ||
      status === StatusImpugnacao.PARCIALMENTE_PROCEDENTE || status === StatusImpugnacao.JULGADA ||
      status === StatusImpugnacao.ARQUIVADA) {
    return status === StatusImpugnacao.IMPROCEDENTE ? CheckCircle : AlertTriangle
  }
  return Clock
}

export function CandidatoDefesaPage() {
  const candidato = useCandidatoStore((s) => s.candidato)
  const [impugnacoes, setImpugnacoes] = useState<Impugnacao[]>([])
  const [defesasMap, setDefesasMap] = useState<Record<string, Defesa[]>>({})
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [expandedId, setExpandedId] = useState<string | null>(null)

  const fetchData = useCallback(async () => {
    if (!candidato?.chapaId) {
      setImpugnacoes([])
      setIsLoading(false)
      return
    }

    setIsLoading(true)
    setError(null)
    try {
      setTokenType('candidate')
      // Fetch impugnacoes against this chapa
      const response = await api.get<Impugnacao[]>(`/impugnacao/chapa/${candidato.chapaId}`)
      const data = response.data || []
      setImpugnacoes(data)

      // For each impugnacao, try to fetch defesas
      const defesas: Record<string, Defesa[]> = {}
      for (const imp of data) {
        try {
          const defResponse = await api.get<Defesa[]>(`/impugnacao/${imp.id}/defesas`)
          defesas[imp.id] = defResponse.data || []
        } catch {
          defesas[imp.id] = []
        }
      }
      setDefesasMap(defesas)
    } catch (err) {
      const apiErr = extractApiError(err)
      if (apiErr.message.includes('404') || apiErr.message.includes('403') || apiErr.message.includes('nao encontrad')) {
        setImpugnacoes([])
      } else {
        setError(apiErr.message)
      }
    } finally {
      setIsLoading(false)
    }
  }, [candidato?.chapaId])

  useEffect(() => {
    fetchData()
  }, [fetchData])

  // Build a flat list of defesas for display
  const allDefesas = impugnacoes.flatMap(imp => {
    const defesas = defesasMap[imp.id] || []
    return defesas.map(d => ({
      ...d,
      impugnacaoTitulo: `${imp.tipoNome || 'Impugnacao'} - ${imp.descricao.substring(0, 60)}`,
      impugnacaoStatus: imp.status,
      impugnacaoStatusNome: imp.statusNome,
    }))
  })

  // Also include impugnacoes that have no defesa yet (aguardando)
  const aguardandoDefesa = impugnacoes.filter(imp =>
    (imp.status === StatusImpugnacao.AGUARDANDO_ALEGACOES ||
     imp.status === StatusImpugnacao.AGUARDANDO_CONTRA_ALEGACOES ||
     imp.status === StatusImpugnacao.RECEBIDA ||
     imp.status === StatusImpugnacao.EM_ANALISE) &&
    (!defesasMap[imp.id] || defesasMap[imp.id].length === 0)
  )

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando defesas...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64 gap-4">
        <AlertTriangle className="h-12 w-12 text-red-500" />
        <p className="text-gray-700">{error}</p>
        <button
          onClick={fetchData}
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          <RefreshCw className="h-4 w-4" />
          Tentar novamente
        </button>
      </div>
    )
  }

  const totalDefesas = allDefesas.length
  const totalImpugnacoes = impugnacoes.length

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Minhas Defesas</h1>
          <p className="text-gray-600 mt-1">Acompanhe suas defesas enviadas</p>
        </div>
        <Link
          to="/candidato/denuncias"
          className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
        >
          <AlertTriangle className="h-4 w-4" />
          Ver Denuncias
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-gray-900">{totalImpugnacoes}</p>
          <p className="text-sm text-gray-500">Impugnacoes</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-blue-600">{totalDefesas}</p>
          <p className="text-sm text-gray-500">Defesas Enviadas</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">{aguardandoDefesa.length}</p>
          <p className="text-sm text-gray-500">Aguardando Defesa</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">
            {impugnacoes.filter(i => i.status === StatusImpugnacao.IMPROCEDENTE).length}
          </p>
          <p className="text-sm text-gray-500">Improcedentes</p>
        </div>
      </div>

      {/* No data state */}
      {totalImpugnacoes === 0 && totalDefesas === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Shield className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Nenhuma defesa necessaria</p>
          <p className="text-sm text-gray-400">
            Nenhuma impugnacao registrada contra sua chapa.
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          {/* Impugnacoes with defesas */}
          {impugnacoes.map(imp => {
            const defesas = defesasMap[imp.id] || []
            const statusLabel = getStatusImpugnacaoLabel(imp.status)
            const statusColor = colorToClass(getStatusImpugnacaoColor(imp.status))
            const StatusIcon = getStatusIcon(imp.status)
            const isExpanded = expandedId === imp.id

            return (
              <div key={imp.id} className="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div className="p-4 sm:p-6">
                  <div className="flex flex-col lg:flex-row lg:items-start gap-4">
                    {/* Icon */}
                    <div className="p-3 bg-green-100 rounded-lg w-fit">
                      <Shield className="h-6 w-6 text-green-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${statusColor}`}>
                          <StatusIcon className="h-3 w-3" />
                          {statusLabel}
                        </span>
                        {imp.protocolo && (
                          <span className="text-xs text-gray-500">
                            Protocolo: {imp.protocolo}
                          </span>
                        )}
                      </div>

                      <h3 className="font-semibold text-gray-900">
                        {imp.tipoNome || 'Impugnacao'}
                      </h3>
                      <p className="text-gray-600 text-sm mt-1 line-clamp-2">{imp.descricao}</p>

                      <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>Registrada em: {new Date(imp.createdAt).toLocaleDateString('pt-BR')}</span>
                        <span>{defesas.length} defesa(s) enviada(s)</span>
                      </div>
                    </div>

                    {/* Actions */}
                    <button
                      onClick={() => setExpandedId(isExpanded ? null : imp.id)}
                      className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm"
                    >
                      <Eye className="h-4 w-4" />
                      {isExpanded ? 'Ocultar' : 'Ver Detalhes'}
                    </button>
                  </div>
                </div>

                {/* Expanded Content */}
                {isExpanded && (
                  <div className="px-4 sm:px-6 pb-6 border-t pt-4">
                    {defesas.length > 0 ? (
                      defesas.map((defesa, idx) => (
                        <div key={defesa.id || idx} className="mb-4">
                          <h4 className="text-sm font-medium text-gray-700 mb-2">Texto da Defesa</h4>
                          <div className="p-4 bg-gray-50 rounded-lg text-sm text-gray-600 leading-relaxed">
                            {defesa.texto}
                          </div>

                          {defesa.dataApresentacao && (
                            <p className="text-xs text-gray-400 mt-2">
                              Enviada em: {new Date(defesa.dataApresentacao).toLocaleDateString('pt-BR')}
                            </p>
                          )}

                          {defesa.arquivos && defesa.arquivos.length > 0 && (
                            <div className="mt-3">
                              <h4 className="text-sm font-medium text-gray-700 mb-2">Anexos</h4>
                              <div className="space-y-2">
                                {defesa.arquivos.map((anexo, aidx) => (
                                  <div key={aidx} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div className="flex items-center gap-2">
                                      <FileText className="h-4 w-4 text-gray-400" />
                                      <span className="text-sm text-gray-700">{anexo.nome}</span>
                                    </div>
                                    <a
                                      href={anexo.url}
                                      className="p-1 text-gray-500 hover:text-primary"
                                      title="Baixar"
                                    >
                                      <Download className="h-4 w-4" />
                                    </a>
                                  </div>
                                ))}
                              </div>
                            </div>
                          )}
                        </div>
                      ))
                    ) : (
                      <div className="text-center py-4 text-gray-500 text-sm">
                        Nenhuma defesa enviada para esta impugnacao.
                      </div>
                    )}

                    {/* Result messages */}
                    {imp.status === StatusImpugnacao.IMPROCEDENTE && (
                      <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div className="flex items-center gap-2 text-green-800">
                          <CheckCircle className="h-5 w-5" />
                          <span className="font-medium">Impugnacao improcedente - Defesa aceita</span>
                        </div>
                      </div>
                    )}

                    {imp.status === StatusImpugnacao.PROCEDENTE && (
                      <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-2 text-red-800">
                            <AlertTriangle className="h-5 w-5" />
                            <span className="font-medium">Impugnacao procedente</span>
                          </div>
                          <Link
                            to="/candidato/recursos/novo"
                            className="text-sm text-red-700 font-medium hover:underline"
                          >
                            Interpor Recurso
                          </Link>
                        </div>
                      </div>
                    )}
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
            <p className="font-medium text-blue-800">Sobre as Defesas</p>
            <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
              <li>As defesas sao analisadas pela Comissao Eleitoral</li>
              <li>Em caso de rejeicao, voce pode interpor recurso</li>
              <li>Mantenha os documentos comprobatorios arquivados</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}
