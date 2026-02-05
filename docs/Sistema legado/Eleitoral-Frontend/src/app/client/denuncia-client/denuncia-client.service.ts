import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';


/**
 * Classe de integração com o serviço de Calendário.
 */
@Injectable({
    providedIn: 'root'
})
export class DenunciaClientService extends AbstractService {
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Recupera a Denuncia UF de acordo com a Atividade Secundária informada.
     *
     * @param idAtivSecundaria
     */
    public getAgrupamentoDenunciaUfPorCalendario(idCalendario: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/calendario/${idCalendario}/agrupamentoUf`);
    }

    /**
     * Recupera a Denuncia UF de acordo com o Cau UF informada.
     *
     * @param idCalendario
     * @param idCauUf
     */
    public getDetalhamentoDenunciaUfPorIdCauUf(idCauUf: number, idCalendario: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/calendario/${idCalendario}/cauUf/${idCauUf}/detalhamentoDenunciaUF`);
    }

    /**
     * Recupera a denuncia de acordo com o 'id' e 'tipoDenuncia' informado
     *
     * @param id
     * @param tipoDenuncia
     */
    public getDenunciaByIdAndTipoDenuncia(id: number, tipoDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${id}/tipoDenuncia/${tipoDenuncia}`);
    }

    /**
     * Recupera a denuncia de acordo com o 'id' informado
     *
     * @param id
     */
    public getDenunciaById(id: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${id}`);
    }

    /**
     * Recupera as abas disponíveis para a denuncia de acordo com o 'id' informado
     *
     * @param id
     */
    public getAbasDisponiveisByIdDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/abasDisponiveis`);
    }

    /**
     * Recupera as condições da denuncia para manipulação de layout
     * @param idDenuncia
     */
    public getCondicaoDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/condicao`);
    }

    /**
     * Recupera a retificacao do julgamento da denuncia de acordo com o 'idRetificacao' informado
     *
     * @param idRetificacao
     */
    public getRetificacaoJulgamentoDenuncia(idRetificacao: number): Observable<any> {
        return this.http.get(`${environment.url}/retificacaoJulgamentoDenuncia/${idRetificacao}`);
    }

    /**
     * Recupera a retificacao do julgamento do recurso de acordo com o 'idRetificacao' informado
     *
     * @param idRetificacao
     */
    public getRetificacaoJulgamentoRecurso(idRetificacao: number): Observable<any> {
        return this.http.get(`${environment.url}/retificacaoJulgamentoRecurso/${idRetificacao}`);
    }

    /**
     * Recupera os encaminhamentos de defesa da denuncia de acordo com o 'idDenuncia' informado
     *
     * @param idDenuncia
     * @param tipoDenuncia
     */
    public getEncaminhamentosDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/denuncia/${idDenuncia}`);
    }

    /**
     * Recupera os julgamentos retificados da denuncia de acordo com o 'idDenuncia' informado
     *
     * @param idDenuncia
     * @param tipoDenuncia
     */
    public getJulgamentosRetificadosDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/retificacoesJulgamentoDenuncia/denuncia/${idDenuncia}`);
    }

    /**
     * Recupera os recursos de julgamentos retificados da denuncia de acordo com o 'idDenuncia' informado
     *
     * @param idDenuncia
     * @param tipoDenuncia
     */
    public getRecursoJulgamentosRetificadosDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoRecurso/denuncia/${idDenuncia}`);
    }

    /**
     * Recupera os tipos de julgamento
     *
     * @param idDenuncia
     * @param tipoDenuncia
     */
    public getTiposJulgamentoPorDenuncia(): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoDenuncia/tiposJulgamento`);
    }

    /**
     * Recupera os tipos de sentença do julgamento
     *
     * @param idDenuncia
     * @param tipoDenuncia
     */
    public getTiposSentencaJulgamento(): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoDenuncia/tiposSentencaJulgamento`);
    }

    /**
     * Salva julgamento de uma Denuncia.
     *
     * @param julgamentoDenuncia
     */
    public salvarJulgamentoDenuncia(julgamentoDenuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(julgamentoDenuncia, data);
        return this.http.post(`${environment.url}/julgamentoDenuncia/salvar`, data);
    }

    /**
     * Salva julgamento de uma Denuncia.
     *
     * @param julgamentoDenuncia
     */
    public salvarJulgamentoRecurso(julgamentoDenuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(julgamentoDenuncia, data);
        return this.http.post(`${environment.url}/julgamentoRecurso/salvar`, data);
    }

    /**
     * Realiza download de denúncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivo(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denuncia/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de denúncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoProvas(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denunciaProvas/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de denúncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoEncaminhamento(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/encaminhamentoDenuncia/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de denúncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoParecerFinal(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/encaminhamentoDenuncia/parecerFinal/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de denúncia inadmitida por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoInadmitida(idArquivo: number): Observable<any> {
        let options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you havedenuncia.module
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };

        return this.http.get(`${environment.url}/denuncia/inadmitida/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de denúncia admitida por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoAdmitida(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denuncia/admitida/arquivo/${idArquivo}/download`, options);
    }

    /**
     * Realiza download de defesa da denúncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoDefesa(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denunciaDefesa/arquivo/${idArquivo}/download`, options);
    }

     /**
     * Realiza download do Recurso ou Contrarrazão.
     *
     * @param idArquivo
     */
    public downloadArquivoRecursoContrarrazao(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/contrarrazaoRecursoDenuncia/${idArquivo}/download`, options);
    }

    /**
     * Realiza download do Recurso ou Contrarrazão.
     *
     * @param idArquivo
     */
    public downloadArquivoJulgamento(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/julgamentoDenuncia/${idArquivo}/download`, options);
    }

    /**
     * Realiza download do Julgamento do Recurso.
     *
     * @param idArquivo
     */
    public downloadArquivoJulgamentoRecurso(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/julgamentoRecurso/${idArquivo}/download`, options);
    }


    /**
     *
     * @param idArquivo
     */
    public downloadArquivoJulgamentoAdmissibilidade(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denuncia/julgamento_admissibilidade/arquivo/${idArquivo}/download`, options);
    }

       /**
     *
     * @param idArquivo
     */
    public downloadArquivoRecursoJulgamentoAdmissibilidade(idArquivo: number): Observable<any> {
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

        return this.http.get(`${environment.url}/denuncia/recurso/arquivo/${idArquivo}`, options);
    }

    /**
     * Valida o formato e tamanho do arquivo de denuncia.
     *
     * @param arquivoDenuncia
     */
    public validarArquivoDenuncia(arquivoDenuncia: any): Observable<any> {
        return this.http.post(`${environment.url}/arquivo/validarArquivoDenuncia`, arquivoDenuncia);
    }

    /**
     * Realiza download do arquivo anexado na audiencia de instrução pelo id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoAudienciaInstrucao(idArquivo: number): Observable<any> {

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

        return this.http.get(`${environment.url}/audienciaInstrucao/documento/${idArquivo}/download`, options);
    }

    /**
     * Realiza download do Encaminhamento Denuncia por id do arquivo.
     *
     * @param idArquivo
     */
    public downloadArquivoEncaminhamentoDenuncia(idArquivo: number): Observable<any> {
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
        return this.http.get(`${environment.url}/encaminhamentoDenuncia/arquivo/${idArquivo}/download`, options);
    }


    public downloadArquivoJulgamentoRecursoAdmissibilidade(idArquivo: number): Observable<any> {
        let options = {
            headers: new HttpHeaders({
                'Content-Type': 'application/json',
            }),
            // Ignore this part or  if you want full response you havedenuncia.module
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };

        return this.http.get(`${environment.url}/denuncia/julgamento-recurso-admissibilidade/arquivo/${idArquivo}`, options);
    }


    /**
     * Valida se o usuário é o relator atual.
     *
     * @param idDenuncia
     * @return
     */
    public validarRelatorAtual(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validarRelatorAtual`);
    }

    /**
     * Valida se existe um encaminhamento de impedimento ou suspeição com status pendente.
     *
     * @param idDenuncia
     * @return
     */
    public validarImpedimentoPendente(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validarImpedimentoPendente`);
    }

    /**
     * Valida se existe defesa ou o prazo de defesa esta encerrado.
     *
     * @param idDenuncia
     * @return
     */
    public validarDefesaPrazo(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validarDefesaPrazo`);
    }

    /**
     * Recupera o encaminhamento de acordo com o 'idEncaminhamento' informado
     *
     * @param idEncaminhamento
     * @param encaminhamento
     */
    public getEncaminhamentosProvas(idEncaminhamento: number): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentoDenuncia/visualizarProvas/${idEncaminhamento}`);
    }

    /**
     * Recupera o Impedimento/Suspeicao de acordo com o 'idEncaminhamento' informado
     *
     * @param idEncaminhamento
     * @param encaminhamento
     */
    public getImpedimentoSuspeicao(idEncaminhamento: number): Observable<any> {
        return this.http.get(`${environment.url}/impedimentoSuspeicao/encaminhamentosDenuncia/${idEncaminhamento}`);
    }
    /**
    * Busca os dados dos encaminhamentos do tipo Audiência de Instrução
    *
    * @param idEncaminhamento
    */
    public getAudienciaInstrucao(idEncaminhamento: any): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/encaminhamento/${idEncaminhamento}`)
    }

    /**
     * Valida se o Usuario Logado tem Acesso a Acompanhamento da Denuncia do modulo Corporativo.
     *
     * @param idDenuncia
     * @return
     */
    public validarAcessoDenunciaCorporativoPorDenuncia(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validarAcessoAcompanharCorporativo`);
    }

    /**
     * Recupera o Alegação Final de acordo com o 'idEncaminhamento' informado
     *
     * @param idEncaminhamento
     */
    public getAlegacoesFinais(idEncaminhamento: number): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/alegacaoFinal/${idEncaminhamento}`);
    }

    /**
     * Busca os dados dos encaminhamentos do tipo Parecer Final
     *
     * @param idEncaminhamento
     */
    public getParecerFinal(idEncaminhamento: any): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/parecerFinal/${idEncaminhamento}`)
    }

    /**
     * Gera o documento com os dados das abas disponíveis para denúncia.
     *
     * @param idEncaminhamento
     */
    public gerarDocumento(geracaoDocumento: any): Observable<any> {
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
        return this.http.post(`${environment.url}/denuncia/gerarDocumento`, geracaoDocumento, options);
    }

    /**
     * Realiza download do arquivo anexado na Alegação Final pelo id do arquivo.
     *
     * @param idArquivo
     */
    public downloadAlegacaoFinal(idArquivo: number): Observable<any> {
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
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/alegacaoFinal/arquivo/${idArquivo}/download`, options);
    }


    public submitJulgamentoAdmissibilidade(idDenuncia: number, data: {}): Observable<any> {
        const postData = new FormData();
        this.appendFormData(data, postData);
        return this.http.post(`${environment.url}/denuncia/${idDenuncia}/julgar_admissibilidade`, postData);
    }

    public submitJulgamentoRecursoAdmissibilidade(data: {}): Observable<any> {
        const postData = new FormData();
        this.appendFormData(data, postData);
        return this.http.post(`${environment.url}/denuncia/julgamento-recurso-admissibilidade`, postData);
    }

    public verificaPrazoRecursoAdmissibilidade(idJulgamentoAdmissibilidade: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/recurso/validar-prazo-recurso-julgamento-admissibilidade/${idJulgamentoAdmissibilidade}`)
    }
}


