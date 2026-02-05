import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { RouterModule } from '@angular/router';
import { UiSwitchModule } from 'ngx-ui-switch';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { CommonModule, DatePipe } from '@angular/common';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { ProgressbarModule, PopoverModule} from 'ngx-bootstrap';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { FileModule, MaskModule, CalendarModule, ValidationModule } from '@cau/component';

import { EleicaoRoutes } from './eleicao.router';

import { CauUFClientModule } from 'src/app/client/cau-uf-client/cau-uf-client.module';
import { SharedComponentsModule } from 'src/app/shared/component/shared-components.module';
import { ProfissionalClientModule } from 'src/app/client/profissional-client/profissional-client.module';
import { ComissaoEleitoralModule } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.module';

import { SubstituicaoChapaUfClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-chapa-uf.resolve';
import { AcompanharSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-client.resolve';
import { JulgamentoSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/julgamento-substituicao-client.resolve';
import { EleicoesSubstituicaoChapaResolve } from 'src/app/client/substituicao-chapa-client/eleicoes-substituicao-chapa-client.resolve';
import { JulgamentoSegundaInstanciaClientResolve } from 'src/app/client/substituicao-chapa-client/julgamento-segunda-instancia-client.resolve';
import { AcompanharSubstituicaoDetalhamentoResolve } from 'src/app/client/acompanhar-substituicao-detalhamento-client/acompanhar-substituicao-detalhamento-client.resolve';

import { ListEleicaoComponent } from './membros-comissao/list-eleicao/list-eleicao.component';
import { VisualizarChapaComponent } from './chapa-eleicao/visualizar-chapa/visualizar-chapa.component';
import { ListChapaCauUfComponent } from './chapa-eleicao/list-chapa-cau-uf/list-chapa-cau-uf.component';
import { ListEleicaoConcluidaComponent } from './list-eleicao-concluida/list-eleicao-concluida.component';
import { ListChapaEleicaoComponent } from './chapa-eleicao/list-chapa-eleicao/list-chapa-eleicao.component';
import { ListEleicaoChapaComponent } from './chapa-eleicao/list-eleicao-chapa/list-eleicao-chapa.component';
import { ListAtividadePrincipalComponent } from './list-atividade-principal/list-atividade-principal.component';
import  { AcompanharSubstituicao } from './substituicao/acompanhar-substituicao/acompanhar-substituicao.component';
import { FormComissaoMembroComponent } from './membros-comissao/form-comissao-membro/form-comissao-membro.component';
import { ListComissaoMembroComponent } from './membros-comissao/list-comissao-membro/list-comissao-membro.component';
import  { AcompanharSubstituicaoUF } from './substituicao/acompanhar-substituicao-uf/acompanhar-substituicao-uf.component';
import { HistNumeroConselheiroComponent } from './numero-conselheiro/hist-numero-conselheiro/hist-numero-conselheiro.component';
import { FormNumeroConselheiroComponent } from './numero-conselheiro/form-numero-conselheiro/form-numero-conselheiro.component';
import { FormInformacaoComissaoMembroComponent } from './form-informacao-comissao-membro/form-informacao-comissao-membro.component';
import { AbaHistoricoChapaEleicaoComponent } from './chapa-eleicao/aba-historico-chapa-eleicao/aba-historico-chapa-eleicao.component';
import  { ListEleicaoChapaSubstituicaoComponent } from './substituicao/list-eleicao-chapa-substituicao/list-eleicao-chapa-substituicao';
import { ExtratoNumeroConselheiroComponent } from './numero-conselheiro/extrato-numero-conselheiro/extrato-numero-conselheiro.component';
import { MembrosChapaSubstituicaoComponent } from './substituicao/shared/membros-chapa-substituicao/membros-chapa-substituicao.component';
import { PublicarDocumentoComissaoMembroComponent } from './publicar-documento-comissao-membro/publicar-documento-comissao-membro.component';
import { AtividadeSecundariaRecursoClientResolve } from 'src/app/client/substituicao-chapa-client/atividade-secundaria-recurso-client.resolve';
import { DefinirEmailDeclaracaoPorAtividadeComponent } from './definir-email-declaracao-por-atividade/definir-email-declaracao-por-atividade.component';
import { AbaCadastroComissaoMembroComponent } from './abas-cadastro-comissao-membro/aba-cadastro-comissao-membro/aba-cadastro-comissao-membro.component';
import  { AcompanharSubstituicaoDetalhamento } from './substituicao/acompanhar-substituicao-detalhamento/acompanhar-substituicao-detalhamento.component';
import { AbaDocumentoComissaoMembroComponent } from './abas-informacao-comissao-membro/aba-documento-comissao-membro/aba-documento-comissao-membro.component';
import { AbaInformacaoComissaoMembroComponent } from './abas-informacao-comissao-membro/aba-informacao-comissao-membro/aba-informacao-comissao-membro.component';
import { AbaJulgamentoSubstituicaoComponent } from './substituicao/acompanhar-substituicao-detalhamento/aba-julgamento-substituicao/aba-julgamento-substituicao.component';
import { AbaMembrosChapaVisualizarComponent } from './chapa-eleicao/visualizar-chapa/abas-visualizar-chapa/aba-membros-chapa-visualizar/aba-membros-chapa-visualizar.component';
import { AbaCadastroHistoricoComissaoMembroComponent } from './abas-cadastro-comissao-membro/aba-cadastro-historico-comissao-membro/aba-cadastro-historico-comissao-membro.component';
import { AbaAcompanharInterporRecursoComponent } from './substituicao/acompanhar-substituicao-detalhamento/aba-acompanhar-interpor-recurso/aba-acompanhar-interpor-recurso.component';
import { ProgressbarInformacaoComissaoMembroComponent } from './abas-informacao-comissao-membro/progressbar-informacao-comissao-membro/progressbar-informacao-comissao-membro.component';
import { AbaJulgamentoSegundaInstanciaComponent } from './substituicao/acompanhar-substituicao-detalhamento/aba-julgamento-segunda-instancia/aba-julgamento-segunda-instancia.component';
import { AbaPlataformaEleitoralRedeSocialVisualizarComponent } from './chapa-eleicao/visualizar-chapa/abas-visualizar-chapa/aba-plataforma-eleitoral-rede-social-visualizar/aba-plataforma-eleitoral-rede-social-visualizar.component';
import { PossuiRetificacaoChapaClientResolve } from 'src/app/client/chapa-client/possui-retificacao-chapa-client.resolve';
import { ModalVisualizarRetificacaoPlataformaComponent } from './chapa-eleicao/visualizar-chapa/abas-visualizar-chapa/aba-plataforma-eleitoral-rede-social-visualizar/modal-visualizar-retificacao-plataforma/modal-visualizar-retificacao-plataforma.component';
import { ListEleitosComponent } from './list-eleitos/list-eleitos.component';
import { FiltroPesquisaPipe } from 'src/app/shared/filtro-pesquisar/filtro-pesquisa.pipe';
import { FormTermoDePosseComponent } from './form-termo-de-posse/form-termo-de-posse.component';
import { FormDiplomaEleitoralComponent } from './form-diploma-eleitoral/form-diploma-eleitoral.component';

/**
 * Modulo Calendario.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        ListEleicaoComponent,
        AcompanharSubstituicao,
        ListChapaCauUfComponent,
        AcompanharSubstituicaoUF,
        VisualizarChapaComponent,
        ListEleicaoChapaComponent,
        ListChapaEleicaoComponent,
        ListComissaoMembroComponent,
        FormComissaoMembroComponent,
        ListEleicaoConcluidaComponent,
        ListEleicaoConcluidaComponent,
        FormNumeroConselheiroComponent,
        HistNumeroConselheiroComponent,
        ListAtividadePrincipalComponent,
        ListAtividadePrincipalComponent,
        AbaHistoricoChapaEleicaoComponent,
        MembrosChapaSubstituicaoComponent,
        ExtratoNumeroConselheiroComponent,
        AbaJulgamentoSubstituicaoComponent,
        AcompanharSubstituicaoDetalhamento,
        AbaMembrosChapaVisualizarComponent,
        AbaCadastroComissaoMembroComponent,
        AbaDocumentoComissaoMembroComponent,
        AbaDocumentoComissaoMembroComponent,
        AbaInformacaoComissaoMembroComponent,
        FormInformacaoComissaoMembroComponent,
        ListEleicaoChapaSubstituicaoComponent,
        AbaAcompanharInterporRecursoComponent,
        AbaJulgamentoSegundaInstanciaComponent,
        PublicarDocumentoComissaoMembroComponent,
        PublicarDocumentoComissaoMembroComponent,
        AbaCadastroHistoricoComissaoMembroComponent,
        ProgressbarInformacaoComissaoMembroComponent,
        DefinirEmailDeclaracaoPorAtividadeComponent,
        ProgressbarInformacaoComissaoMembroComponent,
        ModalVisualizarRetificacaoPlataformaComponent,
        AbaPlataformaEleitoralRedeSocialVisualizarComponent,
        ListEleitosComponent,
        FiltroPesquisaPipe,
        FormTermoDePosseComponent,
        FormDiplomaEleitoralComponent
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
        NgMultiSelectDropDownModule,
        RouterModule.forChild(EleicaoRoutes),
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#14385D',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
    ],
    providers: [
        DatePipe,
        EleicoesSubstituicaoChapaResolve,
        SubstituicaoChapaUfClientResolve,
        AcompanharSubstituicaoClientResolve,
        JulgamentoSubstituicaoClientResolve,
        JulgamentoSegundaInstanciaClientResolve,
        AtividadeSecundariaRecursoClientResolve,
        AcompanharSubstituicaoDetalhamentoResolve,
        PossuiRetificacaoChapaClientResolve
    ],
    entryComponents: [
        ModalVisualizarRetificacaoPlataformaComponent
    ]
})
export class EleicaoModule {

}
