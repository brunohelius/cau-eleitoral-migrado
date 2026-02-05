import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { AcompanharJulgamentoSubstituicaoClient } from './acompanhar-julgamento-substituicao-client.service';



@Injectable()
export class AcompanharJuglamentoSubstituicaoComissaoResolve implements Resolve<any> {

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
        private acompanharJulgamentoSubstituicaoClient: AcompanharJulgamentoSubstituicaoClient
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["idPedido"];
        return new Observable(observer => {
            this.acompanharJulgamentoSubstituicaoClient.julgamentoSubstituicaoComissao(id).subscribe(
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