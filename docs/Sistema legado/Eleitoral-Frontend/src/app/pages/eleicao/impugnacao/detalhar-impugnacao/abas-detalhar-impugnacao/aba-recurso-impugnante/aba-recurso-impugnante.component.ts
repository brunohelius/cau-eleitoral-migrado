import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, Output, EventEmitter} from '@angular/core';

import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';


@Component({
    selector: 'aba-recurso-impugnante',
    templateUrl: './aba-recurso-impugnante.component.html',
    styleUrls: ['./aba-recurso-impugnante.component.scss']
})
export class AbaRecursoImpugnanteComponent implements OnInit {

    @Input() isIes: any;
    @Input() impugnacao: any;
    @Input() isContrarrazao: any;
    @Input() recursoImpugnante: any;
    @Input() recursoResponsavel: any;
    @Input() configuracaoCkeditor: any;
    @Input() hasJulgamentoSegundaInstancia: any;

    public submitted: boolean;
    public hasRecurso: boolean;

    public configModal: any;
    public numeroProtocolo: any;
    public idStatusJulgamento: any;
    public valorStatusJulgamento: any;
    @Input() isFinalizadoContrarrazao: any;
    public isIniciadoJulgamentoRecurso: any;
    public isFinalizadoJulgamentoRecurso: any;
    
    public julgamento: any = {};
    
    public julgamentoModal: BsModalRef;
    public confirmacaoModal: BsModalRef;
    
    @Output() salvarJulgamentoSegundaInstancia: EventEmitter<any> = new EventEmitter<any>();

    /**
     * Método contrutor da classe
     */
    constructor(
        private modalService: BsModalService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private recursoService: ImpugnacaoCandidaturaClientService,
    ) {

    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.submitted = false;
        this.inicializaJulgamento();
        this.hasRecurso = this.recursoImpugnante != null;
        this.inicializaImpugnação();

        this.configModal = { 
            class: 'my-modal  modal-xl', 
            keyboard: false, 
            ignoreBackdropClick: true 
        }
    }

    /**
     * Responsavel por inicializar todos os dados que são utilizados nessa aba a partir da impugnação.
     */
    public inicializaImpugnação(): any {
        this.numeroProtocolo = this.impugnacao.numeroProtocolo;
        this.isIniciadoJulgamentoRecurso = this.impugnacao.isIniciadoAtividadeJulgamentoRecurso;
        this.isFinalizadoJulgamentoRecurso = this.impugnacao.isFinalizadoAtividadeJulgamentoRecurso;
    }

