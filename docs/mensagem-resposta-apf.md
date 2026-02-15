# Mensagem para Rodrigo/Marcos — Resposta sobre APF

---

**Para enviar no WhatsApp (copie e cole):**

---

Rodrigo, Marcos, boa noite! 👋

Primeiro, concordo com o Marcos: na primeira versão da contagem, sim, cada entidade (tabela) foi classificada como um ALI separado. Isso infla o número e não está alinhado com o IFPUG. Já corrigimos isso — agrupamos as 156 tabelas em **16 ALIs lógicos** (ex: todas as 23 tabelas de Denúncia = 1 ALI "Denúncia" com múltiplos RETs).

Porém, mesmo corrigindo os ALIs, a contagem fica em **1.111 PF não ajustados (1.289 ajustados)** — ainda 2x acima dos 590 de vocês.

E o motivo é simples: **as transações**.

Rodei um script direto no código-fonte e os 20 controllers do sistema têm exatamente **326 endpoints reais** (métodos com `[HttpGet]/[HttpPost]/[HttpPut]/[HttpDelete]`). Vejam os números do código:

```
43 endpoints - ImpugnacaoController
25 endpoints - DenunciaController
22 endpoints - UsuarioController
21 endpoints - AuthController
20 endpoints - ConfiguracaoController
19 endpoints - ChapasController
19 endpoints - ApuracaoController
16 endpoints - JulgamentoController
14 endpoints - VotacaoController
14 endpoints - DocumentoController
13 endpoints - RelatorioController
13 endpoints - FilialController
13 endpoints - EleicaoController
13 endpoints - ConselheiroController
13 endpoints - CalendarioController
12 endpoints - AuditoriaController
11 endpoints - NotificacaoController
11 endpoints - MembroChapaController
9  endpoints - DashboardController
5  endpoints - PublicDenunciaController
= 326 endpoints REAIS no código
```

Mesmo agrupando endpoints em processos elementares (ex: GetById + GetByStatus = 1 CE), ficamos com no mínimo **200 transações**. Para dar 590 PF com 16 ALIs (≈200 PF), sobrariam apenas ~390 PF para transações — o que daria ~65 transações a uma média de 6 PF. **65 transações para um sistema com 326 endpoints e 16 módulos não fecha.**

Pra contextualizar — só o módulo de **Impugnação** tem 43 endpoints que cobrem: CRUD + receber + analisar + abrir prazo alegações + registrar alegação + prazo contra-alegações + registrar contra-alegação + enviar para julgamento + julgar + registrar recurso + julgar recurso + arquivar + deferir/indeferir. São no mínimo **15 processos elementares distintos** só nesse módulo. Com 590 PF, cada módulo teria em média 4 transações — isso não condiz com a realidade do código.

A questão central não é a IA ter "alucinado" — é que 590 PF classifica o sistema como **porte Médio** (equivalente a um sistema de 20-30 tabelas com CRUD simples). Mas o CAU Eleitoral tem:
- 156 tabelas no banco
- 16 módulos funcionais
- 326 endpoints na API
- Workflows complexos (Denúncia tem 6+ estados, Impugnação tem 8+)
- 3 perfis de autenticação separados (Admin, Eleitor, Candidato)
- Portal público independente
- Dashboard com 9 visões
- 13 tipos de relatório

Isso é um sistema de porte **Grande**, na faixa de 800-1.500 PF, sem exagero.

Estou aberto para sentar e fazer a contagem linha a linha juntos. Posso disponibilizar acesso ao código para auditoria. O que proponho é uma sessão conjunta onde analisamos módulo por módulo: eu mostro os endpoints no código e vocês classificam. Assim chegamos num número que todo mundo confia.

O que acham? 🤝
