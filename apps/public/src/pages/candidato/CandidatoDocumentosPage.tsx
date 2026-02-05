import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface Documento {
  id: string
  nome: string
  tipo: 'obrigatorio' | 'complementar'
  categoria: string
  status: 'enviado' | 'pendente' | 'aprovado' | 'rejeitado'
  arquivo?: string
  dataEnvio?: string
  observacao?: string
}

// Mock data
const mockDocumentos: Documento[] = [
  {
    id: '1',
    nome: 'Documento de Identidade (RG)',
    tipo: 'obrigatorio',
    categoria: 'Identificacao',
    status: 'aprovado',
    arquivo: 'rg-maria-santos.pdf',
    dataEnvio: '2024-02-10',
  },
  {
    id: '2',
    nome: 'CPF',
    tipo: 'obrigatorio',
    categoria: 'Identificacao',
    status: 'aprovado',
    arquivo: 'cpf-maria-santos.pdf',
    dataEnvio: '2024-02-10',
  },
  {
    id: '3',
    nome: 'Comprovante de Registro CAU',
    tipo: 'obrigatorio',
    categoria: 'Profissional',
    status: 'aprovado',
    arquivo: 'registro-cau.pdf',
    dataEnvio: '2024-02-10',
  },
  {
    id: '4',
    nome: 'Certidao de Quitacao Eleitoral',
    tipo: 'obrigatorio',
    categoria: 'Eleitoral',
    status: 'aprovado',
    arquivo: 'quitacao-eleitoral.pdf',
    dataEnvio: '2024-02-11',
  },
  {
    id: '5',
    nome: 'Certidao Negativa de Debitos',
    tipo: 'obrigatorio',
    categoria: 'Fiscal',
    status: 'pendente',
  },
  {
    id: '6',
    nome: 'Declaracao de Nao Impedimento',
    tipo: 'obrigatorio',
    categoria: 'Declaracoes',
    status: 'enviado',
    arquivo: 'declaracao-impedimento.pdf',
    dataEnvio: '2024-02-12',
  },
  {
    id: '7',
    nome: 'Curriculo Profissional',
    tipo: 'complementar',
    categoria: 'Complementar',
    status: 'aprovado',
    arquivo: 'curriculo.pdf',
    dataEnvio: '2024-02-10',
  },
  {
    id: '8',
    nome: 'Foto 3x4',
    tipo: 'obrigatorio',
    categoria: 'Identificacao',
    status: 'rejeitado',
    arquivo: 'foto-3x4.jpg',
    dataEnvio: '2024-02-10',
    observacao: 'Foto fora do padrao exigido. Envie uma foto com fundo branco.',
  },
]

const statusConfig = {
  enviado: { label: 'Em Analise', color: 'text-blue-600 bg-blue-100', icon: Clock },
  pendente: { label: 'Pendente', color: 'text-yellow-600 bg-yellow-100', icon: Clock },
  aprovado: { label: 'Aprovado', color: 'text-green-600 bg-green-100', icon: CheckCircle },
  rejeitado: { label: 'Rejeitado', color: 'text-red-600 bg-red-100', icon: AlertTriangle },
}

export function CandidatoDocumentosPage() {
  const [documentos, setDocumentos] = useState<Documento[]>(mockDocumentos)
  const [isUploading, setIsUploading] = useState(false)
  const [selectedDoc, setSelectedDoc] = useState<string | null>(null)

  const handleUpload = async (docId: string, file: File) => {
    setIsUploading(true)
    setSelectedDoc(docId)

    try {
      // Simulate upload
      await new Promise(resolve => setTimeout(resolve, 2000))

      setDocumentos(prev =>
        prev.map(d =>
          d.id === docId
            ? {
                ...d,
                status: 'enviado' as const,
                arquivo: file.name,
                dataEnvio: new Date().toISOString(),
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

  const obrigatorios = documentos.filter(d => d.tipo === 'obrigatorio')
  const complementares = documentos.filter(d => d.tipo === 'complementar')

  const totalObrigatorios = obrigatorios.length
  const aprovadosObrigatorios = obrigatorios.filter(d => d.status === 'aprovado').length
  const pendentesObrigatorios = obrigatorios.filter(d => d.status === 'pendente').length

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
          <p className="text-2xl font-bold text-gray-900">{totalObrigatorios}</p>
          <p className="text-sm text-gray-500">Documentos Obrigatorios</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-green-600">{aprovadosObrigatorios}</p>
          <p className="text-sm text-gray-500">Aprovados</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <p className="text-2xl font-bold text-yellow-600">{pendentesObrigatorios}</p>
          <p className="text-sm text-gray-500">Pendentes</p>
        </div>
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-2">
            <div className="flex-1 bg-gray-200 rounded-full h-2">
              <div
                className="bg-green-500 h-2 rounded-full"
                style={{ width: `${(aprovadosObrigatorios / totalObrigatorios) * 100}%` }}
              />
            </div>
            <span className="text-sm font-medium">{Math.round((aprovadosObrigatorios / totalObrigatorios) * 100)}%</span>
          </div>
          <p className="text-sm text-gray-500 mt-1">Progresso</p>
        </div>
      </div>

      {/* Obrigatorios */}
      <div>
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Documentos Obrigatorios</h2>
        <div className="bg-white rounded-lg shadow-sm border divide-y">
          {obrigatorios.map(doc => (
            <DocumentoItem
              key={doc.id}
              documento={doc}
              isUploading={isUploading && selectedDoc === doc.id}
              onFileSelect={handleFileSelect(doc.id)}
            />
          ))}
        </div>
      </div>

      {/* Complementares */}
      <div>
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Documentos Complementares</h2>
        <div className="bg-white rounded-lg shadow-sm border divide-y">
          {complementares.map(doc => (
            <DocumentoItem
              key={doc.id}
              documento={doc}
              isUploading={isUploading && selectedDoc === doc.id}
              onFileSelect={handleFileSelect(doc.id)}
            />
          ))}
        </div>
      </div>

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
  documento: Documento
  isUploading: boolean
  onFileSelect: (e: React.ChangeEvent<HTMLInputElement>) => void
}

function DocumentoItem({ documento, isUploading, onFileSelect }: DocumentoItemProps) {
  const status = statusConfig[documento.status]
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
            <h3 className="font-medium text-gray-900">{documento.nome}</h3>
            <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${status.color}`}>
              <StatusIcon className="h-3 w-3" />
              {status.label}
            </span>
          </div>
          <p className="text-sm text-gray-500 mt-1">Categoria: {documento.categoria}</p>

          {documento.arquivo && documento.dataEnvio && (
            <p className="text-xs text-gray-400 mt-1">
              Enviado em {new Date(documento.dataEnvio).toLocaleDateString('pt-BR')} - {documento.arquivo}
            </p>
          )}

          {documento.observacao && (
            <div className="mt-2 p-2 bg-red-50 rounded text-sm text-red-700">
              <strong>Observacao:</strong> {documento.observacao}
            </div>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center gap-2">
          {documento.arquivo && (
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
