# Guia de Contagem APF — CAU Sistema Eleitoral

> **Para:** Equipe Delta (contagem independente de Pontos de Função)  
> **Projeto:** CAU Sistema Eleitoral Migrado  
> **Metodologia:** IFPUG CPM 4.3.1  
> **Tipo de Contagem:** Contagem de Aplicação (baseline)  
> **Data do Snapshot:** 19/02/2026 — Commit `9e21bf1`  
> **Resultado Esperado:** **2.474 PF não ajustados / 2.870 PF ajustados**

---

## 1. Introdução

Este guia fornece as instruções passo-a-passo para que a equipe Delta realize uma contagem independente de Pontos de Função (APF) do **sistema completo** CAU Eleitoral. A contagem cobre **todos os 16 módulos funcionais** do sistema (não apenas o Portal do Candidato).

> **ATENÇÃO:** A contagem anterior da Deltapoint (590 PF) cobriu apenas o escopo do Portal do Candidato (26 funções, 124 PF). Esta contagem abrange o **sistema inteiro**: Admin + Público + API completa.

---

## 2. Visão Geral do Sistema

### 2.1 Descrição

Sistema de gestão **completa** de processos eleitorais do Conselho de Arquitetura e Urbanismo (CAU):
- Gestão de eleições, chapas, votação e apuração
- Denúncias, impugnações e julgamentos eleitorais (fluxos judiciais completos)
- Documentos oficiais (editais, atas, diplomas, termos de posse, certificados)
- Portal público para denúncias anônimas e votação online
- Dashboard com 9 visões, relatórios em PDF/Excel e auditoria completa

### 2.2 Fronteira da Aplicação

| Dentro da fronteira | Fora da fronteira (AIE) |
|---------------------|------------------------|
| API Backend (.NET 8 Web API) | AWS S3 (storage) |
| Frontend Admin (React 18) | SMTP (email) |
| Frontend Public (React 18) | AWS Secrets Manager |
| Banco PostgreSQL (RDS) | |

### 2.3 Os 16 Módulos Funcionais

| # | Módulo | Descrição |
|---|--------|-----------|
| 1 | **Core Eleitoral** | Eleições, calendários, configurações, circunscrições, etapas |
| 2 | **Chapas e Membros** | Chapas eleitorais, composição, documentos, plataformas |
| 3 | **Votação** | Registro de votos, cédulas, elegibilidade, comprovantes |
| 4 | **Apuração** | Contagem de votos, resultados parciais/finais, homologação |
| 5 | **Denúncias** | Denúncias eleitorais, análise, admissibilidade, defesa |
| 6 | **Impugnações** | Impugnação de resultados, alegações, recursos |
| 7 | **Julgamentos** | Comissões julgadoras, sessões, votos, acórdãos |
| 8 | **Documentos** | Editais, resoluções, atas, termos, certificados |
| 9 | **Usuários** | Usuários, profissionais, conselheiros, permissões |
| 10 | **Auditoria** | Logs de auditoria, rastreabilidade |
| 11 | **Notificações** | Notificações, configurações de notificação |
| 12 | **Dashboard/Relatórios** | Estatísticas, KPIs, relatórios em PDF/Excel |
| 13 | **Autenticação** | Login admin, eleitor, candidato, JWT, refresh token |
| 14 | **Configuração** | Configurações do sistema, email, segurança, votação |
| 15 | **Filiais/Regionais** | Regionais do CAU, UFs, profissionais por filial |
| 16 | **Conselheiros** | Empossamento, mandatos, afastamento |

### 2.4 Onde Encontrar no Código

| Artefato | Caminho |
|----------|---------|
| Entidades de domínio | `apps/api/CAU.Eleitoral.Domain/Entities/` |
| Controllers (API) | `apps/api/CAU.Eleitoral.Api/Controllers/` |
| Services | `apps/api/CAU.Eleitoral.Application/Services/` |
| DTOs | `apps/api/CAU.Eleitoral.Application/DTOs/` |
| Páginas Admin | `apps/admin/src/pages/` |
| Páginas Public | `apps/public/src/pages/` |
| Repositório | `https://github.com/brunohelius/cau-eleitoral-migrado` (branch `main`) |

---

## 3. Passo 1 — Funções de Dados (ALI e AIE)

### 3.1 Regra Aplicada: Cada Entidade = 1 ALI

Cada entidade de domínio que herda de `BaseEntity` no código é contada como um ALI separado. O sistema tem **156 entidades** organizadas em 7 módulos.

#### Como verificar no código:

