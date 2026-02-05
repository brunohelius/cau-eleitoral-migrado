import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AcaoSistema } from 'src/app/app.acao';
import { NgForm } from '@angular/forms';
import { DocumentoComissaoMembroClientService } from 'src/app/client/documento-comissao-membro-client/documento-comissao-membro-client.service';
import { MessageService } from '@cau/message';
import { InformacaoComissaoMembroService } from 'src/app/client/informacao-comissao-membro/informacao-comissao-membro.service';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { Constants } from 'src/app/constants.service';

@Component({
  selector: 'aba-documento-comissao-membro',
  templateUrl: './aba-documento-comissao-membro.component.html',
  styleUrls: ['./aba-documento-comissao-membro.component.scss']
})
export class AbaDocumentoComissaoMembroComponent implements OnInit {

  public documento: any = {};
  public exibirCampos: any = {};
  public acaoSistema: AcaoSistema;
  public submitted: boolean = false;
  public configuracaoCkeditor: any = {};
  public numeroMembrosComissao: any = [];

  @Input() atividadeSecundaria: any;
  @Output() recarregarAtividadeSecundaria: EventEmitter<any> = new EventEmitter<any>();

  /**
   * Construtor da classe.
   *
   * @param route
   * @param messageService
   * @param documentoComissaoMembroService
   * @param informacaoComissaoMembroService
   */
  constructor(public route: ActivatedRoute, private router: Router, private calendarioService: CalendarioClientService, private messageService: MessageService, private documentoComissaoMembroService: DocumentoComissaoMembroClientService, private informacaoComissaoMembroService: InformacaoComissaoMembroService) {
    this.acaoSistema = new AcaoSistema(route);
  }

  /**
   * Método executado quando inicializar o programa.
   */
  ngOnInit() {
    this.inicializaDocumento();
    this.inicializarExibicaoCampos();
    this.inicializarTabelaNumeroMembrosComissao();
    this.inicializarConfiguracaoCkeditor();
  }

