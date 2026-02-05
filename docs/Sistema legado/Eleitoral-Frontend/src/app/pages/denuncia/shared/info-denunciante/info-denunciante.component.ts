import { Component, OnInit, Input } from '@angular/core';

import { MessageService } from '@cau/message';
import { ConfigCardListInterface } from 'src/app/shared/component/card-list/config-card-list-interface';

@Component({
  selector: 'app-info-denunciante',
  templateUrl: './info-denunciante.component.html',
  styleUrls: ['./info-denunciante.component.scss']
})
export class InfoDenuncianteComponent implements OnInit {

  @Input('denunciante') denunciante: any = null;
  public infoDenunciante: ConfigCardListInterface;

  constructor(
    private messageService: MessageService,
  ) { }

  ngOnInit() {
    this.initData();
  }

  /**
   * Carrega os dados da página.
   */
  private initData = () => {
    this.carregarInformacoesDenunciante();
  }

  /**
   * Carrega as informações do denunciante.
   */
  private carregarInformacoesDenunciante = () => {
    this.infoDenunciante = {
      header: [{
        'field': 'nome',
        'header': this.messageService.getDescription('LABEL_NOME_COMPLETO')
      }, {
        'field': 'registro',
        'header': this.messageService.getDescription('LABEL_REGISTRO')
      }, {
        'field': 'email',
        'header': this.messageService.getDescription('LABEL_EMAIL')
      }],
      data: [{
        nome: this.denunciante.name,
        email: this.denunciante.email,
        registro: this.denunciante.registroNacional,
      }]
    };
  }
}
