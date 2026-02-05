import { Component, OnInit, Input, SimpleChanges } from '@angular/core';
import { MessageService } from '@cau/message';

@Component({
  selector: 'progressbar-informacao-comissao-membro',
  templateUrl: './progressbar-informacao-comissao-membro.component.html',
  styleUrls: ['./progressbar-informacao-comissao-membro.component.scss']
})
export class ProgressbarInformacaoComissaoMembroComponent implements OnInit {

  public stacked: any[] = [];

  @Input() atividadeSecundaria: any;

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

    let informacaoComissaoMembro = JSON.parse(JSON.stringify(this.atividadeSecundaria.informacaoComissaoMembro));

    this.stacked = [];
    let informacoesIniciaisProgress = {
      value: 33,
      type: 'gray',
      label: this.messageService.getDescription('LABEL_INFORMACOES_INICIAIS_INCLUIDAS')
    };

    let documentosProgress = {
      value: 33,
      type: 'gray',
      label: this.messageService.getDescription('LABEL_DOCUMENTO_INCLUIDO')
    };

    let concluidoProgress = {
      value: 33,
      type: 'gray',
      label: this.messageService.getDescription('LABEL_DOCUMENTO_CONCLUIDO')
    };

    if (informacaoComissaoMembro.id) {
      informacoesIniciaisProgress.type = 'success';
    }

    if (informacaoComissaoMembro && informacaoComissaoMembro.documentoComissaoMembro && informacaoComissaoMembro.documentoComissaoMembro.id) {
      documentosProgress.type = 'warning';
    }

    if (informacaoComissaoMembro != undefined && informacaoComissaoMembro.situacaoConcluido) {
      concluidoProgress.type = 'info';
    }

    this.stacked.push(informacoesIniciaisProgress);
    this.stacked.push(documentosProgress);
    this.stacked.push(concluidoProgress);
  }

}
