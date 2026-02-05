// ============================================
// CAU Sistema Eleitoral - Admin Types
// ============================================

// ============================================
// Common Types
// ============================================

export interface PaginatedResponse<T> {
  data: T[]
  total: number
  page: number
  pageSize: number
  totalPages: number
}

export interface ApiError {
  message: string
  code?: string
  details?: Record<string, string[]>
}

// ============================================
// Chapa Types
// ============================================

export enum StatusChapa {
  PENDENTE = 0,
  EM_ANALISE = 1,
  APROVADA = 2,
  REPROVADA = 3,
  IMPUGNADA = 4,
  SUSPENSA = 5,
  CANCELADA = 6,
}

export enum TipoMembro {
  TITULAR = 0,
  SUPLENTE = 1,
}

export enum CargoMembro {
  PRESIDENTE = 0,
  VICE_PRESIDENTE = 1,
  CONSELHEIRO = 2,
  DIRETOR = 3,
  COORDENADOR = 4,
}

export interface MembroChapa {
  id: string
  chapaId: string
  candidatoId: string
  candidatoNome: string
  candidatoCpf?: string
  candidatoRegistroCAU?: string
  cargo: CargoMembro
  tipo: TipoMembro
  ordem: number
  status: number
  createdAt: string
  updatedAt?: string
}

export interface DocumentoChapa {
  id: string
  chapaId: string
  nome: string
  tipo: number
  arquivoUrl: string
  tamanho?: number
  status: number
  observacoes?: string
  createdAt: string
}

export interface Chapa {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  descricao?: string
  logoUrl?: string
  status: StatusChapa
  dataCadastro: string
  dataAprovacao?: string
  motivoReprovacao?: string
  representanteLegalId?: string
  representanteLegalNome?: string
  membros?: MembroChapa[]
  documentos?: DocumentoChapa[]
  totalVotos?: number
  createdAt: string
  updatedAt?: string
}

export interface CreateChapaRequest {
  eleicaoId: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  descricao?: string
  representanteLegalId?: string
}

export interface UpdateChapaRequest {
  nome?: string
  sigla?: string
  lema?: string
  descricao?: string
  logoUrl?: string
  representanteLegalId?: string
}

export interface ChapaListParams {
  eleicaoId?: string
  status?: StatusChapa
  search?: string
  page?: number
  pageSize?: number
}

export interface ChapaEstatisticas {
  total: number
  pendentes: number
  aprovadas: number
  reprovadas: number
  impugnadas: number
}

// ============================================
// Denuncia Types
// ============================================

export enum StatusDenuncia {
  PENDENTE = 0,
  EM_ANALISE = 1,
  PROCEDENTE = 2,
  IMPROCEDENTE = 3,
  ARQUIVADA = 4,
}

export enum TipoDenuncia {
  IRREGULARIDADE = 0,
  FRAUDE = 1,
  PROPAGANDA_IRREGULAR = 2,
  ABUSO_PODER = 3,
  COACAO = 4,
  FALSIDADE = 5,
  OUTRO = 99,
}

export enum PrioridadeDenuncia {
  BAIXA = 0,
  NORMAL = 1,
  ALTA = 2,
  URGENTE = 3,
}

export interface AnexoDenuncia {
  id: string
  denunciaId: string
  nome: string
  tipo: string
  arquivoUrl: string
  tamanho: number
  createdAt: string
}

export interface ParecerDenuncia {
  id: string
  denunciaId: string
  analistaId: string
  analistaNome: string
  parecer: string
  decisao: StatusDenuncia
  dataEmissao: string
  createdAt: string
}

export interface AnaliseDenuncia {
  id: string
  denunciaId: string
  analistaId: string
  analistaNome: string
  dataInicio: string
  dataFim?: string
  status: StatusDenuncia
  parecer?: string
  recomendacao?: string
  acaoTomada?: string
  observacoes?: string
  createdAt: string
  updatedAt?: string
}

export interface Denuncia {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  denuncianteId?: string
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  provas?: string
  status: StatusDenuncia
  prioridade: PrioridadeDenuncia
  anonima: boolean
  protocolo: string
  analistaResponsavelId?: string
  analistaResponsavelNome?: string
  dataOcorrencia?: string
  localOcorrencia?: string
  dataAnalise?: string
  parecer?: string
  acaoTomada?: string
  anexos?: AnexoDenuncia[]
  pareceres?: ParecerDenuncia[]
  analises?: AnaliseDenuncia[]
  createdAt: string
  updatedAt?: string
}

