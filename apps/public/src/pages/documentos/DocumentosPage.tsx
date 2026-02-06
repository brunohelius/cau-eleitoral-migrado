import { useState, useEffect } from 'react'
import {
  FileText,
  Download,
  Search,
  Filter,
  Calendar,
  Eye,
  FolderOpen,
  File,
  Loader2,
  ExternalLink,
  AlertCircle,
} from 'lucide-react'
import api from '../../services/api'

// ============================================
// Enums matching backend
// ============================================

export enum TipoDocumento {
  Edital = 0,
  Resolucao = 1,
  Normativa = 2,
  Portaria = 3,
  Deliberacao = 4,
  Ato = 5,
  Comunicado = 6,
  Aviso = 7,
  Convocacao = 8,
  Termo = 9,
  TermoPosse = 10,
  Diploma = 11,
  Certificado = 12,
  Declaracao = 13,
  Ata = 14,
  AtaApuracao = 15,
  BoletimUrna = 16,
  MapaVotacao = 17,
  Resultado = 18,
  Relatorio = 19,
  Modelo = 20,
  Template = 21,
  Outros = 99,
}

export enum CategoriaDocumento {
  Institucional = 0,
  Eleitoral = 1,
  Administrativo = 2,
  Legal = 3,
  Tecnico = 4,
}

export enum StatusDocumento {
  Rascunho = 0,
  EmRevisao = 1,
  Aprovado = 2,
  Publicado = 3,
  Revogado = 4,
  Arquivado = 5,
}

// ============================================
// DTO matching backend DocumentoDto
// ============================================

interface DocumentoDto {
  id: string
  eleicaoId: string | null
  eleicaoNome: string | null
  tipo: TipoDocumento
  tipoNome: string
  categoria: CategoriaDocumento
  categoriaNome: string
  status: StatusDocumento
  statusNome: string
  numero: string
  ano: number | null
  titulo: string
  ementa: string | null
  conteudo: string | null
  dataDocumento: string | null
  dataPublicacao: string | null
  dataVigencia: string | null
  dataRevogacao: string | null
  arquivoUrl: string | null
  arquivoNome: string | null
  arquivoTipo: string | null
  arquivoTamanho: number | null
  totalArquivos: number
  versao: number
  createdAt: string
}

// ============================================
// Display config maps
// ============================================

const tipoDocumentoLabels: Record<number, string> = {
  [TipoDocumento.Edital]: 'Edital',
  [TipoDocumento.Resolucao]: 'Resolucao',
  [TipoDocumento.Normativa]: 'Normativa',
  [TipoDocumento.Portaria]: 'Portaria',
  [TipoDocumento.Deliberacao]: 'Deliberacao',
  [TipoDocumento.Ato]: 'Ato',
  [TipoDocumento.Comunicado]: 'Comunicado',
  [TipoDocumento.Aviso]: 'Aviso',
  [TipoDocumento.Convocacao]: 'Convocacao',
  [TipoDocumento.Termo]: 'Termo',
  [TipoDocumento.TermoPosse]: 'Termo de Posse',
  [TipoDocumento.Diploma]: 'Diploma',
  [TipoDocumento.Certificado]: 'Certificado',
  [TipoDocumento.Declaracao]: 'Declaracao',
  [TipoDocumento.Ata]: 'Ata',
  [TipoDocumento.AtaApuracao]: 'Ata de Apuracao',
  [TipoDocumento.BoletimUrna]: 'Boletim de Urna',
  [TipoDocumento.MapaVotacao]: 'Mapa de Votacao',
  [TipoDocumento.Resultado]: 'Resultado',
  [TipoDocumento.Relatorio]: 'Relatorio',
  [TipoDocumento.Modelo]: 'Modelo',
  [TipoDocumento.Template]: 'Template',
  [TipoDocumento.Outros]: 'Outros',
}

const categoriaConfig: Record<number, { label: string; color: string }> = {
  [CategoriaDocumento.Institucional]: { label: 'Institucional', color: 'bg-blue-100 text-blue-800' },
  [CategoriaDocumento.Eleitoral]: { label: 'Eleitoral', color: 'bg-green-100 text-green-800' },
  [CategoriaDocumento.Administrativo]: { label: 'Administrativo', color: 'bg-purple-100 text-purple-800' },
  [CategoriaDocumento.Legal]: { label: 'Legal', color: 'bg-yellow-100 text-yellow-800' },
  [CategoriaDocumento.Tecnico]: { label: 'Tecnico', color: 'bg-orange-100 text-orange-800' },
}

// ============================================
// Helpers
// ============================================

function getTipoLabel(tipo: TipoDocumento): string {
  return tipoDocumentoLabels[tipo] || 'Outros'
}

function getCategoriaConfig(categoria: CategoriaDocumento) {
  return categoriaConfig[categoria] || { label: 'Outros', color: 'bg-gray-100 text-gray-800' }
}

