import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { MessageService } from '@cau/message';
import * as moment from 'moment';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import { Constants } from 'src/app/constants.service';


@Component({
  selector: 'app-modal-visualizar-julgamento-substituicao',
  templateUrl: './modal-visualizar-julgamento-substituicao.component.html',
  styleUrls: ['./modal-visualizar-julgamento-substituicao.component.scss']
})
export class ModalVisualizarJulgamentoSubstituicaoComponent implements OnInit {

  @Input() public pedidoSubstituicao: any;

  public pedido: any;
  public titleTab: any;
  public titleModal: any;

  public membroChapaSelecionado: any;
  public modalPendeciasMembro: BsModalRef | null;

  constructor(public modalRef: BsModalRef,
    private modalService: BsModalService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
    private messageService: MessageService,) {
  }

  ngOnInit() {
    this.inicializarPedidoSubstituicao();
    this.getTitleTab();
    this.getTitleModal();
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): any {
    this.titleTab = (this.pedido.tipo === 'substituicao') ? 'LABEL_JULGAMENTO_PEDIDO_SUBSTITUICAO' : 'LABEL_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO';
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleModal(): any {
    this.titleModal = (this.pedido.tipo === 'substituicao') ? 'LABEL_REGISTRO_JULGAMENTO_SUBSTITUICAO' : 'LABEL_REGISTRO_JULGAMENTO_RECURSO_SUBSTITUICAO';
  }

  /**
   * Retorna a contagem de caracteres da justificativa.
   */
  public getContagemJustificativa = () => {
    return 2000 - this.pedido.justificativa.length;
  }

  /**
   * Inicia atributo pedido para preencher o formulário
   */
  public inicializarPedidoSubstituicao(): void {

    if (this.pedidoSubstituicao) {

      this.pedido = this.pedidoSubstituicao;
    }
  }

  public isRecurso(substituicao): any {
    return (substituicao.tipo === 'recurso') ? true : false;
  }

  /**
  * Retorna o registro com a mascara
  */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
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

  /**
   * Retorna a label de status de validação
   */
  public getLabelStatusValidacao(): any {
    return this.messageService.getDescription('LABEL_STATUS_VALIDACAO');
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

  public downloadArquivo(event: any): void {
    (this.isRecurso(this.pedido)) ? this.downloadArquivoRecursoSegundoJulgamentoSubstituicao(event) : this.downloadJulgamentoSubstituicaoSegundaInstancia(event);
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivoRecursoSegundoJulgamentoSubstituicao(event: any): void {
    this.julgamentoFinalClientService.getArquivoRecursoSegundoJulgamentoSubstituicao(this.pedido.registro).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadJulgamentoSubstituicaoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoSubstituicaoSegundaInstancia(this.pedido.registro).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }
}
