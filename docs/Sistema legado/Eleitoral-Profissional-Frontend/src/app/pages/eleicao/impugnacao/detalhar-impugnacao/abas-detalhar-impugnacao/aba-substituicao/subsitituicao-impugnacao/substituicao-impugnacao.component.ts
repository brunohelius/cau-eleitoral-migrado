import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { ActivatedRoute, Router } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, TemplateRef, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { JulgamentoImpugnacaoService } from 'src/app/client/julgamento-impugnacao-client.service.ts/julgamento-impugnacao-client.service';

/**
 * Componente responsável pela apresentação do detalhamento do pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'solicitar-substituicao-impugnacao',
    templateUrl: './substituicao-impugnacao.component.html',
    styleUrls: ['./substituicao-impugnacao.component.scss']
})

export class SubstituicaoImpugnacaoComponent  implements OnInit{

    public profissional;
    public isProfissional;
    public impugnado: any = {};
    public membroSubstituto: any;
    public msgConfirmacao:string;
    public dadosFormulario: any = {}
    public membroChapaSelecionado: any;
    public modalConfirmacao: BsModalRef;
    public membroSubstituidoTitular: any;
    public tituloModalConfirmacao: string
    public modalPendeciasMembro: BsModalRef;
    public submitted: boolean = false;
    
    @Input() impugnacao: any;
    @Output() fecharModal: EventEmitter<any> = new EventEmitter();
    @Output() redirecionarVisualizarSubstituicao: EventEmitter<any> = new EventEmitter();
    @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
    @ViewChild('modalPendeciasMembro', { static: true }) templateSubstituicao: TemplateRef<any>;

    constructor(
        private modalService: BsModalService,
        private messageService: MessageService,
        private julgamentoImpugnacaoService: JulgamentoImpugnacaoService
    ){}


    ngOnInit() {
    }

    /**
     * Incluir membro chapa selecionado.
     *
     * @param event
     */
    public adicionarProfissional(profissional): void {

        this.submitted = false;
        let idProfissionalBusca = profissional.profissional.id;
        let parametro = {
            idProfissional: idProfissionalBusca,
            idPedidoImpugnacao: this.impugnacao.id
        }

        this.julgamentoImpugnacaoService.getCandidadoSubstituto(parametro).subscribe(
            data => {
                this.membroSubstituto = data
            },
            error => {
                this.messageService.addMsgWarning(error);
            }
        ); 
    }

    /**
     * Incluir membro chapa selecionado.
     *
     * @param event
     */
    public salvar(profissional): void {

        this.submitted = true;

        if(profissional){
           
            let idProfissionalSubstituto = profissional.profissional.id;
            let parametro = {
                idProfissional: idProfissionalSubstituto,
                idPedidoImpugnacao: this.impugnacao.id
            }
            
            this.submitted = false;

            this.julgamentoImpugnacaoService.salvarCandidadoSubstituto(parametro).subscribe(
                data => {
                    this.redirecionarVisualizarSubstituicao.emit(data);
                    this.abrirModalConfirmacao();
                },
                error => {
                    this.messageService.addMsgWarning(error);
                }
            )
        }
        
    }

    /**
     * Verifica o status de Validação do Membro.
     * 
     * @param membro 
     */
    public statusValidacao(membro): boolean {
        if (membro) {
            return membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
        } else {
            return false;
        }
    }

    /**
     * Exibe modal de listagem de pendencias do profissional selecionado.
     * 
     * @param template 
     * @param element 
     */
    public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
        this.membroChapaSelecionado = element;
        this.modalPendeciasMembro = this.modalService.show(template, Object.assign({}, { class: 'my-modal modal-dialog-centered' }));
    }

    /**
     * Retorna o registro com a mascara 
     * @param str 
     */
    public getRegistroComMask(str) {
        return StringService.maskRegistroProfissional(str);
    }

    /**
     * cancela o pedido de substituição de impugnação
     */
    public cancelaPedido(): void{
        this.membroSubstituto = {}
        this.profissional = null;
        this.fecharModal.emit();
    }

    /**
     * Exibe modal de confirmação.
     */
    public abrirModalConfirmacao(): void {
        
        this.tituloModalConfirmacao = this.messageService.getDescription('TITLE_CONFIRMAR_PEDIDO_DE_SUBSTITUICAO')
        this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMACAO_PEDIDO_SUBSTITUICAO_IMPUGNACAO') + this.impugnacao.numeroProtocolo 
        this.fecharModal.emit();
        
        this.modalConfirmacao = this.modalService.show(this.templateConfirmacao, {
            backdrop: true,
            ignoreBackdropClick: true,
            class: 'modal-lg modal-dialog-centered'
        });
    }

    /**
     * Emite o evento de redirecionamento de tela para visualizar substituição de impugnação
     */
    public redirecionaVisualizarSubstituicao(): void {
        this.modalConfirmacao.hide();
    }


    public getPlaceHolderRegistro(): string {
        return this.messageService.getDescription('MSG_INSIRA_NUMERO_DE_REGISTRO_OU_NOME_CANDIDATO');
    }

   /**
   * Retorna texto de hint apresentado na tela de cadastro de pedido de substituição de impugnação.
   */
    public getHintMensagem(): any {
        return ({
            msg: this.messageService.getDescription('MSG_HINT_MODAL_SUBSTITUICAO_IMPUGNACAO'),
            icon: "fa fa-info-circle fa-2x pointer"
        });
     }

}