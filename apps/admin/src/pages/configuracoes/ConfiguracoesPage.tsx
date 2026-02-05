import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Settings, Building2, Vote, Bell, Shield, FileText, Loader2, RefreshCw, Download, Upload } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { useToast } from '@/hooks/use-toast'
import { configuracoesService } from '@/services/configuracoes'
import {
  ConfiguracoesGerais,
  ConfiguracoesEleicoes,
  ConfiguracoesNotificacoes,
  ConfiguracoesSeguranca,
  ConfiguracoesLogs,
} from './components'

export function ConfiguracoesPage() {
  const { toast } = useToast()
  const queryClient = useQueryClient()
  const [activeTab, setActiveTab] = useState('geral')

  // Queries para carregar dados de cada aba
  const { data: configGeral, isLoading: isLoadingGeral } = useQuery({
    queryKey: ['configuracoes', 'geral'],
    queryFn: async () => {
      // Mock data - em producao viria da API
      return {
        nomeSistema: 'CAU Sistema Eleitoral',
        sigla: 'CAU-SE',
        descricao: 'Sistema de votacao eletronica do Conselho de Arquitetura e Urbanismo',
        logoUrl: '',
        faviconUrl: '',
        emailContato: 'contato@cau.org.br',
        telefoneContato: '(61) 3555-0000',
        enderecoContato: 'SRTVS Quadra 702, Bloco E, Asa Sul, Brasilia - DF',
        siteInstitucional: 'https://www.cau.org.br',
        cnpj: '14.702.767/0001-00',
        textoRodape: '2024 CAU - Todos os direitos reservados',
        versao: '1.0.0',
      }
    },
    enabled: activeTab === 'geral',
  })

  const { data: configEleicao, isLoading: isLoadingEleicao } = useQuery({
    queryKey: ['configuracoes', 'eleicao'],
    queryFn: configuracoesService.getConfiguracoesEleicao,
    enabled: activeTab === 'eleicoes',
  })

  const { data: configNotificacao, isLoading: isLoadingNotificacao } = useQuery({
    queryKey: ['configuracoes', 'notificacao'],
    queryFn: configuracoesService.getConfiguracoesNotificacao,
    enabled: activeTab === 'notificacoes',
  })

  const { data: configSeguranca, isLoading: isLoadingSeguranca } = useQuery({
    queryKey: ['configuracoes', 'seguranca'],
    queryFn: configuracoesService.getConfiguracoesSeguranca,
    enabled: activeTab === 'seguranca',
  })

  // Mutations para salvar
  const saveGeralMutation = useMutation({
    mutationFn: async (data: any) => {
      // Em producao seria uma chamada real a API
      await new Promise((resolve) => setTimeout(resolve, 1000))
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['configuracoes', 'geral'] })
      toast({
        title: 'Configuracoes salvas',
        description: 'As configuracoes gerais foram atualizadas com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro ao salvar',
        description: 'Nao foi possivel salvar as configuracoes. Tente novamente.',
      })
    },
  })

  const saveEleicaoMutation = useMutation({
    mutationFn: configuracoesService.updateConfiguracoesEleicao,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['configuracoes', 'eleicao'] })
      toast({
        title: 'Configuracoes salvas',
        description: 'As configuracoes de eleicao foram atualizadas com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro ao salvar',
        description: 'Nao foi possivel salvar as configuracoes. Tente novamente.',
      })
    },
  })

  const saveNotificacaoMutation = useMutation({
    mutationFn: configuracoesService.updateConfiguracoesNotificacao,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['configuracoes', 'notificacao'] })
      toast({
        title: 'Configuracoes salvas',
        description: 'As configuracoes de notificacao foram atualizadas com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro ao salvar',
        description: 'Nao foi possivel salvar as configuracoes. Tente novamente.',
      })
    },
  })

  const saveSegurancaMutation = useMutation({
    mutationFn: configuracoesService.updateConfiguracoesSeguranca,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['configuracoes', 'seguranca'] })
      toast({
        title: 'Configuracoes salvas',
        description: 'As configuracoes de seguranca foram atualizadas com sucesso.',
      })
    },
    onError: () => {
      toast({
        variant: 'destructive',
        title: 'Erro ao salvar',
        description: 'Nao foi possivel salvar as configuracoes. Tente novamente.',
      })
    },
  })

  const handleTestarEmail = async (destinatario: string) => {
    try {
      const result = await configuracoesService.testarEmail(destinatario)
      return result
    } catch {
      return { sucesso: false, erro: 'Erro ao enviar email de teste' }
    }
  }

  const handleExportarConfiguracoes = async () => {
    try {
      const blob = await configuracoesService.exportarConfiguracoes()
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `configuracoes-backup-${new Date().toISOString().split('T')[0]}.json`
      a.click()
      URL.revokeObjectURL(url)
      toast({
        title: 'Exportacao concluida',
        description: 'As configuracoes foram exportadas com sucesso.',
      })
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro na exportacao',
        description: 'Nao foi possivel exportar as configuracoes.',
      })
    }
  }

  const handleImportarConfiguracoes = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0]
    if (!file) return

    try {
      const result = await configuracoesService.importarConfiguracoes(file)
      if (result.sucesso > 0) {
        queryClient.invalidateQueries({ queryKey: ['configuracoes'] })
        toast({
          title: 'Importacao concluida',
          description: `${result.sucesso} configuracoes importadas com sucesso.`,
        })
      }
      if (result.erros.length > 0) {
        toast({
          variant: 'destructive',
          title: 'Erros na importacao',
          description: `${result.erros.length} erros encontrados.`,
        })
      }
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro na importacao',
        description: 'Nao foi possivel importar as configuracoes.',
      })
    }
    // Limpa o input
    event.target.value = ''
  }

  const handleResetarPadrao = async () => {
    if (!confirm('Tem certeza que deseja restaurar todas as configuracoes para os valores padrao? Esta acao nao pode ser desfeita.')) {
      return
    }

    try {
      await configuracoesService.resetarPadrao()
      queryClient.invalidateQueries({ queryKey: ['configuracoes'] })
      toast({
        title: 'Configuracoes restauradas',
        description: 'Todas as configuracoes foram restauradas para os valores padrao.',
      })
    } catch {
      toast({
        variant: 'destructive',
        title: 'Erro ao restaurar',
        description: 'Nao foi possivel restaurar as configuracoes.',
      })
    }
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Configuracoes</h1>
          <p className="text-gray-600">Gerencie as configuracoes do sistema eleitoral</p>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handleExportarConfiguracoes}>
            <Download className="mr-2 h-4 w-4" />
            Exportar
          </Button>
          <label>
            <Button variant="outline" asChild>
              <span>
                <Upload className="mr-2 h-4 w-4" />
                Importar
              </span>
            </Button>
            <input
              type="file"
              accept=".json"
              className="hidden"
              onChange={handleImportarConfiguracoes}
            />
          </label>
          <Button variant="outline" onClick={handleResetarPadrao}>
            <RefreshCw className="mr-2 h-4 w-4" />
            Restaurar Padrao
          </Button>
        </div>
      </div>

      <Tabs value={activeTab} onValueChange={setActiveTab} className="space-y-6">
        <TabsList className="grid w-full grid-cols-5 lg:w-auto lg:inline-grid">
          <TabsTrigger value="geral" className="flex items-center gap-2">
            <Building2 className="h-4 w-4" />
            <span className="hidden sm:inline">Geral</span>
          </TabsTrigger>
          <TabsTrigger value="eleicoes" className="flex items-center gap-2">
            <Vote className="h-4 w-4" />
            <span className="hidden sm:inline">Eleicoes</span>
          </TabsTrigger>
          <TabsTrigger value="notificacoes" className="flex items-center gap-2">
            <Bell className="h-4 w-4" />
            <span className="hidden sm:inline">Notificacoes</span>
          </TabsTrigger>
          <TabsTrigger value="seguranca" className="flex items-center gap-2">
            <Shield className="h-4 w-4" />
            <span className="hidden sm:inline">Seguranca</span>
          </TabsTrigger>
          <TabsTrigger value="logs" className="flex items-center gap-2">
            <FileText className="h-4 w-4" />
            <span className="hidden sm:inline">Logs</span>
          </TabsTrigger>
        </TabsList>

        <TabsContent value="geral">
          <ConfiguracoesGerais
            data={configGeral}
            isLoading={isLoadingGeral}
            onSave={async (data) => {
              await saveGeralMutation.mutateAsync(data)
            }}
          />
        </TabsContent>

        <TabsContent value="eleicoes">
          <ConfiguracoesEleicoes
            data={configEleicao}
            isLoading={isLoadingEleicao}
            onSave={async (data) => {
              await saveEleicaoMutation.mutateAsync(data)
            }}
          />
        </TabsContent>

        <TabsContent value="notificacoes">
          <ConfiguracoesNotificacoes
            data={configNotificacao as any}
            isLoading={isLoadingNotificacao}
            onSave={async (data) => {
              await saveNotificacaoMutation.mutateAsync(data as any)
            }}
            onTestarEmail={handleTestarEmail}
          />
        </TabsContent>

        <TabsContent value="seguranca">
          <ConfiguracoesSeguranca
            data={configSeguranca as any}
            isLoading={isLoadingSeguranca}
            onSave={async (data) => {
              await saveSegurancaMutation.mutateAsync(data as any)
            }}
          />
        </TabsContent>

        <TabsContent value="logs">
          <ConfiguracoesLogs isLoading={false} />
        </TabsContent>
      </Tabs>
    </div>
  )
}
