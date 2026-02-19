import { useState } from 'react'
import {
  Megaphone,
  Save,
  Eye,
  Edit2,
  Loader2,
  CheckCircle,
  Info,
  AlertTriangle,
  Link as LinkIcon,
} from 'lucide-react'
import api, { extractApiError } from '../../services/api'
import { setTokenType } from '../../services/api'
import { useCandidatoStore } from '../../stores/candidato'

// Types
interface Plataforma {
  titulo: string
  descrição: string
  propostas: string[]
  video?: string
  imagem?: string
  publicada: boolean
  ultimaAtualizacao: string
}

const emptyPlataforma: Plataforma = {
  titulo: '',
  descrição: '',
  propostas: [],
  video: '',
  publicada: false,
  ultimaAtualizacao: new Date().toISOString(),
}

export function CandidatoPlataformaPage() {
  const candidato = useCandidatoStore((s) => s.candidato)
  const [plataforma, setPlataforma] = useState<Plataforma>(emptyPlataforma)
  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [saveError, setSaveError] = useState<string | null>(null)
  const [saveSuccess, setSaveSuccess] = useState(false)
  const [editedPlataforma, setEditedPlataforma] = useState<Plataforma>(plataforma)
  const [newProposta, setNewProposta] = useState('')

  const hasContent = plataforma.titulo.trim() !== '' || plataforma.propostas.length > 0

  const handleSave = async () => {
    setIsSaving(true)
    setSaveError(null)
    setSaveSuccess(false)
    try {
      // Try to persist to backend
      setTokenType('candidate')
      await api.put(`/candidato/plataforma`, {
        ...editedPlataforma,
        chapaId: candidato?.chapaId,
      })
      setSaveSuccess(true)
    } catch (err) {
      const apiErr = extractApiError(err)
      // Even if the API fails (e.g., endpoint not yet implemented),
      // we save locally and show a warning
      console.warn('Plataforma API save failed, saving locally:', apiErr.message)
      setSaveError('Dados salvos localmente. O servidor ainda nao suporta esta operação.')
    } finally {
      // Always update local state
      setPlataforma({
        ...editedPlataforma,
        ultimaAtualizacao: new Date().toISOString(),
      })
      setIsEditing(false)
      setIsSaving(false)
      // Clear success message after 3 seconds
      setTimeout(() => setSaveSuccess(false), 3000)
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
    setSaveError(null)
  }

  const handleStartEditing = () => {
    setEditedPlataforma(plataforma)
    setIsEditing(true)
    setSaveError(null)
    setSaveSuccess(false)
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
            onClick={handleStartEditing}
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            <Edit2 className="h-4 w-4" />
            {hasContent ? 'Editar Plataforma' : 'Criar Plataforma'}
          </button>
        )}
      </div>

      {/* Save success */}
      {saveSuccess && (
        <div className="p-4 rounded-lg border bg-green-50 border-green-200">
          <div className="flex items-center gap-3">
            <CheckCircle className="h-5 w-5 text-green-600" />
            <p className="font-medium text-green-800">Plataforma salva com sucesso!</p>
          </div>
        </div>
      )}

      {/* Save error (warning) */}
      {saveError && (
        <div className="p-4 rounded-lg border bg-yellow-50 border-yellow-200">
          <div className="flex items-center gap-3">
            <AlertTriangle className="h-5 w-5 text-yellow-600" />
            <p className="text-sm text-yellow-800">{saveError}</p>
          </div>
        </div>
      )}

      {/* Status */}
      {hasContent && !isEditing && (
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
      )}

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
                    Salvar Alterações
                  </>
                )}
              </button>
            </div>
          </div>
        ) : hasContent ? (
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
            {plataforma.propostas.length > 0 && (
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
            )}

            {/* Video */}
            {plataforma.video && (
              <div>
                <h3 className="text-lg font-semibold text-gray-900 mb-3">Video de Apresentação</h3>
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
        ) : (
          // Empty State
          <div className="p-12 text-center">
            <Megaphone className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-500 mb-2">Plataforma nao disponível</p>
            <p className="text-sm text-gray-400">
              Clique em "Criar Plataforma" para cadastrar suas propostas eleitorais.
            </p>
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
