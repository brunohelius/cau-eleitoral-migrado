import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";

/**
 * Classe de integração com membros das chapas
 */
@Injectable({
    providedIn: 'root'
})
export class MembroChapaService {

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
    public getEleitoByFilter(filtro): Observable<any> {
        return this.http.post(`${environment.url}/membrosChapa/getEleitoByFilter`, filtro);
    }

    /**
     * Retorna o Presidente da UF
     * @return
     */
    public getPresidenteUf(filtro): Observable<any> {
        return this.http.post(`${environment.url}/membrosChapa/getPresidenteUf`, filtro);
    }
}
