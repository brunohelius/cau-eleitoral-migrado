# CAU Eleitoral Migrado - Memória do Projeto

## Visão Geral do Projeto

Migração completa do sistema CAU Eleitoral de PHP/Java para .NET 8 + React 18.

## Stack Tecnológico

- **Backend**: .NET 8
- **Frontend**: React 18 + TypeScript + Vite
- **UI Components**: shadcn/ui + Radix UI
- **Estado**: React Query + Context API

## Estrutura de Diretórios

```
apps/
├── admin/           # Aplicação admin (React)
│   └── src/
│       ├── components/ui/    # Componentes shadcn
│       ├── pages/             # Páginas do admin
│       ├── lib/               # Utilitários (api.ts)
│       └── App.tsx            # Router principal
└── api/             # Backend .NET 8
```

## Componentes shadcn Instalados

- alert-dialog
- badge
- button
- card
- dialog
- input
- label
- select
- separator
- table
- tabs
- textarea
- toast
- toaster
- dropdown-menu

## Páginas do Admin

1. Dashboard (`/`) - Visão geral
2. Comissões (`/comissoes`) - Gestão de comissões
3. Diploma Eleitoral (`/diploma`) - Diplomas
4. Pedidos (`/pedidos`) - Pedidos diversos
5. Termo de Posse (`/termoposse`) - Termos de posse
6. Denúncias (`/denuncias`) - Sistema de denúncias
7. Configurações (`/configuracoes`) - Configurações

## Problemas Conhecidos e Soluções

### Build Errors (RESOLVIDO)

**Problema**: Erros TS2614 (módulos sem default export) e TS2307 (módulo não encontrado)

**Solução**:
1. Alterar imports em App.tsx de named para default imports
2. Criar api.ts standalone em /lib sem depender de @/services/api
3. Remover DropdownMenu de páginas (substituído por botões simples)

### API Configuration

O arquivo `/lib/api.ts` configura:
- Base URL via VITE_API_URL ou localhost:5000
- Interceptor de autenticação (Bearer token)
- Redirect para login em 401

## Comandos Úteis

```bash
# Build produção
npm run build

# Desenvolvimento
npm run dev -- --host --port 30082

# Instalar dependência
npm install <package> --legacy-peer-deps
```

## Status

- **Backend .NET 8**: Funcional (Controllers, Services, Models)
- **Frontend React**: Build OK, servidor rodando em http://localhost:30083
- **Migração**: ~90% concluída

## Contato

Repositório: brunohelius/cau-eleitoral-migrado
Branch: feature/add-remaining-controllers-frontend
