import * as _ from 'lodash';
import { NgForm } from '@angular/forms';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { ActivatedRoute, Router } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, EventEmitter, Input, OnInit, Output, TemplateRef } from '@angular/core';

import { AcaoSistema } from 'src/app/app.acao';
import { Constants } from 'src/app/constants.service';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { ProfissionalClientService } from 'src/app/client/profissional-client/profissional-client.service';
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';

/**
 * Componente referente á aba de cadastro da comissão de membros.
 *
 */
@Component({
  selector: 'aba-cadastro-comissao-membro',
  templateUrl: './aba-cadastro-comissao-membro.component.html',
  styleUrls: ['./aba-cadastro-comissao-membro.component.scss']
})
export class AbaCadastroComissaoMembroComponent implements OnInit {

  @Input() eleicoes: any;
  @Input() anosEleicoes: any;
  @Input() cauUfs: any[];
  @Input() configuracaoEleicao: any;
  @Input() tiposParticipacao: any;
  @Input() acaoSistema: AcaoSistema;
  @Input() cauUfAlterar: any;
  @Output() conclusao: EventEmitter<any> = new EventEmitter();

  public modalRef: BsModalRef;
  public editable: boolean;
  public eleicao: any;
  public numeroMembros: any;
  public coordenadores: Array<any>;
  public membros: Array<any>;
  public isMembrosSelecionada: boolean = false;
  public qteMembros: any = "";
  public qteMembrosSelecionada: any = "";
  public cauUfSelecionada: string;
  public justificativa: string;
  public pendenciasMembro: Array<any> = null;
  public requerJustificativa: boolean = false;
  public constants: any = Constants;
  public submitted: boolean = false;
  public usuario: any;


  /**
   * Construtor da classe
   *
   * @param route
   * @param messageService
   * @param eleicaoClientService
   * @param profissionalClientService
   * @param modalService
   * @param securtyService
   * @param router
   * @param cauUFService
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
    private profissionalClientService: ProfissionalClientService,
    private modalService: BsModalService,
    private securtyService: SecurityService,
    private router: Router,
    private cauUFService: CauUFService
  ) { }

  /**
   * Inicializaçao das dependencias do componente.
   */
  ngOnInit() {
    this.usuario = {};
    this.usuario = this.securtyService.credential["_user"];
    this.setEleicao();
    this.cauUfSelecionada = this.usuario.cauUf.descricao;
    this.numeroMembros = this.getIntervalodNumeroMembrosComissao();
    this.validarAcaoInicial();
    this.obterUfsMembrosNaoCadastradosPorCalendario();
  }

  /**
   * Valida a ação inicial do componente, caso seja uma alteração o mesmo executa algumas ações
   */
  private validarAcaoInicial(): void {
    if (!this.isCampoUfHabilitadoEdicao()) {
      this.eleicao.cauUf = this.usuario.cauUf.id;
    }

    if (this.acaoSistema.isAcaoAlterar()) {
      this.eleicao.cauUf = (this.cauUfAlterar) ? this.cauUfAlterar : this.usuario.cauUf.id;
      this.getMembrosComissao();
    }
  }

  /**
   * Retorna as possibilidades de números referentes ao intevalos de quantidade de membros.
   */
  private getIntervalodNumeroMembrosComissao(): Array<any> {
    let intervaloNumeroMembros = [this.eleicao.minimoMembrosComissao, this.eleicao.maximoMembrosComissao];
    if (this.eleicao.tipoOpcao === Constants.TIPO_NUMERO_MEMBROS_A) {
      intervaloNumeroMembros = [];
      for (let i = this.eleicao.minimoMembrosComissao; i <= this.eleicao.maximoMembrosComissao; i++) {
        intervaloNumeroMembros.push(i);
      }
    }
    return intervaloNumeroMembros;
  }

  /**
   * Define os dados do objeto eleição
   * objEleicao: any
   */
  private setEleicao(): void {
    let calendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario;
    this.eleicao = {
      id: this.configuracaoEleicao.id,
      eleicao: calendario.eleicao,
      anoEleicao: calendario.ano,
      minimoMembrosComissao: this.configuracaoEleicao.quantidadeMinima,
      maximoMembrosComissao: this.configuracaoEleicao.quantidadeMaxima,
      tipoOpcao: this.configuracaoEleicao.tipoOpcao
    };
    if (!this.isCampoUfHabilitadoEdicao()) {
      this.eleicao.cauUf = this.usuario.cauUf.id;
    }
  }

