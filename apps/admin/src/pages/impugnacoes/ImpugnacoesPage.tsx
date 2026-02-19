import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import {
  Plus,
  Search,
  Eye,
  Filter,
  AlertOctagon,
  Clock,
  CheckCircle,
  XCircle,
  FileText,
  ChevronLeft,
  ChevronRight,
  Loader2,
  RefreshCw,
  Download,
  RotateCcw,
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
  impugnacoesService,
  StatusImpugnacao,
  TipoImpugnacao,
  FaseImpugnacao,
} from '@/services/impugnacoes'
import { eleicoesService } from '@/services/eleicoes'

const statusLabels: Record<number, { label: string; color: string; icon: React.ReactNode }> = {
  [StatusImpugnacao.PENDENTE]: {
    label: 'Pendente',
    color: 'bg-yellow-100 text-yellow-800',
    icon: <Clock className="h-3 w-3" />,
  },
  [StatusImpugnacao.EM_ANALISE]: {
    label: 'Em Análise',
    color: 'bg-blue-100 text-blue-800',
    icon: <RefreshCw className="h-3 w-3" />,
  },
  [StatusImpugnacao.DEFERIDA]: {
    label: 'Deferida',
    color: 'bg-green-100 text-green-800',
    icon: <CheckCircle className="h-3 w-3" />,
  },
  [StatusImpugnacao.INDEFERIDA]: {
    label: 'Indeferida',
    color: 'bg-red-100 text-red-800',
    icon: <XCircle className="h-3 w-3" />,
  },
  [StatusImpugnacao.PARCIALMENTE_DEFERIDA]: {
    label: 'Parcialmente Deferida',
    color: 'bg-teal-100 text-teal-800',
    icon: <CheckCircle className="h-3 w-3" />,
  },
  [StatusImpugnacao.RECURSO]: {
    label: 'Em Recurso',
    color: 'bg-orange-100 text-orange-800',
    icon: <RotateCcw className="h-3 w-3" />,
  },
  [StatusImpugnacao.ARQUIVADA]: {
    label: 'Arquivada',
    color: 'bg-gray-100 text-gray-800',
    icon: <FileText className="h-3 w-3" />,
  },
}

const tipoLabels: Record<number, string> = {
  [TipoImpugnacao.CANDIDATURA]: 'Candidatura',
  [TipoImpugnacao.CHAPA]: 'Chapa',
  [TipoImpugnacao.ELEICAO]: 'Eleicao',
  [TipoImpugnacao.RESULTADO]: 'Resultado',
  [TipoImpugnacao.VOTACAO]: 'Votacao',
}

const faseLabels: Record<number, string> = {
  [FaseImpugnacao.REGISTRO]: 'Registro',
  [FaseImpugnacao.ANALISE_INICIAL]: 'Analise Inicial',
  [FaseImpugnacao.DEFESA]: 'Defesa',
  [FaseImpugnacao.PARECER]: 'Parecer',
  [FaseImpugnacao.JULGAMENTO]: 'Julgamento',
  [FaseImpugnacao.RECURSO]: 'Recurso',
  [FaseImpugnacao.ENCERRADA]: 'Encerrada',
}