/**
 * Classe service responsável por prover os recursos associadas a 'Status da Denuncia'.
 *
 * @author Squadra Tecnologia
 */
export class StatusDenuncia {

    public static ANALISE_ADMISSIBILIDADE: StatusDenuncia = new StatusDenuncia(1, "Em análise de admissibilidade", "bg-warning");
    public static JULGAMENTO: StatusDenuncia = new StatusDenuncia(2, "Em julgamento", "colorCircle");
    public static RELATORIA: StatusDenuncia = new StatusDenuncia(3, "Em relatoria", "bg-danger");
    public static AGUARDANDO_RELATOR: StatusDenuncia = new StatusDenuncia(4, "Aguardando Relator", "bg-danger");
    public static EM_RECURSO: StatusDenuncia = new StatusDenuncia(5, "Em recurso", "bg-danger");
    public static JULGADO_PRIMEIRA_INSTANCIA: StatusDenuncia = new StatusDenuncia(6, "Julgado em 1ª Instância", "bg-success");
    public static JULGAMENTO_ADMITIDA: StatusDenuncia = new StatusDenuncia(7, "Em julgamento 1ª instância", "colorCircle");
    public static AGUARDANDO_CONTRARRAZAO: StatusDenuncia = new StatusDenuncia(8, "Aguardando contrarrazão", "colorCircle");
    public static TRANSITADO_JULGADO: StatusDenuncia = new StatusDenuncia(9, "Transitado em julgado", "colorCircle");
    public static JULGAMENTO_SEGUNDA_INSTANCIA: StatusDenuncia = new StatusDenuncia(10, "Em julgamento 2ª instância", "colorCircle");
    public static JULGAMENTO_RECURSO_ADMISSIBILIDADE : StatusDenuncia = new StatusDenuncia(11, "Em julgamento do recurso de admissibilidade", "bg-warning");
    public static AGUARDANDO_DEFESA: StatusDenuncia = new StatusDenuncia(12, "Aguardando Defesa", "bg-warning");

