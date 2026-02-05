import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { JulgamentoFinalClientService } from './julgamento-final-client.service';

@Injectable({
    providedIn: 'root'
})
export class RecursoJulgamentoFinalResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param julgamentoFinalClientService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private julgamentoFinalClientService: JulgamentoFinalClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {

        let idChapaEleicao = route.params["idChapa"];

        return new Observable(observer => {
            this.julgamentoFinalClientService.getRecursoJulgamentoFinal(idChapaEleicao).subscribe(
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