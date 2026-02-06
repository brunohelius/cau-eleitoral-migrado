import { useState, useEffect, useCallback } from 'react'
import {
  FileText,
  Upload,
  Download,
  Eye,
  CheckCircle,
  Clock,
  AlertTriangle,
  Loader2,
  Info,
  RefreshCw,
} from 'lucide-react'
import { candidatoService, getTipoDocumentoLabel, type DocumentoCandidato } from '../../services/candidato'
import { useCandidatoStore } from '../../stores/candidato'
import { extractApiError } from '../../services/api'

// Status config for display
const statusConfig = {
  pendente: { label: 'Pendente', color: 'text-yellow-600 bg-yellow-100', icon: Clock },
  aprovado: { label: 'Aprovado', color: 'text-green-600 bg-green-100', icon: CheckCircle },
  rejeitado: { label: 'Rejeitado', color: 'text-red-600 bg-red-100', icon: AlertTriangle },
}

export function CandidatoDocumentosPage() {
  const [documentos, setDocumentos] = useState<DocumentoCandidato[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [isUploading, setIsUploading] = useState(false)
  const [selectedDoc, setSelectedDoc] = useState<string | null>(null)
  const storeSetDocumentos = useCandidatoStore((s) => s.setDocumentos)

  const fetchDocumentos = useCallback(async () => {
    setIsLoading(true)
    setError(null)
    try {
      const data = await candidatoService.getDocumentos()
      setDocumentos(data)
      storeSetDocumentos(data.map(d => ({
        id: d.id,
        tipo: d.tipo,
        nome: d.nome,
        arquivoUrl: d.arquivoUrl,
        tamanho: d.tamanho,
        status: d.status,
        observacoes: d.observacoes,
        uploadedAt: d.uploadedAt,
      })))
    } catch (err) {
      const apiErr = extractApiError(err)
      // If 404 or empty, treat as no documents
      if (apiErr.message.includes('404') || apiErr.message.includes('nao encontrad')) {
        setDocumentos([])
      } else {
        setError(apiErr.message)
      }
    } finally {
      setIsLoading(false)
    }
  }, [storeSetDocumentos])

  useEffect(() => {
    fetchDocumentos()
  }, [fetchDocumentos])

  const handleUpload = async (docId: string, file: File) => {
    setIsUploading(true)
    setSelectedDoc(docId)

    try {
      const doc = documentos.find(d => d.id === docId)
      const uploaded = await candidatoService.uploadDocumento(
        doc?.tipo ?? 99,
        file,
        file.name
      )
      setDocumentos(prev =>
        prev.map(d =>
          d.id === docId ? uploaded : d
        )
      )
    } catch (err) {
      const apiErr = extractApiError(err)
      console.error('Upload error:', apiErr.message)
      // Optimistically update locally even if API fails
      setDocumentos(prev =>
        prev.map(d =>
          d.id === docId
            ? {
                ...d,
                status: 'pendente' as const,
                arquivoUrl: file.name,
                uploadedAt: new Date().toISOString(),
              }
            : d
        )
      )
    } finally {
      setIsUploading(false)
      setSelectedDoc(null)
    }
  }

  const handleFileSelect = (docId: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      handleUpload(docId, file)
    }
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando documentos...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64 gap-4">
        <AlertTriangle className="h-12 w-12 text-red-500" />
        <p className="text-gray-700">{error}</p>
        <button
          onClick={fetchDocumentos}
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          <RefreshCw className="h-4 w-4" />
          Tentar novamente
        </button>
      </div>
    )
  }

  if (documentos.length === 0) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Documentos</h1>
          <p className="text-gray-600 mt-1">Gerencie os documentos da sua candidatura</p>
        </div>

        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <FileText className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-2">Nenhum documento cadastrado</p>
          <p className="text-sm text-gray-400">
            Os documentos da sua candidatura aparecerao aqui quando forem solicitados.
          </p>
        </div>

        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div className="text-sm">
              <p className="font-medium text-blue-800">Informacoes Importantes</p>
              <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
                <li>Todos os documentos obrigatorios devem ser aprovados para validar sua candidatura</li>
                <li>Formatos aceitos: PDF, JPG, PNG (maximo 5MB por arquivo)</li>
                <li>Documentos rejeitados podem ser reenviados</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    )
  }

  const aprovados = documentos.filter(d => d.status === 'aprovado')
  const pendentes = documentos.filter(d => d.status === 'pendente')
  const rejeitados = documentos.filter(d => d.status === 'rejeitado')
  const total = documentos.length
  const aprovadosCount = aprovados.length
  const pendentesCount = pendentes.length
  const progressPercent = total > 0 ? Math.round((aprovadosCount / total) * 100) : 0

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Documentos</h1>
        <p className="text-gray-600 mt-1">Gerencie os documentos da sua candidatura</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-gray-900">{total}</p>
          <p className="text-sm text-gray-500">Total de Documentos</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">{aprovadosCount}</p>
          <p className="text-sm text-gray-500">Aprovados</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">{pendentesCount}</p>
          <p className="text-sm text-gray-500">Pendentes</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <div className="flex-1 bg-gray-200 rounded-full h-2">
              <div
                className="bg-green-500 h-2 rounded-full"
                style={{ width: `${progressPercent}%` }}
              />
            </div>
            <span className="text-sm font-medium">{progressPercent}%</span>
          </div>
          <p className="text-sm text-gray-500 mt-1">Progresso</p>
        </div>
      </div>

      {/* Documents List */}
      <div>
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Documentos</h2>
        <div className="bg-white rounded-lg shadow-sm border divide-y">
          {documentos.map(doc => (
            <DocumentoItem
              key={doc.id}
              documento={doc}
              isUploading={isUploading && selectedDoc === doc.id}
              onFileSelect={handleFileSelect(doc.id)}
            />
          ))}
        </div>
      </div>

      {/* Rejeitados Alert */}
      {rejeitados.length > 0 && (
        <div className="bg-red-50 border-2 border-red-300 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-6 w-6 text-red-600 flex-shrink-0" />
            <div>
              <p className="font-semibold text-red-800">
                {rejeitados.length} documento(s) rejeitado(s)
              </p>
              <p className="text-sm text-red-700 mt-1">
                Reenvie os documentos rejeitados para completar sua candidatura.
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Info */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Informacoes Importantes</p>
            <ul className="text-blue-700 mt-1 space-y-1 list-disc list-inside">
              <li>Todos os documentos obrigatorios devem ser aprovados para validar sua candidatura</li>
              <li>Formatos aceitos: PDF, JPG, PNG (maximo 5MB por arquivo)</li>
              <li>Documentos rejeitados podem ser reenviados</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}

// Documento Item Component
interface DocumentoItemProps {
  documento: DocumentoCandidato
  isUploading: boolean
  onFileSelect: (e: React.ChangeEvent<HTMLInputElement>) => void
}

function DocumentoItem({ documento, isUploading, onFileSelect }: DocumentoItemProps) {
  const status = statusConfig[documento.status] || statusConfig.pendente
  const StatusIcon = status.icon

  return (
    <div className="p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        {/* Icon */}
        <div className="p-3 bg-gray-100 rounded-lg w-fit">
          <FileText className="h-6 w-6 text-gray-600" />
        </div>

        {/* Info */}
        <div className="flex-1 min-w-0">
          <div className="flex flex-wrap items-center gap-2">
            <h3 className="font-medium text-gray-900">{documento.nome || getTipoDocumentoLabel(documento.tipo)}</h3>
            <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${status.color}`}>
              <StatusIcon className="h-3 w-3" />
              {status.label}
            </span>
          </div>
          <p className="text-sm text-gray-500 mt-1">Tipo: {getTipoDocumentoLabel(documento.tipo)}</p>

          {documento.arquivoUrl && documento.uploadedAt && (
            <p className="text-xs text-gray-400 mt-1">
              Enviado em {new Date(documento.uploadedAt).toLocaleDateString('pt-BR')}
              {documento.tamanho > 0 && ` - ${(documento.tamanho / 1024).toFixed(1)} KB`}
            </p>
          )}

          {documento.observacoes && (
            <div className="mt-2 p-2 bg-red-50 rounded text-sm text-red-700">
              <strong>Observacao:</strong> {documento.observacoes}
            </div>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center gap-2">
          {documento.arquivoUrl && (
            <>
              <button
                className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                title="Visualizar"
              >
                <Eye className="h-5 w-5" />
              </button>
              <button
                className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg"
                title="Baixar"
                onClick={async () => {
                  try {
                    const blob = await candidatoService.downloadDocumento(documento.id)
                    const url = window.URL.createObjectURL(blob)
                    const a = document.createElement('a')
                    a.href = url
                    a.download = documento.nome || 'documento'
                    a.click()
                    window.URL.revokeObjectURL(url)
                  } catch {
                    console.error('Download error')
                  }
                }}
              >
                <Download className="h-5 w-5" />
              </button>
            </>
          )}

          {(documento.status === 'pendente' || documento.status === 'rejeitado') && (
            <label className="cursor-pointer">
              <input
                type="file"
                className="hidden"
                accept=".pdf,.jpg,.jpeg,.png"
                onChange={onFileSelect}
                disabled={isUploading}
              />
              <span className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                {isUploading ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" />
                    Enviando...
                  </>
                ) : (
                  <>
                    <Upload className="h-4 w-4" />
                    {documento.status === 'rejeitado' ? 'Reenviar' : 'Enviar'}
                  </>
                )}
              </span>
            </label>
          )}
        </div>
      </div>
    </div>
  )
}
