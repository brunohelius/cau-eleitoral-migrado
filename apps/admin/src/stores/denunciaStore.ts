import { create } from 'zustand'
import { devtools } from 'zustand/middleware'
import {
  denunciasService,
  type Denuncia,
  type AnexoDenuncia,
  type DenunciaListParams,
  type CreateDenunciaRequest,
  type UpdateDenunciaRequest,
  StatusDenuncia,
} from '@/services/denuncias'

interface DenunciaEstatisticas {
  total: number
  recebidas: number
  emAnalise: number
  aguardandoJulgamento: number
  procedentes: number
  improcedentes: number
  arquivadas: number
  porTipo: Record<string, number>
  porPrioridade: Record<string, number>
}

interface DenunciaState {
  // Data
  denuncias: Denuncia[]
  denunciaAtual: Denuncia | null
  anexos: AnexoDenuncia[]
  estatisticas: DenunciaEstatisticas | null

  // Pagination
  total: number
  page: number
  pageSize: number
  totalPages: number

  // Loading states
  isLoading: boolean
  isLoadingDenuncia: boolean
  isLoadingAnexos: boolean
  isSaving: boolean

  // Error state
  error: string | null

  // Filters
  filters: DenunciaListParams

  // Actions - CRUD
  fetchDenuncias: (params?: DenunciaListParams) => Promise<void>
  fetchDenunciaById: (id: string) => Promise<void>
  fetchDenunciaByProtocolo: (protocolo: string) => Promise<void>
  fetchDenunciasByEleicao: (eleicaoId: string, params?: Omit<DenunciaListParams, 'eleicaoId'>) => Promise<void>
  fetchDenunciasByChapa: (chapaId: string) => Promise<void>
  createDenuncia: (data: CreateDenunciaRequest) => Promise<Denuncia>
  updateDenuncia: (id: string, data: UpdateDenunciaRequest) => Promise<Denuncia>
  deleteDenuncia: (id: string) => Promise<void>

  // Actions - Status & Analysis
  iniciarAnalise: (id: string) => Promise<Denuncia>
  emitirParecer: (id: string, parecer: string, decisao: StatusDenuncia) => Promise<Denuncia>
  arquivar: (id: string, motivo: string) => Promise<Denuncia>
  reabrir: (id: string, motivo: string) => Promise<Denuncia>
  atribuirAnalista: (id: string, analistaId: string) => Promise<Denuncia>

  // Actions - Anexos
  fetchAnexos: (denunciaId: string) => Promise<void>
  uploadAnexo: (denunciaId: string, arquivo: File, nome?: string) => Promise<AnexoDenuncia>
  removeAnexo: (denunciaId: string, anexoId: string) => Promise<void>
  downloadAnexo: (denunciaId: string, anexoId: string) => Promise<Blob>

  // Actions - Estatisticas & Reports
  fetchEstatisticas: (eleicaoId?: string) => Promise<void>
  gerarRelatorio: (params: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
    formato?: 'pdf' | 'xlsx'
  }) => Promise<Blob>

  // Actions - State management
  setDenunciaAtual: (denuncia: Denuncia | null) => void
  setFilters: (filters: Partial<DenunciaListParams>) => void
  clearFilters: () => void
  setPage: (page: number) => void
  setPageSize: (pageSize: number) => void
  clearError: () => void
  reset: () => void
}

const initialState = {
  denuncias: [],
  denunciaAtual: null,
  anexos: [],
  estatisticas: null,
  total: 0,
  page: 1,
  pageSize: 10,
  totalPages: 0,
  isLoading: false,
  isLoadingDenuncia: false,
  isLoadingAnexos: false,
  isSaving: false,
  error: null,
  filters: {},
}

