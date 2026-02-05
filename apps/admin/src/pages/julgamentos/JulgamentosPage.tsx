import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  Gavel,
  Search,
  Eye,
  Calendar,
  CheckCircle,
  XCircle,
  Clock,
  Filter,
  AlertTriangle,
  AlertOctagon,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader } from '@/components/ui/card'

interface Julgamento {
  id: string
  protocolo: string
  tipo: 'denuncia' | 'impugnacao' | 'recurso'
  titulo: string
  status: 'aguardando' | 'em_julgamento' | 'julgado'
  decisao?: 'deferida' | 'indeferida' | 'arquivada'
  relatorNome: string
  dataDistribuicao: string
  dataJulgamento?: string
  eleicaoNome: string
}

export function JulgamentosPage() {
  const [search, setSearch] = useState('')
  const [filterStatus, setFilterStatus] = useState<string>('all')
  const [filterTipo, setFilterTipo] = useState<string>('all')

  // Mock dados - em producao viria da API
  const { data: julgamentos, isLoading } = useQuery({
    queryKey: ['julgamentos'],
    queryFn: async () => {
      return [
        {
          id: '1',
          protocolo: 'JUL-2024-00001',
          tipo: 'denuncia',
          titulo: 'Irregularidade na campanha eleitoral',
          status: 'julgado',
          decisao: 'deferida',
          relatorNome: 'Dr. Carlos Oliveira',
          dataDistribuicao: '2024-02-17T10:00:00',
          dataJulgamento: '2024-02-20T14:00:00',
          eleicaoNome: 'Eleicao CAU/SP 2024',
        },
        {
          id: '2',
          protocolo: 'JUL-2024-00002',
          tipo: 'impugnacao',
          titulo: 'Irregularidade na documentacao da chapa',
          status: 'em_julgamento',
          relatorNome: 'Dr. Pedro Oliveira',
          dataDistribuicao: '2024-02-18T09:30:00',
          eleicaoNome: 'Eleicao CAU/SP 2024',
        },
        {
          id: '3',
          protocolo: 'JUL-2024-00003',
          tipo: 'denuncia',
          titulo: 'Uso indevido de recursos publicos',
          status: 'aguardando',
          relatorNome: 'Dra. Maria Santos',
          dataDistribuicao: '2024-02-19T11:00:00',
          eleicaoNome: 'Eleicao CAU/SP 2024',
        },
        {
          id: '4',
          protocolo: 'JUL-2024-00004',
          tipo: 'recurso',
          titulo: 'Recurso contra indeferimento de registro',
          status: 'julgado',
          decisao: 'indeferida',
          relatorNome: 'Dr. Antonio Lima',
          dataDistribuicao: '2024-02-15T08:00:00',
          dataJulgamento: '2024-02-18T16:00:00',
          eleicaoNome: 'Eleicao CAU/SP 2024',
        },
        {
          id: '5',
          protocolo: 'JUL-2024-00005',
          tipo: 'impugnacao',
          titulo: 'Inelegibilidade de candidato',
          status: 'aguardando',
          relatorNome: 'Dr. Carlos Oliveira',
          dataDistribuicao: '2024-02-20T14:30:00',
          eleicaoNome: 'Eleicao CAU/SP 2024',
        },
      ] as Julgamento[]
    },
  })

  const getStatusBadge = (status: string, decisao?: string) => {
    if (status === 'julgado' && decisao) {
      const decisaoConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
        deferida: {
          label: 'Deferido',
          color: 'bg-green-100 text-green-800',
          icon: <CheckCircle className="h-3 w-3" />,
        },
        indeferida: {
          label: 'Indeferido',
          color: 'bg-red-100 text-red-800',
          icon: <XCircle className="h-3 w-3" />,
        },
        arquivada: {
          label: 'Arquivado',
          color: 'bg-gray-100 text-gray-800',
          icon: <Clock className="h-3 w-3" />,
        },
      }
      const config = decisaoConfig[decisao]
      return (
        <span
          className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}
        >
          {config.icon}
          {config.label}
        </span>
      )
    }

    const statusConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
      aguardando: {
        label: 'Aguardando',
        color: 'bg-yellow-100 text-yellow-800',
        icon: <Clock className="h-3 w-3" />,
      },
      em_julgamento: {
        label: 'Em Julgamento',
        color: 'bg-blue-100 text-blue-800',
        icon: <Gavel className="h-3 w-3" />,
      },
    }
    const config = statusConfig[status] || statusConfig.aguardando
    return (
      <span
        className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}
      >
        {config.icon}
        {config.label}
      </span>
    )
  }

  const getTipoBadge = (tipo: string) => {
    const tipoConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
      denuncia: {
        label: 'Denuncia',
        color: 'bg-orange-100 text-orange-800',
        icon: <AlertTriangle className="h-3 w-3" />,
      },
      impugnacao: {
        label: 'Impugnacao',
        color: 'bg-red-100 text-red-800',
        icon: <AlertOctagon className="h-3 w-3" />,
      },
      recurso: {
        label: 'Recurso',
        color: 'bg-purple-100 text-purple-800',
        icon: <Gavel className="h-3 w-3" />,
      },
    }
    const config = tipoConfig[tipo]
    return (
      <span
        className={`inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium ${config.color}`}
      >
        {config.icon}
        {config.label}
      </span>
    )
  }

  const filteredJulgamentos = julgamentos?.filter((j) => {
    const matchesSearch =
      j.protocolo.toLowerCase().includes(search.toLowerCase()) ||
      j.titulo.toLowerCase().includes(search.toLowerCase()) ||
      j.relatorNome.toLowerCase().includes(search.toLowerCase())
    const matchesStatus = filterStatus === 'all' || j.status === filterStatus
    const matchesTipo = filterTipo === 'all' || j.tipo === filterTipo
    return matchesSearch && matchesStatus && matchesTipo
  })

  const stats = {
    aguardando: julgamentos?.filter((j) => j.status === 'aguardando').length || 0,
    emJulgamento: julgamentos?.filter((j) => j.status === 'em_julgamento').length || 0,
    julgados: julgamentos?.filter((j) => j.status === 'julgado').length || 0,
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Julgamentos</h1>
          <p className="text-gray-600">Gerencie os julgamentos eleitorais</p>
        </div>
        <Link to="/julgamentos/sessao">
          <Button>
            <Gavel className="mr-2 h-4 w-4" />
            Nova Sessao
          </Button>
        </Link>
      </div>

      {/* Estatisticas */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                <Clock className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{stats.aguardando}</p>
                <p className="text-sm text-gray-500">Aguardando</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                <Gavel className="h-6 w-6 text-blue-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{stats.emJulgamento}</p>
                <p className="text-sm text-gray-500">Em Julgamento</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <CheckCircle className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{stats.julgados}</p>
                <p className="text-sm text-gray-500">Julgados</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Filtros e Busca */}
      <Card>
        <CardHeader>
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar por protocolo, titulo ou relator..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
            <div className="flex gap-2">
              <select
                className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={filterStatus}
                onChange={(e) => setFilterStatus(e.target.value)}
              >
                <option value="all">Todos os Status</option>
                <option value="aguardando">Aguardando</option>
                <option value="em_julgamento">Em Julgamento</option>
                <option value="julgado">Julgados</option>
              </select>
              <select
                className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={filterTipo}
                onChange={(e) => setFilterTipo(e.target.value)}
              >
                <option value="all">Todos os Tipos</option>
                <option value="denuncia">Denuncias</option>
                <option value="impugnacao">Impugnacoes</option>
                <option value="recurso">Recursos</option>
              </select>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <p className="text-center py-8 text-gray-500">Carregando...</p>
          ) : filteredJulgamentos && filteredJulgamentos.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-3 px-4 font-medium">Protocolo</th>
                    <th className="text-left py-3 px-4 font-medium">Tipo</th>
                    <th className="text-left py-3 px-4 font-medium">Titulo</th>
                    <th className="text-left py-3 px-4 font-medium">Relator</th>
                    <th className="text-left py-3 px-4 font-medium">Status</th>
                    <th className="text-left py-3 px-4 font-medium">Data</th>
                    <th className="text-right py-3 px-4 font-medium">Acoes</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredJulgamentos.map((julgamento) => (
                    <tr key={julgamento.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4 font-medium">{julgamento.protocolo}</td>
                      <td className="py-3 px-4">{getTipoBadge(julgamento.tipo)}</td>
                      <td className="py-3 px-4">
                        <span className="line-clamp-1">{julgamento.titulo}</span>
                      </td>
                      <td className="py-3 px-4">{julgamento.relatorNome}</td>
                      <td className="py-3 px-4">
                        {getStatusBadge(julgamento.status, julgamento.decisao)}
                      </td>
                      <td className="py-3 px-4 text-sm text-gray-500">
                        {julgamento.dataJulgamento
                          ? new Date(julgamento.dataJulgamento).toLocaleDateString('pt-BR')
                          : new Date(julgamento.dataDistribuicao).toLocaleDateString('pt-BR')}
                      </td>
                      <td className="py-3 px-4 text-right">
                        <Link to={`/julgamentos/${julgamento.id}`}>
                          <Button variant="ghost" size="icon">
                            <Eye className="h-4 w-4" />
                          </Button>
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">Nenhum julgamento encontrado.</p>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
