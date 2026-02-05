import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AcompanharSubstituicaoResponsavelService } from './acompanhar-substituicao-responsavel-client.service';
import { AcompanharSubstituicaoResponsavelResolve } from './acompanhar-substituicao-responsavel-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a chapa eleção para Responsavel.
 */
@NgModule({
    declarations: [],
    imports: [
        CommonModule
    ],
    providers: [
        AcompanharSubstituicaoResponsavelService,
        AcompanharSubstituicaoResponsavelResolve
    ]
})
export class AcompanharSubstituicaoResponsavelModule { }
