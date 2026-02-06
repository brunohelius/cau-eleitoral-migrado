import { create } from 'zustand'
import { devtools, persist } from 'zustand/middleware'
import type {
  ConfiguracaoSistema,
  ConfiguracoesEleicao,
  ConfiguracoesNotificacao,
  ConfiguracoesSeguranca,
  ConfiguracoesIntegracao,
  ConfiguracoesAparencia,
  LogConfiguracao,
  BackupConfiguracao,
  TipoConfiguracao,
} from '@/types'
import { configuracoesService } from '@/services/configuracoes'

interface ConfiguracaoState {
  // Data
  configuracoes: ConfiguracaoSistema[]
  configEleicao: ConfiguracoesEleicao | null
  configNotificacao: ConfiguracoesNotificacao | null
  configSeguranca: ConfiguracoesSeguranca | null
  configIntegracao: ConfiguracoesIntegracao | null
  configAparencia: ConfiguracoesAparencia | null
  logs: LogConfiguracao[]
  backups: BackupConfiguracao[]

  // Pagination for logs
  logsTotal: number
  logsPage: number
  logsPageSize: number

  // Loading states
  isLoading: boolean
  isLoadingEleicao: boolean
  isLoadingNotificacao: boolean
  isLoadingSeguranca: boolean
  isLoadingIntegracao: boolean
  isLoadingAparencia: boolean
  isLoadingLogs: boolean
  isLoadingBackups: boolean
  isSaving: boolean

  // Error state
  error: string | null

  // Actions - Sistema
  fetchConfiguracoes: (tipo?: TipoConfiguracao) => Promise<void>
  getConfiguracaoByChave: (chave: string) => Promise<ConfiguracaoSistema>
  updateConfiguracao: (chave: string, valor: string) => Promise<ConfiguracaoSistema>
  updateMultiplasConfiguracoes: (configuracoes: { chave: string; valor: string }[]) => Promise<ConfiguracaoSistema[]>

  // Actions - Eleicao
  fetchConfiguracoesEleicao: () => Promise<void>
  updateConfiguracoesEleicao: (data: Partial<ConfiguracoesEleicao>) => Promise<ConfiguracoesEleicao>

  // Actions - Notificacao
  fetchConfiguracoesNotificacao: () => Promise<void>
  updateConfiguracoesNotificacao: (data: Partial<ConfiguracoesNotificacao>) => Promise<ConfiguracoesNotificacao>
  testarEmail: (destinatario: string) => Promise<{ sucesso: boolean; erro?: string }>

  // Actions - Seguranca
  fetchConfiguracoesSeguranca: () => Promise<void>
  updateConfiguracoesSeguranca: (data: Partial<ConfiguracoesSeguranca>) => Promise<ConfiguracoesSeguranca>

  // Actions - Integracao
  fetchConfiguracoesIntegracao: () => Promise<void>
  updateConfiguracoesIntegracao: (data: Partial<ConfiguracoesIntegracao>) => Promise<ConfiguracoesIntegracao>
  testarIntegracaoSIAU: () => Promise<{ sucesso: boolean; mensagem: string }>
  testarWebhook: (url: string) => Promise<{ sucesso: boolean; statusCode?: number; erro?: string }>

  // Actions - Aparencia
  fetchConfiguracoesAparencia: () => Promise<void>
  updateConfiguracoesAparencia: (data: Partial<ConfiguracoesAparencia>) => Promise<ConfiguracoesAparencia>
  uploadLogo: (arquivo: File) => Promise<string>
  uploadFavicon: (arquivo: File) => Promise<string>

  // Actions - Logs
  fetchLogs: (params?: {
    chave?: string
    usuarioId?: string
    dataInicio?: string
    dataFim?: string
    page?: number
    pageSize?: number
  }) => Promise<void>

  // Actions - Backup/Restore
  fetchBackups: () => Promise<void>
  criarBackup: (nome: string, descricao?: string) => Promise<BackupConfiguracao>
  restaurarBackup: (backupId: string) => Promise<void>
  deletarBackup: (backupId: string) => Promise<void>

  // Actions - Export/Import
  exportarConfiguracoes: () => Promise<Blob>
  importarConfiguracoes: (arquivo: File) => Promise<{ sucesso: number; erros: string[] }>

  // Actions - Reset
  resetarPadrao: (tipo?: TipoConfiguracao) => Promise<void>

  // Actions - State management
  clearError: () => void
  reset: () => void
}

const initialState = {
  configuracoes: [],
  configEleicao: null,
  configNotificacao: null,
  configSeguranca: null,
  configIntegracao: null,
  configAparencia: null,
  logs: [],
  backups: [],
  logsTotal: 0,
  logsPage: 1,
  logsPageSize: 20,
  isLoading: false,
  isLoadingEleicao: false,
  isLoadingNotificacao: false,
  isLoadingSeguranca: false,
  isLoadingIntegracao: false,
  isLoadingAparencia: false,
  isLoadingLogs: false,
  isLoadingBackups: false,
  isSaving: false,
  error: null,
}

