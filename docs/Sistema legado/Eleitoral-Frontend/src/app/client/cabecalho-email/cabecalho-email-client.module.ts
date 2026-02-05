import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CabecalhoEmailClientResolve } from './cabecalho-email-client.resolve';
import { CabecalhoEmailClientService } from './cabecalho-email-client.service';
import { ListCabecalhoEmailClientResolve } from './list-cabecalho-email-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    CabecalhoEmailClientResolve,
    CabecalhoEmailClientService,
    ListCabecalhoEmailClientResolve
  ]
})
export class CabecalhoEmailClientModule { }