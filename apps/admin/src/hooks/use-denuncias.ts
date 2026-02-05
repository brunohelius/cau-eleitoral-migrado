import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  denunciasService,
  statusDenunciaLabels,
  tipoDenunciaLabels,
  prioridadeLabels as servicePrioridadeLabels,
  type Denuncia,
  type CreateDenunciaRequest,
  type UpdateDenunciaRequest,
  type EmitirParecerRequest,
  type DenunciaListParams,
  StatusDenuncia,
  TipoDenuncia,
  PrioridadeDenuncia,
} from '@/services/denuncias'
import { useNotify } from '@/stores/notifications'
import { useCurrentEleicao } from '@/stores/eleicao'

// Query keys
export const denunciaKeys = {
  all: ['denuncias'] as const,
  lists: () => [...denunciaKeys.all, 'list'] as const,
  list: (params: DenunciaListParams) => [...denunciaKeys.lists(), params] as const,
  details: () => [...denunciaKeys.all, 'detail'] as const,
  detail: (id: string) => [...denunciaKeys.details(), id] as const,
  byEleicao: (eleicaoId: string) => [...denunciaKeys.all, 'eleicao', eleicaoId] as const,
  byChapa: (chapaId: string) => [...denunciaKeys.all, 'chapa', chapaId] as const,
  byProtocolo: (protocolo: string) => [...denunciaKeys.all, 'protocolo', protocolo] as const,
  anexos: (denunciaId: string) => [...denunciaKeys.all, 'anexos', denunciaId] as const,
  estatisticas: (eleicaoId?: string) => [...denunciaKeys.all, 'estatisticas', eleicaoId] as const,
}

// Get all denuncias with filters
export function useDenuncias(params?: DenunciaListParams) {
  return useQuery({
    queryKey: denunciaKeys.list(params || {}),
    queryFn: () => denunciasService.getAll(params),
    staleTime: 1000 * 60 * 2,
  })
}

// Get denuncia by ID
export function useDenuncia(id: string | undefined) {
  return useQuery({
    queryKey: denunciaKeys.detail(id!),
    queryFn: () => denunciasService.getById(id!),
    enabled: !!id,
  })
}

// Get denuncia by protocolo
export function useDenunciaByProtocolo(protocolo: string | undefined) {
  return useQuery({
    queryKey: denunciaKeys.byProtocolo(protocolo!),
    queryFn: () => denunciasService.getByProtocolo(protocolo!),
    enabled: !!protocolo,
  })
}

// Get denuncias by eleicao
export function useDenunciasByEleicao(eleicaoId: string | undefined, params?: Omit<DenunciaListParams, 'eleicaoId'>) {
  return useQuery({
    queryKey: [...denunciaKeys.byEleicao(eleicaoId!), params],
    queryFn: () => denunciasService.getByEleicao(eleicaoId!, params),
    enabled: !!eleicaoId,
    staleTime: 1000 * 60 * 2,
  })
}

// Get denuncias for current selected eleicao
export function useDenunciasDaEleicaoAtual(params?: Omit<DenunciaListParams, 'eleicaoId'>) {
  const { eleicaoId } = useCurrentEleicao()
  return useDenunciasByEleicao(eleicaoId, params)
}

// Get denuncias by chapa
export function useDenunciasByChapa(chapaId: string | undefined) {
  return useQuery({
    queryKey: denunciaKeys.byChapa(chapaId!),
    queryFn: () => denunciasService.getByChapa(chapaId!),
    enabled: !!chapaId,
  })
}

// Get denuncia anexos
export function useAnexosDenuncia(denunciaId: string | undefined) {
  return useQuery({
    queryKey: denunciaKeys.anexos(denunciaId!),
    queryFn: () => denunciasService.getAnexos(denunciaId!),
    enabled: !!denunciaId,
  })
}

// Get statistics
export function useEstatisticasDenuncias(eleicaoId?: string) {
  return useQuery({
    queryKey: denunciaKeys.estatisticas(eleicaoId),
    queryFn: () => denunciasService.getEstatisticas(eleicaoId),
    staleTime: 1000 * 60, // 1 minute
  })
}

// Create denuncia mutation
export function useCreateDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (data: CreateDenunciaRequest) => denunciasService.create(data),
    onSuccess: (newDenuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      success('Denuncia registrada', `Protocolo: ${newDenuncia.protocolo}`)
    },
    onError: (err: Error) => {
      error('Erro ao registrar denuncia', err.message)
    },
  })
}

// Update denuncia mutation
export function useUpdateDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: UpdateDenunciaRequest }) =>
      denunciasService.update(id, data),
    onSuccess: (updatedDenuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(updatedDenuncia.id), updatedDenuncia)
      success('Denuncia atualizada', 'Os dados da denuncia foram atualizados.')
    },
    onError: (err: Error) => {
      error('Erro ao atualizar denuncia', err.message)
    },
  })
}

