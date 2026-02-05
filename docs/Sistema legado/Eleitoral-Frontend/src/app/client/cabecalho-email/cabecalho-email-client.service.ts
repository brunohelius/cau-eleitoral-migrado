import { Injectable } from '@angular/core';
import { AbstractService } from '../abstract.service';
import { HttpClient } from '@angular/common/http';

import { Observable } from 'rxjs';
import { environment } from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class CabecalhoEmailClientService extends AbstractService {

  /**
   * Construtor da classe.
   *
   * @param http
   */
  constructor(private http: HttpClient) {
    super();
  }

  /**
   * Buscar Cabeçalho de E-mail por ID
   * @param number id
   */
  public getPorId(id: number){
    return this.http.get(`${environment.url}/cabecalhoEmail/${id}`);
  }

  /**
   * Buscar Cabeçalho de E-mail por Filtro
   * @param filtro 
   */
  public getPorFiltro(filtro: any){
    return this.http.post(`${environment.url}/cabecalhoEmail/filtro`, filtro);
  }

  /**
   *  Retorna quantidade total de E-mais vinculados ao cabeçalho.
   * @param id 
   */
  public getTotalCorpoEmailVinculado(id: number){
    return this.http.get(`${environment.url}/cabecalhoEmail/${id}/corpoEmails/total`);
  }

  /**
   * Salvar cabeçalho de E-mail.
   * @param cabecalhoEmail
   */
  public salvar(cabecalhoEmail: any){
    let data = new FormData();
    this.appendFormData(cabecalhoEmail, data);
    return this.http.post(`${environment.url}/cabecalhoEmail`, data);
  }

  /**
   * Validadação de Imagem de Cabeçalho de E-mail.
   * @param arquivoTO
   */
  public validarImagemCabecalho(arquivo){
    let data = new FormData();
    this.appendFormData(arquivo, data);
    return this.http.post(`${environment.url}/arquivo/validacao/cabecalhoEmail`, data);
  }

  /**
   * Validação de Imagem de Rodapé de E-mail.
   * @param arquivoTO
   */
  public validadeImagemRodape(arquivoTO){
    return this.http.post(`${environment.url}/arquivo/validacao/imagemRodape`, arquivoTO);
  }

}