import {
  Component,
  OnInit,
  Input,
  Output,
  EventEmitter,
  TemplateRef,
  ViewChild,
  HostListener,
} from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { NgForm } from '@angular/forms';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';
import { DomSanitizer } from '@angular/platform-browser';
import * as _ from 'lodash';
import { SecurityService } from '@cau/security';

@Component({
  selector: 'aba-membros-chapa-visualizar',
  templateUrl: './aba-membros-chapa-visualizar.component.html',
  styleUrls: ['./aba-membros-chapa-visualizar.component.scss'],
})
export class AbaMembrosChapaVisualizarComponent implements OnInit {
  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Input() isChapaConcluida: boolean;
  @Output() avancar: EventEmitter<any> = new EventEmitter();
  @Input() isUsuarioCEN?: boolean;
  @Input() isUsuarioCE?: boolean;
  @Input() isEleicaoDentroDoPrazoCadastro?: boolean;
  @Output() chapaEleicaoChange = new EventEmitter<any>();

  @ViewChild('templateJustificativaAlterarMembro', null)
  templateJustificativaAlterarMembro: TemplateRef<any>;

  @ViewChild('templateJustificativaAlterarStatusConfirmacaoMembroChapa', null)
  templateJustificativaAlterarStatusConfirmacaoMembroChapa: TemplateRef<any>;

  @ViewChild('templateAlterarStatusConfirmacao', null)
  templateAlterarStatusConfirmacao: TemplateRef<any>;

  @ViewChild('templateJustificativaAlterarStatusValidacaoMembroChapa', null)
  templateJustificativaAlterarStatusValidacaoMembroChapa: TemplateRef<any>;

  @ViewChild('templateAlterarStatusValidacao', null)
  templateAlterarStatusValidacao: TemplateRef<any>;

  @ViewChild('templateJustificativaExcluirMembroChapa', null)
  templateJustificativaExcluirMembroChapa: TemplateRef<any>;

  @ViewChild('templateJustificativaAlterarResponsavelMembroChapa', null)
  templateJustificativaAlterarResponsavelMembroChapa: TemplateRef<any>;

  public pesquisa: any;
  public limitePaginacao: number;
  public limitesPaginacao: Array<number>;
  public selected: any = 1;
  public membrosChapas: any[] = [];
  public _membrosChapas: any[] = [];
  public nomeMembrosChapa: any[] = [];
  public modalInformacaoProfissional: BsModalRef;
  public modalPendeciasMembro: BsModalRef;
  public modalSinteseCurriculoProfissional: BsModalRef;
  public modalJustificativaRef: BsModalRef;
  public membroChapaSelecionado: any;
  public alteracaoMembroChapa: any;

  public statusAlterarMembro: any;
  public submittedFormJustificarAlterarMembro: boolean;
  public filtroCpfNome: string;
  public filtroProfissionaisPorCpfNome: any;

  public limiteResponsaveis: number;

  public alteracaoMembro: any;
  public alterarStatusConfirmacao: any;
  public alterarStatusValidacao: any;
  public exclusaoMembroChapa: any;

  public statusConfirmacaoMembroChapa: any;
  public statusValidacaoMembroChapa: any;

  public showMSGCriteriosRepresentatividade: boolean = false;
  public showMSGCotistasRepresentatividade: boolean = false;

  public permitirSalvar: boolean = false;
  public permitirEditarEleito: boolean = false;

  public user: any;

  /**
   * Construtor da classe.
   *
   * @param router
   * @param messageService
   * @param chapaEleicaoService
   */
  constructor(
    private router: Router,
    private modalService: BsModalService,
    private messageService: MessageService,
    private chapaEleicaoService: ChapaEleicaoClientService,
    public domSanitizer: DomSanitizer,
    private securityService: SecurityService
  ) {
    this.user = this.securityService.credential["_user"];
  }


  /**
   * Executado ao inicializar o componente.
   */
  ngOnInit() {
    this.limitePaginacao = 10;
    this.limitesPaginacao = [10, 20, 30, 50, 100];
    this.limiteResponsaveis = 3;
    this.inicializarMembrosChapa();
    this.inicializarStatusConfirmacaoMembroChapa();
    this.inicializarStatusValidacaoMembroChapa();

    this.statusAlterarMembro = {
      situacaoResponsavel: undefined,
      justificativa: '',
      membro: {},
    };

    this.verificaCriteriosRepresentatividade();
    this.validarPermissaoEleito();
  }

