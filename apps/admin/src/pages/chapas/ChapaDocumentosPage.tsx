import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  Plus,
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
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

interface Documento {
  id: string
  nome: string
  tipo: string
  tamanho: number
  status: 'pendente' | 'aprovado' | 'rejeitado'
  dataUpload: string
  url: string
  observacao?: string
}

interface Chapa {
  id: string
  nome: string
  numero: number
  sigla: string
}

const tiposDocumento = [
  { value: 'ata_registro', label: 'Ata de Registro' },
  { value: 'proposta', label: 'Proposta de Trabalho' },
  { value: 'curriculo', label: 'Curriculo dos Membros' },
  { value: 'declaracao', label: 'Declaracao de Idoneidade' },
  { value: 'comprovante', label: 'Comprovante de Quitacao' },
  { value: 'procuracao', label: 'Procuracao' },
  { value: 'outros', label: 'Outros' },
]

export function ChapaDocumentosPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [selectedFile, setSelectedFile] = useState<File | null>(null)
  const [formData, setFormData] = useState({
    tipo: 'ata_registro',
    nome: '',
  })

  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: async () => {
      const response = await api.get<Chapa>(`/chapa/${id}`)
      return response.data
    },
    enabled: !!id,
  })

  // Mock documentos - em producao viria da API
  const [documentos, setDocumentos] = useState<Documento[]>([
    {
      id: '1',
      nome: 'Ata de Registro da Chapa',
      tipo: 'ata_registro',
      tamanho: 1024000,
      status: 'aprovado',
      dataUpload: '2024-02-15T10:30:00',
      url: '/documentos/ata.pdf',
    },
    {
      id: '2',
      nome: 'Proposta de Trabalho 2024-2027',
      tipo: 'proposta',
      tamanho: 2048000,
      status: 'aprovado',
      dataUpload: '2024-02-15T11:00:00',
      url: '/documentos/proposta.pdf',
    },
    {
      id: '3',
      nome: 'Curriculos dos Membros',
      tipo: 'curriculo',
      tamanho: 5120000,
      status: 'pendente',
      dataUpload: '2024-02-16T09:00:00',
      url: '/documentos/curriculos.zip',
    },
    {
      id: '4',
      nome: 'Declaracao de Idoneidade',
      tipo: 'declaracao',
      tamanho: 512000,
      status: 'rejeitado',
      dataUpload: '2024-02-14T14:30:00',
      url: '/documentos/declaracao.pdf',
      observacao: 'Documento com assinatura ilegivel',
    },
  ])

  const handleOpenModal = () => {
    setFormData({ tipo: 'ata_registro', nome: '' })
    setSelectedFile(null)
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
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

    const novoDocumento: Documento = {
      id: Date.now().toString(),
      nome: formData.nome || selectedFile.name,
      tipo: formData.tipo,
      tamanho: selectedFile.size,
      status: 'pendente',
      dataUpload: new Date().toISOString(),
      url: URL.createObjectURL(selectedFile),
    }

    setDocumentos((prev) => [...prev, novoDocumento])
    toast({
      title: 'Documento enviado',
      description: 'O documento foi enviado para analise.',
    })
    handleCloseModal()
  }

  const handleDelete = (docId: string) => {
    setDocumentos((prev) => prev.filter((d) => d.id !== docId))
    toast({
      title: 'Documento removido',
      description: 'O documento foi removido com sucesso.',
    })
  }

  const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
  }

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'aprovado':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <CheckCircle className="h-3 w-3" />
            Aprovado
          </span>
        )
      case 'rejeitado':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <XCircle className="h-3 w-3" />
            Rejeitado
          </span>
        )
      default:
        return (
          <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <Clock className="h-3 w-3" />
            Pendente
          </span>
        )
    }
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

  const getTipoLabel = (tipo: string) => {
    return tiposDocumento.find((t) => t.value === tipo)?.label || tipo
  }

  if (isLoadingChapa) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
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
              {chapa?.numero}. {chapa?.nome} ({chapa?.sigla})
            </p>
          </div>
        </div>
        <Button onClick={handleOpenModal}>
          <Upload className="mr-2 h-4 w-4" />
          Enviar Documento
        </Button>
      </div>

      {/* Estatisticas */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-4">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                <CheckCircle className="h-6 w-6 text-green-600" />
              </div>
              <div>
                <p className="text-2xl font-bold">{documentos.filter((d) => d.status === 'aprovado').length}</p>
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
                <p className="text-2xl font-bold">{documentos.filter((d) => d.status === 'pendente').length}</p>
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
                <p className="text-2xl font-bold">{documentos.filter((d) => d.status === 'rejeitado').length}</p>
                <p className="text-sm text-gray-500">Rejeitados</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Lista de Documentos */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <File className="h-5 w-5" />
            Documentos Enviados
          </CardTitle>
          <CardDescription>{documentos.length} documentos</CardDescription>
        </CardHeader>
        <CardContent>
          {documentos.length > 0 ? (
            <div className="space-y-4">
              {documentos.map((doc) => (
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
                        Enviado em {new Date(doc.dataUpload).toLocaleString('pt-BR')}
                      </p>
                      {doc.observacao && (
                        <p className="text-xs text-red-500 mt-1">Obs: {doc.observacao}</p>
                      )}
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon">
                      <Eye className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon">
                      <Download className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleDelete(doc.id)}
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

      {/* Modal de Upload */}
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
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  value={formData.tipo}
                  onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                >
                  {tiposDocumento.map((tipo) => (
                    <option key={tipo.value} value={tipo.value}>
                      {tipo.label}
                    </option>
                  ))}
                </select>
              </div>
              <div className="space-y-2">
                <Label htmlFor="nome">Nome do Documento</Label>
                <Input
                  id="nome"
                  value={formData.nome}
                  onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                  placeholder="Nome para identificacao"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="arquivo">Arquivo *</Label>
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
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
                      <p className="text-sm font-medium text-gray-900">{selectedFile.name}</p>
                    ) : (
                      <p className="text-sm text-gray-500">
                        Clique para selecionar ou arraste o arquivo
                      </p>
                    )}
                    <p className="text-xs text-gray-400 mt-1">
                      PDF, DOC, DOCX, JPG, PNG, ZIP (max. 10MB)
                    </p>
                  </label>
                </div>
              </div>
              <div className="flex justify-end gap-2 pt-4">
                <Button variant="outline" onClick={handleCloseModal}>
                  Cancelar
                </Button>
                <Button onClick={handleUpload}>
                  <Upload className="mr-2 h-4 w-4" />
                  Enviar
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
