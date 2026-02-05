import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

export function ChapasPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Chapas</h1>
        <p className="text-gray-600">Gerencie as chapas eleitorais</p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Lista de Chapas</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-gray-500">Pagina em construcao...</p>
        </CardContent>
      </Card>
    </div>
  )
}
