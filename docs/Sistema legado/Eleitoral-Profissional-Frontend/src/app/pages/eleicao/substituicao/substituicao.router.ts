import { Routes } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { AcompanharSubstituicao } from './acompanhar-substituicao/acompanhar-substituicao.component';
import { CadastroSubstituicaoComponent } from './cadastro-substituicao/cadastro-substituicao.component';
import { AcompanharSubstituicaoUF } from './acompanhar-substituicao-uf/acompanhar-substituicao-uf.component';
import { SubstituicaoChapaClientResolve } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-eleicao-client.resolve';
import { SubstituicaoChapaUfClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-chapa-uf.resolve';
import { AcompanharSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-client.resolve';
import { PedidoSubstituicaoChapaClientResolve } from 'src/app/client/substituicao-chapa-client/pedido-substituicao-chapa-client.resolve';
import { AcompanharSubstituicaoDetalhamento } from './acompanhar-substituicao-detalhamento/acompanhar-substituicao-detalhamento.component';
import { JulgamentoSubstituicaoClientResolve } from './../../../client/substituicao-chapa-client/julgamento-segunda-instancia-client.resolve';
import { AtividadeSecundariaRecursoClientResolve } from './../../../client/substituicao-chapa-client/atividade-secundaria-recurso-client.resolve';
import { AcompanharSubstituicaoResponsavelResolve } from 'src/app/client/acompanhar-substituicao-responsavel-client/acompanhar-substituicao-responsavel-client.resolve';
import { AcompanharJuglamentoSubstituicaoChapaResolve } from 'src/app/client/acompanhar-julgamento-substituicao-client/acompanhar-julgamento-substituicao-chapa.resolve';
import { AcompanharSubstituicaoDetalhamentoResolve } from 'src/app/client/acompanhar-substituicao-detalhamento-client/acompanhar-substituicao-detalhamento-client.resolve';
import { AcompanharJuglamentoSubstituicaoComissaoResolve } from 'src/app/client/acompanhar-julgamento-substituicao-client/acompanhar-julgamento-substituicao-comissao.resolve';
import { AcompanharJulgamentoSubstituicaoComponent } from './acompanhar-substituicao-detalhamento/abas/acompanhar-julgamento-substituicao/acompanhar-julgamento-substituicao.component';
import { ComissaoEleicaoVigenteGuard } from 'src/app/client/comissao-eleitoral-client/comissao-eleicao-vigente.guard';


/**
 * Configurações de rota do modulo de julgamento.
 *
 * @author Squadra Tecnologia
 */
export const SubstituicaoRoutes: Routes = [
  {
    path: 'cadastro',
    component: CadastroSubstituicaoComponent,
    data: [{
      'acao': 'cadastrar'
    }],
    resolve: {
      chapaEleicao: SubstituicaoChapaClientResolve
    }
  },

  {
    path: 'acompanhar',
    component: AcompanharSubstituicao,
    resolve: {
      cauUfs: CauUFResolve,
      pedidos: AcompanharSubstituicaoClientResolve
    },
    /*canActivate: [
        ComissaoEleicaoVigenteGuard
    ],*/
    runGuardsAndResolvers: 'always'
  },

  {
    path: 'acompanhar-uf/:id',
    component: AcompanharSubstituicaoUF,
    resolve: {
      cauUf: BandeiraCauUFResolve,
      pedidos: SubstituicaoChapaUfClientResolve
    }
  },

  {
    path: 'acompanhar-uf',
    component: AcompanharSubstituicaoUF,
    resolve: {
      pedidos: SubstituicaoChapaUfClientResolve
    }
  },

  {
    path: 'detalhamento/:id',
    component: AcompanharSubstituicaoDetalhamento,
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO
    }],
    resolve: {
      cauUfs:  CauUFResolve,
      pedido: AcompanharSubstituicaoDetalhamentoResolve,
      atividadeSecundaria : AtividadeSecundariaRecursoClientResolve
    }
  },

  {
    path: 'acompanhar-responsavel-chapa',
    component: AcompanharSubstituicaoUF,
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
    }],
    resolve: {
      pedidos: AcompanharSubstituicaoResponsavelResolve
    }
  },

  {
    path: 'responsavel-chapa-detalhamento/:id',
    component: AcompanharSubstituicaoDetalhamento,
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
    }],
    resolve: {
      cauUfs: CauUFResolve,
      pedido: PedidoSubstituicaoChapaClientResolve,
      atividadeSecundaria : AtividadeSecundariaRecursoClientResolve
    }
  },

  {
    path: 'acompanhar-subsituicao-comissao/:idPedido',
    component: AcompanharJulgamentoSubstituicaoComponent,
    resolve: {
      julgamento: AcompanharJuglamentoSubstituicaoComissaoResolve
    }
  },

  {
    path: 'acompanhar-subsituicao-chapa/:idPedido',
    component: AcompanharJulgamentoSubstituicaoComponent,
    resolve: {
      julgamento: AcompanharJuglamentoSubstituicaoChapaResolve
    }
  },


];