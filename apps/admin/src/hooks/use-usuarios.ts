import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  usuariosService,
  type Usuario,
  type CreateUsuarioRequest,
  type UpdateUsuarioRequest,
  type UsuarioListParams,
  type StatusUsuario,
  type TipoUsuario,
} from '@/services/usuarios'
import { useNotify } from '@/stores/notifications'

// Query keys
export const usuarioKeys = {
  all: ['usuarios'] as const,
  lists: () => [...usuarioKeys.all, 'list'] as const,
  list: (params: UsuarioListParams) => [...usuarioKeys.lists(), params] as const,
  details: () => [...usuarioKeys.all, 'detail'] as const,
  detail: (id: string) => [...usuarioKeys.details(), id] as const,
  byEmail: (email: string) => [...usuarioKeys.all, 'email', email] as const,
  byCpf: (cpf: string) => [...usuarioKeys.all, 'cpf', cpf] as const,
  byRegistroCAU: (registro: string) => [...usuarioKeys.all, 'registro-cau', registro] as const,
  roles: () => [...usuarioKeys.all, 'roles'] as const,
  permissoes: () => [...usuarioKeys.all, 'permissoes'] as const,
  atividades: (id: string) => [...usuarioKeys.all, 'atividades', id] as const,
  estatisticas: () => [...usuarioKeys.all, 'estatisticas'] as const,
}

// Get all usuarios with filters
export function useUsuarios(params?: UsuarioListParams) {
  return useQuery({
    queryKey: usuarioKeys.list(params || {}),
    queryFn: () => usuariosService.getAll(params),
    staleTime: 1000 * 60 * 2,
  })
}

// Get usuario by ID
export function useUsuario(id: string | undefined) {
  return useQuery({
    queryKey: usuarioKeys.detail(id!),
    queryFn: () => usuariosService.getById(id!),
    enabled: !!id,
  })
}

// Get usuario by email
export function useUsuarioByEmail(email: string | undefined) {
  return useQuery({
    queryKey: usuarioKeys.byEmail(email!),
    queryFn: () => usuariosService.getByEmail(email!),
    enabled: !!email,
  })
}

// Get usuario by CPF
export function useUsuarioByCpf(cpf: string | undefined) {
  return useQuery({
    queryKey: usuarioKeys.byCpf(cpf!),
    queryFn: () => usuariosService.getByCpf(cpf!),
    enabled: !!cpf,
  })
}

// Get usuario by Registro CAU
export function useUsuarioByRegistroCAU(registro: string | undefined) {
  return useQuery({
    queryKey: usuarioKeys.byRegistroCAU(registro!),
    queryFn: () => usuariosService.getByRegistroCAU(registro!),
    enabled: !!registro,
  })
}

// Get roles
export function useRoles() {
  return useQuery({
    queryKey: usuarioKeys.roles(),
    queryFn: () => usuariosService.getRoles(),
    staleTime: 1000 * 60 * 10, // 10 minutes - roles don't change often
  })
}

// Get permissoes
export function usePermissoes() {
  return useQuery({
    queryKey: usuarioKeys.permissoes(),
    queryFn: () => usuariosService.getPermissoes(),
    staleTime: 1000 * 60 * 10,
  })
}

// Get usuario atividades
export function useUsuarioAtividades(id: string | undefined, params?: {
  dataInicio?: string
  dataFim?: string
  page?: number
  pageSize?: number
}) {
  return useQuery({
    queryKey: [...usuarioKeys.atividades(id!), params],
    queryFn: () => usuariosService.getAtividades(id!, params),
    enabled: !!id,
  })
}

// Get statistics
export function useEstatisticasUsuarios() {
  return useQuery({
    queryKey: usuarioKeys.estatisticas(),
    queryFn: () => usuariosService.getEstatisticas(),
    staleTime: 1000 * 60, // 1 minute
  })
}

// Create usuario mutation
export function useCreateUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (data: CreateUsuarioRequest) => usuariosService.create(data),
    onSuccess: (newUsuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      success('Usuario criado', `O usuario "${newUsuario.nome}" foi criado com sucesso.`)
    },
    onError: (err: Error) => {
      error('Erro ao criar usuario', err.message)
    },
  })
}

// Update usuario mutation
export function useUpdateUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: UpdateUsuarioRequest }) =>
      usuariosService.update(id, data),
    onSuccess: (updatedUsuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.setQueryData(usuarioKeys.detail(updatedUsuario.id), updatedUsuario)
      success('Usuario atualizado', `Os dados de "${updatedUsuario.nome}" foram atualizados.`)
    },
    onError: (err: Error) => {
      error('Erro ao atualizar usuario', err.message)
    },
  })
}

// Delete usuario mutation
export function useDeleteUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => usuariosService.delete(id),
    onSuccess: (_, deletedId) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.removeQueries({ queryKey: usuarioKeys.detail(deletedId) })
      success('Usuario excluido', 'O usuario foi excluido com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao excluir usuario', err.message)
    },
  })
}

// Activate usuario mutation
export function useAtivarUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => usuariosService.ativar(id),
    onSuccess: (usuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.setQueryData(usuarioKeys.detail(usuario.id), usuario)
      success('Usuario ativado', `O usuario "${usuario.nome}" foi ativado.`)
    },
    onError: (err: Error) => {
      error('Erro ao ativar usuario', err.message)
    },
  })
}

// Deactivate usuario mutation
export function useInativarUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo?: string }) =>
      usuariosService.inativar(id, motivo),
    onSuccess: (usuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.setQueryData(usuarioKeys.detail(usuario.id), usuario)
      success('Usuario inativado', `O usuario "${usuario.nome}" foi inativado.`)
    },
    onError: (err: Error) => {
      error('Erro ao inativar usuario', err.message)
    },
  })
}

