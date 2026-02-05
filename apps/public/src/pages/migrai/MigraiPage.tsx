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
  Cpu,
  HardDrive,
  Globe,
  Lock,
  Zap,
  ArrowRight,
  Activity,
  Target,
  TrendingUp,
  Award,
  Calendar
} from 'lucide-react'

// ============================================================================
// DADOS DA MIGRAÇÃO - CONTAGEM DE PONTOS DE FUNÇÃO (IFPUG)
// ============================================================================

const functionPointData = {
  // ILF - Internal Logical Files (Entidades de Domínio)
  ilf: {
    description: 'Arquivos Lógicos Internos',
    items: [
      { name: 'Core (Eleições, Votos, Resultados)', count: 24, complexity: 'high', points: 15 },
      { name: 'Chapas (Candidaturas)', count: 8, complexity: 'medium', points: 10 },
      { name: 'Denúncias (Reclamações)', count: 23, complexity: 'high', points: 15 },
      { name: 'Documentos (Atas, Editais, etc)', count: 43, complexity: 'high', points: 15 },
      { name: 'Impugnações (Contestações)', count: 13, complexity: 'high', points: 15 },
      { name: 'Julgamentos (Processos)', count: 33, complexity: 'high', points: 15 },
      { name: 'Usuários (Autenticação)', count: 9, complexity: 'medium', points: 10 },
    ],
    totalEntities: 153,
    avgPointsPerEntity: 7,
  },
  // EIF - External Interface Files
  eif: {
    description: 'Arquivos de Interface Externa',
    items: [
      { name: 'AWS S3 (Documentos)', points: 7 },
      { name: 'AWS S3 (Uploads)', points: 7 },
      { name: 'AWS Secrets Manager', points: 5 },
      { name: 'SMTP (E-mail)', points: 5 },
    ],
  },
  // EI - External Inputs (Endpoints de Escrita)
  ei: {
    description: 'Entradas Externas',
    items: [
      { name: 'Auth (Login, Registro, Refresh)', count: 5, complexity: 'medium', points: 4 },
      { name: 'Eleições (CRUD + Config)', count: 8, complexity: 'high', points: 6 },
      { name: 'Chapas (CRUD + Membros)', count: 12, complexity: 'high', points: 6 },
      { name: 'Votação (Registro de Votos)', count: 4, complexity: 'high', points: 6 },
      { name: 'Denúncias (CRUD + Análise)', count: 10, complexity: 'high', points: 6 },
      { name: 'Documentos (Upload, CRUD)', count: 8, complexity: 'medium', points: 4 },
      { name: 'Impugnações (CRUD)', count: 6, complexity: 'high', points: 6 },
      { name: 'Julgamentos (CRUD + Sessões)', count: 10, complexity: 'high', points: 6 },
      { name: 'Usuários (CRUD)', count: 6, complexity: 'medium', points: 4 },
      { name: 'Configurações', count: 4, complexity: 'low', points: 3 },
    ],
  },
  // EO - External Outputs (Relatórios)
  eo: {
    description: 'Saídas Externas',
    items: [
      { name: 'Relatórios de Votação', count: 5, complexity: 'high', points: 7 },
      { name: 'Relatórios de Apuração', count: 4, complexity: 'high', points: 7 },
      { name: 'Boletins de Urna', count: 2, complexity: 'medium', points: 5 },
      { name: 'Atas de Sessão', count: 3, complexity: 'medium', points: 5 },
      { name: 'Dashboard Analytics', count: 6, complexity: 'high', points: 7 },
      { name: 'Exportação de Dados', count: 3, complexity: 'medium', points: 5 },
    ],
  },
  // EQ - External Inquiries (Consultas)
  eq: {
    description: 'Consultas Externas',
    items: [
      { name: 'Listagens Paginadas', count: 20, complexity: 'low', points: 3 },
      { name: 'Detalhes de Entidades', count: 15, complexity: 'low', points: 3 },
      { name: 'Buscas Avançadas', count: 8, complexity: 'medium', points: 4 },
      { name: 'Validações', count: 10, complexity: 'low', points: 3 },
    ],
  },
}

