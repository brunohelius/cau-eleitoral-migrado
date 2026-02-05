import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Trophy,
  Users,
  Vote,
  BarChart3,
  Download,
  Clock,
  CheckCircle,
  Loader2,
  AlertCircle,
  PieChart,
} from 'lucide-react'

// Types
interface ResultadoChapa {
  id: string
  numero: number
  nome: string
  votos: number
  percentual: number
  cor: string
  eleita: boolean
}

interface ResultadoEleicao {
  eleicaoId: string
  eleicaoNome: string
  status: 'em_apuracao' | 'parcial' | 'oficial'
  totalEleitores: number
  totalVotos: number
  votosValidos: number
  votosBrancos: number
  votosNulos: number
  participacao: number
  chapas: ResultadoChapa[]
  dataApuracao?: string
  ultimaAtualizacao: string
}

// Mock data
const mockResultado: ResultadoEleicao = {
  eleicaoId: '1',
  eleicaoNome: 'Eleicao Ordinaria 2024',
  status: 'oficial',
  totalEleitores: 45000,
  totalVotos: 32500,
  votosValidos: 31200,
  votosBrancos: 800,
  votosNulos: 500,
  participacao: 72.2,
  dataApuracao: '2024-03-26T10:30:00',
  ultimaAtualizacao: '2024-03-26T10:30:00',
  chapas: [
    { id: '1', numero: 1, nome: 'Chapa Renovacao', votos: 14500, percentual: 46.5, cor: 'blue', eleita: true },
    { id: '2', numero: 2, nome: 'Chapa Uniao', votos: 10200, percentual: 32.7, cor: 'green', eleita: false },
    { id: '3', numero: 3, nome: 'Chapa Futuro', votos: 6500, percentual: 20.8, cor: 'purple', eleita: false },
  ],
}

const colorConfig: Record<string, { bg: string; light: string; text: string }> = {
  blue: { bg: 'bg-blue-600', light: 'bg-blue-100', text: 'text-blue-600' },
  green: { bg: 'bg-green-600', light: 'bg-green-100', text: 'text-green-600' },
  purple: { bg: 'bg-purple-600', light: 'bg-purple-100', text: 'text-purple-600' },
  red: { bg: 'bg-red-600', light: 'bg-red-100', text: 'text-red-600' },
  yellow: { bg: 'bg-yellow-600', light: 'bg-yellow-100', text: 'text-yellow-600' },
}

const statusConfig = {
  em_apuracao: { label: 'Em Apuracao', color: 'bg-yellow-100 text-yellow-800', icon: Clock },
  parcial: { label: 'Resultado Parcial', color: 'bg-blue-100 text-blue-800', icon: BarChart3 },
  oficial: { label: 'Resultado Oficial', color: 'bg-green-100 text-green-800', icon: CheckCircle },
}

