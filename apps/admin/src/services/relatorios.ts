import api from './api'

// Enums
export enum TipoRelatorio {
  ELEICAO_GERAL = 0,
  VOTACAO = 1,
  APURACAO = 2,
  CHAPAS = 3,
  CANDIDATOS = 4,
  ELEITORES = 5,
  DENUNCIAS = 6,
  IMPUGNACOES = 7,
  JULGAMENTOS = 8,
  AUDITORIA = 9,
  PARTICIPACAO = 10,
  COMPARATIVO = 11,
  ESTATISTICO = 12,
}

export enum FormatoRelatorio {
  PDF = 'pdf',
  XLSX = 'xlsx',
  CSV = 'csv',
  HTML = 'html',
}

export enum StatusRelatorio {
  PENDENTE = 0,
  PROCESSANDO = 1,
  CONCLUIDO = 2,
  ERRO = 3,
  EXPIRADO = 4,
}

// Interfaces
export interface FiltroRelatorio {
  eleicaoId?: string
  chapaId?: string
  regionalId?: string
  dataInicio?: string
  dataFim?: string
  status?: number[]
  tipo?: number[]
  agrupamento?: string
  ordenacao?: string
  limite?: number
  incluirDetalhes?: boolean
  incluirGraficos?: boolean
}

export interface RelatorioAgendado {
  id: string
  nome: string
  tipo: TipoRelatorio
  formato: FormatoRelatorio
  filtros: FiltroRelatorio
  destinatarios: string[]
  cronExpression: string
  ativo: boolean
  proximaExecucao?: string
  ultimaExecucao?: string
  createdAt: string
}

export interface RelatorioGerado {
  id: string
  nome: string
  tipo: TipoRelatorio
  formato: FormatoRelatorio
  status: StatusRelatorio
  arquivoUrl?: string
  tamanhoArquivo?: number
  filtrosAplicados: FiltroRelatorio
  geradoPorId: string
  geradoPorNome: string
  tempoProcessamento?: number
  erro?: string
  expiraEm?: string
  createdAt: string
}

export interface TemplateRelatorio {
  id: string
  nome: string
  descricao?: string
  tipo: TipoRelatorio
  formatosDisponiveis: FormatoRelatorio[]
  filtrosDisponiveis: string[]
  camposDisponiveis: { nome: string; descricao: string; obrigatorio: boolean }[]
  previewUrl?: string
}

export interface DadosRelatorioVotacao {
  eleicaoId: string
  eleicaoNome: string
  totalEleitores: number
  totalVotos: number
  percentualParticipacao: number
  votosValidos: number
  votosNulos: number
  votosBranco: number
  votosPorChapa: { chapaId: string; chapaNome: string; votos: number; percentual: number }[]
  votosPorHora: { hora: string; quantidade: number }[]
  votosPorRegional?: { regional: string; votos: number; percentual: number }[]
}

export interface DadosRelatorioApuracao {
  eleicaoId: string
  eleicaoNome: string
  dataApuracao: string
  statusApuracao: string
  resultadoFinal: {
    chapaId: string
    chapaNome: string
    votos: number
    percentual: number
    eleita: boolean
  }[]
  resumoVotos: {
    validos: number
    nulos: number
    branco: number
    total: number
  }
  auditoria: {
    totalUrnas: number
    urnasApuradas: number
    inconsistencias: number
  }
}

export interface DadosRelatorioParticipacao {
  eleicaoId: string
  eleicaoNome: string
  totalElegiveisVotar: number
  totalVotantes: number
  percentualParticipacao: number
  participacaoPorRegional: { regional: string; elegiveis: number; votantes: number; percentual: number }[]
  participacaoPorDia?: { data: string; votantes: number }[]
  comparativoEleicaoAnterior?: {
    eleicaoAnterior: string
    participacaoAnterior: number
    variacaoPercentual: number
  }
}

