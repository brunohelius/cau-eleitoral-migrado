import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ProfissionalClientService extends AbstractService {

  /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
      super();
  }


  /**
     * Retorna um profissional por cpf.
     * 
     * @return
     */
    public getProfissional(cpf: any): Observable<any> {
      return this.http.get(`${environment.urlPortal}/profissionais/cpf/${cpf}`);
  }

}
