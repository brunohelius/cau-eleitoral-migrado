import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Calendar, Plus, Edit, Trash2, Loader2, Clock } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import { eleicoesService } from '@/services/eleicoes'
import api from '@/services/api'

interface EventoCalendario {
  id: string
  titulo: string
  descricao?: string
  dataInicio: string
  dataFim: string
  tipo: string
  cor: string
}

const tiposEvento = [
  { value: 'inscricao', label: 'Inscricao de Chapas', cor: 'bg-blue-500' },
  { value: 'impugnacao', label: 'Prazo de Impugnacao', cor: 'bg-orange-500' },
  { value: 'campanha', label: 'Campanha Eleitoral', cor: 'bg-green-500' },
  { value: 'votacao', label: 'Periodo de Votacao', cor: 'bg-purple-500' },
  { value: 'apuracao', label: 'Apuracao', cor: 'bg-red-500' },
  { value: 'recurso', label: 'Prazo de Recursos', cor: 'bg-yellow-500' },
  { value: 'diplomacao', label: 'Diplomacao', cor: 'bg-indigo-500' },
]

const mapTipoCalendario = (tipo: number): { value: string; cor: string } => {
  switch (tipo) {
    case 0: return { value: 'inscricao', cor: 'bg-blue-500' }
    case 1: return { value: 'impugnacao', cor: 'bg-orange-500' }
    case 2: return { value: 'campanha', cor: 'bg-green-500' }
    case 3: return { value: 'votacao', cor: 'bg-purple-500' }
    case 4: return { value: 'apuracao', cor: 'bg-red-500' }
    case 5: return { value: 'recurso', cor: 'bg-yellow-500' }
    case 6: return { value: 'diplomacao', cor: 'bg-indigo-500' }
    default: return { value: 'inscricao', cor: 'bg-gray-500' }
  }
}

const mapTipoToIndex = (tipo: string): number => {
  const map: Record<string, number> = {
    inscricao: 0, impugnacao: 1, campanha: 2, votacao: 3,
    apuracao: 4, recurso: 5, diplomacao: 6,
  }
  return map[tipo] ?? 0
}

