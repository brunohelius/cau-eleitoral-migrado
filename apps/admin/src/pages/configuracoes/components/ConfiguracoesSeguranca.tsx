import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Loader2, Save, Shield, Key, Clock, Lock, AlertTriangle } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

const configSegurancaSchema = z.object({
  sessaoTimeoutMinutos: z.coerce.number().min(5, 'Minimo 5 minutos'),
  tokenExpiracaoHoras: z.coerce.number().min(1, 'Minimo 1 hora'),
  refreshTokenExpiracaoDias: z.coerce.number().min(1, 'Minimo 1 dia'),
  requerer2FA: z.boolean(),
  complexidadeSenhaMinima: z.enum(['baixa', 'media', 'alta']),
  tamanhoMinimoSenha: z.coerce.number().min(6, 'Minimo 6 caracteres'),
  diasExpiracaoSenha: z.coerce.number().min(0, 'Valor deve ser positivo'),
  historicoSenhasImpedir: z.coerce.number().min(0, 'Valor deve ser positivo'),
  ipWhitelist: z.string().optional(),
  ipBlacklist: z.string().optional(),
  rateLimitRequests: z.coerce.number().min(1, 'Minimo 1 requisicao'),
  rateLimitWindowMinutos: z.coerce.number().min(1, 'Minimo 1 minuto'),
  auditarTodasAcoes: z.boolean(),
  criptografarVotos: z.boolean(),
  algoritmoHash: z.enum(['bcrypt', 'argon2', 'scrypt']),
})

type ConfigSegurancaFormData = z.infer<typeof configSegurancaSchema>

interface ConfiguracoesSegurancaProps {
  data?: ConfigSegurancaFormData
  isLoading: boolean
  onSave: (data: ConfigSegurancaFormData) => Promise<void>
}

const complexidadeOptions = [
  {
    value: 'baixa',
    label: 'Baixa',
    descricao: 'Minimo 6 caracteres',
    color: 'bg-yellow-100 text-yellow-800',
  },
  {
    value: 'media',
    label: 'Media',
    descricao: 'Letras maiusculas, minusculas e numeros',
    color: 'bg-blue-100 text-blue-800',
  },
  {
    value: 'alta',
    label: 'Alta',
    descricao: 'Maiusculas, minusculas, numeros e simbolos',
    color: 'bg-green-100 text-green-800',
  },
]

const hashOptions = [
  {
    value: 'bcrypt',
    label: 'BCrypt',
    descricao: 'Algoritmo padrao, recomendado para maioria dos casos',
  },
  {
    value: 'argon2',
    label: 'Argon2',
    descricao: 'Algoritmo mais moderno, maior seguranca',
  },
  {
    value: 'scrypt',
    label: 'Scrypt',
    descricao: 'Alto consumo de memoria, resistente a ataques',
  },
]

