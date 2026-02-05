import { Injectable } from '@angular/core';
import { AbstractService } from '../abstract.service';
import {HttpClient, HttpHeaders} from '@angular/common/http';

import { Observable } from 'rxjs';
import { Constants } from 'src/app/constants.service';
import { environment } from '../../../environments/environment';

/**
 * Classe com as possíves representações da 'Situação' das 'Atividades Secundárias'.
 *
 * @author Squadra Tecnologia
 */
export class SituacaoAtividadeSecundaria {

    public static SITUACAO_INDEFINIDA: SituacaoAtividadeSecundaria = new SituacaoAtividadeSecundaria('LABEL_HIFEN', '#000000');
    public static SITUACAO_AGUARDANDO_PARAMETRIZACAO: SituacaoAtividadeSecundaria = new SituacaoAtividadeSecundaria('LABEL_AGUARDANDO_PARAMETRIZACAO', '#E13F26');
    public static SITUACAO_AGUARDANDO_DOCUMENTO: SituacaoAtividadeSecundaria = new SituacaoAtividadeSecundaria('LABEL_AGUARDANDO_DOCUMENTO', '#FF7B24');
    public static SITUACAO_PARAMETRIZACAO_CONCLUIDA: SituacaoAtividadeSecundaria = new SituacaoAtividadeSecundaria('LABEL_PARAMETRIZACAO_CONCLUIDA', '#5F99D6');

    /**
     * Construtor da classe.
     *
     * @param descricao
     * @param cor
     */
    constructor(public descricao: any, public cor: any) {
    }

    /**
     * Retorna a 'Situação' da 'Atividade Secundária', de acordo com os níveis (Principal/Secundária).
     *
     * @param atividadePrincipal
     * @param atividadeSecundaria
     */
    public static getSituacaoPorNiveis(atividadePrincipal: any, atividadeSecundaria: any): SituacaoAtividadeSecundaria {
        switch (atividadePrincipal.nivel) {
            case 1:
                return this.getSituacaoPorAtividadePrincipalUm(atividadeSecundaria);
            default:
                return SituacaoAtividadeSecundaria.SITUACAO_INDEFINIDA;
        }
    }

    /**
     * |------------------------------------------------------------------------------------------------------------------
     * | 1. Atividade Principal
     * |------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Retorna a 'Situação' da 'Atividade Secundária' pertencente à 'Atividade Principal 1'.
     *
     * @param atividadeSecundaria
     */
    private static getSituacaoPorAtividadePrincipalUm(atividadeSecundaria: any): SituacaoAtividadeSecundaria {
        switch (atividadeSecundaria.nivel) {
            case 1: // 1.1
                return this.getSituacaoAtividadePrincipalUmAtividadeSecundariaUm(atividadeSecundaria);
            case 2: // 1.2
                return this.getSituacaoAtividadePrincipalUmAtividadeSecundariaDois(atividadeSecundaria);
            case 3: // 1.3
                return SituacaoAtividadeSecundaria.SITUACAO_INDEFINIDA;
            case 4: // 1.4
                return this.getSituacaoAtividadePrincipalUmAtividadeSecundariaQuatro(atividadeSecundaria);
            case 5: // 1.5
                return SituacaoAtividadeSecundaria.SITUACAO_INDEFINIDA;
            default:
                return SituacaoAtividadeSecundaria.SITUACAO_AGUARDANDO_PARAMETRIZACAO;
        }
    }

