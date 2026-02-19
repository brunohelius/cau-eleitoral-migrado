import api, { setTokenType, extractApiError } from './api'

// ============================================
// Enums (matching backend)
// ============================================

export const StatusChapa = {
  RASCUNHO: 0,
  PENDENTE_DOCUMENTOS: 1,
  AGUARDANDO_ANALISE: 2,
  EM_ANALISE: 3,
  DEFERIDA: 4,
  INDEFERIDA: 5,
  IMPUGNADA: 6,
  AGUARDANDO_JULGAMENTO: 7,
  REGISTRADA: 8,
  CANCELADA: 9,
  // Legacy compatibility
  RECURSO: 4,
  DEFERIDA_COM_RECURSO: 5,
  INDEFERIDA_COM_RECURSO: 6,
} as const

export const TipoMembro = {
  PRESIDENTE: 0,
  VICE_PRESIDENTE: 1,
  PRIMEIRO_SECRETARIO: 2,
  SEGUNDO_SECRETARIO: 3,
  PRIMEIRO_TESOUREIRO: 4,
  SEGUNDO_TESOUREIRO: 5,
  CONSELHEIRO_TITULAR: 6,
  CONSELHEIRO_SUPLENTE: 7,
  DELEGADO: 8,
  DELEGADO_SUPLENTE: 9,
  // Legacy compatibility
  DIRETOR: 2,
  SECRETARIO: 5,
  TESOUREIRO: 6,
} as const

export const StatusMembro = {
  PENDENTE: 0,
  AGUARDANDO_CONFIRMACAO: 1,
  CONFIRMADO: 2,
  RECUSADO: 3,
  SUBSTITUIDO: 4,
  INABILITADO: 5,
} as const

// ============================================
// Interfaces
// ============================================

export interface MembroChapa {
  id: string
  chapaId: string
  profissionalId: string
  nomeProfissional: string
  tipoMembro: number
  tipoMembroNome: string
  cargo?: string
  status: number
  statusNome: string
  ordem: number
  fotoUrl?: string
  curriculo?: string
  biografia?: string
  registroCAU?: string
  email?: string
  telefone?: string
}

export interface ChapaDetalhada {
  id: string
  eleicaoId: string
  eleicaoNome: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  status: number
  statusNome: string
  totalMembros: number
  membros: MembroChapa[]
  dataRegistro?: string
  criadoEm: string
  atualizadoEm?: string
  // Extended fields for public view
  descricao?: string
  proposta?: string
  propostas?: string
  plataformaEleitoral?: string
  plataformaUrl?: string
  logoUrl?: string
  cor?: string
}

export interface ChapaResumo {
  id: string
  eleicaoId?: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  status: number
  statusNome: string
  totalMembros: number
  logoUrl?: string
  cor?: string
  criadoEm?: string
}

export interface ChapaPublica {
  id: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  logoUrl?: string
  descricao?: string
  candidatos: CandidatoPublico[]
  propostas?: string
}

export interface CandidatoPublico {
  id: string
  nome: string
  cargo: string
  tipo: 'titular' | 'suplente'
  fotoUrl?: string
  curriculo?: string
  biografia?: string
}

export interface ChapaDocumento {
  id: string
  tipo: number
  tipoNome: string
  nome: string
  url: string
  tamanho: number
  status: number
  statusNome: string
  observacoes?: string
  uploadedAt: string
}

export interface CargoMembro {
  codigo: number
  nome: string
  descricao?: string
  principal: boolean
}

// ============================================
// Helper Functions
// ============================================

export const getStatusConfig = (status: number) => {
  const configs: Record<number, { label: string; color: string; bgColor: string }> = {
    [StatusChapa.RASCUNHO]: { label: 'Rascunho', color: 'text-gray-600', bgColor: 'bg-gray-100' },
    [StatusChapa.PENDENTE_DOCUMENTOS]: { label: 'Pendente Documentos', color: 'text-yellow-700', bgColor: 'bg-yellow-100' },
    [StatusChapa.AGUARDANDO_ANALISE]: { label: 'Aguardando Analise', color: 'text-blue-700', bgColor: 'bg-blue-100' },
    [StatusChapa.EM_ANALISE]: { label: 'Em Analise', color: 'text-blue-700', bgColor: 'bg-blue-100' },
    [StatusChapa.DEFERIDA]: { label: 'Deferida', color: 'text-green-700', bgColor: 'bg-green-100' },
    [StatusChapa.INDEFERIDA]: { label: 'Indeferida', color: 'text-red-700', bgColor: 'bg-red-100' },
    [StatusChapa.IMPUGNADA]: { label: 'Impugnada', color: 'text-orange-700', bgColor: 'bg-orange-100' },
    [StatusChapa.AGUARDANDO_JULGAMENTO]: { label: 'Aguardando Julgamento', color: 'text-purple-700', bgColor: 'bg-purple-100' },
    [StatusChapa.REGISTRADA]: { label: 'Registrada', color: 'text-green-700', bgColor: 'bg-green-100' },
    [StatusChapa.CANCELADA]: { label: 'Cancelada', color: 'text-red-700', bgColor: 'bg-red-100' },
  }
  return configs[status] || { label: 'Desconhecido', color: 'text-gray-600', bgColor: 'bg-gray-100' }
}

