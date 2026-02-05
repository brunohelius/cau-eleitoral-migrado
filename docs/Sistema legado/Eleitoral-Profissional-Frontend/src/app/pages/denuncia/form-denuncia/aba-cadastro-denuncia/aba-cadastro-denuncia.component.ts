import { NgForm} from '@angular/forms';
import { CKEditor4 } from 'ckeditor4-angular';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';

import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';

@Component({
  selector: 'aba-cadastro-denuncia',
  templateUrl: './aba-cadastro-denuncia.component.html',
  styleUrls: ['./aba-cadastro-denuncia.component.scss']
})
export class AbaCadastroDenunciaComponent implements OnInit {

  public arquivo: any;
  public submitted = false;
  public dadosFormulario: any;
  public configuracaoCkeditor: any = {};
  public nomeArquivoDenuncia: string;

  private keyDenunciaStorage: string;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private securityService: SecurityService,
    private denunciaService: DenunciaClientService,
  ) { }

  ngOnInit(): void {
    this.keyDenunciaStorage = `denuncia${this.securityService.credential.user.id}`;

    this.dadosFormulario = this.getEstruturaDadosFormulario();
    this.dadosFormulario.denunciante = this.securityService.credential.user;
    this.dadosFormulario.denunciante.registroNacional = this.dadosFormulario.denunciante.registroNacional.replace(/^0+/, '') || "-";
    this.dadosFormulario.atividadeSecundaria = {
      id: this.route.snapshot.data.atividadeSecundaria.id
    };
    if (this.hasStateDenuncia()) {
      this.messageService.addConfirmYesNo('MSG_CONFIRMA_DESEJA_CONCLUIR_DENUNCIA_ANTERIOR',() => {
        this.dadosFormulario = this.getStateDenuncia();
        this.dadosFormulario.arquivos = [];
      }, () => {
        localStorage.removeItem(this.keyDenunciaStorage);
      });
    }

    this.inicializaConfiguracaoCkeditor();
  }

  /**
   * Atualiza os dados das testemunhas.
   *
   * @param testemunhas
   */
  public setTestemunhas = (testemunhas, f: NgForm) => {
    this.dadosFormulario.testemunhas = [];

    if(testemunhas.length > 0) {
      this.dadosFormulario.testemunhas = testemunhas;
    }
    this.saveStateDenuncia();
  }

  /**
   * Atualiza os dados do denunciado.
   *
   * @param denunciado
   */
  public setDenunciado = (denunciado) => {
    this.dadosFormulario.denunciado = null;

    if (denunciado.chapa || denunciado.membro || denunciado.uf) {
      this.dadosFormulario.denunciado = denunciado;
    }

    this.saveStateDenuncia();
  }

  /**
   * Recupera o objeto de denunciado
   */
  public getDenunciado() {
    return this.dadosFormulario.denunciado ? this.dadosFormulario.denunciado : null;
  }

  /**
   * Retorna texto de hint de informativo do documento.
   */
  public getHintInformativoDocumento = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_DOCUMENTO_DENUNCIA');
  }

  /**
   * Válida se o tipo da denúncia for Outros.
   */
  public isTipoDenunciaOutros = () => {
    return this.dadosFormulario.denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS;
  }

  /**
   * Valida se o denunciado é valido
   */

  public isDenunciadoValido = () => {
    let denunciado = this.dadosFormulario.denunciado;

    return denunciado && (
      (denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA && denunciado.uf && denunciado.chapa)
      || ((denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_CHAPA || denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO)
        && denunciado.uf && denunciado.membro)
      || (denunciado.tipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS && denunciado.uf)
    );
  }

  /**
   * Verifica se a narração de fatos não está vazia.
   */
  public isEmptyNarracaoFatos = () => {
    return this.dadosFormulario.narracaoFatosSimpleText.length < 1;
  }

  /**
   * Exclui um arquivo.
   *
   * @param indice
   */
  public excluiUpload = (indice) => {
    this.arquivo = null;
    this.dadosFormulario.arquivos.splice(indice, 1);
  }

  /**
   * Verifica se o campo de upload está no máximo.
   */
  public isQuantidadeUploadMaxima = () => {
    return this.dadosFormulario.arquivos.length == 5;
  }

  /**
   * Retorna a contagem de caracteres da narração de fatos.
   */
  public getContagemNarracaoFatos = () => {
    return Constants.TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA - this.dadosFormulario.narracaoFatosSimpleText.length;
  }

  /**
   * Alterar valor da descrição da narração de fatos.
   *
   * @param event
   */
  public onChangeCKDescricao = (event: CKEditor4.EventInfo) => {
    this.setDescricaoNarracaoFatosSimpleText(StringService.getPlainText(event.editor.getData()));
    this.saveStateDenuncia();
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

    if (this.dadosFormulario.arquivos.length < 5) {
      this.denunciaService.validarArquivoDenuncia(arquivoTO).subscribe(data => {
        this.dadosFormulario.arquivos.push(arquivoUpload);
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
   * Adiciona função callback que valida tamanho do texto que descreve a narração dos fatos.
   *
   * @param event
   */
  public onReadyCKNarracaoFatos = (event: CKEditor4.EventInfo) => {
    event.editor.on('key', function (event2) {
      let maxl = Constants.TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

      if (!StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
        event2.cancel();
      }
    });

    event.editor.on('paste', function (event2) {
      let maxl = Constants.TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA;
      let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim() + event2.data.dataValue;
      if (!StringService.isLimitValid(simplesTexto, maxl)) {
        event2.cancel();
      }
    });
  }

  /**
   * Salva a denúncia e retorna a mensagem com o numero de protocolo.
   *
   * @param form
   */
  public salvarDenuncia = (form: NgForm) => {
    this.submitted = true;
    if (form.valid) {
      this.denunciaService.salvar(this.getDadosFormularioFormatados()).subscribe((data) => {
        this.messageService.addMsgSuccess('MSG_PEDIDO_DENUNCIA_CADASTRADO_COM_EXITO', [data.numeroSequencial]);
        this.router.navigate(['denuncia/acompanhamento']);
        localStorage.removeItem(this.keyDenunciaStorage);
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   *  Atualiza os dados salvos da denuncia no localStorage
   */
  public saveStateDenuncia = () => {
    localStorage.setItem(this.keyDenunciaStorage, JSON.stringify(this.dadosFormulario));
  }

  /**
   * Apresenta modal de alerta quando é uma denúncia sigilosa.
   */
  public showModalAlertaDenunciaSigilosa = (isSigiloso: boolean) => {
    if (isSigiloso) {
      this.messageService.addConfirmOk(
        this.messageService.getDescription('MSG_ALERTA_DENUNCIA_SIGILOSA')
      );
    }
  }

  /**
   *
   */
  private getStateDenuncia = () => {
    let stateDenuncia = this.getEstruturaDadosFormulario();

    const denuncia = localStorage.getItem(this.keyDenunciaStorage);
    return denuncia ? JSON.parse(denuncia) : stateDenuncia;
  }

  /**
   *
   */
  private hasStateDenuncia = (): boolean => {
    return localStorage.getItem(this.keyDenunciaStorage) ? true : false;
  }

  /**
   *
   */
  private getEstruturaDadosFormulario = () => {
    return {
      arquivos: [],
      testemunhas: [],
      denunciado: null,
      denunciante: null,
      isSigiloso: false,
      narracaoFatos: '',
      atividadeSecundaria: null,
      narracaoFatosSimpleText: '',
    };
  }

  /**
   * Preenche texto de descrição da narração de fatos em formato de texto simples.
   *
   * @param text
   */
  private setDescricaoNarracaoFatosSimpleText = (text: string) => {
    this.dadosFormulario.narracaoFatosSimpleText = StringService.getPlainText(text).slice(0, -1);
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
   * Retorna os dados do formulário formatados para serem consumidos pelo backend.
   */
  private getDadosFormularioFormatados = () => {
    let tipoDenuncia = this.dadosFormulario.denunciado.tipoDenuncia;

    let dadosFormularioFormatados: any = {
      tipoDenuncia: { id: tipoDenuncia },
      stSigilo: this.dadosFormulario.isSigiloso,
      idPessoa: this.dadosFormulario.denunciante.id,
      testemunhas: this.dadosFormulario.testemunhas,
      arquivosDenuncia: this.dadosFormulario.arquivos,
      descricaoFatos: this.dadosFormulario.narracaoFatos,
      atividadeSecundaria: this.dadosFormulario.atividadeSecundaria,
    };

    let denunciado = this.dadosFormulario.denunciado;
    if (tipoDenuncia == Constants.TIPO_DENUNCIA_OUTROS) {
      dadosFormularioFormatados.denunciaOutro = { idCauUf: denunciado.uf.id };
    }

    if (tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA) {
      dadosFormularioFormatados.denunciaChapa = {
        chapaEleicao: { id: denunciado.chapa.id }
      };
    }

    if (tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_CHAPA) {
      dadosFormularioFormatados.denunciaMembroChapa = {
        membroChapa: { id: denunciado.membro.idMembro }
      };
    }

    if (tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO) {
      dadosFormularioFormatados.denunciaMembroComissao = {
        membroComissao: { id: denunciado.membro.idMembro }
      };
    }

    return dadosFormularioFormatados;
  }
}
