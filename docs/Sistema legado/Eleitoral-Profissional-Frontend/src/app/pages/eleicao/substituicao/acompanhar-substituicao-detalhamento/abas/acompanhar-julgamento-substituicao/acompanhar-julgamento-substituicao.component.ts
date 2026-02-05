import { NgForm } from '@angular/forms';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { DefesaImpugnacaoService } from 'src/app/client/defesa-impugnacao-client/defesa-impugnacao-client.service';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { ActivatedRoute, Router } from "@angular/router";
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';
import { Constants } from 'src/app/constants.service';


/**
 * Componente responsável pela apresentação do julgamento de substituição de comissão.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'acompanhar-julgamento',
  templateUrl: './acompanhar-julgamento-substituicao.component.html',
  styleUrls: ['./acompanhar-julgamento-substituicao.component.scss']
})

export class AcompanharJulgamentoSubstituicaoComponent implements OnInit {

  @Input() dados: any;
  @Input() dadosJulgamento: any;
  @Input() atividadeSecundaria: any;
  @Output() voltarEvent: EventEmitter<any> = new EventEmitter();
  @Output() salvarRecursoEvent = new EventEmitter<any>();

  @ViewChild('templateRecursoCadastrado', { static: true }) private templateRecursoCadastrado: TemplateRef<any>;

  public solicitarRecursoModalRef: BsModalRef;
  public recursoCadastradoModalRef: BsModalRef;

  public configuracaoCkeditor: any = {};
  public dadosServicoJulgamento: any = []
  public julgamentosSubstituicao: any = [];
  @Input() recurso: any;
  public isRecursoSubmetido: boolean;
  public tipoProfissional: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private substituicaoChapaService: SubstiuicaoChapaClientService,
    private modalService: BsModalService,
  ) {
    this.julgamentosSubstituicao = route.snapshot.data["julgamento"];
  }



  ngOnInit() {
    this.tipoProfissional = this.getValorParamDoRoute('tipoProfissional');
    this.setTitulo();
    this.inicializaConfiguracaoCkeditor();
    if(!this.recurso){
      this.inicializarRecurso();
    }
  }

  /**
   * Inicializar recurso de julgamento de substituição.
   */
  public inicializarRecurso(): void {
    this.recurso = {
      descricao: '',
      idJulgamentoSubstituicao: this.dadosJulgamento.id,
      arquivos: [],
    };
  }

  /**
  * Define o título do módulo da página
  */
  private setTitulo() {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('TITLE_PEDIDO_DE_SUBSTITUICAO')
    });
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   *
   * @param event
   * @param resolucao
   */
  public download(event: EventEmitter<any>, julgamento): void {
    this.substituicaoChapaService.getDocumentoJulgamento(julgamento.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
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
   * Verifica se o status do pedido de subtituição é igual a indeferido.
   */
  public isJulgamentoImdeferido(): boolean {
    return this.dados.statusSubstituicaoChapa.id == Constants.INDEFERIDO;
  }

  /**
   * Verifica se a data da Atividade secundária está dentro do período de vigência.
   */
  private isVigente(dataInicio: any, dataFim: any): number {
    dataFim = new Date(dataFim);
    dataFim.setHours(23, 59, 59, 999);
    dataFim.setDate(dataFim.getDate() + 1);

    dataInicio = new Date(dataInicio);
    dataInicio.setHours(0, 0, 0, 0);
    dataInicio.setDate(dataInicio.getDate() + 1);

    let hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    if (hoje <= dataFim && hoje >= dataInicio) {
      return 0;
    }
    return hoje > dataInicio ? 1 : -1;
  }

  /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.dados.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Verifica se a chapa é do tipo UFBR.
   */
  public isChapaUF(): boolean {
    return this.dados.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR;
  }

  /**
   * Verifica se o botão de cadastro de recurso deve ser mostrado.
   */
  public isMostrarCadastroRecurso(): boolean {
    return (
      this.dadosJulgamento.id &&
      this.recurso.id == undefined &&
      this.isJulgamentoImdeferido() &&
      this.tipoProfissional !== Constants.TIPO_PROFISSIONAL_COMISSAO &&
      this.isVigente(this.atividadeSecundaria.dataInicio, this.atividadeSecundaria.dataFim) == 0
    );

  }



      /**
     * Retorna um valor de parâmetro passado na rota
     * @param nameParam
     */
    private getValorParamDoRoute(nameParam) {
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
   * Volta para a aba de pedido de substituição
   */
  public voltar(): void {
    this.voltarEvent.emit(Constants.ABA_PEDIDO_SUBSTITUICAO);
  }

  /**
   * Exibir modal de solicitação de recuso de substituição de membro da chapa.
   */
  public solicitarRecurso(template: TemplateRef<any>): void {
    this.solicitarRecursoModalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-xl modal-dialog-centered', focus: false }));
  }


  /**
   * Cadasta recurso para julgamento de substituição.
   *
   * @param form
   */
  public cadastrarRecurso(form: NgForm): void {
    this.isRecursoSubmetido = true;
    if (form.valid) {
      this.substituicaoChapaService.salvarRecurso(this.recurso).subscribe(
        data => {
          this.recurso = data;
          this.solicitarRecursoModalRef.hide();
          this.recursoCadastradoModalRef = this.modalService.show(this.templateRecursoCadastrado, Object.assign({}, { class: 'modal-lg modal-dialog-centered', focus: false, ignoreBackdropClick: true }));
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    }
  }

  /**
   * Cancela Cadasto de recurso para julgamento de substituição.
   *
   * @param form
   */
  public cancelarRecurso(form: NgForm): void {
    this.inicializarRecurso();
    this.isRecursoSubmetido = false;
    this.solicitarRecursoModalRef.hide();
  }

  /**
   * Baixar arquivo de recurso de substituição.
   *
   * @param arquivo
   */
  public downloadArquivoRecurso(download: any) {
    if (download.arquivo.id) {
      this.substituicaoChapaService.getArquivoRecurso(download.arquivo.id).subscribe(
        data => {
          download.evento.emit(data);
        },
        error => {
          this.messageService.addMsgDanger(error);
        }
      );
    } else {
      download.evento.emit(download.arquivo);
    }
  }

  /**
   * Retorna mensagem de quantidade maxima de arquivos.
   */
  public getMsgMaxArquivos(): string {
    return this.messageService.getDescription('MSG_PERMISSAO_UPLOAD_N_ARQUIVOS', ['um', 1]);
  }

  /**
   * Emitir evento de recurso cadastro e fechar modal de messagem de recurso cadastrado.
   *
   * @param recurso
   */
  public salvarRecursoEmit(recurso): void {
    this.recursoCadastradoModalRef.hide();
    this.salvarRecursoEvent.emit(recurso);
  }
}