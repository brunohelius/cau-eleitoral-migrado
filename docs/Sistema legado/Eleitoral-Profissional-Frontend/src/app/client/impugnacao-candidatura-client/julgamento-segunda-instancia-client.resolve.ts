import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { Resolve, ActivatedRouteSnapshot, Router, ActivatedRoute } from "@angular/router"
import { ImpugnacaoCandidaturaClientService } from './impugnacao-candidatura-client.service';
import { Constants } from 'src/app/constants.service';

@Injectable({
    providedIn: 'root'
})
export class julgamentoSegundaInstanciaResolve implements Resolve<any> {

    /**
     * Construtor da classe.
     *
     * @param router
     * @param calendarioClientService
     * @param messageService
     */
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private messageService: MessageService,
        private ImpugnacaoService: ImpugnacaoCandidaturaClientService
    ) { }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let valor = undefined;
        let id = route.params['id'];
        let nameParam = 'tipoProfissional';
        let data = this.route.snapshot.data;

        for (let index of Object.keys(data)) {
            let param = data[index];

            if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
                valor = param[nameParam];
                break;
            }
        }

        if (valor === Constants.TIPO_PROFISSIONAL_COMISSAO) {
            return new Observable(observer => {
                this.ImpugnacaoService.getJulgamentoSegundaInstanciaMembroComissao(id).subscribe(
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
        } else {
            return new Observable(observer => {
                this.ImpugnacaoService.getJulgamentoSegundaInstanciaResponsavelImpugnante(id).subscribe(
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

}