// Cálculo dos Pontos de Função
const calculateFunctionPoints = () => {
  let total = 0

  // ILF: 7 pontos médio por entidade Low, 10 Medium, 15 High
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
// MÉTRICAS DO SISTEMA
// ============================================================================

const systemMetrics = {
  backend: {
    entities: 153,
    controllers: 20,
    services: 14,
    dtos: 84,
    enums: 8,
    repositories: 2,
    totalFiles: 222,
  },
  frontendAdmin: {
    pages: 32,
    components: 45,
    services: 13,
    hooks: 6,
    stores: 5,
  },
  frontendPublic: {
    pages: 26,
    components: 28,
    services: 6,
    stores: 4,
  },
  infrastructure: {
    dockerFiles: 4,
    terraformFiles: 15,
    awsServices: 13,
    microservices: 3,
  },
  tests: {
    e2eAdmin: 12,
    e2ePublic: 9,
    total: 21,
  },
}

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

// Icon para Scale (não existe no lucide)
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
    { name: 'ASP.NET Core', description: 'Web API', version: '10.0' },
    { name: 'Entity Framework Core', description: 'ORM', version: '10.0.2' },
    { name: 'PostgreSQL', description: 'Banco de dados', version: '15' },
    { name: 'JWT', description: 'Autenticação', version: 'PBKDF2 100K' },
    { name: 'Serilog', description: 'Logging', version: '3.x' },
    { name: 'Swagger/OpenAPI', description: 'Documentação', version: '6.5' },
  ],
  frontend: [
    { name: 'React', description: 'UI Library', version: '18.3' },
    { name: 'TypeScript', description: 'Tipagem', version: '5.6' },
    { name: 'Vite', description: 'Build Tool', version: '5.4' },
    { name: 'shadcn/ui', description: 'Componentes', version: 'latest' },
    { name: 'Tailwind CSS', description: 'Estilos', version: '3.4' },
    { name: 'React Router', description: 'Roteamento', version: '6.x' },
    { name: 'TanStack Query', description: 'State/Cache', version: '5.x' },
  ],
  infrastructure: [
    { name: 'AWS ECS Fargate', description: 'Containers serverless' },
    { name: 'AWS RDS', description: 'PostgreSQL gerenciado' },
    { name: 'AWS ALB', description: 'Load Balancer' },
    { name: 'AWS CloudFront', description: 'CDN' },
    { name: 'AWS S3', description: 'Armazenamento' },
    { name: 'AWS CodeBuild', description: 'CI/CD' },
    { name: 'AWS Secrets Manager', description: 'Segredos' },
    { name: 'AWS Route53', description: 'DNS' },
    { name: 'AWS ACM', description: 'SSL/TLS' },
    { name: 'Terraform', description: 'IaC', version: '1.5+' },
    { name: 'Docker', description: 'Containers' },
  ],
}

// ============================================================================
// TIMELINE DE MIGRAÇÃO
// ============================================================================

const migrationTimeline: Array<{
  phase: string
  status: 'completed' | 'in-progress' | 'pending'
  items: string[]
}> = [
  {
    phase: 'Análise e Planejamento',
    status: 'completed',
    items: [
      'Mapeamento do sistema legado PHP/Java',
      'Análise de ~71 entidades originais',
      'Definição da arquitetura Clean Architecture',
      'Escolha do stack tecnológico',
    ],
  },
  {
    phase: 'Desenvolvimento Backend',
    status: 'completed',
    items: [
      'Criação de 153 entidades de domínio',
      'Implementação de 20 controllers',
      'Desenvolvimento de 14 services',
      'Configuração do Entity Framework Core',
      'Sistema de autenticação JWT',
    ],
  },
  {
    phase: 'Desenvolvimento Frontend',
    status: 'completed',
    items: [
      'Setup React 18 + Vite',
      'Implementação shadcn/ui',
      '32 páginas Admin Dashboard',
      '26 páginas Portal Público',
      'Portal do Eleitor e Candidato',
    ],
  },
  {
    phase: 'Infraestrutura AWS',
    status: 'completed',
    items: [
      'Provisionamento com Terraform',
      'ECS Fargate para containers',
      'RDS PostgreSQL Multi-AZ',
      'CloudFront + ALB',
      'Pipeline CI/CD com CodeBuild',
    ],
  },
  {
    phase: 'Testes e Deploy',
    status: 'completed',
    items: [
      '21 testes E2E com Playwright',
      'Testes de segurança',
      'Deploy em produção',
      'Monitoramento CloudWatch',
    ],
  },
]

