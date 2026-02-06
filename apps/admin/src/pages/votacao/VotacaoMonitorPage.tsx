import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  RefreshCw,
  Users,
  Vote,
  TrendingUp,
  Clock,
  Loader2,
  MapPin,
  Activity,
  CheckCircle,
  Play,
  AlertCircle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import { votacaoService, EstatisticasVotacao, VotosPorRegiao, VotosPorHora } from '@/services/votacao'
import { eleicoesService } from '@/services/eleicoes'

// Mock data for development
const mockEstatisticas: EstatisticasVotacao = {
  eleicaoId: '1',
  eleicaoNome: 'Eleicao CAU/BR 2024',
  totalEleitores: 150000,
  totalVotos: 45678,
  participacao: 30.45,
  votosBrancos: 1234,
  votosNulos: 567,
  votosValidos: 43877,
  status: 'em_andamento',
  dataInicio: '2024-10-15T08:00:00',
  dataFim: '2024-10-15T18:00:00',
  votosPorRegiao: [
    { uf: 'SP', regional: 'CAU/SP', totalEleitores: 35000, totalVotos: 12500, participacao: 35.7 },
    { uf: 'RJ', regional: 'CAU/RJ', totalEleitores: 22000, totalVotos: 7500, participacao: 34.1 },
    { uf: 'MG', regional: 'CAU/MG', totalEleitores: 28000, totalVotos: 8200, participacao: 29.3 },
    { uf: 'RS', regional: 'CAU/RS', totalEleitores: 15000, totalVotos: 5100, participacao: 34.0 },
    { uf: 'PR', regional: 'CAU/PR', totalEleitores: 12000, totalVotos: 3800, participacao: 31.7 },
    { uf: 'BA', regional: 'CAU/BA', totalEleitores: 10000, totalVotos: 2900, participacao: 29.0 },
    { uf: 'SC', regional: 'CAU/SC', totalEleitores: 8000, totalVotos: 2400, participacao: 30.0 },
    { uf: 'GO', regional: 'CAU/GO', totalEleitores: 7000, totalVotos: 1978, participacao: 28.3 },
    { uf: 'DF', regional: 'CAU/DF', totalEleitores: 6000, totalVotos: 1300, participacao: 21.7 },
    { uf: 'CE', regional: 'CAU/CE', totalEleitores: 7000, totalVotos: 0, participacao: 0 },
  ],
  votosPorHora: [
    { hora: '08:00', quantidade: 3500 },
    { hora: '09:00', quantidade: 5200 },
    { hora: '10:00', quantidade: 6800 },
    { hora: '11:00', quantidade: 7200 },
    { hora: '12:00', quantidade: 4100 },
    { hora: '13:00', quantidade: 5500 },
    { hora: '14:00', quantidade: 6900 },
    { hora: '15:00', quantidade: 6478 },
    { hora: '16:00', quantidade: 0 },
    { hora: '17:00', quantidade: 0 },
    { hora: '18:00', quantidade: 0 },
  ],
}

