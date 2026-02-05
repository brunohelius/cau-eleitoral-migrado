import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, EventEmitter, OnInit, Input } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { Router } from '@angular/router';

@Component({
  selector: 'modal-visualizar-producao-provas',
  templateUrl: './modal-visualizar-producao-provas.component.html',
  styleUrls: ['./modal-visualizar-producao-provas.component.scss']
})

export class ModalVisualizarProducaoProvasComponent implements OnInit {

  //public producaoProvas: any;

  @Input() producaoProvas: any;

  //@Output('fecharEncaminhamento') fecharEncaminhamentoEvent: EventEmitter<any> = new EventEmitter();

  constructor(
    private router: Router,
    public modalRef: BsModalRef,
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    //this.getEncaminhamentoProvas();
  }

  /**
   * 
   */
  /*private getEncaminhamentoProvas = () => {
    this.denunciaService.getEncaminhamentosProvas(this.idEncaminhamentoDenuncia).subscribe((data) => {
      this.producaoProvas = data;
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }*/

  /**
   * Verifica se o Status do Encaminhamento é Fechado.
   */
  public isStatusEncaminhamentoFechado() {
    return this.producaoProvas.encaminhamento.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_FECHADO;
  }

  /**
   * Verifica se o Status do Encaminhamento é Transcorrido.
   */
  public isStatusEncaminhamentoTranscorrido() {
    return this.producaoProvas.encaminhamento.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_TRANSCORRIDO;
  }

    /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
 public downloadArquivoProvas = (event: EventEmitter<any>, idArquivo) => {
  return this.denunciaService.downloadArquivoProvas(idArquivo).subscribe((data: Blob) => {
    event.emit(data);
  }, error => {
    this.messageService.addMsgDanger(error);
  });
}

/**
* Retorna o arquivo conforme o id do Arquivo Informado
*
* @param event
* @param idArquivo
*/
public downloadArquivoEncaminhamento = (event: EventEmitter<any>, idArquivo) => {
return this.denunciaService.downloadArquivoEncaminhamento(idArquivo).subscribe((data: Blob) => {
  event.emit(data);
}, error => {
  this.messageService.addMsgDanger(error);
});
}
}
