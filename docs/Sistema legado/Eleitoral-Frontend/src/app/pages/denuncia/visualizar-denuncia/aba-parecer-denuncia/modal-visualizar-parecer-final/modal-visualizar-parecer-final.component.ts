import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { BsModalRef } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';

@Component({
  selector: 'app-modal-visualizar-parecer-final',
  templateUrl: './modal-visualizar-parecer-final.component.html',
  styleUrls: ['./modal-visualizar-parecer-final.component.scss']
})
export class ModalVisualizarParecerFinalComponent implements OnInit {

  @Input() public encaminhamentoParecerFinal: any;

  constructor(private denunciaService: DenunciaClientService,
    public modalRef: BsModalRef,
    private messageService: MessageService,
    private route: ActivatedRoute) { }

  ngOnInit() {

  }

  /**
  * Retorna se o julgamento é procedente ou não
  *
  */
  public isProcedente() {
    return this.encaminhamentoParecerFinal.idTipoJulgamento == Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
  * Retorna label de multa
  *
  */
  public getMulta() {
    return this.encaminhamentoParecerFinal.multa ? 'LABEL_SIM' : 'LABEL_NAO';
  }

  /**
  * Retorna a sentença de acordo com o id
  *
  */
  public getSentenca() {
    
    return (this.encaminhamentoParecerFinal.idTipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA ? 'LABEL_SENTENCA_ADVERTENCIA' :
      (this.encaminhamentoParecerFinal.idTipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA ? 'LABEL_SENTENCA_SUSPENSAO' :
        (this.encaminhamentoParecerFinal.idTipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_CASSACAO_REGISTRO_CANDIDATURA ? 'LABEL_SENTENCA_CASSACAO' :
          (this.encaminhamentoParecerFinal.idTipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_MULTA ? 'LABEL_SENTENCA_MULTA' :
            (this.encaminhamentoParecerFinal.idTipoSentencaJulgamento == Constants.TIPO_SENTENCA_JULGAMENTO_OUTRAS_ADEQ_PROPORC_GRAU_INFRAC_COMETIDA ? 'LABEL_SENTENCA_OUTRAS' : ''
            )

          )

        )
      )
    );
  }

/**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
 public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
  return this.denunciaService.downloadArquivoParecerFinal(idArquivo).subscribe((data: Blob) => {
    event.emit(data);
  }, error => {
    this.messageService.addMsgDanger(error);
  });
}


}
