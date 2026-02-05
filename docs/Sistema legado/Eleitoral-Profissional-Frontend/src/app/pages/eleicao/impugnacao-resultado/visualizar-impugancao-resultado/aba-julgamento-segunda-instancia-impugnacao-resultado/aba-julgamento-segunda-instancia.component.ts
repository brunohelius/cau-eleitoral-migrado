import { LayoutsService } from '@cau/layout';
import { Router } from '@angular/router';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';



/**
 * Componente responsável pela apresentação de julgamento de pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-julgamento-impug-resultado-segunda-instancia',
    templateUrl: './aba-julgamento-segunda-instancia.component.html',
    styleUrls: ['./aba-julgamento-segunda-instancia.component.scss']
})
export class AbaJulgamentoImpugResultadoSegundaInstanciaComponent implements OnInit {

    @Input() bandeira: any;
    @Input() impugnacao: any;
    @Input() julgamentoSegunda?: any;
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
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private julgamentoSegundaInstanciaService: ImpugnacaoResultadoClientService,
    ) {
        this.inicializarJulgamento();
    }

    ngOnInit() {

    }

    /**
     * Verifica se o julgamento é Deferido ou não.
     */
    public isDeferido(): boolean {
      return this.julgamentoSegunda.statusJulgamentoRecursoImpugResultado.id == Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
    }

    /**
     * Realiza download de arquivo para julgamento de impugnação.
     *
     * @param download
     */
    public downloadArquivo(download: any): void {
      this.julgamentoSegundaInstanciaService.getDocumentoJulgamentoSegundaInstancia(this.julgamentoSegunda.id).subscribe(
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
        if (this.julgamentoSegunda == undefined) {
            this.julgamentoSegunda = {
                descricao: "",
                arquivos: []
            };
        }
    }
}
