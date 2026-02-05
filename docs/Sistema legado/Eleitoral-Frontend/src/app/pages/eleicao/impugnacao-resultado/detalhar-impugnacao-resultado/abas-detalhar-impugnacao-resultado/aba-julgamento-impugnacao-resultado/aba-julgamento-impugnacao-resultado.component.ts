import { LayoutsService } from '@cau/layout';
import { Router } from '@angular/router';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { JulgamentoAlegacaoImpugnacaoResultadoClientService } from 'src/app/client/julgamento-alegacao-impugnacao-resultado-client/julgamento-alegacao-impugnacao-resultado-client.service';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';



/**
 * Componente responsável pela apresentação de julgamento de pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-julgamento-impugnacao-resultado',
    templateUrl: './aba-julgamento-impugnacao-resultado.component.html',
    styleUrls: ['./aba-julgamento-impugnacao-resultado.component.scss']
})
export class AbaJulgamentoImpugnacaoResultadoComponent implements OnInit {

    @Input() julgamento?: any;
    @Input() bandeira: any;
    @Output() voltarAba: EventEmitter<any> = new EventEmitter();

    public arquivos = [];
    public configuracaoCkeditor: any = {};

    /**
     * Construtor da classe.
     *
     * @param route
     * @param messageService
     * @param layoutsService
     */
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
        private julgamentoAlegacaoService: JulgamentoAlegacaoImpugnacaoResultadoClientService,
    ) {
        this.inicializarJulgamento();
        this.julgamento = route.snapshot.data["idImpugnacao"];
    }

    ngOnInit() {


    }

    /**
     * Verifica se o julgamento é Deferido ou não.
     */
    public isDeferido(): boolean{
      return this.julgamento.statusJulgamentoAlegacaoResultado.id == Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
    }

    /**
     * Realiza download de arquivo para julgamento de impugnação.
     *
     * @param download
     */
    public downloadArquivo(download: any): void {
      this.julgamentoAlegacaoService.getDocumentoJulgamento(this.julgamento.id).subscribe(
        data => {
            download.evento.emit(data);
        },
        error => {
            this.messageService.addMsgDanger(error);
        }
      );
    }

    /**
     * Inicializar julgamento de impugnação.
     */
    public inicializarJulgamento(): void {
        if (this.julgamento == undefined) {
            this.julgamento = {
                descricao: "",
                arquivos: []
            };
        }
    }

   /**
   * Responsavel por voltar a tela pro inicio.
   */
  public inicio(): any {
    this.router.navigate(['/']);
    this.layoutsService.onLoadTitle.emit({
      description: ''
    });
  }

  /**
   * Volta a página.
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_ALEGACAO);
  }
}
