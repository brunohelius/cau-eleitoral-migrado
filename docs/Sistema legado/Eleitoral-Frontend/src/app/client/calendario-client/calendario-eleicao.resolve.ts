import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { CalendarioClientService } from '../calendario-client/calendario-client.service'

@Injectable({
  providedIn: 'root'
})
export class CalendarioEleicaoResolve implements Resolve<any> {

  /**
   * Construtor da classe.
   * 
   * @param router 
   * @param calendarioService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private calendarioService: CalendarioClientService,
    private messageService: MessageService
  ) { }
    
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let idCalendario = parseInt(route.params['id']);

    return new Observable(observer => {
      this.calendarioService.getCalendarioEleicaoPorId(idCalendario).subscribe(data => {
        observer.next(data);
        observer.complete();
      }, error => {
        observer.error(error);
        this.messageService.addMsgDanger(error);
      });
    });
  }
}