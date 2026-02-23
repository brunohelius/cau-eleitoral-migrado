# CAU Sistema Eleitoral - Documentação de QA

> **Versão:** 1.0 | **Data:** 2026-02-23 | **Sistema:** CAU Sistema Eleitoral Migrado

## Índice

1. [Visão Geral](#1-visão-geral)
2. [Ambientes e Credenciais](#2-ambientes-e-credenciais)
3. [Módulo: Autenticação Admin](#3-módulo-autenticação-admin)
4. [Módulo: Dashboard](#4-módulo-dashboard)
5. [Módulo: Eleições](#5-módulo-eleições)
6. [Módulo: Chapas](#6-módulo-chapas)
7. [Módulo: Votação (Admin)](#7-módulo-votação-admin)
8. [Módulo: Denúncias](#8-módulo-denúncias)
9. [Módulo: Impugnações](#9-módulo-impugnações)
10. [Módulo: Julgamentos](#10-módulo-julgamentos)
11. [Módulo: Usuários](#11-módulo-usuários)
12. [Módulo: Relatórios](#12-módulo-relatórios)
13. [Módulo: Auditoria](#13-módulo-auditoria)
14. [Módulo: Configurações](#14-módulo-configurações)
15. [Portal Público - Autenticação Eleitor](#15-portal-público---autenticação-eleitor)
16. [Portal Público - Votação Eleitor](#16-portal-público---votação-eleitor)
17. [Portal Público - Área do Candidato](#17-portal-público---área-do-candidato)
18. [Portal Público - Páginas Públicas](#18-portal-público---páginas-públicas)
19. [Testes de Responsividade](#19-testes-de-responsividade)
20. [Testes de Segurança](#20-testes-de-segurança)

---

## 1. Visão Geral

O CAU Sistema Eleitoral é composto por 3 aplicações:

| Aplicação | URL Produção | Tecnologia |
|-----------|-------------|------------|
| **API** | https://cau-api.migrai.com.br | .NET 8 + PostgreSQL |
| **Admin** | https://cau-admin.migrai.com.br | React 18 + Vite + shadcn/ui |
| **Public** | https://cau-public.migrai.com.br | React 18 + Vite + shadcn/ui |

### Stack Técnica
- **Backend:** .NET 8, Entity Framework Core, JWT Auth, PostgreSQL
- **Frontend:** React 18, TypeScript, Vite 5, shadcn/ui, Tailwind CSS, Zustand, TanStack Query
- **Infra:** AWS ECS Fargate, RDS, CloudFront, S3

---

## 2. Ambientes e Credenciais

### Ambiente Local
| Serviço | URL |
|---------|-----|
| API | http://localhost:5001 |
| Swagger | http://localhost:5001/swagger |
| Admin | http://localhost:4200 |
| Public | http://localhost:4201 |

### Credenciais de Teste

| Perfil | Login | Senha |
|--------|-------|-------|
| **Admin** | admin@cau.org.br | Admin@123 |
| **Eleitor** | CPF: 60000000003 / Registro: A000005-SP | Eleitor@123 |
| **Candidato** | CPF: 45555555551 / Registro: A000018-DF | Candidato@123 |

### Health Check
```
GET https://cau-api.migrai.com.br/health → Esperado: "Healthy"
```

---

## 3. Módulo: Autenticação Admin

**Rota:** `/login`, `/forgot-password`, `/reset-password`

### CT-AUTH-001: Login com credenciais válidas
- **Pré-condição:** Usuário não autenticado
- **Passos:**
  1. Acessar `/login`
  2. Preencher email: `admin@cau.org.br`
  3. Preencher senha: `Admin@123`
  4. Clicar em "Entrar"
- **Resultado Esperado:** Redireciona para `/dashboard`, token JWT armazenado

### CT-AUTH-002: Login com credenciais inválidas
- **Passos:**
  1. Acessar `/login`
  2. Preencher email inválido ou senha incorreta
  3. Clicar em "Entrar"
- **Resultado Esperado:** Mensagem de erro exibida, permanece na tela de login

### CT-AUTH-003: Login com campos vazios
- **Passos:**
  1. Acessar `/login`
  2. Clicar em "Entrar" sem preencher campos
- **Resultado Esperado:** Validação dos campos obrigatórios exibida

### CT-AUTH-004: Logout
- **Pré-condição:** Usuário autenticado
- **Passos:**
  1. Clicar no menu do usuário
  2. Selecionar "Sair"
- **Resultado Esperado:** Redireciona para `/login`, tokens removidos

### CT-AUTH-005: Recuperação de senha
- **Passos:**
  1. Na tela de login, clicar em "Esqueceu a senha?"
  2. Preencher email cadastrado
  3. Clicar em "Enviar"
- **Resultado Esperado:** Mensagem de sucesso, email enviado com link de recuperação

### CT-AUTH-006: Redefinição de senha
- **Pré-condição:** Link de recuperação válido recebido
- **Passos:**
  1. Acessar link de recuperação
  2. Preencher nova senha e confirmação
  3. Clicar em "Redefinir"
- **Resultado Esperado:** Senha alterada com sucesso, redireciona para login

### CT-AUTH-007: Acesso a rota protegida sem autenticação
- **Passos:**
  1. Acessar `/dashboard` diretamente sem estar logado
- **Resultado Esperado:** Redireciona para `/login`

### CT-AUTH-008: Renovação automática de token (Refresh Token)
- **Pré-condição:** Token JWT expirado, refresh token válido
- **Resultado Esperado:** Token renovado automaticamente sem interrupção

---

## 4. Módulo: Dashboard

**Rota:** `/dashboard`

### CT-DASH-001: Carregamento do Dashboard
- **Pré-condição:** Usuário admin autenticado
- **Passos:**
  1. Acessar `/dashboard`
- **Resultado Esperado:** Exibe cards com estatísticas: eleições ativas, total de chapas, total de votos, denúncias pendentes

### CT-DASH-002: Estatísticas em tempo real
- **Resultado Esperado:** Os contadores refletem os dados atuais do sistema

### CT-DASH-003: Navegação rápida via Dashboard
- **Passos:**
  1. Clicar nos cards/links do dashboard
- **Resultado Esperado:** Navega para a página correspondente

---

## 5. Módulo: Eleições

**Rotas:** `/eleicoes`, `/eleicoes/nova`, `/eleicoes/:id`, `/eleicoes/:id/editar`, `/eleicoes/:id/calendario`, `/eleicoes/:id/apuracao`

### CT-ELE-001: Listagem de eleições
- **Passos:**
  1. Acessar `/eleicoes`
- **Resultado Esperado:** Lista todas as eleições com nome, ano, status, e ações disponíveis

### CT-ELE-002: Criar nova eleição
- **Passos:**
  1. Clicar em "Nova Eleição"
  2. Preencher: nome, ano, tipo, modo de votação, datas de início e fim
  3. Clicar em "Salvar"
- **Resultado Esperado:** Eleição criada com status "Planejada", exibida na listagem

### CT-ELE-003: Visualizar detalhes da eleição
- **Passos:**
  1. Clicar em uma eleição na listagem
- **Resultado Esperado:** Exibe dados completos: informações gerais, chapas vinculadas, calendário, estatísticas

### CT-ELE-004: Editar eleição
- **Pré-condição:** Eleição não finalizada e não cancelada
- **Passos:**
  1. Na tela de detalhes, clicar em "Editar"
  2. Alterar campos desejados
  3. Clicar em "Salvar"
- **Resultado Esperado:** Dados atualizados com sucesso

### CT-ELE-005: Validação de edição - Eleição com votos
- **Pré-condição:** Eleição com votos registrados
- **Passos:**
  1. Tentar editar datas de início ou modo de votação
- **Resultado Esperado:** Campos bloqueados ou aviso de que não podem ser alterados

### CT-ELE-006: Iniciar eleição
- **Pré-condição:** Eleição em status "Planejada" com chapas cadastradas
- **Passos:**
  1. Clicar em "Iniciar Eleição"
- **Resultado Esperado:** Status muda para "Em Andamento"

### CT-ELE-007: Encerrar eleição
- **Pré-condição:** Eleição em status "Em Andamento"
- **Passos:**
  1. Clicar em "Encerrar Eleição"
- **Resultado Esperado:** Status muda para "Encerrada"

### CT-ELE-008: Suspender eleição
- **Pré-condição:** Eleição em andamento
- **Passos:**
  1. Clicar em "Suspender"
  2. Informar motivo
- **Resultado Esperado:** Status muda para "Suspensa", motivo registrado

### CT-ELE-009: Cancelar eleição
- **Passos:**
  1. Clicar em "Cancelar Eleição"
  2. Informar motivo
- **Resultado Esperado:** Status muda para "Cancelada", motivo registrado

### CT-ELE-010: Excluir eleição
- **Pré-condição:** Eleição sem votos e não em andamento
- **Passos:**
  1. Clicar em "Excluir"
  2. Confirmar dialog
- **Resultado Esperado:** Eleição removida (soft delete), chapas associadas também removidas

### CT-ELE-011: Excluir eleição com restrição
- **Pré-condição:** Eleição com votos ou em andamento
- **Passos:**
  1. Tentar excluir
- **Resultado Esperado:** Mensagem de erro indicando que não pode ser excluída

### CT-ELE-012: Verificar editabilidade (CanEdit)
- **Resultado Esperado:** API retorna se pode editar, warnings sobre restrições, qtd de votos/chapas

### CT-ELE-013: Visualizar calendário da eleição
- **Passos:**
  1. Acessar `/eleicoes/:id/calendario`
- **Resultado Esperado:** Calendário com prazos e eventos da eleição

### CT-ELE-014: Visualizar apuração da eleição
- **Passos:**
  1. Acessar `/eleicoes/:id/apuracao`
- **Resultado Esperado:** Resultados da apuração com gráficos

---

## 6. Módulo: Chapas

**Rotas:** `/chapas`, `/chapas/nova`, `/chapas/:id`, `/chapas/:id/editar`, `/chapas/:id/membros`, `/chapas/:id/documentos`

### CT-CHA-001: Listagem de chapas com filtros
- **Passos:**
  1. Acessar `/chapas`
  2. Filtrar por eleição, status, busca textual
- **Resultado Esperado:** Lista paginada com chapas filtradas

### CT-CHA-002: Criar nova chapa
- **Passos:**
  1. Clicar em "Nova Chapa"
  2. Preencher: nome, número, sigla, slogan, eleição vinculada
  3. Clicar em "Salvar"
- **Resultado Esperado:** Chapa criada com status "Rascunho"

### CT-CHA-003: Visualizar detalhes da chapa
- **Passos:**
  1. Clicar em uma chapa na listagem
- **Resultado Esperado:** Exibe dados completos incluindo membros e documentos

### CT-CHA-004: Editar chapa
- **Passos:**
  1. Na tela de detalhes, clicar em "Editar"
  2. Alterar dados
  3. Salvar
- **Resultado Esperado:** Dados atualizados

### CT-CHA-005: Adicionar membro à chapa
- **Passos:**
  1. Na página de membros (`/chapas/:id/membros`)
  2. Clicar em "Adicionar Membro"
  3. Preencher: nome, CPF, registro CAU, cargo
  4. Salvar
- **Resultado Esperado:** Membro adicionado com sucesso

### CT-CHA-006: Atualizar membro da chapa
- **Passos:**
  1. Na lista de membros, editar um membro existente
  2. Alterar cargo ou dados
  3. Salvar
- **Resultado Esperado:** Dados do membro atualizados

### CT-CHA-007: Remover membro da chapa
- **Passos:**
  1. Na lista de membros, clicar em "Remover"
  2. Confirmar
- **Resultado Esperado:** Membro removido da chapa

### CT-CHA-008: Submeter chapa para análise
- **Pré-condição:** Chapa em status "Rascunho" com membros cadastrados
- **Passos:**
  1. Clicar em "Submeter para Análise"
- **Resultado Esperado:** Status muda para "Submetida"

### CT-CHA-009: Iniciar análise da chapa
- **Pré-condição:** Chapa submetida
- **Passos:**
  1. Clicar em "Iniciar Análise"
- **Resultado Esperado:** Status muda para "Em Análise"

### CT-CHA-010: Deferir chapa
- **Pré-condição:** Chapa em análise
- **Passos:**
  1. Clicar em "Deferir"
  2. Adicionar parecer
- **Resultado Esperado:** Status muda para "Deferida"

### CT-CHA-011: Indeferir chapa
- **Pré-condição:** Chapa em análise
- **Passos:**
  1. Clicar em "Indeferir"
  2. Informar motivo
- **Resultado Esperado:** Status muda para "Indeferida"

### CT-CHA-012: Solicitar documentos pendentes
- **Passos:**
  1. Clicar em "Solicitar Documentos"
- **Resultado Esperado:** Chapa notificada sobre documentos pendentes

### CT-CHA-013: Excluir chapa (apenas rascunhos)
- **Pré-condição:** Chapa em status "Rascunho"
- **Resultado Esperado:** Chapa removida

### CT-CHA-014: Gerenciar documentos da chapa
- **Passos:**
  1. Acessar `/chapas/:id/documentos`
- **Resultado Esperado:** Lista documentos, permite upload e download

---

## 7. Módulo: Votação (Admin)

**Rotas:** `/votacao`, `/votacao/:eleicaoId`, `/votacao/:eleicaoId/apuracao`

### CT-VOT-001: Listagem de eleições com status de votação
- **Passos:**
  1. Acessar `/votacao`
- **Resultado Esperado:** Lista eleições com: status da votação, total de eleitores, votos computados, participação

### CT-VOT-002: Monitor de votação em tempo real
- **Passos:**
  1. Clicar em uma eleição em votação
- **Resultado Esperado:** Exibe: total de votos, participação %, votos por hora, votos por região

### CT-VOT-003: Abrir votação
- **Pré-condição:** Eleição iniciada com chapas deferidas
- **Passos:**
  1. Clicar em "Abrir Votação"
- **Resultado Esperado:** Votação aberta, eleitores podem votar

### CT-VOT-004: Fechar votação
- **Pré-condição:** Votação aberta
- **Passos:**
  1. Clicar em "Fechar Votação"
- **Resultado Esperado:** Votação encerrada

### CT-VOT-005: Visualizar estatísticas de votação
- **Resultado Esperado:** Exibe: votos válidos, brancos, nulos, participação por região

### CT-VOT-006: Listar eleitores que votaram
- **Resultado Esperado:** Lista paginada de eleitores com data/hora do voto (sem revelar em quem votaram)

### CT-VOT-007: Anular voto (Admin)
- **Passos:**
  1. Selecionar voto na lista
  2. Clicar em "Anular"
  3. Informar motivo
- **Resultado Esperado:** Voto anulado com registro de auditoria

### CT-VOT-008: Apuração de votos
- **Passos:**
  1. Acessar `/votacao/:eleicaoId/apuracao`
  2. Clicar em "Iniciar Apuração"
- **Resultado Esperado:** Apuração realizada, resultado exibido com ranking de chapas

### CT-VOT-009: Publicar resultados
- **Pré-condição:** Apuração finalizada
- **Passos:**
  1. Clicar em "Publicar Resultados"
- **Resultado Esperado:** Resultados publicados, visíveis no portal público

### CT-VOT-010: Exportar resultados
- **Passos:**
  1. Selecionar formato (PDF, Excel, CSV)
  2. Clicar em "Exportar"
- **Resultado Esperado:** Arquivo gerado e baixado com sucesso

---

## 8. Módulo: Denúncias

**Rotas:** `/denuncias`, `/denuncias/nova`, `/denuncias/:id`, `/denuncias/:id/editar`, `/denuncias/:id/julgamento`

### CT-DEN-001: Listagem de denúncias com filtros
- **Passos:**
  1. Acessar `/denuncias`
  2. Filtrar por status, eleição, chapa
- **Resultado Esperado:** Lista paginada e filtrada

### CT-DEN-002: Criar nova denúncia
- **Passos:**
  1. Clicar em "Nova Denúncia"
  2. Preencher: eleição, chapa denunciada, tipo, descrição, evidências
  3. Salvar
- **Resultado Esperado:** Denúncia criada com protocolo gerado automaticamente

### CT-DEN-003: Consultar denúncia por protocolo
- **Passos:**
  1. Buscar pelo número do protocolo
- **Resultado Esperado:** Denúncia encontrada e exibida

### CT-DEN-004: Visualizar detalhes da denúncia
- **Resultado Esperado:** Exibe todos os dados, histórico de status, documentos anexos

### CT-DEN-005: Iniciar análise da denúncia
- **Passos:**
  1. Clicar em "Iniciar Análise"
- **Resultado Esperado:** Status muda para "Em Análise"

### CT-DEN-006: Concluir análise
- **Passos:**
  1. Preencher parecer da análise
  2. Clicar em "Concluir Análise"
- **Resultado Esperado:** Análise concluída com parecer registrado

### CT-DEN-007: Aceitar admissibilidade
- **Passos:**
  1. Clicar em "Aceitar Admissibilidade"
  2. Informar parecer
- **Resultado Esperado:** Denúncia aceita, segue para próxima etapa

### CT-DEN-008: Rejeitar admissibilidade
- **Passos:**
  1. Clicar em "Rejeitar Admissibilidade"
  2. Informar motivo
- **Resultado Esperado:** Denúncia rejeitada

### CT-DEN-009: Enviar para julgamento
- **Pré-condição:** Denúncia com admissibilidade aceita
- **Passos:**
  1. Clicar em "Enviar para Julgamento"
- **Resultado Esperado:** Denúncia encaminhada para sessão de julgamento

### CT-DEN-010: Listar denúncias por eleição
- **Resultado Esperado:** Filtra corretamente por eleição

### CT-DEN-011: Listar denúncias por chapa
- **Resultado Esperado:** Filtra corretamente por chapa

### CT-DEN-012: Listar minhas denúncias (denunciante)
- **Resultado Esperado:** Lista apenas denúncias do usuário logado

---

## 9. Módulo: Impugnações

**Rotas:** `/impugnacoes`, `/impugnacoes/nova`, `/impugnacoes/:id`, `/impugnacoes/:id/editar`

### CT-IMP-001: Listagem de impugnações
- **Resultado Esperado:** Lista todas as impugnações com status e eleição

### CT-IMP-002: Criar nova impugnação
- **Passos:**
  1. Clicar em "Nova Impugnação"
  2. Preencher dados: eleição, chapa impugnada, motivo, documentos
  3. Salvar
- **Resultado Esperado:** Impugnação criada com protocolo

### CT-IMP-003: Visualizar detalhes
- **Resultado Esperado:** Dados completos da impugnação com histórico

### CT-IMP-004: Editar impugnação
- **Resultado Esperado:** Dados atualizados (apenas se em status editável)

---

## 10. Módulo: Julgamentos

**Rotas:** `/julgamentos`, `/julgamentos/sessao`, `/julgamentos/:id`

### CT-JUL-001: Listagem de julgamentos
- **Resultado Esperado:** Lista julgamentos com data, status e partes envolvidas

### CT-JUL-002: Criar sessão de julgamento
- **Passos:**
  1. Clicar em "Nova Sessão"
  2. Definir data, hora, membros da comissão
  3. Salvar
- **Resultado Esperado:** Sessão criada

### CT-JUL-003: Visualizar detalhes do julgamento
- **Resultado Esperado:** Dados completos: denúncia associada, comissão, decisão, ata

### CT-JUL-004: Registrar decisão do julgamento
- **Resultado Esperado:** Decisão registrada com justificativa

---

## 11. Módulo: Usuários

**Rotas:** `/usuarios`, `/usuarios/novo`, `/usuarios/:id`, `/usuarios/:id/editar`

### CT-USR-001: Listagem de usuários
- **Resultado Esperado:** Lista com nome, email, tipo, status

### CT-USR-002: Criar novo usuário
- **Passos:**
  1. Clicar em "Novo Usuário"
  2. Preencher: nome, email, CPF, tipo (Admin, ComissaoEleitoral, Conselheiro, etc.)
  3. Salvar
- **Resultado Esperado:** Usuário criado

### CT-USR-003: Editar usuário
- **Resultado Esperado:** Dados atualizados

### CT-USR-004: Visualizar detalhes do usuário
- **Resultado Esperado:** Dados completos incluindo permissões e histórico

### CT-USR-005: Tipos de usuário
- **Resultado Esperado:** Suporta: Admin, ComissaoEleitoral, Conselheiro, Profissional, Candidato, Eleitor

---

## 12. Módulo: Relatórios

**Rotas:** `/relatorios`, `/relatorios/eleicao`, `/relatorios/votacao`

### CT-REL-001: Página de relatórios
- **Resultado Esperado:** Lista tipos de relatórios disponíveis

### CT-REL-002: Relatório de eleição
- **Passos:**
  1. Selecionar eleição
  2. Gerar relatório
- **Resultado Esperado:** Relatório com dados da eleição, chapas, timeline

### CT-REL-003: Relatório de votação
- **Passos:**
  1. Selecionar eleição
  2. Gerar relatório
- **Resultado Esperado:** Relatório com estatísticas de votação, participação, resultados

---

## 13. Módulo: Auditoria

**Rota:** `/auditoria`

### CT-AUD-001: Visualizar logs de auditoria
- **Resultado Esperado:** Lista ações do sistema com: usuário, ação, data/hora, IP, detalhes

### CT-AUD-002: Filtrar por período
- **Resultado Esperado:** Filtra corretamente por intervalo de datas

### CT-AUD-003: Filtrar por usuário
- **Resultado Esperado:** Filtra ações de um usuário específico

### CT-AUD-004: Filtrar por tipo de ação
- **Resultado Esperado:** Filtra por categoria de ação (login, CRUD, votação, etc.)

---

## 14. Módulo: Configurações

**Rota:** `/configuracoes`

### CT-CFG-001: Visualizar configurações da eleição
- **Resultado Esperado:** Exibe configurações atuais do sistema eleitoral

### CT-CFG-002: Alterar configurações de votação
- **Passos:**
  1. Alterar parâmetros (horário de votação, timeout, etc.)
  2. Salvar
- **Resultado Esperado:** Configurações atualizadas

### CT-CFG-003: Configurações de email/notificação
- **Resultado Esperado:** Configurações de SMTP e templates de email

### CT-CFG-004: Configurações de segurança
- **Resultado Esperado:** Configurações de senha, sessão, políticas de acesso

---

## 15. Portal Público - Autenticação Eleitor

**Rotas:** `/votacao` (login), `/eleitor/*`

### CT-PUB-AUTH-001: Login do eleitor
- **Passos:**
  1. Acessar `/votacao`
  2. Preencher CPF e Registro CAU
  3. Inserir código de verificação (se necessário)
  4. Clicar em "Entrar"
- **Resultado Esperado:** Redireciona para área do eleitor

### CT-PUB-AUTH-002: Login com dados inválidos
- **Resultado Esperado:** Mensagem de erro

### CT-PUB-AUTH-003: Solicitar código de verificação
- **Passos:**
  1. Preencher CPF e Registro CAU
  2. Solicitar código
- **Resultado Esperado:** Código enviado por email ou SMS

### CT-PUB-AUTH-004: Logout do eleitor
- **Resultado Esperado:** Sessão encerrada, redireciona para página pública

### CT-PUB-AUTH-005: Verificar elegibilidade do eleitor
- **Resultado Esperado:** Retorna se eleitor pode votar, motivo caso não possa

---

## 16. Portal Público - Votação Eleitor

**Rotas:** `/eleitor/votacao`, `/eleitor/votacao/:eleicaoId/cedula`, `/eleitor/votacao/:eleicaoId/confirmacao`, `/eleitor/votacao/:eleicaoId/comprovante`, `/eleitor/votacao/:eleicaoId/ja-votou`

### CT-PUB-VOT-001: Listar eleições disponíveis
- **Pré-condição:** Eleitor autenticado
- **Resultado Esperado:** Lista eleições em que o eleitor pode votar

### CT-PUB-VOT-002: Iniciar sessão de votação
- **Passos:**
  1. Selecionar eleição
  2. Clicar em "Votar"
- **Resultado Esperado:** Exibe cédula de votação com chapas

### CT-PUB-VOT-003: Votar em uma chapa
- **Passos:**
  1. Selecionar chapa na cédula
  2. Confirmar voto
- **Resultado Esperado:** Voto registrado, exibe comprovante com protocolo e hash

### CT-PUB-VOT-004: Voto em branco
- **Passos:**
  1. Selecionar "Voto em Branco"
  2. Confirmar
- **Resultado Esperado:** Voto branco registrado

### CT-PUB-VOT-005: Voto nulo
- **Passos:**
  1. Selecionar "Voto Nulo"
  2. Confirmar
- **Resultado Esperado:** Voto nulo registrado

### CT-PUB-VOT-006: Impedir voto duplicado
- **Pré-condição:** Eleitor já votou na eleição
- **Resultado Esperado:** Redireciona para `/ja-votou` com mensagem informativa

### CT-PUB-VOT-007: Comprovante de votação
- **Resultado Esperado:** Exibe protocolo, hash, QR Code, opções de download PDF e envio por email

### CT-PUB-VOT-008: Validar comprovante
- **Passos:**
  1. Informar protocolo e hash
- **Resultado Esperado:** Sistema confirma validade do comprovante

### CT-PUB-VOT-009: Histórico de votos
- **Passos:**
  1. Acessar `/eleitor/meus-votos`
- **Resultado Esperado:** Lista todas as eleições em que participou com protocolo

### CT-PUB-VOT-010: Justificar ausência
- **Passos:**
  1. Informar motivo e anexar documento
- **Resultado Esperado:** Justificativa registrada com protocolo

### CT-PUB-VOT-011: Cancelar sessão antes de votar
- **Resultado Esperado:** Sessão cancelada sem registro de voto

### CT-PUB-VOT-012: Timeout da sessão de votação
- **Pré-condição:** Sessão iniciada sem confirmação de voto
- **Resultado Esperado:** Sessão expira após tempo máximo

---

## 17. Portal Público - Área do Candidato

**Rotas:** `/candidato/login`, `/candidato/*`

### CT-PUB-CAND-001: Login do candidato
- **Passos:**
  1. Acessar `/candidato/login`
  2. Preencher CPF, Registro CAU e senha
  3. Clicar em "Entrar"
- **Resultado Esperado:** Redireciona para área do candidato

### CT-PUB-CAND-002: Registro de novo candidato
- **Passos:**
  1. Preencher dados: nome, CPF, registro CAU, email, senha
  2. Aceitar termos
  3. Submeter
- **Resultado Esperado:** Conta criada, instruções dos próximos passos

### CT-PUB-CAND-003: Visualizar chapa
- **Rota:** `/candidato` (index)
- **Resultado Esperado:** Exibe dados da chapa do candidato

### CT-PUB-CAND-004: Gerenciar documentos
- **Rota:** `/candidato/documentos`
- **Resultado Esperado:** Upload, download e visualização de documentos

### CT-PUB-CAND-005: Editar plataforma
- **Rota:** `/candidato/plataforma`
- **Resultado Esperado:** Candidato pode editar sua plataforma/proposta

### CT-PUB-CAND-006: Visualizar denúncias
- **Rota:** `/candidato/denuncias`
- **Resultado Esperado:** Lista denúncias relacionadas à chapa

### CT-PUB-CAND-007: Apresentar defesa
- **Rota:** `/candidato/defesas`
- **Resultado Esperado:** Candidato pode apresentar defesa a denúncias

### CT-PUB-CAND-008: Gerenciar recursos
- **Rota:** `/candidato/recursos`
- **Resultado Esperado:** Candidato pode interpor recursos

### CT-PUB-CAND-009: Visualizar histórico
- **Rota:** `/candidato/historico`
- **Resultado Esperado:** Histórico de ações e eventos

### CT-PUB-CAND-010: Alterar senha do candidato
- **Resultado Esperado:** Senha alterada com sucesso

### CT-PUB-CAND-011: Recuperação de senha do candidato
- **Resultado Esperado:** Email de recuperação enviado

---

## 18. Portal Público - Páginas Públicas

**Rotas:** `/`, `/eleicoes`, `/calendario`, `/documentos`, `/faq`, `/denuncias/nova`, `/denuncias/consultar`

### CT-PUB-PAG-001: Home page
- **Resultado Esperado:** Página inicial com informações do sistema eleitoral

### CT-PUB-PAG-002: Lista de eleições públicas
- **Rota:** `/eleicoes`
- **Resultado Esperado:** Lista eleições públicas com status e informações gerais

### CT-PUB-PAG-003: Detalhes da eleição pública
- **Rota:** `/eleicoes/:id`
- **Resultado Esperado:** Informações detalhadas da eleição

### CT-PUB-PAG-004: Chapas da eleição
- **Rota:** `/eleicoes/:id/chapas`
- **Resultado Esperado:** Lista chapas com detalhes dos membros

### CT-PUB-PAG-005: Resultados da eleição
- **Rota:** `/eleicoes/:id/resultados`
- **Resultado Esperado:** Resultados publicados (gráficos e tabelas)

### CT-PUB-PAG-006: Calendário eleitoral
- **Rota:** `/calendario`
- **Resultado Esperado:** Cronograma com prazos e eventos

### CT-PUB-PAG-007: Documentos públicos
- **Rota:** `/documentos`
- **Resultado Esperado:** Lista editais, resoluções e documentos do processo eleitoral

### CT-PUB-PAG-008: FAQ
- **Rota:** `/faq`
- **Resultado Esperado:** Perguntas frequentes com respostas

### CT-PUB-PAG-009: Registrar denúncia pública
- **Rota:** `/denuncias/nova`
- **Resultado Esperado:** Formulário público para registro de denúncia

### CT-PUB-PAG-010: Consultar denúncia por protocolo
- **Rota:** `/denuncias/consultar`
- **Resultado Esperado:** Busca por protocolo retorna status da denúncia

---

## 19. Testes de Responsividade

### CT-RESP-001: Admin - Desktop (1920x1080)
- **Resultado Esperado:** Layout adequado, sidebar visível, tabelas completas

### CT-RESP-002: Admin - Tablet (768x1024)
- **Resultado Esperado:** Sidebar colapsável, tabelas com scroll horizontal

### CT-RESP-003: Admin - Mobile (375x667)
- **Resultado Esperado:** Menu hamburger, cards empilhados, formulários em coluna única

### CT-RESP-004: Public - Desktop
- **Resultado Esperado:** Layout em grid, navegação completa

### CT-RESP-005: Public - Mobile
- **Resultado Esperado:** Menu responsivo, fluxo de votação funcional em mobile

---

## 20. Testes de Segurança

### CT-SEC-001: Acesso não autenticado às rotas admin
- **Resultado Esperado:** Redireciona para login

### CT-SEC-002: Acesso com token expirado
- **Resultado Esperado:** Refresh automático ou redirecionamento para login

### CT-SEC-003: CORS - Origens não autorizadas
- **Resultado Esperado:** Requisições bloqueadas

### CT-SEC-004: Injeção SQL nos campos de busca
- **Resultado Esperado:** Inputs sanitizados, sem execução de SQL

### CT-SEC-005: XSS nos campos de texto
- **Resultado Esperado:** Scripts HTML escapados corretamente

### CT-SEC-006: Voto duplicado via API direta
- **Resultado Esperado:** API rejeita com erro 400/409

### CT-SEC-007: Acesso a dados de outros usuários
- **Resultado Esperado:** Autorização verifica ownership dos dados

### CT-SEC-008: Rate limiting nas rotas de autenticação
- **Resultado Esperado:** Bloqueio após múltiplas tentativas falhas

### CT-SEC-009: Força da senha
- **Resultado Esperado:** Mínimo 8 caracteres, maiúscula, minúscula, número, caractere especial

### CT-SEC-010: Sigilo do voto
- **Resultado Esperado:** Não é possível associar voto a eleitor, apenas confirmar se votou
