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

import { FinalizacaoMandatoFormComponent } from './finalizacao-mandato-form/finalizacao-mandato-form.component';
import { FinalizacaoMandatoRoutes } from './finalizacao-mandato.router';


/**
 * Modulo de FinalizacaoMandato
 */
@NgModule({
  declarations: [
    FinalizacaoMandatoFormComponent,
  ],
  imports: [
    RouterModule.forChild(FinalizacaoMandatoRoutes),
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
    SharedComponentsModule,
    NgMultiSelectDropDownModule,
    ProgressbarModule.forRoot(),
    UiSwitchModule.forRoot({
        size: 'small',
        color: '#14385D',
        switchColor: '#FFFFFF',
        defaultBgColor: '#CDCDCD'
    }),
    BsDropdownModule.forRoot(),
  ],
  exports: [
    FinalizacaoMandatoFormComponent
  ],
  providers: [ DatePipe ]
})
export class FinalizacaoMandatoModule { }
