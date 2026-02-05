import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Constants } from 'src/app/constants.service';

/**
 * Componente responsável pela apresentação de solicitações realizadas na chapa.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'card-pendencias',
    templateUrl: './card-pendencias.component.html',
    styleUrls: ['./card-pendencias.component.scss']
})

export class CardPendencias implements OnInit {

  @Input() public dados: any = {};
  
    public rotas: any;
    public idUf: number;
    public idCalendario: number;
    public solicitacoes: any = [];
    public limitePaginacao: number;
    public tipoSolicitacao: any = [];
    public limitesPaginacao = [5,10,25,50,100];

    constructor(
      private router: Router,
      private route: ActivatedRoute,
      private layoutsService: LayoutsService,
      private messageService: MessageService,
      private securityService: SecurityService,
    ){
      this.idUf = route.snapshot.params.uf;
      this.idCalendario = route.snapshot.params.idCalendario;
    }

    ngOnInit(){
      this.getTituloPagina();
      this.limitePaginacao = 10;
      this.inicializaSolicitacoesChapa();
      this.rotas = this.inicializaTiposSolicitacoes();
    }

    /**
    * retona o título do módulo de julgamento final
    */
    public getTituloPagina(): void {
      this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-wpforms',
        description: this.messageService.getDescription('TITLE_JULGAMENTO')
      });
    }

    /**
     * Método responsável por inicializar a estrutura dos dados para 
     * popular os cards da interface
     */
    public inicializaSolicitacoesChapa(): void {

      const tamanho =  Math.max(
        this.dados.pedidosDenuncia.length,
        this.dados.pedidosImpugnacao.length,
        this.dados.pedidosSubstituicao.length
      )
    
      for(let i = 0; i< tamanho; i++){
        
        let objDados = {
          denuncia: {},
          impugnacao: {},
          substituicao: {},
          isPrimeiraLinha: false
        }

        i == 0 ? objDados.isPrimeiraLinha = true:  objDados.isPrimeiraLinha = false
        this.dados.pedidosDenuncia[i] ? objDados.denuncia = this.dados.pedidosDenuncia[i]: null
        this.dados.pedidosImpugnacao[i] ? objDados.impugnacao = this.dados.pedidosImpugnacao[i]: null
        this.dados.pedidosSubstituicao[i] ? objDados.substituicao = this.dados.pedidosSubstituicao[i]: null
        this.solicitacoes.push(objDados)
      }
    }

    /**
     * Método responsável por redirecionar o usuário
     * para o serviço selecionado
     */
    public redirecionaServico(servico: number, id: number ){

      switch(servico){
        case this.rotas.substituicao.id: window.open(`/eleicao/substituicao-detalhamento/${id}`,'_blank');
        case this.rotas.impugnacao.id: window.open(`/eleicao/impugnacao/${id}/detalhar`,'_blank')
        case this.rotas.denuncia.id: window.open(`/denuncia/${id}/acompanhar`, '_blank');
        default: window.open('/')
      }
    }

    /**
     * retorna a inicialização das rotas dos serviços solicitados
     */  
    public inicializaTiposSolicitacoes(): any {
      return {
        substituicao: {id:1, descricao: 'substituicao'},
        impugnacao: {id:2, descricao: 'impugnacao'},
        denuncia: {id:3, descricao: 'denuncia'}
      }
    }

    /**
    * Método responsável por retornar a classe de cor
    * de acordo com o status da solicitação
    * @param idStatus 
    * @param tipoSolicitacao 
    */
    public getClassStatus(idStatus: number): string  {
      switch(idStatus){
        case 1: return 'color-em-analise'
        case 2: return 'color-deferido'
        case 3: return 'color-indeferido'
        default: ''
      }
    }

    /**
    * retorna a label Pedidos de Substituição
    */
    public getTituloPedidoSubstituicao(): string {
      return this.messageService.getDescription('LABEL_CHAPA_PENDENCIA_PEDIDO_SUBSTITUICAO', 
        ['<div>', '</div><div>', '</div>']);
    }

    /**
    * retorna a label Pedidos de Impugnação
    */
    public getTituloPedidoImpugnacao(): string {
      return this.messageService.getDescription('LABEL_CHAPA_PENDENCIA_PEDIDO_IMPUGNACAO', 
        ['<div>', '</div><div>', '</div>']);
    }

    /**
    * retorna a label Pedidos de Denuncia
    */
    public getTituloPedidoDenuncia(): string {
      return this.messageService.getDescription('LABEL_CHAPA_PENDENCIA_PEDIDO_DENUNCIA', 
        ['<div>', '</div><div>', '</div>']);
    }

    public voltar(): void {
      this.idUf == 165 ? this.idUf = 0: this.idUf
      this.router.navigate([
        `/eleicao/julgamento-final/acompanhar-uf/${this.idUf}/calendario/${this.idCalendario}`, 
        {
          isIES: this.idUf == Constants.ID_IES
        }
      ]);
    }
}