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
    selector: 'visualizar-substituicao',
    templateUrl: './visualizar-substituicao.component.html',
    styleUrls: ['./visualizar-substituicao.component.scss']
})

export class VisualizarSubstituicaoComponent {

    @Input() dados: any = {}
    @Input() impugnacao: any = {}
    
    public membroChapaSelecionado: any;
    public modalPendeciasMembro: BsModalRef;

    @ViewChild('modalPendeciasMembro', { static: true }) templateSubstituicao: TemplateRef<any>;

    constructor(
        private modalService: BsModalService,
        private messageService: MessageService,
    ) { }

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
     * Retorna a classe de acordo com a situação do convite do membro da chapa substituto
     * @param id 
     */
    public getStatus(id:number) : string {
        var classe = "";

        if(id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO) {
            classe = "ribbon-primary"
        }

        if(id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO) {
            classe = "ribbon-danger"
        }

        if(id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO) {
            classe = "ribbon-warning "
        }

        return classe;
    }

    /**
     * Retorna o registro com a mascara 
     * @param str 
     */
    public getRegistroComMask(str) {
        return StringService.maskRegistroProfissional(str);
    }

    /**
    * retorna a label de status de validação com quebra de linha
     */
    public getLabelStatusValidacao(): any {
        return  this.messageService.getDescription('LABEL_STATUS_VALIDACAO_SUBSTITUICAO_IMPUGNACAO',['<br>']);
    }

    /**
    * retorna a label status confirmação com quebra de linha
    */
    public getLabelStatusConfirmacao(): any {
        return  this.messageService.getDescription('LABEL_STATUS_CONFIRMACAO_SUBSTITUICAO_IMPUGNACAO',['<br>']);
    }



}