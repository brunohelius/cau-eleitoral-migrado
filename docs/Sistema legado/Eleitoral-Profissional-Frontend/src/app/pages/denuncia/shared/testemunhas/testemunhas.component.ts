import { ControlContainer, NgForm } from '@angular/forms';
import { Component, OnInit, Output, EventEmitter, Input, OnChanges } from '@angular/core';

import { MessageService } from '@cau/message';

@Component({
  selector: 'app-testemunhas',
  templateUrl: './testemunhas.component.html',
  styleUrls: ['./testemunhas.component.scss'],
  viewProviders: [{ provide: ControlContainer, useExisting: NgForm }]
})
export class TestemunhasComponent implements OnChanges, OnInit {

  @Input('submitted') submitted;
  @Input('testemunhas') testemunhasStorage;
  @Output('onSuccess') testemunhasAdicionadas: EventEmitter<any> = new EventEmitter();

  public testemunhas: any[];

  constructor(
    private messageService: MessageService,
  ) { }


  /**
   * Executado ao iniciar o componente
   */
  ngOnInit() {
    this.testemunhas = [];
  }

  /**
   * Executado quando alguma alteração acontece no componente
   */
  ngOnChanges() {
    if(this.testemunhasStorage) {
      this.testemunhas = this.testemunhasStorage;
    }
  }

  /**
   * Verifica se existe duplicidade de testemunhas.
   */
  public checkDuplicidadeTestemunha(testemunha) {
    this.testemunhas.forEach((testemunhaExistente, index) => {
      if(testemunhaExistente !== testemunha
        && (testemunha.nome === testemunhaExistente.nome
          && ((testemunha.email && testemunha.email === testemunhaExistente.email)
            || (testemunha.telefone && testemunha.telefone === testemunhaExistente.telefone)))
      ) {
        testemunha.nome = '';
        testemunha.email = '';
        testemunha.telefone = '';
        this.messageService.addMsgWarning('MSG_NAO_POSSIVEL_DUPLICAR_TESTEMUNHAS');
      }
    });
  }

  /**
   * Adiciona o form de campos para inserção de testemunhas.
   */
  public addTestemunha(): void {
    this.testemunhas.push({nome: "", email: "", telefone: ""});
    this.testemunhasAdicionadas.emit(JSON.parse(JSON.stringify(this.testemunhas)));
  }

  /**
   * Remove a testemunha.
   *
   * @param indexTestemunha
   */
  public removeTestemunha(indexTestemunha): void {
    this.testemunhas.splice(indexTestemunha, 1);
    this.testemunhas = JSON.parse(JSON.stringify(this.testemunhas));
    this.testemunhasAdicionadas.emit(this.testemunhas || []);
  }

  /**
   * Verifica se o campo de add Testemunha é desabilitado.
   */
  public isAddTestemunhaDisabled() {
    return this.testemunhas.length == 5;
  }
}
