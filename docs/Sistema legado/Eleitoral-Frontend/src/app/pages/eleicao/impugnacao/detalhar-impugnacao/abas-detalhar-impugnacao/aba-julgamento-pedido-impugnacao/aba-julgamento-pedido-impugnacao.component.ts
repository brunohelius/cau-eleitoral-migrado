import { DefesaImpugnacaoService } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-client.service';
import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { LayoutsService } from '@cau/layout';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from '@angular/router';
import { StringService } from 'src/app/string.service';


/**
 * Componente responsável pela apresentação de julgamento de pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-julgamento-pedido-impugnacao',
    templateUrl: './aba-julgamento-pedido-impugnacao.component.html',
    styleUrls: ['./aba-julgamento-pedido-impugnacao.component.scss']
})
export class AbaJulgamentoPedidoImpugnacaoComponent implements OnInit {

    @Input() impugnacao?: any;

    @Input() julgamento?: any;

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
        private messageService: MessageService,
        private defesaImpugnacaoService: DefesaImpugnacaoService,
        private layoutsService: LayoutsService,
    ) {
        this.inicializarJulgamento();
    }

    ngOnInit() {

    }


    /**
     * Retorna o registro com a mascara.
     *
     * @param str
     */
    public getRegistroComMask(str) {
        return StringService.maskRegistroProfissional(str);
    }

    /**
     * Realiza download de arquivo para julgamento de impugnação.
     *
     * @param download
     */
    public downloadArquivo(download: any): void {
        if (this.julgamento.arquivos) {
            this.defesaImpugnacaoService.getArquivoJulgamentoImpugnacao(this.julgamento.id).subscribe(
                data => {
                    download.evento.emit(data);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
      * Realiza download do PDF do Julgamento.
      *
      * @param event
      * @param idCalendario
      */
    public downloadPDF(event: EventEmitter<any>): void {
        this.defesaImpugnacaoService.gerarPDFJulgamento(this.julgamento.id).subscribe(
            (data: Blob) => {
                event.emit(data);
            }, error => {
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
     * Retorna a opsição formatada
     *
     * @param numeroOrdem
     */
    public getPosicaoFormatada(numeroOrdem) {
        return numeroOrdem > 0 ? numeroOrdem : '-';
    }
}