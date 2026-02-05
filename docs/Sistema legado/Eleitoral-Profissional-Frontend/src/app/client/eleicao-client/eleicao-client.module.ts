import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { EleicaoClientService } from './eleicao-client.service';
import { EleicaoClientResolve } from './eleicao-client.resolve';
import { EleicoesConcluidasInativasClientResolve } from './eleicoes-concluidas-inativas-client.resolve';
import { EleicoesMembroComissaoClientResolve } from './eleicos-membro-comissao-client.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a eleição.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    EleicaoClientResolve,
    EleicaoClientService,
    EleicoesConcluidasInativasClientResolve,
    EleicoesMembroComissaoClientResolve
  ]
})
export class EleicaoClientModule {
}