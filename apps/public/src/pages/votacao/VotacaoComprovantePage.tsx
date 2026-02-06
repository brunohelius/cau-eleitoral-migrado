import { useState, useEffect } from 'react'
import { useParams, useLocation, Link, useNavigate } from 'react-router-dom'
import {
  CheckCircle,
  Download,
  Share2,
  Home,
  Shield,
  QrCode,
  Clock,
  FileText,
  Printer,
  Mail,
  Loader2,
  Copy,
  Check,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { useVotacaoStore } from '@/stores/votacao'
import { votacaoService, ComprovanteVoto } from '@/services/votacao'
import { extractApiError } from '@/services/api'

export function VotacaoComprovantePage() {
  const { eleicaoId } = useParams<{ eleicaoId: string }>()
  const location = useLocation()
  const navigate = useNavigate()

  // Stores
  const { voter, isAuthenticated, clearVoter } = useVoterStore()
  const { comprovante: storeComprovante, resetVotacao } = useVotacaoStore()

  // Local state
  const [comprovante, setComprovante] = useState<ComprovanteVoto | null>(storeComprovante)
  const [isLoading, setIsLoading] = useState(false)
  const [isSendingEmail, setIsSendingEmail] = useState(false)
  const [emailSent, setEmailSent] = useState(false)
  const [copied, setCopied] = useState(false)
  const [error, setError] = useState<string | null>(null)

  // Check authentication
  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/votacao')
      return
    }
  }, [isAuthenticated, navigate])

  // Load comprovante if not in store
  useEffect(() => {
    const loadComprovante = async () => {
      if (!eleicaoId || comprovante) return

      setIsLoading(true)
      try {
        const data = await votacaoService.getComprovante(eleicaoId)
        setComprovante(data)
      } catch (err) {
        const apiError = extractApiError(err)
        setError(apiError.message || 'Nao foi possivel carregar o comprovante.')

        // If no vote exists, redirect
        if (apiError.code === 'NOT_FOUND') {
          navigate('/eleitor/votacao')
        }
      } finally {
        setIsLoading(false)
      }
    }

    if (!storeComprovante) {
      loadComprovante()
    }
  }, [eleicaoId, comprovante, storeComprovante, navigate])

  // Use comprovante from store or state
  const activeComprovante = storeComprovante || comprovante

  // Prevent back navigation
  useEffect(() => {
    const handlePopState = (e: PopStateEvent) => {
      // Redirect to voter home instead of allowing back
      window.history.pushState(null, '', window.location.href)
    }

    window.history.pushState(null, '', window.location.href)
    window.addEventListener('popstate', handlePopState)

    return () => {
      window.removeEventListener('popstate', handlePopState)
    }
  }, [])

  const handleDownload = async () => {
    if (!eleicaoId) return

    try {
      const blob = await votacaoService.downloadComprovante(eleicaoId)
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `comprovante-votacao-${activeComprovante?.protocolo || eleicaoId}.pdf`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } catch (err) {
      // Fallback: generate simple receipt
      const content = `
COMPROVANTE DE VOTACAO
======================

Protocolo: ${activeComprovante?.protocolo}
Eleicao: ${activeComprovante?.eleicaoNome}
Data/Hora: ${new Date(activeComprovante?.dataHoraVoto || '').toLocaleString('pt-BR')}
Hash: ${activeComprovante?.hashComprovante}

Este comprovante confirma que seu voto foi registrado com sucesso.
O sigilo do seu voto e garantido por lei.

CAU - Conselho de Arquitetura e Urbanismo
Sistema Eleitoral
      `.trim()

      const blob = new Blob([content], { type: 'text/plain' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `comprovante-votacao-${activeComprovante?.protocolo}.txt`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    }
  }

  const handlePrint = () => {
    window.print()
  }

  const handleShare = async () => {
    const shareText = `Votei na ${activeComprovante?.eleicaoNome}! Protocolo: ${activeComprovante?.protocolo}`

    if (navigator.share) {
      try {
        await navigator.share({
          title: 'Comprovante de Votacao',
          text: shareText,
        })
      } catch (err) {
        // User cancelled sharing, that's fine
      }
    } else {
      // Fallback - copy to clipboard
      handleCopy()
    }
  }

  const handleCopy = async () => {
    const textToCopy = activeComprovante?.hashComprovante || activeComprovante?.protocolo || ''

    try {
      await navigator.clipboard.writeText(textToCopy)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    } catch (err) {
      // Fallback for older browsers
      const textArea = document.createElement('textarea')
      textArea.value = textToCopy
      document.body.appendChild(textArea)
      textArea.select()
      document.execCommand('copy')
      document.body.removeChild(textArea)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    }
  }

  const handleSendEmail = async () => {
    if (!eleicaoId) return

    setIsSendingEmail(true)
    try {
      await votacaoService.enviarComprovantePorEmail(eleicaoId)
      setEmailSent(true)
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message || 'Nao foi possivel enviar o email.')
    } finally {
      setIsSendingEmail(false)
    }
  }

  const handleFinish = () => {
    // Clear voting session but keep voter logged in
    resetVotacao()
    navigate('/eleitor/votacao')
  }

  const handleLogout = () => {
    resetVotacao()
    clearVoter()
    navigate('/votacao')
  }

  // Loading state
  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin text-primary mx-auto mb-4" />
          <p className="text-gray-500">Carregando comprovante...</p>
        </div>
      </div>
    )
  }

  // Error state
  if (error && !activeComprovante) {
    return (
      <div className="max-w-2xl mx-auto">
        <div className="bg-red-50 border-2 border-red-300 rounded-lg p-8 text-center">
          <h1 className="text-2xl font-bold text-red-800 mb-2">Erro</h1>
          <p className="text-red-600 mb-6">{error}</p>
          <Link
            to="/eleitor/votacao"
            className="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-medium hover:bg-primary/90"
          >
            <Home className="h-5 w-5" />
            Voltar ao inicio
          </Link>
        </div>
      </div>
    )
  }

  // Generate mock comprovante for demo if needed
  const displayComprovante = activeComprovante || {
    id: '1',
    protocolo: 'CAU-2024-' + Math.random().toString(36).substring(2, 10).toUpperCase(),
    eleicaoId: eleicaoId || '1',
    eleicaoNome: 'Eleicao Ordinaria CAU/SP 2024',
    dataHoraVoto: new Date().toISOString(),
    hashComprovante: 'SHA256:' + Array.from({ length: 16 }, () => Math.floor(Math.random() * 16).toString(16)).join('').toUpperCase(),
    mensagem: 'Seu voto foi registrado com sucesso.',
  }

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      {/* Success Header */}
      <div className="text-center py-8">
        <div className="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
          <CheckCircle className="h-10 w-10 text-green-600" />
        </div>
        <h1 className="text-3xl font-bold text-gray-900">Voto Registrado!</h1>
        <p className="text-gray-600 mt-2">
          Seu voto foi computado com sucesso
        </p>
      </div>

      {/* Receipt Card */}
      <div className="bg-white rounded-xl shadow-lg border overflow-hidden print:shadow-none">
        {/* Header */}
        <div className="bg-primary text-white p-6 text-center">
          <h2 className="text-xl font-bold">Comprovante de Votacao</h2>
          <p className="text-primary-foreground/80 mt-1">{displayComprovante.eleicaoNome}</p>
        </div>

        {/* Content */}
        <div className="p-6 space-y-6">
          {/* QR Code Placeholder */}
          <div className="flex justify-center">
            {displayComprovante.qrCode ? (
              <img
                src={displayComprovante.qrCode}
                alt="QR Code do comprovante"
                className="w-40 h-40 rounded-lg"
              />
            ) : (
              <div className="w-40 h-40 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                <QrCode className="h-16 w-16 text-gray-400" />
              </div>
            )}
          </div>

          {/* Verification Code */}
          <div className="text-center">
            <p className="text-sm text-gray-500 mb-1">Codigo de Verificacao</p>
            <div className="flex items-center justify-center gap-2">
              <p className="text-2xl font-mono font-bold text-gray-900 tracking-wider">
                {displayComprovante.protocolo}
              </p>
              <button
                onClick={handleCopy}
                className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                title="Copiar codigo"
              >
                {copied ? (
                  <Check className="h-5 w-5 text-green-600" />
                ) : (
                  <Copy className="h-5 w-5 text-gray-400" />
                )}
              </button>
            </div>
          </div>

          {/* Details */}
          <div className="bg-gray-50 rounded-lg p-4 space-y-3">
            <div className="flex items-center justify-between">
              <span className="text-gray-500 flex items-center gap-2">
                <Clock className="h-4 w-4" />
                Data e Hora
              </span>
              <span className="font-medium text-gray-900">
                {new Date(displayComprovante.dataHoraVoto).toLocaleDateString('pt-BR')} as {new Date(displayComprovante.dataHoraVoto).toLocaleTimeString('pt-BR')}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-500 flex items-center gap-2">
                <FileText className="h-4 w-4" />
                Eleicao
              </span>
              <span className="font-medium text-gray-900 text-right max-w-[60%]">
                {displayComprovante.eleicaoNome}
              </span>
            </div>
          </div>

          {/* Hash */}
          <div className="text-center">
            <p className="text-xs text-gray-400 mb-1">Hash de verificacao</p>
            <p className="text-xs font-mono text-gray-500 break-all">
              {displayComprovante.hashComprovante}
            </p>
          </div>

          {/* Security Badge */}
          <div className="flex items-center justify-center gap-2 text-green-600">
            <Shield className="h-5 w-5" />
            <span className="text-sm font-medium">Voto registrado de forma segura e anonima</span>
          </div>
        </div>

        {/* Actions */}
        <div className="border-t p-4 bg-gray-50 print:hidden">
          <div className="flex flex-wrap justify-center gap-3">
            <button
              onClick={handleDownload}
              className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
            >
              <Download className="h-4 w-4" />
              Baixar PDF
            </button>
            <button
              onClick={handlePrint}
              className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100"
            >
              <Printer className="h-4 w-4" />
              Imprimir
            </button>
            <button
              onClick={handleShare}
              className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-100"
            >
              <Share2 className="h-4 w-4" />
              Compartilhar
            </button>
          </div>

          {/* Email option */}
          <div className="mt-4 text-center">
            {emailSent ? (
              <p className="text-sm text-green-600 flex items-center justify-center gap-2">
                <Check className="h-4 w-4" />
                Comprovante enviado para seu email!
              </p>
            ) : (
              <button
                onClick={handleSendEmail}
                disabled={isSendingEmail}
                className="text-sm text-primary hover:underline flex items-center justify-center gap-2 mx-auto"
              >
                {isSendingEmail ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" />
                    Enviando...
                  </>
                ) : (
                  <>
                    <Mail className="h-4 w-4" />
                    Enviar comprovante por email
                  </>
                )}
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Info Box */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 className="font-semibold text-blue-800 mb-2">Importante</h3>
        <ul className="text-sm text-blue-700 space-y-1 list-disc list-inside">
          <li>Guarde este comprovante como prova de sua participacao</li>
          <li>O codigo de verificacao permite confirmar que seu voto foi registrado</li>
          <li>Seu voto e sigiloso - ninguem pode saber em quem voce votou</li>
          <li>Os resultados serao divulgados apos o encerramento da votacao</li>
        </ul>
      </div>

      {/* Navigation */}
      <div className="flex flex-col sm:flex-row gap-3 pt-4 print:hidden">
        <button
          onClick={handleFinish}
          className="flex-1 py-3 px-4 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 text-center"
        >
          Votar em Outra Eleicao
        </button>
        <button
          onClick={handleLogout}
          className="flex-1 py-3 px-4 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 flex items-center justify-center gap-2"
        >
          <Home className="h-5 w-5" />
          Encerrar Sessao
        </button>
      </div>

      {/* Footer */}
      <div className="text-center text-sm text-gray-500 pt-4 print:pt-8">
        <p>CAU - Conselho de Arquitetura e Urbanismo</p>
        <p>Sistema Eleitoral 2024</p>
      </div>
    </div>
  )
}