export interface CreateDenunciaRequest {
  eleicaoId: string
  chapaId?: string
  tipo: TipoDenuncia
  titulo: string
  descricao: string
  provas?: string
  prioridade?: PrioridadeDenuncia
  anonima?: boolean
  denuncianteNome?: string
  denuncianteEmail?: string
  denuncianteTelefone?: string
  dataOcorrencia?: string
  localOcorrencia?: string
}

export interface UpdateDenunciaRequest {
  tipo?: TipoDenuncia
  titulo?: string
  descricao?: string
  provas?: string
  prioridade?: PrioridadeDenuncia
  analistaResponsavelId?: string
}

export interface DenunciaListParams {
  eleicaoId?: string
  chapaId?: string
  status?: StatusDenuncia
  tipo?: TipoDenuncia
  prioridade?: PrioridadeDenuncia
  anonima?: boolean
  search?: string
  dataInicio?: string
  dataFim?: string
  page?: number
  pageSize?: number
}

export interface DenunciaEstatisticas {
  total: number
  pendentes: number
  emAnalise: number
  procedentes: number
  improcedentes: number
  arquivadas: number
  porTipo: Record<string, number>
  porPrioridade: Record<string, number>
}

// ============================================
// Impugnacao Types
// ============================================

export enum StatusImpugnacao {
  PENDENTE = 0,
  EM_ANALISE = 1,
  DEFERIDA = 2,
  INDEFERIDA = 3,
  PARCIALMENTE_DEFERIDA = 4,
  RECURSO = 5,
  ARQUIVADA = 6,
}

export enum TipoImpugnacao {
  CANDIDATURA = 0,
  CHAPA = 1,
  ELEICAO = 2,
  RESULTADO = 3,
  VOTACAO = 4,
}

export enum FaseImpugnacao {
  REGISTRO = 0,
  ANALISE_INICIAL = 1,
  DEFESA = 2,
  PARECER = 3,
  JULGAMENTO = 4,
  RECURSO = 5,
  ENCERRADA = 6,
}

export interface AnexoImpugnacao {
  id: string
  impugnacaoId: string
  nome: string
  tipo: string
  arquivoUrl: string
  tamanho: number
  createdAt: string
}

export interface DefesaImpugnacao {
  id: string
  impugnacaoId: string
  defensorId: string
  defensorNome: string
  texto: string
  dataApresentacao: string
  anexos?: AnexoImpugnacao[]
  createdAt: string
}

export interface ParecerImpugnacao {
  id: string
  impugnacaoId: string
  pareceristaId: string
  pareceristaNome: string
  parecer: string
  recomendacao: StatusImpugnacao
  dataEmissao: string
  createdAt: string
}

export interface RecursoImpugnacao {
  id: string
  impugnacaoId: string
  recorrenteId: string
  recorrenteNome: string
  fundamentacao: string
  dataInterposicao: string
  status: StatusImpugnacao
  decisaoRecurso?: string
  dataDecisao?: string
  anexos?: AnexoImpugnacao[]
  createdAt: string
}

export interface Impugnacao {
  id: string
  eleicaoId: string
  eleicaoNome?: string
  chapaId?: string
  chapaNome?: string
  candidatoId?: string
  candidatoNome?: string
  impugnanteId: string
  impugnanteNome: string
  tipo: TipoImpugnacao
  fase: FaseImpugnacao
  status: StatusImpugnacao
  protocolo: string
  fundamentacao: string
  normasVioladas?: string
  pedido: string
  prazoDefesa?: string
  decisao?: string
  fundamentacaoDecisao?: string
  dataDecisao?: string
  relatorId?: string
  relatorNome?: string
  anexos?: AnexoImpugnacao[]
  defesas?: DefesaImpugnacao[]
  pareceres?: ParecerImpugnacao[]
  recursos?: RecursoImpugnacao[]
  createdAt: string
  updatedAt?: string
}

export interface CreateImpugnacaoRequest {
  eleicaoId: string
  chapaId?: string
  candidatoId?: string
  tipo: TipoImpugnacao
  fundamentacao: string
  normasVioladas?: string
  pedido: string
}

export interface UpdateImpugnacaoRequest {
  fundamentacao?: string
  normasVioladas?: string
  pedido?: string
  relatorId?: string
}

