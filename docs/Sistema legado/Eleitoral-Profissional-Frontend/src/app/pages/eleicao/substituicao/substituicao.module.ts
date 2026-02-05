import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AccordionModule } from 'ngx-bootstrap';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { SubstituicaoRoutes } from './substituicao.router';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';
import { SharedComponentsModule } from 'src/app/shared/shared-components.module';
import { AcompanharSubstituicao } from './acompanhar-substituicao/acompanhar-substituicao.component';
import { CadastroSubstituicaoComponent } from './cadastro-substituicao/cadastro-substituicao.component';
import { AcompanharSubstituicaoUF } from './acompanhar-substituicao-uf/acompanhar-substituicao-uf.component';
import { AcompanharSubstituicaoDetalhamento } from './acompanhar-substituicao-detalhamento/acompanhar-substituicao-detalhamento.component';
import { CardSubstituicaoComponent } from './acompanhar-substituicao-detalhamento/abas/shared/card-subsituicao/card-substituicao.component';
import { AcompanharJulgamentoSubstituicaoModule } from 'src/app/client/acompanhar-julgamento-substituicao-client/acompanhar-julgamento-substituicao-client.module';
import { AcompanharInterporRecursoComponent } from './acompanhar-substituicao-detalhamento/abas/acompanhar-interpor-recurso/acompanhar-interpor-recurso.component';
import { AtividadeSecundariaRecursoClientResolve } from './../../../client/substituicao-chapa-client/atividade-secundaria-recurso-client.resolve';
import { AcompanharJulgamentoSubstituicaoComponent } from './acompanhar-substituicao-detalhamento/abas/acompanhar-julgamento-substituicao/acompanhar-julgamento-substituicao.component';
import { AcompanharJulgamentoSegundaInstanciaComponent } from './acompanhar-substituicao-detalhamento/abas/acompanhar-julgamento-segunda-instancia/acompanhar-julgamento-segunda-instancia.component';

/**
 * Modulo para impugnação de candidaturas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        AcompanharSubstituicao,
        AcompanharSubstituicaoUF,
        CardSubstituicaoComponent,
        CadastroSubstituicaoComponent,
        AcompanharSubstituicaoDetalhamento,
        AcompanharInterporRecursoComponent,
        AcompanharJulgamentoSubstituicaoComponent,
        AcompanharJulgamentoSegundaInstanciaComponent
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
        RouterModule.forChild(SubstituicaoRoutes),
    ],
    providers: [
        AcompanharJulgamentoSubstituicaoModule
    ]
})

export class SubstituicaoModule {

}