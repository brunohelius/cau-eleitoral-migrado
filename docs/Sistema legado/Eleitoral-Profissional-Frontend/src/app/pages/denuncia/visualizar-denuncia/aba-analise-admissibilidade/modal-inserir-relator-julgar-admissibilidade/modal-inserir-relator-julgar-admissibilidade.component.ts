import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {MessageService} from '@cau/message';
import {BsModalRef} from 'ngx-bootstrap';
import {DenunciaClientService} from '../../../../../client/denuncia-client/denuncia-client.service';
import {NgForm} from '@angular/forms';
import {CKEditor4} from 'ckeditor4-angular';
import {Constants} from '../../../../../constants.service';
import {StringService} from '../../../../../string.service';
import {Router} from '@angular/router';

@Component({
  selector: 'app-modal-inserir-relator-julgar-admissibilidade',
  templateUrl: './modal-inserir-relator-julgar-admissibilidade.component.html',
  styleUrls: ['./modal-inserir-relator-julgar-admissibilidade.component.scss']
})
export class ModalInserirRelatorJulgarAdmissibilidadeComponent implements OnInit {
  public model = {
    despacho: null,
    relator: null,
    arquivos: []
  };
  public nomeArquivo;
  public submitted = false;
  public simpleText = '';
  @Output() public event: EventEmitter<any> = new EventEmitter();
  @Input() public denuncia;
  @Input() public relatores: Array<{id, nome, registro, email}>;
  public relatorAtivo: any = '';

  public editorParams = {
    title: 'narracaoFatos',
    removePlugins: 'elementspath',
    toolbar: [
      {
        name: 'basicstyles',
        groups: ['basicstyles', 'cleanup'],
        items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-']
      },
      {name: 'links', items: ['Link']},
      {name: 'insert', items: ['Image']},
      {
        name: 'paragraph',
        groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
        items: [
          '-',
          'Outdent',
          'Indent',
          '-',
          'JustifyLeft',
          'JustifyCenter',
          'JustifyRight',
          'JustifyBlock',
          '-',
          'BidiLtr',
          'BidiRtl',
          'Language'
        ]
      },
    ],
  };

  constructor(
    private messageService: MessageService,
    public modalRef: BsModalRef,
    private denunciaService: DenunciaClientService,
    private router: Router,
  ) { }

  public ngOnInit() {
  }

  public submit(form: NgForm) {
    this.submitted = true;
    if (form.valid) {
      this.denunciaService.submitRelator(this.denuncia.idDenuncia, this.model).subscribe(data => {
        this.messageService.addMsgSuccess('MSG_RELATOR_SUCESSO', [this.denuncia.numeroDenuncia]);
        setTimeout(() => {
          window.location.reload();
        }, 3000);
      }, error => {
        this.messageService.addMsgDanger(error);
      });

    }
  }

  public onReadyCKEditor(event: CKEditor4.EventInfo) {
    event.editor.on('key', event2 => {
      const maxLength = Constants.TAMANHO_LIMITE_2000;
      const simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxLength) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', event2 => {
      const maxLength = Constants.TAMANHO_LIMITE_2000;
      const simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxLength)) {
        event2.cancel();
      }
    });
  }

  public onChangeCKDescricao(event: CKEditor4.EventInfo) {
    this.setDescricaoDesignacaoRelatorSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  private setDescricaoDesignacaoRelatorSimpleText = (text: string) => {
    this.simpleText = StringService.getPlainText(text).slice(0, -1);
  }

  public getSimpleTextLength() {
    return Constants.TAMANHO_LIMITE_2000 - this.simpleText.length;
  }

  public isQuantidadeUploadMaxima() {
    return this.model.arquivos.length === 5;
  }

  public getHintInformativoDocumento() {
    return this.messageService.getDescription('MSG_HINT_UPLOAD_RELATOR');
  }

  public getHintRelator() {
    return this.messageService.getDescription('MSG_HINT_RELATOR');
  }

  public excluiUpload(indice) {
    this.model.arquivos.splice(indice, 1);
  }

  public verifyFiles(arquivoEvent: any) {
    const arquivoUpload = {nome: arquivoEvent.name, tamanho: arquivoEvent.size, arquivo: arquivoEvent};

    if (this.model.arquivos.length < 5) {
      if (!/(.*?)\.(pdf|zip|rar|doc|docx|xls|xlsx|mp4|avi|wmv|mp3|wav|jpg|jpeg|png)/i.test(arquivoUpload.nome)) {
        this.messageService.addMsgWarning('LABEL_ARQUIVO_INVALIDO');
        return;
      }
      if (arquivoUpload.tamanho / Math.pow(1024, 2) > 25) {
        this.messageService.addMsgWarning('LABEL_ARQUIVO_GRANDE');
        return;
      }
      this.model.arquivos.push(arquivoUpload);
      this.nomeArquivo = arquivoEvent.nome;
    } else {
      this.messageService.addMsgWarning('MSG_QTD_MAXIMA_UPLOAD');
    }
  }

  public selectRelator() {
    this.model.relator = this.relatorAtivo.id;
  }

  /**
   * Retorna o registro nacional formatado.
   */
  public getRegistroNacionalFormatado(registroNacional: string) {
    return registroNacional.replace(/^0+/, '');
  }
}
