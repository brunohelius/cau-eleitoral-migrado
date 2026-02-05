import { Component, OnInit, SimpleChanges, Input } from '@angular/core';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';

@Component({
  selector: 'progressbar-visualizar-chapa',
  templateUrl: './progressbar-visualizar-chapa.component.html'
})
export class ProgressbarVisualizarChapaComponent implements OnInit {

  public stacked: any[] = [];
  @Input() chapaEleicao: any;
  @Input() eleicaoVigente: any;

  /**
   * Construtor da classe.
   *
   * @param messageService
   */
  constructor(private messageService: MessageService) { }

  /**
   * Função executada quando o método é inicializado.
   */
  ngOnInit() {
    this.carregarBarraProgresso();
  }

  /**
   * Quando existir atualizações nos objetos.
   *
   * @param changes
   */
  ngOnChanges(changes: SimpleChanges) {
    this.carregarBarraProgresso();
  }

  /**
   * Carrega os valores para a barra de progresso.
   */
  public carregarBarraProgresso() {
    this.stacked = [];

    let labelCandidatura = this.messageService.getDescription('LABEL_CANDIDATURA');
    let labelMembrosChapa = this.messageService.getDescription('LABEL_MEMBROS_CHAPA');
    let labelChapaConcluida = this.messageService.getDescription('LABEL_CHAPA_CONCLUIDA');
    let labelTermoDeclaracao = this.messageService.getDescription('LABEL_TERMO_DECLARACAO');
    let labelAguardandoConclusao = this.messageService.getDescription('LABEL_AGUARDANDO_CONCLUSAO');
    let labelPlataformaEleitoral = this.messageService.getDescription('LABEL_PLATAFORMA_ELEITORAL_REDES_SOCIAIS');

    let candidaturaProgress = { value: 1, type: 'danger', label: labelCandidatura };
    let membrosChapaProgress = { value: 1, type: 'warning', label: labelMembrosChapa };
    let chapaConcluidaProgress = { value: 1, type: 'gray', label: labelChapaConcluida };
    let termoDeclaracaoProgress = { value: 1, type: 'success', label: labelTermoDeclaracao };
    let plataformaEleitoralProgress = { value: 1, type: 'info', label: labelPlataformaEleitoral };
    let aguardandoConclusaoProgress = { value: 1, type: 'gray-dark', label: labelAguardandoConclusao };

    if (this.isChapaConcluida()) {
      chapaConcluidaProgress.type = "blue-dark";
    }

    this.stacked.push(candidaturaProgress);
    this.stacked.push(plataformaEleitoralProgress);
    this.stacked.push(membrosChapaProgress);
    this.stacked.push(termoDeclaracaoProgress);
    this.stacked.push(aguardandoConclusaoProgress);
    this.stacked.push(chapaConcluidaProgress);
  }

  /**
   * Verifica se a eleição está concluída.
   */
  private isChapaConcluida(): boolean {
    if(this.chapaEleicao.statusChapaVigente) {
      return this.chapaEleicao.statusChapaVigente.id == Constants.STATUS_CHAPA_CONCLUIDO;
    }
    return false;
  }

}
