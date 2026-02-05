import * as _ from "lodash";
import { Component, OnInit, Input } from '@angular/core';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';

import { MessageService } from '@cau/message';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { PrimeiraInstanciaComponent } from '../../julgamento/primeira-instancia/primeira-instancia.component';
import { SegundaInstanciaComponent } from '../../julgamento/segunda-instancia/segunda-instancia.component';

@Component({
  selector: 'list-julgamentos-retificados',
  templateUrl: './list-julgamentos-retificados.component.html',
  styleUrls: ['./list-julgamentos-retificados.component.scss']
})
export class ListJulgamentosRetificadosComponent implements OnInit {

  @Input() primeiraInstancia?;
  @Input() segundaInstancia?;
  @Input() julgamentosRetificados;
  @Input('dadosDenuncia') denuncia;

  public limitePaginacao: number = 10;
  public modalVisualizarPrimeiraInstancia: BsModalRef;
  public modalVisualizarSegundaInstancia: BsModalRef;

  constructor(
    private modalService: BsModalService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) { }

  ngOnInit() {
  }

  /**
   * Busca a retificação e aciona o modal de visualização da retificação do julgamento da denuncia.
   */
  public visualizarRetificacaoDenuncia = (idRetificacaoJulgamento) => {
    this.denunciaService.getRetificacaoJulgamentoDenuncia(idRetificacaoJulgamento).subscribe((data) => {
      let denunciaJulgamentoAtualizado = _.cloneDeep(this.denuncia);
      denunciaJulgamentoAtualizado.julgamento_denuncia = data;

      this.abrirModalVisualizarRetificacaoDenuncia(denunciaJulgamentoAtualizado);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Busca a retificação e aciona o modal de visualização da retificação do julgamento da denuncia.
   */
  public visualizarRetificacaoRecurso = (idRetificacaoJulgamento) => {
    this.denunciaService.getRetificacaoJulgamentoRecurso(idRetificacaoJulgamento).subscribe((data) => {
      let denunciaJulgamentoAtualizado = _.cloneDeep(this.denuncia);
      
      if (denunciaJulgamentoAtualizado.recursoDenunciado) {
        denunciaJulgamentoAtualizado.recursoDenunciado.julgamentoRecurso = data;
      }
      
      if (denunciaJulgamentoAtualizado.recursoDenunciante) {
        denunciaJulgamentoAtualizado.recursoDenunciante.julgamentoRecurso = data;
      }

      this.abrirModalVisualizarRetificacaoRecurso(denunciaJulgamentoAtualizado);
    }, error => {
      this.messageService.addMsgDanger(error);
    });
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalVisualizarRetificacaoDenuncia(denuncia: any): void {
    const initialState = {
      dadosDenuncia: denuncia,
      visualizacaoRetificacao: true
    };

    this.modalVisualizarPrimeiraInstancia = this.modalService.show(PrimeiraInstanciaComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));

    this.modalVisualizarPrimeiraInstancia.content["fecharModalRetificacaoEvent"].subscribe(event => {
      if (event) {
        this.modalVisualizarPrimeiraInstancia.hide();
      }
    });
  }

  /**
   * Abre o formulário de análise de defesa.
   */
  public abrirModalVisualizarRetificacaoRecurso(denuncia: any): void {
    const initialState = {
      dadosDenuncia: denuncia,
      visualizacaoRetificacao: true
    };

    this.modalVisualizarSegundaInstancia = this.modalService.show(SegundaInstanciaComponent,
      Object.assign({}, {}, { class: 'modal-xl', initialState }));

    this.modalVisualizarSegundaInstancia.content["fecharModalRetificacaoEvent"].subscribe(event => {
      if (event) {
        this.modalVisualizarSegundaInstancia.hide();
      }
    });
  }
}
