import { Component, OnInit, TemplateRef, Input, Output, EventEmitter } from '@angular/core';
import { LayoutsService } from '@cau/layout';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';
import { ConviteChapaEleicaoClientService } from 'src/app/client/convite-chapa-eleicao-client/convite-chapa-eleicao-client.service';
import { Container } from '@angular/compiler/src/i18n/i18n_ast';

@Component({
    selector: 'app-list-convite-chapa',
    templateUrl: './list-convite-chapa.component.html',
    styleUrls: ['./list-convite-chapa.component.scss']
})
export class ListConviteChapaComponent implements OnInit {

    @Input() public convites: Array<any>;
    @Input() public usuario;
    @Output() public aceitar: EventEmitter<any> = new EventEmitter<any>();
    public conviteSelecionado: any;

    public modalRef: BsModalRef;
    public submitted: boolean;
    public isConviteAceito: boolean;

    /**
     * Construtor da classe.
     *
     * @param route
     * @param router
     * @param messageService
     * @param SecurityService
     * @param ConviteChapaEleicaoClientService
     */
    constructor(
        private layoutsService: LayoutsService,
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private modalService: BsModalService,
        private conviteChapaEleicaoService: ConviteChapaEleicaoClientService
    ) {
        //this.convites = route.snapshot.data["convites"];
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
    /**
    * Define ícone e título do header da página
    */
    this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-wpforms',
        description: this.messageService.getDescription('LABEL_CONFIRMAR_PARTICIPACAO_CHAPA_ELEITORAL')
      });

    }

    /**
     * Mostra modal para visualização de plataforma eleitoral.
     *
     * @param template
     */
    public abrirModalVisualizarPlataformaEleitoral(template: TemplateRef<any>, convite: any): void {
        this.conviteSelecionado = convite;
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg modal-dialog-centered' }));
    }

    /**
   * Retorna a opsição formatada
   *
   * @param numeroOrdem
   */
    public getPosicaoFormatada(numeroOrdem) {
        return numeroOrdem > 0 ? numeroOrdem : '-';
    }

    /**
     * Rejeitar convite para participação de chapa.
     *
     * @param convite
     */
    public rejeitarConvite(convite: any): void{
        this.conviteChapaEleicaoService.rejeitarConvite(convite).subscribe(
            data => {
                this.removerConviteRejeitado(convite);
                this.modalRef.hide();
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Remover convite Rejeitado da lista de convites.
     *
     * @param convite
     */
    public removerConviteRejeitado(convite): void {
        this.convites = this.convites.filter(iConvite => {
            return iConvite.idChapaEleicao != convite.idChapaEleicao
        });
    }

    /**
     * Aceita convite para participação da chapa.
     *
     * @param convite
     */
    public aceitarConvite(convite: any): void {
        this.conviteChapaEleicaoService.validacaoCauUfConvidado(convite.idChapaEleicao).subscribe(
            data => {
                this.aceitar.emit(convite);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Mostra mensagem de membro não possui convite.
     */
    public isMostrarMsgNaoPossuiConvite(): boolean {
        return (this.convites.length == 0);
    }

    /**
     * Retorna CAUUF do usuário logado.
     */
    public getDescricaoCauUfUsuario(): string {
        return this.usuario.cauUf.prefixo;
    }

    /**
     * Abre modal para confirmação de rejeição do convite.
     *
     * @param template
     * @param convite
     */
    public abrirModalConfirmarRejeicaoConvite(template: TemplateRef<any>, convite: any): void {
        this.conviteSelecionado = convite;
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg modal-dialog-centered' }));
    }

    /**
     * Retorna mensagem de descrição.
     * @param msg
     */
    public getMsgDescricao(msg: string): string {
        return this.messageService.getDescription(msg);
    }

    /**
     * Retorna string de msg de convite.
     *
     * @param convites
     */
    public getMessageParabensVoceFoiConvidado(convites: Array<any>): string {
        let msg: string = undefined;
        var tipo: number = undefined
        convites.forEach((convite) => {
            if(convite.tipoCandidaturas == tipo || tipo == undefined) {
                tipo = convite.tipoCandidatura;
            } else {
                tipo = 0;
            }
        });

        if(tipo == Constants.TIPO_CONSELHEIRO_IES) {
            msg = this.messageService.getDescription('LABEL_PARABENS_VOCE_FOI_CONVIDADO_PARTICIPAR_DAS_ELEICOES_IES');
        }

        if(tipo == Constants.TIPO_CONSELHEIRO_UF_BR) {
            msg = this.messageService.getDescription('LABEL_PARABENS_VOCE_FOI_CONVIDADO_PARTICIPAR_DAS_ELEICOES_UF', [this.getDescricaoCauUfUsuario()]);
        }

        if(tipo == 0) {
            msg = this.messageService.getDescription('LABEL_PARABENS_VOCE_FOI_CONVIDADO_PARTICIPAR_DAS_ELEICOES_UF_IES', [this.getDescricaoCauUfUsuario()]);
        }
        return msg;
    }
}