    /**
     * Retorna a 'Situação' da 'Atividade Secundária 1.1'.
     *
     * @param atividadeSecundaria
     */
    private static getSituacaoAtividadePrincipalUmAtividadeSecundariaUm(atividadeSecundaria: any): SituacaoAtividadeSecundaria {

            var informacaoComissaoMembro = atividadeSecundaria.informacaoComissaoMembro;
            switch (true) {
                case (informacaoComissaoMembro && informacaoComissaoMembro.situacaoConcluido):
                    return SituacaoAtividadeSecundaria.SITUACAO_PARAMETRIZACAO_CONCLUIDA;
                case (informacaoComissaoMembro
                    && informacaoComissaoMembro.documentoComissaoMembro !== undefined
                    && Object.entries(informacaoComissaoMembro.documentoComissaoMembro).length > 0):
                    return SituacaoAtividadeSecundaria.SITUACAO_AGUARDANDO_DOCUMENTO;
                default:
                    return SituacaoAtividadeSecundaria.SITUACAO_AGUARDANDO_PARAMETRIZACAO;
            }
    }

  /**
   * Retorna a 'Situação' da 'Atividade Secundária 1.1'.
   *
   * @param atividadeSecundaria
   */
  private static getSituacaoAtividadePrincipalUmAtividadeSecundariaDois(atividadeSecundaria: any): SituacaoAtividadeSecundaria {
    var emailsAtividadeSecundaria = atividadeSecundaria.emailsAtividadeSecundaria;
    var emailsTipos = (emailsAtividadeSecundaria.length > 0) ? emailsAtividadeSecundaria[0].emailsTipos : undefined;
    switch (true) {
      case (emailsAtividadeSecundaria && emailsTipos && emailsTipos.length == Constants.ATIVIDADE_SECUNDARIA_UM_PONTO_DOIS_QUANTIDADE_EMAILS):
        return SituacaoAtividadeSecundaria.SITUACAO_PARAMETRIZACAO_CONCLUIDA;
      default:
        return SituacaoAtividadeSecundaria.SITUACAO_AGUARDANDO_PARAMETRIZACAO;
    }
  }

    /**
     * Retorna a 'Situação' da 'Atividade Secundária 1.4'.
     *
     * @param atividadeSecundaria
     */
    private static getSituacaoAtividadePrincipalUmAtividadeSecundariaQuatro(atividadeSecundaria: any): SituacaoAtividadeSecundaria {
        switch (true) {
            case (atividadeSecundaria.idDeclaracao != undefined):
                return SituacaoAtividadeSecundaria.SITUACAO_PARAMETRIZACAO_CONCLUIDA;
            default:
                return SituacaoAtividadeSecundaria.SITUACAO_AGUARDANDO_PARAMETRIZACAO;
        }
    }

}

