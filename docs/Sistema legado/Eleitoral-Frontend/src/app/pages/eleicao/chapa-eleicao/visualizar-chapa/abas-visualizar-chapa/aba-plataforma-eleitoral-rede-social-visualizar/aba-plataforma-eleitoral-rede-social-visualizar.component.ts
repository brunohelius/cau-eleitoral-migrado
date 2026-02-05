import { Router } from '@angular/router';
import { Component, OnInit, ViewChild, Input, EventEmitter, Output } from '@angular/core';

import * as moment from 'moment';
import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { CKEditor4 } from 'ckeditor4-angular';
import { StringService } from 'src/app/string.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';
import { ModalVisualizarRetificacaoPlataformaComponent } from './modal-visualizar-retificacao-plataforma/modal-visualizar-retificacao-plataforma.component';

@Component({
  selector: 'aba-plataforma-eleitoral-rede-social-visualizar',
  templateUrl: './aba-plataforma-eleitoral-rede-social-visualizar.component.html',
  styleUrls: ['./aba-plataforma-eleitoral-rede-social-visualizar.component.scss']
})
export class AbaPlataformaEleitoralRedeSocialVisualizarComponent implements OnInit {

  public resolucao: any;
  public redesSociais: any;
  public submitted: boolean = false;
  public redesSociaisChapaFixas: any;
  public redesSociaisChapaOutras: any;
  public _redesSociaisChapaFixas: any;
  public _redesSociaisChapaOutras: any;
  public _descricaoPlataforma: string;
  public configuracaoCkeditor: any = {};
  public redesSociaisChapaOutrasAtiva: boolean;
  public modalInformativoEleicaoChapaEleitoral: BsModalRef;
  public isDadosCamposAlterados: boolean = false;
  public isCarregaDadosRetificacao: boolean = true;
  public retificacoes: any;
  public isPossuiAlteracao: boolean = false;
  public tamanhoInicialTextoRestante: any;
  public _redesSociaisChapaOutrasAtiva: any;

  private textoSimples: string;

  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Output() chapaEleicaoChange = new EventEmitter<any>();
  @Input() isUsuarioCEN?: boolean;
  @Input() isUsuarioCE?: boolean;
  @Input() isEleicaoDentroDoPrazoCadastro?: boolean;
  @Input() isPossuiRetificacao?: boolean;

  @Output() avancar: EventEmitter<any> = new EventEmitter();
  @Output() retroceder: EventEmitter<any> = new EventEmitter();
  @Output() cancelar: EventEmitter<any> = new EventEmitter();
  @Output() voltar: EventEmitter<any> = new EventEmitter();


  public modalVisualizarRetificacao: BsModalRef | null;


  @ViewChild('templateInformativoChapaEleitoralModal', { static: true }) private templateInformativoChapaEleitoralModal;

  /**
   * Construtor da classe.
   *
   * @param router
   * @param modalService
   * @param messageService
   * @param calendarioClientService
   */
  constructor(
    private router: Router,
    private modalService: BsModalService,
    private messageService: MessageService,
    private chapaEleitoralService: ChapaEleicaoClientService,
    private calendarioClientService: CalendarioClientService
  ) { }

  /**
   * Executado quando inicializar a função.
   */
  ngOnInit() {
    this.inicializarDadosComponente()
  }

  /**
   * Inicializa as informações iniciais dos dados
   */
  public inicializarDadosComponente(): void {
    this.inicializarResolucao();
    this.inicializarRedesSociais();
    this.inicializaConfiguracaoCkeditor();

    this._descricaoPlataforma = this.chapaEleicao.descricaoPlataforma;
    this.setTextoSimples(StringService.getPlainText(this.chapaEleicao.descricaoPlataforma));
    this.tamanhoInicialTextoRestante = this.getTamanhoTextoRestantes()
  }

