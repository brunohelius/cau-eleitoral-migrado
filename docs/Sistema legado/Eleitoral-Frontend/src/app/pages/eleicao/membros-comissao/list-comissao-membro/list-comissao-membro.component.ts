import * as _ from 'lodash'
import { Component, OnInit, Input, EventEmitter } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'list-comissao-membro',
  templateUrl: './list-comissao-membro.component.html',
  styleUrls: ['./list-comissao-membro.component.scss']
})
export class ListComissaoMembroComponent implements OnInit {

  @Input() eleicoes: any;
  @Input() anosEleicoes: any;
  @Input() cauUfs: any;
  @Input() configuracaoEleicao: any;
  @Input() tiposParticipacao: any;

  public editable: boolean;
  public eleicao: any;
  public numeroMembros: any;
  public coordenadores: Array<any>;
  public membros: Array<any>;
  public qteMembrosSelecionada: boolean = false;
  public cauUfSelecionada: string;
  public justificativa: string;
  public submitted: boolean = false;
  public membrosComissoes: Array<any> = [];
  public constants: any = Constants;
  public usuario: any;
  public urlArquivo: string;
  public nomeDoc: string = "comissaoEleitoral.docx";


  /**
   * Construtor da classe
   * @param route 
   * @param messageService 
   * @param eleicaoClientService 
   */
  constructor(
    private route: ActivatedRoute,
    private messageService: MessageService,
    private eleicaoClientService: EleicaoClientService,
    private securityService: SecurityService
  ) { }

  /**
   * 
   */
  ngOnInit() {
    this.usuario = {};
    this.usuario = this.securityService.credential["_user"];
    let calendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario;
    this.setObjEleicao({
      idEleicao: calendario.id,
      id: this.configuracaoEleicao.id,
      eleicao: calendario.eleicao,
      anoEleicao: calendario.ano,
      cauUf: this.usuario.cauUf.id,
      minimoMembrosComissao: this.configuracaoEleicao.quantidadeMinima,
      maximoMembrosComissao: this.configuracaoEleicao.quantidadeMaxima,
      tipoOpcao: this.configuracaoEleicao.tipoOpcao
    });
    this.getMembrosComissao();
    this.urlArquivo = `${environment.url}/membroComissao/informacaoComissaoMembro/${this.configuracaoEleicao.id}/pdf`;
  }

  /**
   * Define os dados do objeto eleição
   * objEleicao: any   
   */
  public setObjEleicao(objEleicao: any) {
    this.eleicao = objEleicao;
  }

  /**
   * Retorna a estrutura para a construção de uma comissao de membro
   */
  public getNovosMembroComissao(): any {
    return {
      coordenadores: [],
      membros: [],
      cauUf: undefined
    }
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  private getMembrosComissoes(): void {
    this.eleicaoClientService.getMembrosComissoes(this.eleicao.id).subscribe(
      data => {
        this.setMembrosComissao(data);
      },
      error => {
        this.messageService.addMsgDanger(error.message);
      }
    )
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  public getMembrosComissao(): void {
    this.membrosComissoes = [];
    let cauUfId = parseInt(this.eleicao.cauUf);
    if (!isNaN(cauUfId)) {
      this.eleicaoClientService.getMembroComissoes(this.eleicao.id, cauUfId).subscribe(
        data => {
          if (data.membros.length > 0) {
            this.setMembrosComissao({
              [cauUfId]: data.membros
            });
          }
        },
        error => {
          this.messageService.addMsgDanger(error.message);
        }
      )
    } else {
      this.getMembrosComissoes();
    }
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  private setMembrosComissao(data: any): void {
    _.forIn(data, (value, key) => {
      let membrosComissaoEleitoral = this.getNovosMembroComissao();
      membrosComissaoEleitoral.cauUf = _.find(this.cauUfs, { 'id': parseInt(key) })
      membrosComissaoEleitoral.coordenadores = _.filter(value, membro => {
        this.validarMembroSubstituto(membro);
        return membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR || membro.tipoParticipacao.id === Constants.TIPO_COORDENADOR_MEMBRO_SUBSTITUTO;
      });
      membrosComissaoEleitoral.membros = _.filter(value, membro => {
        this.validarMembroSubstituto(membro);
        return membro.tipoParticipacao.id === Constants.TIPO_MEMBRO;
      });
      this.membrosComissoes.push(membrosComissaoEleitoral);
    });
  }

  /**
   * Valida se o membro possui o membro substituto vinculado, caso não o mesmo é criado vazio
   * @param membroComissao 
   */
  private validarMembroSubstituto(membroComissao: any): void {
    if (membroComissao.membroSubstituto === undefined) {
      membroComissao.membroSubstituto = {};
      membroComissao.membroSubstituto.tipoParticipacao = {};
      membroComissao.membroSubstituto.situacaoVigente = {};
      membroComissao.membroSubstituto.profissional = {};
    }
  }

  /**
    * Recupera o arquivo conforme a id da informaçao da comissão.
    *
    * @param event
    * @param idRegimentoEstatuto
    */
  public downloadResolucao(event: EventEmitter<any>, idResolucao: any): void {
    this.eleicaoClientService.downloadArquivo(idResolucao).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Recupera o arquivo conforme a id da entidade 'resolucao' informada.
   *
   * @param event
   * @param idRegimentoEstatuto
   */
  public downloadDocComissaoEleitoral(event: EventEmitter<any>): void {
    this.eleicaoClientService.downloadGerarDocListaComissao(this.configuracaoEleicao.id).subscribe((data: Blob) => {
      event.emit(data);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Método utilizado pelo 'select' de e-mail para comparar values de 'options'.
   * @param optionValue 
   * @param selectedValue 
   */
  public compareFn(optionValue, selectedValue) {
    return optionValue && selectedValue ? optionValue.id === selectedValue.id : optionValue === selectedValue;
  }

  /**
   * Recupera uma mensagem baseada no status do membro medianta a comissão
   * @param membro 
   */
  public getMsgStatusMembro(membro: any): string {
    let message = null;
    switch (membro.situacaoVigente.id) {
      case Constants.SITUACAO_MEMBRO_PENDENTE:
        message = 'LABEL_EM_ANALISE_PELO_MEMBRO';
        break;
      case Constants.SITUACAO_MEMBRO_CONFIRMADO:
        message = 'LABEL_MEMBRO_CONFIRMADO';
        break;
      case Constants.SITUACAO_MEMBRO_REJEITADO:
        message = 'LABEL_MEMBRO_REJEITOU_PARTICIPACAO';
        break;
    }
    return this.messageService.getDescription(message);
  }

  /**
   * 
   * @param comissao 
   */
  public hasPermissaoAlterarMembrosComissao(comissao: any): boolean {
    return (this.usuario.cauUf.id === Constants.ID_CAUBR || this.usuario.cauUf.id === comissao.cauUf.id);
  }

}
