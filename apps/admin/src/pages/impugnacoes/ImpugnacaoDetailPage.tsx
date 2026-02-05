import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  AlertOctagon,
  User,
  Calendar,
  FileText,
  CheckCircle,
  XCircle,
  Clock,
  Download,
  Loader2,
  Gavel,
  Users,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

interface Impugnacao {
  id: string
  protocolo: string
  motivo: string
  fundamentacao: string
  status: 'pendente' | 'em_analise' | 'deferida' | 'indeferida'
  impugnanteNome: string
  impugnanteCpf: string
  impugnanteEmail: string
  chapaId: string
  chapaNome: string
  chapaNumero: number
  eleicaoId: string
  eleicaoNome: string
  dataRegistro: string
  dataJulgamento?: string
  decisao?: string
  relatorNome?: string
  anexos: Array<{
    id: string
    nome: string
    url: string
  }>
  historico: Array<{
    id: string
    data: string
    acao: string
    usuario: string
    observacao?: string
  }>
}

export function ImpugnacaoDetailPage() {
  const { id } = useParams<{ id: string }>()

  // Mock dados - em producao viria da API
  const { data: impugnacao, isLoading } = useQuery({
    queryKey: ['impugnacao', id],
    queryFn: async () => {
      return {
        id: '1',
        protocolo: 'IMP-2024-00001',
        motivo: 'Irregularidade na documentacao da chapa',
        fundamentacao:
          'A Chapa Renovacao apresentou documentacao incompleta para registro. Falta a declaracao de quitacao eleitoral do candidato a vice-presidente, conforme exigido pelo artigo 15 do Regimento Eleitoral. Alem disso, o curriculo apresentado nao contem as informacoes obrigatorias previstas no edital de convocacao.',
        status: 'em_analise',
        impugnanteNome: 'Maria Souza Santos',
        impugnanteCpf: '987.654.321-00',
        impugnanteEmail: 'maria.souza@email.com',
        chapaId: '1',
        chapaNome: 'Chapa Renovacao',
        chapaNumero: 1,
        eleicaoId: '1',
        eleicaoNome: 'Eleicao CAU/SP 2024',
        dataRegistro: '2024-02-18T09:00:00',
        relatorNome: 'Dr. Pedro Oliveira',
        anexos: [
          { id: '1', nome: 'comprovante_irregularidade.pdf', url: '/anexos/comp.pdf' },
          { id: '2', nome: 'documentacao_incompleta.pdf', url: '/anexos/doc.pdf' },
        ],
        historico: [
          {
            id: '1',
            data: '2024-02-18T09:00:00',
            acao: 'Impugnacao registrada',
            usuario: 'Sistema',
          },
          {
            id: '2',
            data: '2024-02-18T10:30:00',
            acao: 'Distribuida ao relator',
            usuario: 'Comissao Eleitoral',
            observacao: 'Relator: Dr. Pedro Oliveira',
          },
          {
            id: '3',
            data: '2024-02-19T14:00:00',
            acao: 'Notificacao enviada a chapa impugnada',
            usuario: 'Sistema',
            observacao: 'Prazo de 3 dias para defesa',
          },
        ],
      } as Impugnacao
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

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!impugnacao) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Impugnacao nao encontrada.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/impugnacoes">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">{impugnacao.protocolo}</h1>
              {getStatusBadge(impugnacao.status)}
            </div>
            <p className="text-gray-600">{impugnacao.motivo}</p>
          </div>
        </div>
        <div className="flex gap-2">
          {impugnacao.status !== 'deferida' && impugnacao.status !== 'indeferida' && (
            <Link to={`/julgamentos/sessao?impugnacao=${id}`}>
              <Button>
                <Gavel className="mr-2 h-4 w-4" />
                Julgar Impugnacao
              </Button>
            </Link>
          )}
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Detalhes Principais */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <AlertOctagon className="h-5 w-5 text-red-500" />
              Fundamentacao
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-gray-700 whitespace-pre-wrap">{impugnacao.fundamentacao}</p>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-gray-500">Eleicao</dt>
                <dd className="mt-1 text-sm text-gray-900">{impugnacao.eleicaoNome}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Relator</dt>
                <dd className="mt-1 text-sm text-gray-900">{impugnacao.relatorNome || 'Nao designado'}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data do Registro</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(impugnacao.dataRegistro).toLocaleString('pt-BR')}
                </dd>
              </div>
              {impugnacao.dataJulgamento && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Data do Julgamento</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {new Date(impugnacao.dataJulgamento).toLocaleString('pt-BR')}
                  </dd>
                </div>
              )}
            </div>

            {impugnacao.decisao && (
              <div className="mt-6 rounded-lg bg-gray-50 p-4">
                <h4 className="font-medium text-gray-900">Decisao</h4>
                <p className="mt-2 text-sm text-gray-700">{impugnacao.decisao}</p>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Chapa Impugnada */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Chapa Impugnada
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="rounded-lg bg-gray-50 p-4">
              <div className="flex items-center gap-3">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold text-lg">
                  {impugnacao.chapaNumero}
                </div>
                <div>
                  <p className="font-medium">{impugnacao.chapaNome}</p>
                  <Link
                    to={`/chapas/${impugnacao.chapaId}`}
                    className="text-sm text-blue-600 hover:underline"
                  >
                    Ver detalhes da chapa
                  </Link>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Impugnante */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Impugnante
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            <div>
              <dt className="text-sm font-medium text-gray-500">Nome</dt>
              <dd className="mt-1 text-sm text-gray-900">{impugnacao.impugnanteNome}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">CPF</dt>
              <dd className="mt-1 text-sm text-gray-900">{impugnacao.impugnanteCpf}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Email</dt>
              <dd className="mt-1 text-sm text-gray-900">{impugnacao.impugnanteEmail}</dd>
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
            <CardDescription>{impugnacao.anexos.length} arquivos anexados</CardDescription>
          </CardHeader>
          <CardContent>
            {impugnacao.anexos.length > 0 ? (
              <div className="space-y-2">
                {impugnacao.anexos.map((anexo) => (
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
                {impugnacao.historico.map((item) => (
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
