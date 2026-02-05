import { Component, OnInit, TemplateRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

import * as _ from "lodash";
import { CorpoEmailClientService } from 'src/app/client/corpo-email/corpo-email-client.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { CabecalhoEmailClientService } from 'src/app/client/cabecalho-email/cabecalho-email-client.service';
import { MessageService } from '@cau/message';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-list-corpo-email',
  templateUrl: './list-corpo-email.component.html',
  styleUrls: ['./list-corpo-email.component.scss']
})
export class ListCorpoEmailComponent implements OnInit {

  public filtro: any;
  public search: any;
  public corposEmail: any = [];
  public corpoEmailVisualizar: any;
  public corposEmailExibicao: any = [];
  public corposEmailAuxiliar: any = [];
  public cabecalhoEmailSelecionado: any;
  public configuracaoDropdownAtivo: any;
  public numeroRegistrosPaginacao: number;
  public configuracaoDropdownAssunto: any;
  public atividadesSecundariasFiltro: Array<any>;
  public modalVisualizarCorpoEmail: BsModalRef;
  public configuracaoDropdownAtividadeSecundaria: any;
  public showMessageFilter: boolean;

  /**
   * Construtor da classe.
   * 
   * @param router 
   * @param route 
   * @param modalService 
   * @param messageService 
   * @param corpoEmailClientService 
   * @param cabecalhoEmailService
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
    this.numeroRegistrosPaginacao = 10;
    this.corposEmail = this.route.snapshot.data['corposEmail'];
    this.agruparAtividadesSecundarias();
    this.corposEmailAuxiliar = JSON.parse(JSON.stringify(this.corposEmail));
    this.corposEmailExibicao = JSON.parse(JSON.stringify(this.corposEmail));
  }

  /**
   * Método executado quando a função inicializar.
   */
  ngOnInit() {
    this.inicializarConfiguracaoDropdownAtivo();
    this.inicializarConfiguracaoDropdownAssunto();
    this.inicializarConfiguracaoDropdownAtividadeSecundaria();
    this.filtro = {
      ativo: [],
      corposEmail: [],
      atividadesSecundarias: []
    };
    this.showMessageFilter = false;
  }

