import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'visualizar-recurso-julgamento',
    templateUrl: './visualizar-recurso-julgamento.component.html',
    styleUrls: ['./visualizar-recurso-julgamento.component.scss']
})
export class VisualizarRecursoJulgamentoComponent implements OnInit {

  @Input() isIes: any;
  @Input() chapa: any;
  @Input() recurso: any;
  @Input() recursoReconsideracao: any;

  public hasRecurso: any;

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  /**
   * Construtor da classe.
   */
  constructor(
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private recursoJulgamentoService: JulgamentoFinalClientService,
  ) {

  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {
    this.getTituloPagina();
    this.hasRecurso = this.recurso != undefined;
  }

  /**
   * Define o título do módulo da página
   */
  public getTituloPagina(): any {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-wpforms',
      description: this.messageService.getDescription('Julgamento')
    });
  }

  /**
   * retorna a label do membro.
   */
  public getTituloMembro(): any {
    return  this.recursoReconsideracao === Constants.IS_RECURSO ?
    'LABEL_MEMBRO_SOLICITACAO_RECURSO' :
    'LABEL_MEMBRO_SOLICITACAO_RECONSIDERACAO';
  }

  /**
   * retorna a label do editor de texto.
   */
  public getTituloEditor(): any {
    return  this.recursoReconsideracao === Constants.IS_RECURSO ?
    'LABEL_RECURSO_DO_JULGAMENTO_FINAL' :
    'LABEL_RECONSIDERACAO_DO_JULGAMENTO_FINAL';
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   *
   * @param event
   * @param recurso
   */
  public download(event: any): void {
    this.recursoJulgamentoService.getDocumentoRecursoJulgamentoFinal(this.recurso.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    return this.recurso.julgamentoFinal.statusJulgamentoFinal.id === Constants.STATUS_JULGAMENTO_INDEFERIDO;
  }

  /**
   * Responsavel por voltar a aba para a principal.
   */
  public voltarAbaPrincipal(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_DETALHAR);
  }
}
