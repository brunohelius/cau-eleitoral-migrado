import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Constants } from 'src/app/constants.service';

/**
 * Componente responsável pela apresentação de solicitações realizadas na chapa.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'table-retificacao',
    templateUrl: './table-retificacao.component.html',
    styleUrls: ['./table-retificacao.component.scss']
})

export class TableRetificacaoComponent implements OnInit {

  @Input() public dados: any = {};
  @Input() public isRecurso?: boolean;
  @Output() retificacaoAtual: EventEmitter<any> = new EventEmitter();

  constructor(){}

  ngOnInit(){
  }

  public acionaVisualizar(param): void {
    this.retificacaoAtual.emit(param);
  }

  
}