
import {Component, OnInit, Input, EventEmitter} from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import * as moment from 'moment';


@Component({
  selector: 'app-aba-julgamento-recurso-admissibilidade',
  templateUrl: './aba-julgamento-recurso-admissibilidade.component.html',
  styleUrls: ['./aba-julgamento-recurso-admissibilidade.component.scss']
})
export class AbaJulgamentoRecursoAdmissibilidadeComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;

  public configuracaoCkeditor: any = {};

  public dataCriacao:string;

  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    if(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso !== undefined) {
      this.dataCriacao = moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.dataCriacao).format('DD/MM/YYYY');
      this.dataCriacao += ' às '
      this.dataCriacao += moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.dataCriacao).format('HH:mm');
    }
  }


  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoJulgamentoRecursoAdmissibilidade(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR - this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.descricao.length;
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
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }
}
