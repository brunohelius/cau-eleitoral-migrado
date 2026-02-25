# Matriz de Rastreabilidade QA (Requisitos x Testes)

- Data de geracao: 2026-02-25 20:10:56
- Fonte de requisitos: `docs/documentacao-qa.md`
- Testes detectados no snapshot: 168

## 1. Resumo Geral

| Indicador | Valor |
|---|---:|
| Total de casos CT-* | 138 |
| Coberto automatizado | 78 (56.5%) |
| Cobertura parcial | 60 (43.5%) |
| Sem cobertura automatizada | 0 (0.0%) |

## 2. Execucao das Suites

| Suite | Status | Duracao (s) | Exit |
|---|---|---:|---:|
| `api-unit` | OK | 2.12 | 0 |
| `admin-unit` | OK | 1.44 | 0 |
| `public-unit` | OK | 0.92 | 0 |
| `admin-build` | OK | 6.11 | 0 |
| `public-build` | OK | 4.44 | 0 |
| `admin-e2e` | OK | 13.82 | 0 |
| `public-e2e` | OK | 4.16 | 0 |

## 3. Cobertura por Dominio

| Dominio | CTs | Coberto | Parcial | Sem cobertura | Evidencias (tests) |
|---|---:|---:|---:|---:|---:|
| `AUD` | 4 | 0 | 4 | 0 | 7 |
| `AUTH` | 8 | 7 | 1 | 0 | 33 |
| `CFG` | 4 | 0 | 4 | 0 | 6 |
| `CHA` | 14 | 11 | 3 | 0 | 35 |
| `DASH` | 3 | 1 | 2 | 0 | 21 |
| `DEN` | 12 | 10 | 2 | 0 | 12 |
| `ELE` | 14 | 11 | 3 | 0 | 41 |
| `IMP` | 4 | 2 | 2 | 0 | 4 |
| `JUL` | 4 | 3 | 1 | 0 | 7 |
| `PUB-AUTH` | 5 | 5 | 0 | 0 | 20 |
| `PUB-CAND` | 11 | 4 | 7 | 0 | 23 |
| `PUB-PAG` | 10 | 0 | 10 | 0 | 40 |
| `PUB-VOT` | 12 | 8 | 4 | 0 | 27 |
| `REL` | 3 | 2 | 1 | 0 | 16 |
| `RESP` | 5 | 5 | 0 | 0 | 5 |
| `SEC` | 10 | 1 | 9 | 0 | 78 |
| `USR` | 5 | 2 | 3 | 0 | 6 |
| `VOT` | 10 | 6 | 4 | 0 | 22 |

## 4. Lacunas Prioritarias

