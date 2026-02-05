import { useState } from 'react'
import { Link } from 'react-router-dom'
import {
  Vote,
  CheckCircle,
  Download,
  Eye,
  Calendar,
  Clock,
  Shield,
  Loader2,
  History,
} from 'lucide-react'

// Types
interface VotoHistorico {
  id: string
  eleicaoId: string
  eleicaoNome: string
  regional: string
  dataVoto: string
  codigoVerificacao: string
  status: 'registrado' | 'verificado'
}

// Mock data
const mockVotos: VotoHistorico[] = [
  {
    id: '1',
    eleicaoId: '1',
    eleicaoNome: 'Eleicao Ordinaria CAU/SP 2024',
    regional: 'CAU/SP',
    dataVoto: '2024-03-16T10:30:00',
    codigoVerificacao: 'CAU-2024-XYZ12345',
    status: 'registrado',
  },
  {
    id: '2',
    eleicaoId: '2',
    eleicaoNome: 'Eleicao Ordinaria CAU/BR 2024',
    regional: 'CAU/BR',
    dataVoto: '2024-03-16T11:45:00',
    codigoVerificacao: 'CAU-2024-ABC67890',
    status: 'registrado',
  },
  {
    id: '3',
    eleicaoId: '3',
    eleicaoNome: 'Eleicao Ordinaria CAU/SP 2021',
    regional: 'CAU/SP',
    dataVoto: '2021-03-18T09:15:00',
    codigoVerificacao: 'CAU-2021-DEF11223',
    status: 'verificado',
  },
  {
    id: '4',
    eleicaoId: '4',
    eleicaoNome: 'Eleicao Ordinaria CAU/BR 2021',
    regional: 'CAU/BR',
    dataVoto: '2021-03-18T14:20:00',
    codigoVerificacao: 'CAU-2021-GHI44556',
    status: 'verificado',
  },
]

