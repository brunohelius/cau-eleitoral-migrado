# CAU Sistema Eleitoral - Migrado

## Visão Geral
Sistema eleitoral migrado de PHP/Java para .NET 8 + React 18 + shadcn/ui.

**Status da Migração:** 98% Completo ✅ (verificado em 2026-02-24)
**Banco de Dados Local:** SQLite (`caueve.db`) — permanente
**Banco de Dados Produção:** PostgreSQL (AWS RDS)

## Status dos Módulos (E2E Verificado)

| Módulo | Status | Observações |
|--------|--------|-------------|
| Dashboard | ✅ | Eleições em andamento, cards de resumo |
| Eleições | ✅ | CRUD completo (create/edit/delete) |
| Votação | ✅ | 140 eleitores, 142 votos no seed |
| Chapas | ✅ | CRUD + regra "já analisada não pode ser alterada" |
| Denúncias | ✅ | LIST + Detail + Arquivar com motivo |
| Impugnações | ✅ | 4 protocolos, filtros por status/tipo |
| Julgamentos | ✅ | Denúncias e Impugnações juntos |
| Usuários | ⚠️ | List OK, Create tem bug no enum `tipo` (string vs number) |
| Relatórios | ✅ | 4 categorias, Exportar Todos |
| Auditoria | ✅ | Filtros por nível, entidade, data |
| Configurações | ✅ | 5 abas: Geral, Eleições, Notificações, Segurança, Logs |
| Auth Admin | ✅ | Login, /me, refresh token |
| Auth Eleitor | ✅ | Verificação CPF + login |
| Auth Candidato | ✅ | Login CPF+CAU+Senha |
| Health Check | ✅ | /health retorna "Healthy" |

### Bug Conhecido
**Usuários → Novo Usuário**: Erro de validação Zod: `"Invalid enum value. Expected 0|1|2|3|4|5, received '0'"`. O frontend envia `tipo` como string. Arquivo: `apps/admin/src/pages/usuarios/UsuarioFormPage.tsx`.

## Estrutura do Projeto

```
cau-eleitoral-migrado/
├── apps/
│   ├── api/                    # .NET 8 Web API (Clean Architecture)
│   │   ├── CAU.Eleitoral.Api/        # Controllers (21), Program.cs
│   │   ├── CAU.Eleitoral.Application/ # Services, DTOs, Interfaces
│   │   ├── CAU.Eleitoral.Domain/     # Entities (~156), Enums, Interfaces
│   │   ├── CAU.Eleitoral.Infrastructure/ # DbContext, Repositories, Seeder
│   │   └── CAU.Eleitoral.Tests/      # Testes .NET
│   ├── admin/                  # React Admin (Vite + shadcn/ui)
│   └── public/                 # React Public (Vite + shadcn/ui)
├── infrastructure/
│   ├── docker/                 # Dockerfiles
│   ├── terraform/              # AWS Infrastructure
│   └── scripts/                # Deploy scripts
├── docs/
│   └── documentacao-qa.md      # ~100 cenários de teste
└── tmp/
    └── test_crud.sh            # Script de teste CRUD via curl
```

## Stack Tecnológica

### Backend
- **.NET 8** + ASP.NET Core Web API
- **Entity Framework Core 8.0** + **SQLite** (local) / **PostgreSQL** (produção)
- **JWT Authentication** (PBKDF2 100000 iterations)
- **Serilog** para logging
- **Swashbuckle** para Swagger/OpenAPI

### Frontend
- **React 18** + TypeScript
- **Vite 5** para build
- **shadcn/ui** + Tailwind CSS
- **React Router v6**
- **TanStack Query** para state management
- **Vitest** para testes unitários
- **Playwright** para testes E2E

### Infrastructure
- **AWS ECS Fargate** (API, Admin, Public)
- **AWS RDS PostgreSQL** (produção)
- **AWS CloudFront** + ALB
- **AWS S3** (documents, uploads, backups)
- **Terraform** para IaC

## Variáveis de Ambiente

### API (.NET) - appsettings.json
```json
{
  "ConnectionStrings": {
    "DefaultConnection": "Host=localhost;Port=5436;Database=cau_eleitoral;Username=postgres;Password=postgres"
  },
  "Jwt": {
    "Key": "YourSuperSecretKeyHereThatIsAtLeast256BitsLong123456",
    "Issuer": "CAU.Eleitoral",
    "Audience": "CAU.Eleitoral.Client",
    "ExpirationInMinutes": 60,
    "RefreshTokenExpirationInDays": 7
  },
  "AWS": {
    "Region": "us-east-1",
    "S3": {
      "BucketDocuments": "cau-eleitoral-documents",
      "BucketUploads": "cau-eleitoral-uploads"
    }
  },
  "Email": {
    "SmtpHost": "smtp.example.com",
    "SmtpPort": 587,
    "FromEmail": "noreply@cau.org.br",
    "FromName": "CAU Sistema Eleitoral"
  }
}
```

