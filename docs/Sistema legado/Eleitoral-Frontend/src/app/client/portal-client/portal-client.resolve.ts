import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { PortalClientService } from './portal-client.service'
import { MessageService } from '@cau/message';

@Injectable({
  providedIn: 'root'
})
export class PortalResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * 
   * @param router 
   * @param PortalClientService 
   * @param messageService 
   */
  constructor(
    private portalClientService: PortalClientService,
    private messageService: MessageService
  ) { }

  /**
   * 
   * @param route 
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    return new Observable(observer => {
      this.portalClientService.getDeclaracoes().subscribe(
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
