
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';
import { StringService } from 'src/app/string.service';


/**
 * Componente responsável pela apresentação do detalhamento do pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-pedido-impugnacao',
    templateUrl: './aba-pedido-impugnacao.component.html',
    styleUrls: ['./aba-pedido-impugnacao.component.scss']
})

export class AbaPedidoImpugnacao implements OnInit {

  @Input() impugnacao: any;
  @Input() defesaValidacao: any;

  @Output() cadastrarDefesaImpugnacao: EventEmitter<any> = new EventEmitter();

  public configuracaoCkeditor: any = {};

  constructor(
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private impugnacaoService: ImpugnacaoCandidaturaClientService,    
  ) {}

  ngOnInit() {
    this.inicializaIconeTitulo();
  }

  /**
   * Inicializa ícone e título do header da página .
   */
  private inicializaIconeTitulo(): void {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('TITLE_PEDIDO_DE_IMPUGNACAO')
    });
  }

  /**
   * Retorna a opsição formatada
   *
   * @param numeroOrdem
   */
  public getPosicaoFormatada(numeroOrdem) {
    return numeroOrdem > 0 ? numeroOrdem : '-';
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
    };
  }

  /**
   * Responsavel por fazer o download do arquivo.
   */
  public downloadArquivo(params): void {
    if(params.arquivo.id) {
      this.impugnacaoService.downloadArquivoImpugnacao(params.arquivo.id).subscribe(
        data => {
        params.evento.emit(data);
      }, error => {
          this.messageService.addMsgDanger(error);
      });
    } else {
      params.evento.emit(params.arquivo);
    }
  }

  /**
   * Aciona evento de cadastro de defesa de impugnação.
   */
  public onCadastrarDefesa(): void {
    this.cadastrarDefesaImpugnacao.emit();
  }

  /**
   * Verifica regras para apresentação do Cadastro de defesa.
   * 
   * @return boolen
   */
  public isMostrarCadastrarDefesa(): boolean {
    if(this.defesaValidacao) {
      return (
        this.defesaValidacao.isResponsavel 
        && !this.defesaValidacao.hasDefesaImpugnacao 
        && this.defesaValidacao.isAtividadeSecundariaVigente
      );
    }
    return false;
  }

  /**
   * Retorna o registro com a mascara 
   * @param str 
   */
  public getRegistroComMask(str) {
    return StringService.maskRegistroProfissional(str);
  }
}
