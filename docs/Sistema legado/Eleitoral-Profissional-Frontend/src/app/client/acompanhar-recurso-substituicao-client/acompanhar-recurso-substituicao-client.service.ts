import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { AbstractService } from '../abstract.service';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})

export class AcompanharRecursoSubstituicaoClientService extends AbstractService {


  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }

  /**
  * Retorna os pedidos de Chapa de acordo com o id da solicitacao.
  */
  public recursoSubstituicao(idPedidoSubstituicao: any): Observable<any> {
    return this.http.get(`${environment.url}/recursosSubstituicao/pedidoSubstituicao/${idPedidoSubstituicao}`);
  }

  /**
   * Retorna o documento do Recurso de substituição.
   * 
   * @param id 
   */
  public getDocumentoRecurso(idRecursoSubistituicao): Observable<any> {
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
    return this.http.get(`${environment.url}/recursosSubstituicao/${idRecursoSubistituicao}/download`, options);
  }
  
}
