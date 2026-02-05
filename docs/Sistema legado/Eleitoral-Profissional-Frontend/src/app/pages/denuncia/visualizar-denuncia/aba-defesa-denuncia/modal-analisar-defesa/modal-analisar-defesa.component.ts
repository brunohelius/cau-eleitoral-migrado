import { NgForm } from '@angular/forms';
import { formatDate } from "@angular/common";
import { CKEditor4 } from 'ckeditor4-angular';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output, ElementRef } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService, TipoDenuncia } from 'src/app/client/denuncia-client/denuncia-client.service';
import { ModalJustificativaEncaminhamentoComponent } from '../modal-justificativa-encaminhamento/modal-justificativa-encaminhamento.component';

@Component({
  selector: 'modal-analisar-defesa',
  templateUrl: './modal-analisar-defesa.component.html',
  styleUrls: ['./modal-analisar-defesa.component.scss']
})
export class ModalAnalisarDefesaComponent implements OnInit {

  @Input() idDenuncia: any;
  @Input() nuDenuncia: any;
  @Input() tipoDenuncia: any;

  public configuracaoCkeditor: any = {};
  public arquivo: any;
  public prazoPadrao: any;
  public encaminhamento: any;
  public tiposEncaminhamento: any;
  public nomeArquivoDefesa: string;
  public submitted: boolean = false;

  public denuncia: any;
  public isDenunciaTipoOutros: boolean = false;

  public modalJustificativaModal: BsModalRef;

  constructor(
    public modalRef: BsModalRef,
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.prazoPadrao = Constants.PRAZO_PADRAO_ENCAMINHAMENTO_DEFESA;
    this.isDenunciaTipoOutros = this.tipoDenuncia == TipoDenuncia.TIPO_DENUNCIA_OUTROS.id;

    this.setEstruturaDadosFormulario();
    this.inicializaConfiguracaoCkeditor();
    this.getTiposEncaminhamento();
    this.getDenunciaPorId();

  }

  /**
   * Retorna a estrutura de dados do formulário.
   */
  private setEstruturaDadosFormulario = () => {
    this.encaminhamento = {
      arquivos: [],
      descricao: '',
      producaoProvas: {
        destinatarios: {
          denunciado: false,
          denunciante: this.isDenunciaTipoOutros
        },
        prazoDias: Constants.PRAZO_PADRAO_ENCAMINHAMENTO_DEFESA
      },
      audienciaInstrucao: {
        data: '',
        horario: ''
      },
      alegacoesFinais: {
        justificativa: '',
      },
      tipoEncaminhamento: '',
      idDenuncia: this.idDenuncia,
      encaminhamentoDefesaSimpleText: [],
    };
  }

  /**
   * Salva o encaminhamento de defesa da denuncia
   *
   * @param form
   */
  public concluir = (form: NgForm) => {
    this.submitted = true;

    let sendEncaminhamento = this.getDadosEncaminhamento();

    if (form.valid) {
      if (this.isTipoEncaminhamentoAudienciaInstrucao()) {
        this.denunciaService.validarAudienciaInstrucaoPendente(this.encaminhamento.idDenuncia).subscribe((data) => {
          if (data.length > 0) {
            this.messageService.addConfirmYesNo('MSG_EXISTE_AUDIENCIA_INSTRUCAO_PENDENTE_DESEJA_CONTINUAR', () => {
              this.salvar(sendEncaminhamento);
            }, () => {
              this.cancelarEncaminhamentoDefesa();
            });
          } else {
            this.salvar(sendEncaminhamento);
          }
        }, error => {
          this.messageService.addMsgDanger(error);
        });

        return;
      }

      if (this.isTipoEncaminhamentoAlegacoesFinais()) {
        this.denunciaService.getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente(this.encaminhamento.idDenuncia).subscribe((data) => {
          if (data.length > 0) {
            this.messageService.addConfirmYesNo('MSG_ENCAMINHAMENTOS_PENDENTES_DESEJA_FECHAR_CONCLUIR_SOLICITACAO', () => {
              this.abrirModalJustificativa(sendEncaminhamento);
            }, () => {
              this.cancelarEncaminhamentoDefesa();
            }, data.join(', '));
          } else {
            this.salvar(sendEncaminhamento);
          }
        }, error => {
          this.messageService.addMsgDanger(error);
        });

        return;
      }

      this.salvar(sendEncaminhamento);
    }
  }

