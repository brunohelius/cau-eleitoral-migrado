import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import {
  Shield,
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
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import api from '@/services/api'

interface LogAuditoria {
  id: string
  data: string
  usuario: string
  usuarioId: string
  acao: string
  entidade: string
  entidadeId?: string
  ip: string
  userAgent: string
  nivel: 'info' | 'warning' | 'error' | 'success'
  detalhes?: string
}

export function AuditoriaPage() {
  const [search, setSearch] = useState('')
  const [filterNivel, setFilterNivel] = useState<string>('all')
  const [filterEntidade, setFilterEntidade] = useState<string>('all')
  const [dataInicio, setDataInicio] = useState('')
  const [dataFim, setDataFim] = useState('')
  const [selectedLog, setSelectedLog] = useState<LogAuditoria | null>(null)

  const { data: logs, isLoading, refetch } = useQuery({
    queryKey: ['auditoria', filterNivel, filterEntidade, dataInicio, dataFim],
    queryFn: async () => {
      try {
        const params: Record<string, any> = { page: 1, pageSize: 100 }
        if (filterNivel === 'error') params.sucesso = false
        if (filterEntidade !== 'all') params.entidadeTipo = filterEntidade
        if (dataInicio) params.dataInicio = dataInicio
        if (dataFim) params.dataFim = dataFim

        const response = await api.get('/auditoria', { params })
        const pagedResult = response.data
        const items = pagedResult?.items || pagedResult || []
        return (Array.isArray(items) ? items : []).map((l: any) => {
          const nivel: LogAuditoria['nivel'] = !l.sucesso
            ? 'error'
            : l.acao?.toLowerCase().includes('login') ||
              l.acao?.toLowerCase().includes('consulta') ||
              l.acao?.toLowerCase().includes('exporta')
            ? 'info'
            : 'success'
          return {
            id: l.id,
            data: l.dataHora,
            usuario: l.usuarioNome || 'Sistema',
            usuarioId: l.usuarioId || 'system',
            acao: l.acao || '',
            entidade: (l.entidadeTipo || 'sistema').toLowerCase(),
            entidadeId: l.entidadeId,
            ip: l.ipAddress || '-',
            userAgent: l.userAgent || '-',
            nivel,
            detalhes: l.mensagem,
          }
        }) as LogAuditoria[]
      } catch {
        return [] as LogAuditoria[]
      }
    },
  })

  const getNivelBadge = (nivel: string) => {
    const nivelConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
      info: {
        label: 'Info',
        color: 'bg-blue-100 text-blue-800',
        icon: <Info className="h-3 w-3" />,
      },
      success: {
        label: 'Sucesso',
        color: 'bg-green-100 text-green-800',
        icon: <CheckCircle className="h-3 w-3" />,
      },
      warning: {
        label: 'Alerta',
        color: 'bg-yellow-100 text-yellow-800',
        icon: <AlertTriangle className="h-3 w-3" />,
      },
      error: {
        label: 'Erro',
        color: 'bg-red-100 text-red-800',
        icon: <XCircle className="h-3 w-3" />,
      },
    }
    const config = nivelConfig[nivel] || nivelConfig.info
    return (
      <span
        className={`inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium ${config.color}`}
      >
        {config.icon}
        {config.label}
      </span>
    )
  }

  const getEntidadeLabel = (entidade: string) => {
    const entidades: Record<string, string> = {
      auth: 'Autenticacao',
      eleicao: 'Eleicao',
      chapa: 'Chapa',
      usuario: 'Usuario',
      voto: 'Voto',
      denuncia: 'Denuncia',
      impugnacao: 'Impugnacao',
      relatorio: 'Relatorio',
      sistema: 'Sistema',
    }
    return entidades[entidade] || entidade
  }

  const filteredLogs = logs?.filter((log) => {
    const matchesSearch =
      log.acao.toLowerCase().includes(search.toLowerCase()) ||
      log.usuario.toLowerCase().includes(search.toLowerCase()) ||
      log.ip.includes(search)
    const matchesNivel = filterNivel === 'all' || log.nivel === filterNivel
    const matchesEntidade = filterEntidade === 'all' || log.entidade === filterEntidade
    return matchesSearch && matchesNivel && matchesEntidade
  })

  const handleExport = async () => {
    try {
      const response = await api.get('/auditoria/exportar', { responseType: 'blob' })
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', `auditoria_${new Date().toISOString().split('T')[0]}.csv`)
      document.body.appendChild(link)
      link.click()
      link.remove()
    } catch {
      console.log('Exportando logs...')
    }
  }

  const stats = {
    total: logs?.length || 0,
    info: logs?.filter((l) => l.nivel === 'info').length || 0,
    success: logs?.filter((l) => l.nivel === 'success').length || 0,
    warning: logs?.filter((l) => l.nivel === 'warning').length || 0,
    error: logs?.filter((l) => l.nivel === 'error').length || 0,
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Auditoria</h1>
          <p className="text-gray-600">Logs de atividades e acoes do sistema</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={() => refetch()}>
            <RefreshCw className="mr-2 h-4 w-4" />
            Atualizar
          </Button>
          <Button onClick={handleExport}>
            <Download className="mr-2 h-4 w-4" />
            Exportar
          </Button>
        </div>
      </div>

      {/* Estatisticas */}
      <div className="grid gap-4 sm:grid-cols-5">
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <Shield className="h-8 w-8 text-gray-500" />
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
              <Info className="h-8 w-8 text-blue-500" />
              <div>
                <p className="text-2xl font-bold">{stats.info}</p>
                <p className="text-sm text-gray-500">Info</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <CheckCircle className="h-8 w-8 text-green-500" />
              <div>
                <p className="text-2xl font-bold">{stats.success}</p>
                <p className="text-sm text-gray-500">Sucesso</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <AlertTriangle className="h-8 w-8 text-yellow-500" />
              <div>
                <p className="text-2xl font-bold">{stats.warning}</p>
                <p className="text-sm text-gray-500">Alertas</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <XCircle className="h-8 w-8 text-red-500" />
              <div>
                <p className="text-2xl font-bold">{stats.error}</p>
                <p className="text-sm text-gray-500">Erros</p>
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
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div className="lg:col-span-2">
              <Label className="sr-only">Buscar</Label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  placeholder="Buscar por acao, usuario ou IP..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <div>
              <Label className="sr-only">Nivel</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={filterNivel}
                onChange={(e) => setFilterNivel(e.target.value)}
              >
                <option value="all">Todos os Niveis</option>
                <option value="info">Info</option>
                <option value="success">Sucesso</option>
                <option value="warning">Alerta</option>
                <option value="error">Erro</option>
              </select>
            </div>
            <div>
              <Label className="sr-only">Entidade</Label>
              <select
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={filterEntidade}
                onChange={(e) => setFilterEntidade(e.target.value)}
              >
                <option value="all">Todas as Entidades</option>
                <option value="auth">Autenticacao</option>
                <option value="eleicao">Eleicao</option>
                <option value="chapa">Chapa</option>
                <option value="usuario">Usuario</option>
                <option value="voto">Voto</option>
                <option value="denuncia">Denuncia</option>
                <option value="relatorio">Relatorio</option>
              </select>
            </div>
            <div className="flex gap-2">
              <Input
                type="date"
                placeholder="Data inicio"
                value={dataInicio}
                onChange={(e) => setDataInicio(e.target.value)}
              />
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Lista de Logs */}
      <Card>
        <CardHeader>
          <CardTitle>Logs de Auditoria</CardTitle>
          <CardDescription>
            {filteredLogs?.length || 0} registros encontrados
          </CardDescription>
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
                    <th className="text-left py-3 px-4 font-medium">Acao</th>
                    <th className="text-left py-3 px-4 font-medium">Entidade</th>
                    <th className="text-left py-3 px-4 font-medium">Nivel</th>
                    <th className="text-left py-3 px-4 font-medium">IP</th>
                    <th className="text-right py-3 px-4 font-medium">Acoes</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredLogs.map((log) => (
                    <tr key={log.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4 text-sm">
                        <div className="flex items-center gap-2">
                          <Clock className="h-4 w-4 text-gray-400" />
                          {new Date(log.data).toLocaleString('pt-BR')}
                        </div>
                      </td>
                      <td className="py-3 px-4">
                        <div className="flex items-center gap-2">
                          <User className="h-4 w-4 text-gray-400" />
                          {log.usuario}
                        </div>
                      </td>
                      <td className="py-3 px-4">{log.acao}</td>
                      <td className="py-3 px-4">
                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                          {getEntidadeLabel(log.entidade)}
                        </span>
                      </td>
                      <td className="py-3 px-4">{getNivelBadge(log.nivel)}</td>
                      <td className="py-3 px-4 text-sm text-gray-500 font-mono">{log.ip}</td>
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
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">Nenhum log encontrado.</p>
          )}
        </CardContent>
      </Card>

      {/* Modal de Detalhes */}
      {selectedLog && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-lg">
            <CardHeader>
              <CardTitle>Detalhes do Log</CardTitle>
              <CardDescription>{selectedLog.acao}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label className="text-gray-500">Data/Hora</Label>
                  <p className="font-medium">{new Date(selectedLog.data).toLocaleString('pt-BR')}</p>
                </div>
                <div>
                  <Label className="text-gray-500">Nivel</Label>
                  <div className="mt-1">{getNivelBadge(selectedLog.nivel)}</div>
                </div>
                <div>
                  <Label className="text-gray-500">Usuario</Label>
                  <p className="font-medium">{selectedLog.usuario}</p>
                </div>
                <div>
                  <Label className="text-gray-500">ID do Usuario</Label>
                  <p className="font-medium font-mono text-sm">{selectedLog.usuarioId}</p>
                </div>
                <div>
                  <Label className="text-gray-500">Entidade</Label>
                  <p className="font-medium">{getEntidadeLabel(selectedLog.entidade)}</p>
                </div>
                <div>
                  <Label className="text-gray-500">ID da Entidade</Label>
                  <p className="font-medium font-mono text-sm">{selectedLog.entidadeId || '-'}</p>
                </div>
                <div>
                  <Label className="text-gray-500">IP</Label>
                  <p className="font-medium font-mono">{selectedLog.ip}</p>
                </div>
              </div>

              {selectedLog.detalhes && (
                <div>
                  <Label className="text-gray-500">Detalhes</Label>
                  <p className="mt-1 text-sm bg-gray-50 p-3 rounded-lg">{selectedLog.detalhes}</p>
                </div>
              )}

              <div>
                <Label className="text-gray-500">User Agent</Label>
                <p className="mt-1 text-xs bg-gray-50 p-3 rounded-lg font-mono break-all">
                  {selectedLog.userAgent}
                </p>
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
