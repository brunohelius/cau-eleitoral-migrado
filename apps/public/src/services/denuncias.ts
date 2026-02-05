import api, { setTokenType, extractApiError } from './api'

// ============================================
// Enums matching backend
// ============================================

export enum TipoDenuncia {
  PropagandaIrregular = 0,
  AbusoPoder = 1,
  CaptacaoIlicitaSufragio = 2,
  UsoIndevido = 3,
  Inelegibilidade = 4,
  FraudeDocumental = 5,
  Outros = 99,
}

export enum StatusDenuncia {
  Recebida = 0,
  EmAnalise = 1,
  AdmissibilidadeAceita = 2,
  AdmissibilidadeRejeitada = 3,
  AguardandoDefesa = 4,
  DefesaApresentada = 5,
  AguardandoJulgamento = 6,
  Julgada = 7,
  Procedente = 8,
  Improcedente = 9,
  ParcialmenteProcedente = 10,
  Arquivada = 11,
  AguardandoRecurso = 12,
  RecursoApresentado = 13,
  RecursoJulgado = 14,
}

export enum StatusDefesa {
  AguardandoDefesa = 0,
  Apresentada = 1,
  NaoApresentada = 2,
  Intempestiva = 3,
}

export enum TipoArquivoDenuncia {
  Documento = 0,
  Imagem = 1,
  Video = 2,
  Audio = 3,
  Planilha = 4,
  Comprovante = 5,
  Outros = 99,
}

// ============================================
// Interfaces
// ============================================

export interface EleicaoParaDenuncia {
  id: string
  nome: string
  ano: number
  regional: string
}

export interface TipoDenunciaInfo {
  valor: number
  nome: string
  descricao: string
}

export interface CaptchaData {
  pergunta: string
  token: string
  expectedValue: number
}

export interface CreateDenunciaPublicaDto {
  eleicaoId: string
  tipo: TipoDenuncia
  descricao: string
  fundamentacao?: string
  captchaValue: number
  captchaExpected: number
  arquivos?: {
    nome: string
    tipo: string
    base64Content: string
  }[]
}

export interface DenunciaPublicaResult {
  protocolo: string
  dataEnvio: string
  mensagem: string
}

export interface ConsultaProtocolo {
  protocolo: string
  status: string
  statusNome: string
  dataEnvio: string
  ultimaAtualizacao: string
  eleicaoNome?: string
  tipoDenuncia?: string
}

export interface ArquivoDenuncia {
  id: string
  nome: string
  tipo: TipoArquivoDenuncia
  tipoNome: string
  url: string
  tamanho: number
  createdAt: string
}

export interface Denuncia {
  id: string
  eleicaoId: string
  eleicaoNome: string
  denuncianteId: string
  denuncianteNome: string
  denunciadoId?: string
  denunciadoNome?: string
  chapaId?: string
  chapaNome?: string
  chapaNumero?: number
  tipo: TipoDenuncia
  tipoNome: string
  status: StatusDenuncia
  statusNome: string
  descricao: string
  fundamentacao?: string
  prazoDefesa?: string
  statusDefesa: StatusDefesa
  statusDefesaNome: string
  defesaTexto?: string
  dataDefesa?: string
  parecer?: string
  decisao?: string
  dataJulgamento?: string
  arquivos: ArquivoDenuncia[]
  protocolo?: string
  createdAt: string
  updatedAt?: string
}

export interface DenunciaResumida {
  id: string
  eleicaoId: string
  eleicaoNome: string
  tipo: TipoDenuncia
  tipoNome: string
  status: StatusDenuncia
  statusNome: string
  descricao: string
  protocolo?: string
  createdAt: string
}

export interface CreateDenunciaDto {
  eleicaoId: string
  denunciadoId?: string
  chapaId?: string
  tipo: TipoDenuncia
  descricao: string
  fundamentacao?: string
}

export interface UpdateDenunciaDto {
  tipo?: TipoDenuncia
  descricao?: string
  fundamentacao?: string
}

