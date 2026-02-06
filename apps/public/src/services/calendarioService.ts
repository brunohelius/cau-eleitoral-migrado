import api, { extractApiError } from './api'
import { TipoCalendario, StatusCalendario } from './types'

// ============================================
// Calendario Interfaces
// ============================================

export interface CalendarioEvento {
  id: string
  eleicaoId: string
  eleicaoNome: string
  titulo: string
  descricao?: string
  tipo: TipoCalendario
  status: StatusCalendario
  dataInicio: string
  dataFim: string
  diaInteiro: boolean
  local?: string
  observacoes?: string
  obrigatorio: boolean
  createdAt: string
  updatedAt?: string
}

export interface CalendarioEventoResumido {
  id: string
  eleicaoId: string
  titulo: string
  tipo: TipoCalendario
  status: StatusCalendario
  dataInicio: string
  dataFim: string
  diaInteiro: boolean
}

// ============================================
// Filters
// ============================================

export interface CalendarioFilters {
  eleicaoId?: string
  tipo?: TipoCalendario
  dataInicio?: string
  dataFim?: string
}

// ============================================
// Helper Functions
// ============================================

export const getTipoCalendarioLabel = (tipo: TipoCalendario): string => {
  const labels: Record<TipoCalendario, string> = {
    [TipoCalendario.INSCRICAO]: 'Inscricao',
    [TipoCalendario.IMPUGNACAO]: 'Impugnacao',
    [TipoCalendario.DEFESA]: 'Defesa',
    [TipoCalendario.JULGAMENTO]: 'Julgamento',
    [TipoCalendario.RECURSO]: 'Recurso',
    [TipoCalendario.PROPAGANDA]: 'Propaganda Eleitoral',
    [TipoCalendario.VOTACAO]: 'Votacao',
    [TipoCalendario.APURACAO]: 'Apuracao',
    [TipoCalendario.RESULTADO]: 'Resultado',
    [TipoCalendario.DIPLOMACAO]: 'Diplomacao',
  }
  return labels[tipo] || 'Outros'
}

export const getTipoCalendarioColor = (tipo: TipoCalendario): string => {
  const colors: Record<TipoCalendario, string> = {
    [TipoCalendario.INSCRICAO]: 'blue',
    [TipoCalendario.IMPUGNACAO]: 'red',
    [TipoCalendario.DEFESA]: 'orange',
    [TipoCalendario.JULGAMENTO]: 'purple',
    [TipoCalendario.RECURSO]: 'red',
    [TipoCalendario.PROPAGANDA]: 'orange',
    [TipoCalendario.VOTACAO]: 'green',
    [TipoCalendario.APURACAO]: 'yellow',
    [TipoCalendario.RESULTADO]: 'cyan',
    [TipoCalendario.DIPLOMACAO]: 'cyan',
  }
  return colors[tipo] || 'gray'
}

