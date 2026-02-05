import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap/modal';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild, ɵConsole } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

import * as _ from 'lodash';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'app-adicionar-substituicao-modal',
    templateUrl: './adicionar-substituicao.component.html',
    styleUrls: ['./adicionar-substituicao.component.scss']
})
export class ModalAddSubsJulgFinalComponent implements OnInit {

  @ViewChild('templateConfirmacaoSubstituicao', null) templateConfirmacaoSubstituicao: TemplateRef<any>;

  @Input() chapa: any;
  @Input() idJulgamentoFinal: number;
  @Input() membrosPendencia: any = [];
  @Input() isPrimeiraInstancia?: boolean = false;
  @Output() fecharModalCancelar: EventEmitter<any> = new EventEmitter();
  @Output() fecharModalConfirmacaoSalvar: EventEmitter<any> = new EventEmitter();

  public submitted: boolean;

  public showDebug = false;

  public modalSubstituicao: BsModalRef | null;
  public modalPendeciasMembro: BsModalRef | null;
  public modalConfirmacaoCancelar: BsModalRef | null;
  public modalConfirmacaoSalvar: BsModalRef | null;

  public tituloModalConfirmacao: string;
  public msgConfirmacaoCancelar: string;

  public tituloModalConfirmacaoSalvar: string;
  public msgConfirmacaoSalvar: string;

  public substituicao: any;
  public titleDescricao: any;

  public membrosDaChapa: any = [];

  public isMembros: boolean;

  public isDisabled = true;
  public isLoading = false;

  public membrosAtuais: any = {};

  public responsaveis: any = [];

  public membroChapaSelecionado: any;
  public nomeMembroChapa: string;

  public substituidoSelecionado: any = [0];
  public substitutoSelecionado: any = [];

  public membroSubstituto: any;
  public nomeJaCadastrado: string;
  public dadosFormatados: any;

  public substituicaoSalva: any;

