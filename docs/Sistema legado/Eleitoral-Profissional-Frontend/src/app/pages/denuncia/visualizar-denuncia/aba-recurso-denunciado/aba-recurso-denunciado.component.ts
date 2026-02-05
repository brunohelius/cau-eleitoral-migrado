import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalContrarrazaoComponent } from '../../julgamento/modal-contrarrazao/modal-contrarrazao.component';
import { MessageService } from '@cau/message';

@Component({
  selector: 'aba-recurso-denunciado',
  templateUrl: './aba-recurso-denunciado.component.html',
  styleUrls: ['./aba-recurso-denunciado.component.scss']
})
export class AbaRecursoDenunciadoComponent implements OnInit {

 
  @Input('dadosDenuncia') denuncia;
  @Input('usuario') usuario;

  public modalCadastroContrarrazao: BsModalRef;
  public descricaoRecurso = this.messageService.getDescription('LABEL_DESCRICAO_RECURSO_RECONSIDERACAO');
  public descricaoContrarrazao = this.messageService.getDescription('LABEL_DESCRICAO_CONTRARRAZAO');


  constructor(
    public modalService: BsModalService,
    public messageService: MessageService,
  ) { }

  ngOnInit() {
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
   * Verifica se Pode exibir o botão
   */
  public isExibirBotao() {
    return !this.hasContrarrazaoDenunciante()  && this.hasRecursoDenunciado() && !this.hasParametroContrarrazaoForaPrazo() && this.usuario.isDenunciante;
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
      descricao: this.denuncia.contrarrazaoDenunciante.descricaoRecurso,
      nomeResponsavel: this.denuncia.contrarrazaoDenunciante.responsavel,
      arquivos: this.denuncia.contrarrazaoDenunciante.arquivosContrarrazao,
      isSigiloso: this.denuncia.isSigiloso
    };
  }

  /**
   * Abre o formulário de  cadastro da contrarrazão.
   */
  public abrirModalCadastroContrarrazao(): void {
    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.idTipoDenuncia
    };

    this.modalCadastroContrarrazao = this.modalService.show(ModalContrarrazaoComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }
  
  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

}
