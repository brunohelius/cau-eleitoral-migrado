import { useState, useEffect, useCallback } from 'react'
import {
  History,
  Calendar,
  FileText,
  Shield,
  Scale,
  AlertTriangle,
  CheckCircle,
  XCircle,
  Clock,
  ChevronDown,
  ChevronUp,
  Loader2,
  Filter,
  RefreshCw,
} from 'lucide-react'
import { useCandidatoStore } from '../../stores/candidato'
import api, { extractApiError, setTokenType } from '../../services/api'
import { type Impugnacao, StatusImpugnacao } from '../../services/impugnacaoService'

// Types
interface EventoHistorico {
  id: string
  tipo: 'documento' | 'defesa' | 'recurso' | 'denuncia' | 'status' | 'outro'
  titulo: string
  descricao: string
  data: string
  resultado?: 'aprovado' | 'rejeitado' | 'pendente'
}

interface HistoricoItem {
  id: string
  tipo: string
  descricao: string
  data: string
  usuario?: string
}

const tipoConfig = {
  documento: { icon: FileText, color: 'bg-blue-100 text-blue-600' },
  defesa: { icon: Shield, color: 'bg-green-100 text-green-600' },
  recurso: { icon: Scale, color: 'bg-purple-100 text-purple-600' },
  denuncia: { icon: AlertTriangle, color: 'bg-red-100 text-red-600' },
  status: { icon: CheckCircle, color: 'bg-yellow-100 text-yellow-600' },
  outro: { icon: Clock, color: 'bg-gray-100 text-gray-600' },
}

const resultadoConfig = {
  aprovado: { icon: CheckCircle, color: 'text-green-600' },
  rejeitado: { icon: XCircle, color: 'text-red-600' },
  pendente: { icon: Clock, color: 'text-yellow-600' },
}

// Map impugnacao status to event tipo
function mapImpugnacaoToEvento(imp: Impugnacao): EventoHistorico {
  let tipo: EventoHistorico['tipo'] = 'denuncia'
  let resultado: EventoHistorico['resultado'] | undefined

  if (imp.status === StatusImpugnacao.IMPROCEDENTE) {
    resultado = 'aprovado' // good for the candidate
  } else if (imp.status === StatusImpugnacao.PROCEDENTE) {
    resultado = 'rejeitado' // bad for the candidate
  } else if (imp.status === StatusImpugnacao.ARQUIVADA) {
    resultado = 'aprovado'
  }

  return {
    id: imp.id,
    tipo,
    titulo: imp.tipoNome || 'Impugnacao',
    descricao: imp.descricao,
    data: imp.createdAt,
    resultado,
  }
}

function mapHistoricoItem(item: HistoricoItem, impId: string): EventoHistorico {
  // Try to map based on tipo string
  let tipo: EventoHistorico['tipo'] = 'outro'
  const tipoLower = (item.tipo || '').toLowerCase()
  if (tipoLower.includes('defesa')) tipo = 'defesa'
  else if (tipoLower.includes('recurso')) tipo = 'recurso'
  else if (tipoLower.includes('denuncia') || tipoLower.includes('impugna')) tipo = 'denuncia'
  else if (tipoLower.includes('documento')) tipo = 'documento'
  else if (tipoLower.includes('status') || tipoLower.includes('julg')) tipo = 'status'

  return {
    id: `${impId}-${item.id}`,
    tipo,
    titulo: item.tipo || 'Evento',
    descricao: item.descricao,
    data: item.data,
  }
}

