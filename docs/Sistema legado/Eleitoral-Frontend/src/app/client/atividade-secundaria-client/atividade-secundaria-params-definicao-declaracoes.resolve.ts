import { Observable } from 'rxjs';
import { Injectable } from "@angular/core";
import { MessageService } from '@cau/message';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";

import { AtividadeSecundariaClientService } from './atividade-secundaria-client.service';

/**
 * Classe resolve responsável pela busca das informações para definição de e-mails.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class AtividadeSecundariaParamsDefinicaoDeclaracoesResolve implements Resolve<any> {
    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param atividadeSecundariaService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private atividadeSecundariaService: AtividadeSecundariaClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.atividadeSecundariaService.getParamsDefinicaoDeclaracoesPorAtividadeSecundaria(id).subscribe(
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