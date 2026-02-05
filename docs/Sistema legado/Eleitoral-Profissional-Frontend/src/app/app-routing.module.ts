import { NgModule } from '@angular/core';
import { LoginResolve } from '@cau/security';
import { LayoutsComponent, GerarCookieComponent } from '@cau/layout';
import { Routes, RouterModule } from '@angular/router';

const routes: Routes = [
  {
    path: '',
    component: LayoutsComponent,
    loadChildren: () => import('./pages/pages.module').then(module => module.PagesModule),
    resolve: {
      credential: LoginResolve,
    }
  },
  {
    path: 'cookie',
    component: GerarCookieComponent
  }
];

/**
 * @author Squadra Tecnologia
 */
@NgModule({
  imports: [RouterModule.forRoot(routes, {
    onSameUrlNavigation: 'reload'
  })],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
