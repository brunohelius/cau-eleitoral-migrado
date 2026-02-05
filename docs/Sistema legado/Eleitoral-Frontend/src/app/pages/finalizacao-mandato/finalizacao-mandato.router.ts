import { Routes } from '@angular/router';
import { SecurityGuard } from '@cau/security';
import { FinalizacaoMandatoFormComponent } from './finalizacao-mandato-form/finalizacao-mandato-form.component';



/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const FinalizacaoMandatoRoutes: Routes = [
    {
        path: '',
        redirectTo: 'incluir',
        pathMatch: 'full'
    },
    {
        path: 'incluir',
        component: FinalizacaoMandatoFormComponent,
        data: [{
            'acao': 'cadastrar'
        }],
        resolve: {}
    },
    {
        path: ':id/alterar',
        component: FinalizacaoMandatoFormComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {}
    }
];