```bash
# Listar todas as entidades
find apps/api/CAU.Eleitoral.Domain/Entities -name "*.cs" | \
  xargs grep -l "BaseEntity" | wc -l
# Resultado esperado: 156

# Ver por módulo
for dir in Chapas Core Denuncias Documentos Impugnacoes Julgamentos Usuarios; do
  count=$(find "apps/api/CAU.Eleitoral.Domain/Entities/$dir" -name "*.cs" | \
    xargs grep -l "BaseEntity" | wc -l)
  echo "$dir: $count"
done
```

### 3.2 Entidades por Módulo

| Módulo | Entidades | PF |
|--------|-----------|-----|
| Core Eleitoral | 27 | 214 |
| Chapas | 8 | 67 |
| Denúncias | 23 | 171 |
| Documentos | 43 | 329 |
| Impugnações | 13 | 101 |
| Julgamentos | 33 | 257 |
| Usuários | 9 | 87 |
| **TOTAL ALIs** | **156** | **1.226** |

### 3.3 Detalhamento — Módulo Core Eleitoral (27 ALIs)

| # | ALI | RET | DET est. | Complexidade | PF |
|---|-----|-----|----------|--------------|-----|
| 1 | Eleicao | 3 | 25+ | Complexa | 15 |
| 2 | Calendario | 2 | 15 | Simples | 7 |
| 3 | ConfiguracaoEleicao | 2 | 20+ | Média | 10 |
| 4 | Configuracao | 1 | 10 | Simples | 7 |
| 5 | Circunscricao | 1 | 8 | Simples | 7 |
| 6 | EleicaoSituacao | 1 | 5 | Simples | 7 |
| 7 | CalendarioSituacao | 1 | 5 | Simples | 7 |
| 8 | AtividadePrincipalCalendario | 1 | 10 | Simples | 7 |
| 9 | AtividadeSecundariaCalendario | 1 | 8 | Simples | 7 |
| 10 | EtapaEleicao | 1 | 8 | Simples | 7 |
| 11 | FaseEleicaoConfig | 1 | 10 | Simples | 7 |
| 12 | TipoEleicaoConfig | 1 | 8 | Simples | 7 |
| 13 | ParametroEleicao | 1 | 10 | Simples | 7 |
| 14 | Eleitor | 2 | 15 | Simples | 7 |
| 15 | Voto | 2 | 12 | Simples | 7 |
| 16 | SecaoEleitoral | 1 | 8 | Simples | 7 |
| 17 | ZonaEleitoral | 1 | 8 | Simples | 7 |
| 18 | MesaReceptora | 1 | 10 | Simples | 7 |
| 19 | UrnaEletronica | 1 | 10 | Simples | 7 |
| 20 | FiscalEleicao | 1 | 8 | Simples | 7 |
| 21 | RegiaoPleito | 1 | 8 | Simples | 7 |
| 22 | RegionalCAU | 1 | 12 | Simples | 7 |
| 23 | Filial | 2 | 15 | Simples | 7 |
| 24 | Notificacao | 2 | 15 | Simples | 7 |
| 25 | AuditoriaLog | 2 | 20+ | Média | 10 |
| 26 | ApuracaoResultado | 3 | 25+ | Complexa | 15 |
| 27 | ApuracaoResultadoChapa | 2 | 15 | Simples | 7 |
| | **Subtotal Core** | | | | **214** |

### 3.4 Detalhamento — Módulo Chapas (8 ALIs)

| # | ALI | RET | DET est. | Complexidade | PF |
|---|-----|-----|----------|--------------|-----|
| 28 | ChapaEleicao | 3 | 25+ | Complexa | 15 |
| 29 | MembroChapa | 2 | 20+ | Média | 10 |
| 30 | ComposicaoChapa | 1 | 10 | Simples | 7 |
| 31 | DocumentoChapa | 2 | 12 | Simples | 7 |
| 32 | HistoricoChapaEleicao | 1 | 10 | Simples | 7 |
| 33 | PlataformaEleitoral | 1 | 12 | Simples | 7 |
| 34 | SubstituicaoMembroChapa | 2 | 15 | Simples | 7 |
| 35 | ConfirmacaoMembroChapa | 1 | 8 | Simples | 7 |
| | **Subtotal Chapas** | | | | **67** |

### 3.5 Detalhamento — Módulo Denúncias (23 ALIs)

