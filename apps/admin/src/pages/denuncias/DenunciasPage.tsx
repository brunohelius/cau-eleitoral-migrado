import { useState, useMemo } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import {
  Plus,
  Search,
  Eye,
  Gavel,
  Archive,
  PlayCircle,
  MoreHorizontal,
  AlertTriangle,
  Filter,
  ChevronLeft,
  ChevronRight,
  FileText,
  Download,
  RefreshCw,
  X,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  denunciasService,
  StatusDenuncia,
  TipoDenuncia,
  statusDenunciaLabels,
  tipoDenunciaLabels,
  type Denuncia,
} from '@/services/denuncias'
import { eleicoesService, type Eleicao } from '@/services/eleicoes'

// Status filter options (grouped for easier filtering)
const statusFilterOptions = [
  { value: '', label: 'Todos os Status' },
  { value: String(StatusDenuncia.RECEBIDA), label: 'Recebida' },
  { value: String(StatusDenuncia.EM_ANALISE), label: 'Em Análise' },
  { value: String(StatusDenuncia.AGUARDANDO_JULGAMENTO), label: 'Aguardando Julgamento' },
  { value: String(StatusDenuncia.JULGADA), label: 'Julgada' },
  { value: String(StatusDenuncia.PROCEDENTE), label: 'Procedente' },
  { value: String(StatusDenuncia.IMPROCEDENTE), label: 'Improcedente' },
  { value: String(StatusDenuncia.ARQUIVADA), label: 'Arquivada' },
]

// Type filter options
const tipoFilterOptions = [
  { value: '', label: 'Todos os Tipos' },
  { value: String(TipoDenuncia.PROPAGANDA_IRREGULAR), label: 'Propaganda Irregular' },
  { value: String(TipoDenuncia.ABUSO_PODER), label: 'Abuso de Poder' },
  { value: String(TipoDenuncia.CAPTACAO_ILICITA_SUFRAGIO), label: 'Captacao Ilicita' },
  { value: String(TipoDenuncia.USO_INDEVIDO), label: 'Uso Indevido' },
  { value: String(TipoDenuncia.INELEGIBILIDADE), label: 'Inelegibilidade' },
  { value: String(TipoDenuncia.FRAUDE_DOCUMENTAL), label: 'Fraude Documental' },
  { value: String(TipoDenuncia.OUTROS), label: 'Outros' },
]

