import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { UiSwitchModule } from 'ngx-ui-switch';
import { RouterModule } from '@angular/router';
import { ProgressbarModule } from 'ngx-bootstrap';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { PopoverModule } from 'ngx-bootstrap/popover';
import { DataTableModule } from 'angular-6-datatable';
import { CommonModule, DatePipe } from '@angular/common';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, CalendarModule, ValidationModule } from '@cau/component';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';

import { PublicacaoRoutes } from './publicacao.router';
import { CalendarioModule } from '../calendario/calendario.module';
import { CauUFClientModule } from '../../client/cau-uf-client/cau-uf-client.module';
import { ListarDocumentoComponent } from './documento/listar-documento/listar-documento.component';
import { ProfissionalClientModule } from '../../client/profissional-client/profissional-client.module';
import { PublicarDocumentoComponent } from './documento/publicar-documento/publicar-documento.component';
import { DocumentoEleicaoClientModule } from 'src/app/client/documento-eleicao/documento-eleicao-client.module';
import { ListarComissaoEleitoralComponent } from './comissao-eleitoral/listar-comissao-eleitoral/listar-comissao-eleitoral.component';
import { VisualizarComissaoEleitoralComponent } from './comissao-eleitoral/visualizar-comissao-eleitoral/visualizar-comissao-eleitoral.component';

/**
 * Modulo de Publicação.
 */
@NgModule({
  declarations: [
    ListarDocumentoComponent,
    PublicarDocumentoComponent,
    ListarComissaoEleitoralComponent,
    VisualizarComissaoEleitoralComponent
  ],
  imports: [
    FileModule,
    FlexModule,
    MaskModule,
    FormsModule,
    CommonModule,
    MessageModule,
    PopoverModule,
    CalendarModule,
    CKEditorModule,
    DataTableModule,
    ValidationModule,
    CalendarioModule,
    CauUFClientModule,
    SharedComponentsModule,
    ProfissionalClientModule,
    NgMultiSelectDropDownModule,
    DocumentoEleicaoClientModule,
    ProgressbarModule.forRoot(),
    UiSwitchModule.forRoot({
        size: 'small',
        color: '#14385D',
        switchColor: '#FFFFFF',
        defaultBgColor: '#CDCDCD'
    }),
    RouterModule.forChild(PublicacaoRoutes),
    BsDropdownModule.forRoot(),
  ],
  providers: [ DatePipe ]
})
export class PublicacaoModule { }
