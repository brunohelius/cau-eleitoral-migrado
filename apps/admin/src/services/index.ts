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
export { impugnacoesService, StatusImpugnacao, TipoImpugnacao, FaseImpugnacao } from './impugnacoes'
export type {
  Impugnacao,
  AnexoImpugnacao,
  DefesaImpugnacao,
  ParecerImpugnacao,
  RecursoImpugnacao,
  CreateImpugnacaoRequest,
  UpdateImpugnacaoRequest,
  ImpugnacaoListParams,
} from './impugnacoes'

// Julgamentos
export { julgamentosService } from './julgamentos'

// Calendario
export { calendarioService } from './calendario'
export type { CalendarioEvento } from './calendario'

// Usuarios
export { usuariosService } from './usuarios'

// Documentos
export { documentosService } from './documentos'

// Relatorios
export { relatoriosService } from './relatorios'

// Dashboard
export { dashboardService } from './dashboard'

// Auditoria
export { auditoriaService } from './auditoria'
export type { AuditoriaLog, AuditoriaLogDetalhe, EstatisticasAuditoria, PagedResult } from './auditoria'

// Configuracoes
export { configuracoesService } from './configuracoes'

// Votacao
export { votacaoService } from './votacao'
export type {
  EleicaoVotacao,
  EstatisticasVotacao,
  VotosPorRegiao,
  VotosPorHora,
  ResultadoChapa,
  ResultadoApuracao,
} from './votacao'
