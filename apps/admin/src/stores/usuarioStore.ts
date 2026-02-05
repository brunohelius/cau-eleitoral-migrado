import { create } from 'zustand'
import { devtools } from 'zustand/middleware'
import type {
  Usuario,
  Role,
  Permissao,
  LogAtividade,
  UsuarioListParams,
  UsuarioEstatisticas,
  CreateUsuarioRequest,
  UpdateUsuarioRequest,
  TipoUsuario,
  PaginatedResponse,
} from '@/types'
import { usuariosService } from '@/services/usuarios'

interface UsuarioState {
  // Data
  usuarios: Usuario[]
  usuarioAtual: Usuario | null
  roles: Role[]
  permissoes: Permissao[]
  atividades: LogAtividade[]
  estatisticas: UsuarioEstatisticas | null

  // Pagination
  total: number
  page: number
  pageSize: number
  totalPages: number

  // Atividades Pagination
  atividadesTotal: number
  atividadesPage: number
  atividadesPageSize: number
  atividadesTotalPages: number

  // Loading states
  isLoading: boolean
  isLoadingUsuario: boolean
  isLoadingRoles: boolean
  isLoadingPermissoes: boolean
  isLoadingAtividades: boolean
  isSaving: boolean

  // Error state
  error: string | null

  // Filters
  filters: UsuarioListParams

  // Actions - CRUD
  fetchUsuarios: (params?: UsuarioListParams) => Promise<void>
  fetchUsuarioById: (id: string) => Promise<void>
  fetchUsuarioByEmail: (email: string) => Promise<void>
  fetchUsuarioByCpf: (cpf: string) => Promise<void>
  fetchUsuarioByRegistroCAU: (registro: string) => Promise<void>
  createUsuario: (data: CreateUsuarioRequest) => Promise<Usuario>
  updateUsuario: (id: string, data: UpdateUsuarioRequest) => Promise<Usuario>
  deleteUsuario: (id: string) => Promise<void>

  // Actions - Status
  ativarUsuario: (id: string) => Promise<Usuario>
  inativarUsuario: (id: string, motivo?: string) => Promise<Usuario>
  bloquearUsuario: (id: string, motivo: string) => Promise<Usuario>
  desbloquearUsuario: (id: string) => Promise<Usuario>
  suspenderUsuario: (id: string, motivo: string, dataFim?: string) => Promise<Usuario>

  // Actions - Password
  alterarSenha: (id: string, senhaAtual: string, novaSenha: string, confirmacaoSenha: string) => Promise<void>
  resetarSenha: (id: string, novaSenha?: string, enviarEmail?: boolean) => Promise<string | undefined>

  // Actions - Roles & Permissions
  fetchRoles: () => Promise<void>
  fetchPermissoes: () => Promise<void>
  atribuirRoles: (id: string, roles: string[]) => Promise<Usuario>
  removerRole: (id: string, roleId: string) => Promise<Usuario>
  atribuirPermissoes: (id: string, permissoes: string[]) => Promise<Usuario>

  // Actions - Avatar
  uploadAvatar: (id: string, arquivo: File) => Promise<string>
  removeAvatar: (id: string) => Promise<void>

  // Actions - 2FA
  ativar2FA: (id: string) => Promise<{ qrCode: string; secret: string }>
  confirmar2FA: (id: string, codigo: string) => Promise<void>
  desativar2FA: (id: string, codigo: string) => Promise<void>

  // Actions - Activities
  fetchAtividades: (id: string, params?: {
    dataInicio?: string
    dataFim?: string
    page?: number
    pageSize?: number
  }) => Promise<void>

  // Actions - Email Verification
  enviarVerificacaoEmail: (id: string) => Promise<void>

  // Actions - Statistics
  fetchEstatisticas: () => Promise<void>

  // Actions - Export/Import
  exportar: (params?: UsuarioListParams & { formato?: 'xlsx' | 'csv' }) => Promise<Blob>
  importar: (arquivo: File, opcoes?: {
    atualizarExistentes?: boolean
    enviarEmailBoasVindas?: boolean
  }) => Promise<{ sucesso: number; erros: { linha: number; erro: string }[] }>

