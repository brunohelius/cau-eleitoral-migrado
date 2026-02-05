import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalContrarrazaoComponent } from '../../julgamento/modal-contrarrazao/modal-contrarrazao.component';
import { MessageService } from '@cau/message';

@Component({
  selector: 'aba-recurso-denunciante',
  templateUrl: './aba-recurso-denunciante.component.html',
  styleUrls: ['./aba-recurso-denunciante.component.scss']
})

export class AbaRecursoDenuncianteComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;
  @Input('usuario') usuario;

  public modalCadastroContrarrazao: BsModalRef;
  public descricaoRecurso = this.messageService.getDescription('LABEL_DESCRICAO_RECURSO_RECONSIDERACAO');
  public descricaoContrarrazao = this.messageService.getDescription('LABEL_DESCRICAO_CONTRARRAZAO');

  constructor(
    public modalService: BsModalService,
    public messageService: MessageService
  ) { }

  ngOnInit() {
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
   * Verifica se Pode exibir o botão
   */
  public isExibirBotao() {
    return !this.hasContrarrazaoDenunciado()  && this.hasRecursoDenunciante() && !this.hasParametroContrarrazaoForaPrazo() && (this.usuario.isDenunciado || this.usuario.isResponsavelChapa);
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
      nomeResponsavel: this.denuncia.recursoDenunciante.responsavel,
      isSigiloso: this.denuncia.isSigiloso
    };
  }

  /**
   * Retorna o contrarrazão do denunciado.
   */
  public getContrarrazaoDenunciado() {
    return {
      nuDenuncia: this.denuncia.numeroDenuncia,
      data: this.denuncia.contrarrazaoDenunciado.data,
      descricao: this.denuncia.contrarrazaoDenunciado.descricaoRecurso,
      nomeResponsavel: this.denuncia.contrarrazaoDenunciado.responsavel,
      arquivos: this.denuncia.contrarrazaoDenunciado.arquivosContrarrazao,
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
