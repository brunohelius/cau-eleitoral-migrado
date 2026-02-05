import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface Recurso {
  id: string
  protocolo: string
  tipo: 'defesa_rejeitada' | 'denuncia_deferida' | 'decisao_comissao'
  titulo: string
  descricao: string
  dataEnvio: string
  status: 'enviado' | 'em_analise' | 'provido' | 'improvido'
  decisaoOriginal: string
  anexos: { nome: string; url: string }[]
}

// Mock data
const mockRecursos: Recurso[] = [
  {
    id: '1',
    protocolo: 'REC-2024-001',
    tipo: 'denuncia_deferida',
    titulo: 'Recurso contra deferimento de impugnacao',
    descricao: 'Recurso interposto contra a decisao que deferiu a impugnacao de candidatura do membro Joao Silva.',
    dataEnvio: '2024-03-08T14:00:00',
    status: 'provido',
    decisaoOriginal: 'Deferimento da impugnacao DEN-2024-001',
    anexos: [
      { nome: 'razoes_recurso.pdf', url: '/docs/razoes.pdf' },
      { nome: 'documentos_complementares.pdf', url: '/docs/complementares.pdf' },
    ],
  },
]

const tipoConfig = {
  defesa_rejeitada: { label: 'Defesa Rejeitada', color: 'bg-yellow-100 text-yellow-800' },
  denuncia_deferida: { label: 'Denuncia Deferida', color: 'bg-red-100 text-red-800' },
  decisao_comissao: { label: 'Decisao Comissao', color: 'bg-blue-100 text-blue-800' },
}

const statusConfig = {
  enviado: { label: 'Enviado', color: 'text-blue-600 bg-blue-100', icon: Clock },
  em_analise: { label: 'Em Analise', color: 'text-yellow-600 bg-yellow-100', icon: Clock },
  provido: { label: 'Provido', color: 'text-green-600 bg-green-100', icon: CheckCircle },
  improvido: { label: 'Improvido', color: 'text-red-600 bg-red-100', icon: XCircle },
}

export function CandidatoRecursosPage() {
  const [isLoading] = useState(false)
  const [expandedId, setExpandedId] = useState<string | null>(null)
  const recursos = mockRecursos

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando recursos...</span>
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
        <Link
          to="/candidato/recursos/novo"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          <Plus className="h-4 w-4" />
          Novo Recurso
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-gray-900">{recursos.length}</p>
          <p className="text-sm text-gray-500">Total</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">
            {recursos.filter(r => ['enviado', 'em_analise'].includes(r.status)).length}
          </p>
          <p className="text-sm text-gray-500">Em Andamento</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">
            {recursos.filter(r => r.status === 'provido').length}
          </p>
          <p className="text-sm text-gray-500">Providos</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-red-600">
            {recursos.filter(r => r.status === 'improvido').length}
          </p>
          <p className="text-sm text-gray-500">Improvidos</p>
        </div>
      </div>

      {/* Recursos List */}
      {recursos.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Scale className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Nenhum recurso interposto</p>
          <Link
            to="/candidato/recursos/novo"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            <Plus className="h-4 w-4" />
            Interpor Recurso
          </Link>
        </div>
      ) : (
        <div className="space-y-4">
          {recursos.map(recurso => {
            const tipo = tipoConfig[recurso.tipo]
            const status = statusConfig[recurso.status]
            const StatusIcon = status.icon
            const isExpanded = expandedId === recurso.id

            return (
              <div key={recurso.id} className="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div className="p-4 sm:p-6">
                  <div className="flex flex-col lg:flex-row lg:items-start gap-4">
                    {/* Icon */}
                    <div className="p-3 bg-purple-100 rounded-lg w-fit">
                      <Scale className="h-6 w-6 text-purple-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${tipo.color}`}>
                          {tipo.label}
                        </span>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${status.color}`}>
                          <StatusIcon className="h-3 w-3" />
                          {status.label}
                        </span>
                        <span className="text-xs text-gray-500">
                          Protocolo: {recurso.protocolo}
                        </span>
                      </div>

                      <h3 className="font-semibold text-gray-900">{recurso.titulo}</h3>
                      <p className="text-gray-600 text-sm mt-1 line-clamp-2">{recurso.descricao}</p>

                      <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>Enviado em: {new Date(recurso.dataEnvio).toLocaleDateString('pt-BR')}</span>
                        <span>{recurso.anexos.length} anexo(s)</span>
                      </div>
                    </div>

                    {/* Actions */}
                    <button
                      onClick={() => setExpandedId(isExpanded ? null : recurso.id)}
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
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Decisao Recorrida</h4>
                      <div className="p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                        {recurso.decisaoOriginal}
                      </div>
                    </div>

                    {/* Descricao */}
                    <div className="mb-4">
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Razoes do Recurso</h4>
                      <div className="p-3 bg-gray-50 rounded-lg text-sm text-gray-600 leading-relaxed">
                        {recurso.descricao}
                      </div>
                    </div>

                    {/* Anexos */}
                    <div>
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Anexos</h4>
                      <div className="space-y-2">
                        {recurso.anexos.map((anexo, idx) => (
                          <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
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

                    {/* Result messages */}
                    {recurso.status === 'provido' && (
                      <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div className="flex items-center gap-2 text-green-800">
                          <CheckCircle className="h-5 w-5" />
                          <span className="font-medium">Recurso provido. A decisao anterior foi reformada.</span>
                        </div>
                      </div>
                    )}

                    {recurso.status === 'improvido' && (
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
