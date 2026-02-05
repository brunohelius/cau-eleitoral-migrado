import { map } from 'rxjs/operators';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from './../../../../../client/julgamento-final/julgamento-final-client.service';
import { Constants } from './../../../../../constants.service';
import { StringService } from 'src/app/string.service';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output } from '@angular/core';
import * as moment from 'moment';
import { ModalVisualizarHistoricoPedidoSubstituicaoComponent } from './modal-visualizar-historico-pedido-substituicao/modal-visualizar-historico-pedido-substituicao.component';


/**
 * Componente responsável pela apresentação do Pedido de substituição do julgamento final..
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'visualizar-pedido-substituicao',
  templateUrl: './visualizar-pedido-substituicao.component.html',
  styleUrls: ['./visualizar-pedido-substituicao.component.scss']
})
export class VisualizarPedidoSubstituicaoComponent implements OnInit {

  @Input() public substituicaoJulgamento: any;
  @Input() public hasSubstituicao: boolean;
  @Input() public chapa: any;
  @Input() public membrosPorSituacao: any;
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  public eIES: boolean;
  public historico: any = [] ;
  public substituicao: any;
  public membroChapaSelecionado: any;
  public pedidoSubstituicao: any;
  public hasJulgamentoSegundaInstancia: boolean;
  public modalPendeciasMembro: BsModalRef | null;
  public modalVisualizarHistoricoPedidoSubstituicao: BsModalRef | null;

  /**
* Construtor da classe.
*/
  constructor(
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
    private modalService: BsModalService,
  ) { }

  ngOnInit(): void {
    this.inicializarSubstituicao();
    this.inicializarHistorico();
    this.inicializaIES();
  }
  /**
   * retorna o numero do registro da substituição
   */
  public getRegistroPedidoSubstituicao(): string {
    return this.substituicao.sequencia;
  }

  /**
   * inicializa o objeto de dados da substituição
   */
  public inicializarSubstituicao(): void {
    let ultimoIndice = this.substituicaoJulgamento.length - 1;

    if (this.substituicaoJulgamento.length > 0) {
      let ultimoRegistro = this.substituicaoJulgamento[ultimoIndice];


      this.substituicao = {
        id: ultimoRegistro.id,
        justificativa: ultimoRegistro.justificativa,
        arquivos: null,
        tipo: ultimoRegistro.tipo,
        sequencia: ultimoRegistro.sequencia,
        membrosSubstitutosDaChapa: ultimoRegistro.membrosSubstituicaoJulgamentoFinal.map(membroSubstituicao => {
            return {
                substituido: membroSubstituicao.indicacaoJulgamentoFinal.membroChapa,
                substituto: membroSubstituicao.membroChapa
            };
        }),
        chapaEleicao: this.chapa,
        dataCadastro: moment.utc(ultimoRegistro.dataCadastro),
        hasJulgamentoSegundaInstancia: ultimoRegistro.hasJulgamentoSegundaInstancia
      };

      if (ultimoRegistro.nomeArquivo) {
        this.substituicao.arquivos = [
          {
            id: ultimoRegistro.id,
            nome: ultimoRegistro.nomeArquivo
          }
        ];
      }
    }
  }

  /**
   * Retorna se o pedido é um recurso
   */
  public isRecurso(): boolean {
    return this.substituicao && this.substituicao.tipo == Constants.E_RECURSO;
  }

  public getTipoJulgamento(): string {
    return this.isRecurso() ? 'RECURSO_SUBSTITUICAO' : 'SUBSTITUICAO';
  }

