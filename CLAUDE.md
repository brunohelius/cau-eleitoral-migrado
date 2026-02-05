# CAU Sistema Eleitoral - Migrado

## Visão Geral
Sistema eleitoral migrado de PHP/Java para .NET 10 + React 18 + shadcn/ui.

**Status:** MVP Funcional - Deploy AWS em andamento

## Estrutura do Projeto

```
cau-eleitoral-migrado/
├── apps/
│   ├── api/                    # .NET 10 Web API (Clean Architecture)
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
- **.NET 10** + ASP.NET Core Web API
- **Entity Framework Core 10.0.2** + PostgreSQL
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
VITE_API_URL=https://api.cau-eleitoral.migrai.com.br/api
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

### URLs de Produção
- Admin: https://cau-admin.migrai.com.br
- Public: https://cau-public.migrai.com.br
- API: https://cau-api.migrai.com.br

### Terraform
```bash
cd infrastructure/terraform

# Inicializar
terraform init

# Planejar
terraform plan -out=tfplan

# Aplicar
terraform apply tfplan
```

### Deploy Script
```bash
./infrastructure/scripts/deploy.sh
```

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

### .NET 10 Specific
1. **Swashbuckle**: Usar versão 6.5.0 (não 7.x) para compatibilidade com OpenApi 1.x
2. **Npgsql**: Usar versão 10.0.0 (não 10.0.2)

### TypeScript/Vite
- Adicionar `"types": ["vite/client"]` no tsconfig.json para `import.meta.env`

### EF Core Foreign Key Warning
- AssinaturaDigital.CertificadoDigitalId shadow property - ignorar warning

## Fluxos Testados com Playwright

1. **Admin Login** - Dashboard com estatísticas ✅
2. **Eleitor Login** - CPF + RegistroCAU + Senha ✅
3. **Votação Completa** - Cédula → Confirmação → Comprovante ✅
4. **Candidato Login** - Visualização da chapa ✅

## Contato
- Suporte: suporte@cau.org.br
- Deploy Domain: cau-eleitoral.migrai.com.br
