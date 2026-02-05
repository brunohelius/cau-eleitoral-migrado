import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Users,
  User,
  ChevronDown,
  ChevronUp,
  FileText,
  ExternalLink,
  Loader2,
  AlertCircle,
  Search,
} from 'lucide-react'

// Types
interface MembroChapa {
  id: string
  nome: string
  cargo: string
  cau: string
  foto?: string
}

interface Chapa {
  id: string
  numero: number
  nome: string
  slogan: string
  descricao: string
  foto?: string
  cor: string
  membros: MembroChapa[]
  plataforma?: string
}

// Mock data
const mockChapas: Chapa[] = [
  {
    id: '1',
    numero: 1,
    nome: 'Chapa Renovacao',
    slogan: 'Por uma arquitetura mais inclusiva',
    descricao: 'A Chapa Renovacao traz uma proposta de modernizacao e inclusao para o CAU, com foco em tecnologia, sustentabilidade e valorizacao profissional. Nossa equipe e composta por profissionais experientes e comprometidos com o futuro da arquitetura brasileira.',
    cor: 'blue',
    membros: [
      { id: '1', nome: 'Joao Silva', cargo: 'Presidente', cau: 'A12345-6' },
      { id: '2', nome: 'Maria Santos', cargo: 'Vice-Presidente', cau: 'A23456-7' },
      { id: '3', nome: 'Carlos Oliveira', cargo: 'Diretor Financeiro', cau: 'A34567-8' },
      { id: '4', nome: 'Ana Costa', cargo: 'Diretora Tecnica', cau: 'A45678-9' },
      { id: '5', nome: 'Pedro Lima', cargo: 'Conselheiro', cau: 'A56789-0' },
      { id: '6', nome: 'Julia Ferreira', cargo: 'Conselheira', cau: 'A67890-1' },
    ],
    plataforma: '/documentos/plataforma-chapa-1.pdf',
  },
  {
    id: '2',
    numero: 2,
    nome: 'Chapa Uniao',
    slogan: 'Unidos pela arquitetura',
    descricao: 'A Chapa Uniao representa a uniao de diferentes correntes de pensamento em prol de um CAU mais forte e representativo. Defendemos a valorizacao do profissional, a etica na profissao e a modernizacao dos servicos do conselho.',
    cor: 'green',
    membros: [
      { id: '7', nome: 'Roberto Almeida', cargo: 'Presidente', cau: 'A11111-1' },
      { id: '8', nome: 'Patricia Souza', cargo: 'Vice-Presidente', cau: 'A22222-2' },
      { id: '9', nome: 'Marcos Pereira', cargo: 'Diretor Financeiro', cau: 'A33333-3' },
      { id: '10', nome: 'Fernanda Lima', cargo: 'Diretora Tecnica', cau: 'A44444-4' },
    ],
    plataforma: '/documentos/plataforma-chapa-2.pdf',
  },
  {
    id: '3',
    numero: 3,
    nome: 'Chapa Futuro',
    slogan: 'Construindo o amanha',
    descricao: 'A Chapa Futuro traz uma visao inovadora para o CAU, com propostas voltadas para a digitalizacao dos servicos, educacao continuada e sustentabilidade. Acreditamos que a arquitetura do futuro se constroi hoje.',
    cor: 'purple',
    membros: [
      { id: '11', nome: 'Lucas Martins', cargo: 'Presidente', cau: 'A55555-5' },
      { id: '12', nome: 'Camila Rocha', cargo: 'Vice-Presidente', cau: 'A66666-6' },
      { id: '13', nome: 'Andre Santos', cargo: 'Diretor Financeiro', cau: 'A77777-7' },
      { id: '14', nome: 'Beatriz Costa', cargo: 'Diretora Tecnica', cau: 'A88888-8' },
      { id: '15', nome: 'Rafael Oliveira', cargo: 'Conselheiro', cau: 'A99999-9' },
    ],
  },
]

const colorConfig: Record<string, { bg: string; text: string; border: string; light: string }> = {
  blue: { bg: 'bg-blue-600', text: 'text-blue-600', border: 'border-blue-600', light: 'bg-blue-100' },
  green: { bg: 'bg-green-600', text: 'text-green-600', border: 'border-green-600', light: 'bg-green-100' },
  purple: { bg: 'bg-purple-600', text: 'text-purple-600', border: 'border-purple-600', light: 'bg-purple-100' },
  red: { bg: 'bg-red-600', text: 'text-red-600', border: 'border-red-600', light: 'bg-red-100' },
  yellow: { bg: 'bg-yellow-600', text: 'text-yellow-600', border: 'border-yellow-600', light: 'bg-yellow-100' },
}

