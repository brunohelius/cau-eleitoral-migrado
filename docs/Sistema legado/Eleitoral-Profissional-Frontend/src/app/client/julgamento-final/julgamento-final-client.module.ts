import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ListarUfEspecificaResolve } from './listar-uf-especifica-resolve';
import { JulgamentoFinalClientService } from './julgamento-final-client.service';
import { DetalharChapaComissaoResolve } from './detalhar-chapa-comissao.resolve';
import { DetalharChapaCadastradaResolve } from './detalhar-chapa-cadastrada.resolve';
import { QuantidadeChapasCadastradasResolve } from './quantidade-chapas-cadastradas.resolve';
import { ListarPendenciasChapaResolve } from './listar-pendencias-chapa-resolve';

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
    DetalharChapaComissaoResolve,
    ListarPendenciasChapaResolve,
    JulgamentoFinalClientService,
    DetalharChapaCadastradaResolve,
    QuantidadeChapasCadastradasResolve,
  ]
})
export class JulgamentoFinalClientModule {
}