function formatFileSize(bytes: number | null | undefined): string {
  if (bytes == null || bytes === 0) return '-'
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`
  if (bytes < 1024 * 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
  return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`
}

function getFileTypeIcon(arquivoTipo: string | null): { icon: typeof FileText; color: string } {
  if (!arquivoTipo) return { icon: FileText, color: 'text-gray-500' }
  const tipo = arquivoTipo.toLowerCase()
  if (tipo.includes('pdf')) return { icon: FileText, color: 'text-red-500' }
  if (tipo.includes('word') || tipo.includes('doc')) return { icon: File, color: 'text-blue-500' }
  if (tipo.includes('excel') || tipo.includes('sheet') || tipo.includes('xls')) return { icon: File, color: 'text-green-500' }
  if (tipo.includes('zip') || tipo.includes('rar') || tipo.includes('compressed')) return { icon: FolderOpen, color: 'text-yellow-500' }
  return { icon: FileText, color: 'text-gray-500' }
}

function getFileTypeLabel(arquivoTipo: string | null, arquivoNome: string | null): string {
  if (arquivoNome) {
    const ext = arquivoNome.split('.').pop()?.toUpperCase()
    if (ext) return ext
  }
  if (!arquivoTipo) return '-'
  if (arquivoTipo.includes('pdf')) return 'PDF'
  if (arquivoTipo.includes('word') || arquivoTipo.includes('doc')) return 'DOC'
  if (arquivoTipo.includes('excel') || arquivoTipo.includes('sheet')) return 'XLS'
  if (arquivoTipo.includes('zip')) return 'ZIP'
  return arquivoTipo.split('/').pop()?.toUpperCase() || '-'
}

function getDocumentDisplayDate(doc: DocumentoDto): string | null {
  return doc.dataPublicacao || doc.dataDocumento || null
}

// ============================================
// API service function
// ============================================

async function fetchDocumentos(): Promise<DocumentoDto[]> {
  const response = await api.get<DocumentoDto[]>('/documento')
  return response.data
}

// ============================================
// Component
// ============================================

