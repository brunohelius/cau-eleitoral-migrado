import api from './api'

export interface EstatisticasVotacao {
  eleicaoId: string
  eleicaoNome: string
  totalEleitores: number
  totalVotos: number
  participacao: number
  votosBrancos: number
  votosNulos: number
  votosValidos: number
  status: string
  dataInicio?: string
  dataFim?: string
  votosPorRegiao: VotosPorRegiao[]
  votosPorHora: VotosPorHora[]
}

export interface VotosPorRegiao {
  uf: string
  regional: string
  totalEleitores: number
  totalVotos: number
  participacao: number
}

export interface VotosPorHora {
  hora: string
  quantidade: number
}

export interface ResultadoChapa {
  id: string
  numero: number
  nome: string
  votos: number
  percentual: number
  posicao: number
  eleita: boolean
}

export interface ResultadoApuracao {
  eleicaoId: string
  eleicaoNome: string
  status: 'aguardando' | 'em_andamento' | 'finalizada' | 'publicada'
  totalEleitores: number
  totalVotos: number
  votosValidos: number
  votosBrancos: number
  votosNulos: number
  participacao: number
  percentualApurado: number
  chapas: ResultadoChapa[]
  vencedora?: ResultadoChapa
  dataApuracao?: string
  dataPublicacao?: string
  ultimaAtualizacao: string
}

export interface EleicaoVotacao {
  id: string
  nome: string
  ano: number
  status: number
  statusVotacao: 'preparada' | 'em_andamento' | 'encerrada' | 'apurada'
  totalEleitores: number
  totalVotos: number
  participacao: number
  votosBrancos: number
  votosNulos: number
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  dataApuracao?: string
}

export const votacaoService = {
  // Get voting status for all elections
  getAll: async (): Promise<EleicaoVotacao[]> => {
    const response = await api.get<EleicaoVotacao[]>('/votacao')
    return response.data
  },

  // Get voting info for a specific election
  getEleicaoVotacao: async (eleicaoId: string): Promise<EleicaoVotacao> => {
    const response = await api.get<EleicaoVotacao>(`/votacao/eleicao/${eleicaoId}`)
    return response.data
  },

  // Get voting statistics for a specific election
  getEstatisticas: async (eleicaoId: string): Promise<EstatisticasVotacao> => {
    const response = await api.get<EstatisticasVotacao>(`/votacao/eleicao/${eleicaoId}/estatisticas`)
    return response.data
  },

  // Start voting for an election
  iniciarVotacao: async (eleicaoId: string): Promise<void> => {
    await api.post(`/eleicao/${eleicaoId}/iniciar-votacao`)
  },

  // End voting for an election
  encerrarVotacao: async (eleicaoId: string): Promise<void> => {
    await api.post(`/eleicao/${eleicaoId}/encerrar-votacao`)
  },

  // Get election results (apuracao)
  getResultados: async (eleicaoId: string): Promise<ResultadoApuracao> => {
    const response = await api.get<ResultadoApuracao>(`/apuracao/${eleicaoId}`)
    return response.data
  },

  // Perform vote counting (apurar)
  apurar: async (eleicaoId: string): Promise<ResultadoApuracao> => {
    const response = await api.post<ResultadoApuracao>(`/apuracao/${eleicaoId}/apurar`)
    return response.data
  },

  // Publish results
  publicarResultados: async (eleicaoId: string): Promise<void> => {
    await api.post(`/apuracao/${eleicaoId}/publicar`)
  },

  // Export results
  exportarResultados: async (eleicaoId: string, formato: 'pdf' | 'excel' | 'csv'): Promise<Blob> => {
    const response = await api.get(`/apuracao/${eleicaoId}/exportar`, {
      params: { formato },
      responseType: 'blob',
    })
    return response.data
  },
}
