import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Users,
  User,
  FileText,
  ExternalLink,
  AlertCircle,
  Award,
  Building2,
  Calendar,
  CheckCircle,
  Mail,
  Phone,
  Share2,
  Bookmark,
  Download,
  Clipboard,
} from 'lucide-react'

import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Skeleton } from '@/components/ui/skeleton'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import {
  chapasService,
  colorConfig,
  getColorByNumber,
  getStatusConfig,
  getTipoMembroLabel,
  type ChapaDetalhada,
  type MembroChapa,
} from '@/services/chapas'
import { eleicoesPublicService, type EleicaoPublica } from '@/services/eleicoes'

// Extended member card with more details
interface ExtendedMembroCardProps {
  membro: MembroChapa
  colorClass: string
  isLeader?: boolean
}

function ExtendedMembroCard({ membro, colorClass, isLeader }: ExtendedMembroCardProps) {
  const colors = colorConfig[colorClass] || colorConfig.blue
  const initials = membro.nomeProfissional
    .split(' ')
    .map((n) => n[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()

  return (
    <Card className={`overflow-hidden ${isLeader ? 'border-2 ' + colors.border : ''}`}>
      <CardContent className="p-4">
        <div className="flex items-start gap-4">
          <Avatar className={`h-16 w-16 ${isLeader ? 'ring-2 ring-offset-2 ' + colors.border.replace('border-', 'ring-') : ''}`}>
            {membro.fotoUrl ? (
              <AvatarImage src={membro.fotoUrl} alt={membro.nomeProfissional} />
            ) : null}
            <AvatarFallback className={`${colors.light} ${colors.text} font-bold text-lg`}>
              {initials}
            </AvatarFallback>
          </Avatar>
          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2 flex-wrap">
              <h3 className="font-semibold text-gray-900">{membro.nomeProfissional}</h3>
              {isLeader && (
                <Award className={`h-4 w-4 ${colors.text}`} />
              )}
            </div>
            <p className={`text-sm font-medium ${colors.text}`}>
              {membro.cargo || membro.tipoMembroNome}
            </p>
            {membro.registroCAU && (
              <p className="text-xs text-gray-500 mt-1">
                Registro CAU: {membro.registroCAU}
              </p>
            )}
            {membro.curriculo && (
              <p className="text-sm text-gray-600 mt-2 line-clamp-3">
                {membro.curriculo}
              </p>
            )}
          </div>
        </div>
      </CardContent>
    </Card>
  )
}

// Skeleton for loading state
function DetailPageSkeleton() {
  return (
    <div className="space-y-6">
      {/* Header skeleton */}
      <div className="flex items-center gap-4">
        <Skeleton className="h-6 w-24" />
      </div>

      {/* Hero skeleton */}
      <Card>
        <CardHeader>
          <div className="flex items-start gap-6">
            <Skeleton className="w-24 h-24 rounded-xl" />
            <div className="flex-1 space-y-3">
              <Skeleton className="h-8 w-64" />
              <Skeleton className="h-5 w-48" />
              <Skeleton className="h-4 w-96" />
            </div>
          </div>
        </CardHeader>
      </Card>

      {/* Tabs skeleton */}
      <Skeleton className="h-10 w-full max-w-md" />

      {/* Content skeleton */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Skeleton className="h-40" />
        <Skeleton className="h-40" />
        <Skeleton className="h-40" />
        <Skeleton className="h-40" />
      </div>
    </div>
  )
}

// Markdown-like content renderer
function PropostaContent({ content }: { content: string }) {
  // Simple markdown-like rendering
  const lines = content.split('\n')

  return (
    <div className="prose prose-sm max-w-none">
      {lines.map((line, index) => {
        if (line.startsWith('## ')) {
          return (
            <h2 key={index} className="text-xl font-bold text-gray-900 mt-6 mb-3 first:mt-0">
              {line.slice(3)}
            </h2>
          )
        }
        if (line.startsWith('### ')) {
          return (
            <h3 key={index} className="text-lg font-semibold text-gray-800 mt-4 mb-2">
              {line.slice(4)}
            </h3>
          )
        }
        if (line.startsWith('- ')) {
          return (
            <li key={index} className="text-gray-600 ml-4 list-disc">
              {line.slice(2)}
            </li>
          )
        }
        if (line.trim() === '') {
          return <br key={index} />
        }
        return (
          <p key={index} className="text-gray-600 leading-relaxed">
            {line}
          </p>
        )
      })}
    </div>
  )
}

export function ChapaDetailPage() {
  const { id: eleicaoId, chapaId } = useParams<{ id: string; chapaId: string }>()
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [chapa, setChapa] = useState<ChapaDetalhada | null>(null)
  const [eleicao, setEleicao] = useState<EleicaoPublica | null>(null)
  const [copied, setCopied] = useState(false)

  // Fetch data on mount
  useEffect(() => {
    async function fetchData() {
      if (!eleicaoId || !chapaId) return

      setIsLoading(true)
      setError(null)

      try {
        // Fetch election info and chapa in parallel
        const [eleicaoData, chapaData] = await Promise.all([
          eleicoesPublicService.getById(eleicaoId).catch(() => null),
          chapasService.getById(chapaId),
        ])

        setEleicao(eleicaoData)

        if (chapaData) {
          setChapa(chapaData)
        } else {
          setError('Chapa nao encontrada')
        }
      } catch (err) {
        console.error('Error fetching chapa:', err)
        setError('Nao foi possivel carregar os detalhes da chapa')
      } finally {
        setIsLoading(false)
      }
    }

    fetchData()
  }, [eleicaoId, chapaId])

  // Copy link to clipboard
  const handleShare = async () => {
    try {
      await navigator.clipboard.writeText(window.location.href)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    } catch {
      console.error('Failed to copy')
    }
  }

  // Loading state
  if (isLoading) {
    return <DetailPageSkeleton />
  }

  // Error state with no data
  if (error && !chapa) {
    return (
      <div className="space-y-6">
        <Link
          to={`/eleicoes/${eleicaoId}/chapas`}
          className="inline-flex items-center text-gray-600 hover:text-gray-900"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar para chapas</span>
        </Link>

        <Card className="py-12">
          <div className="text-center">
            <AlertCircle className="h-12 w-12 text-red-500 mx-auto mb-4" />
            <h2 className="text-xl font-semibold text-gray-900 mb-2">
              Chapa nao encontrada
            </h2>
            <p className="text-gray-500 mb-6">{error}</p>
            <Link to={`/eleicoes/${eleicaoId}/chapas`}>
              <Button>Ver todas as chapas</Button>
            </Link>
          </div>
        </Card>
      </div>
    )
  }

  if (!chapa) return null

  const colorClass = chapa.cor || getColorByNumber(chapa.numero)
  const colors = colorConfig[colorClass] || colorConfig.blue
  const statusConfig = getStatusConfig(chapa.status)

  // Sort members
  const sortedMembros = [...chapa.membros].sort((a, b) => a.ordem - b.ordem)
  const presidente = sortedMembros.find((m) => m.tipoMembro === 0)
  const vicePresidente = sortedMembros.find((m) => m.tipoMembro === 1)
  const diretores = sortedMembros.filter((m) => m.tipoMembro === 2 || m.tipoMembro === 5 || m.tipoMembro === 6)
  const conselheiros = sortedMembros.filter((m) => m.tipoMembro === 3 || m.tipoMembro === 4)

  return (
    <div className="space-y-6">
      {/* Back link */}
      <div className="flex items-center justify-between">
        <Link
          to={`/eleicoes/${eleicaoId}/chapas`}
          className="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar para chapas</span>
        </Link>
        <div className="flex gap-2">
          <Button variant="outline" size="sm" onClick={handleShare}>
            {copied ? (
              <>
                <CheckCircle className="h-4 w-4 mr-1 text-green-600" />
                Copiado!
              </>
            ) : (
              <>
                <Share2 className="h-4 w-4 mr-1" />
                Compartilhar
              </>
            )}
          </Button>
        </div>
      </div>

      {/* Warning banner */}
      {error && chapa && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start gap-3">
          <AlertCircle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
          <p className="text-sm text-yellow-800">{error}</p>
        </div>
      )}

      {/* Hero Card */}
      <Card className="overflow-hidden">
        <div className={`h-2 ${colors.bg}`} />
        <CardHeader className="pb-4">
          <div className="flex flex-col md:flex-row items-start gap-6">
            {/* Number Badge */}
            <div
              className={`w-24 h-24 ${colors.light} rounded-xl flex items-center justify-center flex-shrink-0`}
            >
              <span className={`text-5xl font-bold ${colors.text}`}>{chapa.numero}</span>
            </div>

            {/* Info */}
            <div className="flex-1">
              <div className="flex flex-wrap items-center gap-3 mb-2">
                <CardTitle className="text-2xl md:text-3xl">{chapa.nome}</CardTitle>
                {chapa.sigla && (
                  <Badge variant="outline" className="font-mono text-base">
                    {chapa.sigla}
                  </Badge>
                )}
              </div>
              {chapa.lema && (
                <CardDescription className="text-lg italic mb-3">
                  "{chapa.lema}"
                </CardDescription>
              )}
              <div className="flex flex-wrap items-center gap-4 text-sm">
                <Badge className={`${statusConfig.bgColor} ${statusConfig.color} border-0`}>
                  {statusConfig.label}
                </Badge>
                <span className="flex items-center gap-1 text-gray-500">
                  <Users className="h-4 w-4" />
                  {chapa.totalMembros || chapa.membros.length} membros
                </span>
                {chapa.dataRegistro && (
                  <span className="flex items-center gap-1 text-gray-500">
                    <Calendar className="h-4 w-4" />
                    Registro: {new Date(chapa.dataRegistro).toLocaleDateString('pt-BR')}
                  </span>
                )}
              </div>
            </div>
          </div>
        </CardHeader>

        {/* Quick actions */}
        <CardContent className="border-t pt-4">
          <div className="flex flex-wrap gap-3">
            {chapa.plataformaUrl && (
              <a
                href={chapa.plataformaUrl}
                target="_blank"
                rel="noopener noreferrer"
              >
                <Button className={`${colors.bg} hover:opacity-90`}>
                  <Download className="h-4 w-4 mr-2" />
                  Baixar Plataforma (PDF)
                </Button>
              </a>
            )}
            <Button variant="outline">
              <Mail className="h-4 w-4 mr-2" />
              Contato
            </Button>
          </div>
        </CardContent>
      </Card>

      {/* Tabs for content sections */}
      <Tabs defaultValue="membros" className="space-y-6">
        <TabsList className="grid w-full grid-cols-2 md:w-auto md:inline-grid md:grid-cols-3">
          <TabsTrigger value="membros">
            <Users className="h-4 w-4 mr-2" />
            Membros
          </TabsTrigger>
          <TabsTrigger value="proposta">
            <FileText className="h-4 w-4 mr-2" />
            Proposta
          </TabsTrigger>
          <TabsTrigger value="sobre" className="hidden md:flex">
            <Building2 className="h-4 w-4 mr-2" />
            Sobre
          </TabsTrigger>
        </TabsList>

        {/* Members Tab */}
        <TabsContent value="membros" className="space-y-6">
          {/* Leadership */}
          <div>
            <h3 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <Award className={`h-5 w-5 ${colors.text}`} />
              Lideranca da Chapa
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {presidente && (
                <ExtendedMembroCard
                  membro={presidente}
                  colorClass={colorClass}
                  isLeader
                />
              )}
              {vicePresidente && (
                <ExtendedMembroCard
                  membro={vicePresidente}
                  colorClass={colorClass}
                  isLeader
                />
              )}
            </div>
          </div>

          {/* Directors */}
          {diretores.length > 0 && (
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Diretoria</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {diretores.map((membro) => (
                  <ExtendedMembroCard
                    key={membro.id}
                    membro={membro}
                    colorClass={colorClass}
                  />
                ))}
              </div>
            </div>
          )}

          {/* Councilors */}
          {conselheiros.length > 0 && (
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Conselheiros</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {conselheiros.map((membro) => (
                  <ExtendedMembroCard
                    key={membro.id}
                    membro={membro}
                    colorClass={colorClass}
                  />
                ))}
              </div>
            </div>
          )}
        </TabsContent>

        {/* Proposta Tab */}
        <TabsContent value="proposta">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className={`h-5 w-5 ${colors.text}`} />
                Plataforma e Propostas
              </CardTitle>
              <CardDescription>
                Conhoca as principais propostas e compromissos da {chapa.nome}
              </CardDescription>
            </CardHeader>
            <CardContent>
              {chapa.proposta ? (
                <PropostaContent content={chapa.proposta} />
              ) : chapa.descricao ? (
                <div className="text-gray-600 leading-relaxed">
                  <p>{chapa.descricao}</p>
                  <p className="mt-4 text-sm text-gray-500 italic">
                    Para mais detalhes, baixe o documento completo da plataforma.
                  </p>
                </div>
              ) : (
                <div className="text-center py-8">
                  <FileText className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                  <p className="text-gray-500 mb-4">
                    A plataforma detalhada ainda nao foi disponibilizada.
                  </p>
                  {chapa.plataformaUrl && (
                    <a
                      href={chapa.plataformaUrl}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <Button variant="outline">
                        <Download className="h-4 w-4 mr-2" />
                        Baixar PDF
                      </Button>
                    </a>
                  )}
                </div>
              )}

              {chapa.proposta && chapa.plataformaUrl && (
                <div className="mt-8 pt-6 border-t">
                  <a
                    href={chapa.plataformaUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <Button className={`${colors.bg} hover:opacity-90`}>
                      <Download className="h-4 w-4 mr-2" />
                      Baixar Plataforma Completa (PDF)
                      <ExternalLink className="h-3 w-3 ml-2" />
                    </Button>
                  </a>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* About Tab */}
        <TabsContent value="sobre">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* About the Chapa */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Building2 className={`h-5 w-5 ${colors.text}`} />
                  Sobre a Chapa
                </CardTitle>
              </CardHeader>
              <CardContent>
                {chapa.descricao ? (
                  <p className="text-gray-600 leading-relaxed">{chapa.descricao}</p>
                ) : (
                  <p className="text-gray-500 italic">Descrição nao disponível.</p>
                )}
              </CardContent>
            </Card>

            {/* Election Info */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Calendar className={`h-5 w-5 ${colors.text}`} />
                  Informacoes da Eleicao
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <p className="text-sm text-gray-500">Eleição</p>
                  <p className="font-medium text-gray-900">
                    {eleicao?.nome || chapa.eleicaoNome || 'Eleicao'}
                  </p>
                </div>
                {chapa.dataRegistro && (
                  <div>
                    <p className="text-sm text-gray-500">Data de Registro</p>
                    <p className="font-medium text-gray-900">
                      {new Date(chapa.dataRegistro).toLocaleDateString('pt-BR', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                      })}
                    </p>
                  </div>
                )}
                <div>
                  <p className="text-sm text-gray-500">Status</p>
                  <Badge className={`${statusConfig.bgColor} ${statusConfig.color} border-0 mt-1`}>
                    {statusConfig.label}
                  </Badge>
                </div>
                <div>
                  <p className="text-sm text-gray-500">Número na Cedula</p>
                  <p className="font-bold text-2xl text-gray-900">{chapa.numero}</p>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>

      {/* Contact/Support Section */}
      <Card className="bg-gray-50">
        <CardContent className="py-6">
          <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
              <h3 className="font-semibold text-gray-900 mb-1">
                Duvidas sobre esta chapa?
              </h3>
              <p className="text-sm text-gray-600">
                Entre em contato com a comissao eleitoral para mais informacoes.
              </p>
            </div>
            <div className="flex gap-3">
              <Button variant="outline" asChild>
                <a href="mailto:suporte@cau.org.br">
                  <Mail className="h-4 w-4 mr-2" />
                  Email
                </a>
              </Button>
              <Button variant="outline" asChild>
                <a href="tel:0800-000-0000">
                  <Phone className="h-4 w-4 mr-2" />
                  0800-000-0000
                </a>
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Navigation */}
      <div className="flex justify-between items-center pt-6 border-t">
        <Link to={`/eleicoes/${eleicaoId}/chapas`}>
          <Button variant="outline">
            <ArrowLeft className="h-4 w-4 mr-2" />
            Ver todas as chapas
          </Button>
        </Link>
        <Link to={`/eleicoes/${eleicaoId}`}>
          <Button variant="ghost">
            Voltar para a eleicao
          </Button>
        </Link>
      </div>
    </div>
  )
}
