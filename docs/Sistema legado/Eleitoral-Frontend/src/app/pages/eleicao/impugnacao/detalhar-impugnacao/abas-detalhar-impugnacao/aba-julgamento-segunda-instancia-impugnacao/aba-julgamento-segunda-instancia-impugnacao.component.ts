import { MessageService } from '@cau/message';
import { StringService } from 'src/app/string.service';
import { Component, OnInit, Input } from '@angular/core';

import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';


/**
 * Componente responsável pela apresentação de julgamento de pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-julgamento-segunda-instancia-impugnacao',
    templateUrl: './aba-julgamento-segunda-instancia-impugnacao.component.html',
    styleUrls: ['./aba-julgamento-segunda-instancia-impugnacao.component.scss']
})
export class AbaJulgamentoSegundaInstaciaImpugnacaoComponent implements OnInit {

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
        private messageService: MessageService,
        private ImpugnacaoService: ImpugnacaoCandidaturaClientService,
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
            this.ImpugnacaoService.getArquivoJulgamentoSegundaInstancia(this.julgamento.id).subscribe(
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
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public title(): any {
        return this.messageService.getDescription(!this.impugnacao.isIES ? 'LABEL_DO_RECURSO': 'LABEL_DA_RECONSIDERACAO');
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