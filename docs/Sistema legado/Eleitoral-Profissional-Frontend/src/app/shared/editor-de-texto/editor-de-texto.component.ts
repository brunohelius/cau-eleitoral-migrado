
import { Component, OnInit, Input, Output, EventEmitter, ElementRef, ViewChild, Self, Optional } from '@angular/core';
import { CKEditor4 } from 'ckeditor4-angular';
import { StringService } from 'src/app/string.service';
import { NgModel, NgControl } from '@angular/forms';
import { Constants } from 'src/app/constants.service';
import { AbstractCustomInputComponent } from '../abstract-custon-input-component';

@Component({
  selector: 'editor-de-texto',
  templateUrl: './editor-de-texto.component.html',
  styleUrls: ['./editor-de-texto.component.scss']
})
export class EditorDeTextoComponent extends AbstractCustomInputComponent implements OnInit {
    
    @Input() valor: string;
    @Input() isHabilitado: boolean;
    @Input() configuracao?: any;
    @Input() isRequerido: boolean;
    @Input() tamanhoMaximo?: number;
    @Input() tamanhoMinimo?: number;
    @Input() isSubmetido?: boolean;
    @Input() isMostrarTamanhoTextoRestantes?: boolean;

    @Output() valorChange = new EventEmitter<string>();

    @Input() required;
    @Input() disabled;

    @ViewChild("ckeditorReferencia", { static: true }) ckeditorReferencia: NgModel;

    private textoSimples: string;

    constructor( @Self() @Optional() public control: NgControl ) {
        super(control);
    }

    ngOnInit() {
        if(!this.configuracao) {
            this.inicializarConfiguracaoCkeditor();
        }
        this.valor = this.valor ? this.valor : '';
        this.textoSimples = this.textoSimples != undefined ? this.textoSimples : '';
        this.tamanhoMaximo = this.tamanhoMaximo ? this.tamanhoMaximo : Constants.TAMANHO_MAXIMO_PADRAO_EDITOR_TEXTO;
        this.tamanhoMinimo = this.tamanhoMinimo ? this.tamanhoMinimo : Constants.TAMANHO_MINIMO_PADRAO_EDITOR_TEXTO;
    }

    /**
     * Emitir evento de mudança de valor.
     * 
     * @param valor 
     */
    public valorChangeEmit(valor: any): void {
        this.onChange();
        this.valorChange.emit(valor)
    }

    /**
     * Setar captura de evento de 'key up' e 'colagem(Ctrl + V)' com o objetivo de validar a quantidade de caráteres.
     * 
     * @param event 
     */
    public onRedyCkeditor(event: CKEditor4.EventInfo){
        let max = this.tamanhoMaximo+1;

        event.editor.on('key', function(event2) {
            let simplesTexto = StringService.getPlainText(event2.editor.getData());
            if( !StringService.isLimitValid(simplesTexto, max) && StringService.isTextualCaracter(event2.data.keyCode)) {                
                event2.cancel();
            }               
        });

        event.editor.on('paste', function(event2) {
            let simplesTexto = StringService.getPlainText(event2.editor.getData()) + event2.data.dataValue;
            if(!StringService.isLimitValid(simplesTexto, max)) {
                event2.cancel();
            }                     
        });
    }

     /**
     * Alterar valor da decrição do currículo.
     * 
     * @param event 
     */
    public onChangeCkeditor(event: CKEditor4.EventInfo){
        this.setTextoSimples(StringService.getPlainText(event.editor.getData()));   
    }

    /**
     * Retorna tamanho do texto(em formato de texto simples).
     */
    public getTamanhoTexto(): number {
        return this.textoSimples.length;
    }

    /**
     * Retorna quantidade de caracteres restantes para atingir o valor máximo de tamanho do campo.
     */
    public getTamanhoTextoRestantes(): number {
        return this.tamanhoMaximo - this.textoSimples.length;
    }

    /**
     * Preenche  valor do texto do Ckeditor em formato de texto simples.
     * Por texto simples quero dizem, apenas texto sem tag html.
     * 
     * @param text 
     */
    private setTextoSimples(text: string): void {
        this.textoSimples = StringService.getPlainText(text).slice(0, -1);
    }

    /**
     * Preenche  valor do texto do Ckeditor em formato de texto simples.
     * Por texto simples quero dizem, apenas texto sem tag html.
     */
    public getTextoSimples(): string {
        return this.textoSimples;
    }

    /**
     * Inicializa a configuração do ckeditor.
     */
    private inicializarConfiguracaoCkeditor(): void {
    this.configuracao = {
        toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        ],
    };
    }
}