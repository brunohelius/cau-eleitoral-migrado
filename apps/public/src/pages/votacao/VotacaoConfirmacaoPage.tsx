import { useState, useEffect } from 'react'
import { useParams, useNavigate, useLocation, Link } from 'react-router-dom'
import {
  ArrowLeft,
  AlertTriangle,
  CheckCircle,
  Loader2,
  Shield,
  Vote,
  X,
  Ban,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { useVotacaoStore } from '@/stores/votacao'
import { votacaoService, TipoVoto } from '@/services/votacao'
import { extractApiError } from '@/services/api'

export function VotacaoConfirmacaoPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const navigate = useNavigate()
  const location = useLocation()

  // Stores
  const { voter, isAuthenticated, updateVoter, clearVoter } = useVoterStore()
  const {
    votoSelecionado,
    setComprovante,
    setSubmitting,
    isSubmitting,
    resetVotacao,
    setError: setStoreError,
  } = useVotacaoStore()

  // Local state
  const [showModal, setShowModal] = useState(false)
  const [error, setError] = useState<string | null>(null)

  // Get election name from navigation state
  const { eleicaoNome = 'Eleicao CAU 2024' } = location.state || {}

  // Check authentication and vote selection
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/votacao')
      return
    }

    if (!votoSelecionado) {
      navigate(`/eleitor/votacao/${eleicaoId}/cedula`)
      return
    }
  }, [isAuthenticated, votoSelecionado, navigate, eleicaoId])

  // Prevent back navigation after confirmation starts
  useEffect(() => {
    if (isSubmitting) {
      const handlePopState = (e: PopStateEvent) => {
        window.history.pushState(null, '', window.location.href)
      }

      window.history.pushState(null, '', window.location.href)
      window.addEventListener('popstate', handlePopState)

      return () => {
        window.removeEventListener('popstate', handlePopState)
      }
    }
  }, [isSubmitting])

  const handleConfirmarVoto = async () => {
    if (!eleicaoId || !votoSelecionado) return

    setShowModal(false)
    setSubmitting(true)
    setError(null)

    try {
      // Determine vote type
      let tipoVoto: 'chapa' | 'branco' | 'nulo'
      if (votoSelecionado.tipo === 'chapa') {
        tipoVoto = 'chapa'
      } else if (votoSelecionado.tipo === 'branco') {
        tipoVoto = 'branco'
      } else {
        tipoVoto = 'nulo'
      }

      // Submit vote via API
      const comprovante = await votacaoService.registrarVoto({
        eleicaoId,
        chapaId: votoSelecionado.chapaId,
        tipoVoto,
      })

      // Store receipt in state
      setComprovante(comprovante)

      // Update voter state to mark as voted
      updateVoter({ jaVotou: true })

      // Navigate to receipt page
      navigate(`/eleitor/votacao/${eleicaoId}/comprovante`, {
        replace: true, // Replace history to prevent going back
        state: {
          fromConfirmation: true,
        },
      })
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message || 'Erro ao registrar voto. Tente novamente.')
      setSubmitting(false)

      // If unauthorized, clear session and redirect
      if (apiError.code === 'UNAUTHORIZED' || apiError.code === '401') {
        clearVoter()
        resetVotacao()
        navigate('/votacao')
      }
    }
  }

  // Loading/Submitting state
  if (isSubmitting) {
    return (
      <div className="min-h-[60vh] flex flex-col items-center justify-center">
        <div className="text-center">
          <Loader2 className="h-16 w-16 animate-spin text-primary mx-auto mb-6" />
          <h1 className="text-2xl font-bold text-gray-900 mb-2">
            Registrando seu voto...
          </h1>
          <p className="text-gray-600">
            Por favor, aguarde. Nao feche esta pagina.
          </p>
        </div>

        {/* Security indicator */}
        <div className="mt-8 flex items-center gap-2 text-green-600">
          <Shield className="h-5 w-5" />
          <span className="text-sm font-medium">Conexao segura e criptografada</span>
        </div>
      </div>
    )
  }

  // Guard against missing data
  if (!votoSelecionado) {
    return null
  }

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link
          to={`/eleitor/votacao/${eleicaoId}/cedula`}
          className="p-2 hover:bg-gray-100 rounded-lg"
        >
          <ArrowLeft className="h-5 w-5" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Confirmar Voto</h1>
          <p className="text-gray-600">{eleicaoNome}</p>
        </div>
      </div>

      {/* Error Alert */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
            <div>
              <p className="font-medium text-red-800">Erro ao registrar voto</p>
              <p className="text-red-600 text-sm mt-1">{error}</p>
            </div>
          </div>
        </div>
      )}

      {/* Warning */}
      <div className="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-6">
        <div className="flex items-start gap-4">
          <AlertTriangle className="h-8 w-8 text-yellow-600 flex-shrink-0" />
          <div>
            <h2 className="text-lg font-bold text-yellow-800">Atencao!</h2>
            <p className="text-yellow-700 mt-1">
              Apos a confirmacao, seu voto sera registrado de forma definitiva e <strong>nao podera ser alterado</strong>.
              Revise sua escolha com atencao antes de confirmar.
            </p>
          </div>
        </div>
      </div>

      {/* Vote Summary */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <h3 className="text-sm font-medium text-gray-500 mb-4">Resumo do seu voto:</h3>

        <div className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
          {votoSelecionado.tipo === 'branco' ? (
            <>
              <div className="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                <span className="text-2xl font-bold text-gray-500">B</span>
              </div>
              <div>
                <p className="text-xl font-bold text-gray-700">Voto em Branco</p>
                <p className="text-gray-500">Voce optou por nao escolher nenhuma chapa</p>
              </div>
            </>
          ) : votoSelecionado.tipo === 'nulo' ? (
            <>
              <div className="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center">
                <Ban className="h-8 w-8 text-red-500" />
              </div>
              <div>
                <p className="text-xl font-bold text-red-600">Voto Nulo</p>
                <p className="text-gray-500">Voce optou por anular seu voto</p>
              </div>
            </>
          ) : (
            <>
              <div className="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center">
                {votoSelecionado.chapaNumero ? (
                  <span className="text-2xl font-bold text-primary">{votoSelecionado.chapaNumero}</span>
                ) : (
                  <Vote className="h-8 w-8 text-primary" />
                )}
              </div>
              <div>
                <p className="text-xl font-bold text-gray-900">{votoSelecionado.chapaNome}</p>
                <p className="text-gray-500">
                  {votoSelecionado.chapaNumero && `Chapa ${votoSelecionado.chapaNumero}`}
                </p>
              </div>
            </>
          )}
        </div>

        <div className="mt-6 space-y-3 text-sm">
          <div className="flex justify-between py-2 border-b">
            <span className="text-gray-500">Eleicao:</span>
            <span className="font-medium text-gray-900">{eleicaoNome}</span>
          </div>
          <div className="flex justify-between py-2 border-b">
            <span className="text-gray-500">Data:</span>
            <span className="font-medium text-gray-900">
              {new Date().toLocaleDateString('pt-BR')}
            </span>
          </div>
          <div className="flex justify-between py-2">
            <span className="text-gray-500">Horario:</span>
            <span className="font-medium text-gray-900">
              {new Date().toLocaleTimeString('pt-BR')}
            </span>
          </div>
        </div>
      </div>

      {/* Security Info */}
      <div className="bg-green-50 border border-green-200 rounded-lg p-4">
        <div className="flex items-start gap-3">
          <Shield className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
          <div className="text-sm">
            <p className="font-medium text-green-800">Voto Seguro e Sigiloso</p>
            <p className="text-green-700">
              Seu voto e protegido por criptografia. Nenhuma informacao pessoal sera vinculada ao seu voto.
              O sigilo do voto e garantido por lei.
            </p>
          </div>
        </div>
      </div>

      {/* Actions */}
      <div className="flex flex-col sm:flex-row gap-3 pt-4">
        <Link
          to={`/eleitor/votacao/${eleicaoId}/cedula`}
          className="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 text-center"
        >
          Voltar e Alterar
        </Link>
        <button
          onClick={() => setShowModal(true)}
          className="flex-1 py-3 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 flex items-center justify-center gap-2"
        >
          <CheckCircle className="h-5 w-5" />
          Confirmar Voto
        </button>
      </div>

      {/* Confirmation Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
          <div className="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-bold text-gray-900">Confirmar Voto</h2>
              <button
                onClick={() => setShowModal(false)}
                className="p-2 hover:bg-gray-100 rounded-lg"
              >
                <X className="h-5 w-5" />
              </button>
            </div>

            <p className="text-gray-600 mb-6">
              Voce tem certeza que deseja confirmar seu voto? Esta acao e <strong>irreversivel</strong>.
            </p>

            <div className="p-4 bg-gray-50 rounded-lg mb-6">
              <p className="text-sm text-gray-500">Seu voto:</p>
              <p className="font-bold text-gray-900">
                {votoSelecionado.tipo === 'branco' && 'Voto em Branco'}
                {votoSelecionado.tipo === 'nulo' && 'Voto Nulo'}
                {votoSelecionado.tipo === 'chapa' && votoSelecionado.chapaNome}
              </p>
            </div>

            <div className="flex gap-3">
              <button
                onClick={() => setShowModal(false)}
                className="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                onClick={handleConfirmarVoto}
                className="flex-1 py-2 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700"
              >
                Sim, Confirmar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
