import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { formatDate } from "@angular/common";
import { SecurityService } from '@cau/security';
import { StatusDenuncia } from 'src/app/client/denuncia-client/denuncia-client.service';

/**
 * Componente responsável pela apresentação de Denuncias.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-lista-denuncias-admissibilidade',
    templateUrl: './aba-lista-denuncias-admissibilidade.component.html',
    styleUrls: ['./aba-lista-denuncias-admissibilidade.component.scss']
})

export class AbaListaDenunciasAdmissibilidadeComponent implements OnInit {

    public usuario;
    public denunciasDetalhadas = [];
    public limitePaginacao: number;
    public limitesPaginacao = [];
    public search: string;
    public _denunciasDetalhadas = null
    public dadosSubstituicao: any;

    @Input('denunciasRecebidas') denunciasRec: any[];

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService

    ) {
        
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
        }

        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 25, 50];

        this.denunciasDetalhadas = this.denunciasRec;
        if(!this.denunciasDetalhadas) {
            this.denunciasDetalhadas = this.route.snapshot.data["detalhamentoDenunciaCauUfResolve"];
        }

        this._denunciasDetalhadas = this.denunciasDetalhadas;
    }

    /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this._denunciasDetalhadas.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.denunciasDetalhadas = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem.
     *
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(formatDate(obj.dt_denuncia, 'dd/MM/yyyy mm:ss', 'en-US'));
        values.push(obj.numero_denuncia);
        values.push(obj.nome_denunciante);
        values.push(obj.nome_denunciado);
        values.push(obj.ds_situacao);

        return values;
    }

    /**
     * Recupera o Status da Denuncia de acordo com o ID informado.
     *
     * @param idStatus
     */
    public getStatusDenuncia(idStatus: any): StatusDenuncia {
        return StatusDenuncia.findById(idStatus);
    }

    /**
   * Rota para a Tela de visualizar os dados de Denuncia por Cau UF.
   *
   * @param idCauUf
   */
    public visualizar(denuncia: any) {
        this.router.navigate(['denuncia/comissao', 'visualizar', denuncia.id_denuncia, 'tipoDenuncia', denuncia.id_tipo_denuncia]);
    }

    /**
     * Volta para a Tela anterior
     */
    public voltar() {
        window.history.back();
    }
}