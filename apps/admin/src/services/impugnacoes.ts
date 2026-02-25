import api, { mapPagedResponse } from './api'
import {
  FaseImpugnacao,
  StatusImpugnacao,
  TipoImpugnacao,
} from '@/types'
import type {
  AnexoImpugnacao,
  CreateImpugnacaoRequest,
  DefesaImpugnacao,
  Impugnacao,
  ImpugnacaoListParams,
  PaginatedResponse,
  ParecerImpugnacao,
  RecursoImpugnacao,
  UpdateImpugnacaoRequest,
} from '@/types'

export type {
  AnexoImpugnacao,
  CreateImpugnacaoRequest,
  DefesaImpugnacao,
  Impugnacao,
  ImpugnacaoListParams,
  PaginatedResponse,
  ParecerImpugnacao,
  RecursoImpugnacao,
  UpdateImpugnacaoRequest,
} from '@/types'

export { FaseImpugnacao, StatusImpugnacao, TipoImpugnacao }

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

function toPagedResult<T>(
  payload: unknown,
  fallbackPage = 1,
  fallbackPageSize = 20
): PaginatedResponse<T> {
  if (Array.isArray(payload)) {
    return {
      data: payload as T[],
      total: payload.length,
      page: fallbackPage,
      pageSize: fallbackPageSize,
      totalPages: 1,
    }
  }

  return mapPagedResponse<T>(payload as Record<string, unknown>)
}

function parsePrazoParaDias(prazo: string): number {
  const asNumber = Number(prazo)
  if (Number.isFinite(asNumber) && asNumber > 0) {
    return Math.floor(asNumber)
  }

  const targetDate = new Date(`${prazo}T23:59:59`)
  if (Number.isNaN(targetDate.getTime())) {
    return 5
  }

  const now = new Date()
  const diffMs = targetDate.getTime() - now.getTime()
  const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24))
  return Math.max(1, diffDays)
}

function mapDecisaoToRecursoStatus(decisao: StatusImpugnacao): number {
  if (decisao === StatusImpugnacao.DEFERIDA) {
    return 4 // StatusRecurso.Provido
  }
  if (decisao === StatusImpugnacao.INDEFERIDA) {
    return 6 // StatusRecurso.Desprovido
  }
  return 5 // StatusRecurso.DesprovimidoParcialmente
}

