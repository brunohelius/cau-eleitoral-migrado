import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
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
  Play,
  MessageSquare,
  FileCheck,
  Send,
  Archive,
  RotateCcw,
  ChevronRight,
  UserPlus,
  AlertTriangle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Textarea } from '@/components/ui/textarea'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useToast } from '@/hooks/use-toast'
import {
  impugnacoesService,
  StatusImpugnacao,
  TipoImpugnacao,
  FaseImpugnacao,
  Impugnacao,
} from '@/services/impugnacoes'

const statusLabels: Record<number, { label: string; color: string; icon: React.ReactNode }> = {
  [StatusImpugnacao.PENDENTE]: {
    label: 'Pendente',
    color: 'bg-yellow-100 text-yellow-800',
    icon: <Clock className="h-3 w-3" />,
  },
  [StatusImpugnacao.EM_ANALISE]: {
    label: 'Em Análise',
    color: 'bg-blue-100 text-blue-800',
    icon: <Clock className="h-3 w-3" />,
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

const faseLabels: Record<number, { label: string; icon: React.ReactNode }> = {
  [FaseImpugnacao.REGISTRO]: { label: 'Registro', icon: <FileText className="h-4 w-4" /> },
  [FaseImpugnacao.ANALISE_INICIAL]: { label: 'Análise Inicial', icon: <Play className="h-4 w-4" /> },
  [FaseImpugnacao.DEFESA]: { label: 'Defesa', icon: <MessageSquare className="h-4 w-4" /> },
  [FaseImpugnacao.PARECER]: { label: 'Parecer', icon: <FileCheck className="h-4 w-4" /> },
  [FaseImpugnacao.JULGAMENTO]: { label: 'Julgamento', icon: <Gavel className="h-4 w-4" /> },
  [FaseImpugnacao.RECURSO]: { label: 'Recurso', icon: <RotateCcw className="h-4 w-4" /> },
  [FaseImpugnacao.ENCERRADA]: { label: 'Encerrada', icon: <CheckCircle className="h-4 w-4" /> },
}

export function ImpugnacaoDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Dialog states
  const [showIniciarAnaliseDialog, setShowIniciarAnaliseDialog] = useState(false)
  const [showSolicitarDefesaDialog, setShowSolicitarDefesaDialog] = useState(false)
  const [showEmitirParecerDialog, setShowEmitirParecerDialog] = useState(false)
  const [showDecisaoDialog, setShowDecisaoDialog] = useState(false)
  const [showArquivarDialog, setShowArquivarDialog] = useState(false)
  const [showAtribuirRelatorDialog, setShowAtribuirRelatorDialog] = useState(false)

  // Form states
  const [prazoDefesa, setPrazoDefesa] = useState('')
  const [parecer, setParecer] = useState('')
  const [recomendacaoParecer, setRecomendacaoParecer] = useState<StatusImpugnacao>(StatusImpugnacao.DEFERIDA)
  const [decisao, setDecisao] = useState<StatusImpugnacao>(StatusImpugnacao.DEFERIDA)
  const [fundamentacaoDecisao, setFundamentacaoDecisao] = useState('')
  const [motivoArquivamento, setMotivoArquivamento] = useState('')
  const [relatorId, setRelatorId] = useState('')

  // Fetch impugnacao
  const { data: impugnacao, isLoading } = useQuery({
    queryKey: ['impugnacao', id],
    queryFn: () => impugnacoesService.getById(id!),
    enabled: !!id,
  })

  // Fetch timeline
  const { data: timeline } = useQuery({
    queryKey: ['impugnacao-timeline', id],
    queryFn: () => impugnacoesService.getTimeline(id!),
    enabled: !!id,
  })

  // Mutations
  const iniciarAnaliseMutation = useMutation({
    mutationFn: () => impugnacoesService.iniciarAnalise(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Análise iniciada com sucesso!' })
      setShowIniciarAnaliseDialog(false)
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao iniciar análise' })
    },
  })

  const solicitarDefesaMutation = useMutation({
    mutationFn: () => impugnacoesService.solicitarDefesa(id!, prazoDefesa),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Defesa solicitada com sucesso!' })
      setShowSolicitarDefesaDialog(false)
      setPrazoDefesa('')
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao solicitar defesa' })
    },
  })

  const emitirParecerMutation = useMutation({
    mutationFn: () => impugnacoesService.emitirParecer(id!, { parecer, recomendacao: recomendacaoParecer }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Parecer emitido com sucesso!' })
      setShowEmitirParecerDialog(false)
      setParecer('')
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao emitir parecer' })
    },
  })

  const proferirDecisaoMutation = useMutation({
    mutationFn: () => impugnacoesService.proferirDecisao(id!, { decisao, fundamentacao: fundamentacaoDecisao }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Decisao proferida com sucesso!' })
      setShowDecisaoDialog(false)
      setFundamentacaoDecisao('')
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao proferir decisao' })
    },
  })

  const arquivarMutation = useMutation({
    mutationFn: () => impugnacoesService.arquivar(id!, motivoArquivamento),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Impugnação arquivada com sucesso!' })
      setShowArquivarDialog(false)
      setMotivoArquivamento('')
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao arquivar impugnação' })
    },
  })

  const atribuirRelatorMutation = useMutation({
    mutationFn: () => impugnacoesService.atribuirRelator(id!, relatorId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Relator atribuido com sucesso!' })
      setShowAtribuirRelatorDialog(false)
      setRelatorId('')
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao atribuir relator' })
    },
  })

  const encaminharJulgamentoMutation = useMutation({
    mutationFn: () => impugnacoesService.encaminharJulgamento(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['impugnacao', id] })
      queryClient.invalidateQueries({ queryKey: ['impugnacao-timeline', id] })
      toast({ title: 'Impugnação encaminhada para julgamento!' })
    },
    onError: () => {
      toast({ variant: 'destructive', title: 'Erro ao encaminhar para julgamento' })
    },
  })

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

  const canIniciarAnalise = impugnacao?.fase === FaseImpugnacao.REGISTRO
  const canSolicitarDefesa = impugnacao?.fase === FaseImpugnacao.ANALISE_INICIAL
  const canEmitirParecer = impugnacao?.fase === FaseImpugnacao.DEFESA
  const canEncaminharJulgamento = impugnacao?.fase === FaseImpugnacao.PARECER
  const canProferirDecisao = impugnacao?.fase === FaseImpugnacao.JULGAMENTO
  const canArquivar = impugnacao && impugnacao.fase !== FaseImpugnacao.ENCERRADA && impugnacao.status !== StatusImpugnacao.ARQUIVADA
  const isEncerrada = impugnacao?.fase === FaseImpugnacao.ENCERRADA

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
        <p className="text-gray-500">Impugnação não encontrada.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
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
              <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                {tipoLabels[impugnacao.tipo]}
              </span>
            </div>
            <p className="text-gray-600">
              {impugnacao.eleicaoNome} - Fase: {faseLabels[impugnacao.fase]?.label}
            </p>
          </div>
        </div>
        <div className="flex gap-2">
          {!impugnacao.relatorId && !isEncerrada && (
            <Button variant="outline" onClick={() => setShowAtribuirRelatorDialog(true)}>
              <UserPlus className="mr-2 h-4 w-4" />
              Atribuir Relator
            </Button>
          )}
          {canArquivar && (
            <Button variant="outline" onClick={() => setShowArquivarDialog(true)}>
              <Archive className="mr-2 h-4 w-4" />
              Arquivar
            </Button>
          )}
        </div>
      </div>

      {/* Workflow Actions Bar */}
      {!isEncerrada && (
        <Card className="border-blue-200 bg-blue-50">
          <CardContent className="pt-6">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-2">
                  {faseLabels[impugnacao.fase]?.icon}
                  <span className="font-medium text-blue-900">
                    Fase Atual: {faseLabels[impugnacao.fase]?.label}
                  </span>
                </div>
                <ChevronRight className="h-4 w-4 text-blue-400" />
                <span className="text-sm text-blue-700">
                  {canIniciarAnalise && 'Aguardando inicio da analise'}
                  {canSolicitarDefesa && 'Aguardando solicitacao de defesa'}
                  {canEmitirParecer && 'Aguardando parecer'}
                  {canEncaminharJulgamento && 'Aguardando encaminhamento para julgamento'}
                  {canProferirDecisao && 'Aguardando decisão final'}
                </span>
              </div>
              <div className="flex gap-2">
                {canIniciarAnalise && (
                  <Button onClick={() => setShowIniciarAnaliseDialog(true)}>
                    <Play className="mr-2 h-4 w-4" />
                    Iniciar Análise
                  </Button>
                )}
                {canSolicitarDefesa && (
                  <Button onClick={() => setShowSolicitarDefesaDialog(true)}>
                    <MessageSquare className="mr-2 h-4 w-4" />
                    Solicitar Defesa
                  </Button>
                )}
                {canEmitirParecer && (
                  <Button onClick={() => setShowEmitirParecerDialog(true)}>
                    <FileCheck className="mr-2 h-4 w-4" />
                    Emitir Parecer
                  </Button>
                )}
                {canEncaminharJulgamento && (
                  <Button
                    onClick={() => encaminharJulgamentoMutation.mutate()}
                    disabled={encaminharJulgamentoMutation.isPending}
                  >
                    {encaminharJulgamentoMutation.isPending ? (
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    ) : (
                      <Send className="mr-2 h-4 w-4" />
                    )}
                    Encaminhar Julgamento
                  </Button>
                )}
                {canProferirDecisao && (
                  <Button onClick={() => setShowDecisaoDialog(true)}>
                    <Gavel className="mr-2 h-4 w-4" />
                    Proferir Decisao
                  </Button>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Progress Steps */}
      <Card>
        <CardHeader>
          <CardTitle>Andamento do Processo</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-between">
            {Object.entries(faseLabels).map(([fase, config], index) => {
              const faseNum = Number(fase)
              const isActive = impugnacao.fase === faseNum
              const isCompleted = impugnacao.fase > faseNum
              const isLast = index === Object.keys(faseLabels).length - 1

              return (
                <div key={fase} className="flex items-center flex-1">
                  <div className="flex flex-col items-center">
                    <div
                      className={`flex h-10 w-10 items-center justify-center rounded-full ${
                        isCompleted
                          ? 'bg-green-500 text-white'
                          : isActive
                          ? 'bg-blue-500 text-white'
                          : 'bg-gray-200 text-gray-500'
                      }`}
                    >
                      {isCompleted ? <CheckCircle className="h-5 w-5" /> : config.icon}
                    </div>
                    <span
                      className={`mt-2 text-xs ${
                        isActive ? 'font-medium text-blue-600' : 'text-gray-500'
                      }`}
                    >
                      {config.label}
                    </span>
                  </div>
                  {!isLast && (
                    <div
                      className={`flex-1 h-0.5 mx-2 ${
                        isCompleted ? 'bg-green-500' : 'bg-gray-200'
                      }`}
                    />
                  )}
                </div>
              )
            })}
          </div>
        </CardContent>
      </Card>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Fundamentacao */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <AlertOctagon className="h-5 w-5 text-red-500" />
              Fundamentacao
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-gray-700 whitespace-pre-wrap">{impugnacao.fundamentacao}</p>

            {impugnacao.normasVioladas && (
              <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                <h4 className="font-medium text-gray-900 mb-2">Normas Violadas</h4>
                <p className="text-sm text-gray-700">{impugnacao.normasVioladas}</p>
              </div>
            )}

            <div className="mt-4 p-4 bg-blue-50 rounded-lg">
              <h4 className="font-medium text-blue-900 mb-2">Pedido</h4>
              <p className="text-sm text-blue-800">{impugnacao.pedido}</p>
            </div>

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-gray-500">Eleição</dt>
                <dd className="mt-1 text-sm text-gray-900">{impugnacao.eleicaoNome}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Relator</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {impugnacao.relatorNome || 'Nao designado'}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data do Registro</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(impugnacao.createdAt).toLocaleString('pt-BR')}
                </dd>
              </div>
              {impugnacao.prazoDefesa && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Prazo para Defesa</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {new Date(impugnacao.prazoDefesa).toLocaleDateString('pt-BR')}
                  </dd>
                </div>
              )}
            </div>

            {impugnacao.decisao && (
              <div className="mt-6 rounded-lg bg-gray-50 p-4">
                <h4 className="font-medium text-gray-900">Decisao</h4>
                <div className="mt-2 flex items-center gap-2">
                  {getStatusBadge(impugnacao.status)}
                  {impugnacao.dataDecisao && (
                    <span className="text-sm text-gray-500">
                      em {new Date(impugnacao.dataDecisao).toLocaleDateString('pt-BR')}
                    </span>
                  )}
                </div>
                {impugnacao.fundamentacaoDecisao && (
                  <p className="mt-2 text-sm text-gray-700">{impugnacao.fundamentacaoDecisao}</p>
                )}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Impugnante e Impugnado */}
        <div className="space-y-6">
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
            </CardContent>
          </Card>

          {impugnacao.chapaNome && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5" />
                  Chapa Impugnada
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="rounded-lg bg-gray-50 p-4">
                  <p className="font-medium">{impugnacao.chapaNome}</p>
                  {impugnacao.chapaId && (
                    <Link
                      to={`/chapas/${impugnacao.chapaId}`}
                      className="text-sm text-blue-600 hover:underline"
                    >
                      Ver detalhes da chapa
                    </Link>
                  )}
                </div>
              </CardContent>
            </Card>
          )}

          {impugnacao.candidatoNome && (
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Candidato Impugnado
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="rounded-lg bg-gray-50 p-4">
                  <p className="font-medium">{impugnacao.candidatoNome}</p>
                </div>
              </CardContent>
            </Card>
          )}
        </div>

        {/* Defesas */}
        {impugnacao.defesas && impugnacao.defesas.length > 0 && (
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <MessageSquare className="h-5 w-5" />
                Defesas Apresentadas
              </CardTitle>
              <CardDescription>{impugnacao.defesas.length} defesa(s)</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {impugnacao.defesas.map((defesa) => (
                  <div key={defesa.id} className="rounded-lg border p-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-medium">{defesa.defensorNome}</span>
                      <span className="text-xs text-gray-500">
                        {new Date(defesa.dataApresentacao).toLocaleString('pt-BR')}
                      </span>
                    </div>
                    <p className="text-sm text-gray-700 whitespace-pre-wrap">{defesa.texto}</p>
                    {defesa.anexos && defesa.anexos.length > 0 && (
                      <div className="mt-2 flex flex-wrap gap-2">
                        {defesa.anexos.map((anexo) => (
                          <Button key={anexo.id} variant="outline" size="sm">
                            <Download className="mr-1 h-3 w-3" />
                            {anexo.nome}
                          </Button>
                        ))}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Pareceres */}
        {impugnacao.pareceres && impugnacao.pareceres.length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileCheck className="h-5 w-5" />
                Pareceres
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {impugnacao.pareceres.map((parecerItem) => (
                  <div key={parecerItem.id} className="rounded-lg border p-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-medium">{parecerItem.pareceristaNome}</span>
                      <span className="text-xs text-gray-500">
                        {new Date(parecerItem.dataEmissao).toLocaleString('pt-BR')}
                      </span>
                    </div>
                    <p className="text-sm text-gray-700 whitespace-pre-wrap">{parecerItem.parecer}</p>
                    <div className="mt-2">
                      <span className="text-sm font-medium">Recomendacao: </span>
                      {getStatusBadge(parecerItem.recomendacao)}
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Anexos */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5" />
              Anexos
            </CardTitle>
            <CardDescription>
              {impugnacao.anexos?.length || 0} arquivos anexados
            </CardDescription>
          </CardHeader>
          <CardContent>
            {impugnacao.anexos && impugnacao.anexos.length > 0 ? (
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

        {/* Recursos */}
        {impugnacao.recursos && impugnacao.recursos.length > 0 && (
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <RotateCcw className="h-5 w-5" />
                Recursos
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {impugnacao.recursos.map((recurso) => (
                  <div key={recurso.id} className="rounded-lg border p-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-medium">{recurso.recorrenteNome}</span>
                      <div className="flex items-center gap-2">
                        {getStatusBadge(recurso.status)}
                        <span className="text-xs text-gray-500">
                          {new Date(recurso.dataInterposicao).toLocaleString('pt-BR')}
                        </span>
                      </div>
                    </div>
                    <p className="text-sm text-gray-700 whitespace-pre-wrap">{recurso.fundamentacao}</p>
                    {recurso.decisaoRecurso && (
                      <div className="mt-3 p-3 bg-gray-50 rounded">
                        <h5 className="text-sm font-medium">Decisao do Recurso</h5>
                        <p className="text-sm text-gray-700 mt-1">{recurso.decisaoRecurso}</p>
                        {recurso.dataDecisao && (
                          <span className="text-xs text-gray-500">
                            em {new Date(recurso.dataDecisao).toLocaleDateString('pt-BR')}
                          </span>
                        )}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Timeline */}
        <Card className="lg:col-span-3">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5" />
              Historico
            </CardTitle>
          </CardHeader>
          <CardContent>
            {timeline && timeline.length > 0 ? (
              <div className="relative">
                <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
                <div className="space-y-6">
                  {timeline.map((item, index) => (
                    <div key={index} className="relative flex gap-4 pl-10">
                      <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                      <div className="flex-1">
                        <div className="flex items-center justify-between">
                          <span className="font-medium">{item.evento}</span>
                          <span className="text-xs text-gray-500">
                            {new Date(item.data).toLocaleString('pt-BR')}
                          </span>
                        </div>
                        <p className="text-sm text-gray-600">{item.descricao}</p>
                        {item.usuarioNome && (
                          <p className="text-xs text-gray-500">Por: {item.usuarioNome}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            ) : (
              <p className="text-sm text-gray-500">Nenhum registro no histórico.</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Dialog: Iniciar Analise */}
      <Dialog open={showIniciarAnaliseDialog} onOpenChange={setShowIniciarAnaliseDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Iniciar Análise</DialogTitle>
            <DialogDescription>
              Confirma o início da análise desta impugnação? A fase será alterada para "Análise Inicial".
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowIniciarAnaliseDialog(false)}>
              Cancelar
            </Button>
            <Button
              onClick={() => iniciarAnaliseMutation.mutate()}
              disabled={iniciarAnaliseMutation.isPending}
            >
              {iniciarAnaliseMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Confirmar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog: Solicitar Defesa */}
      <Dialog open={showSolicitarDefesaDialog} onOpenChange={setShowSolicitarDefesaDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Solicitar Defesa</DialogTitle>
            <DialogDescription>
              Defina o prazo para apresentação de defesa pelo impugnado.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label htmlFor="prazoDefesa">Prazo para Defesa</Label>
              <Input
                id="prazoDefesa"
                type="date"
                value={prazoDefesa}
                onChange={(e) => setPrazoDefesa(e.target.value)}
                min={new Date().toISOString().split('T')[0]}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowSolicitarDefesaDialog(false)}>
              Cancelar
            </Button>
            <Button
              onClick={() => solicitarDefesaMutation.mutate()}
              disabled={solicitarDefesaMutation.isPending || !prazoDefesa}
            >
              {solicitarDefesaMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Solicitar Defesa
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog: Emitir Parecer */}
      <Dialog open={showEmitirParecerDialog} onOpenChange={setShowEmitirParecerDialog}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Emitir Parecer</DialogTitle>
            <DialogDescription>
              Emita seu parecer sobre a impugnação e indique sua recomendacao.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label htmlFor="parecer">Parecer</Label>
              <Textarea
                id="parecer"
                placeholder="Descreva sua analise e fundamentacao..."
                value={parecer}
                onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setParecer(e.target.value)}
                rows={6}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="recomendacao">Recomendacao</Label>
              <Select
                value={String(recomendacaoParecer)}
                onValueChange={(value) => setRecomendacaoParecer(Number(value))}
              >
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value={String(StatusImpugnacao.DEFERIDA)}>Deferir</SelectItem>
                  <SelectItem value={String(StatusImpugnacao.INDEFERIDA)}>Indeferir</SelectItem>
                  <SelectItem value={String(StatusImpugnacao.PARCIALMENTE_DEFERIDA)}>
                    Deferir Parcialmente
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowEmitirParecerDialog(false)}>
              Cancelar
            </Button>
            <Button
              onClick={() => emitirParecerMutation.mutate()}
              disabled={emitirParecerMutation.isPending || !parecer}
            >
              {emitirParecerMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Emitir Parecer
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog: Proferir Decisao */}
      <Dialog open={showDecisaoDialog} onOpenChange={setShowDecisaoDialog}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Proferir Decisao</DialogTitle>
            <DialogDescription>
              Profira a decisão final sobre esta impugnacao.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label htmlFor="decisao">Decisao</Label>
              <Select
                value={String(decisao)}
                onValueChange={(value) => setDecisao(Number(value))}
              >
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value={String(StatusImpugnacao.DEFERIDA)}>Deferir</SelectItem>
                  <SelectItem value={String(StatusImpugnacao.INDEFERIDA)}>Indeferir</SelectItem>
                  <SelectItem value={String(StatusImpugnacao.PARCIALMENTE_DEFERIDA)}>
                    Deferir Parcialmente
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="fundamentacaoDecisao">Fundamentação da Decisao</Label>
              <Textarea
                id="fundamentacaoDecisao"
                placeholder="Fundamente a decisao..."
                value={fundamentacaoDecisao}
                onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setFundamentacaoDecisao(e.target.value)}
                rows={6}
              />
            </div>
            <div className="rounded-lg bg-yellow-50 p-4 flex items-start gap-3">
              <AlertTriangle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
              <div>
                <h4 className="font-medium text-yellow-800">Atencao</h4>
                <p className="text-sm text-yellow-700">
                  Esta ação e definitiva e encerrara o processo. O impugnante podera interpor recurso.
                </p>
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowDecisaoDialog(false)}>
              Cancelar
            </Button>
            <Button
              onClick={() => proferirDecisaoMutation.mutate()}
              disabled={proferirDecisaoMutation.isPending || !fundamentacaoDecisao}
            >
              {proferirDecisaoMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Proferir Decisao
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog: Arquivar */}
      <Dialog open={showArquivarDialog} onOpenChange={setShowArquivarDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Arquivar Impugnação</DialogTitle>
            <DialogDescription>
              Informe o motivo do arquivamento desta impugnacao.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label htmlFor="motivoArquivamento">Motivo do Arquivamento</Label>
              <Textarea
                id="motivoArquivamento"
                placeholder="Descreva o motivo do arquivamento..."
                value={motivoArquivamento}
                onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setMotivoArquivamento(e.target.value)}
                rows={4}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowArquivarDialog(false)}>
              Cancelar
            </Button>
            <Button
              variant="destructive"
              onClick={() => arquivarMutation.mutate()}
              disabled={arquivarMutation.isPending || !motivoArquivamento}
            >
              {arquivarMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Arquivar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog: Atribuir Relator */}
      <Dialog open={showAtribuirRelatorDialog} onOpenChange={setShowAtribuirRelatorDialog}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Atribuir Relator</DialogTitle>
            <DialogDescription>
              Selecione o relator responsável por esta impugnacao.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label htmlFor="relatorId">ID do Relator</Label>
              <Input
                id="relatorId"
                placeholder="ID do usuario relator"
                value={relatorId}
                onChange={(e) => setRelatorId(e.target.value)}
              />
              <p className="text-xs text-gray-500">
                Em produção, este campo será um select com os membros da comissão eleitoral.
              </p>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setShowAtribuirRelatorDialog(false)}>
              Cancelar
            </Button>
            <Button
              onClick={() => atribuirRelatorMutation.mutate()}
              disabled={atribuirRelatorMutation.isPending || !relatorId}
            >
              {atribuirRelatorMutation.isPending && (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              )}
              Atribuir Relator
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}
