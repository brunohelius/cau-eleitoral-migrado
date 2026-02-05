import { Component, OnInit, Input, OnChanges, Output, EventEmitter } from '@angular/core';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalVisualizarProducaoProvasComponent } from './../aba-parecer-denuncia/modal-visualizar-producao-provas/modal-visualizar-producao-provas.component';
import { ModalVisualizarImpedimentoSuspeicaoComponent } from './modal-visualizar-impedimento-suspeicao/modal-visualizar-impedimento-suspeicao.component';
import { ModalVisualizarAudienciaInstrucaoComponent } from './modal-visualizar-audiencia-instrucao/modal-visualizar-audiencia-instrucao.component';
import { ModalJulgamentoPrimeiraInstanciaComponent } from './modal-julgamento-primeira-instancia/modal-julgamento-primeira-instancia.component';
import { ModalVisualizarAlegacaoFinalComponent } from './modal-visualizar-alegacao-final/modal-visualizar-alegacao-final.component';
import { ModalVisualizarParecerFinalComponent } from './modal-visualizar-parecer-final/modal-visualizar-parecer-final.component';

@Component({
  selector: 'aba-parecer-denuncia',
  templateUrl: './aba-parecer-denuncia.component.html',
  styleUrls: ['./aba-parecer-denuncia.component.scss']
})
export class AbaParecerDenunciaComponent implements OnChanges, OnInit {

  @Input('dadosDenuncia') denuncia;

  @Input('encaminhamentos') encaminhamentos;
  @Output('onEncaminhamentosCarregados') encaminhamentosCarregadosEvent: EventEmitter<any> = new EventEmitter();

  public modalVisualizarProducaoProvas: BsModalRef;
  public modalVisualizarImpedimentoSuspeicao: BsModalRef;
  public modalVisualizarAudienciaInstrucao: BsModalRef;
  public modalAlegacaoFinal: BsModalRef;
  public modalVisualizarAlegacaoFinal: BsModalRef;

  public modalJulgamentoPrimeiraInstancia: BsModalRef;
  public modalVisualizarParecerFinal: BsModalRef;

