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
  Users,
} from 'lucide-react'
import api, { setTokenType, extractApiError } from '../../services/api'

// ---- JWT helpers ----

interface JwtPayload {
  sub: string
  email: string
  nome: string
  tipo: string
  registroCAU: string
  cpf: string
  exp?: number
  [key: string]: unknown
}

function parseJwt(token: string): JwtPayload | null {
  try {
    const base64Url = token.split('.')[1]
    if (!base64Url) return null
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/')
    const jsonPayload = decodeURIComponent(
      atob(base64)
        .split('')
        .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join('')
    )
    return JSON.parse(jsonPayload) as JwtPayload
  } catch {
    return null
  }
}

function getTokenPayload(): JwtPayload | null {
  const token = localStorage.getItem('candidate-token')
  if (!token) return null
  return parseJwt(token)
}

// ---- Types ----

interface MembroChapaDetalhe {
  id: string
  chapaId: string
  chapaNome: string
  profissionalId: string
  profissionalNome: string
  profissionalRegistroCAU?: string
  profissionalCpf?: string
  profissionalEmail?: string
  tipoMembro: number
  tipoMembroNome: string
  cargo?: string
  status: number
  statusNome: string
  ordem: number
  parecer?: string
  motivoRejeicao?: string
  dataAnalise?: string
  analisadoPor?: string
  analisadoPorNome?: string
  createdAt: string
  updatedAt?: string
}

interface ProfileData {
  nome: string
  email: string
  cpf: string
  registroCAU: string
  userId: string
}

// ---- Helpers ----

function maskCpf(cpf: string): string {
  if (!cpf || cpf.length < 11) return cpf
  const digits = cpf.replace(/\D/g, '')
  if (digits.length !== 11) return cpf
  return `${digits.slice(0, 3)}.***.***.${digits.slice(9)}`
}

function getStatusConfig(statusNome: string | undefined) {
  if (!statusNome) {
    return { label: 'Sem dados', color: 'bg-gray-100 text-gray-800', icon: AlertCircle }
  }
  const lower = statusNome.toLowerCase()
  if (lower.includes('aprovad') || lower.includes('ativo') || lower.includes('confirmad')) {
    return { label: statusNome, color: 'bg-green-100 text-green-800', icon: CheckCircle }
  }
  if (lower.includes('rejeitad') || lower.includes('suspen') || lower.includes('inabilit')) {
    return { label: statusNome, color: 'bg-red-100 text-red-800', icon: AlertCircle }
  }
  if (lower.includes('pendent') || lower.includes('analise')) {
    return { label: statusNome, color: 'bg-yellow-100 text-yellow-800', icon: AlertCircle }
  }
  return { label: statusNome, color: 'bg-gray-100 text-gray-800', icon: AlertCircle }
}

// ---- Component ----

