import { NgForm } from '@angular/forms';
import { formatDate } from "@angular/common";
import { CKEditor4 } from 'ckeditor4-angular';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output, ElementRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-modal-parecer-final',
  templateUrl: './modal-parecer-final.component.html',
  styleUrls: ['./modal-parecer-final.component.scss']
})
export class ModalParecerFinalComponent implements OnInit {

  @Input() idDenuncia: any;
  @Input() nuDenuncia: any;
  @Input() tipoDenuncia: any;

  public configuracaoCkeditor: any = {};
  public arquivo: any;
  public valorPercentual: any;
  public parecerFinal: any;
  public tiposJulgamento: any;
  public tiposSentencaJulgamento: any;
  public nomeArquivoParecerFinal: string;
  public submitted: boolean = false;
  public qtDias: any;

  constructor(
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
    private router : Router
  ) { }

  ngOnInit() {
    this.setEstruturaDadosFormulario();
    this.inicializaConfiguracaoCkeditor();
    this.getTiposJulgamento();
    this.getTiposSentencaJulgamento();
    this.qtDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
  }

  /**
   * Retorna a estrutura de dados do formulário.
   */
  private setEstruturaDadosFormulario = () => {
    this.parecerFinal = {
      arquivos: [],
      descricao: '',
      tipoJulgamento: '',
      valorPercentualMulta: '',
      tipoSentencaJulgamento: '',
      idDenuncia: this.idDenuncia,
      suspensaoPropaganda: {
        quantidadeDias: ''
      },
      descricaoParecerFinalSimpleText: [],
    };
  }

  /**
   * Altera o valor de inserir multa.
   */
  public limparCampos = () => {
    this.parecerFinal.multa = undefined;
    this.parecerFinal.valorPercentualMulta = '';
    this.parecerFinal.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
  }

  /**
   * Altera o valor de inserir multa.
   */
  public changeTipoSentencaJulgamento = () => {
    this.limparCampos();
    this.parecerFinal.tipoSentencaJulgamento = '';
  }

  /**
   * Salva o parecer final
   *
   * @param form
   */
  public concluir = (form: NgForm) => {
    this.submitted = true;

    let sendParecerFinal = this.getDadosParecerFinal();

    if (form.valid) {
      this.salvar(sendParecerFinal);
    }
  }

  /**
   * Salva o parecer final
   *
   * @param sendParecerFinal
   */
  public salvar = (sendParecerFinal: {}) => {
    this.denunciaService.salvarParecerFinalDenuncia(sendParecerFinal).subscribe((data) => {
      this.messageService.addMsgSuccess('MSG_PARECER_FINAL_CADASTRADA_COM_EXITO', [this.nuDenuncia]);
      this.cancelarParecerFinalDenuncia();

      setTimeout(() => {
        this.router.navigate(['denuncia/comissao', 'visualizar', this.idDenuncia, 'tipoDenuncia', this.tipoDenuncia]);
      }, 1000);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Fecha a modal de parecer final da denuncia
   */
  public cancelarParecerFinalDenuncia() {
    this.modalRef.hide();
  }

  /**
   * Retorna a contagem de caracteres da descrição de julgamento.
   */
  public getContagemDescricaoParecerFinal = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.parecerFinal.descricaoParecerFinalSimpleText.length;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice) => {
    this.arquivo = null;
    this.parecerFinal.arquivos.splice(indice, 1);
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.parecerFinal.arquivos.length == 5;
  }

  /**
   * Verifica se o tipo de julgamento é procedente.
   */
  public isTipoJulgamentoProcedente = () => {
    return this.parecerFinal.tipoJulgamento && this.parecerFinal.tipoJulgamento.id == Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
   * Verifica se o tipo de julgamento é improcedente.
   */
  public isTipoJulgamentoImprocedente = () => {
    return this.parecerFinal.tipoJulgamento && this.parecerFinal.tipoJulgamento.id == Constants.TIPO_JULGAMENTO_IMPROCEDENTE;
  }

  /**
   * Verifica se o tipo de sentença de julgamento é multa.
   */
  public isTipoSentencaJulgamentoMulta = () => {
    return this.parecerFinal.tipoSentencaJulgamento && this.parecerFinal.tipoSentencaJulgamento.id == Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Verifica se o tipo de sentença de julgamento é suspensão de propaganda.
   */
  public isTipoSentencaJulgamentoSuspensaoPropaganda = () => {
    return this.parecerFinal.tipoSentencaJulgamento && this.parecerFinal.tipoSentencaJulgamento.id == Constants.TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA;
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

    if (this.parecerFinal.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.parecerFinal.arquivos.push(arquivoUpload);
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
   * Alterar valor da descrição do parecer final.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoParecerFinalSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
   * Retorna texto de hint de informativo do documento.
   */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve o parecer final.
   *
   * @param event
   */
  public onReadyCKDescricaoParecerFinal = (event: CKEditor4.EventInfo) => {
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
      let valor = this.parecerFinal.suspensaoPropaganda.quantidadeDias;
      
      if(Number(valor) > 10) {
        this.parecerFinal.suspensaoPropaganda.quantidadeDias = Constants.PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA;
      }
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
   * Preenche texto de descrição do parecer final em formato de texto simples.
   *
   * @param text
   */
  private setDescricaoParecerFinalSimpleText = (text: string) => {
    this.parecerFinal.descricaoParecerFinalSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna os dados do parecer final para persistência na base de dados.
   * 
   * @return sendParecerFinal
   */
  private getDadosParecerFinal = () => {
    let sendParecerFinal: any = {
      multa: this.parecerFinal.multa,
      arquivosParecerFinal: this.parecerFinal.arquivos,
      descricao: this.parecerFinal.descricao,
      idDenuncia: this.parecerFinal.idDenuncia,
      idTipoJulgamento: this.parecerFinal.tipoJulgamento.id,
      vlPercentualMulta: this.parecerFinal.valorPercentualMulta,
      idTipoSentencaJulgamento: this.parecerFinal.tipoSentencaJulgamento.id,
    };

    if (this.isTipoSentencaJulgamentoSuspensaoPropaganda()) {
      sendParecerFinal.qtDiasSuspensaoPropaganda = this.parecerFinal.suspensaoPropaganda.quantidadeDias;
    }

    return sendParecerFinal;
  }

}
