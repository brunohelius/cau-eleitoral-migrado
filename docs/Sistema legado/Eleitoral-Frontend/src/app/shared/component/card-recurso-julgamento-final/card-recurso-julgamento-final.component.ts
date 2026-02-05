import { Component, OnInit, Input, TemplateRef, Output, EventEmitter } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { StringService } from 'src/app/string.service';
import * as _ from 'lodash';

@Component({
  selector: 'card-recurso-julgamento-final',
  templateUrl: './card-recurso-julgamento-final.component.html',
  styleUrls: ['./card-recurso-julgamento-final.component.scss']
})
export class CardRecursoJulgamentoFinalComponent implements OnInit {

  @Input() public temExcluir: any;
  @Input() public membrosSelecionados: any;

  public membroChapaSelecionado: any;

  public modalPendeciasMembro: BsModalRef;

  @Output() excluirMembro: EventEmitter<any> = new EventEmitter();

  constructor() {

  }

  ngOnInit() {
    this.membrosSelecionados = _.orderBy(this.membrosSelecionados,
      ['indicacaoJulgamentoFinal.numeroOrdem', 'indicacaoJulgamentoFinal.tipoParticipacaoChapa.id'], ['asc', 'asc']);
  }

  /**
   * Responsavel por excluir o membro.
   */
  public excluirMembroEmit(membro, indece): any {
    let event = {
      membro: membro,
      indice: indece
    }
    this.excluirMembro.emit(event);
  }

  /**
   * Retorna o registro com a mascara
   */
  public getRegistroComMask(str: string) {
    return StringService.maskRegistroProfissional(str);
  }
}
