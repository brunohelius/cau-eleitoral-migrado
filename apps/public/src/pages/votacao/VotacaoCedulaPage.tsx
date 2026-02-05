import { useState } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import {
  ArrowLeft,
  Vote,
  User,
  Check,
  AlertTriangle,
  Loader2,
  Info,
  ChevronDown,
  ChevronUp,
} from 'lucide-react'

// Types
interface MembroChapa {
  nome: string
  cargo: string
}

interface ChapaVotacao {
  id: string
  numero: number
  nome: string
  slogan: string
  cor: string
  presidente: string
  vicePresidente: string
  membros: MembroChapa[]
}

// Mock data
const mockEleicao = {
  id: '1',
  nome: 'Eleicao Ordinaria CAU/SP 2024',
  descricao: 'Eleicao para renovacao dos cargos do Conselho Regional de Sao Paulo',
}

const mockChapas: ChapaVotacao[] = [
  {
    id: '1',
    numero: 1,
    nome: 'Chapa Renovacao',
    slogan: 'Por uma arquitetura mais inclusiva',
    cor: 'blue',
    presidente: 'Joao Silva',
    vicePresidente: 'Maria Santos',
    membros: [
      { nome: 'Carlos Oliveira', cargo: 'Diretor Financeiro' },
      { nome: 'Ana Costa', cargo: 'Diretora Tecnica' },
    ],
  },
  {
    id: '2',
    numero: 2,
    nome: 'Chapa Uniao',
    slogan: 'Unidos pela arquitetura',
    cor: 'green',
    presidente: 'Roberto Almeida',
    vicePresidente: 'Patricia Souza',
    membros: [
      { nome: 'Marcos Pereira', cargo: 'Diretor Financeiro' },
      { nome: 'Fernanda Lima', cargo: 'Diretora Tecnica' },
    ],
  },
  {
    id: '3',
    numero: 3,
    nome: 'Chapa Futuro',
    slogan: 'Construindo o amanha',
    cor: 'purple',
    presidente: 'Lucas Martins',
    vicePresidente: 'Camila Rocha',
    membros: [
      { nome: 'Andre Santos', cargo: 'Diretor Financeiro' },
      { nome: 'Beatriz Costa', cargo: 'Diretora Tecnica' },
    ],
  },
]

const colorConfig: Record<string, { bg: string; light: string; text: string; border: string; hover: string }> = {
  blue: { bg: 'bg-blue-600', light: 'bg-blue-100', text: 'text-blue-600', border: 'border-blue-600', hover: 'hover:border-blue-400' },
  green: { bg: 'bg-green-600', light: 'bg-green-100', text: 'text-green-600', border: 'border-green-600', hover: 'hover:border-green-400' },
  purple: { bg: 'bg-purple-600', light: 'bg-purple-100', text: 'text-purple-600', border: 'border-purple-600', hover: 'hover:border-purple-400' },
}

