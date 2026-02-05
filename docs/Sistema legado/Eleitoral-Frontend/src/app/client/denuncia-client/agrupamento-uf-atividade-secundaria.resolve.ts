import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable } from 'rxjs';
import { MessageService } from '@cau/message';
import { DenunciaClientService } from './denuncia-client.service';


/**
 * Classe resolve responsável pela busca de Eleições com chapa.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class AgrupamentoUfAtividadeSecundariaResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param corpoEmailService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private denunciaService: DenunciaClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idCalendario = route.params["idCalendario"];
        return new Observable(observer => {
            this.denunciaService.getAgrupamentoDenunciaUfPorCalendario(idCalendario).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    observer.error(error);
                    this.router.navigate(["denuncia/concluida/listar"]);
                    this.messageService.addMsgDanger(error);
                }
            );
        });
    }
}