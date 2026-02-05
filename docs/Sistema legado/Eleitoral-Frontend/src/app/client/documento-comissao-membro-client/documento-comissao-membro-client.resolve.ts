import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { DocumentoComissaoMembroClientService } from './documento-comissao-membro-client.service'
import { MessageService } from '@cau/message';

@Injectable({
    providedIn: 'root'
})
export class DocumentoComissaoMembroClientResolve implements Resolve<any> {

    /**
     * Construtor da classe
     *
     * @param router
     * @param comissaoEleitoralService
     * @param messageService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private documentoComissaoMembroService: DocumentoComissaoMembroClientService
    ) { }

    /**
     *
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.documentoComissaoMembroService.getPorId(id).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    observer.error(error);
                    this.messageService.addMsgDanger(error);
                }
            );
        });
    }
}
