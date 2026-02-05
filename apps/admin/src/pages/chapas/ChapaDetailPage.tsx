import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  Edit,
  Trash2,
  Users,
  FileText,
  Calendar,
  Mail,
  Phone,
  Check,
  X,
  AlertTriangle,
  Loader2,
  Crown,
  Star,
  Building,
  Clock,
  Image as ImageIcon,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { useToast } from '@/hooks/use-toast'
import {
  chapasService,
  StatusChapa,
  TipoMembro,
  CargoMembro,
  type Chapa,
  type MembroChapa,
} from '@/services/chapas'

// Status labels with colors
const statusConfig: Record<StatusChapa, { label: string; color: string; bgColor: string }> = {
  [StatusChapa.PENDENTE]: {
    label: 'Pendente',
    color: 'text-yellow-700',
    bgColor: 'bg-yellow-100',
  },
  [StatusChapa.EM_ANALISE]: {
    label: 'Em Analise',
    color: 'text-blue-700',
    bgColor: 'bg-blue-100',
  },
  [StatusChapa.APROVADA]: {
    label: 'Aprovada',
    color: 'text-green-700',
    bgColor: 'bg-green-100',
  },
  [StatusChapa.REPROVADA]: {
    label: 'Reprovada',
    color: 'text-red-700',
    bgColor: 'bg-red-100',
  },
  [StatusChapa.IMPUGNADA]: {
    label: 'Impugnada',
    color: 'text-orange-700',
    bgColor: 'bg-orange-100',
  },
  [StatusChapa.SUSPENSA]: {
    label: 'Suspensa',
    color: 'text-gray-700',
    bgColor: 'bg-gray-100',
  },
  [StatusChapa.CANCELADA]: {
    label: 'Cancelada',
    color: 'text-red-700',
    bgColor: 'bg-red-100',
  },
}

// Cargo labels
const cargoLabels: Record<CargoMembro, string> = {
  [CargoMembro.PRESIDENTE]: 'Presidente',
  [CargoMembro.VICE_PRESIDENTE]: 'Vice-Presidente',
  [CargoMembro.CONSELHEIRO]: 'Conselheiro',
  [CargoMembro.DIRETOR]: 'Diretor',
  [CargoMembro.COORDENADOR]: 'Coordenador',
}

type DialogAction = 'delete' | 'approve' | 'reject' | 'suspend' | 'reactivate' | null

