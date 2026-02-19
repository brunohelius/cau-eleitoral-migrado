import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  Edit,
  Trash2,
  Loader2,
  UserPlus,
  Search,
  Crown,
  Star,
  AlertTriangle,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { useToast } from '@/hooks/use-toast'
import {
  chapasService,
  TipoMembro,
  CargoMembro,
  type Chapa,
  type MembroChapa,
  type AddMembroRequest,
} from '@/services/chapas'

// Cargo options
const cargoOptions = [
  { value: CargoMembro.PRESIDENTE, label: 'Presidente' },
  { value: CargoMembro.VICE_PRESIDENTE, label: 'Vice-Presidente' },
  { value: CargoMembro.CONSELHEIRO, label: 'Conselheiro' },
  { value: CargoMembro.DIRETOR, label: 'Diretor' },
  { value: CargoMembro.COORDENADOR, label: 'Coordenador' },
]

// Tipo options
const tipoOptions = [
  { value: TipoMembro.TITULAR, label: 'Titular' },
  { value: TipoMembro.SUPLENTE, label: 'Suplente' },
]

export function ChapaMembrosPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  // Modal states
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingMembro, setEditingMembro] = useState<MembroChapa | null>(null)
  const [search, setSearch] = useState('')

  // Delete dialog state
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [membroToDelete, setMembroToDelete] = useState<MembroChapa | null>(null)

  // Form state
  const [formData, setFormData] = useState<{
    candidatoId: string
    cargo: CargoMembro
    tipo: TipoMembro
    ordem: number
  }>({
    candidatoId: '',
    cargo: CargoMembro.CONSELHEIRO,
    tipo: TipoMembro.TITULAR,
    ordem: 1,
  })

  // Fetch chapa details
  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: () => chapasService.getById(id!),
    enabled: !!id,
  })

  // Fetch membros
  const {
    data: membros,
    isLoading: isLoadingMembros,
    isError,
  } = useQuery({
    queryKey: ['chapa-membros', id],
    queryFn: () => chapasService.getMembros(id!),
    enabled: !!id,
  })

  // Add member mutation
  const addMembroMutation = useMutation({
    mutationFn: (data: AddMembroRequest) => chapasService.addMembro(id!, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapa-membros', id] })
      queryClient.invalidateQueries({ queryKey: ['chapa', id] })
      toast({
        title: 'Membro adicionado',
        description: 'O membro foi adicionado a chapa com sucesso.',
      })
      handleCloseModal()
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao adicionar membro',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Remove member mutation
  const removeMembroMutation = useMutation({
    mutationFn: (membroId: string) => chapasService.removeMembro(id!, membroId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['chapa-membros', id] })
      queryClient.invalidateQueries({ queryKey: ['chapa', id] })
      toast({
        title: 'Membro removido',
        description: 'O membro foi removido da chapa.',
      })
      setDeleteDialogOpen(false)
      setMembroToDelete(null)
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao remover membro',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  // Modal handlers
  const handleOpenModal = (membro?: MembroChapa) => {
    if (membro) {
      setEditingMembro(membro)
      setFormData({
        candidatoId: membro.candidatoId,
        cargo: membro.cargo,
        tipo: membro.tipo,
        ordem: membro.ordem,
      })
    } else {
      setEditingMembro(null)
      const currentMembros = membros || []
      const titulares = currentMembros.filter((m) => m.tipo === TipoMembro.TITULAR)
      setFormData({
        candidatoId: '',
        cargo: CargoMembro.CONSELHEIRO,
        tipo: TipoMembro.TITULAR,
        ordem: titulares.length + 1,
      })
    }
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
    setEditingMembro(null)
    setFormData({
      candidatoId: '',
      cargo: CargoMembro.CONSELHEIRO,
      tipo: TipoMembro.TITULAR,
      ordem: 1,
    })
  }

  const handleSaveMembro = () => {
    if (!formData.candidatoId) {
      toast({
        variant: 'destructive',
        title: 'Campo obrigatório',
        description: 'Selecione um candidato.',
      })
      return
    }

    addMembroMutation.mutate({
      candidatoId: formData.candidatoId,
      cargo: formData.cargo,
      tipo: formData.tipo,
      ordem: formData.ordem,
    })
  }

  const handleDeleteClick = (membro: MembroChapa) => {
    setMembroToDelete(membro)
    setDeleteDialogOpen(true)
  }

  const handleDeleteConfirm = () => {
    if (membroToDelete) {
      removeMembroMutation.mutate(membroToDelete.id)
    }
  }

  // Filter members
  const filteredMembros = (membros || []).filter(
    (m) =>
      m.candidatoNome.toLowerCase().includes(search.toLowerCase()) ||
      m.candidatoCpf?.includes(search) ||
      m.candidatoRegistroCAU?.toLowerCase().includes(search.toLowerCase())
  )

  const titulares = filteredMembros
    .filter((m) => m.tipo === TipoMembro.TITULAR)
    .sort((a, b) => a.ordem - b.ordem)

  const suplentes = filteredMembros
    .filter((m) => m.tipo === TipoMembro.SUPLENTE)
    .sort((a, b) => a.ordem - b.ordem)

  const getCargoLabel = (cargo: CargoMembro) => {
    return cargoOptions.find((c) => c.value === cargo)?.label || 'Membro'
  }

  const isLoading = isLoadingChapa || isLoadingMembros

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (isError || !chapa) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertTriangle className="h-12 w-12 text-red-400 mb-4" />
        <p className="text-gray-500">Chapa não encontrada.</p>
        <Link to="/chapas" className="mt-4">
          <Button variant="outline">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Voltar para lista
          </Button>
        </Link>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to={`/chapas/${id}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Membros da Chapa</h1>
            <p className="text-gray-600">
              {chapa.numero}. {chapa.nome} ({chapa.sigla || 'Sem sigla'})
            </p>
          </div>
        </div>
        <Button onClick={() => handleOpenModal()}>
          <UserPlus className="mr-2 h-4 w-4" />
          Adicionar Membro
        </Button>
      </div>

      {/* Search */}
      <Card>
        <CardHeader>
          <div className="flex items-center gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar membro por nome, CPF ou registro CAU..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </CardHeader>
      </Card>

      {/* Titulares */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Crown className="h-5 w-5 text-yellow-500" />
            Titulares
          </CardTitle>
          <CardDescription>{titulares.length} membros titulares</CardDescription>
        </CardHeader>
        <CardContent>
          {titulares.length > 0 ? (
            <div className="space-y-3">
              {titulares.map((membro) => (
                <div
                  key={membro.id}
                  className="flex items-center justify-between rounded-lg border p-4"
                >
                  <div className="flex items-center gap-4">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-semibold">
                      {membro.ordem}
                    </div>
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{membro.candidatoNome}</span>
                        {membro.cargo === CargoMembro.PRESIDENTE && (
                          <Crown className="h-4 w-4 text-yellow-500" />
                        )}
                      </div>
                      <p className="text-sm text-gray-500">
                        {getCargoLabel(membro.cargo)}
                        {membro.candidatoRegistroCAU && ` | ${membro.candidatoRegistroCAU}`}
                      </p>
                      {membro.candidatoCpf && (
                        <p className="text-sm text-gray-400">CPF: {membro.candidatoCpf}</p>
                      )}
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleOpenModal(membro)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleDeleteClick(membro)}
                    >
                      <Trash2 className="h-4 w-4 text-red-500" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">Nenhum membro titular encontrado.</p>
          )}
        </CardContent>
      </Card>

      {/* Suplentes */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Star className="h-5 w-5 text-gray-500" />
            Suplentes
          </CardTitle>
          <CardDescription>{suplentes.length} membros suplentes</CardDescription>
        </CardHeader>
        <CardContent>
          {suplentes.length > 0 ? (
            <div className="space-y-3">
              {suplentes.map((membro) => (
                <div
                  key={membro.id}
                  className="flex items-center justify-between rounded-lg border p-4"
                >
                  <div className="flex items-center gap-4">
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 font-semibold">
                      {membro.ordem}
                    </div>
                    <div>
                      <span className="font-medium">{membro.candidatoNome}</span>
                      <p className="text-sm text-gray-500">
                        {getCargoLabel(membro.cargo)}
                        {membro.candidatoRegistroCAU && ` | ${membro.candidatoRegistroCAU}`}
                      </p>
                      {membro.candidatoCpf && (
                        <p className="text-sm text-gray-400">CPF: {membro.candidatoCpf}</p>
                      )}
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleOpenModal(membro)}
                    >
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => handleDeleteClick(membro)}
                    >
                      <Trash2 className="h-4 w-4 text-red-500" />
                    </Button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">Nenhum membro suplente encontrado.</p>
          )}
        </CardContent>
      </Card>

      {/* Add/Edit Member Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-md">
            <CardHeader>
              <CardTitle>
                {editingMembro ? 'Editar Membro' : 'Adicionar Membro'}
              </CardTitle>
              <CardDescription>
                {editingMembro
                  ? 'Atualize as informacoes do membro'
                  : 'Adicione um novo membro a chapa'}
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="candidatoId">Candidato *</Label>
                <Input
                  id="candidatoId"
                  value={formData.candidatoId}
                  onChange={(e) => setFormData({ ...formData, candidatoId: e.target.value })}
                  placeholder="ID do candidato ou buscar..."
                  disabled={!!editingMembro}
                />
                <p className="text-xs text-gray-500">
                  Informe o ID do candidato ou utilize o sistema de busca
                </p>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="cargo">Cargo *</Label>
                  <select
                    id="cargo"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    value={formData.cargo}
                    onChange={(e) =>
                      setFormData({ ...formData, cargo: Number(e.target.value) as CargoMembro })
                    }
                  >
                    {cargoOptions.map((cargo) => (
                      <option key={cargo.value} value={cargo.value}>
                        {cargo.label}
                      </option>
                    ))}
                  </select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo *</Label>
                  <select
                    id="tipo"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    value={formData.tipo}
                    onChange={(e) =>
                      setFormData({ ...formData, tipo: Number(e.target.value) as TipoMembro })
                    }
                  >
                    {tipoOptions.map((tipo) => (
                      <option key={tipo.value} value={tipo.value}>
                        {tipo.label}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="ordem">Ordem na chapa</Label>
                <Input
                  id="ordem"
                  type="number"
                  min={1}
                  value={formData.ordem}
                  onChange={(e) => setFormData({ ...formData, ordem: parseInt(e.target.value) || 1 })}
                />
              </div>

              <div className="flex justify-end gap-2 pt-4">
                <Button variant="outline" onClick={handleCloseModal}>
                  Cancelar
                </Button>
                <Button
                  onClick={handleSaveMembro}
                  disabled={addMembroMutation.isPending}
                >
                  {addMembroMutation.isPending ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Salvando...
                    </>
                  ) : editingMembro ? (
                    'Salvar'
                  ) : (
                    'Adicionar'
                  )}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Remover Membro</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja remover <strong>"{membroToDelete?.candidatoNome}"</strong> da
              chapa? Esta acao nao pode ser desfeita.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel disabled={removeMembroMutation.isPending}>
              Cancelar
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDeleteConfirm}
              className="bg-red-600 hover:bg-red-700"
              disabled={removeMembroMutation.isPending}
            >
              {removeMembroMutation.isPending ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Removendo...
                </>
              ) : (
                'Remover'
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
