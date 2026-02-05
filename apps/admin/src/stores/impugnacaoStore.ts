import { create } from 'zustand'
import { devtools } from 'zustand/middleware'
import type {
  Impugnacao,
  AnexoImpugnacao,
  ImpugnacaoListParams,
  ImpugnacaoEstatisticas,
  CreateImpugnacaoRequest,
  UpdateImpugnacaoRequest,
  StatusImpugnacao,
} from '@/types'
import { impugnacoesService } from '@/services/impugnacoes'

interface TimelineEvent {
  data: string
  evento: string
  descricao: string
  usuarioNome?: string
}

interface ImpugnacaoState {
  // Data
  impugnacoes: Impugnacao[]
  impugnacaoAtual: Impugnacao | null
  anexos: AnexoImpugnacao[]
  timeline: TimelineEvent[]
  estatisticas: ImpugnacaoEstatisticas | null

  // Pagination
  total: number
  page: number
  pageSize: number
  totalPages: number

  // Loading states
  isLoading: boolean
  isLoadingImpugnacao: boolean
  isLoadingAnexos: boolean
  isLoadingTimeline: boolean
  isSaving: boolean

  // Error state
  error: string | null

  // Filters
  filters: ImpugnacaoListParams

  // Actions - CRUD
  fetchImpugnacoes: (params?: ImpugnacaoListParams) => Promise<void>
  fetchImpugnacaoById: (id: string) => Promise<void>
  fetchImpugnacaoByProtocolo: (protocolo: string) => Promise<void>
  fetchImpugnacoesByEleicao: (eleicaoId: string, params?: Omit<ImpugnacaoListParams, 'eleicaoId'>) => Promise<void>
  fetchImpugnacoesByChapa: (chapaId: string) => Promise<void>
  fetchImpugnacoesByCandidato: (candidatoId: string) => Promise<void>
  createImpugnacao: (data: CreateImpugnacaoRequest) => Promise<Impugnacao>
  updateImpugnacao: (id: string, data: UpdateImpugnacaoRequest) => Promise<Impugnacao>
  deleteImpugnacao: (id: string) => Promise<void>

  // Actions - Phase Operations
  iniciarAnalise: (id: string) => Promise<Impugnacao>
  solicitarDefesa: (id: string, prazo: string) => Promise<Impugnacao>
  apresentarDefesa: (id: string, texto: string) => Promise<Impugnacao>
  emitirParecer: (id: string, parecer: string, recomendacao: StatusImpugnacao) => Promise<Impugnacao>
  encaminharJulgamento: (id: string) => Promise<Impugnacao>
  proferirDecisao: (id: string, decisao: StatusImpugnacao, fundamentacao: string) => Promise<Impugnacao>
  interporRecurso: (id: string, fundamentacao: string) => Promise<Impugnacao>
  julgarRecurso: (id: string, recursoId: string, decisao: StatusImpugnacao, fundamentacao: string) => Promise<Impugnacao>
  arquivar: (id: string, motivo: string) => Promise<Impugnacao>
  atribuirRelator: (id: string, relatorId: string) => Promise<Impugnacao>

  // Actions - Anexos
  fetchAnexos: (impugnacaoId: string) => Promise<void>
  uploadAnexo: (impugnacaoId: string, arquivo: File, nome?: string) => Promise<AnexoImpugnacao>
  removeAnexo: (impugnacaoId: string, anexoId: string) => Promise<void>
  uploadAnexoDefesa: (impugnacaoId: string, defesaId: string, arquivo: File) => Promise<AnexoImpugnacao>
  uploadAnexoRecurso: (impugnacaoId: string, recursoId: string, arquivo: File) => Promise<AnexoImpugnacao>

  // Actions - Timeline & Stats
  fetchTimeline: (impugnacaoId: string) => Promise<void>
  fetchEstatisticas: (eleicaoId?: string) => Promise<void>
  gerarRelatorio: (params: {
    eleicaoId?: string
    dataInicio?: string
    dataFim?: string
    formato?: 'pdf' | 'xlsx'
  }) => Promise<Blob>

  // Actions - State management
  setImpugnacaoAtual: (impugnacao: Impugnacao | null) => void
  setFilters: (filters: Partial<ImpugnacaoListParams>) => void
  clearFilters: () => void
  setPage: (page: number) => void
  setPageSize: (pageSize: number) => void
  clearError: () => void
  reset: () => void
}

const initialState = {
  impugnacoes: [],
  impugnacaoAtual: null,
  anexos: [],
  timeline: [],
  estatisticas: null,
  total: 0,
  page: 1,
  pageSize: 10,
  totalPages: 0,
  isLoading: false,
  isLoadingImpugnacao: false,
  isLoadingAnexos: false,
  isLoadingTimeline: false,
  isSaving: false,
  error: null,
  filters: {},
}

