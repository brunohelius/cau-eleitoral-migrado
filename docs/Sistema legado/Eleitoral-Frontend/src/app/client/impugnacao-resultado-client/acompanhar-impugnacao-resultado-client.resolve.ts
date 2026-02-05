import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';

@Injectable()
export class AcompanharImpugnacaoResultadoClientResolve implements Resolve<any> {

    public user: any;

    /**
     * Construtor da classe
     * @param router
     * @param messageService
     * @param securtyService
     * @param impugnacaoService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
    ) {}

    resolve(route: ActivatedRouteSnapshot): Observable<any> {

        let idCalendario = route.params["id"];
        return new Observable(observer => {

            this.impugnacaoService.pedidosImpugnacaoResultado(idCalendario).subscribe(
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