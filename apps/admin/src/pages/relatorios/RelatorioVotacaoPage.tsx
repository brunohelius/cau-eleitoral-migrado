import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  Vote,
  Download,
  Printer,
  Filter,
  Users,
  TrendingUp,
  Loader2,
  BarChart3,
  PieChart,
  Clock,
  MapPin,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import api from '@/services/api'

interface DadosVotacao {
  eleicaoId: string
  eleicaoNome: string
  totalEleitores: number
  totalVotos: number
  votosValidos: number
  votosBrancos: number
  votosNulos: number
  participacao: number
  chapas: Array<{
    id: string
    numero: number
    nome: string
    votos: number
    percentual: number
  }>
  votosPorHora: Array<{
    hora: string
    votos: number
  }>
  votosPorRegional: Array<{
    regional: string
    votos: number
    participacao: number
  }>
}

export function RelatorioVotacaoPage() {
  const [selectedEleicao, setSelectedEleicao] = useState<string>('')

  // Load elections from API
  const { data: eleicoes = [] } = useQuery({
    queryKey: ['eleições-votação-relatório'],
    queryFn: async () => {
      try {
        const response = await api.get('/eleicao')
        const apiData = Array.isArray(response.data) ? response.data : response.data?.data || []
        return apiData.map((e: any) => ({
          id: e.id,
          nome: e.nome || e.titulo || '',
        }))
      } catch {
        return []
      }
    },
  })

  // Load voting data for selected election
  const { data: dadosVotacao, isLoading } = useQuery({
    queryKey: ['votacao-relatório', selectedEleicao],
    queryFn: async (): Promise<DadosVotacao | null> => {
      try {
        // Try to get apuracao results first
        const response = await api.get(`/apuracao/${selectedEleicao}`)
        const r = response.data

        const chapas = (r.resultadosChapas || []).map((c: any) => ({
          id: c.chapaId,
          numero: c.numero,
          nome: c.nome,
          votos: c.totalVotos,
          percentual: Number(c.percentualVotosValidos) || Number(c.percentual) || 0,
        }))

        // Try to get statistics for time/regional data
        let votosPorHora: DadosVotacao['votosPorHora'] = []
        let votosPorRegional: DadosVotacao['votosPorRegional'] = []

        try {
          const statsResponse = await api.get(`/apuracao/${selectedEleicao}/estatisticas`)
          const stats = statsResponse.data
          votosPorHora = (stats.votosPorHora || []).map((v: any) => ({
            hora: new Date(v.hora).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
            votos: v.totalVotos,
          }))
          votosPorRegional = (stats.votosPorRegiao || []).map((v: any) => ({
            regional: v.nomeRegiao || v.uf || '',
            votos: v.totalVotantes || 0,
            participacao: Number(v.taxaParticipacao) || 0,
          }))
        } catch {
          // Statistics endpoint may require auth or return empty
        }

        const eleicaoInfo = eleicoes.find((e: any) => e.id === selectedEleicao)

        return {
          eleicaoId: selectedEleicao,
          eleicaoNome: r.eleicaoNome || eleicaoInfo?.nome || '',
          totalEleitores: r.totalEleitores || 0,
          totalVotos: r.totalVotos || 0,
          votosValidos: r.votosValidos || 0,
          votosBrancos: r.votosBrancos || 0,
          votosNulos: r.votosNulos || 0,
          participacao: Number(r.percentualParticipacao) || 0,
          chapas,
          votosPorHora,
          votosPorRegional,
        }
      } catch {
        return null
      }
    },
    enabled: !!selectedEleicao,
  })

  const handleExport = (format: 'pdf' | 'excel') => {
    console.log(`Exportando relatorio em ${format}...`)
  }

  const handlePrint = () => {
    window.print()
  }

  // Encontrar maior valor para escala do grafico
  const maxVotos = dadosVotacao?.votosPorHora.reduce((max, item) => Math.max(max, item.votos), 0) || 1

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/relatorios">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Relatórios de Votação</h1>
            <p className="text-gray-600">Estatisticas e resultados detalhados</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handlePrint}>
            <Printer className="mr-2 h-4 w-4" />
            Imprimir
          </Button>
          <Button variant="outline" onClick={() => handleExport('excel')}>
            <Download className="mr-2 h-4 w-4" />
            Excel
          </Button>
          <Button onClick={() => handleExport('pdf')}>
            <Download className="mr-2 h-4 w-4" />
            PDF
          </Button>
        </div>
      </div>

      {/* Filtro de Eleicao */}
      <Card>
        <CardContent className="pt-6">
          <div className="flex items-center gap-4">
            <Filter className="h-5 w-5 text-gray-400" />
            <div className="flex-1 max-w-xs">
              <Label className="sr-only">Eleição</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={selectedEleicao}
                onChange={(e) => setSelectedEleicao(e.target.value)}
              >
                <option value="">Selecione uma eleição</option>
                {eleicoes.map((eleicao: any) => (
                  <option key={eleicao.id} value={eleicao.id}>
                    {eleicao.nome}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </CardContent>
      </Card>

      {!selectedEleicao ? (
        <Card>
          <CardContent className="py-12 text-center">
            <Vote className="h-12 w-12 mx-auto text-gray-300 mb-4" />
            <p className="text-gray-500">Selecione uma eleição para visualizar os dados de votação</p>
          </CardContent>
        </Card>
      ) : isLoading ? (
        <div className="flex items-center justify-center h-64">
          <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
        </div>
      ) : dadosVotacao ? (
        <>
          {/* Cards de Estatisticas */}
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center gap-3">
                  <div className="p-2 rounded-lg bg-blue-100">
                    <Users className="h-6 w-6 text-blue-600" />
                  </div>
                  <div>
                    <p className="text-2xl font-bold">{dadosVotacao.totalEleitores.toLocaleString()}</p>
                    <p className="text-sm text-gray-500">Eleitores Aptos</p>
                  </div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center gap-3">
                  <div className="p-2 rounded-lg bg-green-100">
                    <Vote className="h-6 w-6 text-green-600" />
                  </div>
                  <div>
                    <p className="text-2xl font-bold">{dadosVotacao.totalVotos.toLocaleString()}</p>
                    <p className="text-sm text-gray-500">Total de Votos</p>
                  </div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center gap-3">
                  <div className="p-2 rounded-lg bg-purple-100">
                    <TrendingUp className="h-6 w-6 text-purple-600" />
                  </div>
                  <div>
                    <p className="text-2xl font-bold">{dadosVotacao.participacao}%</p>
                    <p className="text-sm text-gray-500">Participação</p>
                  </div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="pt-6">
                <div className="flex items-center gap-3">
                  <div className="p-2 rounded-lg bg-orange-100">
                    <BarChart3 className="h-6 w-6 text-orange-600" />
                  </div>
                  <div>
                    <p className="text-2xl font-bold">{dadosVotacao.votosValidos.toLocaleString()}</p>
                    <p className="text-sm text-gray-500">Votos Validos</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="grid gap-6 lg:grid-cols-2">
            {/* Resultado por Chapa */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <PieChart className="h-5 w-5" />
                  Resultado por Chapa
                </CardTitle>
                <CardDescription>Distribuicao dos votos validos</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {dadosVotacao.chapas.length > 0 ? dadosVotacao.chapas.map((chapa, index) => (
                    <div key={chapa.id} className="space-y-2">
                      <div className="flex items-center justify-between">
                        <span className="text-sm font-medium">
                          {chapa.numero}. {chapa.nome}
                        </span>
                        <span className="text-sm text-gray-500">
                          {chapa.votos.toLocaleString()} ({chapa.percentual.toFixed(2)}%)
                        </span>
                      </div>
                      <div className="h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                        <div
                          className={`h-full rounded-full transition-all duration-500 ${
                            index === 0
                              ? 'bg-green-500'
                              : index === 1
                              ? 'bg-blue-500'
                              : index === 2
                              ? 'bg-yellow-500'
                              : 'bg-gray-400'
                          }`}
                          style={{ width: `${chapa.percentual}%` }}
                        />
                      </div>
                    </div>
                  )) : (
                    <p className="text-center py-4 text-gray-500">Nenhum dado disponível</p>
                  )}
                </div>

                <div className="mt-6 pt-4 border-t grid grid-cols-3 gap-4 text-center">
                  <div>
                    <p className="text-lg font-bold text-green-600">{dadosVotacao.votosValidos.toLocaleString()}</p>
                    <p className="text-xs text-gray-500">Validos</p>
                  </div>
                  <div>
                    <p className="text-lg font-bold text-gray-600">{dadosVotacao.votosBrancos.toLocaleString()}</p>
                    <p className="text-xs text-gray-500">Brancos</p>
                  </div>
                  <div>
                    <p className="text-lg font-bold text-red-600">{dadosVotacao.votosNulos.toLocaleString()}</p>
                    <p className="text-xs text-gray-500">Nulos</p>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Votos por Hora */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Clock className="h-5 w-5" />
                  Votos por Hora
                </CardTitle>
                <CardDescription>Distribuicao temporal da votação</CardDescription>
              </CardHeader>
              <CardContent>
                {dadosVotacao.votosPorHora.length > 0 ? (
                  <div className="space-y-2">
                    {dadosVotacao.votosPorHora.map((item) => (
                      <div key={item.hora} className="flex items-center gap-3">
                        <span className="w-12 text-sm text-gray-500">{item.hora}</span>
                        <div className="flex-1 h-6 rounded bg-gray-100 overflow-hidden">
                          <div
                            className="h-full bg-blue-500 rounded transition-all duration-500"
                            style={{ width: `${(item.votos / maxVotos) * 100}%` }}
                          />
                        </div>
                        <span className="w-16 text-sm text-right">{item.votos.toLocaleString()}</span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-center py-8 text-gray-500">Dados de distribuicao por hora nao disponíveis</p>
                )}
              </CardContent>
            </Card>

            {/* Votos por Regional */}
            {dadosVotacao.votosPorRegional.length > 0 && (
              <Card className="lg:col-span-2">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <MapPin className="h-5 w-5" />
                    Participação por Regional
                  </CardTitle>
                  <CardDescription>Distribuicao geografica dos votos</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead>
                        <tr className="border-b">
                          <th className="text-left py-3 px-4 font-medium">Regional</th>
                          <th className="text-right py-3 px-4 font-medium">Votos</th>
                          <th className="text-right py-3 px-4 font-medium">Participação</th>
                          <th className="text-left py-3 px-4 font-medium w-1/3">Grafico</th>
                        </tr>
                      </thead>
                      <tbody>
                        {dadosVotacao.votosPorRegional.map((item) => (
                          <tr key={item.regional} className="border-b hover:bg-gray-50">
                            <td className="py-3 px-4 font-medium">{item.regional}</td>
                            <td className="py-3 px-4 text-right">{item.votos.toLocaleString()}</td>
                            <td className="py-3 px-4 text-right">{item.participacao}%</td>
                            <td className="py-3 px-4">
                              <div className="h-4 w-full rounded-full bg-gray-100 overflow-hidden">
                                <div
                                  className="h-full bg-green-500 rounded-full transition-all duration-500"
                                  style={{ width: `${item.participacao}%` }}
                                />
                              </div>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </>
      ) : (
        <Card>
          <CardContent className="py-12 text-center">
            <BarChart3 className="h-12 w-12 mx-auto text-gray-300 mb-4" />
            <p className="text-gray-500">Nenhum dado de apuração disponível para esta eleição</p>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
