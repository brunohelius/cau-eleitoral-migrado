import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { ModalInadmitirDenunciaComponent } from './modal-inadmitir-denuncia/modal-inadmitir-denuncia.component';
import { StringService } from 'src/app/string.service';

@Component({
  selector: 'aba-acompanhar-denuncia',
  templateUrl: './aba-acompanhar-denuncia.component.html',
  styleUrls: ['./aba-acompanhar-denuncia.component.scss']
})
export class AbaAcompanharDenunciaComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;
  @Input('tipoMembroComissao') tipoMembroComissao;

  public configuracaoCkeditor: any = {};
  public modalAdmitirDenunciaModal: BsModalRef;
  public modalInadmitirDenuncia: BsModalRef;
  public narracaoFatosSimpleText = '';

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    this.narracaoFatosSimpleText = StringService.getPlainText(this.denuncia.narracaoFatosSimpleText).slice(0, -1);
  }

  /**
   * Recupera o objeto de denunciado
   */
  public getDenunciado() {
    return this.denuncia.denunciado ? this.denuncia.denunciado : null;
  }

  /**
   * Abre o formulário de admissão.
   */
  public abrirFormularioAdmissao(modal: TemplateRef<any>): void {
    this.modalAdmitirDenunciaModal = this.modalService.show(modal, Object.assign({}, { class: 'modal-lg' }))
  }

  /**
   * Abre o formulário de inadmitir.
   */
  public abrirModalInadmitirDenuncia(denuncia: any): void {
    const initialState = { idDenuncia: denuncia.idDenuncia, nuDenuncia: denuncia.numeroDenuncia };

    this.modalInadmitirDenuncia = this.modalService.show(ModalInadmitirDenunciaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   * Fecha modal de admissao
   */
  public cancelaAdmitirDenuncia(event: Event): void {
    this.modalAdmitirDenunciaModal.hide();
  }

  /**
   * Fecha o formulário de admissão.
   */
  public fecharFormularioAdmissao(): void {
    this.modalAdmitirDenunciaModal.hide();
  }

  /**
   * Retorna a contagem de caracteres da narração de fatos.
   */
  public getContagemNarracaoFatos = () => {
    return Constants.TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA - this.narracaoFatosSimpleText.length;
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivo(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'narracaoFatos',
      removePlugins: 'elementspath',
      toolbar: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
        { name: 'links', items: ['Link'] },
        { name: 'insert', items: ['Image'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
      ],
    };
  }

  /**
   * Verifica se o usuario é um coordenador da comissao e da UF.
   * 
   * @param tipoMembroComissao 
   * @param idCauUf 
   */
  public isCoordenadorDenuncia(tipoMembroComissao: any, idCauUf: any): boolean {
    let isCoordenador = false;

    if (tipoMembroComissao.isCoordenadorCEN && (idCauUf == Constants.CAUBR_ID || idCauUf == Constants.IES_ID)) {
      isCoordenador = true;
    }

    if (tipoMembroComissao.isCoordenadorCE) {
      tipoMembroComissao.idsCauUfCE.map(function (idCauUfCE) {
        if (idCauUfCE == idCauUf) {
          isCoordenador = true;
        }
      });
    }

    return isCoordenador;
  }

  /**
   * Verifica se a Denuncia está em Analise de Admissibilidade.
   * 
   * @param denuncia 
   */
  public isDenunciaEmAnaliseAdmissibilidade(denuncia: any) {
    return denuncia.idSituacaoDenuncia == Constants.SITUACAO_ANALISE_ADMISSIBILIDADE;
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }
}
