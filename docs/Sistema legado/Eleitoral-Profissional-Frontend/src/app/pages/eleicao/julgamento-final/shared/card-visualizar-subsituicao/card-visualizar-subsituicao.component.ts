import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from '../../../../../client/julgamento-final/julgamento-final-client.service';
import { Constants } from '../../../../../constants.service';
import { StringService } from 'src/app/string.service';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output } from '@angular/core';
import * as moment from 'moment';


/**
 * Componente responsável pela apresentação do Pedido de substituição do julgamento final..
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'card-visualizar-subsituicao',
  templateUrl: './card-visualizar-subsituicao.component.html',
  styleUrls: ['./card-visualizar-subsituicao.component.scss']
})
export class CardVisualizarSubstituicaoComponent implements OnInit {

    @Input() substituicao: any;

    public membroChapaSelecionado: any;

    public modalPendeciasMembro: BsModalRef | null;

    /**
    * Construtor da classe.
    */
    constructor(
        private modalService: BsModalService,
        private messageService: MessageService,
        private julgamentoFinalClientService: JulgamentoFinalClientService,
    ) { }

    ngOnInit(): void {

    }

    /**
     * Exibe modal de listagem de pendencias do profissional selecionado.
     */
    public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
        this.membroChapaSelecionado = element;
        this.modalPendeciasMembro = this.modalService.show(template, {
        backdrop: true,
        ignoreBackdropClick: true,
        class: 'my-modal modal-dialog-centered'
        });
    }

    /**
    * Realiza download de arquivo.
    */
    public downloadArquivoDefesaImpugnacao(download: any): void {
        if (download.arquivo.id) {
        this.julgamentoFinalClientService.getArquivoDefesaImpugnacao(download.arquivo.id).subscribe(
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
    * Retorna o registro com a mascara
    */
    public getRegistroComMask(str: string) {
        return StringService.maskRegistroProfissional(str);
    }

    /**
     * Retorna a label de status de validação
     */
    public getLabelStatusValidacao(): any {
        return this.messageService.getDescription('LABEL_STATUS_VALIDACAO');
    }

    /**
     * Verifica o status de Validação do Membro.
     */
    public statusValidacao(membro: any): boolean {
        if (membro) {
          return membro.statusParticipacaoChapa.id === Constants.STATUS_SEM_PENDENCIA;
        } else {
          return false;
        }
      }
}