import { ActivatedRoute } from '@angular/router';

/**
 * Enum com as possíveis representações de 'Ação'.
 */
export enum Acao {
  LISTAR = 'listar',
  INCLUIR = 'incluir',
  ALTERAR = 'alterar',
  CONSULTAR = 'consultar',
  VISUALIZAR = 'visualizar',
  PUBLICAR = 'publicar'
}

/**
 * Classe de controle 'Ação'.
 *
 * @author Squadra Tecnologia
 */
export class AcaoSistema {

  private acao: Acao;

  /**
   * Construtor da classe.
   *
   * @param route
   */
  constructor(route?: ActivatedRoute) {

    if (route !== null && route !== undefined) {
      let data = route.snapshot.data;

      for (let index of Object.keys(data)) {
        let param = data[index];

        if (param !== null && typeof param === 'object' && param['acao'] !== undefined) {
          this.acao = param['acao'];
          break;
        }
      }
    }
  }

  /**
   * Seta o valor da ação vigente.
   *
   * @param acao
   */
  public setAcao(acao: Acao): AcaoSistema {
    this.acao = acao;
    return this;
  }

  /**
   * Verifica se ação é referente a 'INCLUIR'.
   *
   * @return boolean
   */
  public isAcaoIncluir(): boolean {
    return Acao.INCLUIR === this.acao;
  };

  /**
   * Verifica se ação é referente a 'ALTERAR'.
   *
   * @return boolean
   */
  public isAcaoAlterar(): boolean {
    return Acao.ALTERAR === this.acao;
  }

  /**
   * Verifica se ação é referente a 'LISTAR'.
   *
   * @return boolean
   */
  public isAcaoListar(): boolean {
    return Acao.LISTAR === this.acao;
  }

  /**
   * Verifica se ação é referente a 'CONSULTAR'.
   *
   * @return boolean
   */
  public isAcaoConsultar(): boolean {
    return Acao.CONSULTAR === this.acao;
  }

  /**
   * Verifica se ação é referente a 'VISUALIZAR'.
   *
   * @return boolean
   */
  public isAcaoVisualizar(): boolean {
    return Acao.VISUALIZAR === this.acao;
  }

  /**
   * Verifica se ação é referente a 'PUBLICAR'.
   *
   * @return boolean
   */
  public isAcaoPublicar(): boolean {
    return Acao.PUBLICAR === this.acao;
  }
}