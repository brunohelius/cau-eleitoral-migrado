import { Component, OnInit, TemplateRef, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ConviteChapaEleicaoClientService } from 'src/app/client/convite-chapa-eleicao-client/convite-chapa-eleicao-client.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { element } from 'protractor';

import * as deepEqual from "deep-equal";
import * as _ from 'lodash';

@Component({
    selector: 'app-form-declaracao-convite-chapa',
    templateUrl: './form-declaracao-convite-chapa.component.html',
    styleUrls: ['./form-declaracao-convite-chapa.component.scss']
})
export class FormDeclaracaoConviteChapaComponent implements OnInit {
    @Input() public convite: any;
    @Input() public curriculo: any;
    @Input() public usuario: any;
    @Output() public confirmarEvent: EventEmitter<any> = new EventEmitter<any>();
    @Output() public voltarEvent: EventEmitter<any> = new EventEmitter<any>();
    @Output() public cancelarEvent: EventEmitter<any> = new EventEmitter<any>();

    public declaracaoParametrizada: any;
    public modalRef: BsModalRef;
    private _dataForm: any;

    /**
     * Método contrutor da classe
     *
     * @param route
     * @param router
     * @param messageService
     * @param modalService
     * @param ConviteChapaEleicaoClientService
     * @param chapaEleicaoClientService
     */
    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private modalService: BsModalService,
        private conviteChapaEleicaoService: ConviteChapaEleicaoClientService,
        private chapaEleicaoClientService: ChapaEleicaoClientService
    ) {

    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarDeclaracao();
    }

    /**
     * Confirma preenchimento da delação de participação de eleição.
     */
    public confirmar(): void {
        this.confirmarEvent.emit(null);
    }

    /**
     * Voltar para a pagina de Currículo do membro da chapa..
     */
    public voltar( ): void {
        if(this.isCamposAlterados()) {
            this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR', () => {
                this.voltarEvent.emit(null);
            });
        } else {
            this.voltarEvent.emit(null);
        }
    }

    /**
     * Verifica se houve alteração nos campos do formulário.
     */
    public isCamposAlterados(): boolean {
        return !deepEqual(this._dataForm, this.getDataForm);
    }

    /**
     * Verifica a quantidade itens marcados, para declarações de resposta únicas.
     *
     * @param itemDeclaracao
     */
    public onChanteItemDeclaracao(itemDeclaracao: any): void {
        if (this.declaracaoParametrizada.declaracao.tipoResposta == Constants.TIPO_RESPOSTA_DECLARACAO_UNICA && !itemDeclaracao.valor) {
            this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(element => {
                element.valor = element.id == itemDeclaracao.id ? true : false;
            });
        }
    }

    /**
     * Inicializa declaração para aceitar convite para participação em chapa eleitoral.
     */
    public inicializarDeclaracao(): void {
        this.declaracaoParametrizada = this.chapaEleicaoClientService.getDeclaracaoParametrizada(this.convite.idAtividadeSecundariaConvite, Constants.TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA).subscribe(
            data => {
                this.declaracaoParametrizada = data;
                //this.declaracaoParametrizada.declaracao.itensDeclaracao = _.orderBy(data.declaracao.itensDeclaracao, ['sequencial'], ['desc']);
                this._dataForm = this.getDataForm();
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Confirmar declaração e aceitar convite.
     */
    public confimar(): void {
        if (this.isRespostaDeclaracaoValida()) {
            this.confirmarEvent.emit(this.getIdsItemDeclaracao());
        }
    }

    /**
     * Cancelar currículo membro chapa.
     */
    public cancelar(): void{
        this.modalRef.hide();
        this.cancelarEvent.emit();
    }

    /**
     * Exibe modal de confirmação de cancelamento.
     */
    public abrirModalConfirmarCancelamento(template: TemplateRef<any>): void {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
    }

    /**
     * Verifica se a resposta da declaração é valida.
     */
    private isRespostaDeclaracaoValida(): boolean{
        if(this.declaracaoParametrizada.declaracao.tipoResposta == Constants.TIPO_RESPOSTA_DECLARACAO_UNICA){
            return this.getIdsItemDeclaracao().length > 0;
        } else {
            return this.declaracaoParametrizada.declaracao.itensDeclaracao.length == this.getIdsItemDeclaracao().length;
        }
    }

    /**
     * Monta objeto com as informação necessária para aceitar o convite, que são currículo e declaração.
     */
    private getDataForm(): any {
        if (Constants.TIPO_CONSELHEIRO_IES == this.convite.tipoCandidatura) {
            return {
                declaracoes: this.getIdsItemDeclaracao(),
                idChapaEleicao: this.convite.idChapaEleicao,
                idMembroChapa: this.convite.idMembroChapa,
                sinteseCurriculo: this.curriculo.descricao,
                fotoSinteseCurriculo: this.curriculo.fotoCropped,
                cartasIndicacaoInstituicao: [this.curriculo.cartaIndicacao.file],
                comprovantesVinculoDocenteIes: [this.curriculo.comprovanteDocente.file]
            };
        }
        else {
            return {
                declaracoes: this.getIdsItemDeclaracao(),
                idChapaEleicao: this.convite.idChapaEleicao,
                idMembroChapa: this.convite.idMembroChapa,
                sinteseCurriculo: this.curriculo.descricao,
                fotoSinteseCurriculo: this.curriculo.fotoCropped
            };
        }

    }

    /**
     * Retorna lista de ids com todos os itens de declarações selecionados.
     */
    private getIdsItemDeclaracao(): Array<number> {
        let idsItemDeclaracao = [];
        this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(
            itemDeclaracao => {
                if (itemDeclaracao.valor) {
                    idsItemDeclaracao.push(itemDeclaracao.id);
                }
            }
        );
        return idsItemDeclaracao;
    }
}