    /**
     * Responsavel por inicializar o objeto do julgamento.
     */
    public inicializaJulgamento(): any {
        this.submitted = false;
        this.julgamento = {
            descricao: '',
            idPedidoImpugnacao: this.impugnacao.id,
            idStatusJulgamentoImpugnacao: 0,
            arquivos: []
        }
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public labelDescricao(): any {
        return this.isIes ? 'TITLE_RECONSIDERACAO_JULGMANETO_IMPUGNANTE': 'TITLE_RECURSO_JULGMANETO_IMPUGNANTE';
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public title(): any {
        return this.messageService.getDescription(!this.impugnacao.isIES ? 'LABEL_RECURSO': 'LABEL_RECONSIDERACAO');
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public title2(): any {
        return this.messageService.getDescription(!this.impugnacao.isIES ? 'LABEL_DO_RECURSO': 'LABEL_DA_RECONSIDERACAO');
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     * 
     * @param download 
     */
    public downloadArquivoRecurso(download: any, id: number): void {
        this.recursoService.getArquivoRecurso(id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );       
    }

    /**
     * Realiza download de arquivo do Julgamento Segunda Instancia.
     */
    public downloadArquivoJulgamentoImpugnacao(download: any): void {
        download.evento.emit(download.arquivo);
    }
    

    /**
     * Função responsavel por validar o botão.
     */
    public validarBotao(): boolean {
        return (this.isCEN() && !this.hasJulgamentoSegundaInstancia && 
            ((this.recursoResponsavel != null && this.recursoImpugnante != null        ) ||
            (this.isIniciadoJulgamentoRecurso && this.recursoImpugnante != null        ) || 
            (this.isIniciadoJulgamentoRecurso && this.recursoResponsavel != null       ))
        );
    }

    /**
     * Responsavel por abrir o modal de julgamento.
     * 
     * @param template 
     */
    public modalJulgar(template): any {
        if (!this.isIniciadoJulgamentoRecurso) {
            this.messageService.addConfirmYesNo("LABEL_MSG_CONFIRMAR_DATA_INICIO_JULGAMENTO", () => {
                this.julgamentoModal = this.modalService.show(template, Object.assign({}, this.configModal));
            });
        } else if (this.isFinalizadoJulgamentoRecurso) {
            this.messageService.addConfirmYesNo("LABEL_MSG_CONFIRMAR_DATA_FIM_JULGAMENTO", () => {
                this.julgamentoModal = this.modalService.show(template, Object.assign({}, this.configModal));
            });
        } else {
            this.julgamentoModal = this.modalService.show(template, Object.assign({}, this.configModal));
        }
    }

    /**
     * Reponsavel por iniciar a validação para o salvamento.
     * 
     * @param idStatus 
     */
    public validarJulgamento(idStatus): any {
        this.submitted = true;
        this.idStatusJulgamento = idStatus;
        this.valorStatusJulgamento = this.messageService.getDescription((this.idStatusJulgamento == Constants.ID_TIPO_PROCEDENTE)?'LABEL_PROCEDENTE':'LABEL_IMPROCEDENTE');
    }  

    /**
     * Responsavel por abrir o molda de julgamento.
     * 
     * @param template 
     * @param idStatus 
     */
    public modalConfirmacao(template, idStatus): any {
        this.validarJulgamento(idStatus);
        if (this.submitted == true && this.hasArquivosJulgameto() && this.hasDescricao()){
            this.confirmacaoModal = this.modalService.show(template, Object.assign({}, { class: 'modal-lg modal-dialog-centered'}));
        }
    }

    /**
     * Responsavel por cancelar o salvamento do julgamento.
     */
    public cancelarJulgamento(): any {
        this.submitted = false;
        this.confirmacaoModal.hide();
        this.fecharJulgamento();
    }

    /**
     * Responsavel por confirmar o salvamento do julgamento.
     */
    public confirmarJulgamento(): any {
        this.julgamento.idStatusJulgamentoImpugnacao = this.idStatusJulgamento;
        this.recursoService.salvarJulgamentoSegundaInstancia(this.julgamento).subscribe(
            data => {
                this.messageService.addMsgSuccess(data.statusJulgamentoImpugnacao.id == 1 ? 'MSG_SUCESSO_JULGAMENTO_RECURSO_PROCEDENTE' : 'MSG_SUCESSO_JULGAMENTO_RECURSO_IMPROCEDENTE');
                this.confirmacaoModal.hide();
                this.fecharJulgamento();

                this.julgamento = data;
                this.hasJulgamentoSegundaInstancia = true;
                this.salvarJulgamentoSegundaInstancia.emit(data);
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }
    
    /**
     * Verifica se existe ao menos um arquivo submetido.
     */
    public hasArquivosJulgameto(): any {
        return this.julgamento.arquivos[0] != undefined;
    }
    
    /**
     * Verifica se a discricao foi preencida.
     */
    public hasDescricao(): any {
        return this.julgamento.descricao;
    }

    /**
     * Verifica se o usuario logado e Acessor CEN.
     */
    public isCEN(): any {
        return this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    }

    /**
     * Responsavel por fechar o modal de julgamento.
     */
    public fecharJulgamento(): any {
        this.inicializaJulgamento();
        this.julgamentoModal.hide();
    }

    /**
     * Realiza download de arquivo da Contrarrazao.
     */
    public downloadArquivoContrarrazao(download: any): void {
        this.recursoService.getArquivoContrarrazao(this.recursoResponsavel.contrarrazaoRecursoImpugnacao.id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );       
    }

    /**
    * Verifica se sub-aba de recurso de responsável deve ser exibida.
    */
    public isMostrarRecursoResponsavel(): boolean {
        return this.recursoResponsavel && this.isContrarrazao;
    }

    /**
     * Verifica se pode ou não mostrar a contrarrazão.
     */
    public isMostrarContrarrazao(): boolean {
        return (this.recursoResponsavel && this.recursoResponsavel.contrarrazaoRecursoImpugnacao != undefined) || this.isFinalizadoContrarrazao;
    }

    /**
     * Verifica se pode ou não mostrar a contrarrazão.
     */
    public isMostrarMsgContrarrazao(): boolean {
        return this.isFinalizadoContrarrazao && !((this.recursoResponsavel && this.recursoResponsavel.contrarrazaoRecursoImpugnacao != undefined));
    }
}