    /**
     * Construtor da classe.
     *
     * @param id
     * @param descricao
     * @param style
     */
    constructor(public id: number, public descricao: string, public style: string) { }

    /**
     * Retorna a instância das 'Status da Denuncia' conforme o 'id' informado.
     *
     * @param id
     */
    public static findById(id: number): StatusDenuncia {
        switch (id) {
            case 1:
                return StatusDenuncia.ANALISE_ADMISSIBILIDADE;
            case 2:
                return StatusDenuncia.JULGAMENTO;
            case 3:
                return StatusDenuncia.RELATORIA;
            case 4:
                return StatusDenuncia.AGUARDANDO_RELATOR;
            case 5:
                return StatusDenuncia.EM_RECURSO;
            case 6:
                return StatusDenuncia.JULGADO_PRIMEIRA_INSTANCIA;
            case 7:
                return StatusDenuncia.JULGAMENTO_ADMITIDA;
            case 8:
                return StatusDenuncia.AGUARDANDO_CONTRARRAZAO;
            case 9:
                return StatusDenuncia.TRANSITADO_JULGADO;
            case 10:
                return StatusDenuncia.JULGAMENTO_SEGUNDA_INSTANCIA;
            case 11:
                return StatusDenuncia.JULGAMENTO_RECURSO_ADMISSIBILIDADE;
            case 12:
                return StatusDenuncia.AGUARDANDO_DEFESA;
            default:
                return new StatusDenuncia(null, "STATUS NAO DEFINIDO", "bg-danger");
        }
    }
}
