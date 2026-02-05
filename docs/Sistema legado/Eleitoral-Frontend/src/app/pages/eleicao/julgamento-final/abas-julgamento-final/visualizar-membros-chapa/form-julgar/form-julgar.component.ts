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
  selector: 'form-julgar',
  templateUrl: './form-julgar.component.html',
  styleUrls: ['./form-julgar.component.scss']
})

export class FormJulgarComponent implements OnInit {

  public julgamento: any = {};
  public msgConfirmacao: string;
  public dadosFormulario: any = {}
  public configuracaoCkeditor: any;
  public submitted: boolean = false;
  public possuiRegistroLista = false;
  public membroChapaSelecionado: any;
  public membroSubstituidoTitular: any;
  public tituloModalConfirmacao: string
  public profissionaisComboBox: any = [];
  public possuiMembrosComPendencia = false;
  public msgConfirmacaoIndeferimento: string;
  public membroExclusaoAtual: any = undefined;
  public msgConfirmacaoExclusaoMembroPendencia: string;
  public msgConfirmacaoJulgarIndeferidoSemIndicacao: string;

  public _membrosComPendencia: any;
  public _membrosSemPendencia: any;

  public modalConfirmacao: BsModalRef;
  public modalConfirmacaoIndeferir: BsModalRef;
  public modalConfirmacaoIndeferirSemSubstituicao: BsModalRef;

  public _membrosPorSituacao: any;

