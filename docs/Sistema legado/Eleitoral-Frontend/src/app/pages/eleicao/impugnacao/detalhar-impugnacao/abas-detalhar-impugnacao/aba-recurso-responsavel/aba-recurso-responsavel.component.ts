import { MessageService } from '@cau/message';
import { Component, OnInit, Input} from '@angular/core';

import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';


@Component({
    selector: 'aba-recurso-responsavel',
    templateUrl: './aba-recurso-responsavel.component.html',
    styleUrls: ['./aba-recurso-responsavel.component.scss']
})
export class AbaRecursoResponsavelComponent implements OnInit {

    @Input() isIes: any;
    @Input() isContrarrazao: any;
    @Input() recursoImpugnante: any;
    @Input() recursoResponsavel: any;
    @Input() configuracaoCkeditor: any;
    @Input() isFinalizadoContrarrazao: any;

    public hasRecurso: boolean;

    /**
     * Método contrutor da classe
     */
    constructor(
        private messageService: MessageService,
        private recursoService: ImpugnacaoCandidaturaClientService,
    ) {

    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.hasRecurso = this.recursoResponsavel != null;
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public labelDescricao(): any {
        return this.isIes ? 'TITLE_RECONSIDERACAO_JULGMANETO_RESPONSAVEL': 'TITLE_RECURSO_JULGMANETO_RESPONSAVEL';
    }
    
    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public title(): any {
        return this.messageService.getDescription(!this.isIes ? 'LABEL_RECURSO': 'LABEL_RECONSIDERACAO');
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public title2(): any {
        return this.messageService.getDescription(!this.isIes ? 'LABEL_DO_RECURSO': 'LABEL_DA_RECONSIDERACAO');
    }

    /**
     * Realiza download de arquivo do recurso do pedido de impugnacao.
     */
    public downloadArquivo(download: any, id: number): void {
        this.recursoService.getArquivoRecurso(id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );       
    }

    /**
     * Realiza download de arquivo da Contrarrazao.
     */
    public downloadArquivoContrarrazao(download: any): void {
        this.recursoService.getArquivoContrarrazao(this.recursoImpugnante.contrarrazaoRecursoImpugnacao.id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );       
    }  

    /**
    * Verifica se sub-aba de recurso de responsável deve ser exibida.
    */
    public isMostrarRecurso(): boolean {
        return this.recursoImpugnante && this.isContrarrazao;
    }

    /**
     * Verifica se pode ou não mostrar a contrarrazão.
     */
    public isMostrarContrarrazao(): boolean {
        return (this.recursoImpugnante && this.recursoImpugnante.contrarrazaoRecursoImpugnacao != undefined) || this.isFinalizadoContrarrazao;
    }

    /**
     * Verifica se pode ou não mostrar a contrarrazão.
     */
    public isMostrarMsgContrarrazao(): boolean {
        return this.isFinalizadoContrarrazao && !(this.recursoImpugnante && this.recursoImpugnante.contrarrazaoRecursoImpugnacao != undefined);
    }
}
