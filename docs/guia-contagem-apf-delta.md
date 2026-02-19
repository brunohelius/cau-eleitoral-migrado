# Guia de Contagem APF — CAU Sistema Eleitoral

> **Para:** Equipe Delta (contagem independente de Pontos de Função)  
> **Projeto:** CAU Sistema Eleitoral Migrado  
> **Metodologia:** IFPUG CPM 4.3.1  
> **Data:** 19/02/2026  

---

## 1. Introdução

Este guia fornece as instruções passo-a-passo para que a equipe Delta realize uma contagem independente de Pontos de Função (APF) do sistema CAU Eleitoral. O objetivo é que ambas as equipes cheguem a resultados consistentes, utilizando a mesma metodologia e evidências do código-fonte.

> **Resultado esperado:** Entre **1.100 e 1.300 PF** (não ajustados / ajustados), dependendo do nível de agrupamento das funções transacionais.

---

## 2. Visão Geral do Sistema

### 2.1 Descrição

Sistema de gestão completa de processos eleitorais do Conselho de Arquitetura e Urbanismo (CAU), incluindo:
- Gestão de eleições, chapas, votação e apuração
- Denúncias, impugnações e julgamentos eleitorais (fluxos judiciais)
- Documentos oficiais (editais, atas, diplomas, termos de posse)
- Portal público para denúncias anônimas e votação online
- Dashboard, relatórios e auditoria

### 2.2 Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Backend API | .NET 8, ASP.NET Core Web API, Entity Framework Core, PostgreSQL |
| Frontend Admin | React 18, TypeScript, Vite, shadcn/ui |
| Frontend Public | React 18, TypeScript, Vite, shadcn/ui |
| Infraestrutura | AWS ECS Fargate, RDS PostgreSQL, S3, CloudFront, ALB |

### 2.3 Fronteira da Aplicação

A fronteira do sistema inclui:
- **Dentro:** API Backend + Frontend Admin + Frontend Public + Banco PostgreSQL
- **Fora (AIE):** AWS S3, serviço SMTP, AWS Secrets Manager

### 2.4 Onde Encontrar no Código

| Artefato | Caminho |
|----------|---------|
| Entidades de domínio | `apps/api/CAU.Eleitoral.Domain/Entities/` |
| Controllers (API) | `apps/api/CAU.Eleitoral.Api/Controllers/` |
| Services | `apps/api/CAU.Eleitoral.Application/Services/` |
| DTOs | `apps/api/CAU.Eleitoral.Application/DTOs/` |
| Páginas Admin | `apps/admin/src/pages/` |
| Páginas Public | `apps/public/src/pages/` |

---

## 3. Metodologia — Passo a Passo

### 3.1 Passo 1: Identificar as Funções de Dados (ALI/AIE)

#### Regra IFPUG para ALIs

> Um ALI é um **grupo de dados logicamente relacionados, reconhecível pelo usuário**, mantido dentro da fronteira. **Não é uma tabela do banco de dados.**

**Abordagem recomendada:** Agrupar as 156 entidades do domínio em ALIs lógicos por módulo funcional. Cada módulo com uma entidade principal e suas dependentes constitui **1 ALI**.

#### Entidades por Módulo (evidência do código)

Para listar as entidades, execute no diretório raiz:

```bash
find apps/api/CAU.Eleitoral.Domain/Entities -name "*.cs" | \
  xargs grep -l "class.*:.*BaseEntity" | \
  sed 's|.*/||; s|\.cs||' | sort
```

**Resultado esperado: 156 entidades** organizadas em 7 pastas:

| Pasta | Entidades | Conteúdo |
|-------|-----------|----------|
| `Core/` | 27 | Eleição, Calendário, Votação, Apuração, Configuração, Regional |
| `Chapas/` | 8 | ChapaEleicao, MembroChapa, DocumentoChapa, etc. |
| `Denuncias/` | 23 | Denuncia, Defesa, Admissibilidade, Análise, Alegações, etc. |
| `Documentos/` | 43 | Documento, Edital, Resolução, Ata, Diploma, etc. |
| `Impugnacoes/` | 13 | ImpugnacaoResultado, Pedido, Alegação, Defesa, etc. |
| `Julgamentos/` | 33 | Julgamento, Sessão, Decisão, Acórdão, Recurso, Voto, etc. |
| `Usuarios/` | 9 | Usuario, Profissional, Conselheiro, Role, Permissão |

