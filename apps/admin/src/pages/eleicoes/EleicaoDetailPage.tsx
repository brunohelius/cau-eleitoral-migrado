import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Calendar, Users, Vote, Settings } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { eleicoesService } from '@/services/eleicoes'

export function EleicaoDetailPage() {
  const { id } = useParams<{ id: string }>()

  const { data: eleicao, isLoading } = useQuery({
    queryKey: ['eleicao', id],
    queryFn: () => eleicoesService.getById(id!),
    enabled: !!id,
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Carregando...</p>
      </div>
    )
  }

  if (!eleicao) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Eleição não encontrada.</p>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/eleicoes">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">{eleicao.nome}</h1>
          <p className="text-gray-600">Detalhes da eleição</p>
        </div>
      </div>

      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Chapas</CardTitle>
            <Users className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{eleicao.totalChapas}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total de Eleitores</CardTitle>
            <Vote className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{eleicao.totalEleitores}</div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Vagas</CardTitle>
            <Settings className="h-4 w-4 text-purple-600" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{eleicao.quantidadeVagas || '-'}</div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="h-5 w-5" />
            Informacoes da Eleicao
          </CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <dt className="text-sm font-medium text-gray-500">Ano</dt>
              <dd className="mt-1 text-sm text-gray-900">{eleicao.ano}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Regional</dt>
              <dd className="mt-1 text-sm text-gray-900">{eleicao.regionalNome || '-'}</dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Data de Início</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(eleicao.dataInicio).toLocaleDateString('pt-BR')}
              </dd>
            </div>
            <div>
              <dt className="text-sm font-medium text-gray-500">Data de Fim</dt>
              <dd className="mt-1 text-sm text-gray-900">
                {new Date(eleicao.dataFim).toLocaleDateString('pt-BR')}
              </dd>
            </div>
            {eleicao.dataVotacaoInicio && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Início da Votação</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(eleicao.dataVotacaoInicio).toLocaleDateString('pt-BR')}
                </dd>
              </div>
            )}
            {eleicao.dataVotacaoFim && (
              <div>
                <dt className="text-sm font-medium text-gray-500">Fim da Votação</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(eleicao.dataVotacaoFim).toLocaleDateString('pt-BR')}
                </dd>
              </div>
            )}
            <div>
              <dt className="text-sm font-medium text-gray-500">Descrição</dt>
              <dd className="mt-1 text-sm text-gray-900">{eleicao.descricao || '-'}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>
    </div>
  )
}
