import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Constants } from 'src/app/constants.service';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';



@Injectable({
  providedIn: 'root'
})
export class UfEspecificaImpugnacaoResultadoResolve implements Resolve<any> {

  /**
   * Construtor da classe
   * @param router
   * @param messageService
   * @param securtyService
   * @param substituicaoChapaService
   */
  constructor(
    private router: Router,
    private messageService: MessageService,
    private securtyService: SecurityService,
    private impugnacaoService: ImpugnacaoResultadoClientService,
  ) {

  }

  resolve(route: ActivatedRouteSnapshot): Observable<any> {
    let idCauUf = route.params['idCauUf'] ? route.params['idCauUf'] : 0;
    let idCalendario = route.params['idCalendario'] ? route.params['idCalendario'] : 0;

    return new Observable(observer => {
      this.impugnacaoService.getPorUfECalendario(idCauUf, idCalendario).subscribe(
        data => {
          observer.next(data);
          observer.complete();
        },
        error => {
          observer.error(error);
          this.messageService.addMsgDanger(error);
          this.router.navigate(['/']);
        }
      );
    });
  }

}