import { UfEspecificaImpugnacaoResultadoProfissionalResolve } from './../../../client/impugnacao-resultado-client/uf-especifica-impugnacao-profissional-resultado-client.resolve';
import { Routes } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { CadastroImpugnacaoResultadoComponent } from './cadastro-impugnacao-resultado/cadastro-impugnacao-resultado.component';
import { VisualizarImpugnacaoResultadoComponent } from './visualizar-impugancao-resultado/visualizar-impugancao-resultado.component';
import { AcompanharImpugnacaoResultadoUfComponent } from './acompanhar-impugnacao-resultado-uf/acompanhar-impugnacao-resultado-uf.component';
import { AcompanharImpugnacaoResultadoUfEspecificaComponent } from './acompanhar-impugnacao-resultado-uf-especifica/acompanhar-impugnacao-resultado-uf-especifica.component';
import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { ImpugnacaoResultadoResolve } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.resolve';
import { UfImpugnacaoResultadoResolve } from 'src/app/client/impugnacao-resultado-client/uf-impugnacao-resultado-client.resolve';
import { BandeiraCauUFPorProfissionalLogadoResolve } from './../../../client/cau-uf-client/bandeira-cau-uf-por-profissional-logado-client.resolve';
import { UfEspecificaImpugnacaoResultadoResolve } from './../../../client/impugnacao-resultado-client/uf-especifica-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoChapaClientResolve } from 'src/app/client/impugnacao-resultado-client/acompanhar-impugnacao-resultado-chapa-client.resolve';
import { AcompanharImpugnacaoResultadoComissaoClientResolve } from 'src/app/client/impugnacao-resultado-client/acompanhar-impugnacao-resultado-comissao-client.resolve';
import { AtividadeSecundariaCadastroImpugnacaoResolve } from 'src/app/client/impugnacao-resultado-client/atividade-secundaria-cadastro-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoClientResolve } from 'src/app/client/impugnacao-resultado-client/acompanhar-impugnacao-resultado-client-resolve';
import { ImpugnacaoResultadoChapaResolve } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-chapa-client.resolve';
import { ImpugnacaoResultadoImpugnanteResolve } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-impugnante-client.resolve';
import { ImpugnacaoResultadoComissaoResolve } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-comissao-client.resolve';


/**
 * Configurações de rota de Calendário.
 *
 * @author Squadra Tecnologia
 */
export const ImpugnacaoResultadoRoutes: Routes = [
    {
        path: 'cadastro',
        component: CadastroImpugnacaoResultadoComponent,
        data: [{
            'acao': 'cadastrar'
        }],
        resolve: {
            cauUfs: UfImpugnacaoResultadoResolve,
            atividade: AtividadeSecundariaCadastroImpugnacaoResolve,
        },
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar',
        component: AcompanharImpugnacaoResultadoUfComponent,
        resolve: {
          cauUfs: CauUFResolve,
          pedidos: AcompanharImpugnacaoResultadoClientResolve,
        },
        data: [{
            'isMeusPedidos': true,
            'tipoProfissional': Constants.TIPO_PROFISSIONAL
          }],
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar/:idCauUf',
        component: AcompanharImpugnacaoResultadoUfEspecificaComponent,
        resolve: {
            cauUf: BandeiraCauUFResolve,
            impugnacoes: UfEspecificaImpugnacaoResultadoResolve,
        },
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar-profissional/:idCauUf',
        component: AcompanharImpugnacaoResultadoUfEspecificaComponent,
        resolve: {
            cauUf: BandeiraCauUFResolve,
            impugnacoes: UfEspecificaImpugnacaoResultadoProfissionalResolve,
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL
        }],
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar-comissao',
        component: AcompanharImpugnacaoResultadoUfComponent,
        resolve: {
          cauUfs: CauUFResolve,
          pedidos: AcompanharImpugnacaoResultadoComissaoClientResolve,
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO,
            'isMeusPedidos': false
        }],
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar-chapa',
        component: AcompanharImpugnacaoResultadoUfEspecificaComponent,
        resolve: {
            impugnacoes: AcompanharImpugnacaoResultadoChapaClientResolve,
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
        }],
        runGuardsAndResolvers: 'always'
    },
    {
        path: ':idCauUf/comissao/visualizar/:id',
        component: VisualizarImpugnacaoResultadoComponent,
        data: [{
            'acao': 'visualizar',
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO,
        }],
        resolve: {
            cauUf: BandeiraCauUFResolve,
            impugnacao: ImpugnacaoResultadoComissaoResolve
        },
        runGuardsAndResolvers: 'always'
    },
    {
        path: ':idCauUf/chapa/visualizar/:id',
        component: VisualizarImpugnacaoResultadoComponent,
        data: [{
            'acao': 'visualizar',
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
        }],
        resolve: {
            cauUf: BandeiraCauUFResolve,
            impugnacao: ImpugnacaoResultadoChapaResolve
        },
        runGuardsAndResolvers: 'always'
    },
    {
        path: ':idCauUf/profissional/visualizar/:id',
        component: VisualizarImpugnacaoResultadoComponent,
        data: [{
            'acao': 'visualizar',
            'tipoProfissional': Constants.TIPO_PROFISSIONAL
        }],
        resolve: {
            cauUf: BandeiraCauUFResolve,
            impugnacao: ImpugnacaoResultadoImpugnanteResolve
        },
        runGuardsAndResolvers: 'always'
    },
];
