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
import api from '@/services/api'

interface Julgamento {
  id: string
  protocolo: string
  tipo: 'denúncia' | 'impugnação' | 'recurso'
  titulo: string
  status: 'aguardando' | 'em_julgamento' | 'julgado'
  decisao?: 'deferida' | 'indeferida' | 'arquivada'
  relatorNome: string
  dataDistribuicao: string
  dataJulgamento?: string
  eleicaoNome: string
}

const mapTipo = (tipo: number): 'denuncia' | 'impugnacao' | 'recurso' => {
  // CAU.Eleitoral.Domain.Enums.TipoJulgamento
  switch (tipo) {
    case 0:
      return 'denuncia'
    case 1:
      return 'impugnacao'
    case 2:
    case 3:
    case 4:
    case 5:
      return 'recurso'
    default:
      return 'denuncia'
  }
}

const mapStatus = (status: number): 'aguardando' | 'em_julgamento' | 'julgado' => {
  // CAU.Eleitoral.Domain.Enums.StatusJulgamento
  switch (status) {
    case 0:
      return 'aguardando' // Agendado
    case 1:
      return 'em_julgamento' // EmAndamento
    case 4:
      return 'julgado' // Concluido
    default:
      return 'aguardando'
  }
}

const mapDecisao = (decisaoTexto?: string | null): 'deferida' | 'indeferida' | 'arquivada' | undefined => {
  if (!decisaoTexto) return undefined

  const upper = decisaoTexto.toUpperCase()
  if (upper.includes('ARQUIV')) return 'arquivada'
  if (upper.includes('IMPROCEDENTE')) return 'indeferida'
  if (upper.includes('PROCEDENTE')) return 'deferida'
  if (upper.includes('INDEFER')) return 'indeferida'
  if (upper.includes('DEFER')) return 'deferida'
  return undefined
}

export function JulgamentosPage() {
  const [search, setSearch] = useState('')
  const [filterStatus, setFilterStatus] = useState<string>('all')
  const [filterTipo, setFilterTipo] = useState<string>('all')

  const { data: julgamentos, isLoading } = useQuery({
    queryKey: ['julgamentos'],
    queryFn: async () => {
      try {
        const response = await api.get('/julgamento')
        const apiData = Array.isArray(response.data) ? response.data : response.data?.data || []
        return apiData.map((j: any) => ({
          id: j.id,
          protocolo: j.protocolo || `JUL-${String(j.id).substring(0, 8).toUpperCase()}`,
          tipo: mapTipo(j.tipo),
          titulo: j.ementa || j.decisao || 'Julgamento eleitoral',
          status: mapStatus(j.status),
          decisao: mapDecisao(j.decisao),
          relatorNome: j.relatorNome || 'Nao definido',
          dataDistribuicao: j.createdAt,
          dataJulgamento: j.dataFim,
          eleicaoNome: j.eleicaoNome || '',
        })) as Julgamento[]
      } catch {
        return [] as Julgamento[]
      }
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
      julgado: {
        label: 'Concluido',
        color: 'bg-green-100 text-green-800',
        icon: <CheckCircle className="h-3 w-3" />,
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
        label: 'Denúncia',
        color: 'bg-orange-100 text-orange-800',
        icon: <AlertTriangle className="h-3 w-3" />,
      },
      impugnacao: {
        label: 'Impugnação',
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
            Nova Sessão
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
                <option value="denuncia">Denúncias</option>
                <option value="impugnacao">Impugnações</option>
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
                    <th className="text-right py-3 px-4 font-medium">Ações</th>
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
