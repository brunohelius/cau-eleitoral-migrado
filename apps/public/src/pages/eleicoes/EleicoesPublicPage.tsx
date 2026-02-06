import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { Calendar, Users, Loader2, AlertCircle, Vote } from 'lucide-react'
import {
  eleicoesPublicService,
  EleicaoPublica,
  StatusEleicao,
  getStatusLabel,
  getStatusColor,
  getTipoLabel,
} from '../../services/eleicoes'

export function EleicoesPublicPage() {
  const navigate = useNavigate()
  const [eleicoes, setEleicoes] = useState<EleicaoPublica[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    loadEleicoes()
  }, [])

  const loadEleicoes = async () => {
    try {
      setIsLoading(true)
      setError(null)
      const data = await eleicoesPublicService.getAll()
      setEleicoes(data)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Erro ao carregar eleicoes')
    } finally {
      setIsLoading(false)
    }
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando eleicoes...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-64">
        <AlertCircle className="h-12 w-12 text-red-500 mb-4" />
        <p className="text-gray-700 font-medium">Erro ao carregar eleicoes</p>
        <p className="text-gray-500 text-sm">{error}</p>
        <button
          onClick={loadEleicoes}
          className="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
        >
          Tentar novamente
        </button>
      </div>
    )
  }

  // Group elections by active vs finalized
  const eleicoesAtivas = eleicoes.filter(
    e => e.status !== StatusEleicao.FINALIZADA && e.status !== StatusEleicao.CANCELADA
  )
  const eleicoesConcluidas = eleicoes.filter(
    e => e.status === StatusEleicao.FINALIZADA
  )

  const formatDate = (dateStr?: string) => {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })
  }

  const formatPeriodo = (inicio?: string, fim?: string) => {
    if (!inicio || !fim) return '-'
    const d1 = new Date(inicio)
    const d2 = new Date(fim)
    return `${d1.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })} - ${d2.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })}`
  }

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Eleicoes</h1>
        <p className="text-gray-600 mt-2">Acompanhe as eleicoes em andamento e seus resultados.</p>
      </div>

      {/* Eleicoes Ativas */}
      {eleicoesAtivas.length > 0 && (
        <section>
          <h2 className="text-xl font-semibold mb-4">Eleicoes em Andamento</h2>
          <div className="grid gap-4">
            {eleicoesAtivas.map(eleicao => (
              <div key={eleicao.id} className="bg-white p-6 rounded-lg shadow-sm border">
                <div className="flex justify-between items-start">
                  <div>
                    <h3 className="text-lg font-semibold">{eleicao.nome}</h3>
                    <p className="text-gray-600 mt-1">
                      {getTipoLabel(eleicao.tipo)}
                      {eleicao.regionalNome ? ` - ${eleicao.regionalNome}` : ''}
                    </p>
                    {eleicao.descricao && (
                      <p className="text-gray-500 text-sm mt-2">{eleicao.descricao}</p>
                    )}
                  </div>
                  <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(eleicao.status)}`}>
                    {getStatusLabel(eleicao.status)}
                  </span>
                </div>
                <div className="flex flex-wrap gap-6 mt-4 text-sm text-gray-500">
                  <div className="flex items-center gap-2">
                    <Calendar className="h-4 w-4" />
                    <span>
                      {eleicao.dataVotacaoInicio
                        ? `Votacao: ${formatPeriodo(eleicao.dataVotacaoInicio, eleicao.dataVotacaoFim)}`
                        : `Periodo: ${formatPeriodo(eleicao.dataInicio, eleicao.dataFim)}`
                      }
                    </span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Users className="h-4 w-4" />
                    <span>{eleicao.totalChapas} chapa{eleicao.totalChapas !== 1 ? 's' : ''} registrada{eleicao.totalChapas !== 1 ? 's' : ''}</span>
                  </div>
                  {eleicao.totalEleitores > 0 && (
                    <div className="flex items-center gap-2">
                      <Vote className="h-4 w-4" />
                      <span>{eleicao.totalEleitores.toLocaleString()} eleitores</span>
                    </div>
                  )}
                </div>
                <div className="flex gap-3 mt-4">
                  {eleicao.status === StatusEleicao.EM_ANDAMENTO && (
                    <button
                      onClick={() => navigate('/votacao')}
                      className="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 text-sm font-medium"
                    >
                      Votar Agora
                    </button>
                  )}
                  <button
                    onClick={() => navigate(`/eleicoes/${eleicao.id}`)}
                    className="text-primary hover:underline text-sm font-medium"
                  >
                    Ver Detalhes
                  </button>
                </div>
              </div>
            ))}
          </div>
        </section>
      )}

      {/* Sem eleicoes ativas */}
      {eleicoesAtivas.length === 0 && eleicoesConcluidas.length === 0 && (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Vote className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500">Nenhuma eleicao encontrada</p>
        </div>
      )}

      {/* Eleicoes Anteriores */}
      {eleicoesConcluidas.length > 0 && (
        <section>
          <h2 className="text-xl font-semibold mb-4">Eleicoes Anteriores</h2>
          <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left py-3 px-4 font-medium">Eleicao</th>
                  <th className="text-left py-3 px-4 font-medium hidden sm:table-cell">Tipo</th>
                  <th className="text-left py-3 px-4 font-medium">Periodo</th>
                  <th className="text-left py-3 px-4 font-medium">Status</th>
                  <th className="text-right py-3 px-4 font-medium">Acoes</th>
                </tr>
              </thead>
              <tbody>
                {eleicoesConcluidas.map(eleicao => (
                  <tr key={eleicao.id} className="border-t">
                    <td className="py-3 px-4">
                      <div>
                        <span className="font-medium">{eleicao.nome}</span>
                        <span className="text-gray-500 text-sm block sm:hidden">
                          {getTipoLabel(eleicao.tipo)}
                        </span>
                      </div>
                    </td>
                    <td className="py-3 px-4 hidden sm:table-cell text-gray-600">
                      {getTipoLabel(eleicao.tipo)}
                    </td>
                    <td className="py-3 px-4 text-gray-600 text-sm">
                      {formatDate(eleicao.dataInicio)} - {formatDate(eleicao.dataFim)}
                    </td>
                    <td className="py-3 px-4">
                      <span className={`px-2 py-1 rounded text-sm ${getStatusColor(eleicao.status)}`}>
                        {getStatusLabel(eleicao.status)}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-right">
                      <button
                        onClick={() => navigate(`/eleicoes/${eleicao.id}/resultados`)}
                        className="text-primary hover:underline text-sm font-medium"
                      >
                        Ver Resultado
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>
      )}
    </div>
  )
}
