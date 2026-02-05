import { NgForm } from '@angular/forms';
import { CKEditor4 } from 'ckeditor4-angular';
import { Router, ActivatedRoute } from '@angular/router';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { StringService } from 'src/app/string.service';
import { ConfigCardListInterface } from 'src/app/shared/card-list/config-card-list-interface';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';

@Component({
  selector: 'app-admitir-denuncia',
  templateUrl: './admitir-denuncia.component.html',
  styleUrls: ['./admitir-denuncia.component.scss']
})
export class AdmitirDenunciaComponent implements OnInit {

  @Input('denuncia') denuncia;
  @Output('fecharDenuncia') fecharDenunciaEvent: EventEmitter<any> = new EventEmitter();

  public submitted: boolean = false;
  public relatores: any[];
  public admissao: any;
  public configuracaoCkeditor: any = {};
  public infoRelator: ConfigCardListInterface;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
  ) {

  }

  ngOnInit() {
    this.admissao = this.getEstruturaDadosFormulario();
    this.inicializaConfiguracaoCkeditor();
    this.getMembrosComissaoUf();
  }

  /**
   * Retorna a contagem de caracteres da designacao de relator.
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
        idMembroComissao: this.admissao.relator.id
      }
      this.denunciaService.admitir(sendAdmitir).subscribe((data) => {
        this.messageService.addMsgSuccess('MSG_DENUNCIA_ADMITIDA_RELATOR_SELECIONADO_COM_EXITO', [this.denuncia.numeroDenuncia]);
        this.router.navigate(['denuncia/comissao/acompanhamento']);
        this.cancelarAdmissao();
      }, error => {
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Fecha a modal de admissao
   */
  public cancelarAdmissao() {
    this.fecharDenunciaEvent.emit(null);
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
}
