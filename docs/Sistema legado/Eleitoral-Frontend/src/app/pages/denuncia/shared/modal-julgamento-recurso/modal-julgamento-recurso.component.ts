import { NgForm } from '@angular/forms';
import { CKEditor4 } from 'ckeditor4-angular';
import { BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output, ElementRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import * as _ from 'lodash';

@Component({
  selector: 'modal-julgamento-recurso',
  templateUrl: './modal-julgamento-recurso.component.html',
  styleUrls: ['./modal-julgamento-recurso.component.scss']
})
export class ModalJulgamentoRecursoComponent implements OnInit {

  @Input() idDenuncia: any;
  @Input() nuDenuncia: any;
  @Input() tipoDenuncia: any;
  @Input() retificacao?: any;
  @Input() julgamentoRecursoRetificacao?: any;

  public configuracaoCkeditor: any = {};
  public arquivo: any;
  public valorPercentualMulta: any;
  public julgamentoRecurso: any;
  public tiposJulgamento: any;
  public tiposSentencaJulgamento: any;
  public nomeArquivoJulgamento: string;
  public submitted: boolean = false;
  public qtDias: any;
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
    this.julgamentoRecurso = {
      arquivos: [],
      descricao: '',
      justificativa: '',
      arquivosExcluidos: [],
      valorPercentualMulta: '',
      tipoSentencaJulgamento: '',
      idDenuncia: this.idDenuncia,
      tipoJulgamentoDenunciado: '',
      tipoJulgamentoDenunciante: '',
      suspensaoPropaganda: {
        quantidadeDias: ''
      },
      descricaoJulgamentoSimpleText: [],
      justificativaRetificacaoJulgamentoSimpleText: [],
    };

    if (this.retificacao && this.julgamentoRecursoRetificacao) {
      let julgamentoRetificado = _.cloneDeep(this.julgamentoRecursoRetificacao);
      this.setDescricaoJulgamentoSimpleText(StringService.getPlainText(julgamentoRetificado.descricaoJulgamento));

      this.julgamentoRecurso.multa = julgamentoRetificado.multa;
      this.julgamentoRecurso.sancao = julgamentoRetificado.sancao;
      this.julgamentoRecurso.descricao = julgamentoRetificado.descricaoJulgamento;
      this.julgamentoRecurso.valorPercentualMulta = julgamentoRetificado.valorPercentualMulta;
      this.julgamentoRecurso.tipoSentencaJulgamento = julgamentoRetificado.idTipoSentencaJulgamento;
      this.julgamentoRecurso.tipoJulgamentoDenunciado = julgamentoRetificado.idTipoJulgamentoDenunciado;
      this.julgamentoRecurso.tipoJulgamentoDenunciante = julgamentoRetificado.idTipoJulgamentoDenunciante;
      this.julgamentoRecurso.suspensaoPropaganda.quantidadeDias = julgamentoRetificado.quantidadeDiasSuspensaoPropaganda || '';
      this.julgamentoRecurso.arquivos = this.getDadosArquivosRetificacao(julgamentoRetificado.arquivosJulgamentoRecursoDenuncia);
    }
  }

  /**
   * Altera o valor de inserir multa.
   */
  public limparCampos = () => {
    this.julgamentoRecurso.multa = undefined;
    this.julgamentoRecurso.valorPercentualMulta = '';
    this.julgamentoRecurso.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
  }

  /**
   * Altera o valor de inserir multa.
   */
  public changeTipoSentencaJulgamento = () => {
    this.limparCampos();
    this.julgamentoRecurso.tipoSentencaJulgamento = '';
  }

  /**
   * Salva o julgamento da denuncia
   *
   * @param form
   */
  public concluir = (form: NgForm) => {
    this.submitted = true;

    if(!this.julgamentoInformado()){
      this.messageService.addMsgDanger('Para finalizar o cadastro é obrigatório o preenchimento de um dos campos: Julgar Recurso Denunciante ou Julgar Recurso Denunciado');
      return;
    }

    let sendJulgamento = this.getDadosJulgamento();

    if (form.valid) {
      if (Number(sendJulgamento.valorPercentualMulta) > 300) {
        this.messageService.addConfirmYesNo('MSG_VALOR_PERCENTUAL_MULTA_EXCEDE_LIMITE_300', () => {
          this.salvar(sendJulgamento);
        }, () => {
          this.cancelarJulgamentoRecurso();
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
    this.denunciaService.salvarJulgamentoRecurso(sendJulgamento).subscribe((data) => {
      if (this.retificacao && this.julgamentoRecursoRetificacao){
          this.messageService.addMsgSuccess('MSG_DENUNCIA_ALTERADA_COM_EXITO', [this.nuDenuncia]);
      }else{
          this.messageService.addMsgSuccess('MSG_DENUNCIA_JULGADA_COM_EXITO', [this.nuDenuncia]);
      }
      this.cancelarJulgamentoRecurso();

      setTimeout(() => {
        document.location.reload();
      }, 3000);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Fecha a modal de julgamento do recurso
   */
  public cancelarJulgamentoRecurso() {
    this.modalRef.hide();
  }

  /**
   * Retorna a contagem de caracteres da descrição de julgamento.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.julgamentoRecurso.descricaoJulgamentoSimpleText.length;
  }

  /**
   * Retorna a contagem de caracteres da Justificativa de Retificação de julgamento.
   */
  public getContagemJustificativaRetificacaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.julgamentoRecurso.justificativaRetificacaoJulgamentoSimpleText.length;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice, idArquivo?) => {
    this.arquivo = null;
    this.julgamentoRecurso.arquivos.splice(indice, 1);

    if (this.retificacao && idArquivo) {
      this.julgamentoRecurso.arquivosExcluidos.push(idArquivo);
    }
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.julgamentoRecurso.arquivos.length == 5;
  }

  /**
   * Verifica se existe sanção.
   */
  public hasSancao = () => {
    return this.julgamentoRecurso.sancao;
  }

  /**
   * Reseta os campos abaixo da sanção.
   */
  public resetarCamposSancao = () => {
    this.limparCampos();
    this.julgamentoRecurso.tipoSentencaJulgamento = '';
  }

  /**
   * Verifica se o tipo de sentença de julgamento é multa.
   */
  public isTipoSentencaJulgamentoMulta = () => {
    return this.julgamentoRecurso.tipoSentencaJulgamento && this.julgamentoRecurso.tipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Verifica se o tipo de sentença de julgamento é suspensão de propaganda.
   */
  public isTipoSentencaJulgamentoSuspensaoPropaganda = () => {
    return this.julgamentoRecurso.tipoSentencaJulgamento && this.julgamentoRecurso.tipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA;
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

    if (this.julgamentoRecurso.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.julgamentoRecurso.arquivos.push(arquivoUpload);
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
    return this.denunciaService.downloadArquivoJulgamentoRecurso(idArquivo).subscribe((data: Blob) => {
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
    let valor = this.julgamentoRecurso.suspensaoPropaganda.quantidadeDias;

    if(Number(valor) > 10) {
      this.julgamentoRecurso.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
    }
  }

  /**
   * Limpa valor do campo valor percentual, quando selecionado "não"
   */
  public resetValorPercentual = () =>{
    let vlMulta = this.julgamentoRecurso.multa;
    if(!vlMulta){
        this.julgamentoRecurso.valorPercentualMulta = ''
    }
  }

  /**
   * Retorna apenas os dados necessários dos arquivos de retificação.
   */
  private getDadosArquivosRetificacao = (arquivosJulgamento: any) => {
    arquivosJulgamento.map(function(arquivoJulgamento: any) {
      delete arquivoJulgamento.julgamentoRecursoDenuncia;
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
    this.julgamentoRecurso.descricaoJulgamentoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Preenche texto da Justificativa de Retificação do julgamento em formato de texto simples.
   *
   * @param text
   */
  private setJustificativaRetificacaoJulgamentoSimpleText = (text: string) => {
    this.julgamentoRecurso.justificativaRetificacaoJulgamentoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna os dados de julgamento para persistência na base de dados.
   *
   * @return sendJulgamento
   */
  private getDadosJulgamento = () => {
    let sendJulgamento: any = {
      multa: this.julgamentoRecurso.multa,
      sancao: this.julgamentoRecurso.sancao,
      arquivosJulgamentoRecursoDenuncia: this.julgamentoRecurso.arquivos,
      descricao: this.julgamentoRecurso.descricao,
      idDenuncia: this.julgamentoRecurso.idDenuncia,
      valorPercentualMulta: this.julgamentoRecurso.valorPercentualMulta,
      tipoSentencaJulgamento: this.julgamentoRecurso.tipoSentencaJulgamento,
      tipoJulgamentoDenunciado: this.julgamentoRecurso.tipoJulgamentoDenunciado,
      tipoJulgamentoDenunciante: this.julgamentoRecurso.tipoJulgamentoDenunciante,
    };

    if (this.retificacao) {
      sendJulgamento.retificacao = true;
      sendJulgamento.idJulgamento = this.julgamentoRecursoRetificacao.id;
      sendJulgamento.justificativa = this.julgamentoRecurso.justificativa;

      if (this.julgamentoRecurso.arquivosExcluidos.length > 0) {
        sendJulgamento.arquivosExcluidos = this.julgamentoRecurso.arquivosExcluidos.join();
      }
    }

    if (this.isTipoSentencaJulgamentoSuspensaoPropaganda()) {
      sendJulgamento.quantidadeDiasSuspensaoPropaganda = this.julgamentoRecurso.suspensaoPropaganda.quantidadeDias;
    }

    return sendJulgamento;
  }

  private julgamentoInformado(){
    if(this.julgamentoRecurso.tipoJulgamentoDenunciado == "" && this.julgamentoRecurso.tipoJulgamentoDenunciante == ""){
      return false;
    } else {
      return true;
    }
  }
}
