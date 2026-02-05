import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable,of } from 'rxjs';
import { AtividadePrincipalClientService } from './atividade-principal-client.service';
import { MessageService } from '@cau/message';


/**
 * Classe resolve responsável pela busca das informações de Atividade Principal.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class AtividadesPrincipaisClientResolve implements Resolve<any> {
    
    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param cabecalhoEmailService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private atividadePrincipalService: AtividadePrincipalClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.atividadePrincipalService.getAtividadesPrincipais().subscribe(
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