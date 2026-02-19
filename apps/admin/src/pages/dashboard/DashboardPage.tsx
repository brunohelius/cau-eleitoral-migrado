import { useQuery } from '@tanstack/react-query'
import { Vote, Users, AlertTriangle, Flag, Calendar } from 'lucide-react'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { eleicoesService } from '@/services/eleicoes'

const stats = [
  { name: 'Eleições Ativas', value: '3', icon: Vote, color: 'text-blue-600' },
  { name: 'Chapas Registradas', value: '12', icon: Users, color: 'text-green-600' },
  { name: 'Denuncias Pendentes', value: '5', icon: AlertTriangle, color: 'text-yellow-600' },
  { name: 'Impugnações', value: '2', icon: Flag, color: 'text-red-600' },
]

export function DashboardPage() {
  const { data: eleicoes, isLoading } = useQuery({
    queryKey: ['eleições-ativas'],
    queryFn: eleicoesService.getAtivas,
  })

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-600">Visao geral do sistema eleitoral</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {stats.map((stat) => (
          <Card key={stat.name}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">{stat.name}</CardTitle>
              <stat.icon className={`h-4 w-4 ${stat.color}`} />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stat.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Eleicoes Ativas */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="h-5 w-5" />
            Eleições em Andamento
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <p className="text-gray-500">Carregando...</p>
          ) : eleicoes && eleicoes.length > 0 ? (
            <div className="space-y-4">
              {eleicoes.map((eleicao) => (
                <div
                  key={eleicao.id}
                  className="flex items-center justify-between p-4 border rounded-lg"
                >
                  <div>
                    <h3 className="font-medium">{eleicao.nome}</h3>
                    <p className="text-sm text-gray-500">
                      {eleicao.totalChapas} chapas registradas
                    </p>
                  </div>
                  <div className="text-right">
                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Em andamento
                    </span>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-500">Nenhuma eleição ativa no momento.</p>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
