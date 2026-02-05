import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { StringService } from 'src/app/string.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, ViewChild, TemplateRef, AbstractType, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { arrayMax } from 'highcharts';
import { UtilsService } from 'src/app/utils.service';
import { ActivatedRoute } from '@angular/router';


@Component({
  selector: 'modal-visualizar-recurso-impugnado',
  templateUrl: './modal-visualizar-recurso-impugnado.component.html',
  styleUrls: ['./modal-visualizar-recurso-impugnado.component.scss']
})
export class ModalVisualizarRecursoImpugnadoComponent implements OnInit {

  @Input() recursos: any;
  @Input() impugnacao: any;
  @Input() validacaoAlegacaoData: any;
  @Input() public cauUf: any;

  public modalRefCadastrarContrarrazao: BsModalRef | null;

  public arquivos = [];

  public configuracaoCkeditor: any;

  public modalVisualizar: BsModalRef;

  public tipoProfissional: any;


  @Output() public recarregarRecurso: EventEmitter<any> = new EventEmitter();


  @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao: any;
  @ViewChild('templateVisualizar', { static: true }) private templateVisualizarRecurso: any;
  @ViewChild('templateCadastroImpugnacaoResultadoContrarrazao', { static: true }) private templateCadastroImpugnacaoResultadoContrarrazao: any;

  /**
   * Construtor da classe.
   */
  constructor(
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private securityService: SecurityService,
    private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService,

  ) {
    this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional', this.route);
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.arquivos = this.inicializaArquivo();
  }

  /**
   * método responsável por inicializar os dados do arquivo
   * para download
   */
  public inicializaArquivo(): any {
    if (this.recursos.nomeArquivo) {
      return [{
        nome: this.recursos.nomeArquivo,
        nomeFisico: this.recursos.nomeArquivoFisico
      }];
    }
  }

  /**
   * Verifica se o julgamento é IES ou não.
   * @param id
   */
  public isIES(): boolean {
    let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
    return (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
  }

  /**
   * Exibe modal de visualizar.
   */
  public abrirModal(template: TemplateRef<any>): void {
    this.modalVisualizar = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
  }

  /**
   * Define o titulo a ser exibido na layer de Descrição.
   */
  public getTituloDescricao() {
    if (!this.isIES()) {
      return 'LABEL_DESCRICAO_RECURSO';
    } else {
      return 'LABEL_DESCRICAO_RECONSIDERACAO';
    }
  }

  /**
   * Define o titulo a ser exibido no titulo da modal.
   */
  public getTituloModal() {
    if (!this.isIES()) {
      return this.messageService.getDescription('TITLE_VISUALIZAR_RECURSO');
    } else {
      return this.messageService.getDescription('TITLE_VISUALIZAR_RECONSIDERACAO');
    }
  }

  /**
   * Define o titulo a ser exibido no titulo da modal.
   */
  public getSubTituloRecurso() {
    if (!this.isIES()) {
      return this.messageService.getDescription('LABEL_RECURSO_IMPUGNADO');
    } else {
      return this.messageService.getDescription('LABEL_RECONSIDERACAO_IMPUGNADO');
    }
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.impugnacaoResultadoClientService.getDocumentoRecursoJulgamento(this.recursos.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });

  }

  /**
   * Retorna o registro com a mascara.
   *
   * @param string
   */
  public getRegistroComMask(string): any {
    return StringService.maskRegistroProfissional(string);
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
   * Abri modal de cadastro de contrarrazão de I.R.
   * 
   * @param template 
   */
  public cadastrarContrarrazao(): void {
    if (this.modalVisualizar) {
      this.modalVisualizar.hide();
    }
    this.modalRefCadastrarContrarrazao = this.modalService.show(
      this.templateCadastroImpugnacaoResultadoContrarrazao,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
   * Verifica se botão de cadastro de contrarrazão de I.R deve ser mostrado.
   */
  public isMostrarCadastrarContrarrazao(): boolean {
    let isAtividadeContrarrazaoVigente: boolean = this.impugnacao.isIniciadoAtividadeContrarrazao && !this.impugnacao.isFinalizadoAtividadeContrarrazao;

    return (
      isAtividadeContrarrazaoVigente && 
      this.isImpugnante()&&
      this.tipoProfissional == Constants.TIPO_PROFISSIONAL
    );
  }

  /**
   * Verifica se existe contrarrazão cadastrada.
   */
  public hasContrarrazao(): boolean {
    return this.recursos.contrarrazoesRecursoImpugnacaoResultado != undefined && this.recursos.contrarrazoesRecursoImpugnacaoResultado.length > 0;
  }

  /**
   * Manipular ações de cancelamento de cadastro de contrarrazão.
   */
  public afterCancelarCadastroContrarrazao(): void {
    this.modalRefCadastrarContrarrazao.hide();
    //this.modalVisualizar = this.modalService.show(this.templateVisualizarRecurso, Object.assign({}, { class: 'modal-xl' }));
  }

  /**
   * Manipular ações de cadastro de contrarrazão.
   */
  public afterCadastrarContrarrazao(): void {
    this.modalRefCadastrarContrarrazao.hide();
    this.recarregarRecurso.emit();
  }

  /**
   * Verificar se o usuário logado é o impugnante.
   */
  private isImpugnante(): boolean {
    let usuarioLogado = this.securityService.credential.user;
    return this.impugnacao.profissional.id == usuarioLogado.idProfissional;
  }

  /**
   * retorna o hint da ação de cadastro de contrarrazão de acordo com
   * o tipo de candidatura do resultado e se possui contrarrazão cadastrada
   */
  public getHintBotaoCadastarContrarrazao(): string {

    let label = this.isIES()? 'reconsideração' : 'recurso';
    let msg = this.messageService.getDescription('TITLE_CADASTRAR_CONTRARRAZAO');
    
    if(this.hasContrarrazao()){
      msg = this.messageService.getDescription('MSG_LABEL_BTN_CADASTRAR_CONTRARRAZAO_IMPUGNACAO_RESULTADO_IMPUGNADO', [label]);
    }
    return msg;
  }

  /**
   * Verifica se a atividade de contrarrazão já foi finalizada
   */
  public isFinalizadoAtividadeContrarrazao(): boolean {
    return this.impugnacao.isFinalizadoAtividadeContrarrazao;
  }
}
