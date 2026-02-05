import * as _ from "lodash";
import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { CalendarioClientService } from '../calendario-client/calendario-client.service'

@Injectable({
  providedIn: 'root'
})
export class EleicoesAnosConcluidasClientResolve implements Resolve<any> {

  /**
   * Construtor da classe.
   * 
   * @param router 
   * @param eleicaoClientService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private calendarioClientService: CalendarioClientService,
  ) { }
    
  /**
   * Recupera os anos as quais existem calendários/eleições concluídos.
   * 
   * @param route 
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    return new Observable(observer => {
      this.calendarioClientService.getCalendariosConcluidosAnos().subscribe(data => {
        data = _.orderBy(data, ['eleicao'], ['asc']);
        observer.next(data);
        observer.complete();
      }, error => {
        observer.error(error);
        this.messageService.addMsgDanger(error);
      });
    });
  }
}



