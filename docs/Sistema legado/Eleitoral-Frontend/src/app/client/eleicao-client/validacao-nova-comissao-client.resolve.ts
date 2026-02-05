import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { EleicaoClientService } from './eleicao-client.service'
import { MessageService } from '@cau/message'

@Injectable({
  providedIn: 'root'
})
export class ValidacaoNovaComissaoClientResolve implements Resolve<any> {

   /**
   * Construtor da classe.
   * 
   * @param router 
   * @param calendarioClientService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private eleicaoClientService: EleicaoClientService,
    private messageService: MessageService
  ) { }

  resolve(route: ActivatedRouteSnapshot): Observable<any> {

    return new Observable(observer => {
      let idCalendario = route.params['id'];
      this.eleicaoClientService.validarMembrosComissaoExistentePorCalendarioUsuario(idCalendario).subscribe(
        data => {
          observer.next(data);
          observer.complete();
        },
        error => {
          // @TODO - Interromper os próximos 'resolves'.
          observer.error(error);
          this.messageService.addMsgDanger(error);
        }
      );
    });
  }
}
