// ============================================
// API Client
// ============================================
export { default as api, setToken, setTokenType, extractApiError } from './api'
export type { ApiError } from './api'

// ============================================
// Shared Types (only unique types from types.ts)
// ============================================
export type {
  PaginatedResponse,
  ApiResponse,
  DocumentoPublico,
  Endereco,
  RedesSociais,
} from './types'
// Export enums for new services (calendar, impugnacao)
export {
  TipoCalendario,
  StatusCalendario,
  TipoImpugnacao,
  StatusImpugnacao,
  TipoAlegacao,
} from './types'

// ============================================
// Auth Service
// ============================================
export * from './auth'
export { authService } from './auth'

// ============================================
// Eleicoes Service (before chapas due to ChapaPublica conflict)
// ============================================
export {
  eleicoesPublicService,
  getStatusLabel,
  getTipoLabel,
  StatusEleicao,
  TipoEleicao,
} from './eleicoes'
export type {
  EleicaoPublica,
  ChapaPublica,
  CalendarioEleicao,
  ResultadoEleicao,
  EstatisticasPublicas,
} from './eleicoes'

// ============================================
// Votacao Service
// ============================================
export {
  votacaoService,
  TipoVoto,
} from './votacao'
export type {
  CedulaVotacao,
  ChapaVotacao,
  VotoRequest,
  ComprovanteVoto,
  StatusVotacao,
  JustificativaAusencia,
} from './votacao'

// ============================================
// Candidato Service
// ============================================
export {
  candidatoService,
  getTipoDocumentoLabel,
  getStatusCandidaturaLabel,
  getStatusCandidaturaColor,
  StatusCandidatura,
  TipoDocumentoCandidato,
} from './candidato'
export type {
  DadosCandidato,
  CandidaturaInfo,
  DocumentoCandidato,
  ChapaInfoCandidato,
  AtualizarPerfilRequest,
  SolicitarCandidaturaRequest,
} from './candidato'

// ============================================
// Chapas Service (ChapaPublica aliased to avoid conflict)
// ============================================
export {
  chapasService,
  getStatusConfig,
  getTipoMembroLabel,
  getStatusMembroLabel,
  getStatusColor,
  colorConfig,
  getColorByNumber,
  mockChapas,
  StatusChapa,
  TipoMembro,
  StatusMembro,
} from './chapas'
export type {
  MembroChapa,
  ChapaDetalhada,
  ChapaResumo,
  CandidatoPublico,
  ChapaDocumento,
  CargoMembro,
} from './chapas'
// Re-export ChapaPublica from chapas with different name
export type { ChapaPublica as ChapaPublicaDetalhada } from './chapas'

// ============================================
// Denuncias Service
// ============================================
export {
  denunciasPublicService,
  denunciasService,
  getTipoDenunciaLabel,
  getTipoDenunciaDescricao,
  getStatusDenunciaLabel,
  getStatusDenunciaColor,
  getStatusDefesaLabel,
  getTipoArquivoLabel,
  tiposDenunciaOptions,
  tiposArquivoOptions,
  TipoDenuncia,
  StatusDenuncia,
  StatusDefesa,
  TipoArquivoDenuncia,
} from './denuncias'
export type {
  EleicaoParaDenuncia,
  TipoDenunciaInfo,
  CaptchaData,
  CreateDenunciaPublicaDto,
  DenunciaPublicaResult,
  ConsultaProtocolo,
  ArquivoDenuncia,
  Denuncia,
  DenunciaResumida,
  CreateDenunciaDto,
  UpdateDenunciaDto,
  CreateArquivoDenunciaDto,
} from './denuncias'

// ============================================
// Calendario Service
// ============================================
export {
  calendarioService,
  getTipoCalendarioLabel,
  getTipoCalendarioColor,
  getStatusCalendarioLabel,
  getStatusCalendarioColor,
} from './calendarioService'
export type {
  CalendarioEvento,
  CalendarioEventoResumido,
  CalendarioFilters,
} from './calendarioService'

// ============================================
// Impugnacao Service
// ============================================
export {
  impugnacaoService,
  getTipoImpugnacaoLabel,
  getStatusImpugnacaoLabel,
  getStatusImpugnacaoColor,
  getTipoAlegacaoLabel,
  tiposImpugnacaoOptions,
  tiposAlegacaoOptions,
} from './impugnacaoService'
export type {
  Impugnacao,
  ImpugnacaoResumida,
  CreateImpugnacaoRequest,
  UpdateImpugnacaoRequest,
  AlegacoesRequest,
  RecursoRequest,
} from './impugnacaoService'