**Nota:** Localmente a API usa SQLite (`caueve.db`) configurado diretamente no `Program.cs`. A `ConnectionStrings` acima é usada apenas em produção com PostgreSQL.

### Admin App (.env)
```env
VITE_API_URL=http://localhost:5001/api
VITE_APP_NAME=CAU Sistema Eleitoral Admin
VITE_APP_ENV=development
```

### Public App (.env)
```env
VITE_API_URL=http://localhost:5001/api
VITE_APP_NAME=CAU Sistema Eleitoral
VITE_APP_ENV=development
```

### Produção (.env.production)
```env
VITE_API_URL=https://cau-api.migrai.com.br/api
VITE_APP_ENV=production
```

## Comandos de Desenvolvimento

### Iniciar Tudo (Monorepo com Turborepo)
```bash
cd /Users/brunosouza/Development/cau-eleitoral-migrado
pnpm dev
# Admin em http://localhost:4200
# Public em http://localhost:4201
```

### API (.NET) com SQLite
```bash
ASPNETCORE_ENVIRONMENT=Development dotnet run --project apps/api/CAU.Eleitoral.Api --urls "http://0.0.0.0:5001"
# API em http://localhost:5001
# Swagger em http://localhost:5001/swagger
# Database: caueve.db (SQLite, criado automaticamente)
# Seed automático na primeira execução
```

### Rodar Testes Unitários (Vitest)
```bash
# Admin (56 testes em 7 arquivos)
cd apps/admin && pnpm test

# Public (29 testes em 2 arquivos)
cd apps/public && pnpm test

# Todos via monorepo
pnpm test
```

### Rodar Testes CRUD via API (curl)
```bash
bash tmp/test_crud.sh
# Testa 32 endpoints: Auth, Eleições, Chapas, Denúncias, Usuários, etc.
```

### Docker (apenas para produção/staging)
```bash
docker compose -f infrastructure/docker/docker-compose.yml up -d           # Postgres + Redis
docker compose -f infrastructure/docker/docker-compose.yml --profile app up -d  # Tudo
```

## Credenciais de Teste (Database Seeder)

### Admin
- Email: admin@cau.org.br
- Senha: Admin@123

### Eleitor (by CPF/RegistroCAU)
- CPF: 60000000003
- RegistroCAU: A000005-SP
- Senha: Eleitor@123

### Candidato
- CPF: 45555555551
- RegistroCAU: A000018-DF
- Senha: Candidato@123

## Testes

### Testes Unitários (Vitest) — 85 testes, todos passando ✅

**Admin (56 testes):**
- `services/__tests__/auth.test.ts` — login, me, refresh, logout
- `services/__tests__/eleicoes.test.ts` — CRUD, validations
- `services/__tests__/chapas.test.ts` — list, create, members
- `services/__tests__/denuncias.test.ts` — list, detail, status change
- `services/__tests__/impugnacoes.test.ts` — list, create, analyze
- `services/__tests__/votacao.test.ts` — elections, stats, monitoring
- `stores/__tests__/auth.test.ts` — Zustand auth store

**Public (29 testes):**
- `services/__tests__/auth.test.ts` — eleitor/candidato login
- `services/__tests__/votacao.test.ts` — voting flow, eligibility

### Testes E2E com Playwright
```bash
cd apps/admin && pnpm exec playwright test   # 12 testes
cd apps/public && pnpm exec playwright test  # 9 testes
```

### Testes de Integração CRUD (32 endpoints testados)
```bash
bash tmp/test_crud.sh
# 22/32 passam (10 falhas por rotas de script desatualizadas)
```

## AWS Deployment

### AWS Account Info
- **Account ID:** 801232946361
- **Region:** us-east-1
- **Profile:** default

### URLs de Produção
- Admin: https://cau-admin.migrai.com.br
- Public: https://cau-public.migrai.com.br
- API: https://cau-api.migrai.com.br

### CloudFront Distributions
| Service | CloudFront ID | Domain |
|---------|--------------|--------|
| Admin | d39vg8qyop1yti | cau-admin.migrai.com.br |
| Public | d3nfqhdxqrdzp5 | cau-public.migrai.com.br |
| API | d3izzjw5tijtoz | cau-api.migrai.com.br |

### ECS Resources
- **Cluster:** cau-eleitoral-cluster
- **Services:** cau-eleitoral-api, cau-eleitoral-admin, cau-eleitoral-public
- **ECR Repos:** cau-eleitoral-api, cau-eleitoral-admin, cau-eleitoral-public

### RDS Database (Production)
- **Host:** cau-eleitoral-db.c5caeiwsk43h.us-east-1.rds.amazonaws.com
- **Port:** 5432
- **Database:** cau_eleitoral
- **Username:** postgres
- **Note:** Password stored in AWS Secrets Manager

