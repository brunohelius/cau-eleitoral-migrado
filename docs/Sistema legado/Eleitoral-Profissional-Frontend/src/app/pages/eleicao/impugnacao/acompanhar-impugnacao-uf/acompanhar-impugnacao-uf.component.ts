import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { CauUFService } from 'src/app/client/cau-uf-client/cau-uf-client.service';


/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-impugnacao-uf',
    templateUrl: './acompanhar-impugnacao-uf.component.html',
    styleUrls: ['./acompanhar-impugnacao-uf.component.scss']
})

export class AcompanharImpugnacaoUf implements OnInit {
    
  public cauUfs = [];
  public pedidos = [];
  public retorno = '';
  public configuracaoCkeditor: any = {};
  public solicitacoesImpugnacao: any = [];

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private bandeiraUF: CauUFService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
  ) {
    this.cauUfs = route.snapshot.data["cauUfs"];
    this.solicitacoesImpugnacao = route.snapshot.data["pedidos"];
  }

  ngOnInit() {

      this.layoutsService.onLoadTitle.emit({
          icon: 'fa fa-user',
          description: this.messageService.getDescription('TITLE_PEDIDO_DE_IMPUGNACAO')
      });
  }

  /**
   * Retorna a quantidade todal de pedidos de substituição
   */
  public getTotalPedidos(dados: any[]) {
    let soma = 0;
    dados.forEach((valor) => {
        soma += valor.quantidadePedidosEmAnalise;
    });
    return soma;
  }

  /**
   * Busca imagem de bandeira do estado do CAUUF.
   * 
   * @param idCauUf 
   */
  public getImagemBandeira(idCauUf): String {
    
    idCauUf === 0 ? idCauUf = 165 : idCauUf;
    let imagemBandeira = undefined;
    
    this.cauUfs.forEach(cauUf => {
      if (idCauUf == cauUf.id) {
          imagemBandeira = cauUf.imagemBandeira;
      } else if (idCauUf == undefined && cauUf.id == Constants.ID_CAUBR) {
          imagemBandeira = cauUf.imagemBandeira;
      }
    });
    return imagemBandeira;
  }

  /**
   * verifica se o parametro passado é de uma IES
   * caso seja retorna true;
   * @param id 
   */
  public isIES(id: number): boolean {
    return (id === Constants.ID_CAUBR) || (id === 0);
  }

  /**
   * Método responsável por redirecionar para a página inicial
   */
  public voltar(): void {
    this.router.navigate(['/']);
  }

  public redirecionaUf(id: number){

    let tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

    let ds_url = tipoProfissional == Constants.TIPO_PROFISSIONAL ? 'impugnacao-responsavel-solicitacao' : 'acompanhar-impugnacao-uf';
    this.router.navigate([`/eleicao/impugnacao/${ds_url}/${id}`]);
  }

  /**
     * Retorna um valor de parâmetro passado na rota
     * @param nameParam 
     */
  private getValorParamDoRoute(nameParam) {
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