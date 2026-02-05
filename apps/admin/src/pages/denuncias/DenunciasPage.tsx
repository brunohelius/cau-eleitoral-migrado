import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

export function DenunciasPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Denuncias</h1>
        <p className="text-gray-600">Gerencie as denuncias eleitorais</p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Lista de Denuncias</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-gray-500">Pagina em construcao...</p>
        </CardContent>
      </Card>
    </div>
  )
}
