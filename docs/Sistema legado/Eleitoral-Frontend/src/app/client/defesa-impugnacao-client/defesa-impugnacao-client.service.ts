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
     * Retorna arquivo de defesa de impugnação.
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

    /**
     * Gerar documento em formato PDF do Julgamento de impugnação.
     *
     * @param idJulgamento
     */
    public gerarPDFJulgamento(idJulgamento: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/${idJulgamento}/pdf`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }

    /**
     * Salva julgamento de Defesa de Impugnação.
     *
     * @param julgamento
     */
    public salvarJulgamentoImpugnacao( julgamento: any):  Observable<any> {
        let data = new FormData();
        this.appendFormData(julgamento, data);
        return this.http.post(`${environment.url}/julgamentosImpugnacao/salvar`, data);
    }

    /**
     * Retorna arquivo de julgamento defesa de impugnação.
     *
     * @param idArquivo
     */
    public getArquivoJulgamentoImpugnacao(idArquivo: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/${idArquivo}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }

    /**
     * Retorna julgamento de impugnação.
     *
     * @param idPedidoImpugnacao
     */
    public getJulgamentosImpugnacao(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/pedidoImpugnacao/${idPedidoImpugnacao}`);
    }

    /**
     * Retorna julgamento de impugnação.
     *
     * @param idPedidoImpugnacao
     */
    public getJulgamentosImpugnacaoSegundaInstancia(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/pedidoImpugnacao/${idPedidoImpugnacao}`);
    }
    
    /**
     * Retorna julgamento de impugnação.
     *
     * @param idPedidoImpugnacao
     */
    public getRecursoImpugnacao(idPedidoImpugnacao: number, idTipoSolicitacao: number): Observable<any> {
        return this.http.get(`${environment.url}/recursosImpugnacao/pedidoImpugnacao/${idPedidoImpugnacao}/tipoSolicitacao/${idTipoSolicitacao}`);
    }

    /**
     * Retorna atividade secundaria de nível 3.3, referente ao julgamento do pedido de impugnação da chapa.
     *
     */
    public getAtividadeSecundaria(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/${idPedidoImpugnacao}/atividadeSecundariaCadastro`);
    }

}