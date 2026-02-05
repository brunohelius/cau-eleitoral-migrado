import { ActivatedRoute, Router } from '@angular/router';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, ViewChild, Output, EventEmitter, AfterViewInit } from '@angular/core';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { UtilsService } from 'src/app/utils.service';


@Component({
  selector: 'aba-recurso-julgamento-impugnante',
  templateUrl: './aba-recurso-julgamento-impugnante.component.html',
  styleUrls: ['./aba-recurso-julgamento-impugnante.component.scss']
})
export class AbaRecursoJulgamentoImpugnanteComponent implements OnInit, AfterViewInit{

  public modalRef: BsModalRef | null;
  public modalRefCadastrarContrarrazao: BsModalRef | null;
  public modalRefVisualizarContrarrazao: BsModalRef | null;
  public configuracaoCkeditor: any;
  public arquivos = [];
  public contrarrazao: any;
  public tipoProfissional: any;

  @Input() bandeira: any;
  @Input() recurso: any;
  @Input() impugnacao: any;
  @Input() validacaoAlegacaoData: any;

  @Output() mudarAba: EventEmitter<any> = new EventEmitter();
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateCadastroImpugnacaoResultadoContrarrazao', { static: true }) private templateCadastroImpugnacaoResultadoContrarrazao: any;
  @ViewChild('templatevisualizarImpugnacaoResultadoContrarrazao', { static: true }) private templatevisualizarImpugnacaoResultadoContrarrazao: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private modalService: BsModalService,
    private messageService: MessageService,
    private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService
  ) {
    this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional', this.route);
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.arquivos = this.inicializaArquivo();
  }

  ngAfterViewInit(): void {
    
  }

  public inicializaArquivo(): any {
    if(this.recurso != undefined) {
      if (this.recurso.nomeArquivo) {
        return [{
          nome: this.recurso.nomeArquivo,
          nomeFisico: this.recurso.nomeArquivoFisico
        }];
      } else {
        return [];
      }
    }
  }

  /**
   * Verifica se o usuário logado é Acessor CEN ou CE.
   */
  public isAcessorCENouCE(idUf: number): boolean {
    let isAcessorCEN: boolean = this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    let isAcessorCE: boolean = this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE]) && idUf == this.securityService.credential.user.cauUf.id;
    let isIES: boolean = (idUf == 0);

    return (isIES) ? (isAcessorCEN) : (isAcessorCEN || isAcessorCE);
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
   * Volta para a página da uf da solicitação
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO);
  }

  /**
   * Recarrega aba de recurso.
   */
  public onRecarregarRecurso(): void {
    this.mudarAba.emit(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
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
   * Verifica se o julgamento é IES ou não.
   * @param id
   */
  public isIES(): boolean {
    let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
    return (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
  }

  /**
   * Verifica se botão de cadastro de contrarrazão de I.R deve ser mostrado.
   */
  public isMostrarCadastrarContrarrazao(): boolean {
    let isAtividadeContrarrazaoVigente: boolean = this.impugnacao.isIniciadoAtividadeContrarrazao && !this.impugnacao.isFinalizadoAtividadeContrarrazao;
    let isResponsavelChapaPrejudicada: boolean = this.validacaoAlegacaoData.isResponsavel;
    let isCadastradoChapaResponsavel: boolean = this.hasCadastroContrarrazaoChapa();

    return (
      isAtividadeContrarrazaoVigente && 
      isResponsavelChapaPrejudicada &&
      !isCadastradoChapaResponsavel  &&
      this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA
    );
  }

  /**
   * Manipular ações de cancelamento de cadastro de contrarrazão.
   */
  public afterCancelarCadastroContrarrazao(): void {
    this.modalRefCadastrarContrarrazao.hide();
  }

  /**
   * Manipular ações de cadastro de contrarrazão.
   */
  public afterCadastrarContrarrazao(): void {
    this.modalRefCadastrarContrarrazao.hide();
    this.mudarAba.emit(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
  }

  /**
   * Verificar se o usuário logado é o impugnante.
   */
  private isImpugnante(): boolean {
    let usuarioLogado = this.securityService.credential.user;
    return this.impugnacao.profissional.id == usuarioLogado.id;
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.impugnacaoResultadoClientService.getDocumentoRecursoJulgamento(this.recurso.id).subscribe((data: Blob) => {
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
   * Retorna o título do card principal da tela de acordo com o tipo de candidatura UF ou IES
   */
  public getTituloCard(): string {
    let tituloRecurso = this.messageService.getDescription('LABEL_RECURSO_IMPUGNANTE');
    let tituloReconsideracao = this.messageService.getDescription('LABEL_RECONSIDERACAO_IMPUGNANTE');
    return this.isIES() ? tituloReconsideracao :  tituloRecurso;
  }

  public hasContrarrazoes(): boolean {
    return (
      this.recurso.contrarrazoesRecursoImpugnacaoResultado != undefined
      && this.recurso.contrarrazoesRecursoImpugnacaoResultado.length > 0
    );
  }

  public abrirModal(contrarrazao: any):void {
    this.contrarrazao = contrarrazao;
    this.modalRefVisualizarContrarrazao = this.modalService.show(
      this.templatevisualizarImpugnacaoResultadoContrarrazao,
      Object.assign({ ignoreBackdropClick: false }, { class: 'modal-xl' })
    );
  }

  /**
  * Manipular ações de cancelamento de cadastro de contrarrazão.
  */
  public fecharModal(): void {
    this.modalRefVisualizarContrarrazao.hide();
  }

  /**
   * Abre modal de cadastro de contrarrazão de I.R.
   * 
   * @param template 
   */
  public cadastrarContrarrazao(): void {
    this.modalRefCadastrarContrarrazao = this.modalService.show(
      this.templateCadastroImpugnacaoResultadoContrarrazao,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
   * Verifica se existe algum recurso cadastrado de 
   * contrarrazão cadastrada pela chapa do responsável logado
   */
  public hasCadastroContrarrazaoChapa(): boolean {
    return this.recurso.hasCadastroChapaContrarrazao;
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
    return this.recurso && (this.isFinalizadoAtividadeContrarrazao() || this.hasContrarrazoes());
  }
}
