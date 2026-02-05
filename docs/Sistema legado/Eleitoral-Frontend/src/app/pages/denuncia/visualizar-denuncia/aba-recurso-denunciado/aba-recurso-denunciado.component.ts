import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { ModalJulgamentoRecursoComponent } from '../../shared/modal-julgamento-recurso/modal-julgamento-recurso.component';


@Component({
  selector: 'aba-recurso-denunciado',
  templateUrl: './aba-recurso-denunciado.component.html',
  styleUrls: ['./aba-recurso-denunciado.component.scss']
})
export class AbaRecursoDenunciadoComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;

  public modalCadastroContrarrazao: BsModalRef;
  public modalJulgamentoSegundaInstancia: BsModalRef;
  public descricaoRecurso = this.messageService.getDescription('LABEL_DESCRICAO_RECURSO_RECONSIDERACAO');
  public descricaoContrarrazao = this.messageService.getDescription('LABEL_DESCRICAO_CONTRARRAZAO');

  constructor(
    public modalService: BsModalService,
    public messageService: MessageService,
  ) { }

  ngOnInit() {
  }

  /**
   * Verifica os parametros para julgamento de recurso
   */
  public hasParametrosAcessoParaJulgamentoSegundaInstancia = () => {
    return this.denuncia.isAssessorCEN && (this.denuncia.idSituacaoDenuncia != Constants.SITUACAO_DENUNCIA_TRANSITADA_JULGADO && this.denuncia.idSituacaoDenuncia != Constants.STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA && this.denuncia.idSituacaoDenuncia != Constants.STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO)
      && ((this.denuncia.recursoDenunciado != undefined || this.denuncia.recursoDenunciante != undefined)
      && (!this.denuncia.has_contrarrazao_denunciado_dentro_prazo && !this.denuncia.has_contrarrazao_denunciante_dentro_prazo));
  }

  /**
   * Verifica se o recurso está fora do prazo
   */
  public hasParametrosForaPrazo() {
    return !this.hasRecursoDenunciado();
  }

  /**
   * Verifica se a contrarrazão está fora do prazo
   */
  public hasParametroContrarrazaoForaPrazo() {
    return !this.denuncia.hasContrarrazaoDenuncianteDentroPrazo;
  }

  /**
   * Verifica se Pode exibir a mensagem
   */
  public isExibirMensagemNaoHouveContrarrazao() {
    return !this.hasContrarrazaoDenunciante() && this.hasParametroContrarrazaoForaPrazo() && this.hasRecursoDenunciado() ;
  }

  /**
   * Verifica se existe recurso do denunciado.
   */
  public hasRecursoDenunciado() {
    return this.denuncia.recursoDenunciado != undefined;
  }

  /**
   * Verifica se existe contrarrazão do denunciante.
   */
  public hasContrarrazaoDenunciante() {
    return this.denuncia.contrarrazaoDenunciante != undefined;
  }

  /**
   * Retorna o recurso do denunciado.
   */
  public getRecursoDenunciado() {
    return {
      nuDenuncia: this.denuncia.numeroDenuncia,
      data: this.denuncia.recursoDenunciado.data,
      arquivos: this.denuncia.recursoDenunciado.arquivos,
      descricao: this.denuncia.recursoDenunciado.descricaoRecurso,
      nomeResponsavel: this.denuncia.recursoDenunciado.responsavel,
    };
  }

  /**
   * Retorna o contrarrazão do denunciante.
   */
  public getContrarrazaoDenunciante() {
    return {
      nuDenuncia: this.denuncia.numeroDenuncia,
      data: this.denuncia.contrarrazaoDenunciante.data,
      arquivos: this.denuncia.contrarrazaoDenunciante.arquivosContrarrazao,
      descricao: this.denuncia.contrarrazaoDenunciante.descricaoRecurso,
      nomeResponsavel: this.denuncia.contrarrazaoDenunciante.responsavel
    };
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalJulgamentoSegundaInstancia(): void {
    if (Constants.SITUACAO_DENUNCIA_EM_RELATORIA == this.denuncia.idSituacaoDenuncia) {
      this.messageService.addMsgWarning(this.messageService.getDescription('MSG_AGUARDE_PARECER_FINAL_RELATOR_DENUNCIA'));
      return;
    }

    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.idTipoDenuncia
    };

    this.modalJulgamentoSegundaInstancia = this.modalService.show(ModalJulgamentoRecursoComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }
}
