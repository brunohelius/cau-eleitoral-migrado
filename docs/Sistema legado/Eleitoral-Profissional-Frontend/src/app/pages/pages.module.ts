import { NgModule } from '@angular/core';
import { MessageModule } from '@cau/message';
import { CommonModule } from '@angular/common';
import { BsDatepickerModule } from 'ngx-bootstrap';
import { FlexLayoutModule } from '@angular/flex-layout';
import { PagesRoutingModule } from './pages-routing.module';
import { PaginaInicialComponent } from './home/pagina-inicial.component';

/**
 * Pages Module
 *
 * @author Squadra Tecnologia
 */
@NgModule({
  imports: [
    CommonModule,
    MessageModule,
    FlexLayoutModule,
    PagesRoutingModule,
    BsDatepickerModule
  ],
  declarations: [PaginaInicialComponent]

})
export class PagesModule { }
