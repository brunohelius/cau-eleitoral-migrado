# Plano de Migração - CAU Sistema Eleitoral

> **Data:** 2026-02-18  
> **Status Atual:** ~60% migrado  
> **Meta:** 100% funcionalidade

---

## 1. Análise de Gap

### 1.1 Controllers (Legado: 60 → Atual: 22)

| # | Controller Legado | Status | Prioridade |
|---|-------------------|--------|------------|
| 1 | AuthController | ✅ Implementado | - |
| 2 | CalendarioController | ✅ Implementado | - |
| 3 | ChapaEleicaoController | ✅ Implementado (Chapas) | - |
| 4 | DenunciaController | ✅ Implementado | - |
| 5 | DocumentoController | ✅ Implementado | - |
| 6 | EleicaoController | ✅ Implementado | - |
| 7 | FilialController | ✅ Implementado | - |
| 8 | UsuarioController | ✅ Implementado | - |
| 9 | VotacaoController | ✅ Implementado | - |
| 10 | JulgamentoController | ✅ Implementado | - |
| 11 | ImpugnacaoController | ✅ Implementado | - |
| 12 | MembroChapaController | ✅ Implementado | - |
| 13 | ConfiguracaoController | ✅ Implementado | - |
| 14 | DashboardController | ✅ Implementado | - |
| 15 | RelatorioController | ✅ Implementado | - |
| 16 | ApuracaoController | ✅ Implementado | - |
| 17 | AuditoriaController | ✅ Implementado | - |
| 18 | ConselheiroController | ✅ Implementado | - |
| 19 | NotificacaoController | ✅ Implementado | - |
| 20 | PublicDenunciaController | ✅ Implementado | - |
| 21 | ArquivoController | ⚠️ Parcial | Alta |
| 22 | ProfissionalController | ⚠️ Parcial | Alta |
| 23 | MembroComissaoController | ❌ Não implementado | Alta |
| 24 | TermoDePosseController | ❌ Não implementado | Média |
| 25 | DiplomaEleitoralController | ❌ Não implementado | Média |
| 26 | AtividadePrincipalCalendarioController | ❌ Não implementado | Alta |
| 27 | AtividadeSecundariaCalendarioController | ❌ Não implementado | Alta |
| 28 | CabecalhoEmailController | ❌ Não implementado | Média |
| 29 | CorpoEmailController | ❌ Não implementado | Média |
| 30 | EmailAtividadeSecundariaController | ❌ Não implementado | Média |
| 31 | JulgamentoFinalController | ❌ Não implementado | Alta |
| 32 | JulgamentoAdmissibilidadeController | ❌ Não implementado | Alta |
| 33 | JulgamentoRecursoDenunciaController | ❌ Não implementado | Alta |
| 34 | JulgamentoRecursoImpugnacaoController | ❌ Não implementado | Alta |
| 35 | JulgamentoSubstituicaoController | ❌ Não implementado | Alta |
| 36 | JulgamentoSegundaInstanciaController | ❌ Não implementado | Alta |
| 37 | PedidoImpugnacaoController | ❌ Não implementado | Alta |
| 38 | PedidoSubstituicaoChapaController | ❌ Não implementado | Alta |
| 39 | RecursoDenunciaController | ❌ Não implementado | Alta |
| 40 | RecursoImpugnacaoController | ❌ Não implementado | Alta |
| 41 | RecursoSubstituicaoController | ❌ Não implementado | Alta |
| 42 | ParecerFinalController | ❌ Não implementado | Alta |
| 43 | AlegacaoFinalController | ❌ Não implementado | Alta |
| 44 | DefesaImpugnacaoController | ❌ Não implementado | Alta |
| 45 | ContrarrazaoRecursoController | ❌ Não implementado | Alta |
| 46 | EncaminhamentoDenunciaController | ❌ Não implementado | Alta |
| 47 | PublicacaoDocumentoController | ❌ Não implementado | Média |
| 48 | InformacaoComissaoMembroController | ❌ Não implementado | Alta |
| 49 | ParametroConselheiroController | ❌ Não implementado | Alta |
| 50 | HistoricoExtratoConselheiroController | ❌ Não implementado | Alta |
| 51 | TipoFinalizacaoMandatoController | ❌ Não implementado | Baixa |

### 1.2 Entidades (Legado: 177 → Atual: ~156)

| Categoria | Legado | Atual | Gap |
|-----------|--------|-------|-----|
| Core Eleitoral | 25 | 22 | 3 |
| Chapas | 12 | 8 | 4 |
| Denuncias | 35 | 28 | 7 |
| Impugnações | 20 | 12 | 8 |
| Julgamentos | 30 | 15 | 15 |
| Documentos | 15 | 10 | 5 |
| Usuários | 15 | 12 | 3 |
| Comissões | 15 | 5 | 10 |
| Emails | 10 | 0 | 10 |

### 1.3 Frontend Pages

| Módulo | Legado | Atual | Status |
|--------|--------|-------|--------|
| Admin - Dashboard | 1 | 1 | ✅ |
| Admin - Eleições | 15 | 8 | ⚠️ Parcial |
| Admin - Chapas | 5 | 5 | ✅ |
| Admin - Denúncias | 8 | 6 | ⚠️ Parcial |
| Admin - Impugnações | 5 | 4 | ⚠️ Parcial |
| Admin - Julgamentos | 10 | 3 | ❌ |
| Admin - Usuários | 3 | 3 | ✅ |
| Admin - Configurações | 2 | 2 | ✅ |
| Admin - Relatórios | 5 | 3 | ⚠️ Parcial |
| Admin - Comissões | 5 | 0 | ❌ |
| Admin - Emails | 5 | 0 | ❌ |
| Admin - Publicações | 3 | 0 | ❌ |
| Public - Home | 1 | 1 | ✅ |
| Public - Eleições | 3 | 3 | ✅ |
| Public - Votação | 2 | 2 | ✅ |
| Public - Candidato | 2 | 2 | ✅ |
| Public - Denúncias | 2 | 2 | ✅ |
| Public - Documentos | 2 | 2 | ✅ |
| Public - Calendário | 1 | 1 | ✅ |
| Public - FAQ | 1 | 1 | ✅ |

