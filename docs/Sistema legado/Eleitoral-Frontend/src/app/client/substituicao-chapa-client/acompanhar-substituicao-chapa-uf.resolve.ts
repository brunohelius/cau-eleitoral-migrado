import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { SubstiuicaoChapaClientService } from './substituicao-chapa-client.module';

@Injectable()
export class SubstituicaoChapaUfClientResolve implements Resolve<any> {

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
        private substituicaoChapaService: SubstiuicaoChapaClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idCauUf = route.params["id"];
        let idCalendario = route.params["idCalendario"];
        return new Observable(observer => {
            this.substituicaoChapaService.getPedidosPorUfCalendario(idCalendario, idCauUf).subscribe(
                data => {    
                    let dados = {
                        pedidos: data,
                        idUf: idCauUf
                    }
                    observer.next(dados);
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