import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { AbstractService } from '../abstract.service';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';

/**
 * Classe de integração com o serviço de 'Informação Comissão Membro'.
 */
@Injectable({
    providedIn: 'root'
})
export class JulgamentoFinalClientService extends AbstractService {

    /**
     * Construtor da classe.
     */
    constructor(private http: HttpClient) {
        super();
    }


    /**
     * Retorna a quantidade de chapas cadastradas pelo id do Calendário
     */
    public getQuantidadesChapas(): Observable<any> {
        return this.http.get(`${environment.url}/chapas/membroComissao/quantidades`);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getDetalhamentoPorUf(idCauUf: number): Observable<any> {
        const param = idCauUf != undefined ? `/${idCauUf}` : '';
        return this.http.get(`${environment.url}/julgamentosFinais/chapas/membroComissao${param}`);
    }
    /**
     * Serviço para consultar chapas de membro comissao de uma uf específica
     */
    public getJulgamentoChapaComissao(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamento/membroComissao/chapa/${idChapaEleicao}`);
    }
    /**
     * Serviço para consultar chapas responsavel de uma uf específica
     */
    public getJulgamentoChapaResponsavel(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamento/responsavelChapa/chapa/${idChapaEleicao}`);
    }
    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getMembrosComPendencia(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamento/responsavelChapa/chapa/${idChapaEleicao}`);
    }
    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getRecursoJulgamentoFinal(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/recursoJulgamentoFinal/chapa/${idChapaEleicao}`);
    }

    /**
     * Serviço para consultar de Pedido de substituição de julgamento final pro id chapa.
     *
     * @param idChapaEleicao
     */
    public getSubstituicaoJulgamentoPorChapa(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/chapa/${idChapaEleicao}`);
    }

    /**
     * Serviço para consultar de Pedido de substituição de julgamento final por id.
     * 
     * @param idChapaEleicao 
     */
    public getSubstituicaoJulgamentoPorId(id: number): Observable<any> {
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/${id}`);
    }
    
    public getArquivoDefesaImpugnacao(id): Observable<any> {
        const options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/${id}/download`, options);
    }
    /**
     * Recupera os arquivos recurso substituição...
     */
    public getArquivoRecursoSubstituicao(idRecurso): Observable<any> {
        const options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/recurso/${idRecurso}/download`, options);
    }

    public getArquivoRecursoSegundoJulgamentoSubstituicao(id): Observable<any> {
        const options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/recursoSegundoJulgamentoSubstituicao/${id}/download`, options);
    }

    /**
     * Retorna o documento do Julgamento Final.
     */
    public getDocumentoJulgamentoFinal(id: number): Observable<any> {
        const options = {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        }),
        // Ignore this part or  if you want full response you have
        // to explicitly give as 'body'as http client by default give res.json()
        observe: 'response' as 'body',
        // have to explicitly give as 'blob' or 'json'
        responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/julgamentosFinais/${id}/download`, options);
    }

    /**
     * Retorna o documento do Julgamento Final.
     */
    public getDocumentoRecursoJulgamentoFinal(id: number): Observable<any> {
        const options = {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        }),
        // Ignore this part or  if you want full response you have
        // to explicitly give as 'body'as http client by default give res.json()
        observe: 'response' as 'body',
        // have to explicitly give as 'blob' or 'json'
        responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/recursoJulgamentoFinal/${id}/download`, options);
    }

    /**
     * Serviço para consultar chapas responsável de uma uf específica
     */
    public getDetalhamentoChapaResponsavel(): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/chapaEleicao/responsavelChapa`);
    }
    /**
     * Serviço para consultar chapas membro Comissão uma de uf específica
     */
    public getDetalhamentoChapaComissao(idChapa: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/chapaEleicao/membroComissao/${idChapa}`);
    }

    /**
     * Salvar o recurso de pedido de impugnação.
     *
     * @param recurso Dados do recurso de impugnação.
     * @returns Observable
     */
    public salvarRecurso(recurso: any): Observable<any> {
        const data = new FormData();
        this.appendFormData(recurso, data);
        return this.http.post(`${environment.url}/recursoJulgamentoFinal/salvar`, data);
    }

    /**
     * Salvar o pedido de substituição.
     *
     * @param pedido Dados do pedido de substituição.
     * @returns Observable
     */
    public salvarPedidoSubstituicao(pedido: any): Observable<any> {
      const data = new FormData();
      this.appendFormData(pedido, data);
      return this.http.post(`${environment.url}/substituicaoJulgamentoFinal/salvar`, data);
    }

    /**
     * Busca o membro que será subistituido.
     */
    public getMembroSubstituicao(params: any): Observable<any> {
      const data = new FormData();
      this.appendFormData(params, data);
      return this.http.post(`${environment.url}/substituicaoJulgamentoFinal/membroSubstituto`, data);
    }

    /**
     * Salvar o recurso de pedido de impugnação.
     *
     * @param recurso Dados do recurso de impugnação.
     * @returns Observable
     */
    public salvarRecursoJulgamentoSubstituicao(recurso: any): Observable<any> {
        const data = new FormData();
        this.appendFormData(recurso, data);
        return this.http.post(`${environment.url}/recursoSegundoJulgamentoSubstituicao/salvar`, data);
    }

    /**
     * Serviço para consultar o julgamento de recurso de segunda instância por id do julgamento final
     */
    public getJulgamentoRecursoSegundaInstancia(idJulgamentoFinal: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoRecursoSegundaInstancia/julgamentoFinal/${idJulgamentoFinal}`);
    }

    /**
     * Retorna o documento do Julgamento do Recurso de Segunda Instância.
     */
    public getDocumentoJulgamentoRecursoSegundaInstancia(id: number): Observable<any> {
        const options = {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        }),
        // Ignore this part or  if you want full response you have
        // to explicitly give as 'body'as http client by default give res.json()
        observe: 'response' as 'body',
        // have to explicitly give as 'blob' or 'json'
        responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/julgamentoRecursoSegundaInstancia/${id}/download`, options);
    }

    /**
     * Serviço para consultar o julgamento de substituição de segunda instância por id do julgamento final
     */
    public getJulgamentoSubstituicaoSegundaInstancia(idJulgamentoFinal: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/julgamentoFinal/${idJulgamentoFinal}`);
    }

    /**
     * Retorna o documento do Julgamento do Substituicao de Segunda Instância.
     */
    public getDocumentoJulgamentoSubstituicaoSegundaInstancia(id: number): Observable<any> {
        const options = {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        }),
        // Ignore this part or  if you want full response you have
        // to explicitly give as 'body'as http client by default give res.json()
        observe: 'response' as 'body',
        // have to explicitly give as 'blob' or 'json'
        responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/${id}/download`, options);
    }

    /**
     * Serviço para consultar de julgamento final de segunda instância por id chapa.
     * 
     * @param idChapaEleicao 
     */
    public getJulgamentoSegundaInstanciaPorChapa(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamentoSegundaInstancia/chapa/${idChapaEleicao}`);
    }

    /**
     * Retorna as pendências da chapa selecionada
     * @param idChapa
     */
    public getPedidosSolicitadosChapa(idChapa: number): Observable<any> {
        return this.http.get(`${environment.url}/chapas/${idChapa}/pedidosSolicitados`);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getInfoPlataformaPropagandaChapa(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/chapas/${idChapaEleicao}/infoPlataformaChapa`);
    }

    /**getDocumentoJulgamentoRecursoSegundaInstanciaSubstituicao
     * Retorna o documento do Julgamento do Recurso de Segunda Instância.
     */
    public getDocumentoJulgamentoRecursoSegundaInstanciaSubstituicao(id: number): Observable<any> {
        const options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };
        return this.http.get(`${environment.url}/julgamentoRecursoPedidoSubstituicao/${id}/download`, options);
    }

}
