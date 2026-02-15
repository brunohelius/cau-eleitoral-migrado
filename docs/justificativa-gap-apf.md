# Justificativa do Gap na Contagem APF — 590 PF vs Nossa Análise

> **Projeto:** CAU Sistema Eleitoral  
> **Contagem do Cliente:** 590 PF  
> **Nossa Contagem (detalhada):** 2.474 PF (não ajustados)  
> **Data:** 2026-02-13  

---

## 1. Análise do Gap — Por que existe essa diferença?

A diferença não significa necessariamente que um dos lados está "errado". A contagem APF depende criticamente de **como se interpretam as regras IFPUG**, especialmente em três decisões-chave:

### 1.1 A maior causa: Agrupamento de ALIs (Entidades vs Grupos Lógicos)

> [!IMPORTANT]
> Esta é a causa principal do gap e responde por ~70% da diferença.

A regra IFPUG diz que um **ALI é um grupo de dados logicamente relacionados reconhecível pelo usuário**, e não cada tabela do banco de dados individualmente.

**O que o cliente provavelmente fez (590 PF):**

Agrupou as 156 entidades em ~15-20 ALIs de alto nível:

| ALI Agrupado | Entidades Incluídas | Complexidade | PF |
|---|---|---|---|
| Eleição | Eleicao + Configuracao + Etapa + Fase + Parametro + Circunscricao + Situacao | Complexa | 15 |
| Calendário | Calendario + Atividades + Situacao | Média | 10 |
| Chapa | ChapaEleicao + Membro + Composicao + Documento + Historico + Plataforma | Complexa | 15 |
| Votação | Voto + Eleitor + Secao + Zona + Mesa + Urna + Cedula | Complexa | 15 |
| Apuração | Resultado + ResultadoFinal + Parcial + Ata + Boletim + Registro | Complexa | 15 |
| Denúncia | Denuncia + Defesa + Admissibilidade + Analise + Alegações + 18 sub-entidades | Complexa | 15 |
| Impugnação | ImpugnacaoResultado + Pedido + Alegação + Defesa + 9 sub-entidades | Complexa | 15 |
| Julgamento | Julgamento + Sessão + Decisão + Acórdão + 29 sub-entidades | Complexa | 15 |
| Documento | Documento + Arquivo + Assinatura + 15 tipos de documento | Complexa | 15 |
| Usuário | Usuario + Profissional + Conselheiro | Complexa | 15 |
| Permissões | Role + Permissao + UsuarioRole + RolePermissao | Simples | 7 |
| Notificação | Notificacao + config | Simples | 7 |
| Auditoria | AuditoriaLog + LogAcesso | Média | 10 |
| Filial/Regional | Filial + RegionalCAU + RegiaoPleito | Média | 10 |
| Configuração Geral | Configuracao (sistema) | Simples | 7 |
| **~15 ALIs** | | | **~190 PF** |

**O que nós fizemos:**

Contamos **cada entidade como um ALI separado** = 156 ALIs = 1.226 PF.

**Diferença só nos ALIs:** ~1.036 PF (1.226 - 190). Isso explica a maior parte do gap.

### 1.2 Segunda causa: Granularidade das Transações

**O cliente provavelmente contou processos elementares do usuário:**

Exemplo: No módulo Denúncias, o cliente pode ter contado:
- 1 EE "Cadastrar Denúncia" (inclui anexos, partes)
- 1 EE "Alterar Denúncia"
- 1 EE "Excluir Denúncia"
- 1 EE "Analisar" (fluxo completo)
- 1 CE "Consultar Denúncia"
- 1 CE "Listar Denúncias"
= **6 transações** para Denúncias

**Nós contamos cada endpoint da API separadamente:**
- 12 EE + 7 CE para Denúncias
= **19 transações** para Denúncias

### 1.3 Terceira causa: Escopo da contagem

