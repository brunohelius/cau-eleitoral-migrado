import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { CommonModule } from '@angular/common';
import { UiSwitchModule } from 'ngx-ui-switch';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { ValidationModule, FileModule } from '@cau/component';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';

import { EmailRoutes } from './email.router';
import { RouterModule } from '@angular/router';
import { CauUFClientModule } from 'src/app/client/cau-uf-client/cau-uf-client.module';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { ListCabecalhoEmailComponent } from './cabecalho-email/list-cabecalho-email/list-cabecalho-email.component';
import { FormCabecalhoEmailComponent } from './cabecalho-email/form-cabecalho-email/form-cabecalho-email.component';
import { ListCorpoEmailComponent } from './corpo-email/list-corpo-email/list-corpo-email.component';
import { FormCorpoEmailComponent } from './corpo-email/form-corpo-email/form-corpo-email.component';
import { CabecalhoEmailClientModule } from 'src/app/client/cabecalho-email/cabecalho-email-client.module';

/**
 * Modulo de E-mails.
 */
@NgModule({
    declarations: [
        ListCorpoEmailComponent,
        FormCorpoEmailComponent,
        FormCabecalhoEmailComponent,
        ListCabecalhoEmailComponent,
    ],
    imports: [
        FormsModule,
        CommonModule,
        MessageModule,
        DataTableModule,
        CKEditorModule,
        CauUFClientModule,
        ValidationModule,
        SharedComponentsModule,
        CabecalhoEmailClientModule,
        NgMultiSelectDropDownModule,
        FileModule,
        RouterModule.forChild(EmailRoutes),
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#14385D',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
        BsDropdownModule.forRoot(),
    ]
})
export class EmailModule {

}
