import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, ViewChild, TemplateRef, Output, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { JulgamentoAlegacaoImpugnacaoResultadoClientService } from 'src/app/client/julgamento-alegacao-impugnacao-resultado-client/julgamento-alegacao-impugnacao-resultado-client.service';
import { Constants } from 'src/app/constants.service';


@Component({
  selector: 'aba-recurso-julgamento-impugnado',
  templateUrl: './aba-recurso-julgamento-impugnado.component.html',
  styleUrls: ['./aba-recurso-julgamento-impugnado.component.scss']
})
export class AbaRecursoJulgamentoImpugnadoComponent implements OnInit {

  public modalRef: BsModalRef | null;

  @Input() bandeira: any;
  @Input() recursos: any;
  @Input() impugnacao: any;
  @Input() validacaoAlegacaoData: any;

  @Output() mudarAba: EventEmitter<any> = new EventEmitter();
  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private layoutsService: LayoutsService,
    private securityService: SecurityService,
    private messageService: MessageService,
  ) {
  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {
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
    this.voltarAba.emit(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO);
  }

  /**
   * Verifica se o julgamento é IES ou não.
   * @param id
   */
  public isIES(): boolean {
    let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
    return (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
  }

  /**
   * Recarrega aba de recurso.
   */
  public onRecarregarRecurso(): void {
    this.mudarAba.emit(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO);
  }

  /**
   * 
   * @param recurso 
   */
  public getNumeroChapa(recurso): string {
    const numChapa = recurso.numeroChapa && recurso.numeroChapa != '' ? recurso.numeroChapa : undefined;
    return numChapa ? numChapa : this.messageService.getDescription('LABEL_NAO_APLICADO');
  }
}
