import { VisualizarPedidoSubstituicaoComponent } from './abas-julgamento-final/visualizar-pedido-substituicao/visualizar-pedido-substituicao.component';
import { ListMembrosChapaComponent } from './shared/list-membros-chapa/list-membros-chapa.component';
import { MembrosChapaComponent } from './shared/membros-chapa/membros-chapa.component';
import { SharedComponentsModule } from './../../../shared/shared-components.module';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FlexModule } from '@angular/flex-layout';

import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { JulgamentoFinalRoutes } from './julgamento-final.router';
import { AccordionModule, ProgressbarModule, TooltipModule } from 'ngx-bootstrap';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';

import { ListarUfsComponent } from './listar-ufs/listar-ufs.component';
import { ListarPendencias } from './listar-pendencias/listar-pendencias.component';
import { CardPendencias } from './shared/card-pendencias/card-pendencias.component';
import { ListarUfEspecifica } from './listar-uf-especifica/listar-uf-especifica.component';
import { AbasJulgamentoFinalComponent } from './abas-julgamento-final/abas-julgamento-final.component';
import { CardJulgamentoFinalComponent } from './shared/card-julgamento-final/card-julgamento-final.component';
import { CardVisualizarSubstituicaoComponent } from './shared/card-visualizar-subsituicao/card-visualizar-subsituicao.component';
import { CardRecursoJulgamentoFinalComponent } from './shared/card-recurso-julgamento-final/card-recurso-julgamento-final.component';
import { VisualizarMembrosChapaComponent } from './abas-julgamento-final/visualizar-membros-chapa/visualizar-membros-chapa.component';
import { VisualizarRecursoJulgamentoComponent } from './abas-julgamento-final/visualizar-recurso-julgamento-final/visualizar-recurso-julgamento.component';
import { VisualizarJulgamentoFinalComponent } from './abas-julgamento-final/visualizar-julgamento-final-primeira-instancia/visualizar-julgamento-final.component';
import { CardJulgamentoRecursoSegundaInstanciaComponent } from './shared/card-julgamento-recurso-segunda-instancia/card-julgamento-recurso-segunda-instancia.component';
import { ModalCadastrarRecursoSubstituicaoComponent } from './abas-julgamento-final/modal-cadastrar-recurso-substituicao/modal-cadastrar-recurso-substituicao.component';
import { ModalAddSubsJulgFinalComponent } from './abas-julgamento-final/visualizar-julgamento-final-primeira-instancia/modal-julgamento-final/adicionar-substituicao/adicionar-substituicao.component';
import { VisualizarJulgamentoRecursoSegundaInstanciaComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-recurso/visualizar-julgamento-recurso.component';
import { VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-substituicao/visualizar-julgamento-substituicao.component';
import { ModalVisualizarHistoricoPedidoSubstituicaoComponent } from './abas-julgamento-final/visualizar-pedido-substituicao/modal-visualizar-historico-pedido-substituicao/modal-visualizar-historico-pedido-substituicao.component';
import { ModalVisualizarJulgamentoSubstituicaoComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao.component';

import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';

import { JulgamentoFinalClientModule } from 'src/app/client/julgamento-final/julgamento-final-client.module';
import { ModalPlataformaPropagandaComponent } from './abas-julgamento-final/visualizar-membros-chapa/modal-plataforma-propaganda/modal-plataforma-propaganda.component';
import { UiSwitchModule } from 'ngx-ui-switch';

/**
 * Modulo para impugnação de candidaturas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        CardPendencias,
        ListarPendencias,
        ListarUfsComponent,
        ListarUfEspecifica,
        MembrosChapaComponent,
        ListMembrosChapaComponent,
        CardJulgamentoFinalComponent,
        AbasJulgamentoFinalComponent,
        ModalAddSubsJulgFinalComponent,
        VisualizarMembrosChapaComponent,
        VisualizarJulgamentoFinalComponent,
        CardVisualizarSubstituicaoComponent,
        CardRecursoJulgamentoFinalComponent,
        VisualizarRecursoJulgamentoComponent,
        VisualizarPedidoSubstituicaoComponent,
        ModalCadastrarRecursoSubstituicaoComponent,
        CardJulgamentoRecursoSegundaInstanciaComponent,
        ModalVisualizarJulgamentoSubstituicaoComponent,
        ModalVisualizarHistoricoPedidoSubstituicaoComponent,
        VisualizarJulgamentoRecursoSegundaInstanciaComponent,
        VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent,
        ModalPlataformaPropagandaComponent
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
        TooltipModule.forRoot(),
        AccordionModule.forRoot(),
        BsDropdownModule.forRoot(),
        NgMultiSelectDropDownModule,
        JulgamentoFinalClientModule,
        ProgressbarModule.forRoot(),
        RouterModule.forChild(JulgamentoFinalRoutes),
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#016C71',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
    ],
    providers: [BandeiraCauUFResolve,
    ],
    entryComponents: [
        ModalVisualizarJulgamentoSubstituicaoComponent,
        ModalVisualizarHistoricoPedidoSubstituicaoComponent,
        ModalPlataformaPropagandaComponent
    ]
})
export class JulgamentoFinalModule {

}
