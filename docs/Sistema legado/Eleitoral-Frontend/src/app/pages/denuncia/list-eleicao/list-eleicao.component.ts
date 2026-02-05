import * as _ from "lodash";
import { ActivatedRoute } from "@angular/router";
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter } from '@angular/core';
import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { Constants } from 'src/app/constants.service';
import { LayoutsService } from '@cau/layout';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-eleicao',
    templateUrl: './list-eleicao.component.html',
    styleUrls: ['./list-eleicao.component.scss']
})
export class ListEleicaoComponent implements OnInit {
    public filtro: any;
    public eleicoes: Array<any>;
    public eleicoesAuxiliar: Array<any>;
    public numeroRegistrosPaginacao: number;
    public search: string;
    public selectAnos: any;

    public anosEleicoesConcluidasDropdownData: any;
    public eleicoesConluidasDropdownData: any;
    public tipoProcessoEleicaoDropdownData: any;
    public tipoProcessoEleicaoDropdownDataAux: any;
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
        private layoutsService: LayoutsService
    ) {
        this.eleicoes = _.orderBy(route.snapshot.data["eleicoes"], ['eleicao'], ['asc']);
        this.eleicoesAuxiliar = this.eleicoes;
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.layoutsService.onLoadTitle.emit({
            description: this.messageService.getDescription('LABEL_ACOMPANHAR_DENUNCIA'),
            icon: 'fa fa-wpforms'
        });

        this.numeroRegistrosPaginacao = 10;
        this.showMessageFilter = false;
        this.filtro = {
            anos: [],
            idsCalendariosEleicao: [],
            idTipoProcesso: ''
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
            textField: 'eleicao',
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
        this.getAnosEleicoesConcluidas();
        this.disabledEleicaoDropdown = true;
        this.checkEleicaoConcluida();
    }

    /**
     * Verifica se existe eleição concluídas, se não existir apresentá msg de aviso.
     */
    private checkEleicaoConcluida(): void {
        if (this.eleicoes.length == 0) {
            this.messageService.addMsgWarning('MSG_NAO_POSSUI_CALENDARIO_CONCLUIDO');
        }
    }

    public pesquisar() {
        this.getEleicoesConcluidasPorFiltro();
    }

    /**
     * Listagem de eleições concluídas por filtro de pesquisa.
     */
    public getEleicoesConcluidasPorFiltro() {
        this.calendarioClientService.getCalendariosPorFiltro(this.filtro).subscribe(data => {
            this.eleicoes = _.orderBy(data, ['eleicao'], ['asc']);
            this.showMessageFilter = false;
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

    /**
     * Verifica se tem anos selecionados no filtro
     */
    public isDesabilitarEleicoes() {
        return this.selectAnos === undefined || this.selectAnos.length === 0;
    }

    /**
     * Preenche lista de Anos de eleição.
     */
    public getAnosEleicoesConcluidas() {
        this.calendarioClientService.getCalendariosConcluidosAnos().subscribe((data: Array<any>) => {
            data = _.orderBy(data, ['ano'], ['desc']);
            this.anosEleicoesConcluidasDropdownData = data.map(eleicao => {
                return { ano: eleicao.eleicao.ano };
            });
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Preenche lista eleição clasificadas por ano.
     */
    public getAnosAndEleicoesConcluida() {
        this.tipoProcessoEleicaoDropdownDataAux = _.map(this.eleicoes, item => {
            return { id: item.id, eleicao: item.eleicao.descricao, ano: item.eleicao.ano };
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
        this.eleicoesConluidasDropdownData = [];
        this.disabledEleicaoDropdown = true;
    }

    /**
    * Preencher eleições pelo filtro de ano.
    * de pesquisa
    * @param event
    */
    public getEleicaoPorFiltroAno() {
        let years: Array<any> = this.filtro.anos;
        let eleicoesByYear = this.tipoProcessoEleicaoDropdownDataAux.filter(eleicao => years.some(anoEleicao => eleicao.ano === anoEleicao));
        this.eleicoesConluidasDropdownData = eleicoesByYear;
        this.disabledEleicaoDropdown = eleicoesByYear.length < 1;
    }

    /**
     * Método chamado ao selecionar a opção de "selecionar todos" no Dropdown de ano.
     * Preenche a variável de filtro ano e  habilita o dropdown de eleição concluída com as respectivas informações.
     */
    public onSelectAllYear() {
        this.filtro.anos = _.map(this.anosEleicoesConcluidasDropdownData, item => {
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
        this.eleicoesConluidasDropdownData = [];
        this.disabledEleicaoDropdown = true;
    }
}