export function DenunciasPage() {
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Filter state
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [tipoFilter, setTipoFilter] = useState('')
  const [eleicaoFilter, setEleicaoFilter] = useState('')
  const [showFilters, setShowFilters] = useState(false)

  // Pagination state
  const [page, setPage] = useState(1)
  const [pageSize] = useState(10)

  // Action modal state
  const [actionModal, setActionModal] = useState<{
    type: 'analisar' | 'arquivar' | 'reabrir' | null
    denúncia: Denúncia | null
  }>({ type: null, denuncia: null })
  const [actionMotivo, setActionMotivo] = useState('')

  // Fetch eleicoes for filter
  const { data: eleicoes } = useQuery({
    queryKey: ['eleições'],
    queryFn: eleicoesService.getAll,
  })

  // Fetch denuncias
  const { data: denunciasResponse, isLoading, refetch } = useQuery({
    queryKey: ['denúncias', page, pageSize, statusFilter, tipoFilter, eleicaoFilter, search],
    queryFn: async () => {
      const params: Record<string, unknown> = {
        page,
        pageSize,
      }
      if (statusFilter) params.status = Number(statusFilter)
      if (tipoFilter) params.tipo = Number(tipoFilter)
      if (eleicaoFilter) params.eleicaoId = eleicaoFilter
      if (search) params.search = search

      return denunciasService.getAll(params)
    },
  })

  // Mutations for workflow actions
  const iniciarAnaliseMutation = useMutation({
    mutationFn: (id: string) => denunciasService.iniciarAnalise(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Análise iniciada',
        description: 'A denúncia foi movida para análise.',
      })
      setActionModal({ type: null, denuncia: null })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível iniciar a análise.',
      })
    },
  })

  const arquivarMutation = useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      denunciasService.arquivar(id, motivo),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denúncia arquivada',
        description: 'A denúncia foi arquivada com sucesso.',
      })
      setActionModal({ type: null, denuncia: null })
      setActionMotivo('')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível arquivar a denúncia.',
      })
    },
  })

  const reabrirMutation = useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      denunciasService.reabrir(id, motivo),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denúncia reaberta',
        description: 'A denúncia foi reaberta com sucesso.',
      })
      setActionModal({ type: null, denuncia: null })
      setActionMotivo('')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível reabrir a denúncia.',
      })
    },
  })

  // Filter denuncias locally if needed (for search that API doesn't handle)
  const denuncias = useMemo(() => {
    return denunciasResponse?.data || []
  }, [denunciasResponse])

  const totalPages = denunciasResponse?.totalPages || 1
  const total = denunciasResponse?.total || 0

  // Handle actions
  const handleIniciarAnalise = (denuncia: Denuncia) => {
    iniciarAnaliseMutation.mutate(denuncia.id)
  }

  const handleArquivar = () => {
    if (!actionModal.denuncia || !actionMotivo.trim()) {
      toast({
        variant: 'destructive',
        title: 'Motivo obrigatório',
        description: 'Informe o motivo do arquivamento.',
      })
      return
    }
    arquivarMutation.mutate({ id: actionModal.denuncia.id, motivo: actionMotivo })
  }

  const handleReabrir = () => {
    if (!actionModal.denuncia || !actionMotivo.trim()) {
      toast({
        variant: 'destructive',
        title: 'Motivo obrigatório',
        description: 'Informe o motivo da reabertura.',
      })
      return
    }
    reabrirMutation.mutate({ id: actionModal.denuncia.id, motivo: actionMotivo })
  }

  const handleExportarRelatorio = async () => {
    try {
      const blob = await denunciasService.gerarRelatorio({
        eleicaoId: eleicaoFilter || undefined,
        formato: 'xlsx',
      })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `denuncias-relatorio-${new Date().toISOString().split('T')[0]}.xlsx`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
      toast({
        title: 'Relatório exportado',
        description: 'O download do relatório foi iniciado.',
      })
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível exportar o relatório.',
      })
    }
  }

  const clearFilters = () => {
    setSearch('')
    setStatusFilter('')
    setTipoFilter('')
    setEleicaoFilter('')
    setPage(1)
  }

  const hasActiveFilters = search || statusFilter || tipoFilter || eleicaoFilter

  // Get available actions for a denuncia based on its status
  const getAvailableActions = (denuncia: Denuncia) => {
    const actions: Array<{
      label: string
      icon: React.ReactNode
      action: () => void
      variant?: 'default' | 'destructive'
    }> = []

    switch (denuncia.status) {
      case StatusDenuncia.RECEBIDA:
        actions.push({
          label: 'Iniciar Análise',
          icon: <PlayCircle className="h-4 w-4" />,
          action: () => handleIniciarAnalise(denuncia),
        })
        actions.push({
          label: 'Arquivar',
          icon: <Archive className="h-4 w-4" />,
          action: () => setActionModal({ type: 'arquivar', denuncia }),
          variant: 'destructive',
        })
        break
      case StatusDenuncia.EM_ANALISE:
      case StatusDenuncia.ADMISSIBILIDADE_ACEITA:
      case StatusDenuncia.AGUARDANDO_DEFESA:
      case StatusDenuncia.DEFESA_APRESENTADA:
      case StatusDenuncia.AGUARDANDO_JULGAMENTO:
        actions.push({
          label: 'Arquivar',
          icon: <Archive className="h-4 w-4" />,
          action: () => setActionModal({ type: 'arquivar', denuncia }),
          variant: 'destructive',
        })
        break
      case StatusDenuncia.ARQUIVADA:
        actions.push({
          label: 'Reabrir',
          icon: <RefreshCw className="h-4 w-4" />,
          action: () => setActionModal({ type: 'reabrir', denuncia }),
        })
        break
    }

    return actions
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Denúncias</h1>
          <p className="text-gray-600">Gerencie as denuncias eleitorais ({total} registros)</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handleExportarRelatorio}>
            <Download className="mr-2 h-4 w-4" />
            Exportar
          </Button>
          <Link to="/denuncias/nova">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Nova Denúncia
            </Button>
          </Link>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid gap-4 md:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pendentes</CardTitle>
            <AlertTriangle className="h-4 w-4 text-yellow-500" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {denuncias.filter((d) => d.status === StatusDenuncia.RECEBIDA).length}
            </div>
            <p className="text-xs text-muted-foreground">aguardando análise</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Em Análise</CardTitle>
            <FileText className="h-4 w-4 text-blue-500" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {denuncias.filter((d) => d.status === StatusDenuncia.EM_ANALISE).length}
            </div>
            <p className="text-xs text-muted-foreground">em processamento</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Aguardando Julgamento</CardTitle>
            <Gavel className="h-4 w-4 text-orange-500" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {denuncias.filter((d) => d.status === StatusDenuncia.AGUARDANDO_JULGAMENTO).length}
            </div>
            <p className="text-xs text-muted-foreground">prontas para julgar</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Arquivadas</CardTitle>
            <Archive className="h-4 w-4 text-gray-500" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {denuncias.filter((d) => d.status === StatusDenuncia.ARQUIVADA).length}
            </div>
            <p className="text-xs text-muted-foreground">finalizadas</p>
          </CardContent>
        </Card>
      </div>

      {/* Filters */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-4 flex-1">
              <div className="relative flex-1 max-w-md">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  placeholder="Buscar por protocolo, titulo ou denunciante..."
                  value={search}
                  onChange={(e) => {
                    setSearch(e.target.value)
                    setPage(1)
                  }}
                  className="pl-10"
                />
              </div>
              <Button
                variant={showFilters ? 'secondary' : 'outline'}
                onClick={() => setShowFilters(!showFilters)}
              >
                <Filter className="mr-2 h-4 w-4" />
                Filtros
                {hasActiveFilters && (
                  <span className="ml-2 rounded-full bg-primary px-2 py-0.5 text-xs text-white">
                    {[statusFilter, tipoFilter, eleicaoFilter].filter(Boolean).length}
                  </span>
                )}
              </Button>
              {hasActiveFilters && (
                <Button variant="ghost" onClick={clearFilters}>
                  <X className="mr-2 h-4 w-4" />
                  Limpar
                </Button>
              )}
            </div>
            <Button variant="ghost" size="icon" onClick={() => refetch()}>
              <RefreshCw className="h-4 w-4" />
            </Button>
          </div>

          {/* Expanded Filters */}
          {showFilters && (
            <div className="grid gap-4 pt-4 md:grid-cols-3">
              <div>
                <label className="text-sm font-medium text-gray-700">Status</label>
                <select
                  value={statusFilter}
                  onChange={(e) => {
                    setStatusFilter(e.target.value)
                    setPage(1)
                  }}
                  className="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                  {statusFilterOptions.map((opt) => (
                    <option key={opt.value} value={opt.value}>
                      {opt.label}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="text-sm font-medium text-gray-700">Tipo</label>
                <select
                  value={tipoFilter}
                  onChange={(e) => {
                    setTipoFilter(e.target.value)
                    setPage(1)
                  }}
                  className="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                  {tipoFilterOptions.map((opt) => (
                    <option key={opt.value} value={opt.value}>
                      {opt.label}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="text-sm font-medium text-gray-700">Eleição</label>
                <select
                  value={eleicaoFilter}
                  onChange={(e) => {
                    setEleicaoFilter(e.target.value)
                    setPage(1)
                  }}
                  className="mt-1 flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                >
                  <option value="">Todas as Eleições</option>
                  {eleicoes?.map((eleicao: Eleicao) => (
                    <option key={eleicao.id} value={eleicao.id}>
                      {eleicao.nome} ({eleicao.ano})
                    </option>
                  ))}
                </select>
              </div>
            </div>
          )}
        </CardHeader>

        {/* Table */}
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-12">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
          ) : denuncias.length > 0 ? (
            <>
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b">
                      <th className="text-left py-3 px-4 font-medium">Protocolo</th>
                      <th className="text-left py-3 px-4 font-medium">Denunciante</th>
                      <th className="text-left py-3 px-4 font-medium">Denunciado</th>
                      <th className="text-left py-3 px-4 font-medium">Tipo</th>
                      <th className="text-left py-3 px-4 font-medium">Status</th>
                      <th className="text-left py-3 px-4 font-medium">Data</th>
                      <th className="text-right py-3 px-4 font-medium">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    {denuncias.map((denuncia) => {
                      const statusInfo = statusDenunciaLabels[denuncia.status] || {
                        label: 'Desconhecido',
                        color: 'bg-gray-100 text-gray-800',
                      }
                      const tipoLabel = tipoDenunciaLabels[denuncia.tipo] || 'Outro'
                      const actions = getAvailableActions(denuncia)

                      return (
                        <tr key={denuncia.id} className="border-b hover:bg-gray-50">
                          <td className="py-3 px-4">
                            <Link
                              to={`/denuncias/${denuncia.id}`}
                              className="font-medium text-primary hover:underline"
                            >
                              {denuncia.protocolo}
                            </Link>
                            <p className="text-xs text-gray-500 truncate max-w-[200px]">
                              {denuncia.titulo}
                            </p>
                          </td>
                          <td className="py-3 px-4">
                            {denuncia.anonima ? (
                              <span className="text-gray-400 italic">Anonimo</span>
                            ) : (
                              denúncia.denuncianteNome || '-'
                            )}
                          </td>
                          <td className="py-3 px-4">
                            {denuncia.chapaNome || denuncia.membroNome || '-'}
                          </td>
                          <td className="py-3 px-4">
                            <span className="text-sm">{tipoLabel}</span>
                          </td>
                          <td className="py-3 px-4">
                            <span
                              className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
                            >
                              {statusInfo.label}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-sm text-gray-500">
                            {new Date(denuncia.dataDenuncia).toLocaleDateString('pt-BR')}
                          </td>
                          <td className="py-3 px-4 text-right">
                            <div className="flex items-center justify-end gap-1">
                              <Link to={`/denuncias/${denuncia.id}`}>
                                <Button variant="ghost" size="icon" title="Ver detalhes">
                                  <Eye className="h-4 w-4" />
                                </Button>
                              </Link>
                              {(denuncia.status === StatusDenuncia.EM_ANALISE ||
                                denuncia.status === StatusDenuncia.AGUARDANDO_JULGAMENTO) && (
                                <Link to={`/denuncias/${denuncia.id}/julgamento`}>
                                  <Button variant="ghost" size="icon" title="Julgar">
                                    <Gavel className="h-4 w-4" />
                                  </Button>
                                </Link>
                              )}
                              {actions.length > 0 && (
                                <div className="relative group">
                                  <Button variant="ghost" size="icon">
                                    <MoreHorizontal className="h-4 w-4" />
                                  </Button>
                                  <div className="absolute right-0 top-full mt-1 w-48 rounded-md border bg-white shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                                    {actions.map((action, idx) => (
                                      <button
                                        key={idx}
                                        onClick={action.action}
                                        className={`flex items-center gap-2 w-full px-4 py-2 text-sm hover:bg-gray-100 ${
                                          action.variant === 'destructive'
                                            ? 'text-red-600'
                                            : 'text-gray-700'
                                        }`}
                                      >
                                        {action.icon}
                                        {action.label}
                                      </button>
                                    ))}
                                  </div>
                                </div>
                              )}
                            </div>
                          </td>
                        </tr>
                      )
                    })}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              <div className="flex items-center justify-between pt-4">
                <p className="text-sm text-gray-500">
                  Mostrando {(page - 1) * pageSize + 1} a{' '}
                  {Math.min(page * pageSize, total)} de {total} registros
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
                  <span className="text-sm text-gray-600">
                    Pagina {page} de {totalPages}
                  </span>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                    disabled={page === totalPages}
                  >
                    Proxima
                    <ChevronRight className="h-4 w-4" />
                  </Button>
                </div>
              </div>
            </>
          ) : (
            <div className="text-center py-12">
              <AlertTriangle className="h-12 w-12 text-gray-300 mx-auto mb-4" />
              <p className="text-gray-500">Nenhuma denúncia encontrada.</p>
              {hasActiveFilters && (
                <Button variant="link" onClick={clearFilters} className="mt-2">
                  Limpar filtros
                </Button>
              )}
            </div>
          )}
        </CardContent>
      </Card>

      {/* Action Modal */}
      {actionModal.type && actionModal.denuncia && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div className="p-6">
              <h3 className="text-lg font-semibold mb-4">
                {actionModal.type === 'arquivar' && 'Arquivar Denuncia'}
                {actionModal.type === 'reabrir' && 'Reabrir Denuncia'}
              </h3>
              <p className="text-sm text-gray-600 mb-4">
                Protocolo: <strong>{actionModal.denuncia.protocolo}</strong>
              </p>
              <div className="space-y-4">
                <div>
                  <label className="text-sm font-medium text-gray-700">
                    Motivo *
                  </label>
                  <textarea
                    value={actionMotivo}
                    onChange={(e) => setActionMotivo(e.target.value)}
                    className="mt-1 flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    placeholder={
                      actionModal.type === 'arquivar'
                        ? 'Informe o motivo do arquivamento...'
                        : 'Informe o motivo da reabertura...'
                    }
                  />
                </div>
              </div>
              <div className="flex justify-end gap-3 mt-6">
                <Button
                  variant="outline"
                  onClick={() => {
                    setActionModal({ type: null, denuncia: null })
                    setActionMotivo('')
                  }}
                >
                  Cancelar
                </Button>
                <Button
                  variant={actionModal.type === 'arquivar' ? 'destructive' : 'default'}
                  onClick={actionModal.type === 'arquivar' ? handleArquivar : handleReabrir}
                  disabled={arquivarMutation.isPending || reabrirMutation.isPending}
                >
                  {(arquivarMutation.isPending || reabrirMutation.isPending) && (
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" />
                  )}
                  {actionModal.type === 'arquivar' ? 'Arquivar' : 'Reabrir'}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
