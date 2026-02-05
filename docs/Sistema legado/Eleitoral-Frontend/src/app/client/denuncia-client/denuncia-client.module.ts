import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DenunciaClientService } from './denuncia-client.service';
import { AgrupamentoUfAtividadeSecundariaResolve } from './agrupamento-uf-atividade-secundaria.resolve';
import { DetalhamentoDenunciaCauUfResolve } from './detalhamento-denuncia-cau-uf.resolve';
import {CondicaoDenunciaResolve} from './condicao-denuncia-resolve.service';

/**
 * Modulo de integração do projeto frontend com os serviços backend referente a Denuncia.
 */
@NgModule({
  declarations: [],
  imports: [
    CommonModule
  ],
  providers: [
    DenunciaClientService,
    DetalhamentoDenunciaCauUfResolve,
    AgrupamentoUfAtividadeSecundariaResolve,
    CondicaoDenunciaResolve,
  ]
})
export class DenunciaClientModule {
}