export function ChapaDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Dialog state
  const [dialogOpen, setDialogOpen] = useState(false)
  const [dialogAction, setDialogAction] = useState<DialogAction>(null)
  const [actionReason, setActionReason] = useState('')

  // Fetch chapa details
  const {
    data: chapa,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ['chapa', id],
    queryFn: () => chapasService.getById(id!),
    enabled: !!id,
  })

  // Fetch membros
  const { data: membros } = useQuery({
    queryKey: ['chapa-membros', id],
    queryFn: () => chapasService.getMembros(id!),
    enabled: !!id,
  })

  // Mutations
  const deleteMutation = useMutation({
    mutationFn: () => chapasService.delete(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({ title: 'Chapa excluida', description: 'A chapa foi excluida com sucesso.' })
      navigate('/chapas')
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao excluir',
        description: error.response?.data?.message || 'Tente novamente.',
      })
    },
  })

  const aprovarMutation = useMutation({
    mutationFn: () => chapasService.aprovar(id!),
    onSuccess: (data) => {
      queryClient.setQueryData(['chapa', id], data)
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({ title: 'Chapa aprovada', description: 'A chapa foi aprovada com sucesso.' })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao aprovar',
        description: error.response?.data?.message || 'Tente novamente.',
      })
    },
  })

  const reprovarMutation = useMutation({
    mutationFn: (motivo: string) => chapasService.reprovar(id!, motivo),
    onSuccess: (data) => {
      queryClient.setQueryData(['chapa', id], data)
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({ title: 'Chapa reprovada', description: 'A chapa foi reprovada.' })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao reprovar',
        description: error.response?.data?.message || 'Tente novamente.',
      })
    },
  })

  const suspenderMutation = useMutation({
    mutationFn: (motivo: string) => chapasService.suspender(id!, motivo),
    onSuccess: (data) => {
      queryClient.setQueryData(['chapa', id], data)
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({ title: 'Chapa suspensa', description: 'A chapa foi suspensa.' })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao suspender',
        description: error.response?.data?.message || 'Tente novamente.',
      })
    },
  })

  const reativarMutation = useMutation({
    mutationFn: () => chapasService.reativar(id!),
    onSuccess: (data) => {
      queryClient.setQueryData(['chapa', id], data)
      queryClient.invalidateQueries({ queryKey: ['chapas'] })
      toast({ title: 'Chapa reativada', description: 'A chapa foi reativada.' })
      closeDialog()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao reativar',
        description: error.response?.data?.message || 'Tente novamente.',
      })
    },
  })

  // Dialog helpers
  const openDialog = (action: DialogAction) => {
    setDialogAction(action)
    setActionReason('')
    setDialogOpen(true)
  }

  const closeDialog = () => {
    setDialogOpen(false)
    setDialogAction(null)
    setActionReason('')
  }

  const handleDialogConfirm = () => {
    switch (dialogAction) {
      case 'delete':
        deleteMutation.mutate()
        break
      case 'approve':
        aprovarMutation.mutate()
        break
      case 'reject':
        reprovarMutation.mutate(actionReason || 'Chapa reprovada pela administracao')
        break
      case 'suspend':
        suspenderMutation.mutate(actionReason || 'Chapa suspensa pela administracao')
        break
      case 'reactivate':
        reativarMutation.mutate()
        break
    }
  }

  const isDialogLoading =
    deleteMutation.isPending ||
    aprovarMutation.isPending ||
    reprovarMutation.isPending ||
    suspenderMutation.isPending ||
    reativarMutation.isPending

  // Format date
  const formatDate = (dateString?: string) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }

  // Get dialog content
  const getDialogContent = () => {
    switch (dialogAction) {
      case 'delete':
        return {
          title: 'Excluir Chapa',
          description: (
            <>
              Tem certeza que deseja excluir a chapa <strong>"{chapa?.nome}"</strong>?
              Esta acao nao pode ser desfeita.
            </>
          ),
          confirmLabel: 'Excluir',
          confirmVariant: 'bg-red-600 hover:bg-red-700 text-white',
          showReason: false,
        }
      case 'approve':
        return {
          title: 'Aprovar Chapa',
          description: (
            <>
              Confirma a aprovacao da chapa <strong>"{chapa?.nome}"</strong>?
            </>
          ),
          confirmLabel: 'Aprovar',
          confirmVariant: 'bg-green-600 hover:bg-green-700 text-white',
          showReason: false,
        }
      case 'reject':
        return {
          title: 'Reprovar Chapa',
          description: (
            <>
              Confirma a reprovacao da chapa <strong>"{chapa?.nome}"</strong>?
            </>
          ),
          confirmLabel: 'Reprovar',
          confirmVariant: 'bg-red-600 hover:bg-red-700 text-white',
          showReason: true,
          reasonLabel: 'Motivo da reprovacao',
        }
      case 'suspend':
        return {
          title: 'Suspender Chapa',
          description: (
            <>
              Confirma a suspensao da chapa <strong>"{chapa?.nome}"</strong>?
            </>
          ),
          confirmLabel: 'Suspender',
          confirmVariant: 'bg-orange-600 hover:bg-orange-700 text-white',
          showReason: true,
          reasonLabel: 'Motivo da suspensao',
        }
      case 'reactivate':
        return {
          title: 'Reativar Chapa',
          description: (
            <>
              Confirma a reativacao da chapa <strong>"{chapa?.nome}"</strong>?
            </>
          ),
          confirmLabel: 'Reativar',
          confirmVariant: 'bg-green-600 hover:bg-green-700 text-white',
          showReason: false,
        }
      default:
        return {
          title: '',
          description: '',
          confirmLabel: '',
          confirmVariant: '',
          showReason: false,
        }
    }
  }

  const dialogContent = getDialogContent()

  // Separate members by type
  const titulares = membros?.filter((m) => m.tipo === TipoMembro.TITULAR).sort((a, b) => a.ordem - b.ordem) || []
  const suplentes = membros?.filter((m) => m.tipo === TipoMembro.SUPLENTE).sort((a, b) => a.ordem - b.ordem) || []

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (isError || !chapa) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertTriangle className="h-12 w-12 text-red-400 mb-4" />
        <p className="text-gray-500">Chapa nao encontrada.</p>
        <Link to="/chapas" className="mt-4">
          <Button variant="outline">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Voltar para lista
          </Button>
        </Link>
      </div>
    )
  }

  const statusInfo = statusConfig[chapa.status]

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/chapas">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">{chapa.nome}</h1>
              <span
                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo?.bgColor} ${statusInfo?.color}`}
              >
                {statusInfo?.label}
              </span>
            </div>
            <p className="text-gray-600">
              Chapa #{chapa.numero} {chapa.sigla && `- ${chapa.sigla}`}
            </p>
          </div>
        </div>

        {/* Actions */}
        <div className="flex items-center gap-2">
          <Link to={`/chapas/${id}/editar`}>
            <Button variant="outline">
              <Edit className="mr-2 h-4 w-4" />
              Editar
            </Button>
          </Link>
          <Link to={`/chapas/${id}/membros`}>
            <Button variant="outline">
              <Users className="mr-2 h-4 w-4" />
              Membros
            </Button>
          </Link>
          <Link to={`/chapas/${id}/documentos`}>
            <Button variant="outline">
              <FileText className="mr-2 h-4 w-4" />
              Documentos
            </Button>
          </Link>

          {/* Status Actions */}
          {(chapa.status === StatusChapa.PENDENTE || chapa.status === StatusChapa.EM_ANALISE) && (
            <>
              <Button
                className="bg-green-600 hover:bg-green-700"
                onClick={() => openDialog('approve')}
              >
                <Check className="mr-2 h-4 w-4" />
                Aprovar
              </Button>
              <Button variant="destructive" onClick={() => openDialog('reject')}>
                <X className="mr-2 h-4 w-4" />
                Reprovar
              </Button>
            </>
          )}

          {chapa.status === StatusChapa.APROVADA && (
            <Button
              variant="outline"
              className="text-orange-600 border-orange-600 hover:bg-orange-50"
              onClick={() => openDialog('suspend')}
            >
              Suspender
            </Button>
          )}

          {chapa.status === StatusChapa.SUSPENSA && (
            <Button
              variant="outline"
              className="text-green-600 border-green-600 hover:bg-green-50"
              onClick={() => openDialog('reactivate')}
            >
              Reativar
            </Button>
          )}

          {chapa.status !== StatusChapa.APROVADA && (
            <Button variant="destructive" onClick={() => openDialog('delete')}>
              <Trash2 className="mr-2 h-4 w-4" />
              Excluir
            </Button>
          )}
        </div>
      </div>

      {/* Main Content */}
      <div className="grid gap-6 lg:grid-cols-3">
        {/* Left Column - Main Info */}
        <div className="lg:col-span-2 space-y-6">
          {/* Basic Info */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Building className="h-5 w-5" />
                Informacoes da Chapa
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <dt className="text-sm font-medium text-gray-500">Numero</dt>
                  <dd className="mt-1 text-lg font-bold text-blue-600">{chapa.numero}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Sigla</dt>
                  <dd className="mt-1 text-sm text-gray-900">{chapa.sigla || '-'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Eleicao</dt>
                  <dd className="mt-1 text-sm text-gray-900">{chapa.eleicaoNome || '-'}</dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-gray-500">Representante Legal</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {chapa.representanteLegalNome || '-'}
                  </dd>
                </div>
                <div className="sm:col-span-2">
                  <dt className="text-sm font-medium text-gray-500">Lema</dt>
                  <dd className="mt-1 text-sm text-gray-900 italic">
                    {chapa.lema ? `"${chapa.lema}"` : '-'}
                  </dd>
                </div>
                <div className="sm:col-span-2">
                  <dt className="text-sm font-medium text-gray-500">Descricao</dt>
                  <dd className="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                    {chapa.descricao || 'Sem descricao.'}
                  </dd>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Members Preview */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle className="flex items-center gap-2">
                  <Users className="h-5 w-5" />
                  Membros da Chapa
                </CardTitle>
                <CardDescription>
                  {(titulares.length + suplentes.length)} membros cadastrados
                </CardDescription>
              </div>
              <Link to={`/chapas/${id}/membros`}>
                <Button variant="outline" size="sm">
                  Gerenciar
                </Button>
              </Link>
            </CardHeader>
            <CardContent>
              {titulares.length > 0 || suplentes.length > 0 ? (
                <div className="space-y-4">
                  {/* Titulares */}
                  {titulares.length > 0 && (
                    <div>
                      <h4 className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                        <Crown className="h-4 w-4 text-yellow-500" />
                        Titulares ({titulares.length})
                      </h4>
                      <div className="space-y-2">
                        {titulares.slice(0, 5).map((membro) => (
                          <MemberCard key={membro.id} membro={membro} />
                        ))}
                        {titulares.length > 5 && (
                          <p className="text-sm text-gray-500 text-center py-2">
                            + {titulares.length - 5} titular(es)
                          </p>
                        )}
                      </div>
                    </div>
                  )}

                  {/* Suplentes */}
                  {suplentes.length > 0 && (
                    <div>
                      <h4 className="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                        <Star className="h-4 w-4 text-gray-400" />
                        Suplentes ({suplentes.length})
                      </h4>
                      <div className="space-y-2">
                        {suplentes.slice(0, 3).map((membro) => (
                          <MemberCard key={membro.id} membro={membro} />
                        ))}
                        {suplentes.length > 3 && (
                          <p className="text-sm text-gray-500 text-center py-2">
                            + {suplentes.length - 3} suplente(s)
                          </p>
                        )}
                      </div>
                    </div>
                  )}
                </div>
              ) : (
                <p className="text-center py-8 text-gray-500">Nenhum membro cadastrado.</p>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Right Column - Sidebar */}
        <div className="space-y-6">
          {/* Logo */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-base">
                <ImageIcon className="h-4 w-4" />
                Logo da Chapa
              </CardTitle>
            </CardHeader>
            <CardContent>
              {chapa.logoUrl ? (
                <img
                  src={chapa.logoUrl}
                  alt={chapa.nome}
                  className="w-full h-auto rounded-lg"
                />
              ) : (
                <div className="w-full h-40 bg-gray-100 rounded-lg flex items-center justify-center">
                  <div className="text-center">
                    <ImageIcon className="h-12 w-12 text-gray-300 mx-auto" />
                    <p className="text-sm text-gray-400 mt-2">Sem logo</p>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Timeline */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2 text-base">
                <Clock className="h-4 w-4" />
                Historico
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-start gap-3">
                <div className="h-2 w-2 mt-2 rounded-full bg-gray-400" />
                <div>
                  <p className="text-sm font-medium">Cadastro</p>
                  <p className="text-xs text-gray-500">{formatDate(chapa.dataCadastro)}</p>
                </div>
              </div>
              {chapa.dataAprovacao && (
                <div className="flex items-start gap-3">
                  <div className="h-2 w-2 mt-2 rounded-full bg-green-500" />
                  <div>
                    <p className="text-sm font-medium">Aprovacao</p>
                    <p className="text-xs text-gray-500">{formatDate(chapa.dataAprovacao)}</p>
                  </div>
                </div>
              )}
              {chapa.motivoReprovacao && (
                <div className="flex items-start gap-3">
                  <div className="h-2 w-2 mt-2 rounded-full bg-red-500" />
                  <div>
                    <p className="text-sm font-medium">Motivo da Reprovacao</p>
                    <p className="text-xs text-gray-500">{chapa.motivoReprovacao}</p>
                  </div>
                </div>
              )}
              <div className="flex items-start gap-3">
                <div className="h-2 w-2 mt-2 rounded-full bg-blue-400" />
                <div>
                  <p className="text-sm font-medium">Ultima atualizacao</p>
                  <p className="text-xs text-gray-500">
                    {formatDate(chapa.updatedAt || chapa.createdAt)}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Statistics */}
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Estatisticas</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Total de Membros</span>
                <span className="text-lg font-semibold">{(membros?.length || 0)}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Titulares</span>
                <span className="text-lg font-semibold">{titulares.length}</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">Suplentes</span>
                <span className="text-lg font-semibold">{suplentes.length}</span>
              </div>
              {chapa.totalVotos !== undefined && (
                <div className="flex justify-between items-center border-t pt-4">
                  <span className="text-sm text-gray-500">Total de Votos</span>
                  <span className="text-lg font-semibold text-green-600">{chapa.totalVotos}</span>
                </div>
              )}
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Confirmation Dialog */}
      <AlertDialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>{dialogContent.title}</AlertDialogTitle>
            <AlertDialogDescription asChild>
              <div>
                {dialogContent.description}
                {dialogContent.showReason && (
                  <div className="mt-4 space-y-2">
                    <Label htmlFor="reason">{dialogContent.reasonLabel}</Label>
                    <textarea
                      id="reason"
                      value={actionReason}
                      onChange={(e) => setActionReason(e.target.value)}
                      placeholder="Informe o motivo..."
                      className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    />
                  </div>
                )}
              </div>
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={isDialogLoading}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDialogConfirm}
              className={dialogContent.confirmVariant}
              disabled={isDialogLoading}
            >
              {isDialogLoading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Processando...
                </>
              ) : (
                dialogContent.confirmLabel
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}

// Member Card Component
function MemberCard({ membro }: { membro: MembroChapa }) {
  return (
    <div className="flex items-center justify-between p-3 rounded-lg border bg-gray-50">
      <div className="flex items-center gap-3">
        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 text-sm font-medium">
          {membro.ordem}
        </div>
        <div>
          <p className="text-sm font-medium">{membro.candidatoNome}</p>
          <p className="text-xs text-gray-500">
            {cargoLabels[membro.cargo] || 'Membro'}
            {membro.candidatoRegistroCAU && ` - ${membro.candidatoRegistroCAU}`}
          </p>
        </div>
      </div>
      {membro.cargo === CargoMembro.PRESIDENTE && (
        <Crown className="h-4 w-4 text-yellow-500" />
      )}
    </div>
  )
}
