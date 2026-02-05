import {Component, OnInit, TemplateRef, EventEmitter} from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';

import {AtividadeSecundariaClientService} from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';
import {BsModalRef, BsModalService} from 'ngx-bootstrap';
import {NgForm} from '@angular/forms';
import {CabecalhoEmailClientService} from 'src/app/client/cabecalho-email/cabecalho-email-client.service';
import {DomSanitizer} from '@angular/platform-browser';
import * as deepEqual from "deep-equal";
import * as _ from "lodash";
import {MessageService} from '@cau/message';
import {SecurityService} from "@cau/security";
import { LayoutsService } from '@cau/layout';

@Component({
  selector: 'app-definir-email-declaracao-por-atividade',
  templateUrl: './definir-email-declaracao-por-atividade.component.html',
  styleUrls: ['./definir-email-declaracao-por-atividade.component.scss']
})
export class DefinirEmailDeclaracaoPorAtividadeComponent implements OnInit {

  public idCalendario: number;
  public idAtividadeSecundaria: number;

  public tiposEmails: Array<any>;
  public corpoEmailSelecionado: any;
  public emailsDefinidos: Array<any>;
  public emailsAtividadeSecundaria: Array<any>;
  public modalVisualizarCorpoEmail: BsModalRef;

  public declaracao: any;
  public declaracaoClone: any;
  public declaracaoSelecionada: any;
  public tiposDeclaracoes: Array<any>;
  public declaracoes: Array<any>;
  public declaracoesDefinidas: Array<any>;
  public modalVisualizarDeclaracao: BsModalRef;

  public emailsDeclaracao: any;
  public emailsDeclaracoesClone: Array<any>;

  public inlineSubtitle: boolean;
  public tituloPrincipal: string;
  public tituloSecundario: string;

  private admin: boolean;
  public submitted: boolean;
  private isAlteracao: boolean;
  public rotaVoltar: Array<any>;
  private hasAlteracaoTipos: boolean;

  /**
   * Construtor da classe.
   * @param route
   * @param router
   * @param messageService
   * @param atividadeSecundariaService
   * @param modalService
   * @param securityService
   * @param cabecalhoEmailService
   * @param domSanitizer
   */
  constructor(
      private route: ActivatedRoute,
      private router: Router,
      private messageService: MessageService,
      private layoutsService: LayoutsService,
      private atividadeSecundariaService: AtividadeSecundariaClientService,
      private modalService: BsModalService,
      private securityService: SecurityService,
      private cabecalhoEmailService: CabecalhoEmailClientService,
      public domSanitizer: DomSanitizer,
  ) {
    this.idAtividadeSecundaria = route.snapshot.params.id;

    this.tituloPrincipal = this.getValorParamDoRoute('tituloPrincipal');
    this.tituloSecundario = this.getValorParamDoRoute('tituloSecundario');

    const paramsEmails = route.snapshot.data['paramsEmails'];
    const paramsDeclaracoes = route.snapshot.data['paramsDeclaracoes'];

    this.tiposEmails = paramsEmails.tiposEmails;
    this.declaracoes = paramsDeclaracoes.declaracoes;
    this.emailsDefinidos = paramsEmails.emailsDefinidos;
    this.hasAlteracaoTipos = paramsEmails.hasAlteracaoTipos;
    this.tiposDeclaracoes = paramsDeclaracoes.tiposDeclaracoes;
    this.declaracoesDefinidas = paramsDeclaracoes.declaracoesDefinidas;
    this.emailsAtividadeSecundaria = paramsEmails.emailsAtividadeSecundaria;

    const atividadeSecundaria = route.snapshot.data['atividadeSecundaria'];
    this.idCalendario = atividadeSecundaria.atividadePrincipalCalendario.calendario.id;

    this.admin = securityService.credential['_user'].administrador;
  }

  /**
   * Função inicializada quando o componente carregar.
   */
  ngOnInit() {

      /**
       * Define ícone e título do header da página
       */
      this.layoutsService.onLoadTitle.emit({
          icon: 'fa fa-wpforms',
          description: this.messageService.getDescription(this.tituloPrincipal)
      });

      this.initData();
  }

  private initData() {
      let tiposEmailAux: any = [];
      let tiposDeclaracaoAux: any = [];

      if (this.tiposEmails != undefined && this.tiposEmails.length > 0) {
        this.tiposEmails.forEach((tipoEmail) => tiposEmailAux.push({
            idEmailAtividadeSecundaria: this.getEmailDefinido(tipoEmail.id),
            idTipoEmailAtividadeSecundaria: tipoEmail.id,
            descricaoTipoEmailAtividadeSecundaria: tipoEmail.descricao
        }));
      }

      if (this.tiposDeclaracoes != undefined && this.tiposDeclaracoes.length > 0) {
        this.tiposDeclaracoes.forEach((tipoDeclaracao) => tiposDeclaracaoAux.push({
            idDeclaracao: this.getDeclaracaoDefinida(tipoDeclaracao.id),
            idTipoDeclaracaoAtividade: tipoDeclaracao.id,
            descricaoTipoDeclaracaoAtividade: tipoDeclaracao.descricao
        }));
      }

      this.emailsDeclaracao = {tiposEmail: tiposEmailAux, tiposDeclaracao: tiposDeclaracaoAux};
      this.emailsDeclaracoesClone = _.cloneDeep(this.emailsDeclaracao);

      this.rotaVoltar = ['eleicao', this.idCalendario, 'atividade-principal', 'lista'];

      this.isAlteracao = (this.emailsDefinidos.length > 0);
  }

