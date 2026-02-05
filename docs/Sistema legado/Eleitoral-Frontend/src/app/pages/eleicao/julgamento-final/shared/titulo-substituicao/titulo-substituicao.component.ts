import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Constants } from 'src/app/constants.service';

/**
 * Componente responsável pela apresentação de solicitações realizadas na chapa.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'titulo-substituicao',
    templateUrl: './titulo-substituicao.component.html',
    styleUrls: ['./titulo-substituicao.component.scss']
})

export class TituloSubstituicaoComponent implements OnInit {

  @Input() public isIndeferido: boolean;
  @Input() public titleTab: string;
  @Input() public label?: string;
  @Input() public isMostraLabel?: boolean;
  @Input() public isMostraIcone?: boolean;

  constructor(){}

  ngOnInit(){
  }
}