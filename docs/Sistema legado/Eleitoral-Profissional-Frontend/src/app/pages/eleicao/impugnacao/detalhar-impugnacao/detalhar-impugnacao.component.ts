import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from '@angular/router';

import { DefesaImpugnacaoService } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-client.service';
import { JulgamentoImpugnacaoService } from 'src/app/client/julgamento-impugnacao-client.service.ts/julgamento-impugnacao-client.service';
import { ImpugnacaoCandidaturaClientService } from 'src/app/client/impugnacao-candidatura-client/impugnacao-candidatura-client.service';
import { dateFormat } from 'highcharts';


/**
 * Componente responsável pela apresentação do detalhamento do pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'detalhar-impugnacao',
    templateUrl: './detalhar-impugnacao.component.html',
    styleUrls: ['./detalhar-impugnacao.component.scss']
})

export class DetalharImpugnacao implements OnInit {

  public abas: any;
  public defesa: any;
  public paramIsIES: any;
  public julgamento: any;
  public impugnacao: any;
  public isVisualizar: any;
  public substituicao: any;
  public defesaValidacao: any;
  public idTipoSolicitacao: any;
  public recursoImpugnante: any;
  public recursoResponsavel: any;
  public configuracaoCkeditor: any;
  public recursoReconsideracao: any;
  public julgamentoSegundaInstancia: any;

  public isAbaDefesa: boolean;
  public isAbaImpugnacao: boolean;

  public isRecursoCadastrado: any;
  public isSubstituicaoCadastrada: boolean;

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
    private julgamentoService: JulgamentoImpugnacaoService,
    private defesaImpugnacaoService: DefesaImpugnacaoService,
    private impugnacaoCandidaturaClientService: ImpugnacaoCandidaturaClientService
  ) {
    this.impugnacao = this.route.snapshot.data['impugnacao'];
    this.defesaValidacao = this.route.snapshot.data['defesaValidacao'];
    this.julgamentoSegundaInstancia = this.route.snapshot.data['julgamentoSegundaInstancia'];
    this.substituicao = this.route.snapshot.data['substituicao'];
  }

  ngOnInit() {
    this.inicializaAbas();
    this.inicializaJulgamento();
    this.inicializaIconeTitulo();
    this.inicializaIdTipoSolicitacao();
    this.inicializaConfiguracaoCkeditor();

    this.incializaRecursos();
    this.paramIsIES = this.route.snapshot.paramMap.get('isIES');
    this.isVisualizar = this.route.snapshot.paramMap.get('visualizar');
    this.isRecursoReconsideracao();
  }

  /**
   * Acionar aba defesa.
   *
   * @param defesa
   */
  public mudarParaAbaDefesa(): void {
    this.defesaImpugnacaoService.getPorPedidoImpugnacao(this.impugnacao.id).subscribe(
      data => {
        this.defesa = data;
        this.mudarAba( this.abas.defesa.id);
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );

  }

  /**
   * inicializa o idTipoSolicitacao.
   */
  public inicializaIdTipoSolicitacao(): any {
    if (this.isResponsavelChapa()) {
      this.idTipoSolicitacao = Constants.ID_TIPO_RESPONSAVEL;
    } else if (this.isImpugante()) {
      this.idTipoSolicitacao = Constants.ID_TIPO_IMPUGNANTE;
    }
  }

  /**
   * Inicializa o recurso.
   */
  public incializaRecursos(): any {
    this.julgamentoService.getRecursoJulgamento(this.impugnacao.id, Constants.ID_TIPO_RESPONSAVEL).subscribe(
      data => {
        this.recursoResponsavel = data;
      }, error => {
        this.messageService.addMsgDanger(error);
      }
    );

    this.julgamentoService.getRecursoJulgamento(this.impugnacao.id, Constants.ID_TIPO_IMPUGNANTE).subscribe(
      data => {
        this.recursoImpugnante = data
      }, error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Inicializa Julgamento.
   */
  public inicializaJulgamento():any {
    if ( this.getValorParamDoRoute('tipoProfissional') == Constants.TIPO_PROFISSIONAL_COMISSAO) {
      this.julgamentoService.getPedidoImpugnacaoMembroComissao(this.impugnacao.id).subscribe(
        data => {
          this.julgamento = data;
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else {
      this.julgamentoService.getPedidoImpugnacaoResponsavel(this.impugnacao.id).subscribe(
        data => {
          this.julgamento = data;
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Acionar aba julgamento.
   *
   * @param julgamento
   */
  public mudarParaAbaJulgamento(): void {
    this.mudarAba( this.abas.julgamento.id);
  }
  
  /**
   * Acionar aba recurso responsavel.
   *
   * @param julgamento
   */
  public mudarParaAbaRecursoResponsavel(): void {
    this.mudarAba( this.abas.recursoResponsavel.id);
  }
  
  /**
   * Acionar aba recurso impugnante.
   *
   * @param julgamento
   */
  public mudarParaAbaRecursoImpungnante(): void {
    this.mudarAba( this.abas.recursoImpugnante.id);
  }

  /**
   * Acionar aba julgamento segunda instancia.
   *
   * @param julgamento
   */
  public mudarParaAbaJulgamentoSegundaInstancia(): void {
    this.mudarAba( this.abas.julgamentoSegundaInstancia.id);
  }

  /**
   * Acionar aba Pedido de Substituição.
   *
   * @param julgamento
   */
  public mudarParaAbaPedidoSubstituicao(): void {
    this.mudarAba( this.abas.substituicaoImpugnado.id);
  }

  /**
   * Concelar Cadastro de defesa impugnação.
   */
  public cancelarDefesaImpugnacao(): void {
    this.mudarAba(Constants.ABA_DETALHAR_IMPUGNACAO_PEDIDO);
  }

  /**
   * Inicializa ícone e título do header da página .
   */
  private inicializaIconeTitulo(): void {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('TITLE_PEDIDO_DE_IMPUGNACAO')
    });
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    this.abas = {
      abaVisaoGeral: { id: Constants.ABA_DETALHAR_IMPUGNACAO_VISAO_GERAL, nome: 'visaoGeral', ativa: false },
      pedido: { id: Constants.ABA_DETALHAR_IMPUGNACAO_PEDIDO, nome: 'pedido', ativa: true },
      defesa: { id: Constants.ABA_DETALHAR_IMPUGNACAO_DEFESA, nome: 'defesa', ativa: false },
      julgamento: { id: Constants.ABA_DETALHAR_IMPUGNACAO_JULGAMENTO, nome: 'julgamento', ativa: false },
      recursoResponsavel: { id: Constants.ABA_DETALHAR_RECURSO_RESPONSAVEL, nome: 'recursoResponsavel', ativa: false },
      recursoImpugnante: { id: Constants.ABA_DETALHAR_RECURSO_IMPUGNANTE, nome: 'recursoImpugnante', ativa: false },
      julgamentoSegundaInstancia: { id: Constants.ABA_DETALHAR_JULGAMENTO_SEGUNDA_INSTANCIA, nome: 'julgamentoSegundaInstancia', ativa: false },
      substituicaoImpugnado: { id: Constants.ABA_SUBSTITUICAO_IMPUGNADO, nome: 'substituicaoImpugnado', ativa: false }
    };
  }

  /**
   * Metodo responsável por redirecionar a tela de acordo com o parametro da url
   * se a origem de acesso a está página foi pela opção de listar redireciona o
   * usuário para a página anterior. Caso seja acessada após o cadastro de impugnação
   * o usuário é redirecionado para a página inicial.
   */
  public voltar(): void {

    let id = (this.paramIsIES == true || this.paramIsIES == "true") ? Constants.ID_IES : this.impugnacao.idCauUf;

    let tipoProfissional = this.getValorParamDoRoute('tipoProfissional');

    let ds_url = `impugnacao-responsavel-solicitacao/${id}`;
    if (tipoProfissional  != Constants.TIPO_PROFISSIONAL){
      ds_url = tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA ? 'acompanhar-impugnacao-responsavel' : `acompanhar-impugnacao-uf/${id}`;
    }

    if(this.isVisualizar == true || this.isVisualizar == "true") {

      this.router.navigate([`/eleicao/impugnacao/${ds_url}`]);

    } else {
      this.router.navigate([`/`]);
    }

  }

  /**
   * Retorna pra tela anterior apos o salvamento.
   */
  public redirecionarAposSalvamento(): any {
    this.voltar();
  }

  /**
   * Mudar aba que foi selecionada.
   *
   * @param idAba
   */
  public mudarAba(idAba: number): void {
    this.abas.abaVisaoGeral.ativa = this.abas.abaVisaoGeral.id == idAba;
    this.abas.pedido.ativa = this.abas.pedido.id == idAba;
    this.abas.defesa.ativa = this.abas.defesa.id == idAba;
    this.abas.julgamento.ativa = this.abas.julgamento.id == idAba;
    this.abas.recursoResponsavel.ativa = this.abas.recursoResponsavel.id == idAba;
    this.abas.recursoImpugnante.ativa = this.abas.recursoImpugnante.id == idAba;
    this.abas.julgamentoSegundaInstancia.ativa = this.abas.julgamentoSegundaInstancia.id == idAba;
    this.abas.substituicaoImpugnado.ativa = this.abas.substituicaoImpugnado.id == idAba;
    this.ativaAba(idAba);
  }

  /**
   * Realiza o controle de qual aba está ativa
   * @param idAba
   */
  public ativaAba(idAba: number): void {
    if( idAba == Constants.ABA_DETALHAR_IMPUGNACAO_DEFESA ) {
      this.isAbaDefesa = true;
      this.isAbaImpugnacao = false;
    } else if (idAba == Constants.ABA_DETALHAR_IMPUGNACAO_DEFESA) {
      this.isAbaDefesa = false;
      this.isAbaImpugnacao = true;
    }
  }

  /**
   * Retorna um valor de parâmetro passado na rota
   * @param nameParam
   */
  private getValorParamDoRoute(nameParam):any {
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

  /**
   * Validação para ver se a aba de julgamento deve estar ativa.
   */
  public validarAbaJulgamento(): any {

    let isAcessoComissao = this.getValorParamDoRoute('tipoProfissional') == Constants.TIPO_PROFISSIONAL_COMISSAO;

    return this.julgamento && (isAcessoComissao || (!isAcessoComissao && this.impugnacao.isIniciadoAtividadeRecurso));
  }
  
  /**
   * Validação para ver se a aba de recurso do responsavel deve estar ativa.
   */
  public validarAbaRecursoResponsavel(): any {
    return this.recursoResponsavel || this.impugnacao.isFinalizadoAtividadeRecurso;
  }

  /**
   * Validação para ver se a aba de recurso do impugnante deve estar ativa.
   */
  public validarAbaRecursoImpugnante(): any {
    return this.recursoImpugnante || this.impugnacao.isFinalizadoAtividadeRecurso;
  }

  /**
   * Validação para ver se a aba de julgamento de segunda instancia deve estar ativa.
   */
  public validarAbaJulgamentoSegundaInstancia(): any {
    return this.julgamentoSegundaInstancia && this.impugnacao.isFinalizadoAtividadeJulgamentoRecurso;
  }

  /**
   * Validação para ver se a aba de substituição deve estar ativa
   */
  public validarAbaPedidoDeSubstituicao(): any {
    return (
      this.substituicao != null
      && this.substituicao != undefined  
    )
  }

  /**
   * Redireciona para a aba de recurso apos o salvamento.
   */
  public redirecionarAbaRecurso(obj: any): void {
    this.isRecursoCadastrado = true;
    if (obj.idTipoSolicitacao == Constants.ID_TIPO_IMPUGNANTE){
      this.recursoImpugnante = obj.recurso;
    } else {
      this.recursoResponsavel = obj.recurso;
    }
    
    let aba = this.isResponsavelChapa()? this.abas.recursoResponsavel.id : this.abas.recursoImpugnante.id;
    this.mudarAba(aba);
  }



  /**
   * Redireciona para a aba de visualiar pedido de substituição após o salvamento
   * @param obj 
   */
  public redirecionarAbaSubstituicaoAposSalvamento(obj: any): void {
    this.substituicao = obj;
    this.isSubstituicaoCadastrada = true;
    this.mudarParaAbaPedidoSubstituicao();
  
  }


  /**
    * Verifica se o usuário logado é responsavel pela chapa.
    */
  public isResponsavelChapa(): boolean {
    return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL_CHAPA;
  }

  /**
  * Verifica se o usuário logado é o impugnante.
  */
  public isImpugante(): boolean {
    return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL;
  }



  /**
   * ===================================================================================
   * grupo de títulos de abas de navegação
   * ===================================================================================
   */

  /**
  * retorna o título da aba de substituição com quebra de linha
  */
  public getTituloAbaSubstituicao(): any {
    return  this.messageService.getDescription('TITLE_ABA_PEDIDO_DE_SUBSTITUICAO',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna o título da aba de Impugnacao com quebra de linha
  */
  public getTituloAbaPedidoImpugnacao(): any {
    return  this.messageService.getDescription('TITLE_ABA_PEDIDO_IMPUGNACAO', ['<div>','</div><div>','</div>']);
  }

  /**
  * retorna o título da aba de Julgamento de PrimeiraInstancia com quebra de linha
  */
  public getTituloAbaJulgamentoPrimeiraInstancia(): any {
    return  this.messageService.getDescription('TITLE_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna o título da aba de Recurso Responsavel com quebra de linha
  */
  public getTituloAbaRecursoResponsavel(): any {
    return  this.messageService.getDescription('TITLE_ABA_RESPONSAVEL', ['<div>',this.recursoReconsideracao,'</div><div>','</div>']);
  }
  
  /**
   * Retorna se e recurso ou reconsideração
   */
  public isRecursoReconsideracao(): any {
    this.recursoReconsideracao = this.messageService.getDescription((this.impugnacao.isIES)? 'LABEL_RECONSIDERACAO' : 'LABEL_RECURSO');
  }

  /**
  * retorna o título da aba de Recurso Impugnant com quebra de linha
  */
  public getTituloAbaRecursoImpugnante(): any {
    return  this.messageService.getDescription('TITLE_ABA_IMPUGNANTE', ['<div>',this.recursoReconsideracao,'</div><div>','</div>']);
  }

  /**
  * retorna o título da aba de Julgamento de SegundaInstância com quebra de linha
  */
  public getTituloAbaJulgamentoSegundaInstancia(): any {
    return  this.messageService.getDescription('TITLE_ABA_JULGAMENTO_SEGUNDA_INSTANCIA', ['<div>','</div><div>','</div>']);
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
    };
  }
}