import { create } from 'zustand'
import { persist } from 'zustand/middleware'

export interface CandidatoInfo {
  id: string
  nome: string
  cpf: string
  registroCAU: string
  email: string
  telefone?: string
  chapaId: string
  chapaNome: string
  chapaNumero: number
  cargo: string
  tipo: string
  eleicaoId: string
  eleicaoNome: string
  status: number
}

export interface ChapaInfo {
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

export interface Documento {
  id: string
  tipo: number
  nome: string
  arquivoUrl: string
  tamanho: number
  status: 'pendente' | 'aprovado' | 'rejeitado'
  observacoes?: string
  uploadedAt: string
}

export interface Notificacao {
  id: string
  tipo: string
  titulo: string
  mensagem: string
  lida: boolean
  createdAt: string
}

interface CandidatoState {
  // Candidate data
  candidato: CandidatoInfo | null
  token: string | null
  expiresAt: string | null

  // Related data
  chapa: ChapaInfo | null
  documentos: Documento[]
  notificacoes: Notificacao[]

  // State
  isAuthenticated: boolean
  isLoading: boolean
  error: string | null

  // Actions
  setCandidato: (candidato: CandidatoInfo, token: string, expiresAt: string) => void
  updateCandidato: (updates: Partial<CandidatoInfo>) => void
  clearCandidato: () => void
  setChapa: (chapa: ChapaInfo | null) => void
  setDocumentos: (documentos: Documento[]) => void
  addDocumento: (documento: Documento) => void
  removeDocumento: (documentoId: string) => void
  updateDocumentoStatus: (documentoId: string, status: Documento['status'], observacoes?: string) => void
  setNotificacoes: (notificacoes: Notificacao[]) => void
  addNotificacao: (notificacao: Notificacao) => void
  marcarNotificacaoLida: (notificacaoId: string) => void
  setLoading: (loading: boolean) => void
  setError: (error: string | null) => void

  // Computed getters
  isTokenValid: () => boolean
  getUnreadNotificacoesCount: () => number
  getPendingDocumentosCount: () => number
  getStatusLabel: () => string
}

const statusLabels: Record<number, string> = {
  0: 'Pendente',
  1: 'Em Analise',
  2: 'Aprovada',
  3: 'Reprovada',
  4: 'Impugnada',
  5: 'Renunciada',
}

export const useCandidatoStore = create<CandidatoState>()(
  persist(
    (set, get) => ({
      candidato: null,
      token: null,
      expiresAt: null,
      chapa: null,
      documentos: [],
      notificacoes: [],
      isAuthenticated: false,
      isLoading: false,
      error: null,

      setCandidato: (candidato, token, expiresAt) =>
        set({
          candidato,
          token,
          expiresAt,
          isAuthenticated: true,
          error: null,
        }),

      updateCandidato: (updates) =>
        set((state) => ({
          candidato: state.candidato ? { ...state.candidato, ...updates } : null,
        })),

      clearCandidato: () =>
        set({
          candidato: null,
          token: null,
          expiresAt: null,
          chapa: null,
          documentos: [],
          notificacoes: [],
          isAuthenticated: false,
          error: null,
        }),

      setChapa: (chapa) =>
        set({ chapa }),

      setDocumentos: (documentos) =>
        set({ documentos }),

      addDocumento: (documento) =>
        set((state) => ({
          documentos: [...state.documentos, documento],
        })),

      removeDocumento: (documentoId) =>
        set((state) => ({
          documentos: state.documentos.filter((d) => d.id !== documentoId),
        })),

      updateDocumentoStatus: (documentoId, status, observacoes) =>
        set((state) => ({
          documentos: state.documentos.map((d) =>
            d.id === documentoId ? { ...d, status, observacoes } : d
          ),
        })),

      setNotificacoes: (notificacoes) =>
        set({ notificacoes }),

      addNotificacao: (notificacao) =>
        set((state) => ({
          notificacoes: [notificacao, ...state.notificacoes],
        })),

      marcarNotificacaoLida: (notificacaoId) =>
        set((state) => ({
          notificacoes: state.notificacoes.map((n) =>
            n.id === notificacaoId ? { ...n, lida: true } : n
          ),
        })),

      setLoading: (loading) =>
        set({ isLoading: loading }),

      setError: (error) =>
        set({ error, isLoading: false }),

      isTokenValid: () => {
        const { token, expiresAt } = get()
        if (!token || !expiresAt) return false
        return new Date(expiresAt) > new Date()
      },

      getUnreadNotificacoesCount: () => {
        const { notificacoes } = get()
        return notificacoes.filter((n) => !n.lida).length
      },

      getPendingDocumentosCount: () => {
        const { documentos } = get()
        return documentos.filter((d) => d.status === 'pendente').length
      },

      getStatusLabel: () => {
        const { candidato } = get()
        if (!candidato) return 'Desconhecido'
        return statusLabels[candidato.status] || 'Desconhecido'
      },
    }),
    {
      name: 'candidato-storage',
      partialize: (state) => ({
        candidato: state.candidato,
        token: state.token,
        expiresAt: state.expiresAt,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
)

// Helper hooks
export const useCandidato = () => {
  const candidato = useCandidatoStore((state) => state.candidato)
  const isAuthenticated = useCandidatoStore((state) => state.isAuthenticated)
  const isLoading = useCandidatoStore((state) => state.isLoading)
  const error = useCandidatoStore((state) => state.error)
  const clearCandidato = useCandidatoStore((state) => state.clearCandidato)
  const getStatusLabel = useCandidatoStore((state) => state.getStatusLabel)

  return {
    candidato,
    isAuthenticated,
    isLoading,
    error,
    statusLabel: getStatusLabel(),
    logout: clearCandidato,
  }
}

export const useCandidatoChapa = () => {
  const chapa = useCandidatoStore((state) => state.chapa)
  const setChapa = useCandidatoStore((state) => state.setChapa)

  return {
    chapa,
    setChapa,
    hasChapa: chapa !== null,
    membros: chapa?.membros || [],
  }
}

export const useCandidatoDocumentos = () => {
  const documentos = useCandidatoStore((state) => state.documentos)
  const setDocumentos = useCandidatoStore((state) => state.setDocumentos)
  const addDocumento = useCandidatoStore((state) => state.addDocumento)
  const removeDocumento = useCandidatoStore((state) => state.removeDocumento)
  const getPendingCount = useCandidatoStore((state) => state.getPendingDocumentosCount)

  return {
    documentos,
    setDocumentos,
    addDocumento,
    removeDocumento,
    pendingCount: getPendingCount(),
    aprovados: documentos.filter((d) => d.status === 'aprovado'),
    pendentes: documentos.filter((d) => d.status === 'pendente'),
    rejeitados: documentos.filter((d) => d.status === 'rejeitado'),
  }
}

export const useCandidatoNotificacoes = () => {
  const notificacoes = useCandidatoStore((state) => state.notificacoes)
  const setNotificacoes = useCandidatoStore((state) => state.setNotificacoes)
  const addNotificacao = useCandidatoStore((state) => state.addNotificacao)
  const marcarLida = useCandidatoStore((state) => state.marcarNotificacaoLida)
  const getUnreadCount = useCandidatoStore((state) => state.getUnreadNotificacoesCount)

  return {
    notificacoes,
    setNotificacoes,
    addNotificacao,
    marcarLida,
    unreadCount: getUnreadCount(),
    naoLidas: notificacoes.filter((n) => !n.lida),
  }
}
