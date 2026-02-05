import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter } from '@angular/core';


import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-modal-visualizar-impedimento-suspeicao',
  templateUrl: './modal-visualizar-impedimento-suspeicao.component.html',
  styleUrls: ['./modal-visualizar-impedimento-suspeicao.component.scss']
})
export class ModalVisualizarImpedimentoSuspeicaoComponent implements OnInit {

  @Input() public encaminhamentoDenuncia: any;

  constructor(

    public modalRef: BsModalRef,
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService

  ) { }

  ngOnInit() {}

    /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
 public downloadArquivoEncaminhamentoDenuncia = (event: EventEmitter<any>, idArquivo) => {
  return this.denunciaService.downloadArquivoEncaminhamentoDenuncia(idArquivo).subscribe((data: Blob) => {
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
 public downloadArquivoAdmitida = (event: EventEmitter<any>, idArquivo) => {

  return this.denunciaService.downloadArquivoAdmitida(idArquivo).subscribe((data: Blob) => {
    event.emit(data);
  }, error => {
    this.messageService.addMsgDanger(error);
  });
}


  /**
   * Verifica se o Status do Encaminhamento é Concluído.
   */
  public isStatusEncaminhamentoConcluido() {
    return this.encaminhamentoDenuncia.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_CONCLUIDO;
  }

  /**
   * Verifica se o Status do Encaminhamento é Pendente.
   */
  public isStatusEncaminhamentoPendente() {
    return this.encaminhamentoDenuncia.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_PENDENTE;
  }


}
