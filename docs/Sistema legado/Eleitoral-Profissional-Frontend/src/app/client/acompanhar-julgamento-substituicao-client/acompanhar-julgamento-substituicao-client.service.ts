import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})

export class AcompanharJulgamentoSubstituicaoClient extends AbstractService {


  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }

  /**
  * Retorna os pedidos de Chapa de acordo com o id da solicitacao.
  */
  public julgamentoSubstituicaoComissao(id: any): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosSubstituicao/membroComissao/pedidoSubstituicao/${id}`);
  }
  
  /**
  * Retorna os pedidos de Chapa de acordo com o id da solicitacao.
  */
  public julgamentoSubstituicaoChapa(id: any): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosSubstituicao/responsavelChapa/pedidoSubstituicao/${id}`);
  }

  /**
  * Retorna os pedidos de Chapa de acordo com o id da solicitacao.
  */
 public julgamentoSubstituicaoComissaoSegundaInstancia(id: any): Observable<any> {
  return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/membroComissao/pedidoSubstituicao/${id}`);
}

/**
* Retorna os pedidos de Chapa de acordo com o id da solicitacao.
*/
public julgamentoSubstituicaoChapaSegundaInstancia(id: any): Observable<any> {
  return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/responsavelChapa/pedidoSubstituicao/${id}`);
}


}
