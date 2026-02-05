import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface EventoHistorico {
  id: string
  tipo: 'documento' | 'defesa' | 'recurso' | 'denuncia' | 'status' | 'outro'
  titulo: string
  descricao: string
  data: string
  resultado?: 'aprovado' | 'rejeitado' | 'pendente'
}

// Mock data
const mockHistorico: EventoHistorico[] = [
  {
    id: '1',
    tipo: 'status',
    titulo: 'Chapa Aprovada',
    descricao: 'A chapa foi aprovada pela Comissao Eleitoral e esta apta a participar da eleicao.',
    data: '2024-02-28T16:00:00',
    resultado: 'aprovado',
  },
  {
    id: '2',
    tipo: 'recurso',
    titulo: 'Recurso Provido',
    descricao: 'O recurso contra a impugnacao foi provido. A candidatura foi mantida.',
    data: '2024-02-25T14:30:00',
    resultado: 'aprovado',
  },
  {
    id: '3',
    tipo: 'defesa',
    titulo: 'Defesa Enviada',
    descricao: 'Defesa contra a impugnacao de candidatura foi protocolada.',
    data: '2024-02-20T10:00:00',
  },
  {
    id: '4',
    tipo: 'denuncia',
    titulo: 'Impugnacao Recebida',
    descricao: 'Impugnacao de candidatura registrada contra o membro Joao Silva.',
    data: '2024-02-18T09:00:00',
  },
  {
    id: '5',
    tipo: 'documento',
    titulo: 'Documentos Aprovados',
    descricao: 'Todos os documentos obrigatorios foram aprovados pela Comissao.',
    data: '2024-02-17T15:00:00',
    resultado: 'aprovado',
  },
  {
    id: '6',
    tipo: 'documento',
    titulo: 'Foto Rejeitada',
    descricao: 'A foto 3x4 foi rejeitada por estar fora do padrao. Necessario reenvio.',
    data: '2024-02-15T11:00:00',
    resultado: 'rejeitado',
  },
  {
    id: '7',
    tipo: 'documento',
    titulo: 'Documentos Enviados',
    descricao: 'Documentos da candidatura foram enviados para analise.',
    data: '2024-02-10T14:00:00',
  },
  {
    id: '8',
    tipo: 'status',
    titulo: 'Inscricao Realizada',
    descricao: 'A chapa foi inscrita na Eleicao Ordinaria CAU/SP 2024.',
    data: '2024-02-08T10:30:00',
  },
]

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

export function CandidatoHistoricoPage() {
  const [isLoading] = useState(false)
  const [filter, setFilter] = useState<string | null>(null)
  const [showAll, setShowAll] = useState(false)

  const historico = mockHistorico

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
          <p className="text-gray-500">Nenhum evento encontrado</p>
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="space-y-0">
            {displayedHistorico.map((evento, index) => {
              const tipo = tipoConfig[evento.tipo]
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
                  <div className={`flex-1 pb-6 ${isLast ? '' : ''}`}>
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
