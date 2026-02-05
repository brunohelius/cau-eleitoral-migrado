import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

/**
 * Classe de integração com o serviço de 'Informação Comissão Membro'.
 */
@Injectable({
  providedIn: 'root'
})
export class InformacaoComissaoMembroService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) { }

  /**
   * Método responsável por salvar uma informação de comissão membro.
   *
   * @param informacaoComissaoMembro
   */
  public salvar(informacaoComissaoMembro: any): Observable<any> {
    return this.http.post(`${environment.url}/informacaoComissaoMembro`, informacaoComissaoMembro);
  }

  /**
   * Método responsável por salvar uma informação de comissão membro.
   *
   * @param documentoComissaoMembro
   */
  public concluir(documentoComissaoMembro: any): Observable<any> {
    return this.http.post(`${environment.url}/informacaoComissaoMembro/concluir`, documentoComissaoMembro);
  }
}
