import api from './api'

// Enums
export enum TipoConfiguracao {
  SISTEMA = 0,
  ELEICAO = 1,
  SEGURANCA = 2,
  NOTIFICACAO = 3,
  INTEGRACAO = 4,
  APARENCIA = 5,
}

// Interfaces
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
  tempoMaximoVotacao: number // minutos
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

export const configuracoesService = {
  // Configuracoes Gerais
  getAll: async (tipo?: TipoConfiguracao): Promise<ConfiguracaoSistema[]> => {
    const response = await api.get<ConfiguracaoSistema[]>('/configuracao', {
      params: tipo !== undefined ? { tipo } : undefined,
    })
    return response.data
  },

  getByChave: async (chave: string): Promise<ConfiguracaoSistema> => {
    const response = await api.get<ConfiguracaoSistema>(`/configuracao/${chave}`)
    return response.data
  },

  update: async (chave: string, valor: string): Promise<ConfiguracaoSistema> => {
    const response = await api.put<ConfiguracaoSistema>(`/configuracao/${chave}`, { valor })
    return response.data
  },

  updateMultiplas: async (configuracoes: { chave: string; valor: string }[]): Promise<ConfiguracaoSistema[]> => {
    const response = await api.put<ConfiguracaoSistema[]>('/configuracao/batch', { configuracoes })
    return response.data
  },

  // Configuracoes de Eleicao
  getConfiguracoesEleicao: async (): Promise<ConfiguracoesEleicao> => {
    const response = await api.get<ConfiguracoesEleicao>('/configuracao/eleicao')
    return response.data
  },

  updateConfiguracoesEleicao: async (data: Partial<ConfiguracoesEleicao>): Promise<ConfiguracoesEleicao> => {
    const response = await api.put<ConfiguracoesEleicao>('/configuracao/eleicao', data)
    return response.data
  },

  // Configuracoes de Notificacao
  getConfiguracoesNotificacao: async (): Promise<ConfiguracoesNotificacao> => {
    const response = await api.get<ConfiguracoesNotificacao>('/configuracao/notificacao')
    return response.data
  },

  updateConfiguracoesNotificacao: async (data: Partial<ConfiguracoesNotificacao>): Promise<ConfiguracoesNotificacao> => {
    const response = await api.put<ConfiguracoesNotificacao>('/configuracao/notificacao', data)
    return response.data
  },

  testarEmail: async (destinatario: string): Promise<{ sucesso: boolean; erro?: string }> => {
    const response = await api.post('/configuracao/notificacao/testar-email', { destinatario })
    return response.data
  },

  // Configuracoes de Seguranca
  getConfiguracoesSeguranca: async (): Promise<ConfiguracoesSeguranca> => {
    const response = await api.get<ConfiguracoesSeguranca>('/configuracao/seguranca')
    return response.data
  },

  updateConfiguracoesSeguranca: async (data: Partial<ConfiguracoesSeguranca>): Promise<ConfiguracoesSeguranca> => {
    const response = await api.put<ConfiguracoesSeguranca>('/configuracao/seguranca', data)
    return response.data
  },

  // Configuracoes de Integracao
  getConfiguracoesIntegracao: async (): Promise<ConfiguracoesIntegracao> => {
    const response = await api.get<ConfiguracoesIntegracao>('/configuracao/integracao')
    return response.data
  },

  updateConfiguracoesIntegracao: async (data: Partial<ConfiguracoesIntegracao>): Promise<ConfiguracoesIntegracao> => {
    const response = await api.put<ConfiguracoesIntegracao>('/configuracao/integracao', data)
    return response.data
  },

  testarIntegracaoSIAU: async (): Promise<{ sucesso: boolean; mensagem: string }> => {
    const response = await api.post('/configuracao/integracao/testar-siau')
    return response.data
  },

  testarWebhook: async (url: string): Promise<{ sucesso: boolean; statusCode?: number; erro?: string }> => {
    const response = await api.post('/configuracao/integracao/testar-webhook', { url })
    return response.data
  },

  // Configuracoes de Aparencia
  getConfiguracoesAparencia: async (): Promise<ConfiguracoesAparencia> => {
    const response = await api.get<ConfiguracoesAparencia>('/configuracao/aparencia')
    return response.data
  },

  updateConfiguracoesAparencia: async (data: Partial<ConfiguracoesAparencia>): Promise<ConfiguracoesAparencia> => {
    const response = await api.put<ConfiguracoesAparencia>('/configuracao/aparencia', data)
    return response.data
  },

  uploadLogo: async (arquivo: File): Promise<{ logoUrl: string }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<{ logoUrl: string }>('/configuracao/aparencia/logo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  uploadFavicon: async (arquivo: File): Promise<{ faviconUrl: string }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<{ faviconUrl: string }>('/configuracao/aparencia/favicon', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  // Logs de Alteracao
  getLogs: async (params?: {
    chave?: string
    usuarioId?: string
    dataInicio?: string
    dataFim?: string
    page?: number
    pageSize?: number
  }): Promise<{ data: LogConfiguracao[]; total: number }> => {
    const response = await api.get('/configuracao/logs', { params })
    return response.data
  },

  // Backup e Restauracao
  getBackups: async (): Promise<BackupConfiguracao[]> => {
    const response = await api.get<BackupConfiguracao[]>('/configuracao/backups')
    return response.data
  },

  criarBackup: async (nome: string, descricao?: string): Promise<BackupConfiguracao> => {
    const response = await api.post<BackupConfiguracao>('/configuracao/backups', { nome, descricao })
    return response.data
  },

  restaurarBackup: async (backupId: string): Promise<void> => {
    await api.post(`/configuracao/backups/${backupId}/restaurar`)
  },

  deletarBackup: async (backupId: string): Promise<void> => {
    await api.delete(`/configuracao/backups/${backupId}`)
  },

  exportarConfiguracoes: async (): Promise<Blob> => {
    const response = await api.get('/configuracao/exportar', {
      responseType: 'blob',
    })
    return response.data
  },

  importarConfiguracoes: async (arquivo: File): Promise<{ sucesso: number; erros: string[] }> => {
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post('/configuracao/importar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  // Reset
  resetarPadrao: async (tipo?: TipoConfiguracao): Promise<void> => {
    await api.post('/configuracao/resetar', { tipo })
  },
}
