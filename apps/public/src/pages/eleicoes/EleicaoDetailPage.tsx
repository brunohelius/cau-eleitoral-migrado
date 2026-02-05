import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Calendar,
  Users,
  Vote,
  Clock,
  MapPin,
  ChevronRight,
  FileText,
  Info,
  AlertCircle,
  Loader2,
} from 'lucide-react'

// Types
interface Eleicao {
  id: string
  nome: string
  descricao: string
  ano: number
  regional: string
  tipoEleicao: string
  status: 'agendada' | 'em_inscricao' | 'em_votacao' | 'apuracao' | 'finalizada'
  dataInicio: string
  dataFim: string
  dataVotacaoInicio: string
  dataVotacaoFim: string
  quantidadeVagas: number
  totalChapas: number
  totalEleitores: number
  totalVotos?: number
  regulamento?: string
  edital?: string
}

interface FaseEleicao {
  id: string
  nome: string
  dataInicio: string
  dataFim: string
  status: 'pendente' | 'em_andamento' | 'concluida'
}

// Mock data
const mockEleicao: Eleicao = {
  id: '1',
  nome: 'Eleicao Ordinaria 2024',
  descricao: 'Eleicao para renovacao dos cargos do Conselho de Arquitetura e Urbanismo - Regional Sao Paulo. Esta eleicao visa escolher os novos representantes para o mandato 2024-2027.',
  ano: 2024,
  regional: 'CAU/SP - Conselho Regional de Sao Paulo',
  tipoEleicao: 'Ordinaria',
  status: 'em_votacao',
  dataInicio: '2024-01-15',
  dataFim: '2024-04-30',
  dataVotacaoInicio: '2024-03-15',
  dataVotacaoFim: '2024-03-22',
  quantidadeVagas: 12,
  totalChapas: 5,
  totalEleitores: 45000,
  totalVotos: 12500,
  regulamento: '/documentos/regulamento-2024.pdf',
  edital: '/documentos/edital-2024.pdf',
}

const mockFases: FaseEleicao[] = [
  { id: '1', nome: 'Publicacao do Edital', dataInicio: '2024-01-15', dataFim: '2024-01-15', status: 'concluida' },
  { id: '2', nome: 'Periodo de Inscricoes', dataInicio: '2024-01-20', dataFim: '2024-02-20', status: 'concluida' },
  { id: '3', nome: 'Analise de Documentacao', dataInicio: '2024-02-21', dataFim: '2024-03-05', status: 'concluida' },
  { id: '4', nome: 'Prazo para Impugnacoes', dataInicio: '2024-03-06', dataFim: '2024-03-10', status: 'concluida' },
  { id: '5', nome: 'Campanha Eleitoral', dataInicio: '2024-03-11', dataFim: '2024-03-14', status: 'concluida' },
  { id: '6', nome: 'Periodo de Votacao', dataInicio: '2024-03-15', dataFim: '2024-03-22', status: 'em_andamento' },
  { id: '7', nome: 'Apuracao dos Votos', dataInicio: '2024-03-23', dataFim: '2024-03-25', status: 'pendente' },
  { id: '8', nome: 'Divulgacao do Resultado', dataInicio: '2024-03-26', dataFim: '2024-03-26', status: 'pendente' },
]

const statusConfig = {
  agendada: { label: 'Agendada', color: 'bg-blue-100 text-blue-800' },
  em_inscricao: { label: 'Em Inscricao', color: 'bg-yellow-100 text-yellow-800' },
  em_votacao: { label: 'Em Votacao', color: 'bg-green-100 text-green-800' },
  apuracao: { label: 'Em Apuracao', color: 'bg-purple-100 text-purple-800' },
  finalizada: { label: 'Finalizada', color: 'bg-gray-100 text-gray-800' },
}

const faseStatusConfig = {
  pendente: { color: 'bg-gray-200', dotColor: 'bg-gray-400' },
  em_andamento: { color: 'bg-green-500', dotColor: 'bg-green-500' },
  concluida: { color: 'bg-primary', dotColor: 'bg-primary' },
}

