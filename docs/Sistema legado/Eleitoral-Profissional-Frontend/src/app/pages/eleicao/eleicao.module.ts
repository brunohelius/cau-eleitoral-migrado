import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { MessageModule } from '@cau/message';
import { UiSwitchModule } from 'ngx-ui-switch';
import { CommonModule } from '@angular/common';
import { FlexModule } from '@angular/flex-layout';
import { CKEditorModule } from 'ckeditor4-angular';
import { DataTableModule } from 'angular-6-datatable';
import { ImageCropperModule } from 'ngx-image-cropper';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { FileModule, MaskModule, ValidationModule } from '@cau/component';
import { ProgressbarModule, PopoverModule, TypeaheadModule, AccordionModule } from 'ngx-bootstrap';

import { RouterModule } from '@angular/router';
import { EleicaoRoutes } from './eleicao.router';

import { ComissaoEleitoralComponent } from './comissao-eleitoral/comissao-eleitoral.component';
import { ConviteMembroComissaoComponent } from './convite-membro-comissao/convite-membro-comissao.component';

import { SharedComponentsModule } from 'src/app/shared/shared-components.module';
import { CauUFClientModule } from 'src/app/client/cau-uf-client/cau-uf-client.module';
import { ChapaEleicaoClientModule } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.module'; 
import { ComissaoEleitoralModule } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.module';
import { ConviteMembroComissaoEleitoralModule } from 'src/app/client/convite-membro-comissao-client/convite-membro-comissao-client.module';
import { AcompanharSubstituicaoResponsavelModule } from 'src/app/client/acompanhar-substituicao-responsavel-client/acompanhar-substituicao-responsavel-client.module';
import { AcompanharSubstituicaoDetalhamentoModule } from 'src/app/client/acompanhar-substituicao-detalhamento-client/acompanhar-substituicao-detalhamento-client.module';

