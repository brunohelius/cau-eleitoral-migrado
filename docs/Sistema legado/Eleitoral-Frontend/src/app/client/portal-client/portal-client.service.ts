import { Observable } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';
import { Injectable } from '@angular/core';

/**
 * Classe resolve responsável pela busca das informações no portal corporativo.
 *
 * @author Squadra Tecnologia
 */
@Injectable({
  providedIn: 'root'
})
export class PortalClientService extends AbstractService {
  private readonly CODIGO_MODULO_ELEITORAL = 3;

  /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
      super();
  }


  /**
     * Retorna a lista de declarações para o módulo eleitoral.
     * 
     * @return
     */
    public getDeclaracoes(): Observable<any> {
      let filtro = {
        "idModulo": 3
      }

      return this.http.post(`${environment.urlPortal}/declaracoes/filtro`, filtro);
  }

  /**
   * Retorna declaração filtrando por id
   * @return
   */
  public getDeclaracoesById(idDeclaracao): Observable<any> {
    return this.http.get(`${environment.urlPortal}/declaracoes/${idDeclaracao}`);
  }
}
