import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ComissaoEleitoralService } from './comissao-eleitoral-client.service';
import { ComissaoEleitoralResolve } from './comissao-eleitoral-client.resolve';
import { ComissoesEleitoraisResolve } from './comissoes-eleitorais-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a comissão eleitoral.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ComissaoEleitoralResolve,
    ComissoesEleitoraisResolve,
    ComissaoEleitoralService
  ]
})
export class ComissaoEleitoralModule {
}