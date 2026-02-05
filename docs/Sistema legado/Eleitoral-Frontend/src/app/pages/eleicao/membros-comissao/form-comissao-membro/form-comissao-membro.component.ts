import * as _ from "lodash";
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MessageService } from '@cau/message';
import { AcaoSistema } from 'src/app/app.acao';
import { LayoutsService } from '@cau/layout';

import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';

/**
 * Este componente é responsavel pelo controle das abas da funcionalidade de controle de abas
 */
@Component({
  selector: 'app-form-comissao-membro',
  templateUrl: './form-comissao-membro.component.html',
  styleUrls: ['./form-comissao-membro.component.scss']
})
export class FormComissaoMembroComponent implements OnInit {
  public tab: any;
  public eleicoes: any;
  public anosEleicoes: Array<any>;
  public cauUfs: Array<any>;
  public numerosMembro: Array<number>;
  public configuracaoEleicao: any;
  public tipoParticipacao: any;
  public acaoSistema: AcaoSistema;
  public cauUfAlterar: any = undefined;

  /**
   * Construtor da classe
   * @param route 
   * @param messageService 
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private router: Router,
    private layoutsService: LayoutsService,
    private eleicaoClientService: EleicaoClientService,
  ) { 
    this.acaoSistema = new AcaoSistema(route);
    let cauUF = route.snapshot.params['cauUf'];
    if(cauUF){
      this.cauUfAlterar = cauUF; 
    }
  }


  /**
   * Metodo inicial do componente 
   */
  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      description: this.messageService.getDescription('LABEL_ATOS_PREPARATORIOS_MEMBROS_COMISSAO'),
      icon: 'fa fa-wpforms'
    });
    this.setEleicoes(_.orderBy(this.route.snapshot.data["eleicoes"], ['eleicao'], ['asc']));
    this.setAnosEleicoes(this.route.snapshot.data["anosEleicoes"]);
    this.setCauUFs(this.route.snapshot.data['cauUfs']);
    this.setConfiguracaoEleicao(this.route.snapshot.data['configuracaoEleicao']);
    this.setTipoParticipacao(this.route.snapshot.data['tipoParticipacao']);
    this.tab = {
      tabIncluirMembros: { active: true },
      tabHistorico: {}
    };

    this.tab.current = this.tab.tabIncluirMembros;
  }

  /**
   * Metodo set de eleições
   * @param eleicoes 
   */
  private setEleicoes(eleicoes: any) {
    this.eleicoes = eleicoes;
  }

  /**
   * Metodo set de eleições
   * @param eleicoes 
   */
  private setConfiguracaoEleicao(configEleicao: any) {
    this.configuracaoEleicao = configEleicao;
  }

  /**
   * Metodo set de anos das eleições
   * @param eleicoes 
   */
  private setAnosEleicoes(anosEleicoes: any) {
    this.anosEleicoes = anosEleicoes;
  }

  /**
   * Metodo set de cau UFS
   * @param eleicoes 
   */
  private setCauUFs(cauUfs: any) {
    this.cauUfs = cauUfs;
  }


  /**
     * Método que realiza o controle das abas.
     *
     * @param selectTab
     * @param nomeAba
     */
  public onSelect(selectTab: any): void {
    this.habilitarAba(selectTab);
  }

  /**
     * Método responsável por habilitar as abas.
     *
     * @param selectTab
     */
  public habilitarAba(selectTab: any): void {
    this.tab.current.active = false;
    selectTab.active = true;
    this.tab.current = selectTab;
  }

  public habilitaAbaHistorico(event: Event): void {
    this.habilitarAba(this.tab.tabHistorico);
  }

  /**
   * Metodo de set de tipo de participação
   * @param tipoParticipacao 
   */
  public setTipoParticipacao(tipoParticipacao: any) {
    this.tipoParticipacao = tipoParticipacao;
  }

  /**
    * Válida as funções de redirecionamento para listagem.
    */
  public voltarLista(): void {
    this.router.navigate(['/eleicao/membros-comissao/']);
  }

}
