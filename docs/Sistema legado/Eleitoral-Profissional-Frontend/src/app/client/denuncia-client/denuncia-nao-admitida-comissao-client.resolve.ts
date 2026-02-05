import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { MessageService } from '@cau/message'

import { DenunciaClientService } from './denuncia-client.service'

@Injectable({
  providedIn: 'root'
})
export class DenunciaNaoAdmitidaComissaoRecebidasClientResolve implements Resolve<any> {

  /**
   * Construtor da classe
   *
   * @param router
   * @param denunciaService
   * @param messageService
   */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
  ) { }

  /**
   *
   * @param route
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    return new Observable(observer => {
      this.denunciaService.getDenunciaComissaoAdmissibilidade().subscribe(
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
