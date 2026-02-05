import {Component, OnInit, Input, EventEmitter, OnChanges, Output} from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { StringService } from 'src/app/string.service';
import { ConfigCardListInterface } from 'src/app/shared/component/card-list/config-card-list-interface';
import { ModalJulgamentoRecursoComponent } from 'src/app/pages/denuncia/shared/modal-julgamento-recurso/modal-julgamento-recurso.component';

@Component({
  selector: 'app-segunda-instancia',
  templateUrl: './segunda-instancia.component.html',
  styleUrls: ['./segunda-instancia.component.scss']
})
export class SegundaInstanciaComponent implements OnChanges, OnInit {

  @Input() dadosDenuncia;
  @Input('julgamentosRetificados') julgamentosRetificados?;
  @Input() visualizacaoRetificacao? = false;

  @Output('onRetificacoesCarregadas') retificacoesCarregadasEvent: EventEmitter<any> = new EventEmitter();
  @Output() fecharModalRetificacaoEvent: EventEmitter<any> = new EventEmitter();

  public abas: any = {};
  public julgamentoRecurso: any;

  public descricaoJulgamentoSimpleText = '';
  public descricaoRetificacaoSimplexText = '';

  public configuracaoCkeditor: any = {};
  public infoJulgamento: ConfigCardListInterface;
  public configuracaoCkeditorRetificacao: any = {};

  public modalJulgamentoSegundaInstancia: BsModalRef;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) {}

  ngOnInit() {
    this.julgamentoRecurso = this.getJulgamentoRecurso(this.dadosDenuncia);
    this.inicializaConfiguracaoCkeditor();
    this.inicializaConfiguracaoCkeditorRetificacao();

    if (this.julgamentoRecurso) {
      this.carregarInfoJulgamento();

      this.descricaoRetificacaoSimplexText = StringService.getPlainText(this.julgamentoRecurso.justificativa).slice(0, -1);
      this.descricaoJulgamentoSimpleText = StringService.getPlainText(this.julgamentoRecurso.descricaoJulgamento).slice(0, -1);
      this.getRecursoJulgamentosRetificadosDenuncia();
    }

    this.inicializaAbas();
  }

    ngOnChanges() {
        this.getRecursoJulgamentosRetificadosDenuncia();
    }

  /**
   * Recupera o julgamento do recurso de acordo com o ator que efetuou o pedido de recurso.
   *
   * @param denuncia
   */
  private getJulgamentoRecurso(denuncia: any): void {
    if (denuncia.recursoDenunciado) {
      return denuncia.recursoDenunciado.julgamentoRecurso;
    }

    return denuncia.recursoDenunciante.julgamentoRecurso;
  }

  /**
   * Carrega as informações de julgamento.
   */
  public carregarInfoJulgamento():void {
    let data = {
      'sancao' : this.julgamentoRecurso.descricaoSancao,
      'julgamento_denunciado' : this.julgamentoRecurso.descricaoTipoJulgamentoDenunciado,
      'julgamento_denunciante' : this.julgamentoRecurso.descricaoTipoJulgamentoDenunciante,
    };

    this.infoJulgamento = {
      header: [
        {
          'field': 'julgamento_denunciante',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_DENUNCIANTE')
        },
        {
          'field': 'julgamento_denunciado',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_DENUNCIADO')
        },
        {
          'field': 'sancao',
          'header': this.messageService.getDescription('TITLE_SANCAO_APLICADA')
        }
      ],
      data: []
    };

    if(this.isSancao()) {
      data['sentenca'] = this.julgamentoRecurso.descricaoTipoSentencaJulgamento;
      this.infoJulgamento.header.push(
        {
          'field': 'sentenca',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_COMISSAO')
        }
      );

      if(this.julgamentoRecurso.quantidadeDiasSuspensaoPropaganda) {
        data['qtdDias'] = this.julgamentoRecurso.quantidadeDiasSuspensaoPropaganda;
        this.infoJulgamento.header.push(
          {
            'field': 'qtdDias',
            'header': this.messageService.getDescription('TITLE_QTDE_DIAS')
          }
        );
      }

      if(this.julgamentoRecurso.multa) {
        data['multa'] = `${this.julgamentoRecurso.valorPercentualMulta}% ${this.messageService.getDescription('LABEL_MULTA_ANUIDADE')}`
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
    return this.denunciaService.downloadArquivoJulgamentoRecurso(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres da descrição do julgamento.
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
   * Retorna os recurso julgamentos retificados de denuncia para histórico.
   */
  private getRecursoJulgamentosRetificadosDenuncia = () => {
    if (this.julgamentoRecurso && this.julgamentoRecurso.retificacao && this.julgamentosRetificados == undefined) {
      this.denunciaService.getRecursoJulgamentosRetificadosDenuncia(this.dadosDenuncia.idDenuncia).subscribe((data) => {
        this.retificacoesCarregadasEvent.emit(data);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }


  /**
   * Fecha o modal de Visualização de retificação do Julgamento.
   */
  public fecharModalVisualizacaoRetificacao = () => {
    this.fecharModalRetificacaoEvent.emit(true);
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
   * Verifica se o julgamento é improcedente
   */
  public isJulgamentoImprocedente() {
    return this.julgamentoRecurso.idTipoJulgamento === Constants.TIPO_JULGAMENTO_IMPROCEDENTE;
  }

  /**
   * Verifica se o julgamento é procedente
   */
  public isJulgamentoProcedente() {
    return this.julgamentoRecurso.idTipoJulgamento === Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
   * Verifica se sanção foi aplicada.
   */
  public isSancao() {
    return this.julgamentoRecurso.sancao;
  }

  /**
   * Verifica se a sentença é de multa.
   */
  public isSentencaMulta() {
    return this.julgamentoRecurso.idTipoSentencaJulgamento === Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

   /**
   * Verifica se a situação da eleição a qual está vinculada está vigente e se o usúario é assessor CEN.
   */
  public isSituacaoJulgamentoSegundaInstancia() {
    return this.dadosDenuncia.isAssessorCEN && this.dadosDenuncia.hasEleicaoVigente;
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
      julgamentoRecursoRetificacao: this.julgamentoRecurso,
    };

    this.modalJulgamentoSegundaInstancia = this.modalService.show(ModalJulgamentoRecursoComponent,
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
