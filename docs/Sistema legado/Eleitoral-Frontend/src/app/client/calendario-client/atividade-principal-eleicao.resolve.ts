import { Observable } from "rxjs";
import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { CalendarioClientService } from './calendario-client.service';
import { MessageService } from '@cau/message';

/**
 * Classe resolve responsável pela busca das informações de atividade principal e secundária por Calendário.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class ListAtividadePrincipalEleicaoResolve implements Resolve<any> {
    /**
     * Construtor da classe.
     * @param route 
     * @param calendarioClientService 
     * @param messageService 
     */
    constructor(
        private router: Router,
        private calendarioClientService: CalendarioClientService,
        private messageService: MessageService
    ) {

    }

    /**
     * @param route
    */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idCalendario = route.params["id"];

        return new Observable(observer => {
            this.calendarioClientService.getAtividadesPorCalendario(idCalendario).subscribe(
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