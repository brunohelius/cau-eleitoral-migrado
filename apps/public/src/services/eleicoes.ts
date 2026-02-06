import api, { extractApiError } from './api'

// Enums
export enum StatusEleicao {
  CRIADA = 0,
  ABERTA_INSCRICOES = 1,
  INSCRICOES_ENCERRADAS = 2,
  EM_VOTACAO = 3,
  VOTACAO_ENCERRADA = 4,
  EM_APURACAO = 5,
  CONCLUIDA = 6,
  SUSPENSA = 7,
  CANCELADA = 8,
}

export enum TipoEleicao {
  CAU_BR = 0,
  CAU_UF = 1,
  CONSELHEIRO_FEDERAL = 2,
  CONSELHEIRO_ESTADUAL = 3,
}

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

  // Get elections in voting phase (status=3)
  getEmVotacao: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao/status/3')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eleicoes em votacao')
    }
  },

  // Get concluded elections (status=6)
  getConcluidas: async (): Promise<EleicaoPublica[]> => {
    try {
      const response = await api.get<EleicaoPublica[]>('/eleicao/status/6')
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
    [StatusEleicao.CRIADA]: 'Criada',
    [StatusEleicao.ABERTA_INSCRICOES]: 'Inscricoes Abertas',
    [StatusEleicao.INSCRICOES_ENCERRADAS]: 'Inscricoes Encerradas',
    [StatusEleicao.EM_VOTACAO]: 'Em Votacao',
    [StatusEleicao.VOTACAO_ENCERRADA]: 'Votacao Encerrada',
    [StatusEleicao.EM_APURACAO]: 'Em Apuracao',
    [StatusEleicao.CONCLUIDA]: 'Concluida',
    [StatusEleicao.SUSPENSA]: 'Suspensa',
    [StatusEleicao.CANCELADA]: 'Cancelada',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusColor = (status: StatusEleicao): string => {
  const colors: Record<StatusEleicao, string> = {
    [StatusEleicao.CRIADA]: 'bg-gray-100 text-gray-800',
    [StatusEleicao.ABERTA_INSCRICOES]: 'bg-blue-100 text-blue-800',
    [StatusEleicao.INSCRICOES_ENCERRADAS]: 'bg-yellow-100 text-yellow-800',
    [StatusEleicao.EM_VOTACAO]: 'bg-green-100 text-green-800',
    [StatusEleicao.VOTACAO_ENCERRADA]: 'bg-orange-100 text-orange-800',
    [StatusEleicao.EM_APURACAO]: 'bg-purple-100 text-purple-800',
    [StatusEleicao.CONCLUIDA]: 'bg-gray-100 text-gray-800',
    [StatusEleicao.SUSPENSA]: 'bg-red-100 text-red-800',
    [StatusEleicao.CANCELADA]: 'bg-red-100 text-red-800',
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
