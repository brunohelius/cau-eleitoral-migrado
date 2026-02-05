import { useState } from 'react'
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
} from 'lucide-react'

// Types
interface Documento {
  id: string
  titulo: string
  descricao: string
  categoria: 'edital' | 'regulamento' | 'resolucao' | 'ata' | 'manual' | 'formulario' | 'modelo' | 'outro'
  tipo: 'pdf' | 'doc' | 'xls' | 'zip'
  tamanho: string
  dataPublicacao: string
  eleicaoId?: string
  eleicaoNome?: string
  downloads: number
  url: string
}

// Mock data
const mockDocumentos: Documento[] = [
  {
    id: '1',
    titulo: 'Edital de Convocacao - Eleicao Ordinaria 2024',
    descricao: 'Edital de convocacao para as eleicoes ordinarias do CAU/SP 2024',
    categoria: 'edital',
    tipo: 'pdf',
    tamanho: '245 KB',
    dataPublicacao: '2024-01-15',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    downloads: 1234,
    url: '/documentos/edital-2024.pdf',
  },
  {
    id: '2',
    titulo: 'Regulamento Eleitoral 2024',
    descricao: 'Regulamento completo do processo eleitoral com todas as normas e procedimentos',
    categoria: 'regulamento',
    tipo: 'pdf',
    tamanho: '512 KB',
    dataPublicacao: '2024-01-10',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria 2024',
    downloads: 856,
    url: '/documentos/regulamento-2024.pdf',
  },
  {
    id: '3',
    titulo: 'Resolucao CAU/BR n 123/2024',
    descricao: 'Resolucao que estabelece as diretrizes para o processo eleitoral',
    categoria: 'resolucao',
    tipo: 'pdf',
    tamanho: '156 KB',
    dataPublicacao: '2024-01-05',
    downloads: 432,
    url: '/documentos/resolucao-123-2024.pdf',
  },
  {
    id: '4',
    titulo: 'Ata da Reuniao da Comissao Eleitoral - 10/03/2024',
    descricao: 'Ata da reuniao ordinaria da Comissao Eleitoral',
    categoria: 'ata',
    tipo: 'pdf',
    tamanho: '89 KB',
    dataPublicacao: '2024-03-10',
    downloads: 234,
    url: '/documentos/ata-comissao-10-03.pdf',
  },
  {
    id: '5',
    titulo: 'Manual do Eleitor - Votacao Online',
    descricao: 'Guia passo a passo para realizar a votacao no sistema online',
    categoria: 'manual',
    tipo: 'pdf',
    tamanho: '1.2 MB',
    dataPublicacao: '2024-03-01',
    downloads: 2567,
    url: '/documentos/manual-eleitor.pdf',
  },
  {
    id: '6',
    titulo: 'Formulario de Inscricao de Chapa',
    descricao: 'Formulario oficial para inscricao de chapas no processo eleitoral',
    categoria: 'formulario',
    tipo: 'doc',
    tamanho: '78 KB',
    dataPublicacao: '2024-01-15',
    downloads: 456,
    url: '/documentos/formulario-inscricao.docx',
  },
  {
    id: '7',
    titulo: 'Modelo de Plataforma Eleitoral',
    descricao: 'Template para elaboracao da plataforma eleitoral das chapas',
    categoria: 'modelo',
    tipo: 'doc',
    tamanho: '95 KB',
    dataPublicacao: '2024-01-15',
    downloads: 321,
    url: '/documentos/modelo-plataforma.docx',
  },
  {
    id: '8',
    titulo: 'Planilha de Eleitores por Regional',
    descricao: 'Estatisticas de eleitores aptos por regional',
    categoria: 'outro',
    tipo: 'xls',
    tamanho: '234 KB',
    dataPublicacao: '2024-03-14',
    downloads: 567,
    url: '/documentos/eleitores-regional.xlsx',
  },
]

const categoriaConfig = {
  edital: { label: 'Edital', color: 'bg-blue-100 text-blue-800' },
  regulamento: { label: 'Regulamento', color: 'bg-green-100 text-green-800' },
  resolucao: { label: 'Resolucao', color: 'bg-purple-100 text-purple-800' },
  ata: { label: 'Ata', color: 'bg-yellow-100 text-yellow-800' },
  manual: { label: 'Manual', color: 'bg-pink-100 text-pink-800' },
  formulario: { label: 'Formulario', color: 'bg-orange-100 text-orange-800' },
  modelo: { label: 'Modelo', color: 'bg-teal-100 text-teal-800' },
  outro: { label: 'Outro', color: 'bg-gray-100 text-gray-800' },
}

