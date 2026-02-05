import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { ImpugnacaoCandidaturaClientService } from './impugnacao-candidatura-client.service';

@Injectable()
export class ImpugnacaoDeclaracaoAtividadeResolve implements Resolve<any> {

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
        private ImpugnacaoService: ImpugnacaoCandidaturaClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.ImpugnacaoService.getIdAtividadeSecundaria().subscribe(
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