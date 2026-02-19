import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import {
  Users,
  Lock,
  Eye,
  EyeOff,
  Loader2,
  AlertCircle,
  HelpCircle,
  Shield,
  User,
} from 'lucide-react'
import { useCandidatoStore } from '@/stores/candidato'
import { authService } from '@/services/auth'
import { extractApiError } from '@/services/api'

export function CandidatoLoginPage() {
  const navigate = useNavigate()
  const { setCandidato, isAuthenticated, candidato } = useCandidatoStore()
  const [cpf, setCpf] = useState('')
  const [cau, setCau] = useState('')
  const [senha, setSenha] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const formatCPF = (value: string) => {
    const numbers = value.replace(/\D/g, '')
    return numbers
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d{1,2})/, '$1-$2')
      .substring(0, 14)
  }

  const formatCAU = (value: string) => {
    const cleaned = value.toUpperCase().replace(/[^A-Z0-9-]/g, '')
    if (cleaned.length <= 1) return cleaned

    let formatted = cleaned[0]
    const rest = cleaned.slice(1).replace(/-/g, '')

    if (rest.length > 0) {
      const digits = rest.slice(0, 6)
      formatted += digits

      if (rest.length > 6) {
        formatted += '-' + rest.slice(6, 8)
      }
    }

    return formatted.substring(0, 10)
  }

  useEffect(() => {
    if (isAuthenticated && candidato) {
      navigate('/candidato')
    }
  }, [isAuthenticated, candidato, navigate])

  const cleanCPF = (value: string) => value.replace(/\D/g, '')

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    setError(null)
    setIsLoading(true)

    try {
      const response = await authService.loginCandidato({
        cpf: cleanCPF(cpf),
        registroCAU: cau,
        senha,
      })

      setCandidato(response.candidate, response.token, response.expiresAt)

      // Navigate to candidate area
      navigate('/candidato')
    } catch (err) {
      const apiError = extractApiError(err)
      setError(apiError.message || 'Credenciais invalidas ou voce nao esta registrado como candidato.')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="min-h-[80vh] flex items-center justify-center px-4">
      <div className="w-full max-w-md">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
            <Users className="h-8 w-8 text-blue-600" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Área do Candidato</h1>
          <p className="text-gray-600 mt-2">
            Acesse sua area restrita para gerenciar sua candidatura
          </p>
        </div>

        {/* Error Alert */}
        {error && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
            <AlertCircle className="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
            <div>
              <p className="text-red-800 font-medium">Erro de autenticacao</p>
              <p className="text-red-600 text-sm">{error}</p>
            </div>
          </div>
        )}

        {/* Form */}
        <form onSubmit={handleLogin} className="bg-white p-6 sm:p-8 rounded-xl shadow-sm border space-y-6">
          <div>
            <label htmlFor="cpf" className="block text-sm font-medium text-gray-700 mb-2">
              CPF
            </label>
            <div className="relative">
              <User className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                id="cpf"
                type="text"
                value={cpf}
                onChange={(e) => setCpf(formatCPF(e.target.value))}
                placeholder="000.000.000-00"
                className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                maxLength={14}
              />
            </div>
          </div>

          <div>
            <label htmlFor="cau" className="block text-sm font-medium text-gray-700 mb-2">
              Registro CAU
            </label>
            <div className="relative">
              <Shield className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                id="cau"
                type="text"
                value={cau}
                onChange={(e) => setCau(formatCAU(e.target.value))}
                placeholder="A000018-DF"
                className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                maxLength={10}
              />
            </div>
          </div>

          <div>
            <label htmlFor="senha" className="block text-sm font-medium text-gray-700 mb-2">
              Senha
            </label>
            <div className="relative">
              <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                id="senha"
                type={showPassword ? 'text' : 'password'}
                value={senha}
                onChange={(e) => setSenha(e.target.value)}
                placeholder="Digite sua senha"
                className="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
              </button>
            </div>
          </div>

          <div className="flex items-center justify-end text-sm">
            <Link to="/recuperar-senha" className="text-blue-600 hover:underline">
              Esqueci a senha
            </Link>
          </div>

          <button
            type="submit"
            disabled={isLoading || cleanCPF(cpf).length !== 11 || cau.length < 9 || !senha}
            className="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            {isLoading ? (
              <>
                <Loader2 className="h-5 w-5 animate-spin" />
                Autenticando...
              </>
            ) : (
              'Acessar'
            )}
          </button>
        </form>

        {/* Help */}
        <div className="mt-6 text-center space-y-3">
          <Link
            to="/faq"
            className="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600"
          >
            <HelpCircle className="h-4 w-4" />
            Precisa de ajuda?
          </Link>

          <div className="text-sm text-gray-500">
            Nao e candidato?{' '}
            <Link to="/votacao" className="text-blue-600 hover:underline">
              Acesse a area do eleitor
            </Link>
          </div>
        </div>

        {/* Info */}
        <div className="mt-8 p-4 bg-blue-50 rounded-lg">
          <div className="flex items-start gap-3">
            <Shield className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
            <div className="text-sm">
              <p className="font-medium text-blue-800">Acesso Restrito</p>
              <p className="text-blue-700">
                Esta area e exclusiva para candidatos registrados em chapas aprovadas pela Comissao Eleitoral.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
