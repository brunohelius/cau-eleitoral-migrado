import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AcompanharImpugnacaoClientService } from './impugnacao-client.service';
import { AcompanharImpugnacaoClientResolve } from './acompanhar-impugnacao-client.resolve';
import { AcompanharImpugnacaoUfClientResolve } from './acompanhar-impugnacao-uf-client.resolve';
import { AcompanharImpugnacaoChapaClientResolve } from './acompanhar-impugnacao-chapa-client.resolve';
import { ImpugnacaoRespnsavelSolicitacaoClientResolve } from './impugnacao-responsavel-solicitacao-client.resolve';
import { ImpugnacaoProfissionalSolicitanteClientResolve } from './impugnacao-profissional-solicitante-client.resolve';


/**
 * Modulo de integração do projeto frontend com os serviços backend referente a chapa eleção para Responsavel.
 */
@NgModule({
    declarations: [],
    imports: [
        CommonModule
    ],
    providers: [
      AcompanharImpugnacaoClientResolve,
      AcompanharImpugnacaoClientService,
      AcompanharImpugnacaoUfClientResolve,
      AcompanharImpugnacaoChapaClientResolve,
      ImpugnacaoRespnsavelSolicitacaoClientResolve,
      ImpugnacaoProfissionalSolicitanteClientResolve
    ]
})
export class AcompanharImpugnacaoServiceModule { }