| # | ALI | RET | DET est. | Complexidade | PF |
|---|-----|-----|----------|--------------|-----|
| 36 | Denuncia | 4 | 30+ | Complexa | 15 |
| 37 | DenunciaChapa | 2 | 10 | Simples | 7 |
| 38 | DenunciaMembro | 2 | 10 | Simples | 7 |
| 39 | DefesaDenuncia | 2 | 15 | Simples | 7 |
| 40 | AdmissibilidadeDenuncia | 2 | 15 | Simples | 7 |
| 41 | AlegacoesDenuncia | 1 | 12 | Simples | 7 |
| 42 | ContraAlegacoesDenuncia | 1 | 12 | Simples | 7 |
| 43 | AnaliseDenuncia | 2 | 15 | Simples | 7 |
| 44 | AnexoDenuncia | 1 | 8 | Simples | 7 |
| 45 | ArquivoDefesa | 1 | 8 | Simples | 7 |
| 46 | ArquivoDenuncia | 1 | 8 | Simples | 7 |
| 47 | ContrarrazoesRecursoDenuncia | 1 | 10 | Simples | 7 |
| 48 | DespachoDenuncia | 1 | 10 | Simples | 7 |
| 49 | EncaminhamentoDenuncia | 1 | 10 | Simples | 7 |
| 50 | HistoricoDenuncia | 1 | 10 | Simples | 7 |
| 51 | JulgamentoDenuncia | 2 | 20+ | Média | 10 |
| 52 | JulgamentoRecursoDenuncia | 2 | 15 | Simples | 7 |
| 53 | NotificacaoDenuncia | 1 | 10 | Simples | 7 |
| 54 | ParecerDenuncia | 2 | 15 | Simples | 7 |
| 55 | ProvaDenuncia | 1 | 8 | Simples | 7 |
| 56 | RecursoDenuncia | 2 | 15 | Simples | 7 |
| 57 | VistaDenuncia | 1 | 8 | Simples | 7 |
| 58 | VotacaoJulgamentoDenuncia | 2 | 12 | Simples | 7 |
| | **Subtotal Denúncias** | | | | **171** |

### 3.6 Detalhamento — Módulo Documentos (43 ALIs)

| # | ALI | Complexidade | PF |
|---|-----|--------------|-----|
| 59 | Documento | Complexa | 15 |
| 60 | ArquivoDocumento | Simples | 7 |
| 61 | AssinaturaDigital | Simples | 7 |
| 62 | CertificadoDigital | Simples | 7 |
| 63 | CarimboTempo | Simples | 7 |
| 64 | Edital | Média | 10 |
| 65 | Resolucao | Simples | 7 |
| 66-82 | Ato, Aviso, Comunicado, Convocacao, Declaracao, Deliberacao, Diploma, Normativa, Portaria, Publicacao, PublicacaoOficial, Termo, TermoPosse, Certificado, ModeloDocumento, TemplateDocumento, CategoriaDocumentoEntity | Simples (cada) | 7 × 17 = 119 |
| 83 | AtaApuracao | Simples | 7 |
| 84 | AtaReuniao | Simples | 7 |
| 85 | BoletimUrna | Simples | 7 |
| 86 | ResultadoEleicao | Complexa | 15 |
| 87 | ResultadoFinal | Média | 10 |
| 88-101 | ResultadoParcial, RelatorioApuracao, RelatorioVotacao, EstatisticaEleicao (+10 mais) | Simples/Média | 105 |
| | **Subtotal Documentos** | | **329** |

### 3.7 Detalhamento — Módulo Impugnações (13 ALIs)

| # | ALI | Complexidade | PF |
|---|-----|--------------|-----|
| 102 | ImpugnacaoResultado | Complexa | 15 |
| 103 | PedidoImpugnacao | Simples | 7 |
| 104-114 | ArquivoPedidoImpugnacao, AlegacaoImpugnacaoResultado, ContraAlegacaoImpugnacao, ContrarrazoesRecursoImpugnacao, DefesaImpugnacao, HistoricoImpugnacao, JulgamentoImpugnacao (Média=10), JulgamentoRecursoImpugnacao, ProvaImpugnacao, RecursoImpugnacao, VotacaoJulgamentoImpugnacao | Simples/Média | 79 |
| | **Subtotal Impugnações** | | **101** |

### 3.8 Detalhamento — Módulo Julgamentos (33 ALIs)

| # | ALI | Complexidade | PF |
|---|-----|--------------|-----|
| 115 | ComissaoJulgadora | Média | 10 |
| 116 | MembroComissaoJulgadora | Simples | 7 |
| 117 | SessaoJulgamento | Complexa | 15 |
| 118 | PautaSessao | Simples | 7 |
| 119 | AtaSessao | Simples | 7 |
| 120 | JulgamentoFinal | Complexa | 15 |
| 121 | DecisaoJulgamento | Simples | 7 |
| 122 | AcordaoJulgamento | Média | 10 |
| 123-147 | AlegacaoFinal, ContraAlegacaoFinal, ArquivoJulgamento, ArquivamentoJulgamento, CertidaoJulgamento, DiligenciaJulgamento, EmendaJulgamento, IntimacaoJulgamento, NotificacaoJulgamento, ObservacaoJulgamento, PareceristaProcurador, ProvaJulgamento, PublicacaoJulgamento, RelatorioJulgamento, RecursoJulgamentoFinal, RecursoSegundaInstancia, JulgamentoRecursoSegundaInstancia, SubstituicaoJulgamentoFinal, SuspensaoJulgamento, VotoEmenda, VotoJulgamentoFinal, VotoPlenario, VotoRelator, VotoRevisor, VotoVogal | Simples (cada) | 7 × 25 = 175 |
| | **Subtotal Julgamentos** | | **257** |

