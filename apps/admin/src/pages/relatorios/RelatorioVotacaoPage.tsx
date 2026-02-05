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
  const [selectedEleicao, setSelectedEleicao] = useState<string>('1')

  // Mock dados - em producao viria da API
  const { data: dadosVotacao, isLoading } = useQuery({
    queryKey: ['votacao-relatorio', selectedEleicao],
    queryFn: async () => {
      return {
        eleicaoId: '1',
        eleicaoNome: 'Eleicao CAU/SP 2024',
        totalEleitores: 15000,
        totalVotos: 12450,
        votosValidos: 11800,
        votosBrancos: 350,
        votosNulos: 300,
        participacao: 83,
        chapas: [
          { id: '1', numero: 1, nome: 'Chapa Renovacao', votos: 5200, percentual: 44.07 },
          { id: '2', numero: 2, nome: 'Chapa Unidade', votos: 3800, percentual: 32.2 },
          { id: '3', numero: 3, nome: 'Chapa Mudanca', votos: 2100, percentual: 17.8 },
          { id: '4', numero: 4, nome: 'Chapa Progresso', votos: 700, percentual: 5.93 },
        ],
        votosPorHora: [
          { hora: '08:00', votos: 450 },
          { hora: '09:00', votos: 890 },
          { hora: '10:00', votos: 1230 },
          { hora: '11:00', votos: 1450 },
          { hora: '12:00', votos: 980 },
          { hora: '13:00', votos: 1100 },
          { hora: '14:00', votos: 1350 },
          { hora: '15:00', votos: 1500 },
          { hora: '16:00', votos: 1800 },
          { hora: '17:00', votos: 1700 },
        ],
        votosPorRegional: [
          { regional: 'Capital', votos: 5200, participacao: 85 },
          { regional: 'Grande SP', votos: 3100, participacao: 82 },
          { regional: 'Campinas', votos: 1800, participacao: 80 },
          { regional: 'Ribeirao Preto', votos: 1200, participacao: 78 },
          { regional: 'Santos', votos: 1150, participacao: 81 },
        ],
      } as DadosVotacao
    },
    enabled: !!selectedEleicao,
  })

  const handleExport = (format: 'pdf' | 'excel') => {
    console.log(`Exportando relatorio em ${format}...`)
  }

  const handlePrint = () => {
    window.print()
  }

  const eleicoes = [
    { id: '1', nome: 'Eleicao CAU/SP 2024' },
    { id: '2', nome: 'Eleicao CAU/RJ 2024' },
    { id: '3', nome: 'Eleicao CAU/BR 2021' },
  ]

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
            <h1 className="text-2xl font-bold text-gray-900">Relatorios de Votacao</h1>
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
              <Label className="sr-only">Eleicao</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={selectedEleicao}
                onChange={(e) => setSelectedEleicao(e.target.value)}
              >
                {eleicoes.map((eleicao) => (
                  <option key={eleicao.id} value={eleicao.id}>
                    {eleicao.nome}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </CardContent>
      </Card>

      {isLoading ? (
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
                    <p className="text-sm text-gray-500">Participacao</p>
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
                  {dadosVotacao.chapas.map((chapa, index) => (
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
                  ))}
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
                <CardDescription>Distribuicao temporal da votacao</CardDescription>
              </CardHeader>
              <CardContent>
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
              </CardContent>
            </Card>

            {/* Votos por Regional */}
            <Card className="lg:col-span-2">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MapPin className="h-5 w-5" />
                  Participacao por Regional
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
                        <th className="text-right py-3 px-4 font-medium">Participacao</th>
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
          </div>
        </>
      ) : null}
    </div>
  )
}
