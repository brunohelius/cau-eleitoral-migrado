import { MessageService } from '@cau/message';
import { Component, OnInit, Input } from '@angular/core';

import { ConfigCardListInterface } from 'src/app/shared/component/card-list/config-card-list-interface';

@Component({
  selector: 'app-info-denunciado',
  templateUrl: './info-denunciado.component.html',
  styleUrls: ['./info-denunciado.component.scss']
})
export class InfoDenunciadoComponent implements OnInit {

  @Input('denunciado') denunciado;

  public infoDenunciado: ConfigCardListInterface = null;

  constructor(
    private messageService: MessageService,
  ) { }

  ngOnInit() {
  }

  /**
   * Carrega as informações do denunciado.
   */
  public getInformacoesDenunciado = () => {
    let data: any = { 'uf': this.denunciado.uf.prefixo };
    let header: any = [{
      'field': 'uf',
      'header': this.messageService.getDescription('LABEL_UF')
    }];

    if (this.denunciado.membro) {
      header.push({
        'field': 'nome',
        'header': this.messageService.getDescription('LABEL_NOME_COMPLETO')
      });
      data.nome = this.denunciado.membro.nome;
    } else if (this.denunciado.chapa)  {
      header.push({
        'field': 'numeroChapa',
        'header': this.messageService.getDescription('LABEL_NUMERO_CHAPA')
      });
      data.numeroChapa = this.denunciado.chapa.numeroChapa;
    }

    return { header, data: [data] };
  }
}
