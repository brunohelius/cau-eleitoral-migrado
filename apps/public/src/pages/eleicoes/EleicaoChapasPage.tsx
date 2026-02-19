import { useState, useEffect } from 'react'
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
  Eye,
  Award,
  Building2,
  Phone,
  Mail,
} from 'lucide-react'

import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Skeleton } from '@/components/ui/skeleton'
import {
  chapasService,
  colorConfig,
  getColorByNumber,
  getStatusConfig,
  type ChapaDetalhada,
  StatusChapa,
} from '@/services/chapas'
import { eleicoesPublicService, type EleicaoPublica } from '@/services/eleicoes'

// Skeleton component for loading state
function ChapaCardSkeleton() {
  return (
    <Card className="overflow-hidden">
      <CardHeader className="pb-4">
        <div className="flex items-start gap-4">
          <Skeleton className="w-16 h-16 rounded-lg" />
          <div className="flex-1 space-y-2">
            <Skeleton className="h-6 w-48" />
            <Skeleton className="h-4 w-64" />
            <Skeleton className="h-4 w-32" />
          </div>
        </div>
      </CardHeader>
    </Card>
  )
}

// Member card component
interface MembroCardProps {
  membro: ChapaDetalhada['membros'][0]
  colorClass: string
}

function MembroCard({ membro, colorClass }: MembroCardProps) {
  const colors = colorConfig[colorClass] || colorConfig.blue
  const initials = membro.nomeProfissional
    .split(' ')
    .map((n) => n[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()

  return (
    <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
      <Avatar className="h-12 w-12">
        {membro.fotoUrl ? (
          <AvatarImage src={membro.fotoUrl} alt={membro.nomeProfissional} />
        ) : null}
        <AvatarFallback className={`${colors.light} ${colors.text} font-semibold`}>
          {initials}
        </AvatarFallback>
      </Avatar>
      <div className="flex-1 min-w-0">
        <p className="font-medium text-gray-900 truncate">{membro.nomeProfissional}</p>
        <p className="text-sm text-gray-600">{membro.cargo || membro.tipoMembroNome}</p>
        {membro.registroCAU && (
          <p className="text-xs text-gray-400">CAU: {membro.registroCAU}</p>
        )}
      </div>
      {membro.tipoMembro === 0 && (
        <span title="Presidente da Chapa"><Award className={`h-5 w-5 ${colors.text}`} /></span>
      )}
    </div>
  )
}

// Main chapa card component
interface ChapaCardProps {
  chapa: ChapaDetalhada
  isExpanded: boolean
  onToggle: () => void
  eleicaoId: string
}

function ChapaCard({ chapa, isExpanded, onToggle, eleicaoId }: ChapaCardProps) {
  const colorClass = chapa.cor || getColorByNumber(chapa.numero)
  const colors = colorConfig[colorClass] || colorConfig.blue
  const statusConfig = getStatusConfig(chapa.status)

  // Sort members by ordem
  const sortedMembros = [...chapa.membros].sort((a, b) => a.ordem - b.ordem)

  // Get president and vice president
  const presidente = sortedMembros.find((m) => m.tipoMembro === 0)
  const vicePresidente = sortedMembros.find((m) => m.tipoMembro === 1)
  const outrosMembros = sortedMembros.filter((m) => m.tipoMembro !== 0 && m.tipoMembro !== 1)

  return (
    <Card
      className={`overflow-hidden transition-all duration-200 ${
        isExpanded ? `ring-2 ring-offset-2 ${colors.border.replace('border-', 'ring-')}` : ''
      }`}
    >
      {/* Header - Always visible */}
      <button onClick={onToggle} className="w-full text-left">
        <CardHeader className="pb-4">
          <div className="flex items-start gap-4">
            {/* Number Badge */}
            <div
              className={`w-16 h-16 ${colors.light} rounded-lg flex items-center justify-center flex-shrink-0`}
            >
              <span className={`text-3xl font-bold ${colors.text}`}>{chapa.numero}</span>
            </div>

            {/* Info */}
            <div className="flex-1 min-w-0">
              <div className="flex flex-wrap items-center gap-2 mb-1">
                <h2 className="text-xl font-bold text-gray-900">{chapa.nome}</h2>
                {chapa.sigla && (
                  <Badge variant="outline" className="font-mono">
                    {chapa.sigla}
                  </Badge>
                )}
                <Badge className={`${statusConfig.bgColor} ${statusConfig.color} border-0`}>
                  {statusConfig.label}
                </Badge>
              </div>
              {chapa.lema && (
                <p className="text-gray-600 italic mb-2">"{chapa.lema}"</p>
              )}
              <div className="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                <span className="flex items-center gap-1">
                  <Users className="h-4 w-4" />
                  {chapa.totalMembros || chapa.membros.length} membros
                </span>
                {presidente && (
                  <span className="flex items-center gap-1">
                    <User className="h-4 w-4" />
                    Presidente: {presidente.nomeProfissional}
                  </span>
                )}
              </div>
            </div>

            {/* Expand Icon */}
            <div className="flex-shrink-0 pt-2">
              {isExpanded ? (
                <ChevronUp className="h-6 w-6 text-gray-400" />
              ) : (
                <ChevronDown className="h-6 w-6 text-gray-400" />
              )}
            </div>
          </div>
        </CardHeader>
      </button>

      {/* Expanded Content */}
      {isExpanded && (
        <CardContent className="border-t pt-6">
          {/* Description */}
          {chapa.descricao && (
            <div className="mb-6">
              <h3 className="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                <Building2 className="h-4 w-4" />
                Sobre a Chapa
              </h3>
              <p className="text-gray-600 text-sm leading-relaxed">{chapa.descricao}</p>
            </div>
          )}

          {/* Leadership (President and Vice) */}
          <div className="mb-6">
            <h3 className="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <Award className="h-4 w-4" />
              Lideranca
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {presidente && (
                <MembroCard membro={presidente} colorClass={colorClass} />
              )}
              {vicePresidente && (
                <MembroCard membro={vicePresidente} colorClass={colorClass} />
              )}
            </div>
          </div>

          {/* Other Members */}
          {outrosMembros.length > 0 && (
            <div className="mb-6">
              <h3 className="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <Users className="h-4 w-4" />
                Demais Membros
              </h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                {outrosMembros.map((membro) => (
                  <MembroCard key={membro.id} membro={membro} colorClass={colorClass} />
                ))}
              </div>
            </div>
          )}

          {/* Actions */}
          <div className="pt-4 border-t flex flex-wrap gap-3">
            <Link to={`/eleicoes/${eleicaoId}/chapas/${chapa.id}`}>
              <Button variant="default" className={`${colors.bg} hover:opacity-90`}>
                <Eye className="h-4 w-4 mr-2" />
                Ver Detalhes Completos
              </Button>
            </Link>
            {chapa.plataformaUrl && (
              <a
                href={chapa.plataformaUrl}
                target="_blank"
                rel="noopener noreferrer"
              >
                <Button variant="outline">
                  <FileText className="h-4 w-4 mr-2" />
                  Plataforma (PDF)
                  <ExternalLink className="h-3 w-3 ml-2" />
                </Button>
              </a>
            )}
          </div>
        </CardContent>
      )}
    </Card>
  )
}

// Main page component
export function EleicaoChapasPage() {
  const { id } = useParams<{ id: string }>()
  const [expandedChapa, setExpandedChapa] = useState<string | null>(null)
  const [searchTerm, setSearchTerm] = useState('')
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [chapas, setChapas] = useState<ChapaDetalhada[]>([])
  const [eleicao, setEleicao] = useState<EleicaoPublica | null>(null)

  // Fetch data on mount
  useEffect(() => {
    async function fetchData() {
      if (!id) return

      setIsLoading(true)
      setError(null)

      try {
        // Fetch election info and chapas in parallel
        const [eleicaoData, chapasData] = await Promise.all([
          eleicoesPublicService.getById(id).catch(() => null),
          chapasService.getByEleicao(id),
        ])

        setEleicao(eleicaoData)

        const approvedChapas = chapasData.filter(
          (c) =>
            c.status === StatusChapa.DEFERIDA ||
            c.status === StatusChapa.DEFERIDA_COM_RECURSO
        )
        setChapas(approvedChapas.length > 0 ? approvedChapas : chapasData)
      } catch (err) {
        console.error('Error fetching chapas:', err)
        setChapas([])
        setError('Nao foi possivel carregar os dados da API.')
      } finally {
        setIsLoading(false)
      }
    }

    fetchData()
  }, [id])

  // Filter chapas based on search
  const filteredChapas = chapas.filter(
    (chapa) =>
      chapa.nome.toLowerCase().includes(searchTerm.toLowerCase()) ||
      chapa.sigla?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      chapa.lema?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      chapa.membros.some((m) =>
        m.nomeProfissional.toLowerCase().includes(searchTerm.toLowerCase())
      )
  )

  const toggleChapa = (chapaId: string) => {
    setExpandedChapa(expandedChapa === chapaId ? null : chapaId)
  }

  // Loading state
  if (isLoading) {
    return (
      <div className="space-y-6">
        {/* Header skeleton */}
        <div className="flex flex-col sm:flex-row sm:items-center gap-4">
          <Skeleton className="h-6 w-24" />
          <div className="flex-1">
            <Skeleton className="h-8 w-64 mb-2" />
            <Skeleton className="h-5 w-48" />
          </div>
        </div>

        {/* Search skeleton */}
        <Skeleton className="h-10 w-full max-w-md" />

        {/* Cards skeleton */}
        <div className="space-y-4">
          <ChapaCardSkeleton />
          <ChapaCardSkeleton />
          <ChapaCardSkeleton />
        </div>
      </div>
    )
  }

  if (error && chapas.length === 0) {
    return (
      <div className="space-y-6">
        <div className="flex flex-col sm:flex-row sm:items-center gap-4">
          <Link
            to={`/eleicoes/${id}`}
            className="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors"
          >
            <ArrowLeft className="h-5 w-5 mr-1" />
            <span>Voltar</span>
          </Link>
          <div className="flex-1">
            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
              Chapas Registradas
            </h1>
            <p className="text-gray-600 mt-1">{eleicao?.nome || 'Eleicao'}</p>
          </div>
        </div>

        <Card className="py-12">
          <div className="text-center px-6">
            <AlertCircle className="h-12 w-12 text-red-500 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">
              Nao foi possivel carregar as chapas
            </h3>
            <p className="text-gray-500">{error}</p>
          </div>
        </Card>
      </div>
    )
  }

  // Error state (but still show data)
  const showWarning = error && chapas.length > 0

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        <Link
          to={`/eleicoes/${id}`}
          className="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <div className="flex-1">
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
            Chapas Registradas
          </h1>
          <p className="text-gray-600 mt-1">
            {eleicao?.nome || 'Eleicao'}
          </p>
        </div>
      </div>

      {/* Warning banner */}
      {showWarning && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
          <AlertCircle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
          <div>
            <p className="text-sm text-yellow-800">{error}</p>
          </div>
        </div>
      )}

      {/* Search and Stats */}
      <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        {/* Search */}
        <div className="relative w-full sm:w-80">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
          <input
            type="text"
            placeholder="Buscar chapa ou candidato..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
          />
        </div>

        {/* Stats */}
        <div className="flex items-center gap-4">
          <div className="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 px-3 py-1.5 rounded-full">
            <Users className="h-4 w-4" />
            <span>
              {filteredChapas.length} {filteredChapas.length === 1 ? 'chapa' : 'chapas'}
            </span>
          </div>
        </div>
      </div>

      {/* Chapas List */}
      <div className="space-y-4">
        {filteredChapas.map((chapa) => (
          <ChapaCard
            key={chapa.id}
            chapa={chapa}
            isExpanded={expandedChapa === chapa.id}
            onToggle={() => toggleChapa(chapa.id)}
            eleicaoId={id || ''}
          />
        ))}
      </div>

      {/* Empty state */}
      {filteredChapas.length === 0 && (
        <Card className="py-12">
          <div className="text-center">
            <Users className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">
              Nenhuma chapa encontrada
            </h3>
            <p className="text-gray-500 mb-4">
              {searchTerm
                ? 'Nenhuma chapa corresponde aos termos de busca.'
                : 'Ainda nao ha chapas registradas para esta eleicao.'}
            </p>
            {searchTerm && (
              <Button variant="outline" onClick={() => setSearchTerm('')}>
                Limpar busca
              </Button>
            )}
          </div>
        </Card>
      )}

      {/* Help section */}
      {filteredChapas.length > 0 && (
        <Card className="bg-blue-50 border-blue-200">
          <CardContent className="py-4">
            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div className="flex-1">
                <h3 className="font-medium text-blue-900 mb-1">Precisa de mais informações?</h3>
                <p className="text-sm text-blue-700">
                  Clique em "Ver Detalhes Completos" para conhecer a plataforma e propostas de cada chapa.
                </p>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" size="sm" asChild>
                  <a href="mailto:suporte@cau.org.br">
                    <Mail className="h-4 w-4 mr-1" />
                    Contato
                  </a>
                </Button>
                <Button variant="outline" size="sm" asChild>
                  <a href="tel:0800-000-0000">
                    <Phone className="h-4 w-4 mr-1" />
                    0800
                  </a>
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  )
}
