import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, ViewChild, TemplateRef, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Constants } from 'src/app/constants.service';
import * as moment from 'moment';


@Component({
  selector: 'aba-recurso-julgamento-impugnado',
  templateUrl: './aba-recurso-julgamento-impugnado.component.html',
  styleUrls: ['./aba-recurso-julgamento-impugnado.component.scss']
})
export class AbaRecursoJulgamentoImpugnadoComponent implements OnInit {


  @Input() bandeira: any;
  @Input() recursos: any;
  @Input() impugnacao: any;
  @Input() julgamento?: any;

  public modalRef: BsModalRef | null;
  public recursoContrarrazao: any;

  @Output() mudarAba: EventEmitter<any> = new EventEmitter();
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();
  @Output() mudarAbaJulgRecursoAposCadastro: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateCadastroImpugnacaoResultadoJulgamentoSegundaInstancia', { static: true })
  private templateCadastroJulgamentoSegundaInstancia: TemplateRef<any>;

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private modalService: BsModalService,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private messageService: MessageService
  ) {
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
    this.inicializaDados();
  }

  /**
   * Verifica se o usuário logado é Acessor CEN ou CE.
   */
  public isAcessorCENouCE(idUf: number): boolean {
    let isAcessorCEN: boolean = this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    let isAcessorCE: boolean = this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE]) && idUf == this.securityService.credential.user.cauUf.id;
    let isIES: boolean = (idUf == 0);

    return (isIES) ? (isAcessorCEN) : (isAcessorCEN || isAcessorCE);
  }

  /**
   * Responsavel por voltar a tela pro inicio.
   */
  public inicio(): any {
    this.router.navigate(['/']);
    this.layoutsService.onLoadTitle.emit({
      description: ''
    });
  }

  /**
   * Responsável por inicializar os dados do Recurso
   */
  public inicializaDados() {
    if (this.recursos && this.recursos.length > 0) {
      // Pega o último elemento
      const last = this.recursos.length - 1;

      const recurso = this.recursos[last];
      const arquivos = this.recursos[last].nomeArquivo;

      this.recursoContrarrazao = {
        id: recurso.id,
        numero: recurso.numero,
        descricao: recurso.descricao,
        dataCadastro: moment.utc(recurso.dataCadastro),
        nome: recurso.nomeArquivo,
        nomeFisico: recurso.nomeArquivoFisico,
        contrarrazoes: (recurso.contrarrazoesRecursoImpugnacaoResultado ? recurso.contrarrazoesRecursoImpugnacaoResultado : [])
      };
    }
  }

  /**
   * Retorna número da chapa formatado.
   *
   * @param recurso
   */
  public getNumeroChapa(recurso): string {
    const numChapa = recurso.numeroChapa && recurso.numeroChapa != '' ? recurso.numeroChapa : undefined;
    return numChapa ? numChapa : this.messageService.getDescription('LABEL_NAO_APLICADO');
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
   * Volta para a página da uf da solicitação
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO);
  }

  public cadastrarJulgamentoSegundaInstancia(): void {
    this.modalRef = this.modalService.show(
      this.templateCadastroJulgamentoSegundaInstancia,
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  public afterCadastrarJulgamento(evento: any): void {
    this.modalRef.hide();
    this.mudarAbaJulgRecursoAposCadastro.emit(evento);
  }

    /**
   * Retorna a label do botão de acordo com o cadastro do recurso
   */
  public getLabelBotaoCadastrarJulgamento():string {
    if(!this.impugnacao.hasRecursoJulgamentoImpugnado && !this.impugnacao.hasRecursoJulgamentoImpugnante){
      return this.messageService.getDescription('LABEL_HOMOLOGAR_JULGAMENTO');
    } else {
      return this.messageService.getDescription('LABEL_JULGAMENTO_SEGUNDA_INSTANCIA');
    }
  }

  /**
   * Verifica se o julgamento em primeira instância foi deferido ou indeferido
   */
  public isjulgamentoProcedente(): boolean {
    if (this.julgamento == undefined) {
      return false;
    } else {
      return this.julgamento.statusJulgamentoAlegacaoResultado.id == Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
    }
  }

  /**
   * Retorna se deve apresentar ou não o botão de cadastro de julgamento segunda instância
   */
  public isMostrarBotaoCadastrojugamentoSegundaInstancia(): boolean {
    if (!this.impugnacao.hasRecursoJulgamentoImpugnado && !this.impugnacao.hasRecursoJulgamentoImpugnante){
      return (
        this.impugnacao.isIniciadoAtividadeJulgamentoRecurso &&
        this.isjulgamentoProcedente() && 
        !this.isIES() &&
        this.impugnacao.hasJulgamento && 
        !this.impugnacao.hasJulgamentoRecurso &&
        this.isAcessorCEN()
      );
    } else {
      return (
        this.impugnacao.isIniciadoAtividadeJulgamentoRecurso &&
        this.impugnacao.hasJulgamento && 
        !this.impugnacao.hasJulgamentoRecurso &&
        this.isAcessorCEN()
      );
    }
  }

  /**
   * Verifica se o tipo de ação de cadastro de julgamento é 
   * Julgamento segunda instancia ou homologação
   */
  public isHomologacao(): boolean {
    return this.recursos.length == 0 || this.recursos.length == undefined;
  }

  /**
   * verifica se o usuário logado tem permissão de Assessor CEN
   */
  public isAcessorCEN(): boolean {
    return  this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
  }
}
