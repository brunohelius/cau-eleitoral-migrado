import { Routes } from '@angular/router';
import { ConviteChapaComponent } from './convite-chapa/convite-chapa.component';
import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { CadastroChapaComponent } from './cadastro-chapa/cadastro-chapa.component';
import { VisualizarChapaComponent } from './visualizar-chapa/visualizar-chapa.component';
import { ComissaoEleitoralComponent } from './comissao-eleitoral/comissao-eleitoral.component';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { ConviteMembroComissaoComponent } from './convite-membro-comissao/convite-membro-comissao.component';
import { ComissaoEleitoralResolve } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.resolve';
import { TipoParticipacaoClientResolve } from 'src/app/client/eleicao-client/eleicoes-tipo-participacao-client.resolve';
import { ComissaoEleitoralAceiteResolve } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-aceite.resolve';
import { ComissaoEleitoralInfoResolve } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-info-client.resolve';
import { ConvitesRecebidosClientResolve } from 'src/app/client/convite-chapa-eleicao-client/convites-recebidos.client.resolve';
import { ChapaEleicaoAcompanharClientResolve } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-acompanhar-client.resolve';
import { EleicoesConcluidasInativasClientResolve } from 'src/app/client/eleicao-client/eleicoes-concluidas-inativas-client.resolve';
import { ConviteMembroComissaoResolve } from 'src/app/client/convite-membro-comissao-client/convite-membro-comissao-client.resolve';
import { ChapaEleicaoUfResponsavelClientResolve } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-uf-responsavel-client.resolve';
import { EleicoesAnosConcluidasInativasClientResolve } from 'src/app/client/eleicao-client/eleicoes-anos-concluidas-inativas-client.resolve';
import { EleicoesMembroComissaoClientResolve } from 'src/app/client/eleicao-client/eleicos-membro-comissao-client.resolve';
import { ComissaoEleicaoVigenteGuard } from 'src/app/client/comissao-eleitoral-client/comissao-eleicao-vigente.guard';


/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const EleicaoRoutes: Routes = [
    {
        path: 'impugnacao',
        loadChildren: () => import('./impugnacao/impugnacao.module').then(module => module.ImpugnacaoModule),
    },
    {
        path: 'substituicao',
        loadChildren: () => import('./substituicao/substituicao.module').then(module => module.SubstituicaoModule),
    },
    {
        path: 'julgamento-final',
        loadChildren: () => import('./julgamento-final/julgamento-final.module').then(module => module.JulgamentoFinalModule),
    },
    {
        path: 'impugnacao-resultado',
        loadChildren: () => import('./impugnacao-resultado/impugnacao-resultado.module').then(module => module.ImpugnacaoResultadoModule),
    },
    {
        path: 'comissao-eleitoral',
        component: ComissaoEleitoralComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            aceite: ComissaoEleitoralAceiteResolve,
            cauUfs: CauUFResolve,
            comissaoEleitoral: ComissaoEleitoralResolve,
            calendarios: EleicoesMembroComissaoClientResolve,
            infoComissaoEleitoral: ComissaoEleitoralInfoResolve
        },
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'convite',
        component: ConviteMembroComissaoComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            declaracao: ConviteMembroComissaoResolve
        }
    },
    {
        path: 'cadastro-chapa',
        component: CadastroChapaComponent,
        data: [{
            'acao': 'cadastrar'
        }],
        resolve: {
            chapaEleicao: ChapaEleicaoUfResponsavelClientResolve
        }
    },
    {
        path: 'convite-chapa',
        component: ConviteChapaComponent,
        resolve: {
            convites: ConvitesRecebidosClientResolve
        }
    },
    {
        path: 'visualizar-chapa',
        component: VisualizarChapaComponent,
        data: [{
            'acao': 'visualizar'
        }],
        resolve: {
            chapaEleicao: ChapaEleicaoAcompanharClientResolve
        }
    },

];
