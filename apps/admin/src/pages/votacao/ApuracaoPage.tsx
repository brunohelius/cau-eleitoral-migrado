import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  RefreshCw,
  Users,
  Vote,
  TrendingUp,
  BarChart3,
  Loader2,
  Trophy,
  Download,
  CheckCircle,
  Clock,
  Globe,
  Calculator,
  FileText,
  AlertCircle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useToast } from '@/hooks/use-toast'
import { votacaoService } from '@/services/votacao'
import { eleicoesService } from '@/services/eleicoes'

export function ApuracaoPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [isRefreshing, setIsRefreshing] = useState(false)
  const [exportFormat, setExportFormat] = useState<'pdf' | 'excel' | 'csv'>('pdf')
  const [confirmDialog, setConfirmDialog] = useState<{
    open: boolean
    type: 'apurar' | 'publicar' | null
  }>({ open: false, type: null })

  const { data: eleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['eleicao', eleicaoId],
    queryFn: () => eleicoesService.getById(eleicaoId!),
    enabled: !!eleicaoId,
  })

  const {
    data: resultado,
    isLoading: isLoadingResultado,
    refetch,
  } = useQuery({
    queryKey: ['apuracao', eleicaoId],
    queryFn: () => votacaoService.getResultados(eleicaoId!),
    enabled: !!eleicaoId,
  })

  const apurarMutation = useMutation({
    mutationFn: (id: string) => votacaoService.apurar(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['apuracao', eleicaoId] })
      toast({
        title: 'Apuração realizada',
        description: 'Os votos foram apurados com sucesso.',
      })
      setConfirmDialog({ open: false, type: null })
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro na apuração',
        description: error.response?.data?.message || 'Não foi possível realizar a apuração.',
      })
    },
  })

  const publicarMutation = useMutation({
    mutationFn: (id: string) => votacaoService.publicarResultados(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['apuracao', eleicaoId] })
      toast({
        title: 'Resultados publicados',
        description: 'Os resultados foram publicados com sucesso.',
      })
      setConfirmDialog({ open: false, type: null })
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao publicar',
        description: error.response?.data?.message || 'Não foi possível publicar os resultados.',
      })
    },
  })

  const handleRefresh = async () => {
    setIsRefreshing(true)
    await refetch()
    setIsRefreshing(false)
    toast({
      title: 'Dados atualizados',
      description: 'Os resultados foram atualizados.',
    })
  }

  const handleExport = async () => {
    try {
      toast({
        title: 'Exportando resultados',
        description: `O arquivo ${exportFormat.toUpperCase()} sera baixado em instantes.`,
      })

      const blob = await votacaoService.exportarResultados(eleicaoId!, exportFormat)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `resultado-eleicao-${eleicaoId}.${exportFormat === 'excel' ? 'xlsx' : exportFormat}`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (error: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao exportar',
        description: error.response?.data?.message || 'Não foi possível exportar os resultados.',
      })
    }
  }

  const handleConfirm = () => {
    if (confirmDialog.type === 'apurar') {
      apurarMutation.mutate(eleicaoId!)
    } else if (confirmDialog.type === 'publicar') {
      publicarMutation.mutate(eleicaoId!)
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'publicada':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <Globe className="h-3 w-3" />
            Publicada
          </span>
        )
      case 'finalizada':
        return (
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
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

  const getBarColor = (index: number, eleita: boolean) => {
    if (eleita) return 'bg-green-500'
    const colors = ['bg-blue-500', 'bg-indigo-500', 'bg-violet-500', 'bg-purple-500']
    return colors[index % colors.length]
  }

  if (isLoadingEleicao || isLoadingResultado) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!resultado) {
    return (
      <div className="flex flex-col items-center justify-center h-64 text-gray-500">
        <AlertCircle className="h-12 w-12 mb-4" />
        <p>Não foi possível carregar os resultados.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to={`/votacao/${eleicaoId}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">Apuração</h1>
              {getStatusBadge(resultado.status)}
            </div>
            <p className="text-gray-600">{eleicao?.nome || resultado.eleicaoNome}</p>
          </div>
        </div>
        <div className="flex gap-2">
          {resultado.status === 'aguardando' && (
            <Button onClick={() => setConfirmDialog({ open: true, type: 'apurar' })}>
              <Calculator className="mr-2 h-4 w-4" />
              Apurar Votos
            </Button>
          )}

          {resultado.status === 'finalizada' && (
            <Button onClick={() => setConfirmDialog({ open: true, type: 'publicar' })}>
              <Globe className="mr-2 h-4 w-4" />
              Publicar Resultados
            </Button>
          )}

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

      {/* Statistics Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleitores</CardTitle>
            <Users className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{resultado.totalEleitores.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Eleitores aptos</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Votos</CardTitle>
            <Vote className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{resultado.totalVotos.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Votos computados</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Participação</CardTitle>
            <TrendingUp className="h-4 w-4 text-purple-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{resultado.participacao.toFixed(1)}%</div>
            <p className="text-xs text-gray-500">Taxa de comparecimento</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Apurado</CardTitle>
            <BarChart3 className="h-4 w-4 text-orange-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{resultado.percentualApurado}%</div>
            <p className="text-xs text-gray-500">
              {resultado.percentualApurado === 100 ? 'Apuracao completa' : 'Em processamento'}
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Winner Card (if applicable) */}
      {resultado.vencedora && resultado.status !== 'aguardando' && resultado.status !== 'em_andamento' && (
        <Card className="border-green-200 bg-green-50">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-green-800">
              <Trophy className="h-6 w-6 text-yellow-500" />
              Chapa Vencedora
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-2xl font-bold text-green-800">
                  {resultado.vencedora.numero}. {resultado.vencedora.nome}
                </p>
                <p className="text-green-600">
                  {resultado.vencedora.votos.toLocaleString()} votos ({resultado.vencedora.percentual.toFixed(2)}%)
                </p>
              </div>
              <div className="text-right">
                <Trophy className="h-16 w-16 text-yellow-500" />
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Results by Chapa */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <BarChart3 className="h-5 w-5" />
              Resultado por Chapa
            </CardTitle>
            <CardDescription>
              Votos validos: {resultado.votosValidos.toLocaleString()}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-6">
              {resultado.chapas
                .sort((a, b) => a.posicao - b.posicao)
                .map((chapa, index) => (
                  <div key={chapa.id} className="space-y-2">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        {chapa.eleita && (
                          <Trophy className="h-5 w-5 text-yellow-500" />
                        )}
                        <span className={`text-sm font-medium ${chapa.eleita ? 'text-green-700' : ''}`}>
                          {chapa.numero}. {chapa.nome}
                        </span>
                        {chapa.eleita && (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Eleita
                          </span>
                        )}
                      </div>
                      <div className="text-right">
                        <span className="font-bold">{chapa.votos.toLocaleString()}</span>
                        <span className="text-sm text-gray-500 ml-2">({chapa.percentual.toFixed(2)}%)</span>
                      </div>
                    </div>
                    <div className="h-3 w-full rounded-full bg-gray-100 overflow-hidden">
                      <div
                        className={`h-full rounded-full transition-all duration-500 ${getBarColor(index, chapa.eleita)}`}
                        style={{ width: `${chapa.percentual}%` }}
                      />
                    </div>
                  </div>
                ))}
            </div>
          </CardContent>
        </Card>

        {/* Vote Details */}
        <div className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>Detalhes da Votação</CardTitle>
              <CardDescription>Distribuicao dos votos</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="flex items-center justify-between py-2 border-b">
                  <span className="text-sm text-gray-600">Votos Validos</span>
                  <span className="font-medium text-green-600">
                    {resultado.votosValidos.toLocaleString()}
                  </span>
                </div>
                <div className="flex items-center justify-between py-2 border-b">
                  <span className="text-sm text-gray-600">Votos em Branco</span>
                  <span className="font-medium">{resultado.votosBrancos.toLocaleString()}</span>
                </div>
                <div className="flex items-center justify-between py-2 border-b">
                  <span className="text-sm text-gray-600">Votos Nulos</span>
                  <span className="font-medium">{resultado.votosNulos.toLocaleString()}</span>
                </div>
                <div className="flex items-center justify-between py-2 border-b">
                  <span className="text-sm text-gray-600">Total de Votos</span>
                  <span className="font-bold">{resultado.totalVotos.toLocaleString()}</span>
                </div>
                <div className="flex items-center justify-between py-2">
                  <span className="text-sm text-gray-600">Abstencoes</span>
                  <span className="font-medium text-gray-500">
                    {(resultado.totalEleitores - resultado.totalVotos).toLocaleString()}
                  </span>
                </div>
              </div>

              {resultado.dataApuracao && (
                <div className="mt-6 pt-4 border-t">
                  <p className="text-xs text-gray-500">
                    Data da apuracao:{' '}
                    {new Date(resultado.dataApuracao).toLocaleString('pt-BR')}
                  </p>
                </div>
              )}

              {resultado.dataPublicacao && (
                <div className="mt-2">
                  <p className="text-xs text-gray-500">
                    Data da publicacao:{' '}
                    {new Date(resultado.dataPublicacao).toLocaleString('pt-BR')}
                  </p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Export Card */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5" />
                Exportar Resultados
              </CardTitle>
              <CardDescription>Baixe o relatório em diferentes formatos</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <Select
                  value={exportFormat}
                  onValueChange={(value: 'pdf' | 'excel' | 'csv') => setExportFormat(value)}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Selecione o formato" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="pdf">PDF</SelectItem>
                    <SelectItem value="excel">Excel (XLSX)</SelectItem>
                    <SelectItem value="csv">CSV</SelectItem>
                  </SelectContent>
                </Select>
                <Button className="w-full" onClick={handleExport}>
                  <Download className="mr-2 h-4 w-4" />
                  Exportar {exportFormat.toUpperCase()}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Confirmation Dialog */}
      <AlertDialog open={confirmDialog.open} onOpenChange={(open) => !open && setConfirmDialog({ open: false, type: null })}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>
              {confirmDialog.type === 'apurar' ? 'Apurar Votos' : 'Publicar Resultados'}
            </AlertDialogTitle>
            <AlertDialogDescription>
              {confirmDialog.type === 'apurar' ? (
                <>
                  Tem certeza que deseja iniciar a apuração dos votos?
                  <br /><br />
                  Este processo contabilizara todos os votos registrados e calculara os resultados da eleicao.
                </>
              ) : (
                <>
                  Tem certeza que deseja publicar os resultados?
                  <br /><br />
                  Esta ação tornara os resultados visiveis publicamente e não pode ser desfeita.
                </>
              )}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={() => setConfirmDialog({ open: false, type: null })}>
              Cancelar
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={handleConfirm}
              disabled={apurarMutation.isPending || publicarMutation.isPending}
            >
              {(apurarMutation.isPending || publicarMutation.isPending) && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              {confirmDialog.type === 'apurar' ? 'Apurar' : 'Publicar'}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