  // Actions - State management
  setUsuarioAtual: (usuario: Usuario | null) => void
  setFilters: (filters: Partial<UsuarioListParams>) => void
  clearFilters: () => void
  setPage: (page: number) => void
  setPageSize: (pageSize: number) => void
  clearError: () => void
  reset: () => void
}

const initialState = {
  usuarios: [],
  usuarioAtual: null,
  roles: [],
  permissoes: [],
  atividades: [],
  estatisticas: null,
  total: 0,
  page: 1,
  pageSize: 10,
  totalPages: 0,
  atividadesTotal: 0,
  atividadesPage: 1,
  atividadesPageSize: 10,
  atividadesTotalPages: 0,
  isLoading: false,
  isLoadingUsuario: false,
  isLoadingRoles: false,
  isLoadingPermissoes: false,
  isLoadingAtividades: false,
  isSaving: false,
  error: null,
  filters: {},
}

export const useUsuarioStore = create<UsuarioState>()(
  devtools(
    (set, get) => ({
      ...initialState,

      // CRUD Actions
      fetchUsuarios: async (params?: UsuarioListParams) => {
        set({ isLoading: true, error: null })
        try {
          const mergedParams = { ...get().filters, ...params }
          const response = await usuariosService.getAll(mergedParams)
          set({
            usuarios: response.data,
            total: response.total,
            page: response.page,
            pageSize: response.pageSize,
            totalPages: response.totalPages,
            isLoading: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar usuarios'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      fetchUsuarioById: async (id: string) => {
        set({ isLoadingUsuario: true, error: null })
        try {
          const usuario = await usuariosService.getById(id)
          set({ usuarioAtual: usuario, isLoadingUsuario: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar usuario'
          set({ error: message, isLoadingUsuario: false })
          throw error
        }
      },

      fetchUsuarioByEmail: async (email: string) => {
        set({ isLoadingUsuario: true, error: null })
        try {
          const usuario = await usuariosService.getByEmail(email)
          set({ usuarioAtual: usuario, isLoadingUsuario: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar usuario'
          set({ error: message, isLoadingUsuario: false })
          throw error
        }
      },

      fetchUsuarioByCpf: async (cpf: string) => {
        set({ isLoadingUsuario: true, error: null })
        try {
          const usuario = await usuariosService.getByCpf(cpf)
          set({ usuarioAtual: usuario, isLoadingUsuario: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar usuario'
          set({ error: message, isLoadingUsuario: false })
          throw error
        }
      },

      fetchUsuarioByRegistroCAU: async (registro: string) => {
        set({ isLoadingUsuario: true, error: null })
        try {
          const usuario = await usuariosService.getByRegistroCAU(registro)
          set({ usuarioAtual: usuario, isLoadingUsuario: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar usuario'
          set({ error: message, isLoadingUsuario: false })
          throw error
        }
      },

      createUsuario: async (data: CreateUsuarioRequest) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.create(data)
          set((state) => ({
            usuarios: [usuario, ...state.usuarios],
            total: state.total + 1,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao criar usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      updateUsuario: async (id: string, data: UpdateUsuarioRequest) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.update(id, data)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atualizar usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      deleteUsuario: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.delete(id)
          set((state) => ({
            usuarios: state.usuarios.filter((u) => u.id !== id),
            total: state.total - 1,
            usuarioAtual: state.usuarioAtual?.id === id ? null : state.usuarioAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao excluir usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Status Actions
      ativarUsuario: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.ativar(id)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao ativar usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      inativarUsuario: async (id: string, motivo?: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.inativar(id, motivo)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao inativar usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      bloquearUsuario: async (id: string, motivo: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.bloquear(id, motivo)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao bloquear usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      desbloquearUsuario: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.desbloquear(id)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao desbloquear usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      suspenderUsuario: async (id: string, motivo: string, dataFim?: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.suspender(id, motivo, dataFim)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao suspender usuario'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Password Actions
      alterarSenha: async (id: string, senhaAtual: string, novaSenha: string, confirmacaoSenha: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.alterarSenha(id, { senhaAtual, novaSenha, confirmacaoSenha })
          set({ isSaving: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao alterar senha'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      resetarSenha: async (id: string, novaSenha?: string, enviarEmail?: boolean) => {
        set({ isSaving: true, error: null })
        try {
          const result = await usuariosService.resetarSenha(id, { novaSenha, enviarEmail })
          set({ isSaving: false })
          return result.tempPassword
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao resetar senha'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Roles & Permissions Actions
      fetchRoles: async () => {
        set({ isLoadingRoles: true, error: null })
        try {
          const roles = await usuariosService.getRoles()
          set({ roles, isLoadingRoles: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar roles'
          set({ error: message, isLoadingRoles: false })
          throw error
        }
      },

      fetchPermissoes: async () => {
        set({ isLoadingPermissoes: true, error: null })
        try {
          const permissoes = await usuariosService.getPermissoes()
          set({ permissoes, isLoadingPermissoes: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar permissoes'
          set({ error: message, isLoadingPermissoes: false })
          throw error
        }
      },

      atribuirRoles: async (id: string, roles: string[]) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.atribuirRoles(id, roles)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atribuir roles'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      removerRole: async (id: string, roleId: string) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.removerRole(id, roleId)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao remover role'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      atribuirPermissoes: async (id: string, permissoes: string[]) => {
        set({ isSaving: true, error: null })
        try {
          const usuario = await usuariosService.atribuirPermissoes(id, permissoes)
          set((state) => ({
            usuarios: state.usuarios.map((u) => (u.id === id ? usuario : u)),
            usuarioAtual: state.usuarioAtual?.id === id ? usuario : state.usuarioAtual,
            isSaving: false,
          }))
          return usuario
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao atribuir permissoes'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Avatar Actions
      uploadAvatar: async (id: string, arquivo: File) => {
        set({ isSaving: true, error: null })
        try {
          const result = await usuariosService.uploadAvatar(id, arquivo)
          set((state) => ({
            usuarios: state.usuarios.map((u) =>
              u.id === id ? { ...u, avatarUrl: result.avatarUrl } : u
            ),
            usuarioAtual:
              state.usuarioAtual?.id === id
                ? { ...state.usuarioAtual, avatarUrl: result.avatarUrl }
                : state.usuarioAtual,
            isSaving: false,
          }))
          return result.avatarUrl
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao fazer upload do avatar'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      removeAvatar: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.removeAvatar(id)
          set((state) => ({
            usuarios: state.usuarios.map((u) =>
              u.id === id ? { ...u, avatarUrl: undefined } : u
            ),
            usuarioAtual:
              state.usuarioAtual?.id === id
                ? { ...state.usuarioAtual, avatarUrl: undefined }
                : state.usuarioAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao remover avatar'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // 2FA Actions
      ativar2FA: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          const result = await usuariosService.ativar2FA(id)
          set({ isSaving: false })
          return result
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao ativar 2FA'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      confirmar2FA: async (id: string, codigo: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.confirmar2FA(id, codigo)
          set((state) => ({
            usuarioAtual:
              state.usuarioAtual?.id === id
                ? { ...state.usuarioAtual, doisFatoresAtivo: true }
                : state.usuarioAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao confirmar 2FA'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      desativar2FA: async (id: string, codigo: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.desativar2FA(id, codigo)
          set((state) => ({
            usuarioAtual:
              state.usuarioAtual?.id === id
                ? { ...state.usuarioAtual, doisFatoresAtivo: false }
                : state.usuarioAtual,
            isSaving: false,
          }))
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao desativar 2FA'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Activities Actions
      fetchAtividades: async (id: string, params?) => {
        set({ isLoadingAtividades: true, error: null })
        try {
          const response = await usuariosService.getAtividades(id, params)
          set({
            atividades: response.data,
            atividadesTotal: response.total,
            atividadesPage: response.page,
            atividadesPageSize: response.pageSize,
            atividadesTotalPages: response.totalPages,
            isLoadingAtividades: false,
          })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar atividades'
          set({ error: message, isLoadingAtividades: false })
          throw error
        }
      },

      // Email Verification Actions
      enviarVerificacaoEmail: async (id: string) => {
        set({ isSaving: true, error: null })
        try {
          await usuariosService.enviarVerificacaoEmail(id)
          set({ isSaving: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao enviar verificacao de email'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // Statistics Actions
      fetchEstatisticas: async () => {
        set({ isLoading: true, error: null })
        try {
          const estatisticas = await usuariosService.getEstatisticas()
          set({ estatisticas, isLoading: false })
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao carregar estatisticas'
          set({ error: message, isLoading: false })
          throw error
        }
      },

      // Export/Import Actions
      exportar: async (params) => {
        try {
          return await usuariosService.exportar(params)
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao exportar usuarios'
          set({ error: message })
          throw error
        }
      },

      importar: async (arquivo, opcoes) => {
        set({ isSaving: true, error: null })
        try {
          const result = await usuariosService.importar(arquivo, opcoes)
          set({ isSaving: false })
          // Reload users after import
          await get().fetchUsuarios()
          return result
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Erro ao importar usuarios'
          set({ error: message, isSaving: false })
          throw error
        }
      },

      // State management Actions
      setUsuarioAtual: (usuario: Usuario | null) => set({ usuarioAtual: usuario }),

      setFilters: (filters: Partial<UsuarioListParams>) =>
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
    { name: 'usuario-store' }
  )
)

// Helper hooks
export const useUsuarios = () => {
  const usuarios = useUsuarioStore((state) => state.usuarios)
  const isLoading = useUsuarioStore((state) => state.isLoading)
  const error = useUsuarioStore((state) => state.error)
  const total = useUsuarioStore((state) => state.total)
  const page = useUsuarioStore((state) => state.page)
  const pageSize = useUsuarioStore((state) => state.pageSize)
  const totalPages = useUsuarioStore((state) => state.totalPages)

  return { usuarios, isLoading, error, total, page, pageSize, totalPages }
}

export const useUsuarioAtual = () => {
  const usuarioAtual = useUsuarioStore((state) => state.usuarioAtual)
  const isLoading = useUsuarioStore((state) => state.isLoadingUsuario)
  const error = useUsuarioStore((state) => state.error)

  return { usuario: usuarioAtual, isLoading, error }
}

export const useUsuarioActions = () => {
  const fetchUsuarios = useUsuarioStore((state) => state.fetchUsuarios)
  const fetchUsuarioById = useUsuarioStore((state) => state.fetchUsuarioById)
  const createUsuario = useUsuarioStore((state) => state.createUsuario)
  const updateUsuario = useUsuarioStore((state) => state.updateUsuario)
  const deleteUsuario = useUsuarioStore((state) => state.deleteUsuario)
  const ativarUsuario = useUsuarioStore((state) => state.ativarUsuario)
  const inativarUsuario = useUsuarioStore((state) => state.inativarUsuario)
  const bloquearUsuario = useUsuarioStore((state) => state.bloquearUsuario)
  const desbloquearUsuario = useUsuarioStore((state) => state.desbloquearUsuario)
  const isSaving = useUsuarioStore((state) => state.isSaving)

  return {
    fetchUsuarios,
    fetchUsuarioById,
    createUsuario,
    updateUsuario,
    deleteUsuario,
    ativarUsuario,
    inativarUsuario,
    bloquearUsuario,
    desbloquearUsuario,
    isSaving,
  }
}

export const useRolesPermissoes = () => {
  const roles = useUsuarioStore((state) => state.roles)
  const permissoes = useUsuarioStore((state) => state.permissoes)
  const fetchRoles = useUsuarioStore((state) => state.fetchRoles)
  const fetchPermissoes = useUsuarioStore((state) => state.fetchPermissoes)
  const isLoadingRoles = useUsuarioStore((state) => state.isLoadingRoles)
  const isLoadingPermissoes = useUsuarioStore((state) => state.isLoadingPermissoes)

  return {
    roles,
    permissoes,
    fetchRoles,
    fetchPermissoes,
    isLoadingRoles,
    isLoadingPermissoes,
  }
}

export const useUsuarioEstatisticas = () => {
  const estatisticas = useUsuarioStore((state) => state.estatisticas)
  const fetchEstatisticas = useUsuarioStore((state) => state.fetchEstatisticas)
  const isLoading = useUsuarioStore((state) => state.isLoading)

  return { estatisticas, fetchEstatisticas, isLoading }
}
