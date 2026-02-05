import { Component, OnInit, Input, EventEmitter, Output, OnChanges } from '@angular/core';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { ConfigCardListInterface } from 'src/app/shared/component/card-list/config-card-list-interface';
import { ModalJulgamentoPrimeiraInstanciaComponent } from 'src/app/pages/denuncia/visualizar-denuncia/aba-parecer-denuncia/modal-julgamento-primeira-instancia/modal-julgamento-primeira-instancia.component';

@Component({
  selector: 'app-primeira-instancia',
  templateUrl: './primeira-instancia.component.html',
  styleUrls: ['./primeira-instancia.component.scss']
})
export class PrimeiraInstanciaComponent implements OnChanges, OnInit {

  @Input() dadosDenuncia;
  @Input('julgamentosRetificados') julgamentosRetificados?;
  @Input() visualizacaoRetificacao? = false;

  @Output() fecharModalRetificacaoEvent: EventEmitter<any> = new EventEmitter();
  @Output('onRetificacoesCarregadas') retificacoesCarregadasEvent: EventEmitter<any> = new EventEmitter();

  public abas: any = {};
  public julgamentoDenuncia: any;

  public descricaoJulgamentoSimpleText = '';
  public descricaoRetificacaoSimplexText = '';

  public configuracaoCkeditor: any = {};
  public configuracaoCkeditorRetificacao: any = {};
  public infoJulgamento: ConfigCardListInterface;

  public modalJulgamentoPrimeiraInstancia: BsModalRef;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) {}

  ngOnInit() {
    this.julgamentoDenuncia = this.dadosDenuncia.julgamento_denuncia;
    this.inicializaConfiguracaoCkeditor();
    this.inicializaConfiguracaoCkeditorRetificacao();

    if (this.julgamentoDenuncia) {
      this.carregarInfoJulgamento();

      this.descricaoRetificacaoSimplexText = StringService.getPlainText(this.dadosDenuncia.julgamento_denuncia.justificativa).slice(0, -1);
      this.descricaoJulgamentoSimpleText = StringService.getPlainText(this.dadosDenuncia.julgamento_denuncia.descricaoJulgamento).slice(0, -1);

      this.getJulgamentosRetificadosDenuncia();
    }

    this.inicializaAbas();
  }

  ngOnChanges() {
    this.getJulgamentosRetificadosDenuncia();
  }

  /**
   * Carrega as informações de relator.
   */
  public carregarInfoJulgamento():void {
    let data = {
      'julgado' : this.julgamentoDenuncia.descricaoTipoJulgamento
    };

    this.infoJulgamento = {
      header: [
        {
          'field': 'julgado',
          'header': this.messageService.getDescription('TITLE_JULGADO')
        }
      ],
      data: []
    };

    if(this.isJulgamentoProcedente()) {
      data['sentenca'] = this.julgamentoDenuncia.descricaoTipoSentencaJulgamento;
      this.infoJulgamento.header.push(
        {
          'field': 'sentenca',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_COMISSAO')
        }
      );

      if(this.julgamentoDenuncia.quantidadeDiasSuspensaoPropaganda) {
        data['qtdDias'] = this.julgamentoDenuncia.quantidadeDiasSuspensaoPropaganda;
        this.infoJulgamento.header.push(
          {
            'field': 'qtdDias',
            'header': this.messageService.getDescription('TITLE_QTDE_DIAS')
          }
        );
      }

      if(this.julgamentoDenuncia.multa) {
        data['multa'] = `${this.julgamentoDenuncia.valorPercentualMulta}% ${this.messageService.getDescription('LABEL_MULTA_ANUIDADE')}`
        this.infoJulgamento.header.push(
          {
            'field': 'multa',
            'header': this.messageService.getDescription('TITLE_VALOR_PERCENTUAL_MULTA')
          }
        );
      }

    }

    this.infoJulgamento.data.push(data);
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoJulgamento(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoJulgamentoSimpleText.length;
  }

  /**
   * Retorna a contagem de caracteres do Campo descrição da retificação.
   */
  public getContagemJustificativaRetificacao = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoRetificacaoSimplexText.length;
  }

  /**
   * Fecha o modal de Visualização de retificação do Julgamento.
   */
  public fecharModalVisualizacaoRetificacao = () => {
    this.fecharModalRetificacaoEvent.emit(true);
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'dsJulgamento',
      removePlugins: 'elementspath',
      toolbar: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
        { name: 'links', items: ['Link'] },
        { name: 'insert', items: ['Image'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
      ],
    };
  }

  /**
   * Retorna os julgamentos retificados de denuncia para histórico.
   */
  private getJulgamentosRetificadosDenuncia = () => {
    if (this.julgamentoDenuncia && this.julgamentoDenuncia.retificacao && this.julgamentosRetificados == undefined) {
      this.denunciaService.getJulgamentosRetificadosDenuncia(this.dadosDenuncia.idDenuncia).subscribe((data) => {
        this.retificacoesCarregadasEvent.emit(data);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Inicializa a configuração do ckeditor para a Retificação.
   */
  private inicializaConfiguracaoCkeditorRetificacao = () => {
    this.configuracaoCkeditorRetificacao = {
      height: 115,
      title: 'dsRetificacao',
      removePlugins: 'elementspath',
      toolbar: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
        { name: 'links', items: ['Link'] },
        { name: 'insert', items: ['Image'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
      ],
    };
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    let idAbaAtiva = Constants.ABA_ATUAL;

    this.abas = {
      abaAtual: { id: Constants.ABA_ATUAL, nome: 'abaAtual' },
      abaRetificacoes: {
        id: Constants.ABA_RETIFICACAO,
        nome: 'retificacoes'
      }
    };

    this.mudarAba(idAbaAtiva);
  }

  /**
   * Verifica se o julgamento é improcedente
   */
  public isJulgamentoImprocedente() {
    return this.julgamentoDenuncia.idTipoJulgamento === Constants.TIPO_JULGAMENTO_IMPROCEDENTE;
  }

  /**
   * Verifica se o julgamento é procedente
   */
  public isJulgamentoProcedente() {
    return this.julgamentoDenuncia.idTipoJulgamento === Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
   * Verifica se a sentença é de multa.
   */
  public isSentencaMulta() {
    return this.julgamentoDenuncia.idTipoSentencaJulgamento === Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Verifica se a situação da denuncia não está em segunda instância e se o usúario é assessor CEN.
   */
  public isSituacaoJulgamentoPrimeiraInstancia() {
    return this.dadosDenuncia.isAssessorCEN && this.dadosDenuncia.hasEleicaoVigente
      && this.dadosDenuncia.idSituacaoDenuncia < Constants.SITUACAO_DENUNCIA_EM_JULGAMENTO_SEGUNDA_INSTANCIA
      && this.dadosDenuncia.idSituacaoDenuncia != Constants.SITUACAO_DENUNCIA_TRANSITADO_EM_JULGADO;
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalRetificacao(): void {
    const initialState = {
      retificacao: true,
      idDenuncia: this.dadosDenuncia.idDenuncia,
      nuDenuncia: this.dadosDenuncia.numeroDenuncia,
      tipoDenuncia: this.dadosDenuncia.idTipoDenuncia,
      julgamentoDenunciaRetificacao: this.julgamentoDenuncia,
    };

    this.modalJulgamentoPrimeiraInstancia = this.modalService.show(ModalJulgamentoPrimeiraInstanciaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  public mudarAba(aba: number): void {
    if(this.abas) {
      for(let tab in this.abas) {
        this.abas[tab].ativa = this.abas[tab].id === aba ? true : false;
      }
    }
  }
}
