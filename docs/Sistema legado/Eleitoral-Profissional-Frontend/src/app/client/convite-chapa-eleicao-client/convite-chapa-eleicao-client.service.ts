import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';
import { AbstractService } from '../abstract.service';

@Injectable({
    providedIn: 'root'
})
export class ConviteChapaEleicaoClientService extends AbstractService{
    
    /**
     * Construtor da classe.
     *
     * @param http
     */
    constructor(private http: HttpClient) { 
        super();
    }

    /**
     * Retorna lista de convites para participação de chapa recebidos pelo usuário.
     */
    public getConvitesRecebidos (): Observable<any> {
        return this.http.get(`${environment.url}/membrosChapa/convitesRecebidos`);
    }

    /**
     * Rejeita convite para participação da chapa.
     * 
     * @param convite 
     */
    public rejeitarConvite(convite: any): Observable<any>{
        return this.http.post(`${environment.url}/membrosChapa/rejeitarConvite`, convite);
    }

    /**
     * Aceita convite para participação da chapa.
     * 
     * @param convite
     */
    public aceitarConvite(convite: any): Observable<any>{
        let data = new FormData();
        this.appendFormData(convite, data);
        return this.http.post(`${environment.url}/membrosChapa/aceitarConvite`, data);
    }


    /**
     * Valida foto de síntese do currículo.
     * 
     * @param arquivo 
     */
    public validarFotoSinteseCurriculo(arquivo): Observable<any>{
        let data = new FormData();
        this.appendFormData(arquivo, data);
        return this.http.post(`${environment.url}/arquivo/validacao/fotoSinteseCurriculo`, data);
    }

    /**
     * Valida de arquivos do tipo PDF.
     * 
     * @param arquivo 
     */
    public validarPdf(arquivo): Observable<any>{
        let data = new FormData();
        this.appendFormData(arquivo, data);
        return this.http.post(`${environment.url}/arquivo/validacao/pdf`, data);
    }

    /**
     * Valida se a UF da chapa e do usuário são os mesmos.
     * 
     * @param idChapa 
     */
    public validacaoCauUfConvidado(idChapa: number): Observable<any>{
        return this.http.get(`${environment.url}/chapas/${idChapa}/validacaoCauUfConvidado`);
    }

    /**
     * Serviço para salvar a alteração do currículo do membro chapa
     * @param curriculo 
     */
    public alterarDadosCurriculo(curriculo: any): Observable<any> {
        let data = new FormData();
        this.appendFormData(curriculo, data);
        return this.http.post(`${environment.url}/membrosChapa/alterarDadosCurriculo`, data);
    }
}