### 3.9 Detalhamento — Módulo Usuários (9 ALIs)

| # | ALI | Complexidade | PF |
|---|-----|--------------|-----|
| 148 | Usuario | Complexa | 15 |
| 149 | Profissional | Complexa | 15 |
| 150 | Conselheiro | Complexa | 15 |
| 151-156 | Role, Permissao, UsuarioRole, RolePermissao, LogAcesso, HistoricoExtratoConselheiro | Simples (cada) | 7 × 6 = 42 |
| | **Subtotal Usuários** | | **87** |

### 3.10 AIEs (Arquivos de Interface Externa)

| # | AIE | Descrição | Complexidade | PF |
|---|-----|-----------|--------------|-----|
| 1 | AWS S3 | Storage de documentos | Simples | 5 |
| 2 | SMTP | Email de notificações | Simples | 5 |
| 3 | AWS Secrets Manager | Credenciais | Simples | 5 |
| | **Total AIEs** | | | **15** |

**Total Funções de Dados: 156 ALI (1.226 PF) + 3 AIE (15 PF) = 1.241 PF**

---

## 4. Passo 2 — Funções Transacionais (EE, CE, SE)

### 4.1 Como verificar os endpoints no código

```bash
# Total de endpoints HTTP
grep -rn '\[Http' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | wc -l
# Resultado esperado: 326

# Por controller
grep -rn '\[Http' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | \
  sed 's|.*/||; s|\.cs.*||' | sort | uniq -c | sort -rn

# Por verbo HTTP
grep -c '\[HttpGet' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | awk -F: '{s+=$2}END{print "GET: "s}'
grep -c '\[HttpPost' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | awk -F: '{s+=$2}END{print "POST: "s}'
grep -c '\[HttpPut' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | awk -F: '{s+=$2}END{print "PUT: "s}'
grep -c '\[HttpDelete' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | awk -F: '{s+=$2}END{print "DELETE: "s}'
```

**Resultado esperado:**
- GET: 161, POST: 125, PUT: 22, DELETE: 18, PATCH: 0
- **Total: 326 endpoints**

### 4.2 Endpoints por Controller

| Controller | Endpoints |
|------------|-----------|
| ImpugnacaoController | 43 |
| DenunciaController | 25 |
| UsuarioController | 22 |
| AuthController | 21 |
| ConfiguracaoController | 20 |
| ApuracaoController | 19 |
| ChapasController | 19 |
| JulgamentoController | 16 |
| DocumentoController | 14 |
| VotacaoController | 14 |
| CalendarioController | 13 |
| ConselheiroController | 13 |
| EleicaoController | 13 |
| FilialController | 13 |
| RelatorioController | 13 |
| AuditoriaController | 12 |
| MembroChapaController | 11 |
| NotificacaoController | 11 |
| DashboardController | 9 |
| PublicDenunciaController | 5 |
| **Total** | **326** |

### 4.3 Entradas Externas (EE) — 144 EE = 622 PF

#### Autenticação (15 EE = 55 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 1 | Login Admin | 2 | 6 | Média | 4 |
| 2 | Logout Admin | 1 | 2 | Simples | 3 |
| 3 | Register | 2 | 12 | Média | 4 |
| 4 | ConfirmEmail | 1 | 3 | Simples | 3 |
| 5 | ForgotPassword | 1 | 3 | Simples | 3 |
| 6 | ResetPassword | 2 | 5 | Média | 4 |
| 7 | LoginEleitor | 2 | 8 | Média | 4 |
| 8 | LogoutEleitor | 1 | 2 | Simples | 3 |
| 9 | LoginCandidato | 2 | 8 | Média | 4 |
| 10 | LogoutCandidato | 1 | 2 | Simples | 3 |
| 11 | RegisterCandidato | 3 | 15 | Complexa | 6 |
| 12 | ForgotPasswordCandidato | 1 | 3 | Simples | 3 |
| 13 | ResetPasswordCandidato | 2 | 5 | Média | 4 |
| 14 | ChangePasswordCandidato | 2 | 5 | Média | 4 |
| 15 | RefreshToken | 1 | 3 | Simples | 3 |
| | **Subtotal** | | | | **55** |

