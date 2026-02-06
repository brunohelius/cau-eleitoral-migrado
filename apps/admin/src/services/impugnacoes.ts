import api, { mapPagedResponse } from './api'

// Enums
export enum StatusImpugnacao {
  PENDENTE = 0,
  EM_ANALISE = 1,
  DEFERIDA = 2,
  INDEFERIDA = 3,
  PARCIALMENTE_DEFERIDA = 4,
  RECURSO = 5,
  ARQUIVADA = 6,
}

export enum TipoImpugnacao {
  CANDIDATURA = 0,
  CHAPA = 1,
  ELEICAO = 2,
  RESULTADO = 3,
  VOTACAO = 4,
}

export enum FaseImpugnacao {
  REGISTRO = 0,
  ANALISE_INICIAL = 1,
  DEFESA = 2,
  PARECER = 3,
  JULGAMENTO = 4,
  RECURSO = 5,
  ENCERRADA = 6,
}

// Interfaces
export interface AnexoImpugnacao {
  id: string
  impugnacaoId: string
  nome: string
  tipo: string
  arquivoUrl: string
  tamanho: number
  createdAt: string
}

export interface DefesaImpugnacao {
  id: string
  impugnacaoId: string
  defensorId: string
  defensorNome: string
  texto: string
  dataApresentacao: string
  anexos?: AnexoImpugnacao[]
  createdAt: string
}

export interface ParecerImpugnacao {
  id: string
  impugnacaoId: string
  pareceristaId: string
  pareceristaNome: string
  parecer: string
  recomendacao: StatusImpugnacao
  dataEmissao: string
  createdAt: string
}

export interface RecursoImpugnacao {
  id: string
  impugnacaoId: string
  recorrenteId: string
  recorrenteNome: string
  fundamentacao: string
  dataInterposicao: string
  status: StatusImpugnacao
  decisaoRecurso?: string
  dataDecisao?: string
  anexos?: AnexoImpugnacao[]
  createdAt: string
}

export interface Impugnacao {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  candidatoId?: string
  candidatoNome?: string
  impugnanteId: string
  impugnanteNome: string
  tipo: TipoImpugnacao
  fase: FaseImpugnacao
  status: StatusImpugnacao
  protocolo: string
  fundamentacao: string
  normasVioladas?: string
  pedido: string
  prazoDefesa?: string
  decisao?: string
  fundamentacaoDecisao?: string
  dataDecisao?: string
  relatorId?: string
  relatorNome?: string
  anexos?: AnexoImpugnacao[]
  defesas?: DefesaImpugnacao[]
  pareceres?: ParecerImpugnacao[]
  recursos?: RecursoImpugnacao[]
  createdAt: string
  updatedAt?: string
}

export interface CreateImpugnacaoRequest {
  eleicaoId: string
  chapaId?: string
  candidatoId?: string
  tipo: TipoImpugnacao
  fundamentacao: string
  normasVioladas?: string
  pedido: string
}

export interface UpdateImpugnacaoRequest {
  fundamentacao?: string
  normasVioladas?: string
  pedido?: string
  relatorId?: string
}

export interface ApresentarDefesaRequest {
  texto: string
}

export interface EmitirParecerRequest {
  parecer: string
  recomendacao: StatusImpugnacao
}

export interface ProferirDecisaoRequest {
  decisao: StatusImpugnacao
  fundamentacao: string
}

export interface InterporRecursoRequest {
  fundamentacao: string
}

export interface ImpugnacaoListParams {
  eleicaoId?: string
  chapaId?: string
  candidatoId?: string
  tipo?: TipoImpugnacao
  status?: StatusImpugnacao
  fase?: FaseImpugnacao
  search?: string
  dataInicio?: string
  dataFim?: string
  page?: number
  pageSize?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  total: number
  page: number
  pageSize: number
  totalPages: number
}

