import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

const routes: Routes = [
  {
    path: 'calendario',
    loadChildren: () => import('./calendario/calendario.module').then(module => module.CalendarioModule),
  },
  {
    path: 'eleicao',
    loadChildren: () => import('./eleicao/eleicao.module').then(module => module.EleicaoModule),
  },
  {
    path: 'denuncia',
    loadChildren: () => import('./denuncia/denuncia.module').then(module => module.DenunciaModule),
  },
  {
    path: 'email',
    loadChildren: () => import('./email/email.module').then(module => module.EmailModule),
  },
  {
    path: 'publicacao',
    loadChildren: () => import('./publicacao/publicacao.module').then(module => module.PublicacaoModule),
  },
  {
    path: 'finalizacaoMandato',
    loadChildren: () => import('./finalizacao-mandato/finalizacao-mandato.module').then(module => module.FinalizacaoMandatoModule),
  },

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
