import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { ImpugnacaoCandidaturaClientService } from './impugnacao-candidatura-client.service';


@Injectable()
export class AcompanharImpugnacaoUfClientResolve implements Resolve<any> {

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
     * @param impugnacaoService 
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private impugnacaoService: ImpugnacaoCandidaturaClientService,
    ) {
        
    }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idCauUf = route.params["id"];
        let idCalendario = route.params["idCalendario"];
        return new Observable(observer => {
            this.impugnacaoService.pedidosImpugnacaoCalendarioUf(idCalendario, idCauUf).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    this.messageService.addMsgDanger(error);
                    this.router.navigate([`/`]);
                }
            );
        });
    }

}