import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class UFClientService extends AbstractService {

  /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
      super();
  }


  /**
     * Retorna uma lista de comissões eleitorais.
     * 
     * @return
     */
    public getUFs(): Observable<any> {
      return this.http.get(`${environment.url}/uf`);
  }

  /**
    * Busca lista de filiais
    * 
    */
  public getFilial(data: any): Observable<any> {
    return this.http.post(`${environment.urlAcesso}/api/filial/filter`, data);
  }
}