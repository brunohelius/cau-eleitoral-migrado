import { Constants } from './../../../constants.service';
import * as _ from "lodash";
import { ActivatedRoute } from "@angular/router";
import { MessageService } from '@cau/message';
import { LayoutsService } from '@cau/layout';
import { Component, OnInit } from '@angular/core';
import { CalendarioClientService } from '../../../client/calendario-client/calendario-client.service';
import { SituacaoAtividadeSecundaria } from 'src/app/client/atividade-secundaria-client/atividade-secundaria-client.service';
import { SecurityService } from "@cau/security";

/**
 * Componente responsável pela apresentação do Painel Visual de Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-atividade-principal',
    templateUrl: './list-atividade-principal.component.html',
    styleUrls: ['./list-atividade-principal.component.scss']
})
export class ListAtividadePrincipalComponent implements OnInit {
    public atividades;
    public filtro;
    public numeroRegistrosPaginacao: number;
    public calendarioId: number;
    public calendario;
    public showMessageFilter: boolean;
    public status: {};

    /**
     * Construtor da classe.
     *
     * @param route
     * @param messageService
     * @param calendarioClientService
     */
    constructor(
        private route: ActivatedRoute,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private calendarioClientService: CalendarioClientService
    ) {
        let data = route.snapshot.data["atividadesPrincipais"];
        this.calendarioId = route.snapshot.params.id;
        if (data.hasOwnProperty('atividadePrincipal')) {
            this.atividades = data.atividadePrincipal;
            this.calendario = data.calendario;
            this.showMessageFilter = this.atividades < 1;
        }
        else {
            this.atividades = null;
            this.calendario = null;
            this.showMessageFilter = true;
        }
    }

    /**
     * Inicialização dos dados do campo
     */
    ngOnInit() {
        /**
         * Define ícone e título do header da página
         */
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('LABEL_CONFIGURAR_ELEICAO')
        });
        //this.getListaAtividades();
        this.filtro = {
            atividadeSecundariaDataInicio: null,
            atividadeSecundariaDataFim: null,
        };
        this.numeroRegistrosPaginacao = 1;
        this.initData();
    }

    private initData(): void {
        this.setActionsAtividades();
        this.setConfigStatusAtividade();
    }

    /**
     * Busca de Atividades do calendário
     */
    public getListaAtividades(): void {
        this.calendarioClientService.getAtividadesPorCalendarioComFiltro(this.calendarioId, this.filtro).subscribe(
            data => {
                if (data.hasOwnProperty('atividadePrincipal') && data.atividadePrincipal.length > 0) {
                    this.atividades = data.atividadePrincipal;
                    this.initData();
                } else {
                    this.messageService.addMsgWarning("LABEL_NENHUM_REGISTRO_ENCONTRADO");
                }
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Pesquisar com filtros de data inicial e final de atividades
     */
    public pesquisar() {
        this.getListaAtividades();
    }

    /**
     * Adiciona os botões de ação para cada atividade secundária, vinculando os métodos específicos para cada tópico.
     */
    private setActionsAtividades(): void {
        this.atividades.map((atividadePrincipal) => {
            atividadePrincipal.atividadesSecundarias.map((atividadeSecundaria) => {
                let topicoAtividade = `${atividadePrincipal.nivel}${atividadeSecundaria.nivel}`;
                atividadeSecundaria.actions = this[`getActionsByAtividade${topicoAtividade}`]
                    ? this[`getActionsByAtividade${topicoAtividade}`](atividadeSecundaria)
                    : [];

                return atividadeSecundaria;
            });
            return atividadePrincipal;
        });
    }

    /**
     * Retorna os botões de ação para atividade 1.1.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade11(atividadeSecundaria: any): any {
        let actions = [];

        if (atividadeSecundaria.statusAtividade == Constants.STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO
            && atividadeSecundaria.isPrazoVigente
        ) {
            actions.push({
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/incluir-informacao-comissao-membro`
            });
        }

        if (atividadeSecundaria.statusAtividade != Constants.STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO) {
            actions.push(
                {
                    'label': this.messageService.getDescription('LABEL_ALTERAR'),
                    'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/alterar-informacao-comissao-membro`
                },
                {
                    'label': this.messageService.getDescription('LABEL_VISUALIZAR'),
                    'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/visualizar-informacao-comissao-membro`
                }
            );
        }

        return actions;
    }

    /**
     * Retorna os botões de ação para atividade 1.2.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade12(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_DEFINIR_EMAIL'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-comissao-membro`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 1.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade13(atividadeSecundaria: any): any {
        return [
            {
                'routerLink': this.getUrlPublicarDocumento(),
                'label': this.messageService.getDescription('LABEL_PUBLICAR')
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 1.4.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade14(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-declaracao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 1.5.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade15(atividadeSecundaria: any): any {
        return [
            {
                'routerLink':  `/publicacao/documento/${this.calendarioId}/publicar`,
                'label': this.messageService.getDescription('LABEL_PUBLICAR')
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 1.6.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade16(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-numero-conselheiro`
            }
        ];
    }


    /**
     * Retorna os botões de ação para atividade 1.7.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade17(atividadeSecundaria: any): any {
        return [
            {
                'routerLink':  this.getUrlPublicarDocumento(),
                'label': this.messageService.getDescription('LABEL_PUBLICAR')
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 1.8.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade18(atividadeSecundaria: any): any {
        return [
            {
                'routerLink':  this.getUrlPublicarDocumento(),
                'label': this.messageService.getDescription('LABEL_PUBLICAR')
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.1.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade21(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-declaracao-chapa`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.2.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade22(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-declaracao-confirm-partic-chapa`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade23(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-pedido-substituicao-chapa`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade24(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-substituicao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade25(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-substituicao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade26(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-recurso-substituicao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 3.1.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade31(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-declaracao-pedido-impugnacao`
            }
        ];
    }

     /**
     * Retorna os botões de ação para atividade 4.15.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade415(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-julgamento-admissibilidade`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.14.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade414(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-julgamento`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 3.2.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade32(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-defesa`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 2.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade33(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-impugnacao`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 3.4.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade34(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-impugnacao`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 3.5.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade35(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-contrarrazao-pedido-impugnacao`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 3.6.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade36(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-recurso-impugnacao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.1.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade41(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-denuncia`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.2.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade42(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-admissibilidade-denuncia`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.3.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade43(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-apresentar-defesa`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.4.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade44(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-producao-provas`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.5.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade45(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-impedimento-suspeicao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.6.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade46(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-inserir-provas`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.7.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade47(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-agendamento-audiencia-instrucao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.8.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade48(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-realizacao-audiencia-instrucao`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.9.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade49(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-alegacoes-finais`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.10.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade410(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-inserir-alegacoes-finais`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.11.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade411(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-parecer-final`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.12.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade412(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-primeira-instancia`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.18.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade413(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-admissibilidade-denuncia`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.16.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade416(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-inserir-novo-relator`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.17.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade417(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-reconsideracao`
            }
        ];
    }

     /**
     * Retorna os botões de ação para atividade 4.18.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade418(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-cadastro-contrarrazao`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 5.1.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade51(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-final`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 5.2.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade52(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-julgamento-final`
            }
        ];
    }

     /**
     * Retorna os botões de ação para atividade 4.20.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade420(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-segunda-instancia`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 4.18.
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade419(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-inserir-relator`
            }
        ];
    }

    /**
    * Retorna os botões de ação para atividade 5.3.
    *
    * @param atividadeSecundaria
    */
    private getActionsByAtividade53(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-substituicao-julgamento-final`
            }
        ];
    }

    /**
   * Retorna os botões de ação para atividade 5.3.
   *
   * @param atividadeSecundaria
   */
    private getActionsByAtividade54(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-final-segunda-instancia`
            }
        ];
    }

    /**
   * Retorna os botões de ação para atividade 5.3.
   *
   * @param atividadeSecundaria
   */
    private getActionsByAtividade55(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-da-substituicao-julgamento-final`
            }
        ];
    }

    /**
   * Retorna os botões de ação para atividade 6.1
   *
   * @param atividadeSecundaria
   */
    private getActionsByAtividade61(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-pedido-impugnacao-resultado`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 6.1
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade62(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-alegacao-impugnacao-resultado`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 6.1
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade63(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-impugnacao-resultado`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 6.1
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade64(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-recurso-impugnacao-resultado`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 6.1
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade65(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-contrarrazao-impugnacao-resultado`
            }
        ];
    }

    /**
     * Retorna os botões de ação para atividade 6.1
     *
     * @param atividadeSecundaria
     */
    private getActionsByAtividade66(atividadeSecundaria: any): any {
        return [
            {
                'label': this.messageService.getDescription('LABEL_PARAMETRIZAR'),
                'routerLink': `/eleicao/atividade-secundaria/${atividadeSecundaria.id}/definir-email-julgamento-recurso-impugnacao-resultado`
            }
        ];
    }

    /**
     * Define as parametrizações de cada status atividade.
     */
    private setConfigStatusAtividade(): void {
        this.status = {
            [Constants.STATUS_ATIVIDADE_REGRAS_NAO_DEFINIDAS]: {
                'classCss' : '',
                'label': this.messageService.getDescription('LABEL_HIFEN')
            },
            [Constants.STATUS_ATIVIDADE_AGUARDANDO_DOCUMENTO]: {
                'classCss': 'text-status-2',
                'label': this.messageService.getDescription('LABEL_AGUARDANDO_DOCUMENTO'),
            },
            [Constants.STATUS_ATIVIDADE_ATUALIZACAO_CONCLUIDA]: {
                'classCss': 'text-info',
                'label': this.messageService.getDescription('LABEL_ATUALIZACAO_REALIZADA')
            },
            [Constants.STATUS_ATIVIDADE_AGUARDANDO_ATUALIZACAO]: {
                'classCss': 'text-danger',
                'label': this.messageService.getDescription('LABEL_AGUARDANDO_ATUALIZACAO')
            },
            [Constants.STATUS_ATIVIDADE_PARAMETRIZACAO_CONCLUIDA]: {
                'classCss': 'text-status-1',
                'label': this.messageService.getDescription('LABEL_PARAMETRIZACAO_CONCLUIDA')
            },
            [Constants.STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO]: {
                'classCss': 'text-status-3',
                'label': this.messageService.getDescription('LABEL_AGUARDANDO_PARAMETRIZACAO')
            },
        };
    }

    /**
     * Retorna as parametrizações de acordo com o status atividade.
     *
     * @param idStatusAtividade
     */
    public getEstruturaStatus(idStatusAtividade: number): any {
        return this.status[idStatusAtividade];
    }

    /**
     * Limpa os dados do formulário.
     *
     * @param event
     */
    public limpar(event: any) {
        event.preventDefault();
        this.filtro.atividadeSecundariaDataInicio = null;
        this.filtro.atividadeSecundariaDataFim = null;
    }

    public verificarNomeSubTarefa(atividadePrincipal, atividadeSecundaria): any {
        const isPrimeiraAtvPrincipal = atividadePrincipal == Constants.PRIMEIRA_ATIVIDADE_PRINCIPAL;

        if (isPrimeiraAtvPrincipal && atividadeSecundaria.nivel == Constants.TERCEIRA_ATIVIDADE_SECUNDARIA) {
            return this.messageService.getDescription('LABEL_PUBLICAR_QUANTIDADE_MEMBROS');
        } else if (isPrimeiraAtvPrincipal && atividadeSecundaria.nivel == Constants.QUARTA_ATIVIDADE_SECUNDARIA) {
            return this.messageService.getDescription('LABEL_CONFIRMAR_PARTICIPACAO_COMISSAO');
        } else {
            return atividadeSecundaria.descricao;
        }
    }

    /**
     * Retorna a rota de Publicação de Documentos.
     */
    public getUrlPublicarDocumento(): string {
        return `/publicacao/documento/${this.calendarioId}/publicar`;
    }

}
