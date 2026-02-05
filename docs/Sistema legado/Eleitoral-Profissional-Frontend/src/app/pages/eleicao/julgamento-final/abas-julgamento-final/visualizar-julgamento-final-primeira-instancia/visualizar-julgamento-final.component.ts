import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';

import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

import * as _ from 'lodash';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'visualizar-julgamento-final',
    templateUrl: './visualizar-julgamento-final.component.html',
    styleUrls: ['./visualizar-julgamento-final.component.scss']
})
export class VisualizarJulgamentoFinalComponent implements OnInit {

  @Input() isIes: any;
  @Input() chapa: any;
  @Input() julgamentoFinal: any;
  @Input() recursoReconsideracao: any;
  @Input() tipoProfissional: any;

  public modalRef: BsModalRef;
  public modalRecurso: BsModalRef;

  public user: any;
  public abas: any;
  public membro: any;
  public recurso: any;
  public labelMembro: any;
  public titleSelecione: any;
  public titleDescricao: any;

  public submitted = false;

  public arquivo: any = [];
  public membros: any = [];
  public membrosSelecionados: any = [];

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() redirecionarAposSalvamento = new EventEmitter<any>();
  @Output() redirecionarAposSalvarSubstituicao = new EventEmitter<any>();

  @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao: any;

  public configuracaoCkeditor: any = {};

