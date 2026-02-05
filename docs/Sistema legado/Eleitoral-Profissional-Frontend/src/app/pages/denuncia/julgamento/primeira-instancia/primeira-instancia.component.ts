import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { ConfigCardListInterface } from 'src/app/shared/card-list/config-card-list-interface';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { StringService } from 'src/app/string.service';
import { RecursoReconsideracaoComponent } from '../recurso-reconsideracao/recurso-reconsideracao.component';

@Component({
  selector: 'app-primeira-instancia',
  templateUrl: './primeira-instancia.component.html',
  styleUrls: ['./primeira-instancia.component.scss']
})
export class PrimeiraInstanciaComponent implements OnInit {

  @Input('usuario') usuario;
  @Input('dadosDenuncia') denuncia;
  public julgamentoDenuncia: any;

  public descricaoJulgamentoSimpleText = '';

  public configuracaoCkeditor: any = {};
  public modalRecurso: BsModalRef;
  public infoJulgamento: ConfigCardListInterface;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.julgamentoDenuncia = this.denuncia.julgamento_denuncia;

    this.inicializaConfiguracaoCkeditor();

    if (this.julgamentoDenuncia) {
      this.carregarInfoJulgamento();
      this.descricaoJulgamentoSimpleText = StringService.getPlainText(this.denuncia.julgamento_denuncia.descricaoJulgamento).slice(0, -1);
    }
  }

  /**
   * Carrega as informações de relator.
   */
  public carregarInfoJulgamento():void {
    let data = {
      'julgado' : this.julgamentoDenuncia.descricaoTipoJulgamento
    };

    this.infoJulgamento = {
      header: [
        {
          'field': 'julgado',
          'header': this.messageService.getDescription('TITLE_JULGADO')
        }
      ],
      data: []
    };

    if(this.isJulgamentoProcedente()) {
      data['sentenca'] = this.julgamentoDenuncia.descricaoTipoSentencaJulgamento;
      this.infoJulgamento.header.push(
        {
          'field': 'sentenca',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_COMISSAO')
        }
      );

      if(this.julgamentoDenuncia.quantidadeDiasSuspensaoPropaganda) {
        data['qtdDias'] = this.julgamentoDenuncia.quantidadeDiasSuspensaoPropaganda;
        this.infoJulgamento.header.push(
          {
            'field': 'qtdDias',
            'header': this.messageService.getDescription('TITLE_QTDE_DIAS')
          }
        );       
      }

      if(this.julgamentoDenuncia.multa) {
        data['multa'] = `${this.julgamentoDenuncia.valorPercentualMulta}% ${this.messageService.getDescription('LABEL_MULTA_ANUIDADE')}` 
        this.infoJulgamento.header.push(
          {
            'field': 'multa',
            'header': this.messageService.getDescription('TITLE_VALOR_PERCENTUAL_MULTA')
          }
        );       
      }

    }
    this.infoJulgamento.data.push(data);

  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoJulgamento(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoJulgamentoSimpleText.length;
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'dsJulgamento',
      removePlugins: 'elementspath',
      toolbar: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-'] },
        { name: 'links', items: ['Link'] },
        { name: 'insert', items: ['Image'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
      ],
    };
  }

  public abrirModalRecurso(): void {
    const initialState = {
      idDenuncia: this.denuncia.idDenuncia,
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.idTipoDenuncia
    };

    this.modalRecurso = this.modalService.show(RecursoReconsideracaoComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));

  }

  /**
   * Verifica se o usuario é o denunciado ou responsavel pela chapa.
   * 
   * @param tipoMembroComissao 
   * @param idCauUf 
   */
  public isUsuarioDenunciadoResponsavelChapaAptoRecurso(): boolean {
    let isAptoRecurso = this.usuario.isDenunciado || this.usuario.isResponsavelChapa;
    if(isAptoRecurso) {
      isAptoRecurso = this.denuncia.recursoDenunciado ? false : true;
    }
    return isAptoRecurso;
  }

  /**
   * Verifica se o usuario é o denunciado ou responsavel pela chapa.
   * 
   * @param tipoMembroComissao 
   * @param idCauUf 
   */
  public isUsuarioDenuncianteAptoRecurso(): boolean {
    let isAptoRecurso = this.usuario.isDenunciante;
    if(isAptoRecurso) {
      isAptoRecurso = this.denuncia.recursoDenunciante ? false : true;
    }
    return isAptoRecurso;
  }

  /**
   * Verifica se a possibilidade de abrir um recurso
   */
  public hasPossibilidadeRecurso(): boolean {
    return this.denuncia.prazoRecursoDenuncia && (this.isUsuarioDenuncianteAptoRecurso() || this.isUsuarioDenunciadoResponsavelChapaAptoRecurso());
  }

  /**
   * Verifica se o julgamento é improcedente
   */
  public isJulgamentoImprocedente() {
    return this.julgamentoDenuncia.idTipoJulgamento === Constants.TIPO_JULGAMENTO_IMPROCEDENTE;
  }

  /**
   * Verifica se o julgamento é procedente
   */
  public isJulgamentoProcedente() {
    return this.julgamentoDenuncia.idTipoJulgamento === Constants.TIPO_JULGAMENTO_PROCEDENTE;
  }

  /**
   * Verifica se a sentença é de multa.
   */
  public isSentencaMulta() {
    return this.julgamentoDenuncia.idTipoSentencaJulgamento === Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

}
