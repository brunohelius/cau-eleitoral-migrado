import { Component, OnInit, Input, EventEmitter, TemplateRef } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { MessageService } from '@cau/message';
import * as moment from 'moment';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import { Constants } from 'src/app/constants.service';


@Component({
  selector: 'app-modal-visualizar-historico-pedido-substituicao',
  templateUrl: './modal-visualizar-historico-pedido-substituicao.component.html',
  styleUrls: ['./modal-visualizar-historico-pedido-substituicao.component.scss']
})
export class ModalVisualizarHistoricoPedidoSubstituicaoComponent implements OnInit {

  @Input() public pedidoSubstituicao: any;
  @Input() public registroSeq: any;
  @Input() public chapa: any;

  public eIES: boolean;
  public pedido: any;
  public configuracaoCkeditor: any = {};
  public membroChapaSelecionado: any;
  public modalPendeciasMembro: BsModalRef | null;

  constructor(public modalRef: BsModalRef,
    private modalService: BsModalService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
    private messageService: MessageService,) {
  }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.inicializarPedidoSubstituicao();
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
    this.pedido = this.pedidoSubstituicao;

    let titulo: string = this.messageService.getDescription('LABEL_REGISTRO_PEDIDO_SUBSTITUICAO');

    if (this.isRecurso()) {
      titulo = this.isIES(this.chapa.tipoCandidatura.id)
        ? this.messageService.getDescription('LABEL_RECONSIDERACAO_PEDIDO_SUBSTITUICAO')
        : this.messageService.getDescription('LABEL_RECURSO_PEDIDO_SUBSTITUICAO');
    }

    this.pedido.titulo = titulo;
  }

  /**
  * Retorna o registro com a mascara
  */
  public getRegistroComMask(str: string): string {
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

  public downloadArquivo = (event: any) => {
    if (this.isRecurso()) {
      this.downloadArquivoRecurso(event, event.arquivo.id);
    } else {
      this.downloadArquivoSubstituicao(event, event.arquivo.id);
    }
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivoSubstituicao = (event: any, idArquivo) => {
    return this.julgamentoFinalClientService.getArquivoDefesaImpugnacao(idArquivo).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivoRecurso = (event: any, idArquivo) => {
    return this.julgamentoFinalClientService.getArquivoRecursoSegundoJulgamentoSubstituicao(idArquivo).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna se o registro é recurso
  */
  public isRecurso(): boolean {
    return this.registroSeq.toString().indexOf("-") != -1;
  }

  /**
   * Retorna o registro
   */
  public getRegistroSeq(): any {
    return (this.isRecurso ? this.registroSeq.toString().split("-", 1) : this.registroSeq);
  }

  /**
   * verifica se o parametro passado é de uma IES
   * caso seja retorna true;
   * @param id
   */
  public isIES(id: number): boolean {
    return (id === Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES);
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
   * Responsavel por inicalizar o IsIES.
   */
  public inicializaIES(): boolean {
    return this.eIES = this.chapa.tipoCandidatura.id == Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES;
  }

}