#### ALIs Recomendados (agrupamento IFPUG)

| # | ALI | Entidades Agrupadas | RET | DET Est. | Complexidade | PF |
|---|-----|---------------------|-----|----------|--------------|-----|
| 1 | **Eleição** | Eleicao, ConfiguracaoEleicao, EleicaoSituacao, EtapaEleicao, FaseEleicaoConfig, TipoEleicaoConfig, ParametroEleicao, Circunscricao (8 ent.) | 6+ | 50+ | Complexa | 15 |
| 2 | **Calendário** | Calendario, CalendarioSituacao, AtividadePrincipal, AtividadeSecundaria (4 ent.) | 4 | 30 | Média | 10 |
| 3 | **Chapa Eleitoral** | ChapaEleicao, MembroChapa, ComposicaoChapa, DocumentoChapa, HistoricoChapaEleicao, PlataformaEleitoral, SubstituicaoMembroChapa, ConfirmacaoMembroChapa (8 ent.) | 5 | 50+ | Complexa | 15 |
| 4 | **Votação / Eleitor** | Voto, Eleitor, SecaoEleitoral, ZonaEleitoral, MesaReceptora, UrnaEletronica, FiscalEleicao (7 ent.) | 6+ | 50+ | Complexa | 15 |
| 5 | **Apuração** | ApuracaoResultado, ApuracaoResultadoChapa, AtaApuracao, BoletimUrna, RegistroApuracaoVotos, ResultadoEleicao (6 ent.) | 5 | 40+ | Complexa | 15 |
| 6 | **Denúncia** | Denuncia + 22 sub-entidades (DefesaDenuncia, AdmissibilidadeDenuncia, AlegacoesDenuncia, ContraAlegacoesDenuncia, AnaliseDenuncia, etc.) | 6+ | 80+ | Complexa | 15 |
| 7 | **Impugnação** | ImpugnacaoResultado + 12 sub-entidades | 6+ | 60+ | Complexa | 15 |
| 8 | **Julgamento** | ComissaoJulgadora, SessaoJulgamento, JulgamentoFinal, DecisaoJulgamento, AcordaoJulgamento + 28 sub-entidades | 6+ | 100+ | Complexa | 15 |
| 9 | **Documento** | Documento, ArquivoDocumento + 23 tipos de documentos (Edital, Resolução, Ato, etc.) | 6+ | 60+ | Complexa | 15 |
| 10 | **Resultado / Relatório** | ResultadoFinal, ResultadoParcial, RelatorioApuracao, RelatorioVotacao, EstatisticaEleicao, GraficoResultado, MapaVotacao, ExportacaoDados (8 ent.) | 5 | 40+ | Complexa | 15 |
| 11 | **Usuário** | Usuario, Profissional, Conselheiro (3 ent.) | 3 | 60+ | Complexa | 15 |
| 12 | **Controle de Acesso** | Role, Permissao, UsuarioRole, RolePermissao (4 ent.) | 4 | 20 | Média | 10 |
| 13 | **Notificação** | Notificacao + configurações (2 ent.) | 2 | 20 | Simples | 7 |
| 14 | **Auditoria** | AuditoriaLog, LogAcesso (2 ent.) | 2 | 25 | Média | 10 |
| 15 | **Filial / Regional** | Filial, RegionalCAU, RegiaoPleito (3 ent.) | 3 | 30 | Média | 10 |
| 16 | **Configuração Geral** | Configuracao (1 ent.) | 1 | 15 | Simples | 7 |
| | **TOTAL ALIs** | **156 entidades** | | | | **204 PF** |

#### AIEs (Arquivos de Interface Externa)

