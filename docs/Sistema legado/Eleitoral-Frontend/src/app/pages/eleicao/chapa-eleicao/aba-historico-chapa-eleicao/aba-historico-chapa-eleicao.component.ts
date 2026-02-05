import * as _ from "lodash";
import { ActivatedRoute, Router } from "@angular/router";
import { MessageService } from '@cau/message';
import { Component, OnInit, EventEmitter, Input } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { registerLocaleData } from '@angular/common';
import { formatDate } from "@angular/common";

/**
 * Componente responsável pela apresentação de hitórico de chapas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'aba-historico-chapa-eleicao',
    templateUrl: './aba-historico-chapa-eleicao.component.html',
    styleUrls: ['./aba-historico-chapa-eleicao.component.scss']
})
export class AbaHistoricoChapaEleicaoComponent implements OnInit {

    @Input() public historico: Array<any>;
    private _historico: Array<any>;

    public limitePaginacao: number;
    public limitesPaginacao: Array<number>;
    public search: string;

    constructor(
        private messageService: MessageService,
        private router: Router,
        private route: ActivatedRoute,
    ) { }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this._historico = this.historico;
        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 20, 50, 100];
    }

    /**
     * Filtra os dados da grid conforme o valor informado na variável search.
     *
     * @param search
     */
    public filter(search) {
        let filterItens = this._historico.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.historico = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem.
     *
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(obj.nomeUsuario);
        values.push(formatDate(obj.data, 'dd/MM/yyyy mm:ss', 'en-US'));
        values.push(obj.descricaoOrigem);
        values.push(obj.chapaEleicao.uf);
        values.push(this.getNumeroChapa(obj));
        values.push(this.getDescricaoJustificativa(obj));
        values.push(obj.descricaoAcao);
        return values;
    }

    /**
     * Retorna o número da chapa eleição.
     *
     * @param itemHistorico
     */
    public getNumeroChapa(itemHistorico: any): string {
        return itemHistorico.chapaEleicao.hasOwnProperty('numeroChapa') ? itemHistorico.chapaEleicao.numeroChapa : this.messageService.getDescription('LABEL_NAO_APLICADO');
    }

    /**
     * Retorna o justificativa da chapa eleição.
     *
     * @param itemHistorico
     */
    public getDescricaoJustificativa(itemHistorico: any): string {
        return itemHistorico.hasOwnProperty('descricaoJustificativa') ? itemHistorico.descricaoJustificativa : '-';
    }

    public inicio() {
        this.router.navigate(['/']);
    }

    /**
   * Voltar para a listagem de calendarios
   */
    public voltar() {
        this.router.navigate([`eleicao/chapa/listar`]);
    }
}
