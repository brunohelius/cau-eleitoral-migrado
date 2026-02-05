import { useState } from 'react'
import {
  Megaphone,
  Save,
  Eye,
  Edit2,
  Loader2,
  CheckCircle,
  Info,
  Link as LinkIcon,
} from 'lucide-react'

// Types
interface Plataforma {
  titulo: string
  descricao: string
  propostas: string[]
  video?: string
  imagem?: string
  publicada: boolean
  ultimaAtualizacao: string
}

// Mock data
const mockPlataforma: Plataforma = {
  titulo: 'Por uma arquitetura mais inclusiva e sustentavel',
  descricao: 'Nossa chapa traz uma proposta de modernizacao e inclusao para o CAU, com foco em tecnologia, sustentabilidade e valorizacao profissional. Acreditamos que a arquitetura deve ser acessivel a todos e comprometida com o meio ambiente.',
  propostas: [
    'Modernizacao dos sistemas digitais do CAU para facilitar o atendimento aos profissionais',
    'Criacao de programas de capacitacao em sustentabilidade e novas tecnologias',
    'Estabelecimento de parcerias com universidades para promover a pesquisa em arquitetura',
    'Implementacao de politicas de inclusao e acessibilidade nos projetos',
    'Valorizacao profissional atraves de campanha de conscientizacao junto a sociedade',
  ],
  video: 'https://www.youtube.com/watch?v=example',
  publicada: true,
  ultimaAtualizacao: '2024-02-20T15:30:00',
}

