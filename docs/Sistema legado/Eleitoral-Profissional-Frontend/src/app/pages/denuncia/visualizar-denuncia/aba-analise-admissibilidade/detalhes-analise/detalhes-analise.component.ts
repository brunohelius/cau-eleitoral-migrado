import {Component, EventEmitter, Input, OnInit} from '@angular/core';
import {BsModalRef, BsModalService} from 'ngx-bootstrap';

import {MessageService} from '@cau/message';
import {Constants} from 'src/app/constants.service';
import {ConfigCardListInterface} from 'src/app/shared/card-list/config-card-list-interface';
import {DenunciaClientService} from 'src/app/client/denuncia-client/denuncia-client.service';
import {ModalApresentarDefesaComponent} from '../modal-apresentar-defesa/modal-apresentar-defesa.component';
import {StringService} from 'src/app/string.service';
import {ModalInserirRelatorJulgarAdmissibilidadeComponent} from '../modal-inserir-relator-julgar-admissibilidade/modal-inserir-relator-julgar-admissibilidade.component';

@Component({
  selector: 'detalhes-analise',
  templateUrl: './detalhes-analise.component.html',
  styleUrls: ['./detalhes-analise.component.scss']
})
export class DetalhesAnaliseComponent implements OnInit {

  @Input() usuario;
  @Input() denuncia;
  @Input() analiseAdmissibilidade: any = {};
  @Input() hasAnaliseAdmissaoInadmissao?: any = false;

  public descricaoDespachoSimpleText = '';

  public configuracaoCkeditor: any = {};
  public modalApresentarDefesa: BsModalRef;
  public modalRelator: BsModalRef;
  public infoRelator: ConfigCardListInterface;
  public infoCoordenador: ConfigCardListInterface;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService,
  ) { }

  ngOnInit() {
    this.descricaoDespachoSimpleText = StringService.getPlainText(this.analiseAdmissibilidade.despacho).slice(0, -1);

    this.inicializaConfiguracaoCkeditor();

    if(this.analiseAdmissibilidade.admitida) {
      this.carregarInformacoesMembros();
    } else {
      this.carregarInformacoesCoordenador();
    }
  }

  /**
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoInadmitida(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDespachoAdmissibilidade = () => {
    return Constants.TAMANHO_LIMITE_2000 - this.descricaoDespachoSimpleText.length;
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor = () => {
    this.configuracaoCkeditor = {
      title: 'dsAdmissibilidade',
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
   * Carrega as informações dos membros.
   */
  public carregarInformacoesMembros = () => {
    let relator = this.analiseAdmissibilidade.relator.profissional;
    let coordenador = this.denuncia.coordenadorComissao.name;

    this.infoRelator = {
      header: [{
        'field': 'relator',
        'header': this.messageService.getDescription('LABEL_RELATOR')
      },
      {
        'field': 'coordenador',
        'header': this.messageService.getDescription('LABEL_COORDENADOR_COMISSAO')
      }],
      data: [{
        relator: relator.nome,
        coordenador: coordenador
      }]
    };
  }

  /**
   * Carrega as informações do coordenador.
   */
  public carregarInformacoesCoordenador = () => {
    let coordenador = this.denuncia.coordenadorComissao.name;

    this.infoCoordenador = {
      header: [{
        'field': 'coordenador',
        'header': this.messageService.getDescription('LABEL_COORDENADOR_COMISSAO')
      }],
      data: [{
        coordenador: coordenador
      }]
    };
  }

  public inserirRelator() {
    this.denunciaService.createRelator(this.denuncia.idDenuncia).subscribe(result => {
      const initialState = {
        denuncia: this.denuncia,
        relatores: result.membros_comissao,
      };
      this.modalRelator = this.modalService.show(
        ModalInserirRelatorJulgarAdmissibilidadeComponent,
        Object.assign({}, {}, {class: 'modal-lg', initialState})
      );
    });
  }

  /**
   * Abre o formulário de inadmitir.
   */
  public abrirModalApresentarDefesa(): void {
    const initialState = { 
      idDenuncia: this.denuncia.idDenuncia, 
      nuDenuncia: this.denuncia.numeroDenuncia,
      tipoDenuncia: this.denuncia.tipoDenuncia
    };

    this.modalApresentarDefesa = this.modalService.show(ModalApresentarDefesaComponent,
      Object.assign({}, {}, { class: 'modal-lg', initialState }));
  }

  /**
   * Valida se a situação da denuncia é aguardando defesa
   */
  public isAguardandoDefesaDenuncia(): boolean {
    return this.denuncia.idSituacaoDenuncia === Constants.SITUACAO_DENUNCIA_AGUARDANDO_DEFESA;
  }

  /**
   * Verifica se o usuario é o denunciado ou responsavel pela chapa.
   * 
   * @param tipoMembroComissao 
   * @param idCauUf 
   */
  public isUsuarioDenunciadoResponsavelChapa(): boolean {
    return this.usuario.isDenunciado || this.usuario.isResponsavelChapa;
  }

  /**
   * Verifica se a Denuncia está em Analise de Admissibilidade.
   * 
   * @param denuncia 
   */
  public isDenunciaEmAnaliseAdmissibilidade(denuncia: any) {
    return denuncia.idSituacaoDenuncia == Constants.SITUACAO_ANALISE_ADMISSIBILIDADE;
  }
}