  /**
   * Adiciona função callback que valida tamanho do texto que descreve a plataforma da chapa.
   *
   * @param event
   */
  public onRedyCKDescricaoPlataforma(event: CKEditor4.EventInfo){
    event.editor.on('key', function(event2) {
        let maxl = Constants.TAMALHO_MAXIMO_DESCRICAO_PLATAFORMA_CHAPA;
        let simplesTexto = StringService.getPlainText(event2.editor.getData()).trim();

        if( !StringService.isLimitValid(simplesTexto, maxl) && StringService.isTextualCaracter(event2.data.keyCode)) {
            event2.cancel();
        }
    });

    event.editor.on('paste', function(event2) {
        let maxl = Constants.TAMALHO_MAXIMO_DESCRICAO_PLATAFORMA_CHAPA;
        let simplesTexto = StringService.getPlainText(event2.editor.getData()) + event2.data.dataValue;
        if(!StringService.isLimitValid(simplesTexto, maxl)) {
            event2.cancel();
        }
    });
  }

  /**
   * Alterar valor da decrição do currículo.
   *
   * @param event
   */
  public onChangeCkeditor(event: CKEditor4.EventInfo) {
    this.setTextoSimples(StringService.getPlainText(event.editor.getData()));
    this.isCamposAlterados();
  }

  /**
   * Preenche  valor do texto do Ckeditor em formato de texto simples.
   * Por texto simples quero dizem, apenas texto sem tag html.
   *
   * @param text
   */
  private setTextoSimples(text: string): void {
    this.textoSimples = StringService.getPlainText(text).slice(0, -1);
  }

  /**
   * Retorna quantidade de caracteres restantes para atingir o valor máximo de tamanho do campo.
   */
  public getTamanhoTextoRestantes(): number {
    return Constants.TAMALHO_MAXIMO_DESCRICAO_PLATAFORMA_CHAPA - this.textoSimples.length;
  }

  /**
   * Função chamada quando o método anterior é chamado.
   */
  public anterior(): void {
    let controle: any = { isAlterado: this.isCamposAlterados(), aba: Constants.ABA_VISAO_GERAL };
    this.retroceder.emit(controle);
  }

  /**
   * Função responsável por salvar os dados e avançar para a próxima aba.
   */
  public avancarMembrosChapa(): void {

    let redesSociaisChapaFixas = this.redesSociaisChapaFixas.filter(redeSocial => redeSocial.descricao );
    let redesSociaisChapaOutras = this.redesSociaisChapaOutras.filter(redeSocial => redeSocial.descricao );
    
    if(redesSociaisChapaOutras.length > 0) {
      redesSociaisChapaOutras[0].isAtivo = this.redesSociaisChapaOutrasAtiva
    }

    this.chapaEleicao.redesSociaisChapa = [
      ...redesSociaisChapaFixas,
      ...redesSociaisChapaOutras
    ];

   this.chapaEleitoralService.alterarPlataforma(this.chapaEleicao).subscribe((data) => {
      this.messageService.addMsgSuccess('MSG_PLATAFORMA_ELEITORAL_ALTERADO');
      this.chapaEleicaoChange.emit(data);
      this.isCarregaDadosRetificacao = true;
      this.isPossuiAlteracao = false;
      this.isPossuiRetificacao = true;
      this.inicializarDadosComponente();
    }, error => {
      this.messageService.addMsgDanger(error);
    })
  }

  /**
   * Método que realiza a adição de uma nova rede social.
   */
  public addOutraRedeSocial(): void {
    this.redesSociaisChapaOutras.push({
      tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS },
      descricao: ''
    });

