
import {Component, OnInit, Input, EventEmitter} from '@angular/core';

import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import * as moment from 'moment';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalApresentarDefesaComponent } from '../aba-analise-admissibilidade/modal-apresentar-defesa/modal-apresentar-defesa.component';
import { ModalInserirRelatorJulgarAdmissibilidadeComponent } from '../aba-analise-admissibilidade/modal-inserir-relator-julgar-admissibilidade/modal-inserir-relator-julgar-admissibilidade.component';

@Component({
  selector: 'app-aba-julgamento-recurso-admissibilidade',
  templateUrl: './aba-julgamento-recurso-admissibilidade.component.html',
  styleUrls: ['./aba-julgamento-recurso-admissibilidade.component.scss']
})
export class AbaJulgamentoRecursoAdmissibilidadeComponent implements OnInit {

  @Input('denuncia') denuncia;
  @Input() usuario;

  public modalApresentarDefesa: BsModalRef;
  public modalRelator: BsModalRef;

  public configuracaoCkeditor: any = {};

  public dataCriacao:string;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();

    if(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso !== undefined) {
      this.dataCriacao = moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.dataCriacao).format('DD/MM/YYYY');
      this.dataCriacao += ' às '
      this.dataCriacao += moment.utc(this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.dataCriacao).format('HH:mm');
    }
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
  * Retorna o arquivo conforme o id do Arquivo Informado
  *
  * @param event
  * @param idArquivo
  */
  public downloadArquivo = (event: EventEmitter<any>, idArquivo) => {
    return this.denunciaService.downloadArquivoJulgamentoRecursoAdmissibilidade(idArquivo).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }


  /**
   * Retorna a contagem de caracteres do despacho de admissibilidade.
   */
  public getContagemDescricaoJulgamento = () => {
    return Constants.TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR - this.denuncia.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade.julgamentoRecurso.descricao.length;
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
   * Verifica se o julgamento foi provido
   */
  public isJulgamentoProvido() {
    return Constants.TIPO_JULGAMENTO_RECURSO_ADMISSIBILIDADE_PROVIDO === this.getParecer().id
  }

  /**
   * Verifica se o julgamento foi improvido
   */
  public isJulgamentoImprovido() {
    return Constants.TIPO_JULGAMENTO_RECURSO_ADMISSIBILIDADE_IMPROVIDO === this.getParecer().id
  }

  /**
   * Retorna a descrição resumida do tipo de julgamento
   */
  public getTipoJulgamento() {
    let tipoJulgamento = this.messageService.getDescription('LABEL_IMPROVIDO');
    if(this.isJulgamentoProvido()) {
      tipoJulgamento = this.messageService.getDescription('LABEL_PROVIDO');;
    }
    return tipoJulgamento;
  }

  /**
   * Recupera o parecer do julgamento do recurso de admissibilidade
   */
  public getParecer() {
    const julgamentoAdmissibilidade = this.denuncia.julgamentoAdmissibilidade;
    const recursoJulgamentoAdmissibilidade = julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade;
    const julgamentoRecurso = recursoJulgamentoAdmissibilidade.julgamentoRecurso;
    return julgamentoRecurso.parecer;
  }

  /**
   * Realiza a chamada da modal de inserção do relator, verificando se é possivel esta inserção.
   */
  public inserirRelator(): void {
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
   * Valida se à ação de inserir o relator pode ser visualizada
   */
  public isPossivelInserirRelator(): boolean {
    return this.denuncia.condicao.posso_inserir_relator  
      && (this.denuncia.idSituacaoDenuncia === Constants.SITUACAO_DENUNCIA_AGUARDANDO_RELATOR)
    ;
  }

  public voltar() {
    window.history.back();
  }
}
