import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { DefesaImpugnacaoService } from './defesa-impugnacao-client.service'

@Injectable({
    providedIn: 'root'
})
export class JulgamentoImpugnacaoResolve implements Resolve<any> {

    /**
     * Construtor da classe
     * 
     * @param router 
     * @param comissaoEleitoralService 
     * @param messageService 
     */
    constructor(
        private router: Router,
        private defesaImpugnacaoService: DefesaImpugnacaoService,
        private messageService: MessageService,
        private securtyService: SecurityService
    ) { }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = parseInt(route.params['id']);
        return new Observable(observer => {
            this.defesaImpugnacaoService.getJulgamentosImpugnacao(id).subscribe(
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