export function VotacaoMonitorPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const { toast } = useToast()
  const [isRefreshing, setIsRefreshing] = useState(false)
  const [autoRefresh, setAutoRefresh] = useState(true)
  const [lastUpdate, setLastUpdate] = useState(new Date())

  const { data: eleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['eleicao', eleicaoId],
    queryFn: () => eleicoesService.getById(eleicaoId!),
    enabled: !!eleicaoId,
  })

  const {
    data: estatisticas,
    isLoading: isLoadingEstatisticas,
    refetch,
  } = useQuery({
    queryKey: ['votacao-estatisticas', eleicaoId],
    queryFn: () => votacaoService.getEstatisticas(eleicaoId!),
    enabled: !!eleicaoId,
    refetchInterval: autoRefresh ? 30000 : false, // Refresh every 30 seconds if auto-refresh enabled
    placeholderData: mockEstatisticas,
  })

  // Update last update time when data changes
  useEffect(() => {
    if (estatisticas) {
      setLastUpdate(new Date())
    }
  }, [estatisticas])

  const handleRefresh = async () => {
    setIsRefreshing(true)
    await refetch()
    setIsRefreshing(false)
    setLastUpdate(new Date())
    toast({
      title: 'Dados atualizados',
      description: 'As estatisticas foram atualizadas.',
    })
  }

  const toggleAutoRefresh = () => {
    setAutoRefresh(!autoRefresh)
    toast({
      title: autoRefresh ? 'Atualizacao automatica desativada' : 'Atualizacao automatica ativada',
      description: autoRefresh
        ? 'Os dados nao serao mais atualizados automaticamente.'
        : 'Os dados serao atualizados a cada 30 segundos.',
    })
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'em_andamento':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <Play className="h-3 w-3" />
            Em Andamento
          </span>
        )
      case 'encerrada':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <CheckCircle className="h-3 w-3" />
            Encerrada
          </span>
        )
      default:
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <Clock className="h-3 w-3" />
            Preparada
          </span>
        )
    }
  }

  const getMaxVotosPorHora = () => {
    if (!estatisticas?.votosPorHora) return 1
    return Math.max(...estatisticas.votosPorHora.map((v) => v.quantidade), 1)
  }

  if (isLoadingEleicao || isLoadingEstatisticas) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!estatisticas) {
    return (
      <div className="flex flex-col items-center justify-center h-64 text-gray-500">
        <AlertCircle className="h-12 w-12 mb-4" />
        <p>Nao foi possivel carregar as estatisticas.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/votacao">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">Monitoramento</h1>
              {getStatusBadge(estatisticas.status)}
              {autoRefresh && (
                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                  <Activity className="h-3 w-3 animate-pulse text-green-500" />
                  Ao vivo
                </span>
              )}
            </div>
            <p className="text-gray-600">{eleicao?.nome || estatisticas.eleicaoNome}</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Button
            variant={autoRefresh ? 'default' : 'outline'}
            onClick={toggleAutoRefresh}
          >
            <Activity className="mr-2 h-4 w-4" />
            {autoRefresh ? 'Ao Vivo' : 'Pausado'}
          </Button>
          <Button variant="outline" onClick={handleRefresh} disabled={isRefreshing}>
            {isRefreshing ? (
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            ) : (
              <RefreshCw className="mr-2 h-4 w-4" />
            )}
            Atualizar
          </Button>
        </div>
      </div>

      {/* Main Statistics Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleitores</CardTitle>
            <Users className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{estatisticas.totalEleitores.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Eleitores aptos a votar</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Votos Computados</CardTitle>
            <Vote className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-green-600">{estatisticas.totalVotos.toLocaleString()}</div>
            <p className="text-xs text-gray-500">
              Validos: {estatisticas.votosValidos.toLocaleString()}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Participacao</CardTitle>
            <TrendingUp className="h-4 w-4 text-purple-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold text-purple-600">{estatisticas.participacao.toFixed(1)}%</div>
            <div className="mt-2 h-2 w-full rounded-full bg-gray-100 overflow-hidden">
              <div
                className="h-full bg-purple-500 rounded-full transition-all duration-500"
                style={{ width: `${Math.min(estatisticas.participacao, 100)}%` }}
              />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Ultima Atualizacao</CardTitle>
            <Clock className="h-4 w-4 text-orange-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{lastUpdate.toLocaleTimeString('pt-BR')}</div>
            <p className="text-xs text-gray-500">{lastUpdate.toLocaleDateString('pt-BR')}</p>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Timeline Graph */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Activity className="h-5 w-5" />
              Atividade de Votacao por Hora
            </CardTitle>
            <CardDescription>Quantidade de votos registrados ao longo do dia</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {estatisticas.votosPorHora.map((item) => (
                <div key={item.hora} className="flex items-center gap-3">
                  <span className="w-12 text-sm text-gray-500 font-mono">{item.hora}</span>
                  <div className="flex-1 h-6 rounded bg-gray-100 overflow-hidden">
                    <div
                      className={`h-full rounded transition-all duration-500 ${
                        item.quantidade > 0 ? 'bg-blue-500' : 'bg-gray-200'
                      }`}
                      style={{
                        width: `${(item.quantidade / getMaxVotosPorHora()) * 100}%`,
                      }}
                    />
                  </div>
                  <span className="w-16 text-sm text-right font-medium">
                    {item.quantidade.toLocaleString()}
                  </span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Vote Details */}
        <Card>
          <CardHeader>
            <CardTitle>Detalhes dos Votos</CardTitle>
            <CardDescription>Distribuicao dos votos</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos Validos</span>
                <span className="font-medium text-green-600">
                  {estatisticas.votosValidos.toLocaleString()}
                </span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos em Branco</span>
                <span className="font-medium">{estatisticas.votosBrancos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos Nulos</span>
                <span className="font-medium">{estatisticas.votosNulos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Total de Votos</span>
                <span className="font-bold">{estatisticas.totalVotos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2">
                <span className="text-sm text-gray-600">Abstencoes</span>
                <span className="font-medium text-gray-500">
                  {(estatisticas.totalEleitores - estatisticas.totalVotos).toLocaleString()}
                </span>
              </div>
            </div>

            <div className="mt-6 p-4 bg-yellow-50 rounded-lg">
              <p className="text-xs text-yellow-800">
                <strong>Nota:</strong> Os resultados parciais por chapa nao sao exibidos para preservar o sigilo do voto durante a votacao.
              </p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Votes by Region */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <MapPin className="h-5 w-5" />
            Votacao por Regiao (UF)
          </CardTitle>
          <CardDescription>Participacao por regional do CAU</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="text-left py-3 px-4 font-medium">UF</th>
                  <th className="text-left py-3 px-4 font-medium">Regional</th>
                  <th className="text-right py-3 px-4 font-medium">Eleitores</th>
                  <th className="text-right py-3 px-4 font-medium">Votos</th>
                  <th className="text-right py-3 px-4 font-medium">Participacao</th>
                </tr>
              </thead>
              <tbody>
                {estatisticas.votosPorRegiao
                  .sort((a, b) => b.participacao - a.participacao)
                  .map((regiao) => (
                    <tr key={regiao.uf} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4">
                        <span className="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-medium text-sm">
                          {regiao.uf}
                        </span>
                      </td>
                      <td className="py-3 px-4">{regiao.regional}</td>
                      <td className="py-3 px-4 text-right">
                        {regiao.totalEleitores.toLocaleString()}
                      </td>
                      <td className="py-3 px-4 text-right font-medium">
                        {regiao.totalVotos.toLocaleString()}
                      </td>
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <div className="w-24 h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div
                              className={`h-full rounded-full transition-all duration-500 ${
                                regiao.participacao >= 50
                                  ? 'bg-green-500'
                                  : regiao.participacao >= 25
                                  ? 'bg-yellow-500'
                                  : 'bg-red-500'
                              }`}
                              style={{ width: `${Math.min(regiao.participacao, 100)}%` }}
                            />
                          </div>
                          <span className="text-sm font-medium w-14 text-right">
                            {regiao.participacao.toFixed(1)}%
                          </span>
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
  )
}
