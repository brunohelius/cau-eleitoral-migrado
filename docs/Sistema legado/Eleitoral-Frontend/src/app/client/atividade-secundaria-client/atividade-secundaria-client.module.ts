import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AtividadePrincipalClientService } from '../atividade-principal-client/atividade-principal-client.service';
import { AtividadeSecundariaCalendarioResolve } from './atividade-secundaria-calendario.resolve';
import { AtividadeSecundariaProfissionaisTotaisResolve } from './atividade-secundaria-profissionais-totais.resolve';

/**
 * Modulo de integração do projeto frontend com os serviços backend.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    AtividadePrincipalClientService,
    AtividadeSecundariaCalendarioResolve,
    AtividadeSecundariaProfissionaisTotaisResolve
  ]
})
export class AtividadeSecundariaClientModule { }
