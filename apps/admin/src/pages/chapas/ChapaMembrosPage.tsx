import { useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  ArrowLeft,
  Plus,
  Edit,
  Trash2,
  Loader2,
  Users,
  UserPlus,
  Search,
  Crown,
  Star,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

interface Membro {
  id: string
  nome: string
  cpf: string
  email: string
  cargo: string
  tipo: 'titular' | 'suplente'
  ordem: number
  fotoUrl?: string
}

interface Chapa {
  id: string
  nome: string
  numero: number
  sigla: string
}

const cargoOptions = [
  { value: 'presidente', label: 'Presidente' },
  { value: 'vice_presidente', label: 'Vice-Presidente' },
  { value: 'secretario', label: 'Secretario' },
  { value: 'tesoureiro', label: 'Tesoureiro' },
  { value: 'conselheiro', label: 'Conselheiro' },
  { value: 'conselheiro_suplente', label: 'Conselheiro Suplente' },
]

export function ChapaMembrosPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()
  // const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingMembro, setEditingMembro] = useState<Membro | null>(null)
  const [search, setSearch] = useState('')
  const [formData, setFormData] = useState({
    nome: '',
    cpf: '',
    email: '',
    cargo: 'conselheiro',
    tipo: 'titular' as 'titular' | 'suplente',
    ordem: 1,
  })

  const { data: chapa, isLoading: isLoadingChapa } = useQuery({
    queryKey: ['chapa', id],
    queryFn: async () => {
      const response = await api.get<Chapa>(`/chapa/${id}`)
      return response.data
    },
    enabled: !!id,
  })

  // Mock membros - em producao viria da API
  const [membros, setMembros] = useState<Membro[]>([
    {
      id: '1',
      nome: 'Joao Silva',
      cpf: '123.456.789-00',
      email: 'joao@email.com',
      cargo: 'presidente',
      tipo: 'titular',
      ordem: 1,
    },
    {
      id: '2',
      nome: 'Maria Santos',
      cpf: '987.654.321-00',
      email: 'maria@email.com',
      cargo: 'vice_presidente',
      tipo: 'titular',
      ordem: 2,
    },
    {
      id: '3',
      nome: 'Carlos Oliveira',
      cpf: '456.789.123-00',
      email: 'carlos@email.com',
      cargo: 'secretario',
      tipo: 'titular',
      ordem: 3,
    },
    {
      id: '4',
      nome: 'Ana Costa',
      cpf: '789.123.456-00',
      email: 'ana@email.com',
      cargo: 'tesoureiro',
      tipo: 'titular',
      ordem: 4,
    },
    {
      id: '5',
      nome: 'Pedro Almeida',
      cpf: '321.654.987-00',
      email: 'pedro@email.com',
      cargo: 'conselheiro',
      tipo: 'suplente',
      ordem: 1,
    },
  ])

  const handleOpenModal = (membro?: Membro) => {
    if (membro) {
      setEditingMembro(membro)
      setFormData({
        nome: membro.nome,
        cpf: membro.cpf,
        email: membro.email,
        cargo: membro.cargo,
        tipo: membro.tipo,
        ordem: membro.ordem,
      })
    } else {
      setEditingMembro(null)
      setFormData({
        nome: '',
        cpf: '',
        email: '',
        cargo: 'conselheiro',
        tipo: 'titular',
        ordem: membros.filter((m) => m.tipo === 'titular').length + 1,
      })
    }
    setIsModalOpen(true)
  }

  const handleCloseModal = () => {
    setIsModalOpen(false)
    setEditingMembro(null)
  }

  const handleSaveMembro = () => {
    if (editingMembro) {
      setMembros((prev) =>
        prev.map((m) =>
          m.id === editingMembro.id ? { ...m, ...formData } : m
        )
      )
      toast({
        title: 'Membro atualizado',
        description: 'Os dados do membro foram atualizados.',
      })
    } else {
      const novoMembro: Membro = {
        id: Date.now().toString(),
        ...formData,
      }
      setMembros((prev) => [...prev, novoMembro])
      toast({
        title: 'Membro adicionado',
        description: 'O membro foi adicionado a chapa.',
      })
    }
    handleCloseModal()
  }

  const handleDeleteMembro = (membroId: string) => {
    setMembros((prev) => prev.filter((m) => m.id !== membroId))
    toast({
      title: 'Membro removido',
      description: 'O membro foi removido da chapa.',
    })
  }

  const filteredMembros = membros.filter(
    (m) =>
      m.nome.toLowerCase().includes(search.toLowerCase()) ||
      m.cpf.includes(search) ||
      m.email.toLowerCase().includes(search.toLowerCase())
  )

  const titulares = filteredMembros
    .filter((m) => m.tipo === 'titular')
    .sort((a, b) => a.ordem - b.ordem)

  const suplentes = filteredMembros
    .filter((m) => m.tipo === 'suplente')
    .sort((a, b) => a.ordem - b.ordem)

  const getCargoLabel = (cargo: string) => {
    return cargoOptions.find((c) => c.value === cargo)?.label || cargo
  }

  if (isLoadingChapa) {
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
          <Link to={`/chapas/${id}`}>
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Membros da Chapa</h1>
            <p className="text-gray-600">
              {chapa?.numero}. {chapa?.nome} ({chapa?.sigla})
            </p>
          </div>
        </div>
        <Button onClick={() => handleOpenModal()}>
          <UserPlus className="mr-2 h-4 w-4" />
          Adicionar Membro
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex items-center gap-4">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
              <Input
                placeholder="Buscar membro por nome, CPF ou email..."
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
                        <span className="font-medium">{membro.nome}</span>
                        {membro.cargo === 'presidente' && (
                          <Crown className="h-4 w-4 text-yellow-500" />
                        )}
                      </div>
                      <p className="text-sm text-gray-500">
                        {getCargoLabel(membro.cargo)} | {membro.cpf}
                      </p>
                      <p className="text-sm text-gray-400">{membro.email}</p>
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon" onClick={() => handleOpenModal(membro)}>
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={() => handleDeleteMembro(membro.id)}>
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
                      <span className="font-medium">{membro.nome}</span>
                      <p className="text-sm text-gray-500">
                        {getCargoLabel(membro.cargo)} | {membro.cpf}
                      </p>
                      <p className="text-sm text-gray-400">{membro.email}</p>
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon" onClick={() => handleOpenModal(membro)}>
                      <Edit className="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={() => handleDeleteMembro(membro.id)}>
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

      {/* Modal de Membro */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <Card className="w-full max-w-md">
            <CardHeader>
              <CardTitle>
                {editingMembro ? 'Editar Membro' : 'Novo Membro'}
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="nome">Nome Completo *</Label>
                <Input
                  id="nome"
                  value={formData.nome}
                  onChange={(e) => setFormData({ ...formData, nome: e.target.value })}
                  placeholder="Nome do membro"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="cpf">CPF *</Label>
                  <Input
                    id="cpf"
                    value={formData.cpf}
                    onChange={(e) => setFormData({ ...formData, cpf: e.target.value })}
                    placeholder="000.000.000-00"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email *</Label>
                  <Input
                    id="email"
                    type="email"
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    placeholder="email@exemplo.com"
                  />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="cargo">Cargo *</Label>
                  <select
                    id="cargo"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    value={formData.cargo}
                    onChange={(e) => setFormData({ ...formData, cargo: e.target.value })}
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
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    value={formData.tipo}
                    onChange={(e) =>
                      setFormData({ ...formData, tipo: e.target.value as 'titular' | 'suplente' })
                    }
                  >
                    <option value="titular">Titular</option>
                    <option value="suplente">Suplente</option>
                  </select>
                </div>
              </div>
              <div className="space-y-2">
                <Label htmlFor="ordem">Ordem</Label>
                <Input
                  id="ordem"
                  type="number"
                  min={1}
                  value={formData.ordem}
                  onChange={(e) => setFormData({ ...formData, ordem: parseInt(e.target.value) })}
                />
              </div>
              <div className="flex justify-end gap-2 pt-4">
                <Button variant="outline" onClick={handleCloseModal}>
                  Cancelar
                </Button>
                <Button onClick={handleSaveMembro}>
                  {editingMembro ? 'Salvar' : 'Adicionar'}
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  )
}
