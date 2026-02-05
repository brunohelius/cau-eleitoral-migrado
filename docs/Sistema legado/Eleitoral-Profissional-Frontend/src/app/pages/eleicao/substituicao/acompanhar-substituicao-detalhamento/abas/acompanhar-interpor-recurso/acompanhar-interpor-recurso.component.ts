import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, EventEmitter, Input, Output } from '@angular/core';
import { AcompanharRecursoSubstituicaoClientService } from 'src/app/client/acompanhar-recurso-substituicao-client/acompanhar-recurso-substituicao-client.service';


/**
 * Componente responsável pela apresentação do julgamento de substituição de comissão.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-recurso',
    templateUrl: './acompanhar-interpor-recurso.component.html',
    styleUrls: ['./acompanhar-interpor-recurso.component.scss']
})

export class AcompanharInterporRecursoComponent implements OnInit {
    
  @Input() dados;
  @Input() idAbaVoltar;
  @Input() dadosRecurso;
  @Input() tipoCandidatura;
  @Output() voltarAbaEvent: EventEmitter<any> = new EventEmitter();

  public isIES: boolean;
  public configuracaoCkeditor: any = {};
  public dadosServicoJulgamento: any = []
  public julgamentosSubstituicao: any = [];

  constructor(
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private acompanharRecursoSubstituicaoClientService: AcompanharRecursoSubstituicaoClientService
  ) {}

  ngOnInit() {
    this.setTitulo();
    this.inicializaConfiguracaoCkeditor();
    this.isIES = this.tipoCandidatura === Constants.IES;
  }

  /**
  * Define o título do módulo da página
  */
  private setTitulo() {
    this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-user',
        description: this.messageService.getDescription('TITLE_PEDIDO_DE_SUBSTITUICAO')
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   *
   * @param event
   * @param resolucao
   */
  public download(event: EventEmitter<any>, recurso): void {
    this.acompanharRecursoSubstituicaoClientService.getDocumentoRecurso(recurso.id).subscribe((data: Blob) => {
      event.emit(data);
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

  /**
   * Volta para a aba cujo valor foi informado no Input(): idAbaVoltar
   */
  public voltar(): void {
    this.voltarAbaEvent.emit(this.idAbaVoltar);
  }

  /**
   * Verifica se existe recurso.
   */
  public hasRecurso(): boolean {
    return this.dadosRecurso;
  }

}