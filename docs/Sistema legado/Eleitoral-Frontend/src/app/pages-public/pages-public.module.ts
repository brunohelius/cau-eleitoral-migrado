import { NgModule } from '@angular/core';
import { MessageModule } from '@cau/message';
import { CommonModule } from '@angular/common';
import { BsDatepickerModule, BsDropdownModule } from 'ngx-bootstrap';
import { FlexLayoutModule } from '@angular/flex-layout';

import { PagesPublicRoutingModule } from './pages-public-routing.module';
import { ExtratoChapaComponent } from './extrato-chapa/extrato-chapa.component';
import { FormsModule } from '@angular/forms';
import { SharedComponentsModule } from '../shared/component/shared-components.module';
import { DataTableModule } from 'angular-6-datatable';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule } from '@cau/component';


/**
 * @author Squadra Tecnologia
 */
@NgModule({
  declarations: [
    ExtratoChapaComponent
  ],
  imports: [
    CommonModule,
    MessageModule,
    FlexLayoutModule,
    PagesPublicRoutingModule,
    BsDatepickerModule,
    FormsModule,
    DataTableModule,
    SharedComponentsModule,
    NgMultiSelectDropDownModule,
    BsDropdownModule.forRoot(),
    FileModule,
  ]
})
export class PagesPublicModule {

}
