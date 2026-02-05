import api from './api'

// Enums
export enum TipoDocumento {
  ATA = 0,
  RESOLUCAO = 1,
  EDITAL = 2,
  PORTARIA = 3,
  DECLARACAO = 4,
  CERTIDAO = 5,
  RELATORIO = 6,
  PARECER = 7,
  RECURSO = 8,
  DEFESA = 9,
  COMPROVANTE = 10,
  FOTO = 11,
  OUTROS = 99,
}

export enum StatusDocumento {
  PENDENTE = 0,
  APROVADO = 1,
  REJEITADO = 2,
  EXPIRADO = 3,
  SUBSTITUIDO = 4,
}

export enum CategoriaDocumento {
  ELEICAO = 0,
  CHAPA = 1,
  CANDIDATO = 2,
  DENUNCIA = 3,
  IMPUGNACAO = 4,
  JULGAMENTO = 5,
  VOTACAO = 6,
  APURACAO = 7,
  ADMINISTRATIVO = 8,
}

// Interfaces
export interface Documento {
  id: string
  nome: string
  descricao?: string
  tipo: TipoDocumento
  categoria: CategoriaDocumento
  status: StatusDocumento
  arquivoUrl: string
  arquivoNome: string
  arquivoTipo: string
  arquivoTamanho: number
  hashArquivo?: string
  versao: number
  eleicaoId?: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  candidatoId?: string
  candidatoNome?: string
  denunciaId?: string
  impugnacaoId?: string
  julgamentoId?: string
  uploadPorId: string
  uploadPorNome: string
  dataValidade?: string
  observacoes?: string
  tags?: string[]
  metadados?: Record<string, unknown>
  versaoAnteriorId?: string
  createdAt: string
  updatedAt?: string
}

export interface CreateDocumentoRequest {
  nome: string
  descricao?: string
  tipo: TipoDocumento
  categoria: CategoriaDocumento
  arquivo: File
  eleicaoId?: string
  chapaId?: string
  candidatoId?: string
  denunciaId?: string
  impugnacaoId?: string
  julgamentoId?: string
  dataValidade?: string
  observacoes?: string
  tags?: string[]
  metadados?: Record<string, unknown>
}

export interface UpdateDocumentoRequest {
  nome?: string
  descricao?: string
  tipo?: TipoDocumento
  status?: StatusDocumento
  dataValidade?: string
  observacoes?: string
  tags?: string[]
  metadados?: Record<string, unknown>
}

