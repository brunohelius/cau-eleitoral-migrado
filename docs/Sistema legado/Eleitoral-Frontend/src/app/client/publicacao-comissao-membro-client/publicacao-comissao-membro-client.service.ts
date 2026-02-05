import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

import { AbstractService } from '../abstract.service';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class PublicacaoComissaoMembroClientService extends AbstractService {

  /**
     * Construtor da classe.
     *
     * @param http
     */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Salva uma nova publicação no sistema.
   *
   * @param publicacao
   */
  public salvar(publicacao: any): Observable<any> {
    return this.http.post(`${environment.url}/publicacao/comissaoMembro`, publicacao);
  }

  /**
   * Gera o documento pdf referente a publicação da comissão membro.
   *
   * @param id
   */
  public gerarPDF(id: any): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    };

    return this.http.get(`${environment.url}/publicacao/comissaoMembro/${id}/pdf`, options);
  }

  /**
   * Gera o "docx" de acordo com o modelo de comissão eleitoral.
   *
   * @param id
   */
  public gerarDocumento(id: any): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    };

    return this.http.get(`${environment.url}/publicacao/comissaoMembro/${id}/doc`, options);
  }

  /**
   * Realiza o download do pdf de publicação de comissão membro.
   *
   * @param id
   */
  public downloadPdf(id: any): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    };

    return this.http.get(`${environment.url}/publicacao/${id}/download`, options);
  }

}
