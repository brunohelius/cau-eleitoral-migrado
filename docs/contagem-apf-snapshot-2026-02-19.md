# Recontagem APF - Snapshot do Codigo Migrado

- Data/hora do snapshot: **2026-02-19 15:38:22 -0300**
- Commit analisado: **`9e21bf1`**
- Fonte baseline: `docs/contagem-apf.md`
- Status da reconciliacao: **OK**

## 1. Resultado da Recontagem APF (Completa)

| Tipo | Quantidade | PF |
|---|---:|---:|
| ALI | 156 | 1.226 |
| AIE | 3 | 15 |
| EE | 144 | 622 |
| CE | 117 | 486 |
| SE | 19 | 125 |
| **TOTAL NAO AJUSTADO** | **439** | **2.474 PF** |

- VAF: **1.16**
- **TOTAL AJUSTADO: 2.870 PF**

## 2. Evidencias do Codigo (Snapshot de Hoje)

| Metrica estrutural | Valor |
|---|---:|
| Entidades de dominio (`public class` em `Domain/Entities`) | 156 |
| Controllers (total) | 21 |
| Controllers funcionais (sem `BaseController`) | 20 |
| Endpoints API (`[HttpGet/Post/Put/Delete/Patch]`) | 326 |
| Endpoints GET | 161 |
| Endpoints POST | 125 |
| Endpoints PUT | 22 |
| Endpoints DELETE | 18 |
| Endpoints PATCH | 0 |
| Services de aplicacao (`*Service.cs`) | 15 |
| Paginas Admin (`apps/admin/src/pages/*.tsx`) | 40 |
| Paginas Public (`apps/public/src/pages/*.tsx`) | 31 |

### 2.1 Entidades de dominio por modulo

| Modulo | Entidades (`BaseEntity`) |
|---|---:|
| Chapas | 8 |
| Core | 27 |
| Denuncias | 23 |
| Documentos | 43 |
| Impugnacoes | 13 |
| Julgamentos | 33 |
| Usuarios | 9 |
| **Total** | **156** |

### 2.2 Endpoints por controller

| Controller | Endpoints |
|---|---:|
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

## 3. Checagens de Consistencia

- ALI da contagem (156) == entidades do codigo (156): **OK**
- Soma de funcoes (ALI+AIE+EE+CE+SE) == total funcoes (439): **OK**
- Soma de PF por tipo == total nao ajustado (2474): **OK**
- Calculo de PF ajustado (nao ajustado x VAF) == total ajustado (2870): **OK**

## 4. Conclusao

A recontagem total e completa para o snapshot de hoje (commit `9e21bf1`) confirma o baseline APF em **2.474 PF nao ajustados** e **2.870 PF ajustados**.
