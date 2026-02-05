import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { MessageService } from '@cau/message';
import * as moment from 'moment';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import { Constants } from 'src/app/constants.service';


@Component({
  selector: 'app-modal-visualizar-julgamento-substituicao',
  templateUrl: './modal-visualizar-julgamento-substituicao.component.html',
  styleUrls: ['./modal-visualizar-julgamento-substituicao.component.scss']
})
export class ModalVisualizarJulgamentoSubstituicaoComponent implements OnInit {

  @Input() public pedidoSubstituicao?: any;

  public pedido: any;
  public titleTab: any;
  public titleModal: any;
  public retificacoes: any;
  public isAlterado: boolean  = false;
  public retificacoesCarregadas: boolean = false;

  public modalVisualizarJulgamentoSubstituicao: BsModalRef | null;

  public membroChapaSelecionado: any;
  public modalPendeciasMembro: BsModalRef | null;

  public configuracaoCkeditor: any = {};

  constructor(public modalRef: BsModalRef,
    private modalService: BsModalService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
    private messageService: MessageService,) {
  }

  ngOnInit() {
    this.inicializarPedidoSubstituicao();
    this.getTitleTab();
    this.getTitleModal();
    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): any {
    this.titleTab = (this.pedido.tipo === 'substituicao') ? 'LABEL_JULGAMENTO_PEDIDO_SUBSTITUICAO' : 'LABEL_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO';
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleModal(): any {
    this.titleModal = (this.pedido.tipo === 'substituicao') ? 'LABEL_REGISTRO_JULGAMENTO_SUBSTITUICAO' : 'LABEL_REGISTRO_JULGAMENTO_RECURSO_SUBSTITUICAO';
  }

  /**
   * Retorna a contagem de caracteres da justificativa.
   */
  public getContagemJustificativa = () => {
    return 2000 - this.pedido.justificativa.length;
  }

  /**
   * Inicia atributo pedido para preencher o formulário
   */
  public inicializarPedidoSubstituicao(): void {

    if (this.pedidoSubstituicao) {
      this.pedido = this.pedidoSubstituicao;
    }
  }

  public isRecurso(substituicao): any {
    return (substituicao.tipo === 'recurso') ? true : false;
  }

  /**
  * Retorna o registro com a mascara
  */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
  }

  /**
   * Verifica o status de Validação do Membro.
   */
  public statusValidacao(membro: any): boolean {
    if (membro) {
      return membro.statusValidacaoMembroChapa.id === Constants.STATUS_SEM_PENDENCIA;
    } else {
      return false;
    }
  }

  /**
   * Retorna a label de status de validação
   */
  public getLabelStatusValidacao(): any {
    return this.messageService.getDescription('LABEL_STATUS_VALIDACAO');
  }

     /**
   * Exibe modal de listagem de pendencias do profissional selecionado.
   */
  public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
    this.membroChapaSelecionado = element;
    this.modalPendeciasMembro = this.modalService.show(template, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'my-modal modal-dialog-centered'
    });
  }

  public downloadArquivo(event: any): void {
    (this.isRecurso(this.pedido)) ? this.downloadArquivoRecursoSegundoJulgamentoSubstituicao(event) : this.downloadJulgamentoSubstituicaoSegundaInstancia(event);
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivoRecursoSegundoJulgamentoSubstituicao(event: any): void {
    this.julgamentoFinalClientService.getArquivoRecursoSegundoJulgamentoSubstituicao(this.pedido.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadJulgamentoSubstituicaoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoSubstituicaoSegundaInstancia(this.pedido.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  public processaAbaRetificacao(evento): void {
    if(evento == 'LABEL_RETIFICACAO') {
      this.carregaDadosRetificacao()
    }
  }

    /**
   * Carrega os dados das retificações do jultamento
   */
  public carregaDadosRetificacao(): void {
    if(this.retificacoesCarregadas == false ) {
      this.julgamentoFinalClientService.getRetificacoesPorSubstituicao(this.pedido.idSubstituicaoFinal).subscribe(
        (data)=> {
          this.retificacoes = data;
          this.retificacoesCarregadas = true;
        },error => this.messageService.addMsgDanger(error)
      );
    }
  }

  /**
   * Responsavel por carregar os dados do julgamento da substituição
   */
  public carregarPedidoSubstituicao = (registro: any, isAbaRetificacao?: boolean) => {

    let membrosSubstitutosDaChapa = [];
    registro.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal.forEach((membro) => {
        membrosSubstitutosDaChapa.push({
          substituido: membro.indicacaoJulgamentoFinal.membroChapa,
          substituto: membro.membroChapa
        });
    });

    const substituicao = {
      idSubstituicaoFinal: registro.substituicaoJulgamentoFinal.id,
      tipo: registro.tipo,
      id: registro.id,
      registro: registro.sequencia,
      descricao: registro.descricao,
      julgamentoIndeferido: registro.statusJulgamentoFinal.id === Constants.TIPO_JULGAMENTO_INDEFERIDO,
      arquivos: [{ id: registro.id, nome: registro.nomeArquivo }],
      membrosSubstitutosDaChapa,
      indicacoes: registro.indicacoes,
      chapaUf: this.pedidoSubstituicao.uf,
      numeroChapa: this.pedidoSubstituicao.numeroChapa,
      chapaEleicao: this.pedidoSubstituicao,
      dataCadastro: moment.utc(registro.dataCadastro),
      retificacao: registro.retificacaoJustificativa  && !isAbaRetificacao? registro.retificacaoJustificativa : undefined,
      isAlterado: registro.retificacaoJustificativa && !isAbaRetificacao ? true : false
    };

    this.isAlterado = substituicao.isAlterado;
    this.abrirModalVisualizarPedidoSubstituicao(substituicao);
  }

  /**
   * Método responsável por criar uma nova instância da classe atual
   * e carregar o modal com os dados da substituição
   * @param substituicao
   */
  public abrirModalVisualizarPedidoSubstituicao(substituicao): void {
    const initialState = {
      pedidoSubstituicao: substituicao,
    };

    this.modalVisualizarJulgamentoSubstituicao = this.modalService.show(ModalVisualizarJulgamentoSubstituicaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
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
