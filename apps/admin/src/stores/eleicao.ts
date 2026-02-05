import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import type { Eleicao } from '@/services/eleicoes'

interface EleicaoState {
  // Current selected election
  eleicaoAtual: Eleicao | null

  // List of active elections for quick switch
  eleicoesAtivas: Eleicao[]

  // Loading states
  isLoading: boolean

  // Error state
  error: string | null

  // Actions
  setEleicaoAtual: (eleicao: Eleicao | null) => void
  setEleicoesAtivas: (eleicoes: Eleicao[]) => void
  setLoading: (loading: boolean) => void
  setError: (error: string | null) => void
  updateEleicaoAtual: (updates: Partial<Eleicao>) => void
  clearEleicaoAtual: () => void

  // Computed getters
  isEleicaoSelecionada: () => boolean
  getEleicaoById: (id: string) => Eleicao | undefined
}

export const useEleicaoStore = create<EleicaoState>()(
  persist(
    (set, get) => ({
      eleicaoAtual: null,
      eleicoesAtivas: [],
      isLoading: false,
      error: null,

      setEleicaoAtual: (eleicao) =>
        set({
          eleicaoAtual: eleicao,
          error: null,
        }),

      setEleicoesAtivas: (eleicoes) =>
        set({
          eleicoesAtivas: eleicoes,
        }),

      setLoading: (loading) =>
        set({
          isLoading: loading,
        }),

      setError: (error) =>
        set({
          error,
          isLoading: false,
        }),

      updateEleicaoAtual: (updates) =>
        set((state) => ({
          eleicaoAtual: state.eleicaoAtual
            ? { ...state.eleicaoAtual, ...updates }
            : null,
        })),

      clearEleicaoAtual: () =>
        set({
          eleicaoAtual: null,
          error: null,
        }),

      isEleicaoSelecionada: () => get().eleicaoAtual !== null,

      getEleicaoById: (id) => {
        const { eleicaoAtual, eleicoesAtivas } = get()
        if (eleicaoAtual?.id === id) return eleicaoAtual
        return eleicoesAtivas.find((e) => e.id === id)
      },
    }),
    {
      name: 'eleicao-storage',
      partialize: (state) => ({
        eleicaoAtual: state.eleicaoAtual,
      }),
    }
  )
)

// Helper hook for common patterns
export const useCurrentEleicao = () => {
  const eleicaoAtual = useEleicaoStore((state) => state.eleicaoAtual)
  const isLoading = useEleicaoStore((state) => state.isLoading)
  const error = useEleicaoStore((state) => state.error)

  return {
    eleicao: eleicaoAtual,
    eleicaoId: eleicaoAtual?.id,
    isLoading,
    error,
    hasEleicao: eleicaoAtual !== null,
  }
}
