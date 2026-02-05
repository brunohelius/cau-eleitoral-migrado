import { useState } from 'react'
import { useParams, useNavigate, useLocation, Link } from 'react-router-dom'
import {
  ArrowLeft,
  AlertTriangle,
  CheckCircle,
  Loader2,
  Shield,
  Vote,
  X,
} from 'lucide-react'

export function VotacaoConfirmacaoPage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const navigate = useNavigate()
  const location = useLocation()
  const [isConfirming, setIsConfirming] = useState(false)
  const [showModal, setShowModal] = useState(false)

  // Get data from navigation state
  const { chapaId, votoBranco, eleicaoNome, chapaNome } = location.state || {}

  const handleConfirmarVoto = async () => {
    setShowModal(false)
    setIsConfirming(true)

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 2000))

      // Navigate to receipt
      navigate(`/eleitor/votacao/${eleicaoId}/comprovante`, {
        state: {
          chapaId,
          votoBranco,
          eleicaoNome,
          chapaNome,
          codigoVerificacao: 'CAU-2024-' + Math.random().toString(36).substring(2, 10).toUpperCase(),
          dataVoto: new Date().toISOString(),
        },
      })
    } catch (error) {
      console.error('Error confirming vote:', error)
      setIsConfirming(false)
    }
  }

  if (isConfirming) {
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
          {votoBranco ? (
            <>
              <div className="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                <span className="text-2xl font-bold text-gray-500">B</span>
              </div>
              <div>
                <p className="text-xl font-bold text-gray-700">Voto em Branco</p>
                <p className="text-gray-500">Voce optou por nao escolher nenhuma chapa</p>
              </div>
            </>
          ) : (
            <>
              <div className="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center">
                <Vote className="h-8 w-8 text-primary" />
              </div>
              <div>
                <p className="text-xl font-bold text-gray-900">{chapaNome}</p>
                <p className="text-gray-500">Chapa selecionada</p>
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
                {votoBranco ? 'Voto em Branco' : chapaNome}
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