export function MeusVotosPage() {
  const [isLoading, setIsLoading] = useState(false)
  const [verificationCode, setVerificationCode] = useState('')
  const [verificationResult, setVerificationResult] = useState<'success' | 'error' | null>(null)

  const handleVerify = async () => {
    if (!verificationCode.trim()) return

    setIsLoading(true)
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1500))

      // Check if code exists in our mock data
      const found = mockVotos.some(v => v.codigoVerificacao === verificationCode.toUpperCase())
      setVerificationResult(found ? 'success' : 'error')
    } finally {
      setIsLoading(false)
    }
  }

  const handleDownloadComprovante = (voto: VotoHistorico) => {
    // In a real app, generate PDF
    alert(`Baixando comprovante: ${voto.codigoVerificacao}`)
  }

  const currentYearVotes = mockVotos.filter(v => new Date(v.dataVoto).getFullYear() === 2024)
  const previousVotes = mockVotos.filter(v => new Date(v.dataVoto).getFullYear() < 2024)

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meus Votos</h1>
        <p className="text-gray-600 mt-1">Historico de participacao nas eleicoes</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-primary/10 rounded-lg">
              <Vote className="h-5 w-5 text-primary" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{mockVotos.length}</p>
              <p className="text-sm text-gray-500">Total de Votos</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-green-100 rounded-lg">
              <CheckCircle className="h-5 w-5 text-green-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{currentYearVotes.length}</p>
              <p className="text-sm text-gray-500">Este Ano</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-blue-100 rounded-lg">
              <History className="h-5 w-5 text-blue-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">{previousVotes.length}</p>
              <p className="text-sm text-gray-500">Anos Anteriores</p>
            </div>
          </div>
        </div>

        <div className="bg-white p-4 rounded-lg shadow-sm border">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-purple-100 rounded-lg">
              <Calendar className="h-5 w-5 text-purple-600" />
            </div>
            <div>
              <p className="text-2xl font-bold text-gray-900">2018</p>
              <p className="text-sm text-gray-500">Primeira Votacao</p>
            </div>
          </div>
        </div>
      </div>

      {/* Verification Section */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Shield className="h-5 w-5 text-primary" />
          <h2 className="text-lg font-semibold text-gray-900">Verificar Voto</h2>
        </div>
        <p className="text-gray-600 text-sm mb-4">
          Digite o codigo de verificacao do seu comprovante para confirmar que seu voto foi registrado corretamente.
        </p>

        <div className="flex gap-3">
          <input
            type="text"
            value={verificationCode}
            onChange={(e) => {
              setVerificationCode(e.target.value.toUpperCase())
              setVerificationResult(null)
            }}
            placeholder="Ex: CAU-2024-XYZ12345"
            className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary uppercase font-mono"
          />
          <button
            onClick={handleVerify}
            disabled={isLoading || !verificationCode.trim()}
            className="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 flex items-center gap-2"
          >
            {isLoading ? (
              <>
                <Loader2 className="h-4 w-4 animate-spin" />
                Verificando...
              </>
            ) : (
              'Verificar'
            )}
          </button>
        </div>

        {verificationResult === 'success' && (
          <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <CheckCircle className="h-5 w-5 text-green-600 flex-shrink-0" />
            <div>
              <p className="font-medium text-green-800">Voto verificado!</p>
              <p className="text-sm text-green-700">Seu voto foi registrado com sucesso no sistema.</p>
            </div>
          </div>
        )}

        {verificationResult === 'error' && (
          <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
            <Shield className="h-5 w-5 text-red-600 flex-shrink-0" />
            <div>
              <p className="font-medium text-red-800">Codigo nao encontrado</p>
              <p className="text-sm text-red-700">Verifique se o codigo foi digitado corretamente.</p>
            </div>
          </div>
        )}
      </div>

      {/* Current Year Votes */}
      {currentYearVotes.length > 0 && (
        <div>
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Eleicoes 2024</h2>
          <div className="space-y-4">
            {currentYearVotes.map(voto => (
              <VotoCard key={voto.id} voto={voto} onDownload={handleDownloadComprovante} />
            ))}
          </div>
        </div>
      )}

      {/* Previous Votes */}
      {previousVotes.length > 0 && (
        <div>
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Eleicoes Anteriores</h2>
          <div className="space-y-4">
            {previousVotes.map(voto => (
              <VotoCard key={voto.id} voto={voto} onDownload={handleDownloadComprovante} />
            ))}
          </div>
        </div>
      )}

      {/* Empty State */}
      {mockVotos.length === 0 && (
        <div className="bg-white rounded-lg shadow-sm border p-12 text-center">
          <Vote className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p className="text-gray-500 mb-4">Voce ainda nao participou de nenhuma eleicao</p>
          <Link
            to="/eleitor/votacao"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
          >
            Ver Eleicoes Disponiveis
          </Link>
        </div>
      )}
    </div>
  )
}

// Voto Card Component
interface VotoCardProps {
  voto: VotoHistorico
  onDownload: (voto: VotoHistorico) => void
}

function VotoCard({ voto, onDownload }: VotoCardProps) {
  return (
    <div className="bg-white rounded-lg shadow-sm border p-4 sm:p-6">
      <div className="flex flex-col sm:flex-row sm:items-center gap-4">
        {/* Icon */}
        <div className="p-3 bg-green-100 rounded-lg w-fit">
          <CheckCircle className="h-6 w-6 text-green-600" />
        </div>

        {/* Info */}
        <div className="flex-1">
          <h3 className="font-semibold text-gray-900">{voto.eleicaoNome}</h3>
          <div className="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
            <span className="flex items-center gap-1">
              <Calendar className="h-4 w-4" />
              {new Date(voto.dataVoto).toLocaleDateString('pt-BR')}
            </span>
            <span className="flex items-center gap-1">
              <Clock className="h-4 w-4" />
              {new Date(voto.dataVoto).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
            </span>
          </div>
          <p className="mt-2 text-sm">
            <span className="text-gray-500">Codigo: </span>
            <span className="font-mono font-medium text-gray-900">{voto.codigoVerificacao}</span>
          </p>
        </div>

        {/* Actions */}
        <div className="flex items-center gap-2">
          <Link
            to={`/eleitor/votacao/${voto.eleicaoId}/comprovante`}
            className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
            title="Ver Comprovante"
          >
            <Eye className="h-5 w-5" />
          </Link>
          <button
            onClick={() => onDownload(voto)}
            className="p-2 text-gray-500 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
            title="Baixar Comprovante"
          >
            <Download className="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  )
}
