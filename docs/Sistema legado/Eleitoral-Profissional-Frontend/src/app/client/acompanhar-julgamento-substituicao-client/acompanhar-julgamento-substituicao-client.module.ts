import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AcompanharJulgamentoSubstituicaoClient } from './acompanhar-julgamento-substituicao-client.service';
import { AcompanharJuglamentoSubstituicaoChapaResolve } from './acompanhar-julgamento-substituicao-chapa.resolve';
import { AcompanharJuglamentoSubstituicaoComissaoResolve } from './acompanhar-julgamento-substituicao-comissao.resolve';


/**
 * Modulo de integração do projeto frontend com os serviços backend referente a chapa eleção para Responsavel.
 */
@NgModule({
    declarations: [],
    imports: [
        CommonModule
    ],
    providers: [
        AcompanharJulgamentoSubstituicaoClient,
        AcompanharJuglamentoSubstituicaoChapaResolve,
        AcompanharJuglamentoSubstituicaoComissaoResolve
    ]
})
export class AcompanharJulgamentoSubstituicaoModule { }