// Block usuario mutation
export function useBloquearUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      usuariosService.bloquear(id, motivo),
    onSuccess: (usuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.setQueryData(usuarioKeys.detail(usuario.id), usuario)
      success('Usuario bloqueado', `O usuario "${usuario.nome}" foi bloqueado.`)
    },
    onError: (err: Error) => {
      error('Erro ao bloquear usuario', err.message)
    },
  })
}

// Unblock usuario mutation
export function useDesbloquearUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => usuariosService.desbloquear(id),
    onSuccess: (usuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })
      queryClient.setQueryData(usuarioKeys.detail(usuario.id), usuario)
      success('Usuario desbloqueado', `O usuario "${usuario.nome}" foi desbloqueado.`)
    },
    onError: (err: Error) => {
      error('Erro ao desbloquear usuario', err.message)
    },
  })
}

// Reset password mutation
export function useResetarSenhaUsuario() {
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data?: { novaSenha?: string; enviarEmail?: boolean } }) =>
      usuariosService.resetarSenha(id, data),
    onSuccess: (result) => {
      if (result.tempPassword) {
        success('Senha resetada', `Senha temporaria: ${result.tempPassword}`)
      } else {
        success('Senha resetada', 'Um email com a nova senha foi enviado ao usuario.')
      }
    },
    onError: (err: Error) => {
      error('Erro ao resetar senha', err.message)
    },
  })
}

// Assign roles mutation
export function useAtribuirRolesUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, roles }: { id: string; roles: string[] }) =>
      usuariosService.atribuirRoles(id, roles),
    onSuccess: (usuario) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.detail(usuario.id) })
      success('Roles atualizadas', 'As roles do usuario foram atualizadas.')
    },
    onError: (err: Error) => {
      error('Erro ao atribuir roles', err.message)
    },
  })
}

// Upload avatar mutation
export function useUploadAvatarUsuario() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, arquivo }: { id: string; arquivo: File }) =>
      usuariosService.uploadAvatar(id, arquivo),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.detail(variables.id) })
      success('Avatar atualizado', 'O avatar foi atualizado com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao atualizar avatar', err.message)
    },
  })
}

// Enable 2FA mutation
export function useAtivar2FAUsuario() {
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => usuariosService.ativar2FA(id),
    onSuccess: () => {
      success('2FA iniciado', 'Escaneie o QR Code com seu aplicativo autenticador.')
    },
    onError: (err: Error) => {
      error('Erro ao ativar 2FA', err.message)
    },
  })
}

// Export usuarios mutation
export function useExportarUsuarios() {
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (params?: UsuarioListParams & { formato?: 'xlsx' | 'csv' }) =>
      usuariosService.exportar(params),
    onSuccess: (blob, variables) => {
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `usuarios.${variables?.formato || 'xlsx'}`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)

      success('Exportacao concluida', 'O download foi iniciado.')
    },
    onError: (err: Error) => {
      error('Erro ao exportar', err.message)
    },
  })
}

// Import usuarios mutation
export function useImportarUsuarios() {
  const queryClient = useQueryClient()
  const { success, error, warning } = useNotify()

  return useMutation({
    mutationFn: ({ arquivo, opcoes }: {
      arquivo: File
      opcoes?: { atualizarExistentes?: boolean; enviarEmailBoasVindas?: boolean }
    }) => usuariosService.importar(arquivo, opcoes),
    onSuccess: (result) => {
      queryClient.invalidateQueries({ queryKey: usuarioKeys.all })

      if (result.erros.length > 0) {
        warning(
          'Importacao parcial',
          `${result.sucesso} usuarios importados, ${result.erros.length} erros.`
        )
      } else {
        success('Importacao concluida', `${result.sucesso} usuarios importados com sucesso.`)
      }
    },
    onError: (err: Error) => {
      error('Erro ao importar', err.message)
    },
  })
}

// Status/Type label helpers
export function useUsuarioLabels() {
  const statusLabels: Record<StatusUsuario, string> = {
    0: 'Ativo',
    1: 'Inativo',
    2: 'Pendente',
    3: 'Bloqueado',
    4: 'Suspenso',
  }

  const statusColors: Record<StatusUsuario, string> = {
    0: 'green',
    1: 'gray',
    2: 'yellow',
    3: 'red',
    4: 'orange',
  }

  const tipoLabels: Record<TipoUsuario, string> = {
    0: 'Administrador',
    1: 'Comissao',
    2: 'Fiscal',
    3: 'Analista',
    4: 'Auditor',
    5: 'Operador',
  }

  const tipoColors: Record<TipoUsuario, string> = {
    0: 'purple',
    1: 'blue',
    2: 'cyan',
    3: 'green',
    4: 'orange',
    5: 'gray',
  }

  return {
    getStatusLabel: (status: StatusUsuario) => statusLabels[status] || 'Desconhecido',
    getStatusColor: (status: StatusUsuario) => statusColors[status] || 'gray',
    getTipoLabel: (tipo: TipoUsuario) => tipoLabels[tipo] || 'Desconhecido',
    getTipoColor: (tipo: TipoUsuario) => tipoColors[tipo] || 'gray',
    statusLabels,
    statusColors,
    tipoLabels,
    tipoColors,
  }
}

// Prefetch usuario
export function usePrefetchUsuario() {
  const queryClient = useQueryClient()

  return (id: string) => {
    queryClient.prefetchQuery({
      queryKey: usuarioKeys.detail(id),
      queryFn: () => usuariosService.getById(id),
      staleTime: 1000 * 60 * 5,
    })
  }
}
