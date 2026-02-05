import { Observable } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from "../../../environments/environment";
import { AbstractService } from '../abstract.service';

/**
 * Classe de integração com o serviço de Comissão Eleitoral.
 */
@Injectable({
    providedIn: 'root'
})
export class ComissaoEleitoralService extends AbstractService {
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * Retorna uma lista de comissões eleitorais
     * @return
     */
    public getComissoesEleitorais(): Observable<any> {
        return this.http.get(`${environment.url}/comissoesEleitorais`);
    }

    /**
     * Retorna uma  comissão eleitoral
     * @return
     */
    public getComissoaoEleitoral(user: any): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/${user.idProfissional}/lista`);
    }

    /**
     * Retorna detalhes de um profissional membro de uma comissão
     * @return
     */
    public getMembroComissao(idProfissional: number): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/${idProfissional}`);
    }

    /**
     * Busca a informação referente a uma comissão de membros do usuario profissional
     * @param idProfissional
     */
    public getInformacaoComissaoMembro(idProfissional: number): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/${idProfissional}/informacaoComissaoMembro`)
    }

    /**
     * Recupera as informações de aceite do profissional referente a comissão de membros
     * @param idProfissional
     */
    public getAceiteComissaoMembro(idProfissional: number): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/situacaoConvite/${idProfissional}`);
    }

    /**
     * Retorna uma lista de ufs de membro de comissão.
     *
     * @return
     */
    public getUfs(): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/ufs`);
    }

    /**
     * Valida se o Usuario logado é membro da comissão referente a eleição vigente.
     */
    public validarMembroComissaoEleicaoVigente(): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/validaMembroComissaoEleicaoVigente`);
    }
}
