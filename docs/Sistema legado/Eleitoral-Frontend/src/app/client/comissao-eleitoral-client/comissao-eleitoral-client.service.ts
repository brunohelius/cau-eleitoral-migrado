import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';

/**
 * Classe de integração com o serviço de Comissão Eleitoral.
 */
@Injectable({
    providedIn: 'root'
})
export class ComissaoEleitoralService extends AbstractService {
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Retorna uma lista de comissões eleitorais
     * @return
     */
    public getComissoesEleitorais(): Observable<any> {
        return this.http.get(`${environment.url}/comissoesEleitorais`);
    }

    /**
     * Retorna uma  comissão eleitoral
     * @return
     */
    public getComissoaoEleitoral(id: number): Observable<any> {
        return this.http.get(`${environment.url}/comissaoEleitoral/${id}`);
    }
}