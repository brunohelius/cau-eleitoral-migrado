import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ComissaoEleitoralService } from './comissao-eleitoral-client.service';
import { ComissaoEleitoralResolve } from './comissao-eleitoral-client.resolve';
import { ComissoesEleitoraisResolve } from './comissoes-eleitorais-client.resolve';
import { ComissaoEleitoralInfoResolve } from './comissao-eleitoral-info-client.resolve';
import { ComissaoEleitoralAceiteResolve } from './comissao-eleitoral-aceite.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a comissão eleitoral.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ComissaoEleitoralService,
    ComissaoEleitoralResolve,
    ComissoesEleitoraisResolve,
    ComissaoEleitoralInfoResolve,
    ComissaoEleitoralService,
    ComissaoEleitoralAceiteResolve
  ]
})
export class ComissaoEleitoralModule {
}
