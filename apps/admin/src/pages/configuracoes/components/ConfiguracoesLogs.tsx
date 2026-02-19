import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import {
  Search,
  Download,
  Filter,
  User,
  Clock,
  AlertTriangle,
  CheckCircle,
  Info,
  XCircle,
  Loader2,
  Eye,
  RefreshCw,
  Calendar,
  FileText,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { configuracoesService, LogConfiguracao } from '@/services/configuracoes'

interface ConfiguracoesLogsProps {
  isLoading?: boolean
}

export function ConfiguracoesLogs({ isLoading: parentLoading }: ConfiguracoesLogsProps) {
  const [search, setSearch] = useState('')
  const [filterNivel, setFilterNivel] = useState<string>('all')
  const [dataInicio, setDataInicio] = useState('')
  const [dataFim, setDataFim] = useState('')
  const [selectedLog, setSelectedLog] = useState<LogConfiguracao | null>(null)
  const [page, setPage] = useState(1)
  const pageSize = 20

  const {
    data: logsData,
    isLoading,
    refetch,
  } = useQuery({
    queryKey: ['configurações-logs', filterNivel, dataInicio, dataFim, page],
    queryFn: () => configuracoesService.getLogs({
      ...(dataInicio && { dataInicio }),
      ...(dataFim && { dataFim }),
      page,
      pageSize,
    }),
    enabled: !parentLoading,
  })

  const getChaveLabel = (chave: string) => {
    const parts = chave.split('.')
    const categorias: Record<string, string> = {
      sistema: 'Sistema',
      segurança: 'Segurança',
      notificação: 'Notificação',
      eleicao: 'Eleição',
      aparencia: 'Aparencia',
      integracao: 'Integracao',
    }
    return {
      categoria: categorias[parts[0]] || parts[0],
      campo: parts.slice(1).join('.'),
    }
  }

  const getCategoriaColor = (chave: string) => {
    const categoria = chave.split('.')[0]
    const colors: Record<string, string> = {
      sistema: 'bg-gray-100 text-gray-800',
      segurança: 'bg-red-100 text-red-800',
      notificação: 'bg-blue-100 text-blue-800',
      eleicao: 'bg-green-100 text-green-800',
      aparencia: 'bg-purple-100 text-purple-800',
      integracao: 'bg-yellow-100 text-yellow-800',
    }
    return colors[categoria] || 'bg-gray-100 text-gray-800'
  }

  const filteredLogs = logsData?.data?.filter((log) => {
    const matchesSearch =
      log.chave.toLowerCase().includes(search.toLowerCase()) ||
      log.alteradoPorNome.toLowerCase().includes(search.toLowerCase()) ||
      log.valorAnterior.toLowerCase().includes(search.toLowerCase()) ||
      log.valorNovo.toLowerCase().includes(search.toLowerCase())
    return matchesSearch
  })

  const handleExport = async () => {
    try {
      const blob = await configuracoesService.exportarConfiguracoes()
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `logs-configuracoes-${new Date().toISOString().split('T')[0]}.json`
      a.click()
      URL.revokeObjectURL(url)
    } catch (error) {
      console.error('Erro ao exportar logs:', error)
    }
  }

  const stats = {
    total: logsData?.data?.length || 0,
    sistema: logsData?.data?.filter((l) => l.chave.startsWith('sistema')).length || 0,
    seguranca: logsData?.data?.filter((l) => l.chave.startsWith('seguranca')).length || 0,
    notificacao: logsData?.data?.filter((l) => l.chave.startsWith('notificacao')).length || 0,
    eleicao: logsData?.data?.filter((l) => l.chave.startsWith('eleicao')).length || 0,
  }

  if (parentLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Estatisticas */}
      <div className="grid gap-4 sm:grid-cols-5">
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <FileText className="h-8 w-8 text-gray-500" />
              <div>
                <p className="text-2xl font-bold">{stats.total}</p>
                <p className="text-sm text-gray-500">Total</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <Info className="h-8 w-8 text-gray-500" />
              <div>
                <p className="text-2xl font-bold">{stats.sistema}</p>
                <p className="text-sm text-gray-500">Sistema</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <AlertTriangle className="h-8 w-8 text-red-500" />
              <div>
                <p className="text-2xl font-bold">{stats.seguranca}</p>
                <p className="text-sm text-gray-500">Segurança</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <CheckCircle className="h-8 w-8 text-blue-500" />
              <div>
                <p className="text-2xl font-bold">{stats.notificacao}</p>
                <p className="text-sm text-gray-500">Notificação</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <CheckCircle className="h-8 w-8 text-green-500" />
              <div>
                <p className="text-2xl font-bold">{stats.eleicao}</p>
                <p className="text-sm text-gray-500">Eleição</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Filtros */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Filter className="h-5 w-5" />
            Filtros
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div className="lg:col-span-2">
              <Label className="sr-only">Buscar</Label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  placeholder="Buscar por chave, usuario ou valor..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div>
              <Label className="sr-only">Data Início</Label>
              <div className="relative">
                <Calendar className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  type="date"
                  placeholder="Data inicio"
                  value={dataInicio}
                  onChange={(e) => setDataInicio(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div>
              <Label className="sr-only">Data Fim</Label>
              <div className="relative">
                <Calendar className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  type="date"
                  placeholder="Data fim"
                  value={dataFim}
                  onChange={(e) => setDataFim(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Lista de Logs */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <div>
              <CardTitle>Histórico de Alterações</CardTitle>
              <CardDescription>
                {filteredLogs?.length || 0} registros encontrados
              </CardDescription>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" size="sm" onClick={() => refetch()}>
                <RefreshCw className="mr-2 h-4 w-4" />
                Atualizar
              </Button>
              <Button variant="outline" size="sm" onClick={handleExport}>
                <Download className="mr-2 h-4 w-4" />
                Exportar
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : filteredLogs && filteredLogs.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-3 px-4 font-medium">Data/Hora</th>
                    <th className="text-left py-3 px-4 font-medium">Usuario</th>
                    <th className="text-left py-3 px-4 font-medium">Configuração</th>
                    <th className="text-left py-3 px-4 font-medium">Valor Anterior</th>
                    <th className="text-left py-3 px-4 font-medium">Valor Novo</th>
                    <th className="text-right py-3 px-4 font-medium">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredLogs.map((log) => {
                    const { categoria, campo } = getChaveLabel(log.chave)
                    return (
                      <tr key={log.id} className="border-b hover:bg-gray-50">
                        <td className="py-3 px-4 text-sm">
                          <div className="flex items-center gap-2">
                            <Clock className="h-4 w-4 text-gray-400" />
                            {new Date(log.createdAt).toLocaleString('pt-BR')}
                          </div>
                        </td>
                        <td className="py-3 px-4">
                          <div className="flex items-center gap-2">
                            <User className="h-4 w-4 text-gray-400" />
                            {log.alteradoPorNome}
                          </div>
                        </td>
                        <td className="py-3 px-4">
                          <div className="flex items-center gap-2">
                            <span
                              className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getCategoriaColor(
                                log.chave
                              )}`}
                            >
                              {categoria}
                            </span>
                            <span className="text-sm font-mono">{campo}</span>
                          </div>
                        </td>
                        <td className="py-3 px-4">
                          <span className="inline-flex items-center px-2 py-0.5 rounded text-xs bg-red-50 text-red-700 font-mono">
                            {log.valorAnterior.length > 20
                              ? log.valorAnterior.substring(0, 20) + '...'
                              : log.valorAnterior}
                          </span>
                        </td>
                        <td className="py-3 px-4">
                          <span className="inline-flex items-center px-2 py-0.5 rounded text-xs bg-green-50 text-green-700 font-mono">
                            {log.valorNovo.length > 20
                              ? log.valorNovo.substring(0, 20) + '...'
                              : log.valorNovo}
                          </span>
                        </td>
                        <td className="py-3 px-4 text-right">
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => setSelectedLog(log)}
                          >
                            <Eye className="h-4 w-4" />
                          </Button>
                        </td>
                      </tr>
                    )
                  })}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">
              Nenhum log de alteração encontrado.
            </p>
          )}

          {/* Paginacao */}
          {filteredLogs && filteredLogs.length > 0 && (
            <div className="flex items-center justify-between mt-4 pt-4 border-t">
              <p className="text-sm text-gray-500">
                Mostrando {(page - 1) * pageSize + 1} -{' '}
                {Math.min(page * pageSize, logsData?.total || 0)} de {logsData?.total || 0}
              </p>
              <div className="flex gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPage((p) => Math.max(1, p - 1))}
                  disabled={page === 1}
                >
                  Anterior
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPage((p) => p + 1)}
                  disabled={page * pageSize >= (logsData?.total || 0)}
                >
                  Proximo
                </Button>
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Modal de Detalhes */}
      {selectedLog && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-lg m-4">
            <CardHeader>
              <CardTitle>Detalhes da Alteração</CardTitle>
              <CardDescription>
                {getChaveLabel(selectedLog.chave).categoria} -{' '}
                {getChaveLabel(selectedLog.chave).campo}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label className="text-gray-500">Data/Hora</Label>
                  <p className="font-medium">
                    {new Date(selectedLog.createdAt).toLocaleString('pt-BR')}
                  </p>
                </div>
                <div>
                  <Label className="text-gray-500">Usuario</Label>
                  <p className="font-medium">{selectedLog.alteradoPorNome}</p>
                </div>
                <div>
                  <Label className="text-gray-500">ID do Usuario</Label>
                  <p className="font-medium font-mono text-sm">{selectedLog.alteradoPorId}</p>
                </div>
                <div>
                  <Label className="text-gray-500">IP</Label>
                  <p className="font-medium font-mono">{selectedLog.ip || '-'}</p>
                </div>
              </div>

              <div>
                <Label className="text-gray-500">Chave da Configuração</Label>
                <p className="mt-1 text-sm bg-gray-50 p-3 rounded-lg font-mono">
                  {selectedLog.chave}
                </p>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label className="text-gray-500">Valor Anterior</Label>
                  <div className="mt-1 bg-red-50 p-3 rounded-lg">
                    <pre className="text-sm font-mono text-red-700 whitespace-pre-wrap break-all">
                      {selectedLog.valorAnterior}
                    </pre>
                  </div>
                </div>
                <div>
                  <Label className="text-gray-500">Valor Novo</Label>
                  <div className="mt-1 bg-green-50 p-3 rounded-lg">
                    <pre className="text-sm font-mono text-green-700 whitespace-pre-wrap break-all">
                      {selectedLog.valorNovo}
                    </pre>
                  </div>
                </div>
              </div>

              <div className="flex justify-end pt-4">
                <Button onClick={() => setSelectedLog(null)}>Fechar</Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
