import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

import { AbstractService } from '../abstract.service';
import { environment } from 'src/environments/environment';

/**
 * Classe de integração com o serviço de Denúncia.
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
     * Salva uma nova denuncia.
     *
     * @param denuncia
     */
    public salvar(denuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(denuncia, data);
        return this.http.post(`${environment.url}/denuncia/salvar`, data);
    }

    /**
     * Salva admissão de uma denúncia.
     *
     * @param denuncia
     */
    public admitir(denuncia: any): Observable<any> {
        return this.http.post(`${environment.url}/denuncia/admitir`, denuncia);
    }

    /**
     * Salva o relator de uma denúncia.
     *
     * @param denuncia
     */
    public relator(denuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(denuncia, data);
        return this.http.post(`${environment.url}/denuncia/relator`, data);
    }

    /**
     * Salva o recurso de uma denúncia.
     *
     * @param denuncia
     */
    public recurso(denuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(denuncia, data);
        return this.http.post(`${environment.url}/denuncia/recurso`, data);
    }

    /**
     * Inadmitir a Denuncia.
     *
     * @param denuncia
     */
    public inadmitir(denuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(denuncia, data);
        return this.http.post(`${environment.url}/denuncia/inadmitir`, data);
    }

    /**
     * Salva defesa para Denuncia.
     *
     * @param defesa
     */
    public apresentarDefesa(defesa: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(defesa, data);
        return this.http.post(`${environment.url}/denuncia/defender`, data);
    }

    /**
     * Salva contrarrazao para Denuncia.
     *
     * @param contrarrazao
     */
    public salvarContrarrazao(contrarrazao: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(contrarrazao, data);
        return this.http.post(`${environment.url}/contrarrazaoRecursoDenuncia/salvar`, data);
    }

    /**
     * Salva encaminhamento da Defesa para Denuncia.
     *
     * @param encaminhamento
     */
    public salvarEncaminhamento(encaminhamento: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(encaminhamento, data);
        return this.http.post(`${environment.url}/encaminhamentoDenuncia/salvar`, data);
    }

    /**
     * Recupera a denuncia de acordo com o 'id' e 'tipoDenuncia' informado
     *
     * @param id
     * @param tipoDenuncia
     */
    public getDenunciaById(id: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${id}`);
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
     * Recupera a Denuncia UF de acordo com a Atividade Secundária informada.
     *
     * @param idPessoa
     */
    public getAgrupamentoDenunciaUfPorIdPessoa(idPessoa: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/pessoa/${idPessoa}/agrupadoUf`);
    }

    /**
     * Recupera a Denuncia Comissao UF de acordo com a Pessoa informada.
     */
    public getAgrupamentoDenunciaComissaoUf(): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/comissao/agrupadoUf`);
    }

    /**
     * Recupera a Denuncia UF de acordo com o Cau UF informada.
     *
     * @param idCauUf
     */
    public getDetalhamentoDenunciaUfPorIdCauUf(idCauUf: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/cauUf/${idCauUf}/detalhamentoDenunciaUfPessoa`);
    }

    /**
     * Recupera a Denuncia Comissao UF de acordo com o Cau UF informada.
     *
     * @param idCauUf
     */
    public getDetalhamentoDenunciaComissaoUfPorIdCauUf(idCauUf: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/comissao/cauUf/${idCauUf}/detalhamentoDenunciaUfPessoa`);
    }

    /**
     * Recupera a Denuncia UF em relatoria de acordo com o profissional logado.
     */
    public getDetalhamentoDenunciaRelatoriaPorProfissional(): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/detalhamentoDenunciaRelatoriaProfissional`);
    }

    /**
     * Retorna uma lista de tipos de denúncia.
     *
     * @return
     */
    public getTiposDenuncia(): Observable<any> {
        return this.http.get(`${environment.url}/tiposDenuncia`);
    }

    /**
     * Retorna uma lista de tipos de encaminhamento para defesa.
     *
     * @return
     */
    public getTiposEncaminhamento(): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentoDenuncia/tiposEncaminhamento`);
    }

    /**
     * Retorna uma lista de tipos de encaminhamento para defesa.
     *
     * @return
     */
    public getTiposEncaminhamentoPorDenuncia(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentoDenuncia/tiposEncaminhamento/denuncia/${idDenuncia}`);
    }

    /**
     * Retorna os encaminhamentos de produção de provas e audiência de instrução com status pendente.
     *
     * @param idDenuncia
     * @return
     */
    public getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente`);
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
     * Valida o formato e tamanho do arquivo de contrarrazao.
     *
     * @param arquivoContrarrazao
     */
    public validarArquivoContrarrazao(arquivoContrarrazao: any): Observable<any> {
        return this.http.post(`${environment.url}/arquivo/validarContrarrazao`, arquivoContrarrazao);
    }

    /**
     * Busca os membros de comissão de acordo com UF.
     *
     * @param uf
     */
    public getMembrosComissaoByUf(idCauUf: any, idDenuncia:any): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/uf/${idCauUf}/denuncia/${idDenuncia}`);
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
     * Valida se existe um encaminhamento de audiência de instrução com status pendente.
     *
     * @param idDenuncia
     * @return
     */
    public validarAudienciaInstrucaoPendente(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validarAudienciaInstrucaoPendente`);
    }

    /**
     * Valida o usuário do cadastro da denúncia.
     *
     * @param idDenuncia
     * @return
     */
    public validaProfissionalLogadoPorIdDenuncia(idDenuncia: any): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/validaProfissionalLogado`);
    }

    /**
     * Busca os membros de chapa de acordo com UF e filtrado por nome ou registro.
     *
     * @param ufNomeRegistro
     */
    public getMembrosChapaByUfAutocomplete(ufNomeRegistro: any): Observable<any> {
        return this.http.post(`${environment.url}/membrosChapa/filtro`, ufNomeRegistro);
    }

    /**
     * Busca os membros de comissão de acordo com UF e filtrado por nome ou registro.
     *
     * @param ufNomeRegistro
     */
    public getMembrosComissaoByUfAutocomplete(ufNomeRegistro: any): Observable<any> {
        return this.http.post(`${environment.url}/membroComissao/informacaoComissaoMembro/filtro`, ufNomeRegistro)
    }

    /**
     * Busca as denuncias recebidas do profissional logado
     */
    public getDenunciasRecebidas(): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/profissional`)
    }

    /**
     * Busca as denuncias Nao Admitida da Comissao
     */
    public getDenunciaComissaoAdmissibilidade(): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/comissao/admissibilidade`)
    }

    /**
     * Retorna o tipo de Membro Comissao que o usuario logado se encontra.
     */
    public getTipoMembroComissaoPorUsuarioLogado(): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/comissao/membroComissao`)
    }

    /**
     * Valida o prazo de Defesa da Denuncia Admitida.
     */
    public validaPrazoDefesaDenuncia(): Observable<any> {
        return this.http.get(`${environment.url}/denunciaDefesa/validaPrazoDefesaDenuncia`)
    }

    /**
    * Inserir Provas.
    *
    * @param provaDenuncia
    */
    public inserirProvas(provasDenuncia: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(provasDenuncia, data);
        return this.http.post(`${environment.url}/denuncia/provas/salvar`, data);
    }

    /**
     * Salva alegações finais de um Encaminhamento da Denuncia.
     *
     * @param alegacao
     */
    public salvarAlegacaoFinal(alegacao: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(alegacao, data);
        return this.http.post(`${environment.url}/encaminhamentosDenuncia/alegacaoFinal/salvar`, data);
    }

    /**
     * Valida o formato e tamanho do arquivo de alegações finais.
     *
     * @param arquivoAlegacao
     */
    public validarArquivoAlegacaoFinal(arquivoAlegacao: any): Observable<any> {
        return this.http.post(`${environment.url}/encaminhamentosDenuncia/alegacaoFinal/validarArquivo`, arquivoAlegacao);
    }

    /**
     * Recupera os tipos de julgamento
     *
     */
    public getTiposJulgamentoPorDenuncia(): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoDenuncia/tiposJulgamento`);
    }

    /**
     * Recupera os tipos de sentença do julgamento
     *
     */
    public getTiposSentencaJulgamento(): Observable<any> {
        return this.http.get(`${environment.url}/julgamentoDenuncia/tiposSentencaJulgamento`);
    }

    /**
     * Salva o parecer final de uma Denuncia.
     *
     * @param parecerFinal
     */
    public salvarParecerFinalDenuncia(parecerFinal: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(parecerFinal, data);
        return this.http.post(`${environment.url}/encaminhamentosDenuncia/parecerFinal/salvar`, data);
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
     * Realiza download de arquivos de encaminhamentos por id do arquivo.
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
     * Realiza download de arquivos de parecer final por id do arquivo.
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
            // Ignore this part or  if you want full response you have
            // to explicitly give as 'body'as http client by default give res.json()
            observe: 'response' as 'body',
            // have to explicitly give as 'blob' or 'json'
            responseType: 'blob' as 'blob'
        };

        return this.http.get(`${environment.url}/denuncia/inadmitida/arquivo/${idArquivo}/download`, options);
    }

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
     * Recupera as condições da denuncia para manipulação de layout
     * @param idDenuncia
     */
    public getCondicaoDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncia/${idDenuncia}/condicao`);
    }

    /**
     * Recupera informação de criação de relator
     *
     * @param idDenuncia
     */
    public createRelator(idDenuncia: number) {
        return this.http.get<{membros_comissao}>(`${environment.url}/denuncia/${idDenuncia}/relator/create`);
    }

    /**
     * Submete o formulario de criação de relator
     *
     * @param idDenuncia
     * @param data
     */
    public submitRelator(idDenuncia: number, data: {}) {
        const postData = new FormData();
        this.appendFormData(data, postData);
        return this.http.post(`${environment.url}/denuncia/${idDenuncia}/relator`, postData);
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

    public downloadArquivoJulgamentoRecursoAdmissibilidade(idArquivo: number): Observable<any> {
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
        return this.http.get(`${environment.url}/denuncia/julgamento-recurso-admissibilidade/arquivo/${idArquivo}`, options);
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
    * Busca os dados dos encaminhamentos do tipo Audiência de Instrução
    *
    * @param idEncaminhamento
    */
    public getAudienciaInstrucao(idEncaminhamento: any): Observable<any> {
        return this.http.get(`${environment.url}/encaminhamentosDenuncia/encaminhamento/${idEncaminhamento}`)
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
     * Salvar Audiência de Instrução.
     */
    public salvarAudienciaInstrucao(audienciaInstrucao: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(audienciaInstrucao, data);
        return this.http.post(`${environment.url}/denuncia/audienciaInstrucao/salvar`, data);
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
     * Valida se o Usuario logado tem acesso para acessar a Respectiva Denuncia.
     *
     * @param idDenuncia
     */
    public validarMembroComissaoPorDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/denuncia/${idDenuncia}/validarAcessoMembro`);
    }

    /**
     * Valida se o Usuario logado tem acesso para acessar a Respectiva Denuncia.
     *
     * @param idDenuncia
     */
    public validarAcessoPorDenuncia(idDenuncia: number): Observable<any> {
        return this.http.get(`${environment.url}/denuncias/denuncia/${idDenuncia}/validarAcessoAcompanhar`);
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
    public submitRecursoAdmissibilidade(data: {}): Observable<any> {
        const postData = new FormData();
        this.appendFormData(data, postData);
        return this.http.post(`${environment.url}/denuncia/recurso-julgamento-admissibilidade`, postData);
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

/**
 * Classe service responsável por prover os recursos associadas a 'Tipos da Denuncia'.
 *
 * @author Squadra Tecnologia
 */
export class TipoDenuncia {

    public static TIPO_DENUNCIA_CHAPA: TipoDenuncia = new TipoDenuncia(1, "Chapa");
    public static TIPO_DENUNCIA_MEMBRO_CHAPA: TipoDenuncia = new TipoDenuncia(2, "Membro Chapa");
    public static TIPO_DENUNCIA_MEMBRO_COMISSAO: TipoDenuncia = new TipoDenuncia(3, "Membro Comissao");
    public static TIPO_DENUNCIA_OUTROS: TipoDenuncia = new TipoDenuncia(4, "Outros");

    /**
     * Construtor da classe.
     *
     * @param id
     * @param descricao
     */
    constructor(public id: number, public descricao: string) { }

    /**
     * Retorna a instância das 'Tipos da Denuncia' conforme o 'id' informado.
     *
     * @param id
     */
    public static findById(id: number): TipoDenuncia {
        switch (id) {
            case 1:
                return TipoDenuncia.TIPO_DENUNCIA_CHAPA;
            case 2:
                return TipoDenuncia.TIPO_DENUNCIA_MEMBRO_CHAPA;
            case 3:
                return TipoDenuncia.TIPO_DENUNCIA_MEMBRO_COMISSAO;
            case 4:
                return TipoDenuncia.TIPO_DENUNCIA_OUTROS;
            default:
                return undefined;
        }
    }

}
