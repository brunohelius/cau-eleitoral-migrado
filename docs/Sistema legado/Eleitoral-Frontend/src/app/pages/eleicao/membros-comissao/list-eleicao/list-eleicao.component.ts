import * as _ from "lodash";
import { Component, OnInit, EventEmitter } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { LayoutsService } from '@cau/layout';


/**
 * Componente de listagem de eleições concluidas e inativas
 */
@Component({
  selector: 'app-list-eleicao',
  templateUrl: './list-eleicao.component.html',
  styleUrls: ['./list-eleicao.component.scss']
})
export class ListEleicaoComponent implements OnInit {
  public filtro: any = {};
  public anosEleicao: Array<any>;
  public eleicoes: Array<any>;
  public eleicoesAuxiliar: Array<any>;
  public tipoProcessos: Array<any>;
  public eleicoesPorAno: any;
  public showMessageFilter: Boolean = false;
  public quantidadeEleicoes: number = 0;
  public numeroRegistrosPaginacao: number = 10;
  public tipoProcessoFiltro: any = "";
  public eleicaoFiltro: any = "";
  public anoEleicaoFiltro: any = "";
  public search: string = "";

  /**
   * Construtor da classe
   * @param route
   * @param messageService
   * @param eleicaoClientService
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
    private layoutsService: LayoutsService
  ) {

    this.eleicoes = _.orderBy(route.snapshot.data["eleicoes"], ['eleicao'], ['asc']);
    this.eleicoesAuxiliar = this.eleicoes;
    this.anosEleicao = this.route.snapshot.data["anosEleicoes"];
    this.tipoProcessos = this.route.snapshot.data["tipoProcessos"];
    this.quantidadeEleicoes = this.eleicoes.length;
  }

  /**
   * Executa as paremetrizações iniciais do componente
   */
  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      description: this.messageService.getDescription('LABEL_ATOS_PREPARATORIOS_MEMBROS_COMISSAO'),
      icon: 'fa fa-wpforms'
    });
  }

  /**
     * Metodo set dos anos das eleições
     * @param tipoProcessos
     */
  private setQuantidadeEleicoes(qtdEleicoes: number) {
    this.quantidadeEleicoes = qtdEleicoes;
  }


  /**
   * Retorna a quantidade total de números de registros de eleições
   * @return number
   */
  public getQtdRegistroEleicoes(): number {
    return this.eleicoes.length;
  }

  /**
   * Recupera as eleições de um ano escolhido
   * @param ano
   */
  public getEleicoesAno(ano: number) {
    let eleicoesDoAno = [];

    eleicoesDoAno = this.eleicoesAuxiliar.filter( eleicao => {
      return eleicao.eleicao.ano == ano;
    });

    return eleicoesDoAno;
  }

  /**
   * Metodo que define as eleições pelo ano selecionado
   * @param event
   */
  public geraComboEleicaoFiltro(event: any) {
    this.eleicoesPorAno = this.getEleicoesAno(parseInt(event.target.value));
  }

  /**
 * Busca na lista de Calendario pelos filtro informados
 */
  public pesquisar(): void {
    this.showMessageFilter = false;
    event.preventDefault();
    this.getDadosEleicao();
  }

  /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
  public filter(search: string) {
    let filterItens = this.eleicoesAuxiliar.filter((data) => JSON.stringify(data).toLowerCase().indexOf(search.toLowerCase()) !== -1);
    this.eleicoes = filterItens;
    this.setQuantidadeEleicoes(this.eleicoes.length);
  }

  /**
   * Retorna os dados de eleição conforme os valores informados no filtro.
   *
   * @param anos
   */
  public getDadosEleicao(): void {
    this.filtro = this.getFiltroPesquisa();
    this.filtro.situacoes = [Constants.ELEICAO_CONCLUIDA, Constants.ELEICAO_INATIVA];
    this.eleicaoClientService.getEleicoesFilter(this.filtro).subscribe(data => {
      data = _.orderBy(data, ['ano'], ['asc']);
      this.eleicoes = data;
      this.setQuantidadeEleicoes(data.length);
    }, error => {
      this.setQuantidadeEleicoes(0);
      this.messageService.addMsgDanger(error);
    });
  }

  public getFiltroPesquisa(): any {
    return {
      "idTipoProcesso": this.tipoProcessoFiltro,
      "eleicoes":  this.eleicaoFiltro,
      "anos": this.anoEleicaoFiltro
    };
  }

  /**
   * Recupera o arquivo conforme a id da entidade 'resolucao' informada.
   *
   * @param event
   * @param idRegimentoEstatuto
   */
  public downloadResolucao(event: EventEmitter<any>, idResolucao: any): void {
    this.eleicaoClientService.downloadArquivo(idResolucao).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
     * Limpa os dados de filtro.
     *
     * @param event
     */
  public limpar(event: any) {
    event.preventDefault();
    this.anoEleicaoFiltro = "";
    this.eleicaoFiltro = "";
    this.tipoProcessoFiltro = "";
    this.search = "";
    this.pesquisar();
  }

  public isDesabilitarEleicoes() {
    return this.anoEleicaoFiltro === undefined || this.anoEleicaoFiltro.length === 0;
  }

}
