import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";

/**
 * Classe de integração com membros das chapas
 */
@Injectable({
    providedIn: 'root'
})
export class DiplomaEleitoralService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
    }

    /**
     * Salvar diploma eleitoral
     * @return
     */
    public create(dados): Observable<any> {
        return this.http.post(`${environment.url}/diploma`, dados);
    }

    /**
     * Buscar diploma
     * @return
     */
    public getById(idDiploma): Observable<any> {
        return this.http.get(`${environment.url}/diploma/${idDiploma}`);
    }

    /**
     * Editar diploma eleitoral
     * @return
     */
    public update(dados, idDiploma): Observable<any> {
        return this.http.put(`${environment.url}/diploma/${idDiploma}`, dados);
    }

     /**
     * Realiza download do Diploma Eleitoral
     *
     * @param idDiploma
     */
     public imprimir(idDiploma: any): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({ 'Content-Type': 'application/json' })
        };
        return this.http.get(`${environment.url}/diploma/imprimir/${idDiploma}`, options);
    }
}
