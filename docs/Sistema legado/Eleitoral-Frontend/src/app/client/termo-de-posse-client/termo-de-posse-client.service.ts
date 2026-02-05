import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";

/**
 * Classe de integração com Termo de posse
 */
@Injectable({
    providedIn: 'root'
})
export class TermoDePosseService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
    }

    /**
     * Salvar termo de posse
     * @return
     */
    public create(dados): Observable<any> {
        return this.http.post(`${environment.url}/termo`, dados);
    }

    /**
     * Buscar termo
     * @return
     */
    public getById(idtermo): Observable<any> {
        return this.http.get(`${environment.url}/termo/${idtermo}`);
    }

    /**
     * Editar termo de posse
     * @return
     */
    public update(dados, idtermo): Observable<any> {
        return this.http.put(`${environment.url}/termo/${idtermo}`, dados);
    }

     /**
     * Realiza download do termo de posse
     *
     * @param idtermo
     */
     public imprimir(idtermo: any): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({ 'Content-Type': 'application/json' })
        };
        return this.http.get(`${environment.url}/termo/imprimir/${idtermo}`, options);
    }
}