export function CandidatoPerfilPage() {
  const [profile, setProfile] = useState<ProfileData | null>(null)
  const [memberships, setMemberships] = useState<MembroChapaDetalhe[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [editedEmail, setEditedEmail] = useState('')
  const [editedTelefone, setEditedTelefone] = useState('')

  // Notification preferences (local state only)
  const [notificacoesEmail, setNotificacoesEmail] = useState(true)
  const [notificacoesSms, setNotificacoesSms] = useState(true)

  const loadData = useCallback(async () => {
    setLoading(true)
    setError(null)

    try {
      // 1. Parse JWT for basic profile data
      const payload = getTokenPayload()
      if (!payload) {
        setError('Sessao expirada. Faca login novamente.')
        setLoading(false)
        return
      }

      const profileData: ProfileData = {
        nome: payload.nome || '',
        email: payload.email || '',
        cpf: payload.cpf || '',
        registroCAU: payload.registroCAU || '',
        userId: payload.sub || '',
      }
      setProfile(profileData)
      setEditedEmail(profileData.email)

      // 2. Fetch ticket memberships
      if (profileData.userId) {
        try {
          setTokenType('candidate')
          const response = await api.get<MembroChapaDetalhe[]>(
            `/membro-chapa/profissional/${profileData.userId}`
          )
          setMemberships(response.data || [])
        } catch (membershipErr: unknown) {
          // If it is a 404, we treat as no memberships
          const apiErr = extractApiError(membershipErr)
          if (apiErr.code !== '404') {
            console.error('Erro ao carregar participacoes em chapas:', apiErr.message)
          }
          setMemberships([])
        }
      }
    } catch (err: unknown) {
      const apiErr = extractApiError(err)
      setError(apiErr.message || 'Erro ao carregar dados do perfil')
    } finally {
      setLoading(false)
    }
  }, [])

  useEffect(() => {
    loadData()
  }, [loadData])

  const handleSave = async () => {
    setIsSaving(true)
    try {
      setTokenType('candidate')
      await api.put('/candidato/perfil', {
        email: editedEmail,
        telefone: editedTelefone,
      })
      // Update local profile state
      if (profile) {
        setProfile({ ...profile, email: editedEmail })
      }
      setIsEditing(false)
    } catch (err: unknown) {
      const apiErr = extractApiError(err)
      alert(apiErr.message || 'Erro ao salvar dados')
    } finally {
      setIsSaving(false)
    }
  }

  // ---- Loading state ----
  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="text-center">
          <Loader2 className="h-8 w-8 animate-spin text-primary mx-auto mb-4" />
          <p className="text-gray-600">Carregando perfil...</p>
        </div>
      </div>
    )
  }

  // ---- Error state ----
  if (error || !profile) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="text-center max-w-md">
          <AlertCircle className="h-12 w-12 text-red-400 mx-auto mb-4" />
          <h2 className="text-lg font-semibold text-gray-900 mb-2">Erro ao carregar perfil</h2>
          <p className="text-gray-600 mb-4">{error || 'Dados do perfil nao encontrados.'}</p>
          <button
            onClick={loadData}
            className="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90"
          >
            Tentar novamente
          </button>
        </div>
      </div>
    )
  }

  // Determine main membership for the header (first one if exists)
  const mainMembership = memberships.length > 0 ? memberships[0] : null
  const statusInfo = mainMembership
    ? getStatusConfig(mainMembership.statusNome)
    : { label: 'Sem chapa vinculada', color: 'bg-gray-100 text-gray-800', icon: AlertCircle }
  const StatusIcon = statusInfo.icon

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meu Perfil</h1>
        <p className="text-gray-600 mt-1">Visualize e atualize suas informações</p>
      </div>

      {/* Profile Card */}
      <div className="bg-white rounded-lg shadow-sm border overflow-hidden">
        {/* Header with Avatar */}
        <div className="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white">
          <div className="flex items-center gap-4">
            <div className="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
              <User className="h-10 w-10 text-white" />
            </div>
            <div>
              <h2 className="text-2xl font-bold">{profile.nome}</h2>
              <p className="text-white/80">CAU: {profile.registroCAU}</p>
              <div className="mt-2">
                <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${statusInfo.color}`}>
                  <StatusIcon className="h-3 w-3" />
                  {statusInfo.label}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Chapa Info */}
        {mainMembership ? (
          <div className="p-4 bg-blue-50 border-b">
            <div className="flex items-center gap-3">
              <Users className="h-5 w-5 text-blue-600" />
              <div>
                <p className="text-sm text-gray-500">Candidato pela</p>
                <p className="font-medium text-gray-900">
                  {mainMembership.chapaNome}
                  {mainMembership.cargo ? ` - ${mainMembership.cargo}` : ''}
                </p>
              </div>
            </div>
            {memberships.length > 1 && (
              <div className="mt-3 space-y-2">
                {memberships.slice(1).map((m) => (
                  <div key={m.id} className="flex items-center gap-3 pl-8">
                    <div>
                      <p className="text-sm text-gray-600">
                        {m.chapaNome}
                        {m.cargo ? ` - ${m.cargo}` : ''}
                        <span className={`ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium ${getStatusConfig(m.statusNome).color}`}>
                          {m.statusNome}
                        </span>
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        ) : (
          <div className="p-4 bg-gray-50 border-b">
            <div className="flex items-center gap-3">
              <Users className="h-5 w-5 text-gray-400" />
              <div>
                <p className="text-sm text-gray-500">Nenhuma chapa vinculada</p>
                <p className="text-xs text-gray-400">
                  Voce ainda nao esta associado a nenhuma chapa eleitoral.
                </p>
              </div>
            </div>
          </div>
        )}

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
                {maskCpf(profile.cpf)}
              </div>
            </div>

            {/* CAU - Read only */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                Registro CAU
              </label>
              <div className="flex items-center gap-2 text-gray-900">
                <Shield className="h-4 w-4 text-gray-400" />
                {profile.registroCAU}
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
                  {profile.email}
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
                  {editedTelefone || 'Nao informado'}
                </div>
              )}
            </div>

            {/* Regional - from membership if available */}
            {mainMembership && (
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-500 mb-1">
                  Tipo de Membro
                </label>
                <div className="flex items-center gap-2 text-gray-900">
                  <MapPin className="h-4 w-4 text-gray-400" />
                  {mainMembership.tipoMembroNome}
                </div>
              </div>
            )}
          </div>

          {/* Edit Actions */}
          {isEditing && (
            <div className="flex gap-3 mt-6 pt-6 border-t">
              <button
                onClick={() => {
                  setIsEditing(false)
                  setEditedEmail(profile.email)
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

      {/* Notification Preferences (local state) */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Bell className="h-5 w-5 text-gray-400" />
          <h3 className="text-lg font-semibold text-gray-900">Preferencias de Notificação</h3>
        </div>

        <div className="space-y-4">
          <label className="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
            <div className="flex items-center gap-3">
              <Mail className="h-5 w-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">Notificações por E-mail</p>
                <p className="text-sm text-gray-500">Receba avisos sobre sua candidatura</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={notificacoesEmail}
              onChange={() => setNotificacoesEmail(!notificacoesEmail)}
              className="w-5 h-5 text-primary rounded focus:ring-primary"
            />
          </label>

          <label className="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
            <div className="flex items-center gap-3">
              <Phone className="h-5 w-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">Notificações por SMS</p>
                <p className="text-sm text-gray-500">Receba alertas urgentes no celular</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={notificacoesSms}
              onChange={() => setNotificacoesSms(!notificacoesSms)}
              className="w-5 h-5 text-primary rounded focus:ring-primary"
            />
          </label>
        </div>
      </div>

      {/* Account Info */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex items-center gap-2 mb-4">
          <Shield className="h-5 w-5 text-gray-400" />
          <h3 className="text-lg font-semibold text-gray-900">Informações da Conta</h3>
        </div>

        <div className="space-y-4">
          <div className="flex items-center justify-between py-2">
            <span className="text-gray-500">Tipo de Usuario</span>
            <span className="text-gray-900 capitalize">
              {getTokenPayload()?.tipo || 'Candidato'}
            </span>
          </div>
          {mainMembership && (
            <div className="flex items-center justify-between py-2">
              <span className="text-gray-500">Membro desde</span>
              <span className="text-gray-900">
                {new Date(mainMembership.createdAt).toLocaleDateString('pt-BR')}
              </span>
            </div>
          )}
        </div>

        <button className="mt-6 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
          <Lock className="h-4 w-4" />
          Alterar Senha
        </button>
      </div>
    </div>
  )
}
