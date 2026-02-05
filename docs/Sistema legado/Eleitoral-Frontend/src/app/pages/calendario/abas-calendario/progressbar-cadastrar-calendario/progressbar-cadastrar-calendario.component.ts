import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { MessageService } from '@cau/message';

@Component({
  selector: 'progressbar-cadastrar-calendario',
  templateUrl: './progressbar-cadastrar-calendario.component.html'
})
export class ProgressbarCadastrarCalendarioComponent implements OnInit, OnChanges {

  public type: string;
  public stacked: any[] = [];

  @Input() inputCalendario;

  /**
   * Construtor da classe.
   *
   * @param messageService
   */
  constructor(private messageService: MessageService) { }

  /**
   * Quando ocorrer alterações nos dados do componente.
   *
   * @param changes
   */
  ngOnChanges(changes: SimpleChanges) {
    this.carregarBarraProgresso();
  }

  /**
   * Inicializa as dependências do Component.
   */
  ngOnInit() {
    this.carregarBarraProgresso();
  }

  /**
   * Carrega os valores para a barra de progresso.
   */
  public carregarBarraProgresso() {
    let types = ['danger', 'info', 'warning', 'success', 'gray'];
    let objPrazosDefinidos: any;
    let objCadastroRealizado: any;
    let objAtividadesDefinidas: any;
    let objCalendarioConcluido: any;

    this.stacked = [];
    if (this.inputCalendario) {
      this.inputCalendario.cadastroRealizado ?
        objCadastroRealizado = { value: 25, type: types[0], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CADASTRO') } :
        objCadastroRealizado = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CADASTRO') };

      this.inputCalendario.atividadesDefinidas ?
        objAtividadesDefinidas = { value: 25, type: types[1], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_ATIVIDADES') } :
        objAtividadesDefinidas = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_ATIVIDADES') };

      this.inputCalendario.prazosDefinidos ?
        objPrazosDefinidos = { value: 25, type: types[2], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_PRAZO') } :
        objPrazosDefinidos = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_PRAZO') };

      this.inputCalendario.calendarioConcluido ?
        objCalendarioConcluido = { value: 25, type: types[3], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CONCLUIDO') } :
        objCalendarioConcluido = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CONCLUIDO') };
    } else {
      objCadastroRealizado = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CADASTRO') };
      objAtividadesDefinidas = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_ATIVIDADES') };
      objPrazosDefinidos = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_PRAZO') };
      objCalendarioConcluido = { value: 25, type: types[4], label: this.messageService.getDescription('LABEL_PROGRESSBAR_NIVEL_CONCLUIDO') };
    }

    this.stacked.push(objCadastroRealizado);
    this.stacked.push(objAtividadesDefinidas);
    this.stacked.push(objPrazosDefinidos);
    this.stacked.push(objCalendarioConcluido);
  }
}
