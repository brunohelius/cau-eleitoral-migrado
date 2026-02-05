import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { SubstiuicaoChapaClientService } from './substituicao-chapa-client.module';
import { Constants } from 'src/app/constants.service';

@Injectable()
export class AcompanharSubstituicaoClientResolve implements Resolve<any> {

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
        private substituicaoChapaService: SubstiuicaoChapaClientService,
    ) {
    }


    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params["id"];
        return new Observable(observer => {
            this.substituicaoChapaService.getQuantidadePedidosParaCadaUf(id).subscribe(
                data => {
                    observer.next(data);
                    observer.complete();
                },
                error => {
                    // redireciona caso o usuário logado seja conselheiro UF
                    if(error.code !== undefined && error.code === 'MSG-075') {
                        this.router.navigate([`/eleicao/acompanhar-substituicao-uf/${this.user.cauUf.id}`]);
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