    public hasErrorRequeridEmail(indice): boolean {
        let hasError = true;

        if (this.emailsDeclaracao.tiposEmail[indice].idEmailAtividadeSecundaria) {
            hasError = false;
        }
        return hasError;
    }

    public hasErrorRequeridTipoEmail(indice): boolean {
        let hasError = true;

        if (this.emailsDeclaracao.tiposEmail[indice].descricaoTipoEmailAtividadeSecundaria) {
            hasError = false;
        }
        return hasError;
    }

    public hasErrorMaxLengthTipoEmail(indice): boolean {
        let hasError = false;

        const str = this.emailsDeclaracao.tiposEmail[indice].descricaoTipoEmailAtividadeSecundaria;
        if (str && str.length > 1000) {
            hasError = true;
        }
        return hasError;
    }

    public hasErrorRequeridDeclaracao(indice): boolean {
        let hasError = true;

        if (this.emailsDeclaracao.tiposDeclaracao[indice].idDeclaracao) {
            hasError = false;
        }
        return hasError;
    }

    public hasErrorRequeridTipoDeclaracao(indice): boolean {
        let hasError = true;

        if (this.emailsDeclaracao.tiposDeclaracao[indice].descricaoTipoDeclaracaoAtividade) {
            hasError = false;
        }
        return hasError;
    }

    public hasErrorMaxLengthTipoDeclaracao(indice): boolean {
        let hasError = false;

        const str = this.emailsDeclaracao.tiposDeclaracao[indice].descricaoTipoDeclaracaoAtividade;
        if (str && str.length > 1000) {
            hasError = true;
        }
        return hasError;
    }


  /**
   * Método para salvar formulário de definição de e-mails/declaração.
   */
  public salvar(form: NgForm): void {
      this.submitted = true;
      if (form.valid) {
          if (this.isAlteracao) {
              this.messageService.addConfirmYesNo("MSG_DESEJA_REALMENTE_ALTERAR_DADOS",
                  () => {
                      this.salvarDados();
                  });
          } else {
              this.salvarDados();
          }
      }
  }

  /**
   * Salva os dados de emails e declaração para a sub-atividade.
   */
  private salvarDados() {
      this.atividadeSecundariaService.definirDeclaracaoEmailPorAtivSecundaria(this.getData()).subscribe(() => {
          if (this.isAlteracao) {
              this.messageService.addMsgSuccess('LABEL_DADOS_ALTERADOS_SUCESSO');
          } else {
              this.messageService.addMsgSuccess('LABEL_DADOS_INCLUIDOS_SUCESSO');
          }
          this.emailsDeclaracoesClone = _.cloneDeep(this.emailsDeclaracao);
      }, error => {
          this.messageService.addMsgDanger(error);
      });
  }

  /**
   * Método responsável por apresentar e-mail selecionado em modal.
   * @param template
   * @param idEmailAtividadeSecundaria
   */
  public visualizarCorpoEmail(template: TemplateRef<any>, idEmailAtividadeSecundaria: number) {
      this.corpoEmailSelecionado = this.getEmailSelecionado(idEmailAtividadeSecundaria);
      this.modalVisualizarCorpoEmail = this.modalService.show(template, Object.assign({}, {class: 'modal-lg'}));
  }

  /**
   * Método responsável por apresentar a declaração selecionada em modal.
   * @param template
   * @param idDeclaracao
   */
  public visualizarDeclaracao(template: TemplateRef<any>, idDeclaracao: number) {
      this.declaracaoSelecionada = this.getDeclaracaoSelecionada(idDeclaracao);
      this.modalVisualizarDeclaracao = this.modalService.show(template, Object.assign({}, {class: 'modal-lg'}))
  }

  /**
   * Preenche E-mail selecionado por tipo através de busca no array de emailsAtividadeSecundaria.
   *
   * @param idEmailAtividadeSecundaria
   */
  private getEmailSelecionado(idEmailAtividadeSecundaria: number) {
      let selecionado;
      if (this.emailsAtividadeSecundaria || this.emailsAtividadeSecundaria.length > 0) {
          selecionado = this.emailsAtividadeSecundaria.find((c) => {
              return (c.hasOwnProperty('id') && c.id == idEmailAtividadeSecundaria);
          });
      }
      return selecionado != undefined ? selecionado.corpoEmail : undefined;
  }

