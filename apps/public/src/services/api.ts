import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios'

// Storage keys
const VOTER_TOKEN_KEY = 'voter-token'
const CANDIDATE_TOKEN_KEY = 'candidate-token'

// Token type to identify the authentication context
type TokenType = 'voter' | 'candidate'

let currentTokenType: TokenType = 'voter'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
  },
})

// Helper to get the correct token based on context
const getToken = (): string | null => {
  if (currentTokenType === 'voter') {
    return localStorage.getItem(VOTER_TOKEN_KEY)
  }
  return localStorage.getItem(CANDIDATE_TOKEN_KEY)
}

// Helper to set the token
export const setToken = (token: string | null, type: TokenType = 'voter') => {
  const key = type === 'voter' ? VOTER_TOKEN_KEY : CANDIDATE_TOKEN_KEY
  if (token) {
    localStorage.setItem(key, token)
  } else {
    localStorage.removeItem(key)
  }
}

// Helper to set the current token type for requests
export const setTokenType = (type: TokenType) => {
  currentTokenType = type
}

// Request interceptor to add auth token
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    const token = getToken()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    // Handle 401 - Unauthorized
    if (error.response?.status === 401) {
      // Clear tokens on unauthorized
      localStorage.removeItem(VOTER_TOKEN_KEY)
      localStorage.removeItem(CANDIDATE_TOKEN_KEY)

      // Optionally redirect to login
      // window.location.href = '/login'
    }

    // Handle network errors
    if (!error.response) {
      console.error('Network error:', error.message)
    }

    return Promise.reject(error)
  }
)

// Typed error handling helper
export interface ApiError {
  message: string
  code?: string
  field?: string
  details?: Record<string, unknown>
}

export const extractApiError = (error: unknown): ApiError => {
  if (axios.isAxiosError(error)) {
    const data = error.response?.data
    if (data && typeof data === 'object') {
      return {
        message: data.message || data.error || 'Ocorreu um erro inesperado',
        code: data.code,
        field: data.field,
        details: data.details,
      }
    }
    return {
      message: error.message || 'Erro de conexao com o servidor',
    }
  }
  if (error instanceof Error) {
    return { message: error.message }
  }
  return { message: 'Ocorreu um erro inesperado' }
}

export default api
