import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { MessageService } from '@cau/message'
import { UFClientService } from './uf-client.service';


@Injectable({
  providedIn: 'root'
})
export class UFClientResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * 
   * @param router 
   * @param UFClientService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private ufService: UFClientService,
    private messageService: MessageService
  ) { }
  
    /**
     * Retorna Lista de UFs.
     * @param route 
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
          this.ufService.getUFs().subscribe(
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
