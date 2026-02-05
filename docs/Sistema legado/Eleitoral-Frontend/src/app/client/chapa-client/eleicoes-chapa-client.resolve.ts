import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable } from 'rxjs';
import { CalendarioClientService } from '../calendario-client/calendario-client.service';
import { MessageService } from '@cau/message';


/**
 * Classe resolve responsável pela busca de Eleições com chapa.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class EleicoesChapaClientResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param corpoEmailService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private calendarioService: CalendarioClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let filtro = {listaChapas: true};
        return new Observable(observer => {
            this.calendarioService.getCalendariosPorFiltro(filtro).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    observer.error(error);
                    this.router.navigate([""]);
                    this.messageService.addMsgDanger(error);
                    this.messageService.addMsgDanger('MSG_ERRO_COMUNICACAO_TENTE_MAIS_TARDE');
                }
            );
        });
    }
}