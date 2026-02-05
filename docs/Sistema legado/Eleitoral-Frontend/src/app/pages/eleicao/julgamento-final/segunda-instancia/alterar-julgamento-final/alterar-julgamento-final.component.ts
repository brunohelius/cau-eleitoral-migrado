import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, TemplateRef, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import * as _ from 'lodash';
import * as moment from 'moment';
import { StringService } from 'src/app/string.service';

/**
 * Componente responsável pelo Deferimento do Julgamento Final 2ª Instância.
 *
 * author Squadra Tecnologia.
 */
@Component({
  selector: 'app-alterar-julgamento-final-segunda-instancia',
  templateUrl: './alterar-julgamento-final.component.html',
  styleUrls: ['./alterar-julgamento-final.component.scss']
})

export class AlterarJulgamentoSegundaInstanciaComponent implements OnInit {

  public submitted = false;
  public hasArquivo = false;
  public arquivos: any = [];
  public julgamento: any = {};
  public julgamentoSalvo: any;
  public msgConfirmacao: string;
  public julgamentoAtual: any = {};
  public dadosFormulario: any = {};
  public configuracaoCkeditor: any;
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
  public valorInput: any = String(Constants.ID_SELECAO_OPCAO_CONTEUDO);

  public statusDeferido: number = Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO;
  public statusIndeferido: number = Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;

  public statusJulgamentoAtual: number;
  public tipoJulgamentoAtual: string;
  public statusJulgamentoPaiAtual: number;
  public membrosIndicacaoPai: any;

  public _membrosComPendencia: any;
  public _membrosSemPendencia: any;
  public _valorInput: any = String(Constants.ID_SELECAO_OPCAO_CONTEUDO);

