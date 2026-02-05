import { map } from 'rxjs/operators';
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
  selector: 'form-alterar-julgamento',
  templateUrl: './form-alterar-julgamento.component.html',
  styleUrls: ['./form-alterar-julgamento.component.scss']
})
export class FormAlterarJulgamentoComponent implements OnInit {

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
  public _membrosPorSituacao;

  @Input() idChapaEleicao: number;
  @Input() membrosPorSituacao: any;
  @Input() membrosSelecionados: any;
  @Input() idStatusJulgamento: number;
  @Input() opcaoAlteracaoJulgamento: number;
  @Input() idJulgamentoFinalPai: number;
  @Input() julgamentoFinalPai: any;
  @Input() isChapaIES: boolean = false;
  @Output() fecharModal: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ) {

  }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.julgamento = this.getEstruturaJulgamento();
    this.inicializarMembros();
    this.getMsgConfirmarIndeferimentoSemIndicação();
  }

  public inicializarMembros() {
    let membrosComPendencia = [];
    let membrosSemPendencia = [];

    if (this.opcaoAlteracaoJulgamento == Constants.OPCAO_ALTERAR_CONTEUDO_JULGAMENTO) {

      const membrosComPendenciaAux = _.cloneDeep(this.membrosPorSituacao.membrosComPendencia);
      membrosComPendenciaAux.forEach((membro, index) => {
        let membroEncontrado = _.find(this.membrosSelecionados, membroSelecionados => {
            return membroSelecionados.numeroOrdem == membro.numeroOrdem
              && membroSelecionados.tipoParticipacaoChapa.id == membro.tipoParticipacaoChapa.id;
          }
        );

        if (membroEncontrado == undefined) {
          membrosSemPendencia.push(membro);
        } else {
          membrosComPendencia.push(membro);
        }
      });

    } else {
      membrosComPendencia = _.cloneDeep(this.membrosPorSituacao.membrosComPendencia);
      membrosSemPendencia = _.cloneDeep(this.membrosPorSituacao.membrosSemPendencia);
    }

    membrosComPendencia = _.orderBy(membrosComPendencia, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);
    membrosSemPendencia = _.orderBy(membrosSemPendencia, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);

    this._membrosComPendencia = _.cloneDeep(membrosComPendencia);
    this._membrosSemPendencia = _.cloneDeep(membrosSemPendencia);

    this._membrosPorSituacao = {
      membrosComPendencia: _.cloneDeep(membrosComPendencia),
      membrosSemPendencia: _.cloneDeep(membrosSemPendencia)
    };

    this.profissionaisComboBox = _.cloneDeep(membrosSemPendencia);
    this.verificaListaMembrosPendencia();
  }

  /**
   * verifica se a lista de membros com pendÊncia está com dados no array
   */
  public verificaListaMembrosPendencia(): void {
    this.possuiRegistroLista = this._membrosPorSituacao.membrosComPendencia.length > 0
  }

  /**
   * Realiza a ação de salvar o julgamento de acordo com as validações definidas.
   * @param event
   */
  public salvar(): void {
    let isCamposObrigatoriosOK = false;

    this.submitted = true

    if (this.julgamento.arquivos.length > 0 && this.julgamento.descricao.length > 0 && this.julgamento.retificacaoJustificativa.length > 0) {
      isCamposObrigatoriosOK = true;

      if(this.isIndeferido()) {
        this.julgamento.indicacoes =  this.preparaIndicacoes(this._membrosPorSituacao.membrosComPendencia);
      }
      this.abrirModalConfirmacao();
    }
  }

  /**
   * Método responsável por persistir os dados da requisição
   */
  public persisteDados(): void {
    if (this.submitted) {
      this.julgamentoFinalClientService.salvarJulgamento(this.julgamento).subscribe(data => {
        this.verificacoesJulgamento( true, data);
      }, error => {
        this.messageService.addMsgDanger(error.message);
        this.verificacoesJulgamento(false, null);
      })
    }
  }

  /**
   * Método responsável por mostras as mensgens e fechar os popups de acordo com a ação
   * @param template
   * @param mensagem
   */
  public verificacoesJulgamento(success: boolean, response: any): void {
    if (success) {
      this.messageService.addMsgSuccess(this.isIndeferido() ? 'MSG_CHAPA_INDDEFERIDA_COM_SUCESSO' : 'MSG_CHAPA_DEFERIDA_COM_SUCESSO');
      this.redirecionaAbaVisualizarJulgamento(response);
    }
    this.modalConfirmacao.hide();
    this.fecharModal.emit();
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
   * Adiciona a retificação do julgamento no objeto de julgamento
   * @param evento
   */
  public adicionarRetificacaoJustificativa(evento: any): void {
    this.julgamento.retificacaoJustificativa = evento
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
      descricao: this.julgamentoFinalPai.descricao,
      idStatusJulgamentoFinal: this.getIdStatusJulgamentoFinal(),
      arquivos: _.cloneDeep(this.julgamentoFinalPai.arquivos),
      retificacaoJustificativa: String(''),
      idJulgamentoFinalPai: this.idJulgamentoFinalPai
    }

    if (this.opcaoAlteracaoJulgamento == Constants.OPCAO_ALTERAR_DECISAO_JULGAMENTO) {
      julgamento.descricao = String('');
      julgamento.arquivos = [];
    }

    return julgamento;
  }

  /**
   * Verifica novo status de alteração de julgameto
   */
  private getIdStatusJulgamentoFinal(): number {
    if (
      this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO && Constants.OPCAO_ALTERAR_CONTEUDO_JULGAMENTO == this.opcaoAlteracaoJulgamento ||
      this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO && this.opcaoAlteracaoJulgamento == Constants.OPCAO_ALTERAR_DECISAO_JULGAMENTO
    ) {
      return Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
    }
    return Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO;
  }

  /**
   * Verifica se a ação acionada foi para indeferir o julgamento
   */
  public isIndeferido(): boolean {
    return (
      this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO && Constants.OPCAO_ALTERAR_CONTEUDO_JULGAMENTO == this.opcaoAlteracaoJulgamento ||
      this.idStatusJulgamento == Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO && this.opcaoAlteracaoJulgamento == Constants.OPCAO_ALTERAR_DECISAO_JULGAMENTO
    );
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
      this.confirmaExclusaoMembroPendencia()
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
    this.tituloModalConfirmacao = this.messageService.getDescription('LABEL_CONFIRMACAO_ALTERACAO_JULGAMENTO_1_INSTANCIA')
    this.msgConfirmacao = this.messageService.getDescription(this.getLabelConfirmacao());

    this.modalConfirmacao = this.modalService.show(this.templateConfirmacao, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  /**
   * Retorna mensagem de confirmação da modal de alteração de julgamento.
   */
  private getLabelConfirmacao(): string {
    return this.opcaoAlteracaoJulgamento == Constants.OPCAO_ALTERAR_DECISAO_JULGAMENTO ? 'LABEL_PREZADO_TEM_CERTEZA_DESEJA_ALTERAR_DECISAO_JULGAMENTO' : 'LABEL_PREZADO_TEM_CERTEZA_DESEJA_ALTERAR_CONTEUDO_JULGAMENTO';
  }

  /**
   * Método responsável por fechar o formulário de julgamento
   */
  public fecharFormularios(): void {
    this.modalConfirmacao.hide();
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
    this.msgConfirmacaoJulgarIndeferidoSemIndicacao = this.messageService.getDescription('LABEL_PREZADO_TEM_CERTEZA_DESEJA_ALTERAR_DECISAO_JULGAMENTO');
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
    return this.isIndeferido()
      ? this.messageService.getDescription('LABEL_JULGAR_INDEFERIDO')
      : this.messageService.getDescription('LABEL_JULGAR_DEFERIDO');
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
