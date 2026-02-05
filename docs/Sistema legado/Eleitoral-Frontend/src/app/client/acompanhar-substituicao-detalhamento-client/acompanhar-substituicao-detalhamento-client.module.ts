import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AcompanharSubstituicaoDetalhamentoService } from './acompanhar-substituicao-detalhamento-client.service';
import { AcompanharSubstituicaoDetalhamentoResolve } from './acompanhar-substituicao-detalhamento-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a chapa eleção para Responsavel.
 */
@NgModule({
    declarations: [],
    imports: [
        CommonModule
    ],
    providers: [
        AcompanharSubstituicaoDetalhamentoService,
        AcompanharSubstituicaoDetalhamentoResolve
    ]
})
export class AcompanharSubstituicaoDetalhamentoModule { }
