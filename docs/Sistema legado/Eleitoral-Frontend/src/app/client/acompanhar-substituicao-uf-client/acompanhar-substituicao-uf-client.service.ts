import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
    providedIn: 'root'
})

export class ChapaEleicaoClientService extends AbstractService {
   
   
   /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
  }


  /**
   * método que retorna a quantidade de solicitações de substituicoes por uf
   * @param idProfissional 
   */ 
  public getSolicitacoesSubstituicaoUf( idProfissional: number, idUf: number ): Observable<any>{
    return this.http.get(`${environment.url}/eleicao/solicitacoesSubstituicao/${idUf}`);
  }

  /**
   * método que retorna a quantidade de solicitações de substituicoes geral
   * @param idProfissional 
   */ 
  public getSolicitacoesSubstituicaoBr( idProfissional: number): Observable<any>{
    return this.http.get(`${environment.url}/eleicao/solicitacoesSubstituicao`);
  }
}
