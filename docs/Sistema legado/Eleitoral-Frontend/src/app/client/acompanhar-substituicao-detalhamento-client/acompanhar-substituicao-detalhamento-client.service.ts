import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})

export class AcompanharSubstituicaoDetalhamentoService extends AbstractService {


  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }


  /**
   * Retorna os pedidos de Chapa de acordo com o id da chapa informado.
   */
  public pedidosSubstituicaoChapa(id: any): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/${id}`);
  }
}