| # | AIE | Descrição | Complexidade | PF |
|---|-----|-----------|--------------|-----|
| 1 | AWS S3 | Storage de documentos e uploads | Simples | 5 |
| 2 | SMTP (Email) | Notificações por email | Simples | 5 |
| 3 | AWS Secrets Manager | Credenciais e chaves | Simples | 5 |
| | **Total AIEs** | | | **15 PF** |

---

### 3.2 Passo 2: Identificar as Funções Transacionais (EE/CE/SE)

#### Como identificar os endpoints no código

Execute o seguinte comando para listar todos os endpoints HTTP:

```bash
grep -rn '\[Http\(Get\|Post\|Put\|Delete\|Patch\)\]' \
  apps/api/CAU.Eleitoral.Api/Controllers/*.cs | \
  sed 's|.*/||' | sort | uniq -c | sort -rn
```

**Resultado esperado: 326 endpoints** distribuídos em 20 controllers:

| Controller | Endpoints | Módulo |
|------------|-----------|--------|
| ImpugnacaoController | 43 | Impugnações |
| DenunciaController | 25 | Denúncias |
| UsuarioController | 22 | Usuários |
| AuthController | 21 | Autenticação |
| ConfiguracaoController | 20 | Configuração |
| ApuracaoController | 19 | Apuração |
| ChapasController | 19 | Chapas |
| JulgamentoController | 16 | Julgamentos |
| DocumentoController | 14 | Documentos |
| VotacaoController | 14 | Votação |
| CalendarioController | 13 | Calendário |
| ConselheiroController | 13 | Conselheiros |
| EleicaoController | 13 | Eleições |
| FilialController | 13 | Filiais |
| RelatorioController | 13 | Relatórios |
| AuditoriaController | 12 | Auditoria |
| MembroChapaController | 11 | Membros de Chapa |
| NotificacaoController | 11 | Notificações |
| DashboardController | 9 | Dashboard |
| PublicDenunciaController | 5 | Denúncia Pública |
| **Total** | **326** | |

#### Classificação dos Endpoints em EE/CE/SE

**Regra prática:**
- `[HttpPost]` que **cria/altera** dados → **EE (Entrada Externa)**
- `[HttpPut]`, `[HttpDelete]` → **EE (Entrada Externa)**
- `[HttpPost]` que muda **status/workflow** → **EE (Entrada Externa)**
- `[HttpGet]` que **lista/busca** → **CE (Consulta Externa)**
- `[HttpGet]` que gera **relatório com cálculos/PDF** → **SE (Saída Externa)**

#### Contagem por Módulo (processos elementares do usuário)

> **IMPORTANTE:** Agrupar endpoints que fazem parte do mesmo processo elementar do usuário. Ex: `GetById` e `GetByStatus` para a mesma entidade = 1 CE (não 2).

##### Módulo 1: Autenticação (AuthController — 21 endpoints)

| # | Transação | Tipo | FTR | DET | Complexidade | PF |
|---|-----------|------|-----|-----|--------------|-----|
| 1 | Login Admin (email+senha → token JWT) | EE | 2 | 6 | Média | 4 |
| 2 | Logout Admin | EE | 1 | 2 | Simples | 3 |
| 3 | Registro de usuário | EE | 2 | 12 | Média | 4 |
| 4 | Confirmar email | EE | 1 | 3 | Simples | 3 |
| 5 | Recuperar senha (forgot/reset) | EE | 2 | 5 | Média | 4 |
| 6 | Login Eleitor (CPF + código) | EE | 2 | 8 | Média | 4 |
| 7 | Login Candidato | EE | 2 | 8 | Média | 4 |
| 8 | Registro Candidato | EE | 3 | 15 | Complexa | 6 |
| 9 | Refresh Token | EE | 1 | 3 | Simples | 3 |
| 10 | Alterar senha | EE | 2 | 5 | Média | 4 |
| 11 | Consultar perfil logado | CE | 2 | 10 | Média | 4 |
| 12 | Verificar elegibilidade eleitor | CE | 2 | 6 | Média | 4 |
| 13 | Consultar info candidato | CE | 2 | 10 | Média | 4 |
| 14 | Verificar token | CE | 1 | 3 | Simples | 3 |
| | **Subtotal Auth** | | | | | **54** |

