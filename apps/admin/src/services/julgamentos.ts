import api from './api'

// Enums
export enum StatusJulgamento {
  AGENDADO = 0,
  EM_ANDAMENTO = 1,
  SUSPENSO = 2,
  CONCLUIDO = 3,
  CANCELADO = 4,
  ADIADO = 5,
}

export enum TipoJulgamento {
  IMPUGNACAO = 0,
  RECURSO = 1,
  DENUNCIA = 2,
  RECURSO_ADMINISTRATIVO = 3,
}

export enum ResultadoJulgamento {
  PENDENTE = 0,
  DEFERIDO = 1,
  INDEFERIDO = 2,
  PARCIALMENTE_DEFERIDO = 3,
  NULO = 4,
}

export enum TipoVotoJulgamento {
  FAVORAVEL = 0,
  CONTRARIO = 1,
  ABSTENCAO = 2,
}

// Interfaces
export interface MembroJulgamento {
  id: string
  julgamentoId: string
  membroId: string
  membroNome: string
  cargo: string
  voto?: TipoVotoJulgamento
  justificativaVoto?: string
  dataVoto?: string
  presente: boolean
  impedido: boolean
  motivoImpedimento?: string
}

export interface DocumentoJulgamento {
  id: string
  julgamentoId: string
  nome: string
  tipo: string
  arquivoUrl: string
  tamanho: number
  createdAt: string
}

export interface AtaJulgamento {
  id: string
  julgamentoId: string
  texto: string
  arquivoUrl?: string
  dataEmissao: string
  assinada: boolean
  dataAssinatura?: string
  createdAt: string
}

export interface Julgamento {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  impugnacaoId?: string
  impugnacaoProtocolo?: string
  denunciaId?: string
  denunciaProtocolo?: string
  tipo: TipoJulgamento
  status: StatusJulgamento
  resultado: ResultadoJulgamento
  protocolo: string
  dataAgendamento: string
  horaInicio?: string
  horaFim?: string
  local?: string
  linkVideoconferencia?: string
  relatorId?: string
  relatorNome?: string
  presidenteId?: string
  presidenteNome?: string
  pauta?: string
  resumoProcesso?: string
  decisao?: string
  fundamentacaoDecisao?: string
  ementa?: string
  votoVencedor?: string
  votoVencido?: string
  observacoes?: string
  membros?: MembroJulgamento[]
  documentos?: DocumentoJulgamento[]
  ata?: AtaJulgamento
  createdAt: string
  updatedAt?: string
}

export interface CreateJulgamentoRequest {
  eleicaoId: string
  impugnacaoId?: string
  denunciaId?: string
  tipo: TipoJulgamento
  dataAgendamento: string
  horaInicio?: string
  local?: string
  linkVideoconferencia?: string
  relatorId?: string
  presidenteId?: string
  pauta?: string
  resumoProcesso?: string
}

export interface UpdateJulgamentoRequest {
  dataAgendamento?: string
  horaInicio?: string
  local?: string
  linkVideoconferencia?: string
  relatorId?: string
  presidenteId?: string
  pauta?: string
  resumoProcesso?: string
  observacoes?: string
}

export interface RegistrarDecisaoRequest {
  resultado: ResultadoJulgamento
  decisao: string
  fundamentacao: string
  ementa?: string
  votoVencedor?: string
  votoVencido?: string
}

export interface RegistrarVotoRequest {
  voto: TipoVotoJulgamento
  justificativa?: string
}

export interface RegistrarPresencaRequest {
  membrosPresentes: string[]
  membrosImpedidos?: { membroId: string; motivo: string }[]
}

export interface GerarAtaRequest {
  texto: string
}