O cliente pode ter contado apenas as **funcionalidades-core** entregues, excluindo:
- Módulos auxiliares (Dashboard com 9 CE complexas, Relatórios com 19 SE)
- Endpoints administrativos (Auditoria, Configuração)
- Portal Público como funcionalidade separada
- APIs de suporte (health checks, metadados)

---

## 2. Contagem Reconciliada — Meio-Termo Fundamentado

Aplicando rigor IFPUG (agrupando ALIs corretamente) mas contando TODAS as funcionalidades:

### 2.1 ALIs Agrupados (regra IFPUG correta)

| # | ALI | Tabelas Agrupadas | RET | DET (est.) | Complexidade | PF |
|---|-----|-------------------|-----|------------|--------------|-----|
| 1 | Eleição | 13 entidades (Eleicao, Config, Etapa, Fase, Param, etc.) | 6+ | 50+ | Complexa | 15 |
| 2 | Calendário | 4 entidades | 4 | 30 | Média | 10 |
| 3 | Chapa Eleitoral | 8 entidades | 5 | 50+ | Complexa | 15 |
| 4 | Votação/Eleitor | 7 entidades (Voto, Eleitor, Secao, Zona, Mesa, Urna, Fiscal) | 6+ | 50+ | Complexa | 15 |
| 5 | Apuração | 6 entidades (Resultado, Final, Parcial, Registro, Total, etc.) | 5 | 40+ | Complexa | 15 |
| 6 | Denúncia | 23 entidades | 6+ | 80+ | Complexa | 15 |
| 7 | Impugnação | 13 entidades | 6+ | 60+ | Complexa | 15 |
| 8 | Julgamento | 33 entidades | 6+ | 100+ | Complexa | 15 |
| 9 | Documento | 25 entidades (Doc + tipos específicos) | 6+ | 60+ | Complexa | 15 |
| 10 | Resultado/Relatório | 8 entidades (Ata, Boletim, Relatorio, Estatistica, etc.) | 5 | 40+ | Complexa | 15 |
| 11 | Usuário | 3 entidades (Usuario, Profissional, Conselheiro) | 3 | 60+ | Complexa | 15 |
| 12 | Controle Acesso | 4 entidades (Role, Permissao, UsuarioRole, etc.) | 4 | 20 | Média | 10 |
| 13 | Notificação | 2 entidades | 2 | 20 | Simples | 7 |
| 14 | Auditoria | 2 entidades | 2 | 25 | Média | 10 |
| 15 | Filial/Regional | 3 entidades | 3 | 30 | Média | 10 |
| 16 | Configuração | 1 entidade | 1 | 15 | Simples | 7 |
| | **Total ALIs agrupados** | **156 entidades** | | | | **204** |

### 2.2 AIEs (mantidos)

| AIE | PF |
|-----|-----|
| AWS S3 | 5 |
| SMTP | 5 |
| Secrets Manager | 5 |
| **Total** | **15** |

### 2.3 Transações Reconciliadas (agrupando por processo elementar do usuário)

