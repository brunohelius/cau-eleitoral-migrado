import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import { ArrowLeft, Loader2, Save, Upload, X, AlertOctagon, FileText, Info } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useToast } from '@/hooks/use-toast'
import {
  impugnacoesService,
  TipoImpugnacao,
  CreateImpugnacaoRequest,
} from '@/services/impugnacoes'
import { eleicoesService } from '@/services/eleicoes'
import { chapasService } from '@/services/chapas'

const impugnacaoSchema = z.object({
  eleicaoId: z.string().min(1, 'Selecione uma eleicao'),
  tipo: z.string().min(1, 'Selecione o tipo de impugnacao'),
  chapaId: z.string().optional(),
  candidatoId: z.string().optional(),
  fundamentacao: z.string().min(100, 'Fundamentacao deve ter no minimo 100 caracteres'),
  normasVioladas: z.string().optional(),
  pedido: z.string().min(20, 'Pedido deve ter no minimo 20 caracteres'),
})

type ImpugnacaoFormData = z.infer<typeof impugnacaoSchema>

const tipoLabels: Record<number, string> = {
  [TipoImpugnacao.CANDIDATURA]: 'Candidatura',
  [TipoImpugnacao.CHAPA]: 'Chapa',
  [TipoImpugnacao.ELEICAO]: 'Eleicao',
  [TipoImpugnacao.RESULTADO]: 'Resultado',
  [TipoImpugnacao.VOTACAO]: 'Votacao',
}

const normasExemplos = [
  'Art. 15 do Regimento Eleitoral - Requisitos de elegibilidade',
  'Art. 20 do Regimento Eleitoral - Documentacao obrigatoria',
  'Art. 25 do Regimento Eleitoral - Prazos e procedimentos',
  'Art. 30 do Regimento Eleitoral - Composicao de chapas',
  'Lei 12.378/2010 - Regulamentacao do exercicio da Arquitetura',
  'Resolucao CAU/BR n. 139 - Normas eleitorais',
]

