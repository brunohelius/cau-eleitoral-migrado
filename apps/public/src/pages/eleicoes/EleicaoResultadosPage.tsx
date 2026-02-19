import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Trophy,
  Users,
  Vote,
  BarChart3,
  Loader2,
  AlertCircle,
  PieChart,
  CheckCircle,
  Clock,
} from 'lucide-react'
import { eleicoesPublicService, ResultadoEleicao } from '../../services/eleicoes'

const chapaColors = ['blue', 'green', 'purple', 'red', 'yellow', 'cyan', 'orange', 'pink']

const colorConfig: Record<string, { bg: string; light: string; text: string }> = {
  blue: { bg: 'bg-blue-600', light: 'bg-blue-100', text: 'text-blue-600' },
  green: { bg: 'bg-green-600', light: 'bg-green-100', text: 'text-green-600' },
  purple: { bg: 'bg-purple-600', light: 'bg-purple-100', text: 'text-purple-600' },
  red: { bg: 'bg-red-600', light: 'bg-red-100', text: 'text-red-600' },
  yellow: { bg: 'bg-yellow-600', light: 'bg-yellow-100', text: 'text-yellow-600' },
  cyan: { bg: 'bg-cyan-600', light: 'bg-cyan-100', text: 'text-cyan-600' },
  orange: { bg: 'bg-orange-600', light: 'bg-orange-100', text: 'text-orange-600' },
  pink: { bg: 'bg-pink-600', light: 'bg-pink-100', text: 'text-pink-600' },
}

export function EleicaoResultadosPage() {
  const { id } = useParams<{ id: string }>()
  const [resultado, setResultado] = useState<ResultadoEleicao | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (id) loadResultados()
  }, [id])

  const loadResultados = async () => {
    try {
      setIsLoading(true)
      setError(null)
      const data = await eleicoesPublicService.getResultado(id!)
      setResultado(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Erro ao carregar resultados')
    } finally {
      setIsLoading(false)
    }
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando resultados...</span>
      </div>
    )
  }

  if (error || !resultado) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar resultados</p>
        <p className="text-gray-500 text-sm">{error || 'Resultado nao encontrado'}</p>
        <Link
          to="/eleicoes"
          className="mt-4 text-primary hover:underline"
        >
          Voltar para eleicoes
        </Link>
      </div>
    )
  }

  const chapaVencedora = resultado.resultadosChapas.find(c => c.vencedora)
  const isOficial = resultado.publicado || resultado.homologado
  const participacao = resultado.percentualParticipacao

  return (
    <div className="space-y-6">
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
              Resultados
            </h1>
            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium ${
              isOficial ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
            }`}>
              {isOficial ? (
                <>
                  <CheckCircle className="h-4 w-4" />
                  Resultado Oficial
                </>
              ) : (
                <>
                  <Clock className="h-4 w-4" />
                  Em Apuracao
                </>
              )}
            </span>
          </div>
          <p className="text-gray-600 mt-1">{resultado.eleicaoNome}</p>
        </div>
      </div>

      {/* Winner Card */}
      {chapaVencedora && isOficial && (
        <div className="bg-gradient-to-r from-yellow-50 to-yellow-100 border-2 border-yellow-300 rounded-xl p-6">
          <div className="flex flex-col sm:flex-row items-center gap-4">
            <div className="p-3 bg-yellow-200 rounded-full">
              <Trophy className="h-8 w-8 text-yellow-700" />
            </div>
            <div className="text-center sm:text-left">
              <p className="text-yellow-800 text-sm font-medium">Chapa Eleita</p>
              <h2 className="text-2xl font-bold text-yellow-900">
                {chapaVencedora.nome}
                {chapaVencedora.sigla ? ` (${chapaVencedora.sigla})` : ''}
              </h2>
              <p className="text-yellow-700">
                {chapaVencedora.totalVotos.toLocaleString()} votos ({chapaVencedora.percentual.toFixed(1)}%)
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
              <p className="text-2xl font-bold text-gray-900">{participacao.toFixed(1)}%</p>
              <p className="text-sm text-gray-500">Participação</p>
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
      {resultado.resultadosChapas.length > 0 && (
        <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
          <div className="p-6 border-b">
            <h2 className="text-lg font-semibold text-gray-900">Votação por Chapa</h2>
          </div>
          <div className="p-6 space-y-6">
            {resultado.resultadosChapas
              .sort((a, b) => a.posicao - b.posicao)
              .map((chapa, index) => {
                const cor = chapaColors[index % chapaColors.length]
                const colors = colorConfig[cor]
                return (
                  <div key={chapa.chapaId}>
                    <div className="flex items-center justify-between mb-2">
                      <div className="flex items-center gap-3">
                        <div className={`w-10 h-10 ${colors.light} rounded-lg flex items-center justify-center`}>
                          <span className={`text-lg font-bold ${colors.text}`}>{chapa.numero}</span>
                        </div>
                        <div>
                          <div className="flex items-center gap-2">
                            <span className="font-semibold text-gray-900">
                              {chapa.nome}
                              {chapa.sigla ? ` (${chapa.sigla})` : ''}
                            </span>
                            {chapa.vencedora && (
                              <span className="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                <Trophy className="h-3 w-3" />
                                Eleita
                              </span>
                            )}
                          </div>
                          <span className="text-sm text-gray-500">
                            {chapa.totalVotos.toLocaleString()} votos
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
                        style={{ width: `${Math.min(chapa.percentual, 100)}%` }}
                      />
                    </div>
                  </div>
                )
              })}
          </div>
        </div>
      )}

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
              {resultado.votosAnulados > 0 && (
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">Votos Anulados</span>
                  <span className="font-semibold">{resultado.votosAnulados.toLocaleString()}</span>
                </div>
              )}
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
            <h2 className="text-lg font-semibold text-gray-900">Informações de Participação</h2>
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
                <span className="font-semibold">{resultado.totalAbstencoes.toLocaleString()}</span>
              </div>
              <div className="pt-4 border-t flex items-center justify-between">
                <span className="text-gray-900 font-medium">Taxa de Participação</span>
                <span className="font-bold text-lg text-green-600">{participacao.toFixed(1)}%</span>
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
      </div>
    </div>
  )
}
