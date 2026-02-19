import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Loader2, Save, Vote, Clock, Shield, CheckCircle } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

const configEleicaoSchema = z.object({
  horasAntesInicioVotacao: z.coerce.number().min(0, 'Valor deve ser positivo'),
  horasAposEncerramento: z.coerce.number().min(0, 'Valor deve ser positivo'),
  permitirVotoAntecipado: z.boolean(),
  permitirVotoPorProcuracao: z.boolean(),
  exibirResultadosParciais: z.boolean(),
  exibirResultadosAposEncerramento: z.boolean(),
  requererJustificativaAusencia: z.boolean(),
  tempoMaximoVotacao: z.coerce.number().min(1, 'Minimo 1 minuto'),
  tentativasMaximasLogin: z.coerce.number().min(1, 'Minimo 1 tentativa'),
  bloquearAposXTentativas: z.coerce.number().min(1, 'Minimo 1 tentativa'),
  tempoBloqueioMinutos: z.coerce.number().min(1, 'Minimo 1 minuto'),
  validarCPFReceita: z.boolean(),
  validarRegistroCAU: z.boolean(),
  permitirCandidaturaMultipla: z.boolean(),
  diasMinimosInscricao: z.coerce.number().min(1, 'Minimo 1 dia'),
  diasMaximosRecurso: z.coerce.number().min(1, 'Minimo 1 dia'),
})

type ConfigEleicaoFormData = z.infer<typeof configEleicaoSchema>

interface ConfiguracoesEleicoesProps {
  data?: ConfigEleicaoFormData
  isLoading: boolean
  onSave: (data: ConfigEleicaoFormData) => Promise<void>
}

