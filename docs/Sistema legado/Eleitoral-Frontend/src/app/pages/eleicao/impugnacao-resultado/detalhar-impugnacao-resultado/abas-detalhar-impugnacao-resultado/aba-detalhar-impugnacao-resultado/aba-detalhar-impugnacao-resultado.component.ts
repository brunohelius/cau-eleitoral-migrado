import * as moment from 'moment';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { StringService } from 'src/app/string.service';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';


/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
  selector: 'aba-detalhar-impugnacao-resultado',
  templateUrl: './aba-detalhar-impugnacao-resultado.component.html',
  styleUrls: ['./aba-detalhar-impugnacao-resultado.component.scss']
})

export class AbaDetalharImpugnacaoResultadoComponent implements OnInit {

  public dados: any;
  public bandeira: any;
  public configuracaoCkeditor: any;

  @Input() public impugnacao: any;
  @Input() public cauUf: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private impugnacaoResultadoClientService: ImpugnacaoResultadoClientService,
  ) {
    // this.cauUf = route.snapshot.data["cauUfs"];
    // this.impugnacao = route.snapshot.data["impugnacao"];
  }

  ngOnInit() {
    this.inicializaIconeTitulo();
    this.inicializaDados();
    this.setImagemBandeira();
  }

  public inicializaDados() {
    this.dados = {
      id: this.impugnacao.id,
      idUf: this.impugnacao.cauBR ? this.impugnacao.cauBR.id : null,
      idCalendario: this.impugnacao.calendario.id,
      numero: this.impugnacao.numero,
      narracao: this.impugnacao.narracao,
      nome: this.impugnacao.profissional.nome,
      email: this.impugnacao.profissional.email,
      dataCadastro: moment.utc(this.impugnacao.dataCadastro),
      registro: this.getRegistroComMask(this.impugnacao.profissional.registroNacional),
      arquivos: this.impugnacao.arquivos
    };
  }

  /**
  * Inicializa ícone e título do header da página .
  */
  private inicializaIconeTitulo(): void {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-fw fa-list',
      description: this.messageService.getDescription('LABEL_PARAMETRIZACAO_CALENDARIO_IMPUGNACAO_RESULTADO')
    });
  }

  /**
  * retorna a label da aba de acompanhar chapa com quebra de linha
  */
  public getTituloAbaImpugnacaoResultado(): any {
    return this.messageService.getDescription('LABEL_IMPUGNACAO_RESULTADO', ['<div>', '</div><div>', '</div>']);
  }

  /**
  * Busca imagem de bandeira do estado do CAU/UF.
  * @param idCauUf
  */
  public setImagemBandeira(): void {

    if (!this.dados.idUf) {
      this.bandeira = this.cauUf.filter((data) => data.id == Constants.ID_CAUBR);
    } else {
      this.bandeira = this.cauUf.filter((data) => data.id == this.dados.idUf);
    }

    this.bandeira = this.bandeira[0];

    if (this.bandeira.id == Constants.ID_CAUBR) {
      this.bandeira.descricao = "IES";
    }
  }

  /**
  * Retorna o registro com a mascara
  */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
  }

  /**
   * Realiza o download do arquivo da solicitação
   * @param arquivo
   */
  public downloadArquivo(arquivo: any): void {
    this.impugnacaoResultadoClientService.getArquivoImpugnacaoResultado(this.dados.id).subscribe((data: Blob) => {
      arquivo.evento.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Volta para a página da uf da solicitação
   */
  public voltar() {

    if (this.dados.idUf == undefined || this.dados.idUf == null) {
      this.dados.idUf = 0;
    }

    this.router.navigate([
      `/eleicao/impugnacao-resultado/acompanhar/calendario/${this.dados.idCalendario}/uf/${this.dados.idUf}`
    ]);
  }

  /**
   * Responsavel por voltar a tela pro inicio.
   */
  public inicio(): any {
    this.router.navigate(['/']);
    this.layoutsService.onLoadTitle.emit({
      description: ''
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
    };
  }

}