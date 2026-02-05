import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { UiSwitchModule } from 'ngx-ui-switch';
import { MessageModule } from '@cau/message';
import { OrderModule } from 'ngx-order-pipe';
import { TabsModule } from 'ngx-bootstrap/tabs';
import { ModalModule } from 'ngx-bootstrap/modal';
import { DataTableModule } from 'angular-6-datatable';
import { FlexLayoutModule } from '@angular/flex-layout';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { ProgressbarModule } from 'ngx-bootstrap/progressbar';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { CalendarModule, MaskModule, ValidationModule, FileModule } from '@cau/component';

import { CalendarioRoutes } from './calendario.router';
import { FormCalendarioComponent } from './form-calendario/form-calendario.component';
import { ListCalendarioComponent } from './list-calendario/list-calendario.component';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { PrazoComponent } from './abas-calendario/aba-prazo-calendario/prazo/prazo.component';
import { CalendarioClientModule } from '../../client/calendario-client/calendario-client.module';
import { ModalJustificativaCalendarioComponent } from './abas-calendario/modal-justificativa-calendario/modal-justificativa-calendario.component';
import { ProgressbarCadastrarCalendarioComponent } from './abas-calendario/progressbar-cadastrar-calendario/progressbar-cadastrar-calendario.component';
import { ListHistoricoCalendarioComponent } from './abas-calendario/aba-historico-calendario/list-historico-calendario/list-historico-calendario.component';
import { FormCadastroPeriodoCalendarioComponent } from './abas-calendario/aba-periodo-calendario/form-cadastro-periodo-calendario/form-cadastro-periodo-calendario.component';
import { FormAtividadesArvorePrazoCalendarioComponent } from './abas-calendario/aba-prazo-calendario/form-atividades-arvore-prazo-calendario/form-atividades-arvore-prazo-calendario.component';

/**
 * Modulo Calendario.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
  declarations: [
    PrazoComponent,
    FormCalendarioComponent,
    ListCalendarioComponent,
    ListHistoricoCalendarioComponent,
    ModalJustificativaCalendarioComponent,
    FormCadastroPeriodoCalendarioComponent,
    ProgressbarCadastrarCalendarioComponent,
    FormAtividadesArvorePrazoCalendarioComponent,
  ],
  imports: [
    MaskModule,
    OrderModule,
    FormsModule,
    CommonModule,
    MessageModule,
    CalendarModule,
    CalendarioClientModule,
    FileModule,
    ValidationModule,
    DataTableModule,
    FlexLayoutModule,
    SharedComponentsModule,
    UiSwitchModule.forRoot({
      size: 'small',
      color: '#14385D',
      switchColor: '#FFFFFF',
      defaultBgColor: '#CDCDCD'
    }),
    TabsModule.forRoot(),
    ModalModule.forRoot(),
    BsDropdownModule.forRoot(),
    ProgressbarModule.forRoot(),
    NgMultiSelectDropDownModule.forRoot(),
    RouterModule.forChild(CalendarioRoutes),
  ]
})
export class CalendarioModule {


}
