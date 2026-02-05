import api from './api'

// Enums
export enum StatusChapa {
  PENDENTE = 0,
  EM_ANALISE = 1,
  APROVADA = 2,
  REPROVADA = 3,
  IMPUGNADA = 4,
  SUSPENSA = 5,
  CANCELADA = 6,
}

export enum TipoMembro {
  TITULAR = 0,
  SUPLENTE = 1,
}

export enum CargoMembro {
  PRESIDENTE = 0,
  VICE_PRESIDENTE = 1,
  CONSELHEIRO = 2,
  DIRETOR = 3,
  COORDENADOR = 4,
}

// Interfaces
export interface MembroChapa {
  id: string
  chapaId: string
  candidatoId: string
  candidatoNome: string
  candidatoCpf?: string
  candidatoRegistroCAU?: string
  cargo: CargoMembro
  tipo: TipoMembro
  ordem: number
  status: number
  createdAt: string
  updatedAt?: string
}

export interface DocumentoChapa {
  id: string
  chapaId: string
  nome: string
  tipo: number
  arquivoUrl: string
  tamanho?: number
  status: number
  observacoes?: string
  createdAt: string
}

export interface Chapa {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  descricao?: string
  logoUrl?: string
  status: StatusChapa
  dataCadastro: string
  dataAprovacao?: string
  motivoReprovacao?: string
  representanteLegalId?: string
  representanteLegalNome?: string
  membros?: MembroChapa[]
  documentos?: DocumentoChapa[]
  totalVotos?: number
  createdAt: string
  updatedAt?: string
}

export interface CreateChapaRequest {
  eleicaoId: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  descricao?: string
  representanteLegalId?: string
}

export interface UpdateChapaRequest {
  nome?: string
  sigla?: string
  lema?: string
  descricao?: string
  logoUrl?: string
  representanteLegalId?: string
}

export interface AddMembroRequest {
  candidatoId: string
  cargo: CargoMembro
  tipo: TipoMembro
  ordem: number
}

export interface AddDocumentoRequest {
  nome: string
  tipo: number
  arquivo: File
}

export interface ChapaListParams {
  eleicaoId?: string
  status?: StatusChapa
  search?: string
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

export const chapasService = {
  // CRUD Operations
  getAll: async (params?: ChapaListParams): Promise<PaginatedResponse<Chapa>> => {
    const response = await api.get<PaginatedResponse<Chapa>>('/chapa', { params })
    return response.data
  },

  getById: async (id: string): Promise<Chapa> => {
    const response = await api.get<Chapa>(`/chapa/${id}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string): Promise<Chapa[]> => {
    const response = await api.get<Chapa[]>(`/chapa/eleicao/${eleicaoId}`)
    return response.data
  },

  getByNumero: async (eleicaoId: string, numero: number): Promise<Chapa> => {
    const response = await api.get<Chapa>(`/chapa/eleicao/${eleicaoId}/numero/${numero}`)
    return response.data
  },

  create: async (data: CreateChapaRequest): Promise<Chapa> => {
    const response = await api.post<Chapa>('/chapa', data)
    return response.data
  },

  update: async (id: string, data: UpdateChapaRequest): Promise<Chapa> => {
    const response = await api.put<Chapa>(`/chapa/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/chapa/${id}`)
  },

  // Status Operations
  aprovar: async (id: string): Promise<Chapa> => {
    const response = await api.post<Chapa>(`/chapa/${id}/aprovar`)
    return response.data
  },

  reprovar: async (id: string, motivo: string): Promise<Chapa> => {
    const response = await api.post<Chapa>(`/chapa/${id}/reprovar`, { motivo })
    return response.data
  },

  suspender: async (id: string, motivo: string): Promise<Chapa> => {
    const response = await api.post<Chapa>(`/chapa/${id}/suspender`, { motivo })
    return response.data
  },

  reativar: async (id: string): Promise<Chapa> => {
    const response = await api.post<Chapa>(`/chapa/${id}/reativar`)
    return response.data
  },

  cancelar: async (id: string, motivo: string): Promise<Chapa> => {
    const response = await api.post<Chapa>(`/chapa/${id}/cancelar`, { motivo })
    return response.data
  },

  // Membros Operations
  getMembros: async (chapaId: string): Promise<MembroChapa[]> => {
    const response = await api.get<MembroChapa[]>(`/chapa/${chapaId}/membros`)
    return response.data
  },

  addMembro: async (chapaId: string, data: AddMembroRequest): Promise<MembroChapa> => {
    const response = await api.post<MembroChapa>(`/chapa/${chapaId}/membros`, data)
    return response.data
  },

  removeMembro: async (chapaId: string, membroId: string): Promise<void> => {
    await api.delete(`/chapa/${chapaId}/membros/${membroId}`)
  },

  updateMembroOrdem: async (chapaId: string, membroId: string, ordem: number): Promise<MembroChapa> => {
    const response = await api.patch<MembroChapa>(`/chapa/${chapaId}/membros/${membroId}/ordem`, { ordem })
    return response.data
  },

  // Documentos Operations
  getDocumentos: async (chapaId: string): Promise<DocumentoChapa[]> => {
    const response = await api.get<DocumentoChapa[]>(`/chapa/${chapaId}/documentos`)
    return response.data
  },

  uploadDocumento: async (chapaId: string, data: AddDocumentoRequest): Promise<DocumentoChapa> => {
    const formData = new FormData()
    formData.append('nome', data.nome)
    formData.append('tipo', data.tipo.toString())
    formData.append('arquivo', data.arquivo)

    const response = await api.post<DocumentoChapa>(`/chapa/${chapaId}/documentos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeDocumento: async (chapaId: string, documentoId: string): Promise<void> => {
    await api.delete(`/chapa/${chapaId}/documentos/${documentoId}`)
  },

  // Logo Operations
  uploadLogo: async (chapaId: string, arquivo: File): Promise<{ logoUrl: string }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<{ logoUrl: string }>(`/chapa/${chapaId}/logo`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeLogo: async (chapaId: string): Promise<void> => {
    await api.delete(`/chapa/${chapaId}/logo`)
  },

  // Statistics
  getEstatisticas: async (eleicaoId: string): Promise<{
    total: number
    pendentes: number
    aprovadas: number
    reprovadas: number
    impugnadas: number
  }> => {
    const response = await api.get(`/chapa/estatisticas/${eleicaoId}`)
    return response.data
  },
}
