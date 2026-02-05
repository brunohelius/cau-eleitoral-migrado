import { Component, OnInit, ViewChild } from '@angular/core';
import { Router } from "@angular/router";
import { LayoutsService } from "@cau/layout";
import { MessageService } from "@cau/message";

import { Constants } from "../../../constants.service";
import { AbaCadastroDenunciaComponent } from "./aba-cadastro-denuncia/aba-cadastro-denuncia.component";

@Component({
  selector: 'app-form-denuncia',
  templateUrl: './form-denuncia.component.html',
  styleUrls: ['./form-denuncia.component.scss']
})
export class FormDenunciaComponent implements OnInit {

  public abas: any = {};

  @ViewChild(AbaCadastroDenunciaComponent, null) abaCadastroDenuncia: AbaCadastroDenunciaComponent;

  constructor(
    private router: Router,
    private layoutsService: LayoutsService,
    private messageService: MessageService
  ) {
  }

  ngOnInit() {
    this.inicializaAbas();

    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('LABEL_DENUNCIA')
    });
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      abaVisaoGeral: { id: Constants.ABA_VISAO_GERAL, nome: 'visaoGeral', ativa: false },
      abaCadastroDenuncia: { id: Constants.ABA_CADASTRO_DENUNCIA, nome: 'cadastroDenuncia', ativa: true },
    };
  }

  /**
   * Muda a aba selecionada de acordo com a seleção.
   *
   * @param aba
   */
  public mudarAbaSelecionada(aba: number): void {
    if (aba == Constants.ABA_VISAO_GERAL) {
      this.router.navigate(['/']);
    } else {
      this.mudarAba(aba);
    }
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  private mudarAba(aba: number): void {
    this.abas.abaVisaoGeral.ativa = this.abas.abaVisaoGeral.id == aba;
    this.abas.abaCadastroDenuncia.ativa = this.abas.abaDeclaracao.id == aba;
  }
}
