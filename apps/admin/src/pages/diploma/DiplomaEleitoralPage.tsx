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

interface Diploma {
  id: string
  eleicaoId: string
  eleicaoNome: string
  chapaId: string
  chapaNome: string
  tipo: number
  tipoNome: string
  dataEmissao: string
  status: number
  statusNome: string
}

const tipoOptions: Record<number, string> = {
  1: 'Diploma de Vencedor',
  2: 'Diploma de Participação',
  3: 'Certidão de Eleição'
}

const statusOptions: Record<number, string> = {
  0: 'Pendente',
  1: 'Gerado',
  2: 'Assinado',
  3: 'Emitido'
}

export function DiplomaEleitoralPage() {
  const [diplomas, setDiplomas] = useState<Diploma[]>([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [pageNumber, setPageNumber] = useState(1)
  const [pageSize] = useState(20)

  useEffect(() => {
    loadDiplomas()
  }, [pageNumber, pageSize])

  const loadDiplomas = async () => {
    try {
      setLoading(true)
      const response = await api.get(`/diploma?pageNumber=${pageNumber}&pageSize=${pageSize}`)
      setDiplomas(response.data.items || [])
    } catch (error) {
      console.error('Erro ao carregar diplomas:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredDiplomas = diplomas.filter(d => 
    d.chapaNome?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    d.eleicaoNome?.toLowerCase().includes(searchTerm.toLowerCase())
  )

  const getStatusBadge = (status: number) => {
    const colors: Record<number, string> = {
      0: 'bg-yellow-500',
      1: 'bg-blue-500',
      2: 'bg-purple-500',
      3: 'bg-green-500'
    }
    return <Badge className={colors[status]}>{statusOptions[status]}</Badge>
  }

  return (
    <div className="container mx-auto py-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold">Diplomas Eleitorais</h1>
        <Button asChild>
          <Link to="/diploma/gerar">Gerar Diploma</Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <CardTitle>Lista de Diplomas</CardTitle>
            <Input
              placeholder="Buscar diplomas..."
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
                  <TableHead>Eleição</TableHead>
                  <TableHead>Chapa</TableHead>
                  <TableHead>Tipo</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Data Emissão</TableHead>
                  <TableHead>Ações</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredDiplomas.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={6} className="text-center py-8">
                      Nenhum diploma encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredDiplomas.map((diploma) => (
                    <TableRow key={diploma.id}>
                      <TableCell>{diploma.eleicaoNome}</TableCell>
                      <TableCell>{diploma.chapaNome}</TableCell>
                      <TableCell>{tipoOptions[diploma.tipo] || 'N/A'}</TableCell>
                      <TableCell>{getStatusBadge(diploma.status)}</TableCell>
                      <TableCell>
                        {diploma.dataEmissao 
                          ? new Date(diploma.dataEmissao).toLocaleDateString('pt-BR')
                          : '-'}
                      </TableCell>
                      <TableCell>
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">Ações</Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            <DropdownMenuItem asChild>
                              <Link to={`/diploma/${diploma.id}`}>Ver Detalhes</Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem>Baixar PDF</DropdownMenuItem>
                            <DropdownMenuItem>Enviar por Email</DropdownMenuItem>
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
              disabled={filteredDiplomas.length < pageSize}
            >
              Próxima
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}