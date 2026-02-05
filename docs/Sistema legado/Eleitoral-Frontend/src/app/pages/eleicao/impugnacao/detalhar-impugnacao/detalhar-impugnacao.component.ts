
import { Component, OnInit } from '@angular/core';
import { MessageService } from '@cau/message';
import { LayoutsService } from '@cau/layout';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from '@angular/router';
import { SecurityService } from '@cau/security';


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

export class DetalharImpugnacaoComponent implements OnInit {

  public abas: any;
  public defesa: any;
  public impugnacao: any;
  public julgamento: any;
  public substituicao: any;
  public isVisualizar: any;
  public recursoImpugnante: any;
  public recursoResponsavel: any;
  public atividadeSecundaria: any;
  public julgamentoSegundaInstancia: any;

  private idCalendario: any;

  public configuracaoCkeditor: any = {};

  public isImpugnacao: boolean;
  public isAssessorUf: boolean = false;

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
    private securityService: SecurityService,
    private layoutsService: LayoutsService,
  ) {
    this.defesa = this.route.snapshot.data['defesa'];
    this.impugnacao = this.route.snapshot.data['impugnacao'];
    this.julgamento = this.route.snapshot.data['julgamento'];
    this.substituicao = this.route.snapshot.data['substituicao'];
    this.recursoImpugnante =  this.route.snapshot.data['recursoImpugnante'];
    this.recursoResponsavel =  this.route.snapshot.data['recursoResponsavel'];
    this.atividadeSecundaria =  this.route.snapshot.data['atividadeSecundaria'];
    this.julgamentoSegundaInstancia = this.route.snapshot.data['julgamentoSegundaInstancia'];
    this.idCalendario = this.route.snapshot.params.idCalendario;
  }

  ngOnInit() {
    this.inicializaAbas();
    this.isImpugnacao = true;
    this.inicializaIconeTitulo();

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
      julgamentoSegundaInstancia: { id: Constants.ABA_DETALHAR_JULGAMENTO_2_INSTANCIA, nome: 'julgamentoSegundaInstancia', ativa: false },
      substituicaoImpugnado:  { id: Constants.ABA_SUBSTITUICAO_IMPUGNADO, nome: 'substituicaoImpugnado', ativa: false }
    };
  }

  /**
   * Metodo responsável por redirecionar a tela de acordo com o parametro da url
   * se a origem de acesso a está página foi pela opção de listar redireciona o
   * usuário para a página anterior. Caso seja acessada após o cadastro de impugnação
   * o usuário é redirecionado para a página inicial.
   */
  public voltar(): void {
    if(this.isImpugnacao) {
      let id = this.impugnacao.cauBR.id;
      id = this.impugnacao.isIES != undefined && this.impugnacao.isIES ? Constants.ID_IES : id;

      this.router.navigate([`/eleicao/impugnacao/acompanhar-impugnacao-uf/${id}/calendario/${this.idCalendario}`]);
    } else {
      this.redirecionaAba(1);
    }

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
  }

  /**
   * Salvar julgamento de impugnação.
   *
   * @param julgamento
   */
  public salvarJulgamento(julgamento: any): void {
      this.julgamento = julgamento;
      this.mudarAba(this.abas.julgamento.id);
  }

  /**
   * Salvar julgamento de impugnação.
   *
   * @param julgamento
   */
  public salvarJulgamentoSegundaInstancia(julgamento: any): void {
    this.julgamentoSegundaInstancia = julgamento;
    this.mudarAba(this.abas.julgamentoSegundaInstancia.id);
}

  /**
   * Redirecionar aba de julgamento.
   * @param idAba
   */
  public redirecionaAba(idAba: number): void {
    this.mudarAba(idAba);
  }

  /*
   * Validação para ver se a aba de julgamento deve estar ativa.
   */
  public validarAbaRecursoResponsavel(): any {
    return this.recursoResponsavel || this.impugnacao.isFinalizadoAtividadeRecurso;
  }

  /*
   * Validação para ver se a aba de julgamento deve estar ativa.
   */
  public validarAbaRecursoImpugnante(): any {
    return this.recursoImpugnante || this.impugnacao.isFinalizadoAtividadeRecurso;
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
   * Responsavel por voltar a tela pro inicio.
   */
  public inicio(): any {
    this.router.navigate(['/']);
  }


  /**
  * retorna a label da aba de pedido de substituição com quebra de linha
  */
   public getTituloAbaSubstituicao(): any {
    return  this.messageService.getDescription('TITLE_ABA_PEDIDO_SUBSTITUICAO',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de pedido de impugnação com quebra de linha
  */
  public getTituloAbaPedidoImpugnacao(): any {
    return  this.messageService.getDescription('TITLE_ABA_PEDIDO_IMPUGNACAO',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de Julgamento 1ª Instância com quebra de linha
  */
  public getTituloAbaJulgamentoPrimeiraInstancia(): any {
    return  this.messageService.getDescription('TITLE_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de pedido de Julgamento de 2ª Instância com quebra de linha
  */
  public getTituloAbaJulgamentoSegundaInstancia(): any {
    return  this.messageService.getDescription('TITLE_ABA_JULGAMENTO_SEGUNDA_INSTANCIA',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de pedido de impugnação com quebra de linha
  */
  public getTituloAbaRecursoResponsavel(): any {
    return  this.messageService.getDescription('TITLE_ABA_RECURSO_RESPONSAVEL',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de reconsideração do responsável com quebra de linha
  */
  public getTituloAbaReconsideracaoResponsavel(): any {
    return  this.messageService.getDescription('TITLE_ABA_RECONSIDERACAO_RESPONSAVEL',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de recurso do impugnante com quebra de linha
  */
  public getTituloAbaRecursoImpugnane(): any {
    return  this.messageService.getDescription('TITLE_ABA_RECURSO_IMPUGNANTE',['<div>','</div><div>','</div>']);
  }

  /**
  * retorna a label da aba de reconsideração do impugnante com quebra de linha
  */
  public getTituloAbaReconsideracaoImpugnante(): any {
    return  this.messageService.getDescription('TITLE_ABA_RECONSIDERACAO_IMPUGNANTE',['<div>','</div><div>','</div>']);
  }


}