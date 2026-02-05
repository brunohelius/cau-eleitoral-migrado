import { Observable } from "rxjs"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'

import { Injectable } from '@angular/core';
import { ConviteChapaEleicaoClientService } from './convite-chapa-eleicao-client.service';

/**
 * Classe resolve responsável pela busca de  convites para participação de chapas.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class ConvitesRecebidosClientResolve {

    /**
     * Construtor da classe.
     * 
     * @param router 
     * @param messageService 
     * @param conviteChapaEleicaoService 
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private conviteChapaEleicaoService: ConviteChapaEleicaoClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.conviteChapaEleicaoService.getConvitesRecebidos().subscribe(
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