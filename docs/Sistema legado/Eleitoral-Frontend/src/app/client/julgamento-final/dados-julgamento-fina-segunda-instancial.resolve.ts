import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { JulgamentoFinalClientService } from './julgamento-final-client.service';

@Injectable()
export class DadosJulgamentoFinalSegundaInstanciaResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     * @param router
     * @param calendarioClientService
     * @param messageService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private julgamentoFinalClientService: JulgamentoFinalClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {

        let idChapa = route.params["idChapa"];
        
        return new Observable(observer => {
            this.julgamentoFinalClientService.getJulgamentoSegundaInstanciaPorChapa(idChapa).subscribe(
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