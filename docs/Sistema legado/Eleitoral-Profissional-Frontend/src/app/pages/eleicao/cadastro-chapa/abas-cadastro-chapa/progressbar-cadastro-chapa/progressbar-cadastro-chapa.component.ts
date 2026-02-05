import { Component, OnInit, Input, SimpleChanges } from '@angular/core';
import { MessageService } from '@cau/message';

@Component({
  selector: 'progressbar-cadastro-chapa',
  templateUrl: './progressbar-cadastro-chapa.component.html'
})
export class ProgressbarCadastroChapaComponent implements OnInit {

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

    let candidaturaProgress = { value: 1, type: 'gray', label: labelCandidatura };
    let membrosChapaProgress = { value: 1, type: 'gray', label: labelMembrosChapa };
    let chapaConcluidaProgress = { value: 1, type: 'gray', label: labelChapaConcluida };
    let termoDeclaracaoProgress = { value: 1, type: 'gray', label: labelTermoDeclaracao };
    let plataformaEleitoralProgress = { value: 1, type: 'gray', label: labelPlataformaEleitoral };
    let aguardandoConclusaoProgress = { value: 1, type: 'gray', label: labelAguardandoConclusao };

    if (this.chapaEleicao.tipoCandidatura != undefined && this.chapaEleicao.tipoCandidatura.id) {
      candidaturaProgress.type = "danger";
    }

    if (this.chapaEleicao.id) {
      plataformaEleitoralProgress.type = "info";
    }

    if (this.chapaEleicao.membrosChapa && this.chapaEleicao.membrosChapa.length > 0) {
      membrosChapaProgress.type = "warning";
    }

    if (this.chapaEleicao.termoDeclaracao) {
      termoDeclaracaoProgress.type = "success";
      aguardandoConclusaoProgress.type = "gray-dark";
    }

    if (this.chapaEleicao.isConcluido) {
      chapaConcluidaProgress.type = "blue-dark";
    }

    this.stacked.push(candidaturaProgress);
    this.stacked.push(plataformaEleitoralProgress);
    this.stacked.push(membrosChapaProgress);
    this.stacked.push(termoDeclaracaoProgress);
    this.stacked.push(aguardandoConclusaoProgress);
    this.stacked.push(chapaConcluidaProgress);
  }

}
