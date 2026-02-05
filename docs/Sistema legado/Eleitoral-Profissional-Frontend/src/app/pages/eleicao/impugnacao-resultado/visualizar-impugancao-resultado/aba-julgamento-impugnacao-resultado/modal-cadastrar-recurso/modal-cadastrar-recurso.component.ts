import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { Subject } from 'rxjs';
import { Constants } from 'src/app/constants.service';



@Component({
  selector: 'modal-cadastrar-recurso',
  templateUrl: './modal-cadastrar-recurso.component.html',
  styleUrls: ['./modal-cadastrar-recurso.component.scss']
})
export class ModalCadastrarRecursoComponent implements OnInit {

    @Input() impugnacao: any;
    @Input() julgamento: any;
    @Input() isIES?: boolean = false;
    @Input() tipoProfissional?: any;
    
    public submitted: boolean;
    public onSave: Subject<Number> = new Subject<Number>();
    
    public recurso: any;

    /**
     * Construtor da classe.
     */
    constructor(
        private router: Router,
        public modalRef: BsModalRef,
        private route: ActivatedRoute,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private securtyService: SecurityService,
        private impugnacaoResultadoClientService:ImpugnacaoResultadoClientService
        ) {

    }

    ngOnInit() {
        this.recurso = this.inicializaRecurso();
    }

    public tipoRecurso(): number {
        if(this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA){
            return Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO
        } else {
            return Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE
        }
    }

    public salvar():void {
        this.submitted = true;
       if(this.recurso.descricao != null && this.recurso.descricao != ''){
            this.impugnacaoResultadoClientService.salvarRecurso(this.recurso).subscribe( data =>{       
                this.messageService.addMsgSuccess(this.getMensagemSalvar());
                this.onSave.next(this.recurso.idTipoRecursoImpugnacaoResultado);
                this.modalRef.hide()
            }, err => {
                this.messageService.addMsgDanger(err);
            });
       } else {
           this.messageService.addMsgDanger('MSG_CAMPO_OBRIGATORIO');
       }
    }

    public cancelar(): void {
        this.recurso = this.inicializaRecurso();
        this.modalRef.hide()
    }

    /**
     * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
     *
     * @param descricao
     */
    public adicionarDescricao(descricao): void {
        this.recurso.descricao = descricao;
    }

    /**
     * Resoponsavel por salvar os arquivos que foram submetidos no componete arquivo.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.recurso.arquivos = arquivos;
    }

    /**
     * Retorna texto de hint do arquivo.
     */
    public getHintArquivo(): any {
        return ({
            msg: this.messageService.getDescription('MSG_HINT_IMPUGNACAO_RESULTADO_ARQUIVO'),
            icon: "fa fa-info-circle fa-2x pointer"
        });
    }

    /**
     * Responsavel por fazer o download do arquivo.
     */
    public downloadArquivo(params: any): void {
        params.evento.emit(params.arquivo);
    }

    /**
     * Método responsável por inicializar os dados de alegação
     */
    public inicializaRecurso(): any {
        return {
            descricao: '',
            arquivos: [],
            julgamentoAlegacaoImpugResultado: {
                id: this.julgamento.id
            },
            idTipoRecursoImpugnacaoResultado: this.tipoRecurso()
        }
    }

    /**
     * Retorna o título do modal de acordo com o tipo de candidatura
     */
    public getTituloModal(): string {
        
        let referencia = this.isIES 
        ? this.messageService.getDescription('LABEL_RECONSIDERACAO') 
        : this.messageService.getDescription('LABEL_RECURSO');

        return (
            this.messageService
            .getDescription(
                'TITLE_RECURSO_RECONSIDERACAO_JULGAMENTO_PRIMEIRA_INSTANCIA',
                [referencia]
            )
        )
    }

    /**
     * Retorna a descrição da label do campo de descrição
     */
    public getLabelDescricao(): string {
        
        let msg = this.isIES 
        ? 'LABEL_DESCRICAO_DA_RECONSIDERACAO'
        : 'LABEL_DESCRICAO_DO_RECURSO'

        return msg;
    }


    /**
     * Retorna a mensgem de sucesso ao salvar o recurso ou reconsideração
     */
    public getMensagemSalvar(): string {
        
        let referencia = this.isIES 
        ? 'a Reconsideração' 
        : 'o Recurso';

        return (
            this.messageService
            .getDescription(
                'MSG_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_SALVO_COM_SUCESSO',
                [referencia]
            )
        )
    }
}


