import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';

import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { ModalVisualizarJulgamentoRecursoComponent } from './modal-visualizar-julgamento-recurso/modal-visualizar-julgamento-recurso.component';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'visualizar-julgamento-recurso-segunda-instancia',
    templateUrl: './visualizar-julgamento-recurso.component.html',
    styleUrls: ['./visualizar-julgamento-recurso.component.scss']
})
export class VisualizarJulgamentoRecursoSegundaInstanciaComponent implements OnInit {

  @Input() chapa: any;
  @Input() julgamentoFinal: any;
  @Input() membrosPorSituacao: any;
  @Input() recursoReconsideracao: any;
  @Input() recursoSegundaInstancia: any;

  public modalRef: BsModalRef;
  public modalRecurso: BsModalRef;

  public user: any;
  public abas: any;
  public recurso: any;
  public labelMembro: any;
  public titleSelecione: any;
  public titleDescricao: any;
  public titleTab: any;
  public titleDescription: any;
  public retificacoes: any;
  public retificacoesCarregadas = false;

  public isIes = false;
  public submitted = false;

  public _isAbaAtual: boolean = false;
  public _isMostraIconeTitulo: boolean = true;

  public arquivo: any = [];
  public membros: any = [];

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarAposSalvamento = new EventEmitter<any>();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  public modalVisualizarJulgamentoRecurso: BsModalRef | null;


  @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao;

  public configuracaoCkeditor: any = {};

