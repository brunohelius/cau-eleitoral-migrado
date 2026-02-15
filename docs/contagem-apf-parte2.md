# Contagem APF — Parte 2: Funções Transacionais e Totalização

> Continuação de [contagem-apf-parte1.md](file:///Users/brunosouza/Development/cau-eleitoral-migrado/docs/contagem-apf-parte1.md)

---

## 4. Arquivos de Interface Externa (AIE)

| # | AIE | Descrição | Complexidade | PF |
|---|-----|-----------|--------------|-----|
| 1 | AWS S3 | Armazenamento de documentos e uploads | Simples | 5 |
| 2 | SMTP (Email) | Envio de notificações por email | Simples | 5 |
| 3 | AWS Secrets Manager | Chaves e credenciais | Simples | 5 |
| | **Total AIEs** | | | **15** |

---

## 5. Funções Transacionais — Entradas Externas (EE)

### 5.1 Autenticação (AuthController — 15 EE)

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
| | **Subtotal Auth EE** | | | | **55** |

### 5.2 Eleições (EleicaoController — 6 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 16 | Create Eleição | 3 | 20+ | Complexa | 6 |
| 17 | Update Eleição | 3 | 20+ | Complexa | 6 |
| 18 | Delete Eleição | 2 | 3 | Simples | 3 |
| 19 | Iniciar Eleição | 2 | 5 | Média | 4 |
| 20 | Suspender Eleição | 2 | 6 | Média | 4 |
| 21 | Cancelar Eleição | 2 | 6 | Média | 4 |
| | **Subtotal Eleições EE** | | | | **27** |

### 5.3 Chapas (ChapasController — 12 EE)

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
| | **Subtotal Chapas EE** | | | | **54** |

### 5.4 Denúncias (DenunciaController + PublicDenunciaController — 12 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 34 | Create Denúncia | 3 | 15+ | Complexa | 6 |
| 35 | Update Denúncia | 3 | 15+ | Complexa | 6 |
| 36 | Delete Denúncia | 2 | 3 | Simples | 3 |
| 37 | Analisar Denúncia | 2 | 10 | Média | 4 |
| 38 | ConcluirAnalise | 2 | 10 | Média | 4 |
| 39 | AceitarAdmissibilidade | 2 | 8 | Média | 4 |
| 40 | RejeitarAdmissibilidade | 2 | 8 | Média | 4 |
| 41 | RegistrarDefesa | 3 | 12 | Complexa | 6 |
| 42 | EnviarParaJulgamento | 2 | 5 | Média | 4 |
| 43 | Arquivar | 2 | 6 | Média | 4 |
| 44 | Create Denúncia Pública | 3 | 15+ | Complexa | 6 |
| 45 | Recurso Denúncia | 3 | 12 | Complexa | 6 |
| | **Subtotal Denúncias EE** | | | | **57** |

### 5.5 Votação (VotacaoController — 4 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 46 | Votar | 3 | 10 | Complexa | 6 |
| 47 | AnularVoto | 2 | 6 | Média | 4 |
| 48 | AbrirVotacao | 2 | 5 | Média | 4 |
| 49 | FecharVotacao | 2 | 5 | Média | 4 |
| | **Subtotal Votação EE** | | | | **18** |

### 5.6 Impugnações (ImpugnacaoController — 15 EE)

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
| | **Subtotal Impugnações EE** | | | | **73** |

### 5.7 Julgamentos (JulgamentoController — 12 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 65 | Create Julgamento | 3 | 15+ | Complexa | 6 |
| 66 | Update Julgamento | 3 | 15+ | Complexa | 6 |
| 67 | Delete Julgamento | 2 | 3 | Simples | 3 |
| 68 | Iniciar Julgamento | 2 | 5 | Média | 4 |
| 69 | Suspender | 2 | 6 | Média | 4 |
| 70 | Retomar | 2 | 3 | Simples | 3 |
| 71 | RegistrarVoto | 3 | 10 | Complexa | 6 |
| 72 | Concluir | 3 | 12 | Complexa | 6 |
| 73 | CreateSessao | 3 | 15+ | Complexa | 6 |
| 74 | UpdateSessao | 3 | 15+ | Complexa | 6 |
| 75 | IniciarSessao | 2 | 5 | Média | 4 |
| 76 | EncerrarSessao | 2 | 5 | Média | 4 |
| | **Subtotal Julgamentos EE** | | | | **58** |

### 5.8 Documentos (DocumentoController — 7 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 77 | Upload Documento | 2 | 12 | Média | 4 |
| 78 | UploadFile | 2 | 10 | Média | 4 |
| 79 | Update Documento | 2 | 12 | Média | 4 |
| 80 | Delete Documento | 1 | 3 | Simples | 3 |
| 81 | Aprovar/Publicar | 2 | 5 | Média | 4 |
| 82 | Revogar | 2 | 6 | Média | 4 |
| 83 | Arquivar | 1 | 3 | Simples | 3 |
| | **Subtotal Documentos EE** | | | | **26** |

### 5.9 Usuários (UsuarioController — 6 EE)

| # | EE | FTR | DET | Complexidade | PF |
|---|-----|-----|-----|--------------|-----|
| 84 | Create Usuário | 3 | 15+ | Complexa | 6 |
| 85 | Update Usuário | 3 | 15+ | Complexa | 6 |
| 86 | UpdateProfile | 2 | 10 | Média | 4 |
| 87 | Delete Usuário | 2 | 3 | Simples | 3 |
| 88 | Ativar/Desativar | 2 | 3 | Simples | 3 |
| 89 | AssignRoles | 2 | 6 | Média | 4 |
| | **Subtotal Usuários EE** | | | | **26** |

### 5.10 Demais Módulos (35 EE)

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

### 5.11 Resumo Total EE

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

---

## 6. Saídas Externas (SE) e Consultas Externas (CE)

### 6.1 Consultas Externas (CE) — Listagens e Buscas

| Módulo | Endpoints CE | Complexidade Média | PF |
|--------|-------------|--------------------|----|
| Auth (GetCurrentUser, GetEleitorInfo, GetCandidatoInfo, VerifyToken) | 4 | Média (4) | 16 |
| Auth (VerificarEleitor, VerificarElegibilidade) | 2 | Simples (3) | 6 |
| Eleições (GetAll, GetById, GetByStatus, GetAtivas, CanDelete, CanEdit) | 6 | Média (4) | 24 |
| Chapas (GetAll, GetAllSimple, GetById, GetByEleicao, GetMembros, GetStatus) | 6 | Média (4) | 24 |
| Denúncias (GetAll, GetById, GetByProtocolo, GetByEleicao, GetByStatus, GetByChapa, GetMinhas) | 7 | Média (4) | 28 |
| Votação (VerificarElegibilidade, VerificarStatusVoto, ObterCedula, ObterComprovante, GetChapas, GetDisponiveis, GetHistorico) | 7 | Média (4) | 28 |
| Impugnações (GetAll, GetById, GetByProtocolo, GetByEleicao, GetByChapa, GetByStatus) | 6 | Média (4) | 24 |
| Julgamentos (GetAll, GetById, GetMembros, GetByEleicao, GetAgendados, GetSessoes) | 6 | Média (4) | 24 |
| Documentos (GetAll, GetById, GetByEleicao, GetPublicados, Download) | 5 | Média (4) | 20 |
| Usuários (GetAll, GetPaged, GetByType, GetByStatus, GetById, GetDetailed, GetProfile, GetByEmail, GetByRole) | 9 | Média (4) | 36 |
| Calendário (GetAll, GetById, GetByEleicao, GetProximos, GetEmAndamento, GetByPeriodo) | 6 | Média (4) | 24 |
| Dashboard (Geral, Eleição, Meu, Votação, Processos, Timeline, Gráfico, Atividades, KPIs) | 9 | Complexa (6) | 54 |
| Auditoria (GetAll, GetById, GetByUsuario, GetByEntidade, GetByAcao, GetByPeriodo, GetEstatisticas, GetAcoes, GetTipos, GetHistorico) | 10 | Média (4) | 40 |
| Notificação (GetMinhas, GetById, GetContagem, GetConfiguracoes) | 4 | Simples (3) | 12 |
| Filial (GetAll, GetById, GetByCodigo, GetByUF, GetEstatisticas, GetProfissionais, GetEleicoes, GetUFs) | 8 | Média (4) | 32 |
| Conselheiro (GetAll, GetById, GetByRegional, GetByMandato, GetComposicao, GetHistorico) | 6 | Média (4) | 24 |
| MembroChapa (GetByChapa, GetById, GetByProfissional, ValidarElegibilidade, GetCargos) | 5 | Média (4) | 20 |
| PublicDenúncia (GetEleicoes, GetTipos, ConsultarProtocolo, GetCaptcha) | 4 | Simples (3) | 12 |
| Votação Admin (GetAll com estatísticas, GetEstatisticas, GetEleitoresQueVotaram) | 3 | Complexa (6) | 18 |
| **TOTAL CE** | **117** | | **486** |

### 6.2 Saídas Externas (SE) — Relatórios e Exportações

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

## 7. Totalização — Contagem Não Ajustada

| Tipo de Função | Quantidade | PF Total |
|----------------|-----------|----------|
| **ALI** (Arquivo Lógico Interno) | 156 | 1.226 |
| **AIE** (Arquivo Interface Externa) | 3 | 15 |
| **EE** (Entrada Externa) | 144 | 622 |
| **CE** (Consulta Externa) | 117 | 486 |
| **SE** (Saída Externa) | 19 | 125 |
| **TOTAL NÃO AJUSTADO** | **439** | **2.474 PF** |

---

## 8. Fator de Ajuste (VAF)

### 8.1 Características Gerais do Sistema (CGS)

| # | Característica | Grau (0-5) | Justificativa |
|---|----------------|------------|---------------|
| 1 | Comunicação de dados | 5 | API REST, múltiplos frontends, AWS services |
| 2 | Processamento distribuído | 4 | AWS ECS Fargate, S3, RDS, CloudFront |
| 3 | Desempenho | 3 | Importante mas não crítico |
| 4 | Configuração de uso | 3 | ECS Fargate com auto-scaling |
| 5 | Volume de transações | 3 | Votação em massa, mas periódica |
| 6 | Entrada de dados on-line | 5 | 100% web-based, votação online |
| 7 | Eficiência do usuário | 4 | SPA React, UX moderna, shadcn/ui |
| 8 | Atualização on-line | 4 | CRUD completo, status machines |
| 9 | Processamento complexo | 4 | Apuração, fluxos judiciais, workflows |
| 10 | Reusabilidade | 3 | Clean Architecture, componentes shared |
| 11 | Facilidade de instalação | 3 | Docker, Terraform, CI/CD |
| 12 | Facilidade de operação | 3 | Health checks, Serilog, monitoring |
| 13 | Múltiplos locais | 4 | 27 regionais + CAU Nacional |
| 14 | Facilidade de mudança | 3 | Arquitetura modular, EF Core migrations |
| | **Total DI** | **51** | |

### 8.2 Cálculo do VAF

```
VAF = (TDI × 0,01) + 0,65
VAF = (51 × 0,01) + 0,65
VAF = 0,51 + 0,65
VAF = 1,16
```

---

## 9. Contagem Final Ajustada

```
PF Ajustado = PF Não Ajustado × VAF
PF Ajustado = 2.474 × 1,16
PF Ajustado = 2.869,84 ≈ 2.870 PF
```

---

## 10. Resumo Executivo

| Métrica | Valor |
|---------|-------|
| **Total de Funções de Dados** | 159 (156 ALI + 3 AIE) |
| **Total de Funções Transacionais** | 280 (144 EE + 117 CE + 19 SE) |
| **Total de Funções Identificadas** | 439 |
| **Pontos de Função Não Ajustados** | **2.474 PF** |
| **Fator de Ajuste (VAF)** | **1,16** |
| **Pontos de Função Ajustados** | **2.870 PF** |
| **Entidades de Domínio** | 156 |
| **Controllers** | 21 (20 funcionais + 1 base) |
| **Services** | 15 |
| **Páginas Admin** | 40 |
| **Páginas Public** | 31 |
| **Total de Endpoints API** | 326 |

### 10.1 Distribuição por Módulo

| Módulo | ALI | EE | CE | SE | PF Total |
|--------|-----|----|----|-----|----------|
| Core Eleitoral | 214 | 60 | 48 | 0 | 322 |
| Chapas | 67 | 54 | 24 | 0 | 145 |
| Membros Chapa | 0 | 29 | 20 | 0 | 49 |
| Denúncias | 171 | 57 | 28 | 7 | 263 |
| Denúncias Públicas | 0 | 6 | 12 | 0 | 18 |
| Votação | 0 | 18 | 46 | 0 | 64 |
| Apuração | 0 | 32 | 0 | 52 | 84 |
| Impugnações | 101 | 73 | 24 | 7 | 205 |
| Julgamentos | 257 | 58 | 24 | 0 | 339 |
| Documentos | 329 | 26 | 20 | 0 | 375 |
| Usuários | 87 | 26 | 36 | 0 | 149 |
| Autenticação | 0 | 55 | 22 | 0 | 77 |
| Calendário | 0 | 33 | 24 | 0 | 57 |
| Dashboard | 0 | 0 | 54 | 0 | 54 |
| Relatórios | 0 | 0 | 0 | 59 | 59 |
| Auditoria | 0 | 3 | 40 | 12 | 55 |
| Configuração | 0 | 40 | 0 | 0 | 40 |
| Notificação | 0 | 28 | 12 | 0 | 40 |
| Filial | 0 | 24 | 32 | 0 | 56 |
| Conselheiro | 0 | 39 | 24 | 0 | 63 |
| AIEs | 15 | 0 | 0 | 0 | 15 |

### 10.2 Estimativa de Esforço

Considerando produtividade média de **10 PF/mês** (equipe mista, sistema complexo):

| Métrica | Valor |
|---------|-------|
| PF Ajustados | 2.870 |
| Produtividade | 10 PF/mês |
| **Esforço estimado** | **287 homens/mês** |
| Equipe de 4 pessoas | ~72 meses (~6 anos) |
| Equipe de 6 pessoas | ~48 meses (~4 anos) |

### 10.3 Classificação do Sistema

| Porte | Faixa (PF) | Sistema |
|-------|------------|---------|
| Pequeno | < 100 | — |
| Médio | 100 - 500 | — |
| Grande | 500 - 1.500 | — |
| **Muito Grande** | **> 1.500** | **✅ CAU Eleitoral (2.870 PF)** |

---

> **Nota:** Esta contagem baseia-se na análise estática do código-fonte à data de 2026-02-13. Os valores de DET e RET foram estimados a partir das entidades, DTOs e controllers mapeados no código. Uma contagem detalhada com revisão manual de cada campo de cada entidade poderá refinar os valores em ±10%.