  /**
   * Preenche E-mail definido por tipo através de busca no array de emailsDefinidos.
   *
   * @param idTipoEmail
   */
  private getEmailDefinido(idTipoEmail: number) {
      let definido;
      if (this.emailsDefinidos || this.emailsDefinidos.length > 0) {
          definido = this.emailsDefinidos.find((c) => {
              return c.tipoEmail.id == idTipoEmail;
          });
      }
      return definido != undefined ? definido.emailAtividadeSecundaria.id : '';
  }

  /**
   * Preenche Declaração selecionada por tipo através de busca no array de declaracoesAtividade.
   *
   * @param idDeclaracao
   */
  private getDeclaracaoSelecionada(idDeclaracao: number) {
      let selecionada = undefined;
      if (this.declaracoes || this.declaracoes.length > 0) {
          selecionada = this.declaracoes.find((c) => {
              return (c.hasOwnProperty('id') && c.id == idDeclaracao);
          });
      }
      return selecionada;
  }

  /**
   * Preenche Declaração definida por tipo através de busca no array de emailsAtividadeSecundaria.
   *
   * @param idTipoDeclaracao
   */
  private getDeclaracaoDefinida(idTipoDeclaracao: number) {
      let selecionada;

      if (this.declaracoesDefinidas || this.declaracoesDefinidas.length > 0) {
          selecionada = this.declaracoesDefinidas.find((c) => {
              return c.tipoDeclaracaoAtividade.id == idTipoDeclaracao;
          });
      }
      return selecionada != undefined ? selecionada.idDeclaracao : '';
  }

  /**
   * Método responsável por fechar modal de apresentação de e-mail.
   */
  public fecharModalVisualizarEmail(): void {
      this.modalVisualizarCorpoEmail.hide();
  }

  /**
   * Método responsável por fechar modal de apresentação de declaração.
   */
  public fecharModalVisualizarDeclaracao(): void {
      this.modalVisualizarDeclaracao.hide();
  }

  /**
   * Método responsável por voltar a página anterior(apresentação de atividades).
   */
  public voltar() {
      if (this.hasModificacao()) {
          this.messageService.addConfirmYesNo('LABEL_VOLTAR', () => {
              this.router.navigate(this.rotaVoltar);
          });
      } else {
          this.router.navigate(this.rotaVoltar);
      }
  }

  /**
   * Retorna objeto com dados para requisição de definição de e-mails.
   */
  private getData(): any {
      return {
          emails: this.emailsDeclaracao.tiposEmail,
          idAtividadeSecundaria: this.idAtividadeSecundaria,
          declaracoes: this.emailsDeclaracao.tiposDeclaracao
      };
  }

  /**
   * Método utilizado pelo 'select' de e-mail para comparar values de 'options'.
   * @param optionValue
   * @param selectedValue
   */
  compareFn(optionValue, selectedValue) {
      return optionValue && selectedValue ? optionValue.id === selectedValue.id : optionValue === selectedValue;
  }

  /**
   * Valida se existe modificação nos emails ou na declaração.
   */
  public hasModificacao(): boolean {
      return !deepEqual(this.emailsDeclaracoesClone, this.emailsDeclaracao);
  }

  /**
   * Evento disparado quanto trocar a declaração.
   */
  public onChangeDeclaracao() {
      if (this.isAlteracao) {

        const MSG_ALT_DECLARACAO = this.getValorParamDoRoute('msgConfirmaDeclaracao');

        this.atividadeSecundariaService.getTotalRespostasDeclaracoes(this.idAtividadeSecundaria).subscribe((count) => {
            if (count > 0) {
                this.messageService.addConfirmYesNo(MSG_ALT_DECLARACAO,
                    () => { },
                    () => {
                        this.declaracao = this.declaracaoClone;
                    });
            }
        }, error => {
            this.messageService.addMsgDanger(error);
        });
      }
  }

  /**
   * Verifica se a data fim da atividade secundária não foi excedida.
   *
   * @returns boolean
   */
  public isAdministrador(): boolean {
      return this.admin == true;
  }

  /**
   * Volta a pagina inicial.
   */
  public inicio() {
      this.router.navigate(['/']);
  }

  /**
   * Verifica se o botao visualizar esta desabilitado pra colocar o title.
   */
  public isBotaoVisualizarTitle(declaracaoSelecionado: any) {
      if (declaracaoSelecionado == undefined) {
          return this.messageService.getDescription('MSG_VISUALIZAR_DECLARACAO');
      } else {
          return this.messageService.getDescription('LABEL_VISUALIZAR');
      }
  }

  private getValorParamDoRoute(nameParam){
    let data = this.route.snapshot.data;

    let valor = undefined;

    for (let index of Object.keys(data)) {
      let param = data[index];

      if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
        valor = param[nameParam];
        break;
      }
    }
    return valor;
  }

}
