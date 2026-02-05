import { Injectable } from "@angular/core";
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { Observable } from 'rxjs';
import { MessageService } from '@cau/message';
import { DenunciaClientService } from './denuncia-client.service';
import { SecurityService } from '@cau/security';

/**
 * Classe resolve responsável pela busca de Eleições com chapa.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class DenunciaAgrupamentoUfAtividadeSecundariaResolve implements Resolve<any> {

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
        private securityService: SecurityService,
        private denunciaService: DenunciaClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let usuarioLogado = this.securityService.credential.user;

        return new Observable(observer => {
            this.denunciaService.getAgrupamentoDenunciaUfPorIdPessoa(usuarioLogado.id).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    observer.error(error);
                    this.router.navigate([""]);
                    this.messageService.addMsgDanger(error);
                }
            );
        });
    }
}