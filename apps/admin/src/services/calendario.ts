import api from './api'

export interface CalendarioEvento {
  id: string
  eleicaoId: string
  eleicaoNome: string
  titulo: string
  descricao?: string
  tipo: number
  status: number
  dataInicio: string
  dataFim: string
  diaInteiro: boolean
  obrigatorio: boolean
  createdAt: string
  updatedAt?: string
}

export const calendarioService = {
  getAll: async (params?: {
    eleicaoId?: string
    tipo?: number
  }): Promise<CalendarioEvento[]> => {
    const response = await api.get<CalendarioEvento[]>('/calendario', { params })
    return response.data
  },

  getById: async (id: string): Promise<CalendarioEvento> => {
    const response = await api.get<CalendarioEvento>(`/calendario/${id}`)
    return response.data
  },

  getByEleicao: async (eleicaoId: string): Promise<CalendarioEvento[]> => {
    const response = await api.get<CalendarioEvento[]>(`/calendario/eleicao/${eleicaoId}`)
    return response.data
  },

  create: async (data: {
    eleicaoId: string
    titulo: string
    descricao?: string
    tipo: number
    dataInicio: string
    dataFim: string
    diaInteiro?: boolean
    obrigatorio?: boolean
  }): Promise<CalendarioEvento> => {
    const response = await api.post<CalendarioEvento>('/calendario', data)
    return response.data
  },

  update: async (id: string, data: {
    titulo?: string
    descricao?: string
    tipo?: number
    dataInicio?: string
    dataFim?: string
    diaInteiro?: boolean
    obrigatorio?: boolean
  }): Promise<CalendarioEvento> => {
    const response = await api.put<CalendarioEvento>(`/calendario/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await api.delete(`/calendario/${id}`)
  },

  iniciar: async (id: string): Promise<CalendarioEvento> => {
    const response = await api.post<CalendarioEvento>(`/calendario/${id}/iniciar`)
    return response.data
  },

  concluir: async (id: string): Promise<CalendarioEvento> => {
    const response = await api.post<CalendarioEvento>(`/calendario/${id}/concluir`)
    return response.data
  },
}
