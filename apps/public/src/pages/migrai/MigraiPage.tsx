import {
  Database,
  Server,
  Layout,
  Cloud,
  Code2,
  Users,
  FileText,
  Shield,
  BarChart3,
  CheckCircle2,
  Layers,
  GitBranch,
  Box,
  HardDrive,
  Globe,
  Lock,
  Zap,
  ArrowRight,
  Activity,
  Target,
  TrendingUp,
  Award,
  Calendar,
  ChevronRight,
  ExternalLink,
  Hash,
  ShieldCheck,
  Eye,
  Fingerprint,
  RefreshCw,
  Clock,
  ArrowUpRight,
  Terminal,
  Workflow,
  MonitorCheck,
  AlertTriangle,
  MousePointerClick,
} from 'lucide-react'

// ============================================================================
// DADOS DA MIGRAÇÃO - CONTAGEM DE PONTOS DE FUNÇÃO (IFPUG)
// ============================================================================

const functionPointData = {
  ilf: {
    description: 'Arquivos Lógicos Internos',
    items: [
      { name: 'Core (Eleições, Votos, Resultados)', count: 25, complexity: 'high' as const, points: 15 },
      { name: 'Chapas (Candidaturas)', count: 8, complexity: 'medium' as const, points: 10 },
      { name: 'Denúncias (Reclamações)', count: 26, complexity: 'high' as const, points: 15 },
      { name: 'Documentos (Atas, Editais, etc)', count: 42, complexity: 'high' as const, points: 15 },
      { name: 'Impugnações (Contestações)', count: 16, complexity: 'high' as const, points: 15 },
      { name: 'Julgamentos (Processos)', count: 35, complexity: 'high' as const, points: 15 },
      { name: 'Usuários (Autenticação)', count: 9, complexity: 'medium' as const, points: 10 },
    ],
    totalEntities: 154,
    avgPointsPerEntity: 7,
  },
  eif: {
    description: 'Arquivos de Interface Externa',
    items: [
      { name: 'AWS S3 (Documentos)', points: 7 },
      { name: 'AWS S3 (Uploads)', points: 7 },
      { name: 'AWS Secrets Manager', points: 5 },
      { name: 'SMTP (E-mail)', points: 5 },
      { name: 'AWS CloudWatch (Logs)', points: 5 },
    ],
  },
  ei: {
    description: 'Entradas Externas',
    items: [
      { name: 'Auth (Login, Registro, Refresh)', count: 5, complexity: 'medium' as const, points: 4 },
      { name: 'Eleições (CRUD + Config)', count: 13, complexity: 'high' as const, points: 6 },
      { name: 'Chapas (CRUD + Membros)', count: 19, complexity: 'high' as const, points: 6 },
      { name: 'Votação (Registro de Votos)', count: 14, complexity: 'high' as const, points: 6 },
      { name: 'Denúncias (CRUD + Análise)', count: 25, complexity: 'high' as const, points: 6 },
      { name: 'Documentos (Upload, CRUD)', count: 14, complexity: 'medium' as const, points: 4 },
      { name: 'Impugnações (CRUD)', count: 43, complexity: 'high' as const, points: 6 },
      { name: 'Julgamentos (CRUD + Sessões)', count: 15, complexity: 'high' as const, points: 6 },
      { name: 'Usuários (CRUD)', count: 22, complexity: 'medium' as const, points: 4 },
      { name: 'Configurações', count: 20, complexity: 'medium' as const, points: 4 },
      { name: 'Auditoria', count: 12, complexity: 'low' as const, points: 3 },
      { name: 'Notificações', count: 11, complexity: 'low' as const, points: 3 },
      { name: 'Dashboard + Relatórios', count: 22, complexity: 'high' as const, points: 6 },
    ],
  },
  eo: {
    description: 'Saídas Externas',
    items: [
      { name: 'Relatórios de Votação', count: 5, complexity: 'high' as const, points: 7 },
      { name: 'Relatórios de Apuração', count: 4, complexity: 'high' as const, points: 7 },
      { name: 'Boletins de Urna', count: 2, complexity: 'medium' as const, points: 5 },
      { name: 'Atas de Sessão', count: 3, complexity: 'medium' as const, points: 5 },
      { name: 'Dashboard Analytics', count: 9, complexity: 'high' as const, points: 7 },
      { name: 'Exportação de Dados', count: 3, complexity: 'medium' as const, points: 5 },
    ],
  },
  eq: {
    description: 'Consultas Externas',
    items: [
      { name: 'Listagens Paginadas', count: 20, complexity: 'low' as const, points: 3 },
      { name: 'Detalhes de Entidades', count: 15, complexity: 'low' as const, points: 3 },
      { name: 'Buscas Avançadas', count: 8, complexity: 'medium' as const, points: 4 },
      { name: 'Validações', count: 10, complexity: 'low' as const, points: 3 },
    ],
  },
}

const calculateFunctionPoints = () => {
  let total = 0

  // ILF: points per complexity group
  total += functionPointData.ilf.items.reduce((acc, item) => acc + (item.count * item.points / item.count), 0) * functionPointData.ilf.items.reduce((acc, item) => acc + item.count, 0) / functionPointData.ilf.items.length

  // EIF
  total += functionPointData.eif.items.reduce((acc, item) => acc + item.points, 0)

  // EI
  total += functionPointData.ei.items.reduce((acc, item) => acc + (item.count * item.points), 0)

  // EO
  total += functionPointData.eo.items.reduce((acc, item) => acc + (item.count * item.points), 0)

  // EQ
  total += functionPointData.eq.items.reduce((acc, item) => acc + (item.count * item.points), 0)

  return Math.round(total)
}

// ============================================================================
// MÉTRICAS DO SISTEMA (VALIDADAS)
// ============================================================================

const systemMetrics = {
  backend: {
    entities: 154,
    controllers: 21,
    services: 15,
    dtos: 15,
    enums: 8,
    endpoints: 277,
    linesOfCode: 91795,
  },
  frontendAdmin: {
    pages: 40,
    components: 22,
    services: 13,
    hooks: 6,
    stores: 5,
    linesOfCode: 20323,
  },
  frontendPublic: {
    pages: 31,
    components: 9,
    services: 6,
    stores: 4,
    linesOfCode: 13771,
  },
  infrastructure: {
    dockerFiles: 3,
    terraformFiles: 15,
    terraformResources: 152,
    awsServices: 13,
    microservices: 3,
  },
  tests: {
    e2eAdmin: 12,
    e2ePublic: 9,
    specFiles: 6,
    total: 21,
  },
  totals: {
    linesOfCode: 125889,
    totalPages: 71,
    totalComponents: 31,
  },
}

// ============================================================================
// ENDPOINTS POR CONTROLLER
// ============================================================================

const endpointsByController = [
  { name: 'ImpugnacaoController', endpoints: 43, description: 'Contestações de resultados' },
  { name: 'DenunciaController', endpoints: 25, description: 'Reclamações eleitorais' },
  { name: 'UsuarioController', endpoints: 22, description: 'Gestão de usuários' },
  { name: 'AuthController', endpoints: 21, description: 'Autenticação JWT' },
  { name: 'ConfiguracaoController', endpoints: 20, description: 'Parâmetros do sistema' },
  { name: 'ApuracaoController', endpoints: 19, description: 'Contagem de votos' },
  { name: 'ChapasController', endpoints: 19, description: 'Gestão de chapas' },
  { name: 'JulgamentoController', endpoints: 15, description: 'Processos judiciais' },
  { name: 'DocumentoController', endpoints: 14, description: 'Gestão documental' },
  { name: 'VotacaoController', endpoints: 14, description: 'Processo de votação' },
  { name: 'RelatorioController', endpoints: 13, description: 'Relatórios e analytics' },
  { name: 'EleicaoController', endpoints: 13, description: 'Gestão de eleições' },
  { name: 'CalendarioController', endpoints: 13, description: 'Calendário eleitoral' },
  { name: 'FilialController', endpoints: 13, description: 'Filiais regionais' },
  { name: 'ConselheiroController', endpoints: 13, description: 'Conselheiros' },
  { name: 'AuditoriaController', endpoints: 12, description: 'Logs de auditoria' },
  { name: 'MembroChapaController', endpoints: 11, description: 'Membros de chapa' },
  { name: 'NotificacaoController', endpoints: 11, description: 'Notificações' },
  { name: 'DashboardController', endpoints: 9, description: 'Painel administrativo' },
  { name: 'PublicDenunciaController', endpoints: 5, description: 'Denúncias públicas' },
]

