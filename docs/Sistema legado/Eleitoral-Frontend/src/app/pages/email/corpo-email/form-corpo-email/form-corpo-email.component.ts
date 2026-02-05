import { Component, OnInit, TemplateRef } from '@angular/core';
import { AcaoSistema } from 'src/app/app.acao';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { CorpoEmailClientService } from 'src/app/client/corpo-email/corpo-email-client.service';
import { NgForm } from '@angular/forms';
import { Constants } from 'src/app/constants.service';
import { CabecalhoEmailClientService } from 'src/app/client/cabecalho-email/cabecalho-email-client.service';
import { MessageService } from '@cau/message';
import { DomSanitizer } from '@angular/platform-browser';
import * as deepEqual from "deep-equal";
import * as _ from "lodash";

@Component({
  selector: 'app-form-corpo-email',
  templateUrl: './form-corpo-email.component.html',
  styleUrls: ['./form-corpo-email.component.scss']
})
export class FormCorpoEmailComponent implements OnInit {

  public corpoEmail: any;
  public _corpoEmail: any;
  public configuracaoCkeditor: any;
  public acaoSistema: AcaoSistema;
  public cabecalhosEmail: Array<any>;
  public cabecalhoEmailSelecionado: any;
  public atividadesPrincipais: Array<any>;
  public atividades: Array<any>;
  public _atividades: Array<any>;
  public submitted: boolean;
  public dropdownSettingsCabecalhoEmail: any;
  public dropdownSettingsAtividadePrincipal: any;
  public dropdownSettingsAtividadeSecundaria: any;
  public modalVisualizarCorpoEmail: BsModalRef;

  /**
   * Construtor da classe.
   *
   * @param router
   * @param route
   * @param modalService
   * @param messageService
   * @param corpoEmailClientService
   * @param sanitizer
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private corpoEmailClientService: CorpoEmailClientService,
    private cabecalhoEmailService: CabecalhoEmailClientService,
    private sanitizer: DomSanitizer
  ) {
    this.acaoSistema = new AcaoSistema(route);
    this.cabecalhosEmail = this.route.snapshot.data['cabecalhosEmail'];
    this.cabecalhosEmail.unshift({ id: undefined, titulo: this.messageService.getDescription('LABEL_NENHUM') });
    this.atividadesPrincipais = this.route.snapshot.data['atividadesPrincipais'];
    if (!this.acaoSistema.isAcaoIncluir()) {
      this.corpoEmail = this.route.snapshot.data['corpoEmail'];
    }
  }

  /**
   * Função inicializada quando o componente carregar.
   */
  ngOnInit() {
    if (this.acaoSistema.isAcaoIncluir()) {
      this.inicializarCorpoEmail();
    }
    else {
      this.cabecalhoEmailSelecionado = this.corpoEmail.cabecalhoEmail;
      this.corpoEmail.cabecalhoEmail = this.corpoEmail.cabecalhoEmail.id == undefined ?
        [{ id: undefined, titulo: this.messageService.getDescription('LABEL_NENHUM') }] : [this.corpoEmail.cabecalhoEmail];
    }


    this.inicializarConfiguracaoCkeditor();
    this.inicializardropdownSettings();
    this.inicializarAtividadesSecundarias();
    this._corpoEmail = _.cloneDeep(this.corpoEmail);
    this._atividades = _.cloneDeep(this.atividades);
  }

