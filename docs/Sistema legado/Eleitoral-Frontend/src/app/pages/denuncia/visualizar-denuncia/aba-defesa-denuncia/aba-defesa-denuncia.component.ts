import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'aba-defesa-denuncia',
  templateUrl: './aba-defesa-denuncia.component.html',
  styleUrls: ['./aba-defesa-denuncia.component.scss']
})
export class AbaDefesaDenunciaComponent implements OnInit {
  
  @Input('dadosDenuncia') denuncia;

  public descricaoDefesaSimpleText = '';
  public configuracaoCkeditor: any = {};

  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    if (this.denuncia.defesaApresentada != undefined) {
      this.descricaoDefesaSimpleText = StringService.getPlainText(this.denuncia.defesaApresentada.descricaoDefesa).slice(0, -1);
    }
  }

  /**
   * Verifica se existe parametros para ver a defesa.
   */
  public hasParametrosSemPossibilidadeDefesa() {
    return this.denuncia.idTipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS 
      || this.denuncia.defesaApresentada == undefined;
  }

  /**
   * Retorna a contagem de caracteres da descrição da defesa.
   */
  public getContagemDescricaoDefesa = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoDefesaSimpleText.length;
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoDefesa(idArquivo).subscribe((data: Blob) => {
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
      title: 'dsAdmissibilidade',
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
   * Verifica os parametros para analise de defesa
   */
  public hasParametrosAcessoParaAnalise = () => {
    return 
        this.denuncia.hasDefesaPrazoEncerrado
        && this.denuncia.idSituacaoDenuncia == Constants.SITUACAO_DENUNCIA_EM_RELATORIA
        && !this.denuncia.hasImpedimentoSuspeicaoPendente;
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }
}