import { ModalVisualizarContrarrazaoImpugnadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnante/contrarazao-recurso-julgamento-impugnado/modal-visualizar-contrarrazao-recurso-impugnado/modal-visualizar-contrarrazao-impugnado.component';
import { AbaRecursoJulgamentoImpugnadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnado/aba-recurso-julgamento-impugnado.component';

import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { CommonModule, DatePipe } from '@angular/common';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { HttpClientModule } from '@angular/common/http';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { AccordionModule, ProgressbarModule } from 'ngx-bootstrap';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';

import { RouterModule } from '@angular/router';
import { ImpugnacaoResultadoRoutes } from './impugnacao-resultado.router';

import { DetalharImpugnacaoResultadoComponent } from './detalhar-impugnacao-resultado/detalhar-impugnacao-resultado.component';
import { AcompanharImpugnacaoResultadoUfComponent } from './acompanhar-impugnacao-uf/acompanhar-impugnacao-resultado-uf.component';
import { AcompanharImpugnacaoResultadoComponent } from './acompanhar-impugnacao-resultado/acompanhar-impugnacao-resultado.component';
import { AbAlegacaoImpugnacaoResultadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-alegacao-impugnacao-resultado/aba-alegacao-impugnacao-resultado.component';
import { AcompanharImpugnacaoResultadoUfEspecificaComponent } from './acompanhar-impugnacao-resultado-uf-especifica/acompanhar-impugnacao-resultado-uf-especifica.component';
import { ModalImpugnacaoResultadoJulgamentoPrimeiraInstanciaComponent } from './modal-impugnacao-resultado-julgamento-primeira-instancia/modal-impugnacao-resultado-julgamento-primeira-instancia.component';
import { AbaJulgamentoImpugnacaoResultadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-julgamento-impugnacao-resultado/aba-julgamento-impugnacao-resultado.component';
import { ModalVisualizarAlegacaoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-alegacao-impugnacao-resultado/modal-visualizar-alegacao/modal-visualizar-alegacao.component';

import { EleicoesImpugnacaoResultadoResolve } from 'src/app/client/impugnacao-resultado-client/eleicoes-impugnacao-resultado-client.resolve';
import { DetalharImpugnacaoResultadoClientResolve } from 'src/app/client/impugnacao-resultado-client/detalhar-impugnacao-resultado-client.resolve';
import { AcompanharImpugnacaoResultadoClientResolve } from 'src/app/client/impugnacao-resultado-client/acompanhar-impugnacao-resultado-client.resolve';
import { AbaDetalharImpugnacaoResultadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-detalhar-impugnacao-resultado/aba-detalhar-impugnacao-resultado.component';
import { ModalCadastroImpugnacaoResultadoContrarrazaoComponent } from './modal-cadastro-impugnacao-resultado-contrarrazao/modal-cadastro-impugnacao-resultado-contrarrazao.component';
import { ModalVisualizarRecursoImpugnadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnado/modal-visualizar-recurso-impugnado/modal-visualizar-recurso-impugnado.component';
import { AbaRecursoJulgamentoImpugnanteComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnante/aba-recurso-julgamento-impugnante.component';
import { ContrarazaoRecursoImpugnadoComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-recurso-julgamento-impugnacao-resultado-impugnante/contrarazao-recurso-julgamento-impugnado/contrarazao-recurso-impugnado.component';
import { ModalImpugnacaoResultadoJulgamentoSegundaInstanciaComponent } from './modal-impugnacao-resultado-julgamento-segunda-instancia/modal-impugnacao-resultado-julgamento-segunda-instancia.component';
import { AbaJulgamentoImpugResultadoSegundaInstanciaComponent } from './detalhar-impugnacao-resultado/abas-detalhar-impugnacao-resultado/aba-julgamento-segunda-instancia-impugnacao-resultado/aba-julgamento-segunda-instancia.component';

/**
 * Modulo para impugnação de candidaturas.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
      ModalVisualizarAlegacaoComponent,
      ContrarazaoRecursoImpugnadoComponent,
      DetalharImpugnacaoResultadoComponent,
      AbaRecursoJulgamentoImpugnadoComponent,
      AbAlegacaoImpugnacaoResultadoComponent,
      AcompanharImpugnacaoResultadoComponent,
      AbaDetalharImpugnacaoResultadoComponent,
      AbaRecursoJulgamentoImpugnanteComponent,
      AcompanharImpugnacaoResultadoUfComponent,
      ModalVisualizarRecursoImpugnadoComponent,
      AbaJulgamentoImpugnacaoResultadoComponent,
      ModalVisualizarContrarrazaoImpugnadoComponent,
      AcompanharImpugnacaoResultadoUfEspecificaComponent,
      AbaJulgamentoImpugResultadoSegundaInstanciaComponent,
      ModalCadastroImpugnacaoResultadoContrarrazaoComponent,
      ModalImpugnacaoResultadoJulgamentoSegundaInstanciaComponent,
      ModalImpugnacaoResultadoJulgamentoPrimeiraInstanciaComponent
    ],
    entryComponents: [

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
        HttpClientModule,
        SharedComponentsModule,
        AccordionModule.forRoot(),
        BsDropdownModule.forRoot(),
        NgMultiSelectDropDownModule,
        ProgressbarModule.forRoot(),
        RouterModule.forChild(ImpugnacaoResultadoRoutes),
    ],
    providers: [
      BandeiraCauUFResolve,
      DetalharImpugnacaoResultadoClientResolve,
      AcompanharImpugnacaoResultadoClientResolve,
      AcompanharImpugnacaoResultadoUfEspecificaComponent,
      EleicoesImpugnacaoResultadoResolve,
      DatePipe
    ]
})
export class ImpugnacaoResultadoModule {

}
