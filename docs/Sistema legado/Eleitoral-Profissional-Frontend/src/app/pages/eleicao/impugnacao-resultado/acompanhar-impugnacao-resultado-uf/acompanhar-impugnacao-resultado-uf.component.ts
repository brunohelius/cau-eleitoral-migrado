import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Constants } from './../../../../constants.service';
import { UtilsService } from 'src/app/utils.service';


/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'acompanhar-impugnacao-resultado-uf',
  templateUrl: './acompanhar-impugnacao-resultado-uf.component.html',
  styleUrls: ['./acompanhar-impugnacao-resultado-uf.component.scss']
})

export class AcompanharImpugnacaoResultadoUfComponent implements OnInit {

  public cauUfs = [];
  public pedidos = [];
  public retorno = '';
  public idEleicao: any;
  public configuracaoCkeditor: any = {};
  public solicitacoesImpugnacao: any = [];
  public tipoProfissional: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
  ) {
    this.cauUfs = route.snapshot.data["cauUfs"];
    this.solicitacoesImpugnacao = route.snapshot.data["pedidos"];
    this.idEleicao = this.route.snapshot.params.id;
  }

  ngOnInit() {
    this.inicializaIconeTitulo();
    this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional', this.route);
  }

  /**
   * Inicializa ícone e título do header da página .
   */
  private inicializaIconeTitulo(): void {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-fw fa-list',
      description: this.messageService.getDescription('LABEL_PARAMETRIZACAO_CALENDARIO_IMPUGNACAO_RESULTADO')
    });
  }

  /**
   * Retorna a quantidade todal de pedidos de substituição
   */
  public getTotalPedidos() {
    let soma = 0;
    this.solicitacoesImpugnacao.forEach((valor) => {
      soma += valor.quantidadePedidos;
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
    return (id === Constants.ID_CAUBR) || (id === 0) || (id === undefined);
  }

  /**
   * Método responsável por redirecionar para a página inicial
   */
  public voltar(): void {
    this.router.navigate(['/']);
    this.layoutsService.onLoadTitle.emit({
      icon: '',
      description: ''
    });
  }

  public redirecionaUf(idCauUf: number) {
    if (!idCauUf) {
      idCauUf = 0;
    }
    let isMeusPedidos = UtilsService.getValorParamDoRoute('isMeusPedidos', this.route);
    if (isMeusPedidos) {
      this.router.navigate([`/eleicao/impugnacao-resultado/acompanhar-profissional/${idCauUf}`]);
    } else {
      this.router.navigate([`/eleicao/impugnacao-resultado/acompanhar/${idCauUf}`]);
    }
  }

  /**
   * retorna a label da aba de acompanhar chapa com quebra de linha
   */
  public getTituloAbaImpugnacaoResultado(): any {
    return this.messageService.getDescription('LABEL_IMPUGNACAO_RESULTADO', ['<div>', '</div><div>', '</div>']);
  }
}
