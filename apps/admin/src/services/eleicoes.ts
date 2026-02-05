import api from './api'

export interface Eleicao {
  id: string
  nome: string
  descricao?: string
  tipo: number
  status: number
  faseAtual: number
  ano: number
  mandato?: number
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  dataApuracao?: string
  regionalId?: string
  regionalNome?: string
  modoVotacao: number
  quantidadeVagas?: number
  quantidadeSuplentes?: number
  totalChapas: number
  totalEleitores: number
  createdAt: string
  updatedAt?: string
}

export interface CreateEleicaoRequest {
  nome: string
  descricao?: string
  tipo: number
  ano: number
  mandato?: number
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  regionalId?: string
  modoVotacao: number
  quantidadeVagas?: number
  quantidadeSuplentes?: number
}

export interface UpdateEleicaoRequest {
  nome?: string
  descricao?: string
  dataInicio?: string
  dataFim?: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  dataApuracao?: string
  modoVotacao?: number
  quantidadeVagas?: number
  quantidadeSuplentes?: number
}

export const eleicoesService = {
  getAll: async (): Promise<Eleicao[]> => {
    const response = await api.get<Eleicao[]>('/eleicao')
    return response.data
  },

  getById: async (id: string): Promise<Eleicao> => {
    const response = await api.get<Eleicao>(`/eleicao/${id}`)
    return response.data
  },

  getByStatus: async (status: number): Promise<Eleicao[]> => {
    const response = await api.get<Eleicao[]>(`/eleicao/status/${status}`)
    return response.data
  },

  getAtivas: async (): Promise<Eleicao[]> => {
    const response = await api.get<Eleicao[]>('/eleicao/ativas')
    return response.data
  },

  create: async (data: CreateEleicaoRequest): Promise<Eleicao> => {
    const response = await api.post<Eleicao>('/eleicao', data)
    return response.data
  },

  update: async (id: string, data: UpdateEleicaoRequest): Promise<Eleicao> => {
    const response = await api.put<Eleicao>(`/eleicao/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/eleicao/${id}`)
  },

  iniciar: async (id: string): Promise<Eleicao> => {
    const response = await api.post<Eleicao>(`/eleicao/${id}/iniciar`)
    return response.data
  },

  encerrar: async (id: string): Promise<Eleicao> => {
    const response = await api.post<Eleicao>(`/eleicao/${id}/encerrar`)
    return response.data
  },

  suspender: async (id: string, motivo: string): Promise<Eleicao> => {
    const response = await api.post<Eleicao>(`/eleicao/${id}/suspender`, { motivo })
    return response.data
  },

  cancelar: async (id: string, motivo: string): Promise<Eleicao> => {
    const response = await api.post<Eleicao>(`/eleicao/${id}/cancelar`, { motivo })
    return response.data
  },
}
