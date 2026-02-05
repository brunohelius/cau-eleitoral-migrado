import * as _ from 'lodash'
import { Component, OnInit, EventEmitter, TemplateRef } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { SecurityService } from '@cau/security';
import { environment } from 'src/environments/environment';
import { BsModalService } from 'ngx-bootstrap';
import { ComissaoEleitoralService } from 'src/app/client/comissao-eleitoral-client/comissao-eleitoral-client.service';

/**
 * Componente de listagem de comissões eleitorais
 */
@Component({
  selector: 'comissao-eleitoral',
  templateUrl: './comissao-eleitoral.component.html',
  styleUrls: ['./comissao-eleitoral.component.scss']
})
export class ComissaoEleitoralComponent implements OnInit {
  public calendarios: any;
  public eleicoes: any;
  public anosEleicoes: any;
  public cauUfs: any;
  public configuracaoEleicao: any;
  public numeroMembroComimssao: number = 0;
  public informacoesComissao: any;
  private informacoesEleicoes: any;

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
  public nomeDoc: string = "comissaoEleitoral.docx";
  public userDetails: any;
  public modalRef: any;

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
    private comissaoEleitoralService: ComissaoEleitoralService,
    private securityService: SecurityService,
    private modalService: BsModalService
  ) { }

  /**
   * 
   */
  ngOnInit() {
    
    this.calendarios = this.route.snapshot.data["calendarios"];
    this.cauUfs = this.route.snapshot.data["cauUfs"];
    this.informacoesEleicoes = this.route.snapshot.data['infoComissaoEleitoral'];

    this.setEleicoes();

    this.usuario = {};
    this.usuario = this.securityService.credential["_user"];
    this.setInfoComissaoEleitoral();
    let calendario = this.configuracaoEleicao.atividadeSecundaria.atividadePrincipalCalendario.calendario;
    
    this.setObjEleicao({
      idEleicao: calendario.id,
      id: this.configuracaoEleicao.id,
      eleicao: calendario.eleicao,
      anoEleicao: calendario.eleicao.ano,
      cauUf: this.configuracaoEleicao.idCauUf,
      minimoMembrosComissao: this.configuracaoEleicao.quantidadeMinima,
      maximoMembrosComissao: this.configuracaoEleicao.quantidadeMaxima,
      tipoOpcao: this.configuracaoEleicao.tipoOpcao
    });
    
    this.setMembrosComissao(this.route.snapshot.data['comissaoEleitoral']);
  }

  /**
   * Seta as eleições que o profissional esta participando
   */
  private setEleicoes(){
    this.eleicoes = [];
    let lastIdEleicao = null;
    this.calendarios.forEach(calendario => {
      if(lastIdEleicao !== calendario.eleicao.id){
        this.eleicoes.push(calendario.eleicao);
      }
      lastIdEleicao = calendario.eleicao.id;
    });
  }

  /**
   * Estrutura a informação da comissão, para caso vir mais de uma
   * @param infoComissao 
   */
  public setInfoComissaoEleitoral(): void {
    let infoComissao = this.informacoesEleicoes;
    this.informacoesComissao = infoComissao;
    if (!Array.isArray(infoComissao)) {
      this.configuracaoEleicao = infoComissao.informacaoComissaoMembro
      this.configuracaoEleicao.idCauUf = infoComissao.idCauUf
    } else {
      this.configuracaoEleicao = _.cloneDeep(infoComissao[infoComissao.length - 1].informacaoComissaoMembro);
      this.configuracaoEleicao.idCauUf = infoComissao[infoComissao.length - 1].idCauUf
    }
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
    let informacaoEleicao = this.getInformacaoByEleicao();
    if (informacaoEleicao) {
      const idEleicao = parseInt(informacaoEleicao.informacaoComissaoMembro.id)
      this.eleicaoClientService.getMembrosComissoes(idEleicao).subscribe(
        data => {
          this.setMembrosComissao(data);
        },
        error => {
          this.messageService.addMsgDanger(error.message);
        }
      )
    }
  }

  /**
   * Retorna o objeto informação eleitoral pela eleição selecionada
   */
  private getInformacaoByEleicao(): any {
    const eleicaoId = parseInt(this.eleicao.eleicao.id);
    let informacaoEleicao = null;
    this.informacoesEleicoes.forEach(info => {
      if (parseInt(info.informacaoComissaoMembro.atividadeSecundaria.atividadePrincipalCalendario.calendario.eleicao.id) === eleicaoId) {
        informacaoEleicao = info;
      }
    });
    return informacaoEleicao;
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  public getMembrosComissao(): void {
    this.membrosComissoes = [];
    let cauUfId = parseInt(this.eleicao.cauUf);
    if (!isNaN(cauUfId)) {
      const params = {
        eleicaoId: this.eleicao.eleicao.id,
        cauUfId,
        anoEleicao: this.eleicao.anoEleicao
      }
      this.eleicaoClientService.getMembroComissoes(params).subscribe(
        data => {
          this.numeroMembroComimssao = data.membros.length
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
   * Retorna a descrição/prefixo de acordo com o caubr
   */
  public getDescrucaoCauUf(cauUF: any) : string {
    if (cauUF.id == Constants.ID_CAUBR) {
      return Constants.PREFIXO_CAUBR;
    } else {
      return cauUF.prefixo;
    }
  }

  /**
   * Metodo que requisita a lista de membros das comissões agrupado por CAU/UF
   */
  private setMembrosComissao(data: any): void {


    _.forIn(data, (value, key) => {
      
      this.numeroMembroComimssao = value.length;
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
   * Recupera as pendencias se exitir atraves do cadastro do profissional no SICCAU
   * @param data 
   */
  public getPendencias(data: any): Array<any> {
    let pendencias = [];
    if (!_.isEmpty(data)) {
      if (!data.adimplente) {
        pendencias.push('MSG_PENDENCIA_ANUNIADADE_PROFISSIONAL');
      }
      if (data.tempoRegistroAtivo < 2 || data.situacao_registro.descricao !== Constants.REGISTRO_ATIVO) {
        pendencias.push('MSG_PENDENCIA_REGISTRO_PROFISSIONAL');
      }
      if (data.infracaoEtica) {
        pendencias.push('MSG_PENDENCIA_ETICO_DISCIPLINAR_PROFISSIONAL');
      }
      if (data.infracaoRelacionadaExercicioProfissao) {
        pendencias.push('MSG_PENDENCIA_INFRACAO_PROFISSIONAL');
      }
    }
    return pendencias;
  }

  /**
   * Busca as informações de um profissional especifico e exibe em uma modal
   * @param membro 
   * @param template 
   */
  public detalhesMembro(membro: any, template: TemplateRef<any>): void {
    this.comissaoEleitoralService.getMembroComissao(membro.id).subscribe(data => {
      this.userDetails = data;
      this.userDetails.pendencias = this.getPendencias(data.profissional);
      this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }))
    }, error => {
      this.messageService.addMsgDanger(error.message);
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
      membroComissao.membroSubstituto.profissionalEntity = {};
    }
  }

}

