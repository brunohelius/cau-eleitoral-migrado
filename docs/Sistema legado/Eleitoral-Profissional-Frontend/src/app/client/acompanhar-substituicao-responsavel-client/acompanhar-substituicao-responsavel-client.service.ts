import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})

export class AcompanharSubstituicaoResponsavelService extends AbstractService {


  /**
  * Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }


  /**
   * Retorna os pedidos de Chapa de acordo com o Responsavel da Chapa Logado.
   */
  public getPedidosChapaPorResponsavelChapa(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/pedidosChapaPorResponsavelChapa`);
  }
}

/**
 * Classe service responsável por prover os recursos associadas a 'Status da Substituição'.
 *
 * @author Squadra Tecnologia
 */
export class StatusSubstituicao {

  public static ANDAMENTO: StatusSubstituicao = new StatusSubstituicao(1, "Em andamento", "bg-warning");
  public static DEFERIDO: StatusSubstituicao = new StatusSubstituicao(2, "Deferido", "colorCircle");
  public static INDEFERIDO: StatusSubstituicao = new StatusSubstituicao(3, "Indeferido", "bg-danger");

  /**
   * Construtor da classe.
   *
   * @param id
   * @param descricao
   * @param style
   */
  constructor(public id: number, public descricao: string, public style: string) { }

  /**
   * Retorna a instância das 'Status da Substituição' conforme o 'id' informado.
   *
   * @param id
   */
  public static findById(id: number): StatusSubstituicao {
    switch (id) {
      case 1:
        return StatusSubstituicao.ANDAMENTO;
      case 2:
        return StatusSubstituicao.DEFERIDO;
      case 3:
        return StatusSubstituicao.INDEFERIDO;
      default:
        return undefined;
    }
  }
}
