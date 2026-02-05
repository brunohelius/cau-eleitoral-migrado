import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
  providedIn: 'root'
})
export class ChapaEleicaoClientService extends AbstractService {

  /**
   * Construtor da classe.
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
   * Alterar chapa.
   * 
   * @param chapaEleicao 
   */
  public alterar(chapaEleicao: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/alterarPlataforma`, chapaEleicao);
  }

  /**
   * Retorna uma lista de ufs de membro de chapa/chapa.
   *
   * @return
   */
  public getUfs(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/ufs`);
  }

  /**
   * Salva e valida membros da chapa eleitoral.
   *
   * @param id
   * @param membrosChapa
   */
  public salvarMembros(id: number, membrosChapa: Array<any>): Observable<any>{
    return this.http.post(`${environment.url}/chapas/${id}/salvarMembros`, membrosChapa);
  }

  /**
   * Salva e valida membros da chapa eleitoral.
   *
   * @param id
   * @param membrosChapa
   */
  public excluirMembro(id: number): Observable<any>{
    return this.http.post(`${environment.url}/membrosChapa/responsavel/excluirMembro/${id}`, null);
  }

  /**
   * Confirma cadastro da chapa.
   *
   * @param idChapa
   * @param declaracao
   */
  public confirmarChapa(idChapa: number, declaracao: object): Observable<any> {
    let data = new FormData();
    this.appendFormData(declaracao, data);
    return this.http.post(`${environment.url}/chapas/${idChapa}/confirmarChapa`, data);
  }

  /**
   * Recupera a eleição vigente para a chapa da eleição.
   */
  public getEleicaoVigente(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/eleicaoVigente`);
  }

  /**
   * Recupera a uf da chapa de acordo com o usuário logado.
   */
  public getChapaEleicaoUfResponsavel(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/cadastro`);
  }

  /**
   * Recupera chapa eleitoral que profissional está participando.
   */
  public getChapaEleicaoAcompanhar(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/acompanhar`);
  }

  /**
   * Recupera chapa eleitoral que profissional está participando.
   */
  public getChapaEleicaoSubistuicao(): Observable<any> {
    return this.http.get(`${environment.url}/chapas/substituicao`);
  }

  /**
   * Recupera o profissional pelo nome ou cpf informado.
   *
   * @param nome
   */
  public getProfissionalPorNome(nome: string): Observable<any> {
    return this.http.get(`${environment.url}/chapas/membro/${nome}`);
  }

  /**
   * Recupera o profissional pelo nome ou cpf informado.
   *
   * @param cpf
   */
  public getProfissionalPorCpf(cpf: string): Observable<any> {
    return this.http.get(`${environment.url}/chapas/membro/cpf/${cpf}`);
  }

  /**
   *  Recupera o profissional pelo nome ou cpf informado.
   *
   * @param cpfNome
   */
  public getProfissionalPorCpfNome(cpfNome: any): Observable<any> {
    return this.http.post(`${environment.url}/profissionais/filtro`, cpfNome);
  }

  /**
   * Remove a chapa eleição de acordo com o 'id' informado.
   *
   * @param id
   */
  public excluir(id: number): Observable<any> {
    return this.http.delete(`${environment.url}/chapas/${id}/excluir`);
  }

  /**
   * Recupera a chapa eleição de acordo com o 'id' informado
   *
   * @param id
   */
  public getChapaPorId(id: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/${id}`);
  }

  /**
   * Recupera as chapas de acordo com o 'uf' informado
   *
   * @param uf
   */
  public getChapasPorUf(uf: number): Observable<any> {
    return this.http.get(`${environment.url}/chapas/cauUf/${uf}`);
  }

  /**
   * Inclui membro por CPF.
   */
  public incluirMembroPorCpf(idChapa: number, membro: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapa}/incluirMembroPorCpf`, membro);
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
   * Inclui membro por idProfissinal.
   */
  public incluirMembro(idChapa: number, membro: any): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapa}/incluirMembro`, membro);
  }

  /**
   * Atualiza status da chapa eleitoral.
   */
  public atualizarStatus(idChapa: number): Observable<any> {
    return this.http.post(`${environment.url}/chapas/${idChapa}/atualizarChapa`, null);
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
   * Retorna Declarações que foram definidas na HST20.
   *
   * @param idAtividadesSecundarias
   * @param idTipoDeclaracao
   */
  public getDeclaracaoParametrizada(idAtividadesSecundarias: number, idTipoDeclaracao: number): Observable<any>{
    return this.http.get(`${environment.url}/atividadesSecundarias/${idAtividadesSecundarias}/declaracao-definida-por-tipo/${idTipoDeclaracao}`);
  }

  /**
   * Retorna Declarações de representatividade
   */
  public getDeclaracao(): Observable<any>{
    return this.http.get(`${environment.urlPortal}/declaracoes/36`);
  }

  /**
   * Valida documentos da declaração de cadastro da chapa.
   *
   * @param arquivo
   */
  public validarArquivoRespostaDeclaracaoChapa(arquivo: any): Observable<any> {
    return this.http.post(`${environment.url}/arquivo/validarArquivoRespostaDeclaracaoChapa`, arquivo);
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
}
