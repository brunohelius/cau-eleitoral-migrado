/**
 * Common types and enums for the CAU Electoral System
 * These types mirror the backend DTOs and enums
 */

// ============================================
// Pagination and Response Types
// ============================================

export interface PaginatedResponse<T> {
  items: T[]
  totalCount: number
  pageNumber: number
  pageSize: number
  totalPages: number
  hasNextPage: boolean
  hasPreviousPage: boolean
}

export interface ApiResponse<T> {
  success: boolean
  data?: T
  message?: string
  errors?: string[]
}

// ============================================
// Election Enums
// ============================================

export enum StatusEleicao {
  RASCUNHO = 0,
  AGENDADA = 1,
  EM_ANDAMENTO = 2,
  ENCERRADA = 3,
  SUSPENSA = 4,
  CANCELADA = 5,
  APURACAO_EM_ANDAMENTO = 6,
  FINALIZADA = 7,
}

export enum TipoEleicao {
  CAU_BR = 0,
  CAU_UF = 1,
  CONSELHEIRO_FEDERAL = 2,
  CONSELHEIRO_ESTADUAL = 3,
}

// ============================================
// Chapa Enums
// ============================================

export enum StatusChapa {
  RASCUNHO = 0,
  PENDENTE_DOCUMENTOS = 1,
  AGUARDANDO_ANALISE = 2,
  EM_ANALISE = 3,
  DEFERIDA = 4,
  INDEFERIDA = 5,
  IMPUGNADA = 6,
  AGUARDANDO_JULGAMENTO = 7,
  REGISTRADA = 8,
  CANCELADA = 9,
}

export enum TipoMembroChapa {
  PRESIDENTE = 0,
  VICE_PRESIDENTE = 1,
  PRIMEIRO_SECRETARIO = 2,
  SEGUNDO_SECRETARIO = 3,
  PRIMEIRO_TESOUREIRO = 4,
  SEGUNDO_TESOUREIRO = 5,
  CONSELHEIRO_TITULAR = 6,
  CONSELHEIRO_SUPLENTE = 7,
  DELEGADO = 8,
  DELEGADO_SUPLENTE = 9,
}

export enum StatusMembroChapa {
  PENDENTE = 0,
  AGUARDANDO_CONFIRMACAO = 1,
  CONFIRMADO = 2,
  RECUSADO = 3,
  SUBSTITUIDO = 4,
  INABILITADO = 5,
}

// ============================================
// Denuncia Enums
// ============================================

export enum TipoDenuncia {
  PROPAGANDA_IRREGULAR = 0,
  ABUSO_PODER = 1,
  CAPTACAO_ILICITA_SUFRAGIO = 2,
  USO_INDEVIDO = 3,
  INELEGIBILIDADE = 4,
  FRAUDE_DOCUMENTAL = 5,
  OUTROS = 99,
}

export enum StatusDenuncia {
  RECEBIDA = 0,
  EM_ANALISE = 1,
  ADMISSIBILIDADE_ACEITA = 2,
  ADMISSIBILIDADE_REJEITADA = 3,
  AGUARDANDO_DEFESA = 4,
  DEFESA_APRESENTADA = 5,
  AGUARDANDO_JULGAMENTO = 6,
  JULGADA = 7,
  PROCEDENTE = 8,
  IMPROCEDENTE = 9,
  PARCIALMENTE_PROCEDENTE = 10,
  ARQUIVADA = 11,
  AGUARDANDO_RECURSO = 12,
  RECURSO_APRESENTADO = 13,
  RECURSO_JULGADO = 14,
}

export enum StatusDefesa {
  AGUARDANDO_DEFESA = 0,
  APRESENTADA = 1,
  NAO_APRESENTADA = 2,
  INTEMPESTIVA = 3,
}

export enum TipoArquivoDenuncia {
  DOCUMENTO = 0,
  IMAGEM = 1,
  VIDEO = 2,
  AUDIO = 3,
  PLANILHA = 4,
  COMPROVANTE = 5,
  OUTROS = 99,
}

// ============================================
// Impugnacao Enums
// ============================================

export enum TipoImpugnacao {
  IMPUGNACAO_CHAPA = 0,
  IMPUGNACAO_MEMBRO = 1,
  IMPUGNACAO_DOCUMENTO = 2,
  IMPUGNACAO_RESULTADO = 3,
}

export enum StatusImpugnacao {
  RECEBIDA = 0,
  EM_ANALISE = 1,
  AGUARDANDO_ALEGACOES = 2,
  ALEGACOES_APRESENTADAS = 3,
  AGUARDANDO_CONTRA_ALEGACOES = 4,
  CONTRA_ALEGACOES_APRESENTADAS = 5,
  AGUARDANDO_JULGAMENTO = 6,
  JULGADA = 7,
  PROCEDENTE = 8,
  IMPROCEDENTE = 9,
  PARCIALMENTE_PROCEDENTE = 10,
  ARQUIVADA = 11,
  AGUARDANDO_RECURSO = 12,
  RECURSO_APRESENTADO = 13,
  RECURSO_JULGADO = 14,
}

export enum TipoAlegacao {
  INELEGIBILIDADE = 0,
  IRREGULARIDADE_DOCUMENTAL = 1,
  VIOLACAO_NORMA = 2,
  FRAUDE_ELEITORAL = 3,
  ABUSO_PODER = 4,
  OUTROS = 99,
}

// ============================================
// Calendario Enums
// ============================================

export enum TipoCalendario {
  INSCRICAO = 0,
  IMPUGNACAO = 1,
  DEFESA = 2,
  JULGAMENTO = 3,
  RECURSO = 4,
  PROPAGANDA = 5,
  VOTACAO = 6,
  APURACAO = 7,
  RESULTADO = 8,
  DIPLOMACAO = 9,
}

export enum StatusCalendario {
  PENDENTE = 0,
  EM_ANDAMENTO = 1,
  CONCLUIDO = 2,
  CANCELADO = 3,
}

// ============================================
// Votacao Enums
// ============================================

export enum TipoVoto {
  CHAPA = 0,
  BRANCO = 1,
  NULO = 2,
}

// ============================================
// Shared Interfaces
// ============================================

export interface DocumentoPublico {
  id: string
  nome: string
  tipo: string
  url: string
  tamanho?: number
  dataPublicacao?: string
}

export interface Endereco {
  logradouro: string
  numero: string
  complemento?: string
  bairro: string
  cidade: string
  uf: string
  cep: string
}

export interface RedesSociais {
  facebook?: string
  instagram?: string
  linkedin?: string
  twitter?: string
}
