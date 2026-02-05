import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { MessageService } from '@cau/message'
import { ProfissionalClientService } from './profissional-client.service'

@Injectable({
  providedIn: 'root'
})
export class ProfissionalResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * 
   * @param router 
   * @param ProfissionalClientService 
   * @param messageService 
   */
  constructor(
    private router: Router,
    private profissionalService: ProfissionalClientService,
    private messageService: MessageService
  ) { }

  /**
   * 
   * @param route 
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let cpf = route.params['cpf'];
    return new Observable(observer => {
      this.profissionalService.getProfissional(cpf).subscribe(
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
