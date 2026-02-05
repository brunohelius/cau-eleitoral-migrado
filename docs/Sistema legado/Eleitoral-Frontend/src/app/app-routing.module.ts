import { NgModule } from '@angular/core';
import { LoginResolve } from '@cau/security';
import { LayoutsComponent, GerarCookieComponent } from '@cau/layout';
import { Routes, RouterModule } from '@angular/router';
import { environment } from 'src/environments/environment';

const routes: Routes = [
    {
        path: 'publico',
        component: LayoutsComponent,
        loadChildren: () => import('./pages-public/pages-public.module').then(module => module.PagesPublicModule),       
    },
    {
        path: '',
        component: LayoutsComponent,
        data:{
            urlAcesso: environment.urlLogout
          },
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
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
