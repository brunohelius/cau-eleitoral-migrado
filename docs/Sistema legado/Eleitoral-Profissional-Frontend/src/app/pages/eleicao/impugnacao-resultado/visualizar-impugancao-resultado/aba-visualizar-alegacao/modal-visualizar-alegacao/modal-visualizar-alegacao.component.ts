import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, ViewChild, TemplateRef } from '@angular/core';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { StringService } from 'src/app/string.service';


@Component({
  selector: 'modal-visualizar-alegacao',
  templateUrl: './modal-visualizar-alegacao.component.html',
  styleUrls: ['./modal-visualizar-alegacao.component.scss']
})
export class ModalVisualizarAlegacaoComponent implements OnInit {

    @Input() alegacao: any;
    @Input() impugnacao: any;

    public arquivos = [];
    public modalVisualizar: BsModalRef;

    @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao: any;

    /**
     * Construtor da classe.
     */
    constructor(
        private modalService: BsModalService,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService
        ) {

    }

    /**
     * Quando o componente inicializar.
     */
    ngOnInit() {
        this.arquivos = this.inicializaArquivo();
    }

    /**
     * método responsável por inicializar os dados do arquivo 
     * para download
     */
    public inicializaArquivo(): any {
        if(this.alegacao.nomeArquivo){
            return [{
                nome: this.alegacao.nomeArquivo,
                nomeFisico: this.alegacao.nomeArquivoFisico
            }];
        }
    }

    /**
     * Exibe modal de visualizar.
     */
    public abrirModal(template: TemplateRef<any>): void {
        this.modalVisualizar = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
    }

    /**
     * Recupera o arquivo conforme a entidade 'resolucao' informada.
     */
    public downloadArquivo(event: any): void {
        this.impugnacaoResultadoClientService.getDocumentoAlegacao(this.alegacao.id).subscribe((data: Blob) =>{
            event.evento.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        })

    }

    /**
     * Retorna o registro com a mascara.
     *
     * @param string
     */
    public getRegistroComMask(string): any {
        return StringService.maskRegistroProfissional(string);
    }
}
