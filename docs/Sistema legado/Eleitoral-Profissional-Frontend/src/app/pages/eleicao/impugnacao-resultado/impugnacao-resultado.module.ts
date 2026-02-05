import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { UiSwitchModule } from 'ngx-ui-switch';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { ImageCropperModule } from 'ngx-image-cropper';
import { CommonModule, DatePipe } from '@angular/common';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';
import { ProgressbarModule, PopoverModule, TypeaheadModule, AccordionModule } from 'ngx-bootstrap';

import { RouterModule } from '@angular/router';
import { ImpugnacaoResultadoRoutes } from './impugnacao-resultado.router';

import { SharedComponentsModule } from 'src/app/shared/shared-components.module';
import { CauUFClientModule } from 'src/app/client/cau-uf-client/cau-uf-client.module';
import { ComissaoEleitoralModule } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.module';

import { CadastroImpugnacaoResultadoComponent } from './cadastro-impugnacao-resultado/cadastro-impugnacao-resultado.component';
import { VisualizarImpugnacaoResultadoComponent } from './visualizar-impugancao-resultado/visualizar-impugancao-resultado.component';
import { AbaVisualizarAlegacaoComponent } from './visualizar-impugancao-resultado/aba-visualizar-alegacao/aba-visualizar-alegacao.component';
import { AcompanharImpugnacaoResultadoUfComponent } from './acompanhar-impugnacao-resultado-uf/acompanhar-impugnacao-resultado-uf.component';
import { AbaVisualizarImpugnacaoComponent } from './visualizar-impugancao-resultado/aba-visualizar-impugnacao/aba-visualizar-impugnacao.component';
import { ModalVisualizarAlegacaoComponent } from './visualizar-impugancao-resultado/aba-visualizar-alegacao/modal-visualizar-alegacao/modal-visualizar-alegacao.component';
import {AcompanharImpugnacaoResultadoUfEspecificaComponent } from './acompanhar-impugnacao-resultado-uf-especifica/acompanhar-impugnacao-resultado-uf-especifica.component';
import { ModalCadastrarAlegacaoComponent } from './visualizar-impugancao-resultado/aba-visualizar-impugnacao/modal-cadastrar-alegacao/modal-cadastrar-alegacao.component';
import { AbaJulgamentoImpugnacaoResultadoComponent } from './visualizar-impugancao-resultado/aba-julgamento-impugnacao-resultado/aba-julgamento-impugnacao-resultado.component';
import { ModalCadastrarRecursoComponent } from './visualizar-impugancao-resultado/aba-julgamento-impugnacao-resultado/modal-cadastrar-recurso/modal-cadastrar-recurso.component';
import { ModalCadastroImpugnacaoResultadoContrarrazaoComponent } from './modal-cadastro-impugnacao-resultado-contrarrazao/modal-cadastro-impugnacao-resultado-contrarrazao.component';
import { AbaRecursoJulgamentoImpugnadoComponent } from './visualizar-impugancao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnado/aba-recurso-julgamento-impugnado.component';
import { ModalVisualizarRecursoImpugnadoComponent } from './visualizar-impugancao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnado/modal-visualizar-recurso-impugnado/modal-visualizar-recurso-impugnado.component';
import { AbaRecursoJulgamentoImpugnanteComponent } from './visualizar-impugancao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnante/aba-recurso-julgamento-impugnante.component';
import { ListImpugnacaoResultadoContrarrazaoComponent } from './list-impugnacao-resultado-contrarrazao/list-impugnacao-resultado-contrarrazao.component';
import { AbaJulgamentoImpugResultadoSegundaInstanciaComponent } from './visualizar-impugancao-resultado/aba-julgamento-segunda-instancia-impugnacao-resultado/aba-julgamento-segunda-instancia.component';

/**
 * Modulo CalendariLABEL_ACOESo.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        AbaVisualizarAlegacaoComponent,
        ModalCadastrarRecursoComponent,
        ModalCadastrarAlegacaoComponent,
        ModalVisualizarAlegacaoComponent,
        AbaVisualizarImpugnacaoComponent,
        CadastroImpugnacaoResultadoComponent,
        AbaRecursoJulgamentoImpugnadoComponent,
        VisualizarImpugnacaoResultadoComponent,
        AbaRecursoJulgamentoImpugnanteComponent,
        ModalVisualizarRecursoImpugnadoComponent,
        AcompanharImpugnacaoResultadoUfComponent,
        AbaJulgamentoImpugnacaoResultadoComponent,
        ListImpugnacaoResultadoContrarrazaoComponent,
        AcompanharImpugnacaoResultadoUfEspecificaComponent,
        AbaJulgamentoImpugResultadoSegundaInstanciaComponent,
        ModalCadastroImpugnacaoResultadoContrarrazaoComponent,
    ],
    imports: [
        MaskModule,
        FlexModule,
        FileModule,
        FormsModule,
        CommonModule,
        PopoverModule,
        MessageModule,
        CKEditorModule,
        DataTableModule,
        ValidationModule,
        CauUFClientModule,
        ImageCropperModule,
        SharedComponentsModule,
        ComissaoEleitoralModule,
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#016C71',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
        AccordionModule.forRoot(),
        TypeaheadModule.forRoot(),
        BsDropdownModule.forRoot(),
        ProgressbarModule.forRoot(),
        RouterModule.forChild(ImpugnacaoResultadoRoutes),
    ],
    exports: [

    ],
    providers: [
        DatePipe,
    ],
    entryComponents: [
        ModalCadastrarAlegacaoComponent,
        ModalCadastrarRecursoComponent,
        ModalCadastroImpugnacaoResultadoContrarrazaoComponent,
        ModalVisualizarRecursoImpugnadoComponent
    ]
})
export class ImpugnacaoResultadoModule {

}
