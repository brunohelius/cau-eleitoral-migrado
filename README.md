# CAU Sistema Eleitoral

Sistema Eleitoral completo do Conselho de Arquitetura e Urbanismo.

## Stack Tecnologica

### Backend
- **.NET 10** - ASP.NET Core Web API
- **Entity Framework Core** - ORM
- **PostgreSQL** - Banco de dados
- **JWT** - Autenticacao
- **Serilog** - Logging
- **FluentValidation** - Validacao

### Frontend
- **React 18** - UI Framework
- **TypeScript** - Tipagem
- **Vite 5** - Build tool
- **Tailwind CSS** - Estilizacao
- **shadcn/ui** - Componentes
- **TanStack Query** - Data fetching
- **Zustand** - State management
- **React Hook Form + Zod** - Formularios

### Infrastructure
- **Docker** - Containerizacao
- **AWS ECS** - Orquestracao
- **AWS RDS** - Database
- **AWS S3** - Storage
- **GitHub Actions** - CI/CD

## Estrutura do Projeto

```
cau-eleitoral-migrado/
├── apps/
│   ├── api/                    # Backend .NET
│   │   ├── CAU.Eleitoral.Api/
│   │   ├── CAU.Eleitoral.Application/
│   │   ├── CAU.Eleitoral.Domain/
│   │   └── CAU.Eleitoral.Infrastructure/
│   ├── admin/                  # Frontend Admin
│   └── public/                 # Frontend Publico
├── packages/
│   ├── shared/                 # Componentes compartilhados
│   └── types/                  # Types TypeScript
├── infrastructure/
│   ├── docker/
│   └── terraform/
└── docs/
```

## Requisitos

- .NET SDK 10.0+
- Node.js 20+
- pnpm 10+
- Docker & Docker Compose
- PostgreSQL 16+

## Instalacao

### Backend

```bash
cd apps/api
dotnet restore
dotnet build
```

### Frontend

```bash
# Admin
cd apps/admin
pnpm install
pnpm dev

# Public
cd apps/public
pnpm install
pnpm dev
```

## Desenvolvimento

### Iniciar todos os servicos com Docker

```bash
docker-compose -f infrastructure/docker/docker-compose.yml up -d
```

### URLs de desenvolvimento

- **API**: http://localhost:5000
- **Admin**: http://localhost:4200
- **Public**: http://localhost:4201
- **Swagger**: http://localhost:5000

## Modulos

### Core Eleitoral
- Eleicoes
- Calendarios
- Configuracoes

### Chapas e Membros
- Chapas
- Membros
- Documentos
- Plataformas

### Denuncias
- Denuncias
- Defesas
- Admissibilidade
- Julgamentos

### Impugnacoes
- Impugnacoes
- Alegacoes
- Contra-alegacoes
- Recursos

### Julgamentos
- Comissoes
- Sessoes
- Votacoes
- Acordaos

### Usuarios
- Usuarios
- Profissionais
- Conselheiros
- Permissoes

## Contagem APF

| Componente | Quantidade | Pontos de Funcao |
|------------|------------|------------------|
| Entidades | 177 | 885 |
| Controllers | 67 | 335 |
| Services | 97 | 970 |
| Repositories | 167 | 501 |
| **TOTAL** | **508** | **2.958 PF** |

## Deploy

### AWS

```bash
# Build e push das imagens
docker-compose build
docker-compose push

# Deploy ECS
aws ecs update-service --cluster cau-eleitoral-cluster --service cau-eleitoral-api --force-new-deployment
```

### URLs de Producao

- **Admin**: https://cau-eleitoral.migrai.com.br
- **Public**: https://public.cau-eleitoral.migrai.com.br
- **API**: https://api.cau-eleitoral.migrai.com.br

## Licenca

Proprietary - CAU
