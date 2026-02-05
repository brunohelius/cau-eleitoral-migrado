import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ExtratoChapaComponent } from './extrato-chapa/extrato-chapa.component';
import { ChapasExtratoClientResolve } from '../client/chapa-client/chapa-extrato-client.resolve';
import { BandeiraCauUFResolve } from '../client/cau-uf-client/bandeira-cau-uf-client.resolve';

const routes: Routes = [
  {
    path: 'extrato-chapa/:idCauUf',
    component: ExtratoChapaComponent,
    resolve: {
      dadosExtratoChapa: ChapasExtratoClientResolve,
      cauUf: BandeiraCauUFResolve,
    }
  }
];

/**
 * Configuração global de rotas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
  imports: [
    RouterModule.forChild(routes)
  ],
  exports: [RouterModule]
})

export class PagesPublicRoutingModule {

}
