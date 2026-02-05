import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { AcompanharSubstituicaoDetalhamentoService } from './acompanhar-substituicao-detalhamento-client.service';


@Injectable()
export class AcompanharSubstituicaoDetalhamentoResolve implements Resolve<any> {

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
        private acompanharSubstituicaoDetalhamentoService: AcompanharSubstituicaoDetalhamentoService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.acompanharSubstituicaoDetalhamentoService.pedidosSubstituicaoChapa(id).subscribe(
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