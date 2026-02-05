import { NgForm } from '@angular/forms';
import { CKEditor4 } from 'ckeditor4-angular';
import { Router, ActivatedRoute } from '@angular/router';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { ConfigCardListInterface } from 'src/app/shared/card-list/config-card-list-interface';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { BsModalRef } from 'ngx-bootstrap';

@Component({
  selector: 'app-modal-inserir-relator',
  templateUrl: './modal-inserir-relator.component.html',
  styleUrls: ['./modal-inserir-relator.component.scss']
})
export class ModalInserirRelatorComponent implements OnInit {

  @Input('denuncia') denuncia;
  @Input('idEncaminhamento') idEncaminhamento;

  public arquivo: any;
  public dadosFormulario: any;
  public submitted: boolean = false;
  public relatores: any[];
  public admissao: any;
  public configuracaoCkeditor: any = {};
  public infoRelator: ConfigCardListInterface;
  public nomeArquivoDenuncia: string;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
    public modalRef: BsModalRef
  ) {

  }

  ngOnInit() {
    this.admissao = this.getEstruturaDadosFormulario();
    this.admissao.arquivos = [];
    this.inicializaConfiguracaoCkeditor();
    this.getMembrosComissaoUf();
  }

  /**
   * Retorna a contagem de caracteres da descricao do Recurso.
   */
  public getContagemDesignacaoRelator = () => {
    return Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR - this.admissao.designacaoRelatorSimpleText.length;
  }

  /**
   * Carrega as informações de resultado.
   */
  public carregarInformacoesResultado = () => {
    const profissional = this.admissao.relator.profissional;
    if (profissional) {
      this.infoRelator = {
        header: [{
          'field': 'nome',
          'header': this.messageService.getDescription('LABEL_NOME_COMPLETO')
        }, {
          'field': 'registroNacional',
          'header': this.messageService.getDescription('LABEL_REGISTRO')
        }, {
          'field': 'email',
          'header': this.messageService.getDescription('LABEL_EMAIL')
        }],
        data: [{
          nome: profissional.nome,
          registroNacional: profissional.registroNacional.replace(/^0+/, '') || "-",
          email: profissional.email
        }]
      };
    }
  }

  /**
  * Método responsável por verificar se o relator não é o denunciado.
  */
  public isRelatorDenunciado(relator: any) {
    const denunciado = this.denuncia.denunciado.membro || {};
    if (relator && relator.profissional && relator.profissional.id && denunciado.idProfissional == relator.profissional.id) {
      return true;
    }

    return false;
  }

  /**
   * Admite a denuncia
   *
   * @param form
   */
  public concluirAdmissao = (form: NgForm) => {
    this.submitted = true;
    if (form.valid) {
      const sendAdmitir = {
        descricaoDespacho: this.admissao.descricaoDesignacao,
        idDenuncia: this.admissao.idDenuncia,
        idMembroComissao: this.admissao.relator.id,
        arquivoDenunciaAdmitida: this.admissao.arquivos,
        idEncaminhamento: this.idEncaminhamento
      }
      this.denunciaService.relator(sendAdmitir).subscribe((data) => {
        this.messageService.addMsgSuccess('MSG_INSERIR_NOVO_RELATOR_COM_EXITO', [this.denuncia.numeroDenuncia]);
        this.cancelarInserirRelator();

        setTimeout(() => {
          document.location.reload();
        }, 3000);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Fecha a modal de admissao
   */
  public cancelarInserirRelator() {
    this.modalRef.hide();
  }

  /**
   * Alterar valor da descrição da designação de relator.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoDesignacaoRelatorSimpleText(StringService.getPlainText(event.editor.getData()));
  }


  /**
   * Adiciona função callback que valida tamanho do texto que descreve a designação de relator.
   *
   * @param event
   */
  public onReadyCKDesignacaoRelator = (event: CKEditor4.EventInfo) => {
    event.editor.on('key', function (event2) {
      let maxl = Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', function (event2) {
      let maxl = Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxl)) {
        event2.cancel();
      }
    });
  }

  /**
   * Preenche texto de descrição da designação de relator em formato de texto simples.
   *
   * @param text
   */
  private setDescricaoDesignacaoRelatorSimpleText = (text: string) => {
    this.admissao.designacaoRelatorSimpleText = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'designacaoRelator',
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
   * Retorna a estrutura de dados do formulário.
   */
  private getEstruturaDadosFormulario = () => {
    return {
      idDenuncia: this.denuncia.idDenuncia,
      descricaoDespacho: '',
      relator: undefined,
      designacaoRelatorSimpleText: '',
    };
  }

  /**
  * Verifica se o campo de upload está no máximo.
  */
  public isQuantidadeUploadMaxima = () => {
    return this.admissao.arquivos.length == 5;
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

    if (this.admissao.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.admissao.arquivos.push(arquivoUpload);
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
  * Retorna texto de hint de informativo do documento.
  */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }
  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice) => {
    this.arquivo = null;
    this.admissao.arquivos.splice(indice, 1);
  }

  /**
   * Método responsável por retornar os membros de comissão para relator.
   */
  public getMembrosComissaoUf = () => {
    let idCauUf = this.denuncia.idCauUf;
    let idDenuncia = this.denuncia.idDenuncia;
    this.denunciaService.getMembrosComissaoByUf(idCauUf, idDenuncia).subscribe(data => {
      this.relatores = data;
    },
      error => {
        this.messageService.addMsgWarning(error.message);
      });
  }


}