/**
* Realiza download de arquivo.
*/
  public downloadArquivoDefesaImpugnacao(download: any): void {
    if (download.arquivo.id) {
      this.julgamentoFinalClientService.getArquivoDefesaImpugnacao(download.arquivo.id).subscribe(
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
  * Realiza download de arquivo Recurso Substituição.
  */
  public downloadArquivoRecursoSubstituicao(download: any): void {
    if (download.arquivo.id) {
      this.julgamentoFinalClientService.getArquivoRecursoSubstituicao(download.arquivo.id).subscribe(
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
* Retorna o registro com a mascara
*/
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
  }

  /**
   * Retorna a label de status de validação
   */
  public getLabelStatusValidacao(): any {
    return this.messageService.getDescription('LABEL_STATUS_VALIDACAO');
  }

  /**
   * Verifica o status de Validação do Membro.
   */
  public statusValidacao(membro: any): boolean {
      return  membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
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

   /**
   * Responsavel por voltar a aba para a principal.
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA);
  }

  /**
   * retorna a label de status de validação com quebra de linha
   */
  public getLabelStatusValidacaoQuebraLinha(): any {
    return  this.messageService.getDescription('LABEL_STATUS_VALIDACAO_SUBSTITUICAO_IMPUGNACAO', ['<br>']);
  }

  /**
   * Responsavel por carregar os dados do visualizar histórico
   */
  public carregarPedidoSubstituicao = (id, registro) => {

    this.julgamentoFinalClientService.getSubstituicaoJulgamentoPorId(id).subscribe(data => {
      this.pedidoSubstituicao = data;
      this.abrirModalVisualizarPedidoSubstituicao(registro);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Abre o formulário de visualizar histórico.
   */
  public abrirModalVisualizarPedidoSubstituicao(registro): void {
    const initialState = {
      pedidoSubstituicao: this.pedidoSubstituicao,
      registroSeq: registro,
      chapa: this.chapa
    };

    this.modalVisualizarHistoricoPedidoSubstituicao = this.modalService.show(ModalVisualizarHistoricoPedidoSubstituicaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));

  }
    /**
     * retorna a label do editor de texto.
     */
    public getTituloEditor(): any {
      if(this.historico.length <= 1){
        return 'LABEL_JUSTIFICATIVA_PEDIDO_SUBSTITUICAO';
      }
      if(this.historico.length > 1 && !this.isIES(this.chapa.tipoCandidatura.id)){
        return 'LABEL_JUSTIFICATIVA_RECURSO_PEDIDO_SUBSTITUICAO';
      }
      if(this.historico.length > 1 && this.isIES(this.chapa.tipoCandidatura.id)){
        return 'LABEL_JUSTIFICATIVA_RECONSIDERACAO_PEDIDO_SUBSTITUICAO';
      }
  }
  /**
   * Monta array para grid do histórico.
   */
  public inicializarHistorico(): void {
    const escopo = this;

    if (this.substituicaoJulgamento.length > 0) {
      this.substituicaoJulgamento.forEach(function(value) {

        let tipo: string = escopo.messageService.getDescription('LABEL_SUBSTITUICAO');
        let sequencia = value.sequencia;

        if (value.tipo === Constants.E_RECURSO) {
          tipo = (escopo.isIES(escopo.chapa.tipoCandidatura.id))
            ? escopo.messageService.getDescription('LABEL_RECONSIDERACAO')
            : escopo.messageService.getDescription('LABEL_RECURSO');
          sequencia = sequencia + " - " + tipo;
        }

        escopo.historico.push({
          id: value.id,
          registro: value.sequencia,
          tipo,
          data_cadastro: value.dataCadastro,
          usuario: value.profissional.nome
        });
      });
      escopo.historico.splice(this.substituicaoJulgamento.length - 1, 1);
    }
  }

   /**
   * verifica se o parametro passado é de uma IES
   * caso seja retorna true;
   * @param id
   */
  public isIES(id: number): boolean {
    return (id === Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES);
  }

  /**
   * Redireciona para aba de visualizar julgmaneto após salvar o julgamento
   * @param event
   */
  public redirecionarAposSalvarJulgamento(event): void {
    this.redirecionarVisualizarJulgamento.emit(event);
  }

  /**
   * Responsavel por inicalizar o IsIES.
   */
  public inicializaIES(): boolean {
    return this.eIES = this.chapa.tipoCandidatura.id == Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES;
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

}
