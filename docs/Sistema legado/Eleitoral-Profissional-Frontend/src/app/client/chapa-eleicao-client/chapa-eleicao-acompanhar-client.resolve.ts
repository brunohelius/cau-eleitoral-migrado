import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message'
import { ChapaEleicaoClientService } from './chapa-eleicao-client.service';
import { Constants } from 'src/app/constants.service';

@Injectable()
export class ChapaEleicaoAcompanharClientResolve implements Resolve<any> {

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
        private chapaEleicaoService: ChapaEleicaoClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.chapaEleicaoService.getChapaEleicaoAcompanhar().subscribe(
                data => {
                    if (data.chapaEleicao.idEtapa == undefined || Constants.STATUS_CHAPA_ETAPA_CONCLUIDO < data.chapaEleicao.idEtapa) {
                        this.router.navigate(['eleicao', 'cadastro-chapa']);
                    }
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