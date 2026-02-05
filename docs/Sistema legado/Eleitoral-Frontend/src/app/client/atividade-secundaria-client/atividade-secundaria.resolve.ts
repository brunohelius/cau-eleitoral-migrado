import { Observable } from "rxjs";
import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { MessageService } from '@cau/message';
import { AtividadeSecundariaClientService } from './atividade-secundaria-client.service';

/**
 * Classe resolve responsável pela busca das informações da ação conforme o id.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class AtividadeSecundariaResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param calendarioClientService
     * @param messageService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private atividadeSecundariaService: AtividadeSecundariaClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];

        return new Observable(observer => {
            this.atividadeSecundariaService.getPorId(id).subscribe(
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
