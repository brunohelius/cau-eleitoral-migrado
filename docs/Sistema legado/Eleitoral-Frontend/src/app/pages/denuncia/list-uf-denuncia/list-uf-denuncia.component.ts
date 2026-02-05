
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { formatDate } from "@angular/common";
import { SecurityService } from '@cau/security';
/**
 * Componente responsável pela apresentação de Denuncias.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-uf-denuncia',
    templateUrl: './list-uf-denuncia.component.html',
    styleUrls: ['./list-uf-denuncia.component.scss']
})
export class ListUfDenunciaComponent implements OnInit {

    public usuario;
    public denunciasUF: any;
    public limitePaginacao: number;
    public limitesPaginacao = [];
    public search: string;
    public dadosSubstituicao: any;
    public idCalendario: number;

    constructor(private route: ActivatedRoute, private router: Router, private layoutsService: LayoutsService, private messageService: MessageService, private securityService: SecurityService) {
        this.denunciasUF = route.snapshot.data["agrupamentoUfAtividadeSecundariaResolve"];
        this.idCalendario = route.snapshot.params.idCalendario;
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
        /**
        * Define ícone e título do header da página
        */
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-user',
            description: this.messageService.getDescription('Denuncia')
        });
        // recebe o resolve com os dados passados como parametro
        this.dadosSubstituicao = {
            uf: {
                id: 140,
                descricao: 'AC'
            }
        };
        this.limitePaginacao = 50;
        this.limitesPaginacao = [10, 20, 50, 100];
    }

    /**
     * Rota para a Tela de visualizar os dados de Denuncia por Cau UF.
     *
     * @param idCauUf
     */
    public visualizar(idCauUf: any) {
        this.router.navigate(['denuncia', 'calendario', this.idCalendario, 'cauUf', idCauUf, 'listar']);
    }

    /**
     * Volta para a Tela anterior
     */
    public voltar() {
        window.history.back();
    }
}