export function CandidatoPlataformaPage() {
  const [plataforma, setPlataforma] = useState<Plataforma>(mockPlataforma)
  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [editedPlataforma, setEditedPlataforma] = useState<Plataforma>(plataforma)
  const [newProposta, setNewProposta] = useState('')

  const handleSave = async () => {
    setIsSaving(true)
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1500))
      setPlataforma({
        ...editedPlataforma,
        ultimaAtualizacao: new Date().toISOString(),
      })
      setIsEditing(false)
    } finally {
      setIsSaving(false)
    }
  }

  const handleAddProposta = () => {
    if (newProposta.trim()) {
      setEditedPlataforma({
        ...editedPlataforma,
        propostas: [...editedPlataforma.propostas, newProposta.trim()],
      })
      setNewProposta('')
    }
  }

  const handleRemoveProposta = (index: number) => {
    setEditedPlataforma({
      ...editedPlataforma,
      propostas: editedPlataforma.propostas.filter((_, i) => i !== index),
    })
  }

  const handleCancel = () => {
    setEditedPlataforma(plataforma)
    setIsEditing(false)
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Plataforma Eleitoral</h1>
          <p className="text-gray-600 mt-1">Apresente suas propostas aos eleitores</p>
        </div>
        {!isEditing && (
          <button
            onClick={() => setIsEditing(true)}
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            <Edit2 className="h-4 w-4" />
            Editar Plataforma
          </button>
        )}
      </div>

      {/* Status */}
      <div className={`p-4 rounded-lg border ${plataforma.publicada ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'}`}>
        <div className="flex items-center gap-3">
          {plataforma.publicada ? (
            <CheckCircle className="h-5 w-5 text-green-600" />
          ) : (
            <Info className="h-5 w-5 text-yellow-600" />
          )}
          <div>
            <p className={`font-medium ${plataforma.publicada ? 'text-green-800' : 'text-yellow-800'}`}>
              {plataforma.publicada ? 'Plataforma Publicada' : 'Rascunho'}
            </p>
            <p className={`text-sm ${plataforma.publicada ? 'text-green-700' : 'text-yellow-700'}`}>
              Ultima atualizacao: {new Date(plataforma.ultimaAtualizacao).toLocaleString('pt-BR')}
            </p>
          </div>
        </div>
      </div>

      {/* Content */}
      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        {isEditing ? (
          // Edit Mode
          <div className="p-6 space-y-6">
            {/* Titulo */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Titulo da Plataforma
              </label>
              <input
                type="text"
                value={editedPlataforma.titulo}
                onChange={(e) => setEditedPlataforma({ ...editedPlataforma, titulo: e.target.value })}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                placeholder="Ex: Por uma arquitetura mais inclusiva"
              />
            </div>

            {/* Descricao */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Descricao
              </label>
              <textarea
                value={editedPlataforma.descricao}
                onChange={(e) => setEditedPlataforma({ ...editedPlataforma, descricao: e.target.value })}
                rows={4}
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary resize-none"
                placeholder="Descreva sua plataforma eleitoral..."
              />
            </div>

            {/* Propostas */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Propostas
              </label>
              <div className="space-y-2">
                {editedPlataforma.propostas.map((proposta, index) => (
                  <div key={index} className="flex items-start gap-2">
                    <span className="flex-shrink-0 w-6 h-6 bg-primary/10 text-primary rounded-full flex items-center justify-center text-sm font-medium">
                      {index + 1}
                    </span>
                    <input
                      type="text"
                      value={proposta}
                      onChange={(e) => {
                        const newPropostas = [...editedPlataforma.propostas]
                        newPropostas[index] = e.target.value
                        setEditedPlataforma({ ...editedPlataforma, propostas: newPropostas })
                      }}
                      className="flex-1 px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    />
                    <button
                      onClick={() => handleRemoveProposta(index)}
                      className="text-red-500 hover:text-red-700 p-1"
                    >
                      Remover
                    </button>
                  </div>
                ))}
                <div className="flex gap-2 mt-3">
                  <input
                    type="text"
                    value={newProposta}
                    onChange={(e) => setNewProposta(e.target.value)}
                    onKeyPress={(e) => e.key === 'Enter' && handleAddProposta()}
                    className="flex-1 px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-sm"
                    placeholder="Adicionar nova proposta..."
                  />
                  <button
                    onClick={handleAddProposta}
                    className="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm"
                  >
                    Adicionar
                  </button>
                </div>
              </div>
            </div>

            {/* Video */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Link do Video (opcional)
              </label>
              <div className="relative">
                <LinkIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                <input
                  type="url"
                  value={editedPlataforma.video || ''}
                  onChange={(e) => setEditedPlataforma({ ...editedPlataforma, video: e.target.value })}
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                  placeholder="https://www.youtube.com/watch?v=..."
                />
              </div>
            </div>

            {/* Actions */}
            <div className="flex gap-3 pt-4 border-t">
              <button
                onClick={handleCancel}
                className="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                onClick={handleSave}
                disabled={isSaving}
                className="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 flex items-center gap-2 disabled:opacity-50"
              >
                {isSaving ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" />
                    Salvando...
                  </>
                ) : (
                  <>
                    <Save className="h-4 w-4" />
                    Salvar Alteracoes
                  </>
                )}
              </button>
            </div>
          </div>
        ) : (
          // View Mode
          <div className="p-6 space-y-6">
            {/* Titulo */}
            <div>
              <div className="flex items-center gap-2 mb-2">
                <Megaphone className="h-5 w-5 text-primary" />
                <h2 className="text-xl font-bold text-gray-900">{plataforma.titulo}</h2>
              </div>
              <p className="text-gray-600 leading-relaxed">{plataforma.descricao}</p>
            </div>

            {/* Propostas */}
            <div>
              <h3 className="text-lg font-semibold text-gray-900 mb-3">Nossas Propostas</h3>
              <div className="space-y-3">
                {plataforma.propostas.map((proposta, index) => (
                  <div key={index} className="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span className="flex-shrink-0 w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center text-sm font-medium">
                      {index + 1}
                    </span>
                    <p className="text-gray-700">{proposta}</p>
                  </div>
                ))}
              </div>
            </div>

            {/* Video */}
            {plataforma.video && (
              <div>
                <h3 className="text-lg font-semibold text-gray-900 mb-3">Video de Apresentacao</h3>
                <a
                  href={plataforma.video}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-2 text-primary hover:underline"
                >
                  <Eye className="h-4 w-4" />
                  Assistir video
                </a>
              </div>
            )}
          </div>
        )}
      </div>

      {/* Preview Info */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-blue-800">Dica</p>
            <p className="text-blue-700">
              Sua plataforma eleitoral e visivel para todos os eleitores. Certifique-se de apresentar propostas claras e objetivas.
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
