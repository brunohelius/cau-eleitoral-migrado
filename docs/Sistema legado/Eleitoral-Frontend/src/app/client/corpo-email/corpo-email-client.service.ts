import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { Observable } from 'rxjs';
import { AbstractService } from '../abstract.service';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class CorpoEmailClientService extends AbstractService {

  /**
   * Construtor da classe.
   */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Recupera corpo de e-mail por id
   */
  public getPorId(id: number): Observable<any>{
    return this.http.get(`${environment.url}/corpoEmail/${id}`);
  }

  /**
   * Recupera a lista de corpos de e-mails.
   */
  public getCorposEmail(): Observable<any> {
    return this.http.get(`${environment.url}/corpoEmail`);
  }

  /**
   * Recupera a lista de corpos de e-mails de acordo com o filtro informado.
   */
  public getPorFiltro(filtro: any): Observable<any> {
    return this.http.post(`${environment.url}/corpoEmail/filtro`, filtro);
  }

  /**
   * Salvar Corpo de E-mail.
   * @param corpoEmail 
   */
  public salvar(corpoEmail: any){
    return this.http.post(`${environment.url}/corpoEmail`, corpoEmail);
  }

  /**
   * Salvar Corpo de E-mail.
   * @param corpoEmail
   */
  public validarDefinicao(idEmail: any) {
    return this.http.get(`${environment.url}/emailAtividadeSecundaria/${idEmail}/hasDefinicao`);
  }
}
