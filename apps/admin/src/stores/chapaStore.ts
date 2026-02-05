import { create } from 'zustand'
import { devtools } from 'zustand/middleware'
import type {
  Chapa,
  MembroChapa,
  ChapaListParams,
  ChapaEstatisticas,
  CreateChapaRequest,
  UpdateChapaRequest,
  StatusChapa,
  CargoMembro,
  TipoMembro,
} from '@/types'
import { chapasService } from '@/services/chapas'

interface ChapaState {
  // Data
  chapas: Chapa[]
  chapaAtual: Chapa | null
  membros: MembroChapa[]
  estatisticas: ChapaEstatisticas | null

  // Pagination
  total: number
  page: number
  pageSize: number
  totalPages: number

  // Loading states
  isLoading: boolean
  isLoadingChapa: boolean
  isLoadingMembros: boolean
  isSaving: boolean

  // Error state
  error: string | null

  // Filters
  filters: ChapaListParams

  // Actions - CRUD
  fetchChapas: (params?: ChapaListParams) => Promise<void>
  fetchChapaById: (id: string) => Promise<void>
  fetchChapasByEleicao: (eleicaoId: string) => Promise<void>
  createChapa: (data: CreateChapaRequest) => Promise<Chapa>
  updateChapa: (id: string, data: UpdateChapaRequest) => Promise<Chapa>
  deleteChapa: (id: string) => Promise<void>

  // Actions - Status
  aprovarChapa: (id: string) => Promise<Chapa>
  reprovarChapa: (id: string, motivo: string) => Promise<Chapa>
  suspenderChapa: (id: string, motivo: string) => Promise<Chapa>
  reativarChapa: (id: string) => Promise<Chapa>
  cancelarChapa: (id: string, motivo: string) => Promise<Chapa>

  // Actions - Membros
  fetchMembros: (chapaId: string) => Promise<void>
  addMembro: (chapaId: string, data: {
    candidatoId: string
    cargo: CargoMembro
    tipo: TipoMembro
    ordem: number
  }) => Promise<MembroChapa>
  removeMembro: (chapaId: string, membroId: string) => Promise<void>
  updateMembroOrdem: (chapaId: string, membroId: string, ordem: number) => Promise<MembroChapa>

  // Actions - Logo
  uploadLogo: (chapaId: string, arquivo: File) => Promise<string>
  removeLogo: (chapaId: string) => Promise<void>

  // Actions - Estatisticas
  fetchEstatisticas: (eleicaoId: string) => Promise<void>

  // Actions - State management
  setChapaAtual: (chapa: Chapa | null) => void
  setFilters: (filters: Partial<ChapaListParams>) => void
  clearFilters: () => void
  setPage: (page: number) => void
  setPageSize: (pageSize: number) => void
  clearError: () => void
  reset: () => void
}

const initialState = {
  chapas: [],
  chapaAtual: null,
  membros: [],
  estatisticas: null,
  total: 0,
  page: 1,
  pageSize: 10,
  totalPages: 0,
  isLoading: false,
  isLoadingChapa: false,
  isLoadingMembros: false,
  isSaving: false,
  error: null,
  filters: {},
}

