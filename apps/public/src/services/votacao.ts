import api, { setTokenType } from './api'

// Enums
export enum TipoVoto {
  CHAPA = 0,
  BRANCO = 1,
  NULO = 2,
}

// Interfaces
export interface CedulaVotacao {
  eleicaoId: string
  eleicaoNome: string
  eleitorId: string
  eleitorNome: string
  chapas: {
    id: string
    numero: number
    nome: string
    sigla?: string
    logoUrl?: string
  }[]
  instrucoes: string[]
  tempoMaximoMinutos: number
  iniciadaEm: string
  expiraEm: string
}

export interface VotoRequest {
  eleicaoId: string
  chapaId?: string // null for branco/nulo
  tipoVoto: TipoVoto
  hash?: string // for validation
}

export interface ComprovanteVoto {
  id: string
  protocolo: string
  eleicaoId: string
  eleicaoNome: string
  dataHoraVoto: string
  hashComprovante: string
  qrCode?: string
  mensagem: string
}

export interface StatusVotacao {
  eleicaoId: string
  votacaoAberta: boolean
  inicioVotacao: string
  fimVotacao: string
  horarioServidor: string
  tempoRestante?: number // in seconds
  mensagem?: string
}

export interface JustificativaAusencia {
  eleicaoId: string
  motivo: string
  documentoComprovante?: File
}

export const votacaoService = {
  // Check voting status
  getStatus: async (eleicaoId: string): Promise<StatusVotacao> => {
    const response = await api.get<StatusVotacao>(`/votacao/${eleicaoId}/status`)
    return response.data
  },

  // Check if voter already voted
  verificarVoto: async (eleicaoId: string): Promise<{
    jaVotou: boolean
    dataVoto?: string
    comprovanteDisponivel: boolean
  }> => {
    setTokenType('voter')
    const response = await api.get(`/votacao/${eleicaoId}/verificar`)
    return response.data
  },

  // Start voting session (get ballot)
  iniciarVotacao: async (eleicaoId: string): Promise<CedulaVotacao> => {
    setTokenType('voter')
    const response = await api.post<CedulaVotacao>(`/votacao/${eleicaoId}/iniciar`)
    return response.data
  },

  // Submit vote
  votar: async (data: VotoRequest): Promise<ComprovanteVoto> => {
    setTokenType('voter')
    const response = await api.post<ComprovanteVoto>('/votacao/votar', data)
    return response.data
  },

  // Vote for a chapa (convenience method)
  votarChapa: async (eleicaoId: string, chapaId: string): Promise<ComprovanteVoto> => {
    return votacaoService.votar({
      eleicaoId,
      chapaId,
      tipoVoto: TipoVoto.CHAPA,
    })
  },

  // Vote branco
  votarBranco: async (eleicaoId: string): Promise<ComprovanteVoto> => {
    return votacaoService.votar({
      eleicaoId,
      tipoVoto: TipoVoto.BRANCO,
    })
  },

  // Vote nulo
  votarNulo: async (eleicaoId: string): Promise<ComprovanteVoto> => {
    return votacaoService.votar({
      eleicaoId,
      tipoVoto: TipoVoto.NULO,
    })
  },

  // Get voting receipt
  getComprovante: async (eleicaoId: string): Promise<ComprovanteVoto> => {
    setTokenType('voter')
    const response = await api.get<ComprovanteVoto>(`/votacao/${eleicaoId}/comprovante`)
    return response.data
  },

  // Download voting receipt as PDF
  downloadComprovante: async (eleicaoId: string): Promise<Blob> => {
    setTokenType('voter')
    const response = await api.get(`/votacao/${eleicaoId}/comprovante/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  // Send receipt by email
  enviarComprovantePorEmail: async (eleicaoId: string, email?: string): Promise<{
    enviado: boolean
    emailDestino: string
  }> => {
    setTokenType('voter')
    const response = await api.post(`/votacao/${eleicaoId}/comprovante/email`, { email })
    return response.data
  },

  // Validate voting receipt
  validarComprovante: async (protocolo: string, hash: string): Promise<{
    valido: boolean
    dataVoto?: string
    eleicaoNome?: string
    mensagem: string
  }> => {
    const response = await api.post('/votacao/validar-comprovante', { protocolo, hash })
    return response.data
  },

  // Cancel current voting session (before submitting vote)
  cancelarSessao: async (eleicaoId: string): Promise<void> => {
    setTokenType('voter')
    await api.post(`/votacao/${eleicaoId}/cancelar-sessao`)
  },

  // Extend voting session time (if allowed)
  estenderSessao: async (eleicaoId: string): Promise<{
    novaExpiracao: string
    tempoAdicional: number
  }> => {
    setTokenType('voter')
    const response = await api.post(`/votacao/${eleicaoId}/estender-sessao`)
    return response.data
  },

  // Submit absence justification
  justificarAusencia: async (data: JustificativaAusencia): Promise<{
    protocolo: string
    mensagem: string
  }> => {
    setTokenType('voter')
    const formData = new FormData()
    formData.append('eleicaoId', data.eleicaoId)
    formData.append('motivo', data.motivo)
    if (data.documentoComprovante) {
      formData.append('documento', data.documentoComprovante)
    }

    const response = await api.post('/votacao/justificar-ausencia', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  // Get voting history (all elections voter participated)
  getHistoricoVotos: async (): Promise<{
    eleicaoId: string
    eleicaoNome: string
    dataVoto: string
    protocolo: string
  }[]> => {
    setTokenType('voter')
    const response = await api.get('/votacao/historico')
    return response.data
  },

  // Real-time voting statistics (for public display during voting)
  getEstatisticasTempoReal: async (eleicaoId: string): Promise<{
    totalEleitores: number
    votosComputados: number
    participacao: number
    ultimaAtualizacao: string
    progressoPorHora?: { hora: string; votos: number }[]
  }> => {
    const response = await api.get(`/votacao/${eleicaoId}/estatisticas`)
    return response.data
  },
}
