import { Routes } from '@angular/router';
import { SecurityGuard } from '@cau/security';

import { ListEleicaoComponent } from './list-eleicao/list-eleicao.component';
import { ListDenunciasComponent } from './list-denuncias/list-denuncias.component';
import { ListUfDenunciaComponent } from './list-uf-denuncia/list-uf-denuncia.component';

import { DenunciaClientResolve } from 'src/app/client/denuncia-client/denuncia-cliente.resolve';
import { DetalhamentoDenunciaCauUfResolve } from 'src/app/client/denuncia-client/detalhamento-denuncia-cau-uf.resolve';
import { AgrupamentoUfAtividadeSecundariaResolve } from 'src/app/client/denuncia-client/agrupamento-uf-atividade-secundaria.resolve';
import { VisualizarDenunciaComponent } from './visualizar-denuncia/visualizar-denuncia.component';
import { DenunciaAcompanharGuard } from 'src/app/client/denuncia-client/denuncia-acompanhar.guard';
import {CondicaoDenunciaResolve} from '../../client/denuncia-client/condicao-denuncia-resolve.service';
import { CalendariosAnoAtualResolve } from 'src/app/client/calendario-client/calendarios-ano-atual.resolse';

/**
 * Configurações de rota de Denuncia.
 *
 * @author Squadra Tecnologia
 */
export const DenunciaRoutes: Routes = [
    {
        path: 'acompanhamento',
        component: ListEleicaoComponent,
        resolve: {
            eleicoes: CalendariosAnoAtualResolve
        }
    },
    {
        path: 'uf/calendario/:idCalendario',
        component: ListUfDenunciaComponent,
        resolve: {
            agrupamentoUfAtividadeSecundariaResolve: AgrupamentoUfAtividadeSecundariaResolve
            //eleicoes: CalendariosConcluidosResolve
        }
    },
    {
        path: 'calendario/:idCalendario/cauUf/:idCauUf/listar',
        component: ListDenunciasComponent,
        resolve: {
            detalhamentoDenunciaCauUfResolve: DetalhamentoDenunciaCauUfResolve
            //eleicoes: CalendariosConcluidosResolve
        }
    },
    {
        path: ':id/acompanhar',
        component: VisualizarDenunciaComponent,
        // data: [{
        //     'acao': 'visualizar'
        // }],
        resolve: {
            denunciaResolve: DenunciaClientResolve,
            condicaoResolve: CondicaoDenunciaResolve,
        },
        canActivate: [
            /*SecurityGuard,
            DenunciaAcompanharGuard*/
        ]
    },



];