export const impugnacoesService = {
  // CRUD Operations
  getAll: async (params?: ImpugnacaoListParams): Promise<PaginatedResponse<Impugnacao>> => {
    const response = await api.get('/impugnacao', { params })
    return mapPagedResponse<Impugnacao>(response.data)
  },

  getById: async (id: string): Promise<Impugnacao> => {
    const response = await api.get<Impugnacao>(`/impugnacao/${id}`)
    return response.data
  },

  getByProtocolo: async (protocolo: string): Promise<Impugnacao> => {
    const response = await api.get<Impugnacao>(`/impugnacao/protocolo/${protocolo}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string, params?: Omit<ImpugnacaoListParams, 'eleicaoId'>): Promise<PaginatedResponse<Impugnacao>> => {
    const response = await api.get(`/impugnacao/eleicao/${eleicaoId}`, { params })
    return mapPagedResponse<Impugnacao>(response.data)
  },

  getByChapa: async (chapaId: string): Promise<Impugnacao[]> => {
    const response = await api.get<Impugnacao[]>(`/impugnacao/chapa/${chapaId}`)
    return response.data
  },

  getByCandidato: async (candidatoId: string): Promise<Impugnacao[]> => {
    const response = await api.get<Impugnacao[]>(`/impugnacao/candidato/${candidatoId}`)
    return response.data
  },

  create: async (data: CreateImpugnacaoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>('/impugnacao', data)
    return response.data
  },

  update: async (id: string, data: UpdateImpugnacaoRequest): Promise<Impugnacao> => {
    const response = await api.put<Impugnacao>(`/impugnacao/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/impugnacao/${id}`)
  },

  // Phase Operations
  iniciarAnalise: async (id: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/iniciar-analise`)
    return response.data
  },

  solicitarDefesa: async (id: string, prazo: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/solicitar-defesa`, { prazo })
    return response.data
  },

  apresentarDefesa: async (id: string, data: ApresentarDefesaRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/defesa`, data)
    return response.data
  },

  emitirParecer: async (id: string, data: EmitirParecerRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/parecer`, data)
    return response.data
  },

  encaminharJulgamento: async (id: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/encaminhar-julgamento`)
    return response.data
  },

  proferirDecisao: async (id: string, data: ProferirDecisaoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/decisao`, data)
    return response.data
  },

  interporRecurso: async (id: string, data: InterporRecursoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/recurso`, data)
    return response.data
  },

  julgarRecurso: async (id: string, recursoId: string, data: ProferirDecisaoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/recurso/${recursoId}/julgamento`, data)
    return response.data
  },

  arquivar: async (id: string, motivo: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/arquivar`, { motivo })
    return response.data
  },

  // Relator Operations
  atribuirRelator: async (id: string, relatorId: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/relator`, { relatorId })
    return response.data
  },

  // Anexos Operations
  getAnexos: async (impugnacaoId: string): Promise<AnexoImpugnacao[]> => {
    const response = await api.get<AnexoImpugnacao[]>(`/impugnacao/${impugnacaoId}/anexos`)
    return response.data
  },

  uploadAnexo: async (impugnacaoId: string, arquivo: File, nome?: string): Promise<AnexoImpugnacao> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)
    if (nome) formData.append('nome', nome)

    const response = await api.post<AnexoImpugnacao>(`/impugnacao/${impugnacaoId}/anexos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeAnexo: async (impugnacaoId: string, anexoId: string): Promise<void> => {
    await api.delete(`/impugnacao/${impugnacaoId}/anexos/${anexoId}`)
  },

  uploadAnexoDefesa: async (impugnacaoId: string, defesaId: string, arquivo: File): Promise<AnexoImpugnacao> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<AnexoImpugnacao>(
      `/impugnacao/${impugnacaoId}/defesa/${defesaId}/anexos`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )
    return response.data
  },

  uploadAnexoRecurso: async (impugnacaoId: string, recursoId: string, arquivo: File): Promise<AnexoImpugnacao> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<AnexoImpugnacao>(
      `/impugnacao/${impugnacaoId}/recurso/${recursoId}/anexos`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )
    return response.data
  },

  // Statistics
  getEstatisticas: async (eleicaoId?: string): Promise<{
    total: number
    pendentes: number
    emAnalise: number
    deferidas: number
    indeferidas: number
    parcialmenteDeferidas: number
    emRecurso: number
    arquivadas: number
    porTipo: Record<string, number>
    porFase: Record<string, number>
  }> => {
    const response = await api.get('/impugnacao/estatisticas', {
      params: eleicaoId ? { eleicaoId } : undefined,
    })
    return response.data
  },

  // Timeline
  getTimeline: async (impugnacaoId: string): Promise<{
    data: string
    evento: string
    descricao: string
    usuarioNome?: string
  }[]> => {
    const response = await api.get(`/impugnacao/${impugnacaoId}/timeline`)
    return response.data
  },

  // Reports
  gerarRelatorio: async (params: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
    formato?: 'pdf' | 'xlsx'
  }): Promise<Blob> => {
    const response = await api.get('/impugnacao/relatorio', {
      params,
      responseType: 'blob',
    })
    return response.data
  },
}
