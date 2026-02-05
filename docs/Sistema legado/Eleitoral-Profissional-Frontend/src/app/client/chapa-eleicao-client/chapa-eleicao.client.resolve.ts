import { Observable } from "rxjs"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { ChapaEleicaoClientService } from './chapa-eleicao-client.service';
import { Injectable } from '@angular/core';

@Injectable()
export class ChapaEleicaoClientResolve implements Resolve<any> {
  
  /**
   * Construtor da classe.
   *
   * @param router
   * @param ChapaEleicaoClientService
   * @param messageService
   */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private chapaEleicaoService: ChapaEleicaoClientService
  ) { }

  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let id = route.params["id"];
    return new Observable(observer => {
      this.chapaEleicaoService.getChapaPorId(id).subscribe(
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