  /**
   * Construtor da classe.
   */
  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
  ) { }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {  }

  /**
   * Inicializar recurso de julgamento de substituição.
   */
  public inicializarSubstituicao(): void {
    const semMembro = this.messageService.getDescription('LABEL_SEM_MEMBRO_INCLUSAO');
    if (this.membrosPendencia.length > 0) {

      this.membrosPendencia.forEach((user: any) => {
        const profissional = (user.membroChapa ? user.membroChapa.profissional : null);

        this.membrosDaChapa.push({
          id: user.id,
          posicaoChapa: user.numeroOrdem,
          participacao: user.tipoParticipacaoChapa.descricao,
          idParticipacao: user.tipoParticipacaoChapa.id,
          nome: (profissional ? profissional.nome : semMembro),
          registro: (profissional ? profissional.registroNacional : ''),
          hidden: false
        });
      });

      this.ordernarMembrosDaChapa();
    }

    this.substituicao = {
      justificativa: '',
      chapa: this.chapa,
      idJulgamentoFinal: this.idJulgamentoFinal,
      arquivos: [],
      membrosSubstituicaoJulgamentoFinal: []
    };
  }

  /**
   * Responsável por ordenar os membros do grid selecionado
   */
  private ordernarMembrosDaChapa(): void {
    this.membrosDaChapa = _.orderBy(
      this.membrosDaChapa,
      ['posicaoChapa', 'idParticipacao'],
      ['asc', 'asc']
    );
  }

  /**
   * Exibe modal de cadastro de recurso/reconsideracao.
   */
  public abrirModalSubstituicao(template: TemplateRef<any>): void {
    this.inicializarSubstituicao();

    this.titleDescricao = 'TITLE_SUBSTITUICAO_MEMBRO_JULGAMENTO';
    this.modalSubstituicao = this.modalService.show(template, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-xl modal-dialog-centered'
    });
  }

  /**
    * Validação para apresentar o botão Solicitar Substituicao
    */
  public substituicaoReconsideracao(): any {
    return this.messageService.getDescription('TITLE_SUBSTITUICAO_MEMBRO_JULGAMENTO');
  }

  /**
   * Retorna placeholder utilizado no autocomplete de profissional.
   */
  public getPlaceholderAutoCompleteProfissional(): string {
    return this.messageService.getDescription('MSG_INSIRA_NUMERO_DE_REGISTRO_OU_NOME_CANDIDATO');
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
   * Validar dados do membro substituído.
   */
  public onChangeSubstituido(): any {
    this.isDisabled = JSON.parse(this.substituidoSelecionado) !== 0 && this.nomeMembroChapa ? false : true;
  }

  /**
   * Validar dados do membro substituto.
   */
  public onChangeSubstituto(membro: any): void {
    this.substitutoSelecionado = membro.profissional;
    this.isDisabled = this.substituidoSelecionado.indexOf('id') >= 0 && this.nomeMembroChapa ? false : true;
  }

  /**
   * Validar se existe um nome preenchido ao sair do campo.
   */
  public focusOutSubstituto(): any {
    if (!this.nomeMembroChapa) {
      this.isDisabled = true;
      this.substitutoSelecionado = {};
    }
  }

  /**
   * Adiciona um membro a ser substituído na grid
   */
  public adicionarSubstituto(): any {
    this.isDisabled = true;
    this.isLoading = true;

    if (this.validaExistencia()) {

      const params = {
        idProfissional: this.substitutoSelecionado.id,
        idChapaEleicao: this.chapa.id,
        numeroOrdem: this.substituidoSelecionado.posicaoChapa
      };

      this.julgamentoFinalClientService.getMembroSubstituicao(params).subscribe(
        data => {
          const substituidoSelecionado = JSON.parse(this.substituidoSelecionado);
          const novoSubstituto = {
              substituido: substituidoSelecionado,
              substituto: {
                id: this.substitutoSelecionado.id,
                nome: this.substitutoSelecionado.nome,
                registro: this.substitutoSelecionado.registroNacional,
                statusParticipacaoChapa: data.statusValidacaoMembroChapa,
                pendencias: data.pendencias
              }
            };

          this.substituicao.membrosSubstituicaoJulgamentoFinal.push(novoSubstituto);
          this.abilitaDesabilitaSubstituido(substituidoSelecionado.id, true);
          this.cleanFormSubstituidoSubstituto();
        },
        error => {
            this.isLoading = false;
            this.isDisabled = false;
            this.messageService.addMsgWarning(error);
        },
        () => {
          this.isLoading = false;
        }
      );
    }
  }

  /**
   * Verifica o status de Validação do Membro.
   */
  public removerSubstituto(indexMembro: number, idMembro: number) {
    this.messageService.addConfirmYesNo('MSG_CONFIRMA_EXCLUIR_REGISTRO', () => {
      this.substituicao.membrosSubstituicaoJulgamentoFinal.splice(indexMembro, 1);
      this.messageService.addMsgSuccess('MSG_EXCLUSAO_COM_SUCESSO');
      this.abilitaDesabilitaSubstituido(idMembro, false);
    });
  }

  /**
   * Abilita ou desabilita o substituido no select.
   */
  public abilitaDesabilitaSubstituido(idMembro: number, abilitaDesabilita: boolean) {
    const disabilitaSelect = this.membrosDaChapa.findIndex((membro) => membro.id === idMembro);
    this.membrosDaChapa[disabilitaSelect].hidden = abilitaDesabilita;
  }

  /**
   * Validar se o candidato Substituído ou Substituto ja foi adicionado na grid
   */
  public validaExistencia() {
    const substituidoSelecionado = JSON.parse(this.substituidoSelecionado);

    const nomeSubstituido = this.substituicao.membrosSubstituicaoJulgamentoFinal.find(
      (membro: any) => membro.substituido.id === substituidoSelecionado.id
    );

    const nomeSubstituto = this.substituicao.membrosSubstituicaoJulgamentoFinal.find(
      (membro: any) => membro.substituto.id === this.substitutoSelecionado.id
    );

    if (nomeSubstituido || nomeSubstituto) {
      if (nomeSubstituido) { this.nomeJaCadastrado = nomeSubstituido.substituido.nome; }
      if (nomeSubstituto) { this.nomeJaCadastrado = nomeSubstituto.substituto.nome; }

      this.messageService.addMsgDanger(this.messageService.getDescription('MSG_PROFISSIONAL_JA_INCLUIDO', this.nomeJaCadastrado));
      this.isDisabled = false;
      this.isLoading = false;
      return false;

    } else {
      this.nomeJaCadastrado = null;
      return true;
    }
  }

  /**
   * Retorna a classe de acordo com a situação do convite do membro da chapa substituto
   */
  public getStatus(id: number): string {
      let classe = '';

      if (id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO) {
          classe = 'ribbon-primary';
      }

      if (id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO) {
          classe = 'ribbon-danger';
      }

      if (id === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO) {
          classe = 'ribbon-warning ';
      }

      return classe;
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
    return  this.messageService.getDescription('LABEL_STATUS_VALIDACAO');
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
   * Resoponsavel por adicionar a justificativa que fora submetido no compomente editor de texto.
   */
  public adicionarJustificativa(justificativa: string): void {
    this.substituicao.justificativa = justificativa;
  }

  /**
   * Cancela a substituição.
   */
  public cancelarSubstituicao() {
    this.tituloModalConfirmacao = this.messageService.getDescription('MSG_TITULO_CANCELAR');
    this.msgConfirmacaoCancelar = this.messageService.getDescription('MSG_CANCELAR_SUBSTITUICAO');

    this.messageService.addConfirmYesNo(this.msgConfirmacaoCancelar,()=>{
      this.confirmarCancelamentoModal();
      this.fecharModalCancelar.emit();
    });
  }

  /**
   * Limpa os campos de Substituído e Substituto
   */
  public cleanFormSubstituidoSubstituto() {
    this.substituidoSelecionado = [0];
    this.substitutoSelecionado = {};
    this.nomeMembroChapa = null;
  }

  /**
   * Limpa todos os campos
   */
  public cleanAll() {
    this.cleanFormSubstituidoSubstituto();
    this.substituicao.justificativa = '';
    this.substituicao.arquivos = [];
    this.substituicao.membrosSubstituicaoJulgamentoFinal = [];
    this.membrosDaChapa = [];
    this.dadosFormatados = [];
    this.submitted = false;
  }

  /**
   * Confirmar cancelamento do cadastro de substituição
   */
  public confirmarCancelamentoModal(): any {
    setTimeout(() => {
      this.modalSubstituicao.hide();
      this.cleanAll();
    }, 400);
  }


  /**
     * Verifica se a discricao foi preencida.
     */
    public hasJustificativa(): any {
      return this.substituicao.justificativa;
  }

  /**
   * Salvar a substituição
   */
  public salvarSubstituicao(): void {

    if (this.substituicao.membrosSubstituicaoJulgamentoFinal.length === 0) {
      this.messageService.addMsgDanger('MSG_NENHUM_CANDIDATO_INFORMADO');
      return;
    }

    this.submitted = true;

    if (this.hasJustificativa()) {
      this.formatarEnvio();

      this.julgamentoFinalClientService.salvarPedidoSubstituicao(this.dadosFormatados).subscribe(
        data => {
          this.modalSubstituicao.hide();
          this.cleanAll();

          this.substituicaoSalva = data;
          setTimeout(() => {
            this.abrirModalConfirmacaoSalvar(this.templateConfirmacaoSubstituicao);
          }, 400);
  

        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }

    // this.showDebug = true;
    // return this.dadosFormatados;
  }

  /**
   * Responsavel por formatar os dados para o envio
   */
  public formatarEnvio() {

    const membros = [];
    this.substituicao.membrosSubstituicaoJulgamentoFinal.forEach((membro: any) => {
      membros.push({idIndicacaoJulgamento: membro.substituido.id, idProfissional: membro.substituto.id});
    });

    this.dadosFormatados = {
      justificativa: this.substituicao.justificativa,
      idJulgamentoFinal: this.substituicao.idJulgamentoFinal,
      arquivos: this.substituicao.arquivos,
      membrosSubstituicaoJulgamentoFinal: membros,
      isPrimeiraInstancia: this.isPrimeiraInstancia
    };
  }

  /**
   * Exibe modal de confirmação da substituição.
   */
  public abrirModalConfirmacaoSalvar(template: TemplateRef<any>) {
    this.tituloModalConfirmacaoSalvar = this.messageService.getDescription('TITLE_CONFIRMACAO_PEDIDO_SUBSTITUICAO');
    this.msgConfirmacaoSalvar = this.messageService.getDescription('MSG_CONFIRMACAO_SUBSTITUICAO_MEMBRO_JULGAMENTO');

    this.modalConfirmacaoSalvar = this.modalService.show(template, {
      backdrop: true,
      ignoreBackdropClick: true,
      class: 'modal-lg modal-dialog-centered'
    });
  }

  /**
   * Carregar tela de visualização de substituições
   */
  public redirecionarVisualizarSubstituicao() {
    this.fecharModalConfirmacaoSalvar.emit(this.substituicaoSalva);
    this.modalConfirmacaoSalvar.hide();
  }

  /**
   * Download do arquivo.
   */
  public downloadModalSubstituicao(download: any): any {
    download.evento.emit(download.arquivo);
  }

  /**
   * Responsavel por salvar os arquivos que foram submetidos no componete arquivo.
   */
  public salvarArquivos(arquivos: any): void {
    this.substituicao.arquivos = arquivos;
  }

  public getLabelStatusValidacaoQuebraLinha(): any {
    return  this.messageService.getDescription('LABEL_STATUS_VALIDACAO_SUBSTITUICAO_IMPUGNACAO',['<br>']);
  }

}
