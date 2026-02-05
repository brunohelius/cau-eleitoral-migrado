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
  public pedidosImpugnacaoResultado(idCalendario): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/quantidadeParaCadaUf/${idCalendario}`);
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
   * Retorna Pedidos de impugnação de resultado por calendário e UF.
   */
  public getPorUfECalendario(idUf: number, idCalendario: number): Observable<any> {
    return this.http.get(`${environment.url}/impugnacaoResultado/acompanhar/corporativo/calendario/${idCalendario}/uf/${idUf}`);
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
    * Retorna o Recurso do Julgamento da impugnação de resultado a partir do id.
    *
    * @param idImpugnacao
    */
   public getRecursoJulgamentoPorIdImpugnacao(idImpugnacao, idTipoRecurso): Observable<any> {
    return this.http.get(`${environment.url}/recursoJulgamentoImpugnacaoResultado/${idImpugnacao}/recurso/${idTipoRecurso}`);
  }

  /**
  * Recupera os arquivos...
  */
  public getArquivoImpugnacaoResultado(idSolicitacao): Observable<any> {
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
    return this.http.get(`${environment.url}/impugnacaoResultado/documento/${idSolicitacao}/download`, options);
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
   * Retorna o documento do Recurso do Julgamento de Impugnação de Resultado
   *
   * @param id
   */
  public getDocumentoContrarrazaoRecursoJulgamento(idContrarrazao): Observable<any> {
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
    return this.http.get(`${environment.url}/contrarrazoesImpugnacaoResultado/documento/${idContrarrazao}/download`, options);
  }

  /**
   * Salvar Julgamento primeira instância de Julgamento de alegação de impugnação de resultado.
   *
   * @param julgamento
   */
  public salvarJulgamentoSegundaInstancia(julgamento: any):  Observable<any> {
    const data = new FormData();
    this.appendFormData(julgamento, data);
    return this.http.post(`${environment.url}/julgamentosRecursosImpugResultado/salvar`, data);
  }

}
