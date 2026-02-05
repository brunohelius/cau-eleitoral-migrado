import { Routes } from '@angular/router';
import { UFClientResolve } from 'src/app/client/uf-client/uf-client.resolve';
import { CabecalhoEmailClientResolve } from 'src/app/client/cabecalho-email/cabecalho-email-client.resolve';
import { FormCabecalhoEmailComponent } from './cabecalho-email/form-cabecalho-email/form-cabecalho-email.component';
import { ListCabecalhoEmailComponent } from './cabecalho-email/list-cabecalho-email/list-cabecalho-email.component';
import { ListCabecalhoEmailClientResolve } from 'src/app/client/cabecalho-email/list-cabecalho-email-client.resolve';
import { ListCorpoEmailComponent } from './corpo-email/list-corpo-email/list-corpo-email.component';
import { CorposEmailClientResolve } from 'src/app/client/corpo-email/corpos-email-client.resolve';
import { FormCorpoEmailComponent } from './corpo-email/form-corpo-email/form-corpo-email.component';
import { CorpoEmailClientResolve } from 'src/app/client/corpo-email/corpo-email-client.resolve';
import { AtividadesPrincipaisClientResolve } from 'src/app/client/atividade-principal-client/atividades-principais-client.resolve';
import { ListCabecalhoEmailAtivosClientResolve } from 'src/app/client/cabecalho-email/list-cabecalho-email-ativos-client.resolve';
import { SecurityGuard } from '@cau/security';

/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const EmailRoutes: Routes = [
    {
        path: 'cabecalho/incluir',
        component: FormCabecalhoEmailComponent,
        data: [{
            'acao': 'incluir'
        }],
        resolve: {
            ufs: UFClientResolve,
        }
    },
    {
        path: 'cabecalho/:id/alterar',
        component: FormCabecalhoEmailComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {
            ufs: UFClientResolve,
            cabecalhoEmail: CabecalhoEmailClientResolve
        }
    },
    {
        path: 'cabecalho/:id/visualizar',
        component: FormCabecalhoEmailComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            ufs: UFClientResolve,
            cabecalhoEmail: CabecalhoEmailClientResolve
        }
    },
    {
        path: 'cabecalho/listar',
        component: ListCabecalhoEmailComponent,
        data: [{
            'acao': 'listar'
        }],
        resolve: {
            ufs: UFClientResolve,
            cabecalhosEmail: ListCabecalhoEmailClientResolve
        }
    },
    {
        path: 'corpo/listar',
        component: ListCorpoEmailComponent,
        data: [{
            'acao': 'listar'
        }],
        resolve: {
            corposEmail: CorposEmailClientResolve
        }
    },
    {
        path: 'corpo/:id/alterar',
        component: FormCorpoEmailComponent,
        data: [{
            'acao': 'alterar'
        }],
        resolve: {
            corpoEmail: CorpoEmailClientResolve,
            cabecalhosEmail: ListCabecalhoEmailAtivosClientResolve,
            atividadesPrincipais: AtividadesPrincipaisClientResolve
        }
    },
    {
        path: 'corpo/:id/visualizar',
        component: FormCorpoEmailComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            corpoEmail: CorpoEmailClientResolve,
            cabecalhosEmail: ListCabecalhoEmailAtivosClientResolve,
            atividadesPrincipais: AtividadesPrincipaisClientResolve
        }
    },
    {
        path: 'corpo/incluir',
        component: FormCorpoEmailComponent,
        data: [{
            'acao': 'incluir'
        }],
        resolve: {
            cabecalhosEmail: ListCabecalhoEmailAtivosClientResolve,
            atividadesPrincipais: AtividadesPrincipaisClientResolve
        }
    }
];