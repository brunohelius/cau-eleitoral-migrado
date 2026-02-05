import { Observable } from "rxjs";
import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { CalendarioClientService } from './calendario-client.service';
import { MessageService } from '@cau/message';
import {Constants} from 'src/app/constants.service';

@Injectable({
    providedIn: 'root'
})
export class CalendariosConcluidosResolve implements Resolve<any>{

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
        this.calendarioClientService.getCalendariosPorFiltro({ 'situacoes': [Constants.CALENDARIO_SITUACAO_CONCLUIDO]}).subscribe(
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
