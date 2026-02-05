import {Observable} from 'rxjs';
import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {environment} from '../../../environments/environment';
import {AbstractService} from '../abstract.service';


/**
 * Classe de integração com o serviço de Calendário.
 */
@Injectable({
    providedIn: 'root'
})
export class EleicaoClientService extends AbstractService {
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Metodo que requisita a API de calendarios trazendo uma lista baseado nos filtros passados.
     * @param filtro
     */
    public getEleicoesFilter(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/filtro`, filtro);
    }

    /**
     * Metodo que requisita a API de calendarios trazendo uma lista apenas que o profissional logado faz parte de uma comissão.
     * @param filtro 
     */
    public getEleicoesMembroComissao(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/membroComissao`);
    }

    /**
     * Metodo que requisita a API de calendarios trazendo um calendario.
     */
    public getEleicao(id: number): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${id}`);
    }

    /**
     * Metodo que requisita a API de calendarios trazendo uma lista.
     */
    public getEleicoes(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/`);
    }

    /**
     * Metodo que requisita a API de anos de eleição trazendo uma lista baseado nos filtros passados.
     * @param filtro
     */
    public getAnosFilter(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/anos/filtro`, filtro);
    }

    /**
     * Metodo que requiita a API de calendarios os tipo de processos
     */
    public getTipoProcessos(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/tiposProcessos`)
    }

    /**
     * Metodo que requiita a API de membros da comissão os tipo de participação
     */
    public getTipoParticipacao(): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/tipoParticipacao`)
    }

    /**
     * Metodo que requisita a API de atividades secundárias a atividade vigente.
     */
    public getAtividadeSecundariaVigente(atividade: any): Observable<any> {
        return this.http.get(`${environment.url}/atividadesSecundarias/nivelPrincipal/${atividade.principal}/nivelSecundaria/${atividade.secundaria}`);
    }

    /**
     * Retorna o arquivo conforme o id do Calendario Informado
     *
     * @param idResolucao
     */
    public downloadArquivo(idResolucao: number): Observable<Blob> {
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

        return this.http.get(`${environment.url}/calendarios/arquivo/${idResolucao}/download`, options);
    }

    /**
     * Metodo que recupera os dados da configuração da eleição
     * @param idEleicao
     */
    public getEleicaoConfiguracao(idEleicao: number): Observable<any>{
        return this.http.get(`${environment.url}/informacaoComissaoMembro/${idEleicao}`);
    }

    /**
     * Salva os membros de uma comissao eleitoral
     * @param comissaoMembrosEleicao
     */
    public saveComissaoMembro(comissaoMembrosEleicao: any): Observable<any>{
        return this.http.post(`${environment.url}/membroComissao/salvar`, comissaoMembrosEleicao);
    }

    /**
     * Recupera uma lista do historico de inclusão/alteração membros da comissão
     * @param idComissao
     */
    public getHistoricoMembrosComissao(idComissao: number): Observable<any>{
        return this.http.get(`${environment.url}/informacaoComissaoMembro/${idComissao}/historico`);
    }

    /**
     * Recupera a lista de membros das comissões agrupados por CAU/UF
     * @param params
     */
    public getMembroComissoes(params: any): Observable<any>{
        return this.http.post(`${environment.url}/membroComissao/filtro`, params);
    }

    /**
     * Recupera as listas de membros das comissões agrupados por CAU/UF
     * @param idInformacaoComissao
     */
    public getMembrosComissoes(idInformacaoComissao: number): Observable<any>{
        return this.http.get(`${environment.url}/membroComissao/informacaoComissaoMembro/${idInformacaoComissao}/resumo`);
    }

}
