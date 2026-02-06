import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Link, useNavigate } from 'react-router-dom'
import {
  Search,
  Eye,
  Play,
  Square,
  BarChart3,
  Calculator,
  Loader2,
  Users,
  Vote,
  CheckCircle,
  Clock,
  AlertCircle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
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
import { useToast } from '@/hooks/use-toast'
import { votacaoService, type EleicaoVotacao } from '@/services/votacao'

const statusVotacaoLabels: Record<string, { label: string; color: string; icon: React.ElementType }> = {
  preparada: { label: 'Preparada', color: 'bg-blue-100 text-blue-800', icon: Clock },
  em_andamento: { label: 'Em Andamento', color: 'bg-green-100 text-green-800', icon: Play },
  encerrada: { label: 'Encerrada', color: 'bg-yellow-100 text-yellow-800', icon: Square },
  apurada: { label: 'Apurada', color: 'bg-purple-100 text-purple-800', icon: CheckCircle },
}

export function VotacaoPage() {
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState<string>('all')
  const [confirmDialog, setConfirmDialog] = useState<{
    open: boolean
    type: 'iniciar' | 'encerrar' | null
    eleicao: EleicaoVotacao | null
  }>({ open: false, type: null, eleicao: null })

  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  const { data: eleicoes, isLoading } = useQuery({
    queryKey: ['votacao-eleicoes'],
    queryFn: votacaoService.getAll,
  })

  const iniciarMutation = useMutation({
    mutationFn: (eleicaoId: string) => votacaoService.iniciarVotacao(eleicaoId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['votacao-eleicoes'] })
      toast({
        title: 'Votacao iniciada',
        description: 'A votacao foi iniciada com sucesso.',
      })
      setConfirmDialog({ open: false, type: null, eleicao: null })
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao iniciar votacao',
        description: error.response?.data?.message || 'Nao foi possivel iniciar a votacao.',
      })
    },
  })

  const encerrarMutation = useMutation({
    mutationFn: (eleicaoId: string) => votacaoService.encerrarVotacao(eleicaoId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['votacao-eleicoes'] })
      toast({
        title: 'Votacao encerrada',
        description: 'A votacao foi encerrada com sucesso.',
      })
      setConfirmDialog({ open: false, type: null, eleicao: null })
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao encerrar votacao',
        description: error.response?.data?.message || 'Nao foi possivel encerrar a votacao.',
      })
    },
  })

  const filteredEleicoes = eleicoes?.filter((eleicao) => {
    const matchesSearch = eleicao.nome.toLowerCase().includes(search.toLowerCase())
    const matchesStatus = statusFilter === 'all' || eleicao.statusVotacao === statusFilter
    return matchesSearch && matchesStatus
  })

  const handleIniciarClick = (eleicao: EleicaoVotacao) => {
    setConfirmDialog({ open: true, type: 'iniciar', eleicao })
  }

  const handleEncerrarClick = (eleicao: EleicaoVotacao) => {
    setConfirmDialog({ open: true, type: 'encerrar', eleicao })
  }

  const handleConfirm = () => {
    if (confirmDialog.type === 'iniciar' && confirmDialog.eleicao) {
      iniciarMutation.mutate(confirmDialog.eleicao.id)
    } else if (confirmDialog.type === 'encerrar' && confirmDialog.eleicao) {
      encerrarMutation.mutate(confirmDialog.eleicao.id)
    }
  }

  const handleCancelDialog = () => {
    setConfirmDialog({ open: false, type: null, eleicao: null })
  }

  const getStatusBadge = (statusVotacao: string) => {
    const config = statusVotacaoLabels[statusVotacao] || statusVotacaoLabels.preparada
    const Icon = config.icon
    return (
      <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
        <Icon className="h-3 w-3" />
        {config.label}
      </span>
    )
  }

  // Calculate totals
  const totals = eleicoes?.reduce(
    (acc, el) => ({
      eleitores: acc.eleitores + el.totalEleitores,
      votos: acc.votos + el.totalVotos,
      emAndamento: acc.emAndamento + (el.statusVotacao === 'em_andamento' ? 1 : 0),
    }),
    { eleitores: 0, votos: 0, emAndamento: 0 }
  ) || { eleitores: 0, votos: 0, emAndamento: 0 }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Votacao</h1>
          <p className="text-gray-600">Gerencie as votacoes das eleicoes</p>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleitores</CardTitle>
            <Users className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totals.eleitores.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Em todas as eleicoes</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Votos</CardTitle>
            <Vote className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totals.votos.toLocaleString()}</div>
            <p className="text-xs text-gray-500">Votos computados</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Em Andamento</CardTitle>
            <Play className="h-4 w-4 text-orange-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totals.emAndamento}</div>
            <p className="text-xs text-gray-500">Votacoes ativas agora</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleicoes</CardTitle>
            <BarChart3 className="h-4 w-4 text-purple-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{eleicoes?.length || 0}</div>
            <p className="text-xs text-gray-500">Eleicoes cadastradas</p>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar eleicoes..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-[200px]">
                <SelectValue placeholder="Status da Votacao" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos os Status</SelectItem>
                <SelectItem value="preparada">Preparada</SelectItem>
                <SelectItem value="em_andamento">Em Andamento</SelectItem>
                <SelectItem value="encerrada">Encerrada</SelectItem>
                <SelectItem value="apurada">Apurada</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : filteredEleicoes && filteredEleicoes.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-3 px-4 font-medium">Eleicao</th>
                    <th className="text-left py-3 px-4 font-medium">Status</th>
                    <th className="text-right py-3 px-4 font-medium">Eleitores</th>
                    <th className="text-right py-3 px-4 font-medium">Votos</th>
                    <th className="text-right py-3 px-4 font-medium">Participacao</th>
                    <th className="text-right py-3 px-4 font-medium">Brancos/Nulos</th>
                    <th className="text-right py-3 px-4 font-medium">Acoes</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredEleicoes.map((eleicao) => (
                    <tr key={eleicao.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4">
                        <div>
                          <p className="font-medium">{eleicao.nome}</p>
                          <p className="text-sm text-gray-500">Ano: {eleicao.ano}</p>
                        </div>
                      </td>
                      <td className="py-3 px-4">
                        {getStatusBadge(eleicao.statusVotacao)}
                      </td>
                      <td className="py-3 px-4 text-right">
                        {eleicao.totalEleitores.toLocaleString()}
                      </td>
                      <td className="py-3 px-4 text-right">
                        {eleicao.totalVotos.toLocaleString()}
                      </td>
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <div className="w-20 h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div
                              className="h-full bg-green-500 rounded-full"
                              style={{ width: `${Math.min(eleicao.participacao, 100)}%` }}
                            />
                          </div>
                          <span className="text-sm font-medium">{eleicao.participacao.toFixed(1)}%</span>
                        </div>
                      </td>
                      <td className="py-3 px-4 text-right text-sm text-gray-500">
                        {eleicao.votosBrancos.toLocaleString()} / {eleicao.votosNulos.toLocaleString()}
                      </td>
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <Link to={`/votacao/${eleicao.id}`}>
                            <Button variant="ghost" size="icon" title="Monitorar">
                              <Eye className="h-4 w-4" />
                            </Button>
                          </Link>

                          {eleicao.statusVotacao === 'preparada' && (
                            <Button
                              variant="ghost"
                              size="icon"
                              title="Iniciar Votacao"
                              onClick={() => handleIniciarClick(eleicao)}
                              disabled={iniciarMutation.isPending}
                            >
                              <Play className="h-4 w-4 text-green-600" />
                            </Button>
                          )}

                          {eleicao.statusVotacao === 'em_andamento' && (
                            <Button
                              variant="ghost"
                              size="icon"
                              title="Encerrar Votacao"
                              onClick={() => handleEncerrarClick(eleicao)}
                              disabled={encerrarMutation.isPending}
                            >
                              <Square className="h-4 w-4 text-red-600" />
                            </Button>
                          )}

                          {(eleicao.statusVotacao === 'encerrada' || eleicao.statusVotacao === 'apurada') && (
                            <Link to={`/votacao/${eleicao.id}/apuracao`}>
                              <Button variant="ghost" size="icon" title="Ver Apuracao">
                                <Calculator className="h-4 w-4 text-purple-600" />
                              </Button>
                            </Link>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="flex flex-col items-center justify-center py-12 text-gray-500">
              <AlertCircle className="h-12 w-12 mb-4" />
              <p>Nenhuma eleicao encontrada.</p>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Confirmation Dialog */}
      <AlertDialog open={confirmDialog.open} onOpenChange={(open) => !open && handleCancelDialog()}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>
              {confirmDialog.type === 'iniciar' ? 'Iniciar Votacao' : 'Encerrar Votacao'}
            </AlertDialogTitle>
            <AlertDialogDescription>
              {confirmDialog.type === 'iniciar' ? (
                <>
                  Tem certeza que deseja iniciar a votacao para "{confirmDialog.eleicao?.nome}"?
                  <br /><br />
                  Esta acao permitira que os eleitores comecem a votar.
                </>
              ) : (
                <>
                  Tem certeza que deseja encerrar a votacao para "{confirmDialog.eleicao?.nome}"?
                  <br /><br />
                  Esta acao impedira novos votos e nao pode ser desfeita.
                </>
              )}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={handleCancelDialog}>
              Cancelar
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={handleConfirm}
              disabled={iniciarMutation.isPending || encerrarMutation.isPending}
              className={confirmDialog.type === 'encerrar' ? 'bg-destructive hover:bg-destructive/90' : ''}
            >
              {(iniciarMutation.isPending || encerrarMutation.isPending) && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              {confirmDialog.type === 'iniciar' ? 'Iniciar' : 'Encerrar'}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
