import * as _ from 'lodash';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from "@angular/router";
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
import { ModalPlataformaPropagandaComponent } from './modal-plataforma-propaganda/modal-plataforma-propaganda.component';
import { BsModalService } from 'ngx-bootstrap';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';


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

  @Input() membros: any = [];
  @Input() numeroChapa?: number;
  @Input() situacaoChapa?: number;
  @Input() tipoProfissional: any;

  @Output() voltarAba: EventEmitter<any> = new EventEmitter();

  public conselheiros: any;
  public membrosChapas: any;

  public infoPlataformaPropaganda: any;

  public membrosChapasOld: any = [];

  constructor(
    private route: ActivatedRoute,
    private modalService: BsModalService,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ){}

  ngOnInit() {
    this.getTituloPagina();
    this.inicializarMembrosChapa()
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
  * retona o título do módulo de julgamento final
  */
  public getTituloPagina(): void {
    this.layoutsService.onLoadTitle.emit({
        icon: 'fa fa-wpforms',
        description: this.messageService.getDescription('TITLE_JULGAMENTO')
    });
  }

  /**
   * Responsavel por voltar a aba para a principal.
   */
 public voltarAbaPrincipal(): any {
   this.voltarAba.emit();
}

  /**
  * Inicializa os membros chapa.
  */
  private inicializarMembrosChapa(): void {
     let membrosChapas = [];
    if (this.membros.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR) {

      let totalMembrosChapa = this.membros.numeroProporcaoConselheiros;
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

    let membroChapa = this.membros.membrosChapa.find(membro => {

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

  public isMostrarBotaoPlataforma(): boolean {
    return (
      this.tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO ||
      this.tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO_CE
    );
  }
      

  public abrirModalInforPlataforma(): void {
    if (this.infoPlataformaPropaganda) {
      this.showModalInfoPlataforma(this.infoPlataformaPropaganda);
    } else {
      this.julgamentoFinalClientService.getInfoPlataformaPropagandaChapa(this.membros.id).subscribe(
        data => {
          this.infoPlataformaPropaganda = data;
          this.showModalInfoPlataforma(this.infoPlataformaPropaganda);
        }
      );
    }
  }

  public showModalInfoPlataforma(chapaEleicao): void {
    const initialState = {chapaEleicao};

    const modal = this.modalService.show(ModalPlataformaPropagandaComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));
  }
}
