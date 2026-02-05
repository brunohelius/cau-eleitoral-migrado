import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output, ViewChild } from '@angular/core';
import * as _ from 'lodash';
import * as moment from 'moment';
import { ModalVisualizarJulgamentoSubstituicaoComponent } from './modal-visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao.component';
import { SecurityService } from '@cau/security';


/**
 * Componente responsável pela apresentação do Pedido de substituição do julgamento final..
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'visualizar-julgamento-substituicao-segunda-instancia',
  templateUrl: './visualizar-julgamento-substituicao.component.html',
  styleUrls: ['./visualizar-julgamento-substituicao.component.scss']
})
export class VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent implements OnInit {

  @Input() julgamentoFinal: any;
  @Input() public substituicaoJulgamento: any;
  @Input() public substituicaoSegundaInstancia: any;
  @Input() public hasSubstituicao: boolean;
  @Input() public chapa: any;
  @Input() recursoJulgamento: any;
  @Input() membrosPorSituacao: any;
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @Output() redirecionarAposSalvamento = new EventEmitter<any>();

  public substituicao: any;
  public pedidoSubstituicao: any;
  public membroChapaSelecionado: any;
  public idRecursoOuSubstituicao: any;
  public modalPendeciasMembro: BsModalRef | null;
  public modalVisualizarJulgamentoSubstituicao: BsModalRef | null;

  public titleTab: any;
  public configuracaoCkeditor: any = {};
  public retificacoes: any;
  public retificacoesCarregadas = false;
  public _isAbaRetificacao: boolean = false;
  public _isMostraIconeTitulo: boolean = true;

  /**
  * Construtor da classe.
  */
  constructor(
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
    private modalService: BsModalService,
    private securityService: SecurityService,
  ) { }

  ngOnInit(): void {
    this.initDados();
    this._isAbaRetificacao = this.substituicao.isAlterado;

  }

  /**
   * Método respnsável por atualizar os dados
   */
  initDados(): void  {
    this.inicializarSubstituicao();
    this.getTitleTab();
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): any {
      if(this.substituicao.tipo == 'substituicao') {
        this.titleTab = 'LABEL_JULGAMENTO_PEDIDO_SUBSTITUICAO';
      } else if(this.isChapaIES()) {
        this.titleTab = 'LABEL_JULGAMENTO_RECONSIDERACAO_PEDIDO_SUBSTITUICAO';
      } else {
        this.titleTab = 'LABEL_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO';
      }
  }

  public isRecurso(): any {
    return (this.substituicao.tipo == 'recurso') ? true : false;
  }

  public inicializarSubstituicao(): void {

    if (this.substituicaoSegundaInstancia) {

      // Pega o último elemento
      const last = this.substituicaoSegundaInstancia.length - 1;

      // Histórico de julgamento de substituições
      const historicoJulgamento = [...this.substituicaoSegundaInstancia];
      historicoJulgamento.splice(last, 1);

      const substuicao = this.substituicaoSegundaInstancia[last];

      if (substuicao.tipo == 'recurso') {
        this.idRecursoOuSubstituicao = substuicao.idRecursoSegundoJulgamentoSubstituicao;
      } else {
        this.idRecursoOuSubstituicao = substuicao.substituicaoJulgamentoFinal.id;
      }

      const membrosSubstitutosDaChapa = [];
      substuicao.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal.forEach(function(membro){
        let membroSubstituto;
        membroSubstituto = { substituido : membro.indicacaoJulgamentoFinal.membroChapa, substituto : membro.membroChapa}
        membrosSubstitutosDaChapa.push(membroSubstituto);
      });

      this.substituicao = {
        idSubstituicaoFinal: substuicao.substituicaoJulgamentoFinal.id,
        tipo: substuicao.tipo,
        id: substuicao.id,
        statusJulgamentoFinal: substuicao.statusJulgamentoFinal,
        registro: substuicao.sequencia,
        descricao: substuicao.descricao,
        arquivos: [{ id: substuicao.id, nome: substuicao.nomeArquivo }],
        membrosSubstitutosDaChapa,
        substituicaoJulgamentoFinal: substuicao.substituicaoJulgamentoFinal,
        indicacoes: substuicao.indicacoes,
        historico: historicoJulgamento,
        dataCadastro: moment.utc(substuicao.dataCadastro),
        retificacao: substuicao.retificacaoJustificativa ? substuicao.retificacaoJustificativa : undefined,
        isAlterado: substuicao.retificacaoJustificativa ? true : false,
      };
    }
  }

  public getTipoJulgamentoAlteracao(): string {
    return this.isRecurso() ? 'RECURSO_SUBSTITUICAO' : 'SUBSTITUICAO';
  }

  /**
   * Redireciona para aba de visualizar julgmaneto após salvar o julgamento
   * @param event
   */
  public redirecionarAposSalvarJulgamento(event): void {

    event.reload = (substituicaoSegundaInstancia) => {
      this.substituicaoSegundaInstancia = substituicaoSegundaInstancia;
      this.initDados();
      this.retificacoesCarregadas = false;
      this._isAbaRetificacao = true;
      this._isMostraIconeTitulo= true;
    };
    this.redirecionarVisualizarJulgamento.emit(event);
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    return this.substituicao.statusJulgamentoFinal.id === Constants.TIPO_JULGAMENTO_INDEFERIDO;
  }

  /**
   * Verifica se o usuário e do tipo Acessor CEN/BR
   */
  public isAcessorCenBr(): boolean {
    return this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
  }

  public downloadArquivo(event: any): void {
    (this.isRecurso()) ? this.downloadDocumentoJulgamentoRecursoSegundaInstancia(event) : this.downloadJulgamentoSubstituicaoSegundaInstancia(event);
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadDocumentoJulgamentoRecursoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoRecursoSegundaInstanciaSubstituicao(this.substituicao.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadJulgamentoSubstituicaoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoSubstituicaoSegundaInstancia(this.substituicao.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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
    return  membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
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
   * Responsavel por voltar a aba para a principal.
   */
  public voltarAbaPrincipal(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
  }

  /**
   * Responsavel por carregar os dados do visualizar histórico
   */
  public carregarPedidoSubstituicao = (registro: any, isAbaRetificacao?: boolean) => {

    let membrosSubstitutosDaChapa = [];
    registro.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal.forEach((membro) => {
        membrosSubstitutosDaChapa.push({
          substituido: membro.indicacaoJulgamentoFinal.membroChapa,
          substituto: membro.membroChapa
        });
    });

    const substituicao = {
      idSubstituicaoFinal: registro.substituicaoJulgamentoFinal.id,
      tipo: registro.tipo,
      id: registro.id,
      registro: registro.sequencia,
      descricao: registro.descricao,
      julgamentoIndeferido: registro.statusJulgamentoFinal.id === Constants.TIPO_JULGAMENTO_INDEFERIDO,
      arquivos: [{ id: registro.id, nome: registro.nomeArquivo }],
      membrosSubstitutosDaChapa,
      indicacoes: registro.indicacoes,
      chapaUf: this.chapa.uf,
      numeroChapa: this.chapa.numeroChapa,
      chapaEleicao: this.chapa,
      dataCadastro: moment.utc(registro.dataCadastro),
      retificacao: registro.retificacaoJustificativa ? registro.retificacaoJustificativa : undefined,
      isAlterado: registro.retificacaoJustificativa && !isAbaRetificacao ? true : false
    };

    this.abrirModalVisualizarPedidoSubstituicao(substituicao);
  }

  /**
   * Abre o formulário de visualizar histórico.
   */
  public abrirModalVisualizarPedidoSubstituicao(substituicao): void {
    const initialState = {
      pedidoSubstituicao: substituicao,
    };

    this.modalVisualizarJulgamentoSubstituicao = this.modalService.show(ModalVisualizarJulgamentoSubstituicaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public fecharModalCadastrarSubstituicao() {
    this.redirecionarAposSalvamento.emit(this.substituicao);
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


  public carregarPedidohistorico(id: number): any {
    // TODO - AJUSTAR
  }

  /**
   * Responsavel por retornar o status do julgamento.
   */
  public getStatusJulgamento(): any {
    return this.isJulgamentoIndeferido() ? Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO : Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO;
  }

  public processaAbaRetificacao(evento): void {
    if(evento == 'LABEL_RETIFICACAO') {
      this.carregaDadosRetificacao()
      this._isAbaRetificacao = false;
      this._isMostraIconeTitulo = false
    } else {
      this._isMostraIconeTitulo = true;
      this._isAbaRetificacao = this.substituicao.isAlterado;
    }
  }

  /**
   * Carrega os dados das retificações do jultamento
   */
  public carregaDadosRetificacao(): void {
    if(this.retificacoesCarregadas == false ) {

      if (this.isRecurso()) {
        this.carregarDadosRetificacaoRecurso();
      } else {
        this.carregarDadosRetificacaoSubstituicao();
      }
    }
  }

  /**
   * Carrega os dados das retificações do jultamento da substituição
   */
  private carregarDadosRetificacaoSubstituicao(): void {
    this.julgamentoFinalClientService.getRetificacoesPorSubstituicao(this.substituicao.idSubstituicaoFinal).subscribe(
      (data) => {
        this.retificacoes = data;
        this.retificacoesCarregadas = true;
      }, error => this.messageService.addMsgDanger(error)
    );
  }

  /**
   * Carrega os dados das retificações do jultamento do recurso do pedido de substituição
   */
  private carregarDadosRetificacaoRecurso(): void {
    this.julgamentoFinalClientService.getRetificacoesPorRecursoPedidoSubstituicao(this.idRecursoOuSubstituicao).subscribe(
      (data) => {
        this.retificacoes = data;
        this.retificacoesCarregadas = true;
      }, error => this.messageService.addMsgDanger(error)
    );
  }
}
