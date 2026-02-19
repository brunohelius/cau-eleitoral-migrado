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
import api from '@/services/api'

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

const mapStatusEleicao = (status: number): string => {
  switch (status) {
    case 0: return 'criada'
    case 1: return 'inscricoes_abertas'
    case 2: return 'em_campanha'
    case 3: return 'em_votacao'
    case 4: return 'finalizada'
    case 5: return 'cancelada'
    default: return 'criada'
  }
}

export function RelatorioEleicaoPage() {
  const [selectedEleicao, setSelectedEleicao] = useState<string>('')
  const [tipoRelatorio, setTipoRelatorio] = useState<string>('resumo')

  const { data: eleicoes, isLoading } = useQuery({
    queryKey: ['eleições-relatório'],
    queryFn: async () => {
      try {
        const response = await api.get('/eleicao')
        const apiData = Array.isArray(response.data) ? response.data : response.data?.data || []
        return apiData.map((e: any) => ({
          id: e.id,
          nome: e.nome || e.titulo || '',
          ano: e.ano || new Date(e.dataInicio || e.createdAt).getFullYear(),
          status: typeof e.status === 'number' ? mapStatusEleicao(e.status) : (e.status || 'criada'),
          totalChapas: e.totalChapas || 0,
          totalEleitores: e.totalEleitores || 0,
          totalVotos: e.totalVotos || 0,
          participacao: e.totalEleitores > 0
            ? Math.round((e.totalVotos / e.totalEleitores) * 100 * 10) / 10
            : 0,
        })) as Eleicao[]
      } catch {
        return [] as Eleicao[]
      }
    },
  })

  // Load calendario for selected election
  const { data: calendario } = useQuery({
    queryKey: ['calendário-relatório', selectedEleicao],
    queryFn: async () => {
      try {
        const response = await api.get('/calendario', { params: { eleicaoId: selectedEleicao } })
        return Array.isArray(response.data) ? response.data : []
      } catch {
        return []
      }
    },
    enabled: !!selectedEleicao,
  })

  // Load chapas for selected election
  const { data: chapas } = useQuery({
    queryKey: ['chapas-relatório', selectedEleicao],
    queryFn: async () => {
      try {
        const response = await api.get(`/chapa/eleicao/${selectedEleicao}`)
        return Array.isArray(response.data) ? response.data : (response.data?.items ?? [])
      } catch {
        return []
      }
    },
    enabled: !!selectedEleicao,
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
    { value: 'calendario', label: 'Calendário Eleitoral', icon: Calendar },
    { value: 'historico', label: 'Histórico de Alterações', icon: FileText },
  ]

  const mapTipoCalendario = (tipo: number): string => {
    const tipos: Record<number, string> = {
      0: 'Inscricao de Chapas',
      1: 'Prazo de Impugnacao',
      2: 'Campanha Eleitoral',
      3: 'Periodo de Votacao',
      4: 'Apuracao',
      5: 'Prazo de Recursos',
      6: 'Diplomacao',
    }
    return tipos[tipo] || 'Evento'
  }

  const mapStatusCalendario = (status: number): string => {
    switch (status) {
      case 0: return 'Agendado'
      case 1: return 'Em Andamento'
      case 2: return 'Concluido'
      case 3: return 'Cancelado'
      default: return 'Agendado'
    }
  }

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
            <h1 className="text-2xl font-bold text-gray-900">Relatórios de Eleição</h1>
            <p className="text-gray-600">Visualize e exporte informações das eleições</p>
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
              <Label>Eleição</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={selectedEleicao}
                onChange={(e) => setSelectedEleicao(e.target.value)}
              >
                <option value="">Selecione uma eleição</option>
                {eleicoes?.map((eleicao) => (
                  <option key={eleicao.id} value={eleicao.id}>
                    {eleicao.nome}
                  </option>
                ))}
              </select>
            </div>

            <div className="space-y-2">
              <Label>Tipo de Relatório</Label>
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
                <p className="text-gray-500">Selecione uma eleição para visualizar o relatório</p>
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
                        <p className="text-sm text-gray-500">Participação</p>
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
                          <h4 className="font-medium mb-2">Informações Gerais</h4>
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
                          <h4 className="font-medium mb-2">Votação</h4>
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
                              <dt className="text-gray-500">Participação</dt>
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
                            <th className="text-left py-3 px-4 font-medium">Número</th>
                            <th className="text-left py-3 px-4 font-medium">Nome</th>
                            <th className="text-left py-3 px-4 font-medium">Candidato</th>
                            <th className="text-left py-3 px-4 font-medium">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          {chapas && chapas.length > 0 ? chapas.map((chapa: any) => (
                            <tr key={chapa.id} className="border-b">
                              <td className="py-3 px-4">{chapa.numero || '-'}</td>
                              <td className="py-3 px-4">{chapa.nome || chapa.nomeChapa || '-'}</td>
                              <td className="py-3 px-4">{chapa.candidatoNome || chapa.presidente || '-'}</td>
                              <td className="py-3 px-4 capitalize">{typeof chapa.status === 'number' ? ['Pendente', 'Aprovada', 'Rejeitada', 'Registrada'][chapa.status] || '-' : chapa.status || '-'}</td>
                            </tr>
                          )) : (
                            <tr>
                              <td colSpan={4} className="py-8 text-center text-gray-500">Nenhuma chapa encontrada</td>
                            </tr>
                          )}
                        </tbody>
                      </table>
                    </div>
                  )}

                  {tipoRelatorio === 'calendario' && (
                    <div className="space-y-4">
                      {calendario && calendario.length > 0 ? calendario.map((evento: any) => (
                        <div key={evento.id} className="flex items-center gap-4 rounded-lg border p-4">
                          <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <Calendar className="h-6 w-6 text-blue-600" />
                          </div>
                          <div className="flex-1">
                            <h4 className="font-medium">{evento.titulo || mapTipoCalendario(evento.tipo)}</h4>
                            <p className="text-sm text-gray-500">
                              {new Date(evento.dataInicio).toLocaleDateString('pt-BR')} - {new Date(evento.dataFim).toLocaleDateString('pt-BR')}
                            </p>
                          </div>
                          <span className={`text-sm ${evento.status === 2 ? 'text-green-600' : evento.status === 1 ? 'text-blue-600' : 'text-gray-600'}`}>
                            {mapStatusCalendario(evento.status)}
                          </span>
                        </div>
                      )) : (
                        <p className="text-center py-8 text-gray-500">Nenhum evento no calendário</p>
                      )}
                    </div>
                  )}

                  {tipoRelatorio === 'historico' && (
                    <div className="relative">
                      <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
                      <div className="space-y-6">
                        <div className="relative flex gap-4 pl-10">
                          <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                          <div className="flex-1">
                            <div className="flex items-center justify-between">
                              <span className="font-medium">Eleição criada</span>
                              <span className="text-xs text-gray-500">
                                {selectedEleicaoData?.ano}
                              </span>
                            </div>
                            <p className="text-sm text-gray-600">Por: Sistema</p>
                          </div>
                        </div>
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