  /**
   * Realiza a confirmação de alteração do número de membros.
   *
   * @param event
   */
  public confirmarAlteracaoNumeroMembros(event: any): void {
    if (this.qteMembros > 0) { // Caso já exista um número de membros.
      this.messageService.addConfirmYesNo('MSG_CONFIRMACAO_ALTERAR_NUMERO_MEMBROS', () => {
        this.selecionarNumeroMembros(event);
      }, () => {
        // Mantém o número alterior à alteração.
        this.qteMembrosSelecionada = this.qteMembros;
      });
    } else {
      this.selecionarNumeroMembros(event);
    }
  }

  /**
   * Define a quantidade de membros coordenadores e membros comuns
   * @param event
   */
  public selecionarNumeroMembros(event: any): void {
    this.submitted = false;
    let numeroMembrosSelecionados = parseInt(event.target.value);
    this.coordenadores = [];
    this.membros = [];
    this.isMembrosSelecionada = false;
    this.qteMembros = numeroMembrosSelecionados;
    if (numeroMembrosSelecionados > 0) {
      this.isMembrosSelecionada = true;
      if (event.target.value <= Constants.PADRAO_MEMBROS_COORDENADORES) {
        this.coordenadores = Array(numeroMembrosSelecionados)
      } else {
        this.coordenadores = Array(Constants.PADRAO_MEMBROS_COORDENADORES);
        this.membros = Array(numeroMembrosSelecionados - Constants.PADRAO_MEMBROS_COORDENADORES);
      }
    }
    this.setArrayMembros();
  }

  /**
   * Inicia os objetos de membros comuns e coordenadores da comissão
   */
  private setArrayMembros(): void {
    let contador_index = 0;
    for (let i = 0; i < this.coordenadores.length; i++) {
      if (i === 0) {
        this.coordenadores[i] = this.getNovoMembro(contador_index,
          this.getTipoParticipacao(Constants.TIPO_COORDENADOR),
          this.getTipoParticipacao(Constants.TIPO_COORDENADOR_SUBSTITUTO));
      } else {
        this.coordenadores[i] = this.getNovoMembro(contador_index,
          this.getTipoParticipacao(Constants.TIPO_COORDENADOR_MEMBRO_SUBSTITUTO),
          this.getTipoParticipacao(Constants.TIPO_COORDENADOR_SUBSTITUTO));
      }
      contador_index++;
    }

    for (let i = 0; i < this.membros.length; i++) {
      this.membros[i] = this.getNovoMembro(contador_index,
        this.getTipoParticipacao(Constants.TIPO_MEMBRO),
        this.getTipoParticipacao(Constants.TIPO_SUBSTITUTO));
      contador_index++;
    }
  }

  /**
   * Retorna o objeto de tipo de participação de acordo com o tipo
   * @param tipo
   */
  private getTipoParticipacao(tipo: number): any {
    let indexTipoParticipacao = _.findIndex(this.tiposParticipacao, { 'id': tipo });
    return this.tiposParticipacao[indexTipoParticipacao];
  }

  /**
   * Retorna o objeto de tipo de participação de acordo com o tipo
   * @param tipo
   */
  private getMembrosByCpf(cpf: string): Array<any> {
    let membros = [];

    _.filter(this.coordenadores, coordenador => {
      if (coordenador.cpf === cpf) {
        membros.push(coordenador);
      }
      if (coordenador.membroSubstituto.cpf === cpf) {
        membros.push(coordenador.membroSubstituto);
      }
    });
    _.filter(this.membros, membro => {
      if (membro.cpf === cpf) {
        membros.push(membro);
      }
      if (membro.membroSubstituto.cpf === cpf) {
        membros.push(membro.membroSubstituto);
      }
    });

    return membros;
  }

  /**
   * Consulta os dados do profissional no SICCAU por cpf
   * @param membro
   */
  private getDadosProfissionalByCpf(membro: any, membroCompleto: any = undefined): void {
    this.profissionalClientService.getProfissional(membro.cpf).subscribe(
      data => {
        membro.nome = data.nome;
        membro.pessoa = data.id;
        membro.email = data.pessoa.email;
        membro.pendencias = this.getPendencias(data);
        membro.idSituacao = Constants.SITUACAO_MEMBRO_CONFIRMADO;
        membro.conselheiro = data.conselheiro;
        if (membroCompleto) {
          if (membroCompleto.conselheiro && !membro.conselheiro) {
            membro.pendencias.push('MSG_VALIDA_COORDENADOR_SUB_CONSELHEIRO');
          } else if (!membroCompleto.conselheiro && membro.conselheiro) {
            membro.pendencias.push('MSG_VALIDA_COORDENADOR_SUB_NAO_CONSELHEIRO');
          }
        }
        if (membro.pendencias.length > 0) {
          membro.idSituacao = Constants.SITUACAO_MEMBRO_PENDENTE;
        }
        this.validarCoordenadorConselheiro(membro);
        membro.inserido = true; // Indica que a pessoa está sendo inserida como Membro/Coordenador na comissão.
      }, error => {
        delete membro.cpf;
        this.messageService.addMsgDanger('MSG_CPF_NAO_ENCONTRADO');
      }
    );
  }

