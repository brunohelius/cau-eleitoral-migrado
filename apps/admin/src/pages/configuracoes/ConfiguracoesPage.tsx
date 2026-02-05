import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

export function ConfiguracoesPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Configuracoes</h1>
        <p className="text-gray-600">Configure o sistema</p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Configuracoes Gerais</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-gray-500">Pagina em construcao...</p>
        </CardContent>
      </Card>
    </div>
  )
}
