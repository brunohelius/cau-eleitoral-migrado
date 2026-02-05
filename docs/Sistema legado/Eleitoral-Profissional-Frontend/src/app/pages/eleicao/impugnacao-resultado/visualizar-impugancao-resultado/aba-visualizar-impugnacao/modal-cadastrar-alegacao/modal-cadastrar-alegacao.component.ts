import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { Subject } from 'rxjs';



@Component({
  selector: 'modal-cadastrar-alegacao',
  templateUrl: './modal-cadastrar-alegacao.component.html',
  styleUrls: ['./modal-cadastrar-alegacao.component.scss']
})
export class ModalCadastrarAlegacaoComponent implements OnInit {

    @Input() impugnacao: any;
    
    public submitted: boolean;
    public onSave: Subject<boolean> = new Subject<boolean>();
    
    public alegacao: any;

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
        this.alegacao = this.inicializaAlegacao();
    }

    public salvar():void {
        this.submitted = true;
       if(this.alegacao.narracao != null && this.alegacao.narracao != ''){
            this.impugnacaoResultadoClientService.salvarAlegacao(this.alegacao).subscribe( data =>{       
                this.messageService.addMsgSuccess('MSG_ALEGACAO_SALVA_COM_EXITO', [this.impugnacao.numero])
                this.onSave.next(true);
                this.modalRef.hide()
            }, err => {
                this.messageService.addMsgDanger(err);
            });
       } else {
           this.messageService.addMsgDanger('MSG_CAMPO_OBRIGATORIO');
       }
    }

    public cancelar(): void {
        this.alegacao = this.inicializaAlegacao();
        this.modalRef.hide()
    }

    /**
     * Resoponsavel por adicionar a narracao que fora submetido no compomente editor de texto.
     *
     * @param narracao
     */
    public adicionarDescricao(narracao): void {
        this.alegacao.narracao = narracao;
    }

    /**
     * Resoponsavel por salvar os arquivos que foram submetidos no componete arquivo.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.alegacao.arquivos = arquivos;
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
    public inicializaAlegacao(): any {
        return {
            narracao: '',
            arquivos: [],
            idPedidoImpugnacaoResultado: this.impugnacao.id
        }
    }
}
