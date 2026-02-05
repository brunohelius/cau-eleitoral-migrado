import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ConviteMembroComissaoService } from './convite-membro-comissao-client.service';
import { ConviteMembroComissaoResolve } from './convite-membro-comissao-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a comissão eleitoral.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ConviteMembroComissaoService,
    ConviteMembroComissaoResolve
  ]
})
export class ConviteMembroComissaoEleitoralModule {
}