import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

import { Observable } from 'rxjs';
import { AbstractService } from '../abstract.service';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ChapaEleicaoClientService extends AbstractService {

 /**
  *  Construtor da classe.
  *
  * @param http
  */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Salva uma nova chapa eleição.
   *
   * @param chapaEleicao
   */
  public salvar(chapaEleicao: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/salvar`, chapaEleicao);
  }

  /**
   * Recupera a chapa eleição de acordo com o 'id' informado
   *
   * @param id
   */
  public getChapaPorId(id: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/${id}/comMembros`);
  }

  /**
   * Busca eleição pelo por id chapa.
   *
   * @param idChapa
   */
  public getEleicaoPorIdChapa(idChapa: number):  Observable<any> {
    return this.http.get(`${environment.url}/chapas/${idChapa}/eleicao`);
  }

  /**
   * Busca chapas de uma Eleição.
   * @param id
   */
  public getChapasPorCalendario(id: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/informacaoChapaEleicao/${id}`);
  }

  /**
   * Busca chapas de uma Eleição por CauUF.
   *
   * @param id id do Calendário
   * @param idCauUf id do CauUF
   */
  public getChapaPorCalendarioAndCauUf(id: number, idCauUf: string): Observable<any> {
    return this.http.get(`${environment.url}/chapas/informacaoChapaEleicao/${id}/cauUf/${idCauUf}`);
  }

  /**
   * Inclui membro por idProfissinal.
   */
  public incluirMembro(idChapa: number, membro: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapa}/incluirMembro`, membro);
  }

  /**
   * Retorna currículo do membro da chapa.
   *
   * @param idMembroChapa
   */
  public getCurriculoMembroChapaPorMembroChapa(idMembroChapa: number): Observable<any> {
    return this.http.get(`${environment.url}/membrosChapa/${idMembroChapa}/detalhar`);
  }

  /**
   * Envia E-mail com lista de pendências do membro da chapa.
   *
   * @param idMembroChapa
   */
  public enviarEmailDePendenciasPorMembroChapa(idMembroChapa: number): Observable<any> {
    return this.http.get(`${environment.url}/membrosChapa/${idMembroChapa}/enviarEmailPendencias`);
  }

  /**
   * Envia E-mail de convite para o membro da chapa.
   *
   * @param idMembroChapa
   */
  public enviarEmailDeConvitePorMembroChapa(idMembroChapa: number): Observable<any> {
    return this.http.get(`${environment.url}/membrosChapa/${idMembroChapa}/reenviarConvite`);
  }

  /**
   * Alterar situação de Responsável membro.
   *
   * @param id
   * @param situacao
   */
  public alterarSituacaoResponsavel(idMembroChapa: number, situacao: any): Observable<any> {
    return this.http.post(`${environment.url}/membrosChapa/${idMembroChapa}/alterarSituacaoResponsavel`, situacao);
  }

  /**
   * Calcula Total de Eleição com chapas.
   *
   */
  public validarChapasCalendario(): Observable<any> {
    return this.http.get(`${environment.url}/calendarios/validacao/chapas`);
  }

  /**
   * Exclui chapa eleição.
   *
   * @param id
   * @param justificativa
   */
  public excluirChapa(id: number, justificativa: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${id}/excluirComJustificativa`, justificativa);
  }

  /**
   * Altera status da chapa eleição.
   *
   * @param idChapaEleicao
   * @param alterarChapa
   */
  public alteraracaoStatusChapa(idChapaEleicao: number, alterarChapa: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapaEleicao}/alterarStatus`, alterarChapa);
  }

  /**
   * Busca de histórico de chapa por calendário.
   *
   * @param idCalendario
   */
  public getHistoricoChapaPorCalendario(idCalendario: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/${idCalendario}/historico`);
  }

  /**
   *  Recupera o profissional pelo nome ou cpf informado.
   *
   * @param cpfNome
   */
  public getProfissionalPorCpfNome(cpfNome: any): Observable<any> {
    return this.http.post(`${environment.url}/atividadesSecundarias/profissionaisPorCpfNome`, cpfNome);
  }

  /**
   * Altera status de convite de membro chapa.
   *
   * @param idMembroChapa
   * @param status
   */
  public alterarStatusConvite(idMembroChapa: number, status: any ): Observable<any> {
    return this.http.post(`${environment.url}/membrosChapa/${idMembroChapa}/alterarStatusConvite`, status);
  }

  /**
   * Altera status de convite de membro chapa.
   *
   * @param idMembroChapa
   * @param status
   */
  public alterarStatusValidacao(idMembroChapa: number, status: any ): Observable<any> {
    return this.http.post(`${environment.url}/membrosChapa/${idMembroChapa}/alterarStatusValidacao`, status);
  }

  /**
   * Exclui membro da chapa.
   *
   * @param idMembroChapa
   */
  public exclirMembroChapa(idMembroChapa: number, justificativa: any): Observable<any> {
    return this.http.post(`${environment.url}/membrosChapa/${idMembroChapa}/excluir`, justificativa);
  }

  /**
   * Salvar ou atualizar o número da chapa eleitoral.
   *
   * @param numeroChapa
   */
  public salvarNumeroChapa(numeroChapa: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/salvarNumeroChapa`, numeroChapa);
  }

  /**
   * Realiza download de documento comprobatório por id do documento.
   *
   * @param idDocumento
   */
  public downloadDocumentoComprobatorio(idDocumento: number): Observable<any> {
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
    return this.http.get(`${environment.url}/membrosChapa/documentoComprobatorio/${idDocumento}/download`, options);
  }

    /**
   * Realiza download de documento de declaração de representatividade.
   *
   * @param idDocumento
   */
    public downloadDocumentoRepresentatividade(idMembro: number): Observable<any> {
      let options = {
        observe: 'response' as 'body',
        responseType: 'blob' as 'blob',
        headers: new HttpHeaders({ 'Content-Type': 'application/json' })
    };

    return this.http.get(`${environment.url}/membrosChapa/documentoRepresentatividade/${idMembro}/download`, options);
    }

  /**
   * Gerar extrato de quantidade de chapas da eleição.
   *
   * @param idCalendario
   */
  public gerarExtratoQuantidadeChapa(idCalendario: number):  Observable<any> {
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
    return this.http.get(`${environment.url}/chapas/${idCalendario}/gerarPDFExtratoQuantidadeChapa`, options);
  }

  /**
   * Retorna os dados para montagem de extrato navegável.
   *
   * @param idCauUf
   */
  public getDadosExtratoChapa(idCauUf: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/dadosExtratoChapaJson/${idCauUf}`);
  }

  /**
   * Gerar extrato de chapas da eleição.
   *
   * @param idCalendario
   */
  public gerarExtratoChapa( idCalendario: number, filtro: any ): Observable<any> {
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
    return this.http.post(`${environment.url}/chapas/${idCalendario}/gerarDocumentoPDFExtratoChapa`, filtro,  options);
  }

  /**
   * Gerar extrato de chapas da eleição navegável.
   *
   * @param idCalendario
   */
  public gerarExtratoChapaNavegavel( filtro: any ): Observable<any> {
    return this.http.post(`${environment.url}/chapas/gerarDadosExtratoChapaJson`, filtro);
  }


  public downloadExportarDadosChapa(eleicao: number, tipo: string, filtro: number): Observable<any> {
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
    return this.http.get(`${environment.url}/chapas/${eleicao}/gerar${tipo}Chapas/${filtro}`, options);
  }

  public downloadExportarDadosChapaTRE(eleicao: number, id_cau_uf: number): Observable<any> {
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
    return this.http.get(`${environment.url}/chapas/${eleicao}/gerarCSVChapasTrePorUf/${id_cau_uf}`, options);
  }

    /**
   * Verifica se a chapa possui retificação pelo id chapa.
   *
   * @param idChapa
   */
  public getIsPossuiRetificacao(idChapa: number):  Observable<any> {
    return this.http.get(`${environment.url}/chapas/verificaRetificacoesPlataforma/${idChapa}`);
  }

    /**
   * Salva uma nova chapa eleição.
   *
   * @param chapaEleicao
   */
  public alterarPlataforma(chapaEleicao: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/alterarPlataforma`, chapaEleicao);
  }

  /**
   * Retrona as retificações pelo id da chapa.
   *
   * @param idChapa
   */
  public getRetificacoesPlataforma(idChapa: number):  Observable<any> {
    return this.http.get(`${environment.url}/chapas/retificacoesPlataforma/${idChapa}`);
  }

  /**
   * Gerar extrato de chapa por UF
   *
   * @param idCalendario
   * @param idCauUf
   */
  public gerarExtratoChapaPorUf(idCalendario: number, idCauUf: number):  Observable<any> {
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
    return this.http.get(`${environment.url}/chapas/${idCalendario}/gerarCSVChapasPorUf/${idCauUf}`, options);
  }

  /**
   * Atualizar o status de Eleito dos membros da chapa
   *
   * @param membros
   */
  public atualizarStatusEleito(membros: any):  Observable<any> {
    return this.http.post(`${environment.url}/membrosChapa/setStatusEleito/`, membros);
  }
}