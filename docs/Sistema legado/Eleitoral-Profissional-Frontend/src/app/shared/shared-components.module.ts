import { MaxDateValidator } from './validators/maxDate.validator';
import { TimeValidator } from './validators/time.validator';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { DataTableModule } from 'angular-6-datatable';
import { TypeaheadModule, BsDropdownModule, PopoverModule } from 'ngx-bootstrap';
import { CommonModule } from '@angular/common';
import { DataTable } from './datatable/DataTable';
import { CKEditorModule } from 'ckeditor4-angular';
import { ArquivoComponent } from './arquivo/arquivo.component';
import { BootstrapPaginator } from './datatable/BootstrapPaginator';
import { CardPanelComponent } from './card-panel/card-panel.component';
import { CardListComponent } from './card-list/card-list.component';
import { FlexModule } from '@angular/flex-layout';
import { RouterModule } from '@angular/router';
import { MaskModule, ValidationModule, FileModule } from '@cau/component';
import { AutocompleteComponent } from './autocomplete/autocomplete.component';
import { ModalConfirmComponent } from './modal-confirm/modal-confirm.component';
import { SwitchContentComponent } from './switch-content/switch-content.component';
import { EditorDeTextoComponent } from './editor-de-texto/editor-de-texto.component';
import { CardPanelCustomComponent } from './card-panel-custom/card-panel-custom.component';

@NgModule({
  declarations: [
    AutocompleteComponent,
    CardListComponent,
    DataTable,
    ArquivoComponent,
    CardPanelComponent,
    BootstrapPaginator,
    EditorDeTextoComponent,
    TimeValidator,
    MaxDateValidator,
    ModalConfirmComponent,
    CardPanelCustomComponent,
    SwitchContentComponent
  ],
  imports: [
    MaskModule,
    FileModule,
    FormsModule,
    CommonModule,
    MessageModule,
    CKEditorModule,
    DataTableModule,
    TypeaheadModule.forRoot(),
    FlexModule,
    BsDropdownModule.forRoot(),
    RouterModule,
    PopoverModule,
    ValidationModule,
  ],
  exports: [
    DataTable,
    ArquivoComponent,
    CardPanelComponent,
    BootstrapPaginator,
    AutocompleteComponent,
    EditorDeTextoComponent,
    ModalConfirmComponent,
    CardListComponent,
    EditorDeTextoComponent,
    TimeValidator,
    MaxDateValidator,
    CardPanelCustomComponent,
    SwitchContentComponent
  ]
})
export class SharedComponentsModule { }
