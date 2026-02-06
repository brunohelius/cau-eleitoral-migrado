import api from './api'

export interface AuditoriaLog {
  id: string
  dataHora: string
  acao: string
  entidadeTipo?: string
  entidadeId?: string
  entidadeNome?: string
  usuarioId?: string
  usuarioNome?: string
  usuarioEmail?: string
  ipAddress?: string
  userAgent?: string
  sucesso: boolean
  mensagem?: string
}

export interface AuditoriaLogDetalhe extends AuditoriaLog {
  dadosAnteriores?: string
  dadosNovos?: string
  requestPath?: string
  requestMethod?: string
  responseStatusCode?: number
}

export interface PagedResult<T> {
  items: T[]
  totalCount: number
  page: number
  pageSize: number
}

export interface EstatisticasAuditoria {
  totalLogs: number
  logsHoje: number
  logsSemana: number
  logsMes: number
  errosTotal: number
  logsPorAcao: { acao: string; total: number }[]
  logsPorEntidade: { entidadeTipo: string; total: number }[]
  usuariosMaisAtivos: { usuarioId: string; usuarioNome: string; totalAcoes: number }[]
  logsPorDia: { data: string; total: number; erros: number }[]
}

export const auditoriaService = {
  getAll: async (params?: {
    acao?: string
    entidadeTipo?: string
    usuarioId?: string
    dataInicio?: string
    dataFim?: string
    sucesso?: boolean
    busca?: string
    page?: number
    pageSize?: number
  }): Promise<PagedResult<AuditoriaLog>> => {
    const response = await api.get<PagedResult<AuditoriaLog>>('/auditoria', { params })
    return response.data
  },

  getById: async (id: string): Promise<AuditoriaLogDetalhe> => {
    const response = await api.get<AuditoriaLogDetalhe>(`/auditoria/${id}`)
    return response.data
  },

  getEstatisticas: async (params?: {
    dataInicio?: string
    dataFim?: string
  }): Promise<EstatisticasAuditoria> => {
    const response = await api.get<EstatisticasAuditoria>('/auditoria/estatisticas', { params })
    return response.data
  },

  getAcoes: async (): Promise<string[]> => {
    const response = await api.get<string[]>('/auditoria/acoes')
    return response.data
  },

  getTiposEntidade: async (): Promise<string[]> => {
    const response = await api.get<string[]>('/auditoria/entidades')
    return response.data
  },

  exportar: async (params?: {
    acao?: string
    entidadeTipo?: string
    dataInicio?: string
    dataFim?: string
    formato?: string
  }): Promise<Blob> => {
    const response = await api.get('/auditoria/exportar', {
      params,
      responseType: 'blob',
    })
    return response.data
  },
}