##### Módulo 2: Eleições (EleicaoController — 13 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Criar eleição | EE | Complexa | 6 |
| 2 | Alterar eleição | EE | Complexa | 6 |
| 3 | Excluir eleição | EE | Simples | 3 |
| 4 | Iniciar eleição | EE | Média | 4 |
| 5 | Suspender eleição | EE | Média | 4 |
| 6 | Cancelar eleição | EE | Média | 4 |
| 7 | Listar eleições (todas / por status / ativas) | CE | Média | 4 |
| 8 | Consultar eleição por ID | CE | Média | 4 |
| 9 | Verificar permissões (CanDelete, CanEdit) | CE | Simples | 3 |
| | **Subtotal** | | | **38** |

##### Módulo 3: Chapas (ChapasController — 19 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Criar chapa | EE | Complexa | 6 |
| 2 | Alterar chapa | EE | Complexa | 6 |
| 3 | Excluir chapa | EE | Simples | 3 |
| 4 | Adicionar membro | EE | Complexa | 6 |
| 5 | Alterar membro | EE | Complexa | 6 |
| 6 | Remover membro | EE | Simples | 3 |
| 7 | Submeter para análise | EE | Média | 4 |
| 8 | Deferir / Indeferir chapa | EE | Média | 4 |
| 9 | Solicitar documentos | EE | Média | 4 |
| 10 | Listar chapas (todas / por eleição) | CE | Média | 4 |
| 11 | Consultar chapa por ID | CE | Média | 4 |
| 12 | Listar membros de uma chapa | CE | Média | 4 |
| 13 | Listar status disponíveis | CE | Simples | 3 |
| | **Subtotal** | | | **57** |

##### Módulo 4: Denúncias (DenunciaController + PublicDenunciaController — 30 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Criar denúncia (admin) | EE | Complexa | 6 |
| 2 | Criar denúncia (público anônimo) | EE | Complexa | 6 |
| 3 | Alterar denúncia | EE | Complexa | 6 |
| 4 | Excluir denúncia | EE | Simples | 3 |
| 5 | Iniciar análise | EE | Média | 4 |
| 6 | Concluir análise | EE | Média | 4 |
| 7 | Aceitar admissibilidade | EE | Média | 4 |
| 8 | Rejeitar admissibilidade | EE | Média | 4 |
| 9 | Registrar defesa | EE | Complexa | 6 |
| 10 | Enviar para julgamento | EE | Média | 4 |
| 11 | Arquivar | EE | Média | 4 |
| 12 | Registrar recurso | EE | Complexa | 6 |
| 13 | Listar denúncias (por eleição / status / chapa) | CE | Média | 4 |
| 14 | Consultar por protocolo | CE | Média | 4 |
| 15 | Consultar por ID | CE | Média | 4 |
| 16 | Minhas denúncias | CE | Média | 4 |
| 17 | Consultar protocolo (público) | CE | Simples | 3 |
| 18 | Relatório de denúncias | SE | Complexa | 7 |
| | **Subtotal** | | | **83** |

##### Módulo 5: Impugnações (ImpugnacaoController — 43 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Criar impugnação | EE | Complexa | 6 |
| 2 | Alterar impugnação | EE | Complexa | 6 |
| 3 | Excluir impugnação | EE | Simples | 3 |
| 4 | Receber para análise | EE | Média | 4 |
| 5 | Iniciar análise | EE | Média | 4 |
| 6 | Abrir prazo para alegações | EE | Média | 4 |
| 7 | Registrar alegação | EE | Complexa | 6 |
| 8 | Abrir prazo para contra-alegações | EE | Média | 4 |
| 9 | Registrar contra-alegação | EE | Complexa | 6 |
| 10 | Enviar para julgamento | EE | Média | 4 |
| 11 | Julgar impugnação | EE | Complexa | 6 |
| 12 | Registrar recurso | EE | Complexa | 6 |
| 13 | Julgar recurso | EE | Complexa | 6 |
| 14 | Deferir / Indeferir | EE | Média | 4 |
| 15 | Arquivar | EE | Média | 4 |
| 16 | Listar (por eleição / chapa / status) | CE | Média | 4 |
| 17 | Consultar por ID | CE | Média | 4 |
| 18 | Consultar por protocolo | CE | Média | 4 |
| 19 | Relatório de impugnações | SE | Complexa | 7 |
| | **Subtotal** | | | **96** |