export function EleicaoResultadosPage() {
  const { id } = useParams<{ id: string }>()
  const [isLoading] = useState(false)
  const [error] = useState<string | null>(null)

  // In a real app, fetch from API
  const resultado = mockResultado

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando resultados...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar resultados</p>
        <p className="text-gray-500 text-sm">{error}</p>
        <Link
          to={`/eleicoes/${id}`}
          className="mt-4 text-primary hover:underline"
        >
          Voltar para a eleicao
        </Link>
      </div>
    )
  }

  const statusInfo = statusConfig[resultado.status]
  const StatusIcon = statusInfo.icon
  const chapaVencedora = resultado.chapas.find(c => c.eleita)

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        <Link
          to={`/eleicoes/${id}`}
          className="inline-flex items-center text-gray-600 hover:text-gray-900"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <div className="flex-1">
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
              Resultados
            </h1>
            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium ${statusInfo.color}`}>
              <StatusIcon className="h-4 w-4" />
              {statusInfo.label}
            </span>
          </div>
          <p className="text-gray-600 mt-1">{resultado.eleicaoNome}</p>
        </div>
        {resultado.status === 'oficial' && (
          <button className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            <Download className="h-4 w-4" />
            Exportar
          </button>
        )}
      </div>

      {/* Winner Card */}
      {chapaVencedora && resultado.status === 'oficial' && (
        <div className="bg-gradient-to-r from-yellow-50 to-yellow-100 border-2 border-yellow-300 rounded-xl p-6">
          <div className="flex flex-col sm:flex-row items-center gap-4">
            <div className="p-3 bg-yellow-200 rounded-full">
              <Trophy className="h-8 w-8 text-yellow-700" />
            </div>
            <div className="text-center sm:text-left">
              <p className="text-yellow-800 text-sm font-medium">Chapa Eleita</p>
              <h2 className="text-2xl font-bold text-yellow-900">{chapaVencedora.nome}</h2>
              <p className="text-yellow-700">
                {chapaVencedora.votos.toLocaleString()} votos ({chapaVencedora.percentual.toFixed(1)}%)
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <Users className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{resultado.totalEleitores.toLocaleString()}</p>
              <p className="text-sm text-gray-500">Eleitores</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <Vote className="h-5 w-5 text-green-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{resultado.totalVotos.toLocaleString()}</p>
              <p className="text-sm text-gray-500">Votos Totais</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-purple-100 rounded-lg">
              <BarChart3 className="h-5 w-5 text-purple-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{resultado.participacao.toFixed(1)}%</p>
              <p className="text-sm text-gray-500">Participacao</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-gray-100 rounded-lg">
              <PieChart className="h-5 w-5 text-gray-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{resultado.votosValidos.toLocaleString()}</p>
              <p className="text-sm text-gray-500">Votos Validos</p>
            </div>
          </div>
        </div>
      </div>

      {/* Results by Chapa */}
      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div className="p-6 border-b">
          <h2 className="text-lg font-semibold text-gray-900">Votacao por Chapa</h2>
        </div>
        <div className="p-6 space-y-6">
          {resultado.chapas.map((chapa) => {
            const colors = colorConfig[chapa.cor] || colorConfig.blue
            return (
              <div key={chapa.id}>
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-3">
                    <div className={`w-10 h-10 ${colors.light} rounded-lg flex items-center justify-center`}>
                      <span className={`text-lg font-bold ${colors.text}`}>{chapa.numero}</span>
                    </div>
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-semibold text-gray-900">{chapa.nome}</span>
                        {chapa.eleita && (
                          <span className="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                            <Trophy className="h-3 w-3" />
                            Eleita
                          </span>
                        )}
                      </div>
                      <span className="text-sm text-gray-500">
                        {chapa.votos.toLocaleString()} votos
                      </span>
                    </div>
                  </div>
                  <span className="text-xl font-bold text-gray-900">
                    {chapa.percentual.toFixed(1)}%
                  </span>
                </div>
                <div className="h-4 bg-gray-100 rounded-full overflow-hidden">
                  <div
                    className={`h-full ${colors.bg} transition-all duration-500`}
                    style={{ width: `${chapa.percentual}%` }}
                  />
                </div>
              </div>
            )
          })}
        </div>
      </div>

      {/* Additional Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Vote Distribution */}
        <div className="bg-white rounded-lg shadow-sm border">
          <div className="p-6 border-b">
            <h2 className="text-lg font-semibold text-gray-900">Distribuicao dos Votos</h2>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Votos Validos</span>
                <span className="font-semibold">{resultado.votosValidos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Votos em Branco</span>
                <span className="font-semibold">{resultado.votosBrancos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Votos Nulos</span>
                <span className="font-semibold">{resultado.votosNulos.toLocaleString()}</span>
              </div>
              <div className="pt-4 border-t flex items-center justify-between">
                <span className="text-gray-900 font-medium">Total de Votos</span>
                <span className="font-bold text-lg">{resultado.totalVotos.toLocaleString()}</span>
              </div>
            </div>
          </div>
        </div>

        {/* Participation Info */}
        <div className="bg-white rounded-lg shadow-sm border">
          <div className="p-6 border-b">
            <h2 className="text-lg font-semibold text-gray-900">Informacoes de Participacao</h2>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Eleitores Aptos</span>
                <span className="font-semibold">{resultado.totalEleitores.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Eleitores que Votaram</span>
                <span className="font-semibold">{resultado.totalVotos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-gray-600">Abstencoes</span>
                <span className="font-semibold">{(resultado.totalEleitores - resultado.totalVotos).toLocaleString()}</span>
              </div>
              <div className="pt-4 border-t flex items-center justify-between">
                <span className="text-gray-900 font-medium">Taxa de Participacao</span>
                <span className="font-bold text-lg text-green-600">{resultado.participacao.toFixed(1)}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Footer Info */}
      <div className="text-center text-sm text-gray-500 space-y-1">
        {resultado.dataApuracao && (
          <p>Apuracao realizada em: {new Date(resultado.dataApuracao).toLocaleString('pt-BR')}</p>
        )}
        <p>Ultima atualizacao: {new Date(resultado.ultimaAtualizacao).toLocaleString('pt-BR')}</p>
      </div>
    </div>
  )
}
