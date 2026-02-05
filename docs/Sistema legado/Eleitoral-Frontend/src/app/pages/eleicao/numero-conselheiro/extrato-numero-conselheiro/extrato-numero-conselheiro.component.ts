import * as _ from 'lodash'
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter, Output } from '@angular/core';

import { AtividadeSecundariaClientService } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';
import { Constants } from 'src/app/constants.service';
import { Message } from '@angular/compiler/src/i18n/i18n_ast';
import { tap } from 'rxjs/operators';

@Component({
  selector: 'extrato-numero-conselheiro',
  templateUrl: './extrato-numero-conselheiro.component.html',
  styleUrls: ['./extrato-numero-conselheiro.component.scss']
})
export class ExtratoNumeroConselheiroComponent implements OnInit {

  @Output('voltar') voltar: EventEmitter<any> = new EventEmitter(null);
  
  public extratos: any[];
  public idAtividadeSecundaria: number;
  public numeroRegistrosPaginacao: number = 10;

  /**
   * Construtor da classe
   * @param route
   * @param messageService
   * @param atividadeSecundariaService
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private atividadeSecundariaService: AtividadeSecundariaClientService,
  ) { }

  /**
   * Inicializa as dependências do Component.
   */
  ngOnInit() {
    this.idAtividadeSecundaria = Number(this.route.snapshot.paramMap.get('id'));
    this.getExtratoPorAtividadeSecundaria(this.idAtividadeSecundaria);
  }

  /**
   * Retorna o extrato do Número de Conselheiros, de acordo com o ID da atividade secundária.
   * 
   * @param id
   */
  public getExtratoPorAtividadeSecundaria(id: number): void {
    this.atividadeSecundariaService.getExtratoConselheirosPorAtividadeSecundaria(id).subscribe((data: any[]) => {
      this.extratos = data;
    }, error => {
      console.error(error);
      this.messageService.addMsgDanger(error.message);
    });
  }

  /**
   * Gera o Extrato (Zip) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
   *
   * @param event
   * @param idExtrato
   */
  public gerarExtratoConselheirosZip(event: EventEmitter<any>, idExtrato: number): void {
    this.atividadeSecundariaService.gerarExtratoConselheirosZip(idExtrato).subscribe(data => {
      event.emit(data);
    }, error => {
      //this.messageService.addMsgDanger(error);
      this.messageService.addMsgDanger('MSG_EXTRATO_EM_EXECUCAO');
    });
  }

  /**
   * Retorna a descrição do extrato
   * 
   * @param extrato 
   */
  public getDescricao(extrato) : string {
    return this.messageService.getDescription('LABEL_DESCRICAO_EXTRATO', [this.getNumeroFormatado(extrato.numero), extrato.descricaoEleicao]);
  }

  /**
   * Retorna a descrição do extrato com extensão do arquivo
   * 
   * @param extrato 
   */
  public getDescricaoComExtensao(extrato){
    return this.getDescricao(extrato) + ".zip";
  }


  /**
   * Gera o Extrato (PDF) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
   *
   * @param event
   * @param idExtrato
   */
  public gerarExtratoConselheirosPdf(event: EventEmitter<any>, idExtrato: number): void {
    this.atividadeSecundariaService.gerarExtratoConselheirosPdf(idExtrato).subscribe(data => {
      event.emit(data);
    }, error => {
      console.error(error);
      this.messageService.addMsgDanger('MSG_ERRO_COMUNICACAO_TENTE_MAIS_TARDE');
    });
  }

  /**
   * Gera o Extrato (Excel) referente ao 'Número de Conselheiros', conforme o ID do Extrato.
   *
   * @param event
   * @param idExtrato
   */
  public gerarExtratoConselheirosExcel(event: EventEmitter<any>, idExtrato: number): void {
    this.atividadeSecundariaService.gerarExtratoConselheirosExcel(idExtrato).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      console.error(error);
      this.messageService.addMsgDanger('MSG_ERRO_COMUNICACAO_TENTE_MAIS_TARDE');
    });
  }

  /**
   * Retorna o 'Número do Extrato' formatado.
   * 
   * @param numero
   */
  public getNumeroFormatado(numero: number): string {
    return String(numero).padStart(2, '0');
  }

  /**
   * Retorna para a aba 'Número de Conselheiros'.
   */
  public voltarNumeroConselheiros(): void {
    this.voltar.emit();
  }

}
