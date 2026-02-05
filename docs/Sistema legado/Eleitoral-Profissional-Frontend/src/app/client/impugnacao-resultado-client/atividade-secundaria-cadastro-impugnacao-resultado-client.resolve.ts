import { Observable, empty } from "rxjs"
import { Resolve, Router } from "@angular/router"
import { Injectable } from "@angular/core"

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { EleicaoClientService } from '../eleicao-client/eleicao-client.service';
import { unwatchFile } from 'fs';

@Injectable({
  providedIn: 'root'
})
export class AtividadeSecundariaCadastroImpugnacaoResolve implements Resolve<any> {

  /**
  * Construtor da classe.
  *
  * @param eleicaoClientService
  * @param messageService
  */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
  ) { }

  /**
   * Metodo que realiza a chamada do serviço para recuperar a atividade secundária vigente.
   *
   * @param route
   */
  resolve(): Observable<any> {
    let principal = Constants.TIPO_ATIVIDADE_PRINCIPAL_IMPUGNACAO_RESULTADO;
    let secundaria = Constants.TIPO_ATIVIDADE_SECUNDARIA_CADASTRO_IMPUGNACAO_RESULTADO;

    return new Observable(observer => {
      this.eleicaoClientService.getAtividadeSecundariaVigente({ principal, secundaria }).subscribe(
        data => {
          if (data.id == undefined) {
            this.messageService.addMsgDanger(this.messageService.getDescription('MGS_IMPEDITIVA_ATIVIDADE_SECUNDARIA_CADASTRO_IMPUGNACAO_RESULTADO'));
            this.router.navigate(['/']);
          }
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