import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MembrosPorSituacaoResolve } from './membros-por-situacao.resolve';
import { ListarUfEspecificaResolve } from './listar-uf-especifica-resolve';
import { JulgamentoFinalClientService } from './julgamento-final-client.service';
import { DetalharChapaCadastradaResolve } from './detalhar-chapa-cadastrada.resolve';
import { VisualizarJulgamentoFinalPrimeiraResolve } from './visualizar-julgamento-final-resolve';
import { QuantidadeChapasCadastradasResolve } from './quantidade-chapas-cadastradas.resolve';
import { ListarPendenciasChapaResolve } from './listar-pendencias-chapa';


/**
 * Modulo de integração do projeto frontend com os serviços backend referente ao  julgamento final.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    ListarUfEspecificaResolve,
    MembrosPorSituacaoResolve,
    JulgamentoFinalClientService,
    ListarPendenciasChapaResolve,
    DetalharChapaCadastradaResolve,
    QuantidadeChapasCadastradasResolve,
    VisualizarJulgamentoFinalPrimeiraResolve,
  ]
})
export class JulgamentoFinalClientModule {
}