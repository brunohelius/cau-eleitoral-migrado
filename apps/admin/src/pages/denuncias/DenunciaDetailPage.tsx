import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  AlertTriangle,
  User,
  Calendar,
  FileText,
  MessageSquare,
  CheckCircle,
  XCircle,
  Clock,
  Download,
  Loader2,
  Gavel,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
// import api from '@/services/api'

interface Denuncia {
  id: string
  protocolo: string
  titulo: string
  descricao: string
  tipo: string
  status: 'pendente' | 'em_analise' | 'deferida' | 'indeferida' | 'arquivada'
  prioridade: 'baixa' | 'media' | 'alta' | 'urgente'
  denuncianteNome?: string
  denuncianteEmail?: string
  denunciadoNome: string
  denunciadoCpf?: string
  chapaId?: string
  chapaNome?: string
  eleicaoId: string
  eleicaoNome: string
  dataRegistro: string
  dataAtualizacao?: string
  anexos: Array<{
    id: string
    nome: string
    url: string
    tipo: string
  }>
  historico: Array<{
    id: string
    data: string
    acao: string
    usuario: string
    observacao?: string
  }>
}

export function DenunciaDetailPage() {
  const { id } = useParams<{ id: string }>()

  // Mock dados - em producao viria da API
  const { data: denuncia, isLoading } = useQuery({
    queryKey: ['denuncia', id],
    queryFn: async () => {
      // const response = await api.get<Denuncia>(`/denuncia/${id}`)
      // return response.data
      return {
        id: '1',
        protocolo: 'DEN-2024-00001',
        titulo: 'Irregularidade na campanha eleitoral',
        descricao:
          'Foi observada distribuicao de material de campanha fora do periodo permitido pelo calendario eleitoral. O material estava sendo distribuido na entrada do predio do CAU/SP no dia 15/02/2024, antes do inicio oficial do periodo de campanha.',
        tipo: 'propaganda_irregular',
        status: 'em_analise',
        prioridade: 'alta',
        denuncianteNome: 'Joao Carlos Silva',
        denuncianteEmail: 'joao.silva@email.com',
        denunciadoNome: 'Chapa Renovacao',
        denunciadoCpf: '',
        chapaId: '1',
        chapaNome: 'Chapa Renovacao',
        eleicaoId: '1',
        eleicaoNome: 'Eleicao CAU/SP 2024',
        dataRegistro: '2024-02-16T10:30:00',
        dataAtualizacao: '2024-02-17T14:00:00',
        anexos: [
          { id: '1', nome: 'foto_evidencia_1.jpg', url: '/anexos/foto1.jpg', tipo: 'image/jpeg' },
          { id: '2', nome: 'foto_evidencia_2.jpg', url: '/anexos/foto2.jpg', tipo: 'image/jpeg' },
          { id: '3', nome: 'video_evidencia.mp4', url: '/anexos/video.mp4', tipo: 'video/mp4' },
        ],
        historico: [
          {
            id: '1',
            data: '2024-02-16T10:30:00',
            acao: 'Denuncia registrada',
            usuario: 'Sistema',
            observacao: 'Protocolo gerado automaticamente',
          },
          {
            id: '2',
            data: '2024-02-16T11:00:00',
            acao: 'Denuncia recebida pela Comissao Eleitoral',
            usuario: 'Maria Santos',
          },
          {
            id: '3',
            data: '2024-02-17T14:00:00',
            acao: 'Status alterado para Em Analise',
            usuario: 'Carlos Oliveira',
            observacao: 'Iniciada analise preliminar das evidencias',
          },
        ],
      } as Denuncia
    },
    enabled: !!id,
  })

  const getStatusBadge = (status: string) => {
    const statusConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
      pendente: {
        label: 'Pendente',
        color: 'bg-yellow-100 text-yellow-800',
        icon: <Clock className="h-3 w-3" />,
      },
      em_analise: {
        label: 'Em Analise',
        color: 'bg-blue-100 text-blue-800',
        icon: <Clock className="h-3 w-3" />,
      },
      deferida: {
        label: 'Deferida',
        color: 'bg-green-100 text-green-800',
        icon: <CheckCircle className="h-3 w-3" />,
      },
      indeferida: {
        label: 'Indeferida',
        color: 'bg-red-100 text-red-800',
        icon: <XCircle className="h-3 w-3" />,
      },
      arquivada: {
        label: 'Arquivada',
        color: 'bg-gray-100 text-gray-800',
        icon: <FileText className="h-3 w-3" />,
      },
    }
    const config = statusConfig[status] || statusConfig.pendente
    return (
      <span
        className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}
      >
        {config.icon}
        {config.label}
      </span>
    )
  }

  const getPrioridadeBadge = (prioridade: string) => {
    const prioridadeConfig: Record<string, { label: string; color: string }> = {
      baixa: { label: 'Baixa', color: 'bg-gray-100 text-gray-800' },
      media: { label: 'Media', color: 'bg-blue-100 text-blue-800' },
      alta: { label: 'Alta', color: 'bg-orange-100 text-orange-800' },
      urgente: { label: 'Urgente', color: 'bg-red-100 text-red-800' },
    }
    const config = prioridadeConfig[prioridade] || prioridadeConfig.baixa
    return (
      <span
        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}
      >
        {config.label}
      </span>
    )
  }

  const getTipoLabel = (tipo: string) => {
    const tipos: Record<string, string> = {
      propaganda_irregular: 'Propaganda Irregular',
      abuso_poder: 'Abuso de Poder',
      fraude: 'Fraude',
      coacao: 'Coacao',
      compra_votos: 'Compra de Votos',
      outros: 'Outros',
    }
    return tipos[tipo] || tipo
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!denuncia) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Denuncia nao encontrada.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/denuncias">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">{denuncia.protocolo}</h1>
              {getStatusBadge(denuncia.status)}
              {getPrioridadeBadge(denuncia.prioridade)}
            </div>
            <p className="text-gray-600">{denuncia.titulo}</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Link to={`/denuncias/${id}/julgamento`}>
            <Button>
              <Gavel className="mr-2 h-4 w-4" />
              Julgar Denuncia
            </Button>
          </Link>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Detalhes Principais */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <AlertTriangle className="h-5 w-5 text-orange-500" />
              Descricao da Denuncia
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-gray-700 whitespace-pre-wrap">{denuncia.descricao}</p>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-gray-500">Tipo de Denuncia</dt>
                <dd className="mt-1 text-sm text-gray-900">{getTipoLabel(denuncia.tipo)}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Eleicao</dt>
                <dd className="mt-1 text-sm text-gray-900">{denuncia.eleicaoNome}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data do Registro</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(denuncia.dataRegistro).toLocaleString('pt-BR')}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Ultima Atualizacao</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {denuncia.dataAtualizacao
                    ? new Date(denuncia.dataAtualizacao).toLocaleString('pt-BR')
                    : '-'}
                </dd>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Partes Envolvidas */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Partes Envolvidas
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="rounded-lg bg-gray-50 p-4">
              <h4 className="text-sm font-medium text-gray-500">Denunciante</h4>
              <p className="mt-1 font-medium">{denuncia.denuncianteNome || 'Anonimo'}</p>
              {denuncia.denuncianteEmail && (
                <p className="text-sm text-gray-600">{denuncia.denuncianteEmail}</p>
              )}
            </div>
            <div className="rounded-lg bg-gray-50 p-4">
              <h4 className="text-sm font-medium text-gray-500">Denunciado</h4>
              <p className="mt-1 font-medium">{denuncia.denunciadoNome}</p>
              {denuncia.chapaNome && (
                <p className="text-sm text-gray-600">Chapa: {denuncia.chapaNome}</p>
              )}
              {denuncia.denunciadoCpf && (
                <p className="text-sm text-gray-600">CPF: {denuncia.denunciadoCpf}</p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* Anexos */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5" />
              Anexos
            </CardTitle>
            <CardDescription>{denuncia.anexos.length} arquivos anexados</CardDescription>
          </CardHeader>
          <CardContent>
            {denuncia.anexos.length > 0 ? (
              <div className="space-y-2">
                {denuncia.anexos.map((anexo) => (
                  <div
                    key={anexo.id}
                    className="flex items-center justify-between rounded-lg border p-3"
                  >
                    <div className="flex items-center gap-2">
                      <FileText className="h-4 w-4 text-gray-400" />
                      <span className="text-sm">{anexo.nome}</span>
                    </div>
                    <Button variant="ghost" size="icon">
                      <Download className="h-4 w-4" />
                    </Button>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-sm text-gray-500">Nenhum anexo</p>
            )}
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
                {denuncia.historico.map((item) => (
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
                      {item.observacao && (
                        <p className="mt-1 text-sm text-gray-500 italic">{item.observacao}</p>
                      )}
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