  @Input() idChapaEleicao: number;
  @Input() membrosPorSituacao: any;
  @Input() idStatusJulgamento: number;
  @Input() isChapaIES: boolean = false;
  @Output() fecharModal: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
  @ViewChild('templateConfirmacaoIndeferir', { static: true }) templateConfirmacaoIndeferir: TemplateRef<any>;
  @ViewChild('templateConfirmacaoIndeferirSemSubstituicao', { static: true }) templateConfirmacaoIndeferirSemSubstituicao: TemplateRef<any>;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this._membrosPorSituacao = _.cloneDeep(this.membrosPorSituacao);
    this.julgamento = this.getEstruturaJulgamento();
    this._membrosComPendencia = _.cloneDeep(this._membrosPorSituacao.membrosComPendencia);
    this._membrosSemPendencia = _.cloneDeep(this._membrosPorSituacao.membrosSemPendencia);
    this.profissionaisComboBox = this._membrosPorSituacao.membrosSemPendencia
    this.verificaListaMembrosPendencia();
    this.getMsgConfirmarIndeferimentoSemIndicação();
    this.possuiMembrosComPendencia = this._membrosPorSituacao.membrosComPendencia.length > 0;
  }

  /**
   * verifica se a lista de membros com pendÊncia está com dados no array
   */
  public verificaListaMembrosPendencia(): void {
    this.possuiRegistroLista = this._membrosPorSituacao.membrosComPendencia.length > 0;
  }

  /**
   * Realiza a ação de salvar o julgamento de acordo com as validações definidas.
   * @param event
   */
  public salvar(): void {

    let isCamposObrigatoriosOK = false;

    this.submitted = true

    if (this.julgamento.arquivos.length > 0 && this.julgamento.descricao.length > 0) {

      isCamposObrigatoriosOK = true;

      if (this.isIndeferido()) {
        this.julgamento.indicacoes = this.preparaIndicacoes(this._membrosPorSituacao.membrosComPendencia);

        if (this.possuiMembrosComPendencia && !this.possuiRegistroLista) {
          this.abrirModalConfirmacaoIndeferirSemSubstituicao();
        } else {
          this.abrirModalConfirmacaoIndeferimento();
        }
      } else {
        this.abrirModalConfirmacao();
      }
    }
  }

  /**
   * Método responsável por persistir os dados da requisição
   */
  public persisteDados(templateRef: string): void {
    if (this.submitted) {

      this.julgamentoFinalClientService.salvarJulgamento(this.julgamento).subscribe(data => {
        this.verificacoesJulgamento(templateRef, true, data);
      }, error => {
        this.messageService.addMsgDanger(error.message);
        this.verificacoesJulgamento(templateRef, false, null);
      })
    }
  }

  /**
   * Método responsável por mostras as mensgens e fechar os popups de acordo com a ação
   * @param template
   * @param mensagem
   */
  public verificacoesJulgamento(template: string, success: boolean, response: any): void {
    if (template === 'templateConfirmacao' && success === true) {
      this.messageService.addMsgSuccess('MSG_CHAPA_DEFERIDA_COM_SUCESSO');
      this.redirecionaAbaVisualizarJulgamento(response);
      this.modalConfirmacao.hide();
      this.fecharModal.emit();

    } else if (template === 'templateConfirmacao' && success === false) {
      this.modalConfirmacao.hide();
      this.fecharModal.emit();
    }

    if (template === 'templateConfirmacaoIndeferirSemSubstituicao' && success === true) {
      this.messageService.addMsgSuccess('MSG_CHAPA_INDDEFERIDA_COM_SUCESSO');
      this.redirecionaAbaVisualizarJulgamento(response);
      this.modalConfirmacaoIndeferirSemSubstituicao.hide();
      this.fecharModal.emit();
    } else if (template === 'templateConfirmacaoIndeferirSemSubstituicao' && success === false) {
      this.modalConfirmacaoIndeferirSemSubstituicao.hide();
      this.fecharModal.emit();
    }

    if (template === 'templateConfirmacaoIndeferir' && success === true) {
      this.messageService.addMsgSuccess('MSG_CHAPA_INDDEFERIDA_COM_SUCESSO');
      this.redirecionaAbaVisualizarJulgamento(response);
      this.modalConfirmacaoIndeferir.hide();
      this.fecharModal.emit();
    } else if (template === 'templateConfirmacaoIndeferir' && success === false) {
      this.modalConfirmacaoIndeferir.hide();
      this.fecharModal.emit();
    }
  }

  /**
   * cancela o pedido de substituição de impugnação
   */
  public cancelaPedido(): void {
    this.membroExclusaoAtual = null;
    this.julgamento = {};
    this.submitted = false;
    this._membrosPorSituacao.membrosSemPendencia = this._membrosSemPendencia;
    this._membrosPorSituacao.membrosComPendencia = this._membrosComPendencia;
    this.fecharModal.emit();
  }

  /**
   * Exclui o membro da lista de membros com pendência
   */
  public confirmaExclusaoMembroPendencia(): void {

    this.profissionaisComboBox.push(this.membroExclusaoAtual.membro);
    this._membrosPorSituacao.membrosComPendencia.splice(this.membroExclusaoAtual.indice, 1);
    this.profissionaisComboBox = _.orderBy(this.profissionaisComboBox,
      ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);
    this.verificaListaMembrosPendencia();
    this.membroExclusaoAtual = undefined;
  }

  /**
   * Remove o arquivo anexado no objeto de julgamento
   */
  public excluirArquivo(): void {
    this.julgamento.arquivos = [];
  }

  /**
   * Adiciona a descrição do julgamento no objeto de julgamento
   * @param evento
   */
  public adicionarParecerJulgamento(evento: any): void {
    this.julgamento.descricao = evento
  }

  /**
   * Responsável por salvar os arquivos que foram submetidos no componete arquivo.
   * @param arquivos
   */
  public salvarArquivoJulgamento(arquivos): void {
    this.submitted = false;
    let arquivo = {
      nome: arquivos[0].nome,
      tamanho: arquivos[0].tamanho,
      arquivo: arquivos[0].arquivo,
    }
    this.julgamento.arquivos.push(arquivo)
  }

  /**
   * Realiza o download do arquivo anexado no formulário
   * @param download
   */
  public downloadArquivoJulgamento(download: any): void {
    download.evento.emit(download.arquivo);
  }

  /**
   * Verifica se existe ao menos um arquivo submetido.
   */
  public hasArquivos(): any {
    if (this.julgamento.arquivos) {
      return this.julgamento.arquivos.length > 0;
    } else {
      return false;
    }
  }

  /**
   * retorna a estrutura dos dados do objeto de jultamento
   */
  public getEstruturaJulgamento(): any {
    let julgamento = {
      idChapaEleicao: Number(this.idChapaEleicao),
      descricao: String(''),
      idStatusJulgamentoFinal: Number(this.idStatusJulgamento),
      arquivos: []
    }
    return julgamento;
  }

  /**
   * Verifica se a ação acionada foi para indeferir o julgamento
   */
  public isIndeferido(): boolean {
    return this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
  }

  /**
   * Método responsável por remover os membros da combobox e adicionar os membros na lista
   * de membros com pendência
   * @param evento
   */
  public adicionaMembroListaPendencia(evento: any): any {

    let membroSelecionado;
    const indice = evento.target.value;

    this.profissionaisComboBox.forEach((membro, i) => {
      if (i == indice) {
        membroSelecionado = membro;
      }
    });

    if (membroSelecionado) {
      this.profissionaisComboBox.splice(indice, 1);
      this._membrosPorSituacao.membrosComPendencia.push(membroSelecionado);
    } else if(indice == -2) {
      this._membrosPorSituacao.membrosComPendencia = _.concat(this.profissionaisComboBox, _.cloneDeep(this._membrosPorSituacao.membrosComPendencia));
      this.profissionaisComboBox = [];
    }
    this._membrosPorSituacao.membrosComPendencia = _.orderBy(this._membrosPorSituacao.membrosComPendencia,
      ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);

    this.verificaListaMembrosPendencia();
  }

  /**
   * Verifica se opção de selecionar todos os membros para indicação deve ser exibida.
   */
  public isMostrarOpcaoSelecionarTodosMembroListaPendencia(): boolean {
    return this.profissionaisComboBox.length > 0 && !this.isChapaIES;
  }

  /**
   * Método responsável por remover o membro da lista de pendências e adicionar
   * na combobox de membros
   * @param evento
   */
  public removeMembroListaPendencia(evento: any) {
    this.membroExclusaoAtual = evento;
    this.messageService.addConfirmYesNo('MSG_CONFIRMACAO_EXCLUSAO_MEMBRO_PENDENCIA', () => {
      this.confirmaExclusaoMembroPendencia();
    }, () => {
      this.membroExclusaoAtual = undefined;
    });
  }

  /**
   * Excluir todos os membros da lista de pendências e adicionar na combobox de membros
   * @param evento
   */
  public excluirTodos() {
    this.profissionaisComboBox = _.concat(this.profissionaisComboBox, _.cloneDeep(this._membrosPorSituacao.membrosComPendencia));
    this.profissionaisComboBox = _.orderBy(this.profissionaisComboBox,['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);
    this._membrosPorSituacao.membrosComPendencia = [];
    this.verificaListaMembrosPendencia();
  }

  /**
   * Retorna os membros indicados no formado da requisição
   * @param arrayIndicacoes
   */
  public preparaIndicacoes(arrayIndicacoes: any): any {

    let indicacoes = []
    arrayIndicacoes.forEach(data => {

      indicacoes.push({
        idMembroChapa: data.id ? data.id : '',
        numeroOrdem: data.numeroOrdem,
        idTipoParicipacaoChapa: data.tipoParticipacaoChapa.id
      })
    });
    return indicacoes;
  }

  /**
   * Exibe modal de confirmação.
   */
  public abrirModalConfirmacao(): void {

    this.tituloModalConfirmacao = this.messageService.getDescription('TITLE_CONFIRMACAO_DO_JULGAMENTO')
    this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMACAO_DEFERIMENTO_CHAPA_JULGAMENTO_FINAL')

    this.modalConfirmacao = this.modalService.show(this.templateConfirmacao, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  /**
   * Método responsável por fechar o formulário de julgamento
   */
  public fecharFormularios(): void {
    this.modalConfirmacao.hide();
  }

  /**
   * Exibe o modal de confirmação para julgar indeferimento mesmo sem membros indicados par substituição
   */
  public abrirModalConfirmacaoIndeferirSemSubstituicao(): void {

    this.modalConfirmacaoIndeferirSemSubstituicao = this.modalService.show(this.templateConfirmacaoIndeferirSemSubstituicao, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  public fecharConfirmacaoIndeferirSemSubstituicao(): void {
    this.modalConfirmacaoIndeferirSemSubstituicao.hide();
  }

  /**
   * Exibe o modal de confirmação para julgar indeferimento mesmo sem membros indicados par substituição
   */
  public abrirModalConfirmacaoIndeferimento(): void {

    this.msgConfirmacaoIndeferimento = this.messageService.getDescription('MSG_CONFIRMA_JULGAR_CHAPA_INDEFERIDA')

    this.modalConfirmacaoIndeferir = this.modalService.show(this.templateConfirmacaoIndeferir, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  public fecharConfirmacaoIndeferir(): void {
    this.modalConfirmacaoIndeferir.hide();
  }

  /**
   * Retorna conteúdo da msg que não possui inclusão de membro
   */
  public getLabelSemMembrosCadastrados(): string {
    return this.messageService.getDescription('LABEL_SEM_MEMBRO_INCLUSAO');
  }

  /**
   * Retorna conteúdo da msg que não possui inclusão de membro
   */
  public getMsgConfirmarIndeferimentoSemIndicação() {
    this.msgConfirmacaoJulgarIndeferidoSemIndicacao = this.messageService.getDescription('MSG_CONFIRMAR_JULGAMENTO_INDEFERIDO_SEM_POSSIBILIDADE_DE_SUBSTITUICAO');
  }

  /**
   * Retorna o título de Confirmação do julgamento
   */
  public getTituloConfirmacaoJulgamento(): string {
    return this.messageService.getDescription('TITLE_CONFIRMACAO_DO_JULGAMENTO');
  }

  /**
   * Retorna o título do formulário de acordo com a ação acionada
   */
  public getTituloFormulario(): string {

    let titulo = this.messageService.getDescription('LABEL_JULGAR_DEFERIDO');
    this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO
      ? titulo = this.messageService.getDescription('LABEL_JULGAR_INDEFERIDO')
      : titulo
    return titulo;
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
   * Método responsável por redirecionar para a aba de
   * acompanhar julgamento
   */
  public redirecionaAbaVisualizarJulgamento(event): void {
    this.redirecionarVisualizarJulgamento.emit(event);
  }
}
