import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';


@Injectable({
    providedIn: 'root'
})
export class ContrarrazaoImpugnacaoResultadoClientService extends AbstractService {

    /**
     * Construtor da classe.
     * 
     * @param http 
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Salva contrarrazão de inpugnação de resultado I.R.
     * 
     * @param contrarrazao 
     */
    public salvar(contrarrazao: any): Observable<any> {
        const data = new FormData();
        this.appendFormData(contrarrazao, data);
        return this.http.post(`${environment.url}/julgamentoAlegacaoImpugnacaoResultado/salvar`, data);
    }

    /**
     * Realiza download de documento da contrarrazão.
     * 
     * @param idContrarrazao 
     */
    public download(idContrarrazao: number): Observable<any> {
        let options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/contrarrazoesImpugnacaoResultado/documento/${idContrarrazao}/download`, options);
    }

}