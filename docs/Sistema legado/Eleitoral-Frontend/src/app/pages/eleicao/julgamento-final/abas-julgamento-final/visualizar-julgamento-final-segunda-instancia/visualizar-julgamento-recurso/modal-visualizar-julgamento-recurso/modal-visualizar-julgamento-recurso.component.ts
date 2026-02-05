import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { MessageService } from '@cau/message';
import * as moment from 'moment';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import { Constants } from 'src/app/constants.service';


@Component({
  selector: 'modal-visualizar-julgamento-recurso',
  templateUrl: './modal-visualizar-julgamento-recurso.component.html',
  styleUrls: ['./modal-visualizar-julgamento-recurso.component.scss']
})

export class ModalVisualizarJulgamentoRecursoComponent implements OnInit {

  @Input() public dadosRecurso?: any;
  @Input() public chapa?: any;
  
  public arquivo: any;
  public configuracaoCkeditor: any = {};
  public modalPendeciasMembro: BsModalRef | null;
  public modalVisualizarJulgamentoSubstituicao: BsModalRef | null;

  constructor(
    public modalRef: BsModalRef,
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
  ){}

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.arquivo = [{
      id: this.dadosRecurso.id,
      nome: this.dadosRecurso.nomeArquivo
    }]
  }

  /**
   * Verifica se o tipo de candidatura da chapa é IES
   */
  public isIES(): boolean {
    return this.chapa.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * retorna a label do card dos membros com solicitação de recurso
   */
  public getLabelMembrosComRecurso(): string {
    return this.isIES() ? 'LABEL_MEMBRO_SOLICITACAO_RECONSIDERACAO' :'LABEL_MEMBRO_SOLICITACAO_RECURSO';
  }

  /**
   * retona o tipo de título se é reconsideração ou recurso
   */
  public getTitleDescription(): string {
    return this.isIES() ? 'LABEL_DESCRICAO_JULGAMENTO_RECONSIDERACAO' : 'LABEL_DESCRICAO_JULGAMENTO_RECURSO';
  }

  /**
   * retorna o título superior do Modal
   */
  public getTitleModal(): string {
    return this.isIES() ? 'TITLE_REGISTRO_DO_JULGAMENTO_DA_RECONSIDERACAO' : 'TITLE_REGISTRO_DO_JULGAMENTO_DO_RECURSO';
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): string {
   return this.isIES() ? 'TITLE_JULGAMENTO_RECONSIDERACAO_SEGUNDA_INSTANCIA' : 'TITLE_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA';
  }

  /**
   * Verifica se o julgamento do recurso final foi deferido ou indeferido
   */
  public isIndeferido(): boolean {
    return this.dadosRecurso.statusJulgamentoFinal.id == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
  }

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
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoRecursoSegundaInstancia(this.dadosRecurso.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