  /**
   * Construtor da classe.
   */
  constructor(
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private securtyService: SecurityService,
    private julgamentoFinalService: JulgamentoFinalClientService
  ) {
    this.user = this.securtyService.credential.user;
  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {
    this.getTituloPagina();
    this.isIES();
    this.getTitleTab();
    this.getTitleDescription();
    this.labelMembroModal();
    this.inicializarRecurso();
    this.titleSelecioneModal();
    this.incializarMembrosChapa();
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * verifica se a chapa é do tipo IES
   */
  public isIES(): void {
    this.isIes = this.chapa.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Define o título do módulo da página
   */
  public getTituloPagina(): any {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-wpforms',
      description: this.messageService.getDescription('Julgamento')
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.julgamentoFinalService.getDocumentoJulgamentoRecursoSegundaInstancia(this.recursoSegundaInstancia.id).subscribe((data: Blob) => {
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
   * Verifica se o status do recurso de segunda instância é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    return this.recursoSegundaInstancia.statusJulgamentoFinal.id === Constants.TIPO_JULGAMENTO_INDEFERIDO;
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Responsavel por voltar a aba para a principal.
   */
  public voltarAbaPrincipal(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA);
  }

  public isMostrarCadastrarSubstituicao(): boolean {
    return (this.isJulgamentoIndeferido()
    );
  }

  /**
   * Verifica se o usuário e do tipo Acessor CEN/BR
   */
  public isAcessorCenBr(): boolean {
    return this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
  }
  /**
   * Redireciona para aba de visualizar julgmaneto após salvar o julgamento
   * @param event
   */
  public redirecionarAposSalvarJulgamento(event: any): void {
    event.reload = (recursoSegundaInstancia) => {
      this.recursoSegundaInstancia = recursoSegundaInstancia;
      this.inicializarRecurso();
      this.retificacoesCarregadas = false;

    };
    this.redirecionarVisualizarJulgamento.emit(event);
  }

  /**
   * Verifica se o botão de cadastro de recurso deve ser mostrado.
   */
  public isMostrarCadastroRecurso(): boolean {
    return (
      !(this.chapa.isCadastradoRecursoJulgamentoFinal ||
      this.chapa.isCadastradoSubstituicaoJulgamentoFinal) &&
      this.chapa.isIniciadoAtivRecursoJulgamentoFinal &&
      !this.chapa.isFinalizadoAtivRecursoJulgamentoFinal &&
      this.isJulgamentoIndeferido()
    );
  }

  /**
   * Verifica se possui indicação
   */
  public possuiIndicacao(): boolean {
    return (
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes
    );
  }

  /**
   * Inicializar recurso de julgamento de substituição.
   */
  public inicializarRecurso(): void {
    this.recurso = {
      descricao: '',
      arquivos: [],
      idJulgamentoFinal: this.julgamentoFinal.id,
      idProfissional: this.user.idProfissional,
      indicacoes: [],
      retificacao: this.recursoSegundaInstancia.retificacaoJustificativa ? this.recursoSegundaInstancia.retificacaoJustificativa : undefined,
      isAlterado: this.recursoSegundaInstancia.retificacaoJustificativa ? true : false,
    };

    if (this.recursoSegundaInstancia.nomeArquivo) {
      this.recurso.arquivos = [
        {
          id: this.recursoSegundaInstancia.id,
          nome: this.recursoSegundaInstancia.nomeArquivo
        }
      ]
    }

    this._isAbaAtual = this.recurso.isAlterado;
  }

  /**
   * Baixar download de recurso.
   *
   * @param download
   */
  public downloadModalRecurso(download): any {
    download.evento.emit(download.arquivo);
  }

  /**
   * Validação para apresentar o botão Solicitar Reconsideracao
   */
  public titleSelecioneModal(): any {
    this.titleSelecione =
    this.isIes ? 'TITLE_SELECIONE_CANDITADO_PARA_SOLICITACAO_RECONSIDERACAO' : 'TITLE_SELECIONE_CANDITADO_PARA_SOLICITACAO_RECURSO';
  }

  /**
   * Validação para apresentar o botão Solicitar Reconsideracao
   */
  public labelMembroModal(): any {
    this.labelMembro = this.isIes ? 'LABEL_MEMBRO_SOLICITACAO_RECONSIDERACAO' : 'LABEL_MEMBRO_SOLICITACAO_RECURSO';
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): any {
    this.titleTab = this.isIes ? 'TITLE_JULGAMENTO_RECONSIDERACAO_SEGUNDA_INSTANCIA' : 'TITLE_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA';
  }

  public getTitleDescription(): any {
    this.titleDescription = this.isIes ? 'LABEL_DESCRICAO_JULGAMENTO_RECONSIDERACAO' : 'LABEL_DESCRICAO_JULGAMENTO_RECURSO';
  }

  /**
   * Exibe modal de cadastro de recurso/reconsideracao.
   *
   * @param template
   */
  public abrirModalRecurso(template: TemplateRef<any>): void {
    this.cancelarRecurso();
    this.titleDescricao = 'LABEL_RECURSO_DO_JULGAMENTO';
    this.modalRecurso = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public titleModal() {
    return this.messageService.getDescription(this.isIes ? 'LABEL_RECONSIDERACAO' : 'LABEL_RECURSO');
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public titleModalconfirmacao() {
    return this.messageService.getDescription('LABEL_RECURSO_DO_JULGAMENTO_FINAL', [this.titleModal()]);
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public msgConfirmacao() {
    return this.messageService.getDescription('MGS_CONFIRMACAO_RECURSO_JULGAMENTO_FINAL', [this.titleModal()]);
  }

  /**
   * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
   *
   * @param descricao
   */
  public adicionarDescricao(descricao): void {
    this.recurso.descricao = descricao;
  }

  /**
   * Responsavel por incializar os membros chapas para caso n tenha profissional.
   */
  public incializarMembrosChapa(): any {
    const membro = {membro: {membroChapa: {}}};
  }

  /**
   * Responsavel por excluir o membro.
   */
  public excluirMembro(event): any {
    const membro = event.membro;
    this.messageService.addConfirmYesNo('MSG_CONFIRMA_EXCLUIR_REGISTRO', () => {
      if (!membro.membroChapa) {
        membro.membroChapa = {
          profissional: {
            nome: this.messageService.getDescription('LABEL_SEM_MEMBRO_INCLUSAO'),
            registroNacional: '-',
          }
        };
      }

      this.membros.push(membro);
      this.julgamentoFinal.indicacoes.splice(event.indice, 1);
      this.messageService.addMsgSuccess('MSG_EXCLUSAO_COM_SUCESSO');
    });
  }

  /**
   * Responsavel por selecionar o membro que foi escolhido no modal.
   */
  public getMembrosSelecionados(event): any {
    let indice;
    let membroSelecionado;
    const idMembro = event.target.value;

    if (idMembro != 0) {
      this.membros.forEach((membro, i) => {
        if (membro.id == idMembro) {
          membroSelecionado = membro;
          indice = i;
        }
      });

      this.membros.splice(indice, 1);
      this.julgamentoFinal.indicacoes.push(membroSelecionado);
    }
  }

  /**
   * Responsavel por apagar tudo que foi colocado no recurso quando sai do modal.
   */
  public cancelarRecurso(): any {
    this.inicializarRecurso();
    this.submitted = false;
    this.membros = [];
  }

  /**
   * Responavel por salvar o recurso.
   */
  /*public salvarRecurso(): any {
    this.submitted = true;

    if (this.hasProfissional()) {
      this.recurso.indicacoes = this.julgamentoFinal.indicacoes;
      this.julgamentoFinalService.salvarRecurso(this.recurso).subscribe(
        data => {
          this.recurso = data;
          this.modalRef = this.modalService.show(this.templateConfirmacao, Object.assign({}, { class: 'modal-lg' }));
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }*/

  /**
   * Verifica se existe membros selecionados.
   */
  public hasProfissional(): any {
    return this.julgamentoFinal.indicacoes[0];
  }

  /**
   * Resoponsavel por salvar o arquivo que foi submetido no componete arquivo.
   *
   * @param arquivos
   */
  public salvarArquivos(arquivos): void {
    this.recurso.arquivos = arquivos;
  }

  /**
   * Excluir arquivo de defesa de impugnação.
   *
   * @param arquivo
   */
  public excluirArquivo(arquivo): void {
    if (arquivo.id) {
      this.arquivo.idArquivosRemover.push(arquivo.id);
    }
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public redirecionaVisualizarPedido() {
    this.modalRecurso.hide();
    this.modalRef.hide();
    this.redirecionarAposSalvamento.emit(this.recurso);
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public fecharModalCadastrarSubstituicao() {
    this.redirecionarAposSalvamento.emit(this.recurso);
  }

  public abrirmodal(): void {
    this.modalRef = this.modalService.show(this.templateConfirmacao, Object.assign({}, { class: 'modal-lg' }));
  }

  /**
   * Responsavel por verificar se existe indicações no julgamento final
   */
  public haveIndicacoes(): any {
    return this.isJulgamentoIndeferido() && this.recursoSegundaInstancia.indicacoes[0];
  }


  public processaAbaRetificacao(evento): void {
    if(evento == 'LABEL_RETIFICACAO') {
      this.carregaDadosRetificacao()
      this._isAbaAtual = false;
      this._isMostraIconeTitulo = false
    } else {
      this._isMostraIconeTitulo = true;
      this._isAbaAtual = this.recurso.isAlterado;
    }
  }

    /**
   * Carrega os dados das retificações do jultamento
   */
  public carregaDadosRetificacao(): void {

    if(this.retificacoesCarregadas == false ) {
      this.julgamentoFinalService.getRetificacoesPorRecurso(this.recursoSegundaInstancia.recursoJulgamentoFinal.id,)
        .subscribe( (data)=> {
            this.retificacoes = data;
            this.retificacoesCarregadas = true;
          },error => this.messageService.addMsgDanger(error)
        );
    }
  }

  /**
   * Método responsável por carregar os dados do recurso
   * selecionado na lista de retificações
   * @param recurso
   */
  public carregarJulgamentoRecurso(recurso): void{
    this.abrirModalVisualizarPedidoSubstituicao(recurso);
  }


  /**
   * Abre o modal para visualizar o histórico.
   */
  public abrirModalVisualizarPedidoSubstituicao(recurso): void {
    const initialState = {
      dadosRecurso: recurso,
      chapa: this.chapa
    };

    this.modalVisualizarJulgamentoRecurso = this.modalService.show(ModalVisualizarJulgamentoRecursoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }
}
