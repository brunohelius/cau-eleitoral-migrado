import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable } from 'rxjs';
import { CorpoEmailClientService } from './corpo-email-client.service';
import { MessageService } from '@cau/message';

/**
 * Classe resolve responsável pela busca de corpo de e-mail por id.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class CorpoEmailClientResolve implements Resolve<any> {

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
        private corpoEmailService: CorpoEmailClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.corpoEmailService.getPorId(id).subscribe(
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