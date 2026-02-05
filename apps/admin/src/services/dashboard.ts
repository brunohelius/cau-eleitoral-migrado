import api from './api'

// Interfaces
export interface EstatisticasGerais {
  eleicoesAtivas: number
  eleicoesTotais: number
  eleitoresTotal: number
  chapasTotal: number
  votosRegistrados: number
  denunciasPendentes: number
  impugnacoesPendentes: number
  julgamentosAgendados: number
}

export interface EstatisticasEleicao {
  eleicaoId: string
  eleicaoNome: string
  status: number
  fase: number
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  totalEleitores: number
  totalChapas: number
  chapasAprovadas: number
  chapasPendentes: number
  votosRegistrados: number
  participacaoPercentual: number
  denuncias: number
  impugnacoes: number
}

export interface ResumoVotacao {
  eleicaoId: string
  totalEleitores: number
  votosRegistrados: number
  participacaoPercentual: number
  votosValidos: number
  votosNulos: number
  votosBranco: number
  ultimaAtualizacao: string
  votosPorHora: { hora: string; quantidade: number }[]
  votosPorChapa: { chapaId: string; chapaNome: string; votos: number; percentual: number }[]
}

export interface AtividadeRecente {
  id: string
  tipo: 'eleicao' | 'chapa' | 'voto' | 'denuncia' | 'impugnacao' | 'julgamento' | 'usuario'
  acao: string
  descricao: string
  usuarioId?: string
  usuarioNome?: string
  entidadeId?: string
  entidadeNome?: string
  metadata?: Record<string, unknown>
  createdAt: string
}

export interface AlertaSistema {
  id: string
  tipo: 'info' | 'warning' | 'error' | 'success'
  titulo: string
  mensagem: string
  acao?: {
    label: string
    url: string
  }
  lido: boolean
  createdAt: string
}

export interface TarefaPendente {
  id: string
  tipo: 'analise_chapa' | 'analise_denuncia' | 'julgamento' | 'aprovacao_documento' | 'verificacao_eleitor'
  titulo: string
  descricao: string
  prioridade: 'baixa' | 'normal' | 'alta' | 'urgente'
  entidadeId: string
  entidadeTipo: string
  prazo?: string
  atribuidoA?: string
  createdAt: string
}

export interface GraficoParticipacao {
  labels: string[]
  dados: number[]
  comparativo?: number[]
  tipo: 'diario' | 'horario' | 'regional'
}

export interface GraficoChapas {
  chapaId: string
  chapaNome: string
  chapaNumero: number
  votos: number
  percentual: number
  cor?: string
}

export interface MetricasTempo {
  periodo: string
  eleicoesRealizadas: number
  participacaoMedia: number
  tempoMedioVotacao: number
  denunciasRegistradas: number
  impugnacoesJulgadas: number
}

