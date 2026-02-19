import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  Calendar,
  Users,
  Vote,
  Clock,
  MapPin,
  ChevronRight,
  FileText,
  Info,
  AlertCircle,
  AlertTriangle,
  Loader2,
} from 'lucide-react'
import {
  eleicoesPublicService,
  getStatusLabel,
  getStatusColor,
  getTipoLabel,
  StatusEleicao,
} from '@/services/eleicoes'

const faseStatusConfig = {
  pendente: { color: 'bg-gray-200', dotColor: 'bg-gray-400' },
  em_andamento: { color: 'bg-green-500', dotColor: 'bg-green-500' },
  concluido: { color: 'bg-primary', dotColor: 'bg-primary' },
}

export function EleicaoDetailPage() {
  const { id } = useParams<{ id: string }>()

  const { data: eleicao, isLoading, error } = useQuery({
    queryKey: ['eleicao-detail', id],
    queryFn: () => eleicoesPublicService.getById(id!),
    enabled: !!id,
  })

  const { data: fases } = useQuery({
    queryKey: ['eleicao-calendário', id],
    queryFn: () => eleicoesPublicService.getCalendario(id!),
    enabled: !!id,
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando eleicao...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar eleição</p>
        <p className="text-gray-500 text-sm">{(error as Error).message}</p>
        <Link
          to="/eleicoes"
          className="mt-4 text-primary hover:underline"
        >
          Voltar para lista de eleições
        </Link>
      </div>
    )
  }

  if (!eleicao) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <Info className="h-12 w-12 text-gray-400 mb-4" />
        <p className="text-gray-700 font-medium">Eleição não encontrada</p>
        <Link
          to="/eleicoes"
          className="mt-4 text-primary hover:underline"
        >
          Voltar para lista de eleições
        </Link>
      </div>
    )
  }

  const statusLabel = getStatusLabel(eleicao.status)
  const statusColor = getStatusColor(eleicao.status)
  const isVotacao = eleicao.status === StatusEleicao.EM_ANDAMENTO
  const isFinalizada = eleicao.status === StatusEleicao.FINALIZADA || eleicao.status === StatusEleicao.APURACAO_EM_ANDAMENTO

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        <Link
          to="/eleicoes"
          className="inline-flex items-center text-gray-600 hover:text-gray-900"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <div className="flex-1">
          <div className="flex flex-wrap items-center gap-3">
            <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
              {eleicao.nome}
            </h1>
            <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusColor}`}>
              {statusLabel}
            </span>
          </div>
          <p className="text-gray-600 mt-1 flex items-center gap-2">
            <MapPin className="h-4 w-4" />
            {eleicao.regionalNome || eleicao.regional || getTipoLabel(eleicao.tipo)}
          </p>
        </div>
        {isVotacao && (
          <Link
            to="/votacao"
            className="bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90 text-center"
          >
            Votar Agora
          </Link>
        )}
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <Users className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{eleicao.totalChapas}</p>
              <p className="text-sm text-gray-500">Chapas</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <Vote className="h-5 w-5 text-green-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{eleicao.totalEleitores.toLocaleString()}</p>
              <p className="text-sm text-gray-500">Eleitores</p>
            </div>
          </div>
        </div>

        {eleicao.quantidadeVagas && (
          <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-purple-100 rounded-lg">
                <Calendar className="h-5 w-5 text-purple-600" />
              </div>
              <div>
                <p className="text-2xl font-bold text-gray-900">{eleicao.quantidadeVagas}</p>
                <p className="text-sm text-gray-500">Vagas</p>
              </div>
            </div>
          </div>
        )}

        {eleicao.diasParaVotacao != null && eleicao.diasParaVotacao > 0 && (
          <div className="bg-white p-4 sm:p-6 rounded-lg shadow-sm border">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-yellow-100 rounded-lg">
                <Clock className="h-5 w-5 text-yellow-600" />
              </div>
              <div>
                <p className="text-2xl font-bold text-gray-900">{eleicao.diasParaVotacao}</p>
                <p className="text-sm text-gray-500">Dias para votação</p>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Description and Quick Links */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Description */}
        <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Sobre a Eleição</h2>
          <p className="text-gray-600 leading-relaxed">{eleicao.descricao || 'Sem descrição disponível.'}</p>

          <div className="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <h3 className="text-sm font-medium text-gray-500">Tipo de Eleição</h3>
              <p className="mt-1 text-gray-900">{getTipoLabel(eleicao.tipo)}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Ano</h3>
              <p className="mt-1 text-gray-900">{eleicao.ano}</p>
            </div>
            <div>
              <h3 className="text-sm font-medium text-gray-500">Período da Eleição</h3>
              <p className="mt-1 text-gray-900">
                {new Date(eleicao.dataInicio).toLocaleDateString('pt-BR')} a {new Date(eleicao.dataFim).toLocaleDateString('pt-BR')}
              </p>
            </div>
            {eleicao.dataVotacaoInicio && eleicao.dataVotacaoFim && (
              <div>
                <h3 className="text-sm font-medium text-gray-500">Período de Votação</h3>
                <p className="mt-1 text-gray-900">
                  {new Date(eleicao.dataVotacaoInicio).toLocaleDateString('pt-BR')} a {new Date(eleicao.dataVotacaoFim).toLocaleDateString('pt-BR')}
                </p>
              </div>
            )}
          </div>
        </div>

        {/* Quick Links */}
        <div className="bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Acesso Rapido</h2>
          <div className="space-y-3">
            <Link
              to={`/eleicoes/${eleicao.id}/chapas`}
              className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div className="flex items-center gap-3">
                <Users className="h-5 w-5 text-primary" />
                <span className="font-medium">Ver Chapas</span>
              </div>
              <ChevronRight className="h-5 w-5 text-gray-400" />
            </Link>

            {isFinalizada && (
              <Link
                to={`/eleicoes/${eleicao.id}/resultados`}
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <Vote className="h-5 w-5 text-primary" />
                  <span className="font-medium">Ver Resultados</span>
                </div>
                <ChevronRight className="h-5 w-5 text-gray-400" />
              </Link>
            )}

            {eleicao.documentosPublicos?.map((doc) => (
              <a
                key={doc.id}
                href={doc.url}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <FileText className="h-5 w-5 text-primary" />
                  <span className="font-medium">{doc.nome}</span>
                </div>
                <ChevronRight className="h-5 w-5 text-gray-400" />
              </a>
            ))}

            <Link
              to={`/denuncias/nova?eleicao=${eleicao.id}`}
              className="flex items-center justify-between p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
            >
              <div className="flex items-center gap-3">
                <AlertTriangle className="h-5 w-5 text-red-600" />
                <span className="font-medium text-red-700">Registrar Denuncia</span>
              </div>
              <ChevronRight className="h-5 w-5 text-red-400" />
            </Link>
          </div>
        </div>
      </div>

      {/* Timeline */}
      {fases && fases.length > 0 && (
        <div className="bg-white p-6 rounded-lg shadow-sm border">
          <h2 className="text-lg font-semibold text-gray-900 mb-6">Cronograma da Eleição</h2>
          <div className="space-y-4">
            {fases.map((fase, index) => {
              const config = faseStatusConfig[fase.status] || faseStatusConfig.pendente
              const isLast = index === fases.length - 1

              return (
                <div key={`${fase.fase}-${index}`} className="flex gap-4">
                  <div className="flex flex-col items-center">
                    <div className={`w-3 h-3 rounded-full ${config.dotColor}`} />
                    {!isLast && (
                      <div className={`w-0.5 flex-1 mt-1 ${
                        fase.status === 'concluido' ? 'bg-primary' : 'bg-gray-200'
                      }`} />
                    )}
                  </div>
                  <div className="flex-1 pb-4">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                      <p className={`font-medium ${
                        fase.status === 'em_andamento' ? 'text-green-600' : 'text-gray-900'
                      }`}>
                        {fase.fase}
                        {fase.status === 'em_andamento' && (
                          <span className="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                            Em andamento
                          </span>
                        )}
                      </p>
                      <p className="text-sm text-gray-500">
                        {new Date(fase.dataInicio).toLocaleDateString('pt-BR')}
                        {fase.dataInicio !== fase.dataFim && (
                          <> a {new Date(fase.dataFim).toLocaleDateString('pt-BR')}</>
                        )}
                      </p>
                    </div>
                    {fase.descricao && (
                      <p className="text-sm text-gray-500 mt-1">{fase.descricao}</p>
                    )}
                  </div>
                </div>
              )
            })}
          </div>
        </div>
      )}
    </div>
  )
}
