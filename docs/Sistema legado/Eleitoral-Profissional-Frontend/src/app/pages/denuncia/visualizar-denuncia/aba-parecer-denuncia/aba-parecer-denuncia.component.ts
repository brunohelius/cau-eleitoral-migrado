import { ModalCadastrarAudienciaInstrucaoComponent } from './modal-cadastrar-audiencia-instrucao/modal-cadastrar-audiencia-instrucao.component';
import { NgForm } from '@angular/forms';
import { Component, OnInit, Input, OnChanges, Output, EventEmitter, ViewChild, TemplateRef } from '@angular/core';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalAnalisarDefesaComponent } from './../aba-defesa-denuncia/modal-analisar-defesa/modal-analisar-defesa.component';
import { ModalFormProvasComponent } from './modal-form-provas/modal-form-provas.component';
import { ModalAlegacaoFinalComponent } from './modal-alegacao-final/modal-alegacao-final.component';
import { ModalVisualizarProducaoProvasComponent } from './../aba-parecer-denuncia/modal-visualizar-producao-provas/modal-visualizar-producao-provas.component';
import { ModalVisualizarImpedimentoSuspeicaoComponent } from './modal-visualizar-impedimento-suspeicao/modal-visualizar-impedimento-suspeicao.component';
import { ModalVisualizarAudienciaInstrucaoComponent } from './modal-visualizar-audiencia-instrucao/modal-visualizar-audiencia-instrucao.component';
import { ModalInserirRelatorComponent } from './modal-inserir-relator/modal-inserir-relator.component';
import { ModalParecerFinalComponent } from './modal-parecer-final/modal-parecer-final.component';
import { RecursoReconsideracaoComponent } from '../../julgamento/recurso-reconsideracao/recurso-reconsideracao.component';
import * as moment from 'moment';
import { ModalVisualizarParecerFinalComponent } from './modal-visualizar-parecer-final/modal-visualizar-parecer-final.component';
import { ModalVisualizarAlegacaoFinalComponent } from './modal-visualizar-alegacao-final/modal-visualizar-alegacao-final.component';

@Component({
  selector: 'aba-parecer-denuncia',
  templateUrl: './aba-parecer-denuncia.component.html',
  styleUrls: ['./aba-parecer-denuncia.component.scss']
})
export class AbaParecerDenunciaComponent implements OnChanges {

  @Input('dadosDenuncia') denuncia;
  @Input('encaminhamentos') encaminhamentos;
  @Output('onEncaminhamentosCarregados') encaminhamentosCarregadosEvent: EventEmitter<any> = new EventEmitter();

  public templateCadastroAudienciaInstrucaoModalRef: BsModalRef;

  public audiencia: any;

  public modalAnalisarDenuncia              : BsModalRef;
  public modalVisualizarProducaoProvas      : BsModalRef;
  public modalFormProvasDenuncia            : BsModalRef;
  public modalAlegacaoFinal                 : BsModalRef;
  public modalVisualizarImpedimentoSuspeicao: BsModalRef;
  public modalVisualizarAudienciaInstrucao  : BsModalRef;
  public modalVisualizarParecerFinal        : BsModalRef;
  public modalVisualizarAlegacaoFinal       : BsModalRef;
  public modalParecerFinal: BsModalRef;

  public statusEncaminhamento   : any;
  public limitePaginacao        : number  = 10;
  public tipoEncaminhamentoAcao : any     = null;
  public producaoProvas         : any;
  public audienciaInstrucao     : any;
  public parecerFinal           : any;
  public impedimentoSuspeicao   : any;
  public alegacaoFinal          : any;

  public modalRelator: BsModalRef;

  constructor(
    public modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) {}

  ngOnInit() {
    this.inicializarAudiencia();
  }

  ngOnChanges() {
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

    if (this.encaminhamentos == undefined) {
      this.getEncaminhamentosDenuncia();
    }


    this.inicializaEncaminhamentos(this.encaminhamentos);
  }

  /**
   * Verifica as regras de visualização para cada tipo de Encaminhamento.
   */
  public inicializaEncaminhamentos(encaminhamentos) {
    let scope = this;

    if (encaminhamentos != undefined) {

      encaminhamentos.forEach((encaminhamento, index) => {
        if (encaminhamento != undefined && encaminhamento != null) {
          scope["validaTipoEncaminhamento" + encaminhamento.idTipoEncaminhamento](encaminhamento);
        }
      });

    }
  }

