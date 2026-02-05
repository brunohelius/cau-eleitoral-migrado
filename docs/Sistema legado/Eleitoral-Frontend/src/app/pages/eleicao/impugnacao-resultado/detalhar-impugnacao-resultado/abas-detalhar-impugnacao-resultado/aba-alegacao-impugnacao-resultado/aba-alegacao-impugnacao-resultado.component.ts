import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, ViewChild, TemplateRef, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoAlegacaoImpugnacaoResultadoClientService } from 'src/app/client/julgamento-alegacao-impugnacao-resultado-client/julgamento-alegacao-impugnacao-resultado-client.service';
import { Constants } from 'src/app/constants.service';
import { CdkNestedTreeNode } from '@angular/cdk/tree';


@Component({
  selector: 'aba-alegacao-impugnacao-resultado',
  templateUrl: './aba-alegacao-impugnacao-resultado.component.html',
  styleUrls: ['./aba-alegacao-impugnacao-resultado.component.scss']
})
export class AbAlegacaoImpugnacaoResultadoComponent implements OnInit {

  public modalRef: BsModalRef | null;

  @Input() bandeira: any;
  @Input() alegacoes: any;
  @Input() impugnacao: any;

  @Output() mudarAba: EventEmitter<any> = new EventEmitter();
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateCadastroImpugnacaoResultadoJulgamentoPrimeiraInstancia', { static: true })
  private templateCadastroJulgamentoPrimeiraInstancia: TemplateRef<any>;

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private impugnacaoResultadoClientService: JulgamentoAlegacaoImpugnacaoResultadoClientService,
    private modalService: BsModalService
  ) {

  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
  }

  /**
   * Abrir Modal de Cadastro de Julgamento Alegação de I.R.
   */
  public cadastrarJulgamento(): void {
    this.modalRef = this.modalService.show(
      this.templateCadastroJulgamentoPrimeiraInstancia, 
      Object.assign({ ignoreBackdropClick: true }, { class: 'modal-xl' })
    );
  }

  /**
   * Verifica se o botão de cadastro de julgamento deve ser mostrado.
   */
  public isMostrarCadastrarJulgamento(): boolean {
    let idCaubr = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : 0;
    return !this.impugnacao.hasJulgamento &&
            this.isAcessorCENouCE(idCaubr) &&
            this.impugnacao.isIniciadoAtividadeJulgamento &&
            !this.impugnacao.isFinalizadoAtividadeJulgamento;
  }

  /**
   * Muda para aba de julgamento e fecha modal, após o cadastro de julgamento de alegação.
   *
   * @param event
   */
  public afterCadastrarJulgamento(event): void {
    this.ocultarBotao();
    this.mudarAba.emit(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO);
  }

  /**
   * Ocuta o botão de cadastro de julgamento e fecha o modal de cadastro de Julgamento.
   */
  public ocultarBotao() {
      this.modalRef.hide();
      this.impugnacao.hasJulgamento = true;
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
   * Volta para a página da uf da solicitação
   */
  public voltar(): any {
    this.voltarAba.emit(Constants.ABA_DETALHAR_IMPUGNACAO_RESULTADO_DETALHAR);
  }
}
