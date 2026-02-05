import api, { setTokenType, extractApiError } from './api'
import { TipoImpugnacao, StatusImpugnacao, TipoAlegacao } from './types'

// ============================================
// Impugnacao Interfaces
// ============================================

export interface Impugnacao {
  id: string
  eleicaoId: string
  eleicaoNome: string
  impugnanteId: string
  impugnanteNome: string
  chapaId?: string
  chapaNome?: string
  chapaNumero?: number
  membroId?: string
  membroNome?: string
  tipo: TipoImpugnacao
  tipoNome: string
  status: StatusImpugnacao
  statusNome: string
  tipoAlegacao: TipoAlegacao
  tipoAlegacaoNome: string
  descricao: string
  fundamentacao?: string
  prazoAlegacoes?: string
  alegacoes?: string
  dataAlegacoes?: string
  prazoContraAlegacoes?: string
  contraAlegacoes?: string
  dataContraAlegacoes?: string
  parecer?: string
  decisao?: string
  dataJulgamento?: string
  protocolo?: string
  createdAt: string
  updatedAt?: string
}

export interface ImpugnacaoResumida {
  id: string
  eleicaoId: string
  eleicaoNome: string
  tipo: TipoImpugnacao
  tipoNome: string
  status: StatusImpugnacao
  statusNome: string
  descricao: string
  protocolo?: string
  createdAt: string
}

// ============================================
// Request DTOs
// ============================================

export interface CreateImpugnacaoRequest {
  eleicaoId: string
  chapaId?: string
  membroId?: string
  tipo: TipoImpugnacao
  tipoAlegacao: TipoAlegacao
  descricao: string
  fundamentacao?: string
}

export interface UpdateImpugnacaoRequest {
  tipoAlegacao?: TipoAlegacao
  descricao?: string
  fundamentacao?: string
}

export interface AlegacoesRequest {
  texto: string
  documentosIds?: string[]
}

export interface RecursoRequest {
  fundamentacao: string
  documentosIds?: string[]
}

// ============================================
// Helper Functions
// ============================================

export const getTipoImpugnacaoLabel = (tipo: TipoImpugnacao): string => {
  const labels: Record<TipoImpugnacao, string> = {
    [TipoImpugnacao.IMPUGNACAO_CHAPA]: 'Impugnacao de Chapa',
    [TipoImpugnacao.IMPUGNACAO_MEMBRO]: 'Impugnacao de Membro',
    [TipoImpugnacao.IMPUGNACAO_DOCUMENTO]: 'Impugnacao de Documento',
    [TipoImpugnacao.IMPUGNACAO_RESULTADO]: 'Impugnacao de Resultado',
  }
  return labels[tipo] || 'Desconhecido'
}

