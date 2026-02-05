import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  BarChart3,
  Users,
  Vote,
  CheckCircle,
  Clock,
  RefreshCw,
  Download,
  Loader2,
  Trophy,
  TrendingUp,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import { eleicoesService } from '@/services/eleicoes'

interface ResultadoChapa {
  id: string
  numero: number
  nome: string
  votos: number
  percentual: number
  posicao: number
}

interface DadosApuracao {
  totalEleitores: number
  totalVotos: number
  votosValidos: number
  votosBrancos: number
  votosNulos: number
  participacao: number
  chapas: ResultadoChapa[]
  ultimaAtualizacao: string
  status: 'em_andamento' | 'finalizada' | 'aguardando'
  percentualApurado: number
}

export function EleicaoApuracaoPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  const [isRefreshing, setIsRefreshing] = useState(false)

  const { data: eleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['eleicao', id],
    queryFn: () => eleicoesService.getById(id!),
    enabled: !!id,
  })

  // Mock dados de apuracao - em producao viria da API
  const [dadosApuracao] = useState<DadosApuracao>({
    totalEleitores: 15000,
    totalVotos: 12450,
    votosValidos: 11800,
    votosBrancos: 350,
    votosNulos: 300,
    participacao: 83,
    percentualApurado: 100,
    status: 'finalizada',
    ultimaAtualizacao: new Date().toISOString(),
    chapas: [
      { id: '1', numero: 1, nome: 'Chapa Renovacao', votos: 5200, percentual: 44.07, posicao: 1 },
      { id: '2', numero: 2, nome: 'Chapa Unidade', votos: 3800, percentual: 32.20, posicao: 2 },
      { id: '3', numero: 3, nome: 'Chapa Mudanca', votos: 2100, percentual: 17.80, posicao: 3 },
      { id: '4', numero: 4, nome: 'Chapa Progresso', votos: 700, percentual: 5.93, posicao: 4 },
    ],
  })

  const handleRefresh = async () => {
    setIsRefreshing(true)
    // Simular atualizacao
    await new Promise((resolve) => setTimeout(resolve, 1500))
    setIsRefreshing(false)
    toast({
      title: 'Dados atualizados',
      description: 'Os dados da apuracao foram atualizados.',
    })
  }

  const handleExportarResultados = () => {
    toast({
      title: 'Exportando resultados',
      description: 'O arquivo sera baixado em instantes.',
    })
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'finalizada':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <CheckCircle className="h-3 w-3" />
            Finalizada
          </span>
        )
      case 'em_andamento':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <Clock className="h-3 w-3" />
            Em Andamento
          </span>
        )
      default:
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            <Clock className="h-3 w-3" />
            Aguardando
          </span>
        )
    }
  }

  if (isLoadingEleicao) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to={`/eleicoes/${id}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">Apuracao</h1>
              {getStatusBadge(dadosApuracao.status)}
            </div>
            <p className="text-gray-600">{eleicao?.nome}</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handleRefresh} disabled={isRefreshing}>
            {isRefreshing ? (
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            ) : (
              <RefreshCw className="mr-2 h-4 w-4" />
            )}
            Atualizar
          </Button>
          <Button onClick={handleExportarResultados}>
            <Download className="mr-2 h-4 w-4" />
            Exportar
          </Button>
        </div>
      </div>

      {/* Cards de Estatisticas */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleitores</CardTitle>
            <Users className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{dadosApuracao.totalEleitores.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Eleitores aptos a votar</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Votos</CardTitle>
            <Vote className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{dadosApuracao.totalVotos.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Votos computados</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Participacao</CardTitle>
            <TrendingUp className="h-4 w-4 text-purple-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{dadosApuracao.participacao}%</div>
            <p className="text-xs text-gray-500">Taxa de comparecimento</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Apurado</CardTitle>
            <BarChart3 className="h-4 w-4 text-orange-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{dadosApuracao.percentualApurado}%</div>
            <p className="text-xs text-gray-500">Urnas apuradas</p>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Resultado por Chapa */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Trophy className="h-5 w-5 text-yellow-500" />
              Resultado por Chapa
            </CardTitle>
            <CardDescription>
              Votos validos: {dadosApuracao.votosValidos.toLocaleString()}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {dadosApuracao.chapas
                .sort((a, b) => a.posicao - b.posicao)
                .map((chapa, index) => (
                  <div key={chapa.id} className="space-y-2">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        {index === 0 && dadosApuracao.status === 'finalizada' && (
                          <Trophy className="h-5 w-5 text-yellow-500" />
                        )}
                        <span className="text-sm font-medium">
                          {chapa.numero}. {chapa.nome}
                        </span>
                      </div>
                      <div className="text-right">
                        <span className="font-bold">{chapa.votos.toLocaleString()}</span>
                        <span className="text-sm text-gray-500 ml-2">({chapa.percentual.toFixed(2)}%)</span>
                      </div>
                    </div>
                    <div className="h-2 w-full rounded-full bg-gray-100 overflow-hidden">
                      <div
                        className={`h-full rounded-full transition-all duration-500 ${
                          index === 0 ? 'bg-green-500' : 'bg-blue-500'
                        }`}
                        style={{ width: `${chapa.percentual}%` }}
                      />
                    </div>
                  </div>
                ))}
            </div>
          </CardContent>
        </Card>

        {/* Detalhes da Votacao */}
        <Card>
          <CardHeader>
            <CardTitle>Detalhes da Votacao</CardTitle>
            <CardDescription>Distribuicao dos votos</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos Validos</span>
                <span className="font-medium">{dadosApuracao.votosValidos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos em Branco</span>
                <span className="font-medium">{dadosApuracao.votosBrancos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Votos Nulos</span>
                <span className="font-medium">{dadosApuracao.votosNulos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2 border-b">
                <span className="text-sm text-gray-600">Total de Votos</span>
                <span className="font-bold">{dadosApuracao.totalVotos.toLocaleString()}</span>
              </div>
              <div className="flex items-center justify-between py-2">
                <span className="text-sm text-gray-600">Abstencoes</span>
                <span className="font-medium">
                  {(dadosApuracao.totalEleitores - dadosApuracao.totalVotos).toLocaleString()}
                </span>
              </div>
            </div>

            <div className="mt-6 pt-4 border-t">
              <p className="text-xs text-gray-500">
                Ultima atualizacao:{' '}
                {new Date(dadosApuracao.ultimaAtualizacao).toLocaleString('pt-BR')}
              </p>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
