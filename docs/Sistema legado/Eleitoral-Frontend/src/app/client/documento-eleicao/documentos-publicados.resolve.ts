import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { MessageService } from '@cau/message';
import { DocumentoEleicaoClientService } from './documento-eleicao-client.service';

/**
 * Classe resolve responsável pela busca das informações dos Documentos Publicados.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
    providedIn: 'root'
})
export class DocumentosPublicadosResolve implements Resolve<any> {

    /**
     * Construtor da Classe.
     * 
     * @param router 
     * @param messageService 
     * @param documentoService 
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private documentoEleicaoService: DocumentoEleicaoClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        const idCalendario = route.params["id"];
        return new Observable(observer => {
            this.documentoEleicaoService.getDocumentosPorEleicao(idCalendario).subscribe(data => {
                observer.next(data);
                observer.complete();
            }, error => {
                observer.error(error);
                this.router.navigate(['']);
                this.messageService.addMsgDanger(error);
            });
        });
    }
}
