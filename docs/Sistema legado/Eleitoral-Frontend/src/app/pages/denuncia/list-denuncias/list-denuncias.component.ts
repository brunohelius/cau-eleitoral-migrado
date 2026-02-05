import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { formatDate } from "@angular/common";
import { SecurityService } from '@cau/security';
import { StatusDenuncia, DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { ModalGerarDocumentoComponent } from './modal-gerar-documento/modal-gerar-documento.component';

/**
 * Componente responsável pela apresentação de Denuncias.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-denuncias',
    templateUrl: './list-denuncias.component.html',
    styleUrls: ['./list-denuncias.component.scss']
})

export class ListDenunciasComponent implements OnInit {

    public usuario;
    public denunciasDetalhadas = [];
    public limitePaginacao: number;
    public limitesPaginacao = [];
    public search: string;
    public _denunciasDetalhadas = null
    public dadosSubstituicao: any;

    public modalGerarDocumento: BsModalRef;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private modalService: BsModalService,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private denunciaService: DenunciaClientService
    ) {
        this.denunciasDetalhadas = route.snapshot.data["detalhamentoDenunciaCauUfResolve"];
    }

    /**
     * Encaminha para a rota de visualizar
     */
    public visualizar(denuncia: any) {
        this.router.navigate(['denuncia', denuncia.id_denuncia, 'acompanhar']);
    }

    /**
     * Volta para a Tela anterior
     */
    public voltar() {
        window.history.back();
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
        this.limitesPaginacao = [10, 20, 50, 100];
        // this.usuario = this.securityService.credential["_user"];

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
     * Recupera o Status da Denuncia de acordo com o ID informado.
     *
     * @param idStatus
     */
    public getStatusDenuncia(idStatus: any): StatusDenuncia {
        return StatusDenuncia.findById(idStatus);
    }

    /**
     * Abre o formulário para geração do documento.
     */
    public abrirModalGerarDocumento(denuncia: any): void {
        const initialState = {
            denuncia
        };

        this.modalGerarDocumento = this.modalService.show(ModalGerarDocumentoComponent,
        Object.assign({}, {}, { class: 'modal-lg', initialState }));
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

        return values;
    }
}