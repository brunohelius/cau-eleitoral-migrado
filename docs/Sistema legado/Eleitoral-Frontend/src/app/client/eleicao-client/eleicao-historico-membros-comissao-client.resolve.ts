import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { EleicaoClientService } from './eleicao-client.service'
import { MessageService } from '@cau/message'

@Injectable({
  providedIn: 'root'
})
export class EleicaoHistoricoMembrosComissao implements Resolve<any> {
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
    
  /**
   * Metodo que realiza a chamada do serviço para recuperar 
   * uma lista dos anos de eleições que estejam na situação de concluidas ou inativas
   * @param route 
   */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params['idComissao'];
        return new Observable(observer => {
          this.eleicaoClientService.getHistoricoMembrosComissao(id).subscribe(
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