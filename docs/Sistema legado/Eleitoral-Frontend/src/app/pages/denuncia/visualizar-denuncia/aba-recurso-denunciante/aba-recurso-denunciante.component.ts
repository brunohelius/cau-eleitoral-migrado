import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { ModalJulgamentoRecursoComponent } from '../../shared/modal-julgamento-recurso/modal-julgamento-recurso.component';

@Component({
  selector: 'aba-recurso-denunciante',
  templateUrl: './aba-recurso-denunciante.component.html',
  styleUrls: ['./aba-recurso-denunciante.component.scss']
})
export class AbaRecursoDenuncianteComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;

  public modalCadastroContrarrazao: BsModalRef;
  public modalJulgamentoSegundaInstancia: BsModalRef;
  public descricaoRecurso = this.messageService.getDescription('LABEL_DESCRICAO_RECURSO_RECONSIDERACAO');
  public descricaoContrarrazao = this.messageService.getDescription('LABEL_DESCRICAO_CONTRARRAZAO');

  constructor(
    public modalService: BsModalService,
    public messageService: MessageService
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
    return !this.hasRecursoDenunciante() && !this.denuncia.prazoRecursodenuncia;
  }

  /**
   * Verifica se a contrarrazão está fora do prazo
   */
  public hasParametroContrarrazaoForaPrazo() {
    return !this.denuncia.hasContrarrazaoDenunciadoDentroPrazo;
  }

  /**
   * Verifica se Pode exibir a mensagem
   */
  public isExibirMensagemNaoHouveContrarrazao() {
    return !this.hasContrarrazaoDenunciado() && this.hasParametroContrarrazaoForaPrazo() && this.hasRecursoDenunciante() ;
  }

  /**
   * Verifica se existe recurso do denunciante.
   */
  public hasRecursoDenunciante() {
    return this.denuncia.recursoDenunciante != undefined;
  }

  /**
   * Verifica se existe contrarrazão do denunciado.
   */
  public hasContrarrazaoDenunciado() {
    return this.denuncia.contrarrazaoDenunciado != undefined;
  }

  /**
   * Retorna o recurso do denunciante.
   */
  public getRecursoDenunciante() {
    return {
      nuDenuncia: this.denuncia.numeroDenuncia,
      data: this.denuncia.recursoDenunciante.data,
      arquivos: this.denuncia.recursoDenunciante.arquivos,
      descricao: this.denuncia.recursoDenunciante.descricaoRecurso,
      nomeResponsavel: this.denuncia.recursoDenunciante.responsavel
    };
  }

  /**
   * Retorna o contrarrazão do denunciado.
   */
  public getContrarrazaoDenunciado() {
    return {
      nuDenuncia: this.denuncia.numeroDenuncia,
      data: this.denuncia.contrarrazaoDenunciado.data,
      arquivos: this.denuncia.contrarrazaoDenunciado.arquivosContrarrazao,
      descricao: this.denuncia.contrarrazaoDenunciado.descricaoRecurso,
      nomeResponsavel: this.denuncia.contrarrazaoDenunciado.responsavel
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
