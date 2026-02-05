import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { EleicaoClientService } from './eleicao-client.service'
import { MessageService } from '@cau/message'
import { Constants } from 'src/app/constants.service'

@Injectable({
  providedIn: 'root'
})
export class EleicoesConcluidasInativasClientResolve implements Resolve<any> {
   /**
   * Construtor da classe.
   * 
   * @param router 
   * @param eleicaoClientService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private eleicaoClientService: EleicaoClientService,
    private messageService: MessageService
  ) { }
    
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
          this.eleicaoClientService.getEleicoesFilter({"situacoes" : [Constants.ELEICAO_CONCLUIDA, Constants.ELEICAO_INATIVA]}).subscribe(
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