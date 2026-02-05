import { Router } from '@angular/router';
import { Component, OnInit, ViewChild, Input, EventEmitter, Output } from '@angular/core';

import * as moment from 'moment';
import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { CKEditor4 } from 'ckeditor4-angular';
import { StringService } from 'src/app/string.service';
import { UtilsService } from 'src/app/utils.service';

@Component({
  selector: 'aba-plataforma-eleitoral-redes-sociais',
  templateUrl: './aba-plataforma-eleitoral-redes-sociais.component.html',
  styleUrls: ['./aba-plataforma-eleitoral-redes-sociais.component.scss']
})
export class AbaPlataformaEleitoralRedesSociaisComponent implements OnInit {

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

  private textoSimples: string;

  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Output() chapaEleicaoChange = new EventEmitter<any>();
  @Output() avancar: EventEmitter<any> = new EventEmitter();
  @Output() retroceder: EventEmitter<any> = new EventEmitter();
  @Output() cancelar: EventEmitter<any> = new EventEmitter();
  @Output() voltar: EventEmitter<any> = new EventEmitter();

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
    this.inicializarResolucao();
    this.inicializarRedesSociais();
    this.inicializaConfiguracaoCkeditor();
    if(!this.chapaEleicao.id) {
      this.abrirInformativoEleicaoChapa();
    }

    this._descricaoPlataforma = this.chapaEleicao.descricaoPlataforma ? this.chapaEleicao.descricaoPlataforma : '';
    this.textoSimples = this.textoSimples != undefined ? this.textoSimples : '';
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
    this.chapaEleicao.redesSociaisChapa = [
      ...this.redesSociaisChapaFixas,
      ...this.redesSociaisChapaOutras
    ];

    this.chapaEleitoralService.salvar(this.chapaEleicao).subscribe((data) => {
      this.messageService.addMsgSuccess('MSG_PLATAFORMA_ELEITORAL_INCLUIDA');
      this.chapaEleicaoChange.emit(data);
      this.avancar.emit(Constants.ABA_MEMBROS_CHAPA);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Método que realiza a adição de uma nova rede social.
   */
  public addOutraRedeSocial(): void {
    this.redesSociaisChapaOutras.push({
      tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS },
      descricao: ''
    });
  }

  /**
   * Recupera a mensagem do informativo para eleição chapa.
   */
  public getMensagemInformativoEleicaoChapa(): string {
    let mensagem = '';

    if (this.isPlataformaEleitoralUfBr()) {
      mensagem = this.messageService.getDescription('MSG_INFORMATIVO_ELEICAO_CHAPA_ELEITORAL_UF', [
        this.chapaEleicao.cauUf.prefixo,
        this.eleicao.ano
      ]);
    }

    if (this.isPlataformaEleitoralIes()) {
      mensagem = this.messageService.getDescription('MSG_INFORMATIVO_ELEICAO_CHAPA_ELEITORAL_IES', [
        this.eleicao.ano
      ]);
    }

    return mensagem;
  }

  /**
     * Alterar valor da decrição do currículo.
     *
     * @param event
     */
  public onChangeCkeditor(event: CKEditor4.EventInfo) {
    this.setTextoSimples(StringService.getPlainText(event.editor.getData()));
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
   * Retorna texto de hint apresentado na tela de cadastro de plataforma eleitoral.
   */
  public getHintPlataformaEleitoral(): string {
    return this.messageService.getDescription('MSG_HINT_PLATAFORMA_ELEITORAL');
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
    return  !redeSocial.isAtivo || this.isChapaConcluida();
  }

  public isDescricaoRedeSocialVisivel(redeSocial: any): boolean {
    return redeSocial.descricao.trim() || redeSocial.isAtivo;
  }

  public isRedesSociaisChapaOutrasVisivel(redesSociaisChapaOutras: Array<any>): boolean {
    return this.redesSociaisChapaOutrasAtiva || redesSociaisChapaOutras.length > 1 || redesSociaisChapaOutras[0].descricao.trim();
  }

  public isRedesSociaisChapaOutrasDesabilitada(): boolean {
    return !this.redesSociaisChapaOutrasAtiva || this.isAtividadeSecundariaExpirada();
  }

  /**
   * Válida se a chapa está concluída.
   */
  public isChapaConcluida(): boolean {
    return this.chapaEleicao.idEtapa == Constants.STATUS_CHAPA_ETAPA_CONCLUIDO;
  }

  /**
   * Verifica se o prazo da atividade secundária está expirado.
   */
  public isAtividadeSecundariaExpirada(): boolean {
    let agora = moment().startOf('day').toDate();

    let atividadePrincipal = JSON.parse(JSON.stringify(this.eleicao.calendario.atividadesPrincipais)).shift();
    let atividadeSecundaria = JSON.parse(JSON.stringify(atividadePrincipal.atividadesSecundarias)).shift();
    let dataFim = moment(atividadeSecundaria.dataFim, 'YYYY-MM-DD').toDate();

    return agora > dataFim;
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
    this.configuracaoCkeditor = UtilsService.getConfiguracaoPadraoCKeditor();
  }

  /**
   * Válida se houve alteração em algum campo do formulário.
   */
  public isCamposAlterados(): boolean {
    return this.chapaEleicao.descricaoPlataforma != this._descricaoPlataforma ||
      !deepEqual(this.redesSociaisChapaFixas, this._redesSociaisChapaFixas) ||
      !deepEqual(this._redesSociaisChapaOutras, this.redesSociaisChapaOutras);
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
          this.redesSociaisChapaFixas[0].isAtivo = true;
          this.redesSociaisChapaFixas[0].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_INSTAGRAM) {
          this.redesSociaisChapaFixas[1].isAtivo = true;
          this.redesSociaisChapaFixas[1].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_LINKEDIN) {
          this.redesSociaisChapaFixas[2].isAtivo = true;
          this.redesSociaisChapaFixas[2].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_TWITTER) {
          this.redesSociaisChapaFixas[3].isAtivo = true;
          this.redesSociaisChapaFixas[3].descricao = redeSocialChapa.descricao;
        }

        if (redeSocialChapa.tipoRedeSocial.id == Constants.TIPO_REDE_SOCIAL_OUTROS) {
          this.redesSociaisChapaOutrasAtiva = true;
          this.redesSociaisChapaOutras.push({
            tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: redeSocialChapa.descricao
          });
        }
      });

      if (this.redesSociaisChapaOutras.length === 0) {
        this.redesSociaisChapaOutras = [
          { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: '' }
        ];
      } else {
        this.redesSociaisChapaOutrasAtiva = true;
      }

    }
    this._redesSociaisChapaFixas = _.cloneDeep(this.redesSociaisChapaFixas);
    this._redesSociaisChapaOutras = _.cloneDeep(this.redesSociaisChapaOutras);
  }

  /**
   * Responsável por redicrecionar o usuário para a tela inicial do sistema.
   */
  private redirecionarTelaInicial(): void {
    this.router.navigate(['/']);
  }

}
