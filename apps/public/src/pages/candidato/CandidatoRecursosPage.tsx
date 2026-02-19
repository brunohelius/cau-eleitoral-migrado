import { useState, useEffect, useCallback } from 'react'
import { Link } from 'react-router-dom'
import {
  Scale,
  Clock,
  CheckCircle,
  XCircle,
  Eye,
  FileText,
  Download,
  Plus,
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

// Recurso type from backend
interface Recurso {
  id: string
  impugnacaoId: string
  tipo?: string
  tipoNome?: string
  fundamentação: string
  dataApresentacao: string
  status?: string
  statusNome?: string
  decisao?: string
  dataJulgamento?: string
  arquivos?: { nome: string; url: string }[]
}

// Map color name to tailwind
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

export function CandidatoRecursosPage() {
  const candidato = useCandidatoStore((s) => s.candidato)
  const [impugnacoes, setImpugnacoes] = useState<Impugnacao[]>([])
  const [recursosMap, setRecursosMap] = useState<Record<string, Recurso[]>>({})
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
      const response = await api.get<Impugnacao[]>(`/impugnacao/chapa/${candidato.chapaId}`)
      const data = response.data || []
      setImpugnacoes(data)

      // Fetch recursos for each impugnacao
      const recursos: Record<string, Recurso[]> = {}
      for (const imp of data) {
        try {
          const recResponse = await api.get<Recurso[]>(`/impugnacao/${imp.id}/recursos`)
          recursos[imp.id] = recResponse.data || []
        } catch {
          recursos[imp.id] = []
        }
      }
      setRecursosMap(recursos)
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

  // Flatten all recursos
  const allRecursos = impugnacoes.flatMap(imp => {
    const recursos = recursosMap[imp.id] || []
    return recursos.map(r => ({
      ...r,
      impugnacaoDescricao: imp.descrição,
      impugnacaoTipo: imp.tipoNome || 'Impugnação',
      impugnacaoStatus: imp.status,
    }))
  })

  // Count by status
  const emAndamento = allRecursos.filter(r =>
    !r.status || r.status === 'Enviado' || r.status === 'EmAnalise' ||
    r.status === 'enviado' || r.status === 'em_analise'
  )
  const providos = allRecursos.filter(r =>
    r.status === 'Provido' || r.status === 'provido'
  )
  const improvidos = allRecursos.filter(r =>
    r.status === 'Improvido' || r.status === 'improvido'
  )

  // Impugnacoes that allow recurso
  const podeInterporRecurso = impugnacoes.filter(imp =>
    imp.status === StatusImpugnacao.AGUARDANDO_RECURSO ||
    imp.status === StatusImpugnacao.PROCEDENTE
  )

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando recursos...</span>
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

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Recursos</h1>
          <p className="text-gray-600 mt-1">Acompanhe os recursos interpostos</p>
        </div>
        {podeInterporRecurso.length > 0 && (
          <Link
            to="/candidato/recursos/novo"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            <Plus className="h-4 w-4" />
            Novo Recurso
          </Link>
        )}
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-gray-900">{allRecursos.length}</p>
          <p className="text-sm text-gray-500">Total</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">{emAndamento.length}</p>
          <p className="text-sm text-gray-500">Em Andamento</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">{providos.length}</p>
          <p className="text-sm text-gray-500">Providos</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-red-600">{improvidos.length}</p>
          <p className="text-sm text-gray-500">Improvidos</p>
        </div>
      </div>

      {/* Recursos List */}
      {allRecursos.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Scale className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Nenhum recurso interposto</p>
          <p className="text-sm text-gray-400 mb-4">
            Recursos podem ser interpostos quando uma impugnação e julgada procedente.
          </p>
          {podeInterporRecurso.length > 0 && (
            <Link
              to="/candidato/recursos/novo"
              className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
            >
              <Plus className="h-4 w-4" />
              Interpor Recurso
            </Link>
          )}
        </div>
      ) : (
        <div className="space-y-4">
          {allRecursos.map((recurso, idx) => {
            const isExpanded = expandedId === (recurso.id || `recurso-${idx}`)
            const statusLabel = recurso.statusNome || recurso.status || 'Enviado'
            const isProvido = recurso.status === 'Provido' || recurso.status === 'provido'
            const isImprovido = recurso.status === 'Improvido' || recurso.status === 'improvido'

            const statusColorClass = isProvido
              ? 'text-green-600 bg-green-100'
              : isImprovido
                ? 'text-red-600 bg-red-100'
                : 'text-blue-600 bg-blue-100'

            const StatusIcon = isProvido ? CheckCircle : isImprovido ? XCircle : Clock

            return (
              <div key={recurso.id || `recurso-${idx}`} className="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div className="p-4 sm:p-6">
                  <div className="flex flex-col lg:flex-row lg:items-start gap-4">
                    {/* Icon */}
                    <div className="p-3 bg-purple-100 rounded-lg w-fit">
                      <Scale className="h-6 w-6 text-purple-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <span className="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                          Recurso
                        </span>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${statusColorClass}`}>
                          <StatusIcon className="h-3 w-3" />
                          {statusLabel}
                        </span>
                      </div>

                      <h3 className="font-semibold text-gray-900">
                        Recurso - {recurso.impugnacaoTipo}
                      </h3>
                      <p className="text-gray-600 text-sm mt-1 line-clamp-2">
                        {recurso.fundamentacao}
                      </p>

                      <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                        {recurso.dataApresentacao && (
                          <span>Enviado em: {new Date(recurso.dataApresentacao).toLocaleDateString('pt-BR')}</span>
                        )}
                        {recurso.arquivos && recurso.arquivos.length > 0 && (
                          <span>{recurso.arquivos.length} anexo(s)</span>
                        )}
                      </div>
                    </div>

                    {/* Actions */}
                    <button
                      onClick={() => setExpandedId(isExpanded ? null : (recurso.id || `recurso-${idx}`))}
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
                    {/* Decisao Original */}
                    <div className="mb-4">
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Impugnação Recorrida</h4>
                      <div className="p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                        {recurso.impugnacaoDescricao}
                      </div>
                    </div>

                    {/* Fundamentacao */}
                    <div className="mb-4">
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Razoes do Recurso</h4>
                      <div className="p-3 bg-gray-50 rounded-lg text-sm text-gray-600 leading-relaxed">
                        {recurso.fundamentacao}
                      </div>
                    </div>

                    {/* Anexos */}
                    {recurso.arquivos && recurso.arquivos.length > 0 && (
                      <div>
                        <h4 className="text-sm font-medium text-gray-700 mb-2">Anexos</h4>
                        <div className="space-y-2">
                          {recurso.arquivos.map((anexo, aidx) => (
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

                    {/* Result messages */}
                    {isProvido && (
                      <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div className="flex items-center gap-2 text-green-800">
                          <CheckCircle className="h-5 w-5" />
                          <span className="font-medium">Recurso provido. A decisao anterior foi reformada.</span>
                        </div>
                      </div>
                    )}

                    {isImprovido && (
                      <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div className="flex items-center gap-2 text-red-800">
                          <XCircle className="h-5 w-5" />
                          <span className="font-medium">Recurso improvido. A decisao anterior foi mantida.</span>
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
            <p className="font-medium text-blue-800">Sobre os Recursos</p>
            <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
              <li>Os recursos devem ser interpostos dentro do prazo estabelecido no regulamento</li>
              <li>E necessario fundamentar as razoes do recurso</li>
              <li>A decisao do recurso e definitiva em ultima instancia administrativa</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}
