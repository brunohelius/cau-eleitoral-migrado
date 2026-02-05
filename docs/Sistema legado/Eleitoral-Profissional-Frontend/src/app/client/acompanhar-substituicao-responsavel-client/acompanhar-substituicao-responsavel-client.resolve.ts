import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { Constants } from 'src/app/constants.service';
import { AcompanharSubstituicaoResponsavelService } from './acompanhar-substituicao-responsavel-client.service';

@Injectable()
export class AcompanharSubstituicaoResponsavelResolve implements Resolve<any> {

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
        private acompanharSubstituicaoResponsavelService: AcompanharSubstituicaoResponsavelService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.acompanharSubstituicaoResponsavelService.getPedidosChapaPorResponsavelChapa().subscribe(
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