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

export function JulgamentoDetailPage() {
  const { id } = useParams<{ id: string }>()

  // Mock dados - em producao viria da API
  const { data: julgamento, isLoading } = useQuery({
    queryKey: ['julgamento', id],
    queryFn: async () => {
      return {
        id: '1',
        protocolo: 'JUL-2024-00001',
        tipo: 'denuncia',
        titulo: 'Irregularidade na campanha eleitoral',
        descricao:
          'Denuncia de distribuicao de material de campanha fora do periodo permitido pelo calendario eleitoral.',
        status: 'julgado',
        decisao: 'deferida',
        fundamentacao:
          'Restou comprovado nos autos que a Chapa Renovacao distribuiu material de campanha no dia 15/02/2024, antes do inicio oficial do periodo de campanha estabelecido no calendario eleitoral. As provas apresentadas, incluindo fotografias e testemunhos, sao conclusivas. A conduta configura violacao ao artigo 23 do Regimento Eleitoral.',
        penalidade: 'Advertencia',
        relatorNome: 'Dr. Carlos Oliveira',
        relatorVoto: 'deferido',
        eleicaoNome: 'Eleicao CAU/SP 2024',
        dataDistribuicao: '2024-02-17T10:00:00',
        dataJulgamento: '2024-02-20T14:00:00',
        votos: [
          {
            id: '1',
            membroNome: 'Dr. Carlos Oliveira (Relator)',
            voto: 'deferido',
            fundamentacao: 'Acompanho o voto do relator.',
          },
          {
            id: '2',
            membroNome: 'Dra. Maria Santos',
            voto: 'deferido',
          },
          {
            id: '3',
            membroNome: 'Dr. Pedro Almeida',
            voto: 'deferido',
          },
          {
            id: '4',
            membroNome: 'Dra. Ana Costa',
            voto: 'indeferido',
            fundamentacao: 'Entendo que as provas nao sao conclusivas.',
          },
          {
            id: '5',
            membroNome: 'Dr. Jose Lima',
            voto: 'deferido',
          },
        ],
        documentos: [
          { id: '1', nome: 'Denuncia Original.pdf', url: '/docs/denuncia.pdf' },
          { id: '2', nome: 'Defesa da Chapa.pdf', url: '/docs/defesa.pdf' },
          { id: '3', nome: 'Voto do Relator.pdf', url: '/docs/voto.pdf' },
          { id: '4', nome: 'Acordao.pdf', url: '/docs/acordao.pdf' },
        ],
        historico: [
          {
            id: '1',
            data: '2024-02-16T10:30:00',
            acao: 'Denuncia registrada',
            usuario: 'Sistema',
          },
          {
            id: '2',
            data: '2024-02-17T10:00:00',
            acao: 'Julgamento distribuido ao relator',
            usuario: 'Comissao Eleitoral',
          },
          {
            id: '3',
            data: '2024-02-18T09:00:00',
            acao: 'Vista dos autos ao denunciado',
            usuario: 'Dr. Carlos Oliveira',
          },
          {
            id: '4',
            data: '2024-02-19T15:00:00',
            acao: 'Defesa apresentada',
            usuario: 'Chapa Renovacao',
          },
          {
            id: '5',
            data: '2024-02-20T14:00:00',
            acao: 'Julgamento realizado - Deferido',
            usuario: 'Comissao Eleitoral',
          },
        ],
      } as Julgamento
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
              {votosDeferidos} x {votosIndeferidos}
            </CardDescription>
          </CardHeader>
          <CardContent>
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
          </CardContent>
        </Card>

        {/* Documentos */}
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

        {/* Historico */}
        <Card className="lg:col-span-2">
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
