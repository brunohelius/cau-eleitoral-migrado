import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";

import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import * as _ from 'lodash';
/**
 * Componente responsável pela apresentação de Denuncias.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'app-lista-denuncia-estado-comissao',
  templateUrl: './list-denuncia-estado-comissao.component.html',
  styleUrls: ['./list-denuncia-estado-comissao.component.scss']
})
export class ListDenunciaEstadoComissaoComponent implements OnInit {

  public usuario;
  public denunciasUF: any;
  public limitePaginacao: number;
  public limitesPaginacao = [];
  public search: string;
  public dadosSubstituicao: any;
  public denunciasRecebidas: any;
  public denunciasRelatoria: any;

  public abas: any = {};

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
  ) {
  }

  /**
   * Inicialização dos dados do campo
  */
  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('Denuncia')
    });
    // recebe o resolve com os dados passados como parametro
    this.dadosSubstituicao = {
      uf: {
        id: 140,
        descricao: 'AC'
      }
    };
    
    this.denunciasUF = this.route.snapshot.data["agrupamentoUf"];
    this.denunciasRelatoria = this.route.snapshot.data["denunciasRelatoria"];
    this.denunciasRecebidas = this.route.snapshot.data["denunciasNaoAdmitidas"];

    this.limitePaginacao = 50;
    this.limitesPaginacao = [10, 25, 50];
    this.inicializaAbas();
    this.initData();
  }

  /**
   * Ajuste dos dados para o front end
   */
  private initData() {
    if (this.denunciasUF['agrupamentoUF'].length > 0) {
      this.denunciasUF['agrupamentoUF'] = [...this.denunciasUF['agrupamentoUF'].filter(denuncia => {
        return Number(denuncia.id_cau_uf) !== Constants.CAUBR_ID && Number(denuncia.id_cau_uf) !== Constants.IES_ID
      }), ...this.denunciasUF['agrupamentoUF'].filter(denuncia => {
        if(Number(denuncia.id_cau_uf) === Constants.CAUBR_ID) {
          denuncia.descricao = this.messageService.getDescription('LABEL_CEN');
        }
        return Number(denuncia.id_cau_uf) === Constants.CAUBR_ID || Number(denuncia.id_cau_uf) === Constants.IES_ID
      })];
    }
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      minhasDenuncias: { id: 3, nome: 'minhasDenuncias', ativa: true },
      denunciasRecebidas: { id: 4, nome: 'denunciasRecebidas', ativa: false },
      denunciasRelatoria: { id: 5, nome: 'denunciasRelatoria', ativa: false },
    };
  }

  /**
   * Muda a aba selecionada de acordo com a seleção.
   *
   * @param aba
   */
  public mudarAbaSelecionada(aba: number): void {
    this.mudarAba(aba);
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  private mudarAba(aba: number): void {
    this.abas.minhasDenuncias.ativa = this.abas.minhasDenuncias.id == aba;
    this.abas.denunciasRecebidas.ativa = this.abas.denunciasRecebidas.id == aba;
    this.abas.denunciasRelatoria.ativa = this.abas.denunciasRelatoria.id == aba;
  }

  /**
   * Rota para a Tela de visualizar os dados de Denuncia por Cau UF.
   *
   * @param idCauUf
   */
  public visualizar(idCauUf: any) {
    this.router.navigate(['denuncia/comissao/', 'cauUf', idCauUf, 'listar']);
  }
}
