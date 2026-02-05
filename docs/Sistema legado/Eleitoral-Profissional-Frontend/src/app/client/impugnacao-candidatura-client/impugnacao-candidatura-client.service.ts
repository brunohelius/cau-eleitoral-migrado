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
   * Retorna o julgamento a partir do id da impugnação.
   */
  public getJulgamentoSegundaInstanciaImpugnacao(id): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/pedidoImpugnacao/${id}`);
  }

  /**
   * Retorna o julgamento a partir do id da impugnação.
   */
  public getJulgamentoSegundaInstanciaMembroComissao(id): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/membroComissao/pedidoImpugnacao/${id}`);
  }
  
  /**
   * Retorna o julgamento a partir do id da impugnação.
   */
  public getJulgamentoSegundaInstanciaResponsavelImpugnante(id): Observable<any> {
    return this.http.get(`${environment.url}/julgamentosRecursoImpugnacao/responsavel/pedidoImpugnacao/${id}`);
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
