# Contagem de Pontos de Função (APF) — CAU Sistema Eleitoral

> **Projeto:** CAU Sistema Eleitoral Migrado  
> **Metodologia:** IFPUG CPM 4.3.1 (Análise de Pontos de Função)  
> **Tipo de Contagem:** Contagem de Aplicação (baseline)  
> **Data:** 2026-02-13  
> **Responsável:** Análise automatizada sobre código-fonte  

---

## 1. Escopo e Fronteira da Aplicação

### 1.1 Descrição do Sistema

O CAU Sistema Eleitoral é um sistema completo de gestão de processos eleitorais do Conselho de Arquitetura e Urbanismo (CAU). O sistema foi migrado de PHP/Java para **.NET 8 + React 18** e está em produção na AWS (ECS Fargate).

### 1.2 Fronteira da Aplicação

A fronteira da aplicação contempla:
- **API Backend** (.NET 8 Web API — Clean Architecture)
- **Frontend Admin** (React 18 — painel administrativo)
- **Frontend Public** (React 18 — portal público do eleitor/candidato)
- **Banco de dados** PostgreSQL (RDS)
- **Armazenamento** AWS S3 (documentos e uploads)

### 1.3 Módulos Funcionais

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

---

## 2. Metodologia APF Aplicada

### 2.1 Tipos de Função

A APF classifica as funcionalidades em **Funções de Dados** e **Funções Transacionais**:

**Funções de Dados:**
- **ALI (Arquivo Lógico Interno):** Grupo de dados mantido dentro da fronteira da aplicação
- **AIE (Arquivo de Interface Externa):** Grupo de dados referenciado, mas mantido fora da fronteira

**Funções Transacionais:**
- **EE (Entrada Externa):** Processo que mantém um ALI (inserção, alteração, exclusão)
- **SE (Saída Externa):** Processo que envia dados para fora da fronteira com lógica de processamento
- **CE (Consulta Externa):** Processo que envia dados para fora da fronteira sem lógica adicional

### 2.2 Tabela de Complexidade e Pesos

| Tipo | Simples | Média | Complexa |
|------|---------|-------|----------|
| ALI | 7 PF | 10 PF | 15 PF |
| AIE | 5 PF | 7 PF | 10 PF |
| EE | 3 PF | 4 PF | 6 PF |
| SE | 4 PF | 5 PF | 7 PF |
| CE | 3 PF | 4 PF | 6 PF |

### 2.3 Critérios de Complexidade (DET/RET/FTR)

**ALI/AIE:**
| | 1 RET | 2-5 RET | 6+ RET |
|---|---|---|---|
| 1-19 DET | Simples | Simples | Média |
| 20-50 DET | Simples | Média | Complexa |
| 51+ DET | Média | Complexa | Complexa |

**EE:**
| | 0-1 FTR | 2 FTR | 3+ FTR |
|---|---|---|---|
| 1-4 DET | Simples | Simples | Média |
| 5-15 DET | Simples | Média | Complexa |
| 16+ DET | Média | Complexa | Complexa |

**SE/CE:**
| | 0-1 FTR | 2-3 FTR | 4+ FTR |
|---|---|---|---|
| 1-5 DET | Simples | Simples | Média |
| 6-19 DET | Simples | Média | Complexa |
| 20+ DET | Média | Complexa | Complexa |

---

## 3. Funções de Dados — Arquivos Lógicos Internos (ALI)

### 3.1 Módulo Core Eleitoral (27 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
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

### 3.2 Módulo Chapas (8 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
| 28 | ChapaEleicao | 3 | 25+ | Complexa | 15 |
| 29 | MembroChapa | 2 | 20+ | Média | 10 |
| 30 | ComposicaoChapa | 1 | 10 | Simples | 7 |
| 31 | DocumentoChapa | 2 | 12 | Simples | 7 |
| 32 | HistoricoChapaEleicao | 1 | 10 | Simples | 7 |
| 33 | PlataformaEleitoral | 1 | 12 | Simples | 7 |
| 34 | SubstituicaoMembroChapa | 2 | 15 | Simples | 7 |
| 35 | ConfirmacaoMembroChapa | 1 | 8 | Simples | 7 |
| | **Subtotal Chapas** | | | | **67** |

### 3.3 Módulo Denúncias (23 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
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

