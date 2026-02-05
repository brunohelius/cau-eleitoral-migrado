import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

import { AbstractService } from '../abstract.service';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class TipoFinalizacaoMandatoService extends AbstractService {

  /**
     * Construtor da classe.
     *
     * @param http
     */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Salva um Tipo de Finalização de Mandato
   *
   * @param publicacao
   */
  public salvar(data: any): Observable<any> {
    return this.http.post(`${environment.url}/tipoFinalizacaoMandato/`, data);
  }

  /**
   * Alterar um Tipo de Finalização de Mandato
   *
   * @param publicacao
   */
  public update(data: any, id: number): Observable<any> {
    return this.http.put(`${environment.url}/tipoFinalizacaoMandato/${id}`, data);
  }

  /**
   * Deleter um Tipo de Finalização de Mandato
   *
   * @param publicacao
   */
  public delete(id: number): Observable<any> {
    return this.http.delete(`${environment.url}/tipoFinalizacaoMandato/${id}`);
  }

  /**
   * Busca Tipo de Finalização de Mandato pelo filtro
   *
   */
  public getByFilter(params): Observable<any> {
    return this.http.post(`${environment.url}/tipoFinalizacaoMandato/filter`, params);
  }

  /**
   * Busca Tipo de Finalização de Mandato buscando pelo ID
   *
   */
  public getById(id): Observable<any> {
    return this.http.get(`${environment.url}/tipoFinalizacaoMandato/${id}`);
  }
}
