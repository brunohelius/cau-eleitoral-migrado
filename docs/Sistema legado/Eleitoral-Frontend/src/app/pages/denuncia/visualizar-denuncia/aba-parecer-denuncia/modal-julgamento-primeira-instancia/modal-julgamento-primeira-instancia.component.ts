import * as _ from 'lodash';
import { NgForm } from '@angular/forms';
import { formatDate } from "@angular/common";
import { CKEditor4 } from 'ckeditor4-angular';
import { BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output, ElementRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';

@Component({
  selector: 'app-modal-julgamento-primeira-instancia',
  templateUrl: './modal-julgamento-primeira-instancia.component.html',
  styleUrls: ['./modal-julgamento-primeira-instancia.component.scss']
})
export class ModalJulgamentoPrimeiraInstanciaComponent implements OnInit {

  @Input() idDenuncia: any;
  @Input() nuDenuncia: any;
  @Input() tipoDenuncia: any;
  @Input() retificacao?: any;
  @Input() julgamentoDenunciaRetificacao?: any;

  public qtDias: any;
  public arquivo: any;
  public julgamento: any;
  public valorPercentual: any;
  public julgamentoDenuncia: any;
  public tiposJulgamento: any;
  public submitted: boolean = false;
  public configuracaoCkeditor: any = {};
  public nomeArquivoJulgamento: string;
  public tiposSentencaJulgamento: any;
  public configuracaoCkeditorJustificativaRetificacao: any = {};

  constructor(
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.setEstruturaDadosFormulario();
    this.inicializaConfiguracaoCkeditor();
    this.getTiposJulgamento();
    this.getTiposSentencaJulgamento();
    this.qtDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;

    if (this.retificacao) {
      this.inicializaConfiguracaoCkeditorJustificativaRetificacao();
    }
  }

  /**
   * Retorna a estrutura de dados do formulário.
   */
  private setEstruturaDadosFormulario = () => {
    this.julgamento = {
      arquivos: [],
      descricao: '',
      justificativa: '',
      tipoJulgamento: '',
      arquivosExcluidos: [],
      valorPercentualMulta: '',
      tipoSentencaJulgamento: '',
      idDenuncia: this.idDenuncia,
      suspensaoPropaganda: {
        quantidadeDias: ''
      },
      descricaoJulgamentoSimpleText: [],
      justificativaRetificacaoJulgamentoSimpleText: [],
    };

    if (this.retificacao && this.julgamentoDenunciaRetificacao) {
      let julgamentoRetificado = _.cloneDeep(this.julgamentoDenunciaRetificacao);
      this.setDescricaoJulgamentoSimpleText(StringService.getPlainText(julgamentoRetificado.descricaoJulgamento));

      this.julgamento.multa = julgamentoRetificado.multa;
      this.julgamento.descricao = julgamentoRetificado.descricaoJulgamento;
      this.julgamento.tipoJulgamento = julgamentoRetificado.idTipoJulgamento;
      this.julgamento.valorPercentualMulta = julgamentoRetificado.valorPercentualMulta;
      this.julgamento.tipoSentencaJulgamento = julgamentoRetificado.idTipoSentencaJulgamento;
      this.julgamento.arquivos = this.getDadosArquivosRetificacao(julgamentoRetificado.arquivosJulgamentoDenuncia);
      this.julgamento.suspensaoPropaganda.quantidadeDias = julgamentoRetificado.quantidadeDiasSuspensaoPropaganda || '';
    }
  }

  /**
   * Altera o valor de inserir multa.
   */
  public limparCampos = () => {
    this.julgamento.multa = undefined;
    this.julgamento.valorPercentualMulta = '';
    this.julgamento.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
  }

  /**
   * Altera o valor de inserir multa.
   */
  public changeTipoSentencaJulgamento = () => {
    this.limparCampos();
    this.julgamento.tipoSentencaJulgamento = '';
  }

  /**
   * Limpa valor do campo valor percentual, quando selecionado "não"
   */
  public resetValorPercentual = () =>{
    let vlMulta = this.julgamento.multa;
    if(!vlMulta){
        this.julgamento.valorPercentualMulta = ''
    }
  }

  /**
   * Salva o julgamento da denuncia
   *
   * @param form
   */
  public concluir = (form: NgForm) => {
    this.submitted = true;

    let sendJulgamento = this.getDadosJulgamento();

    if (form.valid) {
      if (Number(sendJulgamento.vlPercentualMulta) > 300) {
        this.messageService.addConfirmYesNo('MSG_VALOR_PERCENTUAL_MULTA_EXCEDE_LIMITE_300', () => {
          this.salvar(sendJulgamento);
        }, () => {
          this.cancelarJulgamentoDenuncia();
        });
        return;
      }

      this.salvar(sendJulgamento);
    }
  }

  /**
   * Salva o julgamento da denuncia
   *
   * @param sendJulgamento
   */
  public salvar = (sendJulgamento: {}) => {
    this.denunciaService.salvarJulgamentoDenuncia(sendJulgamento).subscribe((data) => {
      let msgSuccess = 'MSG_DENUNCIA_JULGADA_COM_EXITO';
      if (this.retificacao) {
        msgSuccess = 'MSG_DENUNCIA_ALTERADA_COM_EXITO';
      }

      this.messageService.addMsgSuccess(msgSuccess, [this.nuDenuncia]);
      this.cancelarJulgamentoDenuncia();

      setTimeout(() => {
        document.location.reload();
      }, 3000);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Fecha a modal de julgamento da denuncia
   */
  public cancelarJulgamentoDenuncia() {
    this.modalRef.hide();
  }

  /**
   * Retorna a contagem de caracteres da descrição de julgamento.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.julgamento.descricaoJulgamentoSimpleText.length;
  }

  /**
   * Retorna a contagem de caracteres da Justificativa de Retificação de julgamento.
   */
  public getContagemJustificativaRetificacaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.julgamento.justificativaRetificacaoJulgamentoSimpleText.length;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice, idArquivo?) => {
    this.arquivo = null;
    this.julgamento.arquivos.splice(indice, 1);

    if (this.retificacao && idArquivo) {
      this.julgamento.arquivosExcluidos.push(idArquivo);
    }
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.julgamento.arquivos.length == 5;
  }

  /**
   * Verifica se o tipo de julgamento é procedente.
   */
  public isTipoJulgamentoProcedente = () => {
    return this.julgamento.tipoJulgamento && this.julgamento.tipoJulgamento == Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
   * Verifica se o tipo de julgamento é improcedente.
   */
  public isTipoJulgamentoImprocedente = () => {
    return this.julgamento.tipoJulgamento && this.julgamento.tipoJulgamento == Constants.TIPO_JULGAMENTO_IMPROCEDENTE;
  }

  /**
   * Verifica se o tipo de sentença de julgamento é multa.
   */
  public isTipoSentencaJulgamentoMulta = () => {
    return this.julgamento.tipoSentencaJulgamento && this.julgamento.tipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Verifica se o tipo de sentença de julgamento é suspensão de propaganda.
   */
  public isTipoSentencaJulgamentoSuspensaoPropaganda = () => {
    return this.julgamento.tipoSentencaJulgamento && this.julgamento.tipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA;
  }

  /**
   * Busca os tipos de julgamento para seleção.
   */
  public getTiposJulgamento = () => {
    this.denunciaService.getTiposJulgamentoPorDenuncia().subscribe(data => {
      this.tiposJulgamento = data;
    },
    error => {
      this.messageService.addMsgWarning(error.message);
    });
  }

  /**
   * Busca os tipos de sentença do julgamento para seleção.
   */
  public getTiposSentencaJulgamento = () => {
    this.denunciaService.getTiposSentencaJulgamento().subscribe(data => {
      this.tiposSentencaJulgamento = data;
    },
    error => {
      this.messageService.addMsgWarning(error.message);
    });
  }

  /**
   * Método responsável por validar se cada arquivo submetido a upload
   * atende os critérios definidos para salvar os binários.
   *
   * @param arquivoEvent
   * @param calendario
   */
  public uploadDocumento = (arquivoEvent: any) => {
    let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size };
    let arquivoUpload = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, 'arquivo': arquivoEvent };

    if (this.julgamento.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.julgamento.arquivos.push(arquivoUpload);
        this.arquivo = arquivoEvent.name;
      },
        error => {
          this.messageService.addMsgWarning(error.message);
        });
    } else {
      this.messageService.addMsgWarning('MSG_QTD_MAXIMA_UPLOAD_5');
    }
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
   * Alterar valor da descrição do julgamento.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoJulgamentoSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
   * Alterar valor da descrição do julgamento.
   *
   * @param event
   */
  public onChangeCKJustificativaRetificacao = (event: CKEditor4.EventInfo) => {
    this.setJustificativaRetificacaoJulgamentoSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
   * Retorna texto de hint de informativo do documento.
   */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve o julgamento.
   *
   * @param event
   */
  public onReadyCKDescricaoJulgamento = (event: CKEditor4.EventInfo) => {
    event.editor.on('key', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxl)) {
        event2.cancel();
      }
    });
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve o justificativa Retificação.
   *
   * @param event
   */
  public onReadyCKDescricaoJustificativaRetificacao = (event: CKEditor4.EventInfo) => {
    event.editor.on('key', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxl)) {
        event2.cancel();
      }
    });
  }

  /**
   * Adiciona função keyup que verifica o valor numerico digitado.
   *
   * @param event
   */
  public onReadyValorNumerico = (event) => {
    let valor: any = event.target.value;

    if(valor) {
      valor = Number(valor).toString();

      let caracteres = valor.split('');
      if(0 == Number(caracteres[0])) {
        caracteres.shift();
      }

      valor = caracteres.join('');
    }

    event.target.value = valor || '';
  }

  public limitQtDiasSuspensaoPropaganda = () => {
    let valor = this.julgamento.suspensaoPropaganda.quantidadeDias;
    
    if(Number(valor) > 10) {
      this.julgamento.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
    }
  }

  /**
   * Retorna apenas os dados necessários dos arquivos de retificação.
   */
  private getDadosArquivosRetificacao = (arquivosJulgamento: any) => {
    arquivosJulgamento.map(function(arquivoJulgamento: any) {
      delete arquivoJulgamento.julgamentoDenuncia;
      return arquivoJulgamento;
    });

    return arquivosJulgamento;
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'narracaoFatos',
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
   * Inicializa a configuração do ckeditor da Justificativa da Retificação.
   */
  private inicializaConfiguracaoCkeditorJustificativaRetificacao = () => {
    this.configuracaoCkeditorJustificativaRetificacao = {
      height: 115,
      title: 'justificativaRetificacao',
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
   * Preenche texto de descrição do julgamento em formato de texto simples.
   *
   * @param text
   */
  private setDescricaoJulgamentoSimpleText = (text: string) => {
    this.julgamento.descricaoJulgamentoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Preenche texto da Justificativa de Retificação do julgamento em formato de texto simples.
   *
   * @param text
   */
  private setJustificativaRetificacaoJulgamentoSimpleText = (text: string) => {
    this.julgamento.justificativaRetificacaoJulgamentoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna os dados de julgamento para persistência na base de dados.
   * 
   * @return sendJulgamento
   */
  private getDadosJulgamento = () => {
    let sendJulgamento: any = {
      multa: this.julgamento.multa,
      arquivos: this.julgamento.arquivos,
      descricao: this.julgamento.descricao,
      idDenuncia: this.julgamento.idDenuncia,
      tpJulgamento: this.julgamento.tipoJulgamento,
      vlPercentualMulta: this.julgamento.valorPercentualMulta,
      tpSentencaJulgamento: this.julgamento.tipoSentencaJulgamento,
    };

    if (this.retificacao) {
      sendJulgamento.retificacao = true;
      sendJulgamento.justificativa = this.julgamento.justificativa;

      if (this.julgamento.arquivosExcluidos.length > 0) {
        sendJulgamento.arquivosExcluidos = this.julgamento.arquivosExcluidos.join();
      }
    }

    if (this.isTipoSentencaJulgamentoSuspensaoPropaganda()) {
      sendJulgamento.qtDiasSuspensaoPropaganda = this.julgamento.suspensaoPropaganda.quantidadeDias;
    }

    return sendJulgamento;
  }
}
