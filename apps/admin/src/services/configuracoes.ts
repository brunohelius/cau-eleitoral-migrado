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
  nomeRemetente?: string
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
  tamanhoMinimoSenha?: number
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

// Response from GET /configuracao - matches backend ConfiguracaoSistemaDto
export interface ConfiguracaoGeral {
  nomeSistema: string
  versao: string
  logoUrl?: string
  faviconUrl?: string
  corPrimaria?: string
  corSecundaria?: string
  modoManutencao: boolean
  mensagemManutencao?: string
  timeZone: string
  locale: string
}

export interface ConfiguracaoEmail {
  smtpHost: string
  smtpPort: number
  smtpUseSsl: boolean
  smtpUsername?: string
  smtpPassword?: string
  emailRemetente: string
  nomeRemetente: string
  emailHabilitado: boolean
}

export interface ConfiguracaoVotacao {
  permitirVotoBranco: boolean
  permitirVotoNulo: boolean
  mostrarResultadoParcial: boolean
  notificarVotoRegistrado: boolean
  tempoSessaoVotacaoEmMinutos: number
  confirmacaoVotoObrigatoria: boolean
  mensagemVotacao?: string
  mensagemConfirmacao?: string
}

export interface ConfiguracaoSegurancaSistema {
  tentativasLoginMax: number
  tempoBloqueioConta: number
  expiracaoSenhaEmDias: number
  tamanhoMinimoSenha: number
  requerLetraMaiuscula: boolean
  requerNumero: boolean
  requerCaractereEspecial: boolean
  expiracaoTokenEmMinutos: number
  expiracaoRefreshTokenEmDias: number
  doisFatoresObrigatorio: boolean
}

export interface ConfiguracaoResponse {
  geral: ConfiguracaoGeral
  email: ConfiguracaoEmail
  seguranca: ConfiguracaoSegurancaSistema
  votacao: ConfiguracaoVotacao
}