export function EleicaoChapasPage() {
  const { id } = useParams<{ id: string }>()
  const [expandedChapa, setExpandedChapa] = useState<string | null>(null)
  const [searchTerm, setSearchTerm] = useState('')
  const [isLoading] = useState(false)
  const [error] = useState<string | null>(null)

  // In a real app, fetch from API
  const chapas = mockChapas
  const eleicaoNome = 'Eleicao Ordinaria 2024'

  const filteredChapas = chapas.filter(chapa =>
    chapa.nome.toLowerCase().includes(searchTerm.toLowerCase()) ||
    chapa.membros.some(m => m.nome.toLowerCase().includes(searchTerm.toLowerCase()))
  )

  const toggleChapa = (chapaId: string) => {
    setExpandedChapa(expandedChapa === chapaId ? null : chapaId)
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando chapas...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar chapas</p>
        <p className="text-gray-500 text-sm">{error}</p>
        <Link
          to={`/eleicoes/${id}`}
          className="mt-4 text-primary hover:underline"
        >
          Voltar para a eleicao
        </Link>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        <Link
          to={`/eleicoes/${id}`}
          className="inline-flex items-center text-gray-600 hover:text-gray-900"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <div className="flex-1">
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
            Chapas Registradas
          </h1>
          <p className="text-gray-600 mt-1">{eleicaoNome}</p>
        </div>
      </div>

      {/* Search */}
      <div className="relative max-w-md">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
        <input
          type="text"
          placeholder="Buscar chapa ou candidato..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
        />
      </div>

      {/* Stats */}
      <div className="flex items-center gap-2 text-sm text-gray-600">
        <Users className="h-4 w-4" />
        <span>{filteredChapas.length} chapas encontradas</span>
      </div>

      {/* Chapas List */}
      <div className="space-y-4">
        {filteredChapas.map((chapa) => {
          const colors = colorConfig[chapa.cor] || colorConfig.blue
          const isExpanded = expandedChapa === chapa.id

          return (
            <div
              key={chapa.id}
              className={`bg-white rounded-lg shadow-sm border-2 transition-colors ${
                isExpanded ? colors.border : 'border-transparent'
              }`}
            >
              {/* Chapa Header */}
              <button
                onClick={() => toggleChapa(chapa.id)}
                className="w-full p-6 text-left"
              >
                <div className="flex items-start gap-4">
                  {/* Number Badge */}
                  <div className={`w-16 h-16 ${colors.light} rounded-lg flex items-center justify-center flex-shrink-0`}>
                    <span className={`text-3xl font-bold ${colors.text}`}>{chapa.numero}</span>
                  </div>

                  {/* Info */}
                  <div className="flex-1 min-w-0">
                    <div className="flex flex-wrap items-center gap-2">
                      <h2 className="text-xl font-bold text-gray-900">{chapa.nome}</h2>
                    </div>
                    <p className="text-gray-600 italic mt-1">"{chapa.slogan}"</p>
                    <div className="flex items-center gap-4 mt-2 text-sm text-gray-500">
                      <span className="flex items-center gap-1">
                        <User className="h-4 w-4" />
                        {chapa.membros.length} membros
                      </span>
                    </div>
                  </div>

                  {/* Expand Icon */}
                  <div className="flex-shrink-0">
                    {isExpanded ? (
                      <ChevronUp className="h-6 w-6 text-gray-400" />
                    ) : (
                      <ChevronDown className="h-6 w-6 text-gray-400" />
                    )}
                  </div>
                </div>
              </button>

              {/* Expanded Content */}
              {isExpanded && (
                <div className="border-t px-6 pb-6">
                  {/* Description */}
                  <div className="py-4">
                    <h3 className="text-sm font-semibold text-gray-900 mb-2">Sobre a Chapa</h3>
                    <p className="text-gray-600 text-sm leading-relaxed">{chapa.descricao}</p>
                  </div>

                  {/* Members */}
                  <div className="py-4 border-t">
                    <h3 className="text-sm font-semibold text-gray-900 mb-4">Composicao da Chapa</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                      {chapa.membros.map((membro) => (
                        <div
                          key={membro.id}
                          className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                        >
                          <div className={`w-10 h-10 ${colors.light} rounded-full flex items-center justify-center flex-shrink-0`}>
                            <User className={`h-5 w-5 ${colors.text}`} />
                          </div>
                          <div className="min-w-0">
                            <p className="font-medium text-gray-900 truncate">{membro.nome}</p>
                            <p className="text-sm text-gray-500">{membro.cargo}</p>
                            <p className="text-xs text-gray-400">CAU: {membro.cau}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Actions */}
                  {chapa.plataforma && (
                    <div className="pt-4 border-t">
                      <a
                        href={chapa.plataforma}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={`inline-flex items-center gap-2 px-4 py-2 ${colors.bg} text-white rounded-lg hover:opacity-90 transition-opacity`}
                      >
                        <FileText className="h-4 w-4" />
                        Ver Plataforma Completa
                        <ExternalLink className="h-4 w-4" />
                      </a>
                    </div>
                  )}
                </div>
              )}
            </div>
          )
        })}
      </div>

      {filteredChapas.length === 0 && (
        <div className="text-center py-12">
          <Users className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhuma chapa encontrada</p>
          {searchTerm && (
            <button
              onClick={() => setSearchTerm('')}
              className="mt-2 text-primary hover:underline"
            >
              Limpar busca
            </button>
          )}
        </div>
      )}
    </div>
  )
}
