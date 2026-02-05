import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable } from 'rxjs';
import { ChapaEleicaoClientService } from './chapa-eleicao-client.service';
import { MessageService } from '@cau/message';

/**
 * Classe resolve responsável pela busca de histórico de chapas.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class HistoricoChapaEleicaoClientResolve  implements Resolve<any> {

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
        private chapaEleicaoService: ChapaEleicaoClientService
    ) { }

    /**
     * @param route
     */
    public resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.chapaEleicaoService.getHistoricoChapaPorCalendario(id).subscribe(
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