    this.isCamposAlterados();
  }

  /**
   * Recupera a mensagem do informativo para eleição chapa.
   */
  public getMensagemInformativoEleicaoChapa(): string {
    let mensagem = '';

    if (this.isPlataformaEleitoralUfBr()) {
      mensagem = this.messageService.getDescription('MSG_INFORMATIVO_ELEICAO_CHAPA_ELEITORAL_UF', [
        this.chapaEleicao.profissional.uf,
        this.eleicao.ano
      ]);
    }

    if (this.isPlataformaEleitoralIes()) {
      mensagem = this.messageService.getDescription('MSG_INFORMATIVO_ELEICAO_CHAPA_ELEITORAL_IES', [
        this.chapaEleicao.profissional.uf,
        this.eleicao.ano
      ]);
    }

    return mensagem;
  }

  /**
   * Recupera o arquivo conforme a entidade 'resolucao' informada.
   *
   * @param event
   * @param resolucao
   */
  public downloadResolucao(event: EventEmitter<any>, resolucao: any): void {
    this.calendarioClientService.downloadArquivo(resolucao.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Abre o informativo de eleição chapa.
   */
  public abrirInformativoEleicaoChapa(): void {
    this.modalInformativoEleicaoChapaEleitoral = this.modalService.show(this.templateInformativoChapaEleitoralModal, Object.assign({}, { class: 'modal-lg' }))
  }

  /**
   * Fecha o informativo de eleição chapa.
   */
  public fecharInformativoEleicaoChapa(): void {
    this.modalInformativoEleicaoChapaEleitoral.hide();
  }

  /**
   * Remove a outra rede social de acordo com o index informado.
   *
   * @param index
   */
  public removeOutraRedeSocial(index): void {
    this.redesSociaisChapaOutras.splice(index, 1);
    this.isCamposAlterados();
  }

  /**
   * Válida se a plataforma eleitoral é uma 'UF-BR'.
   */
  public isPlataformaEleitoralUfBr(): boolean {
    return this.chapaEleicao.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_UF_BR;
  }

  /**
   * Válida se a plataforma eleitoral é uma 'IES'.
   */
  public isPlataformaEleitoralIes(): boolean {
    return this.chapaEleicao.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  public isDisabled(): boolean {
    return !(this.isUsuarioCEN ? true : false);
  }

  /**
   * Válida se a rede social está no tipo 'Outros'.
   *
   * @param redeSocial
   */
  public isOutraRedeSocial(redeSocial): boolean {
    return redeSocial.tipoRedeSocial === Constants.TIPO_REDE_SOCIAL_OUTROS;
  }

  /**
   * Verifica se o campo de descrição de rede social está desabilitado.
   *
   * @param redeSocial
   */
  public isdescricaoRedeSocialDesabilitada(redeSocial: any): boolean {
    return  !redeSocial.isAtivo || this.isDisabled();
  }

  /**
   * Verifica se o campo descrição do rede social está visível.
   * @param redeSocial
   */
  public isDescricaoRedeSocialVisivel(redeSocial: any): boolean {
    return redeSocial.descricao.trim() || redeSocial.isAtivo;
  }

  /**
   * Verifica se o campo rede social outro está visível.
   * @param redesSociaisChapaOutras
   */
  public isRedesSociaisChapaOutrasVisivel(redesSociaisChapaOutras: Array<any>): boolean {
    return this.redesSociaisChapaOutrasAtiva || redesSociaisChapaOutras.length > 1 || redesSociaisChapaOutras[0].descricao.trim();
  }

  /**
   * Verifica se inserção do novos redes sociais do tipo outros está habilitado.
   */
  public isRedesSociaisChapaOutrasDesabilitada(): boolean {
    return this.isDisabled();
  }


  /**
   * Verifica se o campo descrição do rede social outros está desabilitado.
   */
  public isRedesSociaisChapaOutrasDescricaoDesabilitada(): boolean {
    return !this.redesSociaisChapaOutrasAtiva || this.isRedesSociaisChapaOutrasDesabilitada();
  }

  /**
   * Inicializa a variável de resolução.
   */
  private inicializarResolucao(): void {
    this.resolucao = JSON.parse(JSON.stringify(this.eleicao.calendario.arquivos)).shift();
  }

  /**
   * Inicializa a configuração do ckeditor.
   */
  private inicializaConfiguracaoCkeditor(): void {
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
      ],
      title: 'teste123'
    };
  }

  /**
   * Válida se houve alteração em algum campo do formulário.
   */
  public isCamposAlterados(): boolean {

    if(this.isUsuarioCEN) {
      this.isPossuiAlteracao = this.chapaEleicao.descricaoPlataforma != this._descricaoPlataforma||
      !deepEqual(this.redesSociaisChapaFixas, this._redesSociaisChapaFixas) ||
      !deepEqual(this._redesSociaisChapaOutras, this.redesSociaisChapaOutras)||
      !deepEqual(this._redesSociaisChapaOutrasAtiva, this.redesSociaisChapaOutrasAtiva);
      return  this.isPossuiAlteracao;
    }

  }

  /**
   * Inicializa as variáveis referentes a rede social.
   */
  private inicializarRedesSociais(): void {
    this.redesSociaisChapaFixas = [
      { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_FACEBOOK }, icone: 'fa fa-facebook-official fa-2x', descricao: '', isAtivo: false },
      { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_INSTAGRAM }, icone: 'fa fa-instagram fa-2x', descricao: '', isAtivo: false },
      { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_LINKEDIN }, icone: 'fa fa-linkedin fa-2x', descricao: '', isAtivo: false },
      { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_TWITTER }, icone: 'fa fa-twitter fa-2x', descricao: '', isAtivo: false }
    ];

    this.redesSociaisChapaOutras = [];
    this.redesSociaisChapaOutrasAtiva = false;

    if (!this.chapaEleicao.id) {
      this.redesSociaisChapaOutras = [
        { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: '' }
      ];
    } else {

      this.chapaEleicao.redesSociaisChapa.forEach((redeSocialChapa) => {

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_FACEBOOK) {
          this.redesSociaisChapaFixas[0].isAtivo = redeSocialChapa.isAtivo;
          this.redesSociaisChapaFixas[0].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_INSTAGRAM) {
          this.redesSociaisChapaFixas[1].isAtivo = redeSocialChapa.isAtivo;
          this.redesSociaisChapaFixas[1].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_LINKEDIN) {
          this.redesSociaisChapaFixas[2].isAtivo = redeSocialChapa.isAtivo;
          this.redesSociaisChapaFixas[2].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_TWITTER) {
          this.redesSociaisChapaFixas[3].isAtivo = redeSocialChapa.isAtivo;
          this.redesSociaisChapaFixas[3].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_OUTROS) {
          this.redesSociaisChapaOutrasAtiva = redeSocialChapa.isAtivo;
          this.redesSociaisChapaOutras.push({
            tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , 
            descricao: redeSocialChapa.descricao,
            isAtivo: redeSocialChapa.isAtivo
          });
        }
      });

      if (this.redesSociaisChapaOutras.length === 0) {
        this.redesSociaisChapaOutras = [
          { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: '' }
        ];
      } else {
        this.redesSociaisChapaOutrasAtiva = this.redesSociaisChapaOutras[0].isAtivo;
      }

    }
    this._redesSociaisChapaFixas = _.cloneDeep(this.redesSociaisChapaFixas);
    this._redesSociaisChapaOutras = _.cloneDeep(this.redesSociaisChapaOutras);
    this._redesSociaisChapaOutrasAtiva = this.redesSociaisChapaOutrasAtiva;
  }


  /**
   * Método responsável por carregar os dados das retificações da Chapa
   * @param label 
   */
  public carregarRetificacoes(label: any) {
    if(label == 'LABEL_RETIFICACAO' && this.isCarregaDadosRetificacao){
      this.chapaEleitoralService.getRetificacoesPlataforma(this.chapaEleicao.id).subscribe((data)=>{
        this.isCarregaDadosRetificacao = false;
        this.retificacoes = data;
      },
      error =>{
        this.messageService.addMsgDanger(error);
      });
    }
  }

  /**
   * Abre o modal para visualizar o histórico.
   */
  public abrirModalVisualizarRetificacaoPlataforma(retificacao): void {
    const initialState = {
      dadosRetificacao: retificacao,
      isIES: this.isPlataformaEleitoralIes(),
      chapaEleicao: this.chapaEleicao
    };

    this.modalVisualizarRetificacao = this.modalService.show(ModalVisualizarRetificacaoPlataformaComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }

  /**
   * Retorna mensagem de validação do botão
   */
  public getMsgBtn(): string {
      return this.messageService.getDescription('MSG_NECESSARIO_ALTERAR_ALGUM_VALOR_PARA_HABILITAR_BOTAO');
  }

}
