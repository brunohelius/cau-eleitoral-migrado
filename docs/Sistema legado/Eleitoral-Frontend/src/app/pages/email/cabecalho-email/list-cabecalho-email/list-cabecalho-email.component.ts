import { Component, OnInit, TemplateRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import * as _ from "lodash";

import { MessageService } from '@cau/message';
import { CabecalhoEmailClientService } from 'src/app/client/cabecalho-email/cabecalho-email-client.service';
import { AcaoSistema } from 'src/app/app.acao';

/**
 * Componente de Listagem de Cadastro de Cabeçalho de E-mail.
 *
 * @author Squadra Tecnologia
 */
@Component({
    selector: 'list-cabecalho-email',
    templateUrl: './list-cabecalho-email.component.html'
})
export class ListCabecalhoEmailComponent implements OnInit {
    public search: any;
    public filtro: any;
    public ufs: Array<any>;
    public titulos: Array<any>;
    public cabecalhos: Array<any>;
    public dropdownSettingsUF: any;
    public showMessageFilter: boolean;
    public dropdownSettingsAtivo: any;
    public dropdownSettingsTitulo: any;
    public cabecalhosAuxiliar: Array<any>;
    public numeroRegistrosPaginacao: number;

    private acaoSistema: AcaoSistema;

    /**
     * Construtor da classe.
     * 
     * @param route 
     * @param messageService 
     * @param cabecalhoEmailService 
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private cabecalhoEmailService: CabecalhoEmailClientService,
    ) {
        this.ufs = this.route.snapshot.data['ufs'];        
        this.cabecalhos = this.route.snapshot.data['cabecalhosEmail'];
        this.cabecalhosAuxiliar = this.getCabecalhoAuxiliar();
        this.acaoSistema = new AcaoSistema(route);
    }

    /**
     * Função inicializada quando o componente carregar.
     */
    ngOnInit() {
        this.numeroRegistrosPaginacao = 10;
        this.showMessageFilter = false;
        this.inicializarFiltro();
        this.inicializarDropdownSettings();
        this.titulos = this.getTitulosCabecalhos();
        this.agruparUfs();
    }

    /**
     * Pesquisar Cabeçalhos de E-mail por Filtro.
     */
    public pesquisar(){
        this.getCabecalhosPorFiltro();
    }

    /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this.cabecalhosAuxiliar.filter((data) => JSON.stringify(data).toLowerCase().indexOf(search.toLowerCase()) !== -1);
        this.cabecalhos = filterItens;
    }

    /**
    * Limpa os dados do formulário.
    * 
    * @param event 
    */
   public limpar(event: any) {
       this.filtro.ufs = [];
       this.showMessageFilter = true;
       this.filtro.idsCabecalhosEmail = [];
       this.filtro.ativo = [];
       this.cabecalhos = [];
       event.preventDefault();
   }


   /**
    * Retorna Array para preenchimento de dropdown de Títulos de Cabeçalhos de E-mail.
    */
    public getTitulosCabecalhos(){
        return _.map( _.orderBy(this.cabecalhosAuxiliar, ['titulo'], ['asc']), item => {
            return { id: item.id, titulo: item.titulo};
        });
    }

    /**
     * Retorna Array para preenchimento de dropdown de Ativo de Cabeçalhos de E-mail.
     */
    public getTiposAtivo(){
        return [
            { label: this.messageService.getDescription('LABEL_SIM'), valor: true},
            { label: this.messageService.getDescription('LABEL_NAO'), valor: false }
        ];
    }

    /**
     * Retorna descrição do campo Ativo
     * @param ativo
     */
    public getDescricaoAtivo(ativo: boolean): string{
        return ativo ? this.messageService.getDescription('LABEL_SIM') : this.messageService.getDescription('LABEL_NAO');
    }

    /**
     * Retorna String de agrupamento de UFs.
     * @param ufs
     */
    public getDescricaoUfs(cabecalhosUf: Array<any>){
        if(cabecalhosUf.length >= 27){
            return this.messageService.getDescription('LABEL_TODOS')
        } else{
            let descricao: string = "";
            cabecalhosUf.forEach(cabecalhoUf => {
                descricao = descricao + cabecalhoUf.uf.sgUf + ', ';
            });
            return descricao.slice(0, -2);
        }
    }

    /**
     * Retorna descrição de ID formatado
     * @param id
     */
    public getDescricaoId(id: number){
        let descricao: string = id.toString();
        if(id < 999){
            descricao = (("000"+(id)).slice(-4))
        }
        return descricao;
    }

    /**
     * Retorna URL de página de alteração de Cabeçalho.
     * @param idCabecalho
     */
    public getURLCabecalhoAlterar(idCabecalho: number){
        return `/email/cabecalho/${idCabecalho}/alterar`;
    }

    /**
     * Retorna URL de página de visualização de Cabeçalho.
     * @param idCabecalho
     */
    public getURLCabecalhoVisualizar(idCabecalho: number){
        return `/email/cabecalho/${idCabecalho}/visualizar`;
    }

    /**
     * Retorna URL de página de inclusão de Cabeçalho.
     */
    public getURLCabecalhoIncluir(){
        return `/email/cabecalho/incluir`;
    }

    /**
     * Verificar exibição da paginação.
     */
    public isExibirPaginacao(){
        return this.cabecalhos.length > 0;
    }

    /**
     * Verificar exibição da mensagem de nenhum registro encontrado.
     */
    public isExibirMsgNenhumRegistroEncontrado(){
        return !this.isExibirPaginacao();
    }

    /**
     * Verificar exibição da mensagem de filtro vazio.
     */
    public isExibirMSGTableFiltroVazio(){
        return this.showMessageFilter && this.cabecalhos.length == 0;
    }

    /**
     * Retorna o total de cabeçalhos.
     */
    public getTotalRegistrosCabacalho(){
        return this.cabecalhos.length;
    }

    /**
     * Aciona serviço de Cabeçalho de E-mail para obtenção de Cabeçalhos.
     */
    private getCabecalhosPorFiltro(){
        let filtro = this.getDataFiltro();
        this.cabecalhoEmailService.getPorFiltro(filtro).subscribe(data => {
            this.cabecalhos = _.orderBy(data, ['id'], ['desc']);
            this.showMessageFilter = false;
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Retorna dados do filtro.
     */
    private getDataFiltro(){
        return {
            ufs: this.filtro.ufs.map(uf => { return uf.id}),
            idsCabecalhosEmail: this.filtro.idsCabecalhosEmail.map(cabecalho => { return cabecalho.id}),
            ativo: this.filtro.ativo.length > 0 ? this.filtro.ativo[0].valor : null,
        };
    }
    /**
     * Retorna dados de Cabeçaria.
     */
    private getCabecalhoAuxiliar(): Array<any>{
        return this.cabecalhos.map(cabecalho => {
            cabecalho.descricaoAtivo = this.getDescricaoAtivo(cabecalho.ativo);
            cabecalho.descricaoUfs = this.getDescricaoUfs(cabecalho.cabecalhoEmailUfs);
            cabecalho.descricaoId = this.getDescricaoId(cabecalho.id);
            return cabecalho;
        });
    }

    /**
     * Inicializa objeto de filtro de Cabeçalho.
     */
    private inicializarFiltro(){
        this.filtro = {
            ufs: [],
            idsCabecalhosEmail: [],
            ativo: []
        };
    }

    /**
     * Inicializa objeto de configuração de dropdown.
     */
    private inicializarDropdownSettings(){
        this.dropdownSettingsUF = {
            singleSelection: false,
            idField: 'id',
            textField: 'sgUf',
            selectAllText: this.messageService.getDescription('LABEL_SELECIONE_TODOS'),
            unSelectAllText: this.messageService.getDescription('LABEL_REMOVER_TODOS'),
            itemsShowLimit: 5,
            allowSearchFilter: false,
            searchPlaceholderText: this.messageService.getDescription('LABEL_BUSCAR'),
            defaultOpen: false,
            noDataAvailablePlaceholderText: ''
        };

        this.dropdownSettingsTitulo = {
            singleSelection: false,
            idField: 'id',
            textField: 'titulo',
            selectAllText: this.messageService.getDescription('LABEL_SELECIONE_TODOS'),
            unSelectAllText: this.messageService.getDescription('LABEL_REMOVER_TODOS'),
            itemsShowLimit: 5,
            allowSearchFilter: false,
            searchPlaceholderText: this.messageService.getDescription('LABEL_BUSCAR'),
            defaultOpen: false,
            noDataAvailablePlaceholderText: ''
        };

        this.dropdownSettingsAtivo = {
            singleSelection: true,
            idField: 'valor',
            textField: 'label',
            allowSearchFilter: false,
            defaultOpen: false,
            noDataAvailablePlaceholderText: this.messageService.getDescription('LABEL_ARGUARDE')
        };
    }

    /**
     * Agrupa Array de UFS para utilizar ordenação.
     */
    private agruparUfs(): void {
        this.cabecalhos.map((cabecalho) => {
            cabecalho.ufsAgrupados = this.getDescricaoUfs(cabecalho.cabecalhoEmailUfs);
        });
    }
}
