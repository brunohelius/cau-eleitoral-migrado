import { useState, useMemo } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import {
  Plus,
  Search,
  Eye,
  Edit,
  Trash2,
  Users,
  FileText,
  ChevronLeft,
  ChevronRight,
  Filter,
  Check,
  X,
  AlertTriangle,
  Loader2,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
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
import {
  chapasService,
  StatusChapa,
  type Chapa,
  type ChapaListParams,
} from '@/services/chapas'
import { eleicoesService } from '@/services/eleicoes'

// Status labels with colors
const statusConfig: Record<StatusChapa, { label: string; color: string }> = {
  [StatusChapa.PENDENTE]: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800' },
  [StatusChapa.EM_ANALISE]: { label: 'Em Análise', color: 'bg-blue-100 text-blue-800' },
  [StatusChapa.APROVADA]: { label: 'Aprovada', color: 'bg-green-100 text-green-800' },
  [StatusChapa.REPROVADA]: { label: 'Reprovada', color: 'bg-red-100 text-red-800' },
  [StatusChapa.IMPUGNADA]: { label: 'Impugnada', color: 'bg-orange-100 text-orange-800' },
  [StatusChapa.SUSPENSA]: { label: 'Suspensa', color: 'bg-gray-100 text-gray-800' },
  [StatusChapa.CANCELADA]: { label: 'Cancelada', color: 'bg-red-100 text-red-800' },
}

type DialogAction = 'delete' | 'approve' | 'reject' | null

export function ChapasPage() {
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Filter states
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState<StatusChapa | ''>('')
  const [eleicaoFilter, setEleicaoFilter] = useState<string>('')
  const [page, setPage] = useState(1)
  const pageSize = 10

  // Dialog states
  const [dialogOpen, setDialogOpen] = useState(false)
  const [dialogAction, setDialogAction] = useState<DialogAction>(null)
  const [selectedChapa, setSelectedChapa] = useState<Chapa | null>(null)
  const [rejectReason, setRejectReason] = useState('')

  // Build query params
  const queryParams: ChapaListParams = useMemo(
    () => ({
      search: search || undefined,
      status: statusFilter !== '' ? statusFilter : undefined,
      eleicaoId: eleicaoFilter || undefined,
      page,
      pageSize,
    }),
    [search, statusFilter, eleicaoFilter, page]
  )

  // Fetch chapas
  const {
    data: chapasData,
    isLoading,
    isError,
    error,
  } = useQuery({
    queryKey: ['chapas', queryParams],
    queryFn: () => chapasService.getAll(queryParams),
  })

  // Fetch eleicoes for filter
  const { data: eleicoes } = useQuery({
    queryKey: ['eleicoes'],
    queryFn: eleicoesService.getAll,
  })

  // Delete mutation
  const deleteMutation = useMutation({
    mutationFn: (id: string) => chapasService.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({
        title: 'Chapa excluida',
        description: 'A chapa foi excluida com sucesso.',
      })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao excluir chapa',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Approve mutation
  const aprovarMutation = useMutation({
    mutationFn: (id: string) => chapasService.aprovar(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({
        title: 'Chapa aprovada',
        description: 'A chapa foi aprovada com sucesso.',
      })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao aprovar chapa',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Reject mutation
  const reprovarMutation = useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      chapasService.reprovar(id, motivo),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({
        title: 'Chapa reprovada',
        description: 'A chapa foi reprovada.',
      })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao reprovar chapa',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Dialog helpers
  const openDialog = (action: DialogAction, chapa: Chapa) => {
    setSelectedChapa(chapa)
    setDialogAction(action)
    setRejectReason('')
    setDialogOpen(true)
  }

  const closeDialog = () => {
    setDialogOpen(false)
    setDialogAction(null)
    setSelectedChapa(null)
    setRejectReason('')
  }

  const handleDialogConfirm = () => {
    if (!selectedChapa) return

    switch (dialogAction) {
      case 'delete':
        deleteMutation.mutate(selectedChapa.id)
        break
      case 'approve':
        aprovarMutation.mutate(selectedChapa.id)
        break
      case 'reject':
        reprovarMutation.mutate({
          id: selectedChapa.id,
          motivo: rejectReason || 'Chapa reprovada pela administracao',
        })
        break
    }
  }

  const isDialogLoading =
    deleteMutation.isPending || aprovarMutation.isPending || reprovarMutation.isPending

  // Calculate pagination
  const totalPages = chapasData?.totalPages || 1
  const total = chapasData?.total || 0
  const chapas = chapasData?.data || []

  // Reset page when filters change
  const handleFilterChange = () => {
    setPage(1)
  }

  // Format date
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    })
  }

  // Get status badge
  const getStatusBadge = (status: StatusChapa) => {
    const config = statusConfig[status]
    return (
      <span
        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config?.color || 'bg-gray-100 text-gray-800'}`}
      >
        {config?.label || 'Desconhecido'}
      </span>
    )
  }

  // Get dialog content based on action
  const getDialogContent = () => {
    switch (dialogAction) {
      case 'delete':
        return {
          title: 'Excluir Chapa',
          description: (
            <>
              Tem certeza que deseja excluir a chapa <strong>"{selectedChapa?.nome}"</strong>?
              Esta acao nao pode ser desfeita.
              {selectedChapa?.membros && selectedChapa.membros.length > 0 && (
                <span className="block mt-2 text-orange-600">
                  Atencao: Esta chapa possui {selectedChapa.membros.length} membro(s) cadastrado(s)
                  que tambem serao removidos.
                </span>
              )}
            </>
          ),
          confirmLabel: 'Excluir',
          confirmVariant: 'bg-red-600 hover:bg-red-700 text-white',
        }
      case 'approve':
        return {
          title: 'Aprovar Chapa',
          description: (
            <>
              Confirma a aprovacao da chapa <strong>"{selectedChapa?.nome}"</strong>?
              Apos aprovada, a chapa estara apta a participar da eleicao.
            </>
          ),
          confirmLabel: 'Aprovar',
          confirmVariant: 'bg-green-600 hover:bg-green-700 text-white',
        }
      case 'reject':
        return {
          title: 'Reprovar Chapa',
          description: (
            <div className="space-y-4">
              <p>
                Confirma a reprovacao da chapa <strong>"{selectedChapa?.nome}"</strong>?
              </p>
              <div className="space-y-2">
                <Label htmlFor="motivo">Motivo da reprovacao</Label>
                <textarea
                  id="motivo"
                  value={rejectReason}
                  onChange={(e) => setRejectReason(e.target.value)}
                  placeholder="Informe o motivo da reprovacao..."
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                />
              </div>
            </div>
          ),
          confirmLabel: 'Reprovar',
          confirmVariant: 'bg-red-600 hover:bg-red-700 text-white',
        }
      default:
        return {
          title: '',
          description: '',
          confirmLabel: '',
          confirmVariant: '',
        }
    }
  }

  const dialogContent = getDialogContent()

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Chapas</h1>
          <p className="text-gray-600">Gerencie as chapas eleitorais</p>
        </div>
        <Link to="/chapas/nova">
          <Button>
            <Plus className="mr-2 h-4 w-4" />
            Nova Chapa
          </Button>
        </Link>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
            {/* Search */}
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar chapas por nome, sigla ou numero..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value)
                  handleFilterChange()
                }}
                className="pl-10"
              />
            </div>

            {/* Status Filter */}
            <div className="flex items-center gap-2">
              <Filter className="h-4 w-4 text-gray-400" />
              <select
                value={statusFilter}
                onChange={(e) => {
                  setStatusFilter(e.target.value === '' ? '' : (Number(e.target.value) as StatusChapa))
                  handleFilterChange()
                }}
                className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
              >
                <option value="">Todos os status</option>
                {Object.entries(statusConfig).map(([value, config]) => (
                  <option key={value} value={value}>
                    {config.label}
                  </option>
                ))}
              </select>
            </div>

            {/* Eleicao Filter */}
            <select
              value={eleicaoFilter}
              onChange={(e) => {
                setEleicaoFilter(e.target.value)
                handleFilterChange()
              }}
              className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
            >
              <option value="">Todas as eleições</option>
              {eleicoes?.map((eleicao) => (
                <option key={eleicao.id} value={eleicao.id}>
                  {eleicao.nome} ({eleicao.ano})
                </option>
              ))}
            </select>
          </div>
        </CardHeader>

        <CardContent>
          {/* Loading State */}
          {isLoading && (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
              <span className="ml-2 text-gray-500">Carregando chapas...</span>
            </div>
          )}

          {/* Error State */}
          {isError && (
            <div className="flex flex-col items-center justify-center py-12">
              <AlertTriangle className="h-12 w-12 text-red-400" />
              <p className="mt-2 text-center text-gray-500">
                Erro ao carregar chapas.
                <br />
                <span className="text-sm text-red-500">
                  {(error as any)?.message || 'Tente novamente mais tarde.'}
                </span>
              </p>
              <Button
                variant="outline"
                className="mt-4"
                onClick={() => queryClient.invalidateQueries({ queryKey: ['chapas'] })}
              >
                Tentar novamente
              </Button>
            </div>
          )}

          {/* Table */}
          {!isLoading && !isError && (
            <>
              {chapas.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="text-left py-3 px-4 font-medium">Nome</th>
                        <th className="text-left py-3 px-4 font-medium">Eleição</th>
                        <th className="text-left py-3 px-4 font-medium">Status</th>
                        <th className="text-center py-3 px-4 font-medium">Membros</th>
                        <th className="text-left py-3 px-4 font-medium">Data Registro</th>
                        <th className="text-right py-3 px-4 font-medium">Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      {chapas.map((chapa) => (
                        <tr key={chapa.id} className="border-b hover:bg-gray-50">
                          {/* Nome */}
                          <td className="py-3 px-4">
                            <div className="flex items-center gap-3">
                              <div className="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-600">
                                {chapa.numero}
                              </div>
                              <div>
                                <Link
                                  to={`/chapas/${chapa.id}`}
                                  className="font-medium text-gray-900 hover:text-blue-600"
                                >
                                  {chapa.nome}
                                </Link>
                                {chapa.sigla && (
                                  <p className="text-sm text-gray-500">{chapa.sigla}</p>
                                )}
                              </div>
                            </div>
                          </td>

                          {/* Eleicao */}
                          <td className="py-3 px-4">
                            <span className="text-gray-700">
                              {chapa.eleicaoNome || '-'}
                            </span>
                          </td>

                          {/* Status */}
                          <td className="py-3 px-4">{getStatusBadge(chapa.status)}</td>

                          {/* Membros */}
                          <td className="py-3 px-4 text-center">
                            <Link
                              to={`/chapas/${chapa.id}/membros`}
                              className="inline-flex items-center gap-1 text-gray-600 hover:text-blue-600"
                            >
                              <Users className="h-4 w-4" />
                              {chapa.membros?.length || 0}
                            </Link>
                          </td>

                          {/* Data Registro */}
                          <td className="py-3 px-4 text-sm text-gray-500">
                            {formatDate(chapa.dataCadastro || chapa.createdAt)}
                          </td>

                          {/* Acoes */}
                          <td className="py-3 px-4 text-right">
                            <div className="flex items-center justify-end gap-1">
                              {/* View */}
                              <Link to={`/chapas/${chapa.id}`}>
                                <Button variant="ghost" size="icon" title="Ver detalhes">
                                  <Eye className="h-4 w-4" />
                                </Button>
                              </Link>

                              {/* Edit */}
                              <Link to={`/chapas/${chapa.id}/editar`}>
                                <Button variant="ghost" size="icon" title="Editar">
                                  <Edit className="h-4 w-4" />
                                </Button>
                              </Link>

                              {/* Membros */}
                              <Link to={`/chapas/${chapa.id}/membros`}>
                                <Button variant="ghost" size="icon" title="Gerenciar membros">
                                  <Users className="h-4 w-4" />
                                </Button>
                              </Link>

                              {/* Documentos */}
                              <Link to={`/chapas/${chapa.id}/documentos`}>
                                <Button variant="ghost" size="icon" title="Documentos">
                                  <FileText className="h-4 w-4" />
                                </Button>
                              </Link>

                              {/* Approve (only for pending/em_analise) */}
                              {(chapa.status === StatusChapa.PENDENTE ||
                                chapa.status === StatusChapa.EM_ANALISE) && (
                                <>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    title="Aprovar"
                                    onClick={() => openDialog('approve', chapa)}
                                  >
                                    <Check className="h-4 w-4 text-green-600" />
                                  </Button>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    title="Reprovar"
                                    onClick={() => openDialog('reject', chapa)}
                                  >
                                    <X className="h-4 w-4 text-red-600" />
                                  </Button>
                                </>
                              )}

                              {/* Delete */}
                              <Button
                                variant="ghost"
                                size="icon"
                                title="Excluir"
                                onClick={() => openDialog('delete', chapa)}
                                disabled={chapa.status === StatusChapa.APROVADA}
                              >
                                <Trash2 className="h-4 w-4 text-red-500" />
                              </Button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="text-center py-12">
                  <Users className="mx-auto h-12 w-12 text-gray-300" />
                  <p className="mt-2 text-gray-500">Nenhuma chapa encontrada.</p>
                  <Link to="/chapas/nova">
                    <Button variant="outline" className="mt-4">
                      <Plus className="mr-2 h-4 w-4" />
                      Criar primeira chapa
                    </Button>
                  </Link>
                </div>
              )}

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex items-center justify-between border-t pt-4 mt-4">
                  <p className="text-sm text-gray-500">
                    Mostrando {(page - 1) * pageSize + 1} a{' '}
                    {Math.min(page * pageSize, total)} de {total} chapas
                  </p>
                  <div className="flex items-center gap-2">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setPage((p) => Math.max(1, p - 1))}
                      disabled={page === 1}
                    >
                      <ChevronLeft className="h-4 w-4" />
                      Anterior
                    </Button>
                    <div className="flex items-center gap-1">
                      {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                        let pageNum: number
                        if (totalPages <= 5) {
                          pageNum = i + 1
                        } else if (page <= 3) {
                          pageNum = i + 1
                        } else if (page >= totalPages - 2) {
                          pageNum = totalPages - 4 + i
                        } else {
                          pageNum = page - 2 + i
                        }
                        return (
                          <Button
                            key={pageNum}
                            variant={page === pageNum ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => setPage(pageNum)}
                            className="w-10"
                          >
                            {pageNum}
                          </Button>
                        )
                      })}
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                      disabled={page === totalPages}
                    >
                      Proximo
                      <ChevronRight className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </CardContent>
      </Card>

      {/* Confirmation Dialog */}
      <AlertDialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{dialogContent.title}</AlertDialogTitle>
            <AlertDialogDescription asChild>
              <div>{dialogContent.description}</div>
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={isDialogLoading}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDialogConfirm}
              className={dialogContent.confirmVariant}
              disabled={isDialogLoading}
            >
              {isDialogLoading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Processando...
                </>
              ) : (
                dialogContent.confirmLabel
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
