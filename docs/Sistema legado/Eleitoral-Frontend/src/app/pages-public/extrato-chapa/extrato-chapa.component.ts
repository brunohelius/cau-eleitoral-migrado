import { OnInit, Component, OnDestroy, EventEmitter } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { MessageService } from '@cau/message';
import { DomSanitizer } from '@angular/platform-browser';
import { Constants } from 'src/app/constants.service';
import * as _ from "lodash";
import { LayoutsService } from '@cau/layout';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';


/**
 * Componente responsável pela apresentação de Extrato navegável de Chapas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'extrato-chapa',
    templateUrl: './extrato-chapa.component.html',
    styleUrls: ['./extrato-chapa.component.scss']
})
export class ExtratoChapaComponent implements OnInit, OnDestroy {

    public numeroChapaSelecionado: Array<any>;
    public limitePaginacao: number;
    public limitePaginacaoListagemChapas: number;
    public limitePaginacaoChapas: number;
    public dadosExtratoChapa: any;
    public chapaEleicao: any;
    public cauUf: any;
    public isListagemMembrosChapa: boolean;
    public dropdownSettings: any;

    private idCauUf: number;
    public limitesPaginacao: Array<number>;

    public static SELECIONAR_TUPO_CHAPA_ID: number = -1;

    /**
     * Construtor da classe.
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        public domSanitizer: DomSanitizer,
        private layoutsService: LayoutsService,
        private chapaEleicaoService: ChapaEleicaoClientService,
    ) {
        this.dadosExtratoChapa = route.snapshot.data["dadosExtratoChapa"];
        this.cauUf = route.snapshot.data["cauUf"];
        this.layoutsService.showNavLeft = false;
        this.layoutsService.showNavTop = false;
        this.layoutsService.isContentFullPage = true;
    }

    ngOnInit() {
        this.limitePaginacaoListagemChapas = 10;
        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 25, 50, 100];
        this.idCauUf = this.dadosExtratoChapa.idCauUf;
        this.inicializarDropdownSettings();
        this.isListagemMembrosChapa = false;

        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('LABEL_ACOMPANHAR_CHAPA')
        });
    }

    ngOnDestroy() {
        this.layoutsService.showNavLeft = true;
        this.layoutsService.showNavTop = true;
        this.layoutsService.isContentFullPage = false;
    }

    /**
     * Retorna número da chapa formatado.
     *
     * @param numeroChapa
     */
    public getNumeroChapa(numeroChapa: string): string {
        return numeroChapa ? numeroChapa.toString().padStart(2, '0') : this.messageService.getDescription('LABEL_A_DEFINIR');
    }

    /**
     * Selecionar chapa para exibição do extrato navegável.
     *
     * @param chapa
     */
    public acessarChapa(chapa: any): void {
        this.isListagemMembrosChapa = false;
        this.chapaEleicao = this.findChapaPorId(chapa.id);
    }

    /**
     * Procurar chapa no array de dados do
     *
     * @param idChapa
     */
    private findChapaPorId(idChapa: number): any {
        return this.dadosExtratoChapa.chapasEleicao.find(ichapa => ichapa.id == idChapa);
    }

    /**
     * Alterar número da chapa Selecionada.
     */
    public pesquisar(): void {
        this.isListagemMembrosChapa = false;
        if (this.numeroChapaSelecionado && this.numeroChapaSelecionado.length > 0 && this.numeroChapaSelecionado[0].value) {
            this.chapaEleicao = this.findChapaPorId(this.numeroChapaSelecionado[0].value);
        } else {
            this.chapaEleicao = undefined;
        }

    }

    /**
     * Habilita listagem de membros da chapa eleitoral.
     */
    public acessarMembrosChapa(): void {
        this.isListagemMembrosChapa = true;
    }

    /**
     * Verifica se o status da chapa é pendente.
     *
     * @param idStatusChapa
     */
    public isChapaPendente(idStatusChapa: number): boolean {
        return idStatusChapa == Constants.ID_STATUS_CHAPA_PENDENTE;
    }

    /**
     * Inicializa configurações utilizadas no dropdown de seleção de número de chapa.
     */
    public inicializarDropdownSettings(): void {
        this.dropdownSettings = {
            singleSelection: true,
            idField: 'value',
            textField: 'text',
            selectAllText: 'Selecione Todos',
            unSelectAllText: 'Remove Todos',
            itemsShowLimit: 1,
            allowSearchFilter: true,
            searchPlaceholderText: 'Buscar',
            defaultOpen: false,
            noDataAvailablePlaceholderText: ''
        };
    }

    /**
     * Retorna Array de membros de um card.
     *
     * @param cardMembro
     */
    public getMembrosChapa(cardsMembro: Array<any>): Array<any> {
        let membros = [];
        cardsMembro =  Array.isArray(cardsMembro) ? cardsMembro : Object.values(cardsMembro);
        cardsMembro.forEach(cardMembro => {
            if(cardMembro[Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR]) {
                membros.push(cardMembro[Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR]);
            }

            if(cardMembro[Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE]) {
                membros.push(cardMembro[Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE]);
            }
        });
        return membros;
    }

    /**
     * Retorna Array de valores para montagem de dropdown de seleção de número da chapa.
     */
    public getNumeroChapaDropdownData(): Array<any> {
        let data = [];
        if (this.dadosExtratoChapa.chapasEleicao) {
            data = this.dadosExtratoChapa.chapasEleicao.filter( chapa => chapa.numeroChapa ).map(
                chapa => {
                    if (chapa.numeroChapa) {
                        return { text: this.getNumeroChapa(chapa.numeroChapa), value: chapa.id, numero: chapa.numeroChapa };
                    }
                }
            );
        }
        data.push({ text: this.messageService.getDescription('LABEL_TODAS'), value: ExtratoChapaComponent.SELECIONAR_TUPO_CHAPA_ID, numero: ExtratoChapaComponent.SELECIONAR_TUPO_CHAPA_ID });
        return _.orderBy(data, ['numero'], ['asc']);
    }

    /**
     * Retorna valor do campo Representante de acordo com o tipo do membro.
     *
     * @param idTipoMembroChapa
     */
    public getRepresentacao(idTipoMembroChapa): string {
        return idTipoMembroChapa == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL ? this.messageService.getDescription('LABEL_FEDERAL') : this.messageService.getDescription('LABEL_ESTADUAL');
    }

    /**
     * Retorna redes sociais agrupadas por tipo.
     */
    public getRedesSociaisChapa(): Array<any> {
        return Object.values(_.groupBy(this.chapaEleicao.redesSociaisChapa, 'tipoRedeSocial.id'));
    }

    /**
     * Verifica se o campo de posição do membro deve ser exibido.
     *
     * @param membroChapa
     */
    public isMostrarPosicaoMembro(membroChapa): boolean {
        return membroChapa.tipoMembroChapa.id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
    }

    /**
     * Verifica se a visualização de chapa é IES.
     */
    public retornaPrefixoCauUf(): string {
        return (this.cauUf.id == Constants.ID_CAUBR
            || this.cauUf.id == Constants.ID_IES) ? Constants.IES : this.cauUf.prefixo;
    }

    /**
     * Realiza download de declaração de representatividade.
     *
     * @param event
     * @param idDocumento
     */
    public downloadDocumentoRepresentatividade(event: EventEmitter<any>, idMembro): void {
        this.chapaEleicaoService.downloadDocumentoRepresentatividade(idMembro).subscribe(
            data => {
                var file = new Blob([data.body], { type: 'application/pdf' });
                var fileURL = window.URL.createObjectURL(file);
                window.open(fileURL, '_blank');
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }
}