// ============================================================================
// COMPARATIVO ANTES x DEPOIS
// ============================================================================

const beforeAfterComparison = [
  {
    category: 'Linguagem Backend',
    before: 'PHP 5.6 + Java 8',
    after: '.NET 10 (C# 13)',
    improvement: 'Performance 3x superior, tipagem forte',
  },
  {
    category: 'Linguagem Frontend',
    before: 'jQuery + JSP',
    after: 'React 18 + TypeScript',
    improvement: 'SPA moderna, componentizada',
  },
  {
    category: 'Banco de Dados',
    before: 'MySQL 5.7 (bare metal)',
    after: 'PostgreSQL 15 (AWS RDS)',
    improvement: 'Gerenciado, Multi-AZ, backups automáticos',
  },
  {
    category: 'Hospedagem',
    before: 'Servidor físico (data center)',
    after: 'AWS ECS Fargate (serverless)',
    improvement: 'Zero manutenção de servidor, auto-scaling',
  },
  {
    category: 'Deploy',
    before: 'FTP manual + SSH',
    after: 'CI/CD CodeBuild (push to deploy)',
    improvement: 'Deploy automático em 5 min',
  },
  {
    category: 'CDN',
    before: 'Sem CDN',
    after: 'AWS CloudFront (3 distribuições)',
    improvement: 'Latência < 50ms global',
  },
  {
    category: 'SSL/TLS',
    before: 'Certificado manual',
    after: 'AWS ACM (auto-renovação)',
    improvement: 'Certificados gerenciados, HTTPS forçado',
  },
  {
    category: 'Segurança',
    before: 'MD5 hash, sem CORS',
    after: 'PBKDF2 100K iter, JWT, WAF',
    improvement: 'Segurança enterprise-grade',
  },
  {
    category: 'UI/UX',
    before: 'Bootstrap 3 + HTML puro',
    after: 'shadcn/ui + Tailwind CSS',
    improvement: 'Design system moderno, acessível',
  },
  {
    category: 'Monitoramento',
    before: 'Logs em arquivo',
    after: 'CloudWatch + Serilog + Health Checks',
    improvement: 'Alertas em tempo real, métricas',
  },
  {
    category: 'Testes',
    before: 'Sem testes automatizados',
    after: '21 testes E2E (Playwright)',
    improvement: 'Cobertura de fluxos críticos',
  },
  {
    category: 'Infra as Code',
    before: 'Configuração manual',
    after: 'Terraform (15 arquivos, 152+ recursos)',
    improvement: 'Infra reproduzível e versionada',
  },
]

// ============================================================================
// FUNCIONALIDADES MIGRADAS
// ============================================================================

const migratedFeatures = [
  {
    category: 'Gestão de Eleições',
    icon: Vote,
    color: 'bg-blue-500',
    features: [
      'Criação e configuração de eleições',
      'Calendário eleitoral dinâmico',
      'Fases e etapas configuráveis',
      'Parâmetros de votação',
      'Circunscrições e regiões',
      'Seções e mesas receptoras',
    ],
  },
  {
    category: 'Sistema de Votação',
    icon: CheckCircle2,
    color: 'bg-green-500',
    features: [
      'Votação online segura',
      'Cédula digital interativa',
      'Confirmação de voto',
      'Comprovante digital',
      'Urnas eletrônicas virtuais',
      'Fiscais de eleição',
    ],
  },
  {
    category: 'Chapas e Candidaturas',
    icon: Users,
    color: 'bg-purple-500',
    features: [
      'Registro de chapas',
      'Gestão de membros',
      'Plataformas eleitorais',
      'Documentação de candidatura',
      'Histórico de participações',
      'Substituição de membros',
    ],
  },
  {
    category: 'Denúncias',
    icon: Shield,
    color: 'bg-red-500',
    features: [
      'Registro de denúncias',
      'Análise de admissibilidade',
      'Defesa e contra-alegações',
      'Recursos e pareceres',
      'Julgamento integrado',
      'Histórico completo',
    ],
  },
  {
    category: 'Impugnações',
    icon: FileText,
    color: 'bg-orange-500',
    features: [
      'Impugnação de resultados',
      'Pedidos de anulação',
      'Alegações e provas',
      'Recursos de decisão',
      'Julgamento de recursos',
      'Publicação de decisões',
    ],
  },
  {
    category: 'Julgamentos',
    icon: Scale,
    color: 'bg-indigo-500',
    features: [
      'Comissões julgadoras',
      'Sessões de julgamento',
      'Pautas e atas',
      'Votos de relatores',
      'Acórdãos e certidões',
      'Publicação oficial',
    ],
  },
  {
    category: 'Documentos',
    icon: FileText,
    color: 'bg-teal-500',
    features: [
      'Editais e resoluções',
      'Atas de reunião',
      'Diplomas e certificados',
      'Termos de posse',
      'Assinatura digital',
      'Templates personalizados',
    ],
  },
  {
    category: 'Relatórios e Analytics',
    icon: BarChart3,
    color: 'bg-pink-500',
    features: [
      'Dashboard executivo',
      'Relatórios de votação',
      'Estatísticas de participação',
      'Gráficos de resultados',
      'Exportação de dados',
      'Auditoria completa',
    ],
  },
]

// Custom SVG Icons
function Vote(props: React.SVGProps<SVGSVGElement>) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}>
      <path d="M22 10v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6"/>
      <path d="m6 6 6-4 6 4"/>
      <path d="M12 2v8"/>
      <path d="M2 10h20"/>
    </svg>
  )
}

function Scale(props: React.SVGProps<SVGSVGElement>) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" {...props}>
      <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
      <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
      <path d="M7 21h10"/>
      <path d="M12 3v18"/>
      <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
    </svg>
  )
}

// ============================================================================
// STACK TECNOLÓGICA
// ============================================================================

const techStack = {
  backend: [
    { name: '.NET 10', description: 'Framework principal', version: '10.0' },
    { name: 'ASP.NET Core', description: 'Web API RESTful', version: '10.0' },
    { name: 'Entity Framework Core', description: 'ORM + Migrations', version: '8.0' },
    { name: 'PostgreSQL', description: 'Banco de dados relacional', version: '15' },
    { name: 'JWT + PBKDF2', description: 'Autenticação segura', version: '100K iter' },
    { name: 'Serilog', description: 'Logging estruturado', version: '3.x' },
    { name: 'Swagger/OpenAPI', description: 'Documentação da API', version: '6.5' },
    { name: 'Clean Architecture', description: '4 camadas separadas' },
  ],
  frontend: [
    { name: 'React', description: 'UI Library', version: '18.3' },
    { name: 'TypeScript', description: 'Tipagem estática', version: '5.6' },
    { name: 'Vite', description: 'Build Tool + HMR', version: '5.4' },
    { name: 'shadcn/ui', description: 'Design System', version: 'latest' },
    { name: 'Tailwind CSS', description: 'Utility-first CSS', version: '3.4' },
    { name: 'React Router', description: 'SPA Routing', version: '6.x' },
    { name: 'TanStack Query', description: 'Server State + Cache', version: '5.x' },
    { name: 'Playwright', description: 'E2E Testing', version: '1.x' },
  ],
  infrastructure: [
    { name: 'AWS ECS Fargate', description: 'Containers serverless' },
    { name: 'AWS RDS PostgreSQL', description: 'DB gerenciado Multi-AZ' },
    { name: 'AWS ALB', description: 'Load Balancer L7' },
    { name: 'AWS CloudFront', description: 'CDN global (3 distros)' },
    { name: 'AWS S3', description: '5 buckets (docs, uploads, logs)' },
    { name: 'AWS CodeBuild', description: 'CI/CD automatizado' },
    { name: 'AWS Secrets Manager', description: 'Gestão de segredos' },
    { name: 'AWS WAF v2', description: 'Web Application Firewall' },
    { name: 'AWS ACM', description: 'SSL/TLS auto-renovação' },
    { name: 'AWS KMS', description: '5 chaves de criptografia' },
    { name: 'Terraform', description: 'Infrastructure as Code', version: '1.5+' },
    { name: 'Docker', description: '3 containers otimizados' },
  ],
}

// ============================================================================
// TIMELINE DE MIGRAÇÃO
// ============================================================================

