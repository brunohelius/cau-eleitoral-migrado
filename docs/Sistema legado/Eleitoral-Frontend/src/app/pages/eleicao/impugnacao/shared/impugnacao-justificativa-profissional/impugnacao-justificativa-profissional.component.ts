import { CKEditor4 } from 'ckeditor4-angular';
import { Constants} from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'impugnacao-justificativa-profissional',
  templateUrl: './impugnacao-justificativa-profissional.component.html',
  styleUrls: ['./impugnacao-justificativa-profissional.component.scss']
})
export class ImpugnacaoJustificativaProfissionalComponent implements OnInit {
  
  @Input() valor: string;
  @Input() label?: string;
  @Input() tamanhoMaximo?: number;
  @Input() isHabilitado?: boolean;
  @Input() isRequerido?: boolean;
  @Output() valorChange = new EventEmitter<string>();

  public configuracaoCkeditor: any = {};

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.isHabilitado == this.isHabilitado ? this.isHabilitado : true;
    this.isRequerido = this.isRequerido ? this.isRequerido : true;
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
      ],
      title: 'Justificativa'
    };
  }

}
