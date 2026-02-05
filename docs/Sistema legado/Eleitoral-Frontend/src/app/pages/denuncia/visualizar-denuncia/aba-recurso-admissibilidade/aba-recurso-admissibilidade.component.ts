
import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {Constants} from '../../../../constants.service';
import {StringService} from '../../../../string.service';
import {DenunciaClientService, StatusDenuncia} from '../../../../client/denuncia-client/denuncia-client.service';
import {MessageService} from '@cau/message';
import { SecurityService } from '@cau/security';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ModalJulgamentoRecursoAdmissibilidadeComponent } from '../aba-julgamento-recurso-admissibilidade/modal-julgamento-recurso-admissibilidade/modal-julgamento-recurso-admissibilidade.component';
import * as moment from 'moment'

@Component({
  selector: 'app-aba-recurso-admissibilidade',
  templateUrl: './aba-recurso-admissibilidade.component.html',
  styleUrls: ['./aba-recurso-admissibilidade.component.scss']
})
export class AbaRecursoAdmissibilidadeComponent implements OnInit {

  @Input() denuncia;
  public modalJulgarRecursoAdmissibilidade: BsModalRef;

  configuracaoCkeditor = {
    title: 'dsAdmissibilidade',
    removePlugins: 'elementspath',
    toolbar: [
      { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
      { name: 'links', items: ['Link'] },
      { name: 'insert', items: ['Image'] },
      { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
    ],
  };

  public descricaoDespachoSimpleText = '';

  public dataCriacao = '';

  constructor(
    private denunciaService: DenunciaClientService,
    private messageService: MessageService,
    private modalService:  BsModalService,
    private securityService: SecurityService
  ) { }

  ngOnInit() {

    if(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade !== undefined) {
      this.descricaoDespachoSimpleText = StringService.getPlainText(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.descricao).slice(0, -1);

      this.dataCriacao = moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.dataCriacao).format('DD/MM/YYYY');
      this.dataCriacao += ' às '
      this.dataCriacao += moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.dataCriacao).format('HH:mm');
    }
  }

  /**
   * Verifica se o recurso está apto a ser julgado
   * @returns boolean
   */
  public hasHabilitadoJulgarRecursoAdmissibilidade() {
    return this.denuncia.isAssessorCEN && (this.denuncia.idSituacaoDenuncia == StatusDenuncia.JULGAMENTO_RECURSO_ADMISSIBILIDADE.id)
  }

  public getContagemRecursoAdmissibilidade() {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoDespachoSimpleText.length;
  }

  public downloadArquivo(event: EventEmitter<any>, idArquivo) {
    return this.denunciaService.downloadArquivoRecursoJulgamentoAdmissibilidade(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  public voltar() {
    window.history.back();
  }

  public julgarRecursoAdmissibilidade(){
    const initialState = {
        denuncia: this.denuncia,
    };

    this.modalJulgarRecursoAdmissibilidade = this.modalService.show(ModalJulgamentoRecursoAdmissibilidadeComponent,
        Object.assign({}, {}, { class: 'modal-lg', initialState }));

    this.modalJulgarRecursoAdmissibilidade.content.cadastroRecursoJulgamentoAdmissibilidade.subscribe(event => {
        setTimeout(function(){
          window.location.reload();
        }, 2000);
    });
}
}
