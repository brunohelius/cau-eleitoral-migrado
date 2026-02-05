import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ImpugnacaoResultadoResolve } from './impugnacao-resultado-client.resolve';
import { UfImpugnacaoResultadoResolve } from './uf-impugnacao-resultado-client.resolve';
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';
import { AcompanharImpugnacaoResultadoChapaClientResolve } from './acompanhar-impugnacao-resultado-chapa-client.resolve';
import { AcompanharImpugnacaoResultadoComissaoClientResolve } from './acompanhar-impugnacao-resultado-comissao-client.resolve';
import { AtividadeSecundariaCadastroImpugnacaoResolve } from './atividade-secundaria-cadastro-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoClientResolve } from './acompanhar-impugnacao-resultado-client-resolve';
import { JulgamentoAlegacaoImpugnacaoResultadoClientResolve } from './acompanhar-alegacacao-impugnacao-resultado.resolve';
import { ImpugnacaoResultadoChapaResolve } from './impugnacao-resultado-chapa-client.resolve';
import { ImpugnacaoResultadoComissaoResolve } from './impugnacao-resultado-comissao-client.resolve';
import { ImpugnacaoResultadoImpugnanteResolve } from './impugnacao-resultado-impugnante-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a Impugnacao do Resultado.
 */
@NgModule({
    declarations: [],
    imports: [
        CommonModule
    ],
    providers: [
        ImpugnacaoResultadoResolve,
        ImpugnacaoResultadoChapaResolve,
        ImpugnacaoResultadoComissaoResolve,
        ImpugnacaoResultadoImpugnanteResolve,
        UfImpugnacaoResultadoResolve,
        ImpugnacaoResultadoClientService,
        AcompanharImpugnacaoResultadoChapaClientResolve,
        AtividadeSecundariaCadastroImpugnacaoResolve,
        AcompanharImpugnacaoResultadoComissaoClientResolve,
        AcompanharImpugnacaoResultadoClientResolve,
        JulgamentoAlegacaoImpugnacaoResultadoClientResolve,
    ]
})
export class ImpugnacaoResultadoClientModule {
 }
