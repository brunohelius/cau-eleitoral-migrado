import api, { setTokenType } from './api'

// Enums
export enum StatusCandidatura {
  PENDENTE = 0,
  EM_ANALISE = 1,
  APROVADA = 2,
  REPROVADA = 3,
  IMPUGNADA = 4,
  RENUNCIADA = 5,
}

export enum TipoDocumentoCandidato {
  RG = 0,
  CPF = 1,
  COMPROVANTE_CAU = 2,
  CERTIDAO_QUITACAO = 3,
  CURRICULO = 4,
  FOTO = 5,
  DECLARACAO_BENS = 6,
  TERMO_ACEITE = 7,
  OUTROS = 99,
}

// Interfaces
export interface DadosCandidato {
  id: string
  nome: string
  cpf: string
  registroCAU: string
  email: string
  telefone?: string
  fotoUrl?: string
  curriculo?: string
  biografia?: string
  redesSociais?: {
    facebook?: string
    instagram?: string
    linkedin?: string
    twitter?: string
  }
  endereco?: {
    logradouro: string
    numero: string
    complemento?: string
    bairro: string
    cidade: string
    uf: string
    cep: string
  }
}

export interface CandidaturaInfo {
  id: string
  eleicaoId: string
  eleicaoNome: string
  chapaId: string
  chapaNome: string
  chapaNumero: number
  cargo: string
  tipo: 'titular' | 'suplente'
  ordem: number
  status: StatusCandidatura
  dataCadastro: string
  dataAprovacao?: string
  motivoReprovacao?: string
  observacoes?: string
}

export interface DocumentoCandidato {
  id: string
  tipo: TipoDocumentoCandidato
  nome: string
  arquivoUrl: string
  tamanho: number
  status: 'pendente' | 'aprovado' | 'rejeitado'
  observacoes?: string
  uploadedAt: string
}

export interface ChapaInfoCandidato {
  id: string
  numero: number
  nome: string
  sigla?: string
  lema?: string
  logoUrl?: string
  status: number
  membros: {
    id: string
    nome: string
    cargo: string
    tipo: string
    fotoUrl?: string
    isCurrentUser: boolean
  }[]
}

export interface AtualizarPerfilRequest {
  telefone?: string
  biografia?: string
  curriculo?: string
  redesSociais?: {
    facebook?: string
    instagram?: string
    linkedin?: string
    twitter?: string
  }
  endereco?: {
    logradouro: string
    numero: string
    complemento?: string
    bairro: string
    cidade: string
    uf: string
    cep: string
  }
}

export interface SolicitarCandidaturaRequest {
  eleicaoId: string
  chapaId?: string // if joining existing chapa
  cargo: string
  tipo: 'titular' | 'suplente'
  curriculo?: string
  biografia?: string
}

