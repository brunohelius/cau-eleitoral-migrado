import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { SubstiuicaoChapaClientService } from './substituicao-chapa-client.module';

@Injectable({
    providedIn: 'root'
})
export class AtividadeSecundariaRecursoClientResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param substituicaoChapaService
     * @param messageService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private substituicaoChapaService: SubstiuicaoChapaClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.substituicaoChapaService.getAtividadeSecundariaRecursoPorSubstituicao(id).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        });
    }

}