// Delete denuncia mutation
export function useDeleteDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => denunciasService.delete(id),
    onSuccess: (_, deletedId) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.removeQueries({ queryKey: denunciaKeys.detail(deletedId) })
      success('Denuncia excluida', 'A denuncia foi excluida com sucesso.')
    },
    onError: (err: Error) => {
      error('Erro ao excluir denuncia', err.message)
    },
  })
}

// Start analysis mutation
export function useIniciarAnaliseDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (id: string) => denunciasService.iniciarAnalise(id),
    onSuccess: (denuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(denuncia.id), denuncia)
      success('Analise iniciada', 'A analise da denuncia foi iniciada.')
    },
    onError: (err: Error) => {
      error('Erro ao iniciar analise', err.message)
    },
  })
}

// Emit parecer mutation
export function useEmitirParecerDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: EmitirParecerRequest }) =>
      denunciasService.emitirParecer(id, data),
    onSuccess: (denuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(denuncia.id), denuncia)
      success('Parecer emitido', 'O parecer sobre a denuncia foi emitido.')
    },
    onError: (err: Error) => {
      error('Erro ao emitir parecer', err.message)
    },
  })
}

// Archive mutation
export function useArquivarDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      denunciasService.arquivar(id, motivo),
    onSuccess: (denuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(denuncia.id), denuncia)
      success('Denuncia arquivada', 'A denuncia foi arquivada.')
    },
    onError: (err: Error) => {
      error('Erro ao arquivar denuncia', err.message)
    },
  })
}

// Reopen mutation
export function useReabrirDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, motivo }: { id: string; motivo: string }) =>
      denunciasService.reabrir(id, motivo),
    onSuccess: (denuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(denuncia.id), denuncia)
      success('Denuncia reaberta', 'A denuncia foi reaberta para analise.')
    },
    onError: (err: Error) => {
      error('Erro ao reabrir denuncia', err.message)
    },
  })
}

// Assign analyst mutation
export function useAtribuirAnalistaDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ id, analistaId }: { id: string; analistaId: string }) =>
      denunciasService.atribuirAnalista(id, analistaId),
    onSuccess: (denuncia) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.all })
      queryClient.setQueryData(denunciaKeys.detail(denuncia.id), denuncia)
      success('Analista atribuido', 'O analista foi atribuido a denuncia.')
    },
    onError: (err: Error) => {
      error('Erro ao atribuir analista', err.message)
    },
  })
}

// Upload anexo mutation
export function useUploadAnexoDenuncia() {
  const queryClient = useQueryClient()
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: ({ denunciaId, arquivo, nome }: { denunciaId: string; arquivo: File; nome?: string }) =>
      denunciasService.uploadAnexo(denunciaId, arquivo, nome),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: denunciaKeys.anexos(variables.denunciaId) })
      queryClient.invalidateQueries({ queryKey: denunciaKeys.detail(variables.denunciaId) })
      success('Anexo enviado', 'O arquivo foi anexado a denuncia.')
    },
    onError: (err: Error) => {
      error('Erro ao enviar anexo', err.message)
    },
  })
}

// Generate report mutation
export function useGerarRelatorioDenuncias() {
  const { success, error } = useNotify()

  return useMutation({
    mutationFn: (params: {
      eleicaoId?: string
      dataInicio?: string
      dataFim?: string
      formato?: 'pdf' | 'xlsx'
    }) => denunciasService.gerarRelatorio(params),
    onSuccess: (blob, variables) => {
      // Download the file
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `relatorio-denuncias.${variables.formato || 'pdf'}`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)

      success('Relatorio gerado', 'O download do relatorio foi iniciado.')
    },
    onError: (err: Error) => {
      error('Erro ao gerar relatorio', err.message)
    },
  })
}

// Status/Type label helpers - Using imported labels from service
export function useDenunciaLabels() {
  return {
    getStatusLabel: (status: StatusDenuncia) => statusDenunciaLabels[status]?.label || 'Desconhecido',
    getStatusColor: (status: StatusDenuncia) => statusDenunciaLabels[status]?.color || 'bg-gray-100 text-gray-800',
    getTipoLabel: (tipo: TipoDenuncia) => tipoDenunciaLabels[tipo] || 'Desconhecido',
    getPrioridadeLabel: (prioridade: PrioridadeDenuncia) => servicePrioridadeLabels[prioridade]?.label || 'Desconhecido',
    getPrioridadeColor: (prioridade: PrioridadeDenuncia) => servicePrioridadeLabels[prioridade]?.color || 'bg-gray-100 text-gray-800',
    statusLabels: statusDenunciaLabels,
    tipoLabels: tipoDenunciaLabels,
    prioridadeLabels: servicePrioridadeLabels,
  }
}
