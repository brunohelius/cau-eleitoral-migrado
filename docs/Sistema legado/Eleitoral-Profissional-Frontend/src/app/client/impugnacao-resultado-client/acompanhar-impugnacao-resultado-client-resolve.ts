import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { ImpugnacaoResultadoClientService } from './impugnacao-resultado-client.service';


@Injectable({   providedIn: 'root' })

export class AcompanharImpugnacaoResultadoClientResolve implements Resolve<any> {

    public user = this.securtyService.credential["_user"];
    public dados: {
        valor: null,
        cenBR: boolean
    }

    /**
     * Construtor da classe
     * @param router
     * @param messageService
     * @param securtyService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private acompanharImpugnacaoClientService: ImpugnacaoResultadoClientService,
    ) {
    }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {        
        return new Observable(observer => {
            this.acompanharImpugnacaoClientService.acompanharImpugnacaoResultado().subscribe(
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