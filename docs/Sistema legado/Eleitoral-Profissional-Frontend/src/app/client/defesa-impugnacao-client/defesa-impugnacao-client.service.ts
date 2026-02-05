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
export class DefesaImpugnacaoService extends AbstractService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }


    /**
     * Buscar defesa impugnação por id.
     * 
     * @param id Identificador de Defesa de impugnação.
     * @returns Observable
     */
    public getPorId(id: number): Observable<any> {
        return this.http.get(`${environment.url}/defesaImpugnacao/${id}`);
    }

    /**
     * Buscar Defesa de pedido de impugnação pelo identificador de pedido de impugnação.
     * 
     * @param idPedidoImpugnacao 
     * @return Observable
     */
    public getPorPedidoImpugnacao(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/defesaImpugnacao/pedidosImpugnacao/${idPedidoImpugnacao}`);
    }

    /**
     * Salvar defesa de pedido de impugnação.
     * 
     * @param defesa Dados da Defesa de impugnação.
     * @returns Observable
     */
    public salvar(defesa: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(defesa, data);
        return this.http.post(`${environment.url}/defesaImpugnacao/salvar`, data);
    }

    /**
     * Retorna validações necessária para profissional acessar DefesaImpugnação.
     * 
     * @param idPedidoImpugnacao 
     */
    public getDefesaImpugnacaoValidacaoAcessoProfissional(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/defesaImpugnacao/pedidosImpugnacao/${idPedidoImpugnacao}/validarProfissional`);
    }

    /**
     * Dowlaod de arquivos de Defesa de impugnação.
     * 
     * @param idArquivo 
     */
    public getArquivoDefesaImpugnacao(idArquivo: number): Observable<any> {
        return this.http.get(`${environment.url}/defesaImpugnacao/documento/${idArquivo}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }
}