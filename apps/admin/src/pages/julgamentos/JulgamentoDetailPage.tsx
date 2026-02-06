import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  Gavel,
  User,
  Calendar,
  FileText,
  CheckCircle,
  XCircle,
  Clock,
  Download,
  Loader2,
  AlertTriangle,
  AlertOctagon,
  Users,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import api from '@/services/api'

interface Julgamento {
  id: string
  protocolo: string
  tipo: 'denuncia' | 'impugnacao' | 'recurso'
  titulo: string
  descricao: string
  status: 'aguardando' | 'em_julgamento' | 'julgado'
  decisao?: 'deferida' | 'indeferida' | 'arquivada'
  fundamentacao?: string
  penalidade?: string
  relatorNome: string
  relatorVoto?: string
  eleicaoNome: string
  dataDistribuicao: string
  dataJulgamento?: string
  sessaoId?: string
  votos: Array<{
    id: string
    membroNome: string
    voto: 'deferido' | 'indeferido' | 'abstencao'
    fundamentacao?: string
  }>
  documentos: Array<{
    id: string
    nome: string
    url: string
  }>
  historico: Array<{
    id: string
    data: string
    acao: string
    usuario: string
  }>
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
      return 'aguardando'
    case 1:
      return 'em_julgamento'
    case 4:
      return 'julgado'
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

const mapVoto = (voto: number): 'deferido' | 'indeferido' | 'abstencao' => {
  // CAU.Eleitoral.Domain.Enums.TipoVotoJulgamento
  switch (voto) {
    case 0:
      return 'deferido' // Procedente
    case 1:
      return 'indeferido' // Improcedente
    case 3:
      return 'abstencao'
    case 2:
      return 'deferido' // ParcialmenteProcedente
    case 4:
      return 'abstencao' // Impedido
    default:
      return 'abstencao'
  }
}

export function JulgamentoDetailPage() {
  const { id } = useParams<{ id: string }>()

  const { data: julgamento, isLoading } = useQuery({
    queryKey: ['julgamento', id],
    queryFn: async () => {
      try {
        const response = await api.get(`/julgamento/${id}`)
        const j = response.data
        return {
          id: j.id,
          protocolo: j.protocolo || `JUL-${String(j.id).substring(0, 8).toUpperCase()}`,
          tipo: mapTipo(j.tipo),
          titulo: j.ementa || 'Julgamento eleitoral',
          descricao: j.relatorio || j.ementa || 'Detalhes do julgamento',
          status: mapStatus(j.status),
          decisao: mapDecisao(j.decisao),
          fundamentacao: j.fundamentacao,
          penalidade: undefined,
          relatorNome: j.relatorNome || 'Nao definido',
          relatorVoto: undefined,
          eleicaoNome: j.eleicaoNome || '',
          dataDistribuicao: j.createdAt,
          dataJulgamento: j.dataFim,
          votos: (j.votos || []).map((v: any) => ({
            id: v.id,
            membroNome: v.membroNome,
            voto: mapVoto(v.voto),
            fundamentacao: v.fundamentacao,
          })),
          documentos: [],
          historico: [
            { id: '1', data: j.createdAt, acao: 'Julgamento criado', usuario: 'Sistema' },
            ...(j.dataInicio ? [{ id: '2', data: j.dataInicio, acao: 'Julgamento iniciado', usuario: 'Comissao Eleitoral' }] : []),
            ...(j.dataFim ? [{ id: '3', data: j.dataFim, acao: `Julgamento concluido${j.decisao ? ' - ' + j.decisao : ''}`, usuario: 'Comissao Eleitoral' }] : []),
          ],
        } as Julgamento
      } catch {
        return null
      }
    },
    enabled: !!id,
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

    const statusConfig: Record<string, { label: string; color: string }> = {
      aguardando: { label: 'Aguardando', color: 'bg-yellow-100 text-yellow-800' },
      em_julgamento: { label: 'Em Julgamento', color: 'bg-blue-100 text-blue-800' },
      julgado: { label: 'Concluido', color: 'bg-green-100 text-green-800' },
    }
    const config = statusConfig[status] || statusConfig.aguardando
    return (
      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
        {config.label}
      </span>
    )
  }

  const getTipoBadge = (tipo: string) => {
    const tipoConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
      denuncia: {
        label: 'Denuncia',
        color: 'bg-orange-100 text-orange-800',
        icon: <AlertTriangle className="h-4 w-4" />,
      },
      impugnacao: {
        label: 'Impugnacao',
        color: 'bg-red-100 text-red-800',
        icon: <AlertOctagon className="h-4 w-4" />,
      },
      recurso: {
        label: 'Recurso',
        color: 'bg-purple-100 text-purple-800',
        icon: <Gavel className="h-4 w-4" />,
      },
    }
    return tipoConfig[tipo]
  }

  const getVotoBadge = (voto: string) => {
    const votoConfig: Record<string, { label: string; color: string }> = {
      deferido: { label: 'Deferido', color: 'bg-green-100 text-green-800' },
      indeferido: { label: 'Indeferido', color: 'bg-red-100 text-red-800' },
      abstencao: { label: 'Abstencao', color: 'bg-gray-100 text-gray-800' },
    }
    const config = votoConfig[voto]
    return (
      <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${config.color}`}>
        {config.label}
      </span>
    )
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!julgamento) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Julgamento nao encontrado.</p>
      </div>
    )
  }

  const tipoInfo = getTipoBadge(julgamento.tipo)
  const votosDeferidos = julgamento.votos.filter((v) => v.voto === 'deferido').length
  const votosIndeferidos = julgamento.votos.filter((v) => v.voto === 'indeferido').length

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/julgamentos">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">{julgamento.protocolo}</h1>
              {getStatusBadge(julgamento.status, julgamento.decisao)}
            </div>
            <div className="flex items-center gap-2 mt-1">
              <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium ${tipoInfo.color}`}>
                {tipoInfo.icon}
                {tipoInfo.label}
              </span>
              <span className="text-gray-600">{julgamento.titulo}</span>
            </div>
          </div>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Informacoes Principais */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Gavel className="h-5 w-5" />
              Detalhes do Julgamento
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div>
              <h4 className="font-medium text-gray-700 mb-2">Descricao</h4>
              <p className="text-gray-600">{julgamento.descricao}</p>
            </div>

            {julgamento.fundamentacao && (
              <div className="rounded-lg bg-gray-50 p-4">
                <h4 className="font-medium text-gray-700 mb-2">Fundamentacao da Decisao</h4>
                <p className="text-gray-600 whitespace-pre-wrap">{julgamento.fundamentacao}</p>
              </div>
            )}

            {julgamento.penalidade && (
              <div>
                <h4 className="font-medium text-gray-700 mb-2">Penalidade Aplicada</h4>
                <p className="text-gray-900 font-medium">{julgamento.penalidade}</p>
              </div>
            )}

            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-gray-500">Relator</dt>
                <dd className="mt-1 text-sm text-gray-900">{julgamento.relatorNome}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Eleicao</dt>
                <dd className="mt-1 text-sm text-gray-900">{julgamento.eleicaoNome}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data de Distribuicao</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(julgamento.dataDistribuicao).toLocaleString('pt-BR')}
                </dd>
              </div>
              {julgamento.dataJulgamento && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Data do Julgamento</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {new Date(julgamento.dataJulgamento).toLocaleString('pt-BR')}
                  </dd>
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Resultado da Votacao */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Votacao
            </CardTitle>
            <CardDescription>
              {julgamento.votos.length > 0 ? `${votosDeferidos} x ${votosIndeferidos}` : 'Sem votos registrados'}
            </CardDescription>
          </CardHeader>
          <CardContent>
            {julgamento.votos.length > 0 ? (
              <div className="space-y-3">
                {julgamento.votos.map((voto) => (
                  <div key={voto.id} className="flex items-center justify-between py-2 border-b last:border-0">
                    <div>
                      <p className="text-sm font-medium">{voto.membroNome}</p>
                      {voto.fundamentacao && (
                        <p className="text-xs text-gray-500 mt-1">{voto.fundamentacao}</p>
                      )}
                    </div>
                    {getVotoBadge(voto.voto)}
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-gray-500">Nenhum voto registrado ainda.</p>
            )}
          </CardContent>
        </Card>

        {/* Documentos */}
        {julgamento.documentos.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5" />
                Documentos
              </CardTitle>
              <CardDescription>{julgamento.documentos.length} documentos</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                {julgamento.documentos.map((doc) => (
                  <div key={doc.id} className="flex items-center justify-between rounded-lg border p-3">
                    <div className="flex items-center gap-2">
                      <FileText className="h-4 w-4 text-gray-400" />
                      <span className="text-sm">{doc.nome}</span>
                    </div>
                    <Button variant="ghost" size="icon">
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Historico */}
        <Card className={julgamento.documentos.length > 0 ? 'lg:col-span-2' : 'lg:col-span-3'}>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5" />
              Historico
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="relative">
              <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
              <div className="space-y-6">
                {julgamento.historico.map((item) => (
                  <div key={item.id} className="relative flex gap-4 pl-10">
                    <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                    <div className="flex-1">
                      <div className="flex items-center justify-between">
                        <span className="font-medium">{item.acao}</span>
                        <span className="text-xs text-gray-500">
                          {new Date(item.data).toLocaleString('pt-BR')}
                        </span>
                      </div>
                      <p className="text-sm text-gray-600">Por: {item.usuario}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