export function VotacaoCedulaPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const navigate = useNavigate()
  const [selectedChapa, setSelectedChapa] = useState<string | null>(null)
  const [votoBranco, setVotoBranco] = useState(false)
  const [expandedChapa, setExpandedChapa] = useState<string | null>(null)
  const [isLoading] = useState(false)

  const handleSelectChapa = (chapaId: string) => {
    setVotoBranco(false)
    setSelectedChapa(chapaId)
  }

  const handleVotoBranco = () => {
    setSelectedChapa(null)
    setVotoBranco(true)
  }

  const handleConfirmar = () => {
    if (selectedChapa || votoBranco) {
      navigate(`/eleitor/votacao/${eleicaoId}/confirmacao`, {
        state: {
          chapaId: selectedChapa,
          votoBranco,
          eleicaoNome: mockEleicao.nome,
          chapaNome: selectedChapa ? mockChapas.find(c => c.id === selectedChapa)?.nome : null,
        },
      })
    }
  }

  const toggleExpand = (chapaId: string) => {
    setExpandedChapa(expandedChapa === chapaId ? null : chapaId)
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link
          to="/eleitor/votacao"
          className="p-2 hover:bg-gray-100 rounded-lg"
        >
          <ArrowLeft className="h-5 w-5" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Cedula de Votacao</h1>
          <p className="text-gray-600">{mockEleicao.nome}</p>
        </div>
      </div>

      {/* Instructions */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Instrucoes</p>
            <p className="text-blue-700">
              Selecione uma chapa para votar ou escolha a opcao de voto em branco.
              Revise sua escolha antes de confirmar.
            </p>
          </div>
        </div>
      </div>

      {/* Chapas */}
      <div className="space-y-4">
        <h2 className="text-lg font-semibold text-gray-900">Escolha sua opcao:</h2>

        {mockChapas.map((chapa) => {
          const colors = colorConfig[chapa.cor] || colorConfig.blue
          const isSelected = selectedChapa === chapa.id
          const isExpanded = expandedChapa === chapa.id

          return (
            <div
              key={chapa.id}
              className={`bg-white rounded-lg shadow-sm border-2 transition-all ${
                isSelected
                  ? `${colors.border} ring-2 ring-offset-2 ring-${chapa.cor}-500`
                  : `border-gray-200 ${colors.hover}`
              }`}
            >
              {/* Main Content */}
              <button
                onClick={() => handleSelectChapa(chapa.id)}
                className="w-full p-4 sm:p-6 text-left"
              >
                <div className="flex items-center gap-4">
                  {/* Number Badge */}
                  <div className={`w-14 h-14 ${colors.light} rounded-lg flex items-center justify-center flex-shrink-0`}>
                    <span className={`text-2xl font-bold ${colors.text}`}>{chapa.numero}</span>
                  </div>

                  {/* Info */}
                  <div className="flex-1 min-w-0">
                    <h3 className="text-lg font-bold text-gray-900">{chapa.nome}</h3>
                    <p className="text-gray-600 text-sm italic">"{chapa.slogan}"</p>
                    <p className="text-gray-500 text-sm mt-1">
                      Presidente: {chapa.presidente}
                    </p>
                  </div>

                  {/* Selection Indicator */}
                  <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${
                    isSelected
                      ? `${colors.bg} border-transparent`
                      : 'border-gray-300'
                  }`}>
                    {isSelected && <Check className="h-4 w-4 text-white" />}
                  </div>
                </div>
              </button>

              {/* Expand Button */}
              <button
                onClick={(e) => {
                  e.stopPropagation()
                  toggleExpand(chapa.id)
                }}
                className="w-full px-4 sm:px-6 py-2 border-t flex items-center justify-center gap-2 text-sm text-gray-500 hover:bg-gray-50"
              >
                {isExpanded ? (
                  <>
                    <ChevronUp className="h-4 w-4" />
                    Ocultar composicao
                  </>
                ) : (
                  <>
                    <ChevronDown className="h-4 w-4" />
                    Ver composicao da chapa
                  </>
                )}
              </button>

              {/* Expanded Content */}
              {isExpanded && (
                <div className="px-4 sm:px-6 pb-4 border-t">
                  <div className="pt-4 space-y-3">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                        <User className="h-4 w-4 text-gray-400" />
                        <div>
                          <p className="text-xs text-gray-500">Presidente</p>
                          <p className="text-sm font-medium">{chapa.presidente}</p>
                        </div>
                      </div>
                      <div className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                        <User className="h-4 w-4 text-gray-400" />
                        <div>
                          <p className="text-xs text-gray-500">Vice-Presidente</p>
                          <p className="text-sm font-medium">{chapa.vicePresidente}</p>
                        </div>
                      </div>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      {chapa.membros.map((membro, idx) => (
                        <div key={idx} className="flex items-center gap-2 p-2 bg-gray-50 rounded">
                          <User className="h-4 w-4 text-gray-400" />
                          <div>
                            <p className="text-xs text-gray-500">{membro.cargo}</p>
                            <p className="text-sm font-medium">{membro.nome}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              )}
            </div>
          )
        })}

        {/* Voto em Branco */}
        <div
          className={`bg-white rounded-lg shadow-sm border-2 transition-all cursor-pointer ${
            votoBranco
              ? 'border-gray-600 ring-2 ring-offset-2 ring-gray-500'
              : 'border-gray-200 hover:border-gray-400'
          }`}
          onClick={handleVotoBranco}
        >
          <div className="p-4 sm:p-6">
            <div className="flex items-center gap-4">
              <div className="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span className="text-xl font-bold text-gray-400">B</span>
              </div>
              <div className="flex-1">
                <h3 className="text-lg font-bold text-gray-500">Voto em Branco</h3>
                <p className="text-gray-400 text-sm">
                  Votar em branco significa nao escolher nenhuma chapa
                </p>
              </div>
              <div className={`w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 ${
                votoBranco
                  ? 'bg-gray-600 border-transparent'
                  : 'border-gray-300'
              }`}>
                {votoBranco && <Check className="h-4 w-4 text-white" />}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Warning */}
      {(selectedChapa || votoBranco) && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
            <div className="text-sm">
              <p className="font-medium text-yellow-800">Atencao</p>
              <p className="text-yellow-700">
                Apos confirmar, seu voto nao podera ser alterado. Certifique-se de que sua escolha esta correta.
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Actions */}
      <div className="flex flex-col sm:flex-row gap-3 pt-4">
        <Link
          to="/eleitor/votacao"
          className="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 text-center"
        >
          Cancelar
        </Link>
        <button
          onClick={handleConfirmar}
          disabled={!selectedChapa && !votoBranco}
          className="flex-1 py-3 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          {isLoading ? (
            <>
              <Loader2 className="h-5 w-5 animate-spin" />
              Processando...
            </>
          ) : (
            <>
              <Vote className="h-5 w-5" />
              Confirmar Escolha
            </>
          )}
        </button>
      </div>
    </div>
  )
}
