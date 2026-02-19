import { Link } from 'react-router-dom'
import {
  BarChart3,
  Vote,
  Users,
  FileText,
  Download,
  Calendar,
  TrendingUp,
  ArrowRight,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

const relatorioCategories = [
  {
    id: 'eleição',
    title: 'Relatórios de Eleição',
    description: 'Informações gerais sobre as eleições',
    icon: Calendar,
    color: 'bg-blue-100 text-blue-600',
    link: '/relatórios/eleição',
    reports: [
      'Resumo da Eleicao',
      'Chapas Participantes',
      'Calendario Eleitoral',
      'Historico de Eleicoes',
    ],
  },
  {
    id: 'votação',
    title: 'Relatórios de Votação',
    description: 'Estatisticas e resultados da votação',
    icon: Vote,
    color: 'bg-green-100 text-green-600',
    link: '/relatórios/votação',
    reports: [
      'Resultado da Votacao',
      'Participacao por Regional',
      'Votos por Hora',
      'Comparativo de Eleicoes',
    ],
  },
  {
    id: 'eleitores',
    title: 'Relatórios de Eleitores',
    description: 'Informações sobre o corpo eleitoral',
    icon: Users,
    color: 'bg-purple-100 text-purple-600',
    link: '/relatórios/eleitores',
    reports: [
      'Lista de Eleitores',
      'Eleitores por Regional',
      'Situacao Cadastral',
      'Quitacao Eleitoral',
    ],
  },
  {
    id: 'juridico',
    title: 'Relatórios Juridicos',
    description: 'Denuncias, impugnações e julgamentos',
    icon: FileText,
    color: 'bg-orange-100 text-orange-600',
    link: '/relatórios/juridico',
    reports: [
      'Denuncias por Status',
      'Impugnacoes Registradas',
      'Julgamentos Realizados',
      'Prazos e Recursos',
    ],
  },
]

const quickStats = [
  { label: 'Eleições Realizadas', value: '12', icon: Calendar, color: 'text-blue-600' },
  { label: 'Total de Votos', value: '45.832', icon: Vote, color: 'text-green-600' },
  { label: 'Eleitores Cadastrados', value: '58.741', icon: Users, color: 'text-purple-600' },
  { label: 'Participação Media', value: '78%', icon: TrendingUp, color: 'text-orange-600' },
]

export function RelatoriosPage() {
  const handleExportAll = () => {
    // Implementar exportacao
    console.log('Exportando todos os relatórios...')
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Relatórios</h1>
          <p className="text-gray-600">Visualize e exporte relatórios do sistema eleitoral</p>
        </div>
        <Button onClick={handleExportAll}>
          <Download className="mr-2 h-4 w-4" />
          Exportar Todos
        </Button>
      </div>

      {/* Quick Stats */}
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {quickStats.map((stat, index) => (
          <Card key={index}>
            <CardContent className="pt-6">
              <div className="flex items-center gap-4">
                <div className={`p-2 rounded-lg bg-gray-100`}>
                  <stat.icon className={`h-6 w-6 ${stat.color}`} />
                </div>
                <div>
                  <p className="text-2xl font-bold">{stat.value}</p>
                  <p className="text-sm text-gray-500">{stat.label}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Categorias de Relatorios */}
      <div className="grid gap-6 md:grid-cols-2">
        {relatorioCategories.map((category) => (
          <Card key={category.id} className="hover:shadow-md transition-shadow">
            <CardHeader>
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className={`p-2 rounded-lg ${category.color}`}>
                    <category.icon className="h-6 w-6" />
                  </div>
                  <div>
                    <CardTitle className="text-lg">{category.title}</CardTitle>
                    <CardDescription>{category.description}</CardDescription>
                  </div>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <ul className="space-y-2 mb-4">
                {category.reports.map((report, index) => (
                  <li key={index} className="flex items-center gap-2 text-sm text-gray-600">
                    <div className="h-1.5 w-1.5 rounded-full bg-gray-400" />
                    {report}
                  </li>
                ))}
              </ul>
              <Link to={category.link}>
                <Button variant="outline" className="w-full">
                  Acessar Relatórios
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Button>
              </Link>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Relatorios Recentes */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <BarChart3 className="h-5 w-5" />
            Relatórios Recentes
          </CardTitle>
          <CardDescription>Ultimos relatórios gerados</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="text-left py-3 px-4 font-medium">Relatório</th>
                  <th className="text-left py-3 px-4 font-medium">Tipo</th>
                  <th className="text-left py-3 px-4 font-medium">Data</th>
                  <th className="text-left py-3 px-4 font-medium">Gerado por</th>
                  <th className="text-right py-3 px-4 font-medium">Acao</th>
                </tr>
              </thead>
              <tbody>
                <tr className="border-b hover:bg-gray-50">
                  <td className="py-3 px-4">Resultado Final - Eleição CAU/SP 2024</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                      Votacao
                    </span>
                  </td>
                  <td className="py-3 px-4 text-sm text-gray-500">20/02/2024 15:30</td>
                  <td className="py-3 px-4 text-sm text-gray-500">Carlos Silva</td>
                  <td className="py-3 px-4 text-right">
                    <Button variant="ghost" size="sm">
                      <Download className="h-4 w-4" />
                    </Button>
                  </td>
                </tr>
                <tr className="border-b hover:bg-gray-50">
                  <td className="py-3 px-4">Lista de Eleitores - Regional SP</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                      Eleitores
                    </span>
                  </td>
                  <td className="py-3 px-4 text-sm text-gray-500">19/02/2024 10:00</td>
                  <td className="py-3 px-4 text-sm text-gray-500">Maria Santos</td>
                  <td className="py-3 px-4 text-right">
                    <Button variant="ghost" size="sm">
                      <Download className="h-4 w-4" />
                    </Button>
                  </td>
                </tr>
                <tr className="border-b hover:bg-gray-50">
                  <td className="py-3 px-4">Denúncias Julgadas - Fevereiro 2024</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                      Juridico
                    </span>
                  </td>
                  <td className="py-3 px-4 text-sm text-gray-500">18/02/2024 16:45</td>
                  <td className="py-3 px-4 text-sm text-gray-500">Pedro Almeida</td>
                  <td className="py-3 px-4 text-right">
                    <Button variant="ghost" size="sm">
                      <Download className="h-4 w-4" />
                    </Button>
                  </td>
                </tr>
                <tr className="hover:bg-gray-50">
                  <td className="py-3 px-4">Resumo Geral - Eleição CAU/SP 2024</td>
                  <td className="py-3 px-4">
                    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                      Eleicao
                    </span>
                  </td>
                  <td className="py-3 px-4 text-sm text-gray-500">17/02/2024 09:00</td>
                  <td className="py-3 px-4 text-sm text-gray-500">Carlos Silva</td>
                  <td className="py-3 px-4 text-right">
                    <Button variant="ghost" size="sm">
                      <Download className="h-4 w-4" />
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
