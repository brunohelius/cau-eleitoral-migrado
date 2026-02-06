import api, { mapPagedResponse } from './api'

// Enums matching backend
export enum StatusDenuncia {
  RECEBIDA = 0,
  EM_ANALISE = 1,
  ADMISSIBILIDADE_ACEITA = 2,
  ADMISSIBILIDADE_REJEITADA = 3,
  AGUARDANDO_DEFESA = 4,
  DEFESA_APRESENTADA = 5,
  AGUARDANDO_JULGAMENTO = 6,
  JULGADA = 7,
  PROCEDENTE = 8,
  IMPROCEDENTE = 9,
  PARCIALMENTE_PROCEDENTE = 10,
  ARQUIVADA = 11,
  AGUARDANDO_RECURSO = 12,
  RECURSO_APRESENTADO = 13,
  RECURSO_JULGADO = 14,
}

export enum TipoDenuncia {
  PROPAGANDA_IRREGULAR = 0,
  ABUSO_PODER = 1,
  CAPTACAO_ILICITA_SUFRAGIO = 2,
  USO_INDEVIDO = 3,
  INELEGIBILIDADE = 4,
  FRAUDE_DOCUMENTAL = 5,
  OUTROS = 99,
}

export enum PrioridadeDenuncia {
  BAIXA = 0,
  NORMAL = 1,
  ALTA = 2,
  URGENTE = 3,
}

// Labels for display
export const statusDenunciaLabels: Record<StatusDenuncia, { label: string; color: string }> = {
  [StatusDenuncia.RECEBIDA]: { label: 'Recebida', color: 'bg-gray-100 text-gray-800' },
  [StatusDenuncia.EM_ANALISE]: { label: 'Em Analise', color: 'bg-blue-100 text-blue-800' },
  [StatusDenuncia.ADMISSIBILIDADE_ACEITA]: { label: 'Admissibilidade Aceita', color: 'bg-green-100 text-green-800' },
  [StatusDenuncia.ADMISSIBILIDADE_REJEITADA]: { label: 'Admissibilidade Rejeitada', color: 'bg-red-100 text-red-800' },
  [StatusDenuncia.AGUARDANDO_DEFESA]: { label: 'Aguardando Defesa', color: 'bg-yellow-100 text-yellow-800' },
  [StatusDenuncia.DEFESA_APRESENTADA]: { label: 'Defesa Apresentada', color: 'bg-purple-100 text-purple-800' },
  [StatusDenuncia.AGUARDANDO_JULGAMENTO]: { label: 'Aguardando Julgamento', color: 'bg-orange-100 text-orange-800' },
  [StatusDenuncia.JULGADA]: { label: 'Julgada', color: 'bg-indigo-100 text-indigo-800' },
  [StatusDenuncia.PROCEDENTE]: { label: 'Procedente', color: 'bg-green-100 text-green-800' },
  [StatusDenuncia.IMPROCEDENTE]: { label: 'Improcedente', color: 'bg-red-100 text-red-800' },
  [StatusDenuncia.PARCIALMENTE_PROCEDENTE]: { label: 'Parcialmente Procedente', color: 'bg-amber-100 text-amber-800' },
  [StatusDenuncia.ARQUIVADA]: { label: 'Arquivada', color: 'bg-gray-100 text-gray-600' },
  [StatusDenuncia.AGUARDANDO_RECURSO]: { label: 'Aguardando Recurso', color: 'bg-cyan-100 text-cyan-800' },
  [StatusDenuncia.RECURSO_APRESENTADO]: { label: 'Recurso Apresentado', color: 'bg-teal-100 text-teal-800' },
  [StatusDenuncia.RECURSO_JULGADO]: { label: 'Recurso Julgado', color: 'bg-slate-100 text-slate-800' },
}

export const tipoDenunciaLabels: Record<TipoDenuncia, string> = {
  [TipoDenuncia.PROPAGANDA_IRREGULAR]: 'Propaganda Irregular',
  [TipoDenuncia.ABUSO_PODER]: 'Abuso de Poder',
  [TipoDenuncia.CAPTACAO_ILICITA_SUFRAGIO]: 'Captacao Ilicita de Sufragio',
  [TipoDenuncia.USO_INDEVIDO]: 'Uso Indevido de Recursos',
  [TipoDenuncia.INELEGIBILIDADE]: 'Inelegibilidade',
  [TipoDenuncia.FRAUDE_DOCUMENTAL]: 'Fraude Documental',
  [TipoDenuncia.OUTROS]: 'Outros',
}

export const prioridadeLabels: Record<PrioridadeDenuncia, { label: string; color: string }> = {
  [PrioridadeDenuncia.BAIXA]: { label: 'Baixa', color: 'bg-gray-100 text-gray-800' },
  [PrioridadeDenuncia.NORMAL]: { label: 'Normal', color: 'bg-blue-100 text-blue-800' },
  [PrioridadeDenuncia.ALTA]: { label: 'Alta', color: 'bg-orange-100 text-orange-800' },
  [PrioridadeDenuncia.URGENTE]: { label: 'Urgente', color: 'bg-red-100 text-red-800' },
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

export interface HistoricoDenuncia {
  id: string
  denunciaId: string
  acao: string
  descricao: string
  usuarioId: string
  usuarioNome: string
  createdAt: string
}

export interface Denuncia {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  membroId?: string
  membroNome?: string
  denuncianteId?: string
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  fundamentacao?: string
  status: StatusDenuncia
  prioridade?: PrioridadeDenuncia
  anonima: boolean
  protocolo: string
  dataDenuncia: string
  dataRecebimento?: string
  prazoDefesa?: string
  prazoRecurso?: string
  anexos?: AnexoDenuncia[]
  pareceres?: ParecerDenuncia[]
  historicos?: HistoricoDenuncia[]
  createdAt: string
  updatedAt?: string
}

export interface CreateDenunciaRequest {
  eleicaoId: string
  chapaId?: string
  membroId?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  fundamentacao?: string
  prioridade?: PrioridadeDenuncia
  anonima?: boolean
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
}

export interface UpdateDenunciaRequest {
  tipo?: TipoDenuncia
  titulo?: string
  descricao?: string
  fundamentacao?: string
  prioridade?: PrioridadeDenuncia
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
    const response = await api.get('/denuncia', { params })
    return mapPagedResponse<Denuncia>(response.data)
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
    const response = await api.get(`/denuncia/eleicao/${eleicaoId}`, { params })
    return mapPagedResponse<Denuncia>(response.data)
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

  aceitarAdmissibilidade: async (id: string, fundamentacao: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/admissibilidade/aceitar`, { fundamentacao })
    return response.data
  },

  rejeitarAdmissibilidade: async (id: string, fundamentacao: string): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/admissibilidade/rejeitar`, { fundamentacao })
    return response.data
  },

  emitirParecer: async (id: string, data: EmitirParecerRequest): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/parecer`, data)
    return response.data
  },

  julgar: async (id: string, data: { decisao: StatusDenuncia; fundamentacao: string; penalidade?: string }): Promise<Denuncia> => {
    const response = await api.post<Denuncia>(`/denuncia/${id}/julgar`, data)
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
    recebidas: number
    emAnalise: number
    aguardandoJulgamento: number
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
