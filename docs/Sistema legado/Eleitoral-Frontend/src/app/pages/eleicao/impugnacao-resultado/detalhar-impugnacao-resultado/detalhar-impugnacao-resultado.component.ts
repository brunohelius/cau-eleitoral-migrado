import { JulgamentoSegundaInstanciaImpugnacaoResultadoClientService } from './../../../../client/julgamento-segunda-instancia-impugnacao-resultado-client/julgamento-segunda-instancia-impugnacao-resultado-client.service';

import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { LayoutsService } from '@cau/layout';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from '@angular/router';
import { SecurityService } from '@cau/security';
import { JulgamentoAlegacaoImpugnacaoResultadoClientService } from 'src/app/client/julgamento-alegacao-impugnacao-resultado-client/julgamento-alegacao-impugnacao-resultado-client.service';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';


/**
 * Componente responsável pela apresentação do detalhamento do pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'detalhar-impugnacao-resultado',
    templateUrl: './detalhar-impugnacao-resultado.component.html',
    styleUrls: ['./detalhar-impugnacao-resultado.component.scss']
})

export class DetalharImpugnacaoResultadoComponent implements OnInit {

  public abas: any;
  public cauUf: any;
  public defesa: any;
  public alegacao: any;
  public bandeira: any;
  public impugnacao: any;
  public julgamento: any;
  public julgamentoDeferido: any;
  public isVisualizar: any;
  private idCalendario: any;
  public julgamentoSegunda: any;
  public recursosImpugnado: any;
  public recursosImpugnante: any;

  public configuracaoCkeditor: any = {};

  public isImpugnacao: boolean;
  public isAssessorUf: boolean = false;
  public isCarregadoAlegacao: boolean = false;

  public isCarregadoJulgamento: boolean = false;
  public isCarregadoJulgamentoSegunda: boolean = false;

  public isCarregadoRecursoJulgamentoImpugnado: boolean = false;
  public isCarregadoRecursoJulgamentoImpugnante: boolean = false;

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  /**
   * Construtor da classe.
   *
   * @param route
   * @param messageService
   * @param layoutsService
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private impugnacaoService: ImpugnacaoResultadoClientService,
    private julgamentoAlegacaoService: JulgamentoAlegacaoImpugnacaoResultadoClientService,
    private julgamentoSegundaInstanciaService: JulgamentoSegundaInstanciaImpugnacaoResultadoClientService,
  ) {
    this.cauUf = route.snapshot.data["cauUfs"];
    this.impugnacao = route.snapshot.data["impugnacao"];
    this.idCalendario = this.route.snapshot.params.idCalendario;
  }

  ngOnInit() {
    this.inicializaAbas();
    this.isImpugnacao = true;
    this.inicializaIconeTitulo();
    this.setImagemBandeira();
    this.getDadosJulgamento(1);

    if (this.securityService.hasRoles(Constants.ROLE_ACESSOR_CE) && !this.securityService.hasRoles(Constants.ROLE_ACESSOR_CEN)) {
      this.isAssessorUf = true;
    }

    this.inicializaConfiguracaoCkeditor();
    this.isVisualizar = this.route.snapshot.paramMap.get('visualizar');
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
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      abaVisaoGeral: { id: Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_VISAO_GERAL, nome: 'visaoGeral', ativa: false },
      detalhar: { id: Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_DETALHAR, nome: 'detalhar', ativa: true },
      alegacao: { id: Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_ALEGACAO, nome: 'alegacao', ativa: false },
      julgamento: { id: Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO, nome: 'julgamento', ativa: false },
      recursoImpugnante: {  id: Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE, nome: 'recursoImpugnante', ativa: false },
      recursoImpugnado: { id: Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO, nome: 'recursoImpugnado' , ativa: false },
      julgamentoSegunda: { id: Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO_SEGUNDA, nome: 'julgamentoSegunda' , ativa: false },
    };
  }

  /**
   * Mudar aba que foi selecionada.
   *
   * @param idAba
   */
  public mudarAba(idAba: number): void {
    this.abas.abaVisaoGeral.ativa = this.abas.abaVisaoGeral.id == idAba;
    this.abas.detalhar.ativa = this.abas.detalhar.id == idAba;
    this.abas.alegacao.ativa = this.abas.alegacao.id == idAba;
    this.abas.julgamento.ativa = this.abas.julgamento.id == idAba;
    this.abas.recursoImpugnante.ativa = this.abas.recursoImpugnante.id == idAba;
    this.abas.recursoImpugnado.ativa = this.abas.recursoImpugnado.id == idAba;
    this.abas.julgamentoSegunda.ativa = this.abas.julgamentoSegunda.id == idAba;
  }

  /**
   * Redirecionar aba de julgamento.
   * @param idAba
   */
  public redirecionaAba(idAba: number): void {
    if (this.abas.alegacao.id == idAba) {
        this.getDadosAlegacao(idAba);
    }
    if (this.abas.julgamento.id == idAba) {
      this.getDadosJulgamento(idAba);
    }
    if (this.abas.detalhar.id == idAba) {
      this.mudarAba(idAba);
    }
    if (this.abas.recursoImpugnante.id == idAba) {
      this.getDadosRecursoImpugnante(idAba);
    }
    if (this.abas.recursoImpugnado.id == idAba) {
      this.getDadosRecursoImpugnado(idAba);
    }
    if (this.abas.julgamentoSegunda.id == idAba) {
      this.getDadosJulgamentoSegundaInstancia(idAba);
    }
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
      ],
      title: 'Justificativa'
    };
  }

  /**
  * retorna a label da aba de acompanhar chapa com quebra de linha
  */
  public getTituloAbaImpugnacaoResultado(): any {
    return this.messageService.getDescription('LABEL_IMPUGNACAO_RESULTADO', ['<div>', '</div><div>', '</div>']);
  }

  /**
  * retorna a label da aba de Julgamento 1ª Instância com quebra de linha
  */
  public getTituloAbaJulgamentoPrimeiraInstancia(): any {
    return  this.messageService.getDescription('LABEL_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA',['<div>','</div><div>','</div>']);
  }

  /**
     * Verifica se o julgamento é IES ou não.
     * @param id
     */
    public isIES(): boolean {
      let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
      return  (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
  }

  /**
   * retorna a label da aba de recurso IMPUGNANTE com quebra de linha.
   */
  public getTituloAbaRecursoJulgamentoImpugnante(): any {
      if (this.isIES()) {
          return this.messageService.getDescription('LABEL_ABA_RECONSIDERACAO_JULGAMENTO_IMPUGNANTE',['<div>','</div><div>','</div>']);
      }
      if (!this.isIES()) {
          return this.messageService.getDescription('LABEL_ABA_RECURSO_JULGAMENTO_IMPUGNANTE',['<div>','</div><div>','</div>']);
      }
  }

  /**
   * Verifica se a aba de recurso/reconsideração IMPUGNANATE deve ser mostrada.
   */
  public hasRecursoJulgamentoImpugnante(): boolean {
      return (this.impugnacao.hasRecursoJulgamentoImpugnante ||
              this.impugnacao.isFinalizadoAtividadeRecursoJulgamento
              && this.impugnacao.hasJulgamento
          );
  }

  /**
   * Responsavel por carregar os dados referentes a aba do Recurso Impugnante.
   */
  public getDadosRecursoImpugnante(idAba): void {
    if (!this.isCarregadoRecursoJulgamentoImpugnante) {
      this.impugnacaoService.getRecursoJulgamentoPorIdImpugnacao(this.impugnacao.id,
      Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE ).subscribe(
        data => {
          if (data != undefined) {
            this.recursosImpugnante = data;
            this.isCarregadoRecursoJulgamentoImpugnante = true;
            this.mudarAba(idAba);
          }
        }
      );
    } else {
      this.mudarAba(idAba);
    }
  }

   /**
   * retorna a label da aba de recurso IMPUGNADO com quebra de linha.
   */
  public getTituloAbaRecursoJulgamentoImpugnado(): any {
      if (this.isIES()) {
          return this.messageService.getDescription('LABEL_ABA_RECONSIDERACAO_JULGAMENTO_IMPUGNADO',['<div>','</div><div>','</div>']);
      }
      if (!this.isIES()) {
          return this.messageService.getDescription('LABEL_ABA_RECURSO_JULGAMENTO_IMPUGNADO',['<div>','</div><div>','</div>']);
      }
  }

   /**
   * retorna a label da aba de julgamento segunda instância com quebra de linha.
   */
  public getTituloAbaJulgamentoSegundaInstancia(): any {
    return  this.messageService.getDescription('LABEL_ABA_JULGAMENTO_SEGUNDA_INSTANCIA',['<div>','</div><div>','</div>']);
  }

  /**
   * Verifica se a aba de recurso/reconsideração IMPUGNADO deve ser mostrada.
   */
  public hasRecursoJulgamentoImpugnado(): boolean {
      return (this.impugnacao.hasRecursoJulgamentoImpugnado ||
              this.impugnacao.isFinalizadoAtividadeRecursoJulgamento
              && this.impugnacao.hasJulgamento
          );
  }

  /**
   * Responsavel por carregar os dados referentes a aba do Recurso Impugnado.
   */
  public getDadosRecursoImpugnado(idAba): void {
    if (!this.isCarregadoRecursoJulgamentoImpugnado) {
      this.impugnacaoService.getRecursoJulgamentoPorIdImpugnacao(this.impugnacao.id,
      Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO ).subscribe(
        data => {
          if (data != undefined) {
            this.recursosImpugnado = data;
            this.isCarregadoRecursoJulgamentoImpugnado = true;
            this.mudarAba(idAba);
          }
        }
      );
    } else {
      this.mudarAba(idAba);
    }
  }

    /**
    * Busca imagem de bandeira do estado do CAU/UF.
    * @param idCauUf
    */
  public setImagemBandeira(): void {

    const idCauBR = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : null;

    if(!idCauBR) {
      this.bandeira = this.cauUf.filter((data) => data.id == Constants.ID_CAUBR);
    } else {
      this.bandeira = this.cauUf.filter((data) => data.id == idCauBR);
    }

    this.bandeira = this.bandeira[0];

    if(this.bandeira.id == Constants.ID_CAUBR) {
      this.bandeira.descricao = "IES";
    }
  }

  /**
   * valida se a aba de alegação existe ou não.
   */
  public isAbaAlegacao(): boolean {
    return this.impugnacao.isFinalizadoAtividadeAlegacao  ||  this.impugnacao.hasAlegacao == true;
  }

  /**
   * Responsavel por carregar os dados referentes a alegação.
   */
  public getDadosAlegacao(idAba): void {
    if (!this.isCarregadoAlegacao) {
      this.impugnacaoService.getAlegacaPorIdImpugnacao(this.impugnacao.id).subscribe(
        data => {
          if (data != undefined) {
            this.alegacao = data;
            this.isCarregadoAlegacao = true;
            this.mudarAba(idAba);
          }
        }
      );
    } else {
      this.mudarAba(idAba);
    }
  }

  /**
   * Responsavel por carregar os dados referentes ao Julgamento.
   */
  public getDadosJulgamento(idAba): void {
    if (!this.isCarregadoJulgamento) {
      this.julgamentoAlegacaoService.getJulgamentoAlegacaoImpugnacaoResultado(this.impugnacao.id).subscribe(
        data => {
          if (data != undefined) {
            this.julgamento = data;
            this.isCarregadoJulgamento = true;
            this.mudarAba(idAba);
          }
        }
      );
    } else {
      this.mudarAba(idAba);
    }
  }

  public isAbaJulgamento(): boolean {
    return this.impugnacao.hasJulgamento;
  }

  /**
   * Responsavel por carregar os dados referentes ao Julgamento.
   */
  public getDadosJulgamentoSegundaInstancia(idAba): void {
    if (!this.isCarregadoJulgamentoSegunda){
      this.julgamentoSegundaInstanciaService.getJulgamentoSegundaInstanciaImpugnacaoResultado(this.impugnacao.id).subscribe(
        data => {
          if (data != undefined) {
            this.julgamentoSegunda = data;
            this.isCarregadoJulgamentoSegunda = true;
            this.mudarAba(idAba);
          }
        }
      );
    } else {
      this.mudarAba(idAba);
    }
  }

  public isAbaJulgamentoSegunda(): boolean {
    return this.impugnacao.hasJulgamentoRecurso;
  }

  /**
   * Método responsável fazer redirecionamento para aba de julgamento segunda instância após o cadastro
   * @param evento
   */
  public redirecionarAbaJulgRecursoAposCadastro(evento: any): void {
    this.julgamentoSegunda = evento;
    this.isCarregadoJulgamentoSegunda = true;
    this.impugnacao.hasJulgamentoRecurso = true;
    this.mudarAba(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO_SEGUNDA);
  }
}
