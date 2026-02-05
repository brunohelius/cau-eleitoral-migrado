import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from './../../../../../client/julgamento-final/julgamento-final-client.service';
import { Constants } from './../../../../../constants.service';
import { StringService } from 'src/app/string.service';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output } from '@angular/core';
import * as moment from 'moment';
import { ModalVisualizarHistoricoPedidoSubstituicaoComponent } from './modal-visualizar-historico-pedido-substituicao/modal-visualizar-historico-pedido-substituicao.component';
import { isUndefined, isNullOrUndefined } from 'util';
import { constants } from 'os';


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
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  public eIES: boolean;
  public historico: any = [] ;
  public substituicao: any;
  public membroChapaSelecionado: any;
  public pedidoSubstituicao: any;
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

  public inicializarSubstituicao(): void {
    if (this.substituicaoJulgamento.length > 0) {

      const ultimoRegistro = this.substituicaoJulgamento[this.substituicaoJulgamento.length - 1];

      this.substituicao = {
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
   * retorna o numero do registro da substituição
   */
  public getRegistroPedidoSubstituicao(): string {
    return this.substituicao.sequencia;
  }


  /**
   * Retorna se o pedido é um recurso
   */
  public isRecurso(): boolean {
    return this.substituicao.tipo == Constants.E_RECURSO;
  }

  public downloadArquivo(download: any): void {
    if (!this.isRecurso()) {
      this.downloadArquivoDefesaImpugnacao(download);
    } else {
      this.downloadArquivoRecursoSubstituicao(download);
    }
  }

/**
* Realiza download de arquivo.
*/
  public downloadArquivoDefesaImpugnacao(download: any): void {
    if (download.arquivo.id) {
    
      //this.substituicao.id
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
    if (membro) {
      return membro.statusValidacaoMembroChapa.id === Constants.STATUS_SEM_PENDENCIA;
    } else {
      return false;
    }
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
  public voltarAbaPrincipal(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_RECRUSO);
  }

  /**
   * Monta array para grid do histórico.
   */

  public inicializarHistorico(): void {
    const escopo = this;

    if (this.substituicaoJulgamento.length > 0) {
      this.substituicaoJulgamento.forEach( (value) => {

        let tipoFormatado: string = escopo.messageService.getDescription('LABEL_SUBSTITUICAO');
        let sequencia = value.sequencia;

        if (value.tipo === Constants.E_RECURSO) {
          tipoFormatado = (escopo.isIES(escopo.chapa.tipoCandidatura.id))
            ? escopo.messageService.getDescription('LABEL_RECONSIDERACAO')
            : escopo.messageService.getDescription('LABEL_RECURSO');
          sequencia = sequencia + " - " + tipoFormatado;
        }

        let arquivos = null;
        if (value.nomeArquivo) {
          arquivos = [
            {
              id: value.id,
              nome: value.nomeArquivo
            }
          ];
        }

        escopo.historico.push({
          justificativa: value.justificativa,
          arquivos,
          tipo: value.tipo,
          tipoFormatado,
          registro: sequencia,
          membrosSubstitutosDaChapa: value.membrosSubstituicaoJulgamentoFinal.map(membroSubstituicao => {
            return {
              substituido: membroSubstituicao.indicacaoJulgamentoFinal.membroChapa,
              substituto: membroSubstituicao.membroChapa
            };
          }),
          chapaEleicao: escopo.chapa,
          usuario: value.profissional.nome,
          dataCadastro: moment.utc(value.dataCadastro),
          hasJulgamentoSegundaInstancia: value.hasJulgamentoSegundaInstancia
        });
      });
      this.historico.splice(this.substituicaoJulgamento.length - 1, 1);
    }
  }

  /**
   * Responsavel por carregar os dados do visualizar histórico
   */
  public carregarPedidoSubstituicao = (pedido, registro) => {
    this.pedidoSubstituicao = pedido;
    this.abrirModalVisualizarPedidoSubstituicao(registro);
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
   * verifica se o parametro passado é de uma IES
   * caso seja retorna true;
   * @param id
   */
  public isIES(id: number): boolean {
    return (id === Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES);
  }
  /**
   * Responsavel por inicalizar o IsIES.
   */
  public inicializaIES(): boolean {
    return this.eIES = this.chapa.tipoCandidatura.id == Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES;
  }
}