### Cobertura parcial
- `CT-AUTH-008` (Módulo: Autenticação Admin): Renovação automática de token (Refresh Token)
- `CT-DASH-002` (Módulo: Dashboard): Estatísticas em tempo real
- `CT-DASH-003` (Módulo: Dashboard): Navegação rápida via Dashboard
- `CT-ELE-001` (Módulo: Eleições): Listagem de eleições
- `CT-ELE-004` (Módulo: Eleições): Editar eleição
- `CT-ELE-012` (Módulo: Eleições): Verificar editabilidade (CanEdit)
- `CT-CHA-004` (Módulo: Chapas): Editar chapa
- `CT-CHA-008` (Módulo: Chapas): Submeter chapa para análise
- `CT-CHA-009` (Módulo: Chapas): Iniciar análise da chapa
- `CT-VOT-004` (Módulo: Votação (Admin)): Fechar votação
- `CT-VOT-006` (Módulo: Votação (Admin)): Listar eleitores que votaram
- `CT-VOT-007` (Módulo: Votação (Admin)): Anular voto (Admin)
- `CT-VOT-009` (Módulo: Votação (Admin)): Publicar resultados
- `CT-DEN-004` (Módulo: Denúncias): Visualizar detalhes da denúncia
- `CT-DEN-006` (Módulo: Denúncias): Concluir análise
- `CT-IMP-001` (Módulo: Impugnações): Listagem de impugnações
- `CT-IMP-003` (Módulo: Impugnações): Visualizar detalhes
- `CT-JUL-001` (Módulo: Julgamentos): Listagem de julgamentos
- `CT-USR-001` (Módulo: Usuários): Listagem de usuários
- `CT-USR-003` (Módulo: Usuários): Editar usuário
- `CT-USR-004` (Módulo: Usuários): Visualizar detalhes do usuário
- `CT-REL-001` (Módulo: Relatórios): Página de relatórios
- `CT-AUD-001` (Módulo: Auditoria): Visualizar logs de auditoria
- `CT-AUD-002` (Módulo: Auditoria): Filtrar por período
- `CT-AUD-003` (Módulo: Auditoria): Filtrar por usuário
- `CT-AUD-004` (Módulo: Auditoria): Filtrar por tipo de ação
- `CT-CFG-001` (Módulo: Configurações): Visualizar configurações da eleição
- `CT-CFG-002` (Módulo: Configurações): Alterar configurações de votação
- `CT-CFG-003` (Módulo: Configurações): Configurações de email/notificação
- `CT-CFG-004` (Módulo: Configurações): Configurações de segurança
- `CT-PUB-VOT-001` (Portal Público - Votação Eleitor): Listar eleições disponíveis
- `CT-PUB-VOT-006` (Portal Público - Votação Eleitor): Impedir voto duplicado
- `CT-PUB-VOT-009` (Portal Público - Votação Eleitor): Histórico de votos
- `CT-PUB-VOT-010` (Portal Público - Votação Eleitor): Justificar ausência
- `CT-PUB-CAND-003` (Portal Público - Área do Candidato): Visualizar chapa
- `CT-PUB-CAND-004` (Portal Público - Área do Candidato): Gerenciar documentos
- `CT-PUB-CAND-005` (Portal Público - Área do Candidato): Editar plataforma
- `CT-PUB-CAND-006` (Portal Público - Área do Candidato): Visualizar denúncias
- `CT-PUB-CAND-007` (Portal Público - Área do Candidato): Apresentar defesa
- `CT-PUB-CAND-008` (Portal Público - Área do Candidato): Gerenciar recursos
- `CT-PUB-CAND-009` (Portal Público - Área do Candidato): Visualizar histórico
- `CT-PUB-PAG-001` (Portal Público - Páginas Públicas): Home page
- `CT-PUB-PAG-002` (Portal Público - Páginas Públicas): Lista de eleições públicas
- `CT-PUB-PAG-003` (Portal Público - Páginas Públicas): Detalhes da eleição pública
- `CT-PUB-PAG-004` (Portal Público - Páginas Públicas): Chapas da eleição
- `CT-PUB-PAG-005` (Portal Público - Páginas Públicas): Resultados da eleição
- `CT-PUB-PAG-006` (Portal Público - Páginas Públicas): Calendário eleitoral
- `CT-PUB-PAG-007` (Portal Público - Páginas Públicas): Documentos públicos
- `CT-PUB-PAG-008` (Portal Público - Páginas Públicas): FAQ
- `CT-PUB-PAG-009` (Portal Público - Páginas Públicas): Registrar denúncia pública
- `CT-PUB-PAG-010` (Portal Público - Páginas Públicas): Consultar denúncia por protocolo
- `CT-SEC-001` (Testes de Segurança): Acesso não autenticado às rotas admin
- `CT-SEC-003` (Testes de Segurança): CORS - Origens não autorizadas
- `CT-SEC-004` (Testes de Segurança): Injeção SQL nos campos de busca
- `CT-SEC-005` (Testes de Segurança): XSS nos campos de texto
- `CT-SEC-006` (Testes de Segurança): Voto duplicado via API direta
- `CT-SEC-007` (Testes de Segurança): Acesso a dados de outros usuários
- `CT-SEC-008` (Testes de Segurança): Rate limiting nas rotas de autenticação
- `CT-SEC-009` (Testes de Segurança): Força da senha
- `CT-SEC-010` (Testes de Segurança): Sigilo do voto

## 5. Matriz Detalhada

