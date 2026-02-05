import { NgForm } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit, Input, TemplateRef, Output, EventEmitter } from '@angular/core';

import * as _ from "lodash";
import * as moment from 'moment';
import { AcaoSistema } from 'src/app/app.acao';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { InformacaoComissaoMembroService } from 'src/app/client/informacao-comissao-membro/informacao-comissao-membro.service';
import { DomSanitizer } from '@angular/platform-browser';

/**
 * Define um enum para a quantidade de opções.
 */
enum NumeroOpcoes {
  DUAS_OPCOES = 1,
  VARIAS_OPCOES = 2
};

@Component({
  selector: 'aba-informacao-comissao-membro',
  templateUrl: './aba-informacao-comissao-membro.component.html',
  styleUrls: ['./aba-informacao-comissao-membro.component.scss']
})
export class AbaInformacaoComissaoMembroComponent implements OnInit {

  public emails: any = [];
  public acaoSistema: AcaoSistema;
  public submitted: boolean = false;
  public emailSelecionado: any = {};
  public informacaoComissaoMembro: any = {};
  public modalVisualizarCorpoEmail: BsModalRef;
  public emailsDefinidos: any = [];

  @Input() atividadeSecundaria: any;
  @Output() recarregarAtividadeSecundaria: EventEmitter<any> = new EventEmitter<any>();

  /**
   * Construtor da classe.
   *
   * @param router
   * @param route
   * @param sanitizer
   * @param modalService
   * @param messageService
   * @param informacaoComissaoMembroService
   */
  constructor(
    private router: Router,
    public route: ActivatedRoute,
    private sanitizer: DomSanitizer,
    private modalService: BsModalService,
    private messageService: MessageService,
    private informacaoComissaoMembroService: InformacaoComissaoMembroService,
  ) {
    this.acaoSistema = new AcaoSistema(route);
    const paramsEmails = route.snapshot.data['paramsEmails'];

    this.emailsDefinidos = paramsEmails.emailsDefinidos;
    this.emails = paramsEmails.emailsAtividadeSecundaria;
  }

  /**
   * Método executado quando carregar o componente.
   */
  ngOnInit() {
    this.inicializarComissaoEleitoral();
  }

  /**
   * Método responsável por salvar as informações iniciais da comissão eleitoral.
   *
   * @param form
   */
  public salvar(form: NgForm): void {
    this.submitted = true;

    if (form.valid) {

      this.submitted = false;
      let informacaoComissaoMembro = JSON.parse(JSON.stringify(this.atividadeSecundaria.informacaoComissaoMembro));

      let documentoComissaoMembro = informacaoComissaoMembro.documentoComissaoMembro;
      informacaoComissaoMembro.documentoComissaoMembro = [];
      if (documentoComissaoMembro != null) {
        informacaoComissaoMembro.documentoComissaoMembro.push(documentoComissaoMembro);
      }

      this.informacaoComissaoMembroService.salvar(informacaoComissaoMembro).subscribe(data => {

        let msgSalvar = informacaoComissaoMembro.id ? 'LABEL_DADOS_ALTERADOS_SUCESSO' : 'LABEL_DADOS_INCLUIDOS_SUCESSO';
        this.messageService.addMsgSuccess(msgSalvar);

        if (this.atividadeSecundaria.informacaoComissaoMembro.id == undefined) {
          this.router.navigate(['eleicao', 'atividade-secundaria', this.atividadeSecundaria.id, 'alterar-informacao-comissao-membro']);
        }
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Abre o modal para visualizar o corpo do e-mail.
   *
   * @param template
   */
  public visualizarCorpoEmail(template: TemplateRef<any>, idEmail: any): void {
    this.emails.forEach(email => {
      if (email.id == idEmail) {
        this.emailSelecionado = email;
      }
    });

    this.modalVisualizarCorpoEmail = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }))
  }

  /**
   * Abre o modal para visualizar o corpo do e-mail.
   *
   * @param template
   */
  public fecharModalVisualizarEmail(): void {
    this.modalVisualizarCorpoEmail.hide();
  }

  /**
   * Realiza a atualização no campo majoritário.
   */
  public atualizarMajoritario(): void {
    if (this.atividadeSecundaria.informacaoComissaoMembro.situacaoMajoritario) {
      this.messageService.addConfirmYesNo('MSG_DESABILITAR_OPCAO_MAJORITARIA', () => { }, () => {
        this.atividadeSecundaria.informacaoComissaoMembro.situacaoMajoritario = true;
      });
    }
  }

  /**
   * Válida se foi selecionada o radio de duas opções.
   */
  public isTipoOpcaoDuasOpcoes(): boolean {
    return this.atividadeSecundaria.informacaoComissaoMembro.tipoOpcao == NumeroOpcoes.DUAS_OPCOES;
  }

  /**
   * Válida se existe um email selecionado.
   */
  public isEmailSelecionado(): boolean {
    return  this.atividadeSecundaria 
              && this.atividadeSecundaria.informacaoComissaoMembro 
              && this.atividadeSecundaria.informacaoComissaoMembro.email
              && this.atividadeSecundaria.informacaoComissaoMembro.email.id 
              && this.atividadeSecundaria.informacaoComissaoMembro.email.id !== '';
  }

  /**
   * Recupera a descrição referente a eleição do calendário.
   */
  public getDescricaoEleicao(): string {
    return this.atividadeSecundaria.atividadePrincipalCalendario.calendario.eleicao.ano + '/' +
      String(this.atividadeSecundaria.atividadePrincipalCalendario.calendario.eleicao.sequenciaAno).padStart(3, '0');
  }

  /**
   * Retorna se a atividade secundária tem sua data fim inferior a data atual.
   */
  public isAtividadeExpirada(): boolean {
    let agora = moment().startOf('day').toDate();
    let dataFim = moment(this.atividadeSecundaria.dataFim, 'YYYY-MM-DD').toDate();

    return agora > dataFim;
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
   * Inicializa o objeto de comissão eleitoral.
   */
  private inicializarComissaoEleitoral(): void {
    if (this.atividadeSecundaria && !_.isEmpty(this.atividadeSecundaria.informacaoComissaoMembro) && this.atividadeSecundaria.informacaoComissaoMembro.id) {

      this.atividadeSecundaria.informacaoComissaoMembro.atividadeSecundaria = { id: this.atividadeSecundaria.id };

      if (this.atividadeSecundaria.informacaoComissaoMembro && this.emailsDefinidos && this.emailsDefinidos.length > 0) {
        this.atividadeSecundaria.informacaoComissaoMembro.email = { id: this.emailsDefinidos[0].emailAtividadeSecundaria.id };
      } else {
        this.atividadeSecundaria.informacaoComissaoMembro.email = { id: '' };
      }

    } else {
      this.atividadeSecundaria.informacaoComissaoMembro = {
        quantidadeMinima: 3,
        quantidadeMaxima: 5,
        situacaoMajoritario: true,
        situacaoConselheiro: true,
        tipoOpcao: NumeroOpcoes.DUAS_OPCOES,
        email: { id: '' },
        atividadeSecundaria: {
          id: this.atividadeSecundaria && this.atividadeSecundaria.id ? this.atividadeSecundaria.id : undefined
        }
      };
    }
  }

}