const tipoIconConfig = {
  pdf: { icon: FileText, color: 'text-red-500' },
  doc: { icon: File, color: 'text-blue-500' },
  xls: { icon: File, color: 'text-green-500' },
  zip: { icon: FolderOpen, color: 'text-yellow-500' },
}

export function DocumentosPage() {
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedCategoria, setSelectedCategoria] = useState<string | null>(null)
  const [sortBy, setSortBy] = useState<'data' | 'downloads' | 'titulo'>('data')
  const [isLoading] = useState(false)

  // Filter and sort documents
  const filteredDocumentos = mockDocumentos
    .filter(doc => {
      const matchesSearch = doc.titulo.toLowerCase().includes(searchTerm.toLowerCase()) ||
        doc.descricao.toLowerCase().includes(searchTerm.toLowerCase())
      const matchesCategoria = !selectedCategoria || doc.categoria === selectedCategoria
      return matchesSearch && matchesCategoria
    })
    .sort((a, b) => {
      if (sortBy === 'data') {
        return new Date(b.dataPublicacao).getTime() - new Date(a.dataPublicacao).getTime()
      }
      if (sortBy === 'downloads') {
        return b.downloads - a.downloads
      }
      return a.titulo.localeCompare(b.titulo)
    })

  // Group by category for sidebar
  const categoryCounts = mockDocumentos.reduce((acc, doc) => {
    acc[doc.categoria] = (acc[doc.categoria] || 0) + 1
    return acc
  }, {} as Record<string, number>)

  const handleDownload = (doc: Documento) => {
    // In a real app, track download and redirect
    window.open(doc.url, '_blank')
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando documentos...</span>
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
              value={selectedCategoria || ''}
              onChange={(e) => setSelectedCategoria(e.target.value || null)}
              className="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option value="">Todas as categorias</option>
              {Object.entries(categoriaConfig).map(([key, config]) => (
                <option key={key} value={key}>
                  {config.label} ({categoryCounts[key] || 0})
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
              <option value="downloads">Mais baixados</option>
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
          {(searchTerm || selectedCategoria) && (
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
            const categoria = categoriaConfig[doc.categoria]
            const tipo = tipoIconConfig[doc.tipo]
            const TipoIcon = tipo.icon

            return (
              <div key={doc.id} className="p-4 sm:p-6 hover:bg-gray-50 transition-colors">
                <div className="flex items-start gap-4">
                  {/* Icon */}
                  <div className="p-3 bg-gray-100 rounded-lg flex-shrink-0">
                    <TipoIcon className={`h-6 w-6 ${tipo.color}`} />
                  </div>

                  {/* Content */}
                  <div className="flex-1 min-w-0">
                    <div className="flex flex-wrap items-center gap-2 mb-1">
                      <span className={`px-2 py-0.5 text-xs font-medium rounded ${categoria.color}`}>
                        {categoria.label}
                      </span>
                      {doc.eleicaoNome && (
                        <span className="text-xs text-gray-500">
                          {doc.eleicaoNome}
                        </span>
                      )}
                    </div>

                    <h3 className="font-semibold text-gray-900 mb-1">
                      {doc.titulo}
                    </h3>
                    <p className="text-sm text-gray-600 line-clamp-2">
                      {doc.descricao}
                    </p>

                    <div className="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                      <span className="flex items-center gap-1">
                        <Calendar className="h-4 w-4" />
                        {new Date(doc.dataPublicacao).toLocaleDateString('pt-BR')}
                      </span>
                      <span className="uppercase">{doc.tipo}</span>
                      <span>{doc.tamanho}</span>
                      <span className="flex items-center gap-1">
                        <Download className="h-4 w-4" />
                        {doc.downloads.toLocaleString()} downloads
                      </span>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 flex-shrink-0">
                    <a
                      href={doc.url}
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
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      )}

      {/* Quick Links */}
      <div className="bg-white p-6 rounded-lg shadow-sm border">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Documentos em Destaque</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {mockDocumentos.filter(d => ['edital', 'regulamento', 'manual'].includes(d.categoria)).slice(0, 4).map(doc => (
            <a
              key={doc.id}
              href={doc.url}
              target="_blank"
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
    </div>
  )
}
