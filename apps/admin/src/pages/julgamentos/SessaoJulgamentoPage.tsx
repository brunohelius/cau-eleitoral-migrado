import { useState, useEffect } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
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
  Loader2,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'
import api from '@/services/api'

interface ProcessoPauta {
  id: string
  protocolo: string
  tipo: 'denuncia' | 'impugnacao' | 'recurso'
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

const mapTipoJulgamento = (tipo: number): ProcessoPauta['tipo'] => {
  // CAU.Eleitoral.Domain.Enums.TipoJulgamento
  switch (tipo) {
    case 0:
      return 'denuncia'
    case 1:
      return 'impugnacao'
    case 2:
    case 3:
    case 4:
    case 5:
      return 'recurso'
    default:
      return 'denuncia'
  }
}

const mapStatusJulgamento = (status: number): ProcessoPauta['status'] => {
  // CAU.Eleitoral.Domain.Enums.StatusJulgamento
  switch (status) {
    case 0:
      return 'pendente'
    case 1:
      return 'em_julgamento'
    case 4:
      return 'julgado'
    default:
      return 'pendente'
  }
}

const mapTipoMembro = (tipo: number): string => {
  // CAU.Eleitoral.Domain.Enums.TipoMembroComissao
  switch (tipo) {
    case 0:
      return 'Presidente'
    case 1:
      return 'Relator'
    case 2:
      return 'Revisor'
    case 3:
      return 'Vogal'
    case 4:
      return 'Suplente'
    default:
      return 'Membro'
  }
}

const mapVotoToApi = (voto: 'deferido' | 'indeferido' | 'abstencao'): number => {
  // CAU.Eleitoral.Domain.Enums.TipoVotoJulgamento
  switch (voto) {
    case 'deferido':
      return 0 // Procedente
    case 'indeferido':
      return 1 // Improcedente
    case 'abstencao':
      return 3 // Abstencao
  }
}

export function SessaoJulgamentoPage() {
  const navigate = useNavigate()
  const { toast } = useToast()

  const [sessaoIniciada, setSessaoIniciada] = useState(false)
  const [processoAtual, setProcessoAtual] = useState<ProcessoPauta | null>(null)
  const [votacaoAberta, setVotacaoAberta] = useState(false)
  const [votos, setVotos] = useState<Record<string, 'deferido' | 'indeferido' | 'abstencao'>>({})
  const [fundamentacao, setFundamentacao] = useState('')
  const [membros, setMembros] = useState<MembroComissao[]>([])
  const [processos, setProcessos] = useState<ProcessoPauta[]>([])
  const [isFinalizando, setIsFinalizando] = useState(false)

  // Load pending julgamentos as processos
  const { data: processosApi, isLoading: isLoadingProcessos } = useQuery({
    queryKey: ['julgamento-processos'],
    queryFn: async () => {
      try {
        const response = await api.get('/julgamento')
        const apiData = Array.isArray(response.data) ? response.data : []
        return apiData
          .filter((j: any) => j.status === 0 || j.status === 1) // Agendado or EmAndamento
          .map((j: any) => ({
            id: j.id,
            protocolo: j.protocolo || `JUL-${String(j.id).substring(0, 8).toUpperCase()}`,
            tipo: mapTipoJulgamento(j.tipo),
            titulo: j.ementa || 'Processo eleitoral',
            relatorNome: j.relatorNome || 'Nao definido',
            status: mapStatusJulgamento(j.status),
          })) as ProcessoPauta[]
      } catch {
        return [] as ProcessoPauta[]
      }
    },
  })

  // Load commission members for the current comissao (derived from selected or first julgamento)
  const membrosJulgamentoId = processoAtual?.id || processosApi?.[0]?.id
  const { data: membrosApi, isLoading: isLoadingMembros } = useQuery({
    queryKey: ['membros-comissao', membrosJulgamentoId],
    queryFn: async () => {
      if (!membrosJulgamentoId) return [] as MembroComissao[]
      try {
        const response = await api.get(`/julgamento/${membrosJulgamentoId}/membros`)
        const apiData = Array.isArray(response.data) ? response.data : []
        return apiData
          .filter((m: any) => m.ativo !== false)
          .map((m: any) => ({
            id: m.id,
            nome: m.nome || 'Membro',
            cargo: mapTipoMembro(m.tipo),
            presente: true,
          })) as MembroComissao[]
      } catch {
        return [] as MembroComissao[]
      }
    },
    enabled: !!membrosJulgamentoId,
  })

  // Initialize local state from API data
  useEffect(() => {
    if (!membrosApi) return
    setMembros((prev) => {
      const presenceById = new Map(prev.map((m) => [m.id, m.presente]))
      return membrosApi.map((m) => ({
        ...m,
        presente: presenceById.get(m.id) ?? m.presente,
      }))
    })
  }, [membrosApi])

  useEffect(() => {
    if (processosApi && processosApi.length > 0) setProcessos(processosApi)
  }, [processosApi])

  const membrosPresentes = membros.filter((m) => m.presente)
  const quorum = membros.length > 0 && membrosPresentes.length >= Math.ceil(membros.length / 2)

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

  const handleSelecionarProcesso = async (processo: ProcessoPauta) => {
    if (processo.status !== 'pendente') return
    setProcessoAtual(processo)
    setVotacaoAberta(false)
    setVotos({})
    setFundamentacao('')
    setProcessos((prev) =>
      prev.map((p) => (p.id === processo.id ? { ...p, status: 'em_julgamento' } : p))
    )

    try {
      await api.post(`/julgamento/${processo.id}/iniciar`)
    } catch (error: any) {
      setProcessos((prev) =>
        prev.map((p) => (p.id === processo.id ? { ...p, status: 'pendente' } : p))
      )
      toast({
        variant: 'destructive',
        title: 'Erro ao iniciar julgamento',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    }
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

  const handleFinalizarVotacao = async () => {
    if (!processoAtual) return

    const votosDeferidos = Object.values(votos).filter((v) => v === 'deferido').length
    const votosIndeferidos = Object.values(votos).filter((v) => v === 'indeferido').length

    if (votosDeferidos + votosIndeferidos === 0) {
      toast({
        variant: 'destructive',
        title: 'Votacao invalida',
        description: 'E necessario pelo menos um voto valido (deferir ou indeferir).',
      })
      return
    }

    const presidenteId = membros.find((m) => m.cargo === 'Presidente')?.id

    let tipoDecisaoApi = 1 // Maioria
    let decisao: 'deferida' | 'indeferida' = votosDeferidos > votosIndeferidos ? 'deferida' : 'indeferida'

    if (votosDeferidos === votosIndeferidos) {
      tipoDecisaoApi = 2 // VotoDesempate
      const votoPresidente = presidenteId ? votos[presidenteId] : undefined
      decisao = votoPresidente === 'deferido' ? 'deferida' : 'indeferida'
    } else if (votosDeferidos === 0 || votosIndeferidos === 0) {
      tipoDecisaoApi = 0 // Unanime
    }

    const tipoDecisaoTexto =
      tipoDecisaoApi === 0 ? 'UNANIME' : tipoDecisaoApi === 2 ? 'VOTO DE DESEMPATE' : 'MAIORIA'
    const resultadoTexto = decisao === 'deferida' ? 'PROCEDENTE' : 'IMPROCEDENTE'
    const decisaoTexto = `ACORDAM os membros da Comissao Julgadora, por ${tipoDecisaoTexto}, julgar ${resultadoTexto} o processo.`

    setIsFinalizando(true)
    try {
      for (const membro of membrosPresentes) {
        const voto = votos[membro.id]
        await api.post(`/julgamento/${processoAtual.id}/votos`, {
          membroId: membro.id,
          voto: mapVotoToApi(voto),
          fundamentacao: null,
        })
      }

      await api.post(`/julgamento/${processoAtual.id}/concluir`, {
        tipoDecisao: tipoDecisaoApi,
        decisao: decisaoTexto,
        fundamentacao: fundamentacao || null,
      })

      setProcessos((prev) =>
        prev.map((p) =>
          p.id === processoAtual.id ? { ...p, status: 'julgado', decisao } : p
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
    } catch (error: any) {
      toast({
        variant: 'destructive',
        title: 'Erro ao concluir julgamento',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    } finally {
      setIsFinalizando(false)
    }
  }

  const getTipoBadge = (tipo: string) => {
    const config: Record<string, { label: string; color: string }> = {
      denuncia: { label: 'Denuncia', color: 'bg-orange-100 text-orange-800' },
      impugnacao: { label: 'Impugnacao', color: 'bg-red-100 text-red-800' },
      recurso: { label: 'Recurso', color: 'bg-purple-100 text-purple-800' },
    }
    const item = config[tipo] || config.denuncia
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

  if (isLoadingMembros || isLoadingProcessos) {
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
            {membros.length > 0 ? (
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
            ) : (
              <p className="text-sm text-gray-500 text-center py-4">Nenhum membro da comissao encontrado.</p>
            )}
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
            {processos.length > 0 ? (
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
            ) : (
              <p className="text-sm text-gray-500 text-center py-8">Nenhum processo na pauta.</p>
            )}
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
                      disabled={
                        isFinalizando ||
                        Object.keys(votos).length < membrosPresentes.length
                      }
                    >
                      {isFinalizando ? (
                        <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      ) : (
                        <CheckCircle className="mr-2 h-4 w-4" />
                      )}
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
