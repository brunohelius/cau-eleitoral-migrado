import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  Download,
  Trash2,
  Loader2,
  FileText,
  Upload,
  Eye,
  CheckCircle,
  XCircle,
  Clock,
  File,
  FileImage,
  FileArchive,
  AlertTriangle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
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
  type Chapa,
  type DocumentoChapa,
  type AddDocumentoRequest,
} from '@/services/chapas'

// Document type options
const tiposDocumento = [
  { value: 0, label: 'Ata de Registro' },
  { value: 1, label: 'Proposta de Trabalho' },
  { value: 2, label: 'Curriculo dos Membros' },
  { value: 3, label: 'Declaracao de Idoneidade' },
  { value: 4, label: 'Comprovante de Quitacao' },
  { value: 5, label: 'Procuracao' },
  { value: 6, label: 'Outros' },
]

// Status options
const statusConfig: Record<number, { label: string; color: string; icon: typeof CheckCircle }> = {
  0: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800', icon: Clock },
  1: { label: 'Em Analise', color: 'bg-blue-100 text-blue-800', icon: Clock },
  2: { label: 'Aprovado', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  3: { label: 'Rejeitado', color: 'bg-red-100 text-red-800', icon: XCircle },
}

export function ChapaDocumentosPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Modal state
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [selectedFile, setSelectedFile] = useState<File | null>(null)
  const [formData, setFormData] = useState({
    tipo: 0,
    nome: '',
  })

  // Delete dialog state
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [documentoToDelete, setDocumentoToDelete] = useState<DocumentoChapa | null>(null)

  // Fetch chapa details
  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: () => chapasService.getById(id!),
    enabled: !!id,
  })

  // Fetch documentos
  const {
    data: documentos,
    isLoading: isLoadingDocumentos,
    isError,
  } = useQuery({
    queryKey: ['chapa-documentos', id],
    queryFn: () => chapasService.getDocumentos(id!),
    enabled: !!id,
  })

  // Upload mutation
  const uploadMutation = useMutation({
    mutationFn: (data: AddDocumentoRequest) => chapasService.uploadDocumento(id!, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapa-documentos', id] })
      toast({
        title: 'Documento enviado',
        description: 'O documento foi enviado para analise.',
      })
      handleCloseModal()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao enviar documento',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Delete mutation
  const deleteMutation = useMutation({
    mutationFn: (documentoId: string) => chapasService.removeDocumento(id!, documentoId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapa-documentos', id] })
      toast({
        title: 'Documento removido',
        description: 'O documento foi removido com sucesso.',
      })
      setDeleteDialogOpen(false)
      setDocumentoToDelete(null)
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao remover documento',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Modal handlers
  const handleOpenModal = () => {
    setFormData({ tipo: 0, nome: '' })
    setSelectedFile(null)
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
    setSelectedFile(null)
    setFormData({ tipo: 0, nome: '' })
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      setSelectedFile(file)
      if (!formData.nome) {
        setFormData({ ...formData, nome: file.name.replace(/\.[^/.]+$/, '') })
      }
    }
  }

  const handleUpload = () => {
    if (!selectedFile) {
      toast({
        variant: 'destructive',
        title: 'Selecione um arquivo',
        description: 'Voce precisa selecionar um arquivo para upload.',
      })
      return
    }

    if (!formData.nome) {
      toast({
        variant: 'destructive',
        title: 'Nome obrigatorio',
        description: 'Informe um nome para o documento.',
      })
      return
    }

    uploadMutation.mutate({
      nome: formData.nome,
      tipo: formData.tipo,
      arquivo: selectedFile,
    })
  }

  const handleDeleteClick = (documento: DocumentoChapa) => {
    setDocumentoToDelete(documento)
    setDeleteDialogOpen(true)
  }

  const handleDeleteConfirm = () => {
    if (documentoToDelete) {
      deleteMutation.mutate(documentoToDelete.id)
    }
  }

  // Helpers
  const formatFileSize = (bytes?: number) => {
    if (!bytes) return '-'
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
  }

  const getStatusBadge = (status: number) => {
    const config = statusConfig[status] || statusConfig[0]
    const Icon = config.icon
    return (
      <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
        <Icon className="h-3 w-3" />
        {config.label}
      </span>
    )
  }

  const getFileIcon = (nome: string) => {
    const ext = nome.split('.').pop()?.toLowerCase()
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext || '')) {
      return <FileImage className="h-8 w-8 text-blue-500" />
    }
    if (['zip', 'rar', '7z'].includes(ext || '')) {
      return <FileArchive className="h-8 w-8 text-purple-500" />
    }
    return <FileText className="h-8 w-8 text-red-500" />
  }

  const getTipoLabel = (tipo: number) => {
    return tiposDocumento.find((t) => t.value === tipo)?.label || 'Outros'
  }

  const isLoading = isLoadingChapa || isLoadingDocumentos

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

  const docs = documentos || []
  const aprovados = docs.filter((d) => d.status === 2).length
  const pendentes = docs.filter((d) => d.status === 0 || d.status === 1).length
  const rejeitados = docs.filter((d) => d.status === 3).length

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to={`/chapas/${id}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Documentos da Chapa</h1>
            <p className="text-gray-600">
              {chapa.numero}. {chapa.nome} ({chapa.sigla || 'Sem sigla'})
            </p>
          </div>
        </div>
        <Button onClick={handleOpenModal}>
          <Upload className="mr-2 h-4 w-4" />
          Enviar Documento
        </Button>
      </div>

      {/* Statistics */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <CheckCircle className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{aprovados}</p>
                <p className="text-sm text-gray-500">Aprovados</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                <Clock className="h-6 w-6 text-yellow-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{pendentes}</p>
                <p className="text-sm text-gray-500">Pendentes</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100">
                <XCircle className="h-6 w-6 text-red-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{rejeitados}</p>
                <p className="text-sm text-gray-500">Rejeitados</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Document List */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <File className="h-5 w-5" />
            Documentos Enviados
          </CardTitle>
          <CardDescription>{docs.length} documentos</CardDescription>
        </CardHeader>
        <CardContent>
          {docs.length > 0 ? (
            <div className="space-y-4">
              {docs.map((doc) => (
                <div
                  key={doc.id}
                  className="flex items-center justify-between rounded-lg border p-4"
                >
                  <div className="flex items-center gap-4">
                    {getFileIcon(doc.nome)}
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{doc.nome}</span>
                        {getStatusBadge(doc.status)}
                      </div>
                      <p className="text-sm text-gray-500">
                        {getTipoLabel(doc.tipo)} | {formatFileSize(doc.tamanho)}
                      </p>
                      <p className="text-xs text-gray-400">
                        Enviado em {new Date(doc.createdAt).toLocaleString('pt-BR')}
                      </p>
                      {doc.observacoes && (
                        <p className="text-xs text-red-500 mt-1">Obs: {doc.observacoes}</p>
                      )}
                    </div>
                  </div>
                  <div className="flex gap-1">
                    {doc.arquivoUrl && (
                      <>
                        <a href={doc.arquivoUrl} target="_blank" rel="noopener noreferrer">
                          <Button variant="ghost" size="icon" title="Visualizar">
                            <Eye className="h-4 w-4" />
                          </Button>
                        </a>
                        <a href={doc.arquivoUrl} download>
                          <Button variant="ghost" size="icon" title="Baixar">
                            <Download className="h-4 w-4" />
                          </Button>
                        </a>
                      </>
                    )}
                    <Button
                      variant="ghost"
                      size="icon"
                      title="Excluir"
                      onClick={() => handleDeleteClick(doc)}
                    >
                      <Trash2 className="h-4 w-4 text-red-500" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">
              Nenhum documento enviado. Clique em "Enviar Documento" para adicionar.
            </p>
          )}
        </CardContent>
      </Card>

      {/* Upload Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-md">
            <CardHeader>
              <CardTitle>Enviar Documento</CardTitle>
              <CardDescription>Selecione o tipo e o arquivo a ser enviado</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="tipo">Tipo de Documento *</Label>
                <select
                  id="tipo"
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                  value={formData.tipo}
                  onChange={(e) => setFormData({ ...formData, tipo: parseInt(e.target.value) })}
                >
                  {tiposDocumento.map((tipo) => (
                    <option key={tipo.value} value={tipo.value}>
                      {tipo.label}
                    </option>
                  ))}
                </select>
              </div>

              <div className="space-y-2">
                <Label htmlFor="nome">Nome do Documento *</Label>
                <Input
                  id="nome"
                  value={formData.nome}
                  onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                  placeholder="Nome para identificacao"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="arquivo">Arquivo *</Label>
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                  <input
                    id="arquivo"
                    type="file"
                    className="hidden"
                    onChange={handleFileChange}
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                  />
                  <label htmlFor="arquivo" className="cursor-pointer">
                    <Upload className="h-8 w-8 mx-auto text-gray-400 mb-2" />
                    {selectedFile ? (
                      <div>
                        <p className="text-sm font-medium text-gray-900">{selectedFile.name}</p>
                        <p className="text-xs text-gray-500 mt-1">
                          {formatFileSize(selectedFile.size)}
                        </p>
                      </div>
                    ) : (
                      <>
                        <p className="text-sm text-gray-500">
                          Clique para selecionar ou arraste o arquivo
                        </p>
                        <p className="text-xs text-gray-400 mt-1">
                          PDF, DOC, DOCX, JPG, PNG, ZIP (max. 10MB)
                        </p>
                      </>
                    )}
                  </label>
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-4">
                <Button variant="outline" onClick={handleCloseModal}>
                  Cancelar
                </Button>
                <Button onClick={handleUpload} disabled={uploadMutation.isPending}>
                  {uploadMutation.isPending ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Enviando...
                    </>
                  ) : (
                    <>
                      <Upload className="mr-2 h-4 w-4" />
                      Enviar
                    </>
                  )}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Excluir Documento</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja excluir o documento{' '}
              <strong>"{documentoToDelete?.nome}"</strong>? Esta acao nao pode ser desfeita.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={deleteMutation.isPending}>Cancelar</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDeleteConfirm}
              className="bg-red-600 hover:bg-red-700"
              disabled={deleteMutation.isPending}
            >
              {deleteMutation.isPending ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Excluindo...
                </>
              ) : (
                'Excluir'
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
