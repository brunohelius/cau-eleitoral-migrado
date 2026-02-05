import { useState, useEffect, useCallback } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import {
  AlertTriangle,
  ArrowLeft,
  CheckCircle,
  ChevronDown,
  FileText,
  HelpCircle,
  Info,
  Loader2,
  RefreshCw,
  Send,
  Shield,
  Upload,
  X,
} from 'lucide-react'
import api, { extractApiError } from '@/services/api'

// Types
interface EleicaoParaDenuncia {
  id: string
  nome: string
  ano: number
  regional: string
}

interface TipoDenuncia {
  valor: number
  nome: string
  descricao: string
}

interface CaptchaData {
  pergunta: string
  token: string
  expectedValue: number
}

interface ArquivoAnexo {
  id: string
  nome: string
  tipo: string
  tamanho: number
  file: File
}

interface FormData {
  eleicaoId: string
  tipo: number
  descricao: string
  fundamentacao: string
  captchaValue: string
}

interface FormErrors {
  eleicaoId?: string
  tipo?: string
  descricao?: string
  captchaValue?: string
  arquivos?: string
  general?: string
}

// Max file size: 5MB
const MAX_FILE_SIZE = 5 * 1024 * 1024
const ALLOWED_TYPES = [
  'image/jpeg',
  'image/png',
  'image/gif',
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
]