export function EleicaoDetailPage() {
  const { id: _id } = useParams<{ id: string }>()
  const [isLoading] = useState(false)
  const [error] = useState<string | null>(null)

  // In a real app, fetch from API
  const eleicao = mockEleicao
  const fases = mockFases

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando eleicao...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar eleicao</p>
        <p className="text-gray-500 text-sm">{error}</p>
        <Link
          to="/eleicoes"
          className="mt-4 text-primary hover:underline"
        >
          Voltar para lista de eleicoes
        </Link>
      </div>
    )
  }

  if (!eleicao) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <Info className="h-12 w-12 text-gray-400 mb-4" />
        <p className="text-gray-700 font-medium">Eleicao nao encontrada</p>
        <Link
          to="/eleicoes"
          className="mt-4 text-primary hover:underline"
        >
          Voltar para lista de eleicoes
        </Link>
      </div>
    )
  }

  const statusInfo = statusConfig[eleicao.status]
  const participacao = eleicao.totalVotos && eleicao.totalEleitores
    ? ((eleicao.totalVotos / eleicao.totalEleitores) * 100).toFixed(1)
    : null

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        <Link
          to="/eleicoes"
          className="inline-flex items-center text-gray-600 hover:text-gray-900"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <div className="flex-1">
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
              {eleicao.nome}
            </h1>
            <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusInfo.color}`}>
              {statusInfo.label}
            </span>
          </div>
          <p className="text-gray-600 mt-1 flex items-center gap-2">
            <MapPin className="h-4 w-4" />
            {eleicao.regional}
          </p>
        </div>
        {eleicao.status === 'em_votacao' && (
          <Link
            to={`/votacao/${eleicao.id}`}
            className="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90 text-center"
          >
            Votar Agora
          </Link>
        )}
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <Users className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{eleicao.totalChapas}</p>
              <p className="text-sm text-gray-500">Chapas</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <Vote className="h-5 w-5 text-green-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{eleicao.totalEleitores.toLocaleString()}</p>
              <p className="text-sm text-gray-500">Eleitores</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-purple-100 rounded-lg">
              <Calendar className="h-5 w-5 text-purple-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{eleicao.quantidadeVagas}</p>
              <p className="text-sm text-gray-500">Vagas</p>
            </div>
          </div>
        </div>

        {participacao && (
          <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-yellow-100 rounded-lg">
                <Clock className="h-5 w-5 text-yellow-600" />
              </div>
              <div>
                <p className="text-2xl font-bold text-gray-900">{participacao}%</p>
                <p className="text-sm text-gray-500">Participacao</p>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Description and Quick Links */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Description */}
        <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Sobre a Eleicao</h2>
          <p className="text-gray-600 leading-relaxed">{eleicao.descricao}</p>

          <div className="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <h3 className="text-sm font-medium text-gray-500">Tipo de Eleicao</h3>
              <p className="mt-1 text-gray-900">{eleicao.tipoEleicao}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Ano</h3>
              <p className="mt-1 text-gray-900">{eleicao.ano}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Periodo da Eleicao</h3>
              <p className="mt-1 text-gray-900">
                {new Date(eleicao.dataInicio).toLocaleDateString('pt-BR')} a {new Date(eleicao.dataFim).toLocaleDateString('pt-BR')}
              </p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Periodo de Votacao</h3>
              <p className="mt-1 text-gray-900">
                {new Date(eleicao.dataVotacaoInicio).toLocaleDateString('pt-BR')} a {new Date(eleicao.dataVotacaoFim).toLocaleDateString('pt-BR')}
              </p>
            </div>
          </div>
        </div>

        {/* Quick Links */}
        <div className="bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Acesso Rapido</h2>
          <div className="space-y-3">
            <Link
              to={`/eleicoes/${eleicao.id}/chapas`}
              className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div className="flex items-center gap-3">
                <Users className="h-5 w-5 text-primary" />
                <span className="font-medium">Ver Chapas</span>
              </div>
              <ChevronRight className="h-5 w-5 text-gray-400" />
            </Link>

            {(eleicao.status === 'apuracao' || eleicao.status === 'finalizada') && (
              <Link
                to={`/eleicoes/${eleicao.id}/resultados`}
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <Vote className="h-5 w-5 text-primary" />
                  <span className="font-medium">Ver Resultados</span>
                </div>
                <ChevronRight className="h-5 w-5 text-gray-400" />
              </Link>
            )}

            {eleicao.edital && (
              <a
                href={eleicao.edital}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <FileText className="h-5 w-5 text-primary" />
                  <span className="font-medium">Edital</span>
                </div>
                <ChevronRight className="h-5 w-5 text-gray-400" />
              </a>
            )}

            {eleicao.regulamento && (
              <a
                href={eleicao.regulamento}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <FileText className="h-5 w-5 text-primary" />
                  <span className="font-medium">Regulamento</span>
                </div>
                <ChevronRight className="h-5 w-5 text-gray-400" />
              </a>
            )}
          </div>
        </div>
      </div>

      {/* Timeline */}
      <div className="bg-white p-6 rounded-lg shadow-sm border">
        <h2 className="text-lg font-semibold text-gray-900 mb-6">Cronograma da Eleicao</h2>
        <div className="space-y-4">
          {fases.map((fase, index) => {
            const config = faseStatusConfig[fase.status]
            const isLast = index === fases.length - 1

            return (
              <div key={fase.id} className="flex gap-4">
                <div className="flex flex-col items-center">
                  <div className={`w-3 h-3 rounded-full ${config.dotColor}`} />
                  {!isLast && (
                    <div className={`w-0.5 flex-1 mt-1 ${
                      fase.status === 'concluida' ? 'bg-primary' : 'bg-gray-200'
                    }`} />
                  )}
                </div>
                <div className="flex-1 pb-4">
                  <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                    <p className={`font-medium ${
                      fase.status === 'em_andamento' ? 'text-green-600' : 'text-gray-900'
                    }`}>
                      {fase.nome}
                      {fase.status === 'em_andamento' && (
                        <span className="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                          Em andamento
                        </span>
                      )}
                    </p>
                    <p className="text-sm text-gray-500">
                      {new Date(fase.dataInicio).toLocaleDateString('pt-BR')}
                      {fase.dataInicio !== fase.dataFim && (
                        <> a {new Date(fase.dataFim).toLocaleDateString('pt-BR')}</>
                      )}
                    </p>
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      </div>
    </div>
  )
}
