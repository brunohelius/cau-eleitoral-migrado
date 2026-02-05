import { Observable } from "rxjs"
import { Injectable } from "@angular/core"
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"
import { MessageService } from '@cau/message'
import { CauUFService } from './cau-uf-client.service'

@Injectable({
    providedIn: 'root'
})
export class CauUfCalendarioClientResolve implements Resolve<any> {

    /**
     * Construtor da classe
     *
     * @param router
     * @param cauUFService
     * @param messageService
     */
    constructor(
        private router: Router,
        private cauUFService: CauUFService,
        private messageService: MessageService
    ) { }

    /**
     *
     * @param route
     */
    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let idCalendario = route.params['id'];
        return new Observable(observer => {
            this.cauUFService.getBandeirasPorCalendario(idCalendario).subscribe(
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
