import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { Loader2, Save, Upload, Building2, Mail, Phone, Globe } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'

const configGeralSchema = z.object({
  nomeSistema: z.string().min(1, 'Nome do sistema e obrigatorio'),
  sigla: z.string().min(1, 'Sigla e obrigatoria'),
  descricao: z.string().optional(),
  logoUrl: z.string().optional(),
  faviconUrl: z.string().optional(),
  emailContato: z.string().email('Email invalido').optional().or(z.literal('')),
  telefoneContato: z.string().optional(),
  enderecoContato: z.string().optional(),
  siteInstitucional: z.string().url('URL invalida').optional().or(z.literal('')),
  cnpj: z.string().optional(),
  textoRodape: z.string().optional(),
  versao: z.string().optional(),
})

type ConfigGeralFormData = z.infer<typeof configGeralSchema>

interface ConfiguracoesGeraisProps {
  data?: ConfigGeralFormData
  isLoading: boolean
  onSave: (data: ConfigGeralFormData) => Promise<void>
}

export function ConfiguracoesGerais({ data, isLoading, onSave }: ConfiguracoesGeraisProps) {
  const [isSaving, setIsSaving] = useState(false)
  const [logoPreview, setLogoPreview] = useState<string | null>(null)

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors, isDirty },
  } = useForm<ConfigGeralFormData>({
    resolver: zodResolver(configGeralSchema),
    defaultValues: {
      nomeSistema: 'CAU Sistema Eleitoral',
      sigla: 'CAU-SE',
      descricao: '',
      logoUrl: '',
      faviconUrl: '',
      emailContato: '',
      telefoneContato: '',
      enderecoContato: '',
      siteInstitucional: '',
      cnpj: '',
      textoRodape: '',
      versao: '1.0.0',
    },
  })

  useEffect(() => {
    if (data) {
      reset(data)
      if (data.logoUrl) {
        setLogoPreview(data.logoUrl)
      }
    }
  }, [data, reset])

  const handleLogoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onloadend = () => {
        setLogoPreview(reader.result as string)
      }
      reader.readAsDataURL(file)
    }
  }

  const onSubmit = async (formData: ConfigGeralFormData) => {
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
            <Building2 className="h-5 w-5" />
            Identidade do Sistema
          </CardTitle>
          <CardDescription>
            Configure as informacoes basicas do sistema eleitoral
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="nomeSistema">Nome do Sistema *</Label>
              <Input
                id="nomeSistema"
                placeholder="CAU Sistema Eleitoral"
                {...register('nomeSistema')}
              />
              {errors.nomeSistema && (
                <p className="text-sm text-red-500">{errors.nomeSistema.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="sigla">Sigla *</Label>
              <Input
                id="sigla"
                placeholder="CAU-SE"
                {...register('sigla')}
              />
              {errors.sigla && (
                <p className="text-sm text-red-500">{errors.sigla.message}</p>
              )}
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="descricao">Descricao</Label>
            <textarea
              id="descricao"
              placeholder="Sistema de votacao eletronica do Conselho de Arquitetura e Urbanismo"
              className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              {...register('descricao')}
            />
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="cnpj">CNPJ</Label>
              <Input
                id="cnpj"
                placeholder="00.000.000/0000-00"
                {...register('cnpj')}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="versao">Versao do Sistema</Label>
              <Input
                id="versao"
                placeholder="1.0.0"
                {...register('versao')}
                disabled
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Upload className="h-5 w-5" />
            Logotipo e Icones
          </CardTitle>
          <CardDescription>
            Configure o logotipo e favicon do sistema
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-6 sm:grid-cols-2">
            <div className="space-y-4">
              <Label>Logo do Sistema</Label>
              <div className="flex items-center gap-4">
                {logoPreview ? (
                  <div className="h-20 w-20 rounded-lg border bg-gray-50 flex items-center justify-center overflow-hidden">
                    <img
                      src={logoPreview}
                      alt="Logo preview"
                      className="max-h-full max-w-full object-contain"
                    />
                  </div>
                ) : (
                  <div className="h-20 w-20 rounded-lg border-2 border-dashed bg-gray-50 flex items-center justify-center">
                    <Building2 className="h-8 w-8 text-gray-400" />
                  </div>
                )}
                <div>
                  <Input
                    type="file"
                    accept="image/*"
                    onChange={handleLogoChange}
                    className="w-full max-w-xs"
                  />
                  <p className="text-xs text-gray-500 mt-1">
                    PNG, JPG ou SVG. Max 2MB.
                  </p>
                </div>
              </div>
              <Input
                placeholder="Ou insira a URL da imagem"
                {...register('logoUrl')}
              />
            </div>

            <div className="space-y-4">
              <Label>Favicon</Label>
              <div className="flex items-center gap-4">
                <div className="h-20 w-20 rounded-lg border-2 border-dashed bg-gray-50 flex items-center justify-center">
                  <Globe className="h-8 w-8 text-gray-400" />
                </div>
                <div>
                  <Input
                    type="file"
                    accept=".ico,.png"
                    className="w-full max-w-xs"
                  />
                  <p className="text-xs text-gray-500 mt-1">
                    ICO ou PNG. 32x32 ou 64x64 px.
                  </p>
                </div>
              </div>
              <Input
                placeholder="Ou insira a URL do favicon"
                {...register('faviconUrl')}
              />
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Mail className="h-5 w-5" />
            Informacoes de Contato
          </CardTitle>
          <CardDescription>
            Dados de contato exibidos para os usuarios
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="emailContato">Email de Contato</Label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  id="emailContato"
                  type="email"
                  placeholder="contato@cau.org.br"
                  className="pl-10"
                  {...register('emailContato')}
                />
              </div>
              {errors.emailContato && (
                <p className="text-sm text-red-500">{errors.emailContato.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="telefoneContato">Telefone de Contato</Label>
              <div className="relative">
                <Phone className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  id="telefoneContato"
                  placeholder="(61) 3555-0000"
                  className="pl-10"
                  {...register('telefoneContato')}
                />
              </div>
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="enderecoContato">Endereco</Label>
            <textarea
              id="enderecoContato"
              placeholder="SRTVS Quadra 702, Bloco E, Asa Sul, Brasilia - DF"
              className="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              {...register('enderecoContato')}
            />
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="siteInstitucional">Site Institucional</Label>
              <div className="relative">
                <Globe className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <Input
                  id="siteInstitucional"
                  type="url"
                  placeholder="https://www.cau.org.br"
                  className="pl-10"
                  {...register('siteInstitucional')}
                />
              </div>
              {errors.siteInstitucional && (
                <p className="text-sm text-red-500">{errors.siteInstitucional.message}</p>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="textoRodape">Texto do Rodape</Label>
              <Input
                id="textoRodape"
                placeholder="© 2024 CAU - Todos os direitos reservados"
                {...register('textoRodape')}
              />
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
