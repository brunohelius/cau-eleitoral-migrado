import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DocumentoComissaoMembroClientService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) { }

  /**
   * Método responsável por salvar uma informação de comissão membro.
   *
   * @param documentoComissaoMembro
   */
  public salvar(documentoComissaoMembro: any): Observable<any> {
    return this.http.post(`${environment.url}/documentoComissaoMembro`, documentoComissaoMembro);
  }

  /**
   * Recupera o documento de comissão membro de acordo com o 'id' informado.
   *
   * @param id
   */
  public getPorId(id: number): Observable<any> {
    return this.http.get(`${environment.url}/documentoComissaoMembro/${id}`);
  }
}