##### Módulo 6: Julgamentos (JulgamentoController — 16 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Criar julgamento | EE | Complexa | 6 |
| 2 | Alterar julgamento | EE | Complexa | 6 |
| 3 | Excluir julgamento | EE | Simples | 3 |
| 4 | Iniciar julgamento | EE | Média | 4 |
| 5 | Suspender / Retomar | EE | Média | 4 |
| 6 | Registrar voto de membro | EE | Complexa | 6 |
| 7 | Concluir julgamento | EE | Complexa | 6 |
| 8 | Criar sessão de julgamento | EE | Complexa | 6 |
| 9 | Listar julgamentos | CE | Média | 4 |
| 10 | Consultar por ID | CE | Média | 4 |
| 11 | Listar membros da comissão | CE | Média | 4 |
| 12 | Listar por eleição / agendados | CE | Média | 4 |
| | **Subtotal** | | | **57** |

##### Módulo 7: Votação (VotacaoController — 14 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Registrar voto | EE | Complexa | 6 |
| 2 | Anular voto | EE | Média | 4 |
| 3 | Abrir / Fechar votação | EE | Média | 4 |
| 4 | Verificar elegibilidade | CE | Média | 4 |
| 5 | Obter cédula | CE | Média | 4 |
| 6 | Obter comprovante | CE | Média | 4 |
| 7 | Listar chapas para votação | CE | Média | 4 |
| 8 | Estatísticas de votação | CE | Complexa | 6 |
| | **Subtotal** | | | **36** |

##### Módulo 8: Apuração (ApuracaoController — 19 endpoints)

| # | Transação | Tipo | Complexidade | PF |
|---|-----------|------|--------------|-----|
| 1 | Iniciar apuração | EE | Complexa | 6 |
| 2 | Pausar / Retomar | EE | Média | 4 |
| 3 | Finalizar apuração | EE | Complexa | 6 |
| 4 | Homologar resultado | EE | Complexa | 6 |
| 5 | Publicar resultado | EE | Média | 4 |
| 6 | Apurar votos por eleição | EE | Complexa | 6 |
| 7 | Gerar ata de apuração | SE | Complexa | 7 |
| 8 | Gerar boletim de urna | SE | Complexa | 7 |
| 9 | Resultado parcial | SE | Complexa | 7 |
| 10 | Resultado final | SE | Complexa | 7 |
| 11 | Status da apuração | CE | Complexa | 6 |
| | **Subtotal** | | | **66** |

##### Módulos 9-16 (Demais)

| Módulo | EE | CE | SE | PF EE | PF CE | PF SE | **PF Total** |
|--------|----|----|-----|-------|-------|-------|-------------|
| Documentos (CRUD + upload + workflow) | 5 | 4 | 0 | 22 | 16 | 0 | **38** |
| Usuários (CRUD + perfil + roles) | 5 | 5 | 0 | 22 | 20 | 0 | **42** |
| Calendário (CRUD + workflow) | 5 | 4 | 0 | 22 | 16 | 0 | **38** |
| Dashboard (9 consultas complexas) | 0 | 6 | 0 | 0 | 36 | 0 | **36** |
| Relatórios (geração PDF/Excel) | 0 | 0 | 10 | 0 | 0 | 65 | **65** |
| Auditoria (consultas + exportação) | 1 | 5 | 1 | 3 | 20 | 7 | **30** |
| Configuração (10 tipos de config) | 6 | 2 | 0 | 24 | 8 | 0 | **32** |
| Notificação (envio + CRUD) | 5 | 3 | 0 | 20 | 12 | 0 | **32** |
| Filial / Regional | 4 | 4 | 0 | 18 | 16 | 0 | **34** |
| Conselheiro (CRUD + mandato) | 5 | 4 | 0 | 22 | 16 | 0 | **38** |
| Membros Chapa (CRUD + validação) | 4 | 3 | 0 | 18 | 12 | 0 | **30** |
| **Subtotal Demais** | **40** | **40** | **11** | | | | **415** |