## Cloudflare DNS

### Zone Info
- **Domain:** migrai.com.br
- **Zone ID:** b51c069304dc586a4f8c96cc6efe40cc
- **API Token Location:** `/Users/brunosouza/Development/migrai-agentic-coder/.env` (CLOUDFLARE_API_TOKEN)

### CNAME Records (Proxy OFF for CloudFront SSL)
| Record | Target |
|--------|--------|
| cau-admin | d39vg8qyop1yti.cloudfront.net |
| cau-public | d3nfqhdxqrdzp5.cloudfront.net |
| cau-api | d3izzjw5tijtoz.cloudfront.net |

### Deploy com AWS CodeBuild (Recomendado)

O deploy é feito automaticamente via AWS CodeBuild quando há push na branch `main`.

#### Deploy Manual via Console AWS
1. Acesse AWS Console > CodeBuild > Projects
2. Selecione `cau-eleitoral-build`
3. Clique em "Start build"
4. Aguarde a conclusão (aproximadamente 5-10 minutos)

#### Deploy Manual via CLI
```bash
aws codebuild start-build --project-name cau-eleitoral-build --region us-east-1
aws codebuild list-builds-for-project --project-name cau-eleitoral-build --region us-east-1
aws codebuild batch-get-builds --ids <build-id> --region us-east-1
```

#### Verificar Status do Deploy
```bash
aws ecs describe-services \
  --cluster cau-eleitoral-cluster \
  --services cau-eleitoral-api cau-eleitoral-admin cau-eleitoral-public \
  --query 'services[*].{Name:serviceName,Status:status,Running:runningCount,Desired:desiredCount}' \
  --output table

aws logs tail /aws/ecs/cau-eleitoral/api --follow
```

## Database

### SQLite Local (Desenvolvimento)
- **Arquivo:** `caueve.db` (criado em `AppContext.BaseDirectory`)
- **Configurado em:** `Program.cs` via `UseSqlite`
- **Migrations:** Geradas para SQLite
- **Seed:** Automático na primeira execução (DatabaseSeeder.cs)

### PostgreSQL (Produção)
- **Host:** cau-eleitoral-db.c5caeiwsk43h.us-east-1.rds.amazonaws.com
- **Port:** 5432
- **Database:** cau_eleitoral

### Migrations
```bash
cd apps/api
dotnet ef migrations add NomeMigration -p CAU.Eleitoral.Infrastructure -s CAU.Eleitoral.Api
dotnet ef database update -p CAU.Eleitoral.Infrastructure -s CAU.Eleitoral.Api
```

## Entidades Principais (156 entities, 21 controllers)

### Core Eleitoral
- Eleicao, Calendario, ConfiguracaoEleicao
- ChapaEleicao, MembroChapa
- Voto, ResultadoEleicao

### Usuários
- Usuario (TipoUsuario: Admin, ComissaoEleitoral, Conselheiro, Profissional, Candidato, Eleitor)
- Profissional, Conselheiro
- RegionalCAU, Filial

### Processos Jurídicos
- Denuncia, ImpugnacaoResultado
- ComissaoJulgadora, SessaoJulgamento
- Documento, Edital, Resolucao

## GAPs Remanescentes (2%)

| Item | Impacto | Esforço Estimado |
|------|---------|-----------------|
| S3 Document Storage | Docs perdem-se ao reiniciar container | 4-6h |
| PDF/XLSX Export | Só CSV disponível | 6-8h |
| Email Notifications | NotificacaoService não envia emails | 4-6h |
| Digital Signatures | Entidade existe, sem lógica | 12-16h |

## Problemas Conhecidos / Fixes

### .NET 8 Specific
1. **Swashbuckle**: Usar versão 6.5.0 para compatibilidade
2. **Docker Build**: Usar `--platform linux/amd64` para Fargate

### TypeScript/Vite
- Adicionar `"types": ["vite/client"]` no tsconfig.json para `import.meta.env`

### EF Core
- AssinaturaDigital.CertificadoDigitalId shadow property - ignorar warning
- AuditoriaLogs seed pode falhar no SQLite (funciona em PostgreSQL)

### Frontend
- Novo Usuário: `tipo` deve ser number, não string (bug Zod schema)
- Browser subagent do playwright não suporta caractere `ç` ao digitar

## Monitoramento e Health Checks

### Verificar API
```bash
curl https://cau-api.migrai.com.br/health
# Expected: "Healthy"
```

### Verificar Login
```bash
curl -X POST https://cau-api.migrai.com.br/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@cau.org.br","password":"Admin@123"}'
```

### Seed Database (se necessário)
```bash
curl -X POST https://cau-api.migrai.com.br/api/admin/seed \
  -H "X-Seed-Key: CAU-SEED-2026-SECRET"
```

## Contato
- Deploy Domain: migrai.com.br
- Admin URL: https://cau-admin.migrai.com.br
