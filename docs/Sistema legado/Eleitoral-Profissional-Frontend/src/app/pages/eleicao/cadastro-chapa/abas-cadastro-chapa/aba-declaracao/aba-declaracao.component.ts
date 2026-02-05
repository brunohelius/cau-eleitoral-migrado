import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';
import { Constants } from 'src/app/constants.service';

import * as _ from 'lodash';

declare var jQuery: any;

@Component({
  selector: 'aba-declaracao',
  templateUrl: './aba-declaracao.component.html',
  styleUrls: ['./aba-declaracao.component.scss']
})
export class AbaDeclaracaoComponent implements OnInit {

  @Input() eleicao: any;
  @Input() chapaEleicao: any;
  @Output() chapaEleicaoChange = new EventEmitter<any>();
  @Output() retroceder: EventEmitter<any> = new EventEmitter();
  @Output() cancelar: EventEmitter<any> = new EventEmitter();
  @Output() voltar: EventEmitter<any> = new EventEmitter();
  @Output() avancar: EventEmitter<any> = new EventEmitter();


  public declaracaoParametrizada: any;
  public documentosDeclaracao: Array<any>;
  public limiteDocumentosDeclaracao: number;
  public nomeDocumentoDeclaracao: string;

  constructor(
    private router: Router,
    private messageService: MessageService,
    private chapaEleicaoClientService: ChapaEleicaoClientService,
  ) {

  }

  ngOnInit() {
    this.inicializarDeclaracao();
    this.documentosDeclaracao = [];
    this.limiteDocumentosDeclaracao = 2;
  }

  /**
   * Confirma cadastro da chapa.
   */
  public confirmar(): void {
    this.chapaEleicaoClientService.confirmarChapa(this.chapaEleicao.id, this.getDataConfirmar()).subscribe(
      data => {
        this.chapaEleicao.idEtapa = Constants.STATUS_CHAPA_ETAPA_CONCLUIDO;
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Fecha o formulário de avança para etapa 2 (conclusão da chapa).
   */
  public fechar(): void {
    this.avancar.emit(null);
  }

  /**
   * Retorna dados da declaração utilizados para confirmar a chapa.
   */
  private getDataConfirmar(): Object {
    return {
      itensDeclaracao: this.getIdsItemDeclaracao(),
      arquivosRespostaDeclaracaoChapa: this.documentosDeclaracao
    };
  }

  public onClickUpload( ): void{
    if(this.documentosDeclaracao.length >= 2) {
      this.messageService.addMsgWarning('MSG_PERMISSAO_UPLOAD_N_ARQUIVOS', ['dois', this.limiteDocumentosDeclaracao]);      
    }
  }

  /**
   * Verifica a quantidade itens marcados, para declarações de resposta únicas.
   *
   * @param itemDeclaracao
   */
  public onChanteItemDeclaracao(itemDeclaracao: any): void {
    if (this.declaracaoParametrizada.declaracao.tipoResposta == Constants.TIPO_RESPOSTA_DECLARACAO_UNICA && !itemDeclaracao.valor) {
      this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(element => {
        element.valor = element.id == itemDeclaracao.id ? true : false;
      });
    }
  }

  /**
   * Realiza Upload de documento de declaração.
   *
   * @param arquivoEvent
   */
  public uploadDocumentoDeclaracao(arquivoEvent): void {
    let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, idDeclaracao: this.declaracaoParametrizada.declaracao.id, arquivo: undefined };
    this.nomeDocumentoDeclaracao = arquivoTO.nome;
    this.chapaEleicaoClientService.validarArquivoRespostaDeclaracaoChapa(arquivoTO).subscribe(
      data => {
        arquivoTO.arquivo = arquivoEvent;
        this.documentosDeclaracao.push(arquivoTO);
        this.nomeDocumentoDeclaracao = '';
      },
      error => {
        this.nomeDocumentoDeclaracao = '';
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
   * Exclui documento da lista de documentos da declaração.
   *
   * @param indice
   */
  public removerDocumentoDeclaracao(indice: number): void {
    this.documentosDeclaracao.splice(indice, 1);
  }

   /**
   * Válida se a chapa está concluída.
   */
  public isChapaConcluida(): boolean {
    return this.chapaEleicao.idEtapa == Constants.STATUS_CHAPA_ETAPA_CONCLUIDO;
  }

  /**
   * Verifica se o input para upload de documentos de declaração está habilitado.
   */
  public isDocumentoDeclaracaoDesabilitado(): boolean {
    return this.documentosDeclaracao.length >= this.limiteDocumentosDeclaracao;
  }

  /**
   * Função chamada quando o método anterior é chamado.
   */
  public anterior(): void {
    let controle: any = {isAlterado: this.isCamposAlterados(), aba: Constants.ABA_MEMBROS_CHAPA }
    this.retroceder.emit(controle);
  }

  /**
   * Inicializa declaração para aceitar convite para participação em chapa eleitoral.
   */
  public inicializarDeclaracao(): void {
    let tipoDeclaracao: number = this.isConselheiroIES() ? Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_IES : Constants.TIPO_DECLARACAO_CADASTRO_CHAPA_UF;
    let idAtividadeSecundaria = this.chapaEleicao.atividadeSecundariaCalendario.id;

    this.declaracaoParametrizada = this.chapaEleicaoClientService.getDeclaracaoParametrizada(idAtividadeSecundaria, tipoDeclaracao).subscribe(
      data => {
        this.declaracaoParametrizada = data;
        //this.declaracaoParametrizada.declaracao.itensDeclaracao = _.orderBy(data.declaracao.itensDeclaracao, ['sequencial'], ['desc']);
      },
      error => {
        this.messageService.addMsgDanger(error);
      }
    );
  }

  /**
  * Válida se é "Conselheiro UF-BR".
  */
  public isConselheiroUfBR(): boolean {
    return this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR;
  }

  /**
   * Válida se é "IES".
   */
  public isConselheiroIES(): boolean {
    return this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
  }

   /**
   * Válida se houve alteração em algum campo do formulário.
   */
  public isCamposAlterados(): boolean {
    return this.getIdsItemDeclaracao().length > 0 || this.documentosDeclaracao.length > 0;
  }

  /**
   * Retorna mensagem de chapa cadastrada.
   */
  public getMensagemChapaCadastrada(): string {
    return this.messageService.getDescription('LABEL_PARABENS_VOCE_CADASTROU_CHAPA_ELEITORAL', [
      this.chapaEleicao.cauUf.descricao
    ]);
  }


  /**
   * Retorna lista de ids com todos os itens de declarações selecionados.
   */
  private getIdsItemDeclaracao(): Array<number> {
    let idsItemDeclaracao = [];
    this.declaracaoParametrizada.declaracao.itensDeclaracao.forEach(
      itemDeclaracao => {
        if (itemDeclaracao.valor) {
          idsItemDeclaracao.push({id: itemDeclaracao.id});
        }
      }
    );
    return idsItemDeclaracao;
  }

}
