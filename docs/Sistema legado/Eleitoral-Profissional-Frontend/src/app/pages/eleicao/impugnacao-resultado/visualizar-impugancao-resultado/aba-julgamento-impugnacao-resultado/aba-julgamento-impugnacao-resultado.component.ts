import { SecurityService } from '@cau/security';
import { Router } from '@angular/router';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, Output, EventEmitter} from '@angular/core';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ModalCadastrarRecursoComponent } from './modal-cadastrar-recurso/modal-cadastrar-recurso.component';
import { UtilsService } from 'src/app/utils.service';

/**
 * Componente responsável pela apresentação de julgamento de pedido de impugnação.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-julgamento-impugnacao-resultado',
    templateUrl: './aba-julgamento-impugnacao-resultado.component.html',
    styleUrls: ['./aba-julgamento-impugnacao-resultado.component.scss']
})
export class AbaJulgamentoImpugnacaoResultadoComponent implements OnInit {

    @Input() impugnacao?: any;
    @Input() julgamento?: any;
    @Input() validacaoAlegacaoData?: any;
    @Output() isRecursoCadastrado: EventEmitter<any> = new EventEmitter<any>();
    public modalCadastrarRecurso: BsModalRef | null;

    public tipoProfissional: any;
    public arquivos = [];
    public configuracaoCkeditor: any = {};

    /**
     * Construtor da classe.
     *
     * @param route
     * @param messageService
     * @param layoutsService
     */
    constructor(
        private router: Router,
        private utils: UtilsService,
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
    ) {
        this.inicializarJulgamento();
        this.julgamento = route.snapshot.data["idImpugnacao"];
        this.impugnacao = this.route.snapshot.data["impugnacao"];
    }

    ngOnInit() {
      this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional',this.route)
    }

    /**
     * Verifica se o julgamento é Deferido ou não.
     */
    public isDeferido(): boolean {
      return this.julgamento.statusJulgamentoAlegacaoResultado.id == Constants.STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE;
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
     * Verifica se deve ou não mostrar o botão de cadastro de Recurso.
     */
    public isMostrarCadastroRecurso(): boolean {

      let isValidacaoInical = (
        this.impugnacao.hasJulgamento &&
        this.impugnacao.isIniciadoAtividadeRecursoJulgamento &&
        !this.impugnacao.isFinalizadoAtividadeRecursoJulgamento
      )

      if(this.tipoProfissional == Constants.TIPO_PROFISSIONAL) {
        return (
          isValidacaoInical &&
          this.isImpugnante() &&
          !this.impugnacao.hasRecursoJulgamentoImpugnante
        );

      } else if(this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
        return (
          isValidacaoInical &&
          this.validacaoAlegacaoData.isResponsavel &&
          !this.julgamento.hasRecursoPorResponsavelEChapa
        )
      }
    }

    /**
     * Verifica se o usuário é impugnante.
     */
    public isImpugnante(): boolean {
       const user = this.securtyService.credential["_user"];
       return this.impugnacao.profissional.id == user.idProfissional;
    }

    /**
     * Realiza download de arquivo para julgamento de impugnação.
     *
     * @param download
     */
    public downloadArquivo(download: any): void {
        this.impugnacaoService.getDocumentoJulgamento(this.julgamento.id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }
    /**
     * Inicializar julgamento de impugnação.
     */
    public inicializarJulgamento(): void {
        if (this.julgamento == undefined) {
            this.julgamento = {
                descricao: "",
                arquivos: []
            };
        }
    }


      /**
   * Abre o modal para visualizar o histórico.
   */
  public abrirModalCadastrarJulgamento(): void {
    const initialState = {
      impugnacao: this.impugnacao,
      julgamento: this.julgamento,
      isIES: this.isIES(),
      tipoProfissional: this.tipoProfissional
    };

    this.mostraModal(initialState);
  }

    /**
   * Método responsável por chamar a aba de alegação
   * @param initialState
   */
  public mostraModal(initialState: any): void {
    this.modalCadastrarRecurso = this.modalService.show(ModalCadastrarRecursoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));

      this.modalCadastrarRecurso.content.onSave.subscribe(result => {

        if(this.tipoProfissional == Constants.TIPO_PROFISSIONAL) {
          this.impugnacao.hasRecursoJulgamentoImpugnante  = true;
        }
        if(this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
          this.julgamento.hasRecursoPorResponsavelEChapa = true;
          this.impugnacao.hasRecursoJulgamentoImpugnado = true;
        }
        this.isRecursoCadastrado.emit(result);
      });
  }
}
