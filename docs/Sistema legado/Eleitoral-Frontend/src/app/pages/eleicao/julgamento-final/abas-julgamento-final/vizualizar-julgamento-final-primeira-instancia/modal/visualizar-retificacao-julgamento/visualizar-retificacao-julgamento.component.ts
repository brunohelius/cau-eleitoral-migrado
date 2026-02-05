import * as _ from 'lodash';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, TemplateRef, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

/**
 * Componente responsável pela apresentação do detalhamento do pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'visualizar-retificacao-julgamento',
  templateUrl: './visualizar-retificacao-julgamento.component.html',
  styleUrls: ['./visualizar-retificacao-julgamento.component.scss']
})
export class VisualizarRetificacaoJulgamentooComponent implements OnInit {

  public julgamento: any = {};
  public configuracaoCkeditor: any = {};

  @Input() julgamentoFinal: any;
  @Input() chapa: any = [];
  @Input() sequencia: number;

  @Output() fecharModal: EventEmitter<any> = new EventEmitter();

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
  ) {

  }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    return this.julgamentoFinal.statusJulgamentoFinal.id === Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
  }

  /**
  * Verifica se o campo de justificativa de retificação deve ser exibido.
  */
  public isMostrarJustificativaRetificativa(): boolean {
    return this.julgamentoFinal.retificacaoJustificativa != undefined;
  }

  /**
   * Verifica se possui indicações de membros
   */
  public possuiIndicacao(): boolean {
    return (
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes
    );
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoFinal(this.julgamentoFinal.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
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

}