export interface JulgamentoListParams {
  eleicaoId?: string
  tipo?: TipoJulgamento
  status?: StatusJulgamento
  resultado?: ResultadoJulgamento
  relatorId?: string
  dataInicio?: string
  dataFim?: string
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

export const julgamentosService = {
  // CRUD Operations
  getAll: async (params?: JulgamentoListParams): Promise<PaginatedResponse<Julgamento>> => {
    const response = await api.get<PaginatedResponse<Julgamento>>('/julgamento', { params })
    return response.data
  },

  getById: async (id: string): Promise<Julgamento> => {
    const response = await api.get<Julgamento>(`/julgamento/${id}`)
    return response.data
  },

  getByProtocolo: async (protocolo: string): Promise<Julgamento> => {
    const response = await api.get<Julgamento>(`/julgamento/protocolo/${protocolo}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string, params?: Omit<JulgamentoListParams, 'eleicaoId'>): Promise<PaginatedResponse<Julgamento>> => {
    const response = await api.get<PaginatedResponse<Julgamento>>(`/julgamento/eleicao/${eleicaoId}`, { params })
    return response.data
  },

  getByImpugnacao: async (impugnacaoId: string): Promise<Julgamento[]> => {
    const response = await api.get<Julgamento[]>(`/julgamento/impugnacao/${impugnacaoId}`)
    return response.data
  },

  getByDenuncia: async (denunciaId: string): Promise<Julgamento[]> => {
    const response = await api.get<Julgamento[]>(`/julgamento/denuncia/${denunciaId}`)
    return response.data
  },

  getAgendados: async (params?: { dataInicio?: string; dataFim?: string }): Promise<Julgamento[]> => {
    const response = await api.get<Julgamento[]>('/julgamento/agendados', { params })
    return response.data
  },

  create: async (data: CreateJulgamentoRequest): Promise<Julgamento> => {
    const response = await api.post<Julgamento>('/julgamento', data)
    return response.data
  },

  update: async (id: string, data: UpdateJulgamentoRequest): Promise<Julgamento> => {
    const response = await api.put<Julgamento>(`/julgamento/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/julgamento/${id}`)
  },

  // Session Operations
  iniciar: async (id: string): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/iniciar`)
    return response.data
  },

  suspender: async (id: string, motivo: string): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/suspender`, { motivo })
    return response.data
  },

  retomar: async (id: string): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/retomar`)
    return response.data
  },

  adiar: async (id: string, novaData: string, motivo: string): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/adiar`, { novaData, motivo })
    return response.data
  },

  cancelar: async (id: string, motivo: string): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/cancelar`, { motivo })
    return response.data
  },

  encerrar: async (id: string, data: RegistrarDecisaoRequest): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${id}/encerrar`, data)
    return response.data
  },

  // Membros Operations
  getMembros: async (julgamentoId: string): Promise<MembroJulgamento[]> => {
    const response = await api.get<MembroJulgamento[]>(`/julgamento/${julgamentoId}/membros`)
    return response.data
  },

  addMembro: async (julgamentoId: string, membroId: string, cargo: string): Promise<MembroJulgamento> => {
    const response = await api.post<MembroJulgamento>(`/julgamento/${julgamentoId}/membros`, { membroId, cargo })
    return response.data
  },

  removeMembro: async (julgamentoId: string, membroId: string): Promise<void> => {
    await api.delete(`/julgamento/${julgamentoId}/membros/${membroId}`)
  },

  registrarPresenca: async (julgamentoId: string, data: RegistrarPresencaRequest): Promise<Julgamento> => {
    const response = await api.post<Julgamento>(`/julgamento/${julgamentoId}/presenca`, data)
    return response.data
  },

  registrarVoto: async (julgamentoId: string, membroId: string, data: RegistrarVotoRequest): Promise<MembroJulgamento> => {
    const response = await api.post<MembroJulgamento>(`/julgamento/${julgamentoId}/membros/${membroId}/voto`, data)
    return response.data
  },

  // Documentos Operations
  getDocumentos: async (julgamentoId: string): Promise<DocumentoJulgamento[]> => {
    const response = await api.get<DocumentoJulgamento[]>(`/julgamento/${julgamentoId}/documentos`)
    return response.data
  },

  uploadDocumento: async (julgamentoId: string, arquivo: File, nome?: string): Promise<DocumentoJulgamento> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)
    if (nome) formData.append('nome', nome)

    const response = await api.post<DocumentoJulgamento>(`/julgamento/${julgamentoId}/documentos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removeDocumento: async (julgamentoId: string, documentoId: string): Promise<void> => {
    await api.delete(`/julgamento/${julgamentoId}/documentos/${documentoId}`)
  },

  // Ata Operations
  getAta: async (julgamentoId: string): Promise<AtaJulgamento | null> => {
    const response = await api.get<AtaJulgamento>(`/julgamento/${julgamentoId}/ata`)
    return response.data
  },

  gerarAta: async (julgamentoId: string, data: GerarAtaRequest): Promise<AtaJulgamento> => {
    const response = await api.post<AtaJulgamento>(`/julgamento/${julgamentoId}/ata`, data)
    return response.data
  },

  assinarAta: async (julgamentoId: string): Promise<AtaJulgamento> => {
    const response = await api.post<AtaJulgamento>(`/julgamento/${julgamentoId}/ata/assinar`)
    return response.data
  },

  downloadAta: async (julgamentoId: string): Promise<Blob> => {
    const response = await api.get(`/julgamento/${julgamentoId}/ata/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  // Statistics
  getEstatisticas: async (eleicaoId?: string): Promise<{
    total: number
    agendados: number
    emAndamento: number
    concluidos: number
    cancelados: number
    adiados: number
    porTipo: Record<string, number>
    porResultado: Record<string, number>
  }> => {
    const response = await api.get('/julgamento/estatisticas', {
      params: eleicaoId ? { eleicaoId } : undefined,
    })
    return response.data
  },

  // Calendar
  getCalendario: async (mes: number, ano: number): Promise<{
    data: string
    julgamentos: { id: string; protocolo: string; tipo: TipoJulgamento; hora: string }[]
  }[]> => {
    const response = await api.get('/julgamento/calendario', { params: { mes, ano } })
    return response.data
  },
}
