import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AccordionModule } from 'ngx-bootstrap';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { ImpugnacaoRoutes } from './impugnacao.router';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';

import { SharedComponentsModule } from 'src/app/shared/shared-components.module';
import { SharedImpugnacaoComponentsModule } from './shared/shared-impugnacao-components.module';
import { AcompanharImpugnacaoServiceModule } from 'src/app/client/impugnacao-client/impugnacao-client.module';

import { SolicitacaoImpugnacaoResolve } from 'src/app/client/impugnacao-candidatura-client/solicitacao-impugnacao-client.resolve';
import { ImpugnacaoDeclaracaoAtividadeResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-declaracao-atividade-client.resolve';

import { informacaoCard } from './shared/informacao-card/informacao-card.component';
import { DetalharImpugnacao } from './detalhar-impugnacao/detalhar-impugnacao.component';
import { CadastroImpugnacaoComponent } from './cadastro-impugnacao/cadastro-impugnacao.component';
import { AcompanharImpugnacaoUf } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-uf.component';
import { AcompanharImpugnacaoUfEspecifica } from './acompanhar-impugnacao-uf-especifica/acompanhar-impugnacao-uf-especifica.component';
import { AbaPedidoImpugnacao } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-pedido-impugnacao/aba-pedido-impugnacao.component';
import { AbaDefesaImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-defesa-impuganacao/aba-defesa-impugnacao.component';
import { AbaRecursoImpugnanteComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-recurso-impugnante/aba-recurso-impugnante.component';
import { AbaRecursoResponsavelComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-recurso-responsavel/aba-recurso-responsavel.component';
import { AbaJulgamentoImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-julgamento-impugnacao/aba-julgamento-impugnacao.component';
import { SubstituicaoImpugnacaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-substituicao/subsitituicao-impugnacao/substituicao-impugnacao.component';
import { AbaJulgamentoSegundaInstanciaComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-julgamento-segunda-instancia/aba-julgamento-segunda-instancia.component';
import { VisualizarSubstituicaoComponent } from './detalhar-impugnacao/abas-detalhar-impugnacao/aba-substituicao/visualizar-substituicao/visualizar-substituicao.component';
import { ImpugnacaoSubstituicaoResolve } from 'src/app/client/impugnacao-candidatura-client/impugnacao-substituicao-client.resolve';


/**
 * Modulo para impugnação de candidaturas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        informacaoCard,
        DetalharImpugnacao,
        AbaPedidoImpugnacao,
        AcompanharImpugnacaoUf,
        CadastroImpugnacaoComponent,
        AbaDefesaImpugnacaoComponent,
        AbaRecursoImpugnanteComponent,
        AbaRecursoResponsavelComponent,
        VisualizarSubstituicaoComponent,
        SubstituicaoImpugnacaoComponent,
        AcompanharImpugnacaoUfEspecifica,
        AbaJulgamentoImpugnacaoComponent,
        AbaJulgamentoSegundaInstanciaComponent,
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
        SharedImpugnacaoComponentsModule,
        AcompanharImpugnacaoServiceModule,
        RouterModule.forChild(ImpugnacaoRoutes),
    ],
    providers: [
        SolicitacaoImpugnacaoResolve,
        ImpugnacaoSubstituicaoResolve,
        ImpugnacaoDeclaracaoAtividadeResolve,
    ]
})
export class ImpugnacaoModule {

}