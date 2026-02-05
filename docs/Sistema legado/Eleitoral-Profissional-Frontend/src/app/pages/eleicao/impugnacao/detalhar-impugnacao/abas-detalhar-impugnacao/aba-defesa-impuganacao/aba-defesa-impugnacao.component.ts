import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { NgForm } from '@angular/forms';
import { format } from 'url';
import { DefesaImpugnacaoService } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-client.service';
import { ActivatedRoute } from '@angular/router';
@Component({
    selector: 'aba-defesa-impugnacao',
    templateUrl: './aba-defesa-impugnacao.component.html',
    styleUrls: ['./aba-defesa-impugnacao.component.scss']
})
export class AbaDefesaImpugnacaoComponent implements OnInit {

    @Input() defesa: any;
    @Input() isFinalizadoAtividadeDefesa: any;

    private idPedido: number;
    public isSubmetido: boolean = false;

    @Output() cancelarDefesaImpugnacao: EventEmitter<any> = new EventEmitter();
    @Output() redirecionarAposSalvamento: EventEmitter<any> = new EventEmitter();

    /**
     * Método contrutor da classe
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private defesaImpugnacaoService: DefesaImpugnacaoService
    ) {
        this.idPedido = route.snapshot.params.id;    
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarDefesa();
    }

    /**
     * Salvar Defesa de impugnação.
     * 
     * @param form 
     */
    public salvar(form: NgForm): void {
        this.isSubmetido = true;
        if (this.hasDescricao()) {
            this.defesaImpugnacaoService.salvar(this.defesa).subscribe(
                data => {
                    this.messageService.addMsgSuccess('MSG_DEFESA_INCLUIDA_COM_SUCESSO');
                    this.redirecionarAposSalvamento.emit();
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
     * Verifica se a discricao foi preencida.
     */
    public hasDescricao(): any {
        return this.defesa.descricao;
    }

    /**
     * Cancelar Cadastro de defesa impugnação.
     */
    public cancelar(): void {
        this.cancelarDefesaImpugnacao.emit();
    }

    /**
     * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
     *
     * @param descricao
     */
    public adicionarDescricao(descricao): void {
        this.defesa.descricao = descricao;
    }

    /**
     * Verificar se as campos do formulário são epenas de visualização.
     */
    public isVisualizacao() {
        return this.defesa.id != undefined;
    }

    /**
     * Salvar arquivo de Defesa de impugnação.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.defesa.arquivos = arquivos;
    }

    /**
     * Excluir arquivo de defesa de impugnação.
     * 
     * @param arquivo 
     */
    public excluirArquivo(arquivo): void {
        if(arquivo.id) {
            this.defesa.idArquivosRemover.push(arquivo.id);
        }
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     */
    public downloadArquivoDefesaImpugnacao(download: any): void {
        if(download.arquivo.id) {
            this.defesaImpugnacaoService.getArquivoDefesaImpugnacao(download.arquivo.id).subscribe(
                data => {
                    download.evento.emit(data);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
            download.evento.emit(download.arquivo);
        }
    }

    /**
     * Inicializar variavel de Defesa de Impugnação.
     */
    private inicializarDefesa(): void {
        if (!this.isFinalizadoAtividadeDefesa) {

            if (!this.defesa) {
                this.defesa = {
                    descricao: '',
                    arquivos: [],
                    idPedidoImpugnacao: this.idPedido
                };
            }
            this.defesa.arquivos = this.defesa.arquivos ? this.defesa.arquivos : [];
        }
    }
}