export const getStatusLabel = (status: number): string => {
  return getStatusConfig(status).label
}

export const getStatusColor = (status: number): string => {
  const colors: Record<number, string> = {
    [StatusChapa.RASCUNHO]: 'gray',
    [StatusChapa.PENDENTE_DOCUMENTOS]: 'yellow',
    [StatusChapa.AGUARDANDO_ANALISE]: 'blue',
    [StatusChapa.EM_ANALISE]: 'blue',
    [StatusChapa.DEFERIDA]: 'green',
    [StatusChapa.INDEFERIDA]: 'red',
    [StatusChapa.IMPUGNADA]: 'orange',
    [StatusChapa.AGUARDANDO_JULGAMENTO]: 'purple',
    [StatusChapa.REGISTRADA]: 'green',
    [StatusChapa.CANCELADA]: 'red',
  }
  return colors[status] || 'gray'
}

export const getTipoMembroLabel = (tipo: number): string => {
  const labels: Record<number, string> = {
    [TipoMembro.PRESIDENTE]: 'Presidente',
    [TipoMembro.VICE_PRESIDENTE]: 'Vice-Presidente',
    [TipoMembro.PRIMEIRO_SECRETARIO]: '1o Secretario',
    [TipoMembro.SEGUNDO_SECRETARIO]: '2o Secretario',
    [TipoMembro.PRIMEIRO_TESOUREIRO]: '1o Tesoureiro',
    [TipoMembro.SEGUNDO_TESOUREIRO]: '2o Tesoureiro',
    [TipoMembro.CONSELHEIRO_TITULAR]: 'Conselheiro Titular',
    [TipoMembro.CONSELHEIRO_SUPLENTE]: 'Conselheiro Suplente',
    [TipoMembro.DELEGADO]: 'Delegado',
    [TipoMembro.DELEGADO_SUPLENTE]: 'Delegado Suplente',
  }
  return labels[tipo] || 'Membro'
}

export const getStatusMembroLabel = (status: number): string => {
  const labels: Record<number, string> = {
    [StatusMembro.PENDENTE]: 'Pendente',
    [StatusMembro.AGUARDANDO_CONFIRMACAO]: 'Aguardando Confirmacao',
    [StatusMembro.CONFIRMADO]: 'Confirmado',
    [StatusMembro.RECUSADO]: 'Recusado',
    [StatusMembro.SUBSTITUIDO]: 'Substituido',
    [StatusMembro.INABILITADO]: 'Inabilitado',
  }
  return labels[status] || 'Desconhecido'
}

// Color configuration for chapa visual identity
export const colorConfig: Record<string, { bg: string; text: string; border: string; light: string }> = {
  blue: { bg: 'bg-blue-600', text: 'text-blue-600', border: 'border-blue-600', light: 'bg-blue-50' },
  green: { bg: 'bg-green-600', text: 'text-green-600', border: 'border-green-600', light: 'bg-green-50' },
  purple: { bg: 'bg-purple-600', text: 'text-purple-600', border: 'border-purple-600', light: 'bg-purple-50' },
  red: { bg: 'bg-red-600', text: 'text-red-600', border: 'border-red-600', light: 'bg-red-50' },
  yellow: { bg: 'bg-yellow-600', text: 'text-yellow-600', border: 'border-yellow-600', light: 'bg-yellow-50' },
  orange: { bg: 'bg-orange-600', text: 'text-orange-600', border: 'border-orange-600', light: 'bg-orange-50' },
  teal: { bg: 'bg-teal-600', text: 'text-teal-600', border: 'border-teal-600', light: 'bg-teal-50' },
  pink: { bg: 'bg-pink-600', text: 'text-pink-600', border: 'border-pink-600', light: 'bg-pink-50' },
  indigo: { bg: 'bg-indigo-600', text: 'text-indigo-600', border: 'border-indigo-600', light: 'bg-indigo-50' },
  cyan: { bg: 'bg-cyan-600', text: 'text-cyan-600', border: 'border-cyan-600', light: 'bg-cyan-50' },
}

// Get color by chapa number (deterministic)
export const getColorByNumber = (numero: number): string => {
  const colors = Object.keys(colorConfig)
  return colors[(numero - 1) % colors.length]
}

// ============================================
// Service API calls
// ============================================

