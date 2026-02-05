import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';
import { environment } from 'src/environments/environment';

/**
 * Classe de integração com o serviço de 'Informação Comissão Membro'.
 */
@Injectable({
    providedIn: 'root'
})
export class JulgamentoFinalClientService extends AbstractService {

    /**
     * Construtor da classe.
     */
    constructor(private http: HttpClient) {
        super();
    }


    /**
     * Retorna a quantidade de chapas cadastradas pelo id do Calendário
     */
    public getQuantidadesChapas(idCalendario: number): Observable<any> {
        return this.http.get(`${environment.url}/chapas/quantidades/${idCalendario}`);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getDetalhamentoPorUfCalendario(idCalendario: number, idCauUf: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/chapas/calendario/${idCalendario}/cauUf/${idCauUf}`);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getDetalhamentoChapa(idChapa: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/chapaEleicao/${idChapa}`);
    }

    /**
     * Serviço para consultar os membros com pendências e sem pendências
     */
    public getMembrosPorSituacao(idChapa: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/membrosChapaParaIndicacao/chapa/${idChapa}`);
    }

  /**
   * Salva o julgamento da chapa.
   */
    public salvarJulgamento(julgamento: any): Observable<any> {
        const data = new FormData();
        this.appendFormData(julgamento, data);
        return this.http.post(`${environment.url}/julgamentosFinais/salvar`, data);
    }

  /**
   * Salva o julgamento de 2ª Instância da chapa.
   */
  public salvarJulgamentoSegundaInstancia(julgamento: any, tipo: string): Observable<any> {
    const data = new FormData();
    this.appendFormData(julgamento, data);

    switch (tipo) {
      case 'RECURSO':
        return this.http.post(`${environment.url}/julgamentoRecursoSegundaInstancia/salvar`, data);
      case 'SUBSTITUICAO':
        return this.http.post(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/salvar`, data);
      case 'RECURSO_SUBSTITUICAO': // TODO - Proxima Sprint HST167 - OS1632
        return this.http.post(`${environment.url}/julgamentoRecursoPedidoSubstituicao/salvar`, data);
    }
  }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getJulgamentoFinalPrimeira(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamento/chapa/${idChapaEleicao}`);
    }

    /**
     * Retorna o documento do Julgamento Final.
     */
    public getDocumentoJulgamentoFinal(id: number): Observable<any> {
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
        return this.http.get(`${environment.url}/julgamentosFinais/${id}/download`, options);
    }

    public getArquivoDefesaImpugnacao(id): Observable<any> {
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
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/${id}/download`, options);
    }

    /**
     * Serviço para consultar de Pedido de substituição de julgamento final pro id chapa.
     *
     * @param idChapaEleicao
     */
    public getSubstituicaoJulgamentoPorChapa(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/chapa/${idChapaEleicao}`);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getRecursoJulgamentoFinal(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/recursoJulgamentoFinal/chapa/${idChapaEleicao}`);
    }

    /**
     * Retorna o documento do Julgamento Final.
     */
    public getDocumentoRecursoJulgamentoFinal(id: number): Observable<any> {
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
        return this.http.get(`${environment.url}/recursoJulgamentoFinal/${id}/download`, options);
    }

    /**
     * Serviço para consultar chapas de um calendário e uma uf específica
     */
    public getMembrosComPendencia(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamento/responsavelChapa/chapa/${idChapaEleicao}`);
    }

    /**
     * Serviço para consultar o julgamento de recurso de segunda instância por id do julgamento final
     */
    public getJulgamentoRecursoSegundaInstancia(idJulgamentoFinal: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoRecursoSegundaInstancia/julgamentoFinal/${idJulgamentoFinal}`);
    }

    /**
     * Retorna o documento do Julgamento do Recurso de Segunda Instância.
     */
    public getDocumentoJulgamentoRecursoSegundaInstancia(id: number): Observable<any> {
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
        return this.http.get(`${environment.url}/julgamentoRecursoSegundaInstancia/${id}/download`, options);
    }
        /**
     * Retorna o documento do Julgamento do Recurso de Segunda Instância.
     */
    public getDocumentoJulgamentoRecursoSegundaInstanciaSubstituicao(id: number): Observable<any> {
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
        return this.http.get(`${environment.url}/julgamentoRecursoPedidoSubstituicao/${id}/download`, options);
    }

    /**
     * Serviço para consultar o julgamento de substituição de segunda instância por id do julgamento final
     */
    public getJulgamentoSubstituicaoSegundaInstancia(idJulgamentoFinal: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/julgamentoFinal/${idJulgamentoFinal}`);
    }

    /**
     * Retorna o documento do Julgamento do Substituicao de Segunda Instância.
     */
    public getDocumentoJulgamentoSubstituicaoSegundaInstancia(id: number): Observable<any> {
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
        return this.http.get(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/${id}/download`, options);
    }

    /**
     * Serviço para consultar de julgamento final de segunda instância por id chapa.
     *
     * @param idChapaEleicao
     */
    public getJulgamentoSegundaInstanciaPorChapa(idChapaEleicao: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentosFinais/julgamentoSegundaInstancia/chapa/${idChapaEleicao}`);
    }

    /**
     * Recupera os arquivos...
     */
    public getArquivoRecursoSegundoJulgamentoSubstituicao(id): Observable<any> {
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
        return this.http.get(`${environment.url}/recursoSegundoJulgamentoSubstituicao/${id}/download`, options);
    }

    /**
     * Recupera os arquivos recurso substituição...
     */
    public getArquivoRecursoSubstituicao(idRecurso): Observable<any> {
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
      return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/recurso/${idRecurso}/download`, options);
  }



    /**
     * Serviço para consultar de Pedido de substituição de julgamento final por id.
     *
     * @param idChapaEleicao
     */
    public getSubstituicaoJulgamentoPorId(id: number): Observable<any> {
        return this.http.get(`${environment.url}/substituicaoJulgamentoFinal/${id}`);
    }

    /**
     * Retorna as pendências da chapa selecionada
     * @param idChapa
     */
    public getPedidosSolicitadosChapa(idChapa: number): Observable<any> {
        return this.http.get(`${environment.url}/chapas/${idChapa}/pedidosSolicitados`);
    }

    /**
     * Retorna as retificações por substituições
     * @param idSubstituicao
     */
    public getRetificacoesPorSubstituicao(idSubstituicao): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoSubstituicaoSegundaInstancia/retificacoes/${idSubstituicao}`);
    }

    /**
     * Retorna as retificações por substituições
     * @param idSubstituicao
     */
    public getRetificacoesPorRecurso(idRecurso): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoRecursoSegundaInstancia/retificacoes/${idRecurso}`);
    }

    /**
     * Retorna as retificações por substituições
     * @param idRecurso
     */
    public getRetificacoesPorRecursoPedidoSubstituicao(idRecurso): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoRecursoPedidoSubstituicao/retificacoes/${idRecurso}`);
    }

}
