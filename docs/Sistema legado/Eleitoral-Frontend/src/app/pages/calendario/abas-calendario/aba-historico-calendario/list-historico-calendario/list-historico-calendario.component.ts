import { Component, OnInit, Input } from '@angular/core';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { MessageService } from '@cau/message';

@Component({
  selector: 'list-historico-calendario',
  templateUrl: './list-historico-calendario.component.html',
  styleUrls: ['./list-historico-calendario.component.scss']
})
export class ListHistoricoCalendarioComponent implements OnInit {

  public data: any = [];
  public collapseAtivo: any = [];
  public quantidadeRegistros: number = 0;
  public numeroRegistrosPaginacao: number = 10;
  @Input('calendario') calendario: any;

  /**
   * Construtor da classe.
   *
   * @param messageService
   * @param calendarioClientService
   */
  constructor(
    private messageService: MessageService,
    private calendarioClientService: CalendarioClientService
  ) {
  }

  /**
   * Funções executadas quando o componente inicializar.
   */
  ngOnInit() {
    this.getHistoricoCalendario();
  }

  /**
   * Recupera a primeira ação.
   *
   * @param acoes
   */
  public getAcao(acoes): any {
    let acoesTO = JSON.parse(JSON.stringify(acoes));
    return acoesTO.shift().descricao;
  }

  /**
   * Recupera o primeiro elemento das justificativas.
   *
   * @param justifivativas
   */
  public getJustificativa(justifivativas): any {
    let justificativasTO = JSON.parse(JSON.stringify(justifivativas));
    let justificativa = justificativasTO.shift().justificativa;
    return justificativa == '' ? ' - ' : justificativa;
  }

  /**
   * Expande as ações/justificativas do histórico.
   *
   * @param index
   */
  public expandirAcoesJustificativas(index): void {
    this.collapseAtivo[index].isExpandido = true;
  }

  /**
   * Retrai as ações/justificativas do histórico.
   *
   * @param index
   */
  public retrairAcoesJustificativas(index): void {
    this.collapseAtivo[index].isExpandido = false;
  }

  /**
   * Recupera o histórico de calendário de acordo com o calendário informado.
   */
  private getHistoricoCalendario(): void {
    this.calendarioClientService.getHistorico(this.calendario.id).subscribe(data => {
      this.data = data;
      this.quantidadeRegistros = data.length;

      this.data.forEach(() => {
        this.collapseAtivo.push({ isExpandido: false });
      });

    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