| Caso | Modulo | Dominio | Status | Evidencia direta |
|---|---|---|---|---|
| `CT-AUTH-001` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve fazer login com credenciais válidas (score=6) |
| `CT-AUTH-002` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve fazer login com credenciais válidas (score=4) |
| `CT-AUTH-003` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/e2e/auth.spec.ts` :: should display login page (score=3) |
| `CT-AUTH-004` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/e2e/auth.spec.ts` :: should display login page (score=3) |
| `CT-AUTH-005` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha (score=3) |
| `CT-AUTH-006` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha (score=3) |
| `CT-AUTH-007` | Módulo: Autenticação Admin | `AUTH` | Coberto automatizado | `apps/admin/e2e/auth.spec.ts` :: should display login page (score=5) |
| `CT-AUTH-008` | Módulo: Autenticação Admin | `AUTH` | Cobertura parcial | `apps/admin/src/services/__tests__/auth.test.ts` :: deve redefinir senha com token válido (score=2) |
| `CT-DASH-001` | Módulo: Dashboard | `DASH` | Coberto automatizado | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to elections page (score=4) |
| `CT-DASH-002` | Módulo: Dashboard | `DASH` | Cobertura parcial | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve retornar estatísticas de chapas por eleição (score=1) |
| `CT-DASH-003` | Módulo: Dashboard | `DASH` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should display dashboard statistics (score=2) |
| `CT-ELE-001` | Módulo: Eleições | `ELE` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to elections page (score=2) |
| `CT-ELE-002` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve criar uma nova eleição (score=5) |
| `CT-ELE-003` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve retornar estatísticas de chapas por eleição (score=5) |
| `CT-ELE-004` | Módulo: Eleições | `ELE` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to elections page (score=2) |
| `CT-ELE-005` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve retornar estatísticas de votação de uma eleição (score=4) |
| `CT-ELE-006` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve iniciar uma eleição (score=4) |
| `CT-ELE-007` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve encerrar uma eleição (score=4) |
| `CT-ELE-008` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve suspender uma eleição com motivo (score=5) |
| `CT-ELE-009` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve cancelar uma eleição com motivo (score=5) |
| `CT-ELE-010` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve filtrar chapas por eleição (score=4) |
| `CT-ELE-011` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/src/services/__tests__/eleicoes.test.ts` :: deve excluir uma eleição (score=4) |
| `CT-ELE-012` | Módulo: Eleições | `ELE` | Cobertura parcial | `apps/admin/e2e/full-system.spec.ts` :: should fetch slates via API (score=3) |
| `CT-ELE-013` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/admin/e2e/modules-pages.spec.ts` :: should load Eleicao calendario page for first eleicao (score=4) |
| `CT-ELE-014` | Módulo: Eleições | `ELE` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve verificar elegibilidade com eleicaoId (score=3) |
| `CT-CHA-001` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve filtrar chapas por eleição (score=6) |
| `CT-CHA-002` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve criar uma nova chapa (score=5) |
| `CT-CHA-003` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve listar documentos da chapa (score=4) |
| `CT-CHA-004` | Módulo: Chapas | `CHA` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to slates page (score=2) |
| `CT-CHA-005` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve adicionar membro à chapa (score=4) |
| `CT-CHA-006` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve retornar detalhes da chapa com membros (score=3) |
| `CT-CHA-007` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve remover membro da chapa (score=4) |
| `CT-CHA-008` | Módulo: Chapas | `CHA` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to slates page (score=2) |
| `CT-CHA-009` | Módulo: Chapas | `CHA` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to slates page (score=2) |
| `CT-CHA-010` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve adicionar membro à chapa (score=3) |
| `CT-CHA-011` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve reprovar uma chapa com motivo (score=3) |
| `CT-CHA-012` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve listar documentos da chapa (score=4) |
| `CT-CHA-013` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve excluir uma chapa (score=4) |
| `CT-CHA-014` | Módulo: Chapas | `CHA` | Coberto automatizado | `apps/admin/src/services/__tests__/chapas.test.ts` :: deve listar documentos da chapa (score=4) |
| `CT-VOT-001` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve listar eleições com status de votação (score=4) |
| `CT-VOT-002` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve listar eleições com status de votação (score=4) |
| `CT-VOT-003` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve abrir votação para uma eleição (score=3) |
| `CT-VOT-004` | Módulo: Votação (Admin) | `VOT` | Cobertura parcial | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve listar eleições com status de votação (score=2) |
| `CT-VOT-005` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve retornar estatísticas de votação de uma eleição (score=3) |
| `CT-VOT-006` | Módulo: Votação (Admin) | `VOT` | Cobertura parcial | `apps/admin/e2e/full-system.spec.ts`, `apps/admin/src/services/__tests__/votacao.test.ts` |
| `CT-VOT-007` | Módulo: Votação (Admin) | `VOT` | Cobertura parcial | `apps/admin/e2e/full-system.spec.ts` :: should display login page (score=1) |
| `CT-VOT-008` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve realizar apuração de votos (score=5) |
| `CT-VOT-009` | Módulo: Votação (Admin) | `VOT` | Cobertura parcial | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve publicar resultados da eleição (score=2) |
| `CT-VOT-010` | Módulo: Votação (Admin) | `VOT` | Coberto automatizado | `apps/admin/src/services/__tests__/votacao.test.ts` :: deve exportar resultados em PDF (score=3) |
| `CT-DEN-001` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-010 deve listar denuncias por eleicao (score=4) |
| `CT-DEN-002` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-002 deve criar nova denuncia (score=5) |
| `CT-DEN-003` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-003 deve consultar denuncia por protocolo (score=6) |
| `CT-DEN-004` | Módulo: Denúncias | `DEN` | Cobertura parcial | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-001 deve listar denuncias com filtros (score=2) |
| `CT-DEN-005` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-005 deve iniciar analise da denuncia (score=5) |
| `CT-DEN-006` | Módulo: Denúncias | `DEN` | Cobertura parcial | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-006 deve concluir analise com parecer (score=3) |
| `CT-DEN-007` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-007 deve aceitar admissibilidade (score=4) |
| `CT-DEN-008` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-008 deve rejeitar admissibilidade (score=4) |
| `CT-DEN-009` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-009 deve enviar para julgamento e registrar decisao (score=5) |
| `CT-DEN-010` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-010 deve listar denuncias por eleicao (score=4) |
| `CT-DEN-011` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-011 deve listar denuncias por chapa (score=4) |
| `CT-DEN-012` | Módulo: Denúncias | `DEN` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-012 deve listar minhas denuncias como denunciante (score=4) |
| `CT-IMP-001` | Módulo: Impugnações | `IMP` | Cobertura parcial | `apps/admin/src/services/__tests__/impugnacoes.test.ts` :: CT-IMP-001 deve listar impugnacoes (score=2) |
| `CT-IMP-002` | Módulo: Impugnações | `IMP` | Coberto automatizado | `apps/admin/src/services/__tests__/impugnacoes.test.ts` :: CT-IMP-002 deve criar nova impugnacao (score=5) |
| `CT-IMP-003` | Módulo: Impugnações | `IMP` | Cobertura parcial | `apps/admin/src/services/__tests__/impugnacoes.test.ts` :: CT-IMP-001 deve listar impugnacoes (score=2) |
| `CT-IMP-004` | Módulo: Impugnações | `IMP` | Coberto automatizado | `apps/admin/src/services/__tests__/impugnacoes.test.ts` :: CT-IMP-004 deve editar impugnacao (score=4) |
| `CT-JUL-001` | Módulo: Julgamentos | `JUL` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts` :: should load Julgamentos list and open a detail page (score=2) |
| `CT-JUL-002` | Módulo: Julgamentos | `JUL` | Coberto automatizado | `apps/admin/e2e/modules-pages.spec.ts` :: should render Sessao de Julgamento page (score=3) |
| `CT-JUL-003` | Módulo: Julgamentos | `JUL` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-009 deve enviar para julgamento e registrar decisao (score=5) |
| `CT-JUL-004` | Módulo: Julgamentos | `JUL` | Coberto automatizado | `apps/admin/src/services/__tests__/denuncias.test.ts` :: CT-DEN-009 deve enviar para julgamento e registrar decisao (score=5) |
| `CT-USR-001` | Módulo: Usuários | `USR` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to users page (score=2) |
| `CT-USR-002` | Módulo: Usuários | `USR` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve registrar novo usuário com sucesso (score=4) |
| `CT-USR-003` | Módulo: Usuários | `USR` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to users page (score=2) |
| `CT-USR-004` | Módulo: Usuários | `USR` | Cobertura parcial | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to users page (score=2) |
| `CT-USR-005` | Módulo: Usuários | `USR` | Coberto automatizado | `apps/admin/e2e/dashboard.spec.ts` :: should navigate to users page (score=3) |
| `CT-REL-001` | Módulo: Relatórios | `REL` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts` :: should load Relatorios pages and allow selecting an eleicao (score=2) |
| `CT-REL-002` | Módulo: Relatórios | `REL` | Coberto automatizado | `apps/admin/e2e/modules-pages.spec.ts` :: should load Relatorios pages and allow selecting an eleicao (score=4) |
| `CT-REL-003` | Módulo: Relatórios | `REL` | Coberto automatizado | `apps/admin/e2e/modules-pages.spec.ts` :: should load Relatorios pages and allow selecting an eleicao (score=4) |
| `CT-AUD-001` | Módulo: Auditoria | `AUD` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts` :: should load Auditoria page (score=2) |
| `CT-AUD-002` | Módulo: Auditoria | `AUD` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts`, `apps/api/CAU.Eleitoral.Tests/EmailServiceTests.cs` |
| `CT-AUD-003` | Módulo: Auditoria | `AUD` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts`, `apps/api/CAU.Eleitoral.Tests/EmailServiceTests.cs` |
| `CT-AUD-004` | Módulo: Auditoria | `AUD` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts`, `apps/api/CAU.Eleitoral.Tests/EmailServiceTests.cs` |
| `CT-CFG-001` | Módulo: Configurações | `CFG` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts` :: should load Relatorios pages and allow selecting an eleicao (score=2) |
| `CT-CFG-002` | Módulo: Configurações | `CFG` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts`, `apps/api/CAU.Eleitoral.Tests/EmailServiceTests.cs` |
| `CT-CFG-003` | Módulo: Configurações | `CFG` | Cobertura parcial | `apps/api/CAU.Eleitoral.Tests/EmailServiceTests.cs` :: Constructor With Empty Smtp Host Disables Service (score=2) |
| `CT-CFG-004` | Módulo: Configurações | `CFG` | Cobertura parcial | `apps/admin/e2e/modules-pages.spec.ts` :: should render Sessao de Julgamento page (score=1) |
| `CT-PUB-AUTH-001` | Portal Público - Autenticação Eleitor | `PUB-AUTH` | Coberto automatizado | `apps/public/e2e/voting.spec.ts` :: should navigate to voter login (score=7) |
| `CT-PUB-AUTH-002` | Portal Público - Autenticação Eleitor | `PUB-AUTH` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve rejeitar login com dados inválidos (score=4) |
| `CT-PUB-AUTH-003` | Portal Público - Autenticação Eleitor | `PUB-AUTH` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve solicitar código de verificação (score=3) |
| `CT-PUB-AUTH-004` | Portal Público - Autenticação Eleitor | `PUB-AUTH` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve fazer logout do eleitor (score=5) |
| `CT-PUB-AUTH-005` | Portal Público - Autenticação Eleitor | `PUB-AUTH` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve verificar elegibilidade do eleitor (score=4) |
| `CT-PUB-VOT-001` | Portal Público - Votação Eleitor | `PUB-VOT` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should navigate to voter login (score=2) |
| `CT-PUB-VOT-002` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve votar em chapa usando método de conveniência (score=6) |
| `CT-PUB-VOT-003` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto em chapa e retornar comprovante (score=6) |
| `CT-PUB-VOT-004` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto em branco (score=3) |
| `CT-PUB-VOT-005` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto nulo (score=3) |
| `CT-PUB-VOT-006` | Portal Público - Votação Eleitor | `PUB-VOT` | Cobertura parcial | `apps/public/src/services/__tests__/votacao.test.ts` :: deve verificar se eleitor já votou (score=2) |
| `CT-PUB-VOT-007` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto em chapa e retornar comprovante (score=4) |
| `CT-PUB-VOT-008` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve validar comprovante de voto (score=4) |
| `CT-PUB-VOT-009` | Portal Público - Votação Eleitor | `PUB-VOT` | Cobertura parcial | `apps/public/src/services/__tests__/votacao.test.ts` :: deve retornar histórico de votos (score=2) |
| `CT-PUB-VOT-010` | Portal Público - Votação Eleitor | `PUB-VOT` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display documents page (score=2) |
| `CT-PUB-VOT-011` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve cancelar sessão de votação (score=3) |
| `CT-PUB-VOT-012` | Portal Público - Votação Eleitor | `PUB-VOT` | Coberto automatizado | `apps/public/src/services/__tests__/votacao.test.ts` :: deve cancelar sessão de votação (score=3) |
| `CT-PUB-CAND-001` | Portal Público - Área do Candidato | `PUB-CAND` | Coberto automatizado | `apps/public/e2e/voting.spec.ts` :: should display candidate login page (score=5) |
| `CT-PUB-CAND-002` | Portal Público - Área do Candidato | `PUB-CAND` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha do candidato (score=4) |
| `CT-PUB-CAND-003` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display candidate login page (score=2) |
| `CT-PUB-CAND-004` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display documents page (score=2) |
| `CT-PUB-CAND-005` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display candidate login page (score=2) |
| `CT-PUB-CAND-006` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto em chapa e retornar comprovante (score=2) |
| `CT-PUB-CAND-007` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display candidate login page (score=2) |
| `CT-PUB-CAND-008` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display candidate login page (score=2) |
| `CT-PUB-CAND-009` | Portal Público - Área do Candidato | `PUB-CAND` | Cobertura parcial | `apps/public/e2e/voting.spec.ts`, `apps/public/src/services/__tests__/auth.test.ts` |
| `CT-PUB-CAND-010` | Portal Público - Área do Candidato | `PUB-CAND` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha do candidato (score=4) |
| `CT-PUB-CAND-011` | Portal Público - Área do Candidato | `PUB-CAND` | Coberto automatizado | `apps/public/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha do candidato (score=5) |
| `CT-PUB-PAG-001` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display home page (score=2) |
| `CT-PUB-PAG-002` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display elections list on public page (score=2) |
| `CT-PUB-PAG-003` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display elections list on public page (score=2) |
| `CT-PUB-PAG-004` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display elections list on public page (score=2) |
| `CT-PUB-PAG-005` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display elections list on public page (score=2) |
| `CT-PUB-PAG-006` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display calendar page (score=2) |
| `CT-PUB-PAG-007` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display documents page (score=2) |
| `CT-PUB-PAG-008` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/voting.spec.ts` :: should display FAQ page (score=1) |
| `CT-PUB-PAG-009` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/src/services/__tests__/auth.test.ts` :: deve registrar novo candidato (score=2) |
| `CT-PUB-PAG-010` | Portal Público - Páginas Públicas | `PUB-PAG` | Cobertura parcial | `apps/public/e2e/responsividade.spec.ts`, `apps/public/e2e/voting.spec.ts` |
| `CT-RESP-001` | Testes de Responsividade | `RESP` | Coberto automatizado | `apps/admin/e2e/responsividade.spec.ts` :: CT-RESP-001 Admin Desktop 1920x1080 deve renderizar sem quebra de layout (score=5) |
| `CT-RESP-002` | Testes de Responsividade | `RESP` | Coberto automatizado | `apps/admin/e2e/responsividade.spec.ts` :: CT-RESP-002 Admin Tablet 768x1024 deve manter usabilidade no formulario (score=4) |
| `CT-RESP-003` | Testes de Responsividade | `RESP` | Coberto automatizado | `apps/admin/e2e/responsividade.spec.ts` :: CT-RESP-003 Admin Mobile 375x667 deve funcionar sem overflow horizontal (score=4) |
| `CT-RESP-004` | Testes de Responsividade | `RESP` | Coberto automatizado | `apps/admin/e2e/responsividade.spec.ts` :: CT-RESP-001 Admin Desktop 1920x1080 deve renderizar sem quebra de layout (score=3) |
| `CT-RESP-005` | Testes de Responsividade | `RESP` | Coberto automatizado | `apps/public/e2e/responsividade.spec.ts` :: CT-RESP-005 Public Mobile 375x667 deve manter navegacao funcional (score=4) |
| `CT-SEC-001` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/e2e/auth.spec.ts` :: should display login page (score=6) |
| `CT-SEC-002` | Testes de Segurança | `SEC` | Coberto automatizado | `apps/admin/src/services/__tests__/auth.test.ts` :: deve renovar token de acesso (score=3) |
| `CT-SEC-003` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/src/stores/__tests__/auth.test.ts` :: deve iniciar com estado vazio e não autenticado (score=1) |
| `CT-SEC-004` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/e2e/auth.spec.ts`, `apps/admin/e2e/full-system.spec.ts` |
| `CT-SEC-005` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/e2e/auth.spec.ts`, `apps/admin/e2e/full-system.spec.ts` |
| `CT-SEC-006` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/public/src/services/__tests__/votacao.test.ts` :: deve registrar voto em chapa e retornar comprovante (score=2) |
| `CT-SEC-007` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/src/services/__tests__/auth.test.ts` :: deve registrar novo usuário com sucesso (score=2) |
| `CT-SEC-008` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/src/stores/__tests__/auth.test.ts` :: deve manter isAuthenticated após atualização (score=3) |
| `CT-SEC-009` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/admin/src/services/__tests__/auth.test.ts` :: deve solicitar recuperação de senha (score=2) |
| `CT-SEC-010` | Testes de Segurança | `SEC` | Cobertura parcial | `apps/public/src/services/__tests__/votacao.test.ts` :: deve verificar se eleitor já votou (score=3) |

## 6. Criterio de Classificacao

- Coberto automatizado: existe pelo menos uma evidencia direta (matching textual score >= 3).
- Cobertura parcial: ha automacao no dominio, mas sem evidencia direta forte para o caso.
- Sem cobertura automatizada: nenhum teste foi detectado para o dominio do caso.
- O matching textual usa titulo do caso, resultado esperado, passos e titulos reais dos testes.
