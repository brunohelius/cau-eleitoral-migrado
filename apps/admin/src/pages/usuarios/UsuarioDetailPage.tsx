import { useParams, Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  ArrowLeft,
  User,
  Mail,
  Phone,
  Shield,
  Edit,
  Lock,
  Loader2,
  CheckCircle,
  XCircle,
  Activity,
} from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { useToast } from '@/hooks/use-toast'

interface Usuario {
  id: string
  nome: string
  email: string
  cpf: string
  telefone?: string
  cargo?: string
  departamento?: string
  perfil: string
  ativo: boolean
  ultimoAcesso?: string
  dataCadastro: string
  dataAtualizacao?: string
}

interface AtividadeRecente {
  id: string
  data: string
  acao: string
  ip?: string
}

export function UsuarioDetailPage() {
  const { id } = useParams<{ id: string }>()
  const { toast } = useToast()

  // Mock dados - em producao viria da API
  const { data: usuario, isLoading } = useQuery({
    queryKey: ['usuario', id],
    queryFn: async () => {
      return {
        id: '1',
        nome: 'Carlos Alberto Silva',
        email: 'carlos.silva@cau.org.br',
        cpf: '123.456.789-00',
        telefone: '(11) 98765-4321',
        cargo: 'Coordenador de TI',
        departamento: 'Tecnologia da Informacao',
        perfil: 'admin',
        ativo: true,
        ultimoAcesso: '2024-02-20T15:30:00',
        dataCadastro: '2023-01-15T10:00:00',
        dataAtualizacao: '2024-02-10T14:00:00',
      } as Usuario
    },
    enabled: !!id,
  })

  // Mock atividades recentes
  const atividades: AtividadeRecente[] = [
    { id: '1', data: '2024-02-20T15:30:00', acao: 'Login realizado', ip: '192.168.1.100' },
    { id: '2', data: '2024-02-20T14:00:00', acao: 'Eleicao editada - CAU/SP 2024' },
    { id: '3', data: '2024-02-19T16:45:00', acao: 'Usuario criado - Maria Santos' },
    { id: '4', data: '2024-02-19T10:30:00', acao: 'Login realizado', ip: '192.168.1.100' },
    { id: '5', data: '2024-02-18T09:00:00', acao: 'Relatorio exportado - Votacao' },
  ]

  const getPerfilInfo = (perfil: string) => {
    const perfis: Record<string, { label: string; color: string; descricao: string }> = {
      admin: {
        label: 'Administrador',
        color: 'bg-purple-100 text-purple-800',
        descricao: 'Acesso total ao sistema',
      },
      comissao: {
        label: 'Comissao Eleitoral',
        color: 'bg-blue-100 text-blue-800',
        descricao: 'Gerencia eleicoes e julgamentos',
      },
      fiscal: {
        label: 'Fiscal',
        color: 'bg-green-100 text-green-800',
        descricao: 'Acompanha o processo eleitoral',
      },
      operador: {
        label: 'Operador',
        color: 'bg-yellow-100 text-yellow-800',
        descricao: 'Operacoes basicas do sistema',
      },
      auditor: {
        label: 'Auditor',
        color: 'bg-orange-100 text-orange-800',
        descricao: 'Acesso aos logs e relatorios',
      },
    }
    return perfis[perfil] || { label: perfil, color: 'bg-gray-100 text-gray-800', descricao: '' }
  }

  const handleToggleStatus = () => {
    toast({
      title: usuario?.ativo ? 'Usuario desativado' : 'Usuario ativado',
      description: `O usuario foi ${usuario?.ativo ? 'desativado' : 'ativado'} com sucesso.`,
    })
  }

  const handleResetPassword = () => {
    toast({
      title: 'Email enviado',
      description: 'Um email de redefinicao de senha foi enviado ao usuario.',
    })
  }

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <Loader2 className="h-8 w-8 animate-spin text-gray-400" />
      </div>
    )
  }

  if (!usuario) {
    return (
      <div className="flex items-center justify-center h-64">
        <p className="text-gray-500">Usuario nao encontrado.</p>
      </div>
    )
  }

  const perfilInfo = getPerfilInfo(usuario.perfil)

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Link to="/usuarios">
            <Button variant="ghost" size="icon">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <div className="flex items-center gap-3">
              <h1 className="text-2xl font-bold text-gray-900">{usuario.nome}</h1>
              {usuario.ativo ? (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  <CheckCircle className="h-3 w-3" />
                  Ativo
                </span>
              ) : (
                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                  <XCircle className="h-3 w-3" />
                  Inativo
                </span>
              )}
            </div>
            <p className="text-gray-600">{usuario.cargo || 'Usuario do sistema'}</p>
          </div>
        </div>
        <div className="flex gap-2">
          <Button variant="outline" onClick={handleResetPassword}>
            <Lock className="mr-2 h-4 w-4" />
            Redefinir Senha
          </Button>
          <Button
            variant={usuario.ativo ? 'destructive' : 'default'}
            onClick={handleToggleStatus}
          >
            {usuario.ativo ? (
              <>
                <XCircle className="mr-2 h-4 w-4" />
                Desativar
              </>
            ) : (
              <>
                <CheckCircle className="mr-2 h-4 w-4" />
                Ativar
              </>
            )}
          </Button>
          <Link to={`/usuarios/${id}/editar`}>
            <Button>
              <Edit className="mr-2 h-4 w-4" />
              Editar
            </Button>
          </Link>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Informacoes do Usuario */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="h-5 w-5" />
              Informacoes do Usuario
            </CardTitle>
          </CardHeader>
          <CardContent>
            <dl className="grid gap-4 sm:grid-cols-2">
              <div className="flex items-start gap-3">
                <Mail className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Email</dt>
                  <dd className="mt-1 text-sm text-gray-900">{usuario.email}</dd>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <Phone className="h-5 w-5 text-gray-400 mt-0.5" />
                <div>
                  <dt className="text-sm font-medium text-gray-500">Telefone</dt>
                  <dd className="mt-1 text-sm text-gray-900">{usuario.telefone || '-'}</dd>
                </div>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">CPF</dt>
                <dd className="mt-1 text-sm text-gray-900">{usuario.cpf}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Departamento</dt>
                <dd className="mt-1 text-sm text-gray-900">{usuario.departamento || '-'}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {new Date(usuario.dataCadastro).toLocaleDateString('pt-BR')}
                </dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-gray-500">Ultimo Acesso</dt>
                <dd className="mt-1 text-sm text-gray-900">
                  {usuario.ultimoAcesso
                    ? new Date(usuario.ultimoAcesso).toLocaleString('pt-BR')
                    : 'Nunca acessou'}
                </dd>
              </div>
            </dl>
          </CardContent>
        </Card>

        {/* Perfil de Acesso */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Shield className="h-5 w-5" />
              Perfil de Acesso
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="rounded-lg bg-gray-50 p-4">
              <div className="flex items-center gap-3">
                <span className={`inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium ${perfilInfo.color}`}>
                  {perfilInfo.label}
                </span>
              </div>
              <p className="mt-3 text-sm text-gray-600">{perfilInfo.descricao}</p>
            </div>

            <div className="mt-4 space-y-2">
              <h4 className="text-sm font-medium text-gray-700">Permissoes</h4>
              <div className="space-y-1">
                {usuario.perfil === 'admin' && (
                  <>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Gerenciar usuarios
                    </p>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Gerenciar eleicoes
                    </p>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Acessar configuracoes
                    </p>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Visualizar relatorios
                    </p>
                  </>
                )}
                {usuario.perfil === 'comissao' && (
                  <>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Gerenciar eleicoes
                    </p>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Julgar denuncias
                    </p>
                    <p className="text-sm text-gray-600 flex items-center gap-2">
                      <CheckCircle className="h-4 w-4 text-green-500" />
                      Visualizar relatorios
                    </p>
                  </>
                )}
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Atividades Recentes */}
        <Card className="lg:col-span-3">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Activity className="h-5 w-5" />
              Atividades Recentes
            </CardTitle>
            <CardDescription>Ultimas acoes realizadas pelo usuario</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="relative">
              <div className="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200" />
              <div className="space-y-6">
                {atividades.map((atividade) => (
                  <div key={atividade.id} className="relative flex gap-4 pl-10">
                    <div className="absolute left-2 top-1 h-4 w-4 rounded-full bg-blue-500 ring-4 ring-white" />
                    <div className="flex-1">
                      <div className="flex items-center justify-between">
                        <span className="font-medium">{atividade.acao}</span>
                        <span className="text-xs text-gray-500">
                          {new Date(atividade.data).toLocaleString('pt-BR')}
                        </span>
                      </div>
                      {atividade.ip && (
                        <p className="text-sm text-gray-500">IP: {atividade.ip}</p>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