export function EleicaoCalendarioPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingEvento, setEditingEvento] = useState<EventoCalendario | null>(null)
  const [formData, setFormData] = useState({
    titulo: '',
    descricao: '',
    dataInicio: '',
    dataFim: '',
    tipo: 'inscricao',
  })

  const { data: eleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['eleicao', id],
    queryFn: () => eleicoesService.getById(id!),
    enabled: !!id,
  })

  const { data: eventos = [], isLoading: isLoadingEventos, refetch: refetchEventos } = useQuery({
    queryKey: ['calendario', id],
    queryFn: async () => {
      try {
        const response = await api.get('/calendario', { params: { eleicaoId: id } })
        const apiData = Array.isArray(response.data) ? response.data : []
        return apiData.map((e: any) => {
          const tipoInfo = mapTipoCalendario(e.tipo)
          return {
            id: e.id,
            titulo: e.titulo,
            descricao: e.descricao,
            dataInicio: e.dataInicio?.split('T')[0] || '',
            dataFim: e.dataFim?.split('T')[0] || '',
            tipo: tipoInfo.value,
            cor: tipoInfo.cor,
          }
        }) as EventoCalendario[]
      } catch {
        return [] as EventoCalendario[]
      }
    },
    enabled: !!id,
  })

  const handleOpenModal = (evento?: EventoCalendario) => {
    if (evento) {
      setEditingEvento(evento)
      setFormData({
        titulo: evento.titulo,
        descricao: evento.descricao || '',
        dataInicio: evento.dataInicio,
        dataFim: evento.dataFim,
        tipo: evento.tipo,
      })
    } else {
      setEditingEvento(null)
      setFormData({
        titulo: '',
        descricao: '',
        dataInicio: '',
        dataFim: '',
        tipo: 'inscricao',
      })
    }
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
    setEditingEvento(null)
  }

  const handleSaveEvento = async () => {
    try {
      if (editingEvento) {
        await api.put(`/calendario/${editingEvento.id}`, {
          titulo: formData.titulo,
          descricao: formData.descricao || undefined,
          tipo: mapTipoToIndex(formData.tipo),
          dataInicio: formData.dataInicio,
          dataFim: formData.dataFim,
        })
        toast({
          title: 'Evento atualizado',
          description: 'O evento foi atualizado com sucesso.',
        })
      } else {
        await api.post('/calendario', {
          eleicaoId: id,
          titulo: formData.titulo,
          descricao: formData.descricao || undefined,
          tipo: mapTipoToIndex(formData.tipo),
          dataInicio: formData.dataInicio,
          dataFim: formData.dataFim,
        })
        toast({
          title: 'Evento adicionado',
          description: 'O evento foi adicionado ao calendario.',
        })
      }
      refetchEventos()
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Erro ao salvar evento.',
      })
    }
    handleCloseModal()
  }

  const handleDeleteEvento = async (eventoId: string) => {
    try {
      await api.delete(`/calendario/${eventoId}`)
      toast({
        title: 'Evento removido',
        description: 'O evento foi removido do calendario.',
      })
      refetchEventos()
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro',
        description: 'Erro ao remover evento.',
      })
    }
  }

  if (isLoadingEleicao || isLoadingEventos) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to={`/eleicoes/${id}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Calendario da Eleicao</h1>
            <p className="text-gray-600">{eleicao?.nome}</p>
          </div>
        </div>
        <Button onClick={() => handleOpenModal()}>
          <Plus className="mr-2 h-4 w-4" />
          Novo Evento
        </Button>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Timeline */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Calendar className="h-5 w-5" />
              Cronograma
            </CardTitle>
            <CardDescription>Eventos e prazos da eleicao</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="relative">
              <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
              <div className="space-y-6">
                {eventos
                  .sort((a, b) => new Date(a.dataInicio).getTime() - new Date(b.dataInicio).getTime())
                  .map((evento) => (
                    <div key={evento.id} className="relative flex gap-4 pl-10">
                      <div
                        className={`absolute left-2 top-1 h-4 w-4 rounded-full ${evento.cor} ring-4 ring-white`}
                      />
                      <div className="flex-1 rounded-lg border bg-white p-4 shadow-sm">
                        <div className="flex items-start justify-between">
                          <div>
                            <h3 className="font-semibold text-gray-900">{evento.titulo}</h3>
                            <p className="text-sm text-gray-500">{evento.descricao}</p>
                            <div className="mt-2 flex items-center gap-2 text-sm text-gray-600">
                              <Clock className="h-4 w-4" />
                              <span>
                                {new Date(evento.dataInicio).toLocaleDateString('pt-BR')} -{' '}
                                {new Date(evento.dataFim).toLocaleDateString('pt-BR')}
                              </span>
                            </div>
                          </div>
                          <div className="flex gap-1">
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => handleOpenModal(evento)}
                            >
                              <Edit className="h-4 w-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => handleDeleteEvento(evento.id)}
                            >
                              <Trash2 className="h-4 w-4 text-red-500" />
                            </Button>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                {eventos.length === 0 && (
                  <p className="text-center py-8 text-gray-500">
                    Nenhum evento cadastrado. Clique em "Novo Evento" para adicionar.
                  </p>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Legenda */}
        <Card>
          <CardHeader>
            <CardTitle>Tipos de Evento</CardTitle>
            <CardDescription>Legenda dos eventos</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {tiposEvento.map((tipo) => (
                <div key={tipo.value} className="flex items-center gap-3">
                  <div className={`h-4 w-4 rounded-full ${tipo.cor}`} />
                  <span className="text-sm text-gray-700">{tipo.label}</span>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Modal de Evento */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-md">
            <CardHeader>
              <CardTitle>
                {editingEvento ? 'Editar Evento' : 'Novo Evento'}
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="titulo">Titulo *</Label>
                <Input
                  id="titulo"
                  value={formData.titulo}
                  onChange={(e) => setFormData({ ...formData, titulo: e.target.value })}
                  placeholder="Titulo do evento"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="descricao">Descricao</Label>
                <textarea
                  id="descricao"
                  className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  value={formData.descricao}
                  onChange={(e) => setFormData({ ...formData, descricao: e.target.value })}
                  placeholder="Descricao do evento"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="tipo">Tipo de Evento *</Label>
                <select
                  id="tipo"
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  value={formData.tipo}
                  onChange={(e) => setFormData({ ...formData, tipo: e.target.value })}
                >
                  {tiposEvento.map((tipo) => (
                    <option key={tipo.value} value={tipo.value}>
                      {tipo.label}
                    </option>
                  ))}
                </select>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="dataInicio">Data Inicio *</Label>
                  <Input
                    id="dataInicio"
                    type="date"
                    value={formData.dataInicio}
                    onChange={(e) => setFormData({ ...formData, dataInicio: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="dataFim">Data Fim *</Label>
                  <Input
                    id="dataFim"
                    type="date"
                    value={formData.dataFim}
                    onChange={(e) => setFormData({ ...formData, dataFim: e.target.value })}
                  />
                </div>
              </div>
              <div className="flex justify-end gap-2 pt-4">
                <Button variant="outline" onClick={handleCloseModal}>
                  Cancelar
                </Button>
                <Button onClick={handleSaveEvento}>
                  {editingEvento ? 'Salvar' : 'Adicionar'}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