  /**
   * Apresenta mensagem de salvar corpo de e-mail.
   */
  public salvar(): void {
    this.fecharModalCorpoEmail();
    if (this.acaoSistema.isAcaoIncluir()) {
      this.salvarDados();
    }
    else {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_ALTERAR_DADOS', () => {
        this.salvarDados();
      });
    }
  }

  /**
   * Salva corpo de e-mail.
   */
  private salvarDados() {
    let msg: string = this.acaoSistema.isAcaoIncluir() ? 'MSG_DADOS_CADASTRADOS_COM_SUCESSO' : 'LABEL_DADOS_ALTERADOS_SUCESSO';
    let dadosSalvar = this.getDadosSalvar();
    this.corpoEmailClientService.salvar(dadosSalvar).subscribe(
      () => {
        this.messageService.addMsgSuccess(msg);
        this.router.navigate(['email', 'corpo', 'listar']);
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Retorna dados para salvar corpo de e-mail.
   */
  private getDadosSalvar() {
    let emailsAtividadeSecundaria = [];
    let dadosSalvar: any = Object.assign({}, this.corpoEmail);

    if (dadosSalvar.cabecalhoEmail != undefined && dadosSalvar.cabecalhoEmail[0].id != undefined) {
      dadosSalvar.cabecalhoEmail = dadosSalvar.cabecalhoEmail[0];
    } else {
      dadosSalvar.cabecalhoEmail = null;
    }

    this.atividades.forEach(atividade => {
      if (atividade.atividadeSecundaria[0] != undefined) {
        emailsAtividadeSecundaria.push({
          atividadeSecundaria: atividade.atividadeSecundaria[0],
        });
      }
    });

    dadosSalvar.emailsAtividadeSecundaria = emailsAtividadeSecundaria;
    return dadosSalvar;
  }

  /**
   * Voltar a página de listar.
   */
  public voltar(form: NgForm): void {

    if (!this.acaoSistema.isAcaoVisualizar() && this.hasModificacao()) {
      this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_VOLTAR_DADOS_NAO_SALVOS_SERAO_PERDIDOS', () => {
        this.router.navigate(['email', 'corpo', 'listar']);
      });
    } else {
      this.router.navigate(['email', 'corpo', 'listar']);
    }
  }

  /**
  * Valida se existe modificação no objeto de corpo de E-mail.
  */
  public hasModificacao(): boolean {
    return !deepEqual(this._corpoEmail, this.corpoEmail) || !deepEqual(this._atividades, this.atividades);
  }

  /**
   * Verifica se o "Cabeçalho e Rodapé" foi definido.
   */
  public isCabecalhoRodapePreenchido(): boolean {
    return (this.corpoEmail.cabecalhoEmail != undefined && this.corpoEmail.cabecalhoEmail.length > 0);
  }

  /**
   * Verifica se o Corpo do E-mail é Vazio.
   *
   * @param form
   */
  public isEmptyCorpoEmail(): boolean {
    let atividades = this.atividades.filter(ativ => ativ.atividadePrincipal.length > 0 && ativ.atividadeSecundaria.length > 0);
    return (
      this.isEmptyString(this.corpoEmail.assunto) ||
      !this.isCabecalhoRodapePreenchido() ||
      (atividades.length == 0 || atividades.length != this.atividades.length) ||
      this.isEmptyString(this.corpoEmail.descricao)
    );
  }

  /**
   * Limpa o valor apresentado no campo do ckeditor.
   */
  public limparCampo(nomeCampo): void {
    if (nomeCampo == Constants.CORPO_EMAIL_NOME_CAMPO_DESCRICAO) {
      this.corpoEmail.descricao = '';
    }
  }

  /**
   * Ao selecionar cabeçalho Gerencia alteração do cabecalhoEmailSelecionado.
   * @param cabecalhoEmail 
   */
  public onSelectCabecalho() {
    this.setCabecalhoEmailSelecionado();
  }

  /**
   * Alterar lista de Atividades Secundárias apresentadas em drop down.
   * @param atividadePrincipal 
   */
  public onSelectAtividadePrincipal(atividade: any) {
    atividade.atividadeSecundaria = [];
  }

  /**
   * Apresenta corpo de e-mail.
   * 
   * @param template 
   * @param form 
   */
  public apresentarModalCorpoEmail(template: TemplateRef<any>, form: NgForm): void {
    this.submitted = true;
    if (this.isInclusaoCorpoEmailAtivo() && form.valid) {
      this.modalVisualizarCorpoEmail = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
    }
  }

  /**
   * Verifica se o usuário está incluído corpo de e-mail ativo.
   */
  public isInclusaoCorpoEmailAtivo(): boolean {
    let isInclusaoCorpoEmailDesativado: boolean = true;
    if ((this.acaoSistema.isAcaoIncluir() || this.acaoSistema.isAcaoAlterar()) && !this.corpoEmail.ativo && this.isEmptyCorpoEmail()) {
      this.messageService.addMsgWarning('MSG_OPCAO_ATIVAR_EMAIL_ENCONTRASE_DESABILITADA');
      isInclusaoCorpoEmailDesativado = false;
    }
    return isInclusaoCorpoEmailDesativado;
  }

  /**
   * Fecha modal de corpo de e-mail.
   */
  public fecharModalCorpoEmail(): void {
    this.modalVisualizarCorpoEmail.hide();
  }

  /**
   * Adiciona campos para seleção de atividade.
   */
  public addAtividade(): void {
    this.atividades.push({
      hasDefinicao: false,
      emailAtividadeSecundaria: undefined,
      atividadePrincipal: [],
      atividadeSecundaria: []
    });
  }

  /**
   * Filtra atividades secundárias por atividade principal.
   * @param atividadePrincipal
   */
  public getAtividadesSecundariasPorAtividadePrincipal(atividadePrincipal: any) {
    let atividadeSecundaria = [];
    if (atividadePrincipal != undefined) {
      let atividadeSelecionada = this.atividadesPrincipais.find(atividade => { return atividade.id == atividadePrincipal.id });
      if (atividadeSelecionada != undefined) {
        atividadeSecundaria = atividadeSelecionada.atividadesSecundarias;
      }
    }
    return atividadeSecundaria;
  }

  /**
   * Desabilita campo de atividade principal.
   */
  public isAtividadePrincipalDisabled(atividade: any): boolean {
    return (
      this.acaoSistema.isAcaoVisualizar() ||
      this.isCorpoEmailDefault() ||
      !this.corpoEmail.ativo ||
      atividade.hasDefinicao
    );
  }

  /**
   * Desabilita campo de atividade secundária.
   * 
   * @param atividadePrincipal 
   * @param idCorpoEmail
   */
  public isAtividadeSecundariaDisabled(atividade: any, atividadePrincipal: any): boolean {
    return (
      atividadePrincipal == undefined ||
      this.acaoSistema.isAcaoVisualizar() ||
      this.isCorpoEmailDefault() ||
      !this.corpoEmail.ativo ||
      atividade.hasDefinicao
    );
  }

  public getDescricaoMsgAlertaDefinicao(atividade: any) {
    return this.messageService.getDescription('LABEL_DEFINICAO_EMAIL_JA_REALIZADA', [
      atividade.atividadePrincipal[0].descricao,
      atividade.atividadeSecundaria[0].descricao,
    ]);
  }

  /**
   * Verifica se o Corpo de E-mail é default.
   */
  private isCorpoEmailDefault(): boolean {
    return this.corpoEmail.id == Constants.CORPO_EMAIL_ID_DAFAULT;
  }

  /**
   * Verifica se o campo é desabilitado.
   * 
   */
  public isCampoDisabled() {
    return this.acaoSistema.isAcaoVisualizar() || !this.corpoEmail.ativo;
  }

  /**
   * Verifica se o campo de add Atividade é desabilitado.
   */
  public isAddAtividadeDisabled() {
    return this.acaoSistema.isAcaoVisualizar() || this.isCorpoEmailDefault();
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
      ]
    };
  }

  /**
   * Inicializa a configuração de Drop downs.
   */
  private inicializardropdownSettings(): void {
    this.dropdownSettingsCabecalhoEmail = {
      singleSelection: true,
      idField: 'id',
      textField: 'titulo',
      allowSearchFilter: true,
      defaultOpen: false,
      noDataAvailablePlaceholderText: this.messageService.getDescription('LABEL_ARGUARDE')
    };

    this.dropdownSettingsAtividadePrincipal = {
      singleSelection: true,
      idField: 'id',
      textField: 'descricao',
      allowSearchFilter: true,
      defaultOpen: false,
      noDataAvailablePlaceholderText: this.messageService.getDescription('LABEL_ARGUARDE')
    };

    this.dropdownSettingsAtividadeSecundaria = {
      singleSelection: true,
      idField: 'id',
      textField: 'descricao',
      allowSearchFilter: true,
      defaultOpen: false,
      noDataAvailablePlaceholderText: this.messageService.getDescription('LABEL_ARGUARDE')
    };
  }

  /**
   * Inicializa Array de Atividades Secundárias.
   */
  private inicializarAtividadesSecundarias(): void {
    this.atividades = [];
    if (this.acaoSistema.isAcaoIncluir() || this.corpoEmail.atividadesSecundarias.length == 0) {
      this.atividades.push({
        hasDefinicao: false,
        emailAtividadeSecundaria: undefined,
        atividadePrincipal: [],
        atividadeSecundaria: []
      });
    } else {

      this.corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
        this.atividades.push({
          emailAtividadeSecundaria: atividadeSecundaria.emailAtividadeSecundaria,
          hasDefinicao: atividadeSecundaria.hasDefinicao,
          atividadePrincipal: [{
            id: atividadeSecundaria.atividadePrincipalCalendario.id,
            descricao: atividadeSecundaria.atividadePrincipalCalendario.descricao
          }],
          atividadeSecundaria: [{ id: atividadeSecundaria.id, descricao: atividadeSecundaria.descricao }]
        });
      });
    }
  }

  /**
   * Inicializar Corpo de E-mail.
   */
  private inicializarCorpoEmail() {
    this.corpoEmail = {
      "id": "",
      "assunto": "",
      "descricao": "",
      "ativo": true,
      "cabecalhoEmail": undefined,
      "atividadesSecundarias": []
    };
  }

  /**
   * Inicializar cabeçalho de e-mail que foi selecionado em drop down.
   */
  private setCabecalhoEmailSelecionado() {
    if (this.corpoEmail.cabecalhoEmail[0] != undefined && this.corpoEmail.cabecalhoEmail[0].id != undefined) {
      let id = this.corpoEmail.cabecalhoEmail[0].id;
      this.cabecalhoEmailService.getPorId(id).subscribe(
        data => {
          this.cabecalhoEmailSelecionado = data;
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else {
      this.cabecalhoEmailSelecionado = undefined;
    }
  }

  /**
   * Verifica se string é vazia.
   *
   * @param str
   */
  private isEmptyString(str: string): boolean {
    return (str.length === 0 || !str.trim());
  }
}
