import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  Calendar,
  Download,
  Printer,
  Filter,
  Users,
  Vote,
  FileText,
  Loader2,
  BarChart3,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Label } from '@/components/ui/label'

interface Eleicao {
  id: string
  nome: string
  ano: number
  status: string
  totalChapas: number
  totalEleitores: number
  totalVotos: number
  participacao: number
}

export function RelatorioEleicaoPage() {
  const [selectedEleicao, setSelectedEleicao] = useState<string>('')
  const [tipoRelatorio, setTipoRelatorio] = useState<string>('resumo')

  // Mock dados - em producao viria da API
  const { data: eleicoes, isLoading } = useQuery({
    queryKey: ['eleicoes-relatorio'],
    queryFn: async () => {
      return [
        {
          id: '1',
          nome: 'Eleicao CAU/SP 2024',
          ano: 2024,
          status: 'finalizada',
          totalChapas: 4,
          totalEleitores: 15000,
          totalVotos: 12450,
          participacao: 83,
        },
        {
          id: '2',
          nome: 'Eleicao CAU/RJ 2024',
          ano: 2024,
          status: 'em_andamento',
          totalChapas: 3,
          totalEleitores: 12000,
          totalVotos: 8500,
          participacao: 70.8,
        },
        {
          id: '3',
          nome: 'Eleicao CAU/BR 2021',
          ano: 2021,
          status: 'finalizada',
          totalChapas: 5,
          totalEleitores: 180000,
          totalVotos: 145800,
          participacao: 81,
        },
      ] as Eleicao[]
    },
  })

  const selectedEleicaoData = eleicoes?.find((e) => e.id === selectedEleicao)

  const handleExport = (format: 'pdf' | 'excel') => {
    console.log(`Exportando relatorio em ${format}...`)
  }

  const handlePrint = () => {
    window.print()
  }

  const tiposRelatorio = [
    { value: 'resumo', label: 'Resumo Geral', icon: BarChart3 },
    { value: 'chapas', label: 'Chapas Participantes', icon: Users },
    { value: 'calendario', label: 'Calendario Eleitoral', icon: Calendar },
    { value: 'historico', label: 'Historico de Alteracoes', icon: FileText },
  ]

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
            <h1 className="text-2xl font-bold text-gray-900">Relatorios de Eleicao</h1>
            <p className="text-gray-600">Visualize e exporte informacoes das eleicoes</p>
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

      <div className="grid gap-6 lg:grid-cols-4">
        {/* Filtros */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Filter className="h-5 w-5" />
              Filtros
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>Eleicao</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={selectedEleicao}
                onChange={(e) => setSelectedEleicao(e.target.value)}
              >
                <option value="">Selecione uma eleicao</option>
                {eleicoes?.map((eleicao) => (
                  <option key={eleicao.id} value={eleicao.id}>
                    {eleicao.nome}
                  </option>
                ))}
              </select>
            </div>

            <div className="space-y-2">
              <Label>Tipo de Relatorio</Label>
              <div className="space-y-2">
                {tiposRelatorio.map((tipo) => (
                  <label
                    key={tipo.value}
                    className={`flex items-center gap-3 rounded-lg border p-3 cursor-pointer transition-colors ${
                      tipoRelatorio === tipo.value ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
                    }`}
                  >
                    <input
                      type="radio"
                      name="tipoRelatorio"
                      value={tipo.value}
                      checked={tipoRelatorio === tipo.value}
                      onChange={(e) => setTipoRelatorio(e.target.value)}
                      className="sr-only"
                    />
                    <tipo.icon className="h-4 w-4 text-gray-500" />
                    <span className="text-sm">{tipo.label}</span>
                  </label>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Conteudo do Relatorio */}
        <div className="lg:col-span-3 space-y-6">
          {isLoading ? (
            <div className="flex items-center justify-center h-64">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : !selectedEleicao ? (
            <Card>
              <CardContent className="py-12 text-center">
                <Calendar className="h-12 w-12 mx-auto text-gray-300 mb-4" />
                <p className="text-gray-500">Selecione uma eleicao para visualizar o relatorio</p>
              </CardContent>
            </Card>
          ) : (
            <>
              {/* Estatisticas Gerais */}
              <div className="grid gap-4 sm:grid-cols-4">
                <Card>
                  <CardContent className="pt-6">
                    <div className="flex items-center gap-3">
                      <Users className="h-8 w-8 text-blue-500" />
                      <div>
                        <p className="text-2xl font-bold">{selectedEleicaoData?.totalChapas}</p>
                        <p className="text-sm text-gray-500">Chapas</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
                <Card>
                  <CardContent className="pt-6">
                    <div className="flex items-center gap-3">
                      <Users className="h-8 w-8 text-green-500" />
                      <div>
                        <p className="text-2xl font-bold">
                          {selectedEleicaoData?.totalEleitores.toLocaleString()}
                        </p>
                        <p className="text-sm text-gray-500">Eleitores</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
                <Card>
                  <CardContent className="pt-6">
                    <div className="flex items-center gap-3">
                      <Vote className="h-8 w-8 text-purple-500" />
                      <div>
                        <p className="text-2xl font-bold">
                          {selectedEleicaoData?.totalVotos.toLocaleString()}
                        </p>
                        <p className="text-sm text-gray-500">Votos</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
                <Card>
                  <CardContent className="pt-6">
                    <div className="flex items-center gap-3">
                      <BarChart3 className="h-8 w-8 text-orange-500" />
                      <div>
                        <p className="text-2xl font-bold">{selectedEleicaoData?.participacao}%</p>
                        <p className="text-sm text-gray-500">Participacao</p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </div>

              {/* Conteudo baseado no tipo de relatorio */}
              <Card>
                <CardHeader>
                  <CardTitle>{selectedEleicaoData?.nome}</CardTitle>
                  <CardDescription>
                    {tiposRelatorio.find((t) => t.value === tipoRelatorio)?.label}
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  {tipoRelatorio === 'resumo' && (
                    <div className="space-y-6">
                      <div className="grid gap-4 sm:grid-cols-2">
                        <div className="rounded-lg bg-gray-50 p-4">
                          <h4 className="font-medium mb-2">Informacoes Gerais</h4>
                          <dl className="space-y-2 text-sm">
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Ano</dt>
                              <dd className="font-medium">{selectedEleicaoData?.ano}</dd>
                            </div>
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Status</dt>
                              <dd className="font-medium capitalize">{selectedEleicaoData?.status.replace('_', ' ')}</dd>
                            </div>
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Total de Chapas</dt>
                              <dd className="font-medium">{selectedEleicaoData?.totalChapas}</dd>
                            </div>
                          </dl>
                        </div>
                        <div className="rounded-lg bg-gray-50 p-4">
                          <h4 className="font-medium mb-2">Votacao</h4>
                          <dl className="space-y-2 text-sm">
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Eleitores Aptos</dt>
                              <dd className="font-medium">{selectedEleicaoData?.totalEleitores.toLocaleString()}</dd>
                            </div>
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Votos Computados</dt>
                              <dd className="font-medium">{selectedEleicaoData?.totalVotos.toLocaleString()}</dd>
                            </div>
                            <div className="flex justify-between">
                              <dt className="text-gray-500">Participacao</dt>
                              <dd className="font-medium">{selectedEleicaoData?.participacao}%</dd>
                            </div>
                          </dl>
                        </div>
                      </div>
                    </div>
                  )}

                  {tipoRelatorio === 'chapas' && (
                    <div className="overflow-x-auto">
                      <table className="w-full">
                        <thead>
                          <tr className="border-b">
                            <th className="text-left py-3 px-4 font-medium">Numero</th>
                            <th className="text-left py-3 px-4 font-medium">Nome</th>
                            <th className="text-left py-3 px-4 font-medium">Candidato</th>
                            <th className="text-left py-3 px-4 font-medium">Votos</th>
                            <th className="text-left py-3 px-4 font-medium">Percentual</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr className="border-b">
                            <td className="py-3 px-4">1</td>
                            <td className="py-3 px-4">Chapa Renovacao</td>
                            <td className="py-3 px-4">Joao Silva</td>
                            <td className="py-3 px-4">5.200</td>
                            <td className="py-3 px-4">44,07%</td>
                          </tr>
                          <tr className="border-b">
                            <td className="py-3 px-4">2</td>
                            <td className="py-3 px-4">Chapa Unidade</td>
                            <td className="py-3 px-4">Maria Santos</td>
                            <td className="py-3 px-4">3.800</td>
                            <td className="py-3 px-4">32,20%</td>
                          </tr>
                          <tr className="border-b">
                            <td className="py-3 px-4">3</td>
                            <td className="py-3 px-4">Chapa Mudanca</td>
                            <td className="py-3 px-4">Carlos Oliveira</td>
                            <td className="py-3 px-4">2.100</td>
                            <td className="py-3 px-4">17,80%</td>
                          </tr>
                          <tr>
                            <td className="py-3 px-4">4</td>
                            <td className="py-3 px-4">Chapa Progresso</td>
                            <td className="py-3 px-4">Ana Costa</td>
                            <td className="py-3 px-4">700</td>
                            <td className="py-3 px-4">5,93%</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  )}

                  {tipoRelatorio === 'calendario' && (
                    <div className="space-y-4">
                      <div className="flex items-center gap-4 rounded-lg border p-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                          <Calendar className="h-6 w-6 text-blue-600" />
                        </div>
                        <div className="flex-1">
                          <h4 className="font-medium">Inscricao de Chapas</h4>
                          <p className="text-sm text-gray-500">01/03/2024 - 15/03/2024</p>
                        </div>
                        <span className="text-sm text-green-600">Concluido</span>
                      </div>
                      <div className="flex items-center gap-4 rounded-lg border p-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100">
                          <FileText className="h-6 w-6 text-orange-600" />
                        </div>
                        <div className="flex-1">
                          <h4 className="font-medium">Prazo de Impugnacao</h4>
                          <p className="text-sm text-gray-500">16/03/2024 - 20/03/2024</p>
                        </div>
                        <span className="text-sm text-green-600">Concluido</span>
                      </div>
                      <div className="flex items-center gap-4 rounded-lg border p-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                          <Users className="h-6 w-6 text-green-600" />
                        </div>
                        <div className="flex-1">
                          <h4 className="font-medium">Campanha Eleitoral</h4>
                          <p className="text-sm text-gray-500">21/03/2024 - 10/04/2024</p>
                        </div>
                        <span className="text-sm text-green-600">Concluido</span>
                      </div>
                      <div className="flex items-center gap-4 rounded-lg border p-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                          <Vote className="h-6 w-6 text-purple-600" />
                        </div>
                        <div className="flex-1">
                          <h4 className="font-medium">Periodo de Votacao</h4>
                          <p className="text-sm text-gray-500">11/04/2024 - 12/04/2024</p>
                        </div>
                        <span className="text-sm text-green-600">Concluido</span>
                      </div>
                    </div>
                  )}

                  {tipoRelatorio === 'historico' && (
                    <div className="relative">
                      <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
                      <div className="space-y-6">
                        {[
                          { data: '12/04/2024 18:00', acao: 'Eleicao finalizada', usuario: 'Sistema' },
                          { data: '12/04/2024 17:00', acao: 'Votacao encerrada', usuario: 'Sistema' },
                          { data: '11/04/2024 08:00', acao: 'Votacao iniciada', usuario: 'Sistema' },
                          { data: '10/04/2024 23:59', acao: 'Campanha encerrada', usuario: 'Sistema' },
                          { data: '01/03/2024 00:00', acao: 'Eleicao criada', usuario: 'Carlos Silva' },
                        ].map((item, index) => (
                          <div key={index} className="relative flex gap-4 pl-10">
                            <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                            <div className="flex-1">
                              <div className="flex items-center justify-between">
                                <span className="font-medium">{item.acao}</span>
                                <span className="text-xs text-gray-500">{item.data}</span>
                              </div>
                              <p className="text-sm text-gray-600">Por: {item.usuario}</p>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </CardContent>
              </Card>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
