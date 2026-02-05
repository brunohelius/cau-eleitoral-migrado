import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
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

    public idUf: number;
    public dadosPendencias: any;
    public idCalendario: number;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
    ) {
      this.dadosPendencias = route.snapshot.data.solicitacoesChapa
      this.idUf = route.snapshot.params.uf;
      this.idCalendario = route.snapshot.params.idCalendario;
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
    
    /**
     * Método que retorna para a tela de dados de pendências das chapas
     */
    public voltar(): void {
      this.idUf == 165 ? this.idUf = 0: this.idUf
      this.router.navigate([
        `/eleicao/julgamento-final/acompanhar-uf/${this.idUf}`, 
        {
          isIES: this.idUf == Constants.ID_IES
        }
      ]);
    }

    /**
    * retorna a label Pedidos de Denuncia
    */
    public getTituloAbaAcompanharChapa(): string {
      return this.messageService.getDescription('LABEL_ABA_CHAPA_PENDENCIA', 
        ['<div>', '</div><div>', '</div>']);
    }

    /**
     * Redireciona para a tela inicial do sistema
     */
    public redirecionaPaginaInicial(): void {
      this.router.navigate([`/`]);
    }
}