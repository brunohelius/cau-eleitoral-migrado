import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, TemplateRef, ViewChild, Input, EventEmitter, Output } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import * as _ from 'lodash';

/**
 * Componente responsável pelo Deferimento do Julgamento Final 2ª Instância.
 *
 * author Squadra Tecnologia.
 */
@Component({
  selector: 'app-form-julgar-segunda-instancia',
  templateUrl: './form-julgar.component.html',
  styleUrls: ['./form-julgar.component.scss']
})

export class FormJulgarSegundaInstanciaComponent implements OnInit {

  public julgamento: any = {};
  public julgamentoSalvo: any;
  public msgConfirmacao: string;
  public dadosFormulario: any = {};
  public configuracaoCkeditor: any;
  public submitted = false;
  public possuiRegistroLista = false;
  public membroChapaSelecionado: any;
  public membroSubstituidoTitular: any;
  public tituloModalConfirmacao: string;
  public profissionaisComboBox: any = [];
  public possuiMembrosComPendencia = false;
  public msgConfirmacaoIndeferimento: string;
  public membroExclusaoAtual: any = undefined;
  public tituloModalExclusaoMembroPendencia: string;
  public msgConfirmacaoExclusaoMembroPendencia: string;
  public msgConfirmacaoJulgarIndeferidoSemIndicacao: string;

  public statusDeferido: number = Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO;
  public statusIndeferido: number = Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;

  public statusJulgamentoAtual: number;
  public tipoJulgamentoAtual: string;

  public _membrosComPendencia: any;
  public _membrosSemPendencia: any;

  public modalJulgar: BsModalRef | null;
  public showModal = true;

  public modalConfirmacao: BsModalRef;
  public modalPendeciasMembro: BsModalRef;
  public modalConfirmacaoIndeferir: BsModalRef;
  public modalExclusaoMembroPendencia: BsModalRef;
  public modalConfirmacaoIndeferirSemSubstituicao: BsModalRef;

  /**
   * Tipo de deferimento aceito no parametro:
   *
   * Input julgarTipo = 'RECURSO' | 'SUBSTITUICAO' | 'RECURSO_SUBSTITUICAO';
   */
  @Input() julgarTipo: string;
  /**
   * Input julgarTipo = SUBSTITUICAO';
   */
  @Input() substituicaoJulgamento: any;
  /**
   *  Input julgarTipo = RECURSO';
   */
  @Input() recursoJulgamento: any;
  @Input() chapaEleicao: any;
  @Input() membrosPorSituacao: any;