export function ImpugnacoesPage() {
  const [search, setSearch] = useState('')
  const [statusFilter, setStatusFilter] = useState<string>('all')
  const [tipoFilter, setTipoFilter] = useState<string>('all')
  const [eleicaoFilter, setEleicaoFilter] = useState<string>('all')
  const [page, setPage] = useState(1)
  const pageSize = 10

  // Fetch eleicoes for filter
  const { data: eleicoes } = useQuery({
    queryKey: ['eleições'],
    queryFn: eleicoesService.getAll,
  })

  // Build params for API
  const params = {
    search: search || undefined,
    status: statusFilter !== 'all' ? Number(statusFilter) : undefined,
    tipo: tipoFilter !== 'all' ? Number(tipoFilter) : undefined,
    eleicaoId: eleicaoFilter !== 'all' ? eleicaoFilter : undefined,
    page,
    pageSize,
  }

  // Fetch impugnacoes
  const { data: impugnacoesData, isLoading, refetch } = useQuery({
    queryKey: ['impugnações', params],
    queryFn: () => impugnacoesService.getAll(params),
  })

  // Fetch statistics
  const { data: estatisticas } = useQuery({
    queryKey: ['impugnações-estatisticas', eleicaoFilter],
    queryFn: () => impugnacoesService.getEstatisticas(eleicaoFilter !== 'all' ? eleicaoFilter : undefined),
  })

  const impugnacoes = impugnacoesData?.data || []
  const totalPages = impugnacoesData?.totalPages || 1
  const total = impugnacoesData?.total || 0

  const getStatusBadge = (status: number) => {
    const config = statusLabels[status] || statusLabels[StatusImpugnacao.PENDENTE]
    return (
      <span
        className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}
      >
        {config.icon}
        {config.label}
      </span>
    )
  }

  const getTipoBadge = (tipo: number) => {
    return (
      <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
        {tipoLabels[tipo] || 'Desconhecido'}
      </span>
    )
  }

  const getFaseBadge = (fase: number) => {
    return (
      <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
        {faseLabels[fase] || 'Desconhecido'}
      </span>
    )
  }

  const handleClearFilters = () => {
    setSearch('')
    setStatusFilter('all')
    setTipoFilter('all')
    setEleicaoFilter('all')
    setPage(1)
  }

  const handleExportReport = async () => {
    try {
      const blob = await impugnacoesService.gerarRelatorio({
        eleicaoId: eleicaoFilter !== 'all' ? eleicaoFilter : undefined,
        formato: 'xlsx',
      })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `impugnacoes-${new Date().toISOString().split('T')[0]}.xlsx`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (error) {
      console.error('Erro ao exportar relatório:', error)
    }
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Impugnações</h1>
          <p className="text-gray-600">Gerencie as impugnações do processo eleitoral</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handleExportReport}>
            <Download className="mr-2 h-4 w-4" />
            Exportar
          </Button>
          <Link to="/impugnacoes/nova">
            <Button>
              <Plus className="mr-2 h-4 w-4" />
              Nova Impugnação
            </Button>
          </Link>
        </div>
      </div>

      {/* Statistics Cards */}
      {estatisticas && (
        <div className="grid gap-4 md:grid-cols-4 lg:grid-cols-7">
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold">{estatisticas.total}</div>
              <p className="text-xs text-gray-500">Total</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-yellow-600">{estatisticas.pendentes}</div>
              <p className="text-xs text-gray-500">Pendentes</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-blue-600">{estatisticas.emAnalise}</div>
              <p className="text-xs text-gray-500">Em Análise</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-green-600">{estatisticas.deferidas}</div>
              <p className="text-xs text-gray-500">Deferidas</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-red-600">{estatisticas.indeferidas}</div>
              <p className="text-xs text-gray-500">Indeferidas</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-orange-600">{estatisticas.emRecurso}</div>
              <p className="text-xs text-gray-500">Em Recurso</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <div className="text-2xl font-bold text-gray-600">{estatisticas.arquivadas}</div>
              <p className="text-xs text-gray-500">Arquivadas</p>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Filters */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Filter className="h-5 w-5" />
            Filtros
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
            <div className="relative lg:col-span-2">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar por protocolo, requerente..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value)
                  setPage(1)
                }}
                className="pl-10"
              />
            </div>

            <Select
              value={eleicaoFilter}
              onValueChange={(value) => {
                setEleicaoFilter(value)
                setPage(1)
              }}
            >
              <SelectTrigger>
                <SelectValue placeholder="Eleicao" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todas Eleições</SelectItem>
                {eleicoes?.map((eleicao) => (
                  <SelectItem key={eleicao.id} value={eleicao.id}>
                    {eleicao.nome}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <Select
              value={statusFilter}
              onValueChange={(value) => {
                setStatusFilter(value)
                setPage(1)
              }}
            >
              <SelectTrigger>
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos Status</SelectItem>
                <SelectItem value={String(StatusImpugnacao.PENDENTE)}>Pendente</SelectItem>
                <SelectItem value={String(StatusImpugnacao.EM_ANALISE)}>Em Análise</SelectItem>
                <SelectItem value={String(StatusImpugnacao.DEFERIDA)}>Deferida</SelectItem>
                <SelectItem value={String(StatusImpugnacao.INDEFERIDA)}>Indeferida</SelectItem>
                <SelectItem value={String(StatusImpugnacao.PARCIALMENTE_DEFERIDA)}>
                  Parcialmente Deferida
                </SelectItem>
                <SelectItem value={String(StatusImpugnacao.RECURSO)}>Em Recurso</SelectItem>
                <SelectItem value={String(StatusImpugnacao.ARQUIVADA)}>Arquivada</SelectItem>
              </SelectContent>
            </Select>

            <Select
              value={tipoFilter}
              onValueChange={(value) => {
                setTipoFilter(value)
                setPage(1)
              }}
            >
              <SelectTrigger>
                <SelectValue placeholder="Tipo" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Todos Tipos</SelectItem>
                <SelectItem value={String(TipoImpugnacao.CANDIDATURA)}>Candidatura</SelectItem>
                <SelectItem value={String(TipoImpugnacao.CHAPA)}>Chapa</SelectItem>
                <SelectItem value={String(TipoImpugnacao.ELEICAO)}>Eleição</SelectItem>
                <SelectItem value={String(TipoImpugnacao.RESULTADO)}>Resultado</SelectItem>
                <SelectItem value={String(TipoImpugnacao.VOTACAO)}>Votação</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {(search || statusFilter !== 'all' || tipoFilter !== 'all' || eleicaoFilter !== 'all') && (
            <div className="mt-4 flex items-center gap-2">
              <span className="text-sm text-gray-500">Filtros ativos:</span>
              <Button variant="ghost" size="sm" onClick={handleClearFilters}>
                Limpar filtros
              </Button>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Table */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle className="flex items-center gap-2">
              <AlertOctagon className="h-5 w-5 text-red-500" />
              Lista de Impugnações
            </CardTitle>
            <Button variant="ghost" size="sm" onClick={() => refetch()}>
              <RefreshCw className="h-4 w-4" />
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : impugnacoes.length > 0 ? (
            <>
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b">
                      <th className="text-left py-3 px-4 font-medium">Protocolo</th>
                      <th className="text-left py-3 px-4 font-medium">Requerente</th>
                      <th className="text-left py-3 px-4 font-medium">Eleição</th>
                      <th className="text-left py-3 px-4 font-medium">Tipo</th>
                      <th className="text-left py-3 px-4 font-medium">Fase</th>
                      <th className="text-left py-3 px-4 font-medium">Status</th>
                      <th className="text-left py-3 px-4 font-medium">Data</th>
                      <th className="text-right py-3 px-4 font-medium">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    {impugnacoes.map((impugnacao) => (
                      <tr key={impugnacao.id} className="border-b hover:bg-gray-50">
                        <td className="py-3 px-4">
                          <Link
                            to={`/impugnacoes/${impugnacao.id}`}
                            className="text-blue-600 hover:underline font-medium"
                          >
                            {impugnacao.protocolo}
                          </Link>
                        </td>
                        <td className="py-3 px-4">
                          <div>
                            <div className="font-medium">{impugnacao.impugnanteNome}</div>
                            {impugnacao.chapaNome && (
                              <div className="text-xs text-gray-500">
                                Chapa: {impugnacao.chapaNome}
                              </div>
                            )}
                            {impugnacao.candidatoNome && (
                              <div className="text-xs text-gray-500">
                                Candidato: {impugnacao.candidatoNome}
                              </div>
                            )}
                          </div>
                        </td>
                        <td className="py-3 px-4">
                          <span className="text-sm">{impugnacao.eleicaoNome || '-'}</span>
                        </td>
                        <td className="py-3 px-4">{getTipoBadge(impugnacao.tipo)}</td>
                        <td className="py-3 px-4">{getFaseBadge(impugnacao.fase)}</td>
                        <td className="py-3 px-4">{getStatusBadge(impugnacao.status)}</td>
                        <td className="py-3 px-4 text-sm text-gray-500">
                          {new Date(impugnacao.createdAt).toLocaleDateString('pt-BR')}
                        </td>
                        <td className="py-3 px-4 text-right">
                          <div className="flex items-center justify-end gap-2">
                            <Link to={`/impugnacoes/${impugnacao.id}`}>
                              <Button variant="ghost" size="icon" title="Ver detalhes">
                                <Eye className="h-4 w-4" />
                              </Button>
                            </Link>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              <div className="mt-4 flex items-center justify-between">
                <div className="text-sm text-gray-500">
                  Mostrando {(page - 1) * pageSize + 1} a{' '}
                  {Math.min(page * pageSize, total)} de {total} registros
                </div>
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
                  <span className="text-sm text-gray-500">
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
              <AlertOctagon className="h-12 w-12 mx-auto text-gray-300 mb-4" />
              <p className="text-gray-500 mb-4">Nenhuma impugnação encontrada.</p>
              <Link to="/impugnacoes/nova">
                <Button>
                  <Plus className="mr-2 h-4 w-4" />
                  Registrar Nova Impugnação
                </Button>
              </Link>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