#### Eleições (6 EE = 27 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 16 | Create Eleição | 3 | 20+ | Complexa | 6 |
| 17 | Update Eleição | 3 | 20+ | Complexa | 6 |
| 18 | Delete Eleição | 2 | 3 | Simples | 3 |
| 19 | Iniciar Eleição | 2 | 5 | Média | 4 |
| 20 | Suspender Eleição | 2 | 6 | Média | 4 |
| 21 | Cancelar Eleição | 2 | 6 | Média | 4 |
| | **Subtotal** | | | | **27** |

#### Chapas (12 EE = 54 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 22 | Create Chapa | 3 | 15+ | Complexa | 6 |
| 23 | Update Chapa | 3 | 15+ | Complexa | 6 |
| 24 | Delete Chapa | 2 | 3 | Simples | 3 |
| 25 | AddMembro | 3 | 10 | Complexa | 6 |
| 26 | UpdateMembro | 3 | 10 | Complexa | 6 |
| 27 | RemoveMembro | 2 | 3 | Simples | 3 |
| 28 | Submeter Chapa | 2 | 5 | Média | 4 |
| 29 | IniciarAnalise | 2 | 5 | Média | 4 |
| 30 | Deferir Chapa | 2 | 6 | Média | 4 |
| 31 | Indeferir Chapa | 2 | 8 | Média | 4 |
| 32 | SolicitarDocumentos | 2 | 8 | Média | 4 |
| 33 | Recurso Chapa | 2 | 10 | Média | 4 |
| | **Subtotal** | | | | **54** |

#### Denúncias (12 EE = 57 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 34 | Create Denúncia | 3 | 15+ | Complexa | 6 |
| 35 | Update Denúncia | 3 | 15+ | Complexa | 6 |
| 36 | Delete Denúncia | 2 | 3 | Simples | 3 |
| 37 | Analisar | 2 | 10 | Média | 4 |
| 38 | ConcluirAnalise | 2 | 10 | Média | 4 |
| 39 | AceitarAdmissibilidade | 2 | 8 | Média | 4 |
| 40 | RejeitarAdmissibilidade | 2 | 8 | Média | 4 |
| 41 | RegistrarDefesa | 3 | 12 | Complexa | 6 |
| 42 | EnviarParaJulgamento | 2 | 5 | Média | 4 |
| 43 | Arquivar | 2 | 6 | Média | 4 |
| 44 | Create Denúncia Pública | 3 | 15+ | Complexa | 6 |
| 45 | Recurso Denúncia | 3 | 12 | Complexa | 6 |
| | **Subtotal** | | | | **57** |

#### Votação (4 EE = 18 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 46 | Votar | 3 | 10 | Complexa | 6 |
| 47 | AnularVoto | 2 | 6 | Média | 4 |
| 48 | AbrirVotacao | 2 | 5 | Média | 4 |
| 49 | FecharVotacao | 2 | 5 | Média | 4 |
| | **Subtotal** | | | | **18** |

#### Impugnações (15 EE = 73 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 50 | Create Impugnação | 3 | 15+ | Complexa | 6 |
| 51 | Update Impugnação | 3 | 15+ | Complexa | 6 |
| 52 | Delete Impugnação | 2 | 3 | Simples | 3 |
| 53 | Receber | 2 | 5 | Média | 4 |
| 54 | IniciarAnalise | 2 | 5 | Média | 4 |
| 55 | AbrirPrazoAlegacoes | 2 | 6 | Média | 4 |
| 56 | RegistrarAlegacao | 3 | 12 | Complexa | 6 |
| 57 | AbrirPrazoContraAlegacoes | 2 | 6 | Média | 4 |
| 58 | RegistrarContraAlegacao | 3 | 12 | Complexa | 6 |
| 59 | EnviarParaJulgamento | 2 | 5 | Média | 4 |
| 60 | Julgar | 3 | 15+ | Complexa | 6 |
| 61 | RegistrarRecurso | 3 | 12 | Complexa | 6 |
| 62 | JulgarRecurso | 3 | 15+ | Complexa | 6 |
| 63 | Arquivar | 2 | 5 | Média | 4 |
| 64 | Deferir/Indeferir | 2 | 8 | Média | 4 |
| | **Subtotal** | | | | **73** |

#### Julgamentos (12 EE = 58 PF)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 65 | Create Julgamento | 3 | 15+ | Complexa | 6 |
| 66 | Update Julgamento | 3 | 15+ | Complexa | 6 |
| 67 | Delete Julgamento | 2 | 3 | Simples | 3 |
| 68 | Iniciar | 2 | 5 | Média | 4 |
| 69 | Suspender | 2 | 6 | Média | 4 |
| 70 | Retomar | 2 | 3 | Simples | 3 |
| 71 | RegistrarVoto | 3 | 10 | Complexa | 6 |
| 72 | Concluir | 3 | 12 | Complexa | 6 |
| 73 | CreateSessao | 3 | 15+ | Complexa | 6 |
| 74 | UpdateSessao | 3 | 15+ | Complexa | 6 |
| 75 | IniciarSessao | 2 | 5 | Média | 4 |
| 76 | EncerrarSessao | 2 | 5 | Média | 4 |
| | **Subtotal** | | | | **58** |

