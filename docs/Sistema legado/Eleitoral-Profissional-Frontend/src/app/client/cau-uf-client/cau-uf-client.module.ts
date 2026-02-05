import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import {CauUFService} from './cau-uf-client.service';
import {CauUFResolve} from './cau-uf-client.resolve';


/**
 * Modulo de integração do projeto frontend com os serviços backend referente a comissão eleitoral.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    CauUFService,
    CauUFResolve
  ]
})
export class CauUFClientModule {
}
