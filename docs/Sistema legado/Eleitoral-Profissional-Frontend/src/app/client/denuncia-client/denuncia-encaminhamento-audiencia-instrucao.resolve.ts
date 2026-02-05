



import { Resolve, Router, ActivatedRouteSnapshot } from '@angular/router';
import { MessageService } from '@cau/message';
import { DenunciaClientService } from './denuncia-client.service';
import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';

/**
 * Classe resolve responsável pela busca de Audiência de instrução.
 *
 * @author Squadra Tecnologia
 */

@Injectable({
    providedIn: 'root'
  })
export class DenunciaEncaminhamentoAudienciaInstrucaoResolve implements Resolve<any>{

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
     *
     * @param
     *  
     */ 

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idEncaminhamento = route.params["idEncaminhamento"];
        
        return new Observable(observer => {
            /*this.denunciaService.getAudienciaInstrucao(idEncaminhamento).subscribe(
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
            );*/
        });
    }

}