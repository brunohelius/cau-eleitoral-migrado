import {Component, EventEmitter, Input, OnInit} from '@angular/core';
import {BsModalRef, BsModalService} from 'ngx-bootstrap';

import {MessageService} from '@cau/message';
import {Constants} from 'src/app/constants.service';
import {DenunciaClientService} from 'src/app/client/denuncia-client/denuncia-client.service';
import {StringService} from 'src/app/string.service';
import { ModalJulgarAdmissibilidadeComponent } from '../modal-julgar-admissibilidade/modal-julgar-admissibilidade.component';
import { ConfigCardListInterface } from 'src/app/shared/component/card-list/config-card-list-interface';

@Component({
  selector: 'detalhes-analise',
  templateUrl: './detalhes-analise.component.html',
  styleUrls: ['./detalhes-analise.component.scss']
})
export class DetalhesAnaliseComponent implements OnInit {

  @Input() denuncia;
  @Input() analiseAdmissibilidade: any = {};
  @Input() hasAnaliseAdmissaoInadmissao?: any = false;

  public configuracaoCkeditor: any = {};
  public infoRelator: ConfigCardListInterface;
  public infoCoordenador: ConfigCardListInterface;
  public despachoSimpleText = '';
  public modalAnalisarDenuncia: BsModalRef;

  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
    private modalService: BsModalService
  ) { }

  ngOnInit() {

    this.inicializaConfiguracaoCkeditor();

    this.despachoSimpleText = StringService.getPlainText(this.analiseAdmissibilidade.despacho).slice(0, -1);

    if (this.analiseAdmissibilidade.admitida) {
      this.carregarInformacoesMembros();
    }else {
      this.carregarInformacoesCoordenador();
    }
  }

  public julgarAdmissibilidade() {
      const initialState = {
          denuncia: this.denuncia,
      };

      this.modalAnalisarDenuncia = this.modalService.show(ModalJulgarAdmissibilidadeComponent,
          Object.assign({}, {}, { class: 'modal-lg', initialState }));
      this.modalAnalisarDenuncia.content.event.subscribe(event => {
          this.denuncia.condicao.posso_julgar = false;
          this.denuncia.condicao.existe_julgamento = true;
      });
  }

  /**
   * Carrega as informações dos membros.
   */
  public carregarInformacoesMembros = () => {

    let relator = this.analiseAdmissibilidade.relator.profissional;
    let coordenador = this.denuncia.coordenadorComissao.name;

    this.infoRelator = {
      header: [{
        'field': 'relator',
        'header': this.messageService.getDescription('LABEL_RELATOR')
      },
      {
        'field': 'coordenador',
        'header': this.messageService.getDescription('LABEL_COORDENADOR_COMISSAO')
      }],
      data: [{
        relator: relator.nome,
        coordenador: coordenador
      }]
    };
  }

  /**
   * Carrega as informações do coordenador.
   */
  public carregarInformacoesCoordenador = () => {

    let coordenador = this.denuncia.coordenadorComissao.name;

    this.infoCoordenador = {
      header: [{
        'field': 'coordenador',
        'header': this.messageService.getDescription('LABEL_COORDENADOR_COMISSAO')
      }],
      data: [{
        coordenador: coordenador
      }]
    };
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoInadmitida(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }


  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDespachoAdmissibilidade = () => {
    return Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR - this.despachoSimpleText.length;
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
}
