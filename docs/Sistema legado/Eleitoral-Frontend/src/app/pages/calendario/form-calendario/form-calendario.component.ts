import { ActivatedRoute, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';

import { AcaoSistema } from 'src/app/app.acao';
import { MessageService } from '@cau/message';
import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import * as deepEqual from "deep-equal";
import * as _ from "lodash";
import { LayoutsService } from '@cau/layout';

/**
 * Componente de formulário do calendário.
 *
 * @author Squadra Tecnologia
 */
@Component({
    selector: 'form-calendario',
    templateUrl: './form-calendario.component.html'
})
export class FormCalendarioComponent implements OnInit {

    public tab: any;
    public calendario: any;
    public calendarioClone: any;
    public prazosCalendario: any;
    public atividadesPeriodo: any;
    public acaoSistema: AcaoSistema;
    public replicarAtividades = false;
    public calendarioAtividadesPrincipais: any;

    /**
     * Construtor da classe.
     *
     * @param router
     * @param route
     * @param messageService
     * @param calendarioClientService
     */
    constructor(
        route: ActivatedRoute,
        private router: Router,
        private messageService: MessageService,
        private calendarioClientService: CalendarioClientService,
        private layoutsService: LayoutsService
    ) {
        this.acaoSistema = new AcaoSistema(route);
        this.replicarAtividades = false;

        if (this.acaoSistema.isAcaoAlterar() || this.acaoSistema.isAcaoVisualizar()) {
            this.calendario = route.snapshot.data["calendario"];
            this.calendario.idadeFim = this.calendario.idadeFim.toString();
            this.calendario.idadeInicio = this.calendario.idadeInicio.toString();
            this.calendarioClone = _.cloneDeep(this.calendario);
            this.atividadesPeriodo = _.cloneDeep(this.calendario.atividadesPrincipais); //clone do Obj
        } else {

            this.calendario = {
                "eleicao": {
                    "ano": undefined,
                },
                "dataFimMandato": undefined,
                "dataFimVigencia": undefined,
                "dataInicioMandato": undefined,
                "dataInicioVigencia": undefined,
                "idadeFim": undefined,
                "idadeInicio": undefined,
                "ativo": true
            };

            this.calendarioClone = _.cloneDeep(this.calendario);
        }

        this.prazosCalendario = route.snapshot.data["prazosCalendario"];
        this.calendarioAtividadesPrincipais = route.snapshot.data["calendarioAtividadesPrincipais"];
    }

    /**
     * Init component.
     */
    ngOnInit(): void {
        this.layoutsService.onLoadTitle.emit({
            description: this.messageService.getDescription('LABEL_PARAMETRIZACAO_CALENDARIO_ELEITORAL'),
            icon: 'fa fa-wpforms'
        });
        this.tab = {
            tabPeriodo: { active: true },
            tabPrazo: {},
            tabHistorico: {}
        };

        this.tab.current = this.tab.tabPeriodo;
    }

    /**
     * Método que realiza o controle das abas.
     *
     * @param selectTab
     * @param nomeAba
     */
    public onSelect(selectTab: any, nomeAba: any): void {
        if (this.calendario.id && this.hasModificacao() && !this.acaoSistema.isAcaoVisualizar()) {
            this.messageService.addConfirmYesNo('LABEL_MUDAR_ABA', () => {
                this.habilitarAba(selectTab);
            });
        } else if (this.calendario.id) {
            this.habilitarAba(selectTab)
        } else if (nomeAba == 'prazo') {
            this.messageService.addMsgWarning('MSG_HABILITAR_ABA_PRAZO');
        }
    }

    /**
     * Método responsável por habilitar as abas.
     *
     * @param selectTab
     */
    public habilitarAba(selectTab: any): void {
        this.calendarioClientService.getCalendarioPorId(this.calendario.id).subscribe(data => {
            this.recarregarCalendario(data);
            selectTab.active = true;
            this.tab.current.active = false;
            this.tab.current = selectTab;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * Valida se existe modificação no objeto de calendário.
     */
    public hasModificacao(): boolean {
        return !deepEqual(this.calendarioClone, this.calendario);
    }

    /**
     * Responsável por trocar para a aba de prazo.
     */
    public avancarPrazo(): void {
        this.habilitarAba(this.tab.tabPrazo);
    }

    /**
     * Recarrega o calendário de acordo com o calendário salvo.
     *
     * @param calendario
     */
    public recarregarCalendario(calendario: any): void {
        calendario.idadeFim = calendario.idadeFim.toString();
        calendario.idadeInicio = calendario.idadeInicio.toString();
        this.calendario = calendario;
        this.calendarioClone = _.cloneDeep(calendario);
    }

    /**
     * @param calendario 
     */
    public setCalendarioClone(calendario: any): void {
        this.calendarioClone = calendario;
    }

    /**
     * Válida as funções de redirecionamento.
     */
    public voltarLista(): void {
        if (this.hasModificacao()) {
            this.messageService.addConfirmYesNo('LABEL_VOLTAR', () => {
                this.router.navigate([`/calendario/listar`]);
            });
        } else {
            this.router.navigate(['/calendario/', 'listar']);
        }
    }

}
