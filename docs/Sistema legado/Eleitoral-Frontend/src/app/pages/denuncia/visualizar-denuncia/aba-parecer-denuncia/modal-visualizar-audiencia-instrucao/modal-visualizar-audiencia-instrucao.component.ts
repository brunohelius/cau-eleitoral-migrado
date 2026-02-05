import { Component, EventEmitter, OnInit, Input } from '@angular/core';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { BsModalRef } from 'ngx-bootstrap';

@Component({
  selector: 'app-modal-visualizar-audiencia-instrucao',
  templateUrl: './modal-visualizar-audiencia-instrucao.component.html',
  styleUrls: ['./modal-visualizar-audiencia-instrucao.component.scss']
})
export class ModalVisualizarAudienciaInstrucaoComponent implements OnInit {

  @Input() public encaminhamentoAudiencia: any;

  constructor(private denunciaService: DenunciaClientService,
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private route: ActivatedRoute) { }

  ngOnInit() {

  }

  /**
   * Verifica se o encaminhamento está com status concluído
   */

  public isConluido() {
    return this.encaminhamentoAudiencia.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_CONCLUIDO;
  }

  /**
   * Verifica se o encaminhamento está com status fechado
   * 
   */
  public isFechado() {
    return this.encaminhamentoAudiencia.tipoSituacaoEncaminhamento.id == Constants.STATUS_ENCAMINHAMENTO_FECHADO;
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivo(idArquivo).subscribe((data: Blob) => {
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
  public downloadArquivoAudienciaInstrucao = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoAudienciaInstrucao(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
