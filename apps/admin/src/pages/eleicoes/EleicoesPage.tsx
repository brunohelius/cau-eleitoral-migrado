import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Link, useNavigate } from 'react-router-dom'
import { Plus, Search, Eye, Edit, Trash2, Loader2 } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
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
import { eleicoesService, Eleicao } from '@/services/eleicoes'

const statusLabels: Record<number, { label: string; color: string }> = {
  0: { label: 'Rascunho', color: 'bg-gray-100 text-gray-800' },
  1: { label: 'Agendada', color: 'bg-blue-100 text-blue-800' },
  2: { label: 'Em Andamento', color: 'bg-green-100 text-green-800' },
  3: { label: 'Encerrada', color: 'bg-yellow-100 text-yellow-800' },
  4: { label: 'Suspensa', color: 'bg-orange-100 text-orange-800' },
  5: { label: 'Cancelada', color: 'bg-red-100 text-red-800' },
  6: { label: 'Apuracao', color: 'bg-purple-100 text-purple-800' },
  7: { label: 'Finalizada', color: 'bg-indigo-100 text-indigo-800' },
}

export function EleicoesPage() {
  const [search, setSearch] = useState('')
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false)
  const [eleicaoToDelete, setEleicaoToDelete] = useState<Eleicao | null>(null)
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()

  const { data: eleicoes, isLoading } = useQuery({
    queryKey: ['eleicoes'],
    queryFn: eleicoesService.getAll,
  })

  const deleteMutation = useMutation({
    mutationFn: (id: string) => eleicoesService.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['eleicoes'] })
      toast({
        title: 'Eleicao excluida com sucesso!',
        description: 'A eleicao foi removida do sistema.',
      })
      setDeleteDialogOpen(false)
      setEleicaoToDelete(null)
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao excluir eleicao',
        description: error.response?.data?.message || 'Nao foi possivel excluir a eleicao. Tente novamente mais tarde.',
      })
    },
  })

  const filteredEleicoes = eleicoes?.filter((eleicao) =>
    eleicao.nome.toLowerCase().includes(search.toLowerCase())
  )

  const handleEdit = (eleicao: Eleicao) => {
    navigate(`/eleicoes/${eleicao.id}/editar`)
  }

  const handleDeleteClick = (eleicao: Eleicao) => {
    setEleicaoToDelete(eleicao)
    setDeleteDialogOpen(true)
  }

  const handleConfirmDelete = () => {
    if (eleicaoToDelete) {
      deleteMutation.mutate(eleicaoToDelete.id)
    }
  }

  const handleCancelDelete = () => {
    setDeleteDialogOpen(false)
    setEleicaoToDelete(null)
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Eleicoes</h1>
          <p className="text-gray-600">Gerencie as eleicoes do sistema</p>
        </div>
        <Link to="/eleicoes/nova">
          <Button>
            <Plus className="mr-2 h-4 w-4" />
            Nova Eleicao
          </Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar eleicoes..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="flex items-center justify-center py-8">
              <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
            </div>
          ) : filteredEleicoes && filteredEleicoes.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-3 px-4 font-medium">Nome</th>
                    <th className="text-left py-3 px-4 font-medium">Ano</th>
                    <th className="text-left py-3 px-4 font-medium">Status</th>
                    <th className="text-left py-3 px-4 font-medium">Chapas</th>
                    <th className="text-left py-3 px-4 font-medium">Periodo</th>
                    <th className="text-right py-3 px-4 font-medium">Acoes</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredEleicoes.map((eleicao) => (
                    <tr key={eleicao.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4">{eleicao.nome}</td>
                      <td className="py-3 px-4">{eleicao.ano}</td>
                      <td className="py-3 px-4">
                        <span
                          className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            statusLabels[eleicao.status]?.color || 'bg-gray-100 text-gray-800'
                          }`}
                        >
                          {statusLabels[eleicao.status]?.label || 'Desconhecido'}
                        </span>
                      </td>
                      <td className="py-3 px-4">{eleicao.totalChapas}</td>
                      <td className="py-3 px-4 text-sm text-gray-500">
                        {new Date(eleicao.dataInicio).toLocaleDateString('pt-BR')} -{' '}
                        {new Date(eleicao.dataFim).toLocaleDateString('pt-BR')}
                      </td>
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <Link to={`/eleicoes/${eleicao.id}`}>
                            <Button variant="ghost" size="icon" title="Visualizar">
                              <Eye className="h-4 w-4" />
                            </Button>
                          </Link>
                          <Button
                            variant="ghost"
                            size="icon"
                            title="Editar"
                            onClick={() => handleEdit(eleicao)}
                          >
                            <Edit className="h-4 w-4" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon"
                            title="Excluir"
                            onClick={() => handleDeleteClick(eleicao)}
                            disabled={deleteMutation.isPending}
                          >
                            <Trash2 className="h-4 w-4 text-red-500" />
                          </Button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-center py-8 text-gray-500">Nenhuma eleicao encontrada.</p>
          )}
        </CardContent>
      </Card>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirmar Exclusao</AlertDialogTitle>
            <AlertDialogDescription>
              Tem certeza que deseja excluir a eleicao "{eleicaoToDelete?.nome}"?
              <br />
              <br />
              Esta acao nao pode ser desfeita. Todos os dados relacionados a esta eleicao serao permanentemente removidos.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={handleCancelDelete} disabled={deleteMutation.isPending}>
              Cancelar
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={handleConfirmDelete}
              disabled={deleteMutation.isPending}
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            >
              {deleteMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              Excluir
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
