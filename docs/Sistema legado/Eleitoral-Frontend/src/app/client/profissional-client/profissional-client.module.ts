import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ProfissionalResolve } from "./profissional-client.resolve";
import { ProfissionalClientService } from './profissional-client.service';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a comissão eleitoral.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ProfissionalClientService,
    ProfissionalResolve
  ]
})
export class ProfissionalClientModule {
}