  /**
   * Valida Regras para Impedimento e Suspeição.
   */
  public validaTipoEncaminhamento1(encaminhamento) {
    encaminhamento.ativoVisualizar = true;
    encaminhamento.ativoInserir = encaminhamento.isAcaoInserirNovoRelator;
  }

  /**
   * Valida Regras para Produção de Provas.
   */
  public validaTipoEncaminhamento2(encaminhamento) {
    encaminhamento.ativoInserir = true;
    encaminhamento.ativoVisualizar = true;

    if (encaminhamento.idStatus != 1 || encaminhamento.isDestinatarioEncaminhamento == false || encaminhamento.isPrazoVencido == true) {
      encaminhamento.ativoInserir = false;
    }
  }

  /**
   * Valida Regras para Audiencia e Instrução.
   */
  public validaTipoEncaminhamento3(encaminhamento) {
    encaminhamento.ativoInserir = true;
    encaminhamento.ativoVisualizar = true;

    if(encaminhamento.idStatus != 1 || !encaminhamento.isRelatorAtual || encaminhamento.hasEmcaminhamentoSuspeicaoPendente){
      encaminhamento.ativoInserir = false;
    }
  }

    /**
   * Valida Regras para Alegações Finais.
   */
  public validaTipoEncaminhamento4(encaminhamento) {
    encaminhamento.ativoInserir = encaminhamento.isAcaoAlegacoesFinais;
    encaminhamento.ativoVisualizar = true;
  }

