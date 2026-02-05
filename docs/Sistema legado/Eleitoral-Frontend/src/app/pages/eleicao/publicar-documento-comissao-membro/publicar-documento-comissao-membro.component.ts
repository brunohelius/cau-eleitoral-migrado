import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

import * as _ from "lodash";
import { MessageService } from '@cau/message';
import { AtividadeSecundariaClientService } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { LayoutsService } from '@cau/layout';

@Component({
    selector: 'publicar-documento-comissao-membro',
    templateUrl: './publicar-documento-comissao-membro.component.html',
    styleUrls: ['./publicar-documento-comissao-membro.component.scss']
})
export class PublicarDocumentoComissaoMembroComponent implements OnInit {
    public publicacoes: Array<any>;
    public documento: any;
    private atividadeSecundaria: any;
    public numeroRegistrosPaginacao: number;
    public numeroMembrosComissao: Array<any>;

    /**
    * Construtor da classe.
    * 
    * @param route 
    * @param router 
    * @param messageService 
    * @param atividadeSecundariaService 
    * @param modalService 
    */
    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private atividadeSecundariaService: AtividadeSecundariaClientService,
        private calendarioService: CalendarioClientService,
    ) {

        this.atividadeSecundaria = route.snapshot.data['atividadeSecundaria'];
        this.inicializarTabelaNumeroMembrosComissao();
        this.publicacoes = [];
    }

    /**
     * Função inicializada quando o componente carregar.
     */
    ngOnInit() {

        /**
         * Define ícone e título do header da página 
         */
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('LABEL_PUBLICAR_COMISSÃO_ELEITORAL')
        });

        /** Verificando se a atividade secundária conte documento de comissão. */
        if (this.hasDocumento()) {
            this.documento = this.atividadeSecundaria.informacaoComissaoMembro.documentoComissaoMembro;
            this.numeroRegistrosPaginacao = 5;
        }
    }


    /**
     * Retorna a tabela de UFs segmentada em 4 colunas.
     * 
     * @param numeroSegmento 
     */
    public getUfsTabelaSegmento(numeroSegmento: number): any {
        if (this.numeroMembrosComissao != undefined && this.numeroMembrosComissao.length > 0) {
            let ufs = this.numeroMembrosComissao;
            let limiteSegmento = Math.ceil(ufs.length / 4);

            return ufs.filter((uf, index) => {
                return index < (limiteSegmento * numeroSegmento) && index >= (limiteSegmento * numeroSegmento) - limiteSegmento;
            });

        }
        return [];
    }

    /**
   * Inicializa a tabela com a quantidade de membros da comissão.
   */
    private inicializarTabelaNumeroMembrosComissao(): void {
        this.calendarioService.getAgrupamentoNumeroMembrosComissao(this.atividadeSecundaria.atividadePrincipalCalendario.calendario.id).subscribe(numeroMembros => {
            this.numeroMembrosComissao = numeroMembros;
        }, error => {
            this.messageService.addMsgDanger(error);
        });

    }

    /**
     * Verica se a informação da comissão eleitoral possui documento definido.
     * 
     */
    private hasDocumento(): boolean {
        try {
            return this.atividadeSecundaria.informacaoComissaoMembro.hasOwnProperty('documentoComissaoMembro');
        }
        catch (e) {
            return false;
        }
    }

}