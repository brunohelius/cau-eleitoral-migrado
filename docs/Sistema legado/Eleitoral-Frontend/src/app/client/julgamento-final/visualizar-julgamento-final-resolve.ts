import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { JulgamentoFinalClientService } from './julgamento-final-client.service';

@Injectable()
export class VisualizarJulgamentoFinalPrimeiraResolve implements Resolve<any> {
/**
 * Construtor da classe.
 * @param router
 * @param messageService
 */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private julgamentoFinalClientService: JulgamentoFinalClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {

        let idChapaEleicao = route.params["id"];
        idChapaEleicao = 137;

        return new Observable(observer => {
            this.julgamentoFinalClientService.getDetalhamentoChapa(idChapaEleicao).subscribe(
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