export const relatoriosService = {
  // Templates
  getTemplates: async (): Promise<TemplateRelatorio[]> => {
    const response = await api.get<TemplateRelatorio[]>('/relatorio/templates')
    return response.data
  },

  getTemplateById: async (id: string): Promise<TemplateRelatorio> => {
    const response = await api.get<TemplateRelatorio>(`/relatorio/templates/${id}`)
    return response.data
  },

  // Generate Reports
  gerar: async (
    tipo: TipoRelatorio,
    formato: FormatoRelatorio,
    filtros: FiltroRelatorio,
    nomePersonalizado?: string
  ): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/gerar', {
      tipo,
      formato,
      filtros,
      nome: nomePersonalizado,
    })
    return response.data
  },

  gerarVotacao: async (eleicaoId: string, formato: FormatoRelatorio, filtros?: Partial<FiltroRelatorio>): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/votacao', {
      eleicaoId,
      formato,
      ...filtros,
    })
    return response.data
  },

  gerarApuracao: async (eleicaoId: string, formato: FormatoRelatorio): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/apuracao', {
      eleicaoId,
      formato,
    })
    return response.data
  },

  gerarChapas: async (eleicaoId: string, formato: FormatoRelatorio, filtros?: Partial<FiltroRelatorio>): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/chapas', {
      eleicaoId,
      formato,
      ...filtros,
    })
    return response.data
  },

  gerarParticipacao: async (eleicaoId: string, formato: FormatoRelatorio): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/participacao', {
      eleicaoId,
      formato,
    })
    return response.data
  },

  gerarAuditoria: async (eleicaoId: string, formato: FormatoRelatorio, filtros?: Partial<FiltroRelatorio>): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/auditoria', {
      eleicaoId,
      formato,
      ...filtros,
    })
    return response.data
  },

  gerarDenuncias: async (filtros: FiltroRelatorio, formato: FormatoRelatorio): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/denuncias', {
      filtros,
      formato,
    })
    return response.data
  },

  gerarImpugnacoes: async (filtros: FiltroRelatorio, formato: FormatoRelatorio): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/impugnacoes', {
      filtros,
      formato,
    })
    return response.data
  },

  gerarComparativo: async (eleicaoIds: string[], formato: FormatoRelatorio): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>('/relatorio/comparativo', {
      eleicaoIds,
      formato,
    })
    return response.data
  },

  // Generated Reports
  getGerados: async (params?: {
    tipo?: TipoRelatorio
    status?: StatusRelatorio
    dataInicio?: string
    dataFim?: string
    page?: number
    pageSize?: number
  }): Promise<{ data: RelatorioGerado[]; total: number }> => {
    const response = await api.get('/relatorio/gerados', { params })
    return response.data
  },

  getGeradoById: async (id: string): Promise<RelatorioGerado> => {
    const response = await api.get<RelatorioGerado>(`/relatorio/gerados/${id}`)
    return response.data
  },

  download: async (id: string): Promise<Blob> => {
    const response = await api.get(`/relatorio/gerados/${id}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  deletarGerado: async (id: string): Promise<void> => {
    await api.delete(`/relatorio/gerados/${id}`)
  },

  regenerar: async (id: string): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>(`/relatorio/gerados/${id}/regenerar`)
    return response.data
  },

  // Scheduled Reports
  getAgendados: async (): Promise<RelatorioAgendado[]> => {
    const response = await api.get<RelatorioAgendado[]>('/relatorio/agendados')
    return response.data
  },

  criarAgendado: async (data: {
    nome: string
    tipo: TipoRelatorio
    formato: FormatoRelatorio
    filtros: FiltroRelatorio
    cronExpression: string
    destinatarios: string[]
  }): Promise<RelatorioAgendado> => {
    const response = await api.post<RelatorioAgendado>('/relatorio/agendados', data)
    return response.data
  },

  atualizarAgendado: async (id: string, data: Partial<{
    nome: string
    filtros: FiltroRelatorio
    cronExpression: string
    destinatarios: string[]
    ativo: boolean
  }>): Promise<RelatorioAgendado> => {
    const response = await api.put<RelatorioAgendado>(`/relatorio/agendados/${id}`, data)
    return response.data
  },

  deletarAgendado: async (id: string): Promise<void> => {
    await api.delete(`/relatorio/agendados/${id}`)
  },

  executarAgendado: async (id: string): Promise<RelatorioGerado> => {
    const response = await api.post<RelatorioGerado>(`/relatorio/agendados/${id}/executar`)
    return response.data
  },

  // Data (for preview/dashboard)
  getDadosVotacao: async (eleicaoId: string): Promise<DadosRelatorioVotacao> => {
    const response = await api.get<DadosRelatorioVotacao>(`/relatorio/dados/votacao/${eleicaoId}`)
    return response.data
  },

  getDadosApuracao: async (eleicaoId: string): Promise<DadosRelatorioApuracao> => {
    const response = await api.get<DadosRelatorioApuracao>(`/relatorio/dados/apuracao/${eleicaoId}`)
    return response.data
  },

  getDadosParticipacao: async (eleicaoId: string): Promise<DadosRelatorioParticipacao> => {
    const response = await api.get<DadosRelatorioParticipacao>(`/relatorio/dados/participacao/${eleicaoId}`)
    return response.data
  },

  // Email
  enviarPorEmail: async (id: string, destinatarios: string[], assunto?: string, mensagem?: string): Promise<void> => {
    await api.post(`/relatorio/gerados/${id}/enviar-email`, {
      destinatarios,
      assunto,
      mensagem,
    })
  },
}
