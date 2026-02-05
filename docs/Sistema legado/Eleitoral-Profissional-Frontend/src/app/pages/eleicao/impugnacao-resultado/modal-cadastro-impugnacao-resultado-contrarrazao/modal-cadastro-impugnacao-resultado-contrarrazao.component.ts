import * as _ from 'lodash';
import { NgForm } from '@angular/forms';
import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { ContrarrazaoImpugnacaoResultadoClientService } from 'src/app/client/contrarrazao-impugnacao-resultado-client/contrarrazao-impugnacao-resultado-client.service';


@Component({
    selector: 'modal-cadastro-impugnacao-resultado-contrarrazao',
    templateUrl: './modal-cadastro-impugnacao-resultado-contrarrazao.component.html',
    styleUrls: ['./modal-cadastro-impugnacao-resultado-contrarrazao.component.scss']
})
export class ModalCadastroImpugnacaoResultadoContrarrazaoComponent implements OnInit {

    public contrarrazao: any;
    public submitted: boolean = false;
    public tipoValidacaoArquivo: number;

    @Input() public _contrarrazao: any;
    @Input() public cauUf: any;
    @Input() public impugnacaoResultado: any;
    @Input() public recursoImpugnacaoResultado: any;
    @Input() public recurso: any;
    @Input() public readOnly: boolean = false;
    @Input() public isImpugnante?: boolean = false;

    @Output() afterCadastrar: EventEmitter<any> = new EventEmitter();
    @Output() afterCancelar: EventEmitter<any> = new EventEmitter();

    constructor(
        private messageService: MessageService,
        private contrarrazaoService: ContrarrazaoImpugnacaoResultadoClientService
    ) { }

    ngOnInit() {
        this.inicializarContrarrazao();
        this.tipoValidacaoArquivo = Constants.ARQUIVO_TAMANHO_15_MEGA;
    }

    /**
     * Inicializa variável de contrarrazão.
     */
    public inicializarContrarrazao(): void {
        if(this._contrarrazao) {
            this.contrarrazao = {
                descricao: this._contrarrazao.descricao,
                arquivos: this._contrarrazao.arquivos,
                profissional: this._contrarrazao.profissional,
                dataCadastro: this._contrarrazao.dataCadastro
            };
        } else {
            this.contrarrazao = {
                descricao: '',
                arquivos: [],
                profissional: {},
                dataCadastro: undefined
            };
        }
    }

    /**
   * Adiciona a descrição do contrarrazao no objeto de contrarrazao
   * @param evento
   */
    public setDescricaoContrarrazao(evento: any): void {
        this.contrarrazao.descricao = evento
    }

    /**
     * retorna a configuração do ckeditor.
     */
    public getConfiguracaoCkeditor(): any {
        return {
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
     * Realiza o download do arquivo anexado no formulário
     * @param download
     */
    public downloadArquivoContrarrazao(download: any): void {
        if(this._contrarrazao) {
            this.contrarrazaoService.download(this._contrarrazao.id).subscribe((data: Blob) =>{
                download.evento.emit(data);
            }, error => {
                this.messageService.addMsgDanger(error);
            });
          } else {
            download.evento.emit(download.arquivo);
          }
    }

    public isSemArquivoVIsualizar(): boolean {
        return (
            this._contrarrazao && 
            (!this._contrarrazao.arquivos || this._contrarrazao.arquivos.length == 0)
        );
    }

    /**
     * Remove o arquivo anexado no objeto de contrarrazao
     */
    public excluirArquivo(): void {
        this.contrarrazao.arquivos = [];
    }

    /**
     * Verifica se existe ao menos um arquivo submetido.
     */
    public hasArquivos(): any {
        if (this.contrarrazao.arquivos) {
            return this.contrarrazao.arquivos.length > 0;
        } else {
            return false;
        }
    }

    /**
     * Salvar contrarrazao de Alegação I.R.
     *
     * @param form
     */
    public salvar(form: NgForm): void {
        this.submitted = true;
        if (form.valid) {
            this.contrarrazaoService.salvar(this.getDadosSalvar()).subscribe(
                data => {
                    this.messageService.addMsgSuccess(
                        'MSG_SUCESSO_SALVAR_CONTRARRAZAO_INPUGNACAO_RESULTADO',
                        [this.impugnacaoResultado.numero]
                    );
                    this.afterCadastrar.emit();
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
     * Retorna dados para salvar contrarrazao de Alegação I.R.
     */
    private getDadosSalvar(): any {
        return {
            idRecursoImpugnacaoResultado: this.recurso.id,
            descricao: this.contrarrazao.descricao,
            arquivos: this.contrarrazao.arquivos
        };
    }

    /**
     * Cancelar Cadastro de contrarrazao de Alegação I.R
     */
    public cancelar(form: NgForm): void {
        if(this.contrarrazao.descricao != '' || this.contrarrazao.arquivos.length > 0) {
            this.messageService.addConfirmYesNo('MSG_CONFIRMAR_CANCELAR', () => {
                this.limparTudo();
                this.afterCancelar.emit();
            });
        } else {
            this.limparTudo();
            this.afterCancelar.emit();
        } 
    }

    /**
     * Limpa todos as variáveis utilizadas pelo componente.
     */
    private limparTudo(): void {
        this.contrarrazao = null;
        this.submitted = false;
    }

    /**
     * Retorna o 'Número de impugnação de resultado' formatado.
     *
     * @param numero
     */
    public getNumeroFormatado(numero: number, x): string {
        return String(numero).padStart(x, '0');
    }

    /**
     * Retorna texto de hint apresentado na tela de cadastro de pedido de substituição de impugnação.
     */
    public getHintMensagem(): any {
        return ({
            msg: !this.readOnly ? this.messageService.getDescription('MSG_HINT_DOCUMENTO_JUGAMENTO_ALEGACAO_IMPUGNACAO_RESULTADO') : undefined,
            icon: !this.readOnly ? "fa fa-exclamation-circle fa-2x pointer" : undefined
        });
    }

    /**
   * Verifica se o julgamento é IES ou não.
   * @param id
   */
    public isIES(): boolean {
        let id = this.impugnacaoResultado.cauBR ? this.impugnacaoResultado.cauBR.id : undefined;
        return (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
    }
}