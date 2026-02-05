import { Calendar, Users } from 'lucide-react'

export function EleicoesPublicPage() {
  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Eleicoes</h1>
        <p className="text-gray-600 mt-2">Acompanhe as eleicoes em andamento e seus resultados.</p>
      </div>

      {/* Eleicoes Ativas */}
      <section>
        <h2 className="text-xl font-semibold mb-4">Eleicoes em Andamento</h2>
        <div className="grid gap-4">
          <div className="bg-white p-6 rounded-lg shadow-sm border">
            <div className="flex justify-between items-start">
              <div>
                <h3 className="text-lg font-semibold">Eleicao Ordinaria 2024</h3>
                <p className="text-gray-600 mt-1">CAU/BR - Conselho Federal</p>
              </div>
              <span className="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                Em Votacao
              </span>
            </div>
            <div className="flex gap-6 mt-4 text-sm text-gray-500">
              <div className="flex items-center gap-2">
                <Calendar className="h-4 w-4" />
                <span>Votacao: 15/03 - 20/03</span>
              </div>
              <div className="flex items-center gap-2">
                <Users className="h-4 w-4" />
                <span>5 chapas registradas</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Eleicoes Anteriores */}
      <section>
        <h2 className="text-xl font-semibold mb-4">Eleicoes Anteriores</h2>
        <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="text-left py-3 px-4 font-medium">Eleicao</th>
                <th className="text-left py-3 px-4 font-medium">Periodo</th>
                <th className="text-left py-3 px-4 font-medium">Status</th>
                <th className="text-right py-3 px-4 font-medium">Acoes</th>
              </tr>
            </thead>
            <tbody>
              <tr className="border-t">
                <td className="py-3 px-4">Eleicao Ordinaria 2021</td>
                <td className="py-3 px-4">Mar/2021</td>
                <td className="py-3 px-4">
                  <span className="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">
                    Finalizada
                  </span>
                </td>
                <td className="py-3 px-4 text-right">
                  <button className="text-primary hover:underline text-sm">
                    Ver Resultado
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  )
}
