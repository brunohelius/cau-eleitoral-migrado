import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})

export class AcompanharImpugnacaoClientService extends AbstractService {

  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }


  /**
  * Retorna os pedidos de Impugnação.
  */
  public pedidosImpugnacaoChapa(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/quantidadePedidosParaCadaUf`);
  }

  /**
  * Retorna os pedidos de Impugnação.
  */
  public pedidosImpugnacaoChapaPorSolicitante(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/quantidadePedidosParaCadaUfSolicitante`);
  }

  /**
   * Retorna todos os pedidos de impugnação da chapa do responsável logado
   * @param id 
   */
  public pedidosImpugnacaoUfResponsavelChapa(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/pedidosPorResponsavelChapa`);
  }

  /**
   * Retorna todos os pedidos de impugnação da chapa do responsável logado
   * @param id 
   */
  public pedidosImpugnacaoUfResponsavelSolicitante(id: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/pedidosPorProfissionalSolicitante/${id}`);
  }


  /**
   * Retorna todos os pedidos de impugnação de uma UF específica
   * @param id 
   */
  public pedidosImpugnacaoUf(id: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/pedidosPorUf/${id}`);
  }

}