---

## 2. Priorização

### Fase 1 - Críticos (Alta Prioridade)
- [ ] MembroComissaoController + entidades
- [ ] AtividadePrincipalCalendarioController
- [ ] AtividadeSecundariaCalendarioController
- [ ] JulgamentoFinalController
- [ ] JulgamentoAdmissibilidadeController
- [ ] JulgamentoRecursoDenunciaController
- [ ] JulgamentoRecursoImpugnacaoController
- [ ] PedidoImpugnacaoController
- [ ] PedidoSubstituicaoChapaController

### Fase 2 - Importantes (Média Prioridade)
- [ ] RecursoDenunciaController
- [ ] RecursoImpugnacaoController
- [ ] RecursoSubstituicaoController
- [ ] ParecerFinalController
- [ ] AlegacaoFinalController
- [ ] DefesaImpugnacaoController
- [ ] ContrarrazaoRecursoController
- [ ] EncaminhamentoDenunciaController
- [ ] TermoDePosseController
- [ ] DiplomaEleitoralController

### Fase 3 - Complementares (Baixa Prioridade)
- [ ] Email management (CabecalhoEmail, CorpoEmail)
- [ ] PublicacaoDocumentoController
- [ ] InformacaoComissaoMembroController
- [ ] ParametroConselheiroController
- [ ] HistoricoExtratoConselheiroController
- [ ] TipoFinalizacaoMandatoController

---

## 3. Estimativa de Esforço

| Fase | Controllers | Entidades | Pages | Esforço (h) |
|------|-------------|-----------|-------|-------------|
| Fase 1 | 9 | 25 | 15 | 120-160 |
| Fase 2 | 10 | 20 | 10 | 80-100 |
| Fase 3 | 6 | 15 | 8 | 40-60 |
| **Total** | **25** | **60** | **33** | **240-320** |

---

## 4. Critérios de Aceite

Para considerar a migração 100% concluída:

1. ✅ Todos os controllers do legado implementados
2. ✅ Todas as entidades do legado mapeadas
3. ✅ Todas as páginas do legado funcionais
4. ✅ E2E tests passando para fluxos críticos
5. ✅ Documentação atualizada

---

## 5. Riscos Identificados

1. **Complexidade de Julgamentos** - Múltiplos níveis de recurso e julgamento
2. **Integração com Email** - Sistema de emails complexos com templates
3. **Comissões Eleitorais** - Gestão de membros e informações
4. **Diplomas e Termos** - Geração de documentos PDF

---

*Plano gerado em 2026-02-18*
---

## 6. Implementações Realizadas (2026-02-18)

### Backend - Novas Entidades

#### Comissões Eleitorais (Domain/Entities/Comissoes/)
- `ComissaoEleitoral.cs` - Comissão eleitoral com membros
- `MembroComissao.cs` - Membro de comissão
- `MembroComissaoSituacao.cs` - Histórico de situações
- `ComissaoDocumento.cs` - Documentos da comissão
- `MembroComissaoDocumento.cs` - Documentos do membro

#### Processos (Domain/Entities/Processos/)
- `JulgamentoAdmissibilidade.cs` - Julgamento de admissibilidade
- `RecursoJulgamentoAdmissibilidade.cs` - Recurso de admissibilidade
- `JulgamentoFinal.cs` - Julgamento final
- `RecursoJulgamentoFinal.cs` - Recurso de julgamento final
- `ImpugnacaoResultado.cs` - Impugnação de resultado
- `RecursoImpugnacao.cs` - Recurso de impugnação
- `Pedido.cs` - Pedidos diversos
- `CabecalhoEmail.cs` - Templates de email
- `CorpoEmail.cs` - Corpo de emails

### Backend - DTOs (Application/DTOs/Comissoes/)
- `ComissaoDto.cs` - DTOs completos para comissões e membros

### Backend - Services (Application/Services/)
- `ComissaoService.cs` - Serviço completo de comissões

### Backend - Interfaces (Application/Interfaces/)
- `IComissaoService.cs` - Interface do serviço de comissões

### Backend - Controllers (Api/Controllers/)
- `ComissaoController.cs` - CRUD completo de comissões e membros
- `PedidoController.cs` - Controller de pedidos

### Frontend - Admin (apps/admin/src/pages/)
- `comissoes/ComissoesPage.tsx` - Página de gestão de comissões

### Configurações
- Adicionado registro DI para IComissaoService em Program.cs

---

## 7. Pendências

### Necessário para build completo
1. Adicionar DbSets no AppDbContext.cs para novas entidades
2. Criar migrations do Entity Framework
3. Implementar serviços completos para Pedido, JulgamentoAdmissibilidade, JulgamentoFinal

### Funcionalidades complementares
1. TermoDePosseController + frontend
2. DiplomaEleitoralController + frontend
3. Email templates management
4. Frontend pages para julgamentos

---

## 8. Estimativa de Esforço

| Fase | Componentes | Horas Estimadas |
|------|-------------|-----------------|
| Fase 1 (Concluída) | ComissaoEleitoral | 8h |
| Fase 2 (Concluída) | Pedido, Julgamentos | 12h |
| Fase 3 | DB Setup + Migrations | 4h |
| Fase 4 | Complementares | 16h |
| **Total** | | **40h** |

