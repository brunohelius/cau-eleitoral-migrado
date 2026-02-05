import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { DenunciaClientService } from './denuncia-client.service'
import { MessageService } from '@cau/message'

@Injectable({
  providedIn: 'root'
})
export class TipoDenunciaClientResolve implements Resolve<any> {
  /**
  * Construtor da classe.
  *
  * @param router
  * @param messageService
  * @param denunciaClientService
  */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private denunciaClientService: DenunciaClientService,
  ) { }

  /**
   * Metodo que realiza a chamada do serviço para recuperar
   * uma lista dos anos de eleições que estejam na situação de concluidas ou inativas
   *
   * @param route
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    return new Observable(observer => {
      this.denunciaClientService.getTiposDenuncia().subscribe(
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