  /**
   * Recupera as pendencias se exitir atraves do cadastro do profissional no SICCAU
   * @param data
   */
  public getPendencias(data: any): Array<any> {
    let pendencias = [];
    if (!_.isEmpty(data)) {
      if (!data.adimplente && data.adimplente !== undefined) {
        pendencias.push('MSG_PENDENCIA_ANUNIADADE_PROFISSIONAL');
      }

      let situacao_registro: any = {};
      if (data.situacao_registro) {
        situacao_registro = data.situacao_registro;
      } else if (data.situacaoRegistro) {
        situacao_registro = data.situacaoRegistro;
      }

      if (data.tempoRegistroAtivo < 2 && situacao_registro.descricao != Constants.REGISTRO_ATIVO) {
        pendencias.push('MSG_PENDENCIA_REGISTRO_PROFISSIONAL');
      }
      if (data.infracaoEtica) {
        pendencias.push('MSG_PENDENCIA_ETICO_DISCIPLINAR_PROFISSIONAL');
      }
      if (data.infracaoRelacionadaExercicioProfissao) {
        pendencias.push('MSG_PENDENCIA_INFRACAO_PROFISSIONAL');
      }
    }
    return pendencias;
  }

  /**
   * set pendencias para exibição em popup
   */
  public setPendenciasMembro(membro: any) {
    this.pendenciasMembro = membro.pendencias;
  }

  /**
   * Recupera uma mensagem baseada no status do membro medianta a comissão
   * @param membro
   */
  public getMsgStatusMembro(membro: any): string {
    let message = '';
    if (this.acaoSistema.isAcaoAlterar() && !membro.inserido) {
      switch (membro.idSituacao) {
        case Constants.SITUACAO_MEMBRO_PENDENTE:
          message = 'LABEL_EM_ANALISE_PELO_MEMBRO';
          break;
        case Constants.SITUACAO_MEMBRO_CONFIRMADO:
          message = 'LABEL_MEMBRO_CONFIRMADO';
          break;
        case Constants.SITUACAO_MEMBRO_REJEITADO:
          message = 'LABEL_MEMBRO_REJEITOU_PARTICIPACAO';
          break;
      }
    } else if (membro.inserido) {
      if (membro.pendencias && membro.pendencias.length == 0) {
        message = (membro.conselheiro) ? 'LABEL_CONSELHEIRO' : 'LABEL_NAO_CONSELHEIRO';
      } else {
        message = 'LABEL_CLIQUE_VISUALIZAR_PENDENCIAS';
      }
    }
    return this.messageService.getDescription(message);
  }

  /**
   * Reseta a variavel de controle ao focar o campo de cpf
   * @param membro
   */
  public resetEventBuscaMembro(membro: any) {
    membro.triggered = false;
  }

  /**
   * Busca os dados do profissional pelo cpf
   * @param event
   */
  public getProfissional(membro: any, substituto: any): any {
    let _membro = substituto ? membro.membroSubstituto : membro;
    this.limpaMembro(_membro);
    if (_membro.cpf) {
      if (this.isCpfUnico(_membro.cpf)) {
        this.getDadosProfissionalByCpf(_membro, membro);
      } else {
        delete _membro.cpf;
        this.messageService.addMsgWarning('MSG_CPF_DUPLICADO');
      }
    }
  }

  /**
   * Limpa os atributos do membro em questão
   * @param membro
   */
  private limpaMembro(membro: any): void {
    delete membro.email;
    delete membro.nome;
    delete membro.pessoa;
    delete membro.idSituacao;
  }

