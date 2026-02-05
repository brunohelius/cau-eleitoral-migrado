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
export class JulgamentoImpugnacaoService extends AbstractService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Busca o julgamento de acordo com id do pedido de impugnacao.
     * 
     * @param idPedidoImpugnacao 
     * @return Observable
     */
    public getPedidoImpugnacaoMembroComissao(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/membroComissao/pedidoImpugnacao/${idPedidoImpugnacao}`);
    }

    /**
     * Busca o julgamento de acordo com id do pedido de impugnacao.
     * 
     * @param idPedidoImpugnacao 
     * @return Observable
     */
    public getPedidoImpugnacaoResponsavel(idPedidoImpugnacao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/responsavel/pedidoImpugnacao/${idPedidoImpugnacao}`);
    }

    /**
     * Dowlaod de arquivos de Defesa de impugnação.
     * 
     * @param id 
     */
    public getArquivoJulgamento(id: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosImpugnacao/${id}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }

    /**
     * Dowlaod de arquivos do julgamento segunda instancia de impugnação.
     * 
     * @param id 
     */
    public getArquivoJulgamentoSegundaInstancia(id: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/${id}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }
    
    /**
     * Salvar o recurso de pedido de impugnação.
     * 
     * @param recurso Dados do recurso de impugnação.
     * @returns Observable
     */
    public salvarRecurso(recurso: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(recurso, data);
        return this.http.post(`${environment.url}/recursosImpugnacao/salvar`, data);
    }
    
    /**
     * Dowlaod de arquivos do Recurso do julgamento.
     * 
     * @param id 
     */
    public getArquivoRecurso(id: number): Observable<any> {
        return this.http.get(`${environment.url}/recursosImpugnacao/${id}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }

    
    /**
     * Busca o julgamento de acordo com id do pedido de impugnacao.
     * 
     * @param idPedidoImpugnacao 
     * @return Observable
     */
    public getRecursoJulgamento(idPedidoImpugnacao: number, idTipoSolicitacao: number): Observable<any> {
        return this.http.get(`${environment.url}/recursosImpugnacao/pedidoImpugnacao/${idPedidoImpugnacao}/tipoSolicitacao/${idTipoSolicitacao}`);
    }

    /**
     * Salvar Contrarrazao.
     * 
     * @param contrarrazao 
     */
    public salvarContrarrazaoRecursoImpugnacao(contrarrazao: any):  Observable<any> {
        let data = new FormData();
        this.appendFormData(contrarrazao, data);
        return this.http.post(`${environment.url}/contrarrazaoRecursoImpugnacao/salvar`, data);
    }

     /**
     * Dowlaod de arquivos do Contrarrazao.
     * 
     * @param id 
     */
    public getArquivoContrarrazao(id: number): Observable<any> {
        return this.http.get(`${environment.url}/contrarrazaoRecursoImpugnacao/${id}/download`, {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob'
        });
    }

    /**
     * Busca o profissional que será substituto no pedido de impugnação
     * @param dados
     */
    public getCandidadoSubstituto(dados: any): Observable<any> {
        return this.http.post(`${environment.url}/substituicaoImpugnacao/buscaSubstituto`, dados);
    }

    /**
     * Salva o pedido de substituição de impugnação
     * @param dados
     */
    public salvarCandidadoSubstituto(dados: any): Observable<any> {
        return this.http.post(`${environment.url}/substituicaoImpugnacao/salvar`, dados);
    }

}