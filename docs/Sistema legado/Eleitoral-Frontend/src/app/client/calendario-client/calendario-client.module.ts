import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CalendarioResolve } from './calendario.resolve'
import { CalendariosResolve } from './calendarios.resolve';
import { CalendarioClientService } from './calendario-client.service';
import { CalendarioEleicaoResolve } from './calendario-eleicao.resolve';
import { CalendariosAnoAtualResolve } from './calendarios-ano-atual.resolse';
/**
 * Modulo de integração do projeto frontend com os serviços backend.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    CalendarioResolve,
    CalendariosResolve,
    CalendarioClientService,
    CalendarioEleicaoResolve,
    CalendariosAnoAtualResolve,
  ]
})
export class CalendarioClientModule {
}
