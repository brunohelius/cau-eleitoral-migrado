import { SecurityService } from '@cau/security';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Router, ActivatedRoute } from '@angular/router';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';
import { JulgamentoFinalClientService } from './../../../../../client/julgamento-final/julgamento-final-client.service';

import * as _ from 'lodash';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'visualizar-julgamento-final-primeira',
  templateUrl: './vizualizar-julgamento-final-primeira.component.html',
  styleUrls: ['./vizualizar-julgamento-final-primeira.component.scss']
})
export class VisualizarJulgamentoFinalPrimeiraComponent implements OnInit {

  @Input() chapa: any = [];
  @Input() julgamentoFinal: any;
  @Input() recursoReconsideracao: any;
  @Input() membrosPorSituacao: any;
  @Input() isIES: boolean;

  public isAtual: boolean = true;

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarAposSalvamento = new EventEmitter<any>();
  @Output() recarregarAba = new EventEmitter<any>();

  @ViewChild('templateAlterarJulgamento', { static: true }) templateAlterarJulgamento: TemplateRef<any>;
  @ViewChild('templateConfirmarAlterarJulgamento', { static: true }) templateConfirmarAlterarJulgamento: TemplateRef<any>;
  @ViewChild('templateVisualizarRetificacaoJulgamento', { static: true }) templateVisualizarRetificacaoJulgamento: TemplateRef<any>;
  @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao;

  public configuracaoCkeditor: any = {};
  public modalConfirmarAlterarJulgamento: BsModalRef;
  public formAlterarJulgamento: BsModalRef;
  public visualizarRetificacaoJulgamento: BsModalRef;
  public acaoAlterarJulgamento: number;
  public opcaoAlteracaoJulgamento: number;
  public julgamentoFinalRetificacaoSelecionado: any;
  public sequenciaJulgamentoFinalRetificacaoSelecionado: number;

  /**
   * Construtor da classe.
   */
  constructor(
    private route: ActivatedRoute,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private modalService: BsModalService,
    private julgamentoFinalService: JulgamentoFinalClientService,
    private securityService: SecurityService
  ) {
    this.chapa = route.snapshot.data["chapas"];
  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {
    this.getTituloPagina();
    this.inicializaConfiguracaoCkeditor();
    this.ordenarIndicacoes();
  }

  public ordenarIndicacoes(): void {
    if (this.julgamentoFinal.indicacoes) {

      for(let i= 0; i < this.julgamentoFinal.indicacoes.length; i++){
        this.julgamentoFinal.indicacoes[i].numeroOrdem = Number(this.julgamentoFinal.indicacoes[i].numeroOrdem)
      }

      this.julgamentoFinal.indicacoes = _.orderBy(this.julgamentoFinal.indicacoes,
        ['numeroOrdem', 'tipoParticipacaoChapa.id'], ['asc', 'asc']);
    }
  }

  /**
   * Define o título do módulo da página
   */
  public getTituloPagina(): any {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-wpforms',
      description: this.messageService.getDescription('Julgamento')
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   */
  public downloadArquivo(event: any): void {
    this.julgamentoFinalService.getDocumentoJulgamentoFinal(this.julgamentoFinal.id).subscribe((data: Blob) => {
      event.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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

  /**
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoIndeferido(): boolean {
    return this.julgamentoFinal.statusJulgamentoFinal.id === Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Verifica se possui indicações de membros
   */
  public possuiIndicacao(): boolean {
    return (
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes
    );
  }

  /**
   * Volta para uma determinada aba do módulo julgamento final
   */
  public voltar() {
    this.voltarAba.emit(Constants.ABA_ACOMPANHAR_CHAPA);
  }

  /**
  * Retorna label de botão de Alteração de julgamento.
  */
  public getLabelAlterarJulgamento(): string {
    return this.messageService.getDescription('TITLE_ALTERAR_JULGAMENTO');
  }

  /**
   * Exibir modal de confirmação de alteração de julgamento.
   */
  public confirmarAlteracaoJulgamento(): void {
    this.modalConfirmarAlterarJulgamento = this.modalService.show(
      this.templateConfirmarAlterarJulgamento,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
  * Método que apresenta popup de  alterar jultamento.
  */
  public abrirFormAlterarJulgamento(opcaoConfirmacao: number): void {
    this.opcaoAlteracaoJulgamento = opcaoConfirmacao;
    this.modalConfirmarAlterarJulgamento.hide();
    this.acaoAlterarJulgamento = !this.isJulgamentoIndeferido() ? Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO : Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO;
    this.formAlterarJulgamento = this.modalService.show(
      this.templateAlterarJulgamento,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
   * Cancelar cadastro de alteração de julgamento, fechando a modal.
   */
  public fecharformAlterarJulgamento(): void {
    this.formAlterarJulgamento.hide();
  }

  /**
   * Verifica se o botão de alterar julgamento deve ser exibido.
   */
  public isMostrarAlterarJulgamento(): boolean {
    return this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN) && (this.chapa.isCadastradoJulgamentoFinal);
  }

  /**
   * Recarregar aba de julgamento apos salvar.
   */
  public redirecionarVisualizarJulgamento(): void {
    this.recarregarAba.emit(Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA);
  }

  /**
   * Verifica se o campo de justificativa de retificação deve ser exibido.
   */
  public isMostrarJustificativaRetificativa(): boolean {
    return this.julgamentoFinal.retificacaoJustificativa != undefined;
  }

  /**
   * Abre modal de visualização de retificações de julgamento.
   */
  public abrirModalvisualizarRetificacaoJulgamento(posicao: number): void {
    this.julgamentoFinalRetificacaoSelecionado = this.julgamentoFinal.retificacoes[posicao];
    this.sequenciaJulgamentoFinalRetificacaoSelecionado = posicao;
    this.visualizarRetificacaoJulgamento = this.modalService.show(
      this.templateVisualizarRetificacaoJulgamento,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
  * Setar valor de isAtual ao mudar para aba de Retificação.
  *
  * @param value
  */
  public processaAbaRetificacao(evento): void {
    this.isAtual = evento != 'LABEL_RETIFICACAO';
  }

  /**
   * Verifica se o julgamento possui retificação.
   */
  public isJulgamentoFinalRetificado(): boolean {
    return this.julgamentoFinal.hasOwnProperty('retificacoes') && this.julgamentoFinal.retificacoes.length > 0;
  }

  /**
   * Fecha modal de visualização de retificações de julgamento.
   */
  public fecharVisualizarRetificacaoJulgamento(): void {
    this.visualizarRetificacaoJulgamento.hide();
  }

  /**
   * Verifica se o usuário de permissão CEN ou CENUF.
   */
  public isRoleCenOuCeUf(): boolean {
    if (this.isIES) {
      return this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    }
    else {
      return (this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
        || (this.chapa.idCauUf == this.securityService.credential.user.cauUf.id
          && this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE])));
    }
  }

}