  /**
   * Valida se o coordenador é conselheiro ou não
   * @param membro
   */
  private validarCoordenadorConselheiro(membro: any): void {
    if ((membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR ||
      membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR_MEMBRO_SUBSTITUTO ||
      membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR_SUBSTITUTO
    )) {
      let msgCoordenadorConselheiro = 'MSG_VALIDA_COORDENADOR_CONSELHEIRO';
      let msgCoordenadorNaoConselheiro = 'MSG_VALIDA_COORDENADOR_NAO_CONSELHEIRO';
      if (!this.configuracaoEleicao.situacaoConselheiro && !membro.conselheiro) {
        this.messageService.addMsgDanger(msgCoordenadorConselheiro);
      } else if (!this.configuracaoEleicao.situacaoConselheiro && membro.conselheiro) {
        this.messageService.addMsgDanger(msgCoordenadorNaoConselheiro);
      }
    }
  }

  /**
   * Verifica se a quantidade de membros conselheiros é majoritaria
   */
  private isMajoritariamenteConselheiro(): boolean {
    let majoriatarioConslheiro = false;
    if (this.getQtdConselheiros() >= Math.ceil(parseInt(this.qteMembros) * 0.51)) {
      majoriatarioConslheiro = true;
    }
    return majoriatarioConslheiro;
  }

  /**
   * Retorna a quantidade de membros conselheiros
   */
  private getQtdConselheiros(): number {
    let qtdConselheiros = 0;
    this.coordenadores.forEach(membro => {
      if (membro.conselheiro) {
        qtdConselheiros++;
      }
    });
    this.membros.forEach(membro => {
      if (membro.conselheiro) {
        qtdConselheiros++;
      }
    });
    return qtdConselheiros;
  }

  /**
   * Verifica se o usuario logado possui permissão para salvar mediantade a justificativa
   */
  private hasPermissaoSalvarJustificativa(): boolean {
    return _.includes(this.usuario.roles, Constants.ROLE_ACESSOR_CEN);
  }

  /**
   * Valida cpf unico na lista de membros da comissão
   * @param cpf
   */
  private isCpfUnico(cpf: any): boolean {
    let membros = this.getMembrosByCpf(cpf);
    return membros.length > 1 ? false : true;
  }

  /**
   * Metodo que retorna a estrutura de um objeto de membro
   */
  private getNovoMembro(index: number, tipoParticipacao: any, tipoParticipacaoMembroSubstituto: any): any {
    return {
      key: index,
      cpf: undefined,
      pessoa: undefined,
      nome: '-',
      email: '-',
      tipoParticipacao: tipoParticipacao,
      idSituacao: '1',
      "idCauUf": this.eleicao.cauUf,
      membroSubstituto: {
        tipoParticipacao: tipoParticipacaoMembroSubstituto,
        idCauUf: this.eleicao.cauUf
      }
    }
  }