  @Input() isChapaIES: boolean = false;

  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
  @ViewChild('modalPendeciasMembro', { static: true }) templateSubstituicao: TemplateRef<any>;
  @ViewChild('templateConfirmacaoExclusaoMembroPendencia', { static: true }) templateConfirmacaoExclusaoMembroPendencia: TemplateRef<any>;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ) { }

  ngOnInit() { }

  public inicializarJulgar(): void {
    this.julgamento = this.getEstruturaJulgamento();
    this.setTipoTitle();
    this.inicializaConfiguracaoCkeditor();
    this._membrosComPendencia = _.cloneDeep(this.membrosPorSituacao.membrosComPendencia);
    this._membrosSemPendencia = _.cloneDeep(this.membrosPorSituacao.membrosSemPendencia);
    this.profissionaisComboBox = this.membrosPorSituacao.membrosSemPendencia;
    this.verificaListaMembrosPendencia();
    this.getMsgConfirmarIndeferimentoSemIndicação();
    this.possuiMembrosComPendencia = this.membrosPorSituacao.membrosComPendencia.length > 0;
  }

  /**
   * Método que apresenta modal de jultamento na tela
   */
  public abrirModalJulgar(template: TemplateRef<any>, acao: number, chapaEleicao: any): void {
    this.statusJulgamentoAtual = acao;
    this.inicializarJulgar();

    // Verifica o período da atividade 5.4 de julgamento de primeria instância
    if (this.showModal) {
      if (!chapaEleicao.isIniciadoAtivJulgSegundaInstancia == true) {
        this.messageService.addConfirmYesNo('MSG_JULGAMENTO_FINAL_ANTES_DATA_INICIO', () => {
          this.abrirModal(template);
        });
      } else if (chapaEleicao.isFinalizadoAtivJulgSegundaInstancia == true) {
        this.messageService.addConfirmYesNo('MSG_JULGAMENTO_FINAL_DEPOIS_DATA_FIM', () => {
          this.abrirModal(template);
        });
      } else if (chapaEleicao.isIniciadoAtivJulgSegundaInstancia && !chapaEleicao.isFinalizadoAtivJulgSegundaInstancia) {
        this.abrirModal(template);
      }
    }
  }

  /**
   * Definir o Modal de Deferimento
   */
  public abrirModal(template: TemplateRef<any>) {
    this.modalJulgar = this.modalService.show(template, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-xl modal-dialog-centered'
    });
  }

  /**
   * retorna a estrutura dos dados do objeto de jultamento
   */
  public getEstruturaJulgamento(): any {

    const julgamento = {
      idChapaEleicao: Number(this.chapaEleicao.id),
      descricao: String(''),
      idStatusJulgamentoFinal: Number(this.statusJulgamentoAtual),
      arquivos: []
    };

    return julgamento;
  }

  /**
   * Definir o tipo de Deferimento
   */
  public setTipoTitle() {
    this.showModal = true;

    switch (this.julgarTipo) {
      case 'RECURSO':
        this.tipoJulgamentoAtual = 'o Recurso';
        this.julgamento.idRecursoJulgamentoFinal = this.recursoJulgamento.id;
        break;
      case 'SUBSTITUICAO':
        this.tipoJulgamentoAtual = 'a Substituição';
        this.julgamento.idSubstituicaoJulgamentoFinal = this.substituicaoJulgamento.id;
        break;
      case 'RECURSO_SUBSTITUICAO':
        this.tipoJulgamentoAtual = 'o Recurso do Pedido de Substituição';
        this.julgamento.idRecursoSegundoJulgamentoSubstituicao = this.substituicaoJulgamento.id;
        break;
      default:
        this.showModal = false;
        this.messageService.addMsgDanger(this.messageService.getDescription('MSG_ERRO_JULGAR_TIPO'));
        break;
    }
  }

  /**
   * verifica se a lista de membros com pendÊncia está com dados no array
   */
  public verificaListaMembrosPendencia(): void {
    this.possuiRegistroLista = this.membrosPorSituacao.membrosComPendencia.length > 0;
  }

  /**
   * Realiza a ação de salvar o julgamento.
   */
  public salvar(): void {
    let isCamposObrigatoriosOK = false;
    this.submitted = true;

    if (this.julgamento.arquivos.length > 0 && this.julgamento.descricao.length > 0) {
      isCamposObrigatoriosOK = true;

      if (this.isIndeferido()) {
        this.julgamento.indicacoes = this.preparaIndicacoes(this.membrosPorSituacao.membrosComPendencia);
      }

      this.abrirModalConfirmacao();
    }
  }

  /**
   * Método responsável por persistir os dados da requisição
   */
  public persiteDados(): void {
    if (this.submitted) {
      this.julgamentoFinalClientService.salvarJulgamentoSegundaInstancia(this.julgamento, this.julgarTipo).subscribe(
        data => {
          this.cleanAll();

          this.julgamentoSalvo = data;

          if (this.statusJulgamentoAtual == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO) {
            this.messageService.addMsgSuccess(this.messageService.getDescription('MSG_SUCESSO_JULGAR_INDEFERIMENTO'));
          } else {
            this.messageService.addMsgSuccess(this.messageService.getDescription('MSG_SUCESSO_JULGAR_DEFERIMENTO'));
          }

          this.redirecionarVisualizarJulgamento.emit(data);
          this.modalConfirmacao.hide();
          this.modalJulgar.hide();
        },
        error => {
          this.modalConfirmacao.hide();
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Limpa todos os campos
   */
  public cleanAll() {
    this.membroExclusaoAtual = null;
    this.submitted = false;
    this.julgamento = {};
    this.membrosPorSituacao.membrosSemPendencia = this._membrosSemPendencia;
    this.membrosPorSituacao.membrosComPendencia = this._membrosComPendencia;
    this.submitted = false;
  }

  /**
   * cancela o pedido de substituição de impugnação
   */
  public cancelaPedido(): void {
    this.cleanAll();
    this.modalJulgar.hide();
  }

  /**
   * Exclui o membro da lista de membros com pendência
   */
  public confirmaExclusaoMembroPendencia(): void {
    this.profissionaisComboBox.push(this.membroExclusaoAtual.membro);
    this.membrosPorSituacao.membrosComPendencia.splice(this.membroExclusaoAtual.indice, 1);
    this.profissionaisComboBox = _.orderBy(
      this.profissionaisComboBox, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']
    );
    this.verificaListaMembrosPendencia();
    this.modalExclusaoMembroPendencia.hide();
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
   */
  public adicionarParecerJulgamento(evento: any): void {
    this.julgamento.descricao = evento;
  }

  /**
   * Responsável por salvar os arquivos que foram submetidos no componete arquivo.
   */
  public salvarArquivoJulgamento(arquivos: any): void {
    this.submitted = false;

    const arquivo = {
      nome: arquivos[0].nome,
      tamanho: arquivos[0].tamanho,
      arquivo: arquivos[0].arquivo,
    };

    this.julgamento.arquivos.push(arquivo);
  }

  /**
   * Realiza o download do arquivo anexado no formulário
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
   * Verifica se a ação acionada foi para indeferir o julgamento
   */
  public isIndeferido(): boolean {
    return this.statusJulgamentoAtual === Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
  }

  /**
   * Método responsável por remover os membros da combobox e adicionar os membros na lista de membros com pendência
   */
  public adicionaMembroListaPendencia(evento: any): any {

    let membroSelecionado: any;
    const indice = parseInt(evento.target.value, 10);

    this.profissionaisComboBox.forEach((membro: any, i: number) => {
      if (i === indice) {
        membroSelecionado = membro;
      }
    });

    if (membroSelecionado) {
      this.profissionaisComboBox.splice(indice, 1);
      this.membrosPorSituacao.membrosComPendencia.push(membroSelecionado);
    } else if(indice == -2) {
      this.membrosPorSituacao.membrosComPendencia = _.concat(this.profissionaisComboBox, _.cloneDeep(this.membrosPorSituacao.membrosComPendencia));
      this.profissionaisComboBox = [];
    }
    this.membrosPorSituacao.membrosComPendencia = _.orderBy(
      this.membrosPorSituacao.membrosComPendencia, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']
    );

    this.verificaListaMembrosPendencia();
  }

  /**
   * Verifica se opção de selecionar todos os membros para indicação deve ser exibida.
   */
  public isMostrarOpcaoSelecionarTodosMembroListaPendencia(): boolean {
    return this.profissionaisComboBox.length > 0 && !this.isChapaIES;
  }

  /**
   * Método responsável por remover o membro da lista de pendências e adicionar na combobox de membros
   */
  public removeMembroListaPendencia(evento: any) {
    this.membroExclusaoAtual = evento;
    this.abrirModalConfirmacaoExclusaoMembroPendencia();
  }

  /**
   * Retorna os membros indicados no formado da requisição
   */
  public preparaIndicacoes(arrayIndicacoes: any): any {
    const indicacoes: any = [];

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
   * -----------------------------------------------------------------------------------------
   * ------------------------------ Métodos Controle dos Modais ------------------------------
   * -----------------------------------------------------------------------------------------
   */

  /**
   * Exibe modal de confirmação.
   */
  public abrirModalConfirmacao(): void {
    if (this.possuiMembrosComPendencia && !this.possuiRegistroLista) {
      this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_SEM_MEMBRO_INDICACAO');
    } else {
      this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_JULGAR_DEFERIMENTO', [this.tipoJulgamentoAtual]);

      if (this.statusJulgamentoAtual == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO) {
        this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_JULGAR_INDEFERIMENTO', [this.tipoJulgamentoAtual]);
      }

    }

    this.tituloModalConfirmacao = this.messageService.getDescription('TITLE_CONFIRMAR_JULGAR_DEFERIMENTO');

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
   * Exibe modal de confirmação de exclusão de membro.
   */
  public abrirModalConfirmacaoExclusaoMembroPendencia(): void {
    this.tituloModalExclusaoMembroPendencia = this.messageService.getDescription('LABEL_EXCLUIR_MEMBRO');
    this.msgConfirmacaoExclusaoMembroPendencia = this.messageService.getDescription('MSG_CONFIRMACAO_EXCLUSAO_MEMBRO_JULGAR');

    this.modalExclusaoMembroPendencia = this.modalService.show(this.templateConfirmacaoExclusaoMembroPendencia, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  /**
   * Excluir todos os membros da lista de pendências e adicionar na combobox de membros
   * @param evento
   */
  public excluirTodos() {
    this.profissionaisComboBox = _.concat(this.profissionaisComboBox, _.cloneDeep(this.membrosPorSituacao.membrosComPendencia));
    this.profissionaisComboBox = _.orderBy(this.profissionaisComboBox,['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);
    this.membrosPorSituacao.membrosComPendencia = [];
    this.verificaListaMembrosPendencia();
  }

  /**
   * fecha a mensgem de exclusão de membro com pendência
   */
  public fecharConfirmacaoExclusaoMembroPendencia(): void {
    this.modalExclusaoMembroPendencia.hide();
    this.membroExclusaoAtual = undefined;
  }

  public fecharConfirmacaoIndeferirSemSubstituicao(): void {
    this.modalConfirmacaoIndeferirSemSubstituicao.hide();
  }

  public fecharConfirmacaoIndeferir(): void {
    this.modalConfirmacaoIndeferir.hide();
  }

  /**
   * -----------------------------------------------------------------------------------------
   * ----------------------------- Métodos de títulos e mensgens -----------------------------
   * -----------------------------------------------------------------------------------------
   */

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
    return (this.statusJulgamentoAtual === Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO)
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
}
