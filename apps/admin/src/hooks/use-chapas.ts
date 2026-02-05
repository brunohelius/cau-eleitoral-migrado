import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  chapasService,
  type Chapa,
  type CreateChapaRequest,
  type UpdateChapaRequest,
  type AddMembroRequest,
  type ChapaListParams,
  type StatusChapa,
} from '@/services/chapas'
import { useNotify } from '@/stores/notifications'
import { useCurrentEleicao } from '@/stores/eleicao'

// Query keys
export const chapaKeys = {
  all: ['chapas'] as const,
  lists: () => [...chapaKeys.all, 'list'] as const,
  list: (params: ChapaListParams) => [...chapaKeys.lists(), params] as const,
  details: () => [...chapaKeys.all, 'detail'] as const,
  detail: (id: string) => [...chapaKeys.details(), id] as const,
  byEleicao: (eleicaoId: string) => [...chapaKeys.all, 'eleicao', eleicaoId] as const,
  membros: (chapaId: string) => [...chapaKeys.all, 'membros', chapaId] as const,
  documentos: (chapaId: string) => [...chapaKeys.all, 'documentos', chapaId] as const,
  estatisticas: (eleicaoId: string) => [...chapaKeys.all, 'estatisticas', eleicaoId] as const,
}

// Get all chapas with filters
export function useChapas(params?: ChapaListParams) {
  return useQuery({
    queryKey: chapaKeys.list(params || {}),
    queryFn: () => chapasService.getAll(params),
    staleTime: 1000 * 60 * 2, // 2 minutes
  })
}

// Get chapa by ID
export function useChapa(id: string | undefined) {
  return useQuery({
    queryKey: chapaKeys.detail(id!),
    queryFn: () => chapasService.getById(id!),
    enabled: !!id,
  })
}

// Get chapas by eleicao
export function useChapasByEleicao(eleicaoId: string | undefined) {
  return useQuery({
    queryKey: chapaKeys.byEleicao(eleicaoId!),
    queryFn: () => chapasService.getByEleicao(eleicaoId!),
    enabled: !!eleicaoId,
    staleTime: 1000 * 60 * 2,
  })
}

// Get chapas for current selected eleicao
export function useChapasDaEleicaoAtual() {
  const { eleicaoId } = useCurrentEleicao()
  return useChapasByEleicao(eleicaoId)
}

// Get chapa membros
export function useMembrosDaChapa(chapaId: string | undefined) {
  return useQuery({
    queryKey: chapaKeys.membros(chapaId!),
    queryFn: () => chapasService.getMembros(chapaId!),
    enabled: !!chapaId,
  })
}

// Get chapa documentos
export function useDocumentosDaChapa(chapaId: string | undefined) {
  return useQuery({
    queryKey: chapaKeys.documentos(chapaId!),
    queryFn: () => chapasService.getDocumentos(chapaId!),
    enabled: !!chapaId,
  })
}

// Get chapas statistics
export function useEstatisticasChapas(eleicaoId: string | undefined) {
  return useQuery({
    queryKey: chapaKeys.estatisticas(eleicaoId!),
    queryFn: () => chapasService.getEstatisticas(eleicaoId!),
    enabled: !!eleicaoId,
    staleTime: 1000 * 60, // 1 minute
  })
}

// Create chapa mutation
export function useCreateChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (data: CreateChapaRequest) => chapasService.create(data),
    onSuccess: (newChapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      success('Chapa criada', `A chapa "${newChapa.nome}" foi criada com sucesso.`)
    },
    onError: (err: Error) => {
      error('Erro ao criar chapa', err.message)
    },
  })
}

// Update chapa mutation
export function useUpdateChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: UpdateChapaRequest }) =>
      chapasService.update(id, data),
    onSuccess: (updatedChapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.setQueryData(chapaKeys.detail(updatedChapa.id), updatedChapa)
      success('Chapa atualizada', `A chapa "${updatedChapa.nome}" foi atualizada.`)
    },
    onError: (err: Error) => {
      error('Erro ao atualizar chapa', err.message)
    },
  })
}

