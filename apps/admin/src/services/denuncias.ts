import api from './api'

// Enums
export enum StatusDenuncia {
  PENDENTE = 0,
  EM_ANALISE = 1,
  PROCEDENTE = 2,
  IMPROCEDENTE = 3,
  ARQUIVADA = 4,
}

export enum TipoDenuncia {
  IRREGULARIDADE = 0,
  FRAUDE = 1,
  PROPAGANDA_IRREGULAR = 2,
  ABUSO_PODER = 3,
  COACAO = 4,
  FALSIDADE = 5,
  OUTRO = 99,
}

export enum PrioridadeDenuncia {
  BAIXA = 0,
  NORMAL = 1,
  ALTA = 2,
  URGENTE = 3,
}

// Interfaces
export interface AnexoDenuncia {
  id: string
  denunciaId: string
  nome: string
  tipo: string
  arquivoUrl: string
  tamanho: number
  createdAt: string
}

export interface ParecerDenuncia {
  id: string
  denunciaId: string
  analistaId: string
  analistaNome: string
  parecer: string
  decisao: StatusDenuncia
  dataEmissao: string
  createdAt: string
}

export interface Denuncia {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  denuncianteId?: string
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  provas?: string
  status: StatusDenuncia
  prioridade: PrioridadeDenuncia
  anonima: boolean
  protocolo: string
  analistaResponsavelId?: string
  analistaResponsavelNome?: string
  dataOcorrencia?: string
  localOcorrencia?: string
  dataAnalise?: string
  parecer?: string
  acaoTomada?: string
  anexos?: AnexoDenuncia[]
  pareceres?: ParecerDenuncia[]
  createdAt: string
  updatedAt?: string
}

export interface CreateDenunciaRequest {
  eleicaoId: string
  chapaId?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  provas?: string
  prioridade?: PrioridadeDenuncia
  anonima?: boolean
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
  dataOcorrencia?: string
  localOcorrencia?: string
}

export interface UpdateDenunciaRequest {
  tipo?: TipoDenuncia
  titulo?: string
  descricao?: string
  provas?: string
  prioridade?: PrioridadeDenuncia
  analistaResponsavelId?: string
}

export interface EmitirParecerRequest {
  parecer: string
  decisao: StatusDenuncia
}

export interface DenunciaListParams {
  eleicaoId?: string
  chapaId?: string
  status?: StatusDenuncia
  tipo?: TipoDenuncia
  prioridade?: PrioridadeDenuncia
  anonima?: boolean
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

export const denunciasService = {
  // CRUD Operations
  getAll: async (params?: DenunciaListParams): Promise<PaginatedResponse<Denuncia>> => {
    const response = await api.get<PaginatedResponse<Denuncia>>('/denuncia', { params })
    return response.data
  },

  getById: async (id: string): Promise<Denuncia> => {
    const response = await api.get<Denuncia>(`/denuncia/${id}`)
    return response.data
  },

  getByProtocolo: async (protocolo: string): Promise<Denuncia> => {
    const response = await api.get<Denuncia>(`/denuncia/protocolo/${protocolo}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string, params?: Omit<DenunciaListParams, 'eleicaoId'>): Promise<PaginatedResponse<Denuncia>> => {
    const response = await api.get<PaginatedResponse<Denuncia>>(`/denuncia/eleicao/${eleicaoId}`, { params })
    return response.data
  },

  getByChapa: async (chapaId: string): Promise<Denuncia[]> => {
    const response = await api.get<Denuncia[]>(`/denuncia/chapa/${chapaId}`)
    return response.data
  },

  create: async (data: CreateDenunciaRequest): Promise<Denuncia> => {
    const response = await api.post<Denuncia>('/denuncia', data)
    return response.data
  },

  update: async (id: string, data: UpdateDenunciaRequest): Promise<Denuncia> => {
    const response = await api.put<Denuncia>(`/denuncia/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/denuncia/${id}`)
  },

  // Status Operations
  iniciarAnalise: async (id: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/iniciar-analise`)
    return response.data
  },

  emitirParecer: async (id: string, data: EmitirParecerRequest): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/parecer`, data)
    return response.data
  },

  arquivar: async (id: string, motivo: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/arquivar`, { motivo })
    return response.data
  },

  reabrir: async (id: string, motivo: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/reabrir`, { motivo })
    return response.data
  },

  atribuirAnalista: async (id: string, analistaId: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/atribuir`, { analistaId })
    return response.data
  },

  // Anexos Operations
  getAnexos: async (denunciaId: string): Promise<AnexoDenuncia[]> => {
    const response = await api.get<AnexoDenuncia[]>(`/denuncia/${denunciaId}/anexos`)
    return response.data
  },

  uploadAnexo: async (denunciaId: string, arquivo: File, nome?: string): Promise<AnexoDenuncia> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)
    if (nome) formData.append('nome', nome)

    const response = await api.post<AnexoDenuncia>(`/denuncia/${denunciaId}/anexos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeAnexo: async (denunciaId: string, anexoId: string): Promise<void> => {
    await api.delete(`/denuncia/${denunciaId}/anexos/${anexoId}`)
  },

  downloadAnexo: async (denunciaId: string, anexoId: string): Promise<Blob> => {
    const response = await api.get(`/denuncia/${denunciaId}/anexos/${anexoId}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  // Statistics
  getEstatisticas: async (eleicaoId?: string): Promise<{
    total: number
    pendentes: number
    emAnalise: number
    procedentes: number
    improcedentes: number
    arquivadas: number
    porTipo: Record<string, number>
    porPrioridade: Record<string, number>
  }> => {
    const response = await api.get('/denuncia/estatisticas', {
      params: eleicaoId ? { eleicaoId } : undefined,
    })
    return response.data
  },

  // Reports
  gerarRelatorio: async (params: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
    formato?: 'pdf' | 'xlsx'
  }): Promise<Blob> => {
    const response = await api.get('/denuncia/relatorio', {
      params,
      responseType: 'blob',
    })
    return response.data
  },
}
