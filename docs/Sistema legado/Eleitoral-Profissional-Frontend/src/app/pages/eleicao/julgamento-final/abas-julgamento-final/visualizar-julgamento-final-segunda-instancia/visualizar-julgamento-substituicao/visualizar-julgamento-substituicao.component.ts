import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output } from '@angular/core';
import * as moment from 'moment';
import { ModalVisualizarJulgamentoSubstituicaoComponent } from './modal-visualizar-julgamento-substituicao/modal-visualizar-julgamento-substituicao.component';


/**
 * Componente responsável pela apresentação do Pedido de substituição do julgamento final..
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'visualizar-julgamento-substituicao-segunda-instancia',
  templateUrl: './visualizar-julgamento-substituicao.component.html',
  styleUrls: ['./visualizar-julgamento-substituicao.component.scss']
})
export class VisualizarJulgamentoSubstituicaoSegundaInstanciaComponent implements OnInit {

  @Input() public chapa: any;
  @Input() public hasSubstituicao: boolean;
  @Input() public recursoReconsideracao: any;
  @Input() public substituicaoJulgamento: any;
  @Input() public substituicaoSegundaInstancia: any;
  @Input() julgamentoFinal: any;
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  @Output() redirecionarAposSalvamento = new EventEmitter<any>();
  @Output() redirecionarAposSalvarRecursoSubst = new EventEmitter<any>();

  public substituicao: any;
  public pedidoSubstituicao: any;
  public membroChapaSelecionado: any;
  public modalPendeciasMembro: BsModalRef | null;
  public modalVisualizarJulgamentoSubstituicao: BsModalRef | null;

  public titleTab: any;

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
    this.getTitleTab();
  }

  /**
   * Validação para apresentar o título da aba
   */
  public getTitleTab(): any {
      if(this.substituicao.tipo === 'substituicao') {
        this.titleTab = 'LABEL_JULGAMENTO_PEDIDO_SUBSTITUICAO';
      } else if(this.isChapaIES()) {
        this.titleTab = 'LABEL_JULGAMENTO_RECONSIDERACAO_PEDIDO_SUBSTITUICAO';
      } else {
        this.titleTab = 'LABEL_JULGAMENTO_RECURSO_PEDIDO_SUBSTITUICAO';
      }
  }

  public isRecurso(): any {

    return (this.substituicao.tipo == 'recurso') ? true : false;
  }

  public inicializarSubstituicao(): void {

    if (this.substituicaoSegundaInstancia) {

      // Pega o último elemento
      const last = this.substituicaoSegundaInstancia.length - 1;
      
      // Histórico de julgamento de substituições
      const historicoJulgamento = [...this.substituicaoSegundaInstancia];
      historicoJulgamento.splice(last,1);

      const substuicao = this.substituicaoSegundaInstancia[last];
      const numero_registro = (substuicao.tipo == 'recurso') ? substuicao.id + 'Recurso' : substuicao.id;

      const membrosSubstitutosDaChapa = [];
      substuicao.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal.forEach(function(membro){
        let membroSubstituto;
        membroSubstituto = { substituido : membro.indicacaoJulgamentoFinal.membroChapa, substituto : membro.membroChapa}
        membrosSubstitutosDaChapa.push(membroSubstituto);
      });

      this.substituicao = {
        tipo: substuicao.tipo,
        id: substuicao.id,
        registro: substuicao.sequencia,
        descricao: substuicao.descricao,
        arquivos: [{ id: substuicao.id, nome: substuicao.nomeArquivo }],
        membrosSubstitutosDaChapa,
        indicacoes: substuicao.indicacoes,
        historico: historicoJulgamento,
        chapaEleicao: this.chapa,
        isAlterado: substuicao.retificacaoJustificativa ? true : false,
        dataCadastro: moment.utc(substuicao.dataCadastro)
      };
    }
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    // Pega o último elemento
    let last = this.substituicaoSegundaInstancia.length - 1;
    return this.substituicaoSegundaInstancia[last].statusJulgamentoFinal.id === Constants.STATUS_JULGAMENTO_INDEFERIDO;
  }
  
  /**
   * Verifica se o status do julgamneto final está em andamento
   */
  public isChapaEmAndamento(): boolean {
    return this.chapa.statusChapaJulgamentoFinal.id == Constants.STATUS_CHAPA_JULG_FINAL_ANDAMENTO;
  }

  /**
   * Verifica se mostrar botões de cadastrar substituição e recurso da substituição
   */
  public isMostrarBtnsCadastros(): boolean {
    return (
      this.isJulgamentoIndeferido() && !this.isChapaEmAndamento()
    );
  }

  public downloadArquivo(event: any): void {
    (this.isRecurso()) ? this.downloadDocumentoJulgamentoRecursoSegundaInstancia(event) : this.downloadJulgamentoSubstituicaoSegundaInstancia(event);
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadDocumentoJulgamentoRecursoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoRecursoSegundaInstanciaSubstituicao(this.substituicao.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadJulgamentoSubstituicaoSegundaInstancia(event: any): void {
    this.julgamentoFinalClientService.getDocumentoJulgamentoSubstituicaoSegundaInstancia(this.substituicao.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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
      return membro.statusParticipacaoChapa.id === Constants.STATUS_SEM_PENDENCIA;
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
   * Responsavel por carregar os dados do visualizar histórico
   */
  public carregarPedidoSubstituicao = (registro, isAbaRetificacao?: boolean) => {

    let membrosSubstitutosDaChapa = [];
    registro.substituicaoJulgamentoFinal.membrosSubstituicaoJulgamentoFinal.forEach((membro) => {
        membrosSubstitutosDaChapa.push({
          substituido: membro.indicacaoJulgamentoFinal.membroChapa,
          substituto: membro.membroChapa
        });
    });

    const substituicao = {
      tipo: registro.tipo,
      registro: registro.sequencia,
      descricao: registro.descricao,
      julgamentoIndeferido: registro.statusJulgamentoFinal.id === Constants.STATUS_JULGAMENTO_INDEFERIDO,
      arquivos: [{ id: registro.id, nome: registro.nomeArquivo }],
      membrosSubstitutosDaChapa,
      indicacoes: registro.indicacoes,
      chapaUf: this.chapa.uf,
      numeroChapa: this.chapa.numeroChapa,
      chapaEleicao: this.chapa,
      dataCadastro: moment.utc(registro.dataCadastro),
    };

    this.abrirModalVisualizarPedidoSubstituicao(substituicao);
  }

  /**
   * Abre o formulário de visualizar histórico.
   */
  public abrirModalVisualizarPedidoSubstituicao(substituicao): void {
    const initialState = {
      pedidoSubstituicao: substituicao,
    };

    this.modalVisualizarJulgamentoSubstituicao = this.modalService.show(ModalVisualizarJulgamentoSubstituicaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public fecharModalCadastrarSubstituicao() {
    this.redirecionarAposSalvamento.emit(this.substituicao);
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   */
  public fecharModalCadastrarRecurso() {
    this.redirecionarAposSalvarRecursoSubst.emit(this.substituicao);
  }

  public carregarPedidohistorico(id: number): any {
    // TODO - AJUSTAR
  }
}
