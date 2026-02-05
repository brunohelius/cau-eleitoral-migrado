import { Component, OnInit, Input, EventEmitter } from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { StringService } from 'src/app/string.service';
import { ConfigCardListInterface } from 'src/app/shared/card-list/config-card-list-interface';

@Component({
  selector: 'app-segunda-instancia',
  templateUrl: './segunda-instancia.component.html',
  styleUrls: ['./segunda-instancia.component.scss']
})
export class SegundaInstanciaComponent implements OnInit {

  @Input('dadosDenuncia') denuncia;
  public julgamentoRecurso: any;

  public descricaoJulgamentoSimpleText = '';

  public configuracaoCkeditor: any = {};
  public infoJulgamento: ConfigCardListInterface;

  constructor(
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) {}

  ngOnInit() {
    this.julgamentoRecurso = this.getJulgamentoRecurso(this.denuncia);
    this.inicializaConfiguracaoCkeditor();

    if (this.julgamentoRecurso) {
      this.carregarInfoJulgamento();
      this.descricaoJulgamentoSimpleText = StringService.getPlainText(this.julgamentoRecurso.descricaoJulgamento).slice(0, -1);
    }
  }

  /**
   * Recupera o julgamento do recurso de acordo com o ator que efetuou o pedido de recurso.
   * 
   * @param denuncia 
   */
  private getJulgamentoRecurso(denuncia: any): void {
    let recurso = denuncia.recursoDenunciado && denuncia.recursoDenunciado.julgamentoRecurso 
      ? denuncia.recursoDenunciado.julgamentoRecurso : null;

    if (!recurso) {
      recurso = denuncia.recursoDenunciante && denuncia.recursoDenunciante.julgamentoRecurso 
        ? denuncia.recursoDenunciante.julgamentoRecurso : null;
    }

    return recurso;
  }

  /**
   * Carrega as informações de julgamento.
   */
  public carregarInfoJulgamento():void {
    let data = {
      'sancao' : this.julgamentoRecurso.descricaoSancao,
      'julgamento_denunciado' : this.julgamentoRecurso.descricaoTipoJulgamentoDenunciado,
      'julgamento_denunciante' : this.julgamentoRecurso.descricaoTipoJulgamentoDenunciante,
    };

    this.infoJulgamento = {
      header: [
        {
          'field': 'julgamento_denunciante',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_DENUNCIANTE')
        },
        {
          'field': 'julgamento_denunciado',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_DENUNCIADO')
        },
        {
          'field': 'sancao',
          'header': this.messageService.getDescription('TITLE_SANCAO_APLICADA')
        }
      ],
      data: []
    };

    if(this.isSancao()) {
      data['sentenca'] = this.julgamentoRecurso.descricaoTipoSentencaJulgamento;
      this.infoJulgamento.header.push(
        {
          'field': 'sentenca',
          'header': this.messageService.getDescription('TITLE_JULGAMENTO_COMISSAO')
        }
      );

      if(this.julgamentoRecurso.quantidadeDiasSuspensaoPropaganda) {
        data['qtdDias'] = this.julgamentoRecurso.quantidadeDiasSuspensaoPropaganda;
        this.infoJulgamento.header.push(
          {
            'field': 'qtdDias',
            'header': this.messageService.getDescription('TITLE_QTDE_DIAS')
          }
        );       
      }

      if(this.julgamentoRecurso.multa) {
        data['multa'] = `${this.julgamentoRecurso.valorPercentualMulta}% ${this.messageService.getDescription('LABEL_MULTA_ANUIDADE')}` 
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
    return this.denunciaService.downloadArquivoJulgamentoRecurso(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres da descrição do julgamento.
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

  /**
   * Verifica se sanção foi aplicada.
   */
  public isSancao() {
    return this.julgamentoRecurso.sancao;
  }

  /**
   * Verifica se a sentença é de multa.
   */
  public isSentencaMulta() {
    return this.julgamentoRecurso.idTipoSentencaJulgamento === Constants.TIPO_SENTENCA_JULGAMENTO_MULTA;
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }

}