export function ConfiguracoesSeguranca({ data, isLoading, onSave }: ConfiguracoesSegurancaProps) {
  const [isSaving, setIsSaving] = useState(false)

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors, isDirty },
  } = useForm<ConfigSegurancaFormData>({
    resolver: zodResolver(configSegurancaSchema),
    defaultValues: {
      sessaoTimeoutMinutos: 60,
      tokenExpiracaoHoras: 1,
      refreshTokenExpiracaoDias: 7,
      requerer2FA: false,
      complexidadeSenhaMinima: 'media',
      tamanhoMinimoSenha: 8,
      diasExpiracaoSenha: 90,
      historicoSenhasImpedir: 5,
      ipWhitelist: '',
      ipBlacklist: '',
      rateLimitRequests: 100,
      rateLimitWindowMinutos: 1,
      auditarTodasAcoes: true,
      criptografarVotos: true,
      algoritmoHash: 'bcrypt',
    },
  })

  useEffect(() => {
    if (data) {
      reset(data)
    }
  }, [data, reset])

  const onSubmit = async (formData: ConfigSegurancaFormData) => {
    setIsSaving(true)
    try {
      await onSave(formData)
    } finally {
      setIsSaving(false)
    }
  }

  const handleCancel = () => {
    if (data) {
      reset(data)
    }
  }

  const handleToggle = (field: keyof ConfigSegurancaFormData) => {
    const currentValue = watch(field) as boolean
    setValue(field, !currentValue, { shouldDirty: true })
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Clock className="h-5 w-5" />
            Sessao e Tokens
          </CardTitle>
          <CardDescription>
            Configure os tempos de expiracao de sessao e tokens
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-3">
            <div className="space-y-2">
              <Label htmlFor="sessaoTimeoutMinutos">Timeout da Sessao (minutos)</Label>
              <Input
                id="sessaoTimeoutMinutos"
                type="number"
                min={5}
                {...register('sessaoTimeoutMinutos')}
              />
              {errors.sessaoTimeoutMinutos && (
                <p className="text-sm text-red-500">{errors.sessaoTimeoutMinutos.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="tokenExpiracaoHoras">Expiracao do Token (horas)</Label>
              <Input
                id="tokenExpiracaoHoras"
                type="number"
                min={1}
                {...register('tokenExpiracaoHoras')}
              />
              {errors.tokenExpiracaoHoras && (
                <p className="text-sm text-red-500">{errors.tokenExpiracaoHoras.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="refreshTokenExpiracaoDias">Refresh Token (dias)</Label>
              <Input
                id="refreshTokenExpiracaoDias"
                type="number"
                min={1}
                {...register('refreshTokenExpiracaoDias')}
              />
              {errors.refreshTokenExpiracaoDias && (
                <p className="text-sm text-red-500">{errors.refreshTokenExpiracaoDias.message}</p>
              )}
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Key className="h-5 w-5" />
            Politica de Senhas
          </CardTitle>
          <CardDescription>
            Configure a complexidade e expiracao de senhas
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label className="mb-3 block">Complexidade Minima da Senha</Label>
            <div className="grid gap-3 sm:grid-cols-3">
              {complexidadeOptions.map((option) => (
                <label
                  key={option.value}
                  className={`flex flex-col rounded-lg border p-4 cursor-pointer transition-colors ${
                    watch('complexidadeSenhaMinima') === option.value
                      ? 'border-blue-500 bg-blue-50'
                      : 'hover:bg-gray-50'
                  }`}
                >
                  <input
                    type="radio"
                    value={option.value}
                    {...register('complexidadeSenhaMinima')}
                    className="sr-only"
                  />
                  <div className="flex items-center gap-2 mb-1">
                    <span
                      className={`px-2 py-0.5 rounded text-xs font-medium ${option.color}`}
                    >
                      {option.label}
                    </span>
                  </div>
                  <p className="text-sm text-gray-500">{option.descricao}</p>
                </label>
              ))}
            </div>
          </div>

          <div className="grid gap-4 sm:grid-cols-3">
            <div className="space-y-2">
              <Label htmlFor="tamanhoMinimoSenha">Tamanho Minimo</Label>
              <Input
                id="tamanhoMinimoSenha"
                type="number"
                min={6}
                {...register('tamanhoMinimoSenha')}
              />
              {errors.tamanhoMinimoSenha && (
                <p className="text-sm text-red-500">{errors.tamanhoMinimoSenha.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="diasExpiracaoSenha">Expiracao (dias)</Label>
              <Input
                id="diasExpiracaoSenha"
                type="number"
                min={0}
                {...register('diasExpiracaoSenha')}
              />
              <p className="text-xs text-gray-500">0 = nunca expira</p>
            </div>
            <div className="space-y-2">
              <Label htmlFor="historicoSenhasImpedir">Historico a Impedir</Label>
              <Input
                id="historicoSenhasImpedir"
                type="number"
                min={0}
                {...register('historicoSenhasImpedir')}
              />
              <p className="text-xs text-gray-500">Ultimas senhas que nao podem ser reutilizadas</p>
            </div>
          </div>

          <div className="pt-4 border-t">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('requerer2FA') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('requerer2FA')}
            >
              <div className="flex items-center gap-3">
                <Shield className="h-5 w-5 text-gray-500" />
                <div>
                  <p className="font-medium">Autenticacao em Dois Fatores (2FA)</p>
                  <p className="text-sm text-gray-500">
                    Exigir 2FA para todos os usuarios do sistema
                  </p>
                </div>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('requerer2FA') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('requerer2FA') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Lock className="h-5 w-5" />
            Criptografia e Hash
          </CardTitle>
          <CardDescription>
            Configure os algoritmos de criptografia
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div>
            <Label className="mb-3 block">Algoritmo de Hash de Senhas</Label>
            <div className="grid gap-3">
              {hashOptions.map((option) => (
                <label
                  key={option.value}
                  className={`flex items-center gap-4 rounded-lg border p-4 cursor-pointer transition-colors ${
                    watch('algoritmoHash') === option.value
                      ? 'border-blue-500 bg-blue-50'
                      : 'hover:bg-gray-50'
                  }`}
                >
                  <input
                    type="radio"
                    value={option.value}
                    {...register('algoritmoHash')}
                    className="sr-only"
                  />
                  <div
                    className={`h-4 w-4 rounded-full border-2 ${
                      watch('algoritmoHash') === option.value
                        ? 'border-blue-500 bg-blue-500'
                        : 'border-gray-300'
                    }`}
                  >
                    {watch('algoritmoHash') === option.value && (
                      <div className="h-full w-full flex items-center justify-center">
                        <div className="h-1.5 w-1.5 rounded-full bg-white" />
                      </div>
                    )}
                  </div>
                  <div className="flex-1">
                    <p className="font-medium">{option.label}</p>
                    <p className="text-sm text-gray-500">{option.descricao}</p>
                  </div>
                </label>
              ))}
            </div>
          </div>

          <div className="pt-4 border-t">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('criptografarVotos') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('criptografarVotos')}
            >
              <div className="flex items-center gap-3">
                <Lock className="h-5 w-5 text-gray-500" />
                <div>
                  <p className="font-medium">Criptografar Votos</p>
                  <p className="text-sm text-gray-500">
                    Armazenar votos com criptografia AES-256
                  </p>
                </div>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('criptografarVotos') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('criptografarVotos') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <AlertTriangle className="h-5 w-5" />
            Rate Limiting e IP
          </CardTitle>
          <CardDescription>
            Configure limites de requisicoes e restricoes de IP
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="rateLimitRequests">Limite de Requisicoes</Label>
              <Input
                id="rateLimitRequests"
                type="number"
                min={1}
                {...register('rateLimitRequests')}
              />
              {errors.rateLimitRequests && (
                <p className="text-sm text-red-500">{errors.rateLimitRequests.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="rateLimitWindowMinutos">Janela de Tempo (minutos)</Label>
              <Input
                id="rateLimitWindowMinutos"
                type="number"
                min={1}
                {...register('rateLimitWindowMinutos')}
              />
              {errors.rateLimitWindowMinutos && (
                <p className="text-sm text-red-500">{errors.rateLimitWindowMinutos.message}</p>
              )}
            </div>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="ipWhitelist">IP Whitelist</Label>
              <textarea
                id="ipWhitelist"
                placeholder="192.168.1.1&#10;10.0.0.0/24&#10;..."
                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 font-mono"
                {...register('ipWhitelist')}
              />
              <p className="text-xs text-gray-500">Um IP ou CIDR por linha. Deixe vazio para permitir todos.</p>
            </div>
            <div className="space-y-2">
              <Label htmlFor="ipBlacklist">IP Blacklist</Label>
              <textarea
                id="ipBlacklist"
                placeholder="192.168.1.100&#10;10.0.0.50&#10;..."
                className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 font-mono"
                {...register('ipBlacklist')}
              />
              <p className="text-xs text-gray-500">IPs bloqueados. Um por linha.</p>
            </div>
          </div>

          <div className="pt-4 border-t">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('auditarTodasAcoes') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('auditarTodasAcoes')}
            >
              <div className="flex items-center gap-3">
                <Shield className="h-5 w-5 text-gray-500" />
                <div>
                  <p className="font-medium">Auditoria Completa</p>
                  <p className="text-sm text-gray-500">
                    Registrar todas as acoes dos usuarios no sistema
                  </p>
                </div>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('auditarTodasAcoes') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('auditarTodasAcoes') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>
          </div>
        </CardContent>
      </Card>

      <div className="flex justify-end gap-4">
        <Button
          type="button"
          variant="outline"
          onClick={handleCancel}
          disabled={!isDirty || isSaving}
        >
          Cancelar
        </Button>
        <Button type="submit" disabled={!isDirty || isSaving}>
          {isSaving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
          <Save className="mr-2 h-4 w-4" />
          Salvar Alteracoes
        </Button>
      </div>
    </form>
  )
}
