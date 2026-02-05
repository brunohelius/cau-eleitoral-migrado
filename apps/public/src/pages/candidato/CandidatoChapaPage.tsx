import { useState } from 'react'
import { Link } from 'react-router-dom'
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

// Types
interface MembroChapa {
  id: string
  nome: string
  cargo: string
  cau: string
  status: 'aprovado' | 'pendente' | 'reprovado'
  isCurrentUser?: boolean
}

interface Chapa {
  id: string
  numero: number
  nome: string
  slogan: string
  status: 'inscrita' | 'em_analise' | 'aprovada' | 'reprovada' | 'impugnada'
  eleicaoId: string
  eleicaoNome: string
  regional: string
  dataInscricao: string
  membros: MembroChapa[]
}

// Mock data
const mockChapa: Chapa = {
  id: '1',
  numero: 1,
  nome: 'Chapa Renovacao',
  slogan: 'Por uma arquitetura mais inclusiva',
  status: 'aprovada',
  eleicaoId: '1',
  eleicaoNome: 'Eleicao Ordinaria CAU/SP 2024',
  regional: 'CAU/SP',
  dataInscricao: '2024-02-15',
  membros: [
    { id: '1', nome: 'Joao Silva', cargo: 'Presidente', cau: 'A12345-6', status: 'aprovado' },
    { id: '2', nome: 'Maria Santos', cargo: 'Vice-Presidente', cau: 'A54321-0', status: 'aprovado', isCurrentUser: true },
    { id: '3', nome: 'Carlos Oliveira', cargo: 'Diretor Financeiro', cau: 'A34567-8', status: 'aprovado' },
    { id: '4', nome: 'Ana Costa', cargo: 'Diretora Tecnica', cau: 'A45678-9', status: 'aprovado' },
    { id: '5', nome: 'Pedro Lima', cargo: 'Conselheiro', cau: 'A56789-0', status: 'pendente' },
    { id: '6', nome: 'Julia Ferreira', cargo: 'Conselheira', cau: 'A67890-1', status: 'aprovado' },
  ],
}

const statusConfig = {
  inscrita: { label: 'Inscrita', color: 'bg-blue-100 text-blue-800', icon: Clock },
  em_analise: { label: 'Em Analise', color: 'bg-yellow-100 text-yellow-800', icon: Clock },
  aprovada: { label: 'Aprovada', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  reprovada: { label: 'Reprovada', color: 'bg-red-100 text-red-800', icon: AlertTriangle },
  impugnada: { label: 'Impugnada', color: 'bg-red-100 text-red-800', icon: AlertTriangle },
}

const membroStatusConfig = {
  aprovado: { label: 'Aprovado', color: 'text-green-600', icon: CheckCircle },
  pendente: { label: 'Pendente', color: 'text-yellow-600', icon: Clock },
  reprovado: { label: 'Reprovado', color: 'text-red-600', icon: AlertTriangle },
}

export function CandidatoChapaPage() {
  const [isLoading] = useState(false)
  const chapa = mockChapa

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando dados da chapa...</span>
      </div>
    )
  }

  const status = statusConfig[chapa.status]
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
              <p className="text-white/80 italic mt-1">"{chapa.slogan}"</p>
            </div>
          </div>
        </div>

        {/* Info */}
        <div className="p-6 border-b">
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div className="flex items-center gap-3">
              <Calendar className="h-5 w-5 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Eleicao</p>
                <p className="font-medium text-gray-900">{chapa.eleicaoNome}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <MapPin className="h-5 w-5 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Regional</p>
                <p className="font-medium text-gray-900">{chapa.regional}</p>
              </div>
            </div>
            <div className="flex items-center gap-3">
              <FileText className="h-5 w-5 text-gray-400" />
              <div>
                <p className="text-xs text-gray-500">Data de Inscricao</p>
                <p className="font-medium text-gray-900">
                  {new Date(chapa.dataInscricao).toLocaleDateString('pt-BR')}
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Members */}
        <div className="p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Composicao da Chapa</h3>
          <div className="space-y-3">
            {chapa.membros.map(membro => {
              const membroStatus = membroStatusConfig[membro.status]
              const MembroIcon = membroStatus.icon

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
                      <User className={`h-5 w-5 ${membro.isCurrentUser ? 'text-blue-600' : 'text-gray-600'}`} />
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
                      <p className="text-sm text-gray-500">{membro.cargo} - CAU: {membro.cau}</p>
                    </div>
                  </div>
                  <div className={`flex items-center gap-1 ${membroStatus.color}`}>
                    <MembroIcon className="h-4 w-4" />
                    <span className="text-sm font-medium">{membroStatus.label}</span>
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
      {chapa.status === 'aprovada' && (
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

      {chapa.status === 'em_analise' && (
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

      {chapa.status === 'impugnada' && (
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
