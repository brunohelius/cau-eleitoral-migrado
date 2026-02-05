import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';

import { StringService } from 'src/app/string.service';
import { ImageCroppedEvent } from 'ngx-image-cropper';
import { CKEditor4 } from 'ckeditor4-angular';
import { ConviteChapaEleicaoClientService } from 'src/app/client/convite-chapa-eleicao-client/convite-chapa-eleicao-client.service';

import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { UtilsService } from 'src/app/utils.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'app-modal-alterar-dados-membro-chapa',
    templateUrl: './modal-alterar-dados-membro-chapa.component.html',
    styleUrls: ['./modal-alterar-dados-membro-chapa.component.scss']
})
export class ModalAlterarDadosMembroChapaComponent implements OnInit {

    @Input() dadosMembro: any;

    public modalRef: BsModalRef;
    public modalCurriculo: BsModalRef;

    public imageFile: any;
    public imageChangedEvent: any = '';
    public croppedImage: any = '';
    public croppingImage: any = '';

    public usuario: any;
    public curriculo: any;
    public _curriculo: any;

    public submitted = false;
    private mensagemExibida: boolean = false;

    public configuracaoCkeditor: any;

    @Output() fecharModal = new EventEmitter<any>();

    /**
     * Construtor da classe.
     */
    constructor(
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private conviteChapaEleicaoService: ConviteChapaEleicaoClientService,
    ) {
        this.usuario = this.securtyService.credential.user;
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarCurriculo();
        this.inicializarConfiguracaoCkeditor();
    }

    /**
     * Inicializar objeto curriculo.
     */
    public inicializarCurriculo(): void {
        this.curriculo = {
            descricaoSimpleText: StringService.getPlainText(this.dadosMembro.sinteseCurriculo).slice(0, -1),
            descricao: this.dadosMembro.sinteseCurriculo,
            foto: {
                nome: 'foto.png',
                file: undefined,
            },
            fotoCropped: UtilsService.base64toFile(this.dadosMembro.fotoMembroChapa, 'foto.jpg'),
            fileFotoMembro: undefined,
            nomeFotoMembro: undefined
        };

        this.croppedImage = this.dadosMembro.fotoMembroChapa;
        this._curriculo = _.cloneDeep(this.curriculo);
    }

    /**
     * Inicia a configuração do Ckeditor
     */
    private inicializarConfiguracaoCkeditor(): void {
        this.configuracaoCkeditor = UtilsService.getConfiguracaoPadraoCKeditor();
    }

    /**
     * Preenche texto de descrição do currículo em formato de texto simples.
     *
     * @param text
     */
    private setDescricaoCurriculoSimpleText(text: string): void {
        this.curriculo.descricaoSimpleText = StringService.getPlainText(text).slice(0, -1);
    }

    /**
     * @param event
     */
    public onRedyCKDescricao(event: CKEditor4.EventInfo) {
        event.editor.on('key', function (event2) {
            let maxl = Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA;
            let simplesTexto = StringService.getPlainText(event2.editor.getData());

            if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
                event2.cancel();
            }
        });

        event.editor.on('paste', function (event2) {
            let maxl = Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA;
            let simplesTexto = StringService.getPlainText(event2.editor.getData()) + event2.data.dataValue;
            if (!StringService.isLimitValid(simplesTexto, maxl)) {
                event2.cancel();
            }
        });
    }

    /**
     * Alterar valor da decrição do currículo.
     *
     * @param event
     */
    public onChangeCKDescricao(event: CKEditor4.EventInfo) {
        this.setDescricaoCurriculoSimpleText(StringService.getPlainText(event.editor.getData()));
    }

    /**
     * Retorna a contegem de caracteres restantes
     */
    public getContagemDescricaoCurriculo(): number {

        if (this.setDescricaoCurriculoSimpleText.length <= Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA) {

            let contadorCaracteres = Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA - (this.curriculo.descricaoSimpleText.length + 1);
            let mensagemDeveSerExibida = contadorCaracteres <= 0 && !this.mensagemExibida;

            if (mensagemDeveSerExibida) {
                this.mensagemExibida = true;
                this.messageService.addMsgDanger('maxlength');
            } else if (contadorCaracteres > 0) {
                this.mensagemExibida = false;
            }
                
            return contadorCaracteres;
        }

        return 0;
    }

    /**
     * Retorna base64 do recorte da foto.
     *
     * @param event
     */
    public imageCropped(event: ImageCroppedEvent) {
        this.croppingImage = event.base64;
    }

    /**
     * Monta objeto com as informação necessária para alterar dados currículo membro chapa.
     */
    private getDataForm(): any {
        return {
            idMembroChapa: this.dadosMembro.id,
            sinteseCurriculo: this.curriculo.descricao,
            fotoSinteseCurriculo: this.curriculo.fotoCropped
        };
    }

    /**
     * Verifica se houve alteração nos campos do formulário.
     */
    public isCamposAlterados(): boolean {
        return !deepEqual(this.curriculo, this._curriculo);
    }

    /**
     * Confirma recorte da imagem.
     */
    public confirmarCropp() {
        this.curriculo.fotoCropped = UtilsService.base64toFile(this.croppingImage, 'foto.jpg');
        this.croppedImage = this.croppingImage;
        this.modalRef.hide();
    }

    /**
     * Upload de foto currículo.
     *
     * @param arquivoEvent
     * @param template
     */
    public uploadFileFoto(arquivoEvent, template: TemplateRef<any>): void {
        if (arquivoEvent.size <= Constants.TAMANHO_LIMITE_ARQUIVO) {
            let arquivoTO = { file: arquivoEvent };
            this.conviteChapaEleicaoService.validarFotoSinteseCurriculo(arquivoTO).subscribe(
                data => {
                    this.imageFile = arquivoEvent;
                    this.curriculo.foto.file = arquivoEvent;
                    this.curriculo.foto.nome = arquivoEvent.name;
                    this.abrirModalCortarFoto(template);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
            this.messageService.addMsgDanger(this.messageService.getDescription('MSG_TAMANHO_MAXIMO_PERMITIDO_PARA_ARQUIVO', [2]));
        }
    }

    /**
     * Apresenta modal para cortar foto de currículo.
     *
     * @param template
     */
    public abrirModalCortarFoto(template: TemplateRef<any>): void {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg modal-dialog-centered' }));
    }

    /**
     * Cancela a alteração.
     */
    public confirmarCancelamento(): void {
        if (this.isCamposAlterados()) {
            const msgConfirmacaoCancelar = this.messageService.getDescription('MSG_CANCELAR_ALTERACAO');

            this.messageService.addConfirmYesNo(msgConfirmacaoCancelar, () => {
                this.fecharModal.emit();
            });
        } else {
            this.fecharModal.emit();
        }
    }

    /**
     * Confirma e chama o salvar
     * @param template 
     */
    public abrirModalConfirmarAlteracao(): void {
        const msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_ALTERACAO');

        this.messageService.addConfirmYesNo(msgConfirmacao, () => {
            this.salvarAlteracao();
        });
    }

    /**
     * Salva os dados
     */
    public salvarAlteracao(): void {
        this.conviteChapaEleicaoService.alterarDadosCurriculo(this.getDataForm()).subscribe(
            data => {
                this.messageService.addMsgSuccess('MSG_ALTERACAO_REALIZADA_COM_SUCESSO');
                this.fecharModal.emit();
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }
}