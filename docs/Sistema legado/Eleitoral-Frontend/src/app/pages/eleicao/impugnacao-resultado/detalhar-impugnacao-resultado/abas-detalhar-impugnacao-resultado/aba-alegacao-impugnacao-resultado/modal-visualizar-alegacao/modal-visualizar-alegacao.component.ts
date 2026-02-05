import { SecurityService } from '@cau/security';
import { StringService } from 'src/app/string.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, Input, ViewChild, TemplateRef, AbstractType } from '@angular/core';
import { MessageService } from '@cau/message';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';


@Component({
  selector: 'modal-visualizar-alegacao',
  templateUrl: './modal-visualizar-alegacao.component.html',
  styleUrls: ['./modal-visualizar-alegacao.component.scss']
})
export class ModalVisualizarAlegacaoComponent implements OnInit {

    @Input() alegacao: any;
    @Input() impugnacao: any;
    public arquivos = [];

    public configuracaoCkeditor: any;

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
      this.inicializaConfiguracaoCkeditor()
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
