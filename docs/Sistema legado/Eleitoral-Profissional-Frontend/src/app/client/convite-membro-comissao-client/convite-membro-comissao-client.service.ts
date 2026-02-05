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
export class ConviteMembroComissaoService extends AbstractService {
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) {
        super();
    }

    /**
     * retorna a declaração pelo id do profissional
     */
    public getDeclaracaoPorIdProfissional(id: number): Observable<any> {
        return this.http.get(`${environment.url}/membroComissao/declaracao/${id}`);
    }

    public aceitarConviteMembroComissao(dados: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(dados, data);
        return this.http.post(`${environment.url}/membroComissao/aceitarConvite`, data);
    }

    public validarArquivoViaDeclaracao(dados: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(dados, data);
        return this.http.post(`${environment.url}/declaracao/arquivo/validacao`, dados);
    }

}