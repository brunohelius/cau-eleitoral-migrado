import * as _ from 'lodash';
import { NgForm } from '@angular/forms';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, EventEmitter, Output} from '@angular/core';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';

@Component({
  selector: 'modal-cadastro-impugnacao-resultado-julgamento-segunda-instancia',
  templateUrl: './modal-impugnacao-resultado-julgamento-segunda-instancia.component.html',
  styleUrls: ['./modal-impugnacao-resultado-julgamento-segunda-instancia.component.scss']
})

export class ModalImpugnacaoResultadoJulgamentoSegundaInstanciaComponent implements OnInit {

  public julgamento: any;
  public submitted: boolean = false;
  public tipoValidacaoArquivo: number;
  public labelJulgamento: string = '';
  public _dadosIniciais: any;

  @Input() public cauUf: any;
  @Input() public impugnacaoResultado: any;
  @Input() public isHomologacao?: boolean = false;
  @Input() public isJulgamentoPrimeiraProcedente?: any;

  @Output() afterCadastrar: EventEmitter<any> = new EventEmitter();
  @Output() afterCancelar: EventEmitter<any> = new EventEmitter();

  constructor(
    private messageService: MessageService,
    private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService
  ) { }

  ngOnInit() {
    this.inicializarJulgamento();
    this._dadosIniciais = this.getDadosSalvar();
    this.tipoValidacaoArquivo = Constants.ARQUIVO_TAMANHO_15_MEGA;
  }

  /**
   * Inicializar objeto de julgamento.
   */
  public inicializarJulgamento(): void {
    this.julgamento = {
      descricao: '',
      status: this.preDefineStatusJulgamento(),
      arquivos: []
    };
  }

  /**
   * Retorna o resultado do julgamento de primeira instância
   */
  public preDefineStatusJulgamento(){
    if(this.isJulgamentoPrimeiraProcedente) {
      return Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
    } else {
      return Constants.STATUS_IMPUGNACAO_RESULTADO_IMPROCEDENTE;
    }
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
      this.impugnacaoResultadoClientService.salvarJulgamentoSegundaInstancia(this.getDadosSalvar()).subscribe(
        data => {
          this.messageService.addMsgSuccess(
            this.messageService.getDescription(
              'MSG_SUCESSO_SALVAR_JULGAMENTO_SEGUNDA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO',
              [this.impugnacaoResultado.numero]
            )
          );
          this.afterCadastrar.emit(data);
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      )
    }
  }
  /**
   * Retorna dados para salvar Julgamento de Alegação I.R.
   */
  private getDadosSalvar(): any {
    return {
      descricao: this.julgamento.descricao,
      idImpugnacaoResultado: this.impugnacaoResultado.id,
      idStatusJulgamentoRecursoImpugResultado: this.julgamento.status,
      arquivos: this.julgamento.arquivos
    };
  }

  /**
   * Cancelar Cadastro de Julgamento de Alegação I.R
   */
  public cancelar(): void {

    if(this._dadosIniciais.descricao != this.getDadosSalvar().descricao
       ||this.getDadosSalvar().arquivos.length > 0
       || this.getDadosSalvar().idStatusJulgamentoRecursoImpugResultado != this._dadosIniciais.idStatusJulgamentoRecursoImpugResultado
    ){
      this.messageService.addConfirmYesNo('MSG_CONFIRMAR_CANCELAR', () => {
        this.limparTudo();
        this.afterCancelar.emit();
      });
    } else {
      this.limparTudo();
      this.afterCancelar.emit();
    }

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

  /**
   * Retorna a label do card-panel do status do julgamento de segunda instância
   */
  public getLabelComboBox(): string {
    if(this.cauUf.id == Constants.ID_CAUBR) {
      return 'LABEL_JULGAMENTO_RECONSIDERACAO_IMPUGNACAO_RESULTADO';
    } else {
      return 'LABEL_JULGAMENTO_RECURSO_IMPUGNACAO_RESULTADO';
    }
  }

}