import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  eleicoesService,
  type Eleicao,
  type CreateEleicaoRequest,
  type UpdateEleicaoRequest,
} from '@/services/eleicoes'
import { useEleicaoStore } from '@/stores/eleicao'
import { useNotify } from '@/stores/notifications'

// Query keys
export const eleicaoKeys = {
  all: ['eleicoes'] as const,
  lists: () => [...eleicaoKeys.all, 'list'] as const,
  list: (filters: Record<string, unknown>) => [...eleicaoKeys.lists(), filters] as const,
  details: () => [...eleicaoKeys.all, 'detail'] as const,
  detail: (id: string) => [...eleicaoKeys.details(), id] as const,
  ativas: () => [...eleicaoKeys.all, 'ativas'] as const,
  byStatus: (status: number) => [...eleicaoKeys.all, 'status', status] as const,
}

// Get all elections
export function useEleicoes() {
  return useQuery({
    queryKey: eleicaoKeys.lists(),
    queryFn: () => eleicoesService.getAll(),
    staleTime: 1000 * 60 * 5, // 5 minutes
  })
}

// Get election by ID
export function useEleicao(id: string | undefined) {
  return useQuery({
    queryKey: eleicaoKeys.detail(id!),
    queryFn: () => eleicoesService.getById(id!),
    enabled: !!id,
  })
}

// Get active elections
export function useEleicoesAtivas() {
  const setEleicoesAtivas = useEleicaoStore((state) => state.setEleicoesAtivas)

  return useQuery({
    queryKey: eleicaoKeys.ativas(),
    queryFn: async () => {
      const eleicoes = await eleicoesService.getAtivas()
      setEleicoesAtivas(eleicoes)
      return eleicoes
    },
    staleTime: 1000 * 60 * 2, // 2 minutes
  })
}

// Get elections by status
export function useEleicoesByStatus(status: number) {
  return useQuery({
    queryKey: eleicaoKeys.byStatus(status),
    queryFn: () => eleicoesService.getByStatus(status),
    enabled: status !== undefined,
  })
}

// Create election mutation
export function useCreateEleicao() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (data: CreateEleicaoRequest) => eleicoesService.create(data),
    onSuccess: (newEleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      success('Eleicao criada', `A eleicao "${newEleicao.nome}" foi criada com sucesso.`)
    },
    onError: (err: Error) => {
      error('Erro ao criar eleicao', err.message)
    },
  })
}

// Update election mutation
export function useUpdateEleicao() {
  const queryClient = useQueryClient()
  const updateEleicaoAtual = useEleicaoStore((state) => state.updateEleicaoAtual)
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: UpdateEleicaoRequest }) =>
      eleicoesService.update(id, data),
    onSuccess: (updatedEleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.setQueryData(eleicaoKeys.detail(updatedEleicao.id), updatedEleicao)
      updateEleicaoAtual(updatedEleicao)
      success('Eleicao atualizada', `A eleicao "${updatedEleicao.nome}" foi atualizada.`)
    },
    onError: (err: Error) => {
      error('Erro ao atualizar eleicao', err.message)
    },
  })
}

// Delete election mutation
export function useDeleteEleicao() {
  const queryClient = useQueryClient()
  const { eleicaoAtual, clearEleicaoAtual } = useEleicaoStore()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => eleicoesService.delete(id),
    onSuccess: (_, deletedId) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.removeQueries({ queryKey: eleicaoKeys.detail(deletedId) })

      if (eleicaoAtual?.id === deletedId) {
        clearEleicaoAtual()
      }

      success('Eleicao excluida', 'A eleicao foi excluida com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao excluir eleicao', err.message)
    },
  })
}

// Start election mutation
export function useIniciarEleicao() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => eleicoesService.iniciar(id),
    onSuccess: (eleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.setQueryData(eleicaoKeys.detail(eleicao.id), eleicao)
      success('Eleicao iniciada', `A eleicao "${eleicao.nome}" foi iniciada.`)
    },
    onError: (err: Error) => {
      error('Erro ao iniciar eleicao', err.message)
    },
  })
}

// End election mutation
export function useEncerrarEleicao() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => eleicoesService.encerrar(id),
    onSuccess: (eleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.setQueryData(eleicaoKeys.detail(eleicao.id), eleicao)
      success('Eleicao encerrada', `A eleicao "${eleicao.nome}" foi encerrada.`)
    },
    onError: (err: Error) => {
      error('Erro ao encerrar eleicao', err.message)
    },
  })
}

// Suspend election mutation
export function useSuspenderEleicao() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      eleicoesService.suspender(id, motivo),
    onSuccess: (eleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.setQueryData(eleicaoKeys.detail(eleicao.id), eleicao)
      success('Eleicao suspensa', `A eleicao "${eleicao.nome}" foi suspensa.`)
    },
    onError: (err: Error) => {
      error('Erro ao suspender eleicao', err.message)
    },
  })
}

// Cancel election mutation
export function useCancelarEleicao() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      eleicoesService.cancelar(id, motivo),
    onSuccess: (eleicao) => {
      queryClient.invalidateQueries({ queryKey: eleicaoKeys.all })
      queryClient.setQueryData(eleicaoKeys.detail(eleicao.id), eleicao)
      success('Eleicao cancelada', `A eleicao "${eleicao.nome}" foi cancelada.`)
    },
    onError: (err: Error) => {
      error('Erro ao cancelar eleicao', err.message)
    },
  })
}

// Prefetch election
export function usePrefetchEleicao() {
  const queryClient = useQueryClient()

  return (id: string) => {
    queryClient.prefetchQuery({
      queryKey: eleicaoKeys.detail(id),
      queryFn: () => eleicoesService.getById(id),
      staleTime: 1000 * 60 * 5,
    })
  }
}

// Hook to select/set current election
export function useSelectEleicao() {
  const setEleicaoAtual = useEleicaoStore((state) => state.setEleicaoAtual)
  const eleicaoAtual = useEleicaoStore((state) => state.eleicaoAtual)
  const queryClient = useQueryClient()

  const select = async (eleicao: Eleicao | string) => {
    if (typeof eleicao === 'string') {
      // It's an ID, fetch the election
      const cachedEleicao = queryClient.getQueryData<Eleicao>(eleicaoKeys.detail(eleicao))

      if (cachedEleicao) {
        setEleicaoAtual(cachedEleicao)
      } else {
        const fetchedEleicao = await eleicoesService.getById(eleicao)
        setEleicaoAtual(fetchedEleicao)
        queryClient.setQueryData(eleicaoKeys.detail(eleicao), fetchedEleicao)
      }
    } else {
      setEleicaoAtual(eleicao)
    }
  }

  const deselect = () => {
    setEleicaoAtual(null)
  }

  return {
    currentEleicao: eleicaoAtual,
    select,
    deselect,
    isSelected: (id: string) => eleicaoAtual?.id === id,
  }
}
