import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { Constants } from 'src/app/constants.service';
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';

@Injectable()
export class JulgamentoAlegacaoImpugnacaoResultadoClientResolve implements Resolve<any> {

  public user: any;

  /**
   * Construtor da classe
   * @param router
   * @param messageService
   * @param securtyService
   * @param impugnacaoService
   */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private securtyService: SecurityService,
    private impugnacaoResultado: ImpugnacaoResultadoClientService,
  ) {
    this.user = this.securtyService.credential["_user"];
  }

  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let idImpugnacao = route.params["id"];
    return new Observable(observer => {

      this.impugnacaoResultado.getJulgamentoAlegacaoImpugnacaoResultado(idImpugnacao).subscribe(
        data => {
          observer.next(data);
          observer.complete();
        },
        error => {
          this.messageService.addMsgDanger(error);
          this.router.navigate(['/']);
        }
      );
    });
  }

}