export const candidatoService = {
  // Profile
  getPerfil: async (): Promise<DadosCandidato> => {
    setTokenType('candidate')
    const response = await api.get<DadosCandidato>('/candidato/perfil')
    return response.data
  },

  atualizarPerfil: async (data: AtualizarPerfilRequest): Promise<DadosCandidato> => {
    setTokenType('candidate')
    const response = await api.put<DadosCandidato>('/candidato/perfil', data)
    return response.data
  },

  uploadFoto: async (arquivo: File): Promise<{ fotoUrl: string }> => {
    setTokenType('candidate')
    const formData = new FormData()
    formData.append('arquivo', arquivo)

    const response = await api.post<{ fotoUrl: string }>('/candidato/perfil/foto', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removerFoto: async (): Promise<void> => {
    setTokenType('candidate')
    await api.delete('/candidato/perfil/foto')
  },

  // Candidatura
  getCandidatura: async (): Promise<CandidaturaInfo | null> => {
    setTokenType('candidate')
    const response = await api.get<CandidaturaInfo>('/candidato/candidatura')
    return response.data
  },

  getCandidaturas: async (): Promise<CandidaturaInfo[]> => {
    setTokenType('candidate')
    const response = await api.get<CandidaturaInfo[]>('/candidato/candidaturas')
    return response.data
  },

  solicitarCandidatura: async (data: SolicitarCandidaturaRequest): Promise<CandidaturaInfo> => {
    setTokenType('candidate')
    const response = await api.post<CandidaturaInfo>('/candidato/candidatura', data)
    return response.data
  },

  renunciarCandidatura: async (candidaturaId: string, motivo: string): Promise<void> => {
    setTokenType('candidate')
    await api.post(`/candidato/candidatura/${candidaturaId}/renunciar`, { motivo })
  },

  // Chapa
  getChapa: async (): Promise<ChapaInfoCandidato | null> => {
    setTokenType('candidate')
    const response = await api.get<ChapaInfoCandidato>('/candidato/chapa')
    return response.data
  },

  // Documents
  getDocumentos: async (): Promise<DocumentoCandidato[]> => {
    setTokenType('candidate')
    const response = await api.get<DocumentoCandidato[]>('/candidato/documentos')
    return response.data
  },

  uploadDocumento: async (
    tipo: TipoDocumentoCandidato,
    arquivo: File,
    nome?: string
  ): Promise<DocumentoCandidato> => {
    setTokenType('candidate')
    const formData = new FormData()
    formData.append('tipo', tipo.toString())
    formData.append('arquivo', arquivo)
    if (nome) formData.append('nome', nome)

    const response = await api.post<DocumentoCandidato>('/candidato/documentos', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },

  removerDocumento: async (documentoId: string): Promise<void> => {
    setTokenType('candidate')
    await api.delete(`/candidato/documentos/${documentoId}`)
  },

  downloadDocumento: async (documentoId: string): Promise<Blob> => {
    setTokenType('candidate')
    const response = await api.get(`/candidato/documentos/${documentoId}/download`, {
      responseType: 'blob',
    })
    return response.data
  },

  getDocumentosObrigatorios: async (eleicaoId: string): Promise<{
    tipo: TipoDocumentoCandidato
    nome: string
    descricao: string
    obrigatorio: boolean
    enviado: boolean
    status?: 'pendente' | 'aprovado' | 'rejeitado'
  }[]> => {
    setTokenType('candidate')
    const response = await api.get(`/candidato/documentos/obrigatorios/${eleicaoId}`)
    return response.data
  },

  // Elections available for candidacy
  getEleicoesDisponiveis: async (): Promise<{
    id: string
    nome: string
    tipo: number
    dataLimiteInscricao: string
    vagasDisponiveis: number
    requisitos: string[]
  }[]> => {
    setTokenType('candidate')
    const response = await api.get('/candidato/eleicoes-disponiveis')
    return response.data
  },

  // Check eligibility for candidacy
  verificarElegibilidade: async (eleicaoId: string): Promise<{
    elegivel: boolean
    motivos?: string[]
    requisitosAtendidos: string[]
    requisitosPendentes: string[]
  }> => {
    setTokenType('candidate')
    const response = await api.get(`/candidato/elegibilidade/${eleicaoId}`)
    return response.data
  },

  // Notifications
  getNotificacoes: async (): Promise<{
    id: string
    tipo: string
    titulo: string
    mensagem: string
    lida: boolean
    createdAt: string
  }[]> => {
    setTokenType('candidate')
    const response = await api.get('/candidato/notificacoes')
    return response.data
  },

  marcarNotificacaoLida: async (notificacaoId: string): Promise<void> => {
    setTokenType('candidate')
    await api.post(`/candidato/notificacoes/${notificacaoId}/lida`)
  },

  // Accept terms
  aceitarTermos: async (eleicaoId: string): Promise<{
    aceito: boolean
    dataAceite: string
  }> => {
    setTokenType('candidate')
    const response = await api.post(`/candidato/termos/${eleicaoId}/aceitar`)
    return response.data
  },

  getTermos: async (eleicaoId: string): Promise<{
    titulo: string
    conteudo: string
    versao: string
    aceito: boolean
    dataAceite?: string
  }> => {
    setTokenType('candidate')
    const response = await api.get(`/candidato/termos/${eleicaoId}`)
    return response.data
  },
}

// Document type helpers
export const getTipoDocumentoLabel = (tipo: TipoDocumentoCandidato): string => {
  const labels: Record<TipoDocumentoCandidato, string> = {
    [TipoDocumentoCandidato.RG]: 'RG',
    [TipoDocumentoCandidato.CPF]: 'CPF',
    [TipoDocumentoCandidato.COMPROVANTE_CAU]: 'Comprovante de Registro no CAU',
    [TipoDocumentoCandidato.CERTIDAO_QUITACAO]: 'Certidao de Quitacao',
    [TipoDocumentoCandidato.CURRICULO]: 'Curriculo',
    [TipoDocumentoCandidato.FOTO]: 'Foto',
    [TipoDocumentoCandidato.DECLARACAO_BENS]: 'Declaracao de Bens',
    [TipoDocumentoCandidato.TERMO_ACEITE]: 'Termo de Aceite',
    [TipoDocumentoCandidato.OUTROS]: 'Outros',
  }
  return labels[tipo] || 'Desconhecido'
}

export const getStatusCandidaturaLabel = (status: StatusCandidatura): string => {
  const labels: Record<StatusCandidatura, string> = {
    [StatusCandidatura.PENDENTE]: 'Pendente',
    [StatusCandidatura.EM_ANALISE]: 'Em Analise',
    [StatusCandidatura.APROVADA]: 'Aprovada',
    [StatusCandidatura.REPROVADA]: 'Reprovada',
    [StatusCandidatura.IMPUGNADA]: 'Impugnada',
    [StatusCandidatura.RENUNCIADA]: 'Renunciada',
  }
  return labels[status] || 'Desconhecido'
}

export const getStatusCandidaturaColor = (status: StatusCandidatura): string => {
  const colors: Record<StatusCandidatura, string> = {
    [StatusCandidatura.PENDENTE]: 'yellow',
    [StatusCandidatura.EM_ANALISE]: 'blue',
    [StatusCandidatura.APROVADA]: 'green',
    [StatusCandidatura.REPROVADA]: 'red',
    [StatusCandidatura.IMPUGNADA]: 'orange',
    [StatusCandidatura.RENUNCIADA]: 'gray',
  }
  return colors[status] || 'gray'
}