  public showModal = true;
  public hideModal = true;
  public modalOpcao: BsModalRef | null;
  public modalJulgar: BsModalRef | null;

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
  @Input() idSubstituicaoJulgamento: any;
  @Input() julgamentoSubstituicaoPai: any;
  /**
   *  Input julgarTipo = RECURSO';
   */
  @Input() chapaEleicao: any;
  @Input() statusJulgamento: any;
  @Input() recursoJulgamento: any;
  @Input() membrosPorSituacao: any;
  @Input() julgamentoRecursoPai: any;
  @Input() isChapaIES: boolean = false;

  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
  @ViewChild('templatePendeciasMembro', { static: true }) templateSubstituicaoPendeciasMembro: TemplateRef<any>;
  @ViewChild('templateConfirmacaoExclusaoMembroPendencia', { static: true }) templateConfirmacaoExclusaoMembroPendencia: TemplateRef<any>;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ) { }

  ngOnInit() {
  }
  /**
   * Método que apresenta modal de seleção de opção de alteração na tela
   */
  public abrirModalOpcao(template: TemplateRef<any>): void {
    this.fecharModalOpcao(template);
  }
  /**
   * Abrir e Fechar modal opção
   */
  public fecharModalOpcao(template: TemplateRef<any>) {
    this.modalOpcao = this.modalService.show(template, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-xl modal-dialog-centered'
    });
  }
  /**
   * Retorna o título do formulário de acordo com a ação acionada
   */
  public getTituloFormularioUm(): string {
    return this.messageService.getDescription('LABEL_ALTERAR_JULGAMENTO_FINAL');
  }

  public inicializarJulgar(): void {
    this.julgamento = this.getEstruturaJulgamento();
    this.setTipoTitle();
    this.setStatusJulgamentoAtual();
    this.inicializaConfiguracaoCkeditor();
    this._membrosComPendencia = _.cloneDeep(this.membrosPorSituacao.membrosComPendencia);
    this._membrosSemPendencia = _.cloneDeep(this.membrosPorSituacao.membrosSemPendencia);
    this.inicializarMembros();
    this.verificaListaMembrosPendencia();
    this.getMsgConfirmarIndeferimentoSemIndicação();
    this.possuiMembrosComPendencia = this.membrosPorSituacao.membrosComPendencia.length > 0;
  }

  public setStatusJulgamentoAtual(): void {
    if (this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO) {
      this.statusJulgamentoAtual = this.statusJulgamentoPaiAtual;
    } else {
      this.statusJulgamentoAtual = this.statusJulgamentoPaiAtual == Constants.TIPO_JULGAMENTO_DEFERIDO ?
      Constants.TIPO_JULGAMENTO_INDEFERIDO : Constants.TIPO_JULGAMENTO_DEFERIDO;
    }
    this.julgamento.idStatusJulgamentoFinal = Number(this.statusJulgamentoAtual);
  }

  public inicializarMembros(): void {
    if (this.statusJulgamentoPaiAtual == Constants.TIPO_JULGAMENTO_INDEFERIDO && this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO) {
      const membrosComPendencia = _.cloneDeep(this._membrosComPendencia);
      const membrosSemPendencia = _.cloneDeep(this._membrosSemPendencia);
      const membrosChapa = [...membrosComPendencia, ...membrosSemPendencia];
      this.membrosPorSituacao.membrosComPendencia = _.cloneDeep(this.membrosIndicacaoPai);

      this.membrosPorSituacao.membrosSemPendencia = membrosChapa.filter((value) => {
        if (this.isMembroIndicadoPosicaoPai(value.numeroOrdem, value.tipoParticipacaoChapa.id)) {
          return false;
        } else {
          return true;
        }
      });
    }
    this.profissionaisComboBox = this.membrosPorSituacao.membrosSemPendencia;

    this.profissionaisComboBox = _.orderBy(
      this.profissionaisComboBox, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']
    );

    if (this.membrosPorSituacao.membrosComPendencia) {
      this.membrosPorSituacao.membrosComPendencia = _.orderBy(
        this.membrosPorSituacao.membrosComPendencia, ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']
        , ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']
      );
    }
  }

  public isMembroIndicadoPosicaoPai(numeroOrdem: number, idTipoParticipacao: number): boolean {
    let isMembroIndicadoPosicaoPai =  false;
    this.membrosIndicacaoPai.forEach((value) => {
      if (value.numeroOrdem == numeroOrdem && value.tipoParticipacaoChapa.id == idTipoParticipacao) {
        isMembroIndicadoPosicaoPai = true;
      }
    });
    return isMembroIndicadoPosicaoPai;
  }

  public setMembrosIndicacaoPai(indicacoes: any): void{
    this.membrosIndicacaoPai = [];

    if (indicacoes && indicacoes.length > 0) {
      indicacoes.forEach((value) => {
        if (value.membroChapa) {
          this.membrosIndicacaoPai.push(value.membroChapa);
        } else {
          this.membrosIndicacaoPai.push({
            numeroOrdem: value.numeroOrdem,
            tipoParticipacaoChapa: value.tipoParticipacaoChapa
          });
        }
      });
    }
  }

  /**
   * Método que apresenta modal de jultamento na tela
   */
  public abrirModalJulgar(template: TemplateRef<any>): void {
    this.inicializarJulgar();

    if (this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO) {
      if (this.showModal) {
        this.modalOpcao.hide();
        this.abrirModal(template);
      }
    }
    if (this.valorInput == Constants.ID_SELECAO_OPCAO_DECISAO) {
      if (this.showModal) {
        this.modalOpcao.hide();
        this.abrirModal(template);
      }
    }
  }

  public isConteudo(): boolean {
    return this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO;
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
      arquivos: [],
      retificacaoJustificativa: String('')
    };
    return julgamento;
  }

  public carregarDadosDoJulgamento(julgamentoPai: any, isRecurso = false): any {
    if (this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO) {
      this.julgamento.descricao = julgamentoPai.descricao;

      if (isRecurso) {
        this.arquivos = [{ id: julgamentoPai.id, nome: julgamentoPai.nomeArquivo }];
      } else {
        this.arquivos = _.cloneDeep(julgamentoPai.arquivos);
      }

      this.hasArquivo = true;
    }
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
        this.julgamento.idJulgamentoSegundaInstanciaRecursoPai = this.julgamentoRecursoPai.id;
        this.statusJulgamentoPaiAtual = this.julgamentoRecursoPai.statusJulgamentoFinal.id;
        this.setMembrosIndicacaoPai(this.julgamentoRecursoPai.indicacoes);
        this.carregarDadosDoJulgamento(this.julgamentoRecursoPai, true );
        break;
      case 'SUBSTITUICAO':
        this.tipoJulgamentoAtual = 'a Substituição';
        this.julgamento.idSubstituicaoJulgamentoFinal = this.idSubstituicaoJulgamento;
        this.julgamento.idJulgamentoSegundaInstanciaSubstituicaoPai = this.julgamentoSubstituicaoPai.id;
        this.statusJulgamentoPaiAtual = this.julgamentoSubstituicaoPai.statusJulgamentoFinal.id;
        this.setMembrosIndicacaoPai(this.julgamentoSubstituicaoPai.indicacoes);
        this.carregarDadosDoJulgamento(this.julgamentoSubstituicaoPai);
        break;
      case 'RECURSO_SUBSTITUICAO':
        this.tipoJulgamentoAtual = 'o Recurso do Pedido de Substituição';
        this.julgamento.idRecursoSegundoJulgamentoSubstituicao = this.idSubstituicaoJulgamento;
        this.julgamento.idJulgamentoRecursoPedidoSubstituicaoPai = this.julgamentoSubstituicaoPai.id;
        this.statusJulgamentoPaiAtual = this.julgamentoSubstituicaoPai.statusJulgamentoFinal.id;
        this.setMembrosIndicacaoPai(this.julgamentoSubstituicaoPai.indicacoes);
        this.carregarDadosDoJulgamento(this.julgamentoSubstituicaoPai);
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

    this.possuiRegistroLista = this.membrosPorSituacao.membrosComPendencia && this.membrosPorSituacao.membrosComPendencia.length > 0;
  }

  /**
   * Realiza a ação de salvar o julgamento.
   */
  public salvar(): void {
    let isCamposObrigatoriosOK = false;
    this.submitted = true;

    if (this.hasArquivo &&
        this.julgamento.descricao.length > 0 &&
        this.julgamento.retificacaoJustificativa.length > 0) {
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
  public cleanAll(isRecurso = true) {
    this.membroExclusaoAtual = null;
    this.submitted = false;
    this.julgamento = {};
    this.membrosPorSituacao.membrosSemPendencia = this._membrosSemPendencia;
    this.membrosPorSituacao.membrosComPendencia = this._membrosComPendencia;
    this.submitted = false;
    this.valorInput = this._valorInput;
    if (isRecurso) {
      this.arquivos = [];
    }
  }

  /**
   * cancela o pedido de substituição de impugnação
   */
  public cancelaPedido(): void {
    this.cleanAll();
    this.modalOpcao.hide();
    if (this.modalJulgar) {
      this.modalJulgar.hide();
    }
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
    this.hasArquivo = false;
  }

  /**
   * Adiciona a descrição do julgamento no objeto de julgamento
   */
  public adicionarParecerJulgamento(evento: any): void {
    this.julgamento.descricao = evento;
  }

  /**
   * Adiciona a descrição do julgamento no objeto de julgamento
   */
  public adicionarJustificativaJulgamento(evento: any): void {
    this.julgamento.retificacaoJustificativa = evento;
  }

  /**
   * Responsável por salvar os arquivos que foram submetidos no componete arquivo.
   */
  public salvarArquivoJulgamento(arquivos: any): void {
    this.submitted = false;
    this.hasArquivo = true;

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
    if (download.arquivo.id) {
      switch (this.julgarTipo) {
        case 'RECURSO':
          this.julgamentoFinalClientService.getDocumentoJulgamentoRecursoSegundaInstancia(download.arquivo.id).subscribe
          ((data: Blob) => {
            download.evento.emit(data);
          }, error => {
            this.messageService.addMsgDanger(error);
          });
          break;
        case 'SUBSTITUICAO':
          this.julgamentoFinalClientService.getDocumentoJulgamentoSubstituicaoSegundaInstancia(download.arquivo.id).subscribe
          ((data: Blob) => {
            download.evento.emit(data);
          }, error => {
            this.messageService.addMsgDanger(error);
          });
          break;
        case 'RECURSO_SUBSTITUICAO':
          this.julgamentoFinalClientService.getArquivoRecursoSegundoJulgamentoSubstituicao(download.arquivo.id).subscribe
          ((data: Blob) => {
            download.evento.emit(data);
          }, error => {
            this.messageService.addMsgDanger(error);
          });
          break;
      }
    } else {
      download.evento.emit(download.arquivo);
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
   * Método responsável por remover o membro da lista de pendências e adicionar na combobox de membros
   */
  public removeMembroListaPendencia(evento: any) {
    this.membroExclusaoAtual = evento;
    this.abrirModalConfirmacaoExclusaoMembroPendencia();
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
   * Verifica se opção de selecionar todos os membros para indicação deve ser exibida.
   */
  public isMostrarOpcaoSelecionarTodosMembroListaPendencia(): boolean {
    return this.profissionaisComboBox.length > 0 && !this.isChapaIES;
  }

  /**
   * Retorna os membros indicados no formado da requisição
   */
  public preparaIndicacoes(arrayIndicacoes: any): any {
    const indicacoes: any = [];
    arrayIndicacoes.forEach(data => {
      const estrutura: any = {};
      estrutura.numeroOrdem = data.numeroOrdem;
      estrutura.idMembroChapa = data.id;
      estrutura.idTipoParicipacaoChapa = data.tipoParticipacaoChapa.id;
      indicacoes.push(estrutura);
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
    // tslint:disable-next-line: max-line-length
    if (this.possuiMembrosComPendencia && !this.possuiRegistroLista && this.statusJulgamentoAtual == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO) {
      this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_SEM_MEMBRO_INDICACAO');
    } else {
      this.msgConfirmacao = this.messageService.getDescription('MSG_CONFIRMAR_JULGAR_DEFERIMENTO', [this.tipoJulgamentoAtual]);

      if (this.valorInput == Constants.ID_SELECAO_OPCAO_CONTEUDO) {
        this.msgConfirmacao = this.messageService.getDescription('LABEL_CONFIRMAR_ALTERACAO_CONTEUDO');
      } else {
        this.msgConfirmacao = this.messageService.getDescription('LABEL_CONFIRMAR_ALTERACAO_DESICAO');
      }
    }

    this.tituloModalConfirmacao = this.messageService.getDescription('LABEL_TITLE_CONFIRMAR_ALTERACAO');

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
    if (this.statusJulgamentoAtual == Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO) {
        return this.messageService.getDescription('LABEL_JULGAR_INDEFERIDO');
    } else {
        return this.messageService.getDescription('LABEL_JULGAR_DEFERIDO');
    }
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
   * Verifica o status de Validação do Membro.
   */
  public statusValidacao(membro: any): boolean {
    return  membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
  }

  /**
  * Retorna o registro com a mascara
  */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
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
}