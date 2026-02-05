import { Component, OnInit, Input } from '@angular/core';

import { MaskPipe } from '@cau/component';
import { MessageService } from '@cau/message';
import { ConfigCardListInterface } from 'src/app/shared/card-list/config-card-list-interface';

@Component({
  selector: 'visualizar-testemunhas',
  templateUrl: './visualizar-testemunhas.component.html',
  styleUrls: ['./visualizar-testemunhas.component.scss']
})
export class VisualizarTestemunhasComponent implements OnInit {

  @Input('testemunhas') testemunhas: any;
  public infoTestemunhas: ConfigCardListInterface;

  constructor(
    private maskPipe: MaskPipe,
    private messageService: MessageService,
  ) { }

  ngOnInit() {
    this.initData();
  }

  /**
   * Carrega os dados da página.
   */
  private initData = () => {
    this.testemunhas.map((testemunha) => {
      testemunha.telefone = this.maskPipe.transform(testemunha.telefone, '(00) 90000-0000');
      return testemunha;
    });
    this.carregarInformacoesTestemunhas();
  }

  /**
   * Carrega as informações de testemunhas.
   */
  private carregarInformacoesTestemunhas = () => {
    this.infoTestemunhas = {
      header: [{
        'field': 'nome',
        'header': this.messageService.getDescription('LABEL_NOME_COMPLETO')
      }, {
        'field': 'telefone',
        'header': this.messageService.getDescription('LABEL_TELEFONE')
      }, {
        'field': 'email',
        'header': this.messageService.getDescription('LABEL_EMAIL')
      }],
      data: this.testemunhas
    };
  }
}