  /**
   * Construtor da classe.
   */
  constructor(
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private securtyService: SecurityService,
    private julgamentoFinalService: JulgamentoFinalClientService
  ) {
    this.user = this.securtyService.credential.user;
  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {
    this.getTituloPagina();
    this.labelMembroModal();
    this.inicializarRecurso();
    this.titleSelecioneModal();
    this.incializarMembrosChapa();
    this.inicializaConfiguracaoCkeditor();
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
    return this.julgamentoFinal.statusJulgamentoFinal.id === Constants.STATUS_JULGAMENTO_INDEFERIDO;
  }
  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.julgamentoFinal.chapaEleicao.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }
  /**
   * Responsavel por voltar a aba para a principal.
   */
  public voltarAbaPrincipal(): any {
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_FINAL_PRINCIPAL);
  }

  /**
   * Retorna se o acesso é de um responsável da chapa
   */
  private isResponsavel(): boolean {
    return this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA;
  }

  public isMostrarCadastrarSubstituicao(): boolean {
    return (
      this.isResponsavel() &&
      !(
        this.chapa.isCadastradoRecursoJulgamentoFinal ||
        this.chapa.isCadastradoSubstituicaoJulgamentoFinal
        ) &&
      this.chapa.isIniciadoAtivSubstituicaoJulgFinal &&
      !this.chapa.isFinalizadoAtivSubstituicaoJulgFinal &&
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes &&
      this.julgamentoFinal.indicacoes.length > 0
    );
  }

  /**
   * Verifica se o botão de cadastro de recurso deve ser mostrado.
   */
  public isMostrarCadastroRecurso(): boolean {
    return (
      this.isResponsavel() &&
      !(this.chapa.isCadastradoRecursoJulgamentoFinal ||
      this.chapa.isCadastradoSubstituicaoJulgamentoFinal) &&
      this.chapa.isIniciadoAtivRecursoJulgamentoFinal &&
      !this.chapa.isFinalizadoAtivRecursoJulgamentoFinal &&
      this.isJulgamentoIndeferido()
    );
  }

  public possuiIndicacao(): boolean {
    return (
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes &&
      this.julgamentoFinal.indicacoes.length > 0
    );
  }

  /**
   * Inicializar recurso de julgamento de substituição.
   */
  public inicializarRecurso(): void {
    this.recurso = {
      descricao: '',
      arquivos: [],
      idJulgamentoFinal: this.julgamentoFinal.id,
      idProfissional: this.user.idProfissional,
      indicacoes: [],
    };
  }

  /**
   * Baixar download de recurso.
   */
  public downloadModalRecurso(download: any): any {
    download.evento.emit(download.arquivo);
  }

  /**
   * Validação para apresentar o botão Solicitar Reconsideracao
   */
  public titleSelecioneModal(): any {
    this.titleSelecione =
    this.isIes ? 'TITLE_SELECIONE_CANDITADO_PARA_SOLICITACAO_RECONSIDERACAO' : 'TITLE_SELECIONE_CANDITADO_PARA_SOLICITACAO_RECURSO';
  }

  /**
   * Validação para apresentar o botão Solicitar Reconsideracao
   */
  public labelMembroModal(): any {
    this.labelMembro = this.isIes ? 'LABEL_MEMBRO_SOLICITACAO_RECONSIDERACAO' : 'LABEL_MEMBRO_SOLICITACAO_RECURSO';
  }

  /**
   * Exibe modal de cadastro de recurso/reconsideracao.
   */
  public abrirModalRecurso(template: TemplateRef<any>): void {
    this.InicializaModalRecurso();
    this.titleDescricao = this.isIes ? 'LABEL_RECONSIDERACAO_DO_JULGAMENTO' : 'LABEL_RECURSO_DO_JULGAMENTO';
    this.modalRecurso = this.modalService.show(template, Object.assign({}, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-xl'
    }));
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public titleModal() {
    return this.messageService.getDescription(this.isIes ? 'LABEL_RECONSIDERACAO' : 'LABEL_RECURSO');
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public titleModalconfirmacao() {
    return this.messageService.getDescription(
      this.isIes ? 'LABEL_RECONSIDERACAO_DO_JULGAMENTO_FINAL' : 'LABEL_RECURSO_DO_JULGAMENTO_FINAL'
    );
  }

  /**
   * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
   */
  public msgConfirmacao() {
    return this.messageService.getDescription('MGS_CONFIRMACAO_RECURSO_JULGAMENTO_FINAL', [this.titleModal()]);
  }

  /**
   * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
   */
  public adicionarDescricao(descricao: string): void {
    this.recurso.descricao = descricao;
  }

  /**
   * Responsavel por incializar os membros chapas para caso n tenha profissional.
   */
  public incializarMembrosChapa(): any {
    this.membro = {membro: {membroChapa: {}}};
  }

  /**
   * Responsavel por excluir o membro.
   */
  public excluirMembro(event: any): any {
    this.membro = event.membro;
    this.messageService.addConfirmYesNo('MSG_CONFIRMAR_EXCLUIR_MEMBRO_PENDENCIA', () => {
      if (!this.membro.membroChapa) {
        this.membro.membroChapa = {
          profissional: {
            nome: this.messageService.getDescription('LABEL_SEM_MEMBRO_INCLUSAO'),
            registroNacional: '-',
          }
        };
      }

      this.membros.push(this.membro);
      this.ordernarMembros();
      this.membrosSelecionados.splice(event.indice, 1);
      this.messageService.addMsgSuccess('MSG_EXCLUSAO_COM_SUCESSO');
    });
  }

  /**
   * Responsável por ordenar os membros do campo de seleção
   */
  private ordernarMembros(): void {
    this.membros = _.orderBy(
      this.membros,
      ['numeroOrdem', 'tipoParticipacaoChapa.id'],
      ['asc', 'asc']
    );
  }

  /**
   * Responsavel por selecionar o membro que foi escolhido no modal.
   */
  public getMembrosSelecionados(event: any): any {
    let indice: number;
    let membroSelecionado: any;
    const idMembro = event.target.value;

    if (idMembro !== 0) {
      this.membros.forEach((membro: any, i: number) => {
        if (membro.id == idMembro) {
          membroSelecionado = membro;
          indice = i;
        }
      });

      this.membrosSelecionados.push(membroSelecionado);
      this.ordernarMembrosSeleionados();
      this.membros.splice(indice, 1);
    }
  }

  /**
   * Responsável por ordenar os membros do grid selecionado
   */
  private ordernarMembrosSeleionados(): void {
    this.membrosSelecionados = _.orderBy(
      this.membrosSelecionados,
      ['numeroOrdem', 'tipoParticipacaoChapa.id'],
      ['asc', 'asc']
    );
  }

  /**
   * Responsavel por Inicializar o modal do Recurso.
   */
  public InicializaModalRecurso(): any {
    this.membrosSelecionados = [];

    this.julgamentoFinal.indicacoes.forEach(membro => {
      this.membrosSelecionados.push(membro);
    });
    this.ordernarMembrosSeleionados();

    this.inicializarRecurso();
    this.submitted = false;
    this.membros = [];
  }

  /**
   * Responsavel por apagar tudo que foi colocado no recurso quando sai do modal.
   */
  public cancelarRecurso(): any {
    this.modalRecurso.hide();
  }

  /**
   * Responavel por salvar o recurso.
   */
  public salvarRecurso(): any {
    this.submitted = true;

    if (this.hasProfissional() && this.hasDescricao()) {
      this.recurso.indicacoes = this.membrosSelecionados;
      this.julgamentoFinalService.salvarRecurso(this.recurso).subscribe(
        data => {
          this.recurso = data;
          this.modalRecurso.hide();
          this.modalRef = this.modalService.show(this.templateConfirmacao, Object.assign({}, {
            backdrop: true,
            ignoreBackdropClick: true,
            class: 'modal-lg modal-dialog-centered' 
          }));
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else if (!this.hasProfissional()) {
      this.messageService.addMsgDanger(this.isIes ? 'MGS_CONFIRMACAO_RECONSIDERACAO_CHAPA' : 'MGS_CONFIRMACAO_RECURSO_CHAPA');
    }
  }

  /**
   * Verifica se existe membros selecionados.
   */
  public hasProfissional(): boolean {
    return (
      !this.hasIndicacoes() ||
      (this.hasIndicacoes() && this.hasMembrosSeleionadosRecurso())
    );
  }

  /**
   * Verifica se a discricao foi preencida.
   */
  public hasDescricao(): boolean {
    return this.recurso.descricao;
  }

  /**
   * Resoponsavel por salvar o arquivo que foi submetido no componete arquivo.
   */
  public salvarArquivos(arquivos: any): void {
    this.recurso.arquivos = arquivos;
  }

  /**
   * Excluir arquivo de defesa de impugnação.
   */
  public excluirArquivo(arquivo: any): void {
    if (arquivo.id) {
      this.arquivo.idArquivosRemover.push(arquivo.id);
    }
  }

  /**
   * Redireciona o usuário para a tela de visualizar recurso
   */
  public redirecionarVisualizarRecurso(event: any) {
    this.modalRef.hide();
    this.redirecionarAposSalvamento.emit(this.recurso);
  }

  /**
   * Redireciona o usuário para a tela de visualizar pedido de substituicao
   * @param event
   */
  public redirecionarVisualizarSubstituicao(event: any){
    this.redirecionarAposSalvarSubstituicao.emit(event);
  }

  /**
   * Responsavel por verificar se tem indicações no julgamento final
   */
  public hasIndicacoes(): any {
    return (
      this.isJulgamentoIndeferido() &&
      this.julgamentoFinal.indicacoes &&
      this.julgamentoFinal.indicacoes[0]
    );
  }

  /**
   * Responsavel por verificar se tem membros selecionados no recurso do julgamento final
   */
  public hasMembrosSeleionadosRecurso(): any {
    return this.isJulgamentoIndeferido() && this.membrosSelecionados[0];
  }

  public isConselheiroFederal(posicao: number): boolean {
    return posicao === 0;
  }
}
