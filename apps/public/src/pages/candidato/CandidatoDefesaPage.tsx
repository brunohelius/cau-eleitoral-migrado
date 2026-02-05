import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface Defesa {
  id: string
  denunciaId: string
  denunciaTitulo: string
  protocolo: string
  dataEnvio: string
  status: 'enviada' | 'em_analise' | 'aceita' | 'rejeitada'
  texto: string
  anexos: { nome: string; url: string }[]
}

// Mock data
const mockDefesas: Defesa[] = [
  {
    id: '1',
    denunciaId: '1',
    denunciaTitulo: 'Impugnacao de Candidatura',
    protocolo: 'DEF-2024-001',
    dataEnvio: '2024-03-05T15:30:00',
    status: 'aceita',
    texto: 'Em resposta a impugnacao apresentada, informamos que o membro Joao Silva encontra-se plenamente adimplente com suas obrigacoes junto ao CAU, conforme certidao em anexo. A alegacao de pendencia e infundada e carece de qualquer comprovacao...',
    anexos: [
      { nome: 'certidao_quitacao.pdf', url: '/docs/certidao.pdf' },
      { nome: 'declaracao_regularidade.pdf', url: '/docs/declaracao.pdf' },
    ],
  },
  {
    id: '2',
    denunciaId: '2',
    denunciaTitulo: 'Propaganda Irregular',
    protocolo: 'DEF-2024-015',
    dataEnvio: '2024-03-14T10:00:00',
    status: 'em_analise',
    texto: 'Em resposta a alegacao de propaganda irregular, esclarecemos que o material questionado foi afixado em local permitido pelo regulamento eleitoral, conforme mapas e fotos em anexo que comprovam a localizacao...',
    anexos: [
      { nome: 'mapa_localizacao.pdf', url: '/docs/mapa.pdf' },
      { nome: 'fotos_local.zip', url: '/docs/fotos.zip' },
    ],
  },
]

const statusConfig = {
  enviada: { label: 'Enviada', color: 'text-blue-600 bg-blue-100', icon: Clock },
  em_analise: { label: 'Em Analise', color: 'text-yellow-600 bg-yellow-100', icon: Clock },
  aceita: { label: 'Aceita', color: 'text-green-600 bg-green-100', icon: CheckCircle },
  rejeitada: { label: 'Rejeitada', color: 'text-red-600 bg-red-100', icon: AlertTriangle },
}

export function CandidatoDefesaPage() {
  const [isLoading] = useState(false)
  const [expandedId, setExpandedId] = useState<string | null>(null)
  const defesas = mockDefesas

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando defesas...</span>
      </div>
    )
  }

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
          <p className="text-2xl font-bold text-gray-900">{defesas.length}</p>
          <p className="text-sm text-gray-500">Total Enviadas</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">
            {defesas.filter(d => d.status === 'em_analise').length}
          </p>
          <p className="text-sm text-gray-500">Em Analise</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">
            {defesas.filter(d => d.status === 'aceita').length}
          </p>
          <p className="text-sm text-gray-500">Aceitas</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-red-600">
            {defesas.filter(d => d.status === 'rejeitada').length}
          </p>
          <p className="text-sm text-gray-500">Rejeitadas</p>
        </div>
      </div>

      {/* Defesas List */}
      {defesas.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Shield className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Nenhuma defesa enviada</p>
          <Link
            to="/candidato/denuncias"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            Ver Denuncias Pendentes
          </Link>
        </div>
      ) : (
        <div className="space-y-4">
          {defesas.map(defesa => {
            const status = statusConfig[defesa.status]
            const StatusIcon = status.icon
            const isExpanded = expandedId === defesa.id

            return (
              <div key={defesa.id} className="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div className="p-4 sm:p-6">
                  <div className="flex flex-col lg:flex-row lg:items-start gap-4">
                    {/* Icon */}
                    <div className="p-3 bg-green-100 rounded-lg w-fit">
                      <Shield className="h-6 w-6 text-green-600" />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                      <div className="flex flex-wrap items-center gap-2 mb-2">
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${status.color}`}>
                          <StatusIcon className="h-3 w-3" />
                          {status.label}
                        </span>
                        <span className="text-xs text-gray-500">
                          Protocolo: {defesa.protocolo}
                        </span>
                      </div>

                      <h3 className="font-semibold text-gray-900">
                        Defesa - {defesa.denunciaTitulo}
                      </h3>

                      <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                        <span>Enviada em: {new Date(defesa.dataEnvio).toLocaleDateString('pt-BR')}</span>
                        <span>{defesa.anexos.length} anexo(s)</span>
                      </div>
                    </div>

                    {/* Actions */}
                    <button
                      onClick={() => setExpandedId(isExpanded ? null : defesa.id)}
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
                    {/* Texto */}
                    <div className="mb-4">
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Texto da Defesa</h4>
                      <div className="p-4 bg-gray-50 rounded-lg text-sm text-gray-600 leading-relaxed">
                        {defesa.texto}
                      </div>
                    </div>

                    {/* Anexos */}
                    <div>
                      <h4 className="text-sm font-medium text-gray-700 mb-2">Anexos</h4>
                      <div className="space-y-2">
                        {defesa.anexos.map((anexo, idx) => (
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
                    {defesa.status === 'aceita' && (
                      <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div className="flex items-center gap-2 text-green-800">
                          <CheckCircle className="h-5 w-5" />
                          <span className="font-medium">Defesa aceita pela Comissao Eleitoral</span>
                        </div>
                      </div>
                    )}

                    {defesa.status === 'rejeitada' && (
                      <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-2 text-red-800">
                            <AlertTriangle className="h-5 w-5" />
                            <span className="font-medium">Defesa rejeitada</span>
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
