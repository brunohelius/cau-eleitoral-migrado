import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EleicaoClientService } from './eleicao-client.service';
import { EleicoesAnosConcluidasClientResolve } from './eleicoes-anos-concluidas-client.resolve';
import { EleicoesConcluidasInativasClientResolve } from './eleicoes-concluidas-inativas-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a eleição.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    EleicaoClientService,
    EleicoesAnosConcluidasClientResolve,
    EleicoesConcluidasInativasClientResolve,
  ]
})
export class EleicaoClientModule {
}