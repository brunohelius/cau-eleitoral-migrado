import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { UiSwitchModule } from 'ngx-ui-switch';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { ProgressbarModule, PopoverModule } from 'ngx-bootstrap';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, CalendarModule, ValidationModule, MaskPipe } from '@cau/component';

import { DenunciaRoutes } from './denuncia.router';
import { CauUFClientModule } from 'src/app/client/cau-uf-client/cau-uf-client.module';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { ProfissionalClientModule } from 'src/app/client/profissional-client/profissional-client.module';
import { ComissaoEleitoralModule } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.module';

import { ListEleicaoComponent } from './list-eleicao/list-eleicao.component';
import { ListDenunciasComponent } from './list-denuncias/list-denuncias.component';
import { ListUfDenunciaComponent } from './list-uf-denuncia/list-uf-denuncia.component';
import { InfoDenunciadoComponent } from './shared/info-denunciado/info-denunciado.component';
import { InfoDenuncianteComponent } from './shared/info-denunciante/info-denunciante.component';
import { VisualizarTestemunhasComponent } from './shared/visualizar-testemunhas/visualizar-testemunhas.component';
import { AbaDetailAcompanharDenunciaComponent } from './visualizar-denuncia/aba-detail-acompanhar-denuncia/aba-detail-acompanhar-denuncia.component';
import { VisualizarDenunciaComponent } from './visualizar-denuncia/visualizar-denuncia.component';
import { AbaAnaliseAdmissibilidadeComponent } from './visualizar-denuncia/aba-analise-admissibilidade/aba-analise-admissibilidade.component';
import { AbaDefesaDenunciaComponent } from './visualizar-denuncia/aba-defesa-denuncia/aba-defesa-denuncia.component';
import { AbaParecerDenunciaComponent } from './visualizar-denuncia/aba-parecer-denuncia/aba-parecer-denuncia.component';
import { ModalVisualizarProducaoProvasComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-producao-provas/modal-visualizar-producao-provas.component';
import { ModalVisualizarImpedimentoSuspeicaoComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-impedimento-suspeicao/modal-visualizar-impedimento-suspeicao.component';
import { ModalVisualizarAudienciaInstrucaoComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-audiencia-instrucao/modal-visualizar-audiencia-instrucao.component';
import { ModalJulgamentoPrimeiraInstanciaComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-julgamento-primeira-instancia/modal-julgamento-primeira-instancia.component';
import { PrimeiraInstanciaComponent } from './julgamento/primeira-instancia/primeira-instancia.component';
import { AbaRecursoDenuncianteComponent } from './visualizar-denuncia/aba-recurso-denunciante/aba-recurso-denunciante.component';
import { AbaRecursoDenunciadoComponent } from './visualizar-denuncia/aba-recurso-denunciado/aba-recurso-denunciado.component';
import { DadosRecursoDenunciaComponent } from './shared/dados-recurso-denuncia/dados-recurso-denuncia.component';
import { ModalVisualizarAlegacaoFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-alegacao-final/modal-visualizar-alegacao-final.component';
import { ModalVisualizarParecerFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-parecer-final/modal-visualizar-parecer-final.component';
import { SegundaInstanciaComponent } from './julgamento/segunda-instancia/segunda-instancia.component';
import { ModalJulgamentoRecursoComponent } from './shared/modal-julgamento-recurso/modal-julgamento-recurso.component';


import { ModalJulgarAdmissibilidadeComponent } from './visualizar-denuncia/aba-analise-admissibilidade/modal-julgar-admissibilidade/modal-julgar-admissibilidade.component';
import {AbaJulgamentoAdmissibilidadeComponent} from './visualizar-denuncia/aba-julgamento-admissibilidade/aba-julgamento-admissibilidade.component';
import { AbaRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-recurso-admissibilidade/aba-recurso-admissibilidade.component';
import { AbaJulgamentoRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-julgamento-recurso-admissibilidade/aba-julgamento-recurso-admissibilidade.component';
import { ModalJulgamentoRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-julgamento-recurso-admissibilidade/modal-julgamento-recurso-admissibilidade/modal-julgamento-recurso-admissibilidade.component';
import { AlertaDenunciaSigilosaComponent } from './visualizar-denuncia/alerta-denuncia-sigilosa/alerta-denuncia-sigilosa.component';
import { ListJulgamentosRetificadosComponent } from './shared/list-julgamentos-retificados/list-julgamentos-retificados.component';
import { ModalGerarDocumentoComponent } from './list-denuncias/modal-gerar-documento/modal-gerar-documento.component';
import { DetalhesAnaliseComponent } from './visualizar-denuncia/aba-analise-admissibilidade/detalhes-analise/detalhes-analise.component';

/**
 * Modulo Calendario.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        ListEleicaoComponent,
        ListDenunciasComponent,
        ListUfDenunciaComponent,
        InfoDenunciadoComponent,
        InfoDenuncianteComponent,
        VisualizarTestemunhasComponent,
        AbaDetailAcompanharDenunciaComponent,
        AbaAnaliseAdmissibilidadeComponent,
        AbaDefesaDenunciaComponent,
        VisualizarDenunciaComponent,
        AbaParecerDenunciaComponent,
        ModalVisualizarProducaoProvasComponent,
        ModalVisualizarImpedimentoSuspeicaoComponent,
        ModalVisualizarAudienciaInstrucaoComponent,
        ModalJulgamentoPrimeiraInstanciaComponent,
        ModalJulgamentoRecursoComponent,
        PrimeiraInstanciaComponent,
        AbaRecursoDenuncianteComponent,
        AbaRecursoDenunciadoComponent,
        DadosRecursoDenunciaComponent,
        ModalVisualizarAlegacaoFinalComponent,
        ModalVisualizarParecerFinalComponent,
        SegundaInstanciaComponent,
        ModalJulgamentoRecursoComponent,
        ListJulgamentosRetificadosComponent,
        ModalJulgarAdmissibilidadeComponent,
        PrimeiraInstanciaComponent,
        AbaJulgamentoAdmissibilidadeComponent,
        AbaRecursoAdmissibilidadeComponent,
        AbaJulgamentoRecursoAdmissibilidadeComponent,
        AlertaDenunciaSigilosaComponent,

        ModalJulgamentoRecursoAdmissibilidadeComponent,
        AlertaDenunciaSigilosaComponent,
        ListJulgamentosRetificadosComponent,
        ModalGerarDocumentoComponent,
        DetalhesAnaliseComponent
    ],
    entryComponents: [
        ModalJulgarAdmissibilidadeComponent,
        PrimeiraInstanciaComponent,
        SegundaInstanciaComponent,
        ModalGerarDocumentoComponent,
        ModalJulgarAdmissibilidadeComponent,
        ModalVisualizarProducaoProvasComponent,
        ModalVisualizarImpedimentoSuspeicaoComponent,
        ModalVisualizarAudienciaInstrucaoComponent,
        ModalJulgamentoPrimeiraInstanciaComponent,
        ModalVisualizarAlegacaoFinalComponent,
        ModalVisualizarParecerFinalComponent,
        ModalJulgamentoRecursoComponent,
        ModalJulgamentoRecursoAdmissibilidadeComponent
      ],
    imports: [
        MaskModule,
        FlexModule,
        FormsModule,
        FileModule,
        CommonModule,
        PopoverModule,
        MessageModule,
        CalendarModule,
        DataTableModule,
        CKEditorModule,
        ValidationModule,
        CauUFClientModule,
        SharedComponentsModule,
        ProfissionalClientModule,
        ComissaoEleitoralModule,
        BsDropdownModule.forRoot(),
        ProgressbarModule.forRoot(),
        RouterModule.forChild(DenunciaRoutes),
        NgMultiSelectDropDownModule,
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#14385D',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
    ],
    providers: [
        MaskPipe
    ]

})
export class DenunciaModule {

}
