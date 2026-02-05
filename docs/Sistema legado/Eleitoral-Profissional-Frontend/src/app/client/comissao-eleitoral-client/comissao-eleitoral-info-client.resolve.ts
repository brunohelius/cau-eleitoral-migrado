import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { ComissaoEleitoralService } from './comissao-eleitoral-client.service'
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'

@Injectable({
  providedIn: 'root'
})
export class ComissaoEleitoralInfoResolve implements Resolve<any> {

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
    private messageService: MessageService,
    private securtyService: SecurityService
  ) { }
  
    /**
     * 
     * @param route 
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        const user = this.securtyService.credential["_user"];
        return new Observable(observer => {
          this.comissaoEleitoralService.getInformacaoComissaoMembro(user.idProfissional).subscribe(
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
