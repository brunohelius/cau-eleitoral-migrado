import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit, Input, TemplateRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';


@Component({
  selector: 'aba-visualizar-alegacao',
  templateUrl: './aba-visualizar-alegacao.component.html',
  styleUrls: ['./aba-visualizar-alegacao.component.scss']
})
export class AbaVisualizarAlegacaoComponent implements OnInit {

  @Input() alegacoes: any;
  @Input() impugnacao: any;
  @Input() validacaoAlegacaoData: any;

  /**
   * Construtor da classe.
   */
  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private securtyService: SecurityService,
    private impugnacaoService: ImpugnacaoResultadoClientService,
    private modalService: BsModalService,
    private securityService: SecurityService
  ) {

  }

  /**
   * Quando o componente inicializar.
   */
  ngOnInit() {

  }

}
