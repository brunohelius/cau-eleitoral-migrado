import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { MessageService } from '@cau/message'
import { Resolve, ActivatedRouteSnapshot } from "@angular/router"
import { DefesaImpugnacaoService } from './defesa-impugnacao-client.service'

@Injectable({
    providedIn: 'root'
})
export class DefesaImpugnacaoValidacaoResolve implements Resolve<any> {

    /**
     * Construtor da classe
     * 
     * @param router 
     * @param comissaoEleitoralService 
     * @param messageService 
     */
    constructor(
        private defesaImpugnacaoService: DefesaImpugnacaoService,
        private messageService: MessageService,
    ) { }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = parseInt(route.params['id']);
        return new Observable(observer => {
            this.defesaImpugnacaoService.getDefesaImpugnacaoValidacaoAcessoProfissional(id).subscribe(
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