export function ConfiguracoesEleicoes({ data, isLoading, onSave }: ConfiguracoesEleicoesProps) {
  const [isSaving, setIsSaving] = useState(false)

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors, isDirty },
  } = useForm<ConfigEleicaoFormData>({
    resolver: zodResolver(configEleicaoSchema),
    defaultValues: {
      horasAntesInicioVotacao: 24,
      horasAposEncerramento: 48,
      permitirVotoAntecipado: false,
      permitirVotoPorProcuracao: false,
      exibirResultadosParciais: false,
      exibirResultadosAposEncerramento: true,
      requererJustificativaAusencia: true,
      tempoMaximoVotacao: 30,
      tentativasMaximasLogin: 5,
      bloquearAposXTentativas: 3,
      tempoBloqueioMinutos: 30,
      validarCPFReceita: true,
      validarRegistroCAU: true,
      permitirCandidaturaMultipla: false,
      diasMinimosInscricao: 15,
      diasMaximosRecurso: 5,
    },
  })

  useEffect(() => {
    if (data) {
      reset(data)
    }
  }, [data, reset])

  const onSubmit = async (formData: ConfigEleicaoFormData) => {
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

  const handleToggle = (field: keyof ConfigEleicaoFormData) => {
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
            Tempos e Prazos
          </CardTitle>
          <CardDescription>
            Configure os prazos padrao para eleicoes
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div className="space-y-2">
              <Label htmlFor="horasAntesInicioVotacao">Horas antes do início (para avisos)</Label>
              <Input
                id="horasAntesInicioVotacao"
                type="number"
                min={0}
                {...register('horasAntesInicioVotacao')}
              />
              {errors.horasAntesInicioVotacao && (
                <p className="text-sm text-red-500">{errors.horasAntesInicioVotacao.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="horasAposEncerramento">Horas após encerramento (para recursos)</Label>
              <Input
                id="horasAposEncerramento"
                type="number"
                min={0}
                {...register('horasAposEncerramento')}
              />
              {errors.horasAposEncerramento && (
                <p className="text-sm text-red-500">{errors.horasAposEncerramento.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="tempoMaximoVotacao">Tempo máximo para votar (minutos)</Label>
              <Input
                id="tempoMaximoVotacao"
                type="number"
                min={1}
                {...register('tempoMaximoVotacao')}
              />
              {errors.tempoMaximoVotacao && (
                <p className="text-sm text-red-500">{errors.tempoMaximoVotacao.message}</p>
              )}
            </div>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="diasMinimosInscricao">Dias minimos para inscrição de chapas</Label>
              <Input
                id="diasMinimosInscricao"
                type="number"
                min={1}
                {...register('diasMinimosInscricao')}
              />
              {errors.diasMinimosInscricao && (
                <p className="text-sm text-red-500">{errors.diasMinimosInscricao.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="diasMaximosRecurso">Dias maximos para interposicao de recursos</Label>
              <Input
                id="diasMaximosRecurso"
                type="number"
                min={1}
                {...register('diasMaximosRecurso')}
              />
              {errors.diasMaximosRecurso && (
                <p className="text-sm text-red-500">{errors.diasMaximosRecurso.message}</p>
              )}
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Vote className="h-5 w-5" />
            Opcoes de Votacao
          </CardTitle>
          <CardDescription>
            Configure as regras de votacao
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('permitirVotoAntecipado') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('permitirVotoAntecipado')}
            >
              <div>
                <p className="font-medium">Permitir voto antecipado</p>
                <p className="text-sm text-gray-500">
                  Eleitores podem votar antes da data oficial de inicio
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('permitirVotoAntecipado') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('permitirVotoAntecipado') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('permitirVotoPorProcuracao') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('permitirVotoPorProcuracao')}
            >
              <div>
                <p className="font-medium">Permitir voto por procuracao</p>
                <p className="text-sm text-gray-500">
                  Permite que um eleitor vote em nome de outro mediante procuracao
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('permitirVotoPorProcuracao') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('permitirVotoPorProcuracao') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('exibirResultadosParciais') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('exibirResultadosParciais')}
            >
              <div>
                <p className="font-medium">Exibir resultados parciais</p>
                <p className="text-sm text-gray-500">
                  Mostra contagem parcial durante a votacao (nao recomendado)
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('exibirResultadosParciais') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('exibirResultadosParciais') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('exibirResultadosAposEncerramento') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('exibirResultadosAposEncerramento')}
            >
              <div>
                <p className="font-medium">Exibir resultados após encerramento</p>
                <p className="text-sm text-gray-500">
                  Divulga automaticamente os resultados quando a votacao encerrar
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('exibirResultadosAposEncerramento') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('exibirResultadosAposEncerramento') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('requererJustificativaAusencia') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('requererJustificativaAusencia')}
            >
              <div>
                <p className="font-medium">Exigir justificativa de ausencia</p>
                <p className="text-sm text-gray-500">
                  Eleitores que nao votarem devem justificar a ausencia
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('requererJustificativaAusencia') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('requererJustificativaAusencia') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('permitirCandidaturaMultipla') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('permitirCandidaturaMultipla')}
            >
              <div>
                <p className="font-medium">Permitir candidatura multipla</p>
                <p className="text-sm text-gray-500">
                  Permite que um profissional participe de mais de uma chapa
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('permitirCandidaturaMultipla') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('permitirCandidaturaMultipla') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
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
            <CheckCircle className="h-5 w-5" />
            Validacoes
          </CardTitle>
          <CardDescription>
            Configure as validacoes de eleitores e candidatos
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('validarCPFReceita') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('validarCPFReceita')}
            >
              <div>
                <p className="font-medium">Validar CPF na Receita Federal</p>
                <p className="text-sm text-gray-500">
                  Verifica a autenticidade do CPF junto a Receita Federal
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('validarCPFReceita') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('validarCPFReceita') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('validarRegistroCAU') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('validarRegistroCAU')}
            >
              <div>
                <p className="font-medium">Validar Registro no CAU</p>
                <p className="text-sm text-gray-500">
                  Verifica se o profissional possui registro ativo no CAU
                </p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('validarRegistroCAU') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('validarRegistroCAU') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>
          </div>

          <div className="grid gap-4 sm:grid-cols-3 pt-4 border-t">
            <div className="space-y-2">
              <Label htmlFor="tentativasMaximasLogin">Tentativas maximas de login</Label>
              <Input
                id="tentativasMaximasLogin"
                type="number"
                min={1}
                {...register('tentativasMaximasLogin')}
              />
              {errors.tentativasMaximasLogin && (
                <p className="text-sm text-red-500">{errors.tentativasMaximasLogin.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="bloquearAposXTentativas">Bloquear após X tentativas</Label>
              <Input
                id="bloquearAposXTentativas"
                type="number"
                min={1}
                {...register('bloquearAposXTentativas')}
              />
              {errors.bloquearAposXTentativas && (
                <p className="text-sm text-red-500">{errors.bloquearAposXTentativas.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="tempoBloqueioMinutos">Tempo de bloqueio (minutos)</Label>
              <Input
                id="tempoBloqueioMinutos"
                type="number"
                min={1}
                {...register('tempoBloqueioMinutos')}
              />
              {errors.tempoBloqueioMinutos && (
                <p className="text-sm text-red-500">{errors.tempoBloqueioMinutos.message}</p>
              )}
            </div>
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
