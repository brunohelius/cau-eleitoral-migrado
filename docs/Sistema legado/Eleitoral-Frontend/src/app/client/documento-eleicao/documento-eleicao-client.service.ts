import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

import { AbstractService } from '../abstract.service';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class DocumentoEleicaoClientService extends AbstractService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Recupera os documentos publicados de acordo com o ID da eleição.
   * 
   * @param id 
   */
  public getDocumentosPorEleicao(id: number): Observable<any> {
    return this.http.get(`${environment.url}/documentosEleicoes/${id}`);
  }

  /**
   * Salva um novo documento da eleição.
   *
   * @param documentoEleicao
   */
  public salvar(documentoEleicao: any): Observable<any> {
    let data = new FormData();
    this.appendFormData(documentoEleicao, data);
    return this.http.post(`${environment.url}/documentosEleicoes`, data);
  }

  /**
   * Realiza o download do documento da eleição, conforme o ID informado.
   *
   * @param id
   */
  public download(id: number): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    };

    return this.http.get(`${environment.url}/documentosEleicoes/${id}/download`, options);
  }

  /**
   * Realiza a validação do arquivo submetido para upload
   * 
   * @param dados 
   */
  public validarArquivoCalendario(arquivo:any): Observable <any> {
    return this.http.post(`${environment.url}/documentosEleicoes/arquivo/validacao`, arquivo);
  }

}
