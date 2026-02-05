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
    let data = new FormData();
    this.appendFormData(membro, data);
    return this.http.post(`${environment.url}/pedidosSubstituicaoChapa/salvar`, data);
  }

  /**
   * Salvar a Substituição do Membro Chapa.
   */
  public salvarJulgamento(julgamento): Observable<any> {
    let data = new FormData();
    this.appendFormData(julgamento, data);
    return this.http.post(`${environment.url}/julgamentosSubstituicao/salvar`, data);
  }

  /**
   * Salvar a Substituição do Membro Chapa.
   */
  public salvarRecursoJulgamento(julgamento): Observable<any> {
    let data = new FormData();
    this.appendFormData(julgamento, data);
    return this.http.post(`${environment.url}/julgamentosRecursoSubstituicao/salvar`, data);
  }

  public getAtividadeSecundariaCadastro(idPedidoSubstituicao: any): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosSubstituicao/${idPedidoSubstituicao}/atividadeSecundariaCadastro`);
  }

  /*
  * Retorna a quantidade de pedidos de substituição para cada UF
  * e o total para IES
  */
  public getQuantidadePedidosParaCadaUf(id): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/quantidadePedidosParaCadaUf/${id}`);
  }

  /**
   * Retorna a quantidade de pedidos de substituição de uma 
   * UF específica
   * @param id 
   */
  public getPedidosPorUfCalendario(idCalendario: number, idCauUF: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/calendario/${idCalendario}/pedidosPorUf/${idCauUF}`);
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
    let options = {
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
   * Retorna documento em formato PDF no substituição do membro da chapa.
   * 
   * @param idPedidoSubistituicao
   */
  public getDocumentoPedidoSubstituicaoMembro(idPedidoSubistituicao: number): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      // Ignore this part or  if you want full response you have
      // to explicitly give as 'body'as http client by default give res.json()
      observe: 'response' as 'body',
      // have to explicitly give as 'blob' or 'json'
      responseType: 'blob' as 'blob'
    };
    return this.http.get(`${environment.url}/pedidosSubstituicaoChapa/${idPedidoSubistituicao}/pdf`, options);
  }

  /**
  * Retorna Calendário que possui substituições por filtro
  */
  public getCalendariosPorFiltro(filtro: any): Observable<any> {
    return this.http.post(`${environment.url}/calendarios/filtro`, filtro);
  }

  /**
  * Retorna Julgamento de acordo com o id do pedido de substituição
  */
  public getJulgamentoPorPedidoSubstituicao(id): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosSubstituicao/pedidoSubstituicao/${id}`);
  }
  
  /**
  * Retorna Julgamento de acordo com o id do pedido de substituição
  */
 public getJulgamentoSegundaInstaciaPorPedidoSubstituicao(idPedidoSubstituicao): Observable<any> {
  return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/pedidoSubstituicao/${idPedidoSubstituicao}`);
}

/**
  * Retorna Atividade do Julgamento do recurso de acordo com o id do pedido de substituição
  */
 public getAtividadeJulgamentoRecursoSubstituicao(idPedidoSubstituicao): Observable<any> {
  return this.http.get(`${environment.url}/julgamentosRecursoSubstituicao/${idPedidoSubstituicao}/atividadeSecundariaJulgamento`);
}

  /**
   * Retorna o documento do julgamento de substituição.
   * 
   * @param id 
   */
  public getDocumentoJulgamento(idJulgamentoSubistituicao): Observable<any> {
    let options = {
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
    let options = {
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
   * Retorna documento em formato PDF no substituição do membro da chapa.
   * 
   * @param idPedidoSubistituicao
   */
  public getDocumentoPdfJulgamentoSubstituicao(idPedidoSubistituicao: number): Observable<any> {
    let options = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json',
      }),
      // Ignore this part or  if you want full response you have
      // to explicitly give as 'body'as http client by default give res.json()
      observe: 'response' as 'body',
      // have to explicitly give as 'blob' or 'json'
      responseType: 'blob' as 'blob'
    };
    return this.http.get(`${environment.url}/julgamentosSubstituicao/${idPedidoSubistituicao}/pdf`, options);
  }

  /**
   * Retorna atividade secundário(2.5) do recurso de acordo com o pedido de substituição.
   */
  public getAtividadeSecundariaRecursoPorSubstituicao(idPedidoSubistituicao: number): Observable<any> {
    return this.http.get(`${environment.url}/recursosSubstituicao/${idPedidoSubistituicao}/atividadeSecundariaRecurso`);
  }

}
