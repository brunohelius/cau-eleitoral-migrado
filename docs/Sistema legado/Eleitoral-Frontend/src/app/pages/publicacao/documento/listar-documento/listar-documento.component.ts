import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { AcaoSistema } from 'src/app/app.acao';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit, EventEmitter } from '@angular/core';

import { Constants } from 'src/app/constants.service';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';

@Component({
  selector: 'app-listar-documento',
  templateUrl: './listar-documento.component.html',
  styleUrls: ['./listar-documento.component.scss']
})
export class ListarDocumentoComponent implements OnInit {

  public filtro: any;
  public search: any;
  public acao: AcaoSistema;
  public calendarios: any[];
  public anosEleicao: any[];
  public tipoProcessos: any[];
  public calendariosExibicao: any;
  public calendariosAuxiliar: any;
  public exibirMsgFiltroVazio: any;
  public calendariosAnoSelecionado: any;
  public configuracaoDropdownEleicao: any;
  public configuracaoDropdownPublicacao: any;
  public configuracaoDropdownAnoEleicao: any;
  public numeroRegistrosPaginacao: number = 10;
  public configuracaoDropdownTipoProcesso: any;
  public mostrarMsgCalendariosInexistentes: boolean;


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
    this.calendarios = route.snapshot.data['calendarios'];
    this.tipoProcessos = route.snapshot.data['tipoProcessos'];
    this.anosEleicao = route.snapshot.data['anosEleicoes'].map((anoEleicao: any) => { return { ano: anoEleicao.eleicao.ano } });

    this.mostrarMsgCalendariosInexistentes = (this.calendarios.length == 0);
    this.calendariosExibicao = this.getCalendariosCamposGrid(this.calendarios);
    this.calendariosAuxiliar = JSON.parse(JSON.stringify(this.calendariosExibicao));
  }

  /**
   * Método executado quando inicializa a classe.
   */
  ngOnInit() {
    this.filtro = {
      anos: [],
      idTipoProcesso: '',
      eleicoes: [],
      situacoes: Constants.CALENDARIO_SITUACAO_CONCLUIDO
    };

    this.inicializarConfiguracaoEleicaoFiltro();
    this.inicializarConfiguracaoAnoEleicaoFiltro();
    this.inicializarConfiguracaoTipoProcessoFiltro();
  }

  /**
   * Realiza a pesquisa de publicações de comissão de acordo com o filtro informado.
   */
  public pesquisar(): void {
    this.search = null;
    this.exibirMsgFiltroVazio = false;
    const filtro = JSON.parse(JSON.stringify(this.filtro));
    filtro.eleicoes = this.getCalendariosPorSelecaoEleicaoFiltro();

    this.calendarioService.getCalendariosPorFiltro(filtro).subscribe(data => {
      this.calendarios = data;
      this.calendariosExibicao = this.getCalendariosCamposGrid(this.calendarios);
      this.calendariosAuxiliar = JSON.parse(JSON.stringify(this.calendariosExibicao));
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Filtra os dados da grid conforme o valor informado na variável search.
   *
   * @param search
   */
  public filtroGrid(search: string): void {
    search = search.toLowerCase();

    this.calendariosExibicao = this.calendariosAuxiliar.filter((calendario: any) => {
      return (
        calendario.ano.toString().includes(search) ||
        calendario.descricaoEleicao.toLowerCase().includes(search) ||
        calendario.descricaoTipoProcesso.toLowerCase().includes(search) ||
        calendario.descricaoSituacao.toLowerCase().includes(search)
      );
    });
  }

  /**
   * Limpa o filtro.
   *
   * @param event
   */
  public limpar(event): void {
    this.filtro = {
      anos: [],
      eleicoes: [],
      idTipoProcesso: '',
      situacoes: Constants.CALENDARIO_SITUACAO_CONCLUIDO
    };

    this.exibirMsgFiltroVazio = true;
    event.preventDefault();
    this.search = null;
  }

  /**
   * Recupera o total de registros do calendário em exibição na grid.
   */
  public getTotalRegistrosCalendario(): number {
    return this.calendariosExibicao.length;
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
   * Retorna a rota de Publicação de Documentos.
   *
   * @param idCalendario
   */
  public getUrlPublicarDocumento(idCalendario: number): string {
    return `/publicacao/documento/${idCalendario}/publicar`;
  }

  /**
   * Quando usuario seleciona o Ano no filtro de pesquisa.
   */
  public selecaoAnoEleicao(): void {
    this.calendariosAnoSelecionado = [];
    let calendariosAnoSelecionado = this.calendariosAnoSelecionado;

    this.filtro.anos.forEach((ano: number) => {
      let calendarios = this.calendarios.filter((calendario: any) => {
        return calendario.eleicao.ano == ano;
      });

      calendarios.forEach((calendario: any) => {
        calendariosAnoSelecionado.push(calendario.eleicao);
      });
    });
  }

  /**
   * Evento disparado ao selecionar todos os canos da eleição.
   */
  public selecaoTodosAnosEleicao(): void {
    this.calendariosAnoSelecionado = [];
    this.filtro.anos = this.anosEleicao;
    let calendariosAnoSelecionado = this.calendariosAnoSelecionado;

    this.calendarios.forEach((calendario: any) => {
      calendariosAnoSelecionado.push(calendario.eleicao);
    });
  }

  /**
   * Evento disparado ao de-selecionar o ano eleição.
   *
   * @param ano
   */
  public deselecionarAnoEleicao(ano: any) {
    this.calendariosAnoSelecionado = this.calendariosAnoSelecionado.filter((calendarioAno: any) => {
      return calendarioAno.ano != ano;
    });

    let eleicoesFiltro = [];
    this.filtro.eleicoes.forEach((eleicaoFiltro: any) => {
      let anoEleicaoFiltro = eleicaoFiltro.descricao.split('/').shift();
      if (anoEleicaoFiltro != ano) {
        eleicoesFiltro.push(eleicaoFiltro);
      }
    })

    this.filtro.eleicoes = eleicoesFiltro;
  }

  /**
   * Limpa a lista de atividades secundárias.
   */
  public deselecionarTodosAnoEleicao(): any {
    this.filtro.eleicoes = [];
    this.calendariosAnoSelecionado = [];
  }

  /**
   * Retorna o ID dos calendários, de acordo com as eleições selecionadas no filtro.
   */
  public getCalendariosPorSelecaoEleicaoFiltro(): number[] {
    return this.calendarios.filter(calendario =>
      _.find(this.filtro.eleicoes, { id: calendario.eleicao.id })
    ).map(calendario => calendario.id);
  }

  /**
   * Válida se o campo de eleição será apresentado desabilitado.
   */
  public isFiltroEleicaoDesabilitado(): boolean {
    return this.filtro.anos === undefined || this.filtro.anos.length === 0;
  }

  /**
   * Retorna os calendários com os campos utilizados na grid (apresentação/validação).
   *
   * @param calendarios
   */
  private getCalendariosCamposGrid(calendarios: any[]): any[] {
    return calendarios.map(calendario => {
      return {
        id: calendario.id,
        ano: calendario.eleicao.ano,
        resolucao: calendario.resolucao,
        idSituacao: calendario.idSituacao,
        idResolucao: calendario.idResolucao,
        descricaoEleicao: calendario.eleicao.descricao,
        descricaoSituacao: calendario.descricaoSituacao,
        descricaoTipoProcesso: calendario.eleicao.tipoProcesso.descricao,
      };
    });
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
      singleSelection: true,
      allowSearchFilter: false,
      searchPlaceholderText: 'Buscar',
      noDataAvailablePlaceholderText: ''
    };
  }

}