export function DenunciaFormPage() {
  const [searchParams] = useSearchParams()
  const preselectedEleicaoId = searchParams.get('eleicao') || ''

  // States
  const [eleicoes, setEleicoes] = useState<EleicaoParaDenuncia[]>([])
  const [tiposDenuncia, setTiposDenuncia] = useState<TipoDenuncia[]>([])
  const [captcha, setCaptcha] = useState<CaptchaData | null>(null)
  const [isLoadingData, setIsLoadingData] = useState(true)
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [submitSuccess, setSubmitSuccess] = useState<{ protocolo: string; dataEnvio: string } | null>(null)
  const [arquivos, setArquivos] = useState<ArquivoAnexo[]>([])
  const [showTipoInfo, setShowTipoInfo] = useState(false)

  const [formData, setFormData] = useState<FormData>({
    eleicaoId: preselectedEleicaoId,
    tipo: -1,
    descricao: '',
    fundamentacao: '',
    captchaValue: '',
  })

  const [errors, setErrors] = useState<FormErrors>({})
  const [touched, setTouched] = useState<Record<string, boolean>>({})

  // Load initial data
  useEffect(() => {
    const loadData = async () => {
      setIsLoadingData(true)
      try {
        const [eleicoesRes, tiposRes, captchaRes] = await Promise.all([
          api.get<EleicaoParaDenuncia[]>('/public/denuncias/eleicoes'),
          api.get<TipoDenuncia[]>('/public/denuncias/tipos'),
          api.get<CaptchaData>('/public/denuncias/captcha'),
        ])
        setEleicoes(eleicoesRes.data)
        setTiposDenuncia(tiposRes.data)
        setCaptcha(captchaRes.data)
      } catch (error) {
        console.error('Erro ao carregar dados:', error)
        setErrors({ general: 'Erro ao carregar dados. Por favor, recarregue a pagina.' })
      } finally {
        setIsLoadingData(false)
      }
    }
    loadData()
  }, [])

  // Refresh captcha
  const refreshCaptcha = useCallback(async () => {
    try {
      const response = await api.get<CaptchaData>('/public/denuncias/captcha')
      setCaptcha(response.data)
      setFormData(prev => ({ ...prev, captchaValue: '' }))
    } catch (error) {
      console.error('Erro ao atualizar captcha:', error)
    }
  }, [])

  // Form validation
  const validateForm = (): boolean => {
    const newErrors: FormErrors = {}

    if (!formData.eleicaoId) {
      newErrors.eleicaoId = 'Selecione uma eleicao'
    }

    if (formData.tipo < 0) {
      newErrors.tipo = 'Selecione o tipo de denuncia'
    }

    if (!formData.descricao.trim()) {
      newErrors.descricao = 'Descreva a denuncia'
    } else if (formData.descricao.trim().length < 50) {
      newErrors.descricao = `A descricao deve ter pelo menos 50 caracteres (atual: ${formData.descricao.trim().length})`
    } else if (formData.descricao.trim().length > 5000) {
      newErrors.descricao = 'A descricao deve ter no maximo 5000 caracteres'
    }

    if (!formData.captchaValue.trim()) {
      newErrors.captchaValue = 'Responda a verificacao de seguranca'
    } else if (captcha && parseInt(formData.captchaValue) !== captcha.expectedValue) {
      newErrors.captchaValue = 'Resposta incorreta. Tente novamente.'
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  // Handle input changes
  const handleInputChange = (field: keyof FormData, value: string | number) => {
    setFormData(prev => ({ ...prev, [field]: value }))
    // Clear error when user starts typing
    if (errors[field as keyof FormErrors]) {
      setErrors(prev => ({ ...prev, [field]: undefined }))
    }
  }

  // Handle blur
  const handleBlur = (field: string) => {
    setTouched(prev => ({ ...prev, [field]: true }))
  }

  // Handle file upload
  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files
    if (!files) return

    const newErrors: FormErrors = { ...errors }
    delete newErrors.arquivos

    for (let i = 0; i < files.length; i++) {
      const file = files[i]

      // Validate file size
      if (file.size > MAX_FILE_SIZE) {
        newErrors.arquivos = `Arquivo "${file.name}" excede o limite de 5MB`
        setErrors(newErrors)
        continue
      }

      // Validate file type
      if (!ALLOWED_TYPES.includes(file.type)) {
        newErrors.arquivos = `Tipo de arquivo "${file.name}" nao permitido`
        setErrors(newErrors)
        continue
      }

      // Check if already exists
      if (arquivos.some(a => a.nome === file.name)) {
        continue
      }

      // Add to list (max 5 files)
      if (arquivos.length >= 5) {
        newErrors.arquivos = 'Maximo de 5 arquivos permitidos'
        setErrors(newErrors)
        break
      }

      const novoArquivo: ArquivoAnexo = {
        id: crypto.randomUUID(),
        nome: file.name,
        tipo: file.type,
        tamanho: file.size,
        file,
      }

      setArquivos(prev => [...prev, novoArquivo])
    }

    // Reset input
    e.target.value = ''
  }

  // Remove file
  const handleRemoveFile = (id: string) => {
    setArquivos(prev => prev.filter(a => a.id !== id))
    setErrors(prev => ({ ...prev, arquivos: undefined }))
  }

  // Format file size
  const formatFileSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
  }

  // Handle submit
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    // Touch all fields
    setTouched({
      eleicaoId: true,
      tipo: true,
      descricao: true,
      captchaValue: true,
    })

    if (!validateForm()) {
      return
    }

    setIsSubmitting(true)
    setErrors({})

    try {
      const payload = {
        eleicaoId: formData.eleicaoId,
        tipo: formData.tipo,
        descricao: formData.descricao.trim(),
        fundamentacao: formData.fundamentacao.trim() || null,
        captchaValue: parseInt(formData.captchaValue),
        captchaExpected: captcha?.expectedValue || 0,
        // Note: File upload would require multipart/form-data in production
        arquivos: arquivos.map(a => ({
          nome: a.nome,
          tipo: a.tipo,
          base64Content: '', // In production, convert file to base64
        })),
      }

      const response = await api.post<{ protocolo: string; dataEnvio: string; mensagem: string }>(
        '/public/denuncias',
        payload
      )

      setSubmitSuccess({
        protocolo: response.data.protocolo,
        dataEnvio: new Date(response.data.dataEnvio).toLocaleString('pt-BR'),
      })
    } catch (error) {
      const apiError = extractApiError(error)
      setErrors({ general: apiError.message })
      // Refresh captcha on error
      refreshCaptcha()
    } finally {
      setIsSubmitting(false)
    }
  }

  // Get selected tipo info
  const selectedTipo = tiposDenuncia.find(t => t.valor === formData.tipo)

  // Loading state
  if (isLoadingData) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-primary" />
        <span className="ml-2 text-gray-500">Carregando...</span>
      </div>
    )
  }

  // Success state
  if (submitSuccess) {
    return (
      <div className="max-w-2xl mx-auto">
        <div className="bg-white rounded-lg shadow-sm border p-8 text-center">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-6">
            <CheckCircle className="h-8 w-8 text-green-600" />
          </div>
          <h1 className="text-2xl font-bold text-gray-900 mb-4">
            Denuncia Registrada com Sucesso
          </h1>
          <p className="text-gray-600 mb-6">
            Sua denuncia foi recebida e sera analisada pela Comissao Eleitoral.
          </p>

          <div className="bg-gray-50 rounded-lg p-6 mb-6">
            <p className="text-sm text-gray-500 mb-2">Numero do Protocolo</p>
            <p className="text-2xl font-mono font-bold text-primary">
              {submitSuccess.protocolo}
            </p>
            <p className="text-sm text-gray-500 mt-4">
              Data de Envio: {submitSuccess.dataEnvio}
            </p>
          </div>

          <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div className="flex items-start gap-3">
              <AlertTriangle className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
              <div className="text-left">
                <p className="text-sm font-medium text-yellow-800">Importante</p>
                <p className="text-sm text-yellow-700 mt-1">
                  Guarde este numero de protocolo para acompanhar o andamento da sua denuncia.
                </p>
              </div>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              to="/"
              className="inline-flex items-center justify-center px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90"
            >
              Voltar ao Inicio
            </Link>
            <button
              onClick={() => {
                setSubmitSuccess(null)
                setFormData({
                  eleicaoId: preselectedEleicaoId,
                  tipo: -1,
                  descricao: '',
                  fundamentacao: '',
                  captchaValue: '',
                })
                setArquivos([])
                setTouched({})
                setErrors({})
                refreshCaptcha()
              }}
              className="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50"
            >
              Enviar Nova Denuncia
            </button>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-3xl mx-auto">
      {/* Header */}
      <div className="mb-8">
        <Link
          to="/"
          className="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4"
        >
          <ArrowLeft className="h-5 w-5 mr-1" />
          <span>Voltar</span>
        </Link>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">
          Registrar Denuncia Eleitoral
        </h1>
        <p className="text-gray-600 mt-2">
          Utilize este formulario para denunciar irregularidades no processo eleitoral.
          Sua denuncia sera analisada pela Comissao Eleitoral.
        </p>
      </div>

      {/* Info Box */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div className="flex items-start gap-3">
          <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
          <div>
            <p className="text-sm font-medium text-blue-800">Antes de enviar sua denuncia</p>
            <ul className="text-sm text-blue-700 mt-2 list-disc list-inside space-y-1">
              <li>Descreva os fatos com clareza e objetividade</li>
              <li>Inclua datas, locais e envolvidos, se possivel</li>
              <li>Anexe evidencias que comprovem a irregularidade</li>
              <li>A denuncia pode ser feita de forma anonima</li>
            </ul>
          </div>
        </div>
      </div>

      {/* Error Alert */}
      {errors.general && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
          <div className="flex items-start gap-3">
            <AlertTriangle className="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="text-sm font-medium text-red-800">Erro ao enviar denuncia</p>
              <p className="text-sm text-red-700 mt-1">{errors.general}</p>
            </div>
          </div>
        </div>
      )}

      {/* Form */}
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Election Selection */}
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <FileText className="h-5 w-5 text-primary" />
            Informacoes da Denuncia
          </h2>

          {/* Election */}
          <div className="mb-6">
            <label htmlFor="eleicaoId" className="block text-sm font-medium text-gray-700 mb-2">
              Eleicao <span className="text-red-500">*</span>
            </label>
            <div className="relative">
              <select
                id="eleicaoId"
                value={formData.eleicaoId}
                onChange={(e) => handleInputChange('eleicaoId', e.target.value)}
                onBlur={() => handleBlur('eleicaoId')}
                className={`w-full px-4 py-3 border rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent ${
                  errors.eleicaoId && touched.eleicaoId
                    ? 'border-red-500 bg-red-50'
                    : 'border-gray-300'
                }`}
                aria-invalid={!!errors.eleicaoId}
                aria-describedby={errors.eleicaoId ? 'eleicaoId-error' : undefined}
              >
                <option value="">Selecione a eleicao</option>
                {eleicoes.map(e => (
                  <option key={e.id} value={e.id}>
                    {e.nome} - {e.regional} ({e.ano})
                  </option>
                ))}
              </select>
              <ChevronDown className="absolute right-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
            </div>
            {errors.eleicaoId && touched.eleicaoId && (
              <p id="eleicaoId-error" className="mt-1 text-sm text-red-600" role="alert">
                {errors.eleicaoId}
              </p>
            )}
          </div>

          {/* Complaint Type */}
          <div className="mb-6">
            <div className="flex items-center justify-between mb-2">
              <label htmlFor="tipo" className="block text-sm font-medium text-gray-700">
                Tipo de Denuncia <span className="text-red-500">*</span>
              </label>
              <button
                type="button"
                onClick={() => setShowTipoInfo(!showTipoInfo)}
                className="text-sm text-primary hover:underline flex items-center gap-1"
              >
                <HelpCircle className="h-4 w-4" />
                O que significa cada tipo?
              </button>
            </div>
            <div className="relative">
              <select
                id="tipo"
                value={formData.tipo}
                onChange={(e) => handleInputChange('tipo', parseInt(e.target.value))}
                onBlur={() => handleBlur('tipo')}
                className={`w-full px-4 py-3 border rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent ${
                  errors.tipo && touched.tipo
                    ? 'border-red-500 bg-red-50'
                    : 'border-gray-300'
                }`}
                aria-invalid={!!errors.tipo}
                aria-describedby={errors.tipo ? 'tipo-error' : undefined}
              >
                <option value={-1}>Selecione o tipo de denuncia</option>
                {tiposDenuncia.map(t => (
                  <option key={t.valor} value={t.valor}>
                    {t.nome}
                  </option>
                ))}
              </select>
              <ChevronDown className="absolute right-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
            </div>
            {errors.tipo && touched.tipo && (
              <p id="tipo-error" className="mt-1 text-sm text-red-600" role="alert">
                {errors.tipo}
              </p>
            )}
            {selectedTipo && (
              <p className="mt-2 text-sm text-gray-500">
                {selectedTipo.descricao}
              </p>
            )}
          </div>

          {/* Tipo Info Panel */}
          {showTipoInfo && (
            <div className="mb-6 bg-gray-50 rounded-lg p-4">
              <h3 className="text-sm font-medium text-gray-900 mb-3">Tipos de Denuncia</h3>
              <div className="space-y-3">
                {tiposDenuncia.map(t => (
                  <div key={t.valor} className="text-sm">
                    <span className="font-medium text-gray-800">{t.nome}:</span>{' '}
                    <span className="text-gray-600">{t.descricao}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Description */}
          <div className="mb-6">
            <label htmlFor="descricao" className="block text-sm font-medium text-gray-700 mb-2">
              Descricao da Denuncia <span className="text-red-500">*</span>
            </label>
            <textarea
              id="descricao"
              value={formData.descricao}
              onChange={(e) => handleInputChange('descricao', e.target.value)}
              onBlur={() => handleBlur('descricao')}
              rows={6}
              placeholder="Descreva detalhadamente a irregularidade observada, incluindo quando, onde e quem esta envolvido..."
              className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none ${
                errors.descricao && touched.descricao
                  ? 'border-red-500 bg-red-50'
                  : 'border-gray-300'
              }`}
              aria-invalid={!!errors.descricao}
              aria-describedby={errors.descricao ? 'descricao-error' : 'descricao-hint'}
            />
            <div className="flex justify-between mt-1">
              {errors.descricao && touched.descricao ? (
                <p id="descricao-error" className="text-sm text-red-600" role="alert">
                  {errors.descricao}
                </p>
              ) : (
                <p id="descricao-hint" className="text-sm text-gray-500">
                  Minimo de 50 caracteres
                </p>
              )}
              <span className={`text-sm ${
                formData.descricao.length > 5000 ? 'text-red-600' : 'text-gray-500'
              }`}>
                {formData.descricao.length}/5000
              </span>
            </div>
          </div>

          {/* Fundamentacao (optional) */}
          <div>
            <label htmlFor="fundamentacao" className="block text-sm font-medium text-gray-700 mb-2">
              Fundamentacao Legal <span className="text-gray-400">(opcional)</span>
            </label>
            <textarea
              id="fundamentacao"
              value={formData.fundamentacao}
              onChange={(e) => handleInputChange('fundamentacao', e.target.value)}
              rows={3}
              placeholder="Se desejar, indique a norma ou regulamento que foi violado..."
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
            />
          </div>
        </div>

        {/* Evidence Upload */}
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <Upload className="h-5 w-5 text-primary" />
            Anexar Evidencias
            <span className="text-sm font-normal text-gray-400">(opcional)</span>
          </h2>

          <div className="mb-4">
            <label
              htmlFor="arquivos"
              className="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <div className="flex flex-col items-center justify-center pt-5 pb-6">
                <Upload className="h-8 w-8 text-gray-400 mb-2" />
                <p className="text-sm text-gray-600">
                  <span className="font-medium text-primary">Clique para enviar</span> ou arraste arquivos
                </p>
                <p className="text-xs text-gray-500 mt-1">
                  PDF, DOC, DOCX, JPG, PNG, GIF (max. 5MB cada, ate 5 arquivos)
                </p>
              </div>
              <input
                id="arquivos"
                type="file"
                className="hidden"
                multiple
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                onChange={handleFileChange}
              />
            </label>
            {errors.arquivos && (
              <p className="mt-1 text-sm text-red-600" role="alert">
                {errors.arquivos}
              </p>
            )}
          </div>

          {/* File List */}
          {arquivos.length > 0 && (
            <div className="space-y-2">
              {arquivos.map(arquivo => (
                <div
                  key={arquivo.id}
                  className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div className="flex items-center gap-3">
                    <FileText className="h-5 w-5 text-gray-400" />
                    <div>
                      <p className="text-sm font-medium text-gray-700">{arquivo.nome}</p>
                      <p className="text-xs text-gray-500">{formatFileSize(arquivo.tamanho)}</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    onClick={() => handleRemoveFile(arquivo.id)}
                    className="p-1 text-gray-400 hover:text-red-600 transition-colors"
                    aria-label={`Remover ${arquivo.nome}`}
                  >
                    <X className="h-5 w-5" />
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Security Verification */}
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <Shield className="h-5 w-5 text-primary" />
            Verificacao de Seguranca
          </h2>

          {captcha && (
            <div className="flex flex-col sm:flex-row sm:items-center gap-4">
              <div className="flex-1">
                <label htmlFor="captchaValue" className="block text-sm font-medium text-gray-700 mb-2">
                  {captcha.pergunta}
                </label>
                <div className="flex gap-2">
                  <input
                    type="number"
                    id="captchaValue"
                    value={formData.captchaValue}
                    onChange={(e) => handleInputChange('captchaValue', e.target.value)}
                    onBlur={() => handleBlur('captchaValue')}
                    placeholder="Sua resposta"
                    className={`flex-1 px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent ${
                      errors.captchaValue && touched.captchaValue
                        ? 'border-red-500 bg-red-50'
                        : 'border-gray-300'
                    }`}
                    aria-invalid={!!errors.captchaValue}
                    aria-describedby={errors.captchaValue ? 'captcha-error' : undefined}
                  />
                  <button
                    type="button"
                    onClick={refreshCaptcha}
                    className="px-4 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
                    aria-label="Gerar nova pergunta"
                    title="Gerar nova pergunta"
                  >
                    <RefreshCw className="h-5 w-5" />
                  </button>
                </div>
                {errors.captchaValue && touched.captchaValue && (
                  <p id="captcha-error" className="mt-1 text-sm text-red-600" role="alert">
                    {errors.captchaValue}
                  </p>
                )}
              </div>
            </div>
          )}
        </div>

        {/* Privacy Notice */}
        <div className="bg-gray-50 rounded-lg p-4">
          <p className="text-sm text-gray-600">
            <span className="font-medium">Aviso de Privacidade:</span> Sua denuncia sera tratada com
            confidencialidade. A identificacao do denunciante nao sera divulgada, exceto em casos
            previstos em lei. Os dados fornecidos serao utilizados exclusivamente para analise e
            processamento da denuncia.
          </p>
        </div>

        {/* Submit Button */}
        <div className="flex flex-col sm:flex-row gap-4">
          <button
            type="submit"
            disabled={isSubmitting}
            className="flex-1 inline-flex items-center justify-center gap-2 bg-primary text-white px-6 py-4 rounded-lg font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {isSubmitting ? (
              <>
                <Loader2 className="h-5 w-5 animate-spin" />
                Enviando...
              </>
            ) : (
              <>
                <Send className="h-5 w-5" />
                Enviar Denuncia
              </>
            )}
          </button>
          <Link
            to="/"
            className="inline-flex items-center justify-center gap-2 border border-gray-300 text-gray-700 px-6 py-4 rounded-lg font-medium hover:bg-gray-50 transition-colors"
          >
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  )
}
