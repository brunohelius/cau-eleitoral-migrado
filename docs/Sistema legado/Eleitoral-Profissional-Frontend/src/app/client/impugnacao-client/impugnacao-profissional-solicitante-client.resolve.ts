import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { AcompanharImpugnacaoClientService } from './impugnacao-client.service';


@Injectable()
export class ImpugnacaoProfissionalSolicitanteClientResolve implements Resolve<any> {

    public user = this.securtyService.credential["_user"];
    public dados: {
        valor: null,
        cenBR: boolean
    }

    /**
     * Construtor da classe
     * @param router 
     * @param messageService 
     * @param securtyService 
     * @param substituicaoChapaService 
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private acompanharImpugnacaoClientService: AcompanharImpugnacaoClientService,
    ) {
        
    }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        return new Observable(observer => {
            this.acompanharImpugnacaoClientService.pedidosImpugnacaoChapaPorSolicitante().subscribe(
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