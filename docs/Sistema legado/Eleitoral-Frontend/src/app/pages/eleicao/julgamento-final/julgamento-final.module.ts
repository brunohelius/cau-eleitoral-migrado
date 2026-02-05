import { FormAlterarJulgamentoComponent } from './abas-julgamento-final/vizualizar-julgamento-final-primeira-instancia/modal/form-alterar-julgamento/form-alterar-julgamento.component';
import { ModalConfirmarAlterarJulgamentoComponent } from './abas-julgamento-final/vizualizar-julgamento-final-primeira-instancia/modal/confirmar-alterar-julgamento/confirmar-alterar-julgamento.component';
import { VisualizarPedidoSubstituicaoComponent } from './abas-julgamento-final/visualizar-pedido-substituicao/visualizar-pedido-substituicao.component';
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
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';
import { AccordionModule, ProgressbarModule, TooltipModule } from 'ngx-bootstrap';

import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { DadosJulgamentoFinalSegundaInstanciaResolve } from 'src/app/client/julgamento-final/dados-julgamento-fina-segunda-instancial.resolve';

import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { JulgamentoFinalClientModule } from 'src/app/client/julgamento-final/julgamento-final-client.module';

import { ListarUfsComponent } from './listar-ufs/listar-ufs.component';
import { ListarPendencias } from './listar-pendencias/listar-pendencias.component';
import { CardPendencias } from './shared/card-pendencias/card-pendencias.component';
import { MembrosChapaComponent } from './shared/membros-chapa/membros-chapa.component';
import { ListarUfEspecifica } from './listar-uf-especifica/listar-uf-especifica.component';
import { TableRetificacaoComponent } from './shared/table-retificacao/table-retificacao.component';
import { ListMembrosChapaComponent } from './shared/list-membros-chapa/list-membros-chapa.component';
import { AbasJulgamentoFinalComponent } from './abas-julgamento-final/abas-julgamento-final.component';
import { ListarEleicaoJulgamentoComponent } from './listar-eleicao/listar-eleicao-julgamento.component';
import { TituloSubstituicaoComponent } from './shared/titulo-substituicao/titulo-substituicao.component';
import { FormJulgarSegundaInstanciaComponent } from './segunda-instancia/form-julgar/form-julgar.component';
import { FormJulgarComponent } from './abas-julgamento-final/visualizar-membros-chapa/form-julgar/form-julgar.component';
import { CardJulgamentoFinalComponent } from 'src/app/shared/component/card-julgamento-final/card-julgamento-final.component';
import { CardVisualizarSubstituicaoComponent } from './shared/card-visualizar-subsituicao/card-visualizar-subsituicao.component';
import { VisualizarMembrosChapaComponent } from './abas-julgamento-final/visualizar-membros-chapa/visualizar-membros-chapa.component';
import { AlterarJulgamentoSegundaInstanciaComponent } from './segunda-instancia/alterar-julgamento-final/alterar-julgamento-final.component';
import { CardListMembrosPendenciasComponent } from 'src/app/shared/component/card-list-membros-pendencias/card-list-membros-pendencias.component';
import { CardRecursoJulgamentoFinalComponent } from 'src/app/shared/component/card-recurso-julgamento-final/card-recurso-julgamento-final.component';
import { VisualizarRecursoJulgamentoComponent } from './abas-julgamento-final/visualizar-recurso-julgamento-final/visualizar-recurso-julgamento.component';
import { VisualizarJulgamentoFinalPrimeiraComponent } from './abas-julgamento-final/vizualizar-julgamento-final-primeira-instancia/vizualizar-julgamento-final-primeira.component';
import { CardJulgamentoRecursoSegundaInstanciaComponent } from 'src/app/shared/component/card-julgamento-recurso-segunda-instancia/card-julgamento-recurso-segunda-instancia.component';
import { VisualizarJulgamentoRecursoSegundaInstanciaComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-recurso/visualizar-julgamento-recurso.component';
import { VisualizarRetificacaoJulgamentooComponent } from './abas-julgamento-final/vizualizar-julgamento-final-primeira-instancia/modal/visualizar-retificacao-julgamento/visualizar-retificacao-julgamento.component';
import { VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-substituicao/visualizar-julgamento-substituicao.component';
import { ModalVisualizarHistoricoPedidoSubstituicaoComponent } from './abas-julgamento-final/visualizar-pedido-substituicao/modal-visualizar-historico-pedido-substituicao/modal-visualizar-historico-pedido-substituicao.component';
import { ModalVisualizarJulgamentoRecursoComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-recurso/modal-visualizar-julgamento-recurso/modal-visualizar-julgamento-recurso.component';
import { ModalVisualizarJulgamentoSubstituicaoComponent } from './abas-julgamento-final/visualizar-julgamento-final-segunda-instancia/visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao.component';

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
        FormJulgarComponent,
        MembrosChapaComponent,
        ListMembrosChapaComponent,
        TableRetificacaoComponent,
        TituloSubstituicaoComponent,
        AbasJulgamentoFinalComponent,
        CardJulgamentoFinalComponent,
        FormAlterarJulgamentoComponent,
        VisualizarMembrosChapaComponent,
        ListarEleicaoJulgamentoComponent,
        CardListMembrosPendenciasComponent,
        CardRecursoJulgamentoFinalComponent,
        FormJulgarSegundaInstanciaComponent,
        CardVisualizarSubstituicaoComponent,
        VisualizarRecursoJulgamentoComponent,
        VisualizarPedidoSubstituicaoComponent,
        ModalConfirmarAlterarJulgamentoComponent,
        ModalVisualizarJulgamentoRecursoComponent,
        VisualizarRetificacaoJulgamentooComponent,
        VisualizarJulgamentoFinalPrimeiraComponent,
        AlterarJulgamentoSegundaInstanciaComponent,
        CardJulgamentoRecursoSegundaInstanciaComponent,
        ModalVisualizarJulgamentoSubstituicaoComponent,
        ModalVisualizarHistoricoPedidoSubstituicaoComponent,
        VisualizarJulgamentoRecursoSegundaInstanciaComponent,
        VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent,
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
        TooltipModule.forRoot(),
        NgMultiSelectDropDownModule,
        JulgamentoFinalClientModule,
        ProgressbarModule.forRoot(),
        RouterModule.forChild(JulgamentoFinalRoutes),
    ],
    providers: [
        BandeiraCauUFResolve,
        DadosJulgamentoFinalSegundaInstanciaResolve
    ],
    entryComponents: [
        FormAlterarJulgamentoComponent,
        ModalConfirmarAlterarJulgamentoComponent,
        VisualizarRetificacaoJulgamentooComponent,
        AlterarJulgamentoSegundaInstanciaComponent,
        ModalVisualizarJulgamentoSubstituicaoComponent,
        ModalVisualizarHistoricoPedidoSubstituicaoComponent,
        AlterarJulgamentoSegundaInstanciaComponent,
        ModalConfirmarAlterarJulgamentoComponent,
        FormAlterarJulgamentoComponent,
        VisualizarRetificacaoJulgamentooComponent,
        ModalVisualizarJulgamentoRecursoComponent
    ]
})
export class JulgamentoFinalModule {}