export interface ImpugnacaoListParams {
  eleicaoId?: string
  chapaId?: string
  candidatoId?: string
  tipo?: TipoImpugnacao
  status?: StatusImpugnacao
  fase?: FaseImpugnacao
  search?: string
  dataInicio?: string
  dataFim?: string
  page?: number
  pageSize?: number
}

export interface ImpugnacaoEstatisticas {
  total: number
  pendentes: number
  emAnalise: number
  deferidas: number
  indeferidas: number
  parcialmenteDeferidas: number
  emRecurso: number
  arquivadas: number
  porTipo: Record<string, number>
  porFase: Record<string, number>
}

// ============================================
// Usuario Types
// ============================================

export enum StatusUsuario {
  ATIVO = 0,
  INATIVO = 1,
  PENDENTE = 2,
  BLOQUEADO = 3,
  SUSPENSO = 4,
}

export enum TipoUsuario {
  ADMINISTRADOR = 0,
  COMISSAO = 1,
  FISCAL = 2,
  ANALISTA = 3,
  AUDITOR = 4,
  OPERADOR = 5,
}

export interface Permissao {
  id: string
  nome: string
  codigo: string
  descricao?: string
  modulo: string
  ativo?: boolean
  createdAt?: string
}

export interface Role {
  id: string
  nome: string
  codigo: string
  descricao?: string
  permissoes?: Permissao[]
  ativo?: boolean
  createdAt?: string
  updatedAt?: string
}

export interface Usuario {
  id: string
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo: TipoUsuario
  status: StatusUsuario
  avatarUrl?: string
  regionalId?: string
  regionalNome?: string
  roles: Role[]
  permissoes?: string[]
  ultimoAcesso?: string
  emailVerificado: boolean
  doisFatoresAtivo: boolean
  createdAt: string
  updatedAt?: string
}

export interface CreateUsuarioRequest {
  email: string
  nome: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo: TipoUsuario
  regionalId?: string
  roles: string[]
  password?: string
  enviarEmailBoasVindas?: boolean
}

export interface UpdateUsuarioRequest {
  nome?: string
  nomeCompleto?: string
  cpf?: string
  telefone?: string
  registroCAU?: string
  tipo?: TipoUsuario
  regionalId?: string
  avatarUrl?: string
}

export interface UsuarioListParams {
  tipo?: TipoUsuario
  status?: StatusUsuario
  regionalId?: string
  role?: string
  search?: string
  page?: number
  pageSize?: number
}

export interface UsuarioEstatisticas {
  total: number
  ativos: number
  inativos: number
  pendentes: number
  bloqueados: number
  porTipo: Record<string, number>
  porRegional: Record<string, number>
}

export interface LogAtividade {
  id: string
  usuarioId: string
  acao: string
  descricao: string
  ip?: string
  userAgent?: string
  metadata?: Record<string, unknown>
  createdAt: string
}

// ============================================
// Configuracao Types
// ============================================

export enum TipoConfiguracao {
  SISTEMA = 0,
  ELEICAO = 1,
  SEGURANCA = 2,
  NOTIFICACAO = 3,
  INTEGRACAO = 4,
  APARENCIA = 5,
}

export interface ConfiguracaoSistema {
  id: string
  chave: string
  valor: string
  tipo: TipoConfiguracao
  descricao?: string
  publico: boolean
  editavel: boolean
  validacao?: {
    tipo: 'string' | 'number' | 'boolean' | 'json' | 'email' | 'url'
    min?: number
    max?: number
    regex?: string
    opcoes?: string[]
  }
  grupo?: string
  ordem?: number
  updatedAt?: string
  updatedBy?: string
}

export interface ConfiguracoesEleicao {
  horasAntesInicioVotacao: number
  horasAposEncerramento: number
  permitirVotoAntecipado: boolean
  permitirVotoPorProcuracao: boolean
  exibirResultadosParciais: boolean
  exibirResultadosAposEncerramento: boolean
  requererJustificativaAusencia: boolean
  tempoMaximoVotacao: number
  tentativasMaximasLogin: number
  bloquearAposXTentativas: number
  tempoBloqueioMinutos: number
  validarCPFReceita: boolean
  validarRegistroCAU: boolean
  permitirCandidaturaMultipla: boolean
  diasMinimosInscricao: number
  diasMaximosRecurso: number
}

