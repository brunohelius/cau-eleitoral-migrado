import { Component, OnInit, Input, EventEmitter } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'aba-detail-acompanhar-denuncia',
  templateUrl: './aba-detail-acompanhar-denuncia.component.html',
  styleUrls: ['./aba-detail-acompanhar-denuncia.component.scss']
})
export class AbaDetailAcompanharDenunciaComponent implements OnInit {

  public configuracaoCkeditor: any = {};
  public narracaoFatosSimpleText = '';

  @Input('denuncia') denuncia;
  @Input('tipoDenuncia') tipoDenuncia;

  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
  ) { 

  }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    this.narracaoFatosSimpleText = StringService.getPlainText(this.denuncia.narracaoFatosSimpleText).slice(0, -1);
  }

  /**
   * Válida se o tipo da denúncia for Outros.
   */
  public isTipoDenunciaOutros = () => {
    return this.denuncia.denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS;
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

  /**
   * Valida se o denunciado é valido
   */
  public isDenunciadoValido = () => {
    let denunciado = this.denuncia.denunciado;

    return denunciado && (
      (denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA && denunciado.uf && denunciado.chapa)
      || ((denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_CHAPA || denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO)
        && denunciado.uf && denunciado.membro)
      || (denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS && denunciado.uf)
    );
  }

  /**
   * Retorna a contagem de caracteres da narração de fatos.
   */
  public getContagemNarracaoFatos = () => {
    return Constants.TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA - this.narracaoFatosSimpleText.length;
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param idArquivo
  * @param event
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

}
