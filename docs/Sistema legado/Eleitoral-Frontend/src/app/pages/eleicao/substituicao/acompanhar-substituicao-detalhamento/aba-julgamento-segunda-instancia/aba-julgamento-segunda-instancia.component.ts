import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, Input, Output, ViewChild, TemplateRef } from '@angular/core';
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';

@Component({
  selector: 'aba-julgamento-segunda-instancia',
  templateUrl: './aba-julgamento-segunda-instancia.component.html',
  styleUrls: ['./aba-julgamento-segunda-instancia.component.scss']
})
export class AbaJulgamentoSegundaInstanciaComponent implements OnInit {

  @Input() public pedidoSubstituicao: any
  @Input() public configuracaoCkeditor: any;
  @Input() public julgamentoSubstituicao: any;
  @Input() tipoCandidatura;

  public membroSubstitutoTitular: any = {};
  public membroSubstituidoTitular: any = {};
  public membroSubstitutoSuplente: any = {};
  public membroSubstituidoSuplente: any = {};
  public isIES: boolean;

  public parecer: string;

  /** JULGAMENTO */
  /** JULGAMENTO */

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private substituicaoChapaService: SubstiuicaoChapaClientService,
  ) { }

  ngOnInit() {
    this.inicializaMembros();
    this.isIES = this.tipoCandidatura === Constants.IES;
    this.parecer = this.julgamentoSubstituicao.parecer;
  }

  /**
     * Inicializa os objetos com os membros.
     */
  public inicializaMembros(): void {
    this.membroSubstitutoTitular = this.pedidoSubstituicao.membroSubstitutoTitular;
    this.membroSubstituidoTitular = this.pedidoSubstituicao.membroSubstituidoTitular;
    this.membroSubstitutoSuplente = this.pedidoSubstituicao.membroSubstitutoSuplente;
    this.membroSubstituidoSuplente = this.pedidoSubstituicao.membroSubstituidoSuplente;
  }

  /**
    * Verifica se o membro é  responsáveç.
    * @param id
    */
  public isResponsavel(membro?: any): boolean {
    let validacao = false;
    if (membro) {
      validacao = membro.situacaoResponsavel == true;
    }
    return validacao;
  }

  /**
   * Verifica o status de Validação do Membro.
   *
   * @param membro
   */
  public statusValidacao(membro?: any): boolean {
    let validacao = false;
    if (membro) {
      validacao = membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
    }
    return validacao;
  }

  /**
   * Responsavel por retornar a rota.
   */
  public voltar() : void {
    this.voltarAba.emit();
  }

  /**
    * Métodos de retorno do status da solicitação de substituição de membros
    */
  public getStatusConfirmado(id: any) {
    return id == Constants.STATUS_CONFIRMADO;
  }

  /**
    * Verifica se o julgamento está deferido
    * @param id
    */
  public isDeferido(): boolean {
    return this.julgamentoSubstituicao.statusJulgamentoSubstituicao.id === Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO;
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   *
   * @param event
   * @param resolucao
   */
  public download(event: EventEmitter<any>, julgamento): void {
    this.substituicaoChapaService.getDocumentoJulgamentoSegundaInstancia(julgamento.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Realiza download de documento em formato PDF de julgametno substituição.
   */
  public downloadPdfJulgamentoSubstituicao(event: EventEmitter<any>): void {
    this.substituicaoChapaService.getDocumentoPdfJulgamentoSubstituicao(this.julgamentoSubstituicao.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Função criada para não dar erro quando sobe o ambiente.
   * Favor preencher.
   */
  public inicio(): any {
    this.router.navigate(['/']);
  }
}
