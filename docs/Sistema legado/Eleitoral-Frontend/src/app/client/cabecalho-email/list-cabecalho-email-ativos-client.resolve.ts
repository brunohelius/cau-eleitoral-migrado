import { Observable,of } from 'rxjs';
import { Injectable } from "@angular/core";
import { MessageService } from '@cau/message';
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router";
import { CabecalhoEmailClientService } from './cabecalho-email-client.service';


/**
 * Classe resolve responsável pela busca das informações de cabeçalho de e-mail.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class ListCabecalhoEmailAtivosClientResolve implements Resolve<any> {
    
    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param cabecalhoEmailService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private cabecalhoEmailService: CabecalhoEmailClientService
    ) { }

    /**
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.cabecalhoEmailService.getPorFiltro({ativo: true}).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    observer.error(error);
                    this.router.navigate([""]);
                    this.messageService.addMsgDanger(error);
                    this.messageService.addMsgDanger('MSG_ERRO_COMUNICACAO_TENTE_MAIS_TARDE');
                }
            );
        });
    }
}