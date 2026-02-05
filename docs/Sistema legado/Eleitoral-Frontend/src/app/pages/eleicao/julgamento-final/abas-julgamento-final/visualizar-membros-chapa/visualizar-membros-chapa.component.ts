import * as _ from 'lodash';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from "@angular/router";
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, EventEmitter, ViewChild, TemplateRef, Output } from '@angular/core';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'visualizar-membros-chapa',
    templateUrl: './visualizar-membros-chapa.component.html',
    styleUrls: ['./visualizar-membros-chapa.component.scss']
})
export class VisualizarMembrosChapaComponent implements OnInit {

  public acoes: any = {}
  public conselheiros: any;
  public membrosChapas: any;
  public acaoJulgamento: number;
  public msgAcaoFormularioJulgamentoPrimeiraInstancia: string;

  public formJulgamentoDeferido: BsModalRef;
  public formJulgamentoIndeferido: BsModalRef;

  @Input() chapa: any = [];
  @Input() numeroChapa?: number;
  @Input() situacaoChapa?: number;
  @Input() membrosPorSituacao: any;
  @Output() voltarAba :EventEmitter<any> = new EventEmitter();
  @Output() redirecionarVisualizarJulgamento: EventEmitter<any> = new EventEmitter();

  @ViewChild('templateJulgamento', { static: true }) templateJulgamento: TemplateRef<any>;

  constructor(
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
  ){}

  ngOnInit() {
    this.getTituloPagina();
    this.inicializarMembrosChapa();
    this.inicializaAcoes();
  }


  /**
   * inicializa os tipos de ações para julgamento
   */
  public inicializaAcoes(): void {
    this.acoes = {
      julgarDeferido: Constants.ID_STATUS_JULGAMENTO_FINAL_DEFERIDO,
      julgarIndeferido: Constants.ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO
    }
  }

  /**
  * retona o título do módulo de julgamento final
  */
  public getTituloPagina(): void {
    this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-wpforms',
        description: this.messageService.getDescription('TITLE_JULGAMENTO')
    });
  }

  /* Retorna lista de Conselheiros federais da chapa eleitoral.
  * @param membrosChapas
  */
  public initConselheirosFederais(membrosChapas: Array<any>):void {
    this.conselheiros = membrosChapas[0];
  }

  /* Retorna lista de Conselheiros estaduais da chapa eleitoral.
  * @param membrosChapas
  */
  public initConselheirosEstaduais(membrosChapas: Array<any>):void {
    this.membrosChapas = membrosChapas.slice(1);
  }

   /**
   * Verifica se a chapa é do tipo IES.
   */
  public isChapaIES(): boolean {
    return this.chapa.tipoCandidatura.id === Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
  * Inicializa os membros chapa.
  */
  private inicializarMembrosChapa(): void {
     let membrosChapas = [];
    if (this.chapa.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR) {

      let totalMembrosChapa = this.chapa.numeroProporcaoConselheiros;
      for (var i = 0; i <= totalMembrosChapa; i++) {
        let titular: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, i);
        let suplente: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, i);

        membrosChapas.push({
          titular: titular,
          suplente: suplente
        });
      }

      this.initConselheirosEstaduais(membrosChapas);

    } else {

      let titular: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, 0);
      let suplente: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, 0);

      membrosChapas = [
        {
          titular: this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, 0),
          suplente: this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, 0)
        }
      ];
    }

    this.initConselheirosFederais(membrosChapas);
  }

  /**
   * Retorna membro da chapa por posição tipo Participação.
   */
  private findMembroChapa(tipoParticipacao: number, posicao: number): any {

    let membroChapa = this.chapa.membrosChapa.find(membro => {

      if (posicao == 0) {
        return membro.tipoParticipacaoChapa.id == tipoParticipacao &&
         (membro.tipoMembroChapa.id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL
          || membro.tipoMembroChapa.id == Constants.TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES);
      }
      return membro.tipoParticipacaoChapa.id == tipoParticipacao && membro.numeroOrdem == posicao;

    });

    if (membroChapa == undefined) {
      membroChapa = {
        numeroOrdem: posicao,
        tipoParticipacaoChapa: { id: tipoParticipacao, descricao: this.getDescricaoTipoParticipacao(tipoParticipacao) },
        tipoMembroChapa: { id: posicao == 0 ? Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL : Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL },
      };
    }

    return membroChapa;
  }

  /**
   * Retorna a descrição do tipo de participação do membro da chapa
   * @param idTipoParticipacao
   */
  private getDescricaoTipoParticipacao(idTipoParticipacao: number): any {
    return (
      idTipoParticipacao == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR
      ? this.messageService.getDescription('LABEL_TITULAR')
      : this.messageService.getDescription('LABEL_SUPLENTE')
    )
  }

  /**
   * Verifica se a chapa possui pendências
   */
  public isChapaPendente():boolean {
    return this.situacaoChapa == Constants.ID_STATUS_CHAPA_PENDENTE;
  }

  /**
   * Método que apresenta popup de jultamento na tela
   */
  public abrirFormJulgamento(acao: number): void {

    this.acaoJulgamento = acao;
    this.formJulgamentoDeferido = this.modalService.show(
        this.templateJulgamento ,
        Object.assign(
            {
              ignoreBackdropClick: true
            },
            {
              class: 'modal-xl'
            }
        )
    );
  }

  /**
   * Método responsável por fechar o modal de cadastro de substituição
   * @param evento
   */
  public fecharformJulgamentoDeferido(): void {
    this.formJulgamentoDeferido.hide()
  }

  /**
   * Verifica o período da atividade 5.1 de julgamento de primeria instância
   * @param acoes
   */
  public validaPeriodoAtividadeJulgamentoPrimeiraInstancia(acao: number): void {

    this.acaoJulgamento = acao;

    if(this.chapa.isIniciadoAtivJulgFinal && !this.chapa.isFinalizadoAtivJulgFinal){
      this.abrirFormJulgamento(acao);
    }
    else if(this.chapa.isFinalizadoAtivJulgFinal) {

      this.messageService.addConfirmYesNo('MSG_JULGAMENTO_FINAL_DEPOIS_DATA_FIM', () => {
        this.abrirFormJulgamento(this.acaoJulgamento);
      });

    } else {

      this.messageService.addConfirmYesNo('MSG_JULGAMENTO_FINAL_ANTES_DATA_INICIO', () => {
        this.abrirFormJulgamento(this.acaoJulgamento);
      });
    }
  }

  /**
   * Método responsável por redirecionar a página para visualização do julgamento
   * @param event
   */
  public redirecionarVisualizarSubstituicao(event: any): void {
    this.redirecionarVisualizarJulgamento.emit(event);
  }

  /**
   * Volta para uma determinada aba do módulo julgamento final
   */
  public voltar(){
    this.voltarAba.emit(Constants.ABA_ACOMPANHAR_CHAPA);
  }
}
