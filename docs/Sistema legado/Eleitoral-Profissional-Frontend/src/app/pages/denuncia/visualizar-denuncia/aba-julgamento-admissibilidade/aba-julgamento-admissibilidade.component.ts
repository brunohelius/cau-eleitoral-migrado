import { Component, EventEmitter, Input, OnInit } from '@angular/core';
import { Constants } from '../../../../constants.service';
import { StringService } from '../../../../string.service';
import { DenunciaClientService, StatusDenuncia } from '../../../../client/denuncia-client/denuncia-client.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ModalRecursoAdmissibilidadeComponent } from '../aba-recurso-admissibilidade/modal-recurso-admissibilidade/modal-recurso-admissibilidade.component';
import { ModalApresentarDefesaComponent } from '../aba-analise-admissibilidade/modal-apresentar-defesa/modal-apresentar-defesa.component';
import { ModalInserirRelatorJulgarAdmissibilidadeComponent } from '../aba-analise-admissibilidade/modal-inserir-relator-julgar-admissibilidade/modal-inserir-relator-julgar-admissibilidade.component';

@Component({
  selector: 'app-aba-julgamento-admissibilidade',
  templateUrl: './aba-julgamento-admissibilidade.component.html',
  styleUrls: ['./aba-julgamento-admissibilidade.component.scss']
})
export class AbaJulgamentoAdmissibilidadeComponent implements OnInit {

  @Input() denuncia;
  @Input() usuario;

  public modalApresentarDefesa: BsModalRef;
  public modalRelator: BsModalRef;

  configuracaoCkeditor = {
    title: 'dsAdmissibilidade',
    removePlugins: 'elementspath',
    toolbar: [
      { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
      { name: 'links', items: ['Link'] },
      { name: 'insert', items: ['Image'] },
      { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
    ],
  };

  public descricaoDespachoSimpleText = '';

  prazoRecursoAdmissibilidade = false;

  denunciaUsuarioLogado = false;

  denunciaEmRecurso = false;

  hasRecursoAdmissibilidade = false;

  constructor(
    private denunciaService: DenunciaClientService,
    private messageService: MessageService,
    private modalService: BsModalService,
  ) { }

  ngOnInit() {
    this.descricaoDespachoSimpleText = StringService.getPlainText(this.denuncia.julgamentoAdmissibilidade.descricao).slice(0, -1);
    this.validaPrazoRecursoAdmissibilidade();
    this.denunciaEmRecurso = this.denuncia.idSituacaoDenuncia == StatusDenuncia.EM_RECURSO.id;
  }

  /**
   * Abre o formulário de inadmitir.
   */
  public abrirModalApresentarDefesa(): void {
    const initialState = { 
      idDenuncia: this.denuncia.idDenuncia, 
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.tipoDenuncia
    };

    this.modalApresentarDefesa = this.modalService.show(ModalApresentarDefesaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   * Valida se tem acesso para apresentar defesa
   */
  public hasAcessoApresentarDefesa(): boolean {
    return (this.denuncia.defesaApresentada == undefined && this.isUsuarioDenunciadoResponsavelChapa()) 
      && (!this.denuncia.hasDefesaPrazoEncerrado && this.isAguardandoDefesaDenuncia()) 
      && !this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso;
  }

  /**
   * Valida se a situação da denuncia é aguardando defesa
   */
  public isAguardandoDefesaDenuncia(): boolean {
    return this.denuncia.idSituacaoDenuncia === Constants.SITUACAO_DENUNCIA_AGUARDANDO_DEFESA;
  }

  /**
   * Verifica se o usuario é o denunciado ou responsavel pela chapa.
   * 
   * @param tipoMembroComissao 
   * @param idCauUf 
   */
  public isUsuarioDenunciadoResponsavelChapa(): boolean {
    return this.usuario.isDenunciado || this.usuario.isResponsavelChapa;
  }

  public getContagemDespachoAdmissibilidade() {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoDespachoSimpleText.length;
  }

  public downloadArquivo(event: EventEmitter<any>, idArquivo) {
    return this.denunciaService.downloadArquivoJulgamentoAdmissibilidade(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  public recursoAdmissibilidade() {
    const initialState = {
      denuncia: this.denuncia,
    };

    let event = this.modalService.show(ModalRecursoAdmissibilidadeComponent, Object.assign({}, {}, { class: 'modal-lg', initialState }));

    event.content.recursoRealizado.subscribe((data) => {
      this.hasRecursoAdmissibilidade = true;
      window.location.reload();
    });
  }

  public validaPrazoRecursoAdmissibilidade() {

    this.denunciaService.verificaPrazoRecursoAdmissibilidade(this.denuncia.julgamentoAdmissibilidade.id).subscribe((data) => {

      this.prazoRecursoAdmissibilidade = data.parazoRecurso;
      this.denunciaUsuarioLogado = data.denunciaUsuarioLogado;
      this.hasRecursoAdmissibilidade = data.recursoId != 0;
    });
  }

  /**
   * Verifica se o julgamento foi provido
   */
  public isJulgamentoProvido() {
    return Constants.TIPO_JULGAMENTO_ADMISSIBILIDADE_PROVIDO === this.denuncia.julgamentoAdmissibilidade.idTipoJulgamento
  }

  /**
   * Verifica se o julgamento foi improvido
   */
  public isJulgamentoImprovido() {
    return Constants.TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIDO === this.denuncia.julgamentoAdmissibilidade.idTipoJulgamento
  }

  /**
   * Retorna a descrição resumida do tipo de julgamento
   */
  public getTipoJulgamento() {
    let tipoJulgamento = this.messageService.getDescription('LABEL_IMPROVIDO');
    if(this.isJulgamentoProvido()) {
      tipoJulgamento = this.messageService.getDescription('LABEL_PROVIDO');;
    }
    return tipoJulgamento;
  }


  /**
   * Realiza a chamada da modal de inserção do relator, verificando se é possivel esta inserção.
   */
  public inserirRelator(): void {
    this.denunciaService.createRelator(this.denuncia.idDenuncia).subscribe(result => {
      const initialState = {
        denuncia: this.denuncia,
        relatores: result.membros_comissao,
      };
      this.modalRelator = this.modalService.show(
        ModalInserirRelatorJulgarAdmissibilidadeComponent,
        Object.assign({}, {}, {class: 'modal-lg', initialState})
      );
    });
  }

  /**
   * Valida se à ação de inserir o relator pode ser visualizada
   */
  public isPossivelInserirRelator(): boolean {
    const julgamentoAdmissibilidade = this.denuncia.julgamentoAdmissibilidade || {};
    const recursoAdmissibilidade = julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade || {};
    const julgamentoRecursoAdmissibilidade = recursoAdmissibilidade.julgamentoRecurso || false;
    
    return this.denuncia.condicao.posso_inserir_relator && !julgamentoRecursoAdmissibilidade;
  }

  public voltar() {
    window.history.back();
  }
}