  /**
   * Evento ao apertar ESC para esconder o input de alteração de membro
   *
   */
  @HostListener('document:keydown.escape', ['$event']) onKeydownHandler(
    event: KeyboardEvent
  ) {
    this.alteracaoMembroChapa = undefined;
  }

  /**
   * Incluir membro chapa selecionado.
   *
   * @param event
   */
  public adicionarMembroChapa(membro: any): void {
    if (
      this.alteracaoMembroChapa != undefined &&
      this.alteracaoMembroChapa.idProfissional &&
      membro.profissional.id == this.alteracaoMembroChapa.idProfissional
    ) {
      this.alteracaoMembroChapa = undefined;
    } else {
      this.alteracaoMembro = {
        idProfissional: membro.profissional.id,
        idTipoParticipacao: membro.membroChapa.tipoParticipacaoChapa.id,
        idTipoMembroChapa: membro.membroChapa.tipoMembroChapa.id,
        numeroOrdem: membro.membroChapa.numeroOrdem,
        justificativa: '',
      };

      this.modalJustificativaRef = this.modalService.show(
        this.templateJustificativaAlterarMembro,
        Object.assign({}, { class: ' ' })
      );
    }
  }

  /**
   * Salva alteração inclusão de membro com justificativa.
   */
  public salvarJustificativaAlterarMembro(form: NgForm): void {
    if (form.valid) {
      this.chapaEleicaoService
        .incluirMembro(this.chapaEleicao.id, this.alteracaoMembro)
        .subscribe(
          (data) => {
            this.updateMembroChapa();
            this.mostrarMensagemMembroAdicionadoAlterado();
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
      this.modalJustificativaRef.hide();
    }
  }

  /**
   * Aciona ação de alteração de status de confirmação de membro da chapa.
   *
   * @param membro
   */
  public alterarStatusConfirmacaoMembroChapa(membro: any): void {
    this.alterarStatusConfirmacao = {
      idMembroChapa: membro.id,
      idStatusConvite: undefined,
      justificativa: '',
      membro: membro,
    };

    this.modalJustificativaRef = this.modalService.show(
      this.templateAlterarStatusConfirmacao,
      Object.assign({}, { class: ' ' })
    );
  }

  /**
   * Apresenta modal para justificar alteração do status de validaçaõ do status de confirmação do membro da chapa.
   */
  public confirmarAlterarStatusConfirmacaoMembroChapa(): void {
    this.modalJustificativaRef.hide();
    this.modalJustificativaRef = this.modalService.show(
      this.templateJustificativaAlterarStatusConfirmacaoMembroChapa,
      Object.assign({}, { class: ' ' })
    );
  }

  /**
   * Salva alteração do status de validação do membro da chapa.
   *
   * @param form
   */
  public salvarJustificativaAlterarStatusConfirmacaoMembroChapa(
    form: NgForm
  ): void {
    if (form.valid) {
      this.chapaEleicaoService
        .alterarStatusConvite(
          this.alterarStatusConfirmacao.idMembroChapa,
          this.alterarStatusConfirmacao
        )
        .subscribe(
          (data) => {
            this.alterarStatusConfirmacao.membro.statusParticipacaoChapa.id =
              this.alterarStatusConfirmacao.idStatusConvite;
            this.updateMembroChapa();
            //this.messageService.addMsgSuccess(data);
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
      this.modalJustificativaRef.hide();
    }
  }
  /**
   * Aciona ação de alterar status de validação do membro da chapa.
   *
   * @param membro
   */
  public alterarStatusValidacaoMembroChapa(membro: any): void {
    this.alterarStatusValidacao = {
      idMembroChapa: membro.id,
      idStatusValidacao: undefined,
      justificativa: '',
      membro: membro,
    };

    this.modalJustificativaRef = this.modalService.show(
      this.templateAlterarStatusValidacao,
      Object.assign({}, { class: ' ' })
    );
  }

  /**
   * Apresenta modal para justificar a alteração no status de validação do membro da chapa.
   */
  public confirmarAlterarStatusValidacaoMembroChapa(): void {
    this.modalJustificativaRef.hide();
    this.modalJustificativaRef = this.modalService.show(
      this.templateJustificativaAlterarStatusValidacaoMembroChapa,
      Object.assign({}, { class: ' ' })
    );
  }

  /**
   * Salva alteração no status de validação do membro da chapa eleitoral.
   *
   * @param form
   */
  public salvarJustificativaAlterarStatusValidacaoMembroChapa(
    form: NgForm
  ): void {
    if (form.valid) {
      this.chapaEleicaoService
        .alterarStatusValidacao(
          this.alterarStatusValidacao.idMembroChapa,
          this.alterarStatusValidacao
        )
        .subscribe(
          (data) => {
            this.alterarStatusValidacao.membro.statusValidacaoMembroChapa.id =
              this.alterarStatusValidacao.idStatusValidacao;
            this.substituirMembroChapa(this.alterarStatusValidacao.membro);
            this.inicializarMembrosChapa();
            //this.messageService.addMsgSuccess(data);
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
      this.modalJustificativaRef.hide();
    }
  }

  /**
   * Substituir membro chapa em array local da Chapa eleitoral.
   *
   * @param novoMembro
   */
  private substituirMembroChapa(novoMembro: any): void {
    let naoEncontrado: boolean = true;
    this.chapaEleicao.membrosChapa = this.chapaEleicao.membrosChapa.map(
      (membro) => {
        if (
          membro.numeroOrdem == novoMembro.numeroOrdem &&
          membro.tipoParticipacaoChapa.id == novoMembro.tipoParticipacaoChapa.id
        ) {
          naoEncontrado = false;
          return novoMembro;
        }
        return membro;
      }
    );

    if (naoEncontrado) {
      this.chapaEleicao.membrosChapa.push(novoMembro);
    }
  }

  /**
   * Apresenta mensagem para confirmação da exclusão do membro da chapa, e apresenta modal de justificativa.
   *
   * @param membro
   */
  public confirmarExcluirMembroChapa(membro: any): void {
    this.exclusaoMembroChapa = {
      membro: undefined,
      justificativa: '',
    };
    this.messageService.addConfirmYesNo(
      'MSG_DESEJA_REALMENTE_EXCLUIR_MEMBRO_CHAPA',
      () => {
        this.exclusaoMembroChapa.membro = membro;
        this.modalJustificativaRef = this.modalService.show(
          this.templateJustificativaExcluirMembroChapa,
          Object.assign({}, { class: ' ' })
        );
      }
    );
  }

  /**
   * Excluir membro da chapa.
   */
  public excluirMembroChapa(): void {
    let membro = {
      numeroOrdem: this.exclusaoMembroChapa.membro.numeroOrdem,
      tipoParticipacaoChapa: {
        id: this.exclusaoMembroChapa.membro.tipoParticipacaoChapa.id,
      },
      tipoMembroChapa: {
        id: this.exclusaoMembroChapa.membro.tipoMembroChapa.id,
      },
    };

    if (this.exclusaoMembroChapa) {
      this.chapaEleicaoService
        .exclirMembroChapa(
          this.exclusaoMembroChapa.membro.id,
          this.exclusaoMembroChapa
        )
        .subscribe(
          (data) => {
            this.messageService.addMsgSuccess(data);
            this.substituirMembroChapa(membro);
            this.inicializarMembrosChapa();
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
    }
    this.modalJustificativaRef.hide();
  }

  /**
   * Exibe mensagem de membro adicionado ou alterado com sucesso.
   */
  public mostrarMensagemMembroAdicionadoAlterado(): void {
    let msg: string = this.alteracaoMembroChapa
      ? 'MSG_DADOS_ALTERADOS_COM_SUCESSO'
      : 'MSG_DADOS_INCLUIDOS_COM_SUCESSO';
    this.messageService.addMsgSuccess(msg);
  }

  /**
   * Retorna placeholder utilizado no autocomplete de profissional.
   *
   * @param membro
   */
  public getPlaceholderAutoCompleteProfissional(membro): string {
    let msg: string;
    if (this.isConselheiroUfBR()) {
      if (membro.numeroOrdem == 0) {
        msg =
          membro.tipoParticipacaoChapa.id ==
          Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR
            ? 'LABEL_INSIRA_CPF_NOME_CONSELHEIRO_FEDERAL'
            : 'LABEL_INSIRA_CPF_NOME_SUPLENTE';
      } else {
        msg =
          membro.tipoParticipacaoChapa.id ==
          Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR
            ? 'LABEL_INSIRA_CPF_NOME_MEMBRO'
            : 'LABEL_INSIRA_CPF_NOME_SUPLENTE';
      }
    } else {
      msg =
        membro.tipoParticipacaoChapa.id ==
        Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR
          ? 'LABEL_INSIRA_CPF_NOME_TITULAR_IES'
          : 'LABEL_INSIRA_CPF_NOME_SUPLENTE_IES';
    }
    return this.messageService.getDescription(msg);
  }

  /**
   * Retorna lista de Conselheiros federais da chapa eleitoral.
   *
   * @param membrosChapas
   */
  public getConselheirosFederais(membrosChapas: Array<any>): Array<any> {
    return membrosChapas.slice(0, 1);
  }

  /**
   * Retorna lista de Conselheiros estaduais da chapa eleitoral.
   *
   * @param membrosChapas
   */
  public getConselheirosEstaduais(membrosChapas: Array<any>): Array<any> {
    return membrosChapas.slice(1);
  }

  /**
   * Retorna lista de representantes IES da chapa eleitoral.
   *
   * @param membrosChapas
   */
  public getRepresentantesIES(membrosChapas: Array<any>): Array<any> {
    return membrosChapas;
  }

  /**
   * Verifica se o membro da chapa é do tipo Conselheiro Estadual.
   *
   * @param id
   */
  public isConselheirosEstaduais(id: number): boolean {
    return id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
  }

  /**
   * Verifica se o membro da chapa é do tipo Conselheiro Federal.
   *
   * @param id
   */
  public isConselheirosFederais(id: number): boolean {
    return id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL;
  }

  /**
   * Válida se é "Conselheiro UF-BR".
   */
  public isConselheiroUfBR(): boolean {
    return (
      this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR
    );
  }

  /**
   * Válida se é "IES".
   */
  public isConselheiroIES(): boolean {
    return (
      this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES
    );
  }

  /**
   * Verifica se o campo está habilitado para edição.
   */
  public isDisabled(): boolean {
    return !(this.isUsuarioCEN ? true : false);
  }

  /**
   * Verifica se o usuário tem permissão de assessor CEN ou CE pertencendo a UF da Chapa Atual
   */
  public isAssessorCENOuAssessorCECauUfAtual(): boolean {
    return (
      this.isUsuarioCEN ||
      (this.chapaEleicao.idCauUf ==
        this.securityService.credential.user.cauUf.id &&
        this.isUsuarioCE)
    );
  }

  public isPermitidoReenviarEmailPendencia(
    statusValidacaoMembroChapa
  ): boolean {
    return (
      this.isStatusValidacaoMembroChapaPendente(statusValidacaoMembroChapa) &&
      this.isVigenteAtvCadastroChapa() &&
      this.isAssessorCENOuAssessorCECauUfAtual()
    );
  }

  public isPermitidoReenviarConvite(statusParticipacaoChapa): boolean {
    return (
      this.isStatusParticipacaoChapaPendente(statusParticipacaoChapa) &&
      this.isVigenteAtvCadastroChapa() &&
      this.isAssessorCENOuAssessorCECauUfAtual()
    );
  }

  /**
   * Verifica se o membro da chapa confirmou participação.
   *
   * @param idStatus
   */
  public isStatusParticipacaoChapaConfirmado(idStatus: number): boolean {
    return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO;
  }

  /**
   * Verifica se o membro da chapa rejeitou participação.
   *
   * @param idStatus
   */
  public isStatusParticipacaoChapaRejeitado(idStatus: number): boolean {
    return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO;
  }

  /**
   * Verifica se o membro da chapa tem participação pendente.
   *
   * @param idStatus
   */
  public isStatusParticipacaoChapaPendente(idStatus: number): boolean {
    return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO;
  }

  /**
   * Verifica se o membro da chapa tem status de validação igual a valido.
   *
   * @param idStatus
   */
  public isStatusValidacaoMembroChapaValido(idStatus: number): boolean {
    return idStatus == Constants.STATUS_MEMBRO_CHAPA_VALIDO;
  }

  /**
   * Verifica se o membro da chapa tem status de validação igual a pendente.
   *
   * @param idStatus
   */
  public isStatusValidacaoMembroChapaPendente(idStatus: number): boolean {
    return idStatus == Constants.STATUS_MEMBRO_CHAPA_PENDENTE;
  }

  /**
   * Verifica se a ação de Alterar membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoAlterarHabilitada(membroChapa: any): boolean {
    return this.isUsuarioCEN;
  }

  /**
   * Verifica se a ação de Enviar e-mail com pendências do membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoEnviarEmailPendenciaHabilitada(membroChapa: any): boolean {
    return this.isDisabled();
  }

  /**
   * Verifica se a ação de Reenviar e-mail do convite do membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoRenviarConviteHabilitada(membroChapa: any): boolean {
    return this.isDisabled();
  }

  /**
   * Válida se os campos do componente foram alterados.
   */
  public isCamposAlterados(): boolean {
    return this.alteracaoMembroChapa;
  }

  /**
   * Alterando membro da chapa.
   *
   * @param membroChapa
   */
  public isAlterarMembroChapa(membroChapa: any): boolean {
    if (this.alteracaoMembroChapa == undefined && membroChapa.id != undefined) {
      return false;
    }

    return (
      membroChapa.id == undefined ||
      this.alteracaoMembroChapa.id == membroChapa.id
    );
  }

  /**
   * Função chamada quando o método anterior é chamado.
   */
  public anterior(): void {
    if (this.isCamposAlterados()) {
      this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR', () => {
        this.router.navigate(['/']);
      });
    } else {
      this.router.navigate(['/']);
    }
  }

  /**
   * Função responsável por salvar os dados e avançar para a próxima aba.
   */
  public avancarDeclaracao(): void {
    this.avancar.emit(Constants.ABA_MEMBROS_CHAPA);
  }

  /**
   * Exibe mensagem de profissional não encontrado.
   */
  public nenhumRegistroEncontrado(): void {
    this.messageService.addMsgWarning('MSG_CPF_NOME_NAO_ENCONTRADO_SICCAU');
  }

  /**
   * Exibe o modal com o perfil do profissional selecionado.
   *
   * @param template
   * @param element
   */
  public exibirModalPerfilProfissional(
    template: TemplateRef<any>,
    element: any
  ): void {
    this.membroChapaSelecionado = element;
    this.chapaEleicaoService
      .getCurriculoMembroChapaPorMembroChapa(element.id)
      .subscribe(
        (data) => {
          this.membroChapaSelecionado.curriculo = data;
          this.modalInformacaoProfissional = this.modalService.show(
            template,
            Object.assign({}, { class: 'my-modal' })
          );
        },
        (error) => {
          this.messageService.addMsgDanger(error.description);
        }
      );
  }

  /**
   * Exibe modal de listagem de pendencias do profissional selecionado.
   *
   * @param template
   * @param element
   */
  public exibirModalPendeciasMembro(
    template: TemplateRef<any>,
    element: any
  ): void {
    this.membroChapaSelecionado = element;
    this.modalPendeciasMembro = this.modalService.show(
      template,
      Object.assign({}, { class: 'my-modal' })
    );
  }

  /**
   * Oculta o modal de perfil do profissional selecionado.
   */
  public fecharModalPerfilProfissional(): void {
    this.modalInformacaoProfissional.hide();
  }

  /**
   * Exibe modal de Síntese de currículo.
   *
   * @param template
   */
  public exibirModalSinteseCurriculo(template: TemplateRef<any>): void {
    this.modalSinteseCurriculoProfissional = this.modalService.show(
      template,
      Object.assign({}, { class: 'my-modal' })
    );
  }

  /**
   * Realiza download de documento.
   *
   * @param event
   * @param idDocumento
   */
  public downloadDocumentoComprobatorio(
    event: EventEmitter<any>,
    idDocumento
  ): void {
    this.chapaEleicaoService
      .downloadDocumentoComprobatorio(idDocumento)
      .subscribe(
        (data: Blob) => {
          event.emit(data);
        },
        (error) => {
          this.messageService.addMsgDanger(error);
        }
      );
  }

  /**
   * Realiza download de declaração de representatividade.
   *
   * @param event
   * @param idDocumento
   */
  public downloadDocumentoRepresentatividade(
    event: EventEmitter<any>,
    idMembro
  ): void {
    this.chapaEleicaoService
      .downloadDocumentoRepresentatividade(idMembro)
      .subscribe(
        (data) => {
          var file = new Blob([data.body], { type: 'application/pdf' });
          var fileURL = window.URL.createObjectURL(file);
          window.open(fileURL, '_blank');
        },
        (error) => {
          this.messageService.addMsgDanger(error);
        }
      );
  }

  /**
   * Verifica se o membro da chapa tem documentos comprobatórios.
   *
   */
  public hasDocumentosComprobatoriosSinteseCurriculo(): boolean {
    return (
      this.membroChapaSelecionado.curriculo &&
      this.membroChapaSelecionado.curriculo
        .documentosComprobatoriosSinteseCurriculo.length > 0
    );
  }

  /**
   * Retorna a carta de indicação do membro selecionado.
   */
  public getCartaIndicacao(): any {
    return this.membroChapaSelecionado.curriculo.documentosComprobatoriosSinteseCurriculo.find(
      (documento) => {
        return (
          documento.tipoDocumentoComprobatorioSinteseCurriculo ==
          Constants.TIPO_DOCUMENTO_COMPROB_CARTA_INDICACAO
        );
      }
    );
  }

  /**
   * Retorna a carta de indicação do membro selecionado.
   */
  public getDeclaracaoVinculo(): any {
    return this.membroChapaSelecionado.curriculo.documentosComprobatoriosSinteseCurriculo.find(
      (documento) => {
        return (
          documento.tipoDocumentoComprobatorioSinteseCurriculo ==
          Constants.TIPO_DOCUMENTO_COMPROB_COMPROVANTE
        );
      }
    );
  }

  /**
   * Envia e-mail que lista as pendencias de um determinado membro da chapa eleitoral.
   *
   * @param membroChapa
   */
  public enviarEmailPendencia(membroChapa: any): void {
    if (this.isAssessorCENOuAssessorCECauUfAtual()) {
      this.chapaEleicaoService
        .enviarEmailDePendenciasPorMembroChapa(membroChapa.id)
        .subscribe(
          (data) => {
            this.messageService.addMsgSuccess(data);
          },
          (error) => {
            this.messageService.addMsgDanger(error.description);
          }
        );
    }
  }

  /**
   * Renvia e-mail do convite do membro chapa.
   *
   * @param membroChapa
   */
  public renviarConvite(membroChapa: any): void {
    if (this.isAssessorCENOuAssessorCECauUfAtual()) {
      this.chapaEleicaoService
        .enviarEmailDeConvitePorMembroChapa(membroChapa.id)
        .subscribe(
          (data) => {
            this.messageService.addMsgSuccess(data);
          },
          (error) => {
            this.messageService.addMsgDanger(error.description);
          }
        );
    }
  }

  /**
   * Habilita input para alteração do membro da chapa.
   *
   * @param membroChapa
   */
  public alterarMembroChapa(membroChapa: any): void {
    if (
      this.isStatusParticipacaoChapaConfirmado(
        membroChapa.statusParticipacaoChapa.id
      )
    ) {
      this.messageService.addConfirmYesNo(
        'MSG_CONFIRMAR_SUBTITUICAO_MEMBRO_CONVITE_ACEITE',
        () => {
          this.alteracaoMembroChapa = membroChapa;
        }
      );
    } else {
      this.alteracaoMembroChapa = membroChapa;
    }
  }

  public isResponsavelCriador(membroChapa: any): boolean {
    return (
      this.chapaEleicao.idProfissionalInclusao == membroChapa.profissional.id &&
      membroChapa.situacaoResponsavel
    );
  }

  /**
   * Exibe modal justificar alteração de status de responsável de membro da chapa.
   *
   * @param event
   * @param membroChapa
   * @param template
   */
  public justificarAlteracaoSituacaoResponsavel(event, membroChapa: any): void {
    event.preventDefault();
    if (this.validarResponsavel(!membroChapa.situacaoResponsavel)) {
      let status = membroChapa.situacaoResponsavel;
      this.statusAlterarMembro.situacaoResponsavel =
        !membroChapa.situacaoResponsavel;
      this.statusAlterarMembro.justificativa = ' ';
      this.statusAlterarMembro.membro = membroChapa;

      this.modalJustificativaRef = this.modalService.show(
        this.templateJustificativaAlterarResponsavelMembroChapa,
        Object.assign({}, { class: '' })
      );
    }
  }

  /**
   * Valida responsáveis pela chapa.
   *
   * @param novaSituacaoResponsavel
   */
  public validarResponsavel(novaSituacaoResponsavel: boolean): boolean {
    if (novaSituacaoResponsavel) {
      if (this.getNumeroResponsaveis() >= this.limiteResponsaveis) {
        this.messageService.addMsgWarning(
          'MSG_AVISO_NUMERO_MAXIMO_RESPONSAVEIS',
          [this.limiteResponsaveis]
        );
        return false;
      }
    } else {
      if (this.getNumeroResponsaveis() <= 1) {
        this.messageService.addMsgWarning(
          'MSG_INFORME_AO_MENOS_UM_RESPONSAVEL'
        );
        return false;
      }
    }
    return true;
  }

  /**
   * Retorna o total de responsáveis pela chapa.
   */
  private getNumeroResponsaveis(): number {
    let total: number = 0;
    this.membrosChapas.forEach((membro) => {
      total += membro.titular.situacaoResponsavel ? 1 : 0;
      total += membro.suplente.situacaoResponsavel ? 1 : 0;
    });
    return total;
  }

  /**
   * Altera situação do membro chapa.
   *
   * @param membroChapa
   * @param membroChapaSituacao
   */
  public alterarSituacaoResponsavel(form: NgForm): void {
    if (form.valid) {
      let situacao: any = {
        situacaoResponsavel: this.statusAlterarMembro.situacaoResponsavel,
        justificativa: this.statusAlterarMembro.justificativa,
      };

      this.chapaEleicaoService
        .alterarSituacaoResponsavel(
          this.statusAlterarMembro.membro.id,
          situacao
        )
        .subscribe(
          (data) => {
            this.substituirMembroChapa(data);
            //this.inicializarMembrosChapa();
            this.updateMembroChapa();
            this.modalJustificativaRef.hide();
            this.messageService.addMsgSuccess(
              'MSG_DADOS_ALTERADOS_COM_SUCESSO'
            );
          },
          (error) => {
            this.messageService.addMsgDanger(error.description);
          }
        );
      this.modalJustificativaRef.hide();
    }
  }

  /**
   * Atualiza lista de membro da chapa eleitoral.
   */
  private updateMembroChapa(): void {
    this.chapaEleicaoService.getChapaPorId(this.chapaEleicao.id).subscribe(
      (data) => {
        this.chapaEleicao = data;
        this.chapaEleicaoChange.emit(data);
        this.inicializarMembrosChapa();
        this.alteracaoMembroChapa = undefined;
      },
      (error) => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Inicializa os membros chapa.
   */
  private inicializarMembrosChapa(): void {
    this.membrosChapas = [];
    if (
      this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR
    ) {
      let totalMembrosChapa = this.chapaEleicao.numeroProporcaoConselheiros;

      for (var i = 0; i <= totalMembrosChapa; i++) {
        let titular: any = this.findMembroChapa(
          Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR,
          i
        );
        let suplente: any = this.findMembroChapa(
          Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE,
          i
        );

        this.membrosChapas.push({
          titular: titular,
          titular_txtPesquisa: titular.profissional
            ? titular.profissional.cpf
            : '',
          suplente_txtPesquisa: suplente.profissional
            ? suplente.profissional.cpf
            : '',
          suplente: suplente,
        });
      }
    } else {
      let titular: any = this.findMembroChapa(
        Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR,
        0
      );
      let suplente: any = this.findMembroChapa(
        Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE,
        0
      );

      this.membrosChapas = [
        {
          titular: this.findMembroChapa(
            Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR,
            0
          ),
          titular_txtPesquisa: titular.profissional
            ? titular.profissional.cpf
            : '',
          suplente_txtPesquisa: suplente.profissional
            ? suplente.profissional.cpf
            : '',
          suplente: this.findMembroChapa(
            Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE,
            0
          ),
        },
      ];
    }
    this._membrosChapas = _.cloneDeep(this.membrosChapas);
  }

  /**
   * Inicializa status de confirmação dos membros da chapa.
   */
  private inicializarStatusConfirmacaoMembroChapa(): void {
    this.statusConfirmacaoMembroChapa = {
      confirmado: {
        label: this.messageService.getDescription('LABEL_STATUS_CONFIRMADO'),
        id: Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO,
      },
      pendente: {
        label: this.messageService.getDescription('LABEL_STATUS_A_CONFIRMAR'),
        id: Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO,
      },
      rejeitado: {
        label: this.messageService.getDescription('LABEL_STATUS_REJEITADO'),
        id: Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO,
      },
    };
  }

  /**
   * Inicializa lista de status de validação para os membros da chapa.
   */
  private inicializarStatusValidacaoMembroChapa(): void {
    this.statusValidacaoMembroChapa = {
      pendente: {
        id: Constants.STATUS_MEMBRO_CHAPA_PENDENTE,
        label: this.messageService.getDescription('LABEL_PENDENTE'),
      },
      valido: {
        id: Constants.STATUS_MEMBRO_CHAPA_VALIDO,
        label: this.messageService.getDescription('LABEL_SEM_PENDENCIA'),
      },
    };
  }

  /**
   * Retorna membro da chapa por posição tipo Participação.
   */
  private findMembroChapa(tipoParticipacao: number, posicao: number): any {
    let membroChapa = this.chapaEleicao.membrosChapa.find((membro) => {
      if (posicao == 0) {
        return (
          membro.tipoParticipacaoChapa.id == tipoParticipacao &&
          (membro.tipoMembroChapa.id ==
            Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL ||
            membro.tipoMembroChapa.id ==
              Constants.TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES)
        );
      }
      return (
        membro.tipoParticipacaoChapa.id == tipoParticipacao &&
        membro.numeroOrdem == posicao
      );
    });

    if (membroChapa == undefined) {
      membroChapa = {
        numeroOrdem: posicao,
        tipoParticipacaoChapa: { id: tipoParticipacao },
        tipoMembroChapa: {
          id:
            posicao == 0
              ? Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL
              : Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL,
        },
      };
    }
    return membroChapa;
  }

  /**
   * Verifica se a atividade 2.1 está vigente
   */
  public isVigenteAtvCadastroChapa(): boolean {
    return this.isVigente(
      this.chapaEleicao.atividadeSecundariaCalendario.dataInicio,
      this.chapaEleicao.atividadeSecundariaCalendario.dataFim
    );
  }

  /**
   * Verifica se a data está dentro do período de vigência.
   */
  private isVigente(dataInicio, dataFim): boolean {
    dataFim = new Date(dataFim);
    dataFim.setHours(23, 59, 59, 999);
    dataFim.setDate(dataFim.getDate() + 1);

    dataInicio = new Date(dataInicio);
    dataInicio.setHours(0, 0, 0, 0);
    dataInicio.setDate(dataInicio.getDate() + 1);

    let hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    if (hoje <= dataFim && hoje >= dataInicio) {
      return true;
    }
    return false;
  }

  /**
   * Verifica se a chapa atingiu a cota de representatividade.
   */
  public chapaAtingiuCotaRepresentatividade(): boolean {
    let arrMembroTitular = _.filter(
      this.chapaEleicao.membrosChapa,
      (membro) => {
        return (
          membro.tipoParticipacaoChapa.id ==
          Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR
        );
      }
    );

    let arrMembroRepresentatividade = _.filter(
      this.chapaEleicao.membrosChapa,
      (membro) => {
        return (
          membro.tipoParticipacaoChapa.id ==
            Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR &&
          membro.respostaDeclaracaoRepresentatividade &&
          membro.respostaDeclaracaoRepresentatividade.length > 0
        );
      }
    );

    return (
      arrMembroTitular &&
      arrMembroRepresentatividade &&
      arrMembroRepresentatividade.length >= arrMembroTitular.length / 3
    );
  }

  public verificaCriteriosRepresentatividade(): void {
    if (this.chapaEleicao.atendeCriteriosRepresentatividade) {
      this.showMSGCriteriosRepresentatividade = false;
      if (this.chapaEleicao.atendeCotistasRepresentatividade) {
        this.showMSGCotistasRepresentatividade = false;
      } else {
        this.showMSGCotistasRepresentatividade = true;
      }
    } else {
      this.showMSGCriteriosRepresentatividade = true;
    }
  }

  /**
   * Cadastrar status eleito dos membros
   */
  public cadastrarStatusEleito(): void {
    this.messageService.addConfirmYesNo(
      'MSG_CONFIRMAR_MEMBROS',
      () => {
        let membros = [];
        this.membrosChapas.forEach((membro) => {
          membros.push({
            titular: {
              idMembro: membro.titular.id,
              statusEleito: membro.titular.statusEleito
            },
            suplente: {
              idMembro: membro.suplente.id,
              statusEleito: membro.suplente.statusEleito
            }
          })
        });

        let params = {
          membros: membros,
          idChapa: this.chapaEleicao.id,
          idUsuario: this.user.id
        };

        this.chapaEleicaoService.atualizarStatusEleito(params).subscribe(
          (data) => {
            location.reload();
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
      });
  }

   /**
   * Cancela atualização de status eleito dos membros
   */
  public cancelarStatusEleito(): void {
    this.messageService.addConfirmYesNo(
      'MSG_CANCELAR_MEMBROS',
      () => {
        location.reload();
      }
    );
  }

  /**
   * Libera para salvar status eleito
   */
  public ativarSalvarStatusEleito(): void {
    this.permitirSalvar = true;
  }

  /**
   * Libera para salvar status eleito
   */
  public validarPermissaoEleito(): void {
    if (this.user.roles.find(roles => roles == '01602009')) {
      this.permitirEditarEleito = true;
    }
  }
}
