import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security'
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';

import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';
import { AcompanharRecursoSubstituicaoClientService } from 'src/app/client/acompanhar-recurso-substituicao-client/acompanhar-recurso-substituicao-client.service';


/**
 * Componente responsável pela apresentação do julgamento de substituição de comissão.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-acompanhar-recurso',
    templateUrl: './aba-acompanhar-interpor-recurso.component.html',
    styleUrls: ['./aba-acompanhar-interpor-recurso.component.scss']
})

export class AbaAcompanharInterporRecursoComponent implements OnInit {

  @Input() dados;
  @Input() nomeAbaVoltar;
  @Input() dadosRecurso;
  @Input() tipoCandidatura;
  @Output() voltarAbaEvent: EventEmitter<any> = new EventEmitter();
  @Output() salvarJulgamentoEvent: EventEmitter<any> = new EventEmitter();


  @Input() public julgamento: any;
  public msgConfirmacao: any;

  public modalRecurso: BsModalRef;
  public modalConfirmacao: BsModalRef;

  public isIES: boolean;
  public submitted: boolean;
  public deferimento: any;

  public configuracaoCkeditor: any = {};
  public dadosServicoJulgamento: any = [];
  public julgamentosSubstituicao: any = [];

  @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
  public tituloConfirmacaoJulgamento: string;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private substituicaoChapaService: SubstiuicaoChapaClientService,
    private acompanharRecursoSubstituicaoClientService: AcompanharRecursoSubstituicaoClientService
  ) {}

  ngOnInit() {
    this.setTitulo();
    this.inicializaConfiguracaoCkeditor();
    this.isIES = this.tipoCandidatura === Constants.IES;
    if(!this.julgamento) {
        this.inicializaJulgamento();
    }
  }

  /**
   * Responsavel por inicializar o objeto do julgamento.
   */
  public inicializaJulgamento(): any {
    this.submitted = false;
    this.julgamento = {
      descricao: '',
      idRecursoSubstituicao: 0,
      idStatusJulgamentoSubstituicao: 0,
    }
  }

  /**
   * Verifica se o botão de cadastro de julgamento deve ser mostrado.
   */
  public isMostrarJulgamento(): boolean {
      return this.julgamento.id == undefined && this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN);
  }

  /**
  * Define o título do módulo da página.
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
    this.voltarAbaEvent.emit(this.nomeAbaVoltar);
  }

  /**
   * Verifica se existe recurso.
   */
  public hasRecurso(): boolean {
    return this.dadosRecurso;
  }
  /**
   * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
   *
   * @param descricao
   */
  public adicionarDescricao(descricao): void {
    this.julgamento.descricao = descricao;
  }

  /**
   * Salvar arquivo de recurso de impugnação.
   *
   * @param arquivos
   */
  public salvarArquivos(arquivos): void {
    this.julgamento.arquivos = arquivos;
  }

  /**
   * Excluir arquivo de defesa de impugnação.
   *
   * @param arquivo
   */
  public excluirArquivo(arquivo): void {
    if(arquivo.id) {
      this.julgamento.idArquivosRemover.push(arquivo.id);
    }
  }

  /**
   * Realiza download de arquivo de defesa de impugnação.
   *
   * @param download
   */
  public downloadArquivoRecurso(download: any): void {
    download.evento.emit(download.arquivo);
  }

  /**
     * Verifica se existe ao menos um arquivo submetido.
     */
  public hasArquivos(): any {
    return this.julgamento.arquivos;
  }

  /**
     * Verifica se a discricao foi preencida.
     */
  public hasDescricao(): any {
    return this.julgamento.descricao;
  }

  /**
   * Resoponsavel por validar o julgamento.
   *
   * @param arquivos
   */
  public validarJulgamento(deferimento): void {
      this.submitted = true;
      this.deferimento = deferimento;

      if (this.hasArquivos() && this.hasDescricao()) {

          this.julgamento.idStatusJulgamentoSubstituicao = deferimento;
          this.julgamento.idRecursoSubstituicao = this.dadosRecurso.id;

          this.abrirModalConfirmacao();
      }
  }

  /**
   * Responsavel por salvar o julgamento da primeira instacia.
   */
  public salvarJulgamento(): any {
    this.substituicaoChapaService.salvarRecursoJulgamento(this.julgamento).subscribe(
        data => {
            if (this.julgamento.idStatusJulgamentoSubstituicao == Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO) {
                this.messageService.addMsgSuccess('MSG_RECURSO_PEDIDO_SUBST_DEFERIDO_COM_SUCESSO');
            } else {
                this.messageService.addMsgSuccess('MSG_RECURSO_PEDIDO_SUBST_INDEFERIDO_COM_SUCESSO');
            }
            this.modalConfirmacao.hide();
            
            this.julgamento = data;
            
            this.salvarJulgamentoEvent.emit(this.julgamento);
        },
        error => {
            this.messageService.addMsgDanger(error);
        }
    );
  }

  /**
   * Exibe modal de confirmação.
   */
  public abrirModalConfirmacao(): void {
    this.modalRecurso.hide();

    this.atribuirMsgConfirmacaoIndeferimento();
    this.atribuirTituloConfirmacaoIndeferimento();
    if (this.deferimento == Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO) {
        this.atribuirMsgConfirmacaoDeferimento();
        this.atribuirTituloConfirmacaoDeferimento();
    }

    this.modalConfirmacao = this.modalService.show(this.templateConfirmacao, {
        backdrop: true,
        ignoreBackdropClick: true,
        class: 'modal-lg modal-dialog-centered'
    });
  }

  /**
   * Atruibui o título confirmação deferimento com base no tipo de candidatura
   */
  private atribuirTituloConfirmacaoDeferimento(): void {
    let descricao = 'TITLE_CONFIRMAR_DEFERIMENTO_JULGAMENTO_RECURSO_SUBST';

    if (this.isIES) {
      descricao = 'TITLE_CONFIRMAR_DEFERIMENTO_JULGAMENTO_RECONSIDERACAO_SUBST';
    }

    this.tituloConfirmacaoJulgamento = this.messageService.getDescription(descricao);
  }

  /**
   * Atruibui o título confirmação indeferimento com base no tipo de candidatura
   */
  private atribuirTituloConfirmacaoIndeferimento(): void {
    let descricao = 'TITLE_CONFIRMAR_INDEFERIMENTO_JULGAMENTO_RECURSO_SUBST';

    if (this.isIES) {
      descricao = 'TITLE_CONFIRMAR_INDEFERIMENTO_JULGAMENTO_RECONSIDERACAO_SUBST';
    }

    this.tituloConfirmacaoJulgamento = this.messageService.getDescription(descricao);
  }

  /**
   * Atruibui a mensagem com base no tipo de candidatura
   */
  private atribuirMsgConfirmacaoDeferimento(): void {
    let descricao = 'MSG_PREZADO_DEFERIMENTO_JULGAMENTO_RECURSO_SUBST';

    if (this.isIES) {
      descricao = 'MSG_PREZADO_DEFERIMENTO_JULGAMENTO_RECONSIDERACAO_SUBST';
    }

    this.msgConfirmacao = this.messageService.getDescription(descricao) + this.dados.numeroProtocolo;
  }

  /**
   * Atruibui a mensagem com base no tipo de candidatura
   */
  private atribuirMsgConfirmacaoIndeferimento(): void {
    let descricao = 'MSG_PREZADO_INDEFERIMENTO_JULGAMENTO_RECURSO_SUBST';

    if (this.isIES) {
      descricao = 'MSG_PREZADO_INDEFERIMENTO_JULGAMENTO_RECONSIDERACAO_SUBST';
    }

    this.msgConfirmacao = this.messageService.getDescription(descricao) + this.dados.numeroProtocolo;
  }

  public voltarModalJulgamento(template: TemplateRef<any>) {
    this.modalConfirmacao.hide();
    this.modalRecurso = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
  }

  /**
   * Exibe modal de listagem de pendencias do profissional selecionado.
   *
   * @param template
   * @param element
   */
  public abrirModalJulgamento(template: TemplateRef<any>): void {

      this.substituicaoChapaService.getAtividadeJulgamentoRecursoSubstituicao(this.dados.id).subscribe(
          data => {
              let dataAtual = new Date();
              dataAtual.setHours(0, 0, 0, 0);

              let dataInicio = new Date(data.dataInicio);
              dataInicio.setDate(dataInicio.getDate() + 1);
              dataInicio.setHours(0, 0, 0, 0);

              let dataFim = new Date(data.dataFim);
              dataFim.setDate(dataFim.getDate() + 1);
              dataFim.setHours(0, 0, 0, 0);

              if (dataAtual < dataInicio) {
                  this.messageService.addConfirmYesNo('MSG_INICIO_JULGAMENTO_RECURSO_SUBSTITUICAO', () => {
                      this.showModalJulgamento(template);
                  });
              } else if (dataAtual > dataFim) {
                  this.messageService.addConfirmYesNo('MSG_FIM_JULGAMENTO_RECURSO_SUBSTITUICAO', () => {
                      this.showModalJulgamento(template);
                  });
              } else {
                  this.showModalJulgamento(template);
              }
          },
          error => {
              this.messageService.addMsgDanger(error);
          }
      );
  }

  /**
   * Exibe modal de cadastro de recurso/reconsideracao.
   *
   * @param template
   */
  public showModalJulgamento(template: TemplateRef<any>): void {
    this.submitted = false;
    this.inicializaJulgamento();
    this.modalRecurso = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
  }

  /**
   * Responsavel pelo titulo da descricao de acordo com isIes.
   */
  public tituloDescricao(): any {
    return (this.isIES) ? 'LABEL_RECONSIDERACAO_DO_JULGAMENTO' : 'LABEL_RECURSO_DO_JULGAMENTO';
  }
}