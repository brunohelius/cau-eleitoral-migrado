// API Client
export { default as api } from './api'

// Auth
export { authService } from './auth'

// Eleicoes
export { eleicoesService } from './eleicoes'
export type { Eleicao, CreateEleicaoRequest, UpdateEleicaoRequest } from './eleicoes'

// Chapas
export { chapasService } from './chapas'
export type { Chapa, CreateChapaRequest, UpdateChapaRequest, PaginatedResponse } from './chapas'

// Denuncias
export { denunciasService } from './denuncias'

// Impugnacoes
export { impugnacoesService } from './impugnacoes'

// Julgamentos
export { julgamentosService } from './julgamentos'

// Usuarios
export { usuariosService } from './usuarios'

// Documentos
export { documentosService } from './documentos'

// Relatorios
export { relatoriosService } from './relatorios'

// Dashboard
export { dashboardService } from './dashboard'

// Configuracoes
export { configuracoesService } from './configuracoes'