### 3.4 Módulo Documentos (43 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
| 59 | Documento | 3 | 25+ | Complexa | 15 |
| 60 | ArquivoDocumento | 1 | 10 | Simples | 7 |
| 61 | AssinaturaDigital | 2 | 15 | Simples | 7 |
| 62 | CertificadoDigital | 1 | 10 | Simples | 7 |
| 63 | CarimboTempo | 1 | 8 | Simples | 7 |
| 64 | Edital | 2 | 20+ | Média | 10 |
| 65 | Resolucao | 2 | 15 | Simples | 7 |
| 66 | Ato | 1 | 10 | Simples | 7 |
| 67 | Aviso | 1 | 8 | Simples | 7 |
| 68 | Comunicado | 1 | 8 | Simples | 7 |
| 69 | Convocacao | 1 | 10 | Simples | 7 |
| 70 | Declaracao | 1 | 8 | Simples | 7 |
| 71 | Deliberacao | 1 | 10 | Simples | 7 |
| 72 | Diploma | 1 | 10 | Simples | 7 |
| 73 | Normativa | 1 | 10 | Simples | 7 |
| 74 | Portaria | 1 | 10 | Simples | 7 |
| 75 | Publicacao | 1 | 10 | Simples | 7 |
| 76 | PublicacaoOficial | 1 | 10 | Simples | 7 |
| 77 | Termo | 1 | 10 | Simples | 7 |
| 78 | TermoPosse | 1 | 12 | Simples | 7 |
| 79 | Certificado | 1 | 10 | Simples | 7 |
| 80 | ModeloDocumento | 1 | 12 | Simples | 7 |
| 81 | TemplateDocumento | 1 | 12 | Simples | 7 |
| 82 | CategoriaDocumentoEntity | 1 | 8 | Simples | 7 |
| 83 | AtaApuracao | 2 | 15 | Simples | 7 |
| 84 | AtaReuniao | 2 | 15 | Simples | 7 |
| 85 | BoletimUrna | 2 | 15 | Simples | 7 |
| 86 | ResultadoEleicao | 3 | 25+ | Complexa | 15 |
| 87 | ResultadoFinal | 2 | 20+ | Média | 10 |
| 88 | ResultadoParcial | 2 | 15 | Simples | 7 |
| 89 | RelatorioApuracao | 2 | 15 | Simples | 7 |
| 90 | RelatorioVotacao | 2 | 15 | Simples | 7 |
| 91 | EstatisticaEleicao | 2 | 20+ | Média | 10 |
| 92 | GraficoResultado | 1 | 10 | Simples | 7 |
| 93 | MapaVotacao | 2 | 15 | Simples | 7 |
| 94 | ExportacaoDados | 1 | 10 | Simples | 7 |
| 95 | ImportacaoDados | 1 | 10 | Simples | 7 |
| 96 | RegistroApuracaoVotos | 2 | 15 | Simples | 7 |
| 97 | TotalVotos | 1 | 8 | Simples | 7 |
| 98 | VotoAnulado | 1 | 8 | Simples | 7 |
| 99 | VotoBranco | 1 | 6 | Simples | 7 |
| 100 | VotoChapa | 2 | 10 | Simples | 7 |
| 101 | VotoNulo | 1 | 6 | Simples | 7 |
| | **Subtotal Documentos** | | | | **329** |

### 3.5 Módulo Impugnações (13 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
| 102 | ImpugnacaoResultado | 3 | 25+ | Complexa | 15 |
| 103 | PedidoImpugnacao | 2 | 15 | Simples | 7 |
| 104 | ArquivoPedidoImpugnacao | 1 | 8 | Simples | 7 |
| 105 | AlegacaoImpugnacaoResultado | 1 | 12 | Simples | 7 |
| 106 | ContraAlegacaoImpugnacao | 1 | 12 | Simples | 7 |
| 107 | ContrarrazoesRecursoImpugnacao | 1 | 10 | Simples | 7 |
| 108 | DefesaImpugnacao | 2 | 15 | Simples | 7 |
| 109 | HistoricoImpugnacao | 1 | 10 | Simples | 7 |
| 110 | JulgamentoImpugnacao | 2 | 20+ | Média | 10 |
| 111 | JulgamentoRecursoImpugnacao | 2 | 15 | Simples | 7 |
| 112 | ProvaImpugnacao | 1 | 8 | Simples | 7 |
| 113 | RecursoImpugnacao | 2 | 15 | Simples | 7 |
| 114 | VotacaoJulgamentoImpugnacao | 2 | 12 | Simples | 7 |
| | **Subtotal Impugnações** | | | | **101** |