  /**
     * Abre a modal para informa a justificativa, quando houver membro com pendencias
     *
     * @param template
     */
  openModal(template: TemplateRef<any>) {
    this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }))
  }

  /**
   * Requisita a lista de membros das comissões agrupado por CAU/UF
   */
  private getMembrosComissao(): void {
    this.eleicaoClientService.getMembroComissoes(this.eleicao.id, this.eleicao.cauUf).subscribe(
      data => {
        this.isMembrosSelecionada = true;
        this.qteMembros = data.qtdMembros;
        this.qteMembrosSelecionada = data.qtdMembros;
        this.membros = [];
        this.coordenadores = [];
        let index = 0;

        if (data.membros.length <= this.eleicao.minimoMembrosComissao) {
          for (let index = 0; index < (this.eleicao.minimoMembrosComissao - data.membros.length); index++) {
            data.membros.push({});
          }
        } else {
          for (let index = 0; index < (this.eleicao.maximoMembrosComissao - data.membros.length); index++) {
            data.membros.push({});
          }
        }

        _.filter(data.membros, membro => {
          this.setMembroComissao(membro, index);
          index++;
        });
      },
      error => {
        this.messageService.addMsgDanger(error.message);
        this.router.navigate(['/eleicao/membros-comissao/']);
      }
    )
  }

  /**
   * Insere um membro em uma lista de coordenador ou membro comum dependendo do seu tipo de participação
   * @param membro
   */
  private setMembroComissao(membro: any, index: number): void {
    if (!membro.id) {
      let tipoParticipacaoMembro = this.getTipoParticipacao(Constants.TIPO_MEMBRO)
      let tipoParticipacaoSubstituto = this.getTipoParticipacao(Constants.TIPO_SUBSTITUTO)

      this.membros.push({
        tipoParticipacao: {
          id: tipoParticipacaoMembro.id,
          descricao: tipoParticipacaoMembro.descricao,
        },
        membroSubstituto: {
          tipoParticipacao: {
            id: tipoParticipacaoSubstituto.id,
            descricao: tipoParticipacaoSubstituto.descricao,
          },
        },
      });
    } else {
      if (membro.membroSubstituto === undefined) {
        let tipoParticipacaoMembroSubstituto = Constants.TIPO_SUBSTITUTO;
        if (membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR || membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR_MEMBRO_SUBSTITUTO) {
          tipoParticipacaoMembroSubstituto = Constants.TIPO_COORDENADOR_SUBSTITUTO;
        }
        membro.membroSubstituto = {};
        membro.membroSubstituto.tipoParticipacao = this.getTipoParticipacao(tipoParticipacaoMembroSubstituto);
        membro.membroSubstituto.profissional = {};
        membro.membroSubstituto.situacaoVigente = {};
      }
      let membroFormatado = this.getNovoMembro(index, membro.tipoParticipacao, membro.membroSubstituto.tipoParticipacao);
      membroFormatado.id = membro.id;
      membroFormatado.cpf = membro.profissional.cpf;
      membroFormatado.nome = membro.profissional.nome;
      membroFormatado.email = membro.profissional.email;
      membroFormatado.pessoa = membro.pessoa;
      membroFormatado.idSituacao = membro.situacaoVigente.id;
      membroFormatado.pendencias = this.getPendencias(membro.profissional);
      membroFormatado.conselheiro = membro.profissional.conselheiro;
      if (membroFormatado.pendencias.length > 0) {
        membroFormatado.idSituacao = Constants.SITUACAO_MEMBRO_PENDENTE;
      }
      membroFormatado.membroSubstituto.cpf = membro.membroSubstituto.profissional.cpf;
      membroFormatado.membroSubstituto.nome = membro.membroSubstituto.profissional.nome;
      membroFormatado.membroSubstituto.pessoa = membro.membroSubstituto.pessoa;
      membroFormatado.membroSubstituto.email = membro.membroSubstituto.profissional.email;
      membroFormatado.membroSubstituto.idSituacao = membro.membroSubstituto.situacaoVigente.id;
      membroFormatado.membroSubstituto.pendencias = this.getPendencias(membro.membroSubstituto.profissional);
      membroFormatado.membroSubstituto.conselheiro = membro.membroSubstituto.profissional.conselheiro;
      if (membroFormatado.membroSubstituto.pendencias.length > 0) {
        membroFormatado.membroSubstituto.idSituacao = Constants.SITUACAO_MEMBRO_PENDENTE;
      }
      if (membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR || membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR_MEMBRO_SUBSTITUTO) {
        this.coordenadores.push(membroFormatado);
      } else if (membro.tipoParticipacao.id === Constants.TIPO_MEMBRO) {
        this.membros.push(membroFormatado);
      }
    }
  }

  /**
   * Retorna a estrutura do objeto a ser enviado para salvar a comissão de membros
   */
  private getComissaoMembro(): any {
    // Caso o 'idCauUf' dos integrantes esteja desatualizado, em relação à UF selecionada.
    const cauUf = this.eleicao.cauUf;
    _.forEach(this.membros, item => { item.idCauUf = cauUf; item.membroSubstituto.idCauUf = cauUf });
    _.forEach(this.coordenadores, item => { item.idCauUf = cauUf; item.membroSubstituto.idCauUf = cauUf });

    return {
      "coordenadores": this.coordenadores,
      "membros": this.membros,
      "justificativa": this.justificativa,
      "informacaoComissaoMembro": {
        "id": this.eleicao.id
      },
      "calendarioId": this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario.id
    };
  }

  /**
   * Retorna a msg dependendo da ação que o componente esta realizando ao salvar
   */
  private getMsgSalvar(): string {
    let msg = 'MSG_SUCESSO_CADASTRAR_MEMBROS';
    if (this.acaoSistema.isAcaoAlterar()) {
      msg = 'MSG_SUCESSO_ALTERAR_MEMBROS';
    }
    return msg;
  }

  /**
   * Verifica se existe alguma pendencias nos membros
   */
  private hasPendencia(): boolean {
    let pendencias: boolean = false;
    this.coordenadores.forEach(membro => {
      if (this.isMembroInseridoPendente(membro)) {
        pendencias = true;
      }
    });
    this.membros.forEach(membro => {
      if (this.isMembroInseridoPendente(membro)) {
        pendencias = true;
      }
    });
    if (!this.isCampoUfHabilitadoEdicao() && Number(this.usuario.cauUf.id) !== Number(this.eleicao.cauUf)) {
      pendencias = true;
    }
    return pendencias;
  }

  /**
   * Faz validação se membro ou substituto possuí pendências
   * @param membro
   */
  public isMembroInseridoPendente(membro: any): boolean {
    const isMembroPendente = membro.inserido && membro.pendencias && membro.pendencias.length > 0;
    const isMembroSubstPendente = (
      membro.membroSubstituto &&
      membro.membroSubstituto.inserido &&
      membro.membroSubstituto.pendencias &&
      membro.membroSubstituto.pendencias.length > 0
    );

    return isMembroPendente || isMembroSubstPendente;
  }

  /**
   * Submita o formulario para validar os dados, podendo chamar a modal de justificativa ou o metodo de salvar
   */
  public submit(form: NgForm, template): void {
    this.submitted = true;
    if (form.valid) {
      if (this.configuracaoEleicao.situacaoMajoritario && !this.isMajoritariamenteConselheiro()) {
        this.messageService.addMsgDanger('MSG_MAJORITARIAMENTE_CONSELHEIROS');
      } else if (!this.configuracaoEleicao.situacaoMajoritario && this.isMajoritariamenteConselheiro()) {
        this.messageService.addMsgDanger('MSG_NAO_MAJORITARIAMENTE_CONSELHEIROS');
      } else {
        this.acaoSistema.isAcaoAlterar() ? this.salvarComConfirmar() : this.salvar();
      }
    }
  }

  /**
   * Define a justificativa chamando o metodo de salvar.
   * @param form
   */
  public salvarComJustificativa(form: NgForm): void {
    this.submitted = true;
    if (form.valid) {
      this.modalRef.hide();
      this.acaoSistema.isAcaoAlterar() ? this.salvarComConfirmar() : this.salvar();
    }
  }

  /**
   * Método utilizado pelo 'select' para comparar values de 'options'.
   * @param optionValue
   * @param selectedValue
   */
  public compareFn(optionValue, selectedValue) {
    return optionValue && selectedValue ? optionValue.id === selectedValue.id : optionValue === selectedValue;
  }

  /**
  * Verifica se existem pendências relacionadas ao Membro.
  *
  * @param membro
  */
  public hasPendencias(membro) {
    return (membro.pendencias && membro.pendencias.length > 0);
  }

  /**
   * Exige a confirmação do usuario para salvar
   */
  private salvarComConfirmar(): void {
    this.messageService.addConfirmYesNo('MSG_CONFIRMA_ALTERAR_MEMBROS', () => {
      this.salvar();
    });
  }

  private salvar(): void {
    let comissaoMembrosEleicao = this.getComissaoMembro();
    this.eleicaoClientService.saveComissaoMembro(comissaoMembrosEleicao).subscribe(
      data => {
        let msg = this.getMsgSalvar();
        this.messageService.addMsgSuccess(msg);
        let idCalendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario.id;
        if (this.acaoSistema.isAcaoIncluir()) {
          this.router.navigate([`/eleicao/membros-comissao/${idCalendario}/visualizar/`]);
        }
        this.conclusao.emit(null);
      },
      error => {
        this.messageService.addMsgDanger(error.message);
      }
    );
  }

  /**
   * Atualiza a lista de UF's, para que somente UF's que ainda não tiveram membros de comissão cadastrados possam
   * estar diponível para Assesssor CEN (cadastrá-los).
   */
  private obterUfsMembrosNaoCadastradosPorCalendario(): void {
    if (this.isCampoUfHabilitadoEdicao()) {
      let calendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario;
      this.cauUFService.getFiliaisMembrosNaoCadastradosPorCalendario(calendario.id).subscribe(data => {
        this.cauUfs = data;
      }, error => {
        this.messageService.addMsgDanger(error.message);
      })
    }
  }

  /**
   * Verifica se o campo 'UF' pode estar habilitado para edição.
   * Neste caso, apenas se for a ação de 'Incluir' e o usuário logado seja um 'Assessor CEN'.
   */
  public isCampoUfHabilitadoEdicao(): boolean {
    return this.acaoSistema.isAcaoIncluir() && this.isUsuarioLogadoAssessorCen();
  }

  /**
   * Verifica se o usuário logado é um 'Assessor CEN'.
   */
  public isUsuarioLogadoAssessorCen(): boolean {
    return this.usuario.cauUf.id == Constants.ID_CAUBR;
  }
}