function mapDecisaoToResultadoBackend(decisao: StatusImpugnacao): number {
  if (decisao === StatusImpugnacao.DEFERIDA) return 8
  if (decisao === StatusImpugnacao.INDEFERIDA) return 9
  if (decisao === StatusImpugnacao.PARCIALMENTE_DEFERIDA) return 10
  if (decisao === StatusImpugnacao.ARQUIVADA) return 11
  if (decisao === StatusImpugnacao.RECURSO) return 12
  if (decisao === StatusImpugnacao.EM_ANALISE) return 1
  return 7
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
    return toPagedResult<Impugnacao>(response.data, params?.page || 1, params?.pageSize || 20)
  },

  getByChapa: async (chapaId: string): Promise<Impugnacao[]> => {
    const response = await api.get<Impugnacao[]>(`/impugnacao/chapa/${chapaId}`)
    return response.data
  },

  getByCandidato: async (candidatoId: string): Promise<Impugnacao[]> => {
    const response = await api.get('/impugnacao', {
      params: { page: 1, pageSize: 500 },
    })
    const paged = mapPagedResponse<Impugnacao>(response.data)
    return paged.data.filter((item) => item.candidatoId === candidatoId || item.impugnanteId === candidatoId)
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
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/solicitar-alegacoes`, {
      prazoEmDias: parsePrazoParaDias(prazo),
    })
    return response.data
  },

  apresentarDefesa: async (id: string, data: ApresentarDefesaRequest): Promise<Impugnacao> => {
    await api.post(`/impugnacao/${id}/defesas`, {
      conteudo: data.texto,
    })
    const refreshed = await api.get<Impugnacao>(`/impugnacao/${id}`)
    return refreshed.data
  },

  emitirParecer: async (id: string, data: EmitirParecerRequest): Promise<Impugnacao> => {
    const shouldForward = data.recomendacao !== undefined
    if (shouldForward) {
      await api.post(`/impugnacao/${id}/encaminhar-julgamento`, {})
    }
    const refreshed = await api.get<Impugnacao>(`/impugnacao/${id}`)
    return refreshed.data
  },

  encaminharJulgamento: async (id: string): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/encaminhar-julgamento`)
    return response.data
  },

  proferirDecisao: async (id: string, data: ProferirDecisaoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/julgar-completo`, {
      resultado: mapDecisaoToResultadoBackend(data.decisao),
      decisao: data.fundamentacao,
      fundamentacao: data.fundamentacao,
    })
    return response.data
  },

  interporRecurso: async (id: string, data: InterporRecursoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/interpor-recurso`, {
      tipo: 0,
      fundamentacao: data.fundamentacao,
    })
    return response.data
  },

  julgarRecurso: async (id: string, recursoId: string, data: ProferirDecisaoRequest): Promise<Impugnacao> => {
    const response = await api.post<Impugnacao>(`/impugnacao/${id}/recursos/${recursoId}/julgar`, {
      status: mapDecisaoToRecursoStatus(data.decisao),
      decisao: data.fundamentacao,
    })
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
    const response = await api.get<any[]>(`/impugnacao/${impugnacaoId}/pedidos`)
    return (response.data || []).map((pedido) => ({
      id: pedido.id,
      impugnacaoId,
      nome: pedido.descricao || 'Anexo',
      tipo: 'pedido',
      arquivoUrl: '',
      tamanho: 0,
      createdAt: pedido.dataPedido || new Date().toISOString(),
    }))
  },

  uploadAnexo: async (impugnacaoId: string, arquivo: File, nome?: string): Promise<AnexoImpugnacao> => {
    const arquivoNome = nome || arquivo.name
    const response = await api.post<any>(`/impugnacao/${impugnacaoId}/pedidos`, {
      descricao: `Documento anexo: ${arquivoNome}.`,
      fundamentacao: `Arquivo recebido (${arquivo.type || 'application/octet-stream'}, ${arquivo.size} bytes).`,
    })

    return {
      id: response.data.id,
      impugnacaoId,
      nome: arquivoNome,
      tipo: arquivo.type || 'application/octet-stream',
      arquivoUrl: '',
      tamanho: arquivo.size,
      createdAt: response.data.dataPedido || new Date().toISOString(),
    }
  },

  removeAnexo: async (impugnacaoId: string, anexoId: string): Promise<void> => {
    await api.delete(`/impugnacao/${impugnacaoId}/pedidos/${anexoId}`)
  },

  uploadAnexoDefesa: async (impugnacaoId: string, defesaId: string, arquivo: File): Promise<AnexoImpugnacao> => {
    return {
      id: defesaId,
      impugnacaoId,
      nome: arquivo.name,
      tipo: arquivo.type || 'application/octet-stream',
      arquivoUrl: '',
      tamanho: arquivo.size,
      createdAt: new Date().toISOString(),
    }
  },

  uploadAnexoRecurso: async (impugnacaoId: string, recursoId: string, arquivo: File): Promise<AnexoImpugnacao> => {
    return {
      id: recursoId,
      impugnacaoId,
      nome: arquivo.name,
      tipo: arquivo.type || 'application/octet-stream',
      arquivoUrl: '',
      tamanho: arquivo.size,
      createdAt: new Date().toISOString(),
    }
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
    const stats = response.data || {}
    return {
      total: stats.total || 0,
      pendentes: stats.pendentes || 0,
      emAnalise: stats.emAnalise || 0,
      deferidas: stats.deferidas ?? stats.procedentes ?? 0,
      indeferidas: stats.indeferidas ?? stats.improcedentes ?? 0,
      parcialmenteDeferidas: stats.parcialmenteDeferidas || 0,
      emRecurso: stats.emRecurso || 0,
      arquivadas: stats.arquivadas || 0,
      porTipo: stats.porTipo || {},
      porFase: stats.porFase || {},
    }
  },

  // Timeline
  getTimeline: async (impugnacaoId: string): Promise<{
    data: string
    evento: string
    descricao: string
    usuarioNome?: string
  }[]> => {
    const response = await api.get<any[]>(`/impugnacao/${impugnacaoId}/historico`)
    return (response.data || []).map((item) => ({
      data: item.dataAlteracao || item.data || new Date().toISOString(),
      evento: item.statusNovoNome || item.acao || 'Atualizacao',
      descricao: item.descricao || '',
      usuarioNome: item.usuarioNome || undefined,
    }))
  },

  // Reports
  gerarRelatorio: async (params: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
    formato?: 'pdf' | 'xlsx'
  }): Promise<Blob> => {
    let eleicaoId = params.eleicaoId
    if (!eleicaoId) {
      const eleicoesAtivas = await api.get<any[]>('/eleicao/ativas')
      eleicaoId = eleicoesAtivas.data?.[0]?.id
    }
    if (!eleicaoId) {
      throw new Error('Selecione uma eleicao para exportar o relatorio de impugnacoes')
    }

    const response = await api.get(`/relatorio/impugnacoes/${eleicaoId}`, {
      params: { formato: params.formato || 'pdf' },
      responseType: 'blob',
    })
    return response.data
  },
}
