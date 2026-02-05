import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class CauUFService extends AbstractService {

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
    public getCauUFs(): Observable<any> {
      return this.http.get(`${environment.url}/filiais/bandeira`);
  }

  /**
     * Retorna uma lista de comissões eleitorais contendo IES.
     *
     * @return
     */
  public getCauUFsComIES(): Observable<any> {
    return this.http.get(`${environment.url}/filiais/ies`);
  }

    /**
   * Retorna bandeira das filial.
   *
   * @param idCauUf
   */
  public getBandeiraPorCauUF(idCauUf: number): Observable<any> {
    return this.http.get(`${environment.url}/filiais/${idCauUf}/bandeira`);
  }

}
