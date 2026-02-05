import { Injectable } from '@angular/core';
import { Router, Resolve, ActivatedRouteSnapshot } from '@angular/router';
import { CalendarioClientService } from './calendario-client.service';
import { MessageService } from '@cau/message';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CalendarioHistoricoResolve implements Resolve<any> {

  constructor(
    private router: Router,
    private messageService: MessageService,
    private calendarioClientService: CalendarioClientService
  ) { }

  /**
   * Resolver responsável por recuperar o histórico do calendário selecionado.
   *
   * @param route
   */
  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let id = route.params["id"];

    return new Observable(observer => {
      this.calendarioClientService.getHistorico(id).subscribe(
        data => {
          observer.next(data);
          observer.complete();
        },
        error => {
          observer.error(error);
          this.router.navigate([""]);
          this.messageService.addMsgDanger(error);
        }
      );
    });
  }
}
