import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { StringService } from 'src/app/string.service';
import { Router, ActivatedRoute } from '@angular/router';
import { format } from 'url';
import { NgForm } from '@angular/forms';
import { Constants } from 'src/app/constants.service';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { ModalCadastrarAlegacaoComponent } from './modal-cadastrar-alegacao/modal-cadastrar-alegacao.component';


@Component({
  selector: 'aba-visualizar-impugnacao',
  templateUrl: './aba-visualizar-impugnacao.component.html',
  styleUrls: ['./aba-visualizar-impugnacao.component.scss']
})
export class AbaVisualizarImpugnacaoComponent implements OnInit {

    @Input() cauUf: any;
    @Input() impugnacao: any;
    @Input() tipoProfissional: string;
    @Input() validacaoAlegacaoData: any;
    @Output() isCadastrado: EventEmitter<any> = new EventEmitter<any>();
    public modalCadastrarAlegacao: BsModalRef | null;


    /**
     * Construtor da classe.
     */
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private securtyService: SecurityService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
        private impugnacaoResultadoService : ImpugnacaoResultadoClientService
        ) {

    }

    /**
     * Quando o componente inicializar.
     */
    ngOnInit() {
    }

    /**
     * Retorna o registro com a mascara.
     *
     * @param string
     */
    public getRegistroComMask(string): any {
        return StringService.maskRegistroProfissional(string);
    }

    /**
     * Recupera o arquivo conforme a entidade 'resolucao' informada.
     */
    public downloadArquivo(event: any): void {
        this.impugnacaoService.getDocumento(this.impugnacao.id).subscribe((data: Blob) => {
            event.evento.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

  /**
   * Abre o modal para visualizar o histórico.
   */
  public abrirModalCadastrarAlegacao(idPedidoImpugacao?: number): void {
    const initialState = {
      impugnacao: this.impugnacao
    };

    if(this.validacaoAlegacaoData.hasAlegacao) {
      this.messageService.addConfirmYesNo('MSG_JA_EXISTE_ALEGACAO_CADASTRADO',  () => {
        this.mostraModal(initialState);
      });
    } else {
      this.mostraModal(initialState);
    }
  }

  /**
   * Método responsável por chamar a aba de alegação
   * @param initialState 
   */
  public mostraModal(initialState: any): void {
    this.modalCadastrarAlegacao = this.modalService.show(ModalCadastrarAlegacaoComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));

      this.modalCadastrarAlegacao.content.onSave.subscribe(result => {
          this.isCadastrado.emit()
      });
  }
  /**
   * Verifica se o botão de Cadastro de alegação deve ser mostrado.
   */
  public isMostrarCadastroAlegacao(): boolean {
    return (
      this.validacaoAlegacaoData.isResponsavel &&
      this.validacaoAlegacaoData.isVigenteAtivCadastroAlegacaoImpugResultado &&
      this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA &&
      !this.impugnacao.hasJulgamento
    );
  }

}
