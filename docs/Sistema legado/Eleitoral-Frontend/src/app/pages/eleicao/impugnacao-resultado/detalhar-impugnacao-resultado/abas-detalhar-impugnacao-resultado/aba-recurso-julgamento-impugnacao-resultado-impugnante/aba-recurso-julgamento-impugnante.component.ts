import { LayoutsService } from '@cau/layout';
import { Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, Output, EventEmitter, ViewChild, TemplateRef } from '@angular/core';
import { MessageService } from '@cau/message';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import * as moment from 'moment';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';


@Component({
  selector: 'aba-recurso-julgamento-impugnante',
  templateUrl: './aba-recurso-julgamento-impugnante.component.html',
  styleUrls: ['./aba-recurso-julgamento-impugnante.component.scss']
})
export class AbaRecursoJulgamentoImpugnanteComponent implements OnInit {

  @Input() bandeira: any;
  @Input() recursos: any;
  @Input() impugnacao: any;
  @Input() julgamento?: any;

  public modalRef: BsModalRef | null;

  public recurso: any;
  public arquivos = [];

  public configuracaoCkeditor: any;

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() mudarAbaJulgRecursoAposCadastro: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateCadastroImpugnacaoResultadoJulgamentoSegundaInstancia', { static: true })
  private templateCadastroJulgamentoSegundaInstancia: TemplateRef<any>;

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private modalService: BsModalService,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private securityService: SecurityService,
    private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService
    ) {

  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.inicializaDados();
    this.inicializaConfiguracaoCkeditor();
    this.arquivos = this.inicializaArquivo();
  }

  /**
     * Inicializar julgamento de impugnação.
     */
    public inicializaArquivo(): any {
      if (this.recursos.length > 0) {
        if (this.recursos[0].arquivo) {
          return [{
            nome: this.recursos[0].arquivo[0].nome,
            nomeFisico: this.recursos[0].arquivo[0].nomeFisico
          }];
        }
      }
    }

  /**
   * Responsável por inicializar os dados do Recurso
   */
  public inicializaDados() {
    if (this.recursos && this.recursos.length > 0) {
      // Pega o último elemento
      const last = this.recursos.length - 1;

      const recurso = this.recursos[last];

      this.recurso = {
        id: recurso.id,
        numero: recurso.numero,
        descricao: recurso.descricao,
        dataCadastro: moment.utc(recurso.dataCadastro),
        contrarrazoes: (recurso.contrarrazoesRecursoImpugnacaoResultado ? recurso.contrarrazoesRecursoImpugnacaoResultado : [])
      };
    }
  }

  /**
   * Verifica se o julgamento é IES ou não.
   * @param id
   */
  public isIES(): boolean {
    let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
    return  (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
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
  public getTituloPagina() {
    if (!this.isIES()) {
      return this.messageService.getDescription('LABEL_RECURSO_IMPUGNANTE');
    } else {
      return this.messageService.getDescription('LABEL_RECONSIDERACAO_IMPUGNANTE');
    }
  }

  /**
   * Volta a página.
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO);
  }

  /**
   * Responsavel por voltar a tela pro inicio.
   */
  public inicio(): any {
    this.router.navigate(['/']);
    this.layoutsService.onLoadTitle.emit({
      description: ''
    });
  }

  /**
   * Verifica se o julgamento em primeira instância foi deferido ou indeferido
   */
  public isjulgamentoProcedente(): boolean {
    if (this.julgamento == undefined) {
      return false;
    } else {
      return this.julgamento.statusJulgamentoAlegacaoResultado.id == Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
    }
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.impugnacaoResultadoClientService.getDocumentoRecursoJulgamento(this.recurso.id).subscribe((data: Blob) =>{
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

  /**
   * Verifica se a atividade de contrarrazão já foi finalizada
   */
  public isFinalizadoAtividadeContrarrazao(): boolean {
    return this.impugnacao.isFinalizadoAtividadeContrarrazao;
  }

  /**
   * Verifica se pode mostrar a parte de contrarrazões
   */
  public isMostrarContrarrazoes(): boolean {
    return this.recurso && (this.isFinalizadoAtividadeContrarrazao() || this.recurso.contrarrazoes.length > 0);
  }

  public cadastrarJulgamentoSegundaInstancia(): void {
    this.modalRef = this.modalService.show(
      this.templateCadastroJulgamentoSegundaInstancia,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  public afterCadastrarJulgamento(evento: any): void {
    this.modalRef.hide();
    this.mudarAbaJulgRecursoAposCadastro.emit(evento);
  }

  /**
   * Retorna a label do botão de acordo com o cadastro do recurso
   */
  public getLabelBotaoCadastrarJulgamento(): string {
    if (!this.impugnacao.hasRecursoJulgamentoImpugnado && !this.impugnacao.hasRecursoJulgamentoImpugnante){
      return this.messageService.getDescription('LABEL_HOMOLOGAR_JULGAMENTO');
    } else {
      return this.messageService.getDescription('LABEL_JULGAMENTO_SEGUNDA_INSTANCIA');
    }
  }

  /**
   * Retorna se deve apresentar ou não o botão de cadastro de julgamento segunda instância
   */
  public isMostrarBotaoCadastrojugamentoSegundaInstancia(): boolean {
    if (!this.impugnacao.hasRecursoJulgamentoImpugnado && !this.impugnacao.hasRecursoJulgamentoImpugnante ) {
      return (
        this.impugnacao.isIniciadoAtividadeJulgamentoRecurso &&
        this.isjulgamentoProcedente() &&
        !this.isIES() &&
        this.impugnacao.hasJulgamento &&
        !this.impugnacao.hasJulgamentoRecurso &&
        this.isAcessorCEN()
      );
    } else {
      return (
        this.impugnacao.isIniciadoAtividadeJulgamentoRecurso &&
        this.impugnacao.hasJulgamento &&
        !this.impugnacao.hasJulgamentoRecurso &&
        this.isAcessorCEN()
      );
    }
  }

    /**
   * Verifica se o tipo de ação de cadastro de julgamento é
   * Julgamento segunda instancia ou homologação
   */
  public isHomologacao(): boolean {
    return this.recursos.length == 0 || this.recursos.length == undefined;
  }

  /**
   * verifica se o usuário logado tem permissão de Assessor CEN
   */
  public isAcessorCEN(): boolean {
    return  this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
  }
}
