import { Component, OnInit, TemplateRef, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { ConviteChapaEleicaoClientService } from 'src/app/client/convite-chapa-eleicao-client/convite-chapa-eleicao-client.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';

import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { timeStamp } from 'console';

@Component({
  selector: 'app-form-declaracao-representatividade',
  templateUrl: './form-declaracao-representatividade.component.html',
  styleUrls: ['./form-declaracao-representatividade.component.scss']
})
export class FormDeclaracaoRepresentatividadeComponent implements OnInit {
  @Input() public convite: any;
  @Input() public curriculo: any;
  @Input() public usuario: any;
  @Input() public declaracoes: any;
  @Output() public confirmarEvent: EventEmitter<any> = new EventEmitter<any>();
  @Output() public voltarEvent: EventEmitter<any> = new EventEmitter<any>();
  @Output() public cancelarEvent: EventEmitter<any> = new EventEmitter<any>();

  public declaracaoParametrizada: any;
  public modalRef: BsModalRef;
  public confirmarDeclaracao: any;
  public semRepresentatividde: any;
  public mostrarArquivo: boolean = false;
  public arquivo: any;
  public tamanhoArquivo: number = 0;
  public tamanhoArquivos: any;
  public arquivosBase64: any = [];

  private _dataForm: any;

  /**
   * Método contrutor da classe
   *
   * @param route
   * @param router
   * @param messageService
   * @param modalService
   * @param ConviteChapaEleicaoClientService
   * @param chapaEleicaoClientService
   */
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private messageService: MessageService,
    private modalService: BsModalService,
    private conviteChapaEleicaoService: ConviteChapaEleicaoClientService,
    private chapaEleicaoClientService: ChapaEleicaoClientService
  ) {

  }

  /**
   * Inicialização das dependências do componente.
   */
  ngOnInit() {
    if (this.convite.tipoParticChapa == 'Titular' && this.convite.tipoCandidatura == Constants.TIPO_CONSELHEIRO_UF_BR) {
      this.inicializarDeclaracao();
      return;
    }

    this.confimar();
  }

  /**
   * Confirma preenchimento da delação de participação de eleição.
   */
  public confirmar(): void {
    this.confirmarEvent.emit(null);
  }

  /**
   * Voltar para a pagina de Currículo do membro da chapa..
   */
  public voltar(): void {
    if (this.isCamposAlterados()) {
      this.messageService.addConfirmYesNo('MSG_CONFIRMA_VOLTAR', () => {
        this.voltarEvent.emit(null);
      });
    } else {
      this.voltarEvent.emit(null);
    }
  }

  /**
   * Verifica se houve alteração nos campos do formulário.
   */
  public isCamposAlterados(): boolean {
    return !deepEqual(this._dataForm, this.getDataForm);
  }

  /**
   * Inicializa declaração de representatividade.
   */
  public inicializarDeclaracao(): void {
    this.declaracaoParametrizada = this.chapaEleicaoClientService.getDeclaracaoParametrizada(this.convite.idAtividadeSecundariaConvite, Constants.TIPO_DECLARACAO_REPRESENTATIVIDADE).subscribe(
      data => {
        this.declaracaoParametrizada = data;
        this._dataForm = this.getDataForm();
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  public validarCampos(): void {
    if (this.isRespostaDeclaracaoValida()) {
      this.confimar();
    } else {
      this.messageService.addMsgDanger('required');
    }
  }

  /**
   * Confirmar declaração e aceitar convite.
   */
  public confimar(): void {
    this.conviteChapaEleicaoService.aceitarConvite(this.getDataForm()).subscribe(
      data => {
        const prefixo = this.convite.tipoCandidatura == Constants.TIPO_CONSELHEIRO_IES ? Constants.IES : this.usuario.cauUf.prefixo;
        this.messageService.addConfirmOk(this.messageService.getDescription('MSG_ACEITAR_CONVITE_CHAPA_ELEITORAL', [prefixo]), () => {
          this.router.navigate(['eleicao', 'visualizar-chapa']);
        });
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Cancelar currículo membro chapa.
   */
  public cancelar(): void {
    this.modalRef.hide();
    this.cancelarEvent.emit();
  }

  /**
   * Exibe modal de confirmação de cancelamento.
   */
  public abrirModalConfirmarCancelamento(template: TemplateRef<any>): void {
    this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
  }

  /**
   * Verifica se a resposta da declaração é valida.
   */
  private isRespostaDeclaracaoValida(): boolean {
    return (this.getIdsItemDeclaracao().length > 0 || this.semRepresentatividde) && this.confirmarDeclaracao;
  }

  /**
   * Monta objeto com as informação necessária para aceitar o convite, que são currículo e declaração.
   */
  private getDataForm(): any {
    if (Constants.TIPO_CONSELHEIRO_IES == this.convite.tipoCandidatura) {
      return {
        declaracoes: this.declaracoes,
        idChapaEleicao: this.convite.idChapaEleicao,
        idMembroChapa: this.convite.idMembroChapa,
        sinteseCurriculo: this.curriculo.descricao,
        fotoSinteseCurriculo: this.curriculo.fotoCropped,
        cartasIndicacaoInstituicao: [this.curriculo.cartaIndicacao.file],
        comprovantesVinculoDocenteIes: [this.curriculo.comprovanteDocente.file],
        representatividade: this.convite.tipoParticChapa == 'Titular' ? this.getIdsItemDeclaracao() : [],
        arquivos: this.arquivosBase64
      };
    }
    else {
      return {
        declaracoes: this.declaracoes,
        idChapaEleicao: this.convite.idChapaEleicao,
        idMembroChapa: this.convite.idMembroChapa,
        sinteseCurriculo: this.curriculo.descricao,
        fotoSinteseCurriculo: this.curriculo.fotoCropped,
        representatividade: this.convite.tipoParticChapa == 'Titular' ? this.getIdsItemDeclaracao() : [],
        arquivos: this.arquivosBase64
      };
    }

  }

  /**
   * Retorna lista de ids com todos os itens de declarações selecionados.
   */
  private getIdsItemDeclaracao(): Array<number> {
    let idsItemDeclaracao = [];
    if (this.declaracaoParametrizada && this.declaracaoParametrizada.declaracao && this.declaracaoParametrizada.declaracao.itensDeclaracao && this.declaracaoParametrizada.declaracao.itensDeclaracao.length > 0) {
      this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(
        itemDeclaracao => {
          if (itemDeclaracao.valor) {
            idsItemDeclaracao.push(itemDeclaracao.id);
          }
        }
      );
      return idsItemDeclaracao;
    }
  }

  /**
   * Desmarca todos os itens da declaração
   */
  public limparRepresentatividade(): void {
    if (this.semRepresentatividde) {
      this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(element => {
        element.valor = false;
      });
      this.mostrarArquivo = false;
    }
  }

  /**
   * Validação para mostrar input de arquivos
  */
  public canShowFile(itemDeclaracao: any) {
    if (itemDeclaracao.id == Constants.TIPO_PESSOA_FORMACAO_10_ANOS || itemDeclaracao.id == Constants.TIPO_PESSOA_FORMACAO_INTERIOR) {
      this.mostrarArquivo = itemDeclaracao.valor == true;
      !itemDeclaracao.valor ? this.removerArquivo() : '';
    }
  }

  /**
   * Upload do documento de representatividade
   *
   * @param arquivoEvent
   */
  public validarDocumentos(arquivoEvent): void {
    if (arquivoEvent.size > Constants.TAMANHO_LIMITE_ARQUIVO) {
      this.messageService.addMsgDanger(this.messageService.getDescription('MSG_TAMANHO_MAXIMO_10MB'));
      this.removerArquivo();
      return;
    }

    if (!/(.*?)\.(pdf)/i.test(arquivoEvent.name)) {
      this.messageService.addMsgDanger(
        `Formato do arquivo inválido.`
      );
      this.removerArquivo();
      return;
    }

    this.adicionarArquivo(arquivoEvent);
    this.tamanhoArquivos = this.tamanhoArquivo;
    if (this.tamanhoArquivos > Constants.TAMANHO_LIMITE_ARQUIVO) {
      this.messageService.addMsgDanger(this.messageService.getDescription('MSG_TAMANHO_MAXIMO_CONJUNTO_10MB'));
      this.removerArquivo()
      this.tamanhoArquivos = this.tamanhoArquivo;
      return;
    }

    this.uparDocumentos();
  }

  /**
   * Adiciona arquivo selecionado no input
   *
   * @param arquivoEvent
   */
  public adicionarArquivo(arquivoEvent): void {
    this.arquivo = arquivoEvent;
    this.tamanhoArquivo = arquivoEvent.size
  }

  /**
   * Remove arquivo do input
   *
   */
  public removerArquivo(): void {
    this.arquivo = null;
    this.tamanhoArquivo = 0;
  }

  /*
  * Criar array de documentos Base64
  */
  public uparDocumentos(): void {
    this.arquivosBase64 = [];
    if (this.arquivo) {
      const reader = new FileReader();
      reader.readAsDataURL(this.arquivo);
      reader.onload = () => {
        let arquivoBase64 = reader.result;
        this.arquivosBase64.push(arquivoBase64);
      };
    }
  }
}
