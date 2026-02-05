import { Component, OnInit, TemplateRef, Input, EventEmitter, Output } from '@angular/core';
import { CKEditor4 } from 'ckeditor4-angular/ckeditor';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ImageCroppedEvent } from 'ngx-image-cropper';
import { SecurityService } from '@cau/security';
import { ConviteChapaEleicaoClientService } from 'src/app/client/convite-chapa-eleicao-client/convite-chapa-eleicao-client.service';
import { NgForm } from '@angular/forms';
import { StringService } from 'src/app/string.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';

import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { UtilsService } from 'src/app/utils.service';

@Component({
    selector: 'app-form-curriculo-membro-chapa',
    templateUrl: './form-curriculo-membro-chapa.component.html',
    styleUrls: ['./form-curriculo-membro-chapa.component.scss']
})
export class FormCurriculoMembroChapa implements OnInit {

    @Input() public convite: any;
    @Output() public salvarEvent: EventEmitter<any> = new EventEmitter<any>();
    @Output() public voltarEvent: EventEmitter<any> = new EventEmitter<any>();
    
    private mensagemExibida: boolean = false;

    public configuracaoCkeditor: any;
    public usuario: any;
    public curriculo: any;
    public _curriculo: any;

    public modalRef: BsModalRef;

    public submitted: boolean;

    public imageFile: any;
    public imageChangedEvent: any = '';
    public croppedImage: any = '';
    public croppingImage: any = '';
    public isUploadFotoObrigatorio: boolean;

