import { Component, OnInit, Input } from '@angular/core';
import { Constants } from 'src/app/constants.service';

@Component({
  selector: 'aba-analise-admissibilidade',
  templateUrl: './aba-analise-admissibilidade.component.html',
  styleUrls: ['./aba-analise-admissibilidade.component.scss']
})
export class AbaAnaliseAdmissibilidadeComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;

  public abas: any = {};
  public analiseAdmissibilidadeAdmissao: any;
  public analiseAdmissibilidadeInadmissao: any;
  public analiseAdmissibilidade: any = {};

  constructor() { }

  ngOnInit() {
    this.analiseAdmissibilidadeAdmissao = this.denuncia.analiseAdmissibilidade.admissao;
    this.analiseAdmissibilidadeInadmissao = this.denuncia.analiseAdmissibilidade.inadmissao;
    
    this.analiseAdmissibilidade = this.analiseAdmissibilidadeAdmissao || this.analiseAdmissibilidadeInadmissao;

    this.inicializaAbas();
  }

  /**
   * Retorna se existe analise em ambas as situações.
   */
  public hasAnaliseAdmissaoInadmissao(): boolean {
    return this.analiseAdmissibilidadeAdmissao && this.analiseAdmissibilidadeInadmissao;
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  public mudarAba(aba: number): void {
    if(this.abas) {
      for(let tab in this.abas) {
        this.abas[tab].ativa = this.abas[tab].id === aba ? true : false;
      }
    }
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    let idAbaAtiva = Constants.ABA_ADMISSAO;

    this.abas = {
      abaAdmissao: { id: Constants.ABA_ADMISSAO, nome: 'admissao' },
      abaInadmissao: {
        id: Constants.ABA_INADMISSAO,
        nome: 'inadmissao'
      }
    };

    this.mudarAba(idAbaAtiva);
  }
}
