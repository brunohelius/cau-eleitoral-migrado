import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';
import { map, catchError } from 'rxjs/operators';


/**
 * Classe de integração com o serviço de Calendário.
 */
@Injectable({
    providedIn: 'root'
})
export class CalendarioClientService extends AbstractService {

    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Retorna uma lista de instância de Eleicoes.
     * @return
     */
    public getCalendario(): Observable<any> {
        let filtro = {}
        return this.http.get(`${environment.url}/calendarios`);
    }

    /**
     * Retorna total de calendários por situação
     * @param idSituacao
     */
    public getTotalCalendariosPorSituacao(idSituacao: number) {
        return this.http.get(`${environment.url}/calendarios/${idSituacao}/total`);
    }

    /**
    * Retorna a lista de atividades
    * @param id Id do calendário
    * @param filtro
    */
    public getAtividadesPorCalendarioComFiltro(id: number, filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/${id}/atividadesPrincipais/filtro`, filtro);
    }

    /**
     * Retorna a lista de atividades
     * @param id Id do calendário
     * @param filtro
     */
    public getAtividadesPorCalendario(id: number): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/${id}/atividadesPrincipais/filtro`, null);
    }

    /**
     * Retorna lista de anos que contenham calendários com status igual a concluído.
     * @return
     */
    public getCalendariosConcluidosAnos() {
        return this.http.get(`${environment.url}/calendarios/concluidos/anos`);
    }

    /**
     * Retorna uma lista de anos das Eleicoes.
     * @return
     */
    public getAnos(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/anos`);
    }

    /**
     * Retorna uma lista de Eleicoes/ano.
     * @return
     */
    public getCalendariosAno(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/eleicoes`);
    }

    /**
     * Retorna uma lista de Tipo de Processos das Eleicoes.
     * @return
     */
    public getTipoProcesso(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/tiposProcessos`);
    }

    /**
     * Método respnsável por salvar calendario do Periodo.
     *
     * @param calendario
     */
    public salvarDadosPeriodo(calendario: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(calendario, data);
        return this.http.post(`${environment.url}/calendarios/salvar`, data);
    }

    /**
     * Recupera Atividades por calendario
     *
     * @param id
     */
    public getCalendarioPorId(id: any): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${id}`);
    }

    /**
     * Recupera Atividades por calendariocle
     */
    public getAtividades(): Observable<any> {
        return this.http.get(`${environment.url}/atividadesPrincipais`);
    }

    /**
    * Recupera Atividades por calendario
    */
    public getPesquisaFiltro(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/filtro`, filtro);
    }

    /**
    * Retorna Calendário por filtro
    */
    public getCalendariosPorFiltro(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/filtro`, filtro);
    }
    /**
     * busca por id Calendario Informado
     * @param id number
     */
    public getById(id: number): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${id}`);
    }

    /**
     * delete por Calendario Informado
     * @param calendario any
     */
    public deleteCalendario(item: any): Observable<any> {
        return this.http.delete(`${environment.url}/calendarios/${item.id}/excluir`);
    }

    /**
    * Inativar por id Calendario Informado
    * @param calendario
    */
    public inativarCalendarioPorId(calendario: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/inativar`, calendario);
    }

    /**
   * Retorna o arquivo conforme o id do Calendario Informado
   *
   * @param idResolucao
   */
    public downloadArquivo(idResolucao): Observable<Blob> {
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
     * Verifica se o arquivo está em conformidade com os seguintes critérios:
     *
     * O arquivo deve possuir um do(s) seguinte(s) formato(s): PDF.
     * O arquivo não pode ser maior que 10Mb.
     *
     * @param arquivoTO
     */
    public validarAnexoPDF(arquivoTO: any): Observable<any> {
        return this.http.post(`${environment.url}/arquivo/validacao/pdf`, arquivoTO);
    }

    /**
     * Retorna os Tipos de Atividades Principais de acordo com id do Calendario.
     * @return
     */
    public getAtividadesPrincipais(idCalendario: any): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${idCalendario}/atividadesPrincipais`);
    }

    /**
     * Salva os prazos informados.
     */
    public salvarPrazos(prazos: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/prazos/salvar`, prazos);
    }

    /**
     * Recupera os prazos de acordo com Calendario informado.
     *
     * @param idCalendario
     */
    public getPrazosPorCalendario(idCalendario: any): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${idCalendario}/prazos`);
    }

    /**
     * Recupera lista de filiais.
     *
     * @param idCalendario
     */
    public buscarCauUfsServiceAPI(): Observable<any> {
        let data = {
            tipoFilial: 7
        }

        return this.http.post(`${environment.urlAcesso}/api/filial/filter`, data);
    }

    /**
     * Conclui o calendário informado.
     *
     * @param calendario
     */
    public concluir(calendario: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/concluir`, calendario);
    }

    /**
     * Recupera o histórico do calendário de acordo com o 'id' do calendário informado.
     *
     * @param idCalendario
     */
    public getHistorico(idCalendario: any): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${idCalendario}/historico`);
    }

    /**
     * Recupera o número de membros da comissão para o calendário informado.
     *
     * @param idCalendario
     */
    public getAgrupamentoNumeroMembrosComissao(idCalendario: any): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${idCalendario}/numeroMembros`);
    }

    /**
     * Recupera todos os calendários disponíveis para publicação da comissão eleitoral.
     */
    public getCalendariosPublicacaoComissaoEleitoral(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/comissaoEleitoral/publicacao`);
    }

    /**
     * Recupera os anos dos calendários disponíveis para a publicação da comissão eleitoral.
     */
    public getAnosCalendarioPublicacaoComissaoEleitoral(): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/comissaoEleitoral/publicacao/anos`);
    }

    /**
     * Recupera os calendários para publicação de acordo com o filtro informado.
     *
     * @param filtro
     */
    public getCalendariosPublicacaoComissaoEleitoralPorFiltro(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/comissaoEleitoral/publicacao/filtro`, filtro);
    }

    /**
     * Retorna uma lista de Eleicoes/ano.
     * @return
     */
    public getCalendariosAnoPorFiltro(filtro: any): Observable<any> {
        return this.http.post(`${environment.url}/calendarios/anos/filtro`, filtro);
    }

    /**
     * Recupera o Calendário/Eleição de acordo com o ID informado.
     *
     * @param idCalendario
     */
    public getCalendarioEleicaoPorId(idCalendario: number): Observable<any> {
        return this.http.get(`${environment.url}/calendarios/${idCalendario}/eleicao`);
    }

}