// Delete chapa mutation
export function useDeleteChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => chapasService.delete(id),
    onSuccess: (_, deletedId) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.removeQueries({ queryKey: chapaKeys.detail(deletedId) })
      success('Chapa excluida', 'A chapa foi excluida com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao excluir chapa', err.message)
    },
  })
}

// Approve chapa mutation
export function useAprovarChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => chapasService.aprovar(id),
    onSuccess: (chapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.setQueryData(chapaKeys.detail(chapa.id), chapa)
      success('Chapa aprovada', `A chapa "${chapa.nome}" foi aprovada.`)
    },
    onError: (err: Error) => {
      error('Erro ao aprovar chapa', err.message)
    },
  })
}

// Reject chapa mutation
export function useReprovarChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      chapasService.reprovar(id, motivo),
    onSuccess: (chapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.setQueryData(chapaKeys.detail(chapa.id), chapa)
      success('Chapa reprovada', `A chapa "${chapa.nome}" foi reprovada.`)
    },
    onError: (err: Error) => {
      error('Erro ao reprovar chapa', err.message)
    },
  })
}

// Suspend chapa mutation
export function useSuspenderChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      chapasService.suspender(id, motivo),
    onSuccess: (chapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.setQueryData(chapaKeys.detail(chapa.id), chapa)
      success('Chapa suspensa', `A chapa "${chapa.nome}" foi suspensa.`)
    },
    onError: (err: Error) => {
      error('Erro ao suspender chapa', err.message)
    },
  })
}

// Reactivate chapa mutation
export function useReativarChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => chapasService.reativar(id),
    onSuccess: (chapa) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.all })
      queryClient.setQueryData(chapaKeys.detail(chapa.id), chapa)
      success('Chapa reativada', `A chapa "${chapa.nome}" foi reativada.`)
    },
    onError: (err: Error) => {
      error('Erro ao reativar chapa', err.message)
    },
  })
}

// Add member to chapa mutation
export function useAddMembroChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ chapaId, data }: { chapaId: string; data: AddMembroRequest }) =>
      chapasService.addMembro(chapaId, data),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.membros(variables.chapaId) })
      queryClient.invalidateQueries({ queryKey: chapaKeys.detail(variables.chapaId) })
      success('Membro adicionado', 'O membro foi adicionado a chapa com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao adicionar membro', err.message)
    },
  })
}

// Remove member from chapa mutation
export function useRemoveMembroChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ chapaId, membroId }: { chapaId: string; membroId: string }) =>
      chapasService.removeMembro(chapaId, membroId),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.membros(variables.chapaId) })
      queryClient.invalidateQueries({ queryKey: chapaKeys.detail(variables.chapaId) })
      success('Membro removido', 'O membro foi removido da chapa.')
    },
    onError: (err: Error) => {
      error('Erro ao remover membro', err.message)
    },
  })
}

// Upload logo mutation
export function useUploadLogoChapa() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ chapaId, arquivo }: { chapaId: string; arquivo: File }) =>
      chapasService.uploadLogo(chapaId, arquivo),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: chapaKeys.detail(variables.chapaId) })
      success('Logo atualizada', 'A logo da chapa foi atualizada com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao fazer upload da logo', err.message)
    },
  })
}

// Prefetch chapa
export function usePrefetchChapa() {
  const queryClient = useQueryClient()

  return (id: string) => {
    queryClient.prefetchQuery({
      queryKey: chapaKeys.detail(id),
      queryFn: () => chapasService.getById(id),
      staleTime: 1000 * 60 * 5,
    })
  }
}

// Status label helper
export function useChapaStatusLabel() {
  const statusLabels: Record<StatusChapa, string> = {
    0: 'Pendente',
    1: 'Em Analise',
    2: 'Aprovada',
    3: 'Reprovada',
    4: 'Impugnada',
    5: 'Suspensa',
    6: 'Cancelada',
  }

  const statusColors: Record<StatusChapa, string> = {
    0: 'yellow',
    1: 'blue',
    2: 'green',
    3: 'red',
    4: 'orange',
    5: 'gray',
    6: 'red',
  }

  return {
    getLabel: (status: StatusChapa) => statusLabels[status] || 'Desconhecido',
    getColor: (status: StatusChapa) => statusColors[status] || 'gray',
    statusLabels,
    statusColors,
  }
}
