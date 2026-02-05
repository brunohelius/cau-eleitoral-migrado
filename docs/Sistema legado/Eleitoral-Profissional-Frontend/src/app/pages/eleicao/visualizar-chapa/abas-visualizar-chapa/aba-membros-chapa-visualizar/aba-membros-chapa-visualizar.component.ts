import {
  Component,
  OnInit,
  Input,
  Output,
  EventEmitter,
  TemplateRef,
} from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import * as _ from 'lodash';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { BsModalService, BsModalRef, TypeaheadMatch } from 'ngx-bootstrap';
import { NgForm } from '@angular/forms';
import { CookieService } from 'ngx-cookie-service';
import { map } from 'rxjs/operators';
import { SecurityService } from '@cau/security';

@Component({
  selector: 'aba-membros-chapa-visualizar',
  templateUrl: './aba-membros-chapa-visualizar.component.html',
  styleUrls: ['./aba-membros-chapa-visualizar.component.scss'],
})
export class AbaMembrosChapaVisualizarComponent implements OnInit {
  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Output() avancar: EventEmitter<any> = new EventEmitter();
  @Input() isUsuarioEditor?: boolean;
  @Input() isEleicaoDentroDoPrazoCadastro?: boolean;
  @Output() chapaEleicaoChange = new EventEmitter<any>();

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
  public membroChapaSelecionado: any;
  public alteracaoMembroChapa: any;

  public dadosMembroAlterar: any;
  public modalAlterarDadosMembro: BsModalRef;

  public statusAlterarMembro: any;
  public submittedFormJustificarAlterarMembro: boolean;
  public filtroCpfNome: string;
  public filtroProfissionaisPorCpfNome: any;
  public modaljustificarAlteracaoSituacaoResponsavel: BsModalRef;
  public usuario: any;

  public limiteResponsaveis: number;

