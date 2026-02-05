import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { MessageService } from '@cau/message';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Constants } from 'src/app/constants.service';
import * as deepEqual from "deep-equal";
import * as _ from 'lodash';


@Component({
  selector: 'modal-visualizar-retificacao-plataforma',
  templateUrl: './modal-visualizar-retificacao-plataforma.component.html',
  styleUrls: ['./modal-visualizar-retificacao-plataforma.component.scss']
})

export class ModalVisualizarRetificacaoPlataformaComponent implements OnInit {

  @Input() public dadosRetificacao?: any;
  @Input() public isIES?:any;
  @Input() public chapaEleicao?: any
  
  public arquivo: any;
  public configuracaoCkeditor: any = {};
  public modalPendeciasMembro: BsModalRef | null;
  public modalVisualizarJulgamentoSubstituicao: BsModalRef | null;
  
  public redesSociaisChapaFixas: any;
  public redesSociaisChapaOutras: any;
  public redesSociaisChapaOutrasAtiva: boolean;
  public _redesSociaisChapaFixas: any;
  public _redesSociaisChapaOutras: any;

  constructor(
    public modalRef: BsModalRef,
    private modalService: BsModalService,
    private messageService: MessageService,
    private julgamentoFinalClientService: JulgamentoFinalClientService,
  ){}

  ngOnInit() {
    this.inicializaConfiguracaoCkeditor();
    this.inicializarRedesSociais();
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

    
    if(this.dadosRetificacao.redesSociaisChapa && this.dadosRetificacao.redesSociaisChapa.length > 0 ) {
      this.dadosRetificacao.redesSociaisChapa.forEach((redeSocialChapa) => {
  
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
    } else {
      this.redesSociaisChapaOutras = [
        { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: '' }
      ];
    }

    if (this.redesSociaisChapaOutras.length === 0) {
      this.redesSociaisChapaOutras = [
        { tipoRedeSocial: { id: Constants.TIPO_REDE_SOCIAL_OUTROS } , descricao: '' }
      ];
    } else {
      this.redesSociaisChapaOutrasAtiva = this.redesSociaisChapaOutras[0].isAtivo;
    }

    this._redesSociaisChapaFixas = _.cloneDeep(this.redesSociaisChapaFixas);
    this._redesSociaisChapaOutras = _.cloneDeep(this.redesSociaisChapaOutras);
  }

  public isDisabled(): boolean {
    return true;
  }

  /**
   * Verifica se o campo descrição do rede social está visível.
   * @param redeSocial
   */
  public isDescricaoRedeSocialVisivel(redeSocial: any): boolean {
    return redeSocial.descricao.trim() || redeSocial.isAtivo;
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
   * Verifica se o campo rede social outro está visível.
   * @param redesSociaisChapaOutras
   */
  public isRedesSociaisChapaOutrasVisivel(redesSociaisChapaOutras: Array<any>): boolean {
    return this.redesSociaisChapaOutrasAtiva || redesSociaisChapaOutras.length > 1 || redesSociaisChapaOutras[0].descricao.trim();
  }

  /**
   * Verifica se o campo descrição do rede social outros está desabilitado.
   */
  public isRedesSociaisChapaOutrasDescricaoDesabilitada(): boolean {
    return !this.redesSociaisChapaOutrasAtiva || this.isRedesSociaisChapaOutrasDesabilitada();
  }

  /**
   * Verifica se inserção do novos redes sociais do tipo outros está habilitado.
   */
  public isRedesSociaisChapaOutrasDesabilitada(): boolean {
    return this.isDisabled();
  }

  private inicializaConfiguracaoCkeditor(): void {
    this.configuracaoCkeditor = {
      toolbar: [
        { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
      ],
      title: 'Justificativa'
    };
  }

  /**
   * Formata o número de sequencia para suportar zeros a esquerda
   */
  public formatSequencia(): string {
    let result = '';
    if(String(this.dadosRetificacao.sequencia).length < 2){
      result = `0${String(this.dadosRetificacao.sequencia)}`
    } else {
      result = String(this.dadosRetificacao.sequencia)
    }
    return result;
  }
}
