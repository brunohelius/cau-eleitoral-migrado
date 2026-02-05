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
   *  Retorna uma lista de comissões eleitorais.
   */
  public getCauUFs(): Observable<any> {
    let data = {
      tipo: 7
    };
    
    return this.http.post(`${environment.urlPortal}/filiais/bandeira/filter`, data);
  }

  /**
   * Retorna bandeiras das filiais.
   */
  public getBandeiras(): Observable<any> {
    let data = {
      tipo: 7
    };

    return this.http.post(`${environment.urlPortal}/filiais/bandeira/filter`, data);
  }

  /**
   * Retorna bandeira das filial.
   *
   * @param idCauUf
   */
  public getBandeiraPorCauUF(idCauUf: number): Observable<any> {
    return this.http.get(`${environment.url}/filiais/${idCauUf}/bandeira`);
  }

  /**
   * Retorna bandeiras das filiais associadas ao calendário informado.
   */
  public getBandeirasPorCalendario(idCalendario): Observable<any> {
    return this.http.get(`${environment.url}/filiais/bandeira/calendario/${idCalendario}`);
  }

  /**
   * Retorna a lista de filiais, associadas às UF's que ainda não tiveram Membros de Comissão cadastradas, para um
   * determinado calendário.
   *
   * @param idCalendario
   */
  public getFiliaisMembrosNaoCadastradosPorCalendario(idCalendario): Observable<any> {
    return this.http.get(`${environment.url}/filiais/calendario/${idCalendario}/comissao`);
  }

}
