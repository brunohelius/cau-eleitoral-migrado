import { Observable } from "rxjs"
import { Injectable } from '@angular/core';
import { MessageService } from '@cau/message'
import { SecurityService } from '@cau/security'
import { Resolve, ActivatedRouteSnapshot, Router } from "@angular/router"

import { ImpugnacaoCandidaturaClientService } from './impugnacao-candidatura-client.service';
import { Constants } from 'src/app/constants.service';

@Injectable()
export class AcompanharImpugnacaoClientResolve implements Resolve<any> {

    public user: any;
    private permissoes = [];
    public dados: {
        valor: null,
        cenBR: boolean
    }

    /**
     * Construtor da classe
     * @param router
     * @param messageService
     * @param securtyService
     * @param impugnacaoService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private impugnacaoService: ImpugnacaoCandidaturaClientService,
    ) {
        this.user = this.securtyService.credential["_user"];
        this.setPermissoes();
    }

    resolve(route: ActivatedRouteSnapshot): Observable<any> {
        let id = route.params['id'];
        if (this.getPermissao() == Constants.ROLE_ACESSOR_CEN) {

            return new Observable(observer => {

                this.impugnacaoService.pedidosImpugnacaoChapa(id).subscribe(
                    data => {
                        observer.next(data);
                        observer.complete();
                    },
                    error => {
                        // redireciona caso o usuário logado seja conselheiro UF
                        if (error.code !== undefined && error.code === 'MSG-075') {
                            this.router.navigate([`/`]);
                        } else {
                            observer.error(error);
                            this.messageService.addMsgDanger(error);
                            this.router.navigate(['/']);
                        }
                    }
                );
            });

        } else if (this.getPermissao() == Constants.ROLE_ACESSOR_CE) {
            this.router.navigate([`/eleicao/impugnacao/acompanhar-impugnacao-uf/${this.user.cauUf.id}/calendario/${id}`]);

        } else {

            this.router.navigate(['/']);
        }
    }

    /**
    * Atribui as permissões de usuário à variável permissões
    */
    public setPermissoes() {

        const regras = this.user.roles;
        regras.forEach(element => {
            if (element == Constants.ROLE_ACESSOR_CEN || element == Constants.ROLE_ACESSOR_CE) {
                this.permissoes.push(element);
            }
        });
    }

    /**
     * verifica quais tipos de permissão o usuário possui
     */
    public getPermissao() {
        if (this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CEN])) {
            return Constants.ROLE_ACESSOR_CEN;
        }

        if (this.securtyService.hasRoles([Constants.ROLE_ACESSOR_CE])) {
            return Constants.ROLE_ACESSOR_CE;
        }
    }
}