export const getStatusImpugnacaoLabel = (status: StatusImpugnacao): string => {
  const labels: Record<StatusImpugnacao, string> = {
    [StatusImpugnacao.RECEBIDA]: 'Recebida',
    [StatusImpugnacao.EM_ANALISE]: 'Em Analise',
    [StatusImpugnacao.AGUARDANDO_ALEGACOES]: 'Aguardando Alegacoes',
    [StatusImpugnacao.ALEGACOES_APRESENTADAS]: 'Alegacoes Apresentadas',
    [StatusImpugnacao.AGUARDANDO_CONTRA_ALEGACOES]: 'Aguardando Contra-Alegacoes',
    [StatusImpugnacao.CONTRA_ALEGACOES_APRESENTADAS]: 'Contra-Alegacoes Apresentadas',
    [StatusImpugnacao.AGUARDANDO_JULGAMENTO]: 'Aguardando Julgamento',
    [StatusImpugnacao.JULGADA]: 'Julgada',
    [StatusImpugnacao.PROCEDENTE]: 'Procedente',
    [StatusImpugnacao.IMPROCEDENTE]: 'Improcedente',
    [StatusImpugnacao.PARCIALMENTE_PROCEDENTE]: 'Parcialmente Procedente',
    [StatusImpugnacao.ARQUIVADA]: 'Arquivada',
    [StatusImpugnacao.AGUARDANDO_RECURSO]: 'Aguardando Recurso',
    [StatusImpugnacao.RECURSO_APRESENTADO]: 'Recurso Apresentado',
    [StatusImpugnacao.RECURSO_JULGADO]: 'Recurso Julgado',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusImpugnacaoColor = (status: StatusImpugnacao): string => {
  const colors: Record<StatusImpugnacao, string> = {
    [StatusImpugnacao.RECEBIDA]: 'blue',
    [StatusImpugnacao.EM_ANALISE]: 'blue',
    [StatusImpugnacao.AGUARDANDO_ALEGACOES]: 'yellow',
    [StatusImpugnacao.ALEGACOES_APRESENTADAS]: 'blue',
    [StatusImpugnacao.AGUARDANDO_CONTRA_ALEGACOES]: 'yellow',
    [StatusImpugnacao.CONTRA_ALEGACOES_APRESENTADAS]: 'blue',
    [StatusImpugnacao.AGUARDANDO_JULGAMENTO]: 'purple',
    [StatusImpugnacao.JULGADA]: 'gray',
    [StatusImpugnacao.PROCEDENTE]: 'green',
    [StatusImpugnacao.IMPROCEDENTE]: 'red',
    [StatusImpugnacao.PARCIALMENTE_PROCEDENTE]: 'orange',
    [StatusImpugnacao.ARQUIVADA]: 'gray',
    [StatusImpugnacao.AGUARDANDO_RECURSO]: 'yellow',
    [StatusImpugnacao.RECURSO_APRESENTADO]: 'blue',
    [StatusImpugnacao.RECURSO_JULGADO]: 'gray',
  }
  return colors[status] || 'gray'
}

export const getTipoAlegacaoLabel = (tipo: TipoAlegacao): string => {
  const labels: Record<TipoAlegacao, string> = {
    [TipoAlegacao.INELEGIBILIDADE]: 'Inelegibilidade',
    [TipoAlegacao.IRREGULARIDADE_DOCUMENTAL]: 'Irregularidade Documental',
    [TipoAlegacao.VIOLACAO_NORMA]: 'Violacao de Norma',
    [TipoAlegacao.FRAUDE_ELEITORAL]: 'Fraude Eleitoral',
    [TipoAlegacao.ABUSO_PODER]: 'Abuso de Poder',
    [TipoAlegacao.OUTROS]: 'Outros',
  }
  return labels[tipo] || 'Desconhecido'
}

/**
 * Opcoes de tipo de impugnacao para formularios
 */
export const tiposImpugnacaoOptions = [
  { value: TipoImpugnacao.IMPUGNACAO_CHAPA, label: 'Impugnacao de Chapa' },
  { value: TipoImpugnacao.IMPUGNACAO_MEMBRO, label: 'Impugnacao de Membro' },
  { value: TipoImpugnacao.IMPUGNACAO_DOCUMENTO, label: 'Impugnacao de Documento' },
  { value: TipoImpugnacao.IMPUGNACAO_RESULTADO, label: 'Impugnacao de Resultado' },
]

/**
 * Opcoes de tipo de alegacao para formularios
 */
export const tiposAlegacaoOptions = [
  { value: TipoAlegacao.INELEGIBILIDADE, label: 'Inelegibilidade' },
  { value: TipoAlegacao.IRREGULARIDADE_DOCUMENTAL, label: 'Irregularidade Documental' },
  { value: TipoAlegacao.VIOLACAO_NORMA, label: 'Violacao de Norma' },
  { value: TipoAlegacao.FRAUDE_ELEITORAL, label: 'Fraude Eleitoral' },
  { value: TipoAlegacao.ABUSO_PODER, label: 'Abuso de Poder' },
  { value: TipoAlegacao.OUTROS, label: 'Outros' },
]

// ============================================
// Impugnacao Service
// ============================================

export const impugnacaoService = {
  // ========================================
  // Authenticated Endpoints
  // ========================================

  /**
   * Cria uma nova impugnacao
   */
  create: async (data: CreateImpugnacaoRequest): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Impugnacao>('/impugnacao', data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao registrar impugnacao')
    }
  },

  /**
   * Cria impugnacao como eleitor
   */
  createAsVoter: async (data: CreateImpugnacaoRequest): Promise<Impugnacao> => {
    try {
      setTokenType('voter')
      const response = await api.post<Impugnacao>('/impugnacao', data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao registrar impugnacao')
    }
  },

  /**
   * Obtem uma impugnacao pelo ID
   */
  getById: async (id: string): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.get<Impugnacao>(`/impugnacao/${id}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter impugnacao')
    }
  },

  /**
   * Lista impugnacoes do usuario logado (como impugnante)
   */
  getMinhasImpugnacoes: async (): Promise<ImpugnacaoResumida[]> => {
    try {
      setTokenType('candidate')
      const response = await api.get<ImpugnacaoResumida[]>('/impugnacao/minhas')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar suas impugnacoes')
    }
  },

  /**
   * Lista impugnacoes contra uma chapa especifica
   */
  getByChapa: async (chapaId: string): Promise<ImpugnacaoResumida[]> => {
    try {
      setTokenType('candidate')
      const response = await api.get<ImpugnacaoResumida[]>(`/impugnacao/chapa/${chapaId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar impugnacoes da chapa')
    }
  },

  /**
   * Atualiza uma impugnacao (apenas antes da analise)
   */
  update: async (id: string, data: UpdateImpugnacaoRequest): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.put<Impugnacao>(`/impugnacao/${id}`, data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao atualizar impugnacao')
    }
  },

  // ========================================
  // Alegacoes Endpoints
  // ========================================

  /**
   * Apresenta alegacoes (para o impugnado)
   */
  apresentarAlegacoes: async (id: string, data: AlegacoesRequest): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Impugnacao>(`/impugnacao/${id}/apresentar-alegacoes`, data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao apresentar alegacoes')
    }
  },

  /**
   * Apresenta contra-alegacoes (para o impugnante)
   */
  apresentarContraAlegacoes: async (id: string, data: AlegacoesRequest): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Impugnacao>(`/impugnacao/${id}/apresentar-contra-alegacoes`, data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao apresentar contra-alegacoes')
    }
  },

  /**
   * Apresenta recurso contra decisao
   */
  apresentarRecurso: async (id: string, data: RecursoRequest): Promise<Impugnacao> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Impugnacao>(`/impugnacao/${id}/recurso`, data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao apresentar recurso')
    }
  },

  // ========================================
  // Utility Methods
  // ========================================

  /**
   * Verifica se a impugnacao pode ser editada
   */
  podeEditar: (impugnacao: Impugnacao | ImpugnacaoResumida): boolean => {
    return impugnacao.status === StatusImpugnacao.RECEBIDA
  },

  /**
   * Verifica se pode apresentar alegacoes
   */
  podeApresentarAlegacoes: (impugnacao: Impugnacao): boolean => {
    return impugnacao.status === StatusImpugnacao.AGUARDANDO_ALEGACOES
  },

  /**
   * Verifica se pode apresentar contra-alegacoes
   */
  podeApresentarContraAlegacoes: (impugnacao: Impugnacao): boolean => {
    return impugnacao.status === StatusImpugnacao.AGUARDANDO_CONTRA_ALEGACOES
  },

  /**
   * Verifica se pode apresentar recurso
   */
  podeApresentarRecurso: (impugnacao: Impugnacao): boolean => {
    return impugnacao.status === StatusImpugnacao.AGUARDANDO_RECURSO
  },

  /**
   * Verifica se esta no prazo de alegacoes
   */
  estaNoPrazoAlegacoes: (impugnacao: Impugnacao): boolean => {
    if (!impugnacao.prazoAlegacoes) return false
    return new Date(impugnacao.prazoAlegacoes) > new Date()
  },

  /**
   * Verifica se esta no prazo de contra-alegacoes
   */
  estaNoPrazoContraAlegacoes: (impugnacao: Impugnacao): boolean => {
    if (!impugnacao.prazoContraAlegacoes) return false
    return new Date(impugnacao.prazoContraAlegacoes) > new Date()
  },

  /**
   * Calcula dias restantes para alegacoes
   */
  diasRestantesAlegacoes: (impugnacao: Impugnacao): number | null => {
    if (!impugnacao.prazoAlegacoes) return null
    const prazo = new Date(impugnacao.prazoAlegacoes)
    const hoje = new Date()
    const diffTime = prazo.getTime() - hoje.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays > 0 ? diffDays : 0
  },

  /**
   * Calcula dias restantes para contra-alegacoes
   */
  diasRestantesContraAlegacoes: (impugnacao: Impugnacao): number | null => {
    if (!impugnacao.prazoContraAlegacoes) return null
    const prazo = new Date(impugnacao.prazoContraAlegacoes)
    const hoje = new Date()
    const diffTime = prazo.getTime() - hoje.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays > 0 ? diffDays : 0
  },

  /**
   * Verifica se a impugnacao foi julgada
   */
  foiJulgada: (impugnacao: Impugnacao | ImpugnacaoResumida): boolean => {
    return (
      impugnacao.status === StatusImpugnacao.JULGADA ||
      impugnacao.status === StatusImpugnacao.PROCEDENTE ||
      impugnacao.status === StatusImpugnacao.IMPROCEDENTE ||
      impugnacao.status === StatusImpugnacao.PARCIALMENTE_PROCEDENTE ||
      impugnacao.status === StatusImpugnacao.ARQUIVADA
    )
  },

  /**
   * Verifica se a impugnacao foi procedente
   */
  foiProcedente: (impugnacao: Impugnacao | ImpugnacaoResumida): boolean => {
    return (
      impugnacao.status === StatusImpugnacao.PROCEDENTE ||
      impugnacao.status === StatusImpugnacao.PARCIALMENTE_PROCEDENTE
    )
  },

  /**
   * Formata o numero do protocolo
   */
  formatarProtocolo: (protocolo: string): string => {
    if (protocolo.includes('/') || protocolo.includes('-')) {
      return protocolo
    }
    if (protocolo.length >= 6) {
      const ano = protocolo.substring(0, 4)
      const numero = protocolo.substring(4)
      return `IMP-${ano}/${numero.padStart(6, '0')}`
    }
    return protocolo
  },
}

// Re-export enums for convenience
export { TipoImpugnacao, StatusImpugnacao, TipoAlegacao }
