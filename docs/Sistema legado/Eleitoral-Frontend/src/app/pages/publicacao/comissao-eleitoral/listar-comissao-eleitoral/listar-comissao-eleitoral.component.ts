import { AcaoSistema } from 'src/app/app.acao';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit, EventEmitter } from '@angular/core';

import * as _ from "lodash";
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { MessageService } from '@cau/message';

@Component({
  selector: 'app-listar-comissao-eleitoral',
  templateUrl: './listar-comissao-eleitoral.component.html',
  styleUrls: ['./listar-comissao-eleitoral.component.scss']
})
export class ListarComissaoEleitoralComponent implements OnInit {

  public acao: AcaoSistema;
  public filtro: any;
  public search: any;
  public anosEleicao: any;
  public tipoProcessos: any;
  public exibirMsgFiltroVazio: any;
  public documentosPublicados: any;
  public calendariosAnoSelecionado: any;
  public configuracaoDropdownEleicao: any;
  public documentosPublicadosExibicao: any;
  public documentosPublicadosAuxiliar: any;
  public configuracaoDropdownPublicacao: any;
  public configuracaoDropdownAnoEleicao: any;
  public numeroRegistrosPaginacao: number = 10;
  public configuracaoDropdownTipoProcesso: any;

  /**
   * Construtor da classe.
   *
   * @param route 
   * @param router 
   * @param messageService 
   * @param calendarioService 
   */
  constructor(
    route: ActivatedRoute,
    private router: Router,
    private messageService: MessageService,
    private calendarioService: CalendarioClientService
  ) {
    this.acao = new AcaoSistema(route);
    this.anosEleicao = route.snapshot.data['anosEleicoes'];
    this.tipoProcessos = route.snapshot.data['tipoProcessos'];
    this.documentosPublicados = route.snapshot.data['calendariosPublicados'];

    if (this.documentosPublicados) {
      this.documentosPublicados.forEach(documentoPublicado => {
        documentoPublicado.descricaoPublicado = documentoPublicado.totalPublicacoes > 0 ? this.messageService.getDescription('LABEL_SIM') : this.messageService.getDescription('LABEL_NAO');
      });
    }

    this.documentosPublicadosExibicao = JSON.parse(JSON.stringify(this.documentosPublicados));
    this.documentosPublicadosAuxiliar = JSON.parse(JSON.stringify(this.documentosPublicados));
  }

  /**
   * Método executado quando inicializa a classe.
   */
  ngOnInit() {
    this.filtro = {
      eleicoesFiltro: [],
      publicadoFiltro: [],
      anosEleicaoFiltro: [],
      tipoProcessoFiltro: [],
    };

    this.inicializarConfiguracaoEleicaoFiltro();
    this.inicializarConfiguracaoPublicadoFiltro();
    this.inicializarConfiguracaoAnoEleicaoFiltro();
    this.inicializarConfiguracaoTipoProcessoFiltro();
  }

