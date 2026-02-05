import { BandeiraCauUFResolve } from './../../../client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { Routes } from '@angular/router';
import { SecurityGuard } from '@cau/security';

import { CauUFResolve } from 'src/app/client/cau-uf-client/cau-uf-client.resolve';
import { ChapaEleicaoAnoClientResolve } from 'src/app/client/chapa-client/eleicoes-chapa-ano.resolve';
import { AcompanharImpugnacaoResultadoUfComponent } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-resultado-uf.component';
import { AcompanharImpugnacaoResultadoComponent } from './acompanhar-impugnacao-resultado/acompanhar-impugnacao-resultado.component';
import { EleicoesSubstituicaoChapaResolve } from 'src/app/client/substituicao-chapa-client/eleicoes-substituicao-chapa-client.resolve';
import { DetalharImpugnacaoResultadoClientResolve } from 'src/app/client/impugnacao-resultado-client/detalhar-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoClientResolve } from 'src/app/client/impugnacao-resultado-client/acompanhar-impugnacao-resultado-client.resolve';
import { UfEspecificaImpugnacaoResultadoResolve } from './../../../client/impugnacao-resultado-client/uf-especifica-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoUfEspecificaComponent } from './acompanhar-impugnacao-resultado-uf-especifica/acompanhar-impugnacao-resultado-uf-especifica.component';
import { DetalharImpugnacaoResultadoComponent } from './detalhar-impugnacao-resultado/detalhar-impugnacao-resultado.component';
import { EleicoesImpugnacaoResultadoResolve } from 'src/app/client/impugnacao-resultado-client/eleicoes-impugnacao-resultado-client.resolve';

/**
 * Configurações de rota do modulo de impugnação.
 *
 * @author Squadra Tecnologia
 */
export const ImpugnacaoResultadoRoutes: Routes = [
  {
    path: 'acompanhar',
    component: AcompanharImpugnacaoResultadoComponent,
    resolve: {
      cauUfs: CauUFResolve,
      eleicoes: EleicoesImpugnacaoResultadoResolve,
      eleicoesAno: ChapaEleicaoAnoClientResolve
    }
  },
  {
    path: 'acompanhar-impugnacao/:id',
    component: AcompanharImpugnacaoResultadoUfComponent,
    resolve: {
      cauUfs: CauUFResolve,
      pedidos: AcompanharImpugnacaoResultadoClientResolve,
    }
  },
  {
    path: 'detalhar-impugnacao/:id',
    component: DetalharImpugnacaoResultadoComponent,
    resolve: {
      cauUfs: CauUFResolve,
      impugnacao: DetalharImpugnacaoResultadoClientResolve
    }
  },
  {
    path: 'acompanhar/calendario/:idCalendario/uf/:idCauUf',
    component: AcompanharImpugnacaoResultadoUfEspecificaComponent,
    resolve: {
      cauUf: BandeiraCauUFResolve,
      impugnacoes: UfEspecificaImpugnacaoResultadoResolve,
    }
  }
];