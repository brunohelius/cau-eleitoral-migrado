import { Observable } from "rxjs";
import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { CalendarioClientService } from './calendario-client.service';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';

@Injectable({
  providedIn: 'root'
})
export class CalendariosAnoAtualResolve implements Resolve<any>{

  /**
* Construtor da classe.
*
* @param router
* @param calendarioClientService
* @param messageService
*/
  constructor(
    private router: Router,
    private calendarioClientService: CalendarioClientService,
    private messageService: MessageService
  ) { }

  /**
   * @param route
  */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    return new Observable(observer => {
      this.calendarioClientService.getCalendariosPorFiltro({"listaDenuncias" : true}).subscribe(
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