export function CandidatoHistoricoPage() {
  const candidato = useCandidatoStore((s) => s.candidato)
  const [historico, setHistorico] = useState<EventoHistorico[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [filter, setFilter] = useState<string | null>(null)
  const [showAll, setShowAll] = useState(false)

  const fetchHistorico = useCallback(async () => {
    if (!candidato?.chapaId) {
      setHistorico([])
      setIsLoading(false)
      return
    }

    setIsLoading(true)
    setError(null)
    try {
      setTokenType('candidate')
      const eventos: EventoHistorico[] = []

      // 1. Fetch impugnacoes against this chapa
      try {
        const impResponse = await api.get<Impugnacao[]>(`/impugnacao/chapa/${candidato.chapaId}`)
        const impugnacoes = impResponse.data || []

        // Add each impugnacao as an event
        for (const imp of impugnacoes) {
          eventos.push(mapImpugnacaoToEvento(imp))

          // Try to get historico for each impugnacao
          try {
            const histResponse = await api.get<HistoricoItem[]>(`/impugnacao/${imp.id}/historico`)
            const items = histResponse.data || []
            for (const item of items) {
              eventos.push(mapHistoricoItem(item, imp.id))
            }
          } catch {
            // Historico endpoint may not have data, that's ok
          }
        }
      } catch {
        // No impugnacoes, ok
      }

      // 2. Try to get denuncias against chapa
      try {
        const denResponse = await api.get<Array<{
          id: string
          descricao: string
          createdAt: string
          tipoNome?: string
          statusNome?: string
          protocolo?: string
        }>>(`/denuncia/chapa/${candidato.chapaId}`)
        const denuncias = denResponse.data || []
        for (const den of denuncias) {
          eventos.push({
            id: `den-${den.id}`,
            tipo: 'denuncia',
            titulo: den.tipoNome || 'Denuncia',
            descricao: den.descricao,
            data: den.createdAt,
          })
        }
      } catch {
        // No denuncias, ok
      }

      // Sort by date descending
      eventos.sort((a, b) => new Date(b.data).getTime() - new Date(a.data).getTime())

      // Deduplicate by id
      const seen = new Set<string>()
      const unique = eventos.filter(e => {
        if (seen.has(e.id)) return false
        seen.add(e.id)
        return true
      })

      setHistorico(unique)
    } catch (err) {
      const apiErr = extractApiError(err)
      if (apiErr.message.includes('404') || apiErr.message.includes('403')) {
        setHistorico([])
      } else {
        setError(apiErr.message)
      }
    } finally {
      setIsLoading(false)
    }
  }, [candidato?.chapaId])

  useEffect(() => {
    fetchHistorico()
  }, [fetchHistorico])

  const filteredHistorico = filter
    ? historico.filter(e => e.tipo === filter)
    : historico

  const displayedHistorico = showAll
    ? filteredHistorico
    : filteredHistorico.slice(0, 5)

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando historico...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64 gap-4">
        <AlertTriangle className="h-12 w-12 text-red-500" />
        <p className="text-gray-700">{error}</p>
        <button
          onClick={fetchHistorico}
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
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Historico</h1>
        <p className="text-gray-600 mt-1">Acompanhe todas as movimentacoes da sua candidatura</p>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap items-center gap-2">
        <Filter className="h-4 w-4 text-gray-500" />
        <button
          onClick={() => setFilter(null)}
          className={`px-3 py-1 rounded-full text-sm font-medium transition-colors ${
            !filter
              ? 'bg-primary text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          Todos
        </button>
        {Object.entries(tipoConfig).map(([key, config]) => {
          const Icon = config.icon
          return (
            <button
              key={key}
              onClick={() => setFilter(key)}
              className={`inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium transition-colors ${
                filter === key
                  ? 'bg-primary text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              }`}
            >
              <Icon className="h-3 w-3" />
              {key.charAt(0).toUpperCase() + key.slice(1)}
            </button>
          )
        })}
      </div>

      {/* Timeline */}
      {filteredHistorico.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <History className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhum evento registrado</p>
          <p className="text-sm text-gray-400 mt-2">
            O historico da sua candidatura aparecera aqui conforme eventos forem registrados.
          </p>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="space-y-0">
            {displayedHistorico.map((evento, index) => {
              const tipo = tipoConfig[evento.tipo] || tipoConfig.outro
              const Icon = tipo.icon
              const isLast = index === displayedHistorico.length - 1
              const resultado = evento.resultado ? resultadoConfig[evento.resultado] : null
              const ResultadoIcon = resultado?.icon

              return (
                <div key={evento.id} className="flex gap-4">
                  {/* Timeline line */}
                  <div className="flex flex-col items-center">
                    <div className={`w-10 h-10 rounded-full ${tipo.color} flex items-center justify-center flex-shrink-0`}>
                      <Icon className="h-5 w-5" />
                    </div>
                    {!isLast && (
                      <div className="w-0.5 flex-1 bg-gray-200 my-2" />
                    )}
                  </div>

                  {/* Content */}
                  <div className={`flex-1 pb-6`}>
                    <div className="flex flex-wrap items-center gap-2 mb-1">
                      <h3 className="font-semibold text-gray-900">{evento.titulo}</h3>
                      {resultado && ResultadoIcon && (
                        <ResultadoIcon className={`h-4 w-4 ${resultado.color}`} />
                      )}
                    </div>
                    <p className="text-gray-600 text-sm">{evento.descricao}</p>
                    <p className="text-gray-400 text-xs mt-2 flex items-center gap-1">
                      <Calendar className="h-3 w-3" />
                      {new Date(evento.data).toLocaleDateString('pt-BR')} as {new Date(evento.data).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
                    </p>
                  </div>
                </div>
              )
            })}
          </div>

          {/* Show more/less */}
          {filteredHistorico.length > 5 && (
            <div className="text-center pt-4 border-t">
              <button
                onClick={() => setShowAll(!showAll)}
                className="inline-flex items-center gap-1 text-sm text-primary font-medium hover:underline"
              >
                {showAll ? (
                  <>
                    <ChevronUp className="h-4 w-4" />
                    Mostrar menos
                  </>
                ) : (
                  <>
                    <ChevronDown className="h-4 w-4" />
                    Ver todos ({filteredHistorico.length})
                  </>
                )}
              </button>
            </div>
          )}
        </div>
      )}

      {/* Summary */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <FileText className="h-5 w-5 text-blue-600" />
            <div>
              <p className="text-2xl font-bold text-gray-900">
                {historico.filter(e => e.tipo === 'documento').length}
              </p>
              <p className="text-sm text-gray-500">Documentos</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <Shield className="h-5 w-5 text-green-600" />
            <div>
              <p className="text-2xl font-bold text-gray-900">
                {historico.filter(e => e.tipo === 'defesa').length}
              </p>
              <p className="text-sm text-gray-500">Defesas</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <Scale className="h-5 w-5 text-purple-600" />
            <div>
              <p className="text-2xl font-bold text-gray-900">
                {historico.filter(e => e.tipo === 'recurso').length}
              </p>
              <p className="text-sm text-gray-500">Recursos</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <AlertTriangle className="h-5 w-5 text-red-600" />
            <div>
              <p className="text-2xl font-bold text-gray-900">
                {historico.filter(e => e.tipo === 'denuncia').length}
              </p>
              <p className="text-sm text-gray-500">Denuncias</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
