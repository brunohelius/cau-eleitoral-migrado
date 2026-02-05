import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';

import {DenunciaClientService} from './denuncia-client.service';
import { DenunciaRecebidasClientResolve } from './denuncia-recebidas-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente ao módulo de denúncia.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    DenunciaClientService,
    DenunciaRecebidasClientResolve
  ]
})
export class DenunciaClientModule {
}