---

### 3.3 Passo 3: Totalizar a Contagem Não Ajustada

| Tipo de Função | Quantidade | PF |
|----------------|-----------|-----|
| **ALI** (16 agrupados IFPUG) | 16 | 204 |
| **AIE** | 3 | 15 |
| **EE** (Entradas Externas) | ~106 | ~466 |
| **CE** (Consultas Externas) | ~77 | ~316 |
| **SE** (Saídas Externas) | ~17 | ~110 |
| **TOTAL NÃO AJUSTADO** | **~219** | **~1.111 PF** |

---

### 3.4 Passo 4: Calcular o Fator de Ajuste (VAF)

Avaliar as 14 Características Gerais do Sistema (CGS), grau 0 a 5:

| # | Característica | Grau Sugerido | Justificativa |
|---|----------------|---------------|---------------|
| 1 | Comunicação de dados | **5** | API REST + 2 frontends SPA + AWS services |
| 2 | Processamento distribuído | **4** | ECS Fargate + RDS + S3 + CloudFront |
| 3 | Desempenho | **3** | Importante mas não é real-time |
| 4 | Configuração de uso | **3** | ECS com auto-scaling |
| 5 | Volume de transações | **3** | Votação em massa (periódica) |
| 6 | Entrada de dados on-line | **5** | 100% web, votação online |
| 7 | Eficiência do usuário | **4** | SPA React, UX moderna, shadcn/ui |
| 8 | Atualização on-line | **4** | CRUDs completos, state machines |
| 9 | Processamento complexo | **4** | Apuração, workflows judiciais |
| 10 | Reusabilidade | **3** | Clean Architecture, componentes |
| 11 | Facilidade de instalação | **3** | Docker, Terraform, CI/CD |
| 12 | Facilidade de operação | **3** | Health checks, Serilog |
| 13 | Múltiplos locais | **4** | 27 regionais + CAU Nacional |
| 14 | Facilidade de mudança | **3** | Modular, EF Core migrations |
| | **Total DI** | **51** | |

```
VAF = (51 × 0,01) + 0,65 = 1,16
```

---

### 3.5 Passo 5: Calcular PF Ajustados

```
PF Ajustados = PF Não Ajustados × VAF
PF Ajustados = 1.111 × 1,16 = 1.289 PF
```

---

## 4. Resumo da Contagem Esperada

| Métrica | Valor |
|---------|-------|
| Funções de Dados | 19 (16 ALI + 3 AIE) |
| Funções Transacionais | ~200 (106 EE + 77 CE + 17 SE) |
| **PF Não Ajustados** | **~1.111** |
| **VAF** | **1,16** |
| **PF Ajustados** | **~1.289** |

---

## 5. Evidências para Verificação Independente

### 5.1 Scripts de Verificação

Disponibilizamos scripts que podem ser executados sobre o código-fonte para confirmar os números:

```bash
# Contar entidades de domínio
find apps/api/CAU.Eleitoral.Domain/Entities -name "*.cs" | \
  xargs grep -l "BaseEntity" | wc -l
# Resultado esperado: 156

# Contar controllers funcionais (excluindo BaseController)
find apps/api/CAU.Eleitoral.Api/Controllers -name "*Controller.cs" \
  ! -name "BaseController.cs" | wc -l
# Resultado esperado: 20

# Contar endpoints HTTP
grep -rn '\[Http' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | wc -l
# Resultado esperado: 326

# Contar services de aplicação
find apps/api/CAU.Eleitoral.Application/Services -name "*Service.cs" | wc -l
# Resultado esperado: 15

# Contar páginas Admin
find apps/admin/src/pages -name "*.tsx" | wc -l
# Resultado esperado: ~40

# Contar páginas Public
find apps/public/src/pages -name "*.tsx" | wc -l
# Resultado esperado: ~31
```

