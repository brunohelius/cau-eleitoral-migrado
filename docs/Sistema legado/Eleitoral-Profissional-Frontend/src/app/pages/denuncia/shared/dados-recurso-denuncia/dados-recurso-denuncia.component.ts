import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'dados-recurso-denuncia',
  templateUrl: './dados-recurso-denuncia.component.html',
  styleUrls: ['./dados-recurso-denuncia.component.scss']
})
export class DadosRecursoDenunciaComponent implements OnInit {

  @Input() recurso: any;
  @Input('descricao') descricao;

  public configuracaoCkeditor: any = {};
  public descricaoRecursoSimpleText = '';

  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    this.descricaoRecursoSimpleText = StringService.getPlainText(this.recurso.descricao).slice(0, -1);
  }

  /**
   * Retorna a contagem de caracteres da descrição do recurso.
   */
  public getContagemDescricaoRecurso = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoRecursoSimpleText.length ;
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoRecursoContrarrazao(idArquivo).subscribe((data: Blob) => {
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
      title: 'dsRecurso',
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
