import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { ConviteMembroComissaoService } from './convite-membro-comissao-client.service'

@Injectable({
  providedIn: 'root'
})
export class ConviteMembroComissaoResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * 
   * @param router 
   * @param comissaoEleitoralService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private conviteComissaoEleitoralService: ConviteMembroComissaoService,
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
          this.conviteComissaoEleitoralService.getDeclaracaoPorIdProfissional(user.idProfissional).subscribe(
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