  /**
   * Salva um novo documento da comissão do membro.
   *
   * @param form
   */
  public salvar(form: NgForm): void {

    this.submitted = true;
    let isDadosValidos = true;

    if (this.documento.situacaoCabecalhoAtivo && !this.documento.descricaoCabecalho) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_CABECALHO_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoInicial && !this.documento.descricaoTextoInicial) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_TEXTO_INICIAL_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoFinal && !this.documento.descricaoTextoFinal) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_TEXTO_FINAL_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoRodape && !this.documento.descricaoTextoRodape) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_RODAPE_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (form.valid && isDadosValidos) {
      this.submitted = false;
      this.documentoComissaoMembroService.salvar(this.documento).subscribe(data => {
        let msgSalvar = this.documento.id ? 'LABEL_DADOS_ALTERADOS_SUCESSO' : 'LABEL_DADOS_INCLUIDOS_SUCESSO';
        this.messageService.addMsgSuccess(msgSalvar);
        this.recarregarAtividadeSecundaria.emit();

        this.documento = data;
        this.documento.informacaoComissaoMembro = {};
        this.documento.informacaoComissaoMembro.id = this.atividadeSecundaria.informacaoComissaoMembro.id;
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Concluí a informação do membro da comissão eleitoral.
   */
  public concluir(): void {

    this.submitted = true;
    let isDadosValidos = true;

    if (this.documento.situacaoCabecalhoAtivo && !this.documento.descricaoCabecalho) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_CABECALHO_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoInicial && !this.documento.descricaoTextoInicial) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_TEXTO_INICIAL_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoFinal && !this.documento.descricaoTextoFinal) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_TEXTO_FINAL_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.situacaoTextoRodape && !this.documento.descricaoTextoRodape) {
      isDadosValidos = false;
      this.messageService.addMsgWarning('MSG_RODAPE_DOCUMENTO_COMISSAO_MEMBRO_NAO_PREENCHIDO');
    }

    if (this.documento.informacaoComissaoMembro.id && isDadosValidos) {
      this.submitted = false;
      this.informacaoComissaoMembroService.concluir(this.documento).subscribe(data => {
        let idCalendario =  this.atividadeSecundaria.atividadePrincipalCalendario.calendario.id;
        this.router.navigate(['eleicao', idCalendario, 'atividade-principal', 'lista']);
        this.messageService.addMsgSuccess('MSG_INFORMACAO_COMISSAO_MEMBRO_CONCLUIDO');
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Recupera a lista das UFS que devem ser apresentadas na primeira tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela1(cauUfs: any): any {
    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let metadeRegistros = Math.ceil(cauUfs.length / 2);

    let ufsTabela1 = ufs.filter((uf, index) => {
      return index < metadeRegistros;
    });

    return ufsTabela1;
  }

  /**
   * Recupera a lista das UFS que devem ser apresentadas na segunda tabela.
   *
   * @param cauUfs
   */
  public getUfsTabela2(cauUfs: any): any {
    let ufs = JSON.parse(JSON.stringify(cauUfs));
    let metadeRegistros = Math.ceil(cauUfs.length / 2);

    let ufsTabela2 = ufs.filter((uf, index) => {
      return index >= metadeRegistros;
    });

    return ufsTabela2;
  }

  /**
   * Muda o valor da exibição do campo.
   *
   * @param dsCampo
   */
  public alterarExibicaoCampo(dsCampo): void {
    if (dsCampo == 'campoParametrizarCabecalho') {
      this.exibirCampos.campoParametrizarCabecalho = !this.exibirCampos.campoParametrizarCabecalho;
    }

    if (dsCampo == 'campoParametrizarTextoInicial') {
      this.exibirCampos.campoParametrizarTextoInicial = !this.exibirCampos.campoParametrizarTextoInicial;
    }

    if (dsCampo == 'campoQuantidadeMembros') {
      this.exibirCampos.campoQuantidadeMembros = !this.exibirCampos.campoQuantidadeMembros;
    }

    if (dsCampo == 'campoParametrizarTextoFinal') {
      this.exibirCampos.campoParametrizarTextoFinal = !this.exibirCampos.campoParametrizarTextoFinal;
    }

    if (dsCampo == 'campoParametrizarRodape') {
      this.exibirCampos.campoParametrizarRodape = !this.exibirCampos.campoParametrizarRodape;
    }
  }

  /**
   * Limpa o valor apresentado no campo do ckeditor.
   */
  public limparCampo(noCampo): void {
    if (noCampo == 'descricaoTextoFinal') {
      this.documento.descricaoTextoFinal = '';
    }

    if (noCampo == 'descricaoTextoInicial') {
      this.documento.descricaoTextoInicial = '';
    }
  }

  /**
   * Retorna se a ação executada é a ação de visualizar.
   */
  public isAcaoVisualizar(): boolean {
    return this.acaoSistema.isAcaoVisualizar();
  }

  /**
   * Inicializa a configuração do CKeditor apresentado na tela.
   */
  private inicializarConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker'], items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'insert', items: ['Image', 'Table'] }
      ]
    };
    this.configuracaoCkeditor.disallowedContent = 'img{width,height}';
    this.configuracaoCkeditor.extraAllowedContent = 'img[width,height]';

    if (this.acaoSistema.isAcaoVisualizar()) {
      this.configuracaoCkeditor.readOnly = true;
    }
  }

  /**
   * Inicializa o objeto para exibição dos campos.
   */
  private inicializarExibicaoCampos(): void {
    this.exibirCampos = {
      campoParametrizarCabecalho: true,
      campoParametrizarTextoInicial: true,
      campoQuantidadeMembros: true,
      campoParametrizarTextoFinal: true,
      campoParametrizarRodape: true
    };
  }

  /**
   * Inicializa o objeto para exibição dos campos.
   */
  private inicializaDocumento(): void {

    let informacaoComissaoMembro = this.atividadeSecundaria.informacaoComissaoMembro;

    if (informacaoComissaoMembro.documentoComissaoMembro && informacaoComissaoMembro.documentoComissaoMembro.id) {
      this.documento = informacaoComissaoMembro.documentoComissaoMembro;
      this.documento.informacaoComissaoMembro = {};
      this.documento.informacaoComissaoMembro.id = informacaoComissaoMembro.id;
    } else {

      informacaoComissaoMembro.documentoComissaoMembro = {
        descricaoCabecalho: '',
        descricaoTextoFinal: '',
        descricaoTextoRodape: '',
        descricaoTextoInicial: '',
        situacaoTextoFinal: true,
        situacaoTextoRodape: true,
        situacaoTextoInicial: true,
        situacaoCabecalhoAtivo: true,
        informacaoComissaoMembro: {
          id: informacaoComissaoMembro.id
        }
      }

      this.documento = informacaoComissaoMembro.documentoComissaoMembro;
    }
  }

  /**
   * Inicializa a tabela com a quantidade de membros da comissão.
   */
  private inicializarTabelaNumeroMembrosComissao(): void {
    this.calendarioService.getAgrupamentoNumeroMembrosComissao(this.atividadeSecundaria.atividadePrincipalCalendario.calendario.id).subscribe(numeroMembros => {
      let numeroMembrosArr = [];

      numeroMembros.forEach(numeroMembro => {
        if (numeroMembro.idCauUf == Constants.INFORMACAO_COMISSAO_CAU_BR_ID) {
          numeroMembrosArr.push(numeroMembro);
        }
      });

      numeroMembros.forEach(numeroMembro => {
        if (numeroMembro.idCauUf != Constants.INFORMACAO_COMISSAO_CAU_BR_ID) {
          numeroMembrosArr.push(numeroMembro);
        }
      });

      this.numeroMembrosComissao = numeroMembrosArr;
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

}