import { BandeiraCauUFResolve } from 'src/app/client/cau-uf-client/bandeira-cau-uf-client.resolve';
import { SubstituicaoChapaClientResolve } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-eleicao-client.resolve';
import { SubstituicaoChapaUfClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-chapa-uf.resolve';
import { AcompanharSubstituicaoClientResolve } from 'src/app/client/substituicao-chapa-client/acompanhar-substituicao-client.resolve';
import { PedidoSubstituicaoChapaClientResolve } from 'src/app/client/substituicao-chapa-client/pedido-substituicao-chapa-client.resolve';
import { AcompanharSubstituicaoResponsavelResolve } from 'src/app/client/acompanhar-substituicao-responsavel-client/acompanhar-substituicao-responsavel-client.resolve';


import { ConviteChapaComponent } from './convite-chapa/convite-chapa.component';
import { CadastroChapaComponent } from './cadastro-chapa/cadastro-chapa.component';
import { VisualizarChapaComponent } from './visualizar-chapa/visualizar-chapa.component';
import { ListConviteChapaComponent } from './convite-chapa/list-convite-chapa/list-convite-chapa.component';
import { AbaDeclaracaoComponent } from './cadastro-chapa/abas-cadastro-chapa/aba-declaracao/aba-declaracao.component';
import { AbaVisaoGeralComponent } from './cadastro-chapa/abas-cadastro-chapa/aba-visao-geral/aba-visao-geral.component';
import { FormCurriculoMembroChapa } from './convite-chapa/form-corriculo-membro-chapa/form-curriculo-membro-chapa.component';
import { AbaMembrosChapaComponent } from './cadastro-chapa/abas-cadastro-chapa/aba-membros-chapa/aba-membros-chapa.component';
import { FormDeclaracaoConviteChapaComponent } from './convite-chapa/form-declaracao-convite-chapa/form-declaracao-convite-chapa.component';
import { ProgressbarCadastroChapaComponent } from './cadastro-chapa/abas-cadastro-chapa/progressbar-cadastro-chapa/progressbar-cadastro-chapa.component';
import { AbaMembrosChapaVisualizarComponent } from './visualizar-chapa/abas-visualizar-chapa/aba-membros-chapa-visualizar/aba-membros-chapa-visualizar.component';
import { ProgressbarVisualizarChapaComponent } from './visualizar-chapa/abas-visualizar-chapa/progressbar-visualizar-chapa/progressbar-visualizar-chapa.component';
import { AbaPlataformaEleitoralRedesSociaisComponent } from './cadastro-chapa/abas-cadastro-chapa/aba-plataforma-eleitoral-redes-sociais/aba-plataforma-eleitoral-redes-sociais.component';
import { AbaPlataformaEleitoralRedeSocialVisualizarComponent } from './visualizar-chapa/abas-visualizar-chapa/aba-plataforma-eleitoral-rede-social-visualizar/aba-plataforma-eleitoral-rede-social-visualizar.component';
import { ModalAlterarDadosMembroChapaComponent } from './visualizar-chapa/abas-visualizar-chapa/aba-membros-chapa-visualizar/modal/modal-alterar-dados-membro-chapa.component';
import { FormDeclaracaoRepresentatividadeComponent } from './convite-chapa/form-declaracao-representatividade/form-declaracao-representatividade.component';

/**
 * Modulo CalendariLABEL_ACOESo.
 *
 * @author Squadra Tecnologia
 */
@NgModule({
    declarations: [
        ConviteChapaComponent,
        CadastroChapaComponent,
        AbaVisaoGeralComponent,
        AbaDeclaracaoComponent,
        AbaMembrosChapaComponent,
        FormCurriculoMembroChapa,
        VisualizarChapaComponent,
        ListConviteChapaComponent,
        ComissaoEleitoralComponent,       
        ConviteMembroComissaoComponent,
        ProgressbarCadastroChapaComponent,
        AbaMembrosChapaVisualizarComponent,
        FormDeclaracaoConviteChapaComponent,
        ProgressbarVisualizarChapaComponent,
        AbaPlataformaEleitoralRedesSociaisComponent,
        AbaPlataformaEleitoralRedeSocialVisualizarComponent,
        ModalAlterarDadosMembroChapaComponent,
        FormDeclaracaoRepresentatividadeComponent
    ],
    entryComponents: [
        ModalAlterarDadosMembroChapaComponent
    ],
    imports: [
        MaskModule,
        FlexModule,
        FileModule,
        FormsModule,
        CommonModule,
        PopoverModule,
        MessageModule,
        CKEditorModule,
        DataTableModule,
        ValidationModule,
        CauUFClientModule,
        ImageCropperModule,
        SharedComponentsModule,
        ComissaoEleitoralModule,
        ChapaEleicaoClientModule,
        ConviteMembroComissaoEleitoralModule,
        AcompanharSubstituicaoResponsavelModule,
        AcompanharSubstituicaoDetalhamentoModule,
        UiSwitchModule.forRoot({
            size: 'small',
            color: '#016C71',
            switchColor: '#FFFFFF',
            defaultBgColor: '#CDCDCD'
        }),
        AccordionModule.forRoot(),
        TypeaheadModule.forRoot(),
        BsDropdownModule.forRoot(),
        ProgressbarModule.forRoot(),
        RouterModule.forChild(EleicaoRoutes),
    ],
    exports: [
        AbaDeclaracaoComponent,
        AbaVisaoGeralComponent,
        ProgressbarCadastroChapaComponent,
        AbaPlataformaEleitoralRedesSociaisComponent,
    ],
    providers: [
        BandeiraCauUFResolve,
        SubstituicaoChapaClientResolve,
        SubstituicaoChapaUfClientResolve,
        AcompanharSubstituicaoClientResolve,
        PedidoSubstituicaoChapaClientResolve,
        AcompanharSubstituicaoResponsavelResolve
    ]
})
export class EleicaoModule {

}
