import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";

/**
 * Classe de integração com o serviço de Perfil.
 */
@Injectable({
    providedIn: 'root'
})
export class PainelClientService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
    }

    /**
     * Retorna uma lista de instância de Eleicoes.
     * @return
     */
    public getDadosGrafico(): Observable<any> {
        return this.http.get(`${environment.url}/eleicao`);
    }

}
