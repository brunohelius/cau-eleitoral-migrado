# CAU Sistema Eleitoral - Migrado

## Visão Geral
Sistema eleitoral migrado de PHP/Java para .NET 8 + React 18 + shadcn/ui.

**Status:** Em Produção (AWS ECS Fargate) ✅ Verificado em 2026-02-05

## Estrutura do Projeto

```
cau-eleitoral-migrado/
├── apps/
│   ├── api/                    # .NET 8 Web API (Clean Architecture)
│   │   ├── CAU.Eleitoral.Api/        # Controllers, Program.cs
│   │   ├── CAU.Eleitoral.Application/ # Services, DTOs, Interfaces
│   │   ├── CAU.Eleitoral.Domain/     # Entities (~71), Enums, Interfaces
│   │   └── CAU.Eleitoral.Infrastructure/ # DbContext, Repositories, Seeder
│   ├── admin/                  # React Admin (Vite + shadcn/ui)
│   └── public/                 # React Public (Vite + shadcn/ui)
├── infrastructure/
│   ├── docker/                 # Dockerfiles
│   ├── terraform/              # AWS Infrastructure
│   └── scripts/                # Deploy scripts
└── docs/                       # Documentação + Sistema legado
```

## Stack Tecnológica

### Backend
- **.NET 8** + ASP.NET Core Web API
- **Entity Framework Core 8.0** + PostgreSQL
- **JWT Authentication** (PBKDF2 100000 iterations)
- **Serilog** para logging
- **Swashbuckle** para Swagger/OpenAPI

### Frontend
- **React 18** + TypeScript
- **Vite 5** para build
- **shadcn/ui** + Tailwind CSS
- **React Router v6**
- **TanStack Query** para state management

### Infrastructure
- **AWS ECS Fargate** (API, Admin, Public)
- **AWS RDS PostgreSQL**
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

### Iniciar Docker (PostgreSQL + Redis)
```bash
cd /Users/brunosouza/Development/cau-eleitoral-migrado
docker compose -f infrastructure/docker/docker-compose.yml up -d
```

### API (.NET)
```bash
cd apps/api/CAU.Eleitoral.Api
dotnet run
# API disponível em http://localhost:5001
# Swagger em http://localhost:5001/swagger
```

### Admin (React)
```bash
cd apps/admin
pnpm install
pnpm dev
# Disponível em http://localhost:4200
```

### Public (React)
```bash
cd apps/public
pnpm install
pnpm dev
# Disponível em http://localhost:4201
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
# Iniciar build
aws codebuild start-build --project-name cau-eleitoral-build --region us-east-1

# Verificar status do build
aws codebuild list-builds-for-project --project-name cau-eleitoral-build --region us-east-1
aws codebuild batch-get-builds --ids <build-id> --region us-east-1
```

#### Verificar Status do Deploy
```bash
# Status dos serviços ECS
aws ecs describe-services \
  --cluster cau-eleitoral-cluster \
  --services cau-eleitoral-api cau-eleitoral-admin cau-eleitoral-public \
  --query 'services[*].{Name:serviceName,Status:status,Running:runningCount,Desired:desiredCount}' \
  --output table

# Logs do serviço
aws logs tail /aws/ecs/cau-eleitoral/api --follow
```

### Terraform (Infraestrutura)
```bash
cd infrastructure/terraform

# Inicializar
terraform init

# Planejar
terraform plan -out=tfplan

# Aplicar
terraform apply tfplan
```

### Deploy Script Local (Apenas para emergências)
```bash
./infrastructure/scripts/deploy.sh [tag] [service]

# Exemplos:
./infrastructure/scripts/deploy.sh latest all      # Deploy de todos os serviços
./infrastructure/scripts/deploy.sh latest api      # Deploy apenas da API
./infrastructure/scripts/deploy.sh v1.0.0 admin    # Deploy do admin com tag específica
```

**Nota:** O deploy local requer Docker com suporte a buildx para linux/amd64.

## Entidades Principais (Domain)

### Core
- Eleicao, Calendario, ConfiguracaoEleicao
- ChapaEleicao, MembroChapa
- Voto, ResultadoEleicao

### Usuários
- Usuario (TipoUsuario: Admin, ComissaoEleitoral, Conselheiro, Profissional, Candidato, Eleitor)
- Profissional, Conselheiro
- RegionalCAU, Filial

### Processos
- Denuncia, ImpugnacaoResultado
- ComissaoJulgadora, SessaoJulgamento
- Documento, Edital, Resolucao

## Database

### PostgreSQL Local
- Host: localhost
- Port: 5436
- Database: cau_eleitoral
- User: postgres
- Password: postgres

### Migrations
```bash
cd apps/api
dotnet ef migrations add NomeMigration -p CAU.Eleitoral.Infrastructure -s CAU.Eleitoral.Api
dotnet ef database update -p CAU.Eleitoral.Infrastructure -s CAU.Eleitoral.Api
```

## Problemas Conhecidos / Fixes

### .NET 8 Specific
1. **Swashbuckle**: Usar versão 6.5.0 para compatibilidade
2. **Docker Build**: Usar `--platform linux/amd64` para Fargate

### TypeScript/Vite
- Adicionar `"types": ["vite/client"]` no tsconfig.json para `import.meta.env`

### EF Core Foreign Key Warning
- AssinaturaDigital.CertificadoDigitalId shadow property - ignorar warning

## Testes E2E com Playwright

### Executar Testes (Local)
```bash
# Admin App (12 testes)
cd apps/admin
pnpm exec playwright test

# Public App (9 testes)
cd apps/public
pnpm exec playwright test
```

### Executar Testes contra Produção
```bash
cd apps/admin
npx playwright test --config=playwright.production.config.ts
```

### Fluxos Testados
**Admin (12 testes):**
- Login/Logout
- Dashboard com estatísticas
- Navegação: Eleições, Chapas, Denúncias, Usuários
- Sidebar navigation

**Public (9 testes):**
- Home page
- Login do Eleitor (CPF + RegistroCAU)
- Páginas públicas: Eleições, Calendário, Documentos, FAQ
- Portal do Candidato

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