export const useConfiguracaoStore = create<ConfiguracaoState>()(
  devtools(
    persist(
      (set, get) => ({
        ...initialState,

        // Sistema Actions
        fetchConfiguracoes: async (tipo?: TipoConfiguracao) => {
          set({ isLoading: true, error: null })
          try {
            const configuracoes = await configuracoesService.getAll(tipo)
            set({ configuracoes, isLoading: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes'
            set({ error: message, isLoading: false })
            throw error
          }
        },

        getConfiguracaoByChave: async (chave: string) => {
          try {
            return await configuracoesService.getByChave(chave)
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracao'
            set({ error: message })
            throw error
          }
        },

        updateConfiguracao: async (chave: string, valor: string) => {
          set({ isSaving: true, error: null })
          try {
            const config = await configuracoesService.update(chave, valor)
            set((state) => ({
              configuracoes: state.configuracoes.map((c) =>
                c.chave === chave ? config : c
              ),
              isSaving: false,
            }))
            return config
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracao'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        updateMultiplasConfiguracoes: async (configuracoes: { chave: string; valor: string }[]) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.updateMultiplas(configuracoes)
            set({ isSaving: false })
            return result
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Eleicao Actions
        fetchConfiguracoesEleicao: async () => {
          set({ isLoadingEleicao: true, error: null })
          try {
            const configEleicao = await configuracoesService.getConfiguracoesEleicao()
            set({ configEleicao, isLoadingEleicao: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes de eleicao'
            set({ error: message, isLoadingEleicao: false })
            throw error
          }
        },

        updateConfiguracoesEleicao: async (data: Partial<ConfiguracoesEleicao>) => {
          set({ isSaving: true, error: null })
          try {
            const configEleicao = await configuracoesService.updateConfiguracoesEleicao(data)
            set({ configEleicao, isSaving: false })
            return configEleicao
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes de eleicao'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Notificacao Actions
        fetchConfiguracoesNotificacao: async () => {
          set({ isLoadingNotificacao: true, error: null })
          try {
            const configNotificacao = await configuracoesService.getConfiguracoesNotificacao()
            set({ configNotificacao, isLoadingNotificacao: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes de notificacao'
            set({ error: message, isLoadingNotificacao: false })
            throw error
          }
        },

        updateConfiguracoesNotificacao: async (data: Partial<ConfiguracoesNotificacao>) => {
          set({ isSaving: true, error: null })
          try {
            const configNotificacao = await configuracoesService.updateConfiguracoesNotificacao(data)
            set({ configNotificacao, isSaving: false })
            return configNotificacao
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes de notificacao'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        testarEmail: async (destinatario: string) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.testarEmail(destinatario)
            set({ isSaving: false })
            return result
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao testar email'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Seguranca Actions
        fetchConfiguracoesSeguranca: async () => {
          set({ isLoadingSeguranca: true, error: null })
          try {
            const configSeguranca = await configuracoesService.getConfiguracoesSeguranca()
            set({ configSeguranca, isLoadingSeguranca: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes de seguranca'
            set({ error: message, isLoadingSeguranca: false })
            throw error
          }
        },

        updateConfiguracoesSeguranca: async (data: Partial<ConfiguracoesSeguranca>) => {
          set({ isSaving: true, error: null })
          try {
            const configSeguranca = await configuracoesService.updateConfiguracoesSeguranca(data)
            set({ configSeguranca, isSaving: false })
            return configSeguranca
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes de seguranca'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Integracao Actions
        fetchConfiguracoesIntegracao: async () => {
          set({ isLoadingIntegracao: true, error: null })
          try {
            const configIntegracao = await configuracoesService.getConfiguracoesIntegracao()
            set({ configIntegracao, isLoadingIntegracao: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes de integracao'
            set({ error: message, isLoadingIntegracao: false })
            throw error
          }
        },

        updateConfiguracoesIntegracao: async (data: Partial<ConfiguracoesIntegracao>) => {
          set({ isSaving: true, error: null })
          try {
            const configIntegracao = await configuracoesService.updateConfiguracoesIntegracao(data)
            set({ configIntegracao, isSaving: false })
            return configIntegracao
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes de integracao'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        testarIntegracaoSIAU: async () => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.testarIntegracaoSIAU()
            set({ isSaving: false })
            return result
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao testar integracao SIAU'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        testarWebhook: async (url: string) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.testarWebhook(url)
            set({ isSaving: false })
            return result
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao testar webhook'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Aparencia Actions
        fetchConfiguracoesAparencia: async () => {
          set({ isLoadingAparencia: true, error: null })
          try {
            const configAparencia = await configuracoesService.getConfiguracoesAparencia()
            set({ configAparencia, isLoadingAparencia: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar configuracoes de aparencia'
            set({ error: message, isLoadingAparencia: false })
            throw error
          }
        },

        updateConfiguracoesAparencia: async (data: Partial<ConfiguracoesAparencia>) => {
          set({ isSaving: true, error: null })
          try {
            const configAparencia = await configuracoesService.updateConfiguracoesAparencia(data)
            set({ configAparencia, isSaving: false })
            return configAparencia
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao atualizar configuracoes de aparencia'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        uploadLogo: async (arquivo: File) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.uploadLogo(arquivo)
            set((state) => ({
              configAparencia: state.configAparencia
                ? { ...state.configAparencia, logoUrl: result.logoUrl }
                : null,
              isSaving: false,
            }))
            return result.logoUrl
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao fazer upload do logo'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        uploadFavicon: async (arquivo: File) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.uploadFavicon(arquivo)
            set((state) => ({
              configAparencia: state.configAparencia
                ? { ...state.configAparencia, faviconUrl: result.faviconUrl }
                : null,
              isSaving: false,
            }))
            return result.faviconUrl
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao fazer upload do favicon'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Logs Actions
        fetchLogs: async (params?) => {
          set({ isLoadingLogs: true, error: null })
          try {
            const response = await configuracoesService.getLogs(params)
            set({
              logs: response.data,
              logsTotal: response.total,
              isLoadingLogs: false,
            })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar logs'
            set({ error: message, isLoadingLogs: false })
            throw error
          }
        },

        // Backup/Restore Actions
        fetchBackups: async () => {
          set({ isLoadingBackups: true, error: null })
          try {
            const backups = await configuracoesService.getBackups()
            set({ backups, isLoadingBackups: false })
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao carregar backups'
            set({ error: message, isLoadingBackups: false })
            throw error
          }
        },

        criarBackup: async (nome: string, descricao?: string) => {
          set({ isSaving: true, error: null })
          try {
            const backup = await configuracoesService.criarBackup(nome, descricao)
            set((state) => ({
              backups: [backup, ...state.backups],
              isSaving: false,
            }))
            return backup
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao criar backup'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        restaurarBackup: async (backupId: string) => {
          set({ isSaving: true, error: null })
          try {
            await configuracoesService.restaurarBackup(backupId)
            set({ isSaving: false })
            // Reload all configurations after restore
            await Promise.all([
              get().fetchConfiguracoes(),
              get().fetchConfiguracoesEleicao(),
              get().fetchConfiguracoesNotificacao(),
              get().fetchConfiguracoesSeguranca(),
              get().fetchConfiguracoesIntegracao(),
              get().fetchConfiguracoesAparencia(),
            ])
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao restaurar backup'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        deletarBackup: async (backupId: string) => {
          set({ isSaving: true, error: null })
          try {
            await configuracoesService.deletarBackup(backupId)
            set((state) => ({
              backups: state.backups.filter((b) => b.id !== backupId),
              isSaving: false,
            }))
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao deletar backup'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Export/Import Actions
        exportarConfiguracoes: async () => {
          try {
            return await configuracoesService.exportarConfiguracoes()
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao exportar configuracoes'
            set({ error: message })
            throw error
          }
        },

        importarConfiguracoes: async (arquivo: File) => {
          set({ isSaving: true, error: null })
          try {
            const result = await configuracoesService.importarConfiguracoes(arquivo)
            set({ isSaving: false })
            // Reload all configurations after import
            await Promise.all([
              get().fetchConfiguracoes(),
              get().fetchConfiguracoesEleicao(),
              get().fetchConfiguracoesNotificacao(),
              get().fetchConfiguracoesSeguranca(),
              get().fetchConfiguracoesIntegracao(),
              get().fetchConfiguracoesAparencia(),
            ])
            return result
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao importar configuracoes'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // Reset Actions
        resetarPadrao: async (tipo?: TipoConfiguracao) => {
          set({ isSaving: true, error: null })
          try {
            await configuracoesService.resetarPadrao(tipo)
            set({ isSaving: false })
            // Reload configurations based on type or all
            if (tipo === undefined) {
              await Promise.all([
                get().fetchConfiguracoes(),
                get().fetchConfiguracoesEleicao(),
                get().fetchConfiguracoesNotificacao(),
                get().fetchConfiguracoesSeguranca(),
                get().fetchConfiguracoesIntegracao(),
                get().fetchConfiguracoesAparencia(),
              ])
            } else {
              await get().fetchConfiguracoes(tipo)
            }
          } catch (error) {
            const message = error instanceof Error ? error.message : 'Erro ao resetar configuracoes'
            set({ error: message, isSaving: false })
            throw error
          }
        },

        // State management Actions
        clearError: () => set({ error: null }),

        reset: () => set(initialState),
      }),
      {
        name: 'configuracao-storage',
        partialize: (state) => ({
          configAparencia: state.configAparencia,
        }),
      }
    ),
    { name: 'configuracao-store' }
  )
)

// Helper hooks
export const useConfiguracoes = () => {
  const configuracoes = useConfiguracaoStore((state) => state.configuracoes)
  const isLoading = useConfiguracaoStore((state) => state.isLoading)
  const error = useConfiguracaoStore((state) => state.error)
  const fetchConfiguracoes = useConfiguracaoStore((state) => state.fetchConfiguracoes)

  return { configuracoes, isLoading, error, fetchConfiguracoes }
}

export const useConfigEleicao = () => {
  const configEleicao = useConfiguracaoStore((state) => state.configEleicao)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingEleicao)
  const fetchConfiguracoesEleicao = useConfiguracaoStore((state) => state.fetchConfiguracoesEleicao)
  const updateConfiguracoesEleicao = useConfiguracaoStore((state) => state.updateConfiguracoesEleicao)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { configEleicao, isLoading, fetchConfiguracoesEleicao, updateConfiguracoesEleicao, isSaving }
}

export const useConfigNotificacao = () => {
  const configNotificacao = useConfiguracaoStore((state) => state.configNotificacao)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingNotificacao)
  const fetchConfiguracoesNotificacao = useConfiguracaoStore((state) => state.fetchConfiguracoesNotificacao)
  const updateConfiguracoesNotificacao = useConfiguracaoStore((state) => state.updateConfiguracoesNotificacao)
  const testarEmail = useConfiguracaoStore((state) => state.testarEmail)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { configNotificacao, isLoading, fetchConfiguracoesNotificacao, updateConfiguracoesNotificacao, testarEmail, isSaving }
}

export const useConfigSeguranca = () => {
  const configSeguranca = useConfiguracaoStore((state) => state.configSeguranca)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingSeguranca)
  const fetchConfiguracoesSeguranca = useConfiguracaoStore((state) => state.fetchConfiguracoesSeguranca)
  const updateConfiguracoesSeguranca = useConfiguracaoStore((state) => state.updateConfiguracoesSeguranca)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { configSeguranca, isLoading, fetchConfiguracoesSeguranca, updateConfiguracoesSeguranca, isSaving }
}

export const useConfigIntegracao = () => {
  const configIntegracao = useConfiguracaoStore((state) => state.configIntegracao)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingIntegracao)
  const fetchConfiguracoesIntegracao = useConfiguracaoStore((state) => state.fetchConfiguracoesIntegracao)
  const updateConfiguracoesIntegracao = useConfiguracaoStore((state) => state.updateConfiguracoesIntegracao)
  const testarIntegracaoSIAU = useConfiguracaoStore((state) => state.testarIntegracaoSIAU)
  const testarWebhook = useConfiguracaoStore((state) => state.testarWebhook)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { configIntegracao, isLoading, fetchConfiguracoesIntegracao, updateConfiguracoesIntegracao, testarIntegracaoSIAU, testarWebhook, isSaving }
}

export const useConfigAparencia = () => {
  const configAparencia = useConfiguracaoStore((state) => state.configAparencia)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingAparencia)
  const fetchConfiguracoesAparencia = useConfiguracaoStore((state) => state.fetchConfiguracoesAparencia)
  const updateConfiguracoesAparencia = useConfiguracaoStore((state) => state.updateConfiguracoesAparencia)
  const uploadLogo = useConfiguracaoStore((state) => state.uploadLogo)
  const uploadFavicon = useConfiguracaoStore((state) => state.uploadFavicon)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { configAparencia, isLoading, fetchConfiguracoesAparencia, updateConfiguracoesAparencia, uploadLogo, uploadFavicon, isSaving }
}

export const useConfigBackups = () => {
  const backups = useConfiguracaoStore((state) => state.backups)
  const isLoading = useConfiguracaoStore((state) => state.isLoadingBackups)
  const fetchBackups = useConfiguracaoStore((state) => state.fetchBackups)
  const criarBackup = useConfiguracaoStore((state) => state.criarBackup)
  const restaurarBackup = useConfiguracaoStore((state) => state.restaurarBackup)
  const deletarBackup = useConfiguracaoStore((state) => state.deletarBackup)
  const isSaving = useConfiguracaoStore((state) => state.isSaving)

  return { backups, isLoading, fetchBackups, criarBackup, restaurarBackup, deletarBackup, isSaving }
}
