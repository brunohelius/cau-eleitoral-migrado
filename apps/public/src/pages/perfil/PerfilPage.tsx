import { useState, useEffect, useCallback } from 'react'
import {
  User,
  Mail,
  Phone,
  MapPin,
  Shield,
  Bell,
  Lock,
  Save,
  Loader2,
  CheckCircle,
  AlertCircle,
  Edit2,
  RefreshCw,
} from 'lucide-react'
import { useVoterStore } from '@/stores/voter'
import { authService } from '@/services/auth'

// Helper to decode JWT payload without a library
function decodeJwtPayload(token: string): Record<string, unknown> | null {
  try {
    const parts = token.split('.')
    if (parts.length !== 3) return null
    // Base64url decode the payload
    const payload = parts[1]
      .replace(/-/g, '+')
      .replace(/_/g, '/')
    const decoded = atob(payload)
    return JSON.parse(decoded)
  } catch {
    return null
  }
}

// Types
interface EleitorProfile {
  id: string
  nome: string
  cpf: string
  cau: string
  email: string
  telefone: string
  regional: string
  tipo: string
  situacao: 'apto' | 'inapto' | 'pendente'
  notificacoesEmail: boolean
  notificacoesSms: boolean
}

const situacaoConfig = {
  apto: { label: 'Apto a Votar', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  inapto: { label: 'Inapto', color: 'bg-red-100 text-red-800', icon: AlertCircle },
  pendente: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-800', icon: AlertCircle },
}

function maskCpf(cpf: string): string {
  if (!cpf) return ''
  // If already masked, return as is
  if (cpf.includes('*')) return cpf
  // Remove non-digits
  const digits = cpf.replace(/\D/g, '')
  if (digits.length !== 11) return cpf
  return `***.***.${digits.slice(6, 9)}-${digits.slice(9, 11)}`
}

export function PerfilPage() {
  const { voter, isAuthenticated } = useVoterStore()
  const updateVoter = useVoterStore((state) => state.updateVoter)

  const [eleitor, setEleitor] = useState<EleitorProfile | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)
  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [saveSuccess, setSaveSuccess] = useState(false)
  const [editedEmail, setEditedEmail] = useState('')
  const [editedTelefone, setEditedTelefone] = useState('')

  // Notification preferences (local state only, no backend support)
  const [notificacoesEmail, setNotificacoesEmail] = useState(true)
  const [notificacoesSms, setNotificacoesSms] = useState(false)

  const buildProfile = useCallback((): EleitorProfile | null => {
    if (!voter) return null

    // Try to extract extra claims from the JWT token
    const token = localStorage.getItem('voter-token')
    let extraClaims: Record<string, unknown> = {}
    if (token) {
      const decoded = decodeJwtPayload(token)
      if (decoded) {
        extraClaims = decoded
      }
    }

    return {
      id: voter.id,
      nome: voter.nome || (extraClaims.nome as string) || '',
      cpf: voter.cpf || (extraClaims.cpf as string) || '',
      cau: voter.registroCAU || (extraClaims.registroCAU as string) || '',
      email: voter.email || (extraClaims.email as string) || '',
      telefone: (extraClaims.telefone as string) || '',
      regional: voter.regional || (extraClaims.regional as string) || '',
      tipo: (extraClaims.tipo as string) || 'Eleitor',
      situacao: voter.podeVotar ? 'apto' : 'inapto',
      notificacoesEmail: true,
      notificacoesSms: false,
    }
  }, [voter])

  const loadProfile = useCallback(async () => {
    setIsLoading(true)
    setError(null)

    try {
      // First try to refresh voter info from the server
      const freshInfo = await authService.getEleitorInfo()
      if (freshInfo) {
        // Update the store with fresh data
        updateVoter(freshInfo)
      }
    } catch (err) {
      // If API call fails, we still have data from the store/token
      console.warn('Nao foi possivel atualizar dados do servidor:', err)
    }

    // Build profile from current voter data (which may have been updated)
    const profile = buildProfile()
    if (profile) {
      setEleitor(profile)
      setEditedEmail(profile.email)
      setEditedTelefone(profile.telefone)
    } else {
      setError('Nao foi possivel carregar os dados do perfil. Faca login novamente.')
    }

    setIsLoading(false)
  }, [buildProfile, updateVoter])

  useEffect(() => {
    if (isAuthenticated && voter) {
      loadProfile()
    } else {
      // Build from whatever we have immediately
      const profile = buildProfile()
      if (profile) {
        setEleitor(profile)
        setEditedEmail(profile.email)
        setEditedTelefone(profile.telefone)
      }
      setIsLoading(false)
      if (!voter) {
        setError('Nao foi possivel carregar os dados do perfil. Faca login novamente.')
      }
    }
  }, [])

  const handleSave = async () => {
    setIsSaving(true)
    setSaveSuccess(false)
    try {
      // Simulate save (no backend endpoint for profile update)
      await new Promise(resolve => setTimeout(resolve, 1000))
      setEleitor(prev => prev ? {
        ...prev,
        email: editedEmail,
        telefone: editedTelefone,
      } : null)
      setIsEditing(false)
      setSaveSuccess(true)
      // Auto-hide success message after 3 seconds
      setTimeout(() => setSaveSuccess(false), 3000)
    } finally {
      setIsSaving(false)
    }
  }

  const toggleNotification = (type: 'email' | 'sms') => {
    if (type === 'email') {
      setNotificacoesEmail(prev => !prev)
    } else {
      setNotificacoesSms(prev => !prev)
    }
  }

  // Loading state
  if (isLoading) {
    return (
      <div className="flex flex-col items-center justify-center py-20">
        <Loader2 className="h-8 w-8 animate-spin text-primary mb-4" />
        <p className="text-gray-600">Carregando perfil...</p>
      </div>
    )
  }

  // Error state with no data
  if (error && !eleitor) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meu Perfil</h1>
          <p className="text-gray-600 mt-1">Visualize e atualize suas informacoes</p>
        </div>
        <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
          <AlertCircle className="h-8 w-8 text-red-500 mx-auto mb-3" />
          <p className="text-red-700 font-medium">{error}</p>
          <button
            onClick={loadProfile}
            className="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            <RefreshCw className="h-4 w-4" />
            Tentar novamente
          </button>
        </div>
      </div>
    )
  }

  if (!eleitor) return null

  const situacao = situacaoConfig[eleitor.situacao]
  const SituacaoIcon = situacao.icon

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meu Perfil</h1>
        <p className="text-gray-600 mt-1">Visualize e atualize suas informacoes</p>
      </div>

      {/* Success Toast */}
      {saveSuccess && (
        <div className="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
          <CheckCircle className="h-5 w-5 text-green-600 flex-shrink-0" />
          <p className="text-green-700">Dados atualizados com sucesso!</p>
        </div>
      )}

      {/* Error banner (when we have partial data) */}
      {error && eleitor && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-center gap-3">
          <AlertCircle className="h-5 w-5 text-yellow-600 flex-shrink-0" />
          <p className="text-yellow-700 text-sm">{error}</p>
        </div>
      )}

      {/* Profile Card */}
      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        {/* Header with Avatar */}
        <div className="bg-gradient-to-r from-primary to-primary/80 p-6 text-white">
          <div className="flex items-center gap-4">
            <div className="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
              <User className="h-10 w-10 text-white" />
            </div>
            <div>
              <h2 className="text-2xl font-bold">{eleitor.nome}</h2>
              <p className="text-white/80">CAU: {eleitor.cau}</p>
              <div className="mt-2">
                <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${situacao.color}`}>
                  <SituacaoIcon className="h-3 w-3" />
                  {situacao.label}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Info Grid */}
        <div className="p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-gray-900">Dados Pessoais</h3>
            {!isEditing && (
              <button
                onClick={() => setIsEditing(true)}
                className="inline-flex items-center gap-2 text-sm text-primary hover:underline"
              >
                <Edit2 className="h-4 w-4" />
                Editar
              </button>
            )}
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* CPF - Read only */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                CPF
              </label>
              <div className="flex items-center gap-2 text-gray-900">
                <Shield className="h-4 w-4 text-gray-400" />
                {maskCpf(eleitor.cpf)}
              </div>
            </div>

            {/* CAU - Read only */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                Registro CAU
              </label>
              <div className="flex items-center gap-2 text-gray-900">
                <Shield className="h-4 w-4 text-gray-400" />
                {eleitor.cau}
              </div>
            </div>

            {/* Email */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                E-mail
              </label>
              {isEditing ? (
                <input
                  type="email"
                  value={editedEmail}
                  onChange={(e) => setEditedEmail(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                />
              ) : (
                <div className="flex items-center gap-2 text-gray-900">
                  <Mail className="h-4 w-4 text-gray-400" />
                  {eleitor.email || 'Nao informado'}
                </div>
              )}
            </div>

            {/* Phone */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                Telefone
              </label>
              {isEditing ? (
                <input
                  type="tel"
                  value={editedTelefone}
                  onChange={(e) => setEditedTelefone(e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                />
              ) : (
                <div className="flex items-center gap-2 text-gray-900">
                  <Phone className="h-4 w-4 text-gray-400" />
                  {eleitor.telefone || 'Nao informado'}
                </div>
              )}
            </div>

            {/* Regional */}
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-500 mb-1">
                Regional
              </label>
              <div className="flex items-center gap-2 text-gray-900">
                <MapPin className="h-4 w-4 text-gray-400" />
                {eleitor.regional || 'Nao informada'}
              </div>
            </div>
          </div>

          {/* Edit Actions */}
          {isEditing && (
            <div className="flex gap-3 mt-6 pt-6 border-t">
              <button
                onClick={() => {
                  setIsEditing(false)
                  setEditedEmail(eleitor.email)
                  setEditedTelefone(eleitor.telefone)
                }}
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
                    Salvar
                  </>
                )}
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Notification Preferences */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Bell className="h-5 w-5 text-gray-400" />
          <h3 className="text-lg font-semibold text-gray-900">Preferencias de Notificacao</h3>
        </div>

        <div className="space-y-4">
          <label className="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
            <div className="flex items-center gap-3">
              <Mail className="h-5 w-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">Notificacoes por E-mail</p>
                <p className="text-sm text-gray-500">Receba avisos sobre eleicoes no seu e-mail</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={notificacoesEmail}
              onChange={() => toggleNotification('email')}
              className="w-5 h-5 text-primary rounded focus:ring-primary"
            />
          </label>

          <label className="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
            <div className="flex items-center gap-3">
              <Phone className="h-5 w-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">Notificacoes por SMS</p>
                <p className="text-sm text-gray-500">Receba lembretes de votacao no seu celular</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={notificacoesSms}
              onChange={() => toggleNotification('sms')}
              className="w-5 h-5 text-primary rounded focus:ring-primary"
            />
          </label>
        </div>
      </div>

      {/* Account Info */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Shield className="h-5 w-5 text-gray-400" />
          <h3 className="text-lg font-semibold text-gray-900">Informacoes da Conta</h3>
        </div>

        <div className="space-y-4">
          <div className="flex items-center justify-between py-2">
            <span className="text-gray-500">Tipo de Conta</span>
            <span className="text-gray-900 capitalize">{eleitor.tipo}</span>
          </div>
          <div className="flex items-center justify-between py-2">
            <span className="text-gray-500">Situacao Eleitoral</span>
            <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${situacao.color}`}>
              <SituacaoIcon className="h-3 w-3" />
              {situacao.label}
            </span>
          </div>
        </div>

        <button className="mt-6 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
          <Lock className="h-4 w-4" />
          Alterar Senha
        </button>
      </div>
    </div>
  )
}