export const getStatusCalendarioLabel = (status: StatusCalendario): string => {
  const labels: Record<StatusCalendario, string> = {
    [StatusCalendario.PENDENTE]: 'Pendente',
    [StatusCalendario.EM_ANDAMENTO]: 'Em Andamento',
    [StatusCalendario.CONCLUIDO]: 'Concluido',
    [StatusCalendario.CANCELADO]: 'Cancelado',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusCalendarioColor = (status: StatusCalendario): string => {
  const colors: Record<StatusCalendario, string> = {
    [StatusCalendario.PENDENTE]: 'gray',
    [StatusCalendario.EM_ANDAMENTO]: 'blue',
    [StatusCalendario.CONCLUIDO]: 'green',
    [StatusCalendario.CANCELADO]: 'red',
  }
  return colors[status] || 'gray'
}

// ============================================
// Calendario Service
// ============================================

export const calendarioService = {
  // ========================================
  // Public Endpoints (AllowAnonymous)
  // ========================================

  /**
   * Lista todos os eventos do calendario
   */
  getAll: async (filters?: CalendarioFilters): Promise<CalendarioEvento[]> => {
    try {
      const params = new URLSearchParams()
      if (filters?.eleicaoId) params.append('eleicaoId', filters.eleicaoId)
      if (filters?.tipo !== undefined) params.append('tipo', filters.tipo.toString())

      const queryString = params.toString()
      const url = `/calendario${queryString ? `?${queryString}` : ''}`

      const response = await api.get<CalendarioEvento[]>(url)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eventos do calendario')
    }
  },

  /**
   * Obtem um evento pelo ID
   */
  getById: async (id: string): Promise<CalendarioEvento> => {
    try {
      const response = await api.get<CalendarioEvento>(`/calendario/${id}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter evento')
    }
  },

  /**
   * Lista eventos por eleicao
   */
  getByEleicao: async (eleicaoId: string): Promise<CalendarioEvento[]> => {
    try {
      const response = await api.get<CalendarioEvento[]>(`/calendario/eleicao/${eleicaoId}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eventos da eleicao')
    }
  },

  /**
   * Lista eventos proximos (dentro dos proximos dias)
   */
  getProximos: async (dias: number = 7, eleicaoId?: string): Promise<CalendarioEvento[]> => {
    try {
      const params = new URLSearchParams()
      params.append('dias', dias.toString())
      if (eleicaoId) params.append('eleicaoId', eleicaoId)

      const response = await api.get<CalendarioEvento[]>(`/calendario/proximos?${params.toString()}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar proximos eventos')
    }
  },

  /**
   * Lista eventos em andamento
   */
  getEmAndamento: async (eleicaoId?: string): Promise<CalendarioEvento[]> => {
    try {
      const params = eleicaoId ? `?eleicaoId=${eleicaoId}` : ''
      const response = await api.get<CalendarioEvento[]>(`/calendario/em-andamento${params}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eventos em andamento')
    }
  },

  /**
   * Lista eventos por periodo
   */
  getByPeriodo: async (
    dataInicio: string,
    dataFim: string,
    eleicaoId?: string
  ): Promise<CalendarioEvento[]> => {
    try {
      const params = new URLSearchParams()
      params.append('dataInicio', dataInicio)
      params.append('dataFim', dataFim)
      if (eleicaoId) params.append('eleicaoId', eleicaoId)

      const response = await api.get<CalendarioEvento[]>(`/calendario/periodo?${params.toString()}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar eventos do periodo')
    }
  },

  // ========================================
  // Utility Methods
  // ========================================

  /**
   * Verifica se o evento esta em andamento
   */
  isEmAndamento: (evento: CalendarioEvento): boolean => {
    const agora = new Date()
    const inicio = new Date(evento.dataInicio)
    const fim = new Date(evento.dataFim)
    return agora >= inicio && agora <= fim
  },

  /**
   * Verifica se o evento ja passou
   */
  isPassado: (evento: CalendarioEvento): boolean => {
    const agora = new Date()
    const fim = new Date(evento.dataFim)
    return agora > fim
  },

  /**
   * Verifica se o evento e futuro
   */
  isFuturo: (evento: CalendarioEvento): boolean => {
    const agora = new Date()
    const inicio = new Date(evento.dataInicio)
    return agora < inicio
  },

  /**
   * Calcula dias ate o evento
   */
  diasAteEvento: (evento: CalendarioEvento): number => {
    const agora = new Date()
    const inicio = new Date(evento.dataInicio)
    const diffTime = inicio.getTime() - agora.getTime()
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  },

  /**
   * Calcula duracao do evento em dias
   */
  duracaoEmDias: (evento: CalendarioEvento): number => {
    const inicio = new Date(evento.dataInicio)
    const fim = new Date(evento.dataFim)
    const diffTime = fim.getTime() - inicio.getTime()
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  },

  /**
   * Formata periodo do evento
   */
  formatarPeriodo: (evento: CalendarioEvento): string => {
    const opcoes: Intl.DateTimeFormatOptions = {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    }

    const inicio = new Date(evento.dataInicio).toLocaleDateString('pt-BR', opcoes)
    const fim = new Date(evento.dataFim).toLocaleDateString('pt-BR', opcoes)

    if (inicio === fim) {
      return inicio
    }

    return `${inicio} a ${fim}`
  },

  /**
   * Formata hora do evento
   */
  formatarHorario: (evento: CalendarioEvento): string | null => {
    if (evento.diaInteiro) return null

    const opcoes: Intl.DateTimeFormatOptions = {
      hour: '2-digit',
      minute: '2-digit',
    }

    const inicio = new Date(evento.dataInicio).toLocaleTimeString('pt-BR', opcoes)
    const fim = new Date(evento.dataFim).toLocaleTimeString('pt-BR', opcoes)

    return `${inicio} - ${fim}`
  },

  /**
   * Agrupa eventos por mes
   */
  agruparPorMes: (eventos: CalendarioEvento[]): Record<string, CalendarioEvento[]> => {
    const grupos: Record<string, CalendarioEvento[]> = {}

    eventos.forEach((evento) => {
      const data = new Date(evento.dataInicio)
      const chave = `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}`

      if (!grupos[chave]) {
        grupos[chave] = []
      }
      grupos[chave].push(evento)
    })

    return grupos
  },

  /**
   * Filtra eventos por tipo
   */
  filtrarPorTipo: (eventos: CalendarioEvento[], tipo: TipoCalendario): CalendarioEvento[] => {
    return eventos.filter((e) => e.tipo === tipo)
  },

  /**
   * Ordena eventos por data
   */
  ordenarPorData: (eventos: CalendarioEvento[], desc: boolean = false): CalendarioEvento[] => {
    return [...eventos].sort((a, b) => {
      const dataA = new Date(a.dataInicio).getTime()
      const dataB = new Date(b.dataInicio).getTime()
      return desc ? dataB - dataA : dataA - dataB
    })
  },

  /**
   * Obtem o proximo evento de um tipo especifico
   */
  getProximoEventoPorTipo: (
    eventos: CalendarioEvento[],
    tipo: TipoCalendario
  ): CalendarioEvento | null => {
    const agora = new Date()
    const filtrados = eventos
      .filter((e) => e.tipo === tipo && new Date(e.dataInicio) > agora)
      .sort((a, b) => new Date(a.dataInicio).getTime() - new Date(b.dataInicio).getTime())

    return filtrados[0] || null
  },

  /**
   * Verifica se ha evento de votacao ativo
   */
  temVotacaoAtiva: (eventos: CalendarioEvento[]): boolean => {
    return eventos.some(
      (e) => e.tipo === TipoCalendario.VOTACAO && calendarioService.isEmAndamento(e)
    )
  },

  /**
   * Obtem evento de votacao atual
   */
  getEventoVotacaoAtual: (eventos: CalendarioEvento[]): CalendarioEvento | null => {
    return (
      eventos.find(
        (e) => e.tipo === TipoCalendario.VOTACAO && calendarioService.isEmAndamento(e)
      ) || null
    )
  },
}

// Re-export enums for convenience
export { TipoCalendario, StatusCalendario }
