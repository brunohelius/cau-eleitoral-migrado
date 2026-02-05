import { AbaJulgamentoPedidoImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-julgamento-pedido-impugnacao/aba-julgamento-pedido-impugnacao.component';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { ImpugnacaoRoutes } from './impugnacao.router';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { AccordionModule, ProgressbarModule } from 'ngx-bootstrap';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';

import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { SharedImpugnacaoComponentsModule } from './shared/shared-impugnacao-components.module';

import { DetalharImpugnacaoComponent } from './detalhar-impugnacao/detalhar-impugnacao.component';
import { AcompanharImpugnacaoComponent } from './acompanhar-impugnacao/acompanhar-impugnacao.component';
import { AcompanharImpugnacaoUfComponent } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-uf.component';
import { AcompanharImpugnacaoUfEspecificaComponent } from './acompanhar-impugnacao-uf-especifica/acompanhar-impugnacao-uf-especifica.component';
import { AbaPedidoImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-pedido-impugnacao/aba-pedido-impugnacao.component';
import { AbaDefesaImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-defesa-impuganacao/aba-defesa-impugnacao.component';
import { AbaRecursoImpugnanteComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-recurso-impugnante/aba-recurso-impugnante.component';
import { AbaRecursoResponsavelComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-recurso-responsavel/aba-recurso-responsavel.component';
import { AbaVisualizarSubstituicaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-visualizar-substituicao/aba-visualizar-substituicao.component';
import { AbaJulgamentoSegundaInstaciaImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-julgamento-segunda-instancia-impugnacao/aba-julgamento-segunda-instancia-impugnacao.component';

import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { SolicitacaoImpugnacaoResolve } from 'src/app/client/impugnacao-candidatura-client/solicitacao-impugnacao-client.resolve';
import { AcompanharImpugnacaoClientResolve } from 'src/app/client/impugnacao-candidatura-client/acompanhar-impugnacao-client.resolve';
import { AcompanharImpugnacaoUfClientResolve } from 'src/app/client/impugnacao-candidatura-client/acompanhar-impugnacao-uf-client.resolve';
import { ImpugnacaoSubstituicaoResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-substituicao-client.resolve';


/**
 * Modulo para impugnação de candidaturas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        DetalharImpugnacaoComponent,
        AbaPedidoImpugnacaoComponent,
        AbaDefesaImpugnacaoComponent,
        AcompanharImpugnacaoComponent,
        AbaRecursoImpugnanteComponent,
        AbaRecursoResponsavelComponent,
        AcompanharImpugnacaoUfComponent,
        AbaVisualizarSubstituicaoComponent,
        AbaJulgamentoPedidoImpugnacaoComponent,
        AcompanharImpugnacaoUfEspecificaComponent,
        AbaJulgamentoSegundaInstaciaImpugnacaoComponent,
    ],
    imports: [
        MaskModule,
        FlexModule,
        FileModule,
        FormsModule,
        CommonModule,
        MessageModule,
        CKEditorModule,
        DataTableModule,
        ValidationModule,
        SharedComponentsModule,
        AccordionModule.forRoot(),
        BsDropdownModule.forRoot(),
        NgMultiSelectDropDownModule,
        ProgressbarModule.forRoot(),
        SharedImpugnacaoComponentsModule,
        RouterModule.forChild(ImpugnacaoRoutes),
    ],
    providers: [
        BandeiraCauUFResolve,
        SolicitacaoImpugnacaoResolve,
        ImpugnacaoSubstituicaoResolve,
        AcompanharImpugnacaoClientResolve,
        AcompanharImpugnacaoUfClientResolve,
    ]
})
export class ImpugnacaoModule {

}