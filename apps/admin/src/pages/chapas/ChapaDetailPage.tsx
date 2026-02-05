import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

export function ChapaDetailPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Detalhes da Chapa</h1>
        <p className="text-gray-600">Visualize os detalhes da chapa</p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Informacoes da Chapa</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-gray-500">Pagina em construcao...</p>
        </CardContent>
      </Card>
    </div>
  )
}