export const chapasService = {
  // ========================================
  // Public Endpoints (AllowAnonymous)
  // ========================================

  /**
   * Lista todas as chapas (com filtro opcional por eleicao)
   */
  getAll: async (eleicaoId?: string): Promise<ChapaResumo[]> => {
    try {
      const params = eleicaoId ? `?eleicaoId=${eleicaoId}` : ''
      const response = await api.get<ChapaResumo[]>(`/chapa${params}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching chapas:', apiError.message)
      return []
    }
  },

  /**
   * Get all chapas for an election (public)
   */
  getByEleicao: async (eleicaoId: string): Promise<ChapaDetalhada[]> => {
    try {
      const response = await api.get<ChapaDetalhada[]>(`/chapa/eleicao/${eleicaoId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching chapas:', apiError.message)
      throw new Error(apiError.message || 'Erro ao carregar chapas')
    }
  },

  /**
   * Get a specific chapa by ID (public)
   */
  getById: async (chapaId: string): Promise<ChapaDetalhada | null> => {
    try {
      const response = await api.get<ChapaDetalhada>(`/chapa/${chapaId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching chapa:', apiError.message)
      throw new Error(apiError.message || 'Erro ao carregar chapa')
    }
  },

  /**
   * Get all approved chapas for an election (for voting)
   */
  getAprovadas: async (eleicaoId: string): Promise<ChapaDetalhada[]> => {
    try {
      const chapas = await chapasService.getByEleicao(eleicaoId)
      return chapas.filter(c =>
        c.status === StatusChapa.DEFERIDA ||
        c.status === StatusChapa.REGISTRADA
      )
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching approved chapas:', apiError.message)
      return []
    }
  },

  /**
   * Get chapas for voting (public endpoint)
   */
  getPublicasParaVotacao: async (eleicaoId: string): Promise<ChapaPublica[]> => {
    try {
      const response = await api.get<ChapaPublica[]>(`/public/eleicoes/${eleicaoId}/chapas`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching public chapas:', apiError.message)
      return []
    }
  },

  /**
   * Get public chapa details
   */
  getPublicaDetalhes: async (eleicaoId: string, chapaId: string): Promise<ChapaPublica | null> => {
    try {
      const response = await api.get<ChapaPublica>(`/public/eleicoes/${eleicaoId}/chapas/${chapaId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching public chapa details:', apiError.message)
      return null
    }
  },

  // ========================================
  // Membros Endpoints
  // ========================================

  /**
   * Lista membros de uma chapa
   */
  getMembros: async (chapaId: string): Promise<MembroChapa[]> => {
    try {
      const response = await api.get<MembroChapa[]>(`/membrochapa/chapa/${chapaId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching membros:', apiError.message)
      return []
    }
  },

  /**
   * Obtem um membro pelo ID
   */
  getMembroById: async (membroId: string): Promise<MembroChapa | null> => {
    try {
      const response = await api.get<MembroChapa>(`/membrochapa/${membroId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching membro:', apiError.message)
      return null
    }
  },

  /**
   * Lista cargos disponiveis
   */
  getCargos: async (): Promise<CargoMembro[]> => {
    try {
      const response = await api.get<CargoMembro[]>('/membrochapa/cargos')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching cargos:', apiError.message)
      return []
    }
  },

  // ========================================
  // Authenticated Endpoints (Candidate)
  // ========================================

  /**
   * Obtem a chapa do candidato autenticado
   */
  getMinhaChapa: async (): Promise<ChapaDetalhada | null> => {
    try {
      setTokenType('candidate')
      const response = await api.get<ChapaDetalhada>('/candidato/chapa')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      if (apiError.message?.includes('nao encontrad')) {
        return null
      }
      console.error('Error fetching my chapa:', apiError.message)
      return null
    }
  },

  /**
   * Lista participacoes do profissional
   */
  getParticipacoesProfissional: async (profissionalId: string): Promise<MembroChapa[]> => {
    try {
      setTokenType('candidate')
      const response = await api.get<MembroChapa[]>(`/membrochapa/profissional/${profissionalId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching participacoes:', apiError.message)
      return []
    }
  },

  // ========================================
  // Utility Methods
  // ========================================

  /**
   * Verifica se uma chapa esta apta para votacao
   */
  isAptaParaVotacao: (chapa: ChapaResumo | ChapaDetalhada): boolean => {
    return (
      chapa.status === StatusChapa.REGISTRADA ||
      chapa.status === StatusChapa.DEFERIDA
    )
  },

  /**
   * Ordena membros por cargo (ordem hierarquica)
   */
  ordenarMembrosPorCargo: (membros: MembroChapa[]): MembroChapa[] => {
    return [...membros].sort((a, b) => {
      if (a.tipoMembro !== b.tipoMembro) {
        return a.tipoMembro - b.tipoMembro
      }
      return a.ordem - b.ordem
    })
  },

  /**
   * Obtem o presidente da chapa
   */
  getPresidente: (membros: MembroChapa[]): MembroChapa | undefined => {
    return membros.find((m) => m.tipoMembro === TipoMembro.PRESIDENTE)
  },

  /**
   * Obtem o vice-presidente da chapa
   */
  getVicePresidente: (membros: MembroChapa[]): MembroChapa | undefined => {
    return membros.find((m) => m.tipoMembro === TipoMembro.VICE_PRESIDENTE)
  },
}

export default chapasService
