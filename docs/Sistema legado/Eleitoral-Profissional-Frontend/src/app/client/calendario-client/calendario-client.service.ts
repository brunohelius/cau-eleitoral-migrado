import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpHeaders, HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class CalendarioClientService extends AbstractService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
  }

   /**
   * Retorna o arquivo conforme o id do Calendario Informado
   *
   * @param idResolucao
   */
  public downloadArquivo(idResolucao): Observable<Blob> {
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

    return this.http.get(`${environment.url}/calendarios/arquivo/${idResolucao}/download`, options);
}

}