export const useImpugnacaoStore = create<ImpugnacaoState>()(
  devtools(
    (set, get) => ({
      ...initialState,

      // CRUD Actions
      fetchImpugnacoes: async (params?: ImpugnacaoListParams) => {
        set({ isLoading: true, error: null })
        try {
          const mergedParams = { ...get().filters, ...params }
          const response = await impugnacoesService.getAll(mergedParams)
          set({
            impugnacoes: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacoes'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchImpugnacaoById: async (id: string) => {
        set({ isLoadingImpugnacao: true, error: null })
        try {
          const impugnacao = await impugnacoesService.getById(id)
          set({ impugnacaoAtual: impugnacao, isLoadingImpugnacao: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacao'
          set({ error: message, isLoadingImpugnacao: false })
          throw error
        }
      },

      fetchImpugnacaoByProtocolo: async (protocolo: string) => {
        set({ isLoadingImpugnacao: true, error: null })
        try {
          const impugnacao = await impugnacoesService.getByProtocolo(protocolo)
          set({ impugnacaoAtual: impugnacao, isLoadingImpugnacao: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacao'
          set({ error: message, isLoadingImpugnacao: false })
          throw error
        }
      },

      fetchImpugnacoesByEleicao: async (eleicaoId: string, params?) => {
        set({ isLoading: true, error: null })
        try {
          const response = await impugnacoesService.getByEleicao(eleicaoId, params)
          set({
            impugnacoes: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacoes da eleicao'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchImpugnacoesByChapa: async (chapaId: string) => {
        set({ isLoading: true, error: null })
        try {
          const impugnacoes = await impugnacoesService.getByChapa(chapaId)
          set({
            impugnacoes,
            total: impugnacoes.length,
            totalPages: 1,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacoes da chapa'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchImpugnacoesByCandidato: async (candidatoId: string) => {
        set({ isLoading: true, error: null })
        try {
          const impugnacoes = await impugnacoesService.getByCandidato(candidatoId)
          set({
            impugnacoes,
            total: impugnacoes.length,
            totalPages: 1,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar impugnacoes do candidato'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      createImpugnacao: async (data: CreateImpugnacaoRequest) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.create(data)
          set((state) => ({
            impugnacoes: [impugnacao, ...state.impugnacoes],
            total: state.total + 1,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao criar impugnacao'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      updateImpugnacao: async (id: string, data: UpdateImpugnacaoRequest) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.update(id, data)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atualizar impugnacao'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      deleteImpugnacao: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await impugnacoesService.delete(id)
          set((state) => ({
            impugnacoes: state.impugnacoes.filter((i) => i.id !== id),
            total: state.total - 1,
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? null : state.impugnacaoAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao excluir impugnacao'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Phase Operations Actions
      iniciarAnalise: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.iniciarAnalise(id)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao iniciar analise'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      solicitarDefesa: async (id: string, prazo: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.solicitarDefesa(id, prazo)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao solicitar defesa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      apresentarDefesa: async (id: string, texto: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.apresentarDefesa(id, { texto })
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao apresentar defesa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      emitirParecer: async (id: string, parecer: string, recomendacao: StatusImpugnacao) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.emitirParecer(id, { parecer, recomendacao })
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao emitir parecer'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      encaminharJulgamento: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.encaminharJulgamento(id)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao encaminhar para julgamento'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      proferirDecisao: async (id: string, decisao: StatusImpugnacao, fundamentacao: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.proferirDecisao(id, { decisao, fundamentacao })
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao proferir decisao'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      interporRecurso: async (id: string, fundamentacao: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.interporRecurso(id, { fundamentacao })
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao interpor recurso'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      julgarRecurso: async (id: string, recursoId: string, decisao: StatusImpugnacao, fundamentacao: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.julgarRecurso(id, recursoId, { decisao, fundamentacao })
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao julgar recurso'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      arquivar: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.arquivar(id, motivo)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao arquivar impugnacao'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      atribuirRelator: async (id: string, relatorId: string) => {
        set({ isSaving: true, error: null })
        try {
          const impugnacao = await impugnacoesService.atribuirRelator(id, relatorId)
          set((state) => ({
            impugnacoes: state.impugnacoes.map((i) => (i.id === id ? impugnacao : i)),
            impugnacaoAtual: state.impugnacaoAtual?.id === id ? impugnacao : state.impugnacaoAtual,
            isSaving: false,
          }))
          return impugnacao
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atribuir relator'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Anexos Actions
      fetchAnexos: async (impugnacaoId: string) => {
        set({ isLoadingAnexos: true, error: null })
        try {
          const anexos = await impugnacoesService.getAnexos(impugnacaoId)
          set({ anexos, isLoadingAnexos: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar anexos'
          set({ error: message, isLoadingAnexos: false })
          throw error
        }
      },

      uploadAnexo: async (impugnacaoId: string, arquivo: File, nome?: string) => {
        set({ isSaving: true, error: null })
        try {
          const anexo = await impugnacoesService.uploadAnexo(impugnacaoId, arquivo, nome)
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

      removeAnexo: async (impugnacaoId: string, anexoId: string) => {
        set({ isSaving: true, error: null })
        try {
          await impugnacoesService.removeAnexo(impugnacaoId, anexoId)
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

      uploadAnexoDefesa: async (impugnacaoId: string, defesaId: string, arquivo: File) => {
        set({ isSaving: true, error: null })
        try {
          const anexo = await impugnacoesService.uploadAnexoDefesa(impugnacaoId, defesaId, arquivo)
          set({ isSaving: false })
          return anexo
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao fazer upload do anexo da defesa'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      uploadAnexoRecurso: async (impugnacaoId: string, recursoId: string, arquivo: File) => {
        set({ isSaving: true, error: null })
        try {
          const anexo = await impugnacoesService.uploadAnexoRecurso(impugnacaoId, recursoId, arquivo)
          set({ isSaving: false })
          return anexo
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao fazer upload do anexo do recurso'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Timeline & Stats Actions
      fetchTimeline: async (impugnacaoId: string) => {
        set({ isLoadingTimeline: true, error: null })
        try {
          const timeline = await impugnacoesService.getTimeline(impugnacaoId)
          set({ timeline, isLoadingTimeline: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar timeline'
          set({ error: message, isLoadingTimeline: false })
          throw error
        }
      },

      fetchEstatisticas: async (eleicaoId?: string) => {
        set({ isLoading: true, error: null })
        try {
          const estatisticas = await impugnacoesService.getEstatisticas(eleicaoId)
          set({ estatisticas, isLoading: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar estatisticas'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      gerarRelatorio: async (params) => {
        try {
          return await impugnacoesService.gerarRelatorio(params)
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao gerar relatorio'
          set({ error: message })
          throw error
        }
      },

      // State management Actions
      setImpugnacaoAtual: (impugnacao: Impugnacao | null) => set({ impugnacaoAtual: impugnacao }),

      setFilters: (filters: Partial<ImpugnacaoListParams>) =>
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
    { name: 'impugnacao-store' }
  )
)

// Helper hooks
export const useImpugnacoes = () => {
  const impugnacoes = useImpugnacaoStore((state) => state.impugnacoes)
  const isLoading = useImpugnacaoStore((state) => state.isLoading)
  const error = useImpugnacaoStore((state) => state.error)
  const total = useImpugnacaoStore((state) => state.total)
  const page = useImpugnacaoStore((state) => state.page)
  const pageSize = useImpugnacaoStore((state) => state.pageSize)
  const totalPages = useImpugnacaoStore((state) => state.totalPages)

  return { impugnacoes, isLoading, error, total, page, pageSize, totalPages }
}

export const useImpugnacaoAtual = () => {
  const impugnacaoAtual = useImpugnacaoStore((state) => state.impugnacaoAtual)
  const isLoading = useImpugnacaoStore((state) => state.isLoadingImpugnacao)
  const error = useImpugnacaoStore((state) => state.error)
  const timeline = useImpugnacaoStore((state) => state.timeline)
  const isLoadingTimeline = useImpugnacaoStore((state) => state.isLoadingTimeline)

  return { impugnacao: impugnacaoAtual, isLoading, error, timeline, isLoadingTimeline }
}

export const useImpugnacaoActions = () => {
  const fetchImpugnacoes = useImpugnacaoStore((state) => state.fetchImpugnacoes)
  const fetchImpugnacaoById = useImpugnacaoStore((state) => state.fetchImpugnacaoById)
  const createImpugnacao = useImpugnacaoStore((state) => state.createImpugnacao)
  const updateImpugnacao = useImpugnacaoStore((state) => state.updateImpugnacao)
  const deleteImpugnacao = useImpugnacaoStore((state) => state.deleteImpugnacao)
  const iniciarAnalise = useImpugnacaoStore((state) => state.iniciarAnalise)
  const emitirParecer = useImpugnacaoStore((state) => state.emitirParecer)
  const proferirDecisao = useImpugnacaoStore((state) => state.proferirDecisao)
  const arquivar = useImpugnacaoStore((state) => state.arquivar)
  const isSaving = useImpugnacaoStore((state) => state.isSaving)

  return {
    fetchImpugnacoes,
    fetchImpugnacaoById,
    createImpugnacao,
    updateImpugnacao,
    deleteImpugnacao,
    iniciarAnalise,
    emitirParecer,
    proferirDecisao,
    arquivar,
    isSaving,
  }
}

export const useImpugnacaoEstatisticas = () => {
  const estatisticas = useImpugnacaoStore((state) => state.estatisticas)
  const fetchEstatisticas = useImpugnacaoStore((state) => state.fetchEstatisticas)
  const isLoading = useImpugnacaoStore((state) => state.isLoading)

  return { estatisticas, fetchEstatisticas, isLoading }
}
