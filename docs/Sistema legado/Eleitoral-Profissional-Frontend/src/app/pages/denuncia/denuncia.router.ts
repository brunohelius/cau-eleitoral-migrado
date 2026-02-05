import { Routes } from '@angular/router';
import { FormDenunciaComponent } from "./form-denuncia/form-denuncia.component";
import { VisualizarDenunciaComponent } from './visualizar-denuncia/visualizar-denuncia.component';
import { TipoDenunciaClientResolve } from 'src/app/client/denuncia-client/denuncia-tipo-denuncia-client.resolve';
import { AtividadeSecundariaCadastroDenunciaClientResolve } from 'src/app/client/eleicao-client/eleicoes-atividade-secundaria-cadastro-denuncia-client.resolve';
import { DenunciaClientResolve } from 'src/app/client/denuncia-client/denuncia-client.resolve';
import { AbaListaDenunciaEstadoComponent } from './visualizar-denuncia/aba-lista-denuncia-estado/aba-lista-denuncia-estado.component';
import { DenunciaAgrupamentoUfAtividadeSecundariaResolve } from 'src/app/client/denuncia-client/denuncia-agrupamento-uf-atividade-secundaria.resolve';
import { AbaListaDenunciasComponent } from './visualizar-denuncia/aba-lista-denuncias/aba-lista-denuncias.component';
import { DetalhamentoDenunciaCauUfResolve } from 'src/app/client/denuncia-client/denuncia-detalhamento-cau-uf.resolve';
import { DenunciaRecebidasClientResolve } from 'src/app/client/denuncia-client/denuncia-recebidas-client.resolve';
import { ListDenunciaEstadoComissaoComponent } from './comissao/list-denuncia-estado/list-denuncia-estado-comissao.component';
import { DetalhamentoDenunciaRelatoriaProfissionalResolve } from 'src/app/client/denuncia-client/denuncia-detalhamento-relatoria-profissional.resolve';
import { ListDenunciasComissaoComponent } from './comissao/list-denuncias/list-denuncias-comissao.component';
import { DenunciaComissaoAgrupamentoUfResolve } from 'src/app/client/denuncia-client/denuncia-comissao-agrupamento-uf.resolve';
import { DetalhamentoDenunciaComissaoCauUfResolve } from 'src/app/client/denuncia-client/denuncia-comissao-detalhamento-cau-uf.resolve';
import { DenunciaNaoAdmitidaComissaoRecebidasClientResolve } from 'src/app/client/denuncia-client/denuncia-nao-admitida-comissao-client.resolve';
import { DenunciaTipoMembroComissaoClientResolve } from 'src/app/client/denuncia-client/denuncia-tipo-membro-comissao-client.resolve';
import { DenunciaUsuarioClientResolve } from 'src/app/client/denuncia-client/denuncia-usuario-client.resolve';
import { DenunciaComissaoGuard } from 'src/app/client/denuncia-client/denuncia-comissao.guard';
import { DenunciaAcompanharGuard } from 'src/app/client/denuncia-client/denuncia-acompanhar.guard';
import { ModalVisualizarParecerFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-parecer-final/modal-visualizar-parecer-final.component';
import {CondicaoDenunciaResolve} from '../../client/denuncia-client/condicao-denuncia-resolve.service';
import { ComissaoEleicaoVigenteGuard } from 'src/app/client/comissao-eleitoral-client/comissao-eleicao-vigente.guard';

/**
 * Configurações de rota de Denúncia.
 *
 * @author Squadra Tecnologia
 */
export const DenunciaRoutes: Routes = [
  {
    path: 'cadastro',
    component: FormDenunciaComponent,
    data: [{
      'acao': 'cadastrar'
    }],
    resolve: {
      'tiposDenuncia': TipoDenunciaClientResolve,
      'atividadeSecundaria': AtividadeSecundariaCadastroDenunciaClientResolve,
    }
  },
  {
    path: 'visualizar/:id/tipoDenuncia/:tipoDenuncia',
    component: VisualizarDenunciaComponent,
    data: [{
      'acao': 'visualizar'
    }],
    resolve: {
      'denuncia': DenunciaClientResolve,
      'usuarioDenunciadoResponsavel' : DenunciaUsuarioClientResolve,
      'tipoMembroComissao' : DenunciaTipoMembroComissaoClientResolve,
      'condicaoResolve': CondicaoDenunciaResolve,
    },
    /*canActivate: [
      DenunciaAcompanharGuard
    ],*/
    runGuardsAndResolvers: 'always'
  },
  {
    path: 'comissao/visualizar/:id/tipoDenuncia/:tipoDenuncia',
    component: VisualizarDenunciaComponent,
    data: [{
      'acao': 'visualizar'
    }],
    resolve: {
      'denuncia': DenunciaClientResolve,
      'usuarioDenunciadoResponsavel' : DenunciaUsuarioClientResolve,
      'tipoMembroComissao' : DenunciaTipoMembroComissaoClientResolve,
      'condicaoResolve': CondicaoDenunciaResolve
    },
    /*canActivate: [
      DenunciaComissaoGuard
    ],*/
    runGuardsAndResolvers: 'always'
  },
  {
    path: 'acompanhamento',
    component: AbaListaDenunciaEstadoComponent,
    data: [{
      'acao': 'acompanhar'
    }],
    resolve: {
      'agrupamentoUf': DenunciaAgrupamentoUfAtividadeSecundariaResolve,
      'denunciasRecebidas': DenunciaRecebidasClientResolve
    }
  },
  {
    path: 'cauUf/:idCauUf/listar',
    component: AbaListaDenunciasComponent,
    data: [{
      'acao': 'listar'
    }],
    resolve: {
        detalhamentoDenunciaCauUfResolve: DetalhamentoDenunciaCauUfResolve
    }
  },
  {
    path: 'cauUf/:idCauUf/listar-denuncias-relatoria',
    component: AbaListaDenunciasComponent,
    data: [{
      'acao': 'listar'
    }],
    resolve: {
        detalhamentoDenunciaCauUfResolve: DetalhamentoDenunciaRelatoriaProfissionalResolve
    }
  },
  {
    path: 'comissao/acompanhamento',
    component: ListDenunciaEstadoComissaoComponent,
    data: [{
      'acao': 'acompanhar'
    }],
    resolve: {
      'agrupamentoUf': DenunciaComissaoAgrupamentoUfResolve,
      'denunciasNaoAdmitidas': DenunciaNaoAdmitidaComissaoRecebidasClientResolve,
      'denunciasRelatoria': DetalhamentoDenunciaRelatoriaProfissionalResolve
    },
    /*canActivate: [
        ComissaoEleicaoVigenteGuard
    ],*/
    runGuardsAndResolvers: 'always'
  },
  {
    path: 'comissao/cauUf/:idCauUf/listar',
    component: ListDenunciasComissaoComponent,
    data: [{
      'acao': 'listar'
    }],
    resolve: {
        detalhamentoDenunciaCauUfResolve: DetalhamentoDenunciaComissaoCauUfResolve
    }
  },
  {
    path: 'parecer-final',
    component: ModalVisualizarParecerFinalComponent
  },
];