export function ImpugnacaoFormPage() {
  const navigate = useNavigate()
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [anexos, setAnexos] = useState<File[]>([])

  // Fetch eleicoes
  const { data: eleicoes, isLoading: loadingEleicoes } = useQuery({
    queryKey: ['eleicoes-ativas'],
    queryFn: eleicoesService.getAtivas,
  })

  const {
    register,
    handleSubmit,
    watch,
    setValue,
    formState: { errors },
  } = useForm<ImpugnacaoFormData>({
    resolver: zodResolver(impugnacaoSchema),
    defaultValues: {
      tipo: String(TipoImpugnacao.CHAPA),
    },
  })

  const selectedEleicaoId = watch('eleicaoId')
  const selectedTipo = watch('tipo')

  // Fetch chapas when eleicao is selected
  const { data: chapas = [], isLoading: loadingChapas } = useQuery({
    queryKey: ['chapas', selectedEleicaoId],
    queryFn: () => chapasService.getByEleicao(selectedEleicaoId),
    enabled: !!selectedEleicaoId,
  })

  const createMutation = useMutation({
    mutationFn: async (data: ImpugnacaoFormData) => {
      const request: CreateImpugnacaoRequest = {
        eleicaoId: data.eleicaoId,
        tipo: Number(data.tipo),
        fundamentacao: data.fundamentacao,
        normasVioladas: data.normasVioladas,
        pedido: data.pedido,
        chapaId: data.chapaId || undefined,
        candidatoId: data.candidatoId || undefined,
      }

      const impugnacao = await impugnacoesService.create(request)

      // Upload anexos if any
      if (anexos.length > 0) {
        for (const anexo of anexos) {
          await impugnacoesService.uploadAnexo(impugnacao.id, anexo)
        }
      }

      return impugnacao
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ['impugnacoes'] })
      toast({
        title: 'Impugnação registrada com sucesso!',
        description: `Protocolo: ${data.protocolo}. A impugnacao sera analisada pela Comissao Eleitoral.`,
      })
      navigate(`/impugnacoes/${data.id}`)
    },
    onError: (error: any) => {
      toast({
        variant: 'destructive',
        title: 'Erro ao registrar impugnação',
        description: error.response?.data?.message || 'Tente novamente mais tarde.',
      })
    },
  })

  const onSubmit = (data: ImpugnacaoFormData) => {
    createMutation.mutate(data)
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || [])
    const validFiles = files.filter((file) => {
      const maxSize = 10 * 1024 * 1024 // 10MB
      if (file.size > maxSize) {
        toast({
          variant: 'destructive',
          title: 'Arquivo muito grande',
          description: `${file.name} excede o limite de 10MB.`,
        })
        return false
      }
      return true
    })
    setAnexos((prev) => [...prev, ...validFiles])
  }

  const handleRemoveAnexo = (index: number) => {
    setAnexos((prev) => prev.filter((_, i) => i !== index))
  }

  const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
  }

  const needsChapaSelection = selectedTipo === String(TipoImpugnacao.CHAPA)
  const needsCandidatoSelection = selectedTipo === String(TipoImpugnacao.CANDIDATURA)

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link to="/impugnacoes">
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-5 w-5" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Nova Impugnação</h1>
          <p className="text-gray-600">Registre uma impugnação no processo eleitoral</p>
        </div>
      </div>

      <form onSubmit={handleSubmit(onSubmit)}>
        <div className="grid gap-6">
          {/* Tipo e Eleicao */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertOctagon className="h-5 w-5 text-red-500" />
                Dados da Impugnacao
              </CardTitle>
              <CardDescription>Selecione o tipo e a eleição relacionada</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label htmlFor="tipo">Tipo de Impugnação *</Label>
                  <Select
                    value={selectedTipo}
                    onValueChange={(value) => setValue('tipo', value)}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      {Object.entries(tipoLabels).map(([value, label]) => (
                        <SelectItem key={value} value={value}>
                          {label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {errors.tipo && (
                    <p className="text-sm text-red-500">{errors.tipo.message}</p>
                  )}
                </div>

                <div className="space-y-2">
                  <Label htmlFor="eleicaoId">Eleição *</Label>
                  <Select
                    value={selectedEleicaoId}
                    onValueChange={(value) => setValue('eleicaoId', value)}
                    disabled={loadingEleicoes}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione uma eleicao" />
                    </SelectTrigger>
                    <SelectContent>
                      {eleicoes?.map((eleicao) => (
                        <SelectItem key={eleicao.id} value={eleicao.id}>
                          {eleicao.nome} ({eleicao.ano})
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {errors.eleicaoId && (
                    <p className="text-sm text-red-500">{errors.eleicaoId.message}</p>
                  )}
                </div>
              </div>

              {/* Chapa Selection */}
              {needsChapaSelection && selectedEleicaoId && (
                <div className="space-y-2">
                  <Label htmlFor="chapaId">Chapa a ser Impugnada *</Label>
                  <Select
                    value={watch('chapaId') || ''}
                    onValueChange={(value) => setValue('chapaId', value)}
                    disabled={loadingChapas}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a chapa" />
                    </SelectTrigger>
                    <SelectContent>
                      {chapas.map((chapa) => (
                        <SelectItem key={chapa.id} value={chapa.id}>
                          {chapa.numero}. {chapa.nome}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {loadingChapas && (
                    <p className="text-sm text-gray-500">Carregando chapas...</p>
                  )}
                </div>
              )}

              {/* Candidato Selection */}
              {needsCandidatoSelection && selectedEleicaoId && (
                <div className="space-y-2">
                  <Label htmlFor="candidatoId">Candidato a ser Impugnado *</Label>
                  <Input
                    id="candidatoId"
                    placeholder="ID do candidato"
                    {...register('candidatoId')}
                  />
                  <p className="text-xs text-gray-500">
                    Em producao, este sera um campo de busca de candidatos.
                  </p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Fundamentacao */}
          <Card>
            <CardHeader>
              <CardTitle>Fundamentação</CardTitle>
              <CardDescription>
                Descreva detalhadamente os fatos e fundamentos juridicos da impugnacao
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="fundamentacao">Fundamentação de Fato e de Direito *</Label>
                <Textarea
                  id="fundamentacao"
                  placeholder="Descreva detalhadamente os fatos que motivam a impugnacao e os fundamentos juridicos que a sustentam. Seja o mais especifico possivel, citando datas, documentos e testemunhas quando aplicavel..."
                  {...register('fundamentacao')}
                  rows={8}
                />
                {errors.fundamentacao && (
                  <p className="text-sm text-red-500">{errors.fundamentacao.message}</p>
                )}
                <p className="text-xs text-gray-500">
                  Minimo de 100 caracteres. Seja o mais detalhado possivel.
                </p>
              </div>

              <div className="space-y-2">
                <Label htmlFor="normasVioladas">Normas Violadas</Label>
                <Textarea
                  id="normasVioladas"
                  placeholder="Indique os artigos, resolucoes ou leis que foram violados..."
                  {...register('normasVioladas')}
                  rows={3}
                />
                <div className="rounded-lg bg-gray-50 p-3">
                  <p className="text-xs font-medium text-gray-700 mb-2">Exemplos de normas:</p>
                  <ul className="text-xs text-gray-600 space-y-1">
                    {normasExemplos.map((norma, index) => (
                      <li key={index}>- {norma}</li>
                    ))}
                  </ul>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="pedido">Pedido *</Label>
                <Textarea
                  id="pedido"
                  placeholder="Especifique claramente o que voce solicita com esta impugnacao (ex: anulacao do registro da chapa, inabilitacao do candidato, etc.)..."
                  {...register('pedido')}
                  rows={4}
                />
                {errors.pedido && (
                  <p className="text-sm text-red-500">{errors.pedido.message}</p>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Anexos */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <FileText className="h-5 w-5" />
                Documentos Comprobatorios
              </CardTitle>
              <CardDescription>
                Anexe documentos que comprovem suas alegacoes (maximo 10MB por arquivo)
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                  <input
                    type="file"
                    id="anexos"
                    multiple
                    className="hidden"
                    onChange={handleFileChange}
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
                  />
                  <label htmlFor="anexos" className="cursor-pointer">
                    <Upload className="h-8 w-8 mx-auto text-gray-400 mb-2" />
                    <p className="text-sm text-gray-500">
                      Clique para selecionar ou arraste arquivos
                    </p>
                    <p className="text-xs text-gray-400 mt-1">
                      PDF, DOC, DOCX, JPG, PNG, XLS, XLSX (max. 10MB cada)
                    </p>
                  </label>
                </div>

                {anexos.length > 0 && (
                  <div className="space-y-2">
                    <p className="text-sm font-medium text-gray-700">
                      {anexos.length} arquivo(s) selecionado(s)
                    </p>
                    {anexos.map((file, index) => (
                      <div
                        key={index}
                        className="flex items-center justify-between rounded-lg border p-3"
                      >
                        <div className="flex items-center gap-3">
                          <FileText className="h-5 w-5 text-gray-400" />
                          <div>
                            <span className="text-sm font-medium">{file.name}</span>
                            <p className="text-xs text-gray-500">{formatFileSize(file.size)}</p>
                          </div>
                        </div>
                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          onClick={() => handleRemoveAnexo(index)}
                        >
                          <X className="h-4 w-4 text-red-500" />
                        </Button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Informacoes Importantes */}
          <Card className="border-blue-200 bg-blue-50">
            <CardContent className="pt-6">
              <div className="flex gap-3">
                <Info className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                  <h4 className="font-medium text-blue-900">Informações Importantes</h4>
                  <ul className="text-sm text-blue-800 mt-2 space-y-1">
                    <li>- A impugnação deve ser apresentada dentro do prazo estabelecido pelo calendário eleitoral</li>
                    <li>- Impugnações intempestivas não seráo conhecidas</li>
                    <li>- O impugnado será notificado e tera prazo para apresentar defesa</li>
                    <li>- A decisao será proferida pela Comissão Eleitoral</li>
                    <li>- Cabe recurso da decisao no prazo regulamentar</li>
                  </ul>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Alerta */}
          <Card className="border-yellow-200 bg-yellow-50">
            <CardContent className="pt-6">
              <div className="flex gap-3">
                <AlertOctagon className="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                <div>
                  <h4 className="font-medium text-yellow-800">Atencao</h4>
                  <p className="text-sm text-yellow-700 mt-1">
                    Ao registrar esta impugnacao, voce declara que as informacoes prestadas sao
                    verdadeiras e que tem ciencia de que a prestacao de informacoes falsas pode
                    caracterizar crime previsto no Codigo Penal.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="flex justify-end gap-4">
            <Link to="/impugnacoes">
              <Button type="button" variant="outline">
                Cancelar
              </Button>
            </Link>
            <Button type="submit" disabled={createMutation.isPending}>
              {createMutation.isPending && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              <Save className="mr-2 h-4 w-4" />
              Registrar Impugnacao
            </Button>
          </div>
        </div>
      </form>
    </div>
  )
}
