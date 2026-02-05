import api, { setToken, setTokenType } from './api'

// Voter Authentication
export interface VoterLoginRequest {
  cpf: string
  registroCAU: string
  codigoVerificacao?: string
}

export interface VoterLoginResponse {
  token: string
  expiresAt: string
  voter: {
    id: string
    nome: string
    cpf: string
    registroCAU: string
    email?: string
    regional?: string
    podeVotar: boolean
    jaVotou: boolean
    eleicaoId: string
    eleicaoNome: string
  }
}

export interface VoterVerificationRequest {
  cpf: string
  registroCAU: string
}

export interface VoterVerificationResponse {
  verificacaoEnviada: boolean
  canal: 'email' | 'sms'
  destino: string // masked email or phone
}

// Candidate Authentication
export interface CandidateLoginRequest {
  cpf: string
  registroCAU: string
  senha: string
}

export interface CandidateLoginResponse {
  token: string
  expiresAt: string
  candidate: {
    id: string
    nome: string
    cpf: string
    registroCAU: string
    email: string
    telefone?: string
    chapaId: string
    chapaNome: string
    chapaNumero: number
    cargo: string
    tipo: string
    eleicaoId: string
    eleicaoNome: string
    status: number
  }
}

export interface CandidateRegisterRequest {
  nome: string
  cpf: string
  registroCAU: string
  email: string
  telefone?: string
  senha: string
  confirmacaoSenha: string
  aceitouTermos: boolean
}

export interface ForgotPasswordRequest {
  cpf: string
  registroCAU: string
}

export interface ResetPasswordRequest {
  token: string
  novaSenha: string
  confirmacaoSenha: string
}

export const authService = {
  // Voter Authentication
  solicitarCodigoVerificacao: async (data: VoterVerificationRequest): Promise<VoterVerificationResponse> => {
    const response = await api.post<VoterVerificationResponse>('/auth/eleitor/verificacao', data)
    return response.data
  },

  loginEleitor: async (data: VoterLoginRequest): Promise<VoterLoginResponse> => {
    setTokenType('voter')
    const response = await api.post<VoterLoginResponse>('/auth/eleitor/login', data)
    setToken(response.data.token, 'voter')
    return response.data
  },

  logoutEleitor: async (): Promise<void> => {
    try {
      await api.post('/auth/eleitor/logout')
    } finally {
      setToken(null, 'voter')
    }
  },

  getEleitorInfo: async (): Promise<VoterLoginResponse['voter']> => {
    setTokenType('voter')
    const response = await api.get('/auth/eleitor/me')
    return response.data
  },

  verificarElegibilidade: async (eleicaoId: string): Promise<{
    elegivelVotar: boolean
    motivo?: string
    restricoes?: string[]
  }> => {
    setTokenType('voter')
    const response = await api.get(`/auth/eleitor/elegibilidade/${eleicaoId}`)
    return response.data
  },

  // Candidate Authentication
  loginCandidato: async (data: CandidateLoginRequest): Promise<CandidateLoginResponse> => {
    setTokenType('candidate')
    const response = await api.post<CandidateLoginResponse>('/auth/candidato/login', data)
    setToken(response.data.token, 'candidate')
    return response.data
  },

  logoutCandidato: async (): Promise<void> => {
    try {
      await api.post('/auth/candidato/logout')
    } finally {
      setToken(null, 'candidate')
    }
  },

  getCandidatoInfo: async (): Promise<CandidateLoginResponse['candidate']> => {
    setTokenType('candidate')
    const response = await api.get('/auth/candidato/me')
    return response.data
  },

  registerCandidato: async (data: CandidateRegisterRequest): Promise<{
    id: string
    message: string
    proximosPasso: string[]
  }> => {
    const response = await api.post('/auth/candidato/register', data)
    return response.data
  },

  forgotPasswordCandidato: async (data: ForgotPasswordRequest): Promise<{
    message: string
    emailEnviado: boolean
  }> => {
    const response = await api.post('/auth/candidato/forgot-password', data)
    return response.data
  },

  resetPasswordCandidato: async (data: ResetPasswordRequest): Promise<{
    message: string
  }> => {
    const response = await api.post('/auth/candidato/reset-password', data)
    return response.data
  },

  alterarSenhaCandidato: async (data: {
    senhaAtual: string
    novaSenha: string
    confirmacaoSenha: string
  }): Promise<void> => {
    setTokenType('candidate')
    await api.post('/auth/candidato/change-password', data)
  },

  // Common
  verificarToken: async (token: string, tipo: 'voter' | 'candidate'): Promise<{
    valido: boolean
    expiraEm?: string
  }> => {
    const response = await api.post('/auth/verify-token', { token, tipo })
    return response.data
  },
}