  /**
   * Valida Regras para Parecer Final.
   */
  public validaTipoEncaminhamento5(encaminhamento) {
    encaminhamento.ativoInserir = false;
    encaminhamento.ativoVisualizar = true;
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

    this.modalFormProvasDenuncia = scope.modalService.show(ModalFormProvasComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   *
   */
  public inserirAlegacoesFinais(encaminhamento, scope) {
    scope.abrirModalAlegacaoFinal(encaminhamento.idEncaminhamento);
  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarProducaoProvas(encaminhamento, scope) {
    scope.getEncaminhamentoProvas(encaminhamento.idEncaminhamento);
  }

  /**
   *
   */
  public inserirRelator(encaminhamento, scope) {
    const initialState = {
      denuncia: scope.denuncia,
      idEncaminhamento: encaminhamento.idEncaminhamento
    };

    scope.modalRelator = scope.modalService.show(ModalInserirRelatorComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   *
   */
  public visualizarImpedimentoSuspeicao(encaminhamento, scope) {
    scope.getImpedimentoSuspeicao(encaminhamento.idEncaminhamento);
  }

  /**
   *
   * @param encaminhamento
   */
  public visualizarAlegacoesFinais(encaminhamento, scope): void {
    scope.getAlegacoesFinais(encaminhamento.idEncaminhamento);
  }

  /**
   * Ação de inserir Audiência de instrução.
   *
   * @param encaminhamento
   */
  public inserirAudienciaInstrucao(encaminhamento, scope): void {
    scope.audiencia.encaminhamento = encaminhamento;
    const initialState = {
      audiencia: scope.audiencia,
      encaminhamento: encaminhamento,
      denuncia: scope.denuncia,
    };

    scope.templateCadastroAudienciaInstrucaoModalRef = scope.modalService.show( ModalCadastrarAudienciaInstrucaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState })
    );
    scope.templateCadastroAudienciaInstrucaoModalRef.content.cancelar.subscribe(() => {
      scope.inicializarAudiencia();
    });
    scope.templateCadastroAudienciaInstrucaoModalRef.content.cadastro.subscribe(() => {
      scope.denunciaService.getEncaminhamentosDenuncia(scope.denuncia.idDenuncia).subscribe((data) => {
        scope.encaminhamentosCarregadosEvent.emit(data.encaminhamentos);
        scope.encaminhamentos = data.encaminhamentos;
        scope.inicializaEncaminhamentos(scope.encaminhamentos);
      }, error => {
        this.messageService.addMsgDanger(error);
      });

    });
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
   * @param encaminhamento
   */
  public visualizarParecerFinal(encaminhamento, scope): void {

    scope.getEncaminhamentoParecerFinal(encaminhamento.idEncaminhamento);
  }

  /**
   * 
   */
  public getStatusEncaminhamento = (idStatusEncaminhamentoDenuncia) => {
    return this.statusEncaminhamento[idStatusEncaminhamentoDenuncia];
  }

  /**
   * Inicialair variavel de Audiência.
   */
  public inicializarAudiencia(): void {
    this.audiencia = {
      data: undefined,
      time: undefined,
      descricao: '',
      arquivos: '',
      encaminhamento: null,
      datatimeiso: null,
    };
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalAnalisarDenuncia(): void {
    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.idTipoDenuncia
    };

    this.modalAnalisarDenuncia = this.modalService.show(ModalAnalisarDefesaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));

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
   * Abre o formulário de inserir alegações finais.
   */
  public abrirModalAlegacaoFinal(idEncaminhamento): void {
    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      idEncaminhamentoDenuncia: idEncaminhamento
    };

    this.modalAlegacaoFinal = this.modalService.show(ModalAlegacaoFinalComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
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
   * Abre o formulário de análise de defesa.
   */
  public abrirModalParecerFinalDenuncia(): void {

    if (this.denuncia.hasAlegacaoFinalConcluido) {
      const initialState = {
        idDenuncia: this.denuncia.idDenuncia,
        nuDenuncia: this.denuncia.numeroDenuncia,
        tipoDenuncia: this.denuncia.idTipoDenuncia
      };

      this.modalParecerFinal = this.modalService.show(ModalParecerFinalComponent,
        Object.assign({}, {}, { class: 'modal-lg', initialState }));

    } else if (!this.denuncia.hasDefesaPrazoEncerrado) {
      this.messageService.addMsgDanger('LABEL_PARECER_FINAL_ALEGACAO_NAO_RESPONDIDA');
    }
  }


  /**
   * Verifica os parametros para analise de defesa
   */
  public hasParametrosAcessoParaAnalise = () => {
    return (this.denuncia.isRelatorAtual
      && !this.denuncia.hasImpedimentoSuspeicaoPendente
      && !this.denuncia.hasAlegacaoFinalConcluido
      && this.denuncia.idSituacaoDenuncia == Constants.SITUACAO_DENUNCIA_EM_RELATORIA)
      && ((this.denuncia.idTipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS) || this.denuncia.hasDefesaPrazoEncerrado || this.denuncia.defesaApresentada)
  }

  /**
   * Verifica os parametros para paracer final
   */
  public hasParametrosAcessoParaParecerFinal = () => {
    return (this.denuncia.isRelatorAtual && (!this.denuncia.hasParecerFinalInseridoParaDenuncia && this.denuncia.hasEncaminhamentoAlegacaoFinal))
  }


  /**
   * 
   */
  private loadStatusEncaminhamentos = () => {
    let iconConfig = 'align-middle fa fa-2x';
    this.statusEncaminhamento = {
      [Constants.STATUS_ENCAMINHAMENTO_PENDENTE]: {
        'classCss': 'text-warning',
        'icon': `${iconConfig} fa-exclamation-circle pr-0`,
        'title': this.messageService.getDescription('TITLE_PENDENTE'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_TRANSCORRIDO]: {
        'classCss': 'text-danger',
        'icon': `${iconConfig} fa-times-circle pr-0`,
        'title': this.messageService.getDescription('TITLE_TRANSCORRIDO'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_FECHADO]: {
        'classCss': 'text-danger',
        'icon': `${iconConfig} fa-times-circle pr-0`,
        'title': this.messageService.getDescription('TITLE_FECHADO'),
      },
      [Constants.STATUS_ENCAMINHAMENTO_CONCLUIDO]: {
        'classCss': 'text-success',
        'icon': `${iconConfig} fa-check-circle pr-0`,
        'title': this.messageService.getDescription('TITLE_CONCLUIDO'),
      },
    };
  }

  /**
   * 
   */
  private getEncaminhamentosDenuncia = () => {
    this.denunciaService.getEncaminhamentosDenuncia(this.denuncia.idDenuncia).subscribe((data) => {
      this.encaminhamentosCarregadosEvent.emit(data.encaminhamentos);
      this.encaminhamentos = data.encaminhamentos;
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
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
   * Abre o formulário de inserir alegações finais.
   */
  public abrirModalVisualizarImpedimentoSuspeicao(): void {
    const initialState = {
      encaminhamentoDenuncia: this.impedimentoSuspeicao,
    };

    this.modalVisualizarImpedimentoSuspeicao = this.modalService.show(ModalVisualizarImpedimentoSuspeicaoComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
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
   */
  private getEncaminhamentoParecerFinal = (idEncaminhamento) => {
    this.denunciaService.getParecerFinal(idEncaminhamento).subscribe((data) => {
      this.parecerFinal = data;
      this.abrirModalVisualizarParecerFinal();
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
