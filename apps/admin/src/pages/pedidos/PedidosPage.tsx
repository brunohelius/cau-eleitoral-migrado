import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { api } from '@/lib/api'

interface Pedido {
  id: string
  eleicaoId: string
  eleicaoNome: string
  solicitanteId: string
  solicitanteNome: string
  tipo: number
  tipoNome: string
  status: number
  statusNome: string
  descricao: string
  dataCriacao: string
  dataResposta?: string
}

const tipoOptions: Record<number, string> = {
  1: 'Registro de Chapa',
  2: 'Substituição de Membro',
  3: 'Impugnação',
  4: 'Recurso',
  5: 'Outros'
}

const statusOptions: Record<number, string> = {
  0: 'Pendente',
  1: 'Em Análise',
  2: 'Deferido',
  3: 'Indeferido',
  4: 'Cancelado'
}

export function PedidosPage() {
  const [pedidos, setPedidos] = useState<Pedido[]>([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [pageNumber, setPageNumber] = useState(1)
  const [pageSize] = useState(20)
  const [totalCount, setTotalCount] = useState(0)

  useEffect(() => {
    loadPedidos()
  }, [pageNumber, pageSize])

  const loadPedidos = async () => {
    try {
      setLoading(true)
      const response = await api.get(`/pedidos?pageNumber=${pageNumber}&pageSize=${pageSize}`)
      setPedidos(response.data.items || [])
      setTotalCount(response.data.totalCount || 0)
    } catch (error) {
      console.error('Erro ao carregar pedidos:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredPedidos = pedidos.filter(p => 
    p.solicitanteNome.toLowerCase().includes(searchTerm.toLowerCase()) ||
    p.descricao?.toLowerCase().includes(searchTerm.toLowerCase())
  )

  const getStatusBadge = (status: number) => {
    const colors: Record<number, string> = {
      0: 'bg-yellow-500',
      1: 'bg-blue-500',
      2: 'bg-green-500',
      3: 'bg-red-500',
      4: 'bg-gray-500'
    }
    return <Badge className={colors[status]}>{statusOptions[status]}</Badge>
  }

  return (
    <div className="container mx-auto py-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold">Pedidos</h1>
        <Button asChild>
          <Link to="/pedidos/novo">Novo Pedido</Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <CardTitle>Lista de Pedidos</CardTitle>
            <Input
              placeholder="Buscar pedidos..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-64"
            />
          </div>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="text-center py-8">Carregando...</div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>Tipo</TableHead>
                  <TableHead>Solicitante</TableHead>
                  <TableHead>Eleição</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Data Criação</TableHead>
                  <TableHead>Ações</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredPedidos.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-8">
                      Nenhum pedido encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredPedidos.map((pedido) => (
                    <TableRow key={pedido.id}>
                      <TableCell className="font-mono text-sm">
                        {pedido.id.slice(0, 8)}...
                      </TableCell>
                      <TableCell>{tipoOptions[pedido.tipo] || 'N/A'}</TableCell>
                      <TableCell>{pedido.solicitanteNome}</TableCell>
                      <TableCell>{pedido.eleicaoNome}</TableCell>
                      <TableCell>{getStatusBadge(pedido.status)}</TableCell>
                      <TableCell>{new Date(pedido.dataCriacao).toLocaleDateString('pt-BR')}</TableCell>
                      <TableCell>
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">Ações</Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            <DropdownMenuItem asChild>
                              <Link to={`/pedidos/${pedido.id}`}>Ver Detalhes</Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem asChild>
                              <Link to={`/pedidos/${pedido.id}/editar`}>Editar</Link>
                            </DropdownMenuItem>
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          )}
          
          <div className="flex justify-center items-center gap-2 mt-4">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setPageNumber(p => Math.max(1, p - 1))}
              disabled={pageNumber === 1}
            >
              Anterior
            </Button>
            <span className="text-sm">Página {pageNumber}</span>
            <Button
              variant="outline"
              size="sm"
              onClick={() => setPageNumber(p => p + 1)}
              disabled={filteredPedidos.length < pageSize}
            >
              Próxima
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}