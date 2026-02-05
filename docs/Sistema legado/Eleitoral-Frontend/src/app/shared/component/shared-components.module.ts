import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MessageModule } from '@cau/message';
import { PaginacaoComponent } from './paginacao/paginacao.component';
import { TotalRegistrosComponent } from './total-registros/total-registros.component';
import { DataTableModule } from 'angular-6-datatable';
import { AutocompleteComponent } from './autocomplete/autocomplete.component';
import { DataTable } from './datatable/DataTable';
import {BsDropdownModule, TypeaheadModule} from 'ngx-bootstrap';
import { CKEditorModule } from 'ckeditor4-angular';
import { FormsModule } from '@angular/forms';
import { BootstrapPaginator } from './datatable/BootstrapPaginator';
import { CardListComponent } from './card-list/card-list.component';
import { FlexModule } from '@angular/flex-layout';
import { MaskModule, FileModule, ValidationModule } from '@cau/component';
import { ArquivoComponent } from './arquivo/arquivo.component';
import { EditorDeTextoComponent } from './editor-de-texto/editor-de-texto.component';
import { CardPanelComponent } from './card-panel/card-panel.component';
import { ModalConfirmComponent } from './modal-confirm/modal-confirm.component';
import { CardPanelCustomComponent } from './card-panel-custom/card-panel-custom.component';
import { SubAbasContentComponent } from './sub-abas-content/sub-abas-content.component';
import { TableRetificacaoPlataformaComponent } from './table-retificacao-plataforma/table-retificacao-plataforma.component';

@NgModule({
  declarations: [
    PaginacaoComponent,
    TotalRegistrosComponent,
    AutocompleteComponent,
    DataTable,
    BootstrapPaginator,
    CardListComponent,
    ArquivoComponent,
    EditorDeTextoComponent,
    BootstrapPaginator,
    CardPanelComponent,
    ModalConfirmComponent,
    CardPanelCustomComponent,
    SubAbasContentComponent,
    TableRetificacaoPlataformaComponent
  ],
  imports: [
    FlexModule,
    MaskModule,
    FileModule,
    FormsModule,
    CommonModule,
    MessageModule,
    CKEditorModule,
    DataTableModule,
    TypeaheadModule.forRoot(),
    BsDropdownModule,
    ValidationModule
  ],
  exports: [
    PaginacaoComponent,
    TotalRegistrosComponent,
    AutocompleteComponent,
    DataTable,
    BootstrapPaginator,
    CardListComponent,
    ArquivoComponent,
    EditorDeTextoComponent,
    BootstrapPaginator,
    CardPanelComponent,
    ModalConfirmComponent,
    CardPanelCustomComponent,
    SubAbasContentComponent,
    TableRetificacaoPlataformaComponent
  ]
})
export class SharedComponentsModule { }