  public statusEncaminhamento: any;
  public limitePaginacao: number = 10;
  public tipoEncaminhamentoAcao: any = null;
  public producaoProvas: any;
  public audienciaInstrucao: any;
  public impedimentoSuspeicao: any;
  public alegacaoFinal: any;
  public parecerFinal: any;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.loadStatusEncaminhamentos();
    this.tipoEncaminhamentoAcao = {
        [Constants.TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS]: {
          action: {
            inserir: this.inserirAlegacoesFinais,
            visualizar: this.visualizarAlegacoesFinais
          },
          ativo: {
            inserir: true,
            visualizar: true
          },
          label: this.messageService.getDescription('TITLE_INSERIR'),
          icon: 'fa-plus'
        },
        [Constants.TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO]: {
          action: {
            inserir: this.inserirAudienciaInstrucao,
            visualizar: this.visualizarAudienciaInstrucao
          },
          ativo: {
            inserir: true,
            visualizar: true
          },
          label: this.messageService.getDescription('TITLE_REGISTRAR_AUDIENCIA'),
          icon: 'fa-plus'
        },
        [Constants.TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO]: {
          action: {
            inserir: this.inserirRelator,
            visualizar: this.visualizarImpedimentoSuspeicao
          },
          ativo: {
            inserir: true,
            visualizar: true
          },
          label: this.messageService.getDescription('TITLE_INSERIR_RELATOR'),
          icon: 'fa-user-plus'
        },
        [Constants.TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS]: {
          action: {
            inserir: this.inserirProvas,
            visualizar: this.visualizarProducaoProvas
          },
          ativo: {
            inserir: true,
            visualizar: true
          },
          label: this.messageService.getDescription('TITLE_INSERIR_PROVAS'),
          icon: 'fa-plus'
        },
        [Constants.TIPO_ENCAMINHAMENTO_PARECER_FINAL]: {
          action: {
            inserir: false,
            visualizar: this.visualizarParecerFinal
          },
          ativo: {
            inserir: true,
            visualizar: true
          },
          label: this.messageService.getDescription('LABEL_PARECER_FINAL'),
          icon: 'fa-plus'
        }
      }
  }

  ngOnChanges() {
    if (this.encaminhamentos == undefined) {
      this.getEncaminhamentosDenuncia();
    }
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalJulgamentoPrimeiraInstancia(): void {
    if (Constants.SITUACAO_DENUNCIA_EM_RELATORIA == this.denuncia.idSituacaoDenuncia) {
      this.messageService.addMsgWarning(this.messageService.getDescription('MSG_AGUARDE_PARECER_FINAL_RELATOR_DENUNCIA'));
      return;
    }

    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.idTipoDenuncia
    };

    this.modalJulgamentoPrimeiraInstancia = this.modalService.show(ModalJulgamentoPrimeiraInstanciaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   *
   */
  public getStatusEncaminhamento = (idStatusEncaminhamentoDenuncia) => {
    return this.statusEncaminhamento[idStatusEncaminhamentoDenuncia];
  }

  /**
   * Retorna os encaminhamentos de denuncia para parecer.
   */
  private getEncaminhamentosDenuncia = () => {
    this.denunciaService.getEncaminhamentosDenuncia(this.denuncia.idDenuncia).subscribe((data) => {
      this.encaminhamentosCarregadosEvent.emit(data.encaminhamentos);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Verifica os parametros para analise de defesa
   */
  public hasParametrosAcessoParaAnalise = () => {
    return this.denuncia.isRelatorAtual
      && this.denuncia.hasDefesaPrazoEncerrado
      && this.denuncia.idSituacaoDenuncia == Constants.SITUACAO_DENUNCIA_EM_RELATORIA
      && !this.denuncia.hasImpedimentoSuspeicaoPendente;
  }

  /**
   * Verifica os parametros para julgamento de denuncia
   */
  public hasParametrosAcessoParaJulgamento = () => {
      return this.denuncia.julgamentoDenuncia == undefined
          && this.denuncia.analiseAdmissibilidade.admissao.admitida
          && (this.denuncia.isAssessorCEN || this.denuncia.isAssessorCEUf)
          && [Constants.SITUACAO_DENUNCIA_EM_RELATORIA,
              Constants.SITUACAO_DENUNCIA_EM_JULGAMENTO_DENUNCIA_ADMITIDA].includes(this.denuncia.idSituacaoDenuncia);
  }

  /**
   *
   */
  private loadStatusEncaminhamentos = () => {
    let iconConfig = 'align-middle fa fa-2x';
    this.statusEncaminhamento = {
      [Constants.STATUS_ENCAMINHAMENTO_PENDENTE]: {
        'classCss': 'text-warning',
        'icon': `${iconConfig} fa-exclamation-circle`,
        'title': this.messageService.getDescription('TITLE_PENDENTE'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_TRANSCORRIDO]: {
        'classCss': 'text-danger',
        'icon': `${iconConfig} fa-times-circle`,
        'title': this.messageService.getDescription('TITLE_TRANSCORRIDO'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_FECHADO]: {
        'classCss': 'text-danger',
        'icon': `${iconConfig} fa-times-circle`,
        'title': this.messageService.getDescription('TITLE_FECHADO'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_CONCLUIDO]: {
        'classCss': 'text-success',
        'icon': `${iconConfig} fa-check-circle`,
        'title': this.messageService.getDescription('TITLE_CONCLUIDO'),
      },
    };
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

  /**
   *
   */
  public inserirAlegacoesFinais(encaminhamento, scope) {
    this.abrirModalAlegacaoFinal(encaminhamento.idEncaminhamento);
  }

  /**
   * Abre o formulário de inserir alegações finais.
   */
  public abrirModalAlegacaoFinal(idEncaminhamento): void {
    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      idEncaminhamentoDenuncia: idEncaminhamento
    };

    /*this.modalAlegacaoFinal = this.modalService.show(ModalAlegacaoFinalComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));*/
  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarAlegacoesFinais(encaminhamento, scope): void {
    scope.getAlegacoesFinais(encaminhamento.idEncaminhamento);
  }

  /**
   *
   * @param encaminhamento
   */
  public inserirAudienciaInstrucao(encaminhamento, scope): void {

  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarAudienciaInstrucao(encaminhamento, scope): void {
    scope.getEncaminhamentoAudienciaInstrucao(encaminhamento.idEncaminhamento);
  }

  /**
 *
 */
  public inserirRelator(encaminhamento, scope) {

  }

  /**
   *
   */
  public visualizarImpedimentoSuspeicao(encaminhamento, scope) {
    scope.getImpedimentoSuspeicao(encaminhamento.idEncaminhamento);
  }

  /**
 * Abre o formulário de Cadastrar Provas.
 *
 * @param encaminhamento
 */
  public inserirProvas(encaminhamento, scope) {
    const initialState = {
      idDenuncia: scope.denuncia.idDenuncia,
      idEncaminhamento: encaminhamento.idEncaminhamento,
      nuDenuncia: scope.denuncia.numeroDenuncia
    };

    /*this.modalFormProvasDenuncia = scope.modalService.show(ModalFormProvasComponent,
    Object.assign({}, {}, { class: 'modal-lg', initialState }));*/
  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarProducaoProvas(encaminhamento, scope): void {
    scope.getEncaminhamentoProvas(encaminhamento.idEncaminhamento);
  }

  /**
   *
   */
  private getEncaminhamentoProvas = (idEncaminhamento) => {
    this.denunciaService.getEncaminhamentosProvas(idEncaminhamento).subscribe((data) => {
      this.producaoProvas = data;
      this.abrirModalVisualizarProducaoProvas();
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
 * Abre o formulário de análise de defesa.
 */
  public abrirModalVisualizarProducaoProvas(): void {

    const initialState = {
      producaoProvas: this.producaoProvas
    };

    this.modalVisualizarProducaoProvas = this.modalService.show(ModalVisualizarProducaoProvasComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }

  /**
*
*/
  private getImpedimentoSuspeicao = (idEncaminhamento) => {
    this.denunciaService.getImpedimentoSuspeicao(idEncaminhamento).subscribe((data) => {
      this.impedimentoSuspeicao = data;
      this.abrirModalVisualizarImpedimentoSuspeicao();
    }, error => {
      this.messageService.addMsgDanger(error);
    });

  }

  /**
*
*/
  public abrirModalVisualizarImpedimentoSuspeicao(): void {
    const initialState = {
      encaminhamentoDenuncia: this.impedimentoSuspeicao,
    };
    this.modalVisualizarImpedimentoSuspeicao = this.modalService.show(ModalVisualizarImpedimentoSuspeicaoComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   *
   */
  private getEncaminhamentoAudienciaInstrucao = (idEncaminhamento) => {
    this.denunciaService.getAudienciaInstrucao(idEncaminhamento).subscribe((data) => {
      this.audienciaInstrucao = data;
      this.abrirModalVisualizarAudienciaInstrucao();
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }


  /**
   * Abre o formulário de audiência de instrução.
   */
  public abrirModalVisualizarAudienciaInstrucao(): void {

    const initialState = {
      encaminhamentoAudiencia: this.audienciaInstrucao
    };

    this.modalVisualizarAudienciaInstrucao = this.modalService.show(ModalVisualizarAudienciaInstrucaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }

  /**
   *
   */
  private getAlegacoesFinais = (idEncaminhamento) => {
    this.denunciaService.getAlegacoesFinais(idEncaminhamento).subscribe((data) => {
      this.alegacaoFinal = data;
      this.abrirModalVisualizarAlegacoesFinais();
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Abre o formulário de inserir alegações finais.
   */
  public abrirModalVisualizarAlegacoesFinais(): void {
    const initialState = {
      alegacaoFinal: this.alegacaoFinal,
    };
    this.modalVisualizarAlegacaoFinal = this.modalService.show(ModalVisualizarAlegacaoFinalComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarParecerFinal(encaminhamento, scope): void {

    scope.getEncaminhamentoParecerFinal(encaminhamento.idEncaminhamento);
  }


  /**
     *
     */
  private getEncaminhamentoParecerFinal = (idEncaminhamento) => {
    this.denunciaService.getParecerFinal(idEncaminhamento).subscribe((data) => {
      this.parecerFinal = data;
      this.abrirModalVisualizarParecerFinal();
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }


  /**
   * Abre o formulário de parecer final.
   */
  public abrirModalVisualizarParecerFinal(): void {

    const initialState = {
      encaminhamentoParecerFinal: this.parecerFinal
    };
    
    this.modalVisualizarParecerFinal = this.modalService.show(ModalVisualizarParecerFinalComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }
}
