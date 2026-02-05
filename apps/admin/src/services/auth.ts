import api from './api'

export interface LoginRequest {
  email: string
  password: string
  rememberMe?: boolean
}

export interface LoginResponse {
  accessToken: string
  refreshToken: string
  expiresAt: string
  user: {
    id: string
    email: string
    nome: string
    nomeCompleto?: string
    roles: string[]
    permissions: string[]
  }
}

export interface RegisterRequest {
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  password: string
  confirmPassword: string
  registroCAU?: string
}

export const authService = {
  login: async (data: LoginRequest): Promise<LoginResponse> => {
    const response = await api.post<LoginResponse>('/auth/login', data)
    return response.data
  },

  logout: async (): Promise<void> => {
    await api.post('/auth/logout')
  },

  register: async (data: RegisterRequest): Promise<{ userId: string; message: string }> => {
    const response = await api.post('/auth/register', data)
    return response.data
  },

  forgotPassword: async (email: string): Promise<{ message: string }> => {
    const response = await api.post('/auth/forgot-password', { email })
    return response.data
  },

  resetPassword: async (
    token: string,
    newPassword: string,
    confirmPassword: string
  ): Promise<{ message: string }> => {
    const response = await api.post('/auth/reset-password', {
      token,
      newPassword,
      confirmPassword,
    })
    return response.data
  },

  getMe: async (): Promise<LoginResponse['user']> => {
    const response = await api.get('/auth/me')
    return response.data
  },

  refreshToken: async (refreshToken: string): Promise<LoginResponse> => {
    const response = await api.post<LoginResponse>('/auth/refresh-token', { refreshToken })
    return response.data
  },
}
