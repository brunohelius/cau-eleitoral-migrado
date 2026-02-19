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
import { api } from '@/lib/api'

interface TermoPosse {
  id: string
  eleicaoId: string
  eleicaoNome: string
  membroId: string
  membroNome: string
  cargo: string
  dataPosse: string
  dataTermo: string
  status: number
  statusNome: string
  termosAssinados: number
}

const statusOptions: Record<number, string> = {
  0: 'Pendente',
  1: 'Em Andamento',
  2: 'Assinado',
  3: 'Concluído'
}

export function TermoPossePage() {
  const [termos, setTermos] = useState<TermoPosse[]>([])
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState('')
  const [pageNumber, setPageNumber] = useState(1)
  const [pageSize] = useState(20)

  useEffect(() => {
    loadTermos()
  }, [pageNumber, pageSize])

  const loadTermos = async () => {
    try {
      setLoading(true)
      const response = await api.get(`/termoposse?pageNumber=${pageNumber}&pageSize=${pageSize}`)
      setTermos(response.data.items || [])
    } catch (error) {
      console.error('Erro ao carregar termos de posse:', error)
    } finally {
      setLoading(false)
    }
  }

  const filteredTermos = termos.filter(t => 
    t.membroNome?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    t.eleicaoNome?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    t.cargo?.toLowerCase().includes(searchTerm.toLowerCase())
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
        <h1 className="text-3xl font-bold">Termos de Posse</h1>
        <Button asChild>
          <Link to="/termoposse/gerar">Gerar Termos</Link>
        </Button>
      </div>

      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <CardTitle>Lista de Termos de Posse</CardTitle>
            <Input
              placeholder="Buscar termos..."
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
                  <TableHead>Membro</TableHead>
                  <TableHead>Cargo</TableHead>
                  <TableHead>Data Posse</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Assinaturas</TableHead>
                  <TableHead>Ações</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredTermos.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={7} className="text-center py-8">
                      Nenhum termo de posse encontrado
                    </TableCell>
                  </TableRow>
                ) : (
                  filteredTermos.map((termo) => (
                    <TableRow key={termo.id}>
                      <TableCell>{termo.eleicaoNome}</TableCell>
                      <TableCell>{termo.membroNome}</TableCell>
                      <TableCell>{termo.cargo}</TableCell>
                      <TableCell>
                        {termo.dataPosse 
                          ? new Date(termo.dataPosse).toLocaleDateString('pt-BR')
                          : '-'}
                      </TableCell>
                      <TableCell>{getStatusBadge(termo.status)}</TableCell>
                      <TableCell>{termo.termosAssinados}/2</TableCell>
                      <TableCell>
                        <Button variant="ghost" size="sm" asChild>
                          <Link to={`/termoposse/${termo.id}`}>Ver</Link>
                        </Button>
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
              disabled={filteredTermos.length < pageSize}
            >
              Próxima
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}