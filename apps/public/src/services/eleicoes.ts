import api from './api'

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
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
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
  eleicaoId: string
  eleicaoNome: string
  dataApuracao: string
  totalEleitores: number
  totalVotos: number
  participacao: number
  votosValidos: number
  votosNulos: number
  votosBrancos: number
  resultado: {
    posicao: number
    chapaId: string
    chapaNome: string
    chapaNumero: number
    votos: number
    percentual: number
    eleita: boolean
  }[]
  chapaVencedora?: {
    id: string
    nome: string
    numero: number
    votos: number
  }
}

export interface EstatisticasPublicas {
  totalEleitores: number
  votosComputados: number
  participacao: number
  ultimaAtualizacao: string
}

export const eleicoesPublicService = {
  // Get all public elections
  getAll: async (): Promise<EleicaoPublica[]> => {
    const response = await api.get<EleicaoPublica[]>('/public/eleicoes')
    return response.data
  },

  // Get active elections
  getAtivas: async (): Promise<EleicaoPublica[]> => {
    const response = await api.get<EleicaoPublica[]>('/public/eleicoes/ativas')
    return response.data
  },

  // Get elections in voting phase
  getEmVotacao: async (): Promise<EleicaoPublica[]> => {
    const response = await api.get<EleicaoPublica[]>('/public/eleicoes/em-votacao')
    return response.data
  },

  // Get concluded elections (with results)
  getConcluidas: async (): Promise<EleicaoPublica[]> => {
    const response = await api.get<EleicaoPublica[]>('/public/eleicoes/concluidas')
    return response.data
  },

  // Get election by ID
  getById: async (id: string): Promise<EleicaoPublica> => {
    const response = await api.get<EleicaoPublica>(`/public/eleicoes/${id}`)
    return response.data
  },

  // Get election chapas
  getChapas: async (eleicaoId: string): Promise<ChapaPublica[]> => {
    const response = await api.get<ChapaPublica[]>(`/public/eleicoes/${eleicaoId}/chapas`)
    return response.data
  },

  // Get specific chapa details
  getChapa: async (eleicaoId: string, chapaId: string): Promise<ChapaPublica> => {
    const response = await api.get<ChapaPublica>(`/public/eleicoes/${eleicaoId}/chapas/${chapaId}`)
    return response.data
  },

  // Get election calendar
  getCalendario: async (eleicaoId: string): Promise<CalendarioEleicao[]> => {
    const response = await api.get<CalendarioEleicao[]>(`/public/eleicoes/${eleicaoId}/calendario`)
    return response.data
  },

  // Get election results (only for concluded elections)
  getResultado: async (eleicaoId: string): Promise<ResultadoEleicao> => {
    const response = await api.get<ResultadoEleicao>(`/public/eleicoes/${eleicaoId}/resultado`)
    return response.data
  },

  // Get election documents
  getDocumentos: async (eleicaoId: string): Promise<{
    id: string
    nome: string
    tipo: string
    url: string
    tamanho: number
    dataPublicacao: string
  }[]> => {
    const response = await api.get(`/public/eleicoes/${eleicaoId}/documentos`)
    return response.data
  },

  // Download election document
  downloadDocumento: async (eleicaoId: string, documentoId: string): Promise<Blob> => {
    const response = await api.get(`/public/eleicoes/${eleicaoId}/documentos/${documentoId}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  // Get public statistics (for voting tracking)
  getEstatisticas: async (eleicaoId: string): Promise<EstatisticasPublicas> => {
    const response = await api.get<EstatisticasPublicas>(`/public/eleicoes/${eleicaoId}/estatisticas`)
    return response.data
  },

  // Check voter eligibility (before login)
  verificarEleitor: async (
    eleicaoId: string,
    cpf: string,
    registroCAU: string
  ): Promise<{
    encontrado: boolean
    podeVotar: boolean
    motivo?: string
    regional?: string
  }> => {
    const response = await api.post(`/public/eleicoes/${eleicaoId}/verificar-eleitor`, {
      cpf,
      registroCAU,
    })
    return response.data
  },

  // Get voting instructions
  getInstrucoes: async (eleicaoId: string): Promise<{
    titulo: string
    passos: { numero: number; descricao: string }[]
    avisos: string[]
    faq: { pergunta: string; resposta: string }[]
  }> => {
    const response = await api.get(`/public/eleicoes/${eleicaoId}/instrucoes`)
    return response.data
  },

  // Subscribe to election notifications
  inscreverNotificacoes: async (
    eleicaoId: string,
    email: string
  ): Promise<{ message: string }> => {
    const response = await api.post(`/public/eleicoes/${eleicaoId}/notificacoes`, { email })
    return response.data
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

export const getTipoLabel = (tipo: TipoEleicao): string => {
  const labels: Record<TipoEleicao, string> = {
    [TipoEleicao.CAU_BR]: 'CAU/BR',
    [TipoEleicao.CAU_UF]: 'CAU/UF',
    [TipoEleicao.CONSELHEIRO_FEDERAL]: 'Conselheiro Federal',
    [TipoEleicao.CONSELHEIRO_ESTADUAL]: 'Conselheiro Estadual',
  }
  return labels[tipo] || 'Desconhecido'
}