  /**
   * Realiza a pesquisa dos corpos de email.
   */
  public pesquisar(): void {
    this.corpoEmailClientService.getPorFiltro(this.filtro).subscribe(data => {
      let corposEmail = _.orderBy(data, ['id'], ['asc']);
      this.showMessageFilter = false;
      this.corposEmailExibicao = JSON.parse(JSON.stringify(corposEmail));
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Filtra os dados da grid conforme o valor informado na variável search.
   *
   * @param search
   */
  public filtroGrid(search): void {
    let filterItens = this.corposEmail.filter((data) => JSON.stringify(this.searchArray(data)).toLowerCase().indexOf(search.toLowerCase()) !== -1);
    this.corposEmailExibicao = filterItens;
  }

  /**
   * Array utilizada na busca gerar da grid.
   * 
   */
  private searchArray(corpoEmail: any): Array<any> {
    let atividadesSecundariasText = '';
    if (corpoEmail.atividadesSecundarias) {
      corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
        atividadesSecundariasText += (' ' + atividadeSecundaria.descricao);
      });
    }

    return [
      corpoEmail.id,
      corpoEmail.assunto,
      atividadesSecundariasText,
      this.messageService.getDescription(this.getLabelAtivo(corpoEmail.ativo))
    ];
  }


  /**
   * Recupera a label para o campo de ativo na tabela.
   *
   * @param isAtivo
   */
  public getLabelAtivo(isAtivo): string {
    return isAtivo ? 'LABEL_SIM' : 'LABEL_NAO';
  }

  /**
   * Recupera o total de registros de corpo de e-mail.
   */
  public getTotalRegistrosCorposEmail(): number {
    return this.corposEmailExibicao.length;
  }

  /**
   * Recupera a URL para o inserção do corpo de e-mail.
   */
  public getUrlIncluirCorpoEmail(): string {
    return `/email/corpo/incluir`;
  }

  /**
   * Recupera a URL para o visualizar corpo de e-mail.
   *
   * @param idCorpo
   */
  public getUrlAlterarCorpoEmail(idCorpo: number): string {
    return `/email/corpo/${idCorpo}/alterar`;
  }

  /**
   * Recupera a URL para o alterar corpo de e-mail.
   *
   * @param idCorpo
   */
  public getURLVisualizarCorpoEmail(idCorpo: number): string {
    return `/email/corpo/${idCorpo}/visualizar`;
  }

  /**
   * Recupera a listagem de assuntos do corpo de e-mail.
   */
  public getAssuntosCorpoEmail(): any {
    let assuntosFiltro = [];

    this.corposEmailAuxiliar.forEach(corpoEmail => {
      if (corpoEmail.assunto != undefined) {
        assuntosFiltro.push({ id: corpoEmail.id, assunto: corpoEmail.assunto });
      }
    });

    return _.orderBy(assuntosFiltro, [item => item.assunto.toLowerCase()], ['asc']);
  }

  /**
   * Recupera a lista de atividades secundárias de acordo com os assuntos selecionados.
   */
  public getAtividadesSecundariasAssunto(): any {
    let atividadesSecundarias = [];

    if (this.filtro.corposEmail.length == 0) {
      this.corposEmailAuxiliar.forEach(corpoEmail => {
        corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
          atividadesSecundarias.push({ id: atividadeSecundaria.id, emailAtividadeSecundaria: atividadeSecundaria.emailAtividadeSecundaria, descricao: atividadeSecundaria.descricao });
        });
      });
    } else {

      this.corposEmailAuxiliar.forEach(corpoEmail => {
        this.filtro.corposEmail.forEach(assunto => {
          if (corpoEmail.id == assunto.id) {
            corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
              atividadesSecundarias.push({ id: atividadeSecundaria.id, emailAtividadeSecundaria: atividadeSecundaria.emailAtividadeSecundaria, descricao: atividadeSecundaria.descricao });
            });
          }
        });
      });
    }

    this.atividadesSecundariasFiltro = atividadesSecundarias;
  }

  /**
   * Seleciona todas as atividades secundárias do assunto do corpo do e-mail.
   */
  public getTodasAtividadesSecundariasAssunto(): void {
    let atividadesSecundarias = [];

    this.corposEmailAuxiliar.forEach(corpoEmail => {
      corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
        atividadesSecundarias.push({ id: atividadeSecundaria.id, emailAtividadeSecundaria: atividadeSecundaria.emailAtividadeSecundaria, descricao: atividadeSecundaria.descricao });
      });
    });

    this.atividadesSecundariasFiltro = atividadesSecundarias;
  }

  /**
   * Limpa a as atividades secundárias.
   *
   * @param corpoEmail
   */
  public removerAtividadeSecundariaAssunto(corpoEmail: any): any {
    let atividadesSecundarias = [];

    this.corposEmailAuxiliar.forEach(corpoEmail => {
      this.filtro.corposEmail.forEach(assunto => {
        if (corpoEmail.id == assunto.id) {
          corpoEmail.atividadesSecundarias.forEach(atividadeSecundaria => {
            atividadesSecundarias.push({ id: atividadeSecundaria.id, emailAtividadeSecundaria: atividadeSecundaria.emailAtividadeSecundaria, descricao: atividadeSecundaria.descricao });
          });
        }
      });
    });

    this.atividadesSecundariasFiltro = atividadesSecundarias;

    let corpoEmailSelecionado = this.corposEmailAuxiliar.filter(corpoEmailGeral => {
      return corpoEmailGeral.id == corpoEmail.id;
    });

    let atividadesSecundariasParaSelecao = [];
    corpoEmailSelecionado = corpoEmailSelecionado.shift();
    corpoEmailSelecionado.atividadesSecundarias.forEach(atividadeSecundaria => {
      let atividadesSecundarias = this.filtro.atividadesSecundarias.filter(atividadeSecundariaFiltro => {
        return atividadeSecundariaFiltro.emailAtividadeSecundaria != atividadeSecundaria.emailAtividadeSecundaria;
      });

      atividadesSecundariasParaSelecao.push(...atividadesSecundarias);
    });

    this.filtro.atividadesSecundarias = atividadesSecundariasParaSelecao;
  }

  /**
   * Limpa a lista de atividades secundárias.
   */
  public removerTodasAtividadesSecundariasAssunto(): any {
    this.atividadesSecundariasFiltro = [];
    this.filtro.atividadesSecundarias = [];
  }

  /**
   * Recupera as opções exibidas no campo de filtro ativo.
   */
  public getOpcoesFiltroAtivo(): any {
    let camposAtivo = [];
    camposAtivo.push({ valor: true, label: this.messageService.getDescription('LABEL_SIM') });
    camposAtivo.push({ valor: false, label: this.messageService.getDescription('LABEL_NAO') });
    return camposAtivo;
  }

  /**
   * Limpa os campos do filtro do corpo do e-mail.
   *
   * @param event
   */
  public limpar(event: any): void {
    this.filtro = {
      ativo: [],
      corposEmail: [],
      atividadesSecundarias: []
    };
    this.showMessageFilter = true;
    this.corposEmailExibicao = [];
    this.atividadesSecundariasFiltro = [];
    event.preventDefault();
  }

  /**
   * Abre o modal de visualização do corpo do e-mail.
   *
   * @param template
   * @param corpoEmail
   */
  public visualizarCorpoEmail(template: TemplateRef<any>, corpoEmail: any): void {
    this.corpoEmailVisualizar = corpoEmail;
    if (corpoEmail.cabecalhoEmail.id != undefined) {
      this.cabecalhoEmailService.getPorId(corpoEmail.cabecalhoEmail.id).subscribe(
        (data: any) => {
          this.corpoEmailVisualizar.cabecalhoEmail.imagemCabecalho = data.imagemCabecalho;
          this.corpoEmailVisualizar.cabecalhoEmail.imagemRodape = data.imagemRodape;
          this.modalVisualizarCorpoEmail = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else {
      this.modalVisualizarCorpoEmail = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
    }

  }

  /**
   * Verifica se o campo de filtro de  Atividade Secundária está desabilitado.
   */
  public isAtividadeSecundariaFiltroDisabled(): boolean {
    return this.filtro.corposEmail == undefined || (this.filtro.corposEmail.length == 0);
  }

  /**
    * Verificar exibição da mensagem de filtro vazio.
    */
  public isExibirMSGTableFiltroVazio() {
    return this.showMessageFilter && this.corposEmailExibicao.length == 0;
  }

  /**
   * Método responsável por fechar modal de apresentação de e-mail.
   */
  public fecharModalVisualizarCorpoEmail(): void {
    this.modalVisualizarCorpoEmail.hide();
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
   * Inicializa as configurações do dropdown de ativo.
   */
  private inicializarConfiguracaoDropdownAtivo(): void {
    this.configuracaoDropdownAtivo = {
      idField: 'valor',
      itemsShowLimit: 5,
      defaultOpen: false,
      textField: 'label',
      singleSelection: true,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      unSelectAllText: 'Remove Todos',
      selectAllText: 'Selecione Todos',
      noDataAvailablePlaceholderText: ''
    };
  }

  /**
   * Inicializa as configurações do dropdown de assunto.
   */
  private inicializarConfiguracaoDropdownAssunto(): void {
    this.configuracaoDropdownAssunto = {
      idField: 'id',
      itemsShowLimit: 5,
      defaultOpen: false,
      textField: 'assunto',
      singleSelection: false,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      unSelectAllText: 'Remove Todos',
      selectAllText: 'Selecione Todos',
      noDataAvailablePlaceholderText: ''
    };
  }

  /**
   * Inicializa as configurações do dropdown de atividade secundária.
   */
  private inicializarConfiguracaoDropdownAtividadeSecundaria(): void {
    this.configuracaoDropdownAtividadeSecundaria = {
      idField: 'emailAtividadeSecundaria',
      itemsShowLimit: 5,
      defaultOpen: false,
      textField: 'descricao',
      singleSelection: false,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      unSelectAllText: 'Remove Todos',
      selectAllText: 'Selecione Todos',
      noDataAvailablePlaceholderText: ''
    };
  }

  /**
   * Agrupa Array de Atividades secundárias para utilizar ordenação.
   * 
   */
  private agruparAtividadesSecundarias() {
    this.corposEmail.map((corpoEmail) => {
      let agrupamento = '';
      corpoEmail.atividadesSecundarias.forEach((atividadeSecundaria) => {
        agrupamento = agrupamento + ' ' + atividadeSecundaria.descricao;
      });
      corpoEmail.atividadesSecundariasAgrupadas = agrupamento;
      corpoEmail.ativoDescricao = this.messageService.getDescription(this.getLabelAtivo(corpoEmail.ativo));
      return corpoEmail;
    });

  }

}
