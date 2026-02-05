import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface Denuncia {
  id: string
  protocolo: string
  tipo: 'impugnacao' | 'irregularidade' | 'propaganda'
  titulo: string
  descricao: string
  dataRegistro: string
  status: 'pendente' | 'em_analise' | 'deferida' | 'indeferida'
  prazoDefesa?: string
  defesaEnviada: boolean
}

// Mock data
const mockDenuncias: Denuncia[] = [
  {
    id: '1',
    protocolo: 'DEN-2024-001',
    tipo: 'impugnacao',
    titulo: 'Impugnacao de Candidatura',
    descricao: 'Alegacao de inelegibilidade do membro Joao Silva por suposta pendencia junto ao CAU.',
    dataRegistro: '2024-03-01T10:00:00',
    status: 'indeferida',
    defesaEnviada: true,
  },
  {
    id: '2',
    protocolo: 'DEN-2024-015',
    tipo: 'propaganda',
    titulo: 'Propaganda Irregular',
    descricao: 'Alegacao de uso de material de campanha em local proibido pelo regulamento eleitoral.',
    dataRegistro: '2024-03-10T14:30:00',
    status: 'em_analise',
    prazoDefesa: '2024-03-17T23:59:59',
    defesaEnviada: true,
  },
  {
    id: '3',
    protocolo: 'DEN-2024-022',
    tipo: 'irregularidade',
    titulo: 'Irregularidade em Documentacao',
    descricao: 'Questionamento sobre a autenticidade de documento apresentado na inscricao da chapa.',
    dataRegistro: '2024-03-12T09:00:00',
    status: 'pendente',
    prazoDefesa: '2024-03-19T23:59:59',
    defesaEnviada: false,
  },
]

const tipoConfig = {
  impugnacao: { label: 'Impugnacao', color: 'bg-red-100 text-red-800' },
  irregularidade: { label: 'Irregularidade', color: 'bg-yellow-100 text-yellow-800' },
  propaganda: { label: 'Propaganda', color: 'bg-blue-100 text-blue-800' },
}

const statusConfig = {
  pendente: { label: 'Aguardando Defesa', color: 'text-yellow-600 bg-yellow-100', icon: Clock },
  em_analise: { label: 'Em Analise', color: 'text-blue-600 bg-blue-100', icon: Clock },
  deferida: { label: 'Deferida', color: 'text-red-600 bg-red-100', icon: XCircle },
  indeferida: { label: 'Indeferida', color: 'text-green-600 bg-green-100', icon: CheckCircle },
}

export function CandidatoDenunciasPage() {
  const [isLoading] = useState(false)
  const denuncias = mockDenuncias

  const pendentes = denuncias.filter(d => d.status === 'pendente' && !d.defesaEnviada)
  const emAndamento = denuncias.filter(d => d.status === 'em_analise' || (d.status === 'pendente' && d.defesaEnviada))
  const finalizadas = denuncias.filter(d => ['deferida', 'indeferida'].includes(d.status))

  const getDaysRemaining = (prazo: string) => {
    const diff = new Date(prazo).getTime() - new Date().getTime()
    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)))
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando denuncias...</span>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Denuncias</h1>
        <p className="text-gray-600 mt-1">Acompanhe as denuncias registradas contra sua chapa</p>
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
          <p className="text-gray-500">Nenhuma denuncia registrada contra sua chapa</p>
        </div>
      ) : (
        <div className="space-y-4">
          {denuncias.map(denuncia => {
            const tipo = tipoConfig[denuncia.tipo]
            const status = statusConfig[denuncia.status]
            const StatusIcon = status.icon
            const daysRemaining = denuncia.prazoDefesa ? getDaysRemaining(denuncia.prazoDefesa) : null

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
                        <span className={`px-2 py-0.5 rounded-full text-xs font-medium ${tipo.color}`}>
                          {tipo.label}
                        </span>
                        <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${status.color}`}>
                          <StatusIcon className="h-3 w-3" />
                          {status.label}
                        </span>
                        <span className="text-xs text-gray-500">
                          Protocolo: {denuncia.protocolo}
                        </span>
                      </div>

                      <h3 className="font-semibold text-gray-900">{denuncia.titulo}</h3>
                      <p className="text-gray-600 text-sm mt-1 line-clamp-2">{denuncia.descricao}</p>

                      <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                        <span>Registrada em: {new Date(denuncia.dataRegistro).toLocaleDateString('pt-BR')}</span>

                        {daysRemaining !== null && denuncia.status === 'pendente' && !denuncia.defesaEnviada && (
                          <span className={`font-medium ${daysRemaining <= 2 ? 'text-red-600' : 'text-yellow-600'}`}>
                            {daysRemaining === 0 ? 'Ultimo dia!' : `${daysRemaining} dias restantes`}
                          </span>
                        )}

                        {denuncia.defesaEnviada && (
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

                      {denuncia.status === 'pendente' && !denuncia.defesaEnviada && (
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
                {denuncia.status === 'deferida' && (
                  <div className="px-4 sm:px-6 py-3 bg-red-50 border-t">
                    <div className="flex items-center gap-2 text-sm text-red-800">
                      <XCircle className="h-4 w-4" />
                      <span>Denuncia deferida. Voce pode interpor recurso.</span>
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

                {denuncia.status === 'indeferida' && (
                  <div className="px-4 sm:px-6 py-3 bg-green-50 border-t">
                    <div className="flex items-center gap-2 text-sm text-green-800">
                      <CheckCircle className="h-4 w-4" />
                      <span>Denuncia indeferida. Sua defesa foi aceita.</span>
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
            <p className="font-medium text-blue-800">Sobre as Denuncias</p>
            <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
              <li>Voce tem direito a apresentar defesa dentro do prazo estabelecido</li>
              <li>Em caso de deferimento, e possivel interpor recurso</li>
              <li>Todas as decisoes sao publicadas oficialmente</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}