// ============================================================================
// COMPONENTE PRINCIPAL
// ============================================================================

export function MigraiPage() {
  const totalFunctionPoints = calculateFunctionPoints()

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
      {/* Hero Section */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20" />
        <div className="absolute inset-0">
          <div className="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl" />
          <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl" />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 py-20 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 rounded-full px-4 py-2 mb-6">
              <Zap className="h-4 w-4 text-blue-400" />
              <span className="text-blue-300 text-sm font-medium">Migração Concluída</span>
            </div>

            <h1 className="text-5xl md:text-7xl font-bold bg-gradient-to-r from-white via-blue-100 to-purple-200 bg-clip-text text-transparent mb-6">
              CAU Sistema Eleitoral
            </h1>

            <p className="text-xl md:text-2xl text-slate-300 mb-4">
              Migração Completa de PHP/Java para
            </p>

            <div className="flex flex-wrap justify-center gap-3 mb-8">
              <span className="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-semibold">
                .NET 10
              </span>
              <span className="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-4 py-2 rounded-lg font-semibold">
                React 18
              </span>
              <span className="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-lg font-semibold">
                shadcn/ui
              </span>
              <span className="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg font-semibold">
                AWS
              </span>
            </div>

            <p className="text-slate-400 max-w-3xl mx-auto">
              Documentação completa da migração do sistema eleitoral do Conselho de Arquitetura
              e Urbanismo, incluindo análise de pontos de função, métricas detalhadas e
              todas as funcionalidades implementadas.
            </p>
          </div>
        </div>
      </section>

      {/* Stats Overview */}
      <section className="relative -mt-10 z-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            <StatCard
              icon={Target}
              value={totalFunctionPoints.toString()}
              label="Pontos de Função"
              color="blue"
            />
            <StatCard
              icon={Database}
              value="153"
              label="Entidades"
              color="green"
            />
            <StatCard
              icon={Layout}
              value="58"
              label="Páginas"
              color="purple"
            />
            <StatCard
              icon={Cloud}
              value="13"
              label="Serviços AWS"
              color="orange"
            />
          </div>
        </div>
      </section>

      {/* Function Point Analysis */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={BarChart3}
            title="Análise de Pontos de Função"
            subtitle="Metodologia IFPUG - Contagem Detalhada"
          />

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12">
            {/* ILF */}
            <FPCard
              title="ILF - Arquivos Lógicos Internos"
              description="Entidades de domínio mantidas pelo sistema"
              items={functionPointData.ilf.items.map(i => ({
                name: i.name,
                detail: `${i.count} entidades`,
                points: i.count * (i.complexity === 'high' ? 15 : i.complexity === 'medium' ? 10 : 7) / i.count,
              }))}
              totalLabel={`${functionPointData.ilf.totalEntities} entidades totais`}
              color="blue"
            />

            {/* EIF */}
            <FPCard
              title="EIF - Arquivos de Interface Externa"
              description="Integrações com sistemas externos"
              items={functionPointData.eif.items.map(i => ({
                name: i.name,
                detail: 'Integração',
                points: i.points,
              }))}
              totalLabel="4 integrações externas"
              color="green"
            />

            {/* EI */}
            <FPCard
              title="EI - Entradas Externas"
              description="Operações de escrita/atualização"
              items={functionPointData.ei.items.map(i => ({
                name: i.name,
                detail: `${i.count} endpoints`,
                points: i.count * i.points,
              }))}
              totalLabel="73 endpoints de escrita"
              color="purple"
            />

            {/* EO */}
            <FPCard
              title="EO - Saídas Externas"
              description="Relatórios e exportações"
              items={functionPointData.eo.items.map(i => ({
                name: i.name,
                detail: `${i.count} relatórios`,
                points: i.count * i.points,
              }))}
              totalLabel="23 tipos de relatórios"
              color="orange"
            />

            {/* EQ */}
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

            {/* Total */}
            <div className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700">
              <div className="text-center">
                <div className="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl mb-6">
                  <Award className="h-10 w-10 text-white" />
                </div>
                <h3 className="text-4xl font-bold text-white mb-2">{totalFunctionPoints}</h3>
                <p className="text-slate-400 text-lg mb-6">Pontos de Função Totais</p>

                <div className="grid grid-cols-2 gap-4 text-left">
                  <div className="bg-slate-800/50 rounded-lg p-4">
                    <p className="text-slate-400 text-sm">Complexidade</p>
                    <p className="text-white font-semibold">Alta</p>
                  </div>
                  <div className="bg-slate-800/50 rounded-lg p-4">
                    <p className="text-slate-400 text-sm">Fator de Ajuste</p>
                    <p className="text-white font-semibold">1.15</p>
                  </div>
                  <div className="bg-slate-800/50 rounded-lg p-4">
                    <p className="text-slate-400 text-sm">PF Ajustados</p>
                    <p className="text-white font-semibold">{Math.round(totalFunctionPoints * 1.15)}</p>
                  </div>
                  <div className="bg-slate-800/50 rounded-lg p-4">
                    <p className="text-slate-400 text-sm">Horas Estimadas</p>
                    <p className="text-white font-semibold">{Math.round(totalFunctionPoints * 1.15 * 8)}h</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* System Metrics */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Activity}
            title="Métricas do Sistema"
            subtitle="Contagem Detalhada de Artefatos"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
            {/* Backend */}
            <MetricsCard
              title="Backend .NET"
              icon={Server}
              color="blue"
              metrics={[
                { label: 'Entidades', value: systemMetrics.backend.entities },
                { label: 'Controllers', value: systemMetrics.backend.controllers },
                { label: 'Services', value: systemMetrics.backend.services },
                { label: 'DTOs', value: systemMetrics.backend.dtos },
                { label: 'Enums', value: systemMetrics.backend.enums },
                { label: 'Total Arquivos', value: systemMetrics.backend.totalFiles },
              ]}
            />

            {/* Admin Frontend */}
            <MetricsCard
              title="Frontend Admin"
              icon={Layout}
              color="purple"
              metrics={[
                { label: 'Páginas', value: systemMetrics.frontendAdmin.pages },
                { label: 'Componentes', value: systemMetrics.frontendAdmin.components },
                { label: 'Services', value: systemMetrics.frontendAdmin.services },
                { label: 'Hooks', value: systemMetrics.frontendAdmin.hooks },
                { label: 'Stores', value: systemMetrics.frontendAdmin.stores },
              ]}
            />

            {/* Public Frontend */}
            <MetricsCard
              title="Frontend Public"
              icon={Globe}
              color="green"
              metrics={[
                { label: 'Páginas', value: systemMetrics.frontendPublic.pages },
                { label: 'Componentes', value: systemMetrics.frontendPublic.components },
                { label: 'Services', value: systemMetrics.frontendPublic.services },
                { label: 'Stores', value: systemMetrics.frontendPublic.stores },
              ]}
            />

            {/* Infrastructure */}
            <MetricsCard
              title="Infraestrutura"
              icon={Cloud}
              color="orange"
              metrics={[
                { label: 'Dockerfiles', value: systemMetrics.infrastructure.dockerFiles },
                { label: 'Terraform Files', value: systemMetrics.infrastructure.terraformFiles },
                { label: 'Serviços AWS', value: systemMetrics.infrastructure.awsServices },
                { label: 'Microservices', value: systemMetrics.infrastructure.microservices },
                { label: 'Testes E2E', value: systemMetrics.tests.total },
              ]}
            />
          </div>
        </div>
      </section>

      {/* Migrated Features */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Layers}
            title="Funcionalidades Migradas"
            subtitle="Módulos e Features Implementados"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">
            {migratedFeatures.map((feature, idx) => (
              <FeatureCard key={idx} {...feature} />
            ))}
          </div>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-20 bg-slate-800/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Code2}
            title="Stack Tecnológica"
            subtitle="Tecnologias Utilizadas na Migração"
          />

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
            {/* Backend Stack */}
            <TechStackCard
              title="Backend"
              icon={Server}
              color="blue"
              technologies={techStack.backend}
            />

            {/* Frontend Stack */}
            <TechStackCard
              title="Frontend"
              icon={Layout}
              color="cyan"
              technologies={techStack.frontend}
            />

            {/* Infrastructure Stack */}
            <TechStackCard
              title="Infraestrutura"
              icon={Cloud}
              color="orange"
              technologies={techStack.infrastructure}
            />
          </div>
        </div>
      </section>

      {/* Entity Categories */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Database}
            title="Entidades de Domínio"
            subtitle="153 Entidades Distribuídas em 7 Categorias"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            <EntityCard
              name="Core"
              count={24}
              description="Eleições, votos, resultados, calendário"
              items={['Eleicao', 'Calendario', 'Voto', 'ResultadoEleicao', 'SecaoEleitoral', 'UrnaEletronica']}
              color="blue"
            />
            <EntityCard
              name="Chapas"
              count={8}
              description="Candidaturas e composições"
              items={['ChapaEleicao', 'MembroChapa', 'PlataformaEleitoral', 'SubstituicaoMembroChapa']}
              color="purple"
            />
            <EntityCard
              name="Denúncias"
              count={23}
              description="Sistema completo de reclamações"
              items={['Denuncia', 'AnaliseDenuncia', 'DefesaDenuncia', 'RecursoDenuncia', 'JulgamentoDenuncia']}
              color="red"
            />
            <EntityCard
              name="Documentos"
              count={43}
              description="Gestão documental completa"
              items={['Documento', 'Edital', 'Resolucao', 'AtaReuniao', 'Diploma', 'TermoPosse']}
              color="teal"
            />
            <EntityCard
              name="Impugnações"
              count={13}
              description="Contestações de resultados"
              items={['ImpugnacaoResultado', 'PedidoImpugnacao', 'DefesaImpugnacao', 'RecursoImpugnacao']}
              color="orange"
            />
            <EntityCard
              name="Julgamentos"
              count={33}
              description="Processos e sessões judiciais"
              items={['ComissaoJulgadora', 'SessaoJulgamento', 'VotoRelator', 'AcordaoJulgamento']}
              color="indigo"
            />
            <EntityCard
              name="Usuários"
              count={9}
              description="Autenticação e permissões"
              items={['Usuario', 'Profissional', 'Conselheiro', 'Role', 'Permissao']}
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
            subtitle="Fases do Projeto"
          />

          <div className="mt-12">
            <div className="relative">
              {/* Timeline Line */}
              <div className="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 via-purple-500 to-green-500" />

              {/* Timeline Items */}
              <div className="space-y-8">
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
            title="Arquitetura AWS"
            subtitle="Infraestrutura de Produção"
          />

          <div className="mt-12 bg-slate-800/50 rounded-2xl p-8 border border-slate-700">
            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <AWSServiceBadge name="ECS Fargate" icon={Box} description="3 containers" />
              <AWSServiceBadge name="RDS PostgreSQL" icon={Database} description="Multi-AZ" />
              <AWSServiceBadge name="ALB" icon={Globe} description="Load Balancer" />
              <AWSServiceBadge name="CloudFront" icon={Zap} description="CDN Global" />
              <AWSServiceBadge name="S3" icon={HardDrive} description="2 buckets" />
              <AWSServiceBadge name="Route53" icon={Globe} description="DNS" />
              <AWSServiceBadge name="ACM" icon={Lock} description="SSL/TLS" />
              <AWSServiceBadge name="Secrets Manager" icon={Shield} description="Segredos" />
              <AWSServiceBadge name="CodeBuild" icon={GitBranch} description="CI/CD" />
              <AWSServiceBadge name="ECR" icon={Box} description="Registry" />
              <AWSServiceBadge name="VPC" icon={Layers} description="Rede" />
              <AWSServiceBadge name="IAM" icon={Users} description="Permissões" />
              <AWSServiceBadge name="CloudWatch" icon={Activity} description="Monitoring" />
            </div>
          </div>
        </div>
      </section>

      {/* Production URLs */}
      <section className="py-20 bg-gradient-to-r from-blue-600/10 to-purple-600/10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-white mb-4">Ambiente de Produção</h2>
            <p className="text-slate-400">Sistema em operação na AWS</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <URLCard
              title="Portal Público"
              url="cau-public.migrai.com.br"
              description="Área do eleitor e candidato"
              icon={Globe}
            />
            <URLCard
              title="Admin Dashboard"
              url="cau-admin.migrai.com.br"
              description="Gestão administrativa"
              icon={Layout}
            />
            <URLCard
              title="API REST"
              url="cau-api.migrai.com.br"
              description="Backend .NET 10"
              icon={Server}
            />
          </div>
        </div>
      </section>

      {/* Test Credentials */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <SectionHeader
            icon={Lock}
            title="Credenciais de Teste"
            subtitle="Usuários para demonstração do sistema"
          />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
            {/* Admin */}
            <div className="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-2xl p-6 border border-blue-500/30">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                  <Shield className="h-6 w-6 text-white" />
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-white">Administrador</h3>
                  <p className="text-blue-400 text-sm">Acesso total ao sistema</p>
                </div>
              </div>
              <div className="space-y-3 bg-slate-800/50 rounded-lg p-4">
                <div>
                  <p className="text-slate-500 text-xs uppercase">URL</p>
                  <a href="https://cau-admin.migrai.com.br" target="_blank" rel="noopener noreferrer" className="text-blue-400 hover:underline text-sm">cau-admin.migrai.com.br</a>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">E-mail</p>
                  <p className="text-white font-mono text-sm">admin@cau.org.br</p>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">Senha</p>
                  <p className="text-white font-mono text-sm">Admin@123</p>
                </div>
              </div>
            </div>

            {/* Eleitor */}
            <div className="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-2xl p-6 border border-green-500/30">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                  <Users className="h-6 w-6 text-white" />
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-white">Eleitor</h3>
                  <p className="text-green-400 text-sm">Portal de votação</p>
                </div>
              </div>
              <div className="space-y-3 bg-slate-800/50 rounded-lg p-4">
                <div>
                  <p className="text-slate-500 text-xs uppercase">URL</p>
                  <a href="https://cau-public.migrai.com.br/votacao" target="_blank" rel="noopener noreferrer" className="text-green-400 hover:underline text-sm">cau-public.migrai.com.br/votacao</a>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">CPF</p>
                  <p className="text-white font-mono text-sm">60000000003</p>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">Registro CAU</p>
                  <p className="text-white font-mono text-sm">A000005SP</p>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">Senha</p>
                  <p className="text-white font-mono text-sm">Eleitor@123</p>
                </div>
              </div>
            </div>

            {/* Candidato */}
            <div className="bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-2xl p-6 border border-purple-500/30">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                  <Award className="h-6 w-6 text-white" />
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-white">Candidato</h3>
                  <p className="text-purple-400 text-sm">Portal do candidato</p>
                </div>
              </div>
              <div className="space-y-3 bg-slate-800/50 rounded-lg p-4">
                <div>
                  <p className="text-slate-500 text-xs uppercase">URL</p>
                  <a href="https://cau-public.migrai.com.br/candidato/login" target="_blank" rel="noopener noreferrer" className="text-purple-400 hover:underline text-sm">cau-public.migrai.com.br/candidato</a>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">CPF</p>
                  <p className="text-white font-mono text-sm">45555555551</p>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">Registro CAU</p>
                  <p className="text-white font-mono text-sm">A000018DF</p>
                </div>
                <div>
                  <p className="text-slate-500 text-xs uppercase">Senha</p>
                  <p className="text-white font-mono text-sm">Candidato@123</p>
                </div>
              </div>
            </div>
          </div>

          <div className="mt-8 text-center">
            <div className="inline-flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/30 rounded-lg px-4 py-3">
              <Zap className="h-5 w-5 text-yellow-400" />
              <p className="text-yellow-200 text-sm">
                Estas credenciais são exclusivamente para demonstração e testes do sistema migrado.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-12 border-t border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <p className="text-slate-400 mb-4">
              Migração realizada com tecnologias de ponta e melhores práticas de desenvolvimento
            </p>
            <div className="flex justify-center gap-4 text-sm text-slate-500">
              <span>Clean Architecture</span>
              <span>•</span>
              <span>Domain-Driven Design</span>
              <span>•</span>
              <span>Infrastructure as Code</span>
              <span>•</span>
              <span>CI/CD Pipeline</span>
            </div>
            <p className="text-slate-600 text-sm mt-6">
              © 2024 CAU Sistema Eleitoral - Migração por MigrAI
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
  color: 'blue' | 'green' | 'purple' | 'orange'
}

function StatCard({ icon: Icon, value, label, color }: StatCardProps) {
  const colorClasses = {
    blue: 'from-blue-500 to-blue-600',
    green: 'from-green-500 to-green-600',
    purple: 'from-purple-500 to-purple-600',
    orange: 'from-orange-500 to-orange-600',
  }

  return (
    <div className="bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 border border-slate-700">
      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br ${colorClasses[color]} rounded-xl mb-4`}>
        <Icon className="h-6 w-6 text-white" />
      </div>
      <p className="text-3xl font-bold text-white">{value}</p>
      <p className="text-slate-400 text-sm">{label}</p>
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
  const colorClasses = {
    blue: 'border-blue-500/30 bg-blue-500/5',
    green: 'border-green-500/30 bg-green-500/5',
    purple: 'border-purple-500/30 bg-purple-500/5',
    orange: 'border-orange-500/30 bg-orange-500/5',
    pink: 'border-pink-500/30 bg-pink-500/5',
  }

  const textColors = {
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
            <span className={`${textColors[color]} font-medium`}>{item.points} PF</span>
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
  metrics: Array<{ label: string; value: number }>
}

function MetricsCard({ title, icon: Icon, color, metrics }: MetricsCardProps) {
  const colorClasses = {
    blue: 'from-blue-500 to-blue-600',
    purple: 'from-purple-500 to-purple-600',
    green: 'from-green-500 to-green-600',
    orange: 'from-orange-500 to-orange-600',
  }

  return (
    <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br ${colorClasses[color]} rounded-xl mb-4`}>
        <Icon className="h-6 w-6 text-white" />
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
  const colorClasses = {
    blue: 'from-blue-500 to-blue-600',
    cyan: 'from-cyan-500 to-cyan-600',
    orange: 'from-orange-500 to-orange-600',
  }

  return (
    <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br ${colorClasses[color]} rounded-xl mb-4`}>
        <Icon className="h-6 w-6 text-white" />
      </div>
      <h3 className="text-xl font-semibold text-white mb-6">{title}</h3>

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
  const colorClasses = {
    blue: 'border-blue-500/30 text-blue-400',
    purple: 'border-purple-500/30 text-purple-400',
    red: 'border-red-500/30 text-red-400',
    teal: 'border-teal-500/30 text-teal-400',
    orange: 'border-orange-500/30 text-orange-400',
    indigo: 'border-indigo-500/30 text-indigo-400',
    green: 'border-green-500/30 text-green-400',
  }

  return (
    <div className={`bg-slate-800/50 rounded-2xl p-6 border ${colorClasses[color].split(' ')[0]}`}>
      <div className="flex items-center justify-between mb-2">
        <h3 className={`text-lg font-semibold ${colorClasses[color].split(' ')[1]}`}>{name}</h3>
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
  items: string[]
  index: number
}

function TimelineItem({ phase, status, items, index }: TimelineItemProps) {
  return (
    <div className="relative pl-20">
      {/* Circle */}
      <div className="absolute left-5 w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full border-4 border-slate-900 flex items-center justify-center">
        <CheckCircle2 className="h-3 w-3 text-white" />
      </div>

      {/* Content */}
      <div className="bg-slate-800/50 rounded-2xl p-6 border border-slate-700">
        <div className="flex items-center gap-3 mb-4">
          <span className="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded">
            Concluído
          </span>
          <h3 className="text-lg font-semibold text-white">{phase}</h3>
        </div>

        <ul className="space-y-2">
          {items.map((item, idx) => (
            <li key={idx} className="flex items-center gap-2 text-sm text-slate-400">
              <ArrowRight className="h-3 w-3 text-slate-600" />
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
    <div className="flex items-center gap-3 bg-slate-700/50 rounded-lg p-3">
      <div className="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
        <Icon className="h-5 w-5 text-orange-400" />
      </div>
      <div>
        <p className="text-white text-sm font-medium">{name}</p>
        <p className="text-slate-500 text-xs">{description}</p>
      </div>
    </div>
  )
}

interface URLCardProps {
  title: string
  url: string
  description: string
  icon: React.ComponentType<{ className?: string }>
}

function URLCard({ title, url, description, icon: Icon }: URLCardProps) {
  return (
    <div className="bg-slate-800/80 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 hover:border-blue-500/50 transition-colors">
      <div className="flex items-center gap-4 mb-4">
        <div className="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
          <Icon className="h-6 w-6 text-blue-400" />
        </div>
        <div>
          <h3 className="text-white font-semibold">{title}</h3>
          <p className="text-slate-500 text-sm">{description}</p>
        </div>
      </div>
      <a
        href={`https://${url}`}
        target="_blank"
        rel="noopener noreferrer"
        className="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition-colors"
      >
        <Globe className="h-4 w-4" />
        <span className="text-sm">{url}</span>
        <ArrowRight className="h-4 w-4" />
      </a>
    </div>
  )
}
