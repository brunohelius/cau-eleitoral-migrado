import { create } from 'zustand'
import { persist } from 'zustand/middleware'

export interface VoterInfo {
  id: string
  nome: string
  cpf: string
  registroCAU: string
  email?: string
  regional?: string
  podeVotar: boolean
  jaVotou: boolean
  eleicaoId: string
  eleicaoNome: string
}

interface VoterState {
  // Voter data
  voter: VoterInfo | null
  token: string | null
  expiresAt: string | null

  // State
  isAuthenticated: boolean
  isLoading: boolean
  error: string | null

  // Actions
  setVoter: (voter: VoterInfo, token: string, expiresAt: string) => void
  updateVoter: (updates: Partial<VoterInfo>) => void
  clearVoter: () => void
  setLoading: (loading: boolean) => void
  setError: (error: string | null) => void

  // Computed getters
  canVote: () => boolean
  hasVoted: () => boolean
  isTokenValid: () => boolean
}

export const useVoterStore = create<VoterState>()(
  persist(
    (set, get) => ({
      voter: null,
      token: null,
      expiresAt: null,
      isAuthenticated: false,
      isLoading: false,
      error: null,

      setVoter: (voter, token, expiresAt) =>
        set({
          voter,
          token,
          expiresAt,
          isAuthenticated: true,
          error: null,
        }),

      updateVoter: (updates) =>
        set((state) => ({
          voter: state.voter ? { ...state.voter, ...updates } : null,
        })),

      clearVoter: () =>
        set({
          voter: null,
          token: null,
          expiresAt: null,
          isAuthenticated: false,
          error: null,
        }),

      setLoading: (loading) =>
        set({ isLoading: loading }),

      setError: (error) =>
        set({ error, isLoading: false }),

      canVote: () => {
        const { voter, isAuthenticated } = get()
        if (!isAuthenticated || !voter) return false
        return voter.podeVotar && !voter.jaVotou
      },

      hasVoted: () => {
        const { voter } = get()
        return voter?.jaVotou ?? false
      },

      isTokenValid: () => {
        const { token, expiresAt } = get()
        if (!token || !expiresAt) return false
        return new Date(expiresAt) > new Date()
      },
    }),
    {
      name: 'voter-storage',
      partialize: (state) => ({
        voter: state.voter,
        token: state.token,
        expiresAt: state.expiresAt,
        isAuthenticated: state.isAuthenticated,
      }),
    }
  )
)

// Helper hook for common patterns
export const useVoter = () => {
  const voter = useVoterStore((state) => state.voter)
  const isAuthenticated = useVoterStore((state) => state.isAuthenticated)
  const isLoading = useVoterStore((state) => state.isLoading)
  const error = useVoterStore((state) => state.error)
  const canVote = useVoterStore((state) => state.canVote)
  const hasVoted = useVoterStore((state) => state.hasVoted)
  const clearVoter = useVoterStore((state) => state.clearVoter)

  return {
    voter,
    isAuthenticated,
    isLoading,
    error,
    canVote: canVote(),
    hasVoted: hasVoted(),
    logout: clearVoter,
  }
}