export function DocumentosPage() {
  const [documentos, setDocumentos] = useState<DocumentoDto[]>([])
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCategoria, setSelectedCategoria] = useState<CategoriaDocumento | null>(null)
  const [sortBy, setSortBy] = useState<'data' | 'titulo'>('data')
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false

    async function loadDocumentos() {
      setIsLoading(true)
      setError(null)
      try {
        const data = await fetchDocumentos()
        if (!cancelled) {
          setDocumentos(data)
        }
      } catch (err) {
        if (!cancelled) {
          console.error('Erro ao carregar documentos:', err)
          setError('Nao foi possivel carregar os documentos. Tente novamente mais tarde.')
        }
      } finally {
        if (!cancelled) {
          setIsLoading(false)
        }
      }
    }

    loadDocumentos()

    return () => {
      cancelled = true
    }
  }, [])

  // Filter and sort documents
  const filteredDocumentos = documentos
    .filter(doc => {
      const matchesSearch =
        doc.titulo.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (doc.ementa || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
        (doc.numero || '').toLowerCase().includes(searchTerm.toLowerCase())
      const matchesCategoria = selectedCategoria === null || doc.categoria === selectedCategoria
      return matchesSearch && matchesCategoria
    })
    .sort((a, b) => {
      if (sortBy === 'data') {
        const dateA = getDocumentDisplayDate(a)
        const dateB = getDocumentDisplayDate(b)
        if (!dateA && !dateB) return 0
        if (!dateA) return 1
        if (!dateB) return -1
        return new Date(dateB).getTime() - new Date(dateA).getTime()
      }
      return a.titulo.localeCompare(b.titulo)
    })

  // Group by category for sidebar counts
  const categoryCounts = documentos.reduce((acc, doc) => {
    acc[doc.categoria] = (acc[doc.categoria] || 0) + 1
    return acc
  }, {} as Record<number, number>)

  // Published documents for the "Documentos em Destaque" section (Status = Publicado = 3)
  const publishedDocumentos = documentos.filter(doc => doc.status === StatusDocumento.Publicado)

  const handleDownload = (doc: DocumentoDto) => {
    if (doc.arquivoUrl) {
      window.open(doc.arquivoUrl, '_blank')
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
      <div className="flex flex-col items-center justify-center h-64 space-y-4">
        <AlertCircle className="h-12 w-12 text-red-400" />
        <p className="text-gray-600">{error}</p>
        <button
          onClick={() => window.location.reload()}
          className="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
        >
          Tentar novamente
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
          Documentos Publicos
        </h1>
        <p className="text-gray-600 mt-1">
          Acesse todos os documentos oficiais do processo eleitoral
        </p>
      </div>

      {/* Search and Filters */}
      <div className="bg-white p-4 rounded-lg shadow-sm border">
        <div className="flex flex-col lg:flex-row gap-4">
          {/* Search */}
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
            <input
              type="text"
              placeholder="Buscar documento..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>

          {/* Category Filter */}
          <div className="flex items-center gap-2">
            <Filter className="h-4 w-4 text-gray-500" />
            <select
              value={selectedCategoria ?? ''}
              onChange={(e) => setSelectedCategoria(e.target.value === '' ? null : Number(e.target.value) as CategoriaDocumento)}
              className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option value="">Todas as categorias</option>
              {Object.entries(categoriaConfig).map(([key, config]) => (
                <option key={key} value={key}>
                  {config.label} ({categoryCounts[Number(key)] || 0})
                </option>
              ))}
            </select>
          </div>

          {/* Sort */}
          <div className="flex items-center gap-2">
            <span className="text-sm text-gray-500">Ordenar:</span>
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value as typeof sortBy)}
              className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option value="data">Mais recentes</option>
              <option value="titulo">A-Z</option>
            </select>
          </div>
        </div>
      </div>

      {/* Results */}
      <div className="flex items-center justify-between text-sm text-gray-600">
        <span>{filteredDocumentos.length} documento(s) encontrado(s)</span>
      </div>

      {/* Documents List */}
      {filteredDocumentos.length === 0 ? (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <FolderOpen className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhum documento encontrado</p>
          {(searchTerm || selectedCategoria !== null) && (
            <button
              onClick={() => {
                setSearchTerm('')
                setSelectedCategoria(null)
              }}
              className="mt-2 text-primary hover:underline"
            >
              Limpar filtros
            </button>
          )}
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm border divide-y">
          {filteredDocumentos.map(doc => {
            const catConfig = getCategoriaConfig(doc.categoria)
            const fileIcon = getFileTypeIcon(doc.arquivoTipo)
            const TipoIcon = fileIcon.icon

            return (
              <div key={doc.id} className="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                <div className="flex items-start gap-4">
                  {/* Icon */}
                  <div className="p-3 bg-gray-100 rounded-lg flex-shrink-0">
                    <TipoIcon className={`h-6 w-6 ${fileIcon.color}`} />
                  </div>

                  {/* Content */}
                  <div className="flex-1 min-w-0">
                    <div className="flex flex-wrap items-center gap-2 mb-1">
                      <span className={`px-2 py-0.5 text-xs font-medium rounded ${catConfig.color}`}>
                        {catConfig.label}
                      </span>
                      <span className="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700">
                        {getTipoLabel(doc.tipo)}
                      </span>
                      {doc.eleicaoNome && (
                        <span className="text-xs text-gray-500">
                          {doc.eleicaoNome}
                        </span>
                      )}
                      {doc.numero && (
                        <span className="text-xs text-gray-400">
                          N. {doc.numero}{doc.ano ? `/${doc.ano}` : ''}
                        </span>
                      )}
                    </div>

                    <h3 className="font-semibold text-gray-900 mb-1">
                      {doc.titulo}
                    </h3>
                    {doc.ementa && (
                      <p className="text-sm text-gray-600 line-clamp-2">
                        {doc.ementa}
                      </p>
                    )}

                    <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                      {getDocumentDisplayDate(doc) && (
                        <span className="flex items-center gap-1">
                          <Calendar className="h-4 w-4" />
                          {new Date(getDocumentDisplayDate(doc)!).toLocaleDateString('pt-BR')}
                        </span>
                      )}
                      <span className="uppercase">
                        {getFileTypeLabel(doc.arquivoTipo, doc.arquivoNome)}
                      </span>
                      <span>{formatFileSize(doc.arquivoTamanho)}</span>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 flex-shrink-0">
                    {doc.arquivoUrl && (
                      <>
                        <a
                          href={doc.arquivoUrl}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                          title="Visualizar"
                        >
                          <Eye className="h-5 w-5" />
                        </a>
                        <button
                          onClick={() => handleDownload(doc)}
                          className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                          title="Baixar"
                        >
                          <Download className="h-5 w-5" />
                        </button>
                      </>
                    )}
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      )}

      {/* Documentos em Destaque - Published documents (Status = 3) */}
      {publishedDocumentos.length > 0 && (
        <div className="bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Documentos em Destaque</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {publishedDocumentos.slice(0, 4).map(doc => (
              <a
                key={doc.id}
                href={doc.arquivoUrl || '#'}
                target={doc.arquivoUrl ? '_blank' : undefined}
                rel="noopener noreferrer"
                className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group"
              >
                <FileText className="h-5 w-5 text-primary flex-shrink-0" />
                <span className="text-sm font-medium text-gray-700 group-hover:text-primary truncate">
                  {doc.titulo}
                </span>
                <ExternalLink className="h-4 w-4 text-gray-400 flex-shrink-0" />
              </a>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
