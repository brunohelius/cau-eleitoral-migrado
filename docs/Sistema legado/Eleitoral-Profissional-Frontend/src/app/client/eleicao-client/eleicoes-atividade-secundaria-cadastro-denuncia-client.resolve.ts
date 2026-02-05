import { Observable } from "rxjs"
import { Resolve } from "@angular/router"
import { Injectable } from "@angular/core"

import { MessageService } from '@cau/message'
import { Constants } from 'src/app/constants.service'
import { EleicaoClientService } from './eleicao-client.service'

@Injectable({
  providedIn: 'root'
})
export class AtividadeSecundariaCadastroDenunciaClientResolve implements Resolve<any> {

  /**
  * Construtor da classe.
  *
  * @param eleicaoClientService
  * @param messageService
  */
  constructor(
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
  ) { }

  /**
   * Metodo que realiza a chamada do serviço para recuperar a atividade secundária vigente.
   *
   * @param route
   */
  resolve(): Observable<any> {
    let principal = Constants.TIPO_ATIVIDADE_PRINCIPAL_DENUNCIA;
    let secundaria = Constants.TIPO_ATIVIDADE_SECUNDARIA_DENUNCIA;

    return new Observable(observer => {
      this.eleicaoClientService.getAtividadeSecundariaVigente({ principal, secundaria }).subscribe(
        data => {
          observer.next(data);
          observer.complete();
        },
        error => {
          observer.error(error);
          this.messageService.addMsgDanger(error);
        }
      );
    });
  }
}