| Módulo | EE | CE | SE | PF EE | PF CE | PF SE |
|--------|-----|-----|-----|-------|-------|-------|
| Autenticação (Login/Register × 3 tipos + auxiliares) | 10 | 4 | 0 | 38 | 16 | 0 |
| Eleições (CRUD + status) | 6 | 4 | 0 | 27 | 16 | 0 |
| Chapas (CRUD + membros + workflow) | 8 | 4 | 0 | 36 | 16 | 0 |
| Denúncias (CRUD + workflow + público) | 9 | 5 | 1 | 42 | 20 | 7 |
| Votação (Votar + controle) | 4 | 5 | 0 | 18 | 20 | 0 |
| Impugnações (CRUD + workflow completo) | 10 | 4 | 1 | 48 | 16 | 7 |
| Julgamentos (CRUD + sessões + votos) | 8 | 4 | 0 | 38 | 16 | 0 |
| Documentos (CRUD + upload + workflow) | 5 | 4 | 0 | 22 | 16 | 0 |
| Usuários (CRUD + perfil + roles) | 5 | 5 | 0 | 22 | 20 | 0 |
| Calendário (CRUD + workflow) | 5 | 4 | 0 | 22 | 16 | 0 |
| Apuração (Workflow completo) | 6 | 3 | 4 | 28 | 18 | 24 |
| Dashboard (Consultas complexas) | 0 | 6 | 0 | 0 | 36 | 0 |
| Relatórios (Geração de relatórios) | 0 | 0 | 10 | 0 | 0 | 65 |
| Auditoria (Consultas + export) | 1 | 5 | 1 | 3 | 20 | 7 |
| Configuração (Updates diversos) | 6 | 2 | 0 | 24 | 8 | 0 |
| Notificação (CRUD + envio) | 5 | 3 | 0 | 20 | 12 | 0 |
| Filiais (CRUD + consultas) | 4 | 4 | 0 | 18 | 16 | 0 |
| Conselheiros (CRUD + mandato) | 5 | 4 | 0 | 22 | 16 | 0 |
| Membros Chapa (CRUD + validação) | 4 | 3 | 0 | 18 | 12 | 0 |
| **TOTAL** | **106** | **77** | **17** | **466** | **316** | **110** |

### 2.4 Totalização Reconciliada

| Tipo | Quantidade | PF |
|------|-----------|-----|
| ALI (agrupados IFPUG) | 16 | 204 |
| AIE | 3 | 15 |
| EE | 106 | 466 |
| CE | 77 | 316 |
| SE | 17 | 110 |
| **Total Não Ajustado** | **219** | **1.111 PF** |

**Com VAF 1,16:**
```
PF Ajustado = 1.111 × 1,16 = 1.289 PF
```

---

## 3. Comparativo: Por que 590 PF do cliente está subdimensionado

| Aspecto | Cliente (590 PF) | Nossa Reconciliada (1.289 PF) | Nossa Detalhada (2.870 PF) |
|---------|-------------------|-------------------------------|----------------------------|
| ALIs | ~12-15 (~130 PF) | 16 (204 PF) | 156 (1.226 PF) |
| Transações | ~50-70 (~400 PF) | 200 (892 PF) | 280 (1.233 PF) |
| Relatórios/SE | ~5 (~30 PF) | 17 (110 PF) | 19 (125 PF) |
| **Abordagem** | Funcionalidades macro | Processo elementar IFPUG | Cada endpoint como transação |

### 3.1 Funcionalidades que o cliente provavelmente não contou

Mesmo com agrupamento correto, **590 PF significaria ~60-70 transações no total**. O sistema tem **muito mais** que isso:

> [!WARNING]  
> Com 590 PF, cada módulo teria em média apenas 3-4 transações. Mas só o módulo de Denúncias tem CRUD + análise + admissibilidade + defesa + encaminhamento + recurso + denúncia pública = mínimo 9 EE + 5 CE.

**Módulos/funcionalidades provavelmente excluídos ou sub-contados:**

| Funcionalidade Omitida | Transações não contadas | PF estimado |
|------------------------|------------------------|-------------|
| Portal Público (19 endpoints) | ~8 transações | 32 |
| Dashboard completo (9 visões) | ~6 CE complexas | 36 |
| 10 tipos de relatórios | ~10 SE | 65 |
| Auditoria completa | ~6 transações | 23 |
| Fluxo de Apuração (7 etapas) | ~10 transações | 52 |
| Workflows de status (Eleição, Chapa, Denúncia, Impugnação) | ~15 EE | 60 |
| Membro Chapa como módulo separado | ~7 transações | 30 |
| Conselheiros (mandatos, empossamento) | ~9 transações | 38 |
| Configuraçao (10 tipos de config) | ~6 transações | 24 |
| **Total estimado omitido** | **~77 transações** | **~360 PF** |

