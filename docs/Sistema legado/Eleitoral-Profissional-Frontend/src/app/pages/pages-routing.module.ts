import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PaginaInicialComponent } from './home/pagina-inicial.component';

const routes: Routes = [
  {
    path: '',
    component: PaginaInicialComponent
  },
  {
    path: 'eleicao',
    loadChildren: () => import('./eleicao/eleicao.module').then(module => module.EleicaoModule),
  },
  {
    path: 'denuncia',
    loadChildren: () => import('./denuncia/denuncia.module').then(module => module.DenunciaModule),
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

export class PagesRoutingModule {
}
