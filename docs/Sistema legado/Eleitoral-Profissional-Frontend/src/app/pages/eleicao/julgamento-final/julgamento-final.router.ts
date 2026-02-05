import { Routes } from '@angular/router';
import { Component } from '@angular/core';
import { Constants } from 'src/app/constants.service';

import { ListarUfsComponent } from './listar-ufs/listar-ufs.component';
import { ListarUfEspecifica } from './listar-uf-especifica/listar-uf-especifica.component';
import { AbasJulgamentoFinalComponent } from './abas-julgamento-final/abas-julgamento-final.component';

import { ListarPendencias } from './listar-pendencias/listar-pendencias.component';
import { CauUFResolve } from './../../../client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { ListarUfEspecificaResolve } from 'src/app/client/julgamento-final/listar-uf-especifica-resolve';
import { DetalharChapaComissaoResolve } from 'src/app/client/julgamento-final/detalhar-chapa-comissao.resolve';
import { DetalharChapaCadastradaResolve } from 'src/app/client/julgamento-final/detalhar-chapa-cadastrada.resolve';
import { QuantidadeChapasCadastradasResolve } from './../../../client/julgamento-final/quantidade-chapas-cadastradas.resolve';
import { ListarPendenciasChapaResolve } from 'src/app/client/julgamento-final/listar-pendencias-chapa-resolve';
import { ComissaoEleicaoVigenteGuard } from 'src/app/client/comissao-eleitoral-client/comissao-eleicao-vigente.guard';


/**
 * Configurações de rota do modulo de impugnação.
 *
 * @author Squadra Tecnologia
 */
export const JulgamentoFinalRoutes: Routes = [
    {
        path: 'acompanhar-ufs',
        component: ListarUfsComponent,
        resolve: {
            cauUfs: CauUFResolve,
            chapas: QuantidadeChapasCadastradasResolve
        },
        /*canActivate: [
            ComissaoEleicaoVigenteGuard
        ],*/
        runGuardsAndResolvers: 'always'
    },
    {
        path: 'acompanhar-uf/:id',
        component: ListarUfEspecifica,
        resolve: {
            cauUf: BandeiraCauUFResolve,
            chapas: ListarUfEspecificaResolve
        }
    },
    {
        path: 'acompanhar-uf',
        component: ListarUfEspecifica,
        resolve: {
            chapas: ListarUfEspecificaResolve
        }
    },
    {
        path: 'chapaEleicao',
        component: AbasJulgamentoFinalComponent,
        resolve: {
            dadosChapa: DetalharChapaCadastradaResolve
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_CHAPA
          }]
    },
    {
        path: 'membroComissao/chapaEleicao/:idChapa',
        component: AbasJulgamentoFinalComponent,
        resolve: {
            dadosChapa: DetalharChapaComissaoResolve
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO
          }]
    },
    {
        path: 'membroComissaoUf/chapaEleicao/:idChapa',
        component: AbasJulgamentoFinalComponent,
        resolve: {
            dadosChapa: DetalharChapaComissaoResolve
        },
        data: [{
            'tipoProfissional': Constants.TIPO_PROFISSIONAL_COMISSAO_CE
          }]
    },
    {
        path:'chapa-eleicao/listar-pendencias/:idChapa',
        component: ListarPendencias,
        resolve: {
            solicitacoesChapa: ListarPendenciasChapaResolve
        }
    },

];
