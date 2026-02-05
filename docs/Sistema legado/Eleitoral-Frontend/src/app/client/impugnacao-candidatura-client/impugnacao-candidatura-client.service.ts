import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class ImpugnacaoCandidaturaClientService extends AbstractService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
   }

  /**
   * Salva a solicitação de impugnação do profissional da chapa.
   *
   * @param impugnacao
   */
  public salvar(impugnacao: any): Observable<any> {
    let data = new FormData();
    this.appendFormData(impugnacao, data);
    return this.http.post(`${environment.url}/pedidosImpugnacao/salvar`, data);
  }

  /**
   * Salva a solicitação de impugnação do profissional da chapa.
   *
   * @param impugnacao
   */
  public salvarJulgamentoSegundaInstancia(impugnacao: any): Observable<any> {
    let data = new FormData();
    this.appendFormData(impugnacao, data);
    return this.http.post(`${environment.url}/julgamentosRecursoImpugnacao/salvar`, data);
  }

  /**
   * Recupera a eleição vigente para a chapa da eleição.
   */
  public getProfissionalImpugnado(idProfissional): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/consultarMembroChapa/${idProfissional}`);
  }

  /**
   * Valida documentos da declaração de cadastro da chapa.
   * 
   * @param arquivo 
   */
  public validarArquivo(arquivo: any): Observable<any> {
    return this.http.post(`${environment.url}/arquivo/validarArquivo`, arquivo);
  }

  /**
   * Retorna o id da atividade secundaria.
   */
  public getIdAtividadeSecundaria(): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/atividadeSecundaria`);
  }

  /**
   * Retorna as declarações para a atividade secuncadaria selecionada.
   */
  public getDeclaracoesAtividade(id): Observable<any> {
    return this.http.get(`${environment.url}/atividadesSecundarias/${id}/declaracoes-atividade`);
  }

  /**
   * Retorna a solicitacao de impugncao do candidato.
   */
  public getSolicitacaoImpugnacao(id): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/${id}`);
  }
  
  /**
  * Retorna os pedidos de Impugnação.
  */
  public pedidosImpugnacaoChapa(id): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/quantidadePedidosParaCadaUf/${id}`);
  }
  
  /**
   * Dowlaod de arquivos do Recurso do julgamento.
   * 
   * @param id 
   */
  public getArquivoRecurso(id: number): Observable<any> {
    return this.http.get(`${environment.url}/recursosImpugnacao/${id}/download`, {
      headers: new HttpHeaders({
          'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    });
  }

  /**
   * Dowlaod de arquivos da Contrarrazao.
   * 
   * @param id 
   */
  public getArquivoContrarrazao(id: number): Observable<any> {
    return this.http.get(`${environment.url}/contrarrazaoRecursoImpugnacao/${id}/download`, {
      headers: new HttpHeaders({
          'Content-Type': 'application/json',
      }),
      observe: 'response' as 'body',
      responseType: 'blob' as 'blob'
    });
  }
  
  /**
   * Retorna arquivo de julgamento defesa de impugnação.
   *
   * @param idArquivo
   */
  public getArquivoJulgamentoSegundaInstancia(idArquivo: number): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/${idArquivo}/download`, {
        headers: new HttpHeaders({
            'Content-Type': 'application/json',
        }),
        observe: 'response' as 'body',
        responseType: 'blob' as 'blob'
    });
  }

  /**
   * Retorna todos os pedidos de impugnação de uma UF específica
   * @param id 
   */
  public pedidosImpugnacaoUf(id: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/pedidosPorUf/${id}`);
  }

  /**
   * Retorna todos os pedidos de impugnação de uma UF e calendário específico
   * @param id 
   */
  public pedidosImpugnacaoCalendarioUf(idCalendario: number, id: number): Observable<any> {
    return this.http.get(`${environment.url}/pedidosImpugnacao/calendario/${idCalendario}/pedidosPorUf/${id}`);
  }

  public downloadArquivoImpugnacao(idArquivoPedidoImpugnacao): Observable<any> {
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
    return this.http.get(`${environment.url}/pedidosImpugnacao/documento/${idArquivoPedidoImpugnacao}/download`, options);
  }

  /**
   * Retorna o pedido de substituição de impugnação
   */
  public getSubstituicaoImpugnacao(id): Observable<any> {
    return this.http.get(`${environment.url}/substituicaoImpugnacao/pedidoImpugnacao/${id}`);
  }

}
