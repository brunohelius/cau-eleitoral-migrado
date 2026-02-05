import { MessageService } from '@cau/message';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';

@Component({
  selector: 'arquivo',
  templateUrl: './arquivo.component.html',
  styleUrls: ['./arquivo.component.scss']
})
export class ArquivoComponent implements OnInit {

    @Input() msgHint?: any = {};
    @Input() arquivos?: any = [];
    @Input() maxArquivos?: number = 10;
    @Input() hasUpload: boolean = false;
    @Input() hasDownload: boolean = false;
    @Input() hasExclusao: boolean = false;
    @Input() hasCadastro: boolean = false;
    @Input() hasConfirmacaoExclusao?: boolean = true;
    @Input() msgMaxArquivos?: string = 'MSG_QTD_MAXIMA_UPLOAD';
    @Input() tamanhoArquivo?: number = Constants.ARQUIVO_TAMANHO_10_MEGA;
    @Input() msgArquivoExcluidoComSucesso?: string = 'MSG_EXCLUSAO_COM_SUCESSO';
    @Input() msgConfirmacaoExcluirArquivo?: string = 'MSG_CONFIRMA_EXCLUSAO_ARQUIVO';
    @Input() tipoValidacao?: number = Constants.ARQUIVO_TAMANHO_10_MEGA;

    @Output() download: EventEmitter<any> = new EventEmitter();
    @Output() salvarEvent: EventEmitter<any> = new EventEmitter();
    @Output() excluirArquivo: EventEmitter<any> = new EventEmitter();

    public nomeArquivo: string;

    /**
     * Construtor da classe.
     */
    constructor(
        private messageService: MessageService,
        private ImpugnacaoService: ImpugnacaoCandidaturaClientService
    ) {

    }

    /**
     * Quando o componente inicializar.
     */
    ngOnInit() {

    }

    /**
     * Recupera o arquivo upload.
     *
     * @param event
     * @param posicao
     */
    public downloadArquivo(event: EventEmitter<any>, posicao): void {
        let download = {
            "evento": event,
            "arquivo": this.arquivos[posicao]
        }

        this.download.emit(download);
    }

    /**
     * Exclui a receita da variável itensReceita passando o indice como parâmetro.
     *
     * @param indice
     */
    public excluiUpload(indice) {
        if (this.hasConfirmacaoExclusao) {
            this.messageService.addConfirmYesNo(this.msgConfirmacaoExcluirArquivo,
            () => {
                this.excluirArquivoUpload(indice);
            }, () => {}
            );
        } else {
            this.excluirArquivoUpload(indice);
        }
    }

    /**
     * Realiza a exclusão do arquivo de upload de acordo com o indice informado
     *
     * @param indice
     */
    private excluirArquivoUpload(indice) {
        this.excluirArquivo.emit(this.arquivos[indice]);
        this.arquivos.splice(indice,1);
        this.messageService.addMsgSuccess(this.msgArquivoExcluidoComSucesso);
    }

    /**
     * Realiza Upload do arquivo.
     *
     * @param arquivoEvent
     */
    public upload(arquivoEvent): void {
        let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, "tipoValidacao": this.tipoValidacao, "arquivo": null };
        arquivoTO.arquivo = arquivoEvent

        if (this.arquivos.length < this.maxArquivos) {
            this.ImpugnacaoService.validarArquivo(arquivoTO).subscribe(
                data => {
                    this.arquivos.push(arquivoTO);
                    this.salvarEvent.emit(this.arquivos);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
          this.messageService.addMsgWarning(this.msgMaxArquivos);
        }
    }
}
