import { Routes } from '@angular/router';

import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { ChapaEleicaoAnoClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-ano.resolve';
import { EleicoesChapaClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-client.resolve';
import { ListarUfEspecificaResolve } from 'src/app/client/julgamento-final/listar-uf-especifica-resolve';
import { MembrosPorSituacaoResolve } from 'src/app/client/julgamento-final/membros-por-situacao.resolve';
import { RecursoJulgamentoFinalResolve } from 'src/app/client/julgamento-final/Recurso-julgamento-final.resolve';
import { DetalharChapaCadastradaResolve } from 'src/app/client/julgamento-final/detalhar-chapa-cadastrada.resolve';
import { QuantidadeChapasCadastradasResolve } from 'src/app/client/julgamento-final/quantidade-chapas-cadastradas.resolve';

import { ListarUfsComponent } from './listar-ufs/listar-ufs.component';
import { ListarUfEspecifica } from './listar-uf-especifica/listar-uf-especifica.component';
import { AbasJulgamentoFinalComponent } from './abas-julgamento-final/abas-julgamento-final.component';
import { ListarEleicaoJulgamentoComponent } from './listar-eleicao/listar-eleicao-julgamento.component';
import { ListarPendencias } from './listar-pendencias/listar-pendencias.component';
import { ListarPendenciasChapaResolve } from 'src/app/client/julgamento-final/listar-pendencias-chapa';
import { DadosJulgamentoFinalSegundaInstanciaResolve } from 'src/app/client/julgamento-final/dados-julgamento-fina-segunda-instancial.resolve';


/**
 * Configurações de rota do modulo de impugnação.
 *
 * @author Squadra Tecnologia
 */
export const JulgamentoFinalRoutes: Routes = [
    {
        path: 'calendario/listar',
        component: ListarEleicaoJulgamentoComponent,
        resolve: {
            eleicoes: EleicoesChapaClientResolve,
            eleicoesAno: ChapaEleicaoAnoClientResolve
        }
    },
    {
        path: 'acompanhar-ufs/:id',
        component: ListarUfsComponent,
        resolve: {
            cauUfs: CauUFResolve,
            chapas: QuantidadeChapasCadastradasResolve
        }
    },
    {
        path: 'acompanhar-uf/:id/calendario/:idCalendario',
        component: ListarUfEspecifica,
        resolve: {
            cauUf: BandeiraCauUFResolve,
            chapas: ListarUfEspecificaResolve
        }
    },
    {
        path: 'chapaEleicao/:idChapa',
        component: AbasJulgamentoFinalComponent,
        resolve: {
            membros: MembrosPorSituacaoResolve,
            recurso: RecursoJulgamentoFinalResolve,
            dadosChapa: DetalharChapaCadastradaResolve,
            dadosJulgamentoFinalSegundaInstancia: DadosJulgamentoFinalSegundaInstanciaResolve
        }
    },
    {
        path:'chapa-eleicao/listar-pendencias/:idChapa',
        component: ListarPendencias,
        resolve: {
            solicitacoesChapa: ListarPendenciasChapaResolve
        }
    },

];