  public showMSGCriteriosRepresentatividade: boolean = false;
  public showMSGCotistasRepresentatividade: boolean = false;

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
    private securityService: SecurityService,
    private chapaEleicaoClientService: ChapaEleicaoClientService
  ) {}

  /**
   * Executado ao inicializar o componente.
   */
  ngOnInit() {
    this.limitePaginacao = 10;
    this.limitesPaginacao = [10, 20, 30, 50, 100];
    this.limiteResponsaveis = 3;
    this.isUsuarioEditor =
      this.isUsuarioEditor === undefined ? true : this.isUsuarioEditor;

    this.usuario = this.securityService.credential['_user'];
    this.inicializarMembrosChapa();

    this.statusAlterarMembro = {
      situacaoResponsavel: undefined,
      justificativa: '',
      membro: {},
    };

    this.verificaCriteriosRepresentatividade();
  }

  /**
   * Responsável por cancelar o cadastro na aba.
   */
  public cancelar(): void {
    this.messageService.addConfirmYesNo('MSG_CANCELAR_CADASTRO_CHAPA', () => {
      this.router.navigate(['/']);
    });
  }

  /**
   * Incluir membro chapa selecionado.
   *
   * @param event
   */
  public adicionarMembroChapa(membro: any): void {
    if (
      this.alteracaoMembroChapa &&
      membro.profissional.id == this.alteracaoMembroChapa.idProfissional
    ) {
      this.alteracaoMembroChapa = undefined;
    } else {
      this.chapaEleicaoService
        .incluirMembro(this.chapaEleicao.id, {
          idProfissional: membro.profissional.id,
          idTipoParticipacao: membro.membroChapa.tipoParticipacaoChapa.id,
          idTipoMembroChapa: membro.membroChapa.tipoMembroChapa.id,
          numeroOrdem: membro.membroChapa.numeroOrdem,
        })
        .subscribe(
          (data) => {
            this.updateMembroChapa();
            this.mostrarMensagemMembroAdicionadoAlterado();
            this.alteracaoMembroChapa = undefined;
          },
          (error) => {
            this.messageService.addMsgDanger(error);
          }
        );
    }
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
   * Atualiza lista de membro da chapa eleitoral.
   */
  public atualizarChapaEleicao(): void {
    this.chapaEleicaoService.atualizarStatus(this.chapaEleicao.id).subscribe(
      (data) => {
        this.chapaEleicao = data;
        this.chapaEleicaoChange.emit(data);
        this.inicializarMembrosChapa();
      },
      (error) => {
        this.messageService.addMsgDanger(error);
      }
    );
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
    if (this.isPermitidoVisualizarPendencias(element)) {
      this.membroChapaSelecionado = element;
      this.modalPendeciasMembro = this.modalService.show(
        template,
        Object.assign({}, { class: 'my-modal' })
      );
    }
  }

  /**
   * Retorna se o profissional logado é o mesmo membro passado como parâmetro
   * @param membro
   */
  public isUsuarioMembro(membro: any): boolean {
    return (
      membro &&
      membro.profissional &&
      this.usuario.idProfissional == membro.profissional.id
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
    return this.isUsuarioEditor && this.isEleicaoDentroDoPrazoCadastro;
  }

  /**
   * Verifica se a ação de Enviar e-mail com pendências do membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoEnviarEmailPendenciaHabilitada(membroChapa: any): boolean {
    return (
      this.isUsuarioEditor &&
      this.isStatusValidacaoMembroChapaPendente(
        membroChapa.statusValidacaoMembroChapa.id
      )
    );
  }

  /**
   * Verifica se a ação de Reenviar e-mail do convite do membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoRenviarConviteHabilitada(membroChapa: any): boolean {
    return (
      this.isUsuarioEditor &&
      (this.isStatusParticipacaoChapaPendente(
        membroChapa.statusParticipacaoChapa.id
      ) ||
        this.isStatusParticipacaoChapaRejeitado(
          membroChapa.statusParticipacaoChapa.id
        ))
    );
  }

  /**
   * Verifica se a ação de Reenviar e-mail do convite do membro está habilitada.
   *
   * @param membroChapa
   */
  public isAcaoExcluirMembroChapa(membroChapa: any): boolean {
    return this.isUsuarioEditor && this.isEleicaoDentroDoPrazoCadastro;
  }

  /**
   * Válida se os campos do componente foram alterados.
   */
  private isCamposAlterados(): boolean {
    return true;
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
   * Envia e-mail que lista as pendencias de um determinado membro da chapa eleitoral.
   *
   * @param membroChapa
   */
  public enviarEmailPendencia(membroChapa: any): void {
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

  /**
   * Renvia e-mail do convite do membro chapa.
   *
   * @param membroChapa
   */
  public renviarConvite(membroChapa: any): void {
    this.chapaEleicaoService
      .enviarEmailDeConvitePorMembroChapa(membroChapa.id)
      .subscribe(
        (data) => {
          this.updateMembroChapa();
          this.messageService.addMsgSuccess(data);
        },
        (error) => {
          this.messageService.addMsgDanger(error);
        }
      );
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

  /**
   * Habilita input para alteração do membro da chapa.
   *
   * @param membroChapa
   */
  public excluirMembroChapa(membroChapa: any): void {
    this.messageService.addConfirmYesNo(
      'MSG_DESEJA_REALMENTE_EXCLUIR_MEMBRO_CHAPA',
      () => {
        this.chapaEleicaoService.excluirMembro(membroChapa.id).subscribe(
          (data) => {
            this.updateMembroChapa();
            this.messageService.addMsgSuccess('MSG_EXCLUSAO_COM_SUCESSO');
          },
          (error) => {
            this.messageService.addMsgDanger(error.description);
          }
        );
      }
    );
  }

  /**
   * Busca os dados e abri modal de alteração de dados do membro logado
   * @param membroChapa
   * @param template
   */
  public alterarDadosMembroChapa(
    membroChapa: any,
    template: TemplateRef<any>
  ): void {
    this.chapaEleicaoClientService
      .getCurriculoMembroChapaPorMembroChapa(membroChapa.id)
      .subscribe(
        (data) => {
          this.dadosMembroAlterar = data;
          this.dadosMembroAlterar.id = membroChapa.id;
          this.modalAlterarDadosMembro = this.modalService.show(
            template,
            Object.assign({}, { class: 'modal-xl' })
          );
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
   * Fecha a modal de alteração de dados do membro
   */
  public fecharModalAlterarDadosMembro(): void {
    this.dadosMembroAlterar = undefined;
    if (this.modalAlterarDadosMembro) {
      this.modalAlterarDadosMembro.hide();
    }
  }

  /**
   * Verifica se o campo de responsável está desabilitado.
   *
   * @param membroChapa
   */
  public isInputResponsavelDesabilitado(membroChapa: any): boolean {
    return (
      this.chapaEleicao.idProfissionalInclusao == membroChapa.profissional.id ||
      !this.isEleicaoDentroDoPrazoCadastro ||
      !this.isUsuarioEditor
    );
  }

  /**
   * Exibe modal justificar alteração de status de responsável de membro da chapa.
   *
   * @param event
   * @param membroChapa
   * @param template
   */
  public justificarAlteracaoSituacaoResponsavel(
    event,
    membroChapa: any,
    template: TemplateRef<any>
  ): void {
    event.preventDefault();
    if (membroChapa.situacaoResponsavel || this.validarResponsavel()) {
      let status = membroChapa.situacaoResponsavel;
      this.statusAlterarMembro.situacaoResponsavel =
        !membroChapa.situacaoResponsavel;
      this.statusAlterarMembro.justificativa = ' ';
      this.statusAlterarMembro.membro = membroChapa;

      this.modaljustificarAlteracaoSituacaoResponsavel = this.modalService.show(
        template,
        Object.assign({}, { class: 'modal-lg' })
      );
    }
  }

  /**
   * Valida responsáveis pela chapa.
   */
  public validarResponsavel(): boolean {
    if (this.getNumeroResponsaveis() >= this.limiteResponsaveis) {
      this.messageService.addMsgWarning(
        'MSG_AVISO_NUMERO_MAXIMO_RESPONSAVEIS',
        [this.limiteResponsaveis]
      );
    }
    return this.getNumeroResponsaveis() < this.limiteResponsaveis;
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
    let situacao: any = {
      situacaoResponsavel: this.statusAlterarMembro.situacaoResponsavel,
      justificativa: this.statusAlterarMembro.justificativa,
    };

    this.chapaEleicaoService
      .alterarSituacaoResponsavel(this.statusAlterarMembro.membro.id, situacao)
      .subscribe(
        (data) => {
          this.updateMembroChapa();
          this.modaljustificarAlteracaoSituacaoResponsavel.hide();
          this.messageService.addMsgSuccess('MSG_DADOS_ALTERADOS_COM_SUCESSO');
        },
        (error) => {
          this.messageService.addMsgDanger(error.description);
        }
      );
  }

  /**
   * Atualiza lista de membro da chapa eleitoral.
   */
  private updateMembroChapa(): void {
    this.chapaEleicaoService.getChapaEleicaoAcompanhar().subscribe(
      (data) => {
        this.chapaEleicao = data.chapaEleicao;
        this.chapaEleicaoChange.emit(data.chapaEleicao);
        this.inicializarMembrosChapa();
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
   * Verifica se o profissional é responsável de chama ou está tentando visualizar suas próprias pendências
   */
  private isPermitidoVisualizarPendencias(membroAtual: any): boolean {
    let responsavelConfirmado = this.chapaEleicao.membrosChapa.find(
      (membroChapa) => {
        if (membroChapa.profissional.id == this.usuario.idProfissional) {
          let isUsuarioResponsavel: boolean = membroChapa.situacaoResponsavel;
          return (
            isUsuarioResponsavel ||
            membroAtual.profissional.id == this.usuario.idProfissional
          );
        }
        return false;
      }
    );
    return responsavelConfirmado != undefined;
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
}
