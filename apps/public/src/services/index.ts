// API Client
export { default as api, setToken, setTokenType, extractApiError } from './api'
export type { ApiError } from './api'

// Auth
export * from './auth'
export { authService } from './auth'

// Eleicoes
export * from './eleicoes'
export { eleicoesPublicService, getStatusLabel, getTipoLabel } from './eleicoes'

// Votacao
export * from './votacao'
export { votacaoService } from './votacao'

// Candidato
export * from './candidato'
export {
  candidatoService,
  getTipoDocumentoLabel,
  getStatusCandidaturaLabel,
  getStatusCandidaturaColor,
} from './candidato'