export const configuracoesService = {
  // Configuracoes Gerais
  getAll: async (): Promise<ConfiguracaoResponse> => {
    const response = await api.get<ConfiguracaoResponse>('/configuracao')
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

  updateMultiplas: async (configuracoes: { chave: string; valor: string }[]): Promise<any> => {
    // API expects PUT /configuracao with { configuracoes: { "key": "value", ... } }
    const configMap: Record<string, string> = {}
    configuracoes.forEach((c) => { configMap[c.chave] = c.valor })
    const response = await api.put('/configuracao', { configuracoes: configMap })
    return response.data
  },

  // Configuracoes de Eleicao
  getConfiguracoesEleicao: async (): Promise<ConfiguracoesEleicao> => {
    const response = await api.get<any>('/configuracao/eleicoes')
    const cfg = response.data || {}

    return {
      horasAntesInicioVotacao: 24,
      horasAposEncerramento: Number(cfg.diasRecurso || 5) * 24,
      permitirVotoAntecipado: false,
      permitirVotoPorProcuracao: false,
      exibirResultadosParciais: false,
      exibirResultadosAposEncerramento: true,
      requererJustificativaAusencia: false,
      tempoMaximoVotacao: 30,
      tentativasMaximasLogin: 5,
      bloquearAposXTentativas: 3,
      tempoBloqueioMinutos: 30,
      validarCPFReceita: true,
      validarRegistroCAU: true,
      permitirCandidaturaMultipla: false,
      diasMinimosInscricao: Number(cfg.diasInscricaoChapa || 15),
      diasMaximosRecurso: Number(cfg.diasRecurso || 5),
    }
  },

  updateConfiguracoesEleicao: async (data: Partial<ConfiguracoesEleicao>): Promise<ConfiguracoesEleicao> => {
    const current = await api.get<any>('/configuracao/eleicoes')
    const payload = {
      ...current.data,
      diasInscricaoChapa: data.diasMinimosInscricao ?? current.data?.diasInscricaoChapa ?? 15,
      diasRecurso: data.diasMaximosRecurso ?? current.data?.diasRecurso ?? 5,
    }

    await api.put('/configuracao/eleicoes', payload)
    return configuracoesService.getConfiguracoesEleicao()
  },

  // Configuracoes de Notificacao
  getConfiguracoesNotificacao: async (): Promise<ConfiguracoesNotificacao> => {
    const response = await api.get<any>('/configuracao/email')
    const email = response.data || {}
    return {
      emailHabilitado: !!email.emailHabilitado,
      smsHabilitado: false,
      pushHabilitado: false,
      notificarNovaEleicao: true,
      notificarInicioVotacao: true,
      notificarEncerramentoVotacao: true,
      notificarResultado: true,
      notificarDenuncia: true,
      notificarImpugnacao: true,
      notificarJulgamento: true,
      remetenteEmail: email.emailRemetente || 'noreply@cau.org.br',
      nomeRemetente: email.nomeRemetente || 'CAU Sistema Eleitoral',
      templateEmailBoasVindas: '',
      templateEmailRecuperacaoSenha: '',
      templateEmailNotificacao: '',
    }
  },

  updateConfiguracoesNotificacao: async (data: Partial<ConfiguracoesNotificacao>): Promise<ConfiguracoesNotificacao> => {
    const current = await api.get<any>('/configuracao/email')
    await api.put('/configuracao/email', {
      ...current.data,
      emailHabilitado: data.emailHabilitado ?? current.data?.emailHabilitado ?? false,
      emailRemetente: data.remetenteEmail ?? current.data?.emailRemetente ?? 'noreply@cau.org.br',
      nomeRemetente: data.nomeRemetente ?? current.data?.nomeRemetente ?? 'CAU Sistema Eleitoral',
    })

    return configuracoesService.getConfiguracoesNotificacao()
  },

  testarEmail: async (destinatario: string): Promise<{ sucesso: boolean; erro?: string }> => {
    try {
      await api.post('/configuracao/email/testar', { emailDestino: destinatario })
      return { sucesso: true }
    } catch (error: any) {
      return {
        sucesso: false,
        erro: error?.response?.data?.message || 'Falha ao enviar email de teste',
      }
    }
  },

  // Configuracoes de Seguranca
  getConfiguracoesSeguranca: async (): Promise<ConfiguracoesSeguranca> => {
    const response = await api.get<any>('/configuracao/seguranca')
    const seg = response.data || {}
    const tamanhoMinimoSenha = Number(seg.tamanhoMinimoSenha || 8)
    return {
      sessaoTimeoutMinutos: Number(seg.expiracaoTokenEmMinutos || 60),
      tokenExpiracaoHoras: Math.max(1, Math.ceil(Number(seg.expiracaoTokenEmMinutos || 60) / 60)),
      refreshTokenExpiracaoDias: Number(seg.expiracaoRefreshTokenEmDias || 7),
      requerer2FA: !!seg.doisFatoresObrigatorio,
      complexidadeSenhaMinima: tamanhoMinimoSenha >= 10 ? 'alta' : tamanhoMinimoSenha >= 8 ? 'media' : 'baixa',
      tamanhoMinimoSenha,
      diasExpiracaoSenha: Number(seg.expiracaoSenhaEmDias || 90),
      historicoSenhasImpedir: 5,
      ipWhitelist: [],
      ipBlacklist: [],
      rateLimitRequests: Number(seg.tentativasLoginMax || 5),
      rateLimitWindowMinutos: Number(seg.tempoBloqueioConta || 30),
      auditarTodasAcoes: true,
      criptografarVotos: true,
      algoritmoHash: 'bcrypt',
    }
  },

  updateConfiguracoesSeguranca: async (data: Partial<ConfiguracoesSeguranca>): Promise<ConfiguracoesSeguranca> => {
    const current = await api.get<any>('/configuracao/seguranca')
    const currentData = current.data || {}
    const tokenExpMin = data.tokenExpiracaoHoras
      ? Number(data.tokenExpiracaoHoras) * 60
      : Number(currentData.expiracaoTokenEmMinutos || 60)

    await api.put('/configuracao/seguranca', {
      ...currentData,
      tentativasLoginMax: data.rateLimitRequests ?? currentData.tentativasLoginMax ?? 5,
      tempoBloqueioConta: data.rateLimitWindowMinutos ?? currentData.tempoBloqueioConta ?? 30,
      expiracaoSenhaEmDias: data.diasExpiracaoSenha ?? currentData.expiracaoSenhaEmDias ?? 90,
      tamanhoMinimoSenha: data.tamanhoMinimoSenha ?? currentData.tamanhoMinimoSenha ?? 8,
      expiracaoTokenEmMinutos: tokenExpMin,
      expiracaoRefreshTokenEmDias: data.refreshTokenExpiracaoDias ?? currentData.expiracaoRefreshTokenEmDias ?? 7,
      doisFatoresObrigatorio: data.requerer2FA ?? currentData.doisFatoresObrigatorio ?? false,
    })

    return configuracoesService.getConfiguracoesSeguranca()
  },

  // Configuracoes de Integracao
  getConfiguracoesIntegracao: async (): Promise<ConfiguracoesIntegracao> => {
    return {
      apiExternaHabilitada: false,
      webhooksHabilitados: false,
      integracaoSIAU: false,
      integracaoReceitaFederal: false,
      integracaoEmail: 'smtp',
      integracaoStorage: 'local',
    }
  },

  updateConfiguracoesIntegracao: async (data: Partial<ConfiguracoesIntegracao>): Promise<ConfiguracoesIntegracao> => {
    return {
      apiExternaHabilitada: false,
      webhooksHabilitados: false,
      integracaoSIAU: false,
      integracaoReceitaFederal: false,
      integracaoEmail: 'smtp',
      integracaoStorage: 'local',
      ...data,
    }
  },

  testarIntegracaoSIAU: async (): Promise<{ sucesso: boolean; mensagem: string }> => {
    return { sucesso: false, mensagem: 'Integracao SIAU nao esta habilitada nesta API.' }
  },

  testarWebhook: async (url: string): Promise<{ sucesso: boolean; statusCode?: number; erro?: string }> => {
    try {
      const response = await fetch(url, { method: 'HEAD' })
      return { sucesso: response.ok, statusCode: response.status }
    } catch (error: any) {
      return { sucesso: false, erro: error?.message || 'Falha ao testar webhook' }
    }
  },

  // Configuracoes de Aparencia
  getConfiguracoesAparencia: async (): Promise<ConfiguracoesAparencia> => {
    const response = await api.get<any>('/configuracao')
    const geral = response.data?.geral || {}
    return {
      logoUrl: geral.logoUrl,
      faviconUrl: geral.faviconUrl,
      corPrimaria: geral.corPrimaria || '#1E40AF',
      corSecundaria: geral.corSecundaria || '#3B82F6',
      corAcento: '#0EA5E9',
      tema: 'claro',
      fontePrincipal: 'Inter',
      fonteSecundaria: 'Inter',
      borderRadius: 8,
      mostrarLogoPaginaLogin: true,
      textoRodape: '',
    }
  },

  updateConfiguracoesAparencia: async (data: Partial<ConfiguracoesAparencia>): Promise<ConfiguracoesAparencia> => {
    const keyValues: { chave: string; valor: string }[] = []
    if (data.corPrimaria !== undefined) keyValues.push({ chave: 'sistema.corPrimaria', valor: data.corPrimaria })
    if (data.corSecundaria !== undefined) keyValues.push({ chave: 'sistema.corSecundaria', valor: data.corSecundaria })
    if (data.logoUrl !== undefined) keyValues.push({ chave: 'sistema.logoUrl', valor: data.logoUrl || '' })
    if (data.faviconUrl !== undefined) keyValues.push({ chave: 'sistema.faviconUrl', valor: data.faviconUrl || '' })
    if (keyValues.length) {
      await configuracoesService.updateMultiplas(keyValues)
    }
    return configuracoesService.getConfiguracoesAparencia()
  },

  uploadLogo: async (arquivo: File): Promise<{ logoUrl: string }> => {
    const logoUrl = URL.createObjectURL(arquivo)
    await configuracoesService.updateMultiplas([{ chave: 'sistema.logoUrl', valor: logoUrl }])
    return { logoUrl }
  },

  uploadFavicon: async (arquivo: File): Promise<{ faviconUrl: string }> => {
    const faviconUrl = URL.createObjectURL(arquivo)
    await configuracoesService.updateMultiplas([{ chave: 'sistema.faviconUrl', valor: faviconUrl }])
    return { faviconUrl }
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
    return { data: [], total: 0 }
  },

  // Backup e Restauracao
  getBackups: async (): Promise<BackupConfiguracao[]> => {
    return []
  },

  criarBackup: async (nome: string, descricao?: string): Promise<BackupConfiguracao> => {
    return {
      id: crypto.randomUUID(),
      nome,
      descricao,
      dados: {},
      criadoPorId: '',
      criadoPorNome: 'Sistema',
      createdAt: new Date().toISOString(),
    }
  },

  restaurarBackup: async (backupId: string): Promise<void> => {
    await Promise.resolve()
  },

  deletarBackup: async (backupId: string): Promise<void> => {
    await Promise.resolve()
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
    await api.post('/configuracao/restaurar-padrao')
  },
}
