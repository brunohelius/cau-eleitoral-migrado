import { useState } from 'react'
import { Lock } from 'lucide-react'

export function VotacaoPage() {
  const [step, setStep] = useState<'login' | 'votacao'>('login')
  const [cpf, setCpf] = useState('')
  const [cau, setCau] = useState('')

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault()
    // TODO: Implement authentication
    setStep('votacao')
  }

  if (step === 'login') {
    return (
      <div className="max-w-md mx-auto">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
            <Lock className="h-8 w-8 text-primary" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Area do Eleitor</h1>
          <p className="text-gray-600 mt-2">
            Identifique-se para acessar a votacao
          </p>
        </div>

        <form onSubmit={handleLogin} className="bg-white p-6 rounded-lg shadow-sm space-y-4">
          <div>
            <label htmlFor="cpf" className="block text-sm font-medium text-gray-700 mb-1">
              CPF
            </label>
            <input
              id="cpf"
              type="text"
              value={cpf}
              onChange={(e) => setCpf(e.target.value)}
              placeholder="000.000.000-00"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
              required
            />
          </div>
          <div>
            <label htmlFor="cau" className="block text-sm font-medium text-gray-700 mb-1">
              Registro CAU
            </label>
            <input
              id="cau"
              type="text"
              value={cau}
              onChange={(e) => setCau(e.target.value)}
              placeholder="A00000-0"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
              required
            />
          </div>
          <button
            type="submit"
            className="w-full bg-primary text-white py-2 rounded-md hover:bg-primary/90 font-medium"
          >
            Acessar
          </button>
        </form>
      </div>
    )
  }

  return (
    <div className="max-w-2xl mx-auto">
      <div className="text-center mb-8">
        <h1 className="text-2xl font-bold text-gray-900">Eleicao Ordinaria 2024</h1>
        <p className="text-gray-600 mt-2">Selecione uma chapa para votar</p>
      </div>

      <div className="space-y-4">
        {/* Chapa 1 */}
        <div className="bg-white p-6 rounded-lg shadow-sm border hover:border-primary cursor-pointer transition-colors">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
              <span className="text-2xl font-bold text-blue-600">1</span>
            </div>
            <div>
              <h3 className="text-lg font-semibold">Chapa Renovacao</h3>
              <p className="text-gray-600 text-sm">Presidente: Joao Silva</p>
            </div>
          </div>
        </div>

        {/* Chapa 2 */}
        <div className="bg-white p-6 rounded-lg shadow-sm border hover:border-primary cursor-pointer transition-colors">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
              <span className="text-2xl font-bold text-green-600">2</span>
            </div>
            <div>
              <h3 className="text-lg font-semibold">Chapa Uniao</h3>
              <p className="text-gray-600 text-sm">Presidente: Maria Santos</p>
            </div>
          </div>
        </div>

        {/* Voto em Branco */}
        <div className="bg-white p-6 rounded-lg shadow-sm border hover:border-gray-400 cursor-pointer transition-colors">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
              <span className="text-gray-400 font-bold">B</span>
            </div>
            <div>
              <h3 className="text-lg font-semibold text-gray-500">Voto em Branco</h3>
            </div>
          </div>
        </div>
      </div>

      <div className="mt-8 flex justify-center">
        <button className="bg-primary text-white px-8 py-3 rounded-lg font-medium hover:bg-primary/90">
          Confirmar Voto
        </button>
      </div>
    </div>
  )
}
