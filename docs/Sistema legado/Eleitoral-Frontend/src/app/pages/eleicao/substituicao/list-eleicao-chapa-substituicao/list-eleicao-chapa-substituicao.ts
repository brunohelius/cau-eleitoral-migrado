import * as _ from "lodash";
import { ActivatedRoute } from "@angular/router";
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter } from '@angular/core';
import { CalendarioClientService } from '../../../../client/calendario-client/calendario-client.service';
import { Constants } from 'src/app/constants.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-eleicao-chapa-substituicao',
    templateUrl: './list-eleicao-chapa-substituicao.component.html',
    styleUrls: ['./list-eleicao-chapa-substituicao.component.scss']
})
export class ListEleicaoChapaSubstituicaoComponent implements OnInit {
    public filtro: any;
    public eleicoes: Array<any>;
    public eleicoesAuxiliar: Array<any>;
    public numeroRegistrosPaginacao: number;
    public search: string;
    public selectAnos: any;

    public anosEleicoesChapaDropdownData: any;
    public eleicoesChapaDropdownData: any;
    public tipoProcessoEleicaoDropdownData: any;
    public eleicaoDropdownDataAux: any;
    public disabledEleicaoDropdown: boolean;

    public dropdownSettings;
    public dropdownSettingsCalendario;
    public dropdownSettingsProcesso;

    public showMessageFilter: boolean;

    /**
     * Construtor da classe.
     *
     * @param messageService
     * @param calendarioClientService
     * @param modalService
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private calendarioClientService: CalendarioClientService,
        private layoutsService: LayoutsService,
    ) {
        this.eleicoes = _.orderBy(route.snapshot.data["eleicoes"], ['eleicao'], ['asc']);
        this.anosEleicoesChapaDropdownData = route.snapshot.data["eleicoesAno"];
        this.eleicoesAuxiliar = this.eleicoes;
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        
        /**
         * Define ícone e título do header da página 
         */
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('Pedido de Substituição')
        });



        this.numeroRegistrosPaginacao = 10;
        this.showMessageFilter = false;
        this.filtro = {
            anos: [],
            idsCalendariosEleicao: [],
            idTipoProcesso: '',
            situacoes: Constants.CALENDARIO_SITUACAO_CONCLUIDO
        };

        this.dropdownSettings = {
            singleSelection: false,
            idField: 'ano',
            textField: 'ano',
            selectAllText: 'Selecione Todos',
            unSelectAllText: 'Remove Todos',
            itemsShowLimit: 5,
            allowSearchFilter: false,
            searchPlaceholderText: 'Buscar',
            defaultOpen: false,
            noDataAvailablePlaceholderText: ''
        };

        this.dropdownSettingsCalendario = {
            singleSelection: false,
            idField: 'id',
            textField: 'descricao',
            selectAllText: 'Selecione Todos',
            unSelectAllText: 'Remove Todos',
            itemsShowLimit: 8,
            allowSearchFilter: false,
            searchPlaceholderText: 'Buscar',
            defaultOpen: false,
            noDataAvailablePlaceholderText: ''
        };

        this.dropdownSettingsProcesso = {
            singleSelection: true,
            idField: 'id',
            textField: 'descricao',
            allowSearchFilter: false,
            defaultOpen: false,
            noDataAvailablePlaceholderText: 'Aguarde...'
        };
        this.getAnosAndEleicoesConcluida();
        this.getTipoProcessoEleicao();
        this.disabledEleicaoDropdown = true;
    }

    public pesquisar() {
        this.getEleicoesConcluidasPorFiltro();
        this.showMessageFilter = false;
    }

    public getUrlVisualizar(id): Array<any> {
        return [`/eleicao/acompanhar-substituicao-ufs/${id}`];
    }

    /**
     * Apresenta MSG de tabela vazia.
     */
    public isShowMsgTabelaVazia(): boolean {
        return this.showMessageFilter && this.eleicoes.length == 0;
    }

    /**
     * Listagem de eleições concluídas por filtro de pesquisa.
     */
    public getEleicoesConcluidasPorFiltro() {
        this.filtro.listaChapas = true;
        this.calendarioClientService.getCalendariosPorFiltro(this.filtro).subscribe(data => {
            this.eleicoes = _.orderBy(data, ['eleicao'], ['asc']);
            this.eleicoesAuxiliar = this.eleicoes;
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this.eleicoesAuxiliar.filter((data) => JSON.stringify(data).toLowerCase().indexOf(search.toLowerCase()) !== -1);
        this.eleicoes = filterItens;
    }

    /**
     * Retorna a quantidade total de números de registros de eleições concluídas
     * @return number
     */
    public getQtdRegistroEleicoes(): number {
        return this.eleicoes.length;
    }

    public getAnosAndEleicoesConcluida() {
        this.eleicaoDropdownDataAux = _.map(this.eleicoes, item => {
            return { id: item.id, eleicao: item.eleicao, ano: item.ano };
        });
    }

    /**
    * Busca Lista calendarios Tipo do Processo
    */
    public getTipoProcessoEleicao(): void {
        this.calendarioClientService.getTipoProcesso().subscribe(data => {
            this.tipoProcessoEleicaoDropdownData = data;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
    * Recupera o arquivo conforme a id da entidade 'resolucao' informada.
    *
    * @param event
    * @param idRegimentoEstatuto
    */
    public downloadResolucao(event: EventEmitter<any>, idResolucao: any): void {
        this.calendarioClientService.downloadArquivo(idResolucao).subscribe((data: Blob) => {
            event.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
    * Limpa os dados do formulário.
    *
    * @param event
    */
    public limpar(event: any) {
        event.preventDefault();
        this.filtro.anos = [];
        this.filtro.idsCalendariosEleicao = [];
        this.filtro.idTipoProcesso = '';
        this.search = "";
        this.eleicoes = [];
        this.eleicoesAuxiliar = [];
        this.showMessageFilter = true;
        /**Limpando e desabilitando campo de eleição, pois ele depende de ano eleição. */
        this.eleicoesChapaDropdownData = [];
        this.disabledEleicaoDropdown = true;
    }

    /**
    * Preencher eleições pelo filtro de ano.
    * de pesquisa
    * @param event
    */
    public getEleicaoPorFiltroAno() {
        let years: Array<any> = this.filtro.anos;
        let eleicoesByYear = this.eleicoes.filter(calendario => years.some(year => calendario.eleicao.ano === year));
        this.eleicoesChapaDropdownData = eleicoesByYear.map(caledario => {
            return { descricao: caledario.eleicao.descricao, id: caledario.id };
        });
        this.disabledEleicaoDropdown = eleicoesByYear.length < 1;
    }

    /**
     * Método chamado ao selecionar a opção de "selecionar todos" no Dropdown de ano.
     * Preenche a variável de filtro ano e  habilita o dropdown de eleição concluída com as respectivas informações.
     */
    public onSelectAllYear() {
        this.filtro.anos = _.map(this.anosEleicoesChapaDropdownData, item => {
            return item.ano;
        });
        

        this.getEleicaoPorFiltroAno();
    }

    /**
     * Método chamado ao deselecionar a opção de "selecionar todos" no Dropdown de ano.
     * Limpa a variável de filtro de ano e desabilita o dropdown de eleição.
     */
    public onDeSelectAllYear() {
        this.filtro.anos = [];
        this.eleicoesChapaDropdownData = [];
        this.disabledEleicaoDropdown = true;
    }
}
