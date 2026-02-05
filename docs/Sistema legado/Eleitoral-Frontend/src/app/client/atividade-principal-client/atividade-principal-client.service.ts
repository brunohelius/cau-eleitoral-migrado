import { Injectable } from '@angular/core';
import { AbstractService } from '../abstract.service';
import { HttpClient } from '@angular/common/http';

import { Observable } from 'rxjs';
import { environment } from "../../../environments/environment";

/**
 * Classe de integração com o serviço de Atividade principal.
 */
@Injectable({
    providedIn: 'root'
})
export class AtividadePrincipalClientService extends AbstractService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Retorna lista de Atividades Principais de calendários concluídos.
     */
    public getAtividadesPrincipais(): Observable<any> {
        return this.http.get(`${environment.url}/atividadesPrincipais`);
    }
}