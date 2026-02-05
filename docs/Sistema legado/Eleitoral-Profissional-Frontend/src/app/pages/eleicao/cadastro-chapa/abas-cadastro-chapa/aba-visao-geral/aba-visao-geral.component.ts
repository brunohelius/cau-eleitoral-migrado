import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';

enum TipoCandidaturaEnum {
  CONSELHEIRO_UF_BR = Constants.TIPO_CONSELHEIRO_UF_BR,
  CONSELHEIRO_IES = Constants.TIPO_CONSELHEIRO_IES
};

export class TipoCandidatura {

  /**
   * Retorna o 'id' do conselheiro UF-BR.
   */
  public getConselheiroUfBr(): number {
    return TipoCandidaturaEnum.CONSELHEIRO_UF_BR;
  }

  /**
   * Retorna o 'id' do conselheiro IES.
   */
  public getConselheiroIes(): number {
    return TipoCandidaturaEnum.CONSELHEIRO_IES;
  }
}

@Component({
  selector: 'aba-visao-geral',
  templateUrl: './aba-visao-geral.component.html',
  styleUrls: ['./aba-visao-geral.component.scss']
})
export class AbaVisaoGeralComponent implements OnInit {

  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Output() avancar: EventEmitter<any> = new EventEmitter();

  public tipoCandidatura: TipoCandidatura;

  /**
   * Construtor da classe.
   *
   * @param messageService
   */
  constructor(private router: Router, private messageService: MessageService, private chapaEleicaoService: ChapaEleicaoClientService) {
    this.tipoCandidatura = new TipoCandidatura();
  }

  /**
   * Executado ao inicializar o componente.
   */
  ngOnInit() {
    if (!this.eleicao.calendario) {
      this.messageService.addMsgWarning(this.messageService.getDescription('MSG_SEM_PERIODO_CADASTRO_CHAPA'));
      this.voltar();
    }
  }

  /**
   * Recupera a mensagem de seleção de representação da chapa.
   */
  public getMensagemSelecaoRepresentacaoChapa(): string {
    return this.messageService.getDescription('MSG_SELECAO_REPRESENTACAO_CHAPA');
  }

  /**
   * Válida se o tipo do processo é ordinário com situação IES.
   */
  public isOrdinarioIES(): boolean {
    if (this.eleicao && this.eleicao.calendario) {
      return this.eleicao && this.eleicao.calendario.situacaoIES && this.eleicao.tipoProcesso.id === Constants.TIPO_PROCESSO_ORDINARIO;
    }

    return false;
  }

  /**
   * Válida se o tipo do processo é extraordinário sem situação IES.
   */
  public isExtraordinarioSemIES(): boolean {
    if (this.eleicao && this.eleicao.calendario) {
      return !this.eleicao.calendario.situacaoIES && this.eleicao.tipoProcesso.id === Constants.TIPO_PROCESSO_EXTRAORDINARIO;
    }

    return false;
  }

  /**
   * Válida se o tipo do processo é extraordinário com situação IES.
   */
  public isExtraordinarioIES(): boolean {
    if (this.eleicao && this.eleicao.calendario) {
      return this.eleicao && this.eleicao.calendario.situacaoIES && this.eleicao.tipoProcesso.id === Constants.TIPO_PROCESSO_EXTRAORDINARIO;;
    }

    return false;
  }

   /**
   * Válida se a chapa está concluída.
   */
  public isChapaConcluida(): boolean {  
    return this.chapaEleicao.idEtapa == Constants.STATUS_CHAPA_ETAPA_CONCLUIDO;   
  }

  /**
   * Responsável por salvar uma nova chapa eleição.
   *
   * @param tpCandidatura
   */
  public salvar(tpCandidatura: TipoCandidaturaEnum): void {
    this.chapaEleicao.tipoCandidatura = { id: tpCandidatura };

    if (!this.chapaEleicao.id) {
      let atividadePrincipal = JSON.parse(JSON.stringify(this.eleicao.calendario.atividadesPrincipais)).shift();
      let atividadeSecundaria = JSON.parse(JSON.stringify(atividadePrincipal.atividadesSecundarias)).shift()
      this.chapaEleicao.atividadeSecundariaCalendario = atividadeSecundaria;
    }

    this.avancar.emit(Constants.ABA_PLATAFORMA_ELEITORAL_REDES_SOCIAIS);
  }

  /**
   * Retorna para a tela anterior.
   */
  public voltar(): void {
    this.router.navigate(['/']);
  }

}