export interface CreateArquivoDenunciaDto {
  nome: string
  tipo: TipoArquivoDenuncia
  url: string
  tamanho: number
}

// ============================================
// Helper Functions
// ============================================

export const getTipoDenunciaLabel = (tipo: TipoDenuncia): string => {
  const labels: Record<TipoDenuncia, string> = {
    [TipoDenuncia.PropagandaIrregular]: 'Propaganda Irregular',
    [TipoDenuncia.AbusoPoder]: 'Abuso de Poder',
    [TipoDenuncia.CaptacaoIlicitaSufragio]: 'Captacao Ilicita de Sufragio',
    [TipoDenuncia.UsoIndevido]: 'Uso Indevido de Recursos',
    [TipoDenuncia.Inelegibilidade]: 'Inelegibilidade',
    [TipoDenuncia.FraudeDocumental]: 'Fraude Documental',
    [TipoDenuncia.Outros]: 'Outros',
  }
  return labels[tipo] || 'Desconhecido'
}

export const getTipoDenunciaDescricao = (tipo: TipoDenuncia): string => {
  const descricoes: Record<TipoDenuncia, string> = {
    [TipoDenuncia.PropagandaIrregular]: 'Propaganda eleitoral fora das normas estabelecidas pelo CAU',
    [TipoDenuncia.AbusoPoder]: 'Uso indevido de cargo ou funcao para influenciar a eleicao',
    [TipoDenuncia.CaptacaoIlicitaSufragio]: 'Oferta de vantagens para obter votos',
    [TipoDenuncia.UsoIndevido]: 'Utilizacao irregular de recursos publicos ou privados',
    [TipoDenuncia.Inelegibilidade]: 'Candidato que nao atende aos requisitos legais',
    [TipoDenuncia.FraudeDocumental]: 'Falsificacao ou adulteracao de documentos',
    [TipoDenuncia.Outros]: 'Outras irregularidades nao listadas acima',
  }
  return descricoes[tipo] || ''
}

