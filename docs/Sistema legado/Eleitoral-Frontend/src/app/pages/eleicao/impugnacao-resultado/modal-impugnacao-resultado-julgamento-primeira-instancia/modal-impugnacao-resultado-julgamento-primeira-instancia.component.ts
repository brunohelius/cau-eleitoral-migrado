import { NgForm } from '@angular/forms';
import { CKEditor4 } from 'ckeditor4-angular';
import { BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output, ElementRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import * as _ from 'lodash';
import { JulgamentoAlegacaoImpugnacaoResultadoClientService } from 'src/app/client/julgamento-alegacao-impugnacao-resultado-client/julgamento-alegacao-impugnacao-resultado-client.service';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';

@Component({
  selector: 'modal-cadastro-impugnacao-resultado-julgamento-primeira-instancia',
  templateUrl: './modal-impugnacao-resultado-julgamento-primeira-instancia.component.html',
  styleUrls: ['./modal-impugnacao-resultado-julgamento-primeira-instancia.component.scss']
})
export class ModalImpugnacaoResultadoJulgamentoPrimeiraInstanciaComponent implements OnInit {

  public julgamento: any;
  public submitted: boolean = false;
  public tipoValidacaoArquivo: number;

  @Input() public cauUf: any;
  @Input() public impugnacaoResultado: any;

  @Output() afterCadastrar: EventEmitter<any> = new EventEmitter();
  @Output() afterCancelar: EventEmitter<any> = new EventEmitter();

  constructor(
    private messageService: MessageService,
    private julgamentoAlegacaoImpugnacaoResultadoService: JulgamentoAlegacaoImpugnacaoResultadoClientService
  ) { }

  ngOnInit() {
    this.inicializarJulgamento();
    this.tipoValidacaoArquivo = Constants.ARQUIVO_TAMANHO_15_MEGA;
  }

  /**
   * Inicializar objeto de julgamento.
   */
  public inicializarJulgamento(): void {
    this.julgamento = {
      descricao: '',
      status: null,
      arquivos: []
    };
  }

  /**
   * Adiciona a descrição do julgamento no objeto de julgamento
   * @param evento
   */
  public setDescricaoJulgamento(evento: any): void {
    this.julgamento.descricao = evento
  }

  /**
   * retorna a configuração do ckeditor.
   */
  public getConfiguracaoCkeditor(): any {
    return {
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

  /**
   * Realiza o download do arquivo anexado no formulário
   * @param download
   */
  public downloadArquivoJulgamento(download: any): void {
    download.evento.emit(download.arquivo);
  }

  /**
   * Remove o arquivo anexado no objeto de julgamento
   */
  public excluirArquivo(): void {
    this.julgamento.arquivos = [];
  }

  /**
   * Verifica se existe ao menos um arquivo submetido.
   */
  public hasArquivos(): any {
    if (this.julgamento.arquivos) {
      return this.julgamento.arquivos.length > 0;
    } else {
      return false;
    }
  }

  /**
   * Salvar julgamento de Alegação I.R.
   *
   * @param form
   */
  public salvar(form: NgForm): void {
    this.submitted = true;
    if (form.valid) {
      this.julgamentoAlegacaoImpugnacaoResultadoService.salvarJulgamentoPrimeiraInstancia(this.getDadosSalvar()).subscribe(
        data => {
          this.messageService.addMsgSuccess(
            'MSG_SUCESSO_SALVAR_JULGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO',
            [this.impugnacaoResultado.numero]
          );
          this.afterCadastrar.emit();
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }
  /**
   * Retorna dados para salvar Julgamento de Alegação I.R.
   */
  private getDadosSalvar(): any {
    return {
      descricao: this.julgamento.descricao,
      idImpugnacaoResultado: this.impugnacaoResultado.id,
      idStatusJulgamentoAlegacaoResultado: this.julgamento.status,
      arquivos: this.julgamento.arquivos
    };
  }

  /**
   * Cancelar Cadastro de Julgamento de Alegação I.R
   */
  public cancelar(): void {
    this.messageService.addConfirmYesNo('MSG_CONFIRMAR_CANCELAR', () => {
      this.limparTudo();
      this.afterCancelar.emit();
    });
  }

  /**
   * Limpa todos as variáveis utilizadas pelo componente.
   */
  private limparTudo(): void {
    this.julgamento = [];
    this.submitted = false;
  }

  /**
   * Retorna o 'Número de impugnação de resultado' formatado.
   *
   * @param numero
   */
  public getNumeroFormatado(numero: number, x): string {
    return String(numero).padStart(x, '0');
  }

  /**
   * Retorna texto de hint apresentado na tela de cadastro de pedido de substituição de impugnação.
   */
  public getHintMensagem(): any {
    return ({
        msg: this.messageService.getDescription('MSG_HINT_DOCUMENTO_JUGAMENTO_ALEGACAO_IMPUGNACAO_RESULTADO'),
        icon: "fa fa-exclamation-circle fa-2x pointer"
    });
  }

}