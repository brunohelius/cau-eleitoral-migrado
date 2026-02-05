import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';

@Injectable({
    providedIn: 'root'
})
export class UfImpugnacaoResultadoResolve implements Resolve<any> {

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
        private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService,
    ) {

    }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.impugnacaoResultadoClientService.getCauUfImpugnacaoResultado().subscribe(
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