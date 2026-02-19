import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Loader2, Save, Mail, Bell, MessageSquare, Send, CheckCircle, XCircle } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

const configNotificacaoSchema = z.object({
  emailHabilitado: z.boolean(),
  smsHabilitado: z.boolean(),
  pushHabilitado: z.boolean(),
  notificarNovaEleicao: z.boolean(),
  notificarInicioVotacao: z.boolean(),
  notificarEncerramentoVotacao: z.boolean(),
  notificarResultado: z.boolean(),
  notificarDenuncia: z.boolean(),
  notificarImpugnacao: z.boolean(),
  notificarJulgamento: z.boolean(),
  remetenteEmail: z.string().email('Email inválido').optional().or(z.literal('')),
  nomeRemetente: z.string().optional(),
  templateEmailBoasVindas: z.string().optional(),
  templateEmailRecuperacaoSenha: z.string().optional(),
  templateEmailNotificacao: z.string().optional(),
})

type ConfigNotificacaoFormData = z.infer<typeof configNotificacaoSchema>

interface ConfiguracoesNotificacoesProps {
  data?: ConfigNotificacaoFormData
  isLoading: boolean
  onSave: (data: ConfigNotificacaoFormData) => Promise<void>
  onTestarEmail: (destinatario: string) => Promise<{ sucesso: boolean; erro?: string }>
}