#### Documentos (7 EE = 26 PF)

| # | EE | Complexidade | PF |
|---|-----|--------------|-----|
| 77 | Upload Documento | Média | 4 |
| 78 | UploadFile | Média | 4 |
| 79 | Update Documento | Média | 4 |
| 80 | Delete Documento | Simples | 3 |
| 81 | Aprovar/Publicar | Média | 4 |
| 82 | Revogar | Média | 4 |
| 83 | Arquivar | Simples | 3 |
| | **Subtotal** | | **26** |

#### Usuários (6 EE = 26 PF)

| # | EE | Complexidade | PF |
|---|-----|--------------|-----|
| 84 | Create Usuário | Complexa | 6 |
| 85 | Update Usuário | Complexa | 6 |
| 86 | UpdateProfile | Média | 4 |
| 87 | Delete Usuário | Simples | 3 |
| 88 | Ativar/Desativar | Simples | 3 |
| 89 | AssignRoles | Média | 4 |
| | **Subtotal** | | **26** |

#### Demais Módulos (55 EE = 228 PF)

| Módulo | Qtd EE | PF |
|--------|--------|-----|
| Calendário (CRUD + Iniciar/Concluir/Cancelar/Gerar) | 8 | 33 |
| Apuração (Iniciar/Pausar/Retomar/Finalizar/Homologar/Publicar/Apurar) | 7 | 32 |
| Configuração (Update diversas configs, TestEmail, Import/Export) | 10 | 40 |
| Notificação (Enviar/EnviarMassa/MarcarLida/MarcarTodas/Delete/Config) | 7 | 28 |
| Filial (CRUD + Ativar/Desativar) | 6 | 24 |
| Conselheiro (CRUD + Empossar/Afastar/Reintegrar/Renovar/Encerrar) | 9 | 39 |
| MembroChapa (CRUD + Reordenar/Aprovar/Rejeitar) | 7 | 29 |
| Auditoria (LimparLogsAntigos) | 1 | 3 |
| **Subtotal** | **55** | **228** |

#### Resumo Total EE

| Módulo | Qtd EE | PF |
|--------|--------|-----|
| Autenticação | 15 | 55 |
| Eleições | 6 | 27 |
| Chapas | 12 | 54 |
| Denúncias | 12 | 57 |
| Votação | 4 | 18 |
| Impugnações | 15 | 73 |
| Julgamentos | 12 | 58 |
| Documentos | 7 | 26 |
| Usuários | 6 | 26 |
| Demais Módulos | 55 | 228 |
| **TOTAL EE** | **144** | **622** |

### 4.4 Consultas Externas (CE) — 117 CE = 486 PF

| Módulo | Endpoints CE | Complexidade | PF |
|--------|-------------|--------------|-----|
| Auth (GetCurrentUser, GetEleitorInfo, GetCandidatoInfo, VerifyToken) | 4 | Média | 16 |
| Auth (VerificarEleitor, VerificarElegibilidade) | 2 | Simples | 6 |
| Eleições (GetAll, GetById, GetByStatus, GetAtivas, CanDelete, CanEdit) | 6 | Média | 24 |
| Chapas (GetAll, GetAllSimple, GetById, GetByEleicao, GetMembros, GetStatus) | 6 | Média | 24 |
| Denúncias (GetAll, GetById, GetByProtocolo, GetByEleicao, GetByStatus, GetByChapa, GetMinhas) | 7 | Média | 28 |
| Votação (VerificarElegibilidade, VerificarStatusVoto, ObterCedula, ObterComprovante, GetChapas, GetDisponiveis, GetHistorico) | 7 | Média | 28 |
| Impugnações (GetAll, GetById, GetByProtocolo, GetByEleicao, GetByChapa, GetByStatus) | 6 | Média | 24 |
| Julgamentos (GetAll, GetById, GetMembros, GetByEleicao, GetAgendados, GetSessoes) | 6 | Média | 24 |
| Documentos (GetAll, GetById, GetByEleicao, GetPublicados, Download) | 5 | Média | 20 |
| Usuários (GetAll, GetPaged, GetByType, GetByStatus, GetById, GetDetailed, GetProfile, GetByEmail, GetByRole) | 9 | Média | 36 |
| Calendário (GetAll, GetById, GetByEleicao, GetProximos, GetEmAndamento, GetByPeriodo) | 6 | Média | 24 |
| Dashboard (Geral, Eleição, Meu, Votação, Processos, Timeline, Gráfico, Atividades, KPIs) | 9 | Complexa | 54 |
| Auditoria (GetAll, GetById, GetByUsuario, GetByEntidade, GetByAcao, GetByPeriodo, GetEstatisticas, GetAcoes, GetTipos, GetHistorico) | 10 | Média | 40 |
| Notificação (GetMinhas, GetById, GetContagem, GetConfiguracoes) | 4 | Simples | 12 |
| Filial (GetAll, GetById, GetByCodigo, GetByUF, GetEstatisticas, GetProfissionais, GetEleicoes, GetUFs) | 8 | Média | 32 |
| Conselheiro (GetAll, GetById, GetByRegional, GetByMandato, GetComposicao, GetHistorico) | 6 | Média | 24 |
| MembroChapa (GetByChapa, GetById, GetByProfissional, ValidarElegibilidade, GetCargos) | 5 | Média | 20 |
| PublicDenúncia (GetEleicoes, GetTipos, ConsultarProtocolo, GetCaptcha) | 4 | Simples | 12 |
| Votação Admin (GetAll com estatísticas, GetEstatisticas, GetEleitoresQueVotaram) | 3 | Complexa | 18 |
| **TOTAL CE** | **117** | | **486** |

