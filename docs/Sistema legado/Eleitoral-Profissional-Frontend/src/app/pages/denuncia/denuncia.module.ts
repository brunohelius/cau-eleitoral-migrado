import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { RouterModule } from "@angular/router";
import { FlexModule } from '@angular/flex-layout';

import { MessageModule } from "@cau/message";
import { UiSwitchModule } from "ngx-ui-switch";
import { CKEditorModule } from "ckeditor4-angular";
import { DenunciaRoutes } from './denuncia.router';
import { DataTableModule } from 'angular-6-datatable';
import { PopoverModule, TypeaheadModule, BsDropdownModule } from 'ngx-bootstrap';
import { CalendarModule, FileModule, MaskModule, ValidationModule, MaskPipe } from '@cau/component';
import { FormDenunciaComponent } from "./form-denuncia/form-denuncia.component";
import { SharedComponentsModule } from 'src/app/shared/shared-components.module';
import { TestemunhasComponent } from './shared/testemunhas/testemunhas.component';
import { InfoDenunciadoComponent } from './shared/info-denunciado/info-denunciado.component';
import { InfoDenuncianteComponent } from './shared/info-denunciante/info-denunciante.component';
import { SelecaoDenunciadoComponent } from './shared/selecao-denunciado/selecao-denunciado.component';
import { AutocompleteMembroComponent } from './shared/selecao-denunciado/autocomplete/autocomplete.component';
import { AbaCadastroDenunciaComponent } from "./form-denuncia/aba-cadastro-denuncia/aba-cadastro-denuncia.component";
import { VisualizarDenunciaComponent } from './visualizar-denuncia/visualizar-denuncia.component';
import { AbaAcompanharDenunciaComponent } from './visualizar-denuncia/aba-acompanhar-denuncia/aba-acompanhar-denuncia.component';
import { AbaListaDenunciaEstadoComponent } from './visualizar-denuncia/aba-lista-denuncia-estado/aba-lista-denuncia-estado.component';
import { AbaListaDenunciasComponent } from './visualizar-denuncia/aba-lista-denuncias/aba-lista-denuncias.component';
import { VisualizarTestemunhasComponent } from './shared/visualizar-testemunhas/visualizar-testemunhas.component';
import { ListDenunciaEstadoComissaoComponent } from './comissao/list-denuncia-estado/list-denuncia-estado-comissao.component';
import { ListDenunciasComissaoComponent } from './comissao/list-denuncias/list-denuncias-comissao.component';
import { AbaListaDenunciasAdmissibilidadeComponent } from './comissao/list-denuncia-estado/aba-lista-denuncias-admissibilidade/aba-lista-denuncias-admissibilidade.component';
import { AdmitirDenunciaComponent } from './admitir-denuncia/admitir-denuncia.component';
import { ModalInadmitirDenunciaComponent } from './visualizar-denuncia/aba-acompanhar-denuncia/modal-inadmitir-denuncia/modal-inadmitir-denuncia.component';
import { AbaAnaliseAdmissibilidadeComponent } from './visualizar-denuncia/aba-analise-admissibilidade/aba-analise-admissibilidade.component';
import { ModalApresentarDefesaComponent } from './visualizar-denuncia/aba-analise-admissibilidade/modal-apresentar-defesa/modal-apresentar-defesa.component';
import { ModalAnalisarDefesaComponent } from './visualizar-denuncia/aba-defesa-denuncia/modal-analisar-defesa/modal-analisar-defesa.component';
import { AbaDefesaDenunciaComponent } from './visualizar-denuncia/aba-defesa-denuncia/aba-defesa-denuncia.component';
import { AbaParecerDenunciaComponent } from './visualizar-denuncia/aba-parecer-denuncia/aba-parecer-denuncia.component';
import { ModalVisualizarProducaoProvasComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-producao-provas/modal-visualizar-producao-provas.component';
import { ModalFormProvasComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-form-provas/modal-form-provas.component';
import { ModalJustificativaEncaminhamentoComponent } from './visualizar-denuncia/aba-defesa-denuncia/modal-justificativa-encaminhamento/modal-justificativa-encaminhamento.component';
import { ModalAlegacaoFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-alegacao-final/modal-alegacao-final.component';
import { ModalVisualizarImpedimentoSuspeicaoComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-impedimento-suspeicao/modal-visualizar-impedimento-suspeicao.component';
import { ModalVisualizarAudienciaInstrucaoComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-audiencia-instrucao/modal-visualizar-audiencia-instrucao.component';
import { ModalInserirRelatorComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-inserir-relator/modal-inserir-relator.component';
import { ModalContrarrazaoComponent } from './julgamento/modal-contrarrazao/modal-contrarrazao.component';
import { RecursoReconsideracaoComponent } from './julgamento/recurso-reconsideracao/recurso-reconsideracao.component';
import { PrimeiraInstanciaComponent } from './julgamento/primeira-instancia/primeira-instancia.component';
import { ModalParecerFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-parecer-final/modal-parecer-final.component';
import { AbaRecursoDenuncianteComponent } from './visualizar-denuncia/aba-recurso-denunciante/aba-recurso-denunciante.component';
import { AbaRecursoDenunciadoComponent } from './visualizar-denuncia/aba-recurso-denunciado/aba-recurso-denunciado.component';
import { ModalVisualizarParecerFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-parecer-final/modal-visualizar-parecer-final.component';
import { ModalVisualizarAlegacaoFinalComponent } from './visualizar-denuncia/aba-parecer-denuncia/modal-visualizar-alegacao-final/modal-visualizar-alegacao-final.component';
import { DadosRecursoDenunciaComponent } from './shared/dados-recurso-denuncia/dados-recurso-denuncia.component';
import { SegundaInstanciaComponent } from './julgamento/segunda-instancia/segunda-instancia.component';
import {ModalCadastrarAudienciaInstrucaoComponent} from './visualizar-denuncia/aba-parecer-denuncia/modal-cadastrar-audiencia-instrucao/modal-cadastrar-audiencia-instrucao.component';
import { ModalInserirRelatorJulgarAdmissibilidadeComponent } from './visualizar-denuncia/aba-analise-admissibilidade/modal-inserir-relator-julgar-admissibilidade/modal-inserir-relator-julgar-admissibilidade.component';
import { AbaJulgamentoAdmissibilidadeComponent } from './visualizar-denuncia/aba-julgamento-admissibilidade/aba-julgamento-admissibilidade.component';
import { ModalRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-recurso-admissibilidade/modal-recurso-admissibilidade/modal-recurso-admissibilidade.component';
import { AbaRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-recurso-admissibilidade/aba-recurso-admissibilidade.component';
import { AbaJulgamentoRecursoAdmissibilidadeComponent } from './visualizar-denuncia/aba-julgamento-recurso-admissibilidade/aba-julgamento-recurso-admissibilidade.component';
import { AlertaDenunciaSigilosaComponent } from './visualizar-denuncia/alerta-denuncia-sigilosa/alerta-denuncia-sigilosa.component';
import { DetalhesAnaliseComponent } from './visualizar-denuncia/aba-analise-admissibilidade/detalhes-analise/detalhes-analise.component';

@NgModule({
  declarations: [
    TestemunhasComponent,
    FormDenunciaComponent,
    InfoDenunciadoComponent,
    AdmitirDenunciaComponent,
    InfoDenuncianteComponent,
    ModalFormProvasComponent,
    SegundaInstanciaComponent,
    AbaDefesaDenunciaComponent,
    SelecaoDenunciadoComponent,
    AbaListaDenunciasComponent,
    ModalContrarrazaoComponent,
    PrimeiraInstanciaComponent,
    ModalParecerFinalComponent,
    AbaParecerDenunciaComponent,
    VisualizarDenunciaComponent,
    AutocompleteMembroComponent,
    ModalAlegacaoFinalComponent,
    AbaCadastroDenunciaComponent,
    ModalAnalisarDefesaComponent,
    ModalInserirRelatorComponent,
    AbaRecursoDenunciadoComponent,
    DadosRecursoDenunciaComponent,
    RecursoReconsideracaoComponent,
    AbaRecursoDenuncianteComponent,
    AbaAcompanharDenunciaComponent,
    VisualizarTestemunhasComponent,
    ListDenunciasComissaoComponent,
    ModalApresentarDefesaComponent,
    AbaListaDenunciaEstadoComponent,
    ModalInadmitirDenunciaComponent,
    AbaAnaliseAdmissibilidadeComponent,
    ListDenunciaEstadoComissaoComponent,
    AbaListaDenunciasAdmissibilidadeComponent,
    ModalVisualizarProducaoProvasComponent,
    ModalJustificativaEncaminhamentoComponent,
    ModalCadastrarAudienciaInstrucaoComponent,
    ModalVisualizarImpedimentoSuspeicaoComponent,
    ModalVisualizarAudienciaInstrucaoComponent,
    ModalVisualizarParecerFinalComponent,
    ModalVisualizarAlegacaoFinalComponent,
    ModalInserirRelatorJulgarAdmissibilidadeComponent,
    AbaJulgamentoAdmissibilidadeComponent,
    AbaRecursoAdmissibilidadeComponent,
    RecursoReconsideracaoComponent,
    AbaJulgamentoAdmissibilidadeComponent,
    ModalRecursoAdmissibilidadeComponent,
    AbaRecursoAdmissibilidadeComponent,
    AbaJulgamentoRecursoAdmissibilidadeComponent,
    AlertaDenunciaSigilosaComponent,
    DetalhesAnaliseComponent
  ],
  entryComponents: [
    ModalParecerFinalComponent,
    ModalContrarrazaoComponent,
    ModalAlegacaoFinalComponent,
    ModalInserirRelatorComponent,
    ModalContrarrazaoComponent,
    RecursoReconsideracaoComponent,
    PrimeiraInstanciaComponent,
    ModalParecerFinalComponent,
    ModalVisualizarImpedimentoSuspeicaoComponent,
    ModalVisualizarAudienciaInstrucaoComponent,
    ModalCadastrarAudienciaInstrucaoComponent,
    ModalVisualizarParecerFinalComponent,
    ModalVisualizarAlegacaoFinalComponent,
    ModalInserirRelatorJulgarAdmissibilidadeComponent,
    ModalFormProvasComponent,
    ModalAnalisarDefesaComponent,
    ModalApresentarDefesaComponent,
    ModalInadmitirDenunciaComponent,
    ModalVisualizarProducaoProvasComponent,
    ModalInadmitirDenunciaComponent,
    ModalJustificativaEncaminhamentoComponent,
    ModalAlegacaoFinalComponent,
    ModalInserirRelatorComponent,
    ModalVisualizarImpedimentoSuspeicaoComponent,
    ModalVisualizarAudienciaInstrucaoComponent,
    ModalInserirRelatorComponent,
    ModalParecerFinalComponent,
    ModalContrarrazaoComponent,
    ModalRecursoAdmissibilidadeComponent
  ],
  imports: [
    FlexModule,
    FileModule,
    MaskModule,
    FormsModule,
    CommonModule,
    PopoverModule,
    MessageModule,
    CalendarModule,
    CKEditorModule,
    DataTableModule,
    TypeaheadModule,
    ValidationModule,
    BsDropdownModule.forRoot(),
    SharedComponentsModule,
    UiSwitchModule.forRoot({
      size: 'small',
      color: '#016C71',
      switchColor: '#FFFFFF',
      defaultBgColor: '#CDCDCD'
    }),
    RouterModule.forChild(DenunciaRoutes),
  ],
  providers: [
    MaskPipe
  ]
})
export class DenunciaModule {

}
