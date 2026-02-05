import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import {
  Lock,
  User,
  Eye,
  EyeOff,
  Loader2,
  AlertCircle,
  HelpCircle,
  Shield,
  CheckCircle,
} from 'lucide-react'

export function VotacaoLoginPage() {
  const navigate = useNavigate()
  const [cpf, setCpf] = useState('')
  const [cau, setCau] = useState('')
  const [senha, setSenha] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [step, setStep] = useState<'identificacao' | 'senha'>('identificacao')

  const formatCPF = (value: string) => {
    const numbers = value.replace(/\D/g, '')
    return numbers
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d)/, '$1.$2')
      .replace(/(\d{3})(\d{1,2})/, '$1-$2')
      .substring(0, 14)
  }

  const formatCAU = (value: string) => {
    const cleaned = value.toUpperCase().replace(/[^A-Z0-9]/g, '')
    return cleaned
      .replace(/^([A-Z])(\d{5})(\d)$/, '$1$2-$3')
      .substring(0, 8)
  }

  const handleCpfChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setCpf(formatCPF(e.target.value))
  }

  const handleCauChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setCau(formatCAU(e.target.value))
  }

  const handleIdentificacao = async (e: React.FormEvent) => {
    e.preventDefault()
    setError(null)
    setIsLoading(true)

    try {
      // Simulate API call to verify voter
      await new Promise(resolve => setTimeout(resolve, 1500))

      // Move to password step
      setStep('senha')
    } catch (err) {
      setError('Nao foi possivel verificar seus dados. Tente novamente.')
    } finally {
      setIsLoading(false)
    }
  }

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    setError(null)
    setIsLoading(true)

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1500))

      // Navigate to voter area
      navigate('/eleitor')
    } catch (err) {
      setError('Senha incorreta. Tente novamente.')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <div className="min-h-[80vh] flex items-center justify-center px-4">
      <div className="w-full max-w-md">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
            <Lock className="h-8 w-8 text-primary" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900">Area do Eleitor</h1>
          <p className="text-gray-600 mt-2">
            {step === 'identificacao'
              ? 'Identifique-se para acessar a votacao'
              : 'Digite sua senha para continuar'}
          </p>
        </div>

        {/* Progress Steps */}
        <div className="flex items-center justify-center gap-2 mb-8">
          <div className={`flex items-center gap-2 ${step === 'identificacao' ? 'text-primary' : 'text-green-600'}`}>
            <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
              step === 'identificacao' ? 'bg-primary text-white' : 'bg-green-100'
            }`}>
              {step === 'senha' ? <CheckCircle className="h-5 w-5" /> : '1'}
            </div>
            <span className="text-sm font-medium hidden sm:block">Identificacao</span>
          </div>
          <div className="w-8 h-0.5 bg-gray-200" />
          <div className={`flex items-center gap-2 ${step === 'senha' ? 'text-primary' : 'text-gray-400'}`}>
            <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
              step === 'senha' ? 'bg-primary text-white' : 'bg-gray-200'
            }`}>
              2
            </div>
            <span className="text-sm font-medium hidden sm:block">Senha</span>
          </div>
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
        <div className="bg-white p-6 sm:p-8 rounded-xl shadow-sm border">
          {step === 'identificacao' ? (
            <form onSubmit={handleIdentificacao} className="space-y-6">
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
                    onChange={handleCpfChange}
                    placeholder="000.000.000-00"
                    className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
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
                    onChange={handleCauChange}
                    placeholder="A00000-0"
                    className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    required
                    maxLength={8}
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={isLoading || cpf.length < 14 || cau.length < 8}
                className="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="h-5 w-5 animate-spin" />
                    Verificando...
                  </>
                ) : (
                  'Continuar'
                )}
              </button>
            </form>
          ) : (
            <form onSubmit={handleLogin} className="space-y-6">
              {/* Show user info */}
              <div className="p-4 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Voce esta acessando como:</p>
                <p className="font-medium text-gray-900">CPF: {cpf}</p>
                <p className="text-sm text-gray-600">CAU: {cau}</p>
                <button
                  type="button"
                  onClick={() => setStep('identificacao')}
                  className="text-sm text-primary hover:underline mt-2"
                >
                  Alterar dados
                </button>
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
                    className="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
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

              <div className="flex items-center justify-between text-sm">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" className="rounded border-gray-300 text-primary focus:ring-primary" />
                  <span className="text-gray-600">Lembrar de mim</span>
                </label>
                <Link to="/recuperar-senha" className="text-primary hover:underline">
                  Esqueci a senha
                </Link>
              </div>

              <button
                type="submit"
                disabled={isLoading || !senha}
                className="w-full bg-primary text-white py-3 rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
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
          )}
        </div>

        {/* Help */}
        <div className="mt-6 text-center">
          <Link
            to="/faq"
            className="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-primary"
          >
            <HelpCircle className="h-4 w-4" />
            Precisa de ajuda?
          </Link>
        </div>

        {/* Security Info */}
        <div className="mt-8 p-4 bg-green-50 rounded-lg">
          <div className="flex items-start gap-3">
            <Shield className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
            <div className="text-sm">
              <p className="font-medium text-green-800">Ambiente Seguro</p>
              <p className="text-green-700">
                Sua conexao e protegida por criptografia SSL. Seus dados sao mantidos em sigilo absoluto.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
