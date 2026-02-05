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
        let id = route.params["id"];
        return new Observable(observer => {
            this.substituicaoChapaService.getPedidosPorUf(id).subscribe(
                data => {    
                    let dados = {
                        pedidos: data,
                        idUf: id
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