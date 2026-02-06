import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  User,
  CheckCircle,
  Clock,
  AlertTriangle,
  FileText,
  Calendar,
  MapPin,
  ExternalLink,
  Loader2,
} from 'lucide-react'
import { candidatoService, type ChapaInfoCandidato } from '@/services/candidato'

const statusConfig: Record<number, { label: string; color: string; icon: React.ElementType }> = {
  0: { label: 'Rascunho', color: 'bg-gray-100 text-gray-800', icon: Clock },
  1: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800', icon: Clock },
  2: { label: 'Aguardando Analise', color: 'bg-blue-100 text-blue-800', icon: Clock },
  3: { label: 'Em Analise', color: 'bg-blue-100 text-blue-800', icon: Clock },
  4: { label: 'Aprovada', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  5: { label: 'Reprovada', color: 'bg-red-100 text-red-800', icon: AlertTriangle },
  6: { label: 'Impugnada', color: 'bg-red-100 text-red-800', icon: AlertTriangle },
  8: { label: 'Registrada', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  9: { label: 'Cancelada', color: 'bg-gray-100 text-gray-800', icon: AlertTriangle },
}

const membroStatusConfig: Record<string, { label: string; color: string; icon: React.ElementType }> = {
  aprovado: { label: 'Aprovado', color: 'text-green-600', icon: CheckCircle },
  pendente: { label: 'Pendente', color: 'text-yellow-600', icon: Clock },
  reprovado: { label: 'Reprovado', color: 'text-red-600', icon: AlertTriangle },
}

export function CandidatoChapaPage() {
  const { data: chapa, isLoading, error } = useQuery({
    queryKey: ['candidato-chapa'],
    queryFn: candidatoService.getChapa,
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando dados da chapa...</span>
      </div>
    )
  }

  if (error || !chapa) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertTriangle className="h-12 w-12 text-gray-400 mb-4" />
        <p className="text-gray-700 font-medium">Nenhuma chapa encontrada</p>
        <p className="text-gray-500 text-sm mt-1">Voce ainda nao esta vinculado a nenhuma chapa.</p>
      </div>
    )
  }

  const status = statusConfig[chapa.status] || statusConfig[0]
  const StatusIcon = status.icon

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Minha Chapa</h1>
        <p className="text-gray-600 mt-1">Informacoes sobre sua chapa e composicao</p>
      </div>

      {/* Chapa Card */}
      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        {/* Header */}
        <div className="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white">
          <div className="flex flex-col sm:flex-row sm:items-center gap-4">
            <div className="w-20 h-20 bg-white/20 rounded-xl flex items-center justify-center">
              <span className="text-4xl font-bold">{chapa.numero}</span>
            </div>
            <div className="flex-1">
              <div className="flex flex-wrap items-center gap-3">
                <h2 className="text-2xl font-bold">{chapa.nome}</h2>
                <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${status.color}`}>
                  <StatusIcon className="h-3 w-3" />
                  {status.label}
                </span>
              </div>
              {chapa.lema && <p className="text-white/80 italic mt-1">"{chapa.lema}"</p>}
            </div>
          </div>
        </div>

        {/* Info */}
        <div className="p-6 border-b">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {chapa.sigla && (
              <div className="flex items-center gap-3">
                <FileText className="h-5 w-5 text-gray-400" />
                <div>
                  <p className="text-xs text-gray-500">Sigla</p>
                  <p className="font-medium text-gray-900">{chapa.sigla}</p>
                </div>
              </div>
            )}
            <div className="flex items-center gap-3">
              <User className="h-5 w-5 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Membros</p>
                <p className="font-medium text-gray-900">{chapa.membros.length} integrantes</p>
              </div>
            </div>
          </div>
        </div>

        {/* Members */}
        <div className="p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Composicao da Chapa</h3>
          <div className="space-y-3">
            {chapa.membros.map(membro => {
              return (
                <div
                  key={membro.id}
                  className={`flex items-center justify-between p-4 rounded-lg ${
                    membro.isCurrentUser ? 'bg-blue-50 border-2 border-blue-200' : 'bg-gray-50'
                  }`}
                >
                  <div className="flex items-center gap-3">
                    <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                      membro.isCurrentUser ? 'bg-blue-200' : 'bg-gray-200'
                    }`}>
                      {membro.fotoUrl ? (
                        <img src={membro.fotoUrl} alt={membro.nome} className="w-10 h-10 rounded-full object-cover" />
                      ) : (
                        <User className={`h-5 w-5 ${membro.isCurrentUser ? 'text-blue-600' : 'text-gray-600'}`} />
                      )}
                    </div>
                    <div>
                      <div className="flex items-center gap-2">
                        <p className="font-medium text-gray-900">{membro.nome}</p>
                        {membro.isCurrentUser && (
                          <span className="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                            Voce
                          </span>
                        )}
                      </div>
                      <p className="text-sm text-gray-500">{membro.cargo} - {membro.tipo}</p>
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        </div>

        {/* Actions */}
        <div className="p-6 border-t bg-gray-50">
          <div className="flex flex-wrap gap-3">
            <Link
              to="/candidato/documentos"
              className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
            >
              <FileText className="h-4 w-4" />
              Ver Documentos
            </Link>
            <Link
              to="/candidato/plataforma"
              className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100"
            >
              <ExternalLink className="h-4 w-4" />
              Editar Plataforma
            </Link>
          </div>
        </div>
      </div>

      {/* Status Info */}
      {(chapa.status === 4 || chapa.status === 8) && (
        <div className="bg-green-50 border border-green-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <CheckCircle className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="font-medium text-green-800">Chapa Aprovada</p>
              <p className="text-sm text-green-700">
                Sua chapa foi aprovada pela Comissao Eleitoral e esta apta a participar da eleicao.
              </p>
            </div>
          </div>
        </div>
      )}

      {chapa.status === 3 && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <Clock className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="font-medium text-yellow-800">Em Analise</p>
              <p className="text-sm text-yellow-700">
                Sua chapa esta em analise pela Comissao Eleitoral. Aguarde a comunicacao oficial.
              </p>
            </div>
          </div>
        </div>
      )}

      {chapa.status === 6 && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="font-medium text-red-800">Chapa Impugnada</p>
              <p className="text-sm text-red-700">
                Sua chapa recebeu uma impugnacao. Verifique a area de denuncias e defesas.
              </p>
              <Link
                to="/candidato/defesas"
                className="inline-flex items-center gap-1 mt-2 text-red-800 font-medium hover:underline"
              >
                Ver impugnacao
                <ExternalLink className="h-4 w-4" />
              </Link>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
