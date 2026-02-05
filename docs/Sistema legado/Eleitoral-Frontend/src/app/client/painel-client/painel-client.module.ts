import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PainelClientService } from './painel-client.service';

/**
 * Modulo de integração do projeto frontend com os serviços backend.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    PainelClientService
  ]
})
export class PainelClientModule {
}
