import { Component, OnInit, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

import * as _ from "lodash";
import * as deepEqual from "deep-equal";
import { MessageService } from '@cau/message';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { CabecalhoEmailClientService } from 'src/app/client/cabecalho-email/cabecalho-email-client.service';
import { NgForm } from '@angular/forms';
import { AcaoSistema } from 'src/app/app.acao';
import { Constants } from 'src/app/constants.service'
import { DomSanitizer } from '@angular/platform-browser';


/**
 * Componente de formulário de Cadastro de Cabeçalho de E-mail.
 *
 * @author Squadra Tecnologia
 */
@Component({
  selector: 'form-cabecalho-email',
  templateUrl: './form-cabecalho-email.component.html'
})
export class FormCabecalhoEmailComponent implements OnInit {
  public cabecalho: any = {};
  public _cabecalho: any = {};
  public configuracaoCkeditor: any;
  public configuracaoCkeditorTextoCabecalho: any;
  public configuracaoCkeditorTextoRodape: any;
  public ufs: Array<any>;
  public modalVisualizarCabecalhoEmail: BsModalRef;
  public submitted: boolean;
  public acaoSistema: AcaoSistema;
  private totalCorpoEmailVinculado: number;
  private arquivoCabecalho: any;
  public dropdownSettingsUF: any;
  public imgURLCabecalhoPreview: any;
  public imgURLRodapePreview: any;
  private auxCabecalhoAtivo: boolean;
  private auxRodapeAtivo: boolean;
  public ufsSelecionadas = [];
  public _ufsSelecionadas = [];

  /**
   * Método contrutor da classe
   * @param route
   * @param router
   * @param messageService
   * @param modalService
   * @param cabecalhoEmailService
   * @param sanitizer
   */
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private messageService: MessageService,
    private modalService: BsModalService,
    private cabecalhoEmailService: CabecalhoEmailClientService,
    private sanitizer: DomSanitizer
  ) {
    this.ufs = this.route.snapshot.data['ufs'];
    this.acaoSistema = new AcaoSistema(route);
    if (!this.acaoSistema.isAcaoIncluir()) {
      this.cabecalho = this.route.snapshot.data['cabecalhoEmail'];
      this.totalCorpoEmailVinculado = this.cabecalho.totalCorpoEmailVinculado;
      this.auxCabecalhoAtivo = this.cabecalho.isCabecalhoAtivo;
      this.auxRodapeAtivo = this.cabecalho.isRodapeAtivo;
    }
  }

  /**
   * Função inicializada quando o componente carregar.
   */
  ngOnInit() {
    this.submitted = false;
    if (this.acaoSistema.isAcaoIncluir()) {
      this.cabecalho = {
        id: '',
        titulo: '',
        nomeImagemCabecalho: '',
        nomeImagemFisicaCabecalho: '',
        imagemCabecalho: '',
        textoCabecalho: '',
        isCabecalhoAtivo: true,
        nomeImagemRodape: '',
        nomeImagemFisicaRodape: '',
        imagemRodape: '',
        textoRodape: '',
        isRodapeAtivo: true,
        uf: { id: '' },
        cabecalhoEmailUfs: [],
      };
      this.totalCorpoEmailVinculado = 0;
    } else {

      if (this.cabecalho && this.cabecalho.cabecalhoEmailUfs) {
        this.cabecalho.cabecalhoEmailUfs.forEach(cabecalhoUf => {
          this.ufsSelecionadas.push(cabecalhoUf.uf);
        });
      }

      this.totalCorpoEmailVinculado = this.cabecalho.corpoEmails.length;
      this.imgURLCabecalhoPreview = this.cabecalho.imagemCabecalho;
      this.imgURLRodapePreview = this.cabecalho.imagemRodape;
    }
    this.inicializarConfiguracaoCkeditor();
    this.inicializarDropdownSettings();
    this._cabecalho = _.cloneDeep(this.cabecalho);
    this._ufsSelecionadas = _.cloneDeep(this.ufsSelecionadas);
  }

  /**
   * Inicializa a configuração do CKeditor apresentado na tela.
   */
  private inicializarConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker'], items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'insert', items: ['Image', 'Table'] }
      ],
      title: false
    };
    this.configuracaoCkeditorTextoCabecalho = this.configuracaoCkeditor;
    this.configuracaoCkeditorTextoRodape = this.configuracaoCkeditor;
  }


  /**
   * Salvar Cabeçalho
   */
  public salvar() {
    this.submitted = true;
    this.fecharModalVisualizarCabecalhoEmail();

    if (this.acaoSistema.isAcaoAlterar()) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_ALTERAR_DADOS', () => {
        this.salvarDados();
      });
    } else {
      this.salvarDados();
    }
  }

  /**
   * Chamar service para salvar.
   */
  public salvarDados() {
    this.cabecalho.cabecalhoEmailUfs = [];
    this.ufsSelecionadas.forEach(uf => {
      let cabecalhoEmailUf = {
        'id': undefined,
        'uf': {
          'id': uf.id
        }
      };

      this.cabecalho.cabecalhoEmailUfs.push(cabecalhoEmailUf);
    });

    this.cabecalhoEmailService.salvar(this.cabecalho).subscribe(
      () => {
        let msg: string = this.acaoSistema.isAcaoIncluir() ? 'MSG_DADOS_CADASTRADOS_COM_SUCESSO' : 'LABEL_DADOS_ALTERADOS_SUCESSO';
        this.messageService.addMsgSuccess(msg);
        this.fecharModalVisualizarCabecalhoEmail();
        this.router.navigate(['email', 'cabecalho', 'listar']);
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );

  }

  /**
   * Validar Arquivos de Rodapé e Cabeçalho.
   */
  public isArquivosValidos(): boolean {
    let valido: boolean = true;
    if (this.cabecalho.isCabecalhoAtivo && this.imgURLCabecalhoPreview == undefined) {
      this.messageService.addMsgWarning('MSG_ARQUIVO_CABECALHO_NAO_FOI_INCLUIDO');
      valido = false;
    }

    if (this.cabecalho.isRodapeAtivo && this.imgURLRodapePreview == undefined) {
      this.messageService.addMsgWarning('MSG_ARQUIVO_RODAPE_NAO_FOI_INCLUIDO');
      valido = false;
    }
    return valido;
  }

  /**
   * Voltar a página de listar.
   */
  public voltar() {
    if (!this.acaoSistema.isAcaoVisualizar() && this.hasModificacao()) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_VOLTAR_DADOS_NAO_SALVOS_SERAO_PERDIDOS', () => {
        this.router.navigate(['email', 'cabecalho', 'listar']);
      });
    } else {
      this.router.navigate(['email', 'cabecalho', 'listar']);
    }
  }

  /**
  * Valida se existe modificação no objeto de Cabecalho de E-mail.
  */
  public hasModificacao(): boolean {
    return !deepEqual(this._cabecalho, this.cabecalho) || !deepEqual(this._ufsSelecionadas, this.ufsSelecionadas);
  }

  /**
   * Retorna HTML Conviavel para exibição em tela.
   * Leia mais em: https://netbasal.com/angular-2-security-the-domsanitizer-service-2202c83bd90
   * 
   */
  public getHtmlConfiavel(html: string): any {
    return this.sanitizer.bypassSecurityTrustHtml(html);
  }
  /**
   * Limpa o valor apresentado no campo do ckeditor.
   */
  public limparCampo(nomeCampo): void {
    if (nomeCampo == 'descricaoParametrizacaoCabecalho') {
      this.cabecalho.textoCabecalho = undefined;
      this.cabecalho.nomeImagemCabecalho = undefined;
      this.cabecalho.imagemCabecalho = undefined;
      this.imgURLCabecalhoPreview = undefined;
    }

    if (nomeCampo == 'descricaoParametrizacaoRodape') {
      this.cabecalho.textoRodape = undefined;
      this.cabecalho.nomeImagemRodape = undefined;
      this.cabecalho.imagemRodape = undefined;
      this.imgURLRodapePreview = undefined;
    }
  }

  /**
   * Método responsável por apresentar e-mail selecionado em modal.
   * @param template 
   * @param idEmail 
   */
  public visualizarCabecalhoEmail(template: TemplateRef<any>, form: NgForm) {
    this.submitted = true;
    if (this.isArquivosValidos() && form.valid) {
      this.modalVisualizarCabecalhoEmail = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }))
    }
  }

  /**
   * Prenchimento dos campos relacionados a imagem de Rodapé de E-mail.
   * @param arquivoEvent
   */
  public uploadImagemCabecalho(arquivoEvent) {
    if (arquivoEvent.size <= Constants.TAMANHO_LIMITE_ARQUIVO) {
      let arquivoTO = { file: arquivoEvent };
      this.cabecalhoEmailService.validarImagemCabecalho(arquivoTO).subscribe(() => {
        this.cabecalho.nomeImagemCabecalho = arquivoEvent.name;
        this.cabecalho.imagemCabecalho = arquivoEvent;
        this.setImgURLCabecalhoPreview(arquivoEvent);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
    else {
      this.messageService.addMsgDanger('MSG_MAXIMO_TAMANHO_PERMITIDO_ARQUIVOS_CABECALHO_RODAPE');
    }

  }

  /**
   * Preencher variável de exibição de imagem de upload de cabeçalho.
   * @param file
   */
  private setImgURLCabecalhoPreview(file) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (_event) => {
      this.imgURLCabecalhoPreview = reader.result;
    }
  }

  /**
   * Prenchimento dos campos relacionados a imagem de cabeçalho de E-mail.
   * @param arquivoEvent
   */
  public uploadImagemRodape(arquivoEvent) {
    if (arquivoEvent.size <= Constants.TAMANHO_LIMITE_ARQUIVO) {
      let arquivoTO = { file: arquivoEvent };
      this.cabecalhoEmailService.validarImagemCabecalho(arquivoTO).subscribe(() => {
        this.cabecalho.nomeImagemRodape = arquivoEvent.name;
        this.cabecalho.imagemRodape = arquivoEvent;
        this.setImgURLRodapePreview(arquivoEvent);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
    else {
      this.messageService.addMsgDanger('MSG_MAXIMO_TAMANHO_PERMITIDO_ARQUIVOS_CABECALHO_RODAPE');
    }

  }

  /**
   * Prenchimento dos campos relacionados a imagem de Rodapé de E-mail.
   * @param arquivoEvent
   */
  private setImgURLRodapePreview(file) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = (_event) => {
      this.imgURLRodapePreview = reader.result;
    }
  }

  /**
   * Método responsável por fechar modal de apresentação de e-mail.
   */
  public fecharModalVisualizarCabecalhoEmail(): void {
    this.modalVisualizarCabecalhoEmail.hide();
  }

  /**
   * Verificar se cabeçalho é desabilitado.
   */
  public isCabecalhoAtivoDisabled(): boolean {
    return ((this.hasCorpoEmailVinculado() && this.cabecalho.isCabecalhoAtivo) || this.acaoSistema.isAcaoVisualizar());
  }

  /**
   * Verificar se rodapé é desabilitado.
   */
  public isRodapeoAtivoDisabled(): boolean {
    return ((this.hasCorpoEmailVinculado() && this.cabecalho.isRodapeAtivo) || this.acaoSistema.isAcaoVisualizar());
  }

  /**
   * Retorna verdadeiro se existe corpos de e-mail vinculado ao cabeçalho.
   * @return boolean
   */
  public hasCorpoEmailVinculado(): boolean {
    return this.totalCorpoEmailVinculado > 0;
  }

  /**
   * Verificar se corpo de e-mail vinculados.
   * @param campo
   * @param ativo
   */
  public verificarCorpoEmailVinculado(campo: string, ativo: boolean) {
    if (ativo && this.hasCorpoEmailVinculado() && !this.acaoSistema.isAcaoVisualizar()) {
      if (campo == Constants.CABECALHO_EMAIL_NOME_CAMPO_RODAPE_ATIVO) {
        if (this.auxCabecalhoAtivo) {
          this.messageService.addMsgDanger('MSG_RODAPE_NAO_PODE_SER_DESATIVADO', this.getAssuntoEmail());
        } else {
          this.auxCabecalhoAtivo = true;
        }
      }

      if (campo == Constants.CABECALHO_EMAIL_NOME_CAMPO_CABECALHO_ATIVO) {
        if (this.auxRodapeAtivo) {
          this.messageService.addMsgDanger('MSG_CABECALHO_NAO_PODE_SER_DESATIVADO', this.getAssuntoEmail());
        } else {
          this.auxRodapeAtivo = true;
        }
      }
    }

  }

  /**
   * Remove a UF informada.
   * @param item
   */
  public onRemoveUf(item: any) {
    this.ufsSelecionadas = _.remove(this.ufsSelecionadas, function (uf) {
      return uf['id'] !== item.id
    });
  }

  /**
   * Adiciona todas as UFs á coleção.
   */
  public onSelectAllUfs(): void {
    this.ufsSelecionadas = [];
    this.ufs.forEach(uf => {
      this.ufsSelecionadas.push(uf);
    });
  }

  /**
   * Remove todas as UFs que foram selacionadas.
   */
  public onRemoveAllUfs() {
    this.ufsSelecionadas = [];
  }

  /**
   * Retorna string dos assuntos de Corpo de E-mail vinculados ao Cabeçalho.
   */
  private getAssuntoEmail() {
    let assunto: string;
    if (this.totalCorpoEmailVinculado > 1) {
      assunto = this.cabecalho.corpoEmails[0].assunto + "(" + (this.totalCorpoEmailVinculado - 1) + ").";
    } else {
      assunto = assunto = this.cabecalho.corpoEmails[0].assunto;
    }
    return assunto;
  }

  /**
   * Inicializa objeto de configuração de dropdown.
   */
  private inicializarDropdownSettings() {
    this.dropdownSettingsUF = {
      singleSelection: false,
      idField: 'id',
      textField: 'sgUf',
      selectAllText: this.messageService.getDescription('LABEL_SELECIONE_TODOS'),
      unSelectAllText: this.messageService.getDescription('LABEL_REMOVER_TODOS'),
      itemsShowLimit: 5,
      allowSearchFilter: false,
      searchPlaceholderText: this.messageService.getDescription('LABEL_BUSCAR'),
      defaultOpen: false,
      noDataAvailablePlaceholderText: ''
    };
  }
}
