import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class ImpugnacaoResultadoClientService extends AbstractService {

  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }


  /**
  * Retorna os pedidos de Impugnação.
  */
  public getCauUfImpugnacaoResultado(): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/cauUf`);
  }

  /**
   * Retorna o pedido de Impugnação a partir da uf.
   *
   * @param impugnacao
   * @param uf
   */
  public getImpugnacaoResultadoPorUf(uf): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/${uf}`);
  }

  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
  public getImpugnacaoPorId(id): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/${id}/impugnacao`);
  }

  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
  public getImpugnacaoComVerificacaoImpugnantePorId(id): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/impugnante/${id}/impugnacao`);
  }

  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
  public getImpugnacaoComVerificacaoChapaPorId(id): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/chapa/${id}/impugnacao`);
  }

  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
  public getImpugnacaoComVerificacaoComissaoPorId(id): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/comissao/${id}/impugnacao`);
  }

  /**
    * Retorna a alegação da impugnação de resultado a partir do id.
    *
    * @param idImpugnacao
    */
  public getAlegacaPorIdImpugnacao(idImpugnacao): Observable<any> {
    return this.http.get(`${environment.url}/alegacaoImpugnacaoResultado/${idImpugnacao}/alegacao`);
  }

  /**
   * Retornas pedidos de impugnação por ID da CauUF.
   * @param idCauUf
   */
  public getImpugnacoesPorCauUf(idCauUf: number): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/acompanhar/membroComissao/${idCauUf}`);
  }

  /**
   * Retorna Pedidos de Impugnação de resultado por profissional logado.
   */
  public acompanharPorProfissionalLogado(idCauUf): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/acompanhar/profissional/${idCauUf}`);
  }

  /**
   * Retorna Pedidos de Impugnação de resultado pelo uf da chapa do profissional logado.
   */
  public acompanharPorChapaDoProfissionalLogado(): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/acompanhar/chapa`);
  }

  /**
   * Salva a Impugnacao de Resultado.
   *
   * @param impugnacao
   */
  public salvar(impugnacao): Observable<any> {
    let data = new FormData();
    this.appendFormData(impugnacao, data);
    return this.http.post(`${environment.url}/impugnacaoResultado/salvar`, data);
  }

  /**
   * Salva a Impugnacao de Resultado.
   *
   * @param impugnacao
   */
  public verificarDuplicidade(impugnacao): Observable<any> {
    let data = new FormData();
    this.appendFormData(impugnacao, data);
    return this.http.post(`${environment.url}/impugnacaoResultado/verificacaoDuplicidade`, data);
  }

 /**
  * Retorna os pedidos de Impugnação membro Comissão.
  */
  public pedidosImpugnacaoComissao(): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/comissao/quantidadeParaCadaUf`);
  }

  /**
    * Retorna Pedidos de Impugnação de resultado pelo uf da chapa do profissional logado.
    */
  public acompanharImpugnacaoResultado(): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/comissao/quantidadeParaCadaUf/impugnante`);
  }

  /**
   * Retorna dados para validação de alegação de impugnação de resultado.
   * 
   * @param id 
   */
  public validacao(id):  Observable<any> {
    return this.http.get(`${environment.url}/alegacaoImpugnacaoResultado/${id}/validacao`);
  }

  /**
   * Retorna o documento da Impugnação de Resultado
   *
   * @param id
   */
  public getDocumento(id): Observable<any> {
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
    return this.http.get(`${environment.url}/impugnacaoResultado/documento/${id}/download`, options);
  }

  /**
  * Salva a Impugnacao de Resultado.
  *
  * @param alegacao
  */
  public salvarAlegacao(alegacao): Observable<any> {
    let data = new FormData();
    this.appendFormData(alegacao, data);
    return this.http.post(`${environment.url}/alegacaoImpugnacaoResultado/salvar`, data);
  }
  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
  public getJulgamentoAlegacaoImpugnacaoResultado(idImpugnacao): Observable<any> {
    return this.http.get(`${environment.url}/julgamentoAlegacaoImpugnacaoResultado/${idImpugnacao}`);
  }
  /**
 * Retorna o documento da Alegação de Impugnação de Resultado
 *
 * @param id
 */
  public getDocumentoAlegacao(idAlegacao): Observable<any> {
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
    return this.http.get(`${environment.url}/alegacaoImpugnacaoResultado/documento/${idAlegacao}/download`, options);
  }

  /**
   * Retorna o documento do Julgamento da Alegação de Impugnação de Resultado
   *
   * @param idJulgamento
   */
  public getDocumentoJulgamento(idJulgamento): Observable<any> {
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
    return this.http.get(`${environment.url}/julgamentoAlegacaoImpugnacaoResultado/documento/${idJulgamento}/download`, options);
  }

  /**
  * Salva o recurso do julgamento de Impugnação de Resultado.
  *
  * @param alegacao
  */
  public salvarRecurso(recurso): Observable<any> {
    let data = new FormData();
    this.appendFormData(recurso, data);
    return this.http.post(`${environment.url}/recursoJulgamentoImpugnacaoResultado/salvar`, data);
  }


  /**
    * Retorna o Recurso do Julgamento da impugnação de resultado a partir do id.
    *
    * @param idImpugnacao
    */
  public getRecursoJulgamentoPorIdImpugnacao(idImpugnacao, idTipoRecurso): Observable<any> {
    return this.http.get(`${environment.url}/recursoJulgamentoImpugnacaoResultado/${idImpugnacao}/recurso/${idTipoRecurso}`);
  }

  /**
   * Retorna o documento do Recurso do Julgamento de Impugnação de Resultado
   *
   * @param id
   */
  public getDocumentoRecursoJulgamento(idRecursoJulgamento): Observable<any> {
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
    return this.http.get(`${environment.url}/recursoJulgamentoImpugnacaoResultado/documento/${idRecursoJulgamento}/download`, options);
  }

  /**
    * Retorna a impugnação de resultado a partir do id.
    *
    * @param id
    */
   public getJulgamentoSegundaInstanciaImpugnacaoResultado(idImpugResultado): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursosImpugResultado/impugnacaoResultado/${idImpugResultado}`);
  }

  /**
   * Retorna o documento do Julgamento da Alegação de Impugnação de Resultado
   *
   * @param idJulgamento
   */
  public getDocumentoJulgamentoSegundaInstancia(idArquivo): Observable<any> {
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
    return this.http.get(`${environment.url}/julgamentosRecursosImpugResultado/impugnacaoResultado/${idArquivo}/download`, options);
  }
}