export const useChapaStore = create<ChapaState>()(
  devtools(
    (set, get) => ({
      ...initialState,

      // CRUD Actions
      fetchChapas: async (params?: ChapaListParams) => {
        set({ isLoading: true, error: null })
        try {
          const mergedParams = { ...get().filters, ...params }
          const response = await chapasService.getAll(mergedParams)
          set({
            chapas: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar chapas'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchChapaById: async (id: string) => {
        set({ isLoadingChapa: true, error: null })
        try {
          const chapa = await chapasService.getById(id)
          set({ chapaAtual: chapa, isLoadingChapa: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar chapa'
          set({ error: message, isLoadingChapa: false })
          throw error
        }
      },

      fetchChapasByEleicao: async (eleicaoId: string) => {
        set({ isLoading: true, error: null })
        try {
          const chapas = await chapasService.getByEleicao(eleicaoId)
          set({
            chapas,
            total: chapas.length,
            totalPages: 1,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar chapas da eleicao'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      createChapa: async (data: CreateChapaRequest) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.create(data)
          set((state) => ({
            chapas: [chapa, ...state.chapas],
            total: state.total + 1,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao criar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      updateChapa: async (id: string, data: UpdateChapaRequest) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.update(id, data)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atualizar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      deleteChapa: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await chapasService.delete(id)
          set((state) => ({
            chapas: state.chapas.filter((c) => c.id !== id),
            total: state.total - 1,
            chapaAtual: state.chapaAtual?.id === id ? null : state.chapaAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao excluir chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Status Actions
      aprovarChapa: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.aprovar(id)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao aprovar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      reprovarChapa: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.reprovar(id, motivo)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao reprovar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      suspenderChapa: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.suspender(id, motivo)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao suspender chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      reativarChapa: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.reativar(id)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao reativar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      cancelarChapa: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const chapa = await chapasService.cancelar(id, motivo)
          set((state) => ({
            chapas: state.chapas.map((c) => (c.id === id ? chapa : c)),
            chapaAtual: state.chapaAtual?.id === id ? chapa : state.chapaAtual,
            isSaving: false,
          }))
          return chapa
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao cancelar chapa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Membros Actions
      fetchMembros: async (chapaId: string) => {
        set({ isLoadingMembros: true, error: null })
        try {
          const membros = await chapasService.getMembros(chapaId)
          set({ membros, isLoadingMembros: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar membros'
          set({ error: message, isLoadingMembros: false })
          throw error
        }
      },

      addMembro: async (chapaId: string, data) => {
        set({ isSaving: true, error: null })
        try {
          const membro = await chapasService.addMembro(chapaId, data)
          set((state) => ({
            membros: [...state.membros, membro],
            isSaving: false,
          }))
          return membro
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao adicionar membro'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      removeMembro: async (chapaId: string, membroId: string) => {
        set({ isSaving: true, error: null })
        try {
          await chapasService.removeMembro(chapaId, membroId)
          set((state) => ({
            membros: state.membros.filter((m) => m.id !== membroId),
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao remover membro'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      updateMembroOrdem: async (chapaId: string, membroId: string, ordem: number) => {
        set({ isSaving: true, error: null })
        try {
          const membro = await chapasService.updateMembroOrdem(chapaId, membroId, ordem)
          set((state) => ({
            membros: state.membros.map((m) => (m.id === membroId ? membro : m)),
            isSaving: false,
          }))
          return membro
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atualizar ordem do membro'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Logo Actions
      uploadLogo: async (chapaId: string, arquivo: File) => {
        set({ isSaving: true, error: null })
        try {
          const result = await chapasService.uploadLogo(chapaId, arquivo)
          set((state) => ({
            chapas: state.chapas.map((c) =>
              c.id === chapaId ? { ...c, logoUrl: result.logoUrl } : c
            ),
            chapaAtual:
              state.chapaAtual?.id === chapaId
                ? { ...state.chapaAtual, logoUrl: result.logoUrl }
                : state.chapaAtual,
            isSaving: false,
          }))
          return result.logoUrl
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao fazer upload do logo'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      removeLogo: async (chapaId: string) => {
        set({ isSaving: true, error: null })
        try {
          await chapasService.removeLogo(chapaId)
          set((state) => ({
            chapas: state.chapas.map((c) =>
              c.id === chapaId ? { ...c, logoUrl: undefined } : c
            ),
            chapaAtual:
              state.chapaAtual?.id === chapaId
                ? { ...state.chapaAtual, logoUrl: undefined }
                : state.chapaAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao remover logo'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Estatisticas Actions
      fetchEstatisticas: async (eleicaoId: string) => {
        set({ isLoading: true, error: null })
        try {
          const estatisticas = await chapasService.getEstatisticas(eleicaoId)
          set({ estatisticas, isLoading: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar estatisticas'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      // State management Actions
      setChapaAtual: (chapa: Chapa | null) => set({ chapaAtual: chapa }),

      setFilters: (filters: Partial<ChapaListParams>) =>
        set((state) => ({
          filters: { ...state.filters, ...filters },
        })),

      clearFilters: () => set({ filters: {} }),

      setPage: (page: number) =>
        set((state) => ({
          filters: { ...state.filters, page },
        })),

      setPageSize: (pageSize: number) =>
        set((state) => ({
          filters: { ...state.filters, pageSize, page: 1 },
        })),

      clearError: () => set({ error: null }),

      reset: () => set(initialState),
    }),
    { name: 'chapa-store' }
  )
)

// Helper hooks
export const useChapas = () => {
  const chapas = useChapaStore((state) => state.chapas)
  const isLoading = useChapaStore((state) => state.isLoading)
  const error = useChapaStore((state) => state.error)
  const total = useChapaStore((state) => state.total)
  const page = useChapaStore((state) => state.page)
  const pageSize = useChapaStore((state) => state.pageSize)
  const totalPages = useChapaStore((state) => state.totalPages)

  return { chapas, isLoading, error, total, page, pageSize, totalPages }
}

export const useChapaAtual = () => {
  const chapaAtual = useChapaStore((state) => state.chapaAtual)
  const isLoading = useChapaStore((state) => state.isLoadingChapa)
  const error = useChapaStore((state) => state.error)

  return { chapa: chapaAtual, isLoading, error }
}

export const useChapaActions = () => {
  const fetchChapas = useChapaStore((state) => state.fetchChapas)
  const fetchChapaById = useChapaStore((state) => state.fetchChapaById)
  const createChapa = useChapaStore((state) => state.createChapa)
  const updateChapa = useChapaStore((state) => state.updateChapa)
  const deleteChapa = useChapaStore((state) => state.deleteChapa)
  const aprovarChapa = useChapaStore((state) => state.aprovarChapa)
  const reprovarChapa = useChapaStore((state) => state.reprovarChapa)
  const isSaving = useChapaStore((state) => state.isSaving)

  return {
    fetchChapas,
    fetchChapaById,
    createChapa,
    updateChapa,
    deleteChapa,
    aprovarChapa,
    reprovarChapa,
    isSaving,
  }
}
