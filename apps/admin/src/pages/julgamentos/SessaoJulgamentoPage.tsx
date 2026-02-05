import { useState } from 'react'
import { useNavigate, Link, useSearchParams } from 'react-router-dom'
import {
  ArrowLeft,
  Gavel,
  Users,
  Play,
  Pause,
  CheckCircle,
  XCircle,
  Clock,
  FileText,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'

interface ProcessoPauta {
  id: string
  protocolo: string
  tipo: string
  titulo: string
  relatorNome: string
  status: 'pendente' | 'em_julgamento' | 'julgado'
  decisao?: 'deferida' | 'indeferida'
}

interface MembroComissao {
  id: string
  nome: string
  cargo: string
  presente: boolean
}

export function SessaoJulgamentoPage() {
  const navigate = useNavigate()
  // const [searchParams] = useSearchParams()
  const { toast } = useToast()
  // const queryClient = useQueryClient()

  const [sessaoIniciada, setSessaoIniciada] = useState(false)
  const [processoAtual, setProcessoAtual] = useState<ProcessoPauta | null>(null)
  const [votacaoAberta, setVotacaoAberta] = useState(false)
  const [votos, setVotos] = useState<Record<string, 'deferido' | 'indeferido' | 'abstencao'>>({})
  const [fundamentacao, setFundamentacao] = useState('')

  // Mock membros da comissao
  const [membros, setMembros] = useState<MembroComissao[]>([
    { id: '1', nome: 'Dr. Carlos Oliveira', cargo: 'Presidente', presente: true },
    { id: '2', nome: 'Dra. Maria Santos', cargo: 'Vice-Presidente', presente: true },
    { id: '3', nome: 'Dr. Pedro Almeida', cargo: 'Secretario', presente: true },
    { id: '4', nome: 'Dra. Ana Costa', cargo: 'Membro', presente: false },
    { id: '5', nome: 'Dr. Jose Lima', cargo: 'Membro', presente: true },
  ])

  // Mock processos na pauta
  const [processos, setProcessos] = useState<ProcessoPauta[]>([
    {
      id: '1',
      protocolo: 'DEN-2024-00001',
      tipo: 'denuncia',
      titulo: 'Irregularidade na campanha eleitoral',
      relatorNome: 'Dr. Carlos Oliveira',
      status: 'pendente',
    },
    {
      id: '2',
      protocolo: 'IMP-2024-00001',
      tipo: 'impugnacao',
      titulo: 'Irregularidade na documentacao da chapa',
      relatorNome: 'Dr. Pedro Almeida',
      status: 'pendente',
    },
    {
      id: '3',
      protocolo: 'DEN-2024-00003',
      tipo: 'denuncia',
      titulo: 'Uso indevido de recursos publicos',
      relatorNome: 'Dra. Maria Santos',
      status: 'pendente',
    },
  ])

  const membrosPresentes = membros.filter((m) => m.presente)
  const quorum = membrosPresentes.length >= Math.ceil(membros.length / 2)

  const handleTogglePresenca = (membroId: string) => {
    setMembros((prev) =>
      prev.map((m) => (m.id === membroId ? { ...m, presente: !m.presente } : m))
    )
  }

  const handleIniciarSessao = () => {
    if (!quorum) {
      toast({
        variant: 'destructive',
        title: 'Quorum insuficiente',
        description: 'E necessario quorum minimo para iniciar a sessao.',
      })
      return
    }
    setSessaoIniciada(true)
    toast({
      title: 'Sessao iniciada',
      description: 'A sessao de julgamento foi iniciada.',
    })
  }

  const handleEncerrarSessao = () => {
    setSessaoIniciada(false)
    setProcessoAtual(null)
    setVotacaoAberta(false)
    toast({
      title: 'Sessao encerrada',
      description: 'A sessao de julgamento foi encerrada.',
    })
    navigate('/julgamentos')
  }

  const handleSelecionarProcesso = (processo: ProcessoPauta) => {
    if (processo.status !== 'pendente') return
    setProcessoAtual(processo)
    setVotacaoAberta(false)
    setVotos({})
    setFundamentacao('')
    setProcessos((prev) =>
      prev.map((p) => (p.id === processo.id ? { ...p, status: 'em_julgamento' } : p))
    )
  }

  const handleIniciarVotacao = () => {
    setVotacaoAberta(true)
    toast({
      title: 'Votacao iniciada',
      description: 'Os membros podem registrar seus votos.',
    })
  }

  const handleRegistrarVoto = (membroId: string, voto: 'deferido' | 'indeferido' | 'abstencao') => {
    setVotos((prev) => ({ ...prev, [membroId]: voto }))
  }

  const handleFinalizarVotacao = () => {
    const votosDeferidos = Object.values(votos).filter((v) => v === 'deferido').length
    const votosIndeferidos = Object.values(votos).filter((v) => v === 'indeferido').length
    const decisao = votosDeferidos > votosIndeferidos ? 'deferida' : 'indeferida'

    setProcessos((prev) =>
      prev.map((p) =>
        p.id === processoAtual?.id ? { ...p, status: 'julgado', decisao } : p
      )
    )

    toast({
      title: 'Votacao finalizada',
      description: `Processo ${decisao === 'deferida' ? 'deferido' : 'indeferido'} por ${votosDeferidos} x ${votosIndeferidos}.`,
    })

    setProcessoAtual(null)
    setVotacaoAberta(false)
    setVotos({})
    setFundamentacao('')
  }

  const getTipoBadge = (tipo: string) => {
    const config: Record<string, { label: string; color: string }> = {
      denuncia: { label: 'Denuncia', color: 'bg-orange-100 text-orange-800' },
      impugnacao: { label: 'Impugnacao', color: 'bg-red-100 text-red-800' },
      recurso: { label: 'Recurso', color: 'bg-purple-100 text-purple-800' },
    }
    const item = config[tipo]
    return (
      <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${item.color}`}>
        {item.label}
      </span>
    )
  }

  const getStatusBadge = (status: string, decisao?: string) => {
    if (status === 'julgado') {
      return decisao === 'deferida' ? (
        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
          <CheckCircle className="h-3 w-3" />
          Deferido
        </span>
      ) : (
        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
          <XCircle className="h-3 w-3" />
          Indeferido
        </span>
      )
    }
    if (status === 'em_julgamento') {
      return (
        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
          <Gavel className="h-3 w-3" />
          Em Julgamento
        </span>
      )
    }
    return (
      <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
        <Clock className="h-3 w-3" />
        Pendente
      </span>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/julgamentos">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Sessao de Julgamento</h1>
            <p className="text-gray-600">
              {sessaoIniciada ? 'Sessao em andamento' : 'Configure a sessao antes de iniciar'}
            </p>
          </div>
        </div>
        {!sessaoIniciada ? (
          <Button onClick={handleIniciarSessao} disabled={!quorum}>
            <Play className="mr-2 h-4 w-4" />
            Iniciar Sessao
          </Button>
        ) : (
          <Button variant="destructive" onClick={handleEncerrarSessao}>
            <Pause className="mr-2 h-4 w-4" />
            Encerrar Sessao
          </Button>
        )}
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Membros Presentes */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Users className="h-5 w-5" />
              Membros da Comissao
            </CardTitle>
            <CardDescription>
              {membrosPresentes.length}/{membros.length} presentes
              {quorum ? (
                <span className="ml-2 text-green-600">(Quorum atingido)</span>
              ) : (
                <span className="ml-2 text-red-600">(Sem quorum)</span>
              )}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-2">
              {membros.map((membro) => (
                <div
                  key={membro.id}
                  className="flex items-center justify-between rounded-lg border p-3"
                >
                  <div>
                    <p className="font-medium text-sm">{membro.nome}</p>
                    <p className="text-xs text-gray-500">{membro.cargo}</p>
                  </div>
                  <Button
                    variant={membro.presente ? 'default' : 'outline'}
                    size="sm"
                    onClick={() => handleTogglePresenca(membro.id)}
                    disabled={sessaoIniciada}
                  >
                    {membro.presente ? 'Presente' : 'Ausente'}
                  </Button>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Pauta */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileText className="h-5 w-5" />
              Pauta da Sessao
            </CardTitle>
            <CardDescription>
              {processos.filter((p) => p.status === 'julgado').length}/{processos.length} julgados
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {processos.map((processo) => (
                <div
                  key={processo.id}
                  className={`rounded-lg border p-4 ${
                    processo.id === processoAtual?.id
                      ? 'border-blue-500 bg-blue-50'
                      : processo.status === 'pendente' && sessaoIniciada
                      ? 'cursor-pointer hover:bg-gray-50'
                      : ''
                  }`}
                  onClick={() =>
                    sessaoIniciada && processo.status === 'pendente' && handleSelecionarProcesso(processo)
                  }
                >
                  <div className="flex items-start justify-between">
                    <div>
                      <div className="flex items-center gap-2">
                        <span className="font-medium">{processo.protocolo}</span>
                        {getTipoBadge(processo.tipo)}
                        {getStatusBadge(processo.status, processo.decisao)}
                      </div>
                      <p className="text-sm text-gray-600 mt-1">{processo.titulo}</p>
                      <p className="text-xs text-gray-500 mt-1">Relator: {processo.relatorNome}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>

        {/* Votacao (quando um processo esta selecionado) */}
        {processoAtual && sessaoIniciada && (
          <Card className="lg:col-span-3">
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Gavel className="h-5 w-5" />
                Votacao - {processoAtual.protocolo}
              </CardTitle>
              <CardDescription>{processoAtual.titulo}</CardDescription>
            </CardHeader>
            <CardContent>
              {!votacaoAberta ? (
                <div className="text-center py-8">
                  <p className="text-gray-500 mb-4">
                    Relator: {processoAtual.relatorNome}. Clique para iniciar a votacao.
                  </p>
                  <Button onClick={handleIniciarVotacao}>
                    <Play className="mr-2 h-4 w-4" />
                    Iniciar Votacao
                  </Button>
                </div>
              ) : (
                <div className="space-y-6">
                  <div className="space-y-2">
                    <Label>Fundamentacao da Decisao</Label>
                    <textarea
                      className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      placeholder="Digite a fundamentacao..."
                      value={fundamentacao}
                      onChange={(e) => setFundamentacao(e.target.value)}
                    />
                  </div>

                  <div className="space-y-3">
                    <Label>Votos dos Membros</Label>
                    {membrosPresentes.map((membro) => (
                      <div
                        key={membro.id}
                        className="flex items-center justify-between rounded-lg border p-3"
                      >
                        <span className="font-medium">{membro.nome}</span>
                        <div className="flex gap-2">
                          <Button
                            variant={votos[membro.id] === 'deferido' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => handleRegistrarVoto(membro.id, 'deferido')}
                            className={votos[membro.id] === 'deferido' ? 'bg-green-600 hover:bg-green-700' : ''}
                          >
                            <CheckCircle className="mr-1 h-3 w-3" />
                            Deferir
                          </Button>
                          <Button
                            variant={votos[membro.id] === 'indeferido' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => handleRegistrarVoto(membro.id, 'indeferido')}
                            className={votos[membro.id] === 'indeferido' ? 'bg-red-600 hover:bg-red-700' : ''}
                          >
                            <XCircle className="mr-1 h-3 w-3" />
                            Indeferir
                          </Button>
                          <Button
                            variant={votos[membro.id] === 'abstencao' ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => handleRegistrarVoto(membro.id, 'abstencao')}
                          >
                            Abster
                          </Button>
                        </div>
                      </div>
                    ))}
                  </div>

                  <div className="flex justify-between items-center pt-4 border-t">
                    <div>
                      <span className="text-sm text-gray-500">
                        Votos registrados: {Object.keys(votos).length}/{membrosPresentes.length}
                      </span>
                    </div>
                    <Button
                      onClick={handleFinalizarVotacao}
                      disabled={Object.keys(votos).length < membrosPresentes.length}
                    >
                      <CheckCircle className="mr-2 h-4 w-4" />
                      Finalizar Votacao
                    </Button>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  )
}