### 5.2 Acesso ao Código

O repositório está em: **https://github.com/brunohelius/cau-eleitoral-migrado**

Branch principal: `main`

### 5.3 Sistema em Produção

| Ambiente | URL |
|----------|-----|
| Admin | https://cau-admin.migrai.com.br |
| Portal Público | https://cau-public.migrai.com.br |
| API Health Check | https://cau-api.migrai.com.br/health |
| Swagger (API Docs) | https://cau-api.migrai.com.br/swagger |

### 5.4 Documentação APF Existente

| Documento | Localização |
|-----------|-------------|
| Contagem detalhada (entity-per-ALI) | `docs/contagem-apf.md` |
| Contagem parte 1 | `docs/contagem-apf-parte1.md` |
| Contagem parte 2 | `docs/contagem-apf-parte2.md` |
| Snapshot recontagem | `docs/contagem-apf-snapshot-2026-02-19.md` |
| Justificativa do gap | `docs/justificativa-gap-apf.md` |
| PDF consolidado | `docs/contagem-apf-consolidado.pdf` |

---

## 6. Pontos de Atenção para a Contagem

> [!IMPORTANT]
> **NÃO contar apenas CRUD por módulo.** Cada transição de estado/workflow é um processo elementar separado. Ex: "Submeter chapa" (Rascunho → Submetida) é diferente de "Deferir chapa" (Em Análise → Deferida).

> [!WARNING]
> **NÃO excluir módulos auxiliares.** Dashboard (9 consultas complexas), Relatórios (13 tipos), Auditoria e Configuração são funcionalidades reais do sistema com endpoints distintos.

> [!CAUTION]
> **NÃO esquecer o Portal Público.** O Frontend Public tem funcionalidades separadas (denúncia anônima, votação online, calendário, FAQ, consulta de protocolo) que devem ser contadas.

### 6.1 Workflows de Status (State Machines)

O sistema tem máquinas de estado complexas que geram múltiplas EEs:

| Entidade | Estados | Transições (EEs) |
|----------|---------|-------------------|
| Eleição | Criada → Ativa → Suspensa → Cancelada → Finalizada | 5 |
| Chapa | Rascunho → Submetida → Em Análise → Deferida/Indeferida → Recurso | 7+ |
| Denúncia | Recebida → Em Análise → Admitida → Com Defesa → Em Julgamento → Concluída | 6+ |
| Impugnação | Criada → Recebida → Em Análise → Alegações → Contra-alegações → Julgamento → Recurso → Conclusão | 8+ |

### 6.2 Múltiplos Perfis de Usuário

| Perfil | Funcionalidades Próprias |
|--------|------------------------|
| Administrador | Todas as funções admin |
| Comissão Eleitoral | Gestão de eleições, julgamentos |
| Eleitor | Votação, consulta de chapas |
| Candidato | Registro de chapa, defesa |
| Público anônimo | Denúncia anônima, consulta protocolo |

Cada perfil tem **fluxos de autenticação distintos** (Login Admin ≠ Login Eleitor ≠ Login Candidato).

---

## 7. Proposta de Sessão Conjunta

Sugerimos uma sessão de contagem conjunta, módulo por módulo, onde:

1. Abrimos cada controller no código
2. Listamos os endpoints (métodos `[HttpGet/Post/Put/Delete]`)
3. Classificamos juntos cada endpoint como EE, CE ou SE
4. Determinamos FTR e DET
5. Chegamos a um número consensual

Isso garante transparência e rastreabilidade na contagem.

---

> **Contato para dúvidas ou acesso ao código:** Bruno Souza (brunohelius)