### 4.5 Saídas Externas (SE) — 19 SE = 125 PF

| # | SE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 1 | Relatório Participação | 4+ | 20+ | Complexa | 7 |
| 2 | Relatório Resultado | 4+ | 20+ | Complexa | 7 |
| 3 | Relatório Chapas | 3 | 15+ | Complexa | 7 |
| 4 | Relatório Eleitores | 3 | 15+ | Complexa | 7 |
| 5 | Relatório Denúncias | 3 | 15+ | Complexa | 7 |
| 6 | Relatório Impugnações | 3 | 15+ | Complexa | 7 |
| 7 | Relatório Auditoria | 3 | 15+ | Complexa | 7 |
| 8 | Relatório Consolidado | 5+ | 30+ | Complexa | 7 |
| 9 | Relatório Comparativo | 4+ | 20+ | Complexa | 7 |
| 10 | Relatório Personalizado | 4+ | 20+ | Complexa | 7 |
| 11 | Download Relatório | 1 | 5 | Simples | 4 |
| 12 | Exportar Auditoria (CSV/Excel) | 3 | 20+ | Complexa | 7 |
| 13 | Apuração — Ata | 3 | 20+ | Complexa | 7 |
| 14 | Apuração — Boletim Urna | 3 | 15+ | Complexa | 7 |
| 15 | Apuração — Resultado Parcial | 3 | 15+ | Complexa | 7 |
| 16 | Apuração — Resultado Final | 4+ | 25+ | Complexa | 7 |
| 17 | Apuração — Eleitos | 3 | 15+ | Complexa | 7 |
| 18 | Apuração — Votos por Chapa | 3 | 10 | Média | 5 |
| 19 | Apuração — Status | 2 | 10 | Média | 5 |
| | **TOTAL SE** | | | | **125** |

---

## 5. Passo 3 — Totalização Não Ajustada

| Tipo de Função | Quantidade | PF Total |
|----------------|-----------|----------|
| **ALI** (Arquivo Lógico Interno) | 156 | 1.226 |
| **AIE** (Arquivo Interface Externa) | 3 | 15 |
| **EE** (Entrada Externa) | 144 | 622 |
| **CE** (Consulta Externa) | 117 | 486 |
| **SE** (Saída Externa) | 19 | 125 |
| **TOTAL NÃO AJUSTADO** | **439** | **2.474 PF** |

### Checagem de consistência:

```
ALI + AIE + EE + CE + SE = 156 + 3 + 144 + 117 + 19 = 439 ✓
1.226 + 15 + 622 + 486 + 125 = 2.474 ✓
```

---

## 6. Passo 4 — Fator de Ajuste (VAF)

### 14 Características Gerais do Sistema (CGS)