    /**
     * Método contrutor da classe
     *
     * @param route
     * @param router
     * @param messageService
     * @param modalService
     * @param securtyService
     * @param ConviteChapaEleicaoClientService
     * @param chapaEleicaoClientService
     */
    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private modalService: BsModalService,
        private securtyService: SecurityService,
        private conviteChapaEleicaoService: ConviteChapaEleicaoClientService,
        private chapaEleicaoClientService: ChapaEleicaoClientService
    ) {
        this.inicializarConfiguracaoCkeditor();
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.isUploadFotoObrigatorio = true;
        this.curriculo = {
            descricaoSimpleText: '',
            descricao: '',
            foto: {
                nome: undefined,
                file: undefined,
            },
            comprovanteDocente: {
                nome: undefined,
                file: undefined
            },
            cartaIndicacao: {
                nome: undefined,
                file: undefined,
            },
            fileFotoMembro: undefined,
            nomeFotoMembro: undefined,
            fileComprovanteDocente: undefined,
            convite: this.convite
        };
        this.usuario = this.securtyService.credential["_user"];
        this.submitted = false;
        this.inicialirCurriculo();
    }

    /**
     * Salva Currículo do membro.
     *
     * @param form
     */
    public salvar(form: NgForm): void {
        this.submitted = true;
        if (form.valid) {
            this.messageService.addMsgSuccess('MSG_DADOS_INCLUIDOS_COM_SUCESSO');
            this.salvarEvent.emit(this.curriculo);
        }
    }

    public onRedyCKDescricao(event: CKEditor4.EventInfo){
        event.editor.on('key', function(event2) {
            let maxl = Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA;
            let simplesTexto = StringService.getPlainText(event2.editor.getData());

            if( !StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
                event2.cancel();
            }
        });

        event.editor.on('paste', function(event2) {
            let maxl = Constants.TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA;
            let simplesTexto = StringService.getPlainText(event2.editor.getData()) + event2.data.dataValue;
            if(!StringService.isLimitValid(simplesTexto, maxl)) {
                event2.cancel();
            }
        });
    }

    public imageLoaded(){

    }

    public cropperReady(){

    }

    public loadImageFailed(){

    }

    /**
     * Alterar valor da decrição do currículo.
     *
     * @param event
     */
    public onChangeCKDescricao(event: CKEditor4.EventInfo){
        this.setDescricaoCurriculoSimpleText(StringService.getPlainText(event.editor.getData()));
    }
    
    /**
     * Método para validar e retornar a contagem de caracteres e exibir alerta caso o usuário tenha atingido o limite.
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
     * Preenche texto de descrição do currículo em formato de texto simples.
     *
     * @param text
     */
    private setDescricaoCurriculoSimpleText(text: string): void {
        this.curriculo.descricaoSimpleText = StringService.getPlainText(text).slice(0, -1);
    }

    /**
     * Voltar para a pagina de listagem de convites.
     */
    public voltar(): void {
        if(this.isCamposAlterados()) {
            this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR', () => {
                if (this.modalRef != undefined) {
                    this.modalRef.hide();
                }
                this.voltarEvent.emit(null);
            });
        } else {
            if (this.modalRef != undefined) {
                this.modalRef.hide();
            }
            this.voltarEvent.emit(null);
        }
    }

    /**
     * Verifica se houve alteração nos campos do formulário.
     */
    public isCamposAlterados(): boolean {
        return !deepEqual(this.curriculo, this._curriculo);
    }

    /**
     * Verifica se o convite é do tipo IES.
     *
     * @param convite
     */
    public isConviteTipoIES(convite: any): boolean {
        return Constants.TIPO_CONSELHEIRO_IES == convite.tipoCandidatura;
    }

    /**
     * Cancelar currículo membro chapa.
     */
    public abrirModalConfirmarCancelamento(template: TemplateRef<any>): void {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
    }

    /**
     * Cancela aceite da chapa.
     */
    public cancelar(): void {
        this.voltarEvent.emit();
        this.modalRef.hide();
    }

    /**
     * Apresenta modal para cortar foto de currículo.
     *
     * @param template
     */
    public abrirModalCortarFoto(template: TemplateRef<any>): void {
        this.modalRef = this.modalService.show(template, Object.assign({ ignoreBackdropClick: true }, { class: 'modal-lg' }));
    }

    public cancelarCortarFoto(): void {
        this.imageFile = undefined;
        this.curriculo.foto.file = undefined;
        this.curriculo.foto.nome = undefined;
        this.modalRef.hide();
    }

    private inicializarConfiguracaoCkeditor(): void {
        this.configuracaoCkeditor = UtilsService.getConfiguracaoPadraoCKeditor();
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
     * Upload de documentos comprobatórios.
     *
     * @param arquivoEvent
     * @param documento
     */
    public uploadDocumentoComprobatorio(arquivoEvent, documento) {
        if (arquivoEvent.size <= Constants.TAMANHO_LIMITE_ARQUIVO) {
            let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size }
            this.conviteChapaEleicaoService.validarPdf(arquivoTO).subscribe(
                data => {
                    documento.file = arquivoEvent;
                    documento.nome = arquivoEvent.name;
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
            this.messageService.addMsgDanger(this.messageService.getDescription('MSG_TAMANHO_MAXIMO_PERMITIDO_PARA_ARQUIVO', [10]));
        }
    }

    /**
     * Remover Comprovante de vinculo docente com a IES
     */
    public removerDocumentoComprobatorio(documento): void {
        documento.file = undefined;
        documento.nome = undefined;
    }

    /**
     * Valida Quantidade de Documentos Comprobátorios.
     *
     * @param documento
     */
    public validarDocumentoComprobatorio(documento): void {
        if (documento.nome != undefined) {
            this.messageService.addMsgDanger('MSG_QUANTIDADE_MAXIMA_ARQUIVOS_EXCEDIDO');
        }
    }

    /**
     * Inicializa currículo do membro da chapa.
     */
    public inicialirCurriculo(): void{
        this.chapaEleicaoClientService.getCurriculoMembroChapaPorMembroChapa(this.convite.idMembroChapa).subscribe(
            data => {
                this.croppedImage = data.fotoMembroChapa;
                this.curriculo.descricao = data.sinteseCurriculo;
                this.curriculo.fotoCropped = this.base64toFile(data.fotoMembroChapa);
                if(data.fotoMembroChapa) {
                    this.isUploadFotoObrigatorio = false;
                    this.curriculo.foto.nome = 'Foto base de dados do Carteiras';
                }
                this.setDescricaoCurriculoSimpleText(this.curriculo.descricao);
                this._curriculo = _.cloneDeep(this.curriculo);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Inicializa foto do currículo.
     */
    public inicializarImageFile(): void {

    }

    /**
     * Evento acionado ao alterar arquivo do componente de corta foto.
     *
     * @param event
     */
    public fileChangeEvent(event: any): void {
        this.imageChangedEvent = event;
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
     * Confirma recorte da imagem.
     */
    public confirmarCropp() {
        this.curriculo.fotoCropped = this.base64toFile(this.croppingImage);
        this.croppedImage = this.croppingImage;
        this.modalRef.hide();
    }

    /**
     * Converte string base64 para file objeto.
     *
     * @param dataURI
     */
    private  base64toFile(dataURI) {
        if(dataURI) {
            // convert base64/URLEncoded data component to raw binary data held in a string
            var byteString;
            if (dataURI.split(',')[0].indexOf('base64') >= 0)
                byteString = atob(dataURI.split(',')[1]);
            else
                byteString = unescape(dataURI.split(',')[1]);

            // separate out the mime component
            var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

            // write the bytes of the string to a typed array
            var ia = new Uint8Array(byteString.length);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }

            return new File([new Blob([ia], { type: mimeString })], "foto.jpg");
        }
    }

}