export interface ConfiguracoesNotificacao {
  emailHabilitado: boolean
  smsHabilitado: boolean
  pushHabilitado: boolean
  notificarNovaEleicao: boolean
  notificarInicioVotacao: boolean
  notificarEncerramentoVotacao: boolean
  notificarResultado: boolean
  notificarDenuncia: boolean
  notificarImpugnacao: boolean
  notificarJulgamento: boolean
  remetenteEmail: string
  templateEmailBoasVindas?: string
  templateEmailRecuperacaoSenha?: string
  templateEmailNotificacao?: string
}

export interface ConfiguracoesSeguranca {
  sessaoTimeoutMinutos: number
  tokenExpiracaoHoras: number
  refreshTokenExpiracaoDias: number
  requerer2FA: boolean
  complexidadeSenhaMinima: 'baixa' | 'media' | 'alta'
  diasExpiracaoSenha: number
  historicoSenhasImpedir: number
  ipWhitelist?: string[]
  ipBlacklist?: string[]
  rateLimitRequests: number
  rateLimitWindowMinutos: number
  auditarTodasAcoes: boolean
  criptografarVotos: boolean
  algoritmoHash: 'bcrypt' | 'argon2' | 'scrypt'
}

export interface ConfiguracoesIntegracao {
  apiExternaHabilitada: boolean
  webhooksHabilitados: boolean
  urlWebhook?: string
  secretWebhook?: string
  integracaoSIAU: boolean
  urlSIAU?: string
  tokenSIAU?: string
  integracaoReceitaFederal: boolean
  urlReceita?: string
  certificadoReceita?: string
  integracaoEmail: 'smtp' | 'sendgrid' | 'ses' | 'mailgun'
  configEmail?: {
    host?: string
    port?: number
    user?: string
    password?: string
    apiKey?: string
  }
  integracaoStorage: 'local' | 's3' | 'azure' | 'gcs'
  configStorage?: {
    bucket?: string
    region?: string
    accessKey?: string
    secretKey?: string
  }
}

export interface ConfiguracoesAparencia {
  logoUrl?: string
  faviconUrl?: string
  corPrimaria: string
  corSecundaria: string
  corAcento: string
  tema: 'claro' | 'escuro' | 'sistema'
  fontePrincipal: string
  fonteSecundaria: string
  borderRadius: number
  mostrarLogoPaginaLogin: boolean
  textoRodape?: string
  linksPoliticas?: {
    termos?: string
    privacidade?: string
    cookies?: string
  }
}

export interface Configuracao {
  sistema: ConfiguracaoSistema[]
  eleicao: ConfiguracoesEleicao
  notificacao: ConfiguracoesNotificacao
  seguranca: ConfiguracoesSeguranca
  integracao: ConfiguracoesIntegracao
  aparencia: ConfiguracoesAparencia
}

export interface LogConfiguracao {
  id: string
  configuracaoId: string
  chave: string
  valorAnterior: string
  valorNovo: string
  alteradoPorId: string
  alteradoPorNome: string
  ip?: string
  createdAt: string
}

export interface BackupConfiguracao {
  id: string
  nome: string
  descricao?: string
  dados: Record<string, unknown>
  criadoPorId: string
  criadoPorNome: string
  createdAt: string
}

// ============================================
// Eleicao Types (re-export for convenience)
// ============================================

export enum StatusEleicao {
  RASCUNHO = 0,
  AGENDADA = 1,
  INSCRICOES_ABERTAS = 2,
  INSCRICOES_ENCERRADAS = 3,
  VOTACAO_ABERTA = 4,
  VOTACAO_ENCERRADA = 5,
  APURACAO = 6,
  FINALIZADA = 7,
  SUSPENSA = 8,
  CANCELADA = 9,
}

export enum TipoEleicao {
  CONSELHEIRO_FEDERAL = 0,
  CONSELHEIRO_ESTADUAL = 1,
  DIRETORIA = 2,
  COMISSAO = 3,
}

export enum ModoVotacao {
  ONLINE = 0,
  PRESENCIAL = 1,
  HIBRIDO = 2,
}

export interface Eleicao {
  id: string
  nome: string
  descricao?: string
  tipo: TipoEleicao
  status: StatusEleicao
  faseAtual: number
  ano: number
  mandato?: number
  dataInicio: string
  dataFim: string
  dataVotacaoInicio?: string
  dataVotacaoFim?: string
  dataApuracao?: string
  regionalId?: string
  regionalNome?: string
  modoVotacao: ModoVotacao
  quantidadeVagas?: number
  quantidadeSuplentes?: number
  totalChapas: number
  totalEleitores: number
  createdAt: string
  updatedAt?: string
}