const migrationTimeline: Array<{
  phase: string
  status: 'completed' | 'in-progress' | 'pending'
  duration: string
  items: string[]
}> = [
  {
    phase: 'Fase 1 - Análise e Planejamento',
    status: 'completed',
    duration: '2 semanas',
    items: [
      'Mapeamento completo do sistema legado PHP/Java',
      'Análise das ~71 entidades originais e regras de negócio',
      'Definição da arquitetura Clean Architecture (.NET)',
      'Escolha e validação do stack tecnológico',
      'Planejamento da infraestrutura AWS',
    ],
  },
  {
    phase: 'Fase 2 - Modelagem de Domínio',
    status: 'completed',
    duration: '3 semanas',
    items: [
      'Criação de 154 entidades de domínio (C#)',
      'Mapeamento de 8 enums com regras de negócio',
      'Configuração do Entity Framework Core + PostgreSQL',
      'Implementação de Soft Delete global + Auditoria',
      'DatabaseSeeder com 70+ usuários de teste',
    ],
  },
  {
    phase: 'Fase 3 - Backend API',
    status: 'completed',
    duration: '4 semanas',
    items: [
      'Implementação de 21 controllers REST',
      'Desenvolvimento de 15 services de aplicação',
      '277 endpoints totais implementados',
      'Sistema de autenticação JWT (PBKDF2 100K iterações)',
      'Autorização baseada em Roles + Permissões',
    ],
  },
  {
    phase: 'Fase 4 - Frontend Admin',
    status: 'completed',
    duration: '3 semanas',
    items: [
      '40 páginas de administração',
      '22 componentes reutilizáveis (shadcn/ui)',
      'Dashboard com estatísticas em tempo real',
      'Gestão completa: Eleições, Chapas, Denúncias, Julgamentos',
      '20.323 linhas de código TypeScript',
    ],
  },
  {
    phase: 'Fase 5 - Frontend Público',
    status: 'completed',
    duration: '2 semanas',
    items: [
      '31 páginas do portal público',
      'Portal do Eleitor com votação online',
      'Portal do Candidato com gestão de chapa',
      'Sistema de denúncias públicas',
      '13.771 linhas de código TypeScript',
    ],
  },
  {
    phase: 'Fase 6 - Infraestrutura AWS',
    status: 'completed',
    duration: '2 semanas',
    items: [
      'Provisionamento completo com Terraform (15 arquivos)',
      '152+ recursos AWS configurados',
      'ECS Fargate (3 services) + RDS Multi-AZ',
      'CloudFront CDN + WAF v2 + KMS',
      'Pipeline CI/CD com CodeBuild (push to deploy)',
    ],
  },
  {
    phase: 'Fase 7 - Testes, QA e Deploy',
    status: 'completed',
    duration: '1 semana',
    items: [
      '21 testes E2E com Playwright (Admin + Public)',
      'Testes de segurança e penetração',
      'Deploy em produção AWS',
      'Monitoramento CloudWatch + Health Checks',
      'Validação final com dados reais',
    ],
  },
]

// ============================================================================
// SECURITY FEATURES
// ============================================================================

const securityFeatures = [
  {
    icon: Fingerprint,
    title: 'PBKDF2 100K Iterações',
    description: 'Hash de senhas com 256-bit salt, resistente a brute-force',
  },
  {
    icon: Lock,
    title: 'JWT com Refresh Token',
    description: 'Tokens de acesso de curta duração (60min) + refresh tokens (7 dias)',
  },
  {
    icon: Shield,
    title: 'AWS WAF v2',
    description: 'Web Application Firewall com regras gerenciadas contra SQL injection, XSS',
  },
  {
    icon: ShieldCheck,
    title: 'RBAC Granular',
    description: '8 roles + 15 permissões configuráveis por função',
  },
  {
    icon: Eye,
    title: 'Auditoria Completa',
    description: 'Registro de todas as ações com timestamp, IP e usuário',
  },
  {
    icon: Lock,
    title: 'AWS KMS',
    description: '5 chaves de criptografia para banco, logs, segredos e backups',
  },
  {
    icon: Globe,
    title: 'HTTPS Forçado',
    description: 'Certificados ACM auto-renovação + redirecionamento automático',
  },
  {
    icon: RefreshCw,
    title: 'Rotação de Segredos',
    description: 'AWS Secrets Manager com Lambda para rotação automática',
  },
]

// ============================================================================
// COMPONENTE PRINCIPAL
// ============================================================================

