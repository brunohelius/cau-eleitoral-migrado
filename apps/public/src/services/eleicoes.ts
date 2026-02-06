import api, { extractApiError } from './api'
import { StatusEleicao, TipoEleicao } from './types'

export { StatusEleicao, TipoEleicao }

// Interfaces
export interface EleicaoPublica {
  id: string
  nome: string
  descricao?: string
  tipo: TipoEleicao
  status: StatusEleicao
  ano: number
  regional?: string
  regionalNome?: string
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  dataApuracao?: string
  quantidadeVagas?: number
  totalChapas: number
  totalEleitores: number
  isVotacaoAberta: boolean
  diasParaVotacao?: number
  horarioVotacao?: {
    inicio: string
    fim: string
  }
  regrasEleicao?: string
  documentosPublicos?: {
    id: string
    nome: string
    tipo: string
    url: string
  }[]
}

export interface ChapaPublica {
  id: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  descricao?: string
  logoUrl?: string
  candidatos: {
    id: string
    nome: string
    cargo: string
    tipo: 'titular' | 'suplente'
    fotoUrl?: string
    curriculo?: string
  }[]
  propostas?: string
}

export interface CalendarioEleicao {
  fase: string
  descricao: string
  dataInicio: string
  dataFim: string
  status: 'pendente' | 'em_andamento' | 'concluido'
}

export interface ResultadoEleicao {
  id: string
  eleicaoId: string
  eleicaoNome: string
  statusApuracao: number
  homologado: boolean
  publicado: boolean
  dataApuracao?: string
  totalEleitores: number
  totalVotos: number
  votosValidos: number
  votosNulos: number
  votosBrancos: number
  votosAnulados: number
  totalAbstencoes: number
  percentualParticipacao: number
  percentualAbstencao: number
  resultadosChapas: {
    chapaId: string
    numero: number
    nome: string
    sigla?: string
    totalVotos: number
    percentual: number
    percentualVotosValidos: number
    posicao: number
    vencedora: boolean
  }[]
  chapaVencedoraId?: string
  chapaVencedoraNome?: string
  votosChapaVencedora?: number
}

export interface EstatisticasPublicas {
  totalEleitores: number
  votosComputados: number
  participacao: number
  ultimaAtualizacao: string
}

export const eleicoesPublicService = {
  // Get all elections (public - no auth required)
  getAll: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eleicoes')
    }
  },

  // Get active elections
  getAtivas: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao/ativas')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eleicoes ativas')
    }
  },

  // Get elections in voting phase (EmAndamento=2)
  getEmVotacao: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao/status/2')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eleicoes em votacao')
    }
  },

  // Get concluded elections (Finalizada=7)
  getConcluidas: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao/status/7')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eleicoes concluidas')
    }
  },

  // Get election by ID
  getById: async (id: string): Promise<EleicaoPublica> => {
    try {
      const response = await api.get<EleicaoPublica>(`/eleicao/${id}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter eleicao')
    }
  },

  // Get election chapas
  getChapas: async (eleicaoId: string): Promise<ChapaPublica[]> => {
    try {
      const response = await api.get<ChapaPublica[]>(`/chapa/eleicao/${eleicaoId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar chapas')
    }
  },

  // Get election calendar
  getCalendario: async (eleicaoId: string): Promise<CalendarioEleicao[]> => {
    try {
      const response = await api.get<CalendarioEleicao[]>(`/calendario/eleicao/${eleicaoId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar calendario')
    }
  },

  // Get election results (apuracao) - AllowAnonymous
  getResultado: async (eleicaoId: string): Promise<ResultadoEleicao> => {
    try {
      const response = await api.get<ResultadoEleicao>(`/apuracao/${eleicaoId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter resultados')
    }
  },

  // Get votes per chapa - AllowAnonymous
  getVotosPorChapa: async (eleicaoId: string) => {
    try {
      const response = await api.get(`/apuracao/${eleicaoId}/votos-por-chapa`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter votos por chapa')
    }
  },

  // Get winner info - AllowAnonymous
  getVencedor: async (eleicaoId: string) => {
    try {
      const response = await api.get(`/apuracao/${eleicaoId}/vencedor`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter vencedor')
    }
  },
}

// Status helpers
export const getStatusLabel = (status: StatusEleicao): string => {
  const labels: Record<StatusEleicao, string> = {
    [StatusEleicao.RASCUNHO]: 'Rascunho',
    [StatusEleicao.AGENDADA]: 'Agendada',
    [StatusEleicao.EM_ANDAMENTO]: 'Em Andamento',
    [StatusEleicao.ENCERRADA]: 'Encerrada',
    [StatusEleicao.SUSPENSA]: 'Suspensa',
    [StatusEleicao.CANCELADA]: 'Cancelada',
    [StatusEleicao.APURACAO_EM_ANDAMENTO]: 'Apuracao em Andamento',
    [StatusEleicao.FINALIZADA]: 'Finalizada',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusColor = (status: StatusEleicao): string => {
  const colors: Record<StatusEleicao, string> = {
    [StatusEleicao.RASCUNHO]: 'bg-gray-100 text-gray-800',
    [StatusEleicao.AGENDADA]: 'bg-blue-100 text-blue-800',
    [StatusEleicao.EM_ANDAMENTO]: 'bg-green-100 text-green-800',
    [StatusEleicao.ENCERRADA]: 'bg-orange-100 text-orange-800',
    [StatusEleicao.SUSPENSA]: 'bg-red-100 text-red-800',
    [StatusEleicao.CANCELADA]: 'bg-red-100 text-red-800',
    [StatusEleicao.APURACAO_EM_ANDAMENTO]: 'bg-purple-100 text-purple-800',
    [StatusEleicao.FINALIZADA]: 'bg-gray-100 text-gray-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

export const getTipoLabel = (tipo: TipoEleicao): string => {
  const labels: Record<TipoEleicao, string> = {
    [TipoEleicao.CAU_BR]: 'CAU/BR',
    [TipoEleicao.CAU_UF]: 'CAU/UF',
    [TipoEleicao.CONSELHEIRO_FEDERAL]: 'Conselheiro Federal',
    [TipoEleicao.CONSELHEIRO_ESTADUAL]: 'Conselheiro Estadual',
  }
  return labels[tipo] || 'Desconhecido'
}
