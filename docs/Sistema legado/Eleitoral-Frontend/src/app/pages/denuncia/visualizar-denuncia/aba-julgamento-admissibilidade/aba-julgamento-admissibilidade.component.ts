import {Component, EventEmitter, Input, OnInit} from '@angular/core';
import {Constants} from '../../../../constants.service';
import {StringService} from '../../../../string.service';
import {DenunciaClientService} from '../../../../client/denuncia-client/denuncia-client.service';
import {MessageService} from '@cau/message';

@Component({
    selector: 'app-aba-julgamento-admissibilidade',
    templateUrl: './aba-julgamento-admissibilidade.component.html',
    styleUrls: ['./aba-julgamento-admissibilidade.component.scss']
})
export class AbaJulgamentoAdmissibilidadeComponent implements OnInit {

    @Input() denuncia;
    @Input() usuario;

    configuracaoCkeditor = {
        title: 'dsAdmissibilidade',
        removePlugins: 'elementspath',
        toolbar: [
            {
                name: 'basicstyles',
                groups: ['basicstyles', 'cleanup'],
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-']
            },
            {name: 'links', items: ['Link']},
            {name: 'insert', items: ['Image']},
            {
                name: 'paragraph',
                groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']
            },
        ],
    };

    public descricaoDespachoSimpleText = '';

    constructor(
        private denunciaService: DenunciaClientService,
        private messageService: MessageService
    ) {
    }

    ngOnInit() {
        this.descricaoDespachoSimpleText = StringService.getPlainText(this.denuncia.julgamentoAdmissibilidade.descricao).slice(0, -1);
    }

    public getContagemDespachoAdmissibilidade() {
        return Constants.TAMANHO_LIMITE_2000 - this.descricaoDespachoSimpleText.length;
    }

    public downloadArquivo(event: EventEmitter<any>, idArquivo) {
        return this.denunciaService.downloadArquivoJulgamentoAdmissibilidade(idArquivo).subscribe((data: Blob) => {
            event.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    public voltar() {
        window.history.back();
    }
}