export interface DocumentoListParams {
  tipo?: TipoDocumento
  categoria?: CategoriaDocumento
  status?: StatusDocumento
  eleicaoId?: string
  chapaId?: string
  candidatoId?: string
  denunciaId?: string
  impugnacaoId?: string
  julgamentoId?: string
  uploadPorId?: string
  tags?: string[]
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

export interface VersaoDocumento {
  id: string
  documentoId: string
  versao: number
  arquivoUrl: string
  arquivoNome: string
  arquivoTamanho: number
  hashArquivo: string
  uploadPorId: string
  uploadPorNome: string
  motivo?: string
  createdAt: string
}

export const documentosService = {
  // CRUD Operations
  getAll: async (params?: DocumentoListParams): Promise<PaginatedResponse<Documento>> => {
    const response = await api.get<PaginatedResponse<Documento>>('/documento', { params })
    return response.data
  },

  getById: async (id: string): Promise<Documento> => {
    const response = await api.get<Documento>(`/documento/${id}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string, params?: Omit<DocumentoListParams, 'eleicaoId'>): Promise<PaginatedResponse<Documento>> => {
    const response = await api.get<PaginatedResponse<Documento>>(`/documento/eleicao/${eleicaoId}`, { params })
    return response.data
  },

  getByChapa: async (chapaId: string): Promise<Documento[]> => {
    const response = await api.get<Documento[]>(`/documento/chapa/${chapaId}`)
    return response.data
  },

  getByCandidato: async (candidatoId: string): Promise<Documento[]> => {
    const response = await api.get<Documento[]>(`/documento/candidato/${candidatoId}`)
    return response.data
  },

  create: async (data: CreateDocumentoRequest): Promise<Documento> => {
    const formData = new FormData()
    formData.append('arquivo', data.arquivo)
    formData.append('nome', data.nome)
    formData.append('tipo', data.tipo.toString())
    formData.append('categoria', data.categoria.toString())

    if (data.descricao) formData.append('descricao', data.descricao)
    if (data.eleicaoId) formData.append('eleicaoId', data.eleicaoId)
    if (data.chapaId) formData.append('chapaId', data.chapaId)
    if (data.candidatoId) formData.append('candidatoId', data.candidatoId)
    if (data.denunciaId) formData.append('denunciaId', data.denunciaId)
    if (data.impugnacaoId) formData.append('impugnacaoId', data.impugnacaoId)
    if (data.julgamentoId) formData.append('julgamentoId', data.julgamentoId)
    if (data.dataValidade) formData.append('dataValidade', data.dataValidade)
    if (data.observacoes) formData.append('observacoes', data.observacoes)
    if (data.tags) formData.append('tags', JSON.stringify(data.tags))
    if (data.metadados) formData.append('metadados', JSON.stringify(data.metadados))

    const response = await api.post<Documento>('/documento', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  update: async (id: string, data: UpdateDocumentoRequest): Promise<Documento> => {
    const response = await api.put<Documento>(`/documento/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/documento/${id}`)
  },

  // Status Operations
  aprovar: async (id: string, observacoes?: string): Promise<Documento> => {
    const response = await api.post<Documento>(`/documento/${id}/aprovar`, { observacoes })
    return response.data
  },

  rejeitar: async (id: string, motivo: string): Promise<Documento> => {
    const response = await api.post<Documento>(`/documento/${id}/rejeitar`, { motivo })
    return response.data
  },

  // Version Operations
  getVersoes: async (id: string): Promise<VersaoDocumento[]> => {
    const response = await api.get<VersaoDocumento[]>(`/documento/${id}/versoes`)
    return response.data
  },

  uploadNovaVersao: async (id: string, arquivo: File, motivo?: string): Promise<Documento> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)
    if (motivo) formData.append('motivo', motivo)

    const response = await api.post<Documento>(`/documento/${id}/versoes`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  restaurarVersao: async (id: string, versaoId: string): Promise<Documento> => {
    const response = await api.post<Documento>(`/documento/${id}/versoes/${versaoId}/restaurar`)
    return response.data
  },

  // Download Operations
  download: async (id: string): Promise<Blob> => {
    const response = await api.get(`/documento/${id}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  downloadVersao: async (id: string, versaoId: string): Promise<Blob> => {
    const response = await api.get(`/documento/${id}/versoes/${versaoId}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  getPreviewUrl: async (id: string): Promise<{ url: string; expiraEm: string }> => {
    const response = await api.get(`/documento/${id}/preview`)
    return response.data
  },

  // Batch Operations
  downloadMultiplos: async (ids: string[]): Promise<Blob> => {
    const response = await api.post('/documento/download-multiplos', { ids }, {
      responseType: 'blob',
    })
    return response.data
  },

  deletarMultiplos: async (ids: string[]): Promise<{ sucesso: number; erros: { id: string; erro: string }[] }> => {
    const response = await api.post('/documento/deletar-multiplos', { ids })
    return response.data
  },

  // Tags Operations
  getTags: async (): Promise<string[]> => {
    const response = await api.get<string[]>('/documento/tags')
    return response.data
  },

  addTag: async (id: string, tag: string): Promise<Documento> => {
    const response = await api.post<Documento>(`/documento/${id}/tags`, { tag })
    return response.data
  },

  removeTag: async (id: string, tag: string): Promise<Documento> => {
    const response = await api.delete<Documento>(`/documento/${id}/tags/${encodeURIComponent(tag)}`)
    return response.data
  },

  // Statistics
  getEstatisticas: async (params?: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
  }): Promise<{
    total: number
    porTipo: Record<string, number>
    porCategoria: Record<string, number>
    porStatus: Record<string, number>
    tamanhoTotal: number
    mediaUploadDiario: number
  }> => {
    const response = await api.get('/documento/estatisticas', { params })
    return response.data
  },

  // Validation
  validarArquivo: async (arquivo: File): Promise<{
    valido: boolean
    erros?: string[]
    avisos?: string[]
    tipoDetectado?: string
  }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post('/documento/validar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  // Search
  pesquisarConteudo: async (query: string, params?: {
    tipo?: TipoDocumento
    categoria?: CategoriaDocumento
    eleicaoId?: string
    page?: number
    pageSize?: number
  }): Promise<PaginatedResponse<Documento & { trechoEncontrado?: string }>> => {
    const response = await api.get('/documento/pesquisar', {
      params: { query, ...params },
    })
    return response.data
  },
}