### 3.6 Módulo Julgamentos (33 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
| 115 | ComissaoJulgadora | 2 | 20+ | Média | 10 |
| 116 | MembroComissaoJulgadora | 2 | 15 | Simples | 7 |
| 117 | SessaoJulgamento | 3 | 25+ | Complexa | 15 |
| 118 | PautaSessao | 1 | 10 | Simples | 7 |
| 119 | AtaSessao | 2 | 15 | Simples | 7 |
| 120 | JulgamentoFinal | 3 | 25+ | Complexa | 15 |
| 121 | DecisaoJulgamento | 2 | 15 | Simples | 7 |
| 122 | AcordaoJulgamento | 2 | 20+ | Média | 10 |
| 123 | AlegacaoFinal | 1 | 12 | Simples | 7 |
| 124 | ContraAlegacaoFinal | 1 | 12 | Simples | 7 |
| 125 | ArquivoJulgamento | 1 | 8 | Simples | 7 |
| 126 | ArquivamentoJulgamento | 1 | 10 | Simples | 7 |
| 127 | CertidaoJulgamento | 1 | 10 | Simples | 7 |
| 128 | DiligenciaJulgamento | 1 | 10 | Simples | 7 |
| 129 | EmendaJulgamento | 1 | 10 | Simples | 7 |
| 130 | IntimacaoJulgamento | 1 | 10 | Simples | 7 |
| 131 | NotificacaoJulgamento | 1 | 10 | Simples | 7 |
| 132 | ObservacaoJulgamento | 1 | 8 | Simples | 7 |
| 133 | PareceristaProcurador | 1 | 10 | Simples | 7 |
| 134 | ProvaJulgamento | 1 | 8 | Simples | 7 |
| 135 | PublicacaoJulgamento | 1 | 10 | Simples | 7 |
| 136 | RelatorioJulgamento | 2 | 15 | Simples | 7 |
| 137 | RecursoJulgamentoFinal | 2 | 15 | Simples | 7 |
| 138 | RecursoSegundaInstancia | 2 | 15 | Simples | 7 |
| 139 | JulgamentoRecursoSegundaInstancia | 2 | 15 | Simples | 7 |
| 140 | SubstituicaoJulgamentoFinal | 1 | 10 | Simples | 7 |
| 141 | SuspensaoJulgamento | 1 | 10 | Simples | 7 |
| 142 | VotoEmenda | 1 | 8 | Simples | 7 |
| 143 | VotoJulgamentoFinal | 2 | 12 | Simples | 7 |
| 144 | VotoPlenario | 1 | 10 | Simples | 7 |
| 145 | VotoRelator | 1 | 10 | Simples | 7 |
| 146 | VotoRevisor | 1 | 10 | Simples | 7 |
| 147 | VotoVogal | 1 | 10 | Simples | 7 |
| | **Subtotal Julgamentos** | | | | **257** |

### 3.7 Módulo Usuários (9 entidades)

| # | ALI | RET | DET (est.) | Complexidade | PF |
|---|-----|-----|------------|--------------|-----|
| 148 | Usuario | 4 | 30+ | Complexa | 15 |
| 149 | Profissional | 3 | 25+ | Complexa | 15 |
| 150 | Conselheiro | 3 | 25+ | Complexa | 15 |
| 151 | Role | 1 | 8 | Simples | 7 |
| 152 | Permissao | 1 | 8 | Simples | 7 |
| 153 | UsuarioRole | 1 | 4 | Simples | 7 |
| 154 | RolePermissao | 1 | 4 | Simples | 7 |
| 155 | LogAcesso | 1 | 12 | Simples | 7 |
| 156 | HistoricoExtratoConselheiro | 1 | 12 | Simples | 7 |
| | **Subtotal Usuários** | | | | **87** |

### 3.8 Resumo Total ALIs

| Módulo | Qtd ALIs | PF |
|--------|----------|-----|
| Core Eleitoral | 27 | 214 |
| Chapas | 8 | 67 |
| Denúncias | 23 | 171 |
| Documentos | 43 | 329 |
| Impugnações | 13 | 101 |
| Julgamentos | 33 | 257 |
| Usuários | 9 | 87 |
| **TOTAL ALIs** | **156** | **1.226** |

---

> **Continua em** [contagem-apf-parte2.md](file:///Users/brunosouza/Development/cau-eleitoral-migrado/docs/contagem-apf-parte2.md)