export const dashboardService = {
  // Estatisticas Gerais
  getEstatisticasGerais: async (): Promise<EstatisticasGerais> => {
    const response = await api.get<EstatisticasGerais>('/dashboard/estatisticas')
    return response.data
  },

  // Eleicoes
  getEstatisticasEleicoes: async (): Promise<EstatisticasEleicao[]> => {
    const response = await api.get<EstatisticasEleicao[]>('/dashboard/eleicoes')
    return response.data
  },

  getEstatisticasEleicao: async (eleicaoId: string): Promise<EstatisticasEleicao> => {
    const response = await api.get<EstatisticasEleicao>(`/dashboard/eleicoes/${eleicaoId}`)
    return response.data
  },

  // Votacao
  getResumoVotacao: async (eleicaoId: string): Promise<ResumoVotacao> => {
    const response = await api.get<ResumoVotacao>(`/dashboard/votacao/${eleicaoId}`)
    return response.data
  },

  getResumoVotacaoEmTempoReal: async (eleicaoId: string): Promise<ResumoVotacao> => {
    const response = await api.get<ResumoVotacao>(`/dashboard/votacao/${eleicaoId}/tempo-real`)
    return response.data
  },

  // Atividades
  getAtividadesRecentes: async (params?: {
    tipo?: string
    limite?: number
    page?: number
  }): Promise<{ data: AtividadeRecente[]; total: number }> => {
    const response = await api.get('/dashboard/atividades', { params })
    return response.data
  },

  // Alertas
  getAlertas: async (params?: {
    tipo?: string
    lido?: boolean
    limite?: number
  }): Promise<AlertaSistema[]> => {
    const response = await api.get<AlertaSistema[]>('/dashboard/alertas', { params })
    return response.data
  },

  marcarAlertaComoLido: async (alertaId: string): Promise<void> => {
    await api.post(`/dashboard/alertas/${alertaId}/lido`)
  },

  marcarTodosAlertasComoLidos: async (): Promise<void> => {
    await api.post('/dashboard/alertas/marcar-todos-lidos')
  },

  // Tarefas Pendentes
  getTarefasPendentes: async (params?: {
    tipo?: string
    prioridade?: string
    atribuidoA?: string
    limite?: number
  }): Promise<TarefaPendente[]> => {
    const response = await api.get<TarefaPendente[]>('/dashboard/tarefas', { params })
    return response.data
  },

  getMinhasTarefas: async (): Promise<TarefaPendente[]> => {
    const response = await api.get<TarefaPendente[]>('/dashboard/tarefas/minhas')
    return response.data
  },

  // Graficos
  getGraficoParticipacao: async (
    eleicaoId: string,
    tipo: 'diario' | 'horario' | 'regional'
  ): Promise<GraficoParticipacao> => {
    const response = await api.get<GraficoParticipacao>(`/dashboard/graficos/participacao/${eleicaoId}`, {
      params: { tipo },
    })
    return response.data
  },

  getGraficoChapas: async (eleicaoId: string): Promise<GraficoChapas[]> => {
    const response = await api.get<GraficoChapas[]>(`/dashboard/graficos/chapas/${eleicaoId}`)
    return response.data
  },

  getGraficoComparativo: async (eleicaoIds: string[]): Promise<{
    eleicoes: { id: string; nome: string; ano: number }[]
    participacao: number[]
    votosValidos: number[]
    chapas: number[]
  }> => {
    const response = await api.get('/dashboard/graficos/comparativo', {
      params: { eleicaoIds: eleicaoIds.join(',') },
    })
    return response.data
  },

  // Metricas Temporais
  getMetricasTempo: async (periodo: 'dia' | 'semana' | 'mes' | 'ano'): Promise<MetricasTempo[]> => {
    const response = await api.get<MetricasTempo[]>('/dashboard/metricas', {
      params: { periodo },
    })
    return response.data
  },

  // Contadores Rapidos
  getContadores: async (): Promise<{
    eleicoesEmAndamento: number
    chapasPendentesAnalise: number
    denunciasPendentes: number
    impugnacoesPendentes: number
    julgamentosHoje: number
    documentosPendentes: number
  }> => {
    const response = await api.get('/dashboard/contadores')
    return response.data
  },

  // Status do Sistema
  getStatusSistema: async (): Promise<{
    status: 'online' | 'degradado' | 'offline'
    servicos: {
      nome: string
      status: 'online' | 'offline'
      latencia?: number
    }[]
    ultimaVerificacao: string
  }> => {
    const response = await api.get('/dashboard/status-sistema')
    return response.data
  },

  // Widgets Personalizados
  getWidgetsUsuario: async (): Promise<{
    id: string
    tipo: string
    posicao: { x: number; y: number; w: number; h: number }
    configuracao?: Record<string, unknown>
  }[]> => {
    const response = await api.get('/dashboard/widgets')
    return response.data
  },

  salvarWidgetsUsuario: async (widgets: {
    id: string
    tipo: string
    posicao: { x: number; y: number; w: number; h: number }
    configuracao?: Record<string, unknown>
  }[]): Promise<void> => {
    await api.post('/dashboard/widgets', { widgets })
  },

  // Exportar Dashboard
  exportarDashboard: async (eleicaoId?: string, formato?: 'pdf' | 'png'): Promise<Blob> => {
    const response = await api.get('/dashboard/exportar', {
      params: { eleicaoId, formato },
      responseType: 'blob',
    })
    return response.data
  },
}
