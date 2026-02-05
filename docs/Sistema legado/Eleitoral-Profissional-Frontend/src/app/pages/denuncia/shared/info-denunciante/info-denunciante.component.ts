import { Component, OnInit, Input, OnChanges } from '@angular/core';
import { ConfigCardListInterface } from '../../../../shared/card-list/config-card-list-interface';

import { MessageService } from '@cau/message';

@Component({
  selector: 'app-info-denunciante',
  templateUrl: './info-denunciante.component.html',
  styleUrls: ['./info-denunciante.component.scss']
})
export class InfoDenuncianteComponent implements OnInit, OnChanges {

  @Input() isSigiloso: any = false;
  @Input('denunciante') denunciante: any = null;
  public infoDenunciante: ConfigCardListInterface;

  constructor(
    private messageService: MessageService,
  ) { }

  ngOnInit() {
    this.initData();
  }

  ngOnChanges() {
    this.initData();
  }

  /**
   * Carrega os dados da página.
   */
  private initData = () => {
    this.carregarInformacoesDenunciante();
  }

  /**
   * Carrega as informações do denunciante.
   */
  private carregarInformacoesDenunciante = () => {
    this.infoDenunciante = {
      header: [{
        'field': 'nome',
        'shadow': this.isSigiloso,
        'header': this.messageService.getDescription('LABEL_NOME_COMPLETO')
      }, {
        'field': 'registro',
        'shadow': this.isSigiloso,
        'header': this.messageService.getDescription('LABEL_REGISTRO')
      }, {
        'field': 'email',
        'shadow': this.isSigiloso,
        'header': this.messageService.getDescription('LABEl_EMAIL_MIN')
      }],
      data: [{
        nome: this.getRandomString(this.denunciante.name),
        email: this.getRandomString(this.denunciante.email),
        registro: this.getRandomString(this.denunciante.registroNacional),
      }]
    };
  }

  /**
   * Retorna uma string randômica.
   * 
   * @param text 
   * @return string 
   */
  getRandomString(text: string) {
    if (this.isSigiloso) {
      var length = text.length;
      var randomChars = 'ABCÇDEFGHIJKLMNOPQRSTUVWXYZabcçdefghijklmnopqrstuvwxyz0123456789^`$@%#(+_).!';

      text = '';
      
      for (var i = 0; i < length; i++) {
        text += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
      }
    }

    return text;
  }
}
