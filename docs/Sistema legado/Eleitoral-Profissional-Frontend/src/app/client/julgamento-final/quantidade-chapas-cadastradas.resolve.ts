import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { JulgamentoFinalClientService } from './julgamento-final-client.service';

@Injectable()
export class QuantidadeChapasCadastradasResolve implements Resolve<any> {

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
        private julgamentoFinalClientService: JulgamentoFinalClientService,
    ) {
    }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        
        return new Observable(observer => {
            this.julgamentoFinalClientService.getQuantidadesChapas().subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    // redireciona caso o usuário logado seja conselheiro UF
                    if(error.code !== undefined && error.code === 'MSG-075') {
                       this.router.navigate([`/eleicao/julgamento-final/acompanhar-uf`]);
                    } else {
                        observer.error(error);
                        this.messageService.addMsgDanger(error);
                        this.router.navigate(['/']);
                    }
                }
            );
        });

    }

}