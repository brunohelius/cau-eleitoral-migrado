import * as _ from 'lodash'
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter, Output } from '@angular/core';

import { AtividadeSecundariaClientService } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';

@Component({
  selector: 'hist-numero-conselheiro',
  templateUrl: './hist-numero-conselheiro.component.html',
  styleUrls: ['./hist-numero-conselheiro.component.scss']
})
export class HistNumeroConselheiroComponent implements OnInit {

  @Output('voltar') voltar: EventEmitter<any> = new EventEmitter(null);

  public historicos: any[];
  public idAtividadeSecundaria: number;
  public numeroRegistrosPaginacao: number = 10;

  /**
   * Construtor da classe
   * @param route 
   * @param messageService 
   * @param atividadeSecundariaService 
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private atividadeSecundariaService: AtividadeSecundariaClientService,
  ) { }

  /**
   * Inicializa as dependências do Component.
   */
  ngOnInit( ) {
    this.idAtividadeSecundaria = Number(this.route.snapshot.paramMap.get('id'));
    this.getHistoricoPorAtividadeSecundaria(this.idAtividadeSecundaria);
  }

  /**
   * Retorna o histórico do Número de Conselheiros, de acordo com o ID da atividade secundária.
   * 
   * @param id
   */
  public getHistoricoPorAtividadeSecundaria(id: number): void {
    this.atividadeSecundariaService.getHistoricoConselheirosPorAtividadeSecundaria(id).subscribe((data: any[]) => {
      this.historicos = data;
    }, error => {
      console.error(error);
      this.messageService.addMsgDanger(error.message);
    });
  }

  /**
   * Retorna para a aba 'Número de Conselheiros'.
   */
  public voltarNumeroConselheiros(): void {
    this.voltar.emit();
  }

}
