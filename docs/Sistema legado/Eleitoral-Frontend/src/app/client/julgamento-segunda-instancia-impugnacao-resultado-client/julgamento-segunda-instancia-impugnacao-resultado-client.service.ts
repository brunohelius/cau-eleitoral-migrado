import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class JulgamentoSegundaInstanciaImpugnacaoResultadoClientService extends AbstractService {

   /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
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