  /**
   * Realiza a pesquisa de publicações de comissão de acordo com o filtro informado.
   */
  public pesquisar(): void {
    this.exibirMsgFiltroVazio = false;

    this.calendarioService.getCalendariosPublicacaoComissaoEleitoralPorFiltro(this.filtro).subscribe(data => {

      if (data) {
        data.forEach(documentoPublicado => {
          documentoPublicado.descricaoPublicado = documentoPublicado.totalPublicacoes > 0 ? this.messageService.getDescription('LABEL_SIM') : this.messageService.getDescription('LABEL_NAO');
        });
      }

      this.documentosPublicadosAuxiliar = _.orderBy(data, ['id'], ['asc']);
      this.documentosPublicadosExibicao = JSON.parse(JSON.stringify(this.documentosPublicadosAuxiliar));
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
    let filterItens = this.documentosPublicados.filter((data) => JSON.stringify(data).toLowerCase().indexOf(search.toLowerCase()) !== -1);
    this.documentosPublicadosExibicao = filterItens;
  }

  /**
   * Limpa o filtro.
   *
   * @param event
   */
  public limpar(event): void {
    this.filtro = {
      eleicoesFiltro: [],
      publicadoFiltro: [],
      anosEleicaoFiltro: [],
      tipoProcessoFiltro: [],
    };

    this.exibirMsgFiltroVazio = true;
    event.preventDefault();
  }

  /**
   * Recupera o total de registros do calendário em exibição na grid.
   */
  public getTotalRegistrosCalendario(): number {
    return this.documentosPublicadosExibicao.length;
  }

  /**
   * Recupera o arquivo conforme a id da entidade 'resolucao' informada.
   *
   * @param event
   * @param idRegimentoEstatuto
   */
  public downloadResolucao(event: EventEmitter<any>, idResolucao: any): void {
    this.calendarioService.downloadArquivo(idResolucao).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a mensagem de status publicado.
   *
   * @param totalPublicacoes
   */
  public getStatusPublicado(totalPublicacoes: number): string {
    return totalPublicacoes > 0 ? 'LABEL_SIM' : 'LABEL_NAO';
  }

  /**
   * Retorna a rota de visualizar calendario para publicação.
   *
   * @param idCalendario
   */
  public getUrlVisualizarPublicacao(idCalendario): string {
    return `/publicacao/comissao-eleitoral/${idCalendario}/visualizar`;
  }

  /**
   * Retorna a rota de Publicação de Documentos.
   *
   * @param idEleicao
   */
  public getUrlPublicarDocumento(idEleicao): string {
    return `/publicacao/documento/${idEleicao}/publicar`;
  }

  /**
   * Quando usuario seleciona o Ano no filtro de pesquisa.
   *
   * @param event
   */
  public selecaoAnoEleicao(ano: any): void {
    this.calendariosAnoSelecionado = [];
    let calendariosPublicados = this.documentosPublicados;
    let calendariosAnoSelecionado = this.calendariosAnoSelecionado;

    this.filtro.anosEleicaoFiltro.forEach(anoEleicaoFiltro => {
      let calendarios = calendariosPublicados.filter(documento => {
        return documento.calendario.eleicao.ano == anoEleicaoFiltro;
      });

      calendarios.forEach(documento => {
        calendariosAnoSelecionado.push(documento.calendario.eleicao);
      });
    });
  }

  /**
   * Evento disparado ao selecionar todos os canos da eleição.
   */
  public selecaoTodosAnosEleicao(): void {
    this.calendariosAnoSelecionado = [];
    this.filtro.anosEleicaoFiltro = this.anosEleicao;
    let calendariosAnoSelecionado = this.calendariosAnoSelecionado;

    this.documentosPublicados.forEach(documento => {
      calendariosAnoSelecionado.push(documento.calendario.eleicao);
    });
  }

  /**
   * Evento disparado ao de-selecionar o ano eleição.
   *
   * @param ano
   */
  public deselecionarAnoEleicao(ano: any) {
    this.calendariosAnoSelecionado = this.calendariosAnoSelecionado.filter(calendarioAno => {
      return calendarioAno.ano != ano;
    });

    let eleicoesFiltro = [];
    this.filtro.eleicoesFiltro.forEach(eleicaoFiltro => {
      let anoEleicaoFiltro = eleicaoFiltro.descricao.split('/').shift();
      if (anoEleicaoFiltro != ano) {
        eleicoesFiltro.push(eleicaoFiltro);
      }
    })

    this.filtro.eleicoesFiltro = eleicoesFiltro;
  }

  /**
   * Limpa a lista de atividades secundárias.
   */
  public deselecionarTodosAnoEleicao(): any {
    this.filtro.eleicoesFiltro = [];
    this.calendariosAnoSelecionado = [];
  }

  /**
   * Válida se o campo de eleição será apresentado desabilitado.
   */
  public isFiltroEleicaoDesabilitado(): boolean {
    return this.filtro.anosEleicaoFiltro === undefined || this.filtro.anosEleicaoFiltro.length === 0;
  }

  /**
   * Recupera as opções para o campos de publicado.
   */
  public getOpcoesPublicado(): any {
    let opcoesPublicado = [];
    opcoesPublicado.push({ valor: true, label: this.messageService.getDescription('LABEL_SIM') });
    opcoesPublicado.push({ valor: false, label: this.messageService.getDescription('LABEL_NAO') });
    return opcoesPublicado;
  }

  /**
   * Inicializa a configuração do campos de "Ano Eleição" para o filtro.
   */
  private inicializarConfiguracaoAnoEleicaoFiltro(): void {
    this.configuracaoDropdownAnoEleicao = {
      idField: 'ano',
      itemsShowLimit: 5,
      defaultOpen: false,
      textField: 'ano',
      singleSelection: false,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      unSelectAllText: 'Remove Todos',
      selectAllText: 'Selecione Todos',
      noDataAvailablePlaceholderText: ''
    };
  }

  /**
   * Inicializa a configuração do campos de "Publicado" para o filtro.
   */
  private inicializarConfiguracaoPublicadoFiltro(): void {
    this.configuracaoDropdownPublicacao = {
      idField: 'valor',
      itemsShowLimit: 5,
      defaultOpen: false,
      textField: 'label',
      singleSelection: false,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      unSelectAllText: 'Remove Todos',
      selectAllText: 'Selecione Todos',
      noDataAvailablePlaceholderText: ''
    };
  }

  /**
   * Inicializa a configuração do campos de "Eleição" para o filtro.
   */
  private inicializarConfiguracaoEleicaoFiltro(): void {
    this.configuracaoDropdownEleicao = {
      idField: 'id',
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
   * Inicializa a configuração do campos de "Tipo Processo" para o filtro.
   */
  private inicializarConfiguracaoTipoProcessoFiltro(): void {
    this.configuracaoDropdownTipoProcesso = {
      idField: 'id',
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

}
