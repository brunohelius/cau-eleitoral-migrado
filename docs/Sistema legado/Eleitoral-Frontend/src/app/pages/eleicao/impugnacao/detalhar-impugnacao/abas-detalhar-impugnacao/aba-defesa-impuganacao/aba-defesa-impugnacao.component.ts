import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output } from '@angular/core';
import { MessageService } from '@cau/message';
import { NgForm } from '@angular/forms';
import { format } from 'url';
import { DefesaImpugnacaoService } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-client.service';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'aba-defesa-impugnacao',
    templateUrl: './aba-defesa-impugnacao.component.html',
    styleUrls: ['./aba-defesa-impugnacao.component.scss']
})
export class AbaDefesaImpugnacaoComponent implements OnInit {

    @Input() public defesa: any;

    @Input() public julgamento: any;

    @Input() public impugnacao: any;

    @Input() public atividadeSecundaria: any;

    @Output() salvarJulgamento: EventEmitter<any> = new EventEmitter<any>();

    private idPedido: number;

    public configuracaoCkeditor: any = {};

    public julgamentomodalRef: BsModalRef;

    public confirmarJulgamentomodalRef: BsModalRef;

    public submitted: boolean;

    public usuario: any;

    public idStatusJulgamentoImpugnacao: number;

    /**
     * Método contrutor da classe
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private defesaImpugnacaoService: DefesaImpugnacaoService,
        private securtyService: SecurityService,
        private modalService: BsModalService,
    ) {
        this.usuario = this.securtyService.credential["_user"];
        this.idPedido = route.snapshot.params.id;

    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarJulgamento();
        this.inicializaConfiguracaoCkeditor();
    }

    /**
     * Salvar Defesa de impugnação.
     *
     * @param form
     */
    public salvar(form: NgForm): void {
        if (form.valid) {
            this.defesaImpugnacaoService.salvar(this.defesa).subscribe(
                data => {
                    this.messageService.addMsgSuccess('');
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
     * Verifica se o botão de julgar impugnação deve ser mostrado.
     */
    public isMostrarJulgarImpugnacao(): boolean {
        let vigente: number = this.isVigente(this.atividadeSecundaria.dataInicio, this.atividadeSecundaria.dataFim);
        return !this.julgamento.id && (this.isUsuarioCE() || this.isUsuarioCEN()) && (vigente == 0 || this.defesa);
    }

    /**
     * Verifica se o usuário é Acessor CEN.
     */
    public isUsuarioCEN(): boolean {
        return this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    }

    /**
     * Verifica se o usuário é Acessor CE.
     */
    public isUsuarioCE(): boolean {
        return this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CE]) && this.usuario.cauUf.id == this.impugnacao.idCauUf;
    }

    /**
     * Exibir modal de cadastro de julgamento de impugnação.
     *
     * @param template
     */
    public julgarImpugnacao(template: TemplateRef<any>): void {

        this.resetJulgamento();

        let vigente: number = this.isVigente(this.atividadeSecundaria.dataInicio, this.atividadeSecundaria.dataFim);
        if (vigente == 0) {
            this.julgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-xl', focus: false }));
        } else {
            if (vigente > 0) {
                this.messageService.addConfirmYesNo("LABEL_MSG_CONFIRMAR_DATA_FIM_JULGAMENTO", () => {
                    this.julgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-xl', focus: false }));
                });
            } else {
                this.messageService.addConfirmYesNo("LABEL_MSG_CONFIRMAR_DATA_INICIO_JULGAMENTO", () => {
                    this.julgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-xl', focus: false }));
                });
            }
        }

    }

    /**
     * Verifica se a data está dentro do período de vigência.
     */
    public isVigente(dataInicio, dataFim): number {
        dataFim = new Date(dataFim);
        dataFim.setHours(23, 59, 59, 999);
        dataFim.setDate(dataFim.getDate() + 1);

        dataInicio = new Date(dataInicio);
        dataInicio.setHours(0, 0, 0, 0);
        dataInicio.setDate(dataInicio.getDate() + 1);

        let hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        if (hoje <= dataFim && hoje >= dataInicio) {
            return 0;
        }
        return hoje > dataFim ? 1 : -1;
    }

    /**
     * Abre a modal para cadastrar julgamento.
     *
     * @param template
     */
    public abrirModal(template: TemplateRef<any>) {
        this.julgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal  modal-lg' }));
    }

    /**
     * Salvar arquivo de Defesa de impugnação.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.defesa.arquivos = arquivos;
    }

    /**
     * Excluir arquivo de defesa de impugnação.
     *
     * @param arquivo
     */
    public excluirArquivo(arquivo): void {
        if (arquivo.id) {
            this.defesa.idArquivosRemover.push(arquivo.id);
        }
    }

    /**
   * Verifica se existe ao menos um arquivo submetido.
   */
    public hasArquivosJulgameto(): any {
        let isValido = false;

        if (this.julgamento.arquivos[0]) {
            isValido = true;
        }

        return isValido;
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     */
    public downloadArquivo(download: any): void {
        this.defesaImpugnacaoService.getArquivoDefesaImpugnacao(download.arquivo.id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Realiza download de arquivo para julgamento de impugnação.
     */
    public downloadArquivoJulgamentoImpugnacao(download: any): void {
        if (download.arquivo.id) {
            this.defesaImpugnacaoService.getArquivoJulgamentoImpugnacao(download.arquivo.id).subscribe(
                data => {
                    download.evento.emit(data);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
            download.evento.emit(download.arquivo);
        }
    }

    /**
     * Confirmar e salvar julgamento de impugnação.
     */
    public confirmarJulgamento(): void {
        this.defesaImpugnacaoService.salvarJulgamentoImpugnacao(this.julgamento).subscribe(
            data => {
                this.messageService.addMsgSuccess(data.statusJulgamentoImpugnacao.id == 1 ? 'MSG_JULGADO_PROCEDENTE' : 'MSG_JULGADO_IMPROCEDENTE');
                this.confirmarJulgamentomodalRef.hide();
                this.julgamentomodalRef.hide();

                this.julgamento = data;
                this.salvarJulgamento.emit(data);
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Cancelar julgamento de impugnação.
     */
    public cancelarJulgamento(): void {
        this.confirmarJulgamentomodalRef.hide();
        this.julgamentomodalRef.hide();
    }

    /**
     * Indeferir julgamento de impugnação.
     *
     * @param template
     */
    public indeferirJulgamento(template: TemplateRef<any>): void {
        this.idStatusJulgamentoImpugnacao = 2;
        this.julgamento.idStatusJulgamentoImpugnacao = this.idStatusJulgamentoImpugnacao;
        this.submitted = true;
        if (this.julgamento.descricao && this.hasArquivosJulgameto()) {
            this.confirmarJulgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal  modal-lg modal-dialog-centered' }));
        }
    }

    /**
     * Deferir julgamento de impugnação.
     *
     * @param template
     */
    public deferirJulgamento(template: TemplateRef<any>): void {
        this.idStatusJulgamentoImpugnacao = 1;
        this.julgamento.idStatusJulgamentoImpugnacao = this.idStatusJulgamentoImpugnacao;
        this.submitted = true;
        if (this.julgamento.descricao && this.hasArquivosJulgameto()) {
            this.confirmarJulgamentomodalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal  modal-lg modal-dialog-centered' }));
        }
    }

    /**
     * Inicializa a configuração do ckeditor.
     */
    private inicializaConfiguracaoCkeditor(): void {
        this.configuracaoCkeditor = {
            toolbar: [
                { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            ],
            title: 'Justificativa'
        };
    }

    /**
     * Inicializar julgamento de impugnação.
     */
    private inicializarJulgamento(): void {
        if (this.julgamento == undefined) {
            this.julgamento = {
                idPedidoImpugnacao: this.idPedido,
                idStatusJulgamentoImpugnacao: undefined,
                descricao: "",
                arquivos: []
            };
        }
    }

    /**
     * Reseta o  julgamento de impugnação.
     */
    private resetJulgamento(): void {
        this.submitted = false;
        this.julgamento = undefined;
        this.inicializarJulgamento();
    }
}