export function ConfiguracoesNotificacoes({
  data,
  isLoading,
  onSave,
  onTestarEmail,
}: ConfiguracoesNotificacoesProps) {
  const [isSaving, setIsSaving] = useState(false)
  const [testEmail, setTestEmail] = useState('')
  const [testResult, setTestResult] = useState<{ sucesso: boolean; erro?: string } | null>(null)
  const [isTesting, setIsTesting] = useState(false)

  const {
    register,
    handleSubmit,
    reset,
    watch,
    setValue,
    formState: { errors, isDirty },
  } = useForm<ConfigNotificacaoFormData>({
    resolver: zodResolver(configNotificacaoSchema),
    defaultValues: {
      emailHabilitado: true,
      smsHabilitado: false,
      pushHabilitado: false,
      notificarNovaEleicao: true,
      notificarInicioVotacao: true,
      notificarEncerramentoVotacao: true,
      notificarResultado: true,
      notificarDenuncia: true,
      notificarImpugnacao: true,
      notificarJulgamento: true,
      remetenteEmail: 'noreply@cau.org.br',
      nomeRemetente: 'CAU Sistema Eleitoral',
      templateEmailBoasVindas: '',
      templateEmailRecuperacaoSenha: '',
      templateEmailNotificacao: '',
    },
  })

  useEffect(() => {
    if (data) {
      reset(data)
    }
  }, [data, reset])

  const onSubmit = async (formData: ConfigNotificacaoFormData) => {
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

  const handleToggle = (field: keyof ConfigNotificacaoFormData) => {
    const currentValue = watch(field) as boolean
    setValue(field, !currentValue, { shouldDirty: true })
  }

  const handleTestarEmail = async () => {
    if (!testEmail) return

    setIsTesting(true)
    setTestResult(null)
    try {
      const result = await onTestarEmail(testEmail)
      setTestResult(result)
    } catch {
      setTestResult({ sucesso: false, erro: 'Erro ao enviar email de teste' })
    } finally {
      setIsTesting(false)
    }
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
            <Bell className="h-5 w-5" />
            Canais de Notificação
          </CardTitle>
          <CardDescription>
            Configure quais canais de notificação estão habilitados
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-3">
            <label
              className={`flex flex-col items-center justify-center rounded-lg border p-6 cursor-pointer transition-colors ${
                watch('emailHabilitado') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('emailHabilitado')}
            >
              <Mail
                className={`h-10 w-10 mb-2 ${
                  watch('emailHabilitado') ? 'text-blue-500' : 'text-gray-400'
                }`}
              />
              <p className="font-medium">Email</p>
              <p className="text-sm text-gray-500 text-center mt-1">
                {watch('emailHabilitado') ? 'Habilitado' : 'Desabilitado'}
              </p>
            </label>

            <label
              className={`flex flex-col items-center justify-center rounded-lg border p-6 cursor-pointer transition-colors ${
                watch('smsHabilitado') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('smsHabilitado')}
            >
              <MessageSquare
                className={`h-10 w-10 mb-2 ${
                  watch('smsHabilitado') ? 'text-blue-500' : 'text-gray-400'
                }`}
              />
              <p className="font-medium">SMS</p>
              <p className="text-sm text-gray-500 text-center mt-1">
                {watch('smsHabilitado') ? 'Habilitado' : 'Desabilitado'}
              </p>
            </label>

            <label
              className={`flex flex-col items-center justify-center rounded-lg border p-6 cursor-pointer transition-colors ${
                watch('pushHabilitado') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('pushHabilitado')}
            >
              <Bell
                className={`h-10 w-10 mb-2 ${
                  watch('pushHabilitado') ? 'text-blue-500' : 'text-gray-400'
                }`}
              />
              <p className="font-medium">Push</p>
              <p className="text-sm text-gray-500 text-center mt-1">
                {watch('pushHabilitado') ? 'Habilitado' : 'Desabilitado'}
              </p>
            </label>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Mail className="h-5 w-5" />
            Configurações de Email
          </CardTitle>
          <CardDescription>
            Configure o remetente e templates de email
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="nomeRemetente">Nome do Remetente</Label>
              <Input
                id="nomeRemetente"
                placeholder="CAU Sistema Eleitoral"
                {...register('nomeRemetente')}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="remetenteEmail">Email do Remetente</Label>
              <Input
                id="remetenteEmail"
                type="email"
                placeholder="noreply@cau.org.br"
                {...register('remetenteEmail')}
              />
              {errors.remetenteEmail && (
                <p className="text-sm text-red-500">{errors.remetenteEmail.message}</p>
              )}
            </div>
          </div>

          <div className="border-t pt-4">
            <Label className="text-sm font-medium">Testar Envio de Email</Label>
            <p className="text-sm text-gray-500 mb-3">
              Envie um email de teste para verificar se as configurações estão corretas
            </p>
            <div className="flex gap-2">
              <Input
                type="email"
                placeholder="Digite um email para teste"
                value={testEmail}
                onChange={(e) => setTestEmail(e.target.value)}
                className="max-w-xs"
              />
              <Button
                type="button"
                variant="outline"
                onClick={handleTestarEmail}
                disabled={!testEmail || isTesting}
              >
                {isTesting ? (
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                ) : (
                  <Send className="mr-2 h-4 w-4" />
                )}
                Testar
              </Button>
            </div>
            {testResult && (
              <div
                className={`mt-2 flex items-center gap-2 text-sm ${
                  testResult.sucesso ? 'text-green-600' : 'text-red-600'
                }`}
              >
                {testResult.sucesso ? (
                  <>
                    <CheckCircle className="h-4 w-4" />
                    Email enviado com sucesso!
                  </>
                ) : (
                  <>
                    <XCircle className="h-4 w-4" />
                    {testResult.erro || 'Falha ao enviar email'}
                  </>
                )}
              </div>
            )}
          </div>

          <div className="border-t pt-4 space-y-4">
            <div className="space-y-2">
              <Label htmlFor="templateEmailBoasVindas">Template de Boas-vindas</Label>
              <textarea
                id="templateEmailBoasVindas"
                placeholder="Ola {nome}, bem-vindo ao Sistema Eleitoral do CAU..."
                className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 font-mono"
                {...register('templateEmailBoasVindas')}
              />
              <p className="text-xs text-gray-500">
                Variaveis disponiveis: {'{nome}'}, {'{email}'}, {'{cpf}'}
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="templateEmailRecuperacaoSenha">Template de Recuperação de Senha</Label>
              <textarea
                id="templateEmailRecuperacaoSenha"
                placeholder="Ola {nome}, voce solicitou a recuperacao de senha..."
                className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 font-mono"
                {...register('templateEmailRecuperacaoSenha')}
              />
              <p className="text-xs text-gray-500">
                Variaveis disponiveis: {'{nome}'}, {'{link}'}, {'{expiracao}'}
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="templateEmailNotificacao">Template de Notificação Geral</Label>
              <textarea
                id="templateEmailNotificacao"
                placeholder="Ola {nome}, {mensagem}..."
                className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 font-mono"
                {...register('templateEmailNotificacao')}
              />
              <p className="text-xs text-gray-500">
                Variaveis disponiveis: {'{nome}'}, {'{mensagem}'}, {'{assunto}'}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Bell className="h-5 w-5" />
            Eventos de Notificação
          </CardTitle>
          <CardDescription>
            Configure quais eventos geram notificações automaticas
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarNovaEleicao') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarNovaEleicao')}
            >
              <div>
                <p className="font-medium">Nova Eleição</p>
                <p className="text-sm text-gray-500">Quando uma nova eleição for criada</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarNovaEleicao') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarNovaEleicao') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarInicioVotacao') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarInicioVotacao')}
            >
              <div>
                <p className="font-medium">Início da Votação</p>
                <p className="text-sm text-gray-500">Quando a votação iniciar</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarInicioVotacao') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarInicioVotacao') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarEncerramentoVotacao') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarEncerramentoVotacao')}
            >
              <div>
                <p className="font-medium">Encerramento da Votação</p>
                <p className="text-sm text-gray-500">Quando a votação encerrar</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarEncerramentoVotacao') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarEncerramentoVotacao') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarResultado') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarResultado')}
            >
              <div>
                <p className="font-medium">Resultado</p>
                <p className="text-sm text-gray-500">Quando o resultado for divulgado</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarResultado') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarResultado') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarDenuncia') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarDenuncia')}
            >
              <div>
                <p className="font-medium">Denúncia</p>
                <p className="text-sm text-gray-500">Quando uma denúncia for registrada</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarDenuncia') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarDenuncia') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors ${
                watch('notificarImpugnacao') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarImpugnacao')}
            >
              <div>
                <p className="font-medium">Impugnação</p>
                <p className="text-sm text-gray-500">Quando uma impugnação for registrada</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarImpugnacao') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarImpugnacao') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
                  }`}
                />
              </div>
            </label>

            <label
              className={`flex items-center justify-between rounded-lg border p-4 cursor-pointer transition-colors sm:col-span-2 ${
                watch('notificarJulgamento') ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'
              }`}
              onClick={() => handleToggle('notificarJulgamento')}
            >
              <div>
                <p className="font-medium">Julgamento</p>
                <p className="text-sm text-gray-500">Quando um julgamento for realizado</p>
              </div>
              <div
                className={`h-6 w-11 rounded-full transition-colors ${
                  watch('notificarJulgamento') ? 'bg-blue-500' : 'bg-gray-200'
                }`}
              >
                <div
                  className={`h-5 w-5 mt-0.5 rounded-full bg-white shadow-sm transition-transform ${
                    watch('notificarJulgamento') ? 'translate-x-5 ml-0.5' : 'translate-x-0.5'
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
          Salvar Alterações
        </Button>
      </div>
    </form>
  )
}