export const getStatusDenunciaLabel = (status: StatusDenuncia): string => {
  const labels: Record<StatusDenuncia, string> = {
    [StatusDenuncia.Recebida]: 'Recebida',
    [StatusDenuncia.EmAnalise]: 'Em Analise',
    [StatusDenuncia.AdmissibilidadeAceita]: 'Admissibilidade Aceita',
    [StatusDenuncia.AdmissibilidadeRejeitada]: 'Admissibilidade Rejeitada',
    [StatusDenuncia.AguardandoDefesa]: 'Aguardando Defesa',
    [StatusDenuncia.DefesaApresentada]: 'Defesa Apresentada',
    [StatusDenuncia.AguardandoJulgamento]: 'Aguardando Julgamento',
    [StatusDenuncia.Julgada]: 'Julgada',
    [StatusDenuncia.Procedente]: 'Procedente',
    [StatusDenuncia.Improcedente]: 'Improcedente',
    [StatusDenuncia.ParcialmenteProcedente]: 'Parcialmente Procedente',
    [StatusDenuncia.Arquivada]: 'Arquivada',
    [StatusDenuncia.AguardandoRecurso]: 'Aguardando Recurso',
    [StatusDenuncia.RecursoApresentado]: 'Recurso Apresentado',
    [StatusDenuncia.RecursoJulgado]: 'Recurso Julgado',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusDenunciaColor = (status: StatusDenuncia): string => {
  const colors: Record<StatusDenuncia, string> = {
    [StatusDenuncia.Recebida]: 'blue',
    [StatusDenuncia.EmAnalise]: 'blue',
    [StatusDenuncia.AdmissibilidadeAceita]: 'green',
    [StatusDenuncia.AdmissibilidadeRejeitada]: 'red',
    [StatusDenuncia.AguardandoDefesa]: 'yellow',
    [StatusDenuncia.DefesaApresentada]: 'blue',
    [StatusDenuncia.AguardandoJulgamento]: 'purple',
    [StatusDenuncia.Julgada]: 'gray',
    [StatusDenuncia.Procedente]: 'green',
    [StatusDenuncia.Improcedente]: 'red',
    [StatusDenuncia.ParcialmenteProcedente]: 'orange',
    [StatusDenuncia.Arquivada]: 'gray',
    [StatusDenuncia.AguardandoRecurso]: 'yellow',
    [StatusDenuncia.RecursoApresentado]: 'blue',
    [StatusDenuncia.RecursoJulgado]: 'gray',
  }
  return colors[status] || 'gray'
}

export const getStatusDefesaLabel = (status: StatusDefesa): string => {
  const labels: Record<StatusDefesa, string> = {
    [StatusDefesa.AguardandoDefesa]: 'Aguardando Defesa',
    [StatusDefesa.Apresentada]: 'Apresentada',
    [StatusDefesa.NaoApresentada]: 'Nao Apresentada',
    [StatusDefesa.Intempestiva]: 'Intempestiva',
  }
  return labels[status] || 'Desconhecido'
}

export const getTipoArquivoLabel = (tipo: TipoArquivoDenuncia): string => {
  const labels: Record<TipoArquivoDenuncia, string> = {
    [TipoArquivoDenuncia.Documento]: 'Documento',
    [TipoArquivoDenuncia.Imagem]: 'Imagem',
    [TipoArquivoDenuncia.Video]: 'Video',
    [TipoArquivoDenuncia.Audio]: 'Audio',
    [TipoArquivoDenuncia.Planilha]: 'Planilha',
    [TipoArquivoDenuncia.Comprovante]: 'Comprovante',
    [TipoArquivoDenuncia.Outros]: 'Outros',
  }
  return labels[tipo] || 'Desconhecido'
}

/**
 * Opcoes de tipos de denuncia para formularios
 */
export const tiposDenunciaOptions = [
  { value: TipoDenuncia.PropagandaIrregular, label: 'Propaganda Irregular' },
  { value: TipoDenuncia.AbusoPoder, label: 'Abuso de Poder' },
  { value: TipoDenuncia.CaptacaoIlicitaSufragio, label: 'Captacao Ilicita de Sufragio' },
  { value: TipoDenuncia.UsoIndevido, label: 'Uso Indevido de Recursos' },
  { value: TipoDenuncia.Inelegibilidade, label: 'Inelegibilidade' },
  { value: TipoDenuncia.FraudeDocumental, label: 'Fraude Documental' },
  { value: TipoDenuncia.Outros, label: 'Outros' },
]

/**
 * Opcoes de tipos de arquivo para upload
 */
export const tiposArquivoOptions = [
  { value: TipoArquivoDenuncia.Documento, label: 'Documento' },
  { value: TipoArquivoDenuncia.Imagem, label: 'Imagem' },
  { value: TipoArquivoDenuncia.Video, label: 'Video' },
  { value: TipoArquivoDenuncia.Audio, label: 'Audio' },
  { value: TipoArquivoDenuncia.Planilha, label: 'Planilha' },
  { value: TipoArquivoDenuncia.Comprovante, label: 'Comprovante' },
  { value: TipoArquivoDenuncia.Outros, label: 'Outros' },
]

// ============================================
// Public Denuncias Service (Anonymous)
// ============================================

export const denunciasPublicService = {
  /**
   * Get elections available for complaints
   */
  getEleicoesParaDenuncia: async (): Promise<EleicaoParaDenuncia[]> => {
    try {
      const response = await api.get<EleicaoParaDenuncia[]>('/public/denuncias/eleicoes')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching eleicoes for denuncia:', apiError.message)
      return []
    }
  },

  /**
   * Get complaint types
   */
  getTiposDenuncia: async (): Promise<TipoDenunciaInfo[]> => {
    try {
      const response = await api.get<TipoDenunciaInfo[]>('/public/denuncias/tipos')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      console.error('Error fetching tipos denuncia:', apiError.message)
      return []
    }
  },

  /**
   * Get captcha data
   */
  getCaptcha: async (): Promise<CaptchaData> => {
    try {
      const response = await api.get<CaptchaData>('/public/denuncias/captcha')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter captcha')
    }
  },

  /**
   * Submit public complaint (anonymous)
   */
  create: async (dto: CreateDenunciaPublicaDto): Promise<DenunciaPublicaResult> => {
    try {
      const response = await api.post<DenunciaPublicaResult>('/public/denuncias', dto)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao enviar denuncia')
    }
  },

  /**
   * Check complaint status by protocol
   */
  consultarProtocolo: async (protocolo: string): Promise<ConsultaProtocolo> => {
    try {
      const response = await api.get<ConsultaProtocolo>(`/public/denuncias/protocolo/${protocolo}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Protocolo nao encontrado')
    }
  },
}

// ============================================
// Authenticated Denuncias Service
// ============================================

export const denunciasService = {
  // ========================================
  // Authenticated User Endpoints
  // ========================================

  /**
   * Cria uma nova denuncia como eleitor
   */
  create: async (data: CreateDenunciaDto): Promise<Denuncia> => {
    try {
      setTokenType('voter')
      const response = await api.post<Denuncia>('/denuncia', data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao registrar denuncia')
    }
  },

  /**
   * Cria uma nova denuncia como candidato
   */
  createAsCandidato: async (data: CreateDenunciaDto): Promise<Denuncia> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Denuncia>('/denuncia', data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao registrar denuncia')
    }
  },

  /**
   * Obtem uma denuncia pelo ID
   */
  getById: async (id: string): Promise<Denuncia> => {
    try {
      setTokenType('voter')
      const response = await api.get<Denuncia>(`/denuncia/${id}`)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao obter denuncia')
    }
  },

  /**
   * Lista denuncias do usuario logado (como denunciante)
   */
  getMinhasDenuncias: async (): Promise<DenunciaResumida[]> => {
    try {
      setTokenType('voter')
      const response = await api.get<DenunciaResumida[]>('/denuncia/minhas')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar suas denuncias')
    }
  },

  /**
   * Lista denuncias do candidato logado (como denunciante)
   */
  getMinhasDenunciasAsCandidato: async (): Promise<DenunciaResumida[]> => {
    try {
      setTokenType('candidate')
      const response = await api.get<DenunciaResumida[]>('/denuncia/minhas')
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao listar suas denuncias')
    }
  },

  /**
   * Atualiza uma denuncia existente (apenas antes da analise)
   */
  update: async (id: string, data: UpdateDenunciaDto): Promise<Denuncia> => {
    try {
      setTokenType('voter')
      const response = await api.put<Denuncia>(`/denuncia/${id}`, data)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao atualizar denuncia')
    }
  },

  /**
   * Adiciona arquivo de prova
   */
  addArquivo: async (
    denunciaId: string,
    dto: CreateArquivoDenunciaDto
  ): Promise<ArquivoDenuncia> => {
    try {
      setTokenType('voter')
      const response = await api.post<ArquivoDenuncia>(`/denuncia/${denunciaId}/arquivos`, dto)
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao adicionar arquivo')
    }
  },

  /**
   * Remove um arquivo de prova
   */
  removeArquivo: async (denunciaId: string, arquivoId: string): Promise<void> => {
    try {
      setTokenType('voter')
      await api.delete(`/denuncia/${denunciaId}/arquivos/${arquivoId}`)
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao remover arquivo')
    }
  },

  /**
   * Apresenta defesa (para o denunciado)
   */
  apresentarDefesa: async (
    denunciaId: string,
    texto: string,
    arquivos?: CreateArquivoDenunciaDto[]
  ): Promise<Denuncia> => {
    try {
      setTokenType('candidate')
      const response = await api.post<Denuncia>(`/denuncia/${denunciaId}/apresentar-defesa`, {
        texto,
        arquivos,
      })
      return response.data
    } catch (error) {
      const apiError = extractApiError(error)
      throw new Error(apiError.message || 'Erro ao apresentar defesa')
    }
  },

  // ========================================
  // Utility Methods
  // ========================================

  /**
   * Verifica se a denuncia pode ser editada
   */
  podeEditar: (denuncia: Denuncia | DenunciaResumida): boolean => {
    return denuncia.status === StatusDenuncia.Recebida
  },

  /**
   * Verifica se a denuncia permite upload de arquivos
   */
  podeAdicionarArquivo: (denuncia: Denuncia): boolean => {
    return (
      denuncia.status === StatusDenuncia.Recebida ||
      denuncia.status === StatusDenuncia.EmAnalise
    )
  },

  /**
   * Verifica se a defesa pode ser apresentada
   */
  podeApresentarDefesa: (denuncia: Denuncia): boolean => {
    return (
      denuncia.status === StatusDenuncia.AguardandoDefesa &&
      denuncia.statusDefesa === StatusDefesa.AguardandoDefesa
    )
  },

  /**
   * Calcula dias restantes para defesa
   */
  diasRestantesDefesa: (denuncia: Denuncia): number | null => {
    if (!denuncia.prazoDefesa) return null
    const prazo = new Date(denuncia.prazoDefesa)
    const hoje = new Date()
    const diffTime = prazo.getTime() - hoje.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays > 0 ? diffDays : 0
  },

  /**
   * Verifica se a denuncia foi julgada
   */
  foiJulgada: (denuncia: Denuncia | DenunciaResumida): boolean => {
    return (
      denuncia.status === StatusDenuncia.Julgada ||
      denuncia.status === StatusDenuncia.Procedente ||
      denuncia.status === StatusDenuncia.Improcedente ||
      denuncia.status === StatusDenuncia.ParcialmenteProcedente ||
      denuncia.status === StatusDenuncia.Arquivada
    )
  },

  /**
   * Formata o numero do protocolo
   */
  formatarProtocolo: (protocolo: string): string => {
    if (protocolo.includes('/') || protocolo.includes('-')) {
      return protocolo
    }
    if (protocolo.length >= 6) {
      const ano = protocolo.substring(0, 4)
      const numero = protocolo.substring(4)
      return `${ano}/${numero.padStart(6, '0')}`
    }
    return protocolo
  },

  /**
   * Extensoes de arquivo permitidas por tipo
   */
  getExtensoesPermitidas: (tipo: TipoArquivoDenuncia): string[] => {
    const extensoes: Record<TipoArquivoDenuncia, string[]> = {
      [TipoArquivoDenuncia.Documento]: ['.pdf', '.doc', '.docx', '.txt', '.odt'],
      [TipoArquivoDenuncia.Imagem]: ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
      [TipoArquivoDenuncia.Video]: ['.mp4', '.avi', '.mov', '.wmv', '.webm'],
      [TipoArquivoDenuncia.Audio]: ['.mp3', '.wav', '.ogg', '.m4a'],
      [TipoArquivoDenuncia.Planilha]: ['.xls', '.xlsx', '.csv', '.ods'],
      [TipoArquivoDenuncia.Comprovante]: ['.pdf', '.jpg', '.jpeg', '.png'],
      [TipoArquivoDenuncia.Outros]: ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png', '.zip'],
    }
    return extensoes[tipo] || []
  },

  /**
   * Tamanho maximo de arquivo em bytes (10MB)
   */
  tamanhoMaximoArquivo: 10 * 1024 * 1024,

  /**
   * Valida arquivo antes do upload
   */
  validarArquivo: (arquivo: File, tipo: TipoArquivoDenuncia): { valido: boolean; erro?: string } => {
    if (arquivo.size > denunciasService.tamanhoMaximoArquivo) {
      return {
        valido: false,
        erro: `Arquivo muito grande. Tamanho maximo: ${denunciasService.tamanhoMaximoArquivo / (1024 * 1024)}MB`,
      }
    }

    const extensao = '.' + arquivo.name.split('.').pop()?.toLowerCase()
    const extensoesPermitidas = denunciasService.getExtensoesPermitidas(tipo)
    if (!extensoesPermitidas.includes(extensao)) {
      return {
        valido: false,
        erro: `Extensao nao permitida. Permitidos: ${extensoesPermitidas.join(', ')}`,
      }
    }

    return { valido: true }
  },
}

export default denunciasPublicService
