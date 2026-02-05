import { Component, OnInit, Input } from '@angular/core';
import { BsModalRef } from 'ngx-bootstrap';
import { CKEditor4 } from 'ckeditor4-angular';
import { StringService } from 'src/app/string.service';
import { Constants } from 'src/app/constants.service';
import { NgForm } from '@angular/forms';

@Component({
  selector: 'modal-justificativa-encaminhamento',
  templateUrl: './modal-justificativa-encaminhamento.component.html',
  styleUrls: ['./modal-justificativa-encaminhamento.component.scss']
})
export class ModalJustificativaEncaminhamentoComponent implements OnInit {

  public submitted: boolean = false;
  public configuracaoCkeditor: any = {};
  public justificativaEncaminhamento = '';
  public justificativaEncaminhamentoSimpleText: any = [];

  constructor(
    public modalRef: BsModalRef
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Salva a justificativa do encaminhamento de defesa da denuncia
   *
   * @param form
   */
  public salvar = (form: NgForm) => {
    this.submitted = true;

    if (form.valid) {
      this.cancelarJustificativaEncaminhamento();
    }
  }

  /**
   * Fecha a modal de justificativa do encaminhamento de defesa
   */
  public cancelarJustificativaEncaminhamento(isButtonCancel: boolean = false) {
    if (isButtonCancel) {
      this.justificativaEncaminhamento = '';
      this.justificativaEncaminhamentoSimpleText = [];
    }

    this.modalRef.hide();
  }

  /**
   * Alterar valor da justificativa do encaminhamento da defesa.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setJustificativaEncaminhamentoDefesaSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
   * Retorna a contagem de caracteres da encaminhamento da defesa.
   */
  public getContagemJustificativaEncaminhamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.justificativaEncaminhamentoSimpleText.length;
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve a justificativa do encaminhamento da defesa.
   *
   * @param event
   */
  public onReadyCKJustificativa = (event: CKEditor4.EventInfo) => {
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
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'justificativa',
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
   * Preenche texto da justificativa do encaminhamento da defesa em formato de texto simples.
   *
   * @param text
   */
  private setJustificativaEncaminhamentoDefesaSimpleText = (text: string) => {
    this.justificativaEncaminhamentoSimpleText = StringService.getPlainText(text).slice(0, -1);
  }
}
