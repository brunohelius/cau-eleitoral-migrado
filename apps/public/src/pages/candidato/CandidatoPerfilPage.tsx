import { useState } from 'react'
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

// Types
interface Candidato {
  id: string
  nome: string
  cpf: string
  cau: string
  email: string
  telefone: string
  regional: string
  situacao: 'ativo' | 'inativo' | 'suspenso'
  chapaId: string
  chapaNome: string
  cargo: string
  dataCadastro: string
  ultimoAcesso: string
  notificacoesEmail: boolean
  notificacoesSms: boolean
}

// Mock data
const mockCandidato: Candidato = {
  id: '1',
  nome: 'Maria Santos',
  cpf: '***.***.***-00',
  cau: 'A54321-0',
  email: 'maria.santos@email.com',
  telefone: '(11) *****-1234',
  regional: 'CAU/SP - Sao Paulo',
  situacao: 'ativo',
  chapaId: '1',
  chapaNome: 'Chapa Renovacao',
  cargo: 'Vice-Presidente',
  dataCadastro: '2024-02-08',
  ultimoAcesso: '2024-03-16T14:30:00',
  notificacoesEmail: true,
  notificacoesSms: true,
}

const situacaoConfig = {
  ativo: { label: 'Candidatura Ativa', color: 'bg-green-100 text-green-800', icon: CheckCircle },
  inativo: { label: 'Inativo', color: 'bg-gray-100 text-gray-800', icon: AlertCircle },
  suspenso: { label: 'Suspenso', color: 'bg-red-100 text-red-800', icon: AlertCircle },
}

export function CandidatoPerfilPage() {
  const [candidato, setCandidato] = useState<Candidato>(mockCandidato)
  const [isEditing, setIsEditing] = useState(false)
  const [isSaving, setIsSaving] = useState(false)
  const [editedEmail, setEditedEmail] = useState(candidato.email)
  const [editedTelefone, setEditedTelefone] = useState(candidato.telefone)

  const handleSave = async () => {
    setIsSaving(true)
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1500))
      setCandidato({
        ...candidato,
        email: editedEmail,
        telefone: editedTelefone,
      })
      setIsEditing(false)
    } finally {
      setIsSaving(false)
    }
  }

  const toggleNotification = (type: 'email' | 'sms') => {
    setCandidato({
      ...candidato,
      notificacoesEmail: type === 'email' ? !candidato.notificacoesEmail : candidato.notificacoesEmail,
      notificacoesSms: type === 'sms' ? !candidato.notificacoesSms : candidato.notificacoesSms,
    })
  }

  const situacao = situacaoConfig[candidato.situacao]
  const SituacaoIcon = situacao.icon

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Meu Perfil</h1>
        <p className="text-gray-600 mt-1">Visualize e atualize suas informacoes</p>
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
              <h2 className="text-2xl font-bold">{candidato.nome}</h2>
              <p className="text-white/80">CAU: {candidato.cau}</p>
              <div className="mt-2">
                <span className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${situacao.color}`}>
                  <SituacaoIcon className="h-3 w-3" />
                  {situacao.label}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Chapa Info */}
        <div className="p-4 bg-blue-50 border-b">
          <div className="flex items-center gap-3">
            <Users className="h-5 w-5 text-blue-600" />
            <div>
              <p className="text-sm text-gray-500">Candidato pela</p>
              <p className="font-medium text-gray-900">{candidato.chapaNome} - {candidato.cargo}</p>
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
                {candidato.cpf}
              </div>
            </div>

            {/* CAU - Read only */}
            <div>
              <label className="block text-sm font-medium text-gray-500 mb-1">
                Registro CAU
              </label>
              <div className="flex items-center gap-2 text-gray-900">
                <Shield className="h-4 w-4 text-gray-400" />
                {candidato.cau}
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
                  {candidato.email}
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
                  {candidato.telefone}
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
                {candidato.regional}
              </div>
            </div>
          </div>

          {/* Edit Actions */}
          {isEditing && (
            <div className="flex gap-3 mt-6 pt-6 border-t">
              <button
                onClick={() => {
                  setIsEditing(false)
                  setEditedEmail(candidato.email)
                  setEditedTelefone(candidato.telefone)
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
                <p className="text-sm text-gray-500">Receba avisos sobre sua candidatura</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={candidato.notificacoesEmail}
              onChange={() => toggleNotification('email')}
              className="w-5 h-5 text-primary rounded focus:ring-primary"
            />
          </label>

          <label className="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
            <div className="flex items-center gap-3">
              <Phone className="h-5 w-5 text-gray-400" />
              <div>
                <p className="font-medium text-gray-900">Notificacoes por SMS</p>
                <p className="text-sm text-gray-500">Receba alertas urgentes no celular</p>
              </div>
            </div>
            <input
              type="checkbox"
              checked={candidato.notificacoesSms}
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
            <span className="text-gray-500">Data de Cadastro</span>
            <span className="text-gray-900">{new Date(candidato.dataCadastro).toLocaleDateString('pt-BR')}</span>
          </div>
          <div className="flex items-center justify-between py-2">
            <span className="text-gray-500">Ultimo Acesso</span>
            <span className="text-gray-900">
              {new Date(candidato.ultimoAcesso).toLocaleDateString('pt-BR')} as {new Date(candidato.ultimoAcesso).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
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
