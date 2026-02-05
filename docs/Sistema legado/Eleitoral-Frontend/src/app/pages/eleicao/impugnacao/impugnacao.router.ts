import { AtividadeSecundariaResolve } from './../../../client/defesa-impugnacao-client/atividade-secundaria-client.resolve';
import { JulgamentoImpugnacaoResolve } from './../../../client/defesa-impugnacao-client/julgamento-impugnacao-client.resolve';
import { Routes } from '@angular/router';
import { SecurityGuard } from '@cau/security';

import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { ChapaEleicaoAnoClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-ano.resolve';
import { RecursoImpugnanteResolve } from 'src/app/client/defesa-impugnacao-client/recurso-inpugnante-impugnacao-client.resolve';
import { SolicitacaoImpugnacaoResolve } from 'src/app/client/impugnacao-candidatura-client/solicitacao-impugnacao-client.resolve';
import { RecursoResponsavelResolve } from 'src/app/client/defesa-impugnacao-client/recurso-responsavel-impugnacao-client.resolve';
import { AcompanharImpugnacaoClientResolve } from 'src/app/client/impugnacao-candidatura-client/acompanhar-impugnacao-client.resolve';
import { EleicoesSubstituicaoChapaResolve } from 'src/app/client/substituicao-chapa-client/eleicoes-substituicao-chapa-client.resolve';
import { JulgamentoSegundaInstanciaResolve } from 'src/app/client/defesa-impugnacao-client/julgamento-segunda-instancia-client.resolve';
import { AcompanharImpugnacaoUfClientResolve } from 'src/app/client/impugnacao-candidatura-client/acompanhar-impugnacao-uf-client.resolve';
import { DefesaImpugnacaoPedidoImpugnacaoResolve } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-pedido-impugnacao-client.resolve';

import { DetalharImpugnacaoComponent } from './detalhar-impugnacao/detalhar-impugnacao.component';
import { AcompanharImpugnacaoComponent } from './acompanhar-impugnacao/acompanhar-impugnacao.component';
import { AcompanharImpugnacaoUfComponent } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-uf.component';
import { ImpugnacaoSubstituicaoResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-substituicao-client.resolve';
import { AcompanharImpugnacaoUfEspecificaComponent } from './acompanhar-impugnacao-uf-especifica/acompanhar-impugnacao-uf-especifica.component';


/**
 * Configurações de rota do modulo de impugnação.
 *
 * @author Squadra Tecnologia
 */
export const ImpugnacaoRoutes: Routes = [
    {
        path: ':id/detalhar',
        component: DetalharImpugnacaoComponent,
        resolve: {
            julgamento: JulgamentoImpugnacaoResolve,
            impugnacao: SolicitacaoImpugnacaoResolve,
            recursoImpugnante: RecursoImpugnanteResolve,
            substituicao: ImpugnacaoSubstituicaoResolve,
            recursoResponsavel: RecursoResponsavelResolve,
            defesa: DefesaImpugnacaoPedidoImpugnacaoResolve,
            atividadeSecundaria: AtividadeSecundariaResolve,
            julgamentoSegundaInstancia: JulgamentoSegundaInstanciaResolve,
        }
    },
    {
      path: 'acompanhar',
      component: AcompanharImpugnacaoComponent,
      resolve: {
        cauUfs: CauUFResolve,
        eleicoes: EleicoesSubstituicaoChapaResolve,
        eleicoesAno: ChapaEleicaoAnoClientResolve
      }
    },
    {
      path: 'acompanhar-impugnacao/:id',
      component: AcompanharImpugnacaoUfComponent,
      resolve: {
        cauUfs: CauUFResolve,
        pedidos: AcompanharImpugnacaoClientResolve
      }
    },
    {
      path: 'acompanhar-impugnacao-uf/:id/calendario/:idCalendario',
      component: AcompanharImpugnacaoUfEspecificaComponent,
      resolve: {
        cauUf: BandeiraCauUFResolve,
        pedidos: AcompanharImpugnacaoUfClientResolve
      }
    }
];