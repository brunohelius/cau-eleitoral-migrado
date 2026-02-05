import { Routes } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { DetalharImpugnacao } from './detalhar-impugnacao/detalhar-impugnacao.component';
import { CadastroImpugnacaoComponent } from './cadastro-impugnacao/cadastro-impugnacao.component';
import { AcompanharImpugnacaoUf } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-uf.component';
import { AcompanharImpugnacaoUfEspecifica } from './acompanhar-impugnacao-uf-especifica/acompanhar-impugnacao-uf-especifica.component';
import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { AcompanharImpugnacaoClientResolve } from 'src/app/client/impugnacao-client/acompanhar-impugnacao-client.resolve';
import { AcompanharImpugnacaoUfClientResolve } from 'src/app/client/impugnacao-client/acompanhar-impugnacao-uf-client.resolve';
import { SolicitacaoImpugnacaoResolve } from 'src/app/client/impugnacao-candidatura-client/solicitacao-impugnacao-client.resolve';
import { AcompanharImpugnacaoChapaClientResolve } from 'src/app/client/impugnacao-client/acompanhar-impugnacao-chapa-client.resolve';
import { DefesaImpugnacaoValidacaoResolve } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-validacao-client.resolve';
import { julgamentoSegundaInstanciaResolve } from 'src/app/client/impugnacao-candidatura-client/julgamento-segunda-instancia-client.resolve';
import { ImpugnacaoRespnsavelSolicitacaoClientResolve } from 'src/app/client/impugnacao-client/impugnacao-responsavel-solicitacao-client.resolve';
import { ImpugnacaoDeclaracaoAtividadeResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-declaracao-atividade-client.resolve';
import { ImpugnacaoProfissionalSolicitanteClientResolve } from 'src/app/client/impugnacao-client/impugnacao-profissional-solicitante-client.resolve';
import { ImpugnacaoSubstituicaoResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-substituicao-client.resolve';
import { ComissaoEleicaoVigenteGuard } from 'src/app/client/comissao-eleitoral-client/comissao-eleicao-vigente.guard';

/**
 * Configurações de rota do modulo de impugnação.
 *
 * @author Squadra Tecnologia
 */
export const ImpugnacaoRoutes: Routes = [
  {
      path: 'cadastro-impugnacao',
      component: CadastroImpugnacaoComponent,
      resolve: {
        atividades: ImpugnacaoDeclaracaoAtividadeResolve
      }
  },
  {
    path: 'acompanhar',
    component: AcompanharImpugnacaoUf,
    resolve: {
      cauUfs: CauUFResolve,
      pedidos: AcompanharImpugnacaoClientResolve
    },
    /*canActivate: [
        ComissaoEleicaoVigenteGuard
    ],*/
    runGuardsAndResolvers: 'always'
  },
  {
    path: 'acompanhar-profissional-solicitante',
    component: AcompanharImpugnacaoUf,
    resolve: {
      cauUfs: CauUFResolve,
      pedidos: ImpugnacaoProfissionalSolicitanteClientResolve
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL,
      'impugnante': true
    }]
  },
  {
    path: 'acompanhar-impugnacao-uf/:id',
    component: AcompanharImpugnacaoUfEspecifica,
    resolve: {
      cauUf: BandeiraCauUFResolve,
      pedidos: AcompanharImpugnacaoUfClientResolve
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO
    }]
  },
  {
    path: 'acompanhar-impugnacao-responsavel',
    component: AcompanharImpugnacaoUfEspecifica,
    resolve: {
      pedidos: AcompanharImpugnacaoChapaClientResolve
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
    }]
  },

  {
    path: 'impugnacao-responsavel-solicitacao/:id',
    component: AcompanharImpugnacaoUfEspecifica,
    resolve: {
      pedidos: ImpugnacaoRespnsavelSolicitacaoClientResolve
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL
    }]
  },
  {
    path: ':id/detalhar',
    component: DetalharImpugnacao,
    resolve: {
      impugnacao: SolicitacaoImpugnacaoResolve,
      substituicao: ImpugnacaoSubstituicaoResolve,
      defesaValidacao: DefesaImpugnacaoValidacaoResolve,
      julgamentoSegundaInstancia: julgamentoSegundaInstanciaResolve,
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO
    }]
  },
  {
    path: ':id/detalhar-responsavel',
    component: DetalharImpugnacao,
    resolve: {
      impugnacao: SolicitacaoImpugnacaoResolve,
      substituicao: ImpugnacaoSubstituicaoResolve,
      defesaValidacao: DefesaImpugnacaoValidacaoResolve,
      julgamentoSegundaInstancia: julgamentoSegundaInstanciaResolve,
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
    }]
  },
  {
    path: ':id/detalhar-solicintante',
    component: DetalharImpugnacao,
    resolve: {
      impugnacao: SolicitacaoImpugnacaoResolve,
      substituicao: ImpugnacaoSubstituicaoResolve,
      defesaValidacao: DefesaImpugnacaoValidacaoResolve,
      julgamentoSegundaInstancia: julgamentoSegundaInstanciaResolve,
    },
    data: [{
      'tipoProfissional': Constants.TIPO_PROFISSIONAL
    }]
  }
];