export const useDenunciaStore = create<DenunciaState>()(
  devtools(
    (set, get) => ({
      ...initialState,

      // CRUD Actions
      fetchDenuncias: async (params?: DenunciaListParams) => {
        set({ isLoading: true, error: null })
        try {
          const mergedParams = { ...get().filters, ...params }
          const response = await denunciasService.getAll(mergedParams)
          set({
            denuncias: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar denuncias'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchDenunciaById: async (id: string) => {
        set({ isLoadingDenuncia: true, error: null })
        try {
          const denuncia = await denunciasService.getById(id)
          set({ denunciaAtual: denuncia, isLoadingDenuncia: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar denuncia'
          set({ error: message, isLoadingDenuncia: false })
          throw error
        }
      },

      fetchDenunciaByProtocolo: async (protocolo: string) => {
        set({ isLoadingDenuncia: true, error: null })
        try {
          const denuncia = await denunciasService.getByProtocolo(protocolo)
          set({ denunciaAtual: denuncia, isLoadingDenuncia: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar denuncia'
          set({ error: message, isLoadingDenuncia: false })
          throw error
        }
      },

      fetchDenunciasByEleicao: async (eleicaoId: string, params?) => {
        set({ isLoading: true, error: null })
        try {
          const response = await denunciasService.getByEleicao(eleicaoId, params)
          set({
            denuncias: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar denuncias da eleicao'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchDenunciasByChapa: async (chapaId: string) => {
        set({ isLoading: true, error: null })
        try {
          const denuncias = await denunciasService.getByChapa(chapaId)
          set({
            denuncias,
            total: denuncias.length,
            totalPages: 1,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar denuncias da chapa'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      createDenuncia: async (data: CreateDenunciaRequest) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.create(data)
          set((state) => ({
            denuncias: [denuncia, ...state.denuncias],
            total: state.total + 1,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao criar denuncia'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      updateDenuncia: async (id: string, data: UpdateDenunciaRequest) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.update(id, data)
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atualizar denuncia'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      deleteDenuncia: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await denunciasService.delete(id)
          set((state) => ({
            denuncias: state.denuncias.filter((d) => d.id !== id),
            total: state.total - 1,
            denunciaAtual: state.denunciaAtual?.id === id ? null : state.denunciaAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao excluir denuncia'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Status & Analysis Actions
      iniciarAnalise: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.iniciarAnalise(id)
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao iniciar analise'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      emitirParecer: async (id: string, parecer: string, decisao: StatusDenuncia) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.emitirParecer(id, { parecer, decisao })
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao emitir parecer'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      arquivar: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.arquivar(id, motivo)
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao arquivar denuncia'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      reabrir: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.reabrir(id, motivo)
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao reabrir denuncia'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      atribuirAnalista: async (id: string, analistaId: string) => {
        set({ isSaving: true, error: null })
        try {
          const denuncia = await denunciasService.atribuirAnalista(id, analistaId)
          set((state) => ({
            denuncias: state.denuncias.map((d) => (d.id === id ? denuncia : d)),
            denunciaAtual: state.denunciaAtual?.id === id ? denuncia : state.denunciaAtual,
            isSaving: false,
          }))
          return denuncia
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atribuir analista'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Anexos Actions
      fetchAnexos: async (denunciaId: string) => {
        set({ isLoadingAnexos: true, error: null })
        try {
          const anexos = await denunciasService.getAnexos(denunciaId)
          set({ anexos, isLoadingAnexos: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar anexos'
          set({ error: message, isLoadingAnexos: false })
          throw error
        }
      },

      uploadAnexo: async (denunciaId: string, arquivo: File, nome?: string) => {
        set({ isSaving: true, error: null })
        try {
          const anexo = await denunciasService.uploadAnexo(denunciaId, arquivo, nome)
          set((state) => ({
            anexos: [...state.anexos, anexo],
            isSaving: false,
          }))
          return anexo
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao fazer upload do anexo'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      removeAnexo: async (denunciaId: string, anexoId: string) => {
        set({ isSaving: true, error: null })
        try {
          await denunciasService.removeAnexo(denunciaId, anexoId)
          set((state) => ({
            anexos: state.anexos.filter((a) => a.id !== anexoId),
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao remover anexo'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      downloadAnexo: async (denunciaId: string, anexoId: string) => {
        try {
          return await denunciasService.downloadAnexo(denunciaId, anexoId)
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao baixar anexo'
          set({ error: message })
          throw error
        }
      },

      // Estatisticas & Reports Actions
      fetchEstatisticas: async (eleicaoId?: string) => {
        set({ isLoading: true, error: null })
        try {
          const estatisticas = await denunciasService.getEstatisticas(eleicaoId)
          set({ estatisticas, isLoading: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar estatisticas'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      gerarRelatorio: async (params) => {
        try {
          return await denunciasService.gerarRelatorio(params)
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao gerar relatorio'
          set({ error: message })
          throw error
        }
      },

      // State management Actions
      setDenunciaAtual: (denuncia: Denuncia | null) => set({ denunciaAtual: denuncia }),

      setFilters: (filters: Partial<DenunciaListParams>) =>
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
    { name: 'denuncia-store' }
  )
)

// Helper hooks
export const useDenuncias = () => {
  const denuncias = useDenunciaStore((state) => state.denuncias)
  const isLoading = useDenunciaStore((state) => state.isLoading)
  const error = useDenunciaStore((state) => state.error)
  const total = useDenunciaStore((state) => state.total)
  const page = useDenunciaStore((state) => state.page)
  const pageSize = useDenunciaStore((state) => state.pageSize)
  const totalPages = useDenunciaStore((state) => state.totalPages)

  return { denuncias, isLoading, error, total, page, pageSize, totalPages }
}

export const useDenunciaAtual = () => {
  const denunciaAtual = useDenunciaStore((state) => state.denunciaAtual)
  const isLoading = useDenunciaStore((state) => state.isLoadingDenuncia)
  const error = useDenunciaStore((state) => state.error)

  return { denuncia: denunciaAtual, isLoading, error }
}

export const useDenunciaActions = () => {
  const fetchDenuncias = useDenunciaStore((state) => state.fetchDenuncias)
  const fetchDenunciaById = useDenunciaStore((state) => state.fetchDenunciaById)
  const createDenuncia = useDenunciaStore((state) => state.createDenuncia)
  const updateDenuncia = useDenunciaStore((state) => state.updateDenuncia)
  const deleteDenuncia = useDenunciaStore((state) => state.deleteDenuncia)
  const iniciarAnalise = useDenunciaStore((state) => state.iniciarAnalise)
  const emitirParecer = useDenunciaStore((state) => state.emitirParecer)
  const arquivar = useDenunciaStore((state) => state.arquivar)
  const isSaving = useDenunciaStore((state) => state.isSaving)

  return {
    fetchDenuncias,
    fetchDenunciaById,
    createDenuncia,
    updateDenuncia,
    deleteDenuncia,
    iniciarAnalise,
    emitirParecer,
    arquivar,
    isSaving,
  }
}

export const useDenunciaEstatisticas = () => {
  const estatisticas = useDenunciaStore((state) => state.estatisticas)
  const fetchEstatisticas = useDenunciaStore((state) => state.fetchEstatisticas)
  const isLoading = useDenunciaStore((state) => state.isLoading)

  return { estatisticas, fetchEstatisticas, isLoading }
}
