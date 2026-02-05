import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { ComissaoEleitoralService } from './comissao-eleitoral-client.service'
import { MessageService } from '@cau/message'

@Injectable({
  providedIn: 'root'
})
export class ComissaoEleitoralResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * 
   * @param router 
   * @param comissaoEleitoralService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private comissaoEleitoralService: ComissaoEleitoralService,
    private messageService: MessageService
  ) { }
  
    /**
     * 
     * @param route 
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
          this.comissaoEleitoralService.getComissoaoEleitoral(id).subscribe(
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
