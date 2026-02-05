import * as _ from "lodash";
import * as moment from 'moment';
import { ActivatedRoute } from "@angular/router";
import { BsModalRef, BsModalService } from 'ngx-bootstrap/modal';
import { MessageService } from '@cau/message';
import { Component, EventEmitter, OnInit, TemplateRef } from '@angular/core';
import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { LayoutsService } from '@cau/layout';

/**
 * Componente referente à listagem de calendários.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-eleitoral',
    templateUrl: './list-calendario.component.html'
})
export class ListCalendarioComponent implements OnInit {
    public calendariosAuxiliar = [];
    public calendarios: any[];
    public anos: any[];
    public anosTO: any[];
    public calendariosAnos: any[];
    public calendariosAnosTO: any[];
    public selectAnos: any[];
    public selectCalendariosAnos: any[];
    public tipoProcesso: any;
    public processo: any[];
    public itemExcluir: any[];
    public filtro: any = {};
    public search: string;
    public quantidadeCalendarios: number = 0;
    public dropdownSettings = {};
    public dropdownSettingsCalendario = {};
    public dropdownSettingsProcesso = {};
    modalRef: BsModalRef;
    message: string;
    public showMessageFilter: Boolean;

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
        private modalService: BsModalService,
        private layoutsService: LayoutsService
    ) {
    }

    /**
     * Inicialização dos dados do compo
     */
    ngOnInit() {
        this.layoutsService.onLoadTitle.emit({
            description: this.messageService.getDescription('LABEL_PARAMETRIZACAO_CALENDARIO_ELEITORAL'),
            icon: 'fa fa-wpforms'
        });
        this.calendarios = this.route.snapshot.data["listCalendario"];
        this.getInicializarCalendarios();

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

        this.showMessageFilter = false;
    }

    /**
     * Inicializacao de todos os dados usados na tela de listagem
     * de Calendarios
     */
    public getInicializarCalendarios() {
        this.getListaCalendarios();
        this.getListaAnos();
        this.getListaCalendariosBarraAno();
        this.getListaTipoProcessoEleicao();
    }

    /**
     * Busca Lista Geral de calendarios
     */
    public getListaCalendarios(): void {
        this.calendarios = _.orderBy(this.calendarios, ['ano'], ['asc']);
        this.calendariosAuxiliar = this.calendarios;
        this.setQuantidadeCalendarios(this.calendarios.length);

        if (this.calendariosAuxiliar.length === 0) {
            this.messageService.addMsgDanger('LABEL_NENHUM_REGISTRO_ENCONTRADO');
        }
    }

    /**
     * Busca Lista Anos de calendarios
     */
    public getListaAnos(): void {
        this.calendarioClientService.getAnos().subscribe(data => {
            data = _.orderBy(data, ['ano'], ['desc']);
            this.anos = data.map( ano => { return { ano: ano.eleicao.ano}; } );
            this.anosTO = data;
            this.selectAnos = undefined;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
    * Busca Lista calendarios barra Ano
    */
    public getListaCalendariosBarraAno(): void {
        this.calendarioClientService.getCalendariosAno().subscribe(data => {
            this.calendariosAnos = [];
            this.calendariosAnosTO = _.orderBy(data, ['eleicao'], ['desc']);            
            this.selectCalendariosAnos = undefined;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
    * Busca Lista calendarios Tipo do Processo
    */
    public getListaTipoProcessoEleicao(): void {
        this.calendarioClientService.getTipoProcesso().subscribe(data => {
            this.tipoProcesso = data;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * Preencher select de calendarios por ano selecionado
     * @param ano array ano e select false(nao selecionado) or true (selecionado)
     */
    public preencherCalendariosPorAnoSelecionada(ano: any): void {
        let calendariosAnosAnt = [];
        calendariosAnosAnt = this.calendariosAnos;
        this.calendariosAnos = this.filtrarAnoInformado(ano);
        this.calendariosAnos = _.concat(this.calendariosAnos, calendariosAnosAnt);
        this.calendariosAnos = _.orderBy(this.calendariosAnos, ['eleicao'], ['desc']);
    }

    /**
    * filtro de busca por texto informado ano select
    * @param ano texto a ser procurado no array
    */
    public filtrarAnoInformado(ano: string) {
        return this.calendariosAnosTO.filter((data) => JSON.stringify(data).toLowerCase().indexOf(ano.toString().toLowerCase()) !== -1).map(eleicao => { return { id: eleicao.id, descricao: eleicao.eleicao.descricao};});
    }

    /**
     * Preencher select de calendarios por ano selecionado
     * @param ano array ano e select false(nao selecionado) or true (selecionado)
     */
    public retirarCalendariosPorAnoSelecionada(ano: any) {
        return this.selectCalendariosAnos.filter((data) => JSON.stringify(data).toLowerCase().indexOf(ano.ano.toString().toLowerCase()) !== -1);
    }

    /**
     * deletar select de calendarios por ano selecionado o campo selected
     * @param ano array ano e select false(nao selecionado) or true (selecionado)
     */
    public deletarFieldSelectedPorAnoSelecionada(array: any, ano: any) {
        _.forEach(array, function (valueAno) {
            if (valueAno.eleicao.toString().includes(ano.toString())) {
                delete valueAno.selected;
            }
        })
        return array
    }

    /**
     * Retirar eleicoes nao selecionadas pelo ano
     * @param selectAno 
     */
    public retirarEleicoesObjCalendariosAnos(selectAno: any) {
        return this.calendariosAnos.filter((data) => JSON.stringify(data).indexOf(selectAno.ano) === -1)

    }

    /**
     * quando desmarcar o ano e tiver dados no calendarios seleciondados
     * o mesmo retira da selecao
     * @param selectAno 
     */
    public removerCalendariosAnosSelecionadas(selectAno) {
        if (this.selectCalendariosAnos !== undefined) {
            this.selectCalendariosAnos = this.selectCalendariosAnos.filter((data) => JSON.stringify(data).toLowerCase().indexOf(selectAno.toString().toLowerCase()) === -1);
        }

        if (this.selectAnos !== undefined && this.selectAnos.length === 0) {
            this.calendariosAnos = [];
        }

        if (this.selectAnos !== undefined) {
            this.adicionarCalendariosPorAnosSelecionados();
        }
    }

    /** 
     * Metodo resposnavel por adicionar os anos que estao 
     * selecionados ao retirar um  da selecao
    */
    public adicionarCalendariosPorAnosSelecionados() {
        this.calendariosAnos = [];
        let escopo = this;
        _.forEach(this.selectAnos, function (valueA) {
            _.forEach(escopo.calendariosAnosTO, function (valueE) {
                if (valueE.eleicao.toString().includes(valueA.toString())) {
                    escopo.calendariosAnos.push(valueE);
                }
            })
        })
    }

    /**
     * Limpa os dados do formulário.
     * 
     * @param event 
     */
    public limpar(event: any) {
        event.preventDefault()
        this.selectAnos = [];
        this.selectCalendariosAnos = [];
        this.processo = [];
        this.calendariosAnos = [];
        this.calendarios = [];
        this.search = "";
        this.setQuantidadeCalendarios(0);
        this.showMessageFilter = true;
    }

    /**
     * Busca na lista de Calendario pelos filtro informados
     */
    public pesquisar(): void {
        this.showMessageFilter = false;
        event.preventDefault();
        let escopo = this;
        let eleicao = [];
        let anos = [];
        _.forEach(this.selectAnos, function (ano) {
            let isExistente = false;
            
            _.forEach(escopo.selectCalendariosAnos, function (anoCalendario) {
                if (anoCalendario.descricao.toString().includes(ano.toString())) {
                    isExistente = true;
                    eleicao.push(anoCalendario);
                }
            })
            
            isExistente ? true : anos.push(ano);
        })
        this.getDadosCalendarios(anos);
    }

    /**
     * Retorna os dados de calendários conforme os valores informados no filtro.
     * 
     * @param anos 
     */
    public getDadosCalendarios(anos: any): void {
        this.filtro = this.getFiltroPesquisa(anos);
        this.calendarioClientService.getPesquisaFiltro(this.filtro).subscribe(data => {
            data = _.orderBy(data, ['ano'], ['asc']);
            this.calendarios = data;
            this.setQuantidadeCalendarios(data.length);
        }, error => {
            this.setQuantidadeCalendarios(0);
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Retorna o filtro de pesquisa de calendários.
     * 
     * @param anos 
     */
    private getFiltroPesquisa(anos: any): any {
        return {
            "idTipoProcesso": this.processo && this.processo.length > 0 ? [this.processo[0].id] : [],
            "eleicoes": this.getEleicoesFiltro(this.selectCalendariosAnos),
            "anos": anos
        };
    }

    /**
     * Retorna os anos eleições para o filtro de pesquisa.
     */
    public getEleicoesFiltro(anosEleicao): any[] {
        let eleicoes = [];
        let escopo = this;
        
        _.forEach(anosEleicao, function (anoEleicao) {
            
            _.forEach(escopo.calendariosAuxiliar, function (anoCalendario) {
                if (anoEleicao.id == anoCalendario.id) {
                    eleicoes.push(anoCalendario.id);
                }
            })
            
        })
        
        return eleicoes;
    }

    /**
     * metodo para abrir modalConfirmDialog
     * @param template 
     * @param item 
     */
    openModal(template: TemplateRef<any>, item: any) {
        this.itemExcluir = item
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }))
    }

    /**
     * metodo SIM do confirmDialog
     * o mesmo refere-se a exclusao de uma eleicao
     */
    public excluir(item: any): void {
        this.messageService.addConfirmYesNo('LABEL_CONFIRMACAO_EXCLUIR', () => {
            this.calendarioClientService.deleteCalendario(item).subscribe(data => {
                this.calendarioClientService.getCalendario().subscribe(calendarios => {
                    this.calendarios = calendarios;
                    this.getInicializarCalendarios();
                    this.messageService.addMsgSuccess('MSG_SUCESSO_EXCLUSAO');
                }, error => {
                    this.messageService.addMsgDanger(error);
                });
            }, error => {
                this.messageService.addMsgDanger(error);
            })
        })
    }

    /**
     * metodo NAO do confirmDialog
     */
    decline(): void {
        this.modalRef.hide()
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
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this.calendariosAuxiliar.filter((data) => JSON.stringify(data).toLowerCase().indexOf(search.toLowerCase()) !== -1);
        this.calendarios = filterItens;
        this.setQuantidadeCalendarios(filterItens.length);
    }

    /**
     * Mostar o total de itens encontrado na tabela
     * @param total number
     */
    public setQuantidadeCalendarios(total) {
        this.quantidadeCalendarios = total
    }

    /**
     * Quando usuario seleciona o Ano no filtro 
     * de pesquisa
     * @param event 
     */
    public onItemSelectYear(ano: any) {
        this.preencherCalendariosPorAnoSelecionada(ano);
    }

    public onSelectAllYear() {
        this.calendariosAnos = this.calendariosAnosTO;
    }

    public onDeSelectAllYear() {
        this.calendariosAnos = [];
        this.selectCalendariosAnos = [];
    }

    public onItemDeSelectYear(ano: any) {
        this.removerCalendariosAnosSelecionadas(ano);
    }
    public onDropDownCloseYear() { }

    /**
     *
     * @param event
     */
    public onItemSelectCalendar(event: any) { }

    public onSelectAllCalendar(event: any) { }

    public isDesabilitarEleicoes() {
        return this.selectAnos === undefined || this.selectAnos.length === 0;
    }

    /**
     * Válida se o botão de excluir será exibido.
     * 
     * @param item
     */
    public isExibeExcluir(item): boolean {
        let agora = moment().toDate();
        let dataInicioVigencia = moment(item.dataInicioVigencia, 'YYYY-MM-DD').toDate();
        return item.idSituacao === 1 ? true : agora > dataInicioVigencia ? false : true;
    }
}
