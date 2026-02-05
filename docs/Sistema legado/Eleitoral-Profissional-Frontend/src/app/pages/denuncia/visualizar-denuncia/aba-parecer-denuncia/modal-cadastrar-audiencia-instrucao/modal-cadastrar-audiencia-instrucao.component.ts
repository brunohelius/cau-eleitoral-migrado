import { NgForm } from '@angular/forms';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, EventEmitter, OnInit, Input, Output, OnChanges } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { Router } from '@angular/router';
import * as moment from 'moment';

@Component({
    selector: 'modal-cadastrar-audiencia-instrucao',
    templateUrl: './modal-cadastrar-audiencia-instrucao.component.html',
    styleUrls: ['./modal-cadastrar-audiencia-instrucao.component.scss']
})
export class ModalCadastrarAudienciaInstrucaoComponent implements OnInit {

    @Input() audiencia: any;
    @Input() encaminhamento: any;
    @Input() denuncia: any;
    @Output() cancelar: EventEmitter<any> = new EventEmitter();
    @Output() cadastro: EventEmitter<any> = new EventEmitter();
    

    public isSubmetido: boolean = false;

    constructor(
        private router: Router,
        public modalRef: BsModalRef,
        private modalService: BsModalService,
        private messageService: MessageService,
        private denunciaService: DenunciaClientService
    ) { }

    ngOnInit(): void {
        this.audiencia.data = moment(new Date(this.encaminhamento.prazoEnvio)).format("YYYY-MM-DD 00:00");
        this.audiencia.time = moment(new Date(this.encaminhamento.prazoEnvio)).format("HHmm");
    }

    public touchValidaDataHora(element) {
        element.control.markAsTouched();
    }

    /**
     * Cancelar cadastro de Audiencia de instrução.
     * 
     * @param form 
     */
    public cancelaroAudienciaInstrucao(form: NgForm): void {
        this.cancelar.emit();
        this.modalRef.hide();
    }

    /**
     * Salvar Audiência de instrução.
     */
    public salvarAudienciaInstrucao(form: NgForm): void {
        this.isSubmetido = true;
        if (form.valid) {
            let dataEncaminhamento: string =  moment(new Date( this.encaminhamento.prazoEnvio)).format("YYYY-MM-DD HH:mm");
            let dataAudienciaInstrucao: string = moment(new Date( this.getDataIsoAudienciaInstrucao())).format("YYYY-MM-DD HH:mm");
            if( dataEncaminhamento != dataAudienciaInstrucao) {
                this.messageService.addConfirmYesNo('MSG_CONFIRMACAO_DATAHORA_AUDIENCIA_DIFERENTE_DO_AGENDADO',  () => {
                    this.persistirAudienciaInstrucao();
                });
            } else {
               this.persistirAudienciaInstrucao();
            }
            
        }
    }

    /**
     * Aciona serviço que persiste os dados de 'AudienciaInstrucao'.
     */
    private persistirAudienciaInstrucao(): void {
        this.denunciaService.salvarAudienciaInstrucao(this.getDadosAudienciaInstrucao()).subscribe(
            (data) => {
                this.messageService.addMsgSuccess('MSG_AUDIENCIA_INSTRUCAO_CADASTRADA_COM_SUCESSO', [this.denuncia.numeroDenuncia]);
                this.modalRef.hide();
                this.cadastro.emit();
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Montar dados para acionar o serviço responsável por salvar Audiência de instrução.
     */
    private getDadosAudienciaInstrucao(): any {
        let data: string =  moment(new Date(this.getDataIsoAudienciaInstrucao())).format("YYYY-MM-DD HH:mm");
        let dununcia: any = { id: this.denuncia.idDenuncia };
        let encaminhamento: any = { id: this.audiencia.encaminhamento.idEncaminhamento };

        let audienciaInstrucao: any = {
            descricaoDenunciaAudienciaInstrucao: this.audiencia.descricao,
            denuncia: dununcia,
            encaminhamentoDenuncia: encaminhamento,
            arquivosDenunciaAudienciaInstrucao: this.audiencia.arquivos,
            dataAudienciaInstrucao: data,
        };
        return audienciaInstrucao;
    }

    /**
     * Concatenar data e hora no formato datetime.
     */
    public getDataIsoAudienciaInstrucao(): string {
        if(this.audiencia.data && this.audiencia.time) {
            return this.audiencia.data.substring(0, 10) + 'T' + this.audiencia.time.substring(0, 2) + ':' + this.audiencia.time.substring(2, 4) + ':' + '00-03:00';
        }
        return null;
    }

    /**
     * Atualizar valor datetime para validação.
     */
    public updateDatatimeiso(event:Date): void {
        
        if(event) {
            this.audiencia.data =  moment.utc(event).format('YYYY-MM-DD');
        }

        this.audiencia.datatimeiso = this.getDataIsoAudienciaInstrucao();
    }

    /**
     * Salvar arquivo de Audiência.
     *
     * @param arquivos
     */
    public salvarArquivosAudienciaInstrucao(arquivos): void {
        this.audiencia.arquivos = arquivos;
    }

    /**
     * Baixar arquivo de recurso de substituição.
     * 
     * @param arquivo 
     */
    public downloadArquivoAudienciaInstrucao(download: any) {
        download.evento.emit(download.arquivo);
    }

}