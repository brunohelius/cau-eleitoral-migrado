import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class SubstiuicaoChapaClientService extends AbstractService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Busca o membro que será subistituido.
   *
   * @param idProfissional
   */
  public getMembroSubstituicao(idProfissional): Observable<any> {
    return this.http.get(`${environment.url}/membrosChapa/busca-substituicao/${idProfissional}`);
  }

  /**
   * Busca o membro subistituto.
   *
   * @param idProfissional
   */
  public buscarMembroSubstituto(idChapa, membro): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapa}/buscaSubstituto`, membro);
  }

  /**
   * Valida documentos da declaração de cadastro da chapa.
   *
   * @param arquivo
   */
  public validarArquivoJustificativaSubstituicao(arquivo: any): Observable<any> {
    return this.http.post(`${environment.url}/arquivo/validarArquivo`, arquivo);
  }

  /**
   * Recupera chapa eleitoral que profissional está participando.
   */
  public getChapaEleicaoSubistuicao(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/substituicao`);
  }

  /**
   * Salvar a Substituição do Membro Chapa.
   */
  public salvarSubstituicao(membro): Observable<any> {
    const data = new FormData();
    this.appendFormData(membro, data);
    return this.http.post(`${environment.url}/pedidosSubstituicaoChapa/salvar`, data);
  }


  /*
  * Retorna a quantidade de pedidos de substituição para cada UF
  * e o total para IES
  */
  public getQuantidadePedidosParaCadaUf(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/quantidadePedidosParaCadaUf`);
  }

  /**
   * Retorna a quantidade de pedidos de substituição de uma
   * UF específica
   * @param id
   */
  public getPedidosPorUf(id: number): Observable<any> {
    const idCauUf = id != undefined ? `/${id}` : '';

    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/pedidosPorUf${idCauUf}`);
  }

  /**
   * Retorna o pedido de substituição a partir do id.
   *
   * @param id
   */
  public getPedidoSubstituicaoChapa(id: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/${id}`);
  }

  /**
   * Retorna o julgamento de substituição 2° instância a partir do id.
   *
   * @param id
   */
  public getJulgamentoSubstituicaoSegundaResponsavel(id: number): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/responsavelChapa/pedidoSubstituicao/${id}`);
  }

  /**
   * Retorna o julgamento de substituição 2° instância a partir do id.
   *
   * @param id
   */
  public getJulgamentoSubstituicaoSegundaMembroComissao(id: number): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/membroComissao/pedidoSubstituicao/${id}`);
  }

  /**
   * Retorna o tipo de profissional logado.
   *
   * @param id
   */
  public getTipoConselheiroProfissionalLogado(): Observable<any> {
    return this.http.get(`${environment.url}/membroComissao/tipoConselheiroProfissionalLogado`);
  }


  /**
   * Retorna o documento do pedido de substituição.
   *
   * @param id
   */
  public getDocumentoSubstituicao(idPedidoSubistituicao): Observable<any> {
    const options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      // Ignore this part or  if you want full response you have
      // to explicitly give as 'body'as http client by default give res.json()
      observe: 'response' as 'body',
      // have to explicitly give as 'blob' or 'json'
      responseType: 'blob' as 'blob'
    };
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/${idPedidoSubistituicao}/download`, options);
  }

  /**
   * Retorna o documento do julgamento de substituição.
   *
   * @param id
   */
  public getDocumentoJulgamento(idJulgamentoSubistituicao): Observable<any> {
    const options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      // Ignore this part or  if you want full response you have
      // to explicitly give as 'body'as http client by default give res.json()
      observe: 'response' as 'body',
      // have to explicitly give as 'blob' or 'json'
      responseType: 'blob' as 'blob'
    };
    return this.http.get(`${environment.url}/julgamentosSubstituicao/${idJulgamentoSubistituicao}/download`, options);
  }

  /**
   * Retorna o documento do julgamento de substituição 2º instancia.
   *
   * @param id
   */
  public getDocumentoJulgamentoSegundaInstancia(idJulgamentoSubistituicao): Observable<any> {
    const options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      // Ignore this part or  if you want full response you have
      // to explicitly give as 'body'as http client by default give res.json()
      observe: 'response' as 'body',
      // have to explicitly give as 'blob' or 'json'
      responseType: 'blob' as 'blob'
    };
    return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/${idJulgamentoSubistituicao}/download`, options);
  }

  /**
   * Salvar recurso de substituição.
   *
   * @param recurso
   */
  public salvarRecurso(recurso: any): Observable<any> {
    const data = new FormData();
    this.appendFormData(recurso, data);
    return this.http.post(`${environment.url}/recursosSubstituicao/salvar`, data);
  }

  /**
   * Baixa arquivo de recurso.
   *
   * @param idArquivo
   */
  public getArquivoRecurso(idArquivo: any): Observable<any> {
    return this.http.get(`${environment.url}/recursosSubstituicao/${idArquivo}/download`, {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    });
  }

  /**
   * Retorna atividade secundário(2.5) do recurso de acordo com o pedido de substituição.
   */
  public getAtividadeSecundariaRecursoPorSubstituicao(idPedidoSubistituicao: number): Observable<any> {
    return this.http.get(`${environment.url}/recursosSubstituicao/${idPedidoSubistituicao}/atividadeSecundariaRecurso`);
  }

}