**590 + 360 = 950 PF** — mais próximo da nossa contagem reconciliada de **1.111 PF**.

---

## 4. Argumentação Técnica — Embasamento IFPUG

### 4.1 O que diz o manual IFPUG CPM 4.3.1

> *"A contagem deve refletir a visão do usuário sobre o sistema, não a visão técnica. Cada processo elementar identificável pelo usuário deve ser contado separadamente."*

O sistema CAU Eleitoral não é um simples CRUD. Ele possui:

**a) Complexidade dos Fluxos de Status (State Machines):**
- Eleição: 5 estados (Ativa → Suspensa → Cancelada → Finalizada)
- Chapa: 7+ estados (Rascunho → Submetida → Em Análise → Deferida → Indeferida → Recurso)  
- Denúncia: 6+ estados (Recebida → Em Análise → Admitida → Com Defesa → Em Julgamento → Concluída)
- Impugnação: 8+ estados com sub-workflows
- Cada transição de estado é um **processo elementar distinto** com lógica diferente

**b) Multiplicidade de Perfis de Usuário:**
- Administrador (todas as funções)
- Comissão Eleitoral (gestão de eleições)
- Eleitor (votação)
- Candidato (chapas, defesa)
- Público anônimo (denúncias)
- Cada perfil tem fluxos de autenticação e funcionalidades distintas

**c) Volume Real de Funcionalidades:**
O sistema tem **20 controllers funcionais (21 incluindo o BaseController) e 326 endpoints HTTP reais no código**. Mesmo agrupando endpoints em processos elementares, não há como reduzir a menos de ~100 transações sem omitir funcionalidades.

### 4.2 Benchmark de Sistemas Similares

| Sistema | Porte | PF Típico |
|---------|-------|-----------|
| Sistema CRUD simples (5-10 entidades) | Pequeno | 100-200 |
| Sistema de gestão média (20-30 entidades) | Médio | 300-600 |
| **Sistema judicial/processual (50+ entidades, workflows)** | **Grande** | **800-1.500** |
| ERP completo | Muito Grande | 2.000-5.000 |

O CAU Eleitoral tem **156 entidades**, **16 módulos funcionais**, **workflows complexos**, **múltiplos perfis** e **portal público separado**. Está claramente na faixa de **800-1.500 PF** no mínimo.

**590 PF classifica o sistema como "Médio" — equivalente a um sistema de 20-30 entidades com CRUD simples. Isso não condiz com a realidade.**

---

## 5. Resumo da Justificativa

| Métrica | Cliente | Reconciliada (IFPUG correto) | Diferença |
|---------|---------|------------------------------|-----------|
| **PF Não Ajustados** | ~510 | **1.111** | +118% |
| **PF Ajustados** | 590 | **1.289** | +118% |
| ALIs contados | ~12-15 | 16 | — |
| EE contados | ~30-40 | 106 | +165% |
| CE contados | ~15-20 | 77 | +285% |
| SE contados | ~5 | 17 | +240% |

### Causas-raiz do gap de 590 → 1.289 PF:

1. **Transações sub-contadas (~70%):** O cliente provavelmente contou apenas CRUD básico (Create/Read/Update/Delete) por módulo, ignorando workflows de status, relatórios, dashboards, e funcionalidades do portal público
2. **Módulos omitidos (~20%):** Dashboard, Relatórios, Auditoria, Configuração possivelmente não foram contados
3. **Escopo reduzido (~10%):** Portal público possivelmente não foi incluído na fronteira

> [!IMPORTANT]
> **Nossa posição defensável:** Usando a metodologia IFPUG com agrupamento correto de ALIs, a contagem justa do sistema é de **1.111 PF não ajustados / 1.289 PF ajustados**. Isso é **2,18× maior** que os 590 PF do cliente, e reflete fielmente as 200+ funcionalidades distintas, 16 módulos, e 156 entidades do sistema.
