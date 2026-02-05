import { NgModule } from '@angular/core';
import { MessageModule } from '@cau/message';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { TypeaheadModule } from 'ngx-bootstrap';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { MaskModule, ValidationModule, FileModule } from '@cau/component';

import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';

import { ImpugnacaoJustificativaProfissionalComponent } from './impugnacao-justificativa-profissional/impugnacao-justificativa-profissional.component';

@NgModule({
  declarations: [
    ImpugnacaoJustificativaProfissionalComponent
  ],
  imports: [
    FileModule,
    MaskModule,
    FormsModule,
    CommonModule,
    MessageModule,
    CKEditorModule,
    DataTableModule,
    ValidationModule,
    SharedComponentsModule,
    TypeaheadModule.forRoot()
  ],
  exports: [
    ImpugnacaoJustificativaProfissionalComponent
  ]
})
export class SharedImpugnacaoComponentsModule {
  
 }