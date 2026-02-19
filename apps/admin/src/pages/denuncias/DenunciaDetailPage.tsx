import { useState } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  AlertTriangle,
  User,
  Calendar,
  FileText,
  CheckCircle,
  XCircle,
  Clock,
  Download,
  Loader2,
  Gavel,
  PlayCircle,
  Archive,
  RefreshCw,
  Edit,
  Trash2,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import {
  denunciasService,
  StatusDenuncia,
  TipoDenuncia,
  statusDenunciaLabels,
  tipoDenunciaLabels,
  type Denuncia,
} from '@/services/denuncias'

export function DenunciaDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Modal state
  const [actionModal, setActionModal] = useState<{
    type: 'arquivar' | 'reabrir' | 'delete' | null
  }>({ type: null })
  const [actionMotivo, setActionMotivo] = useState('')

  // Fetch denuncia
  const { data: denuncia, isLoading, error } = useQuery({
    queryKey: ['denuncia', id],
    queryFn: () => denunciasService.getById(id!),
    enabled: !!id,
  })

  // Mutations
  const iniciarAnaliseMutation = useMutation({
    mutationFn: () => denunciasService.iniciarAnalise(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncia', id] })
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Análise iniciada',
        description: 'A denuncia foi movida para análise.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível iniciar a análise.',
      })
    },
  })

  const arquivarMutation = useMutation({
    mutationFn: (motivo: string) => denunciasService.arquivar(id!, motivo),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncia', id] })
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denuncia arquivada',
        description: 'A denuncia foi arquivada com sucesso.',
      })
      setActionModal({ type: null })
      setActionMotivo('')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível arquivar a denuncia.',
      })
    },
  })

  const reabrirMutation = useMutation({
    mutationFn: (motivo: string) => denunciasService.reabrir(id!, motivo),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncia', id] })
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denuncia reaberta',
        description: 'A denuncia foi reaberta com sucesso.',
      })
      setActionModal({ type: null })
      setActionMotivo('')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível reabrir a denuncia.',
      })
    },
  })

  const deleteMutation = useMutation({
    mutationFn: () => denunciasService.delete(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['denuncias'] })
      toast({
        title: 'Denuncia excluida',
        description: 'A denuncia foi excluida com sucesso.',
      })
      navigate('/denuncias')
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível excluir a denuncia.',
      })
    },
  })

  const handleDownloadAnexo = async (anexoId: string, nome: string) => {
    try {
      const blob = await denunciasService.downloadAnexo(id!, anexoId)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = nome
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Não foi possível baixar o anexo.',
      })
    }
  }

  const handleAction = () => {
    if (!actionMotivo.trim() && (actionModal.type === 'arquivar' || actionModal.type === 'reabrir')) {
      toast({
        variant: 'destructive',
        title: 'Motivo obrigatório',
        description: 'Informe o motivo da acao.',
      })
      return
    }

    if (actionModal.type === 'arquivar') {
      arquivarMutation.mutate(actionMotivo)
    } else if (actionModal.type === 'reabrir') {
      reabrirMutation.mutate(actionMotivo)
    } else if (actionModal.type === 'delete') {
      deleteMutation.mutate()
    }
  }

  const getStatusIcon = (status: StatusDenuncia) => {
    switch (status) {
      case StatusDenuncia.RECEBIDA:
        return <Clock className="h-3 w-3" />
      case StatusDenuncia.EM_ANALISE:
        return <FileText className="h-3 w-3" />
      case StatusDenuncia.PROCEDENTE:
      case StatusDenuncia.ADMISSIBILIDADE_ACEITA:
        return <CheckCircle className="h-3 w-3" />
      case StatusDenuncia.IMPROCEDENTE:
      case StatusDenuncia.ADMISSIBILIDADE_REJEITADA:
        return <XCircle className="h-3 w-3" />
      case StatusDenuncia.ARQUIVADA:
        return <Archive className="h-3 w-3" />
      default:
        return <Clock className="h-3 w-3" />
    }
  }

  // Check if actions can be performed based on status
  const canIniciarAnalise = denuncia?.status === StatusDenuncia.RECEBIDA
  const canJulgar =
    denuncia?.status === StatusDenuncia.EM_ANALISE ||
    denuncia?.status === StatusDenuncia.AGUARDANDO_JULGAMENTO ||
    denuncia?.status === StatusDenuncia.ADMISSIBILIDADE_ACEITA ||
    denuncia?.status === StatusDenuncia.DEFESA_APRESENTADA
  const canArquivar =
    denuncia?.status !== StatusDenuncia.ARQUIVADA &&
    denuncia?.status !== StatusDenuncia.PROCEDENTE &&
    denuncia?.status !== StatusDenuncia.IMPROCEDENTE
  const canReabrir = denuncia?.status === StatusDenuncia.ARQUIVADA
  const canEdit =
    denuncia?.status === StatusDenuncia.RECEBIDA || denuncia?.status === StatusDenuncia.EM_ANALISE
  const canDelete = denuncia?.status === StatusDenuncia.RECEBIDA

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (error || !denuncia) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertTriangle className="h-12 w-12 text-gray-300 mb-4" />
        <p className="text-gray-500">Denuncia não encontrada.</p>
        <Link to="/denuncias">
          <Button variant="link" className="mt-2">
            Voltar para lista
          </Button>
        </Link>
      </div>
    )
  }

  const statusInfo = statusDenunciaLabels[denuncia.status] || {
    label: 'Desconhecido',
    color: 'bg-gray-100 text-gray-800',
  }
  const tipoLabel = tipoDenunciaLabels[denuncia.tipo] || 'Outro'

  return (
    <div className="space-y-6">
      {/* Header */}
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
              <span
                className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusInfo.color}`}
              >
                {getStatusIcon(denuncia.status)}
                {statusInfo.label}
              </span>
            </div>
            <p className="text-gray-600">{denuncia.titulo}</p>
          </div>
        </div>
        <div className="flex gap-2">
          {canIniciarAnalise && (
            <Button
              variant="outline"
              onClick={() => iniciarAnaliseMutation.mutate()}
              disabled={iniciarAnaliseMutation.isPending}
            >
              {iniciarAnaliseMutation.isPending ? (
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              ) : (
                <PlayCircle className="mr-2 h-4 w-4" />
              )}
              Iniciar Análise
            </Button>
          )}
          {canArquivar && (
            <Button variant="outline" onClick={() => setActionModal({ type: 'arquivar' })}>
              <Archive className="mr-2 h-4 w-4" />
              Arquivar
            </Button>
          )}
          {canReabrir && (
            <Button variant="outline" onClick={() => setActionModal({ type: 'reabrir' })}>
              <RefreshCw className="mr-2 h-4 w-4" />
              Reabrir
            </Button>
          )}
          {canEdit && (
            <Link to={`/denuncias/${id}/editar`}>
              <Button variant="outline">
                <Edit className="mr-2 h-4 w-4" />
                Editar
              </Button>
            </Link>
          )}
          {canDelete && (
            <Button variant="outline" onClick={() => setActionModal({ type: 'delete' })}>
              <Trash2 className="mr-2 h-4 w-4 text-red-500" />
            </Button>
          )}
          {canJulgar && (
            <Link to={`/denuncias/${id}/julgamento`}>
              <Button>
                <Gavel className="mr-2 h-4 w-4" />
                Julgar Denuncia
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
              <AlertTriangle className="h-5 w-5 text-orange-500" />
              Descrição da Denuncia
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-gray-700 whitespace-pre-wrap">{denuncia.descricao}</p>

            {denuncia.fundamentacao && (
              <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                <h4 className="text-sm font-medium text-gray-500 mb-2">Fundamentação</h4>
                <p className="text-gray-700 whitespace-pre-wrap">{denuncia.fundamentacao}</p>
              </div>
            )}

            <div className="mt-6 grid gap-4 sm:grid-cols-2">
              <div>
                <dt className="text-sm font-medium text-gray-500">Tipo de Denuncia</dt>
                <dd className="mt-1 text-sm text-gray-900">{tipoLabel}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Eleição</dt>
                <dd className="mt-1 text-sm text-gray-900">{denuncia.eleicaoNome || '-'}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data da Denuncia</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(denuncia.dataDenuncia).toLocaleString('pt-BR')}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data de Recebimento</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {denuncia.dataRecebimento
                    ? new Date(denuncia.dataRecebimento).toLocaleString('pt-BR')
                    : '-'}
                </dd>
              </div>
              {denuncia.prazoDefesa && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Prazo para Defesa</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {new Date(denuncia.prazoDefesa).toLocaleDateString('pt-BR')}
                  </dd>
                </div>
              )}
              {denuncia.prazoRecurso && (
                <div>
                  <dt className="text-sm font-medium text-gray-500">Prazo para Recurso</dt>
                  <dd className="mt-1 text-sm text-gray-900">
                    {new Date(denuncia.prazoRecurso).toLocaleDateString('pt-BR')}
                  </dd>
                </div>
              )}
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
              {denuncia.anonima ? (
                <p className="mt-1 font-medium text-gray-400 italic">Anonimo</p>
              ) : (
                <>
                  <p className="mt-1 font-medium">{denuncia.denuncianteNome || 'Nao informado'}</p>
                  {denuncia.denuncianteEmail && (
                    <p className="text-sm text-gray-600">{denuncia.denuncianteEmail}</p>
                  )}
                  {denuncia.denuncianteTelefone && (
                    <p className="text-sm text-gray-600">{denuncia.denuncianteTelefone}</p>
                  )}
                </>
              )}
            </div>
            <div className="rounded-lg bg-gray-50 p-4">
              <h4 className="text-sm font-medium text-gray-500">Denunciado</h4>
              {denuncia.chapaNome && (
                <>
                  <p className="mt-1 font-medium">{denuncia.chapaNome}</p>
                  <p className="text-xs text-gray-500">Chapa</p>
                </>
              )}
              {denuncia.membroNome && (
                <>
                  <p className="mt-1 font-medium">{denuncia.membroNome}</p>
                  <p className="text-xs text-gray-500">Membro</p>
                </>
              )}
              {!denuncia.chapaNome && !denuncia.membroNome && (
                <p className="mt-1 text-gray-400">Nao especificado</p>
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
            <CardDescription>
              {denuncia.anexos?.length || 0} arquivos anexados
            </CardDescription>
          </CardHeader>
          <CardContent>
            {denuncia.anexos && denuncia.anexos.length > 0 ? (
              <div className="space-y-2">
                {denuncia.anexos.map((anexo) => (
                  <div
                    key={anexo.id}
                    className="flex items-center justify-between rounded-lg border p-3"
                  >
                    <div className="flex items-center gap-2">
                      <FileText className="h-4 w-4 text-gray-400" />
                      <div>
                        <span className="text-sm">{anexo.nome}</span>
                        <p className="text-xs text-gray-400">
                          {(anexo.tamanho / 1024).toFixed(1)} KB
                        </p>
                      </div>
                    </div>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleDownloadAnexo(anexo.id, anexo.nome)}
                    >
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

        {/* Pareceres */}
        {denuncia.pareceres && denuncia.pareceres.length > 0 && (
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Gavel className="h-5 w-5" />
                Pareceres
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {denuncia.pareceres.map((parecer) => (
                  <div key={parecer.id} className="border rounded-lg p-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-medium">{parecer.analistaNome}</span>
                      <span className="text-xs text-gray-500">
                        {new Date(parecer.dataEmissao).toLocaleString('pt-BR')}
                      </span>
                    </div>
                    <p className="text-gray-700">{parecer.parecer}</p>
                    <div className="mt-2">
                      <span
                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                          statusDenunciaLabels[parecer.decisao]?.color || 'bg-gray-100 text-gray-800'
                        }`}
                      >
                        {statusDenunciaLabels[parecer.decisao]?.label || 'Desconhecido'}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Historico */}
        {denuncia.historicos && denuncia.historicos.length > 0 && (
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
                  {denuncia.historicos.map((item) => (
                    <div key={item.id} className="relative flex gap-4 pl-10">
                      <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                      <div className="flex-1">
                        <div className="flex items-center justify-between">
                          <span className="font-medium">{item.acao}</span>
                          <span className="text-xs text-gray-500">
                            {new Date(item.createdAt).toLocaleString('pt-BR')}
                          </span>
                        </div>
                        <p className="text-sm text-gray-600">Por: {item.usuarioNome}</p>
                        {item.descricao && (
                          <p className="mt-1 text-sm text-gray-500 italic">{item.descricao}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>
        )}
      </div>

      {/* Action Modal */}
      {actionModal.type && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div className="p-6">
              <h3 className="text-lg font-semibold mb-4">
                {actionModal.type === 'arquivar' && 'Arquivar Denuncia'}
                {actionModal.type === 'reabrir' && 'Reabrir Denuncia'}
                {actionModal.type === 'delete' && 'Excluir Denuncia'}
              </h3>

              {actionModal.type === 'delete' ? (
                <p className="text-sm text-gray-600 mb-4">
                  Tem certeza que deseja excluir esta denuncia? Esta ação não pode ser desfeita.
                </p>
              ) : (
                <>
                  <p className="text-sm text-gray-600 mb-4">
                    Protocolo: <strong>{denuncia.protocolo}</strong>
                  </p>
                  <div className="space-y-4">
                    <div>
                      <label className="text-sm font-medium text-gray-700">Motivo *</label>
                      <textarea
                        value={actionMotivo}
                        onChange={(e) => setActionMotivo(e.target.value)}
                        className="mt-1 flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder={
                          actionModal.type === 'arquivar'
                            ? 'Informe o motivo do arquivamento...'
                            : 'Informe o motivo da reabertura...'
                        }
                      />
                    </div>
                  </div>
                </>
              )}

              <div className="flex justify-end gap-3 mt-6">
                <Button
                  variant="outline"
                  onClick={() => {
                    setActionModal({ type: null })
                    setActionMotivo('')
                  }}
                >
                  Cancelar
                </Button>
                <Button
                  variant={actionModal.type === 'delete' ? 'destructive' : 'default'}
                  onClick={handleAction}
                  disabled={
                    arquivarMutation.isPending ||
                    reabrirMutation.isPending ||
                    deleteMutation.isPending
                  }
                >
                  {(arquivarMutation.isPending ||
                    reabrirMutation.isPending ||
                    deleteMutation.isPending) && (
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  )}
                  {actionModal.type === 'arquivar' && 'Arquivar'}
                  {actionModal.type === 'reabrir' && 'Reabrir'}
                  {actionModal.type === 'delete' && 'Excluir'}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