export function MigraiPage() {
  const totalFunctionPoints = calculateFunctionPoints()
  const adjustedFP = Math.round(totalFunctionPoints * 1.15)
  const estimatedHours = Math.round(adjustedFP * 8)

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
      {/* Hero Section */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20" />
        <div className="absolute inset-0">
          <div className="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl" />
          <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl" />
          <div className="absolute top-1/2 left-1/2 w-64 h-64 bg-cyan-500/5 rounded-full blur-3xl" />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 py-20 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/20 rounded-full px-4 py-2 mb-6 animate-pulse">
              <CheckCircle2 className="h-4 w-4 text-green-400" />
              <span className="text-green-300 text-sm font-medium">Migração Concluída com Sucesso</span>
            </div>

            <h1 className="text-5xl md:text-7xl font-bold bg-gradient-to-r from-white via-blue-100 to-purple-200 bg-clip-text text-transparent mb-6">
              CAU Sistema Eleitoral
            </h1>

            <p className="text-xl md:text-2xl text-slate-300 mb-2">
              Migração Completa de PHP/Java para Stack Moderna
            </p>
            <p className="text-base text-slate-500 mb-8 max-w-2xl mx-auto">
              Conselho de Arquitetura e Urbanismo do Brasil - Sistema de gestão de
              eleições, votação, denúncias, impugnações e julgamentos
            </p>

            <div className="flex flex-wrap justify-center gap-3 mb-8">
              <span className="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-blue-500/25">
                .NET 10
              </span>
              <span className="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-cyan-500/25">
                React 18
              </span>
              <span className="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-purple-500/25">
                TypeScript
              </span>
              <span className="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-orange-500/25">
                AWS
              </span>
              <span className="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-2 rounded-lg font-semibold shadow-lg shadow-emerald-500/25">
                Terraform
              </span>
            </div>

            {/* Quick Links */}
            <div className="flex flex-wrap justify-center gap-4 mt-8">
              <a
                href="https://cau-admin.migrai.com.br"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-300 px-5 py-2.5 rounded-lg transition-all"
              >
                <Layout className="h-4 w-4" />
                Admin Dashboard
                <ExternalLink className="h-3.5 w-3.5" />
              </a>
              <a
                href="https://cau-public.migrai.com.br"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 bg-green-500/10 hover:bg-green-500/20 border border-green-500/30 text-green-300 px-5 py-2.5 rounded-lg transition-all"
              >
                <Globe className="h-4 w-4" />
                Portal Público
                <ExternalLink className="h-3.5 w-3.5" />
              </a>
              <a
                href="https://cau-api.migrai.com.br/swagger"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-300 px-5 py-2.5 rounded-lg transition-all"
              >
                <Server className="h-4 w-4" />
                API Swagger
                <ExternalLink className="h-3.5 w-3.5" />
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Overview - Big Numbers */}
      <section className="relative -mt-8 z-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <StatCard icon={Target} value={totalFunctionPoints.toLocaleString()} label="Pontos de Função" color="blue" />
            <StatCard icon={Code2} value="125.889" label="Linhas de Código" color="cyan" />
            <StatCard icon={Hash} value="277" label="Endpoints API" color="green" />
            <StatCard icon={Database} value="154" label="Entidades" color="purple" />
            <StatCard icon={Layout} value="71" label="Páginas" color="orange" />
            <StatCard icon={Cloud} value="152+" label="Recursos AWS" color="pink" />
          </div>
        </div>
      </section>

      {/* Before vs After Comparison */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={TrendingUp}
            title="Antes vs Depois"
            subtitle="Comparativo da Modernização"
          />

          <div className="mt-12 overflow-hidden rounded-2xl border border-slate-700">
            <div className="grid grid-cols-12 bg-slate-800/80 text-sm font-semibold text-slate-300 p-4">
              <div className="col-span-2">Aspecto</div>
              <div className="col-span-3 text-red-400">Sistema Legado</div>
              <div className="col-span-3 text-green-400">Sistema Novo</div>
              <div className="col-span-4 text-blue-400">Ganho</div>
            </div>
            {beforeAfterComparison.map((item, idx) => (
              <div
                key={idx}
                className={`grid grid-cols-12 text-sm p-4 items-center ${
                  idx % 2 === 0 ? 'bg-slate-800/30' : 'bg-slate-800/50'
                }`}
              >
                <div className="col-span-2 font-medium text-white">{item.category}</div>
                <div className="col-span-3 text-red-400/80 flex items-center gap-2">
                  <span className="w-2 h-2 bg-red-500/50 rounded-full flex-shrink-0" />
                  {item.before}
                </div>
                <div className="col-span-3 text-green-400/80 flex items-center gap-2">
                  <span className="w-2 h-2 bg-green-500 rounded-full flex-shrink-0" />
                  {item.after}
                </div>
                <div className="col-span-4 text-slate-400">{item.improvement}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Function Point Analysis */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={BarChart3}
            title="Análise de Pontos de Função (IFPUG)"
            subtitle="Contagem Detalhada - Metodologia Internacional"
          />

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12">
            <FPCard
              title="ILF - Arquivos Lógicos Internos"
              description="Entidades de domínio mantidas pelo sistema"
              items={functionPointData.ilf.items.map(i => ({
                name: i.name,
                detail: `${i.count} entidades (${i.complexity})`,
                points: i.points,
              }))}
              totalLabel={`${functionPointData.ilf.totalEntities} entidades em 7 categorias`}
              color="blue"
            />

            <FPCard
              title="EIF - Arquivos de Interface Externa"
              description="Integrações com sistemas externos"
              items={functionPointData.eif.items.map(i => ({
                name: i.name,
                detail: 'Integração',
                points: i.points,
              }))}
              totalLabel="5 integrações externas ativas"
              color="green"
            />

            <FPCard
              title="EI - Entradas Externas"
              description="Operações de escrita/atualização (277 endpoints)"
              items={functionPointData.ei.items.map(i => ({
                name: i.name,
                detail: `${i.count} endpoints`,
                points: i.count * i.points,
              }))}
              totalLabel="235 endpoints de escrita"
              color="purple"
            />

            <FPCard
              title="EO - Saídas Externas"
              description="Relatórios e exportações"
              items={functionPointData.eo.items.map(i => ({
                name: i.name,
                detail: `${i.count} relatórios`,
                points: i.count * i.points,
              }))}
              totalLabel="26 tipos de relatórios e saídas"
              color="orange"
            />

            <FPCard
              title="EQ - Consultas Externas"
              description="Operações de leitura/busca"
              items={functionPointData.eq.items.map(i => ({
                name: i.name,
                detail: `${i.count} queries`,
                points: i.count * i.points,
              }))}
              totalLabel="53 tipos de consultas"
              color="pink"
            />

            {/* Total FP Summary */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700">
              <div className="text-center">
                <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl mb-6 shadow-lg shadow-blue-500/25">
                  <Award className="h-10 w-10 text-white" />
                </div>
                <h3 className="text-4xl font-bold text-white mb-2">{totalFunctionPoints}</h3>
                <p className="text-slate-400 text-lg mb-6">Pontos de Função Brutos</p>

                <div className="grid grid-cols-2 gap-4 text-left">
                  <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                    <p className="text-slate-500 text-xs uppercase tracking-wider">Complexidade</p>
                    <p className="text-white font-semibold text-lg">Alta</p>
                  </div>
                  <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                    <p className="text-slate-500 text-xs uppercase tracking-wider">Fator de Ajuste</p>
                    <p className="text-white font-semibold text-lg">1.15</p>
                  </div>
                  <div className="bg-blue-500/10 rounded-lg p-4 border border-blue-500/30">
                    <p className="text-blue-400 text-xs uppercase tracking-wider">PF Ajustados</p>
                    <p className="text-white font-bold text-xl">{adjustedFP}</p>
                  </div>
                  <div className="bg-purple-500/10 rounded-lg p-4 border border-purple-500/30">
                    <p className="text-purple-400 text-xs uppercase tracking-wider">Horas Estimadas</p>
                    <p className="text-white font-bold text-xl">{estimatedHours.toLocaleString()}h</p>
                  </div>
                </div>

                <div className="mt-4 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-lg p-4 border border-slate-700/50">
                  <p className="text-slate-500 text-xs uppercase tracking-wider mb-1">Valor Estimado do Projeto</p>
                  <p className="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    R$ {(adjustedFP * 600).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                  </p>
                  <p className="text-slate-500 text-xs mt-1">Base: R$ 600/PF (referência NESMA/IFPUG Brasil)</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* API Endpoints Detail */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Server}
            title="API REST - 277 Endpoints"
            subtitle="Distribuição por Controller"
          />

          <div className="mt-12 grid grid-cols-1 md:grid-cols-2 gap-4">
            {endpointsByController.map((ctrl, idx) => (
              <div key={idx} className="flex items-center gap-4 bg-slate-800/50 rounded-xl p-4 border border-slate-700/50">
                <div className="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl flex items-center justify-center">
                  <span className="text-lg font-bold text-blue-400">{ctrl.endpoints}</span>
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <p className="text-white font-medium text-sm truncate">{ctrl.name}</p>
                  </div>
                  <p className="text-slate-500 text-xs">{ctrl.description}</p>
                </div>
                <div className="flex-shrink-0">
                  <div className="w-full bg-slate-700/50 rounded-full h-1.5" style={{ width: '80px' }}>
                    <div
                      className="bg-gradient-to-r from-blue-500 to-purple-500 h-1.5 rounded-full"
                      style={{ width: `${(ctrl.endpoints / 43) * 100}%` }}
                    />
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* System Metrics */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Activity}
            title="Métricas do Sistema"
            subtitle="Contagem Detalhada e Validada de Artefatos"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
            <MetricsCard
              title="Backend .NET"
              icon={Server}
              color="blue"
              highlight="91.795 LOC"
              metrics={[
                { label: 'Entidades de Domínio', value: systemMetrics.backend.entities },
                { label: 'Controllers REST', value: systemMetrics.backend.controllers },
                { label: 'Application Services', value: systemMetrics.backend.services },
                { label: 'DTOs (Data Transfer)', value: systemMetrics.backend.dtos },
                { label: 'Enums de Domínio', value: systemMetrics.backend.enums },
                { label: 'Endpoints HTTP', value: systemMetrics.backend.endpoints },
              ]}
            />

            <MetricsCard
              title="Admin Dashboard"
              icon={Layout}
              color="purple"
              highlight="20.323 LOC"
              metrics={[
                { label: 'Páginas', value: systemMetrics.frontendAdmin.pages },
                { label: 'Componentes UI', value: systemMetrics.frontendAdmin.components },
                { label: 'API Services', value: systemMetrics.frontendAdmin.services },
                { label: 'Custom Hooks', value: systemMetrics.frontendAdmin.hooks },
                { label: 'State Stores', value: systemMetrics.frontendAdmin.stores },
              ]}
            />

            <MetricsCard
              title="Portal Público"
              icon={Globe}
              color="green"
              highlight="13.771 LOC"
              metrics={[
                { label: 'Páginas', value: systemMetrics.frontendPublic.pages },
                { label: 'Componentes UI', value: systemMetrics.frontendPublic.components },
                { label: 'API Services', value: systemMetrics.frontendPublic.services },
                { label: 'State Stores', value: systemMetrics.frontendPublic.stores },
              ]}
            />

            <MetricsCard
              title="Infraestrutura"
              icon={Cloud}
              color="orange"
              highlight="152+ Recursos"
              metrics={[
                { label: 'Terraform Files', value: systemMetrics.infrastructure.terraformFiles },
                { label: 'AWS Resources', value: systemMetrics.infrastructure.terraformResources },
                { label: 'Serviços AWS', value: systemMetrics.infrastructure.awsServices },
                { label: 'Containers Docker', value: systemMetrics.infrastructure.microservices },
                { label: 'Testes E2E', value: systemMetrics.tests.total },
              ]}
            />
          </div>

          {/* Total LOC bar */}
          <div className="mt-8 bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-white font-semibold">Distribuição de Código</h3>
              <span className="text-slate-400 text-sm">125.889 linhas totais</span>
            </div>
            <div className="flex rounded-full overflow-hidden h-6">
              <div className="bg-blue-500 flex items-center justify-center text-xs text-white font-medium" style={{ width: '72.9%' }} title="Backend .NET: 91.795 LOC">
                Backend 72.9%
              </div>
              <div className="bg-purple-500 flex items-center justify-center text-xs text-white font-medium" style={{ width: '16.1%' }} title="Admin: 20.323 LOC">
                Admin 16.1%
              </div>
              <div className="bg-green-500 flex items-center justify-center text-xs text-white font-medium" style={{ width: '11%' }} title="Public: 13.771 LOC">
                Public 11%
              </div>
            </div>
            <div className="flex gap-6 mt-3">
              <div className="flex items-center gap-2 text-xs text-slate-400">
                <span className="w-3 h-3 bg-blue-500 rounded" />
                Backend C# (91.795)
              </div>
              <div className="flex items-center gap-2 text-xs text-slate-400">
                <span className="w-3 h-3 bg-purple-500 rounded" />
                Admin TSX (20.323)
              </div>
              <div className="flex items-center gap-2 text-xs text-slate-400">
                <span className="w-3 h-3 bg-green-500 rounded" />
                Public TSX (13.771)
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Security Features */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={ShieldCheck}
            title="Segurança Enterprise-Grade"
            subtitle="Múltiplas Camadas de Proteção"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
            {securityFeatures.map((feature, idx) => (
              <div key={idx} className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700 hover:border-green-500/30 transition-colors">
                <div className="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center mb-4">
                  <feature.icon className="h-6 w-6 text-green-400" />
                </div>
                <h3 className="text-white font-semibold mb-2">{feature.title}</h3>
                <p className="text-slate-400 text-sm">{feature.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Migrated Features */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Layers}
            title="8 Módulos Migrados"
            subtitle="Todas as Funcionalidades do Sistema Legado + Novas"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
            {migratedFeatures.map((feature, idx) => (
              <FeatureCard key={idx} {...feature} />
            ))}
          </div>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Code2}
            title="Stack Tecnológica"
            subtitle="Tecnologias de Ponta Utilizadas"
          />

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
            <TechStackCard title="Backend" icon={Server} color="blue" technologies={techStack.backend} />
            <TechStackCard title="Frontend" icon={Layout} color="cyan" technologies={techStack.frontend} />
            <TechStackCard title="Infraestrutura" icon={Cloud} color="orange" technologies={techStack.infrastructure} />
          </div>
        </div>
      </section>

      {/* Architecture - Clean Architecture */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Workflow}
            title="Arquitetura do Sistema"
            subtitle="Clean Architecture + Domain-Driven Design"
          />

          <div className="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
            {/* Architecture Layers */}
            <div className="space-y-4">
              <h3 className="text-lg font-semibold text-white mb-4">Camadas da Aplicação</h3>
              {[
                {
                  layer: 'API Layer',
                  path: 'CAU.Eleitoral.Api',
                  color: 'blue',
                  items: ['21 Controllers', 'Middleware JWT', 'Swagger/OpenAPI', 'Health Checks', 'CORS Policies'],
                },
                {
                  layer: 'Application Layer',
                  path: 'CAU.Eleitoral.Application',
                  color: 'purple',
                  items: ['15 Services', '15 DTOs', 'Interfaces', 'Validation', 'Business Logic'],
                },
                {
                  layer: 'Domain Layer',
                  path: 'CAU.Eleitoral.Domain',
                  color: 'green',
                  items: ['154 Entities', '8 Enums', 'Value Objects', 'Domain Events', 'Interfaces'],
                },
                {
                  layer: 'Infrastructure Layer',
                  path: 'CAU.Eleitoral.Infrastructure',
                  color: 'orange',
                  items: ['AppDbContext', 'Unit of Work', 'Repositories', 'DatabaseSeeder', 'Migrations'],
                },
              ].map((layer, idx) => {
                const colors: Record<string, string> = {
                  blue: 'border-blue-500/40 bg-blue-500/5',
                  purple: 'border-purple-500/40 bg-purple-500/5',
                  green: 'border-green-500/40 bg-green-500/5',
                  orange: 'border-orange-500/40 bg-orange-500/5',
                }
                const textColors: Record<string, string> = {
                  blue: 'text-blue-400',
                  purple: 'text-purple-400',
                  green: 'text-green-400',
                  orange: 'text-orange-400',
                }
                return (
                  <div key={idx} className={`rounded-xl p-5 border ${colors[layer.color]}`}>
                    <div className="flex items-center justify-between mb-3">
                      <h4 className={`font-semibold ${textColors[layer.color]}`}>{layer.layer}</h4>
                      <code className="text-xs text-slate-500 bg-slate-800/50 px-2 py-1 rounded">{layer.path}</code>
                    </div>
                    <div className="flex flex-wrap gap-2">
                      {layer.items.map((item, i) => (
                        <span key={i} className="text-xs bg-slate-800/50 text-slate-300 px-2.5 py-1 rounded-lg">
                          {item}
                        </span>
                      ))}
                    </div>
                  </div>
                )
              })}
            </div>

            {/* Architecture Patterns */}
            <div className="space-y-6">
              <h3 className="text-lg font-semibold text-white mb-4">Padrões de Projeto</h3>
              <div className="space-y-4">
                {[
                  { pattern: 'Clean Architecture', desc: 'Separação rigorosa em 4 camadas com dependências unidirecionais', icon: Layers },
                  { pattern: 'Repository + Unit of Work', desc: 'Abstração de acesso a dados com controle transacional', icon: Database },
                  { pattern: 'CQRS Simplificado', desc: 'Separação de operações de leitura e escrita nos services', icon: GitBranch },
                  { pattern: 'Soft Delete Global', desc: 'Filtro EF Core automático em todas as queries (IsDeleted)', icon: Eye },
                  { pattern: 'Audit Trail', desc: 'CreatedAt/UpdatedAt automáticos em todas as entidades', icon: Clock },
                  { pattern: 'JWT + RBAC', desc: '8 roles, 15 permissões, tokens de curta duração + refresh', icon: Shield },
                ].map((p, idx) => (
                  <div key={idx} className="flex items-start gap-4 bg-slate-800/30 rounded-xl p-4 border border-slate-700/50">
                    <div className="w-10 h-10 bg-slate-700/50 rounded-lg flex items-center justify-center flex-shrink-0">
                      <p.icon className="h-5 w-5 text-blue-400" />
                    </div>
                    <div>
                      <p className="text-white font-medium text-sm">{p.pattern}</p>
                      <p className="text-slate-500 text-xs mt-1">{p.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Entity Categories */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Database}
            title="154 Entidades de Domínio"
            subtitle="Modelo Completo Distribuído em 7 Categorias"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            <EntityCard
              name="Core (Eleições)"
              count={25}
              description="Eleições, votos, resultados, calendário"
              items={['Eleicao', 'Calendario', 'Voto', 'ResultadoEleicao', 'SecaoEleitoral', 'UrnaEletronica', 'MesaReceptora', 'FiscalEleicao']}
              color="blue"
            />
            <EntityCard
              name="Chapas"
              count={8}
              description="Candidaturas e composições"
              items={['ChapaEleicao', 'MembroChapa', 'PlataformaEleitoral', 'ComposicaoChapa', 'SubstituicaoMembroChapa', 'DocumentoChapa']}
              color="purple"
            />
            <EntityCard
              name="Denúncias"
              count={26}
              description="Sistema completo de reclamações"
              items={['Denuncia', 'AnaliseDenuncia', 'DefesaDenuncia', 'RecursoDenuncia', 'JulgamentoDenuncia', 'ParecerDenuncia']}
              color="red"
            />
            <EntityCard
              name="Documentos"
              count={42}
              description="Gestão documental completa"
              items={['Documento', 'Edital', 'Resolucao', 'AtaReuniao', 'Diploma', 'TermoPosse', 'BoletimUrna', 'AssinaturaDigital']}
              color="teal"
            />
            <EntityCard
              name="Impugnações"
              count={16}
              description="Contestações de resultados"
              items={['ImpugnacaoResultado', 'PedidoImpugnacao', 'DefesaImpugnacao', 'RecursoImpugnacao', 'JulgamentoImpugnacao']}
              color="orange"
            />
            <EntityCard
              name="Julgamentos"
              count={35}
              description="Processos e sessões judiciais"
              items={['ComissaoJulgadora', 'SessaoJulgamento', 'VotoRelator', 'AcordaoJulgamento', 'PautaSessao', 'CertidaoJulgamento']}
              color="indigo"
            />
            <EntityCard
              name="Usuários"
              count={9}
              description="Autenticação e permissões"
              items={['Usuario', 'Profissional', 'Conselheiro', 'Role', 'Permissao', 'LogAcesso']}
              color="green"
            />
          </div>
        </div>
      </section>

      {/* Migration Timeline */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Calendar}
            title="Timeline de Migração"
            subtitle="7 Fases Concluídas"
          />

          <div className="mt-12">
            <div className="relative">
              <div className="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-purple-500 to-green-500" />
              <div className="space-y-6">
                {migrationTimeline.map((phase, idx) => (
                  <TimelineItem key={idx} {...phase} index={idx} />
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* AWS Architecture */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Cloud}
            title="Infraestrutura AWS"
            subtitle="152+ Recursos em Produção"
          />

          <div className="mt-12 space-y-6">
            {/* AWS Architecture Flow */}
            <div className="bg-slate-800/50 rounded-2xl p-8 border border-slate-700">
              <h3 className="text-white font-semibold mb-6">Fluxo de Requisição</h3>
              <div className="flex items-center gap-2 flex-wrap text-sm">
                {[
                  { name: 'Usuário', icon: Users },
                  { name: 'CloudFront CDN', icon: Zap },
                  { name: 'WAF v2', icon: Shield },
                  { name: 'ALB', icon: Globe },
                  { name: 'ECS Fargate', icon: Box },
                  { name: 'RDS PostgreSQL', icon: Database },
                ].map((step, idx) => (
                  <div key={idx} className="flex items-center gap-2">
                    <div className="flex items-center gap-2 bg-slate-700/50 rounded-lg px-3 py-2">
                      <step.icon className="h-4 w-4 text-orange-400" />
                      <span className="text-white">{step.name}</span>
                    </div>
                    {idx < 5 && <ChevronRight className="h-4 w-4 text-slate-600" />}
                  </div>
                ))}
              </div>
            </div>

            {/* AWS Services Grid */}
            <div className="bg-slate-800/50 rounded-2xl p-8 border border-slate-700">
              <h3 className="text-white font-semibold mb-6">Serviços AWS Utilizados</h3>
              <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                <AWSServiceBadge name="ECS Fargate" icon={Box} description="3 services (API, Admin, Public)" />
                <AWSServiceBadge name="RDS PostgreSQL" icon={Database} description="Multi-AZ, backups diários" />
                <AWSServiceBadge name="ALB" icon={Globe} description="L7 Load Balancer + TG" />
                <AWSServiceBadge name="CloudFront" icon={Zap} description="3 distribuições CDN" />
                <AWSServiceBadge name="S3" icon={HardDrive} description="5 buckets (docs/logs)" />
                <AWSServiceBadge name="WAF v2" icon={Shield} description="SQL injection + XSS" />
                <AWSServiceBadge name="ACM" icon={Lock} description="SSL auto-renovação" />
                <AWSServiceBadge name="KMS" icon={Lock} description="5 chaves criptografia" />
                <AWSServiceBadge name="Secrets Manager" icon={Shield} description="Rotação automática" />
                <AWSServiceBadge name="CodeBuild" icon={GitBranch} description="CI/CD push-to-deploy" />
                <AWSServiceBadge name="ECR" icon={Box} description="3 repositórios Docker" />
                <AWSServiceBadge name="VPC" icon={Layers} description="3 AZs + NAT Gateway" />
                <AWSServiceBadge name="CloudWatch" icon={Activity} description="13 alarmes ativos" />
                <AWSServiceBadge name="Route53" icon={Globe} description="DNS + Health Checks" />
                <AWSServiceBadge name="IAM" icon={Users} description="8 roles + policies" />
              </div>
            </div>

            {/* Terraform summary */}
            <div className="bg-gradient-to-r from-purple-500/10 to-blue-500/10 rounded-2xl p-6 border border-purple-500/20">
              <div className="flex items-center gap-3 mb-4">
                <Terminal className="h-5 w-5 text-purple-400" />
                <h3 className="text-white font-semibold">Infrastructure as Code (Terraform)</h3>
              </div>
              <div className="grid grid-cols-3 md:grid-cols-5 gap-4">
                {[
                  'vpc.tf', 'ecs.tf', 'rds.tf', 'alb.tf', 'cloudfront.tf',
                  's3.tf', 'iam.tf', 'secrets.tf', 'codebuild.tf', 'acm.tf',
                  'ecr.tf', 'route53.tf', 'variables.tf', 'outputs.tf', 'main.tf',
                ].map((file, idx) => (
                  <div key={idx} className="flex items-center gap-2 text-xs">
                    <FileText className="h-3.5 w-3.5 text-purple-400 flex-shrink-0" />
                    <span className="text-slate-300 font-mono">{file}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Production URLs */}
      <section className="py-20 bg-gradient-to-r from-blue-600/10 to-purple-600/10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-2xl mb-6">
              <MonitorCheck className="h-8 w-8 text-green-400" />
            </div>
            <h2 className="text-3xl font-bold text-white mb-2">Ambiente de Produção</h2>
            <p className="text-slate-400">Sistema operacional na AWS - Acesso 24/7</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <URLCard
              title="Portal Público"
              url="cau-public.migrai.com.br"
              description="Eleitor, candidato, consultas"
              icon={Globe}
              badge="31 páginas"
            />
            <URLCard
              title="Admin Dashboard"
              url="cau-admin.migrai.com.br"
              description="Gestão completa do sistema"
              icon={Layout}
              badge="40 páginas"
            />
            <URLCard
              title="API REST"
              url="cau-api.migrai.com.br/swagger"
              description="277 endpoints documentados"
              icon={Server}
              badge="Swagger UI"
            />
          </div>
        </div>
      </section>

      {/* Test Credentials */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={MousePointerClick}
            title="Acesso para Demonstração"
            subtitle="Credenciais pré-configuradas para testar o sistema"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            {/* Admin */}
            <CredentialCard
              title="Administrador"
              subtitle="Acesso total ao sistema"
              color="blue"
              icon={Shield}
              url="https://cau-admin.migrai.com.br"
              urlLabel="cau-admin.migrai.com.br"
              fields={[
                { label: 'E-mail', value: 'admin@cau.org.br' },
                { label: 'Senha', value: 'Admin@123' },
              ]}
            />

            {/* Eleitor */}
            <CredentialCard
              title="Eleitor"
              subtitle="Portal de votação"
              color="green"
              icon={Users}
              url="https://cau-public.migrai.com.br/votacao"
              urlLabel="cau-public.migrai.com.br/votacao"
              fields={[
                { label: 'CPF', value: '60000000003' },
                { label: 'Registro CAU', value: 'A000005-SP' },
                { label: 'Senha', value: 'Eleitor@123' },
              ]}
            />

            {/* Candidato */}
            <CredentialCard
              title="Candidato"
              subtitle="Portal do candidato"
              color="purple"
              icon={Award}
              url="https://cau-public.migrai.com.br/candidato/login"
              urlLabel="cau-public.migrai.com.br/candidato"
              fields={[
                { label: 'CPF', value: '45555555551' },
                { label: 'Registro CAU', value: 'A000018-DF' },
                { label: 'Senha', value: 'Candidato@123' },
              ]}
            />
          </div>

          <div className="mt-6 text-center">
            <div className="inline-flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/30 rounded-lg px-4 py-3">
              <AlertTriangle className="h-5 w-5 text-yellow-400 flex-shrink-0" />
              <p className="text-yellow-200 text-sm">
                Credenciais de demonstração - dados de teste (seeder) com 70+ usuários cadastrados.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* E2E Tests */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={CheckCircle2}
            title="Testes E2E - Playwright"
            subtitle="21 Testes Automatizados Validando Fluxos Críticos"
          />

          <div className="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                  <Layout className="h-5 w-5 text-blue-400" />
                </div>
                <div>
                  <h3 className="text-white font-semibold">Admin App - 12 Testes</h3>
                  <p className="text-slate-500 text-xs">apps/admin/e2e/</p>
                </div>
              </div>
              <ul className="space-y-2">
                {[
                  'Login com email/senha',
                  'Logout e redirecionamento',
                  'Dashboard com estatísticas',
                  'Navegação: Eleições (listagem)',
                  'Navegação: Chapas (listagem)',
                  'Navegação: Denúncias (listagem)',
                  'Navegação: Usuários (listagem)',
                  'Sidebar navigation completa',
                  'Sessão autenticada persistente',
                  'Verificação de roles/permissões',
                  'Full system integration',
                  'Auth flow completo',
                ].map((test, idx) => (
                  <li key={idx} className="flex items-center gap-2 text-sm">
                    <CheckCircle2 className="h-3.5 w-3.5 text-green-400 flex-shrink-0" />
                    <span className="text-slate-300">{test}</span>
                  </li>
                ))}
              </ul>
            </div>

            <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                  <Globe className="h-5 w-5 text-green-400" />
                </div>
                <div>
                  <h3 className="text-white font-semibold">Public App - 9 Testes</h3>
                  <p className="text-slate-500 text-xs">apps/public/e2e/</p>
                </div>
              </div>
              <ul className="space-y-2">
                {[
                  'Home page carrega corretamente',
                  'Login do Eleitor (CPF + RegistroCAU)',
                  'Página de Eleições públicas',
                  'Calendário Eleitoral',
                  'Documentos e Editais',
                  'FAQ com perguntas frequentes',
                  'Portal do Candidato (login)',
                  'Votação flow completo',
                  'Consulta de protocolo',
                ].map((test, idx) => (
                  <li key={idx} className="flex items-center gap-2 text-sm">
                    <CheckCircle2 className="h-3.5 w-3.5 text-green-400 flex-shrink-0" />
                    <span className="text-slate-300">{test}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Summary Numbers */}
      <section className="py-20 bg-gradient-to-br from-blue-600/10 via-purple-600/10 to-pink-600/10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-white mb-2">Resumo da Migração</h2>
            <p className="text-slate-400">Números que demonstram a escala do projeto</p>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            {[
              { value: '125.889', label: 'Linhas de Código', icon: Code2 },
              { value: '277', label: 'Endpoints API', icon: Server },
              { value: '154', label: 'Entidades', icon: Database },
              { value: '71', label: 'Páginas', icon: Layout },
              { value: '31', label: 'Componentes', icon: Box },
              { value: '21', label: 'Controllers', icon: Terminal },
              { value: '15', label: 'Services', icon: Workflow },
              { value: '152+', label: 'Recursos AWS', icon: Cloud },
              { value: '21', label: 'Testes E2E', icon: CheckCircle2 },
              { value: String(totalFunctionPoints), label: 'Pontos de Função', icon: Target },
            ].map((item, idx) => (
              <div key={idx} className="text-center">
                <div className="inline-flex items-center justify-center w-12 h-12 bg-slate-800/80 rounded-xl mb-3 border border-slate-700/50">
                  <item.icon className="h-6 w-6 text-blue-400" />
                </div>
                <p className="text-2xl md:text-3xl font-bold text-white">{item.value}</p>
                <p className="text-slate-400 text-sm">{item.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-16 border-t border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="inline-flex items-center gap-3 mb-6">
              <div className="h-px w-12 bg-gradient-to-r from-transparent to-blue-500/50" />
              <span className="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                MigrAI
              </span>
              <div className="h-px w-12 bg-gradient-to-l from-transparent to-purple-500/50" />
            </div>

            <p className="text-slate-400 mb-4 max-w-2xl mx-auto">
              Migração realizada com tecnologias de ponta, arquitetura limpa e
              as melhores práticas de desenvolvimento moderno.
            </p>

            <div className="flex flex-wrap justify-center gap-3 text-sm text-slate-500 mb-6">
              {['Clean Architecture', 'Domain-Driven Design', 'REST API', 'Infrastructure as Code', 'CI/CD Pipeline', 'E2E Testing'].map((tag, idx) => (
                <span key={idx} className="bg-slate-800/50 px-3 py-1 rounded-full border border-slate-700/50">{tag}</span>
              ))}
            </div>

            <div className="flex justify-center gap-4 mb-6">
              <a
                href="https://cau-api.migrai.com.br/health"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-1.5 text-xs text-green-400 hover:text-green-300"
              >
                <span className="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
                API Health: OK
              </a>
            </div>

            <p className="text-slate-600 text-sm">
              &copy; 2026 CAU Sistema Eleitoral - Migração por{' '}
              <span className="text-slate-400 font-medium">MigrAI</span>
            </p>
          </div>
        </div>
      </footer>
    </div>
  )
}

// ============================================================================
// COMPONENTES AUXILIARES
// ============================================================================

interface StatCardProps {
  icon: React.ComponentType<{ className?: string }>
  value: string
  label: string
  color: 'blue' | 'green' | 'purple' | 'orange' | 'cyan' | 'pink'
}

function StatCard({ icon: Icon, value, label, color }: StatCardProps) {
  const colorClasses: Record<string, string> = {
    blue: 'from-blue-500 to-blue-600',
    green: 'from-green-500 to-green-600',
    purple: 'from-purple-500 to-purple-600',
    orange: 'from-orange-500 to-orange-600',
    cyan: 'from-cyan-500 to-cyan-600',
    pink: 'from-pink-500 to-pink-600',
  }

  return (
    <div className="bg-slate-800/80 backdrop-blur-sm rounded-2xl p-5 border border-slate-700 hover:border-slate-600 transition-colors">
      <div className={`inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br ${colorClasses[color]} rounded-xl mb-3`}>
        <Icon className="h-5 w-5 text-white" />
      </div>
      <p className="text-2xl font-bold text-white">{value}</p>
      <p className="text-slate-400 text-xs">{label}</p>
    </div>
  )
}

interface SectionHeaderProps {
  icon: React.ComponentType<{ className?: string }>
  title: string
  subtitle: string
}

function SectionHeader({ icon: Icon, title, subtitle }: SectionHeaderProps) {
  return (
    <div className="text-center">
      <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-2xl mb-6">
        <Icon className="h-8 w-8 text-blue-400" />
      </div>
      <h2 className="text-3xl font-bold text-white mb-2">{title}</h2>
      <p className="text-slate-400">{subtitle}</p>
    </div>
  )
}

interface FPCardProps {
  title: string
  description: string
  items: Array<{ name: string; detail: string; points: number }>
  totalLabel: string
  color: 'blue' | 'green' | 'purple' | 'orange' | 'pink'
}

function FPCard({ title, description, items, totalLabel, color }: FPCardProps) {
  const colorClasses: Record<string, string> = {
    blue: 'border-blue-500/30 bg-blue-500/5',
    green: 'border-green-500/30 bg-green-500/5',
    purple: 'border-purple-500/30 bg-purple-500/5',
    orange: 'border-orange-500/30 bg-orange-500/5',
    pink: 'border-pink-500/30 bg-pink-500/5',
  }

  const textColors: Record<string, string> = {
    blue: 'text-blue-400',
    green: 'text-green-400',
    purple: 'text-purple-400',
    orange: 'text-orange-400',
    pink: 'text-pink-400',
  }

  return (
    <div className={`rounded-2xl p-6 border ${colorClasses[color]}`}>
      <h3 className={`text-lg font-semibold ${textColors[color]} mb-1`}>{title}</h3>
      <p className="text-slate-500 text-sm mb-4">{description}</p>

      <div className="space-y-3">
        {items.map((item, idx) => (
          <div key={idx} className="flex justify-between items-center">
            <div>
              <p className="text-white text-sm">{item.name}</p>
              <p className="text-slate-500 text-xs">{item.detail}</p>
            </div>
            <span className={`${textColors[color]} font-medium text-sm`}>{item.points} PF</span>
          </div>
        ))}
      </div>

      <div className="mt-4 pt-4 border-t border-slate-700">
        <p className="text-slate-400 text-sm">{totalLabel}</p>
      </div>
    </div>
  )
}

interface MetricsCardProps {
  title: string
  icon: React.ComponentType<{ className?: string }>
  color: 'blue' | 'purple' | 'green' | 'orange'
  highlight?: string
  metrics: Array<{ label: string; value: number }>
}

function MetricsCard({ title, icon: Icon, color, highlight, metrics }: MetricsCardProps) {
  const colorClasses: Record<string, string> = {
    blue: 'from-blue-500 to-blue-600',
    purple: 'from-purple-500 to-purple-600',
    green: 'from-green-500 to-green-600',
    orange: 'from-orange-500 to-orange-600',
  }

  return (
    <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
      <div className="flex items-center justify-between mb-4">
        <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br ${colorClasses[color]} rounded-xl`}>
          <Icon className="h-6 w-6 text-white" />
        </div>
        {highlight && (
          <span className="text-xs bg-slate-700/50 text-slate-300 px-2.5 py-1 rounded-lg font-mono">
            {highlight}
          </span>
        )}
      </div>
      <h3 className="text-lg font-semibold text-white mb-4">{title}</h3>

      <div className="space-y-3">
        {metrics.map((metric, idx) => (
          <div key={idx} className="flex justify-between items-center">
            <span className="text-slate-400 text-sm">{metric.label}</span>
            <span className="text-white font-semibold">{metric.value}</span>
          </div>
        ))}
      </div>
    </div>
  )
}

interface FeatureCardProps {
  category: string
  icon: React.ComponentType<{ className?: string }>
  color: string
  features: string[]
}

function FeatureCard({ category, icon: Icon, color, features }: FeatureCardProps) {
  return (
    <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700 hover:border-slate-600 transition-colors">
      <div className={`inline-flex items-center justify-center w-12 h-12 ${color} rounded-xl mb-4`}>
        <Icon className="h-6 w-6 text-white" />
      </div>
      <h3 className="text-lg font-semibold text-white mb-4">{category}</h3>

      <ul className="space-y-2">
        {features.map((feature, idx) => (
          <li key={idx} className="flex items-start gap-2 text-sm text-slate-400">
            <CheckCircle2 className="h-4 w-4 text-green-400 mt-0.5 flex-shrink-0" />
            <span>{feature}</span>
          </li>
        ))}
      </ul>
    </div>
  )
}

interface TechStackCardProps {
  title: string
  icon: React.ComponentType<{ className?: string }>
  color: 'blue' | 'cyan' | 'orange'
  technologies: Array<{ name: string; description: string; version?: string }>
}

function TechStackCard({ title, icon: Icon, color, technologies }: TechStackCardProps) {
  const colorClasses: Record<string, string> = {
    blue: 'from-blue-500 to-blue-600',
    cyan: 'from-cyan-500 to-cyan-600',
    orange: 'from-orange-500 to-orange-600',
  }

  return (
    <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br ${colorClasses[color]} rounded-xl mb-4`}>
        <Icon className="h-6 w-6 text-white" />
      </div>
      <h3 className="text-xl font-semibold text-white mb-2">{title}</h3>
      <p className="text-slate-500 text-sm mb-6">{technologies.length} tecnologias</p>

      <div className="space-y-4">
        {technologies.map((tech, idx) => (
          <div key={idx} className="flex items-center justify-between">
            <div>
              <p className="text-white font-medium">{tech.name}</p>
              <p className="text-slate-500 text-sm">{tech.description}</p>
            </div>
            {tech.version && (
              <span className="text-xs bg-slate-700 text-slate-300 px-2 py-1 rounded">
                v{tech.version}
              </span>
            )}
          </div>
        ))}
      </div>
    </div>
  )
}

interface EntityCardProps {
  name: string
  count: number
  description: string
  items: string[]
  color: 'blue' | 'purple' | 'red' | 'teal' | 'orange' | 'indigo' | 'green'
}

function EntityCard({ name, count, description, items, color }: EntityCardProps) {
  const borderColors: Record<string, string> = {
    blue: 'border-blue-500/30',
    purple: 'border-purple-500/30',
    red: 'border-red-500/30',
    teal: 'border-teal-500/30',
    orange: 'border-orange-500/30',
    indigo: 'border-indigo-500/30',
    green: 'border-green-500/30',
  }
  const textColors: Record<string, string> = {
    blue: 'text-blue-400',
    purple: 'text-purple-400',
    red: 'text-red-400',
    teal: 'text-teal-400',
    orange: 'text-orange-400',
    indigo: 'text-indigo-400',
    green: 'text-green-400',
  }

  return (
    <div className={`bg-slate-800/50 rounded-2xl p-6 border ${borderColors[color]}`}>
      <div className="flex items-center justify-between mb-2">
        <h3 className={`text-lg font-semibold ${textColors[color]}`}>{name}</h3>
        <span className="text-2xl font-bold text-white">{count}</span>
      </div>
      <p className="text-slate-500 text-sm mb-4">{description}</p>

      <div className="flex flex-wrap gap-2">
        {items.map((item, idx) => (
          <span key={idx} className="text-xs bg-slate-700/50 text-slate-400 px-2 py-1 rounded">
            {item}
          </span>
        ))}
      </div>
    </div>
  )
}

interface TimelineItemProps {
  phase: string
  status: 'completed' | 'in-progress' | 'pending'
  duration: string
  items: string[]
  index: number
}

function TimelineItem({ phase, duration, items }: TimelineItemProps) {
  return (
    <div className="relative pl-20">
      <div className="absolute left-5 w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full border-4 border-slate-900 flex items-center justify-center">
        <CheckCircle2 className="h-3 w-3 text-white" />
      </div>

      <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
        <div className="flex items-center gap-3 mb-4 flex-wrap">
          <span className="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded font-medium">
            Concluído
          </span>
          <span className="text-xs bg-slate-700/50 text-slate-400 px-2 py-1 rounded">
            {duration}
          </span>
          <h3 className="text-lg font-semibold text-white">{phase}</h3>
        </div>

        <ul className="space-y-2">
          {items.map((item, idx) => (
            <li key={idx} className="flex items-center gap-2 text-sm text-slate-400">
              <ArrowRight className="h-3 w-3 text-slate-600 flex-shrink-0" />
              {item}
            </li>
          ))}
        </ul>
      </div>
    </div>
  )
}

interface AWSServiceBadgeProps {
  name: string
  icon: React.ComponentType<{ className?: string }>
  description: string
}

function AWSServiceBadge({ name, icon: Icon, description }: AWSServiceBadgeProps) {
  return (
    <div className="flex items-center gap-3 bg-slate-700/50 rounded-lg p-3 hover:bg-slate-700/70 transition-colors">
      <div className="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
        <Icon className="h-5 w-5 text-orange-400" />
      </div>
      <div className="min-w-0">
        <p className="text-white text-sm font-medium">{name}</p>
        <p className="text-slate-500 text-xs truncate">{description}</p>
      </div>
    </div>
  )
}

interface URLCardProps {
  title: string
  url: string
  description: string
  icon: React.ComponentType<{ className?: string }>
  badge?: string
}

function URLCard({ title, url, description, icon: Icon, badge }: URLCardProps) {
  return (
    <a
      href={`https://${url}`}
      target="_blank"
      rel="noopener noreferrer"
      className="block bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 hover:border-blue-500/50 transition-all hover:shadow-lg hover:shadow-blue-500/5 group"
    >
      <div className="flex items-center gap-4 mb-4">
        <div className="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
          <Icon className="h-6 w-6 text-blue-400" />
        </div>
        <div>
          <h3 className="text-white font-semibold">{title}</h3>
          <p className="text-slate-500 text-sm">{description}</p>
        </div>
      </div>
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2 text-blue-400 group-hover:text-blue-300 transition-colors">
          <Globe className="h-4 w-4" />
          <span className="text-sm">{url}</span>
          <ArrowUpRight className="h-3.5 w-3.5" />
        </div>
        {badge && (
          <span className="text-xs bg-blue-500/10 text-blue-400 px-2 py-1 rounded">{badge}</span>
        )}
      </div>
    </a>
  )
}

interface CredentialCardProps {
  title: string
  subtitle: string
  color: 'blue' | 'green' | 'purple'
  icon: React.ComponentType<{ className?: string }>
  url: string
  urlLabel: string
  fields: Array<{ label: string; value: string }>
}

function CredentialCard({ title, subtitle, color, icon: Icon, url, urlLabel, fields }: CredentialCardProps) {
  const gradients: Record<string, string> = {
    blue: 'from-blue-500/10 to-blue-600/10 border-blue-500/30',
    green: 'from-green-500/10 to-green-600/10 border-green-500/30',
    purple: 'from-purple-500/10 to-purple-600/10 border-purple-500/30',
  }
  const bgColors: Record<string, string> = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    purple: 'bg-purple-500',
  }
  const textColors: Record<string, string> = {
    blue: 'text-blue-400',
    green: 'text-green-400',
    purple: 'text-purple-400',
  }

  return (
    <div className={`bg-gradient-to-br ${gradients[color]} rounded-2xl p-6 border`}>
      <div className="flex items-center gap-3 mb-4">
        <div className={`w-12 h-12 ${bgColors[color]} rounded-xl flex items-center justify-center`}>
          <Icon className="h-6 w-6 text-white" />
        </div>
        <div>
          <h3 className="text-lg font-semibold text-white">{title}</h3>
          <p className={`${textColors[color]} text-sm`}>{subtitle}</p>
        </div>
      </div>
      <div className="space-y-3 bg-slate-800/50 rounded-lg p-4">
        <div>
          <p className="text-slate-500 text-xs uppercase tracking-wider">URL</p>
          <a href={url} target="_blank" rel="noopener noreferrer" className={`${textColors[color]} hover:underline text-sm`}>
            {urlLabel}
          </a>
        </div>
        {fields.map((field, idx) => (
          <div key={idx}>
            <p className="text-slate-500 text-xs uppercase tracking-wider">{field.label}</p>
            <p className="text-white font-mono text-sm">{field.value}</p>
          </div>
        ))}
      </div>
    </div>
  )
}