  /**
   * Salva o encaminhamento de defesa da denuncia
   *
   * @param sendEncaminhamento
   */
  public salvar = (sendEncaminhamento: {}) => {
    this.denunciaService.salvarEncaminhamento(sendEncaminhamento).subscribe((data) => {
      this.messageService.addMsgSuccess('MSG_DADOS_CADASTRADOS_COM_EXITO', [this.nuDenuncia]);
      this.cancelarEncaminhamentoDefesa();

      setTimeout(() => {
        document.location.reload();
      }, 3000);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Fecha a modal de encaminhamento de defesa
   */
  public cancelarEncaminhamentoDefesa() {
    this.modalRef.hide();
  }

  /**
   * Retorna a contagem de caracteres da encaminhamento da defesa.
   */
  public getContagemEncaminhamentoDefesa = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.encaminhamento.encaminhamentoDefesaSimpleText.length;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice) => {
    this.arquivo = null;
    this.encaminhamento.arquivos.splice(indice, 1);
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.encaminhamento.arquivos.length == 5;
  }

  /**
   * Verifica se o tipo de encaminhamento é produção de provas.
   */
  public isTipoEncaminhamentoProducaoProvas = () => {
    return this.encaminhamento.tipoEncaminhamento && this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS;
  }

  /**
   * Verifica se o tipo de encaminhamento é audiencia de instrução.
   */
  public isTipoEncaminhamentoAudienciaInstrucao = () => {
    return this.encaminhamento.tipoEncaminhamento && this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO;
  }

  /**
   * Verifica se o tipo de encaminhamento é alegações finais.
   */
  public isTipoEncaminhamentoAlegacoesFinais = () => {
    return this.encaminhamento.tipoEncaminhamento && this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS;
  }

  /**
   * Busca os tipos de encaminhamentos para seleção.
   */
  public getTiposEncaminhamento = () => {
    this.denunciaService.getTiposEncaminhamentoPorDenuncia(this.idDenuncia).subscribe(data => {
      this.tiposEncaminhamento = data;
    },
    error => {
      this.messageService.addMsgWarning(error.message);
    });
  }

  /**
   * Busca a denuncia de acordo com o ID informado.
   */
  public getDenunciaPorId = () => {
    this.denunciaService.getDenunciaById(this.idDenuncia).subscribe(data => {
      this.denuncia = data.denuncia;
      if(data.denuncia.id_tipo_denuncia == TipoDenuncia.TIPO_DENUNCIA_OUTROS.id)
        this.isDenunciaTipoOutros = true;
        this.encaminhamento.producaoProvas.destinatarios.denunciante = true;
      },
    error => {
      this.messageService.addMsgWarning(error.message);
    });
  }

  /**
   * Método responsável por validar se cada arquivo submetido a upload
   * atende os critérios definidos para salvar os binários.
   *
   * @param arquivoEvent
   * @param calendario
   */
  public uploadDocumento = (arquivoEvent: any) => {
    let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size };
    let arquivoUpload = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, 'arquivo': arquivoEvent };

    if (this.encaminhamento.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.encaminhamento.arquivos.push(arquivoUpload);
        this.arquivo = arquivoEvent.name;
      },
        error => {
          this.messageService.addMsgWarning(error.message);
        });
    } else {
      this.messageService.addMsgWarning('MSG_QTD_MAXIMA_UPLOAD');
    }
  }

  /**
   * Abre o formulário de justificativa.
   */
  public abrirModalJustificativa(encaminhamento: any): void {
    this.modalJustificativaModal = this.modalService.show(ModalJustificativaEncaminhamentoComponent,
      Object.assign({}, {}, { class: 'modal-lg second' }));

    let eventConfirmJustificativa = this.modalService.onHide.subscribe(() => {
      let contentModal = this.modalJustificativaModal.content;

      if (contentModal.justificativaEncaminhamentoSimpleText.length > 0) {
        encaminhamento.justificativa = contentModal.justificativaEncaminhamento;
        eventConfirmJustificativa.unsubscribe();
        this.salvar(encaminhamento);
      }
    });
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'narracaoFatos',
      removePlugins: 'elementspath',
      toolbar: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
        { name: 'links', items: ['Link'] },
        { name: 'insert', items: ['Image'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
      ],
    };
  }

  /**
   * Alterar valor da descrição do encaminhamento da defesa.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoEncaminhamentoDefesaSimpleText(StringService.getPlainText(event.editor.getData()));
  }

  /**
   * Preenche texto de descrição do encaminhamento da defesa em formato de texto simples.
   *
   * @param text
   */
  private setDescricaoEncaminhamentoDefesaSimpleText = (text: string) => {
    this.encaminhamento.encaminhamentoDefesaSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna texto de hint de informativo do documento.
   */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve o encaminhamento da defesa.
   *
   * @param event
   */
  public onReadyCKDefesa = (event: CKEditor4.EventInfo) => {
    event.editor.on('key', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', function (event2) {
      let maxl = Constants.TAMANHO_LIMITE_2000;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxl)) {
        event2.cancel();
      }
    });
  }

  /**
   * Adiciona função keyup que verifica o prazo digitado.
   *
   * @param event
   */
  public onReadyPrazo = (event) => {
    let valor: any = event.target.value;

    if(valor) {
      let caracteres = valor.split('');
      if(0 == Number(caracteres[0])) {
        caracteres.shift();
      }
      valor = caracteres.join('');
    }

    event.target.value = valor || '';
  }

  /**
   * Retorna os dados de encaminhamento para persistência na base de dados.
   * 
   * @return sendEncaminhamento
   */
  private getDadosEncaminhamento = () => {
    let sendEncaminhamento: any = {
      descricao: this.encaminhamento.descricao,
      idDenuncia: this.encaminhamento.idDenuncia,
      arquivoEncaminhamento: this.encaminhamento.arquivos,
      tipoEncaminhamento: {id: this.encaminhamento.tipoEncaminhamento.id}
    };

    if (this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS) {
      sendEncaminhamento.destinoDenunciado = this.encaminhamento.producaoProvas.destinatarios.denunciado;
      sendEncaminhamento.destinoDenunciante = this.encaminhamento.producaoProvas.destinatarios.denunciante;
      sendEncaminhamento.prazoProducaoProvas = this.encaminhamento.producaoProvas.prazoDias;
    }

    if (this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO) {
      let dataAudiencia = formatDate(this.encaminhamento.audienciaInstrucao.data, 'yyyy-MM-dd', 'en-US');
      sendEncaminhamento.agendamentoEncaminhamento = {
        data: `${dataAudiencia}T${this.encaminhamento.audienciaInstrucao.horario}:00.000Z`
      };
      sendEncaminhamento.destinoDenunciado = true;
      sendEncaminhamento.destinoDenunciante = true;
    }

    if (this.encaminhamento.tipoEncaminhamento.id == Constants.TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS) {
      sendEncaminhamento.justificativa = this.encaminhamento.alegacoesFinais.justificativa;
    }

    return sendEncaminhamento;
  }

  /**
   * Se houve pelo menos 1 dos destinatarios não se torna mais obrigatorio
   * @returns hasDestinatario boolean
   */
  public hasDestinatarioProducaoProvas() {
    let hasDestinatario = true;

    if(this.encaminhamento.producaoProvas.destinatarios.denunciante || this.encaminhamento.producaoProvas.destinatarios.denunciado) {
      hasDestinatario = false;
    }
    return hasDestinatario;
  }
  
}