@Injectable({
    providedIn: 'root'
})
export class AtividadeSecundariaClientService extends AbstractService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Recupera a atividade secundária de acordo com o 'id' informado.
     *
     * @param id
     */
    public getPorId(id: number): Observable<any> {
        return this.http.get(`${environment.url}/atividadesSecundarias/${id}`);
    }

    /**
     * Recupera os e-mails vinculados a atividade secundário de acordo com o 'id' informado.
     *
     * @param id
     */
    public getEmails(id: number): Observable<any> {
        return this.http.get(`${environment.url}/atividadesSecundarias/${id}/emails`);
    }

  /**
   * Retorna params de definição de e-mails de atividade secundária.
   *
   * @param id
   */
  public getParamsDefinicaoEmailsPorAtividadeSecundaria(id: number) {
    return this.http.get(`${environment.url}/atividadesSecundarias/${id}/definicao-emails`);
  }

  /**
   * Retorna params de definição de declarações de atividade secundária.
   *
   * @param id
   */
  public getParamsDefinicaoDeclaracoesPorAtividadeSecundaria(id: number) {
    return this.http.get(`${environment.url}/atividadesSecundarias/${id}/definicao-declaracoes`);
  }

  /**
   * Defini e-mails e declaração para a sub-atividade.
   * @param data
   */
  public definirDeclaracaoEmailPorAtivSecundaria(data: any) {
    return this.http.post(`${environment.url}/atividadesSecundarias/definir-emails-declaracoes`, data);
  }

  /**
   * Verifica se existem respostas para a sub-atividade informadas.
   * @param idAtividadeSecundaria
   */
  public getTotalRespostasDeclaracoes(idAtividadeSecundaria: number) {
    return this.http.get(`${environment.url}/atividadesSecundarias/${idAtividadeSecundaria}/total-resposta-declaracoes`);
  }

  /**
   * Requisita o calendario a qual a atividade secundaria pertence
   * @param idAtividadeSecundaria
   */
  public getCalendario(idAtividadeSecundaria: number): Observable<any> {
    return this.http.get(`${environment.url}/calendarios/atividadeSecundaria/${idAtividadeSecundaria}`);
  }

    /**
     * Retorna a lista de totais de profissional por uf
     * @return
     */
    public getQuantidadeProfissionalPorCauUf(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/profissionais/total/filtro`, filtro);
    }

  /**
   * Salva a proporção de conselheiro manualmente
   * @param profissionais
   */
  public salvarProporcaoConselheiro(profissionais: any): Observable<any> {
    return this.http.post(`${environment.url}/conselheiros/salvar`, profissionais);
  }

    /**
     * Atualiza as quantidade de profissionais e proporção de conselheiros
     * @param proporcao: any
     */
    public atualizaNumeroConselheiro(proporcao: any): Observable<any> {
        return this.http.post(`${environment.url}/conselheiros/total/atualizar`, proporcao);
    }

    /**
     * Gera o Extrato (PDF) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
     *
     * @param id
     */
    public gerarExtratoConselheirosZip(id: number): Observable<any> {
       let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({'Content-Type': 'application/json'})
        };
        return this.http.get(`${environment.url}/conselheiros/extrato/${id}/gerarZIP`, options);
    }

    /**
     * Gera o Extrato (PDF) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
     *
     * @param id
     */
    public gerarExtratoConselheirosPdf(id: number): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({'Content-Type': 'application/json'})
        };
        return this.http.get(`${environment.url}/conselheiros/extrato/${id}/gerarPDF`, options);
    }

    /**
     * Gera o Extrato (Excel) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
     *
     * @param id
     */
    public gerarExtratoConselheirosExcel(id: number): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({'Content-Type': 'application/json'})
        };
        return this.http.get(`${environment.url}/conselheiros/extrato/${id}/gerarXLS`, options);
    }

    /**
     * Retorna o extrato do Número de Conselheiros, de acordo com o ID da atividade secundária.
     *
     * @param id
     */
    public getExtratoConselheirosPorAtividadeSecundaria(id: number) {
        return this.http.get(`${environment.url}/conselheiros/extrato/${id}`);
    }

    /**
     * Retorna o histórico do Número de Conselheiros, de acordo com o ID da atividade secundária.
     *
     * @param id
     */
    public getHistoricoConselheirosPorAtividadeSecundaria(id: number) {
        return this.http.get(`${environment.url}/conselheiros/atividadeSecundaria/${id}/historico`);
    }

    /**
     * Gera o Extrato (Excel) referente ao 'Número de Conselheiros', conforme o ID da atividade secundaria.
     *
     * @param id
     */
    public gerarConselheirosFilexls(idAtividadeSecundaria: number): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({'Content-Type': 'application/json'})
        };
        return this.http.get(`${environment.url}/conselheiros/atividadeSecundaria/${idAtividadeSecundaria}/gerarXLS`, options);
    }

    /**
     * Gera o Extrato (PDF) referente ao 'Número de Conselheiros', conforme o ID da atividade secundaria.
     *
     * @param id
     */
    public gerarConselheirosFilepdf(idAtividadeSecundaria: number): Observable<any> {
        let options = {
            observe: 'response' as 'body',
            responseType: 'blob' as 'blob',
            headers: new HttpHeaders({'Content-Type': 'application/json'})
        };
        return this.http.get(`${environment.url}/conselheiros/atividadeSecundaria/${idAtividadeSecundaria}/gerarPDF`, options);
    }
}
