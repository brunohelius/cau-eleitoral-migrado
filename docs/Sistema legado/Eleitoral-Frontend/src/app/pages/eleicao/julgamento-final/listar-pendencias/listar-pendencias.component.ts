import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";

/**
 * Componente responsável pela apresentação de listagem de Chapas pela UF Selecionada.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'listar-pendencias',
    templateUrl: './listar-pendencias.component.html',
    styleUrls: ['./listar-pendencias.component.scss']
})

export class ListarPendencias implements OnInit {

    public dadosPendencias: any;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
    ) {
      this.dadosPendencias = route.snapshot.data.solicitacoesChapa
    }

    ngOnInit() {
      this.getTituloPagina();
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
}