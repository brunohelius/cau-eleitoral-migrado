import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { StringService } from 'src/app/string.service';
import { CKEditor4 } from 'ckeditor4-angular';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-modal-contrarrazao',
  templateUrl: './modal-contrarrazao.component.html',
  styleUrls: ['./modal-contrarrazao.component.scss']
})
export class ModalContrarrazaoComponent implements OnInit {

  public configuracaoCkeditor: any = {};
  public arquivo: any;
  public contrarrazao: any;
  public submitted: boolean = false;
  public nomeArquivoContrarrazao: string;
  @Input() idDenuncia: any;
  @Input() nuDenuncia: any;
  @Input() tipoDenuncia: any;

  constructor(
    private router: Router,
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.contrarrazao = this.getEstruturaDadosFormulario();
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Retorna a estrutura de dados do formulário.
   */
  private getEstruturaDadosFormulario = () => {
    return {
      descricao: '',
      arquivoContrarrazao: [],
      idDenuncia: this.idDenuncia, 
      contrarrazaoSimpleText: []
    };
  }

  /**
   * Salva a contrarrazao da denuncia
   *
   * @param form
   */
  public concluir = (form: NgForm) => {
    this.submitted = true;

    const sendContrarrazao = {
      descricao: this.contrarrazao.descricao,
      idDenuncia: this.contrarrazao.idDenuncia,
      arquivosContrarrazao: this.contrarrazao.arquivoContrarrazao
    };

    if (form.valid) {
      this.denunciaService.salvarContrarrazao(sendContrarrazao).subscribe((data) => {
        this.messageService.addMsgSuccess('MSG_CONTRARRAZAO_CADASTRADA_COM_EXITO', [this.nuDenuncia]);
        this.cancelarContrarrazao();

        setTimeout(() => {
          document.location.reload();
        }, 3000);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Fecha a modal de  Contrarrazao
   */
  public cancelarContrarrazao() {
    this.modalRef.hide();
  }

  /**
   * Retorna a contagem de caracteres da contrarrazao.
   */
  public getContagemContrarrazao = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.contrarrazao.contrarrazaoSimpleText.length;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice) => {
    this.arquivo = null;
    this.contrarrazao.arquivoContrarrazao.splice(indice, 1);
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.contrarrazao.arquivoContrarrazao.length == 5;
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

    if (this.contrarrazao.arquivoContrarrazao.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.contrarrazao.arquivoContrarrazao.push(arquivoUpload);
        this.arquivo = arquivoEvent.name;
      },
        error => {
          this.messageService.addMsgWarning(error.message);
        });
    } else {
      this.messageService.addMsgWarning('MSG_QTD_MAXIMA_UPLOAD');
    }
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivo(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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
 * Alterar valor da descrição da  Contrarrazao.
 *
 * @param event
 */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoContrarrazaoSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
  * Preenche texto de descrição da  contrarrazao em formato de texto simples.
  *
  * @param text
  */
  private setDescricaoContrarrazaoSimpleText = (text: string) => {
    this.contrarrazao.contrarrazaoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna texto de hint de informativo do documento.
   */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve a contrarrazao.
   *
   * @param event
   */
  public onReadyCKContrarrazao = (event: CKEditor4.EventInfo) => {
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
}