| # | Característica | Grau (0-5) | Justificativa |
|---|----------------|------------|---------------|
| 1 | Comunicação de dados | **5** | API REST + 2 frontends SPA + AWS S3/SES |
| 2 | Processamento distribuído | **4** | AWS ECS Fargate + RDS + S3 + CloudFront |
| 3 | Desempenho | **3** | Importante mas não é hard real-time |
| 4 | Configuração de uso | **3** | ECS Fargate com auto-scaling |
| 5 | Volume de transações | **3** | Votação em massa, porém periódica |
| 6 | Entrada de dados on-line | **5** | 100% web-based, votação online |
| 7 | Eficiência do usuário | **4** | SPA React 18, shadcn/ui, UX moderna |
| 8 | Atualização on-line | **4** | CRUDs completos, máquinas de estado |
| 9 | Processamento complexo | **4** | Apuração, fluxos judiciais, workflows |
| 10 | Reusabilidade | **3** | Clean Architecture, componentes shared |
| 11 | Facilidade de instalação | **3** | Docker, Terraform, CI/CD automático |
| 12 | Facilidade de operação | **3** | Health checks, Serilog, monitoring |
| 13 | Múltiplos locais | **4** | 27 regionais do CAU + CAU Nacional |
| 14 | Facilidade de mudança | **3** | Arquitetura modular, EF Core migrations |
| | **Total DI** | **51** | |

### Cálculo do VAF:

```
VAF = (TDI × 0,01) + 0,65
VAF = (51 × 0,01) + 0,65
VAF = 0,51 + 0,65
VAF = 1,16
```

---

## 7. Passo 5 — Contagem Final Ajustada

```
PF Ajustado = PF Não Ajustado × VAF
PF Ajustado = 2.474 × 1,16
PF Ajustado = 2.869,84 ≈ 2.870 PF
```

---

## 8. Resumo Executivo

| Métrica | Valor |
|---------|-------|
| **Total de Funções de Dados** | 159 (156 ALI + 3 AIE) |
| **Total de Funções Transacionais** | 280 (144 EE + 117 CE + 19 SE) |
| **Total de Funções Identificadas** | **439** |
| **Pontos de Função Não Ajustados** | **2.474 PF** |
| **Fator de Ajuste (VAF)** | **1,16** |
| **Pontos de Função Ajustados** | **2.870 PF** |

### Evidências Estruturais do Código

| Métrica | Valor |
|---------|-------|
| Entidades de domínio | 156 |
| Controllers funcionais | 20 |
| Endpoints HTTP | 326 (161 GET + 125 POST + 22 PUT + 18 DELETE) |
| Services de aplicação | 15 |
| Páginas Admin | 40 |
| Páginas Public | 31 |

### Classificação do Sistema

| Porte | Faixa (PF) | Este Sistema |
|-------|------------|-------------|
| Pequeno | < 100 | — |
| Médio | 100 - 500 | — |
| Grande | 500 - 1.500 | — |
| **Muito Grande** | **> 1.500** | **✅ 2.870 PF** |

---

## 9. Scripts de Verificação

Todos os comandos abaixo podem ser executados na raiz do repositório para validar os números:

```bash
# 1. Contar entidades = ALIs
find apps/api/CAU.Eleitoral.Domain/Entities -name "*.cs" | \
  xargs grep -l "BaseEntity" | wc -l
# Esperado: 156

# 2. Contar controllers funcionais
find apps/api/CAU.Eleitoral.Api/Controllers -name "*Controller.cs" \
  ! -name "BaseController.cs" | wc -l
# Esperado: 20

# 3. Contar endpoints HTTP totais
grep -rn '\[Http' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | wc -l
# Esperado: 326

# 4. Detalhar endpoints por controller
grep -rn '\[Http' apps/api/CAU.Eleitoral.Api/Controllers/*.cs | \
  sed 's|.*/||; s|\.cs.*||' | sort | uniq -c | sort -rn

# 5. Contar services
find apps/api/CAU.Eleitoral.Application -name "*Service.cs" \
  ! -name "I*Service.cs" | wc -l
# Esperado: 15

# 6. Contar páginas frontend
find apps/admin/src/pages -name "*.tsx" | wc -l  # Esperado: ~40
find apps/public/src/pages -name "*.tsx" | wc -l  # Esperado: ~31
```

---

## 10. Acesso e Documentação de Apoio

| Item | Localização |
|------|-------------|
| Repositório | https://github.com/brunohelius/cau-eleitoral-migrado (`main`) |
| Admin (produção) | https://cau-admin.migrai.com.br |
| Portal Público | https://cau-public.migrai.com.br |
| API Swagger | https://cau-api.migrai.com.br/swagger |
| Health Check | https://cau-api.migrai.com.br/health |
| Contagem baseline | `docs/contagem-apf.md` |
| Snapshot recontagem | `docs/contagem-apf-snapshot-2026-02-19.md` |
| PDF consolidado | `docs/contagem-apf-consolidado.pdf` |

---

> **Nota:** Esta contagem baseia-se na análise estática do código-fonte (commit `9e21bf1`, 19/02/2026). Os valores de DET e RET foram estimados a partir das entidades, DTOs e controllers mapeados no código. O script `scripts/recount_apf_snapshot.py` pode ser executado para reproduzir exatamente o snapshot.
