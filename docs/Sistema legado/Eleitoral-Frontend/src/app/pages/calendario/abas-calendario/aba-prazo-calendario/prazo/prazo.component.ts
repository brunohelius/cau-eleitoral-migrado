import { Component, Input, OnInit, EventEmitter, ViewChild } from '@angular/core';
import { AcaoSistema } from 'src/app/app.acao';
import { MessageService } from '@cau/message';
import { NgForm } from '@angular/forms';

export class PrazoControl {

    private _prazos: PrazoComponent[];
    public onSubmit: EventEmitter<boolean> = new EventEmitter();

    /**
     * @param prazo
     */
    set prazo(prazo: PrazoComponent) {
        this._prazos = this._prazos || [];
        this._prazos.push(prazo);
    }

    /**
     * @returns valid
     */
    get valid(): boolean {
        return this._prazos.filter((component: PrazoComponent) => {
            return component.form.invalid;
        }).length === 0;
    }

    /**
     * @returns prazos
     */
    get prazos(): PrazoComponent[] {
        return this._prazos;
    }
}

@Component({
    selector: 'prazo',
    templateUrl: './prazo.component.html',
    styleUrls: ['./prazo.component.scss']
})
export class PrazoComponent implements OnInit {

    public accordionsAtividades = [];
    public submitted: boolean;

    @Input() prazo: any = {};
    @Input() atividades: any;
    @Input() prazosRemovidos: any;
    @Input('acao') acaoSistema: AcaoSistema;
    @Input('control') prazoControl: PrazoControl;
    @Input('disabled') isCamposDesabilitados: boolean;
    @Input() ocultarExcluir: boolean;

    @ViewChild('formPrazo', { static: true }) form: NgForm;

    /**
     * Construtor da Classe.
     * 
     * @param messageService 
     */
    constructor(
        public messageService: MessageService
    ) { }

    /**
     * Init
     */
    ngOnInit(): void {
        this.prazoControl.prazo = this;
        this.prazoControl.onSubmit.subscribe((submitted: boolean) => {
            this.form.ngSubmit.emit();
            this.submitted = submitted;
        });
        this.prazo.prazos = this.prazo.prazos || [];
    }

    /**
     * Cria um novo prazo.
     */
    public add() {
        let nivelNumerico = this.prazo.nivelNumerico + 1;
        let nivelFilho = this.prazo.nivel + '.' + (this.prazo.prazos.length + 1);

        this.prazo.prazos.push({
            descricaoNivel: this.prazo.descricaoNivel + '.' + (this.prazo.prazos.length + 1),
            nivel: nivelNumerico,
            isExibeIncluirSubAtividade: true,
            nivelNumerico: nivelNumerico,
            nivelAtividadePrincipal: this.prazo.nivelAtividadePrincipal,
            nivelIrmao: this.prazo.prazos.length + 1,
            situacaoDiaUtil: true
        });

        let nivel = this.atividades.filter((data) => {
            return data.nivelAtividadePrincipal == this.prazo.nivelAtividadePrincipal;
        });

        this.accordionsAtividades[nivelFilho] = true;
        this.desabilitarBotaoIncluirSubAtividade(nivel, nivelNumerico, this.prazo.nivelAtividadePrincipal, this.prazo.nivelIrmao);
    }

    /**
    * Adiciona um irmão de nivel 2 clicando no botão adicionar SubAtividade Paralela.
    *
    * @param nivelAtividadePrincipal
    */
    public addPorAtividade(nivelAtividadePrincipal: any) {
        let atividade = this.atividades.filter((data) => {
            return data.nivelAtividadePrincipal == nivelAtividadePrincipal;
        });

        atividade = atividade.shift();

        let nivelFilho = atividade.descricaoNivel + '.' + (atividade.prazos.length + 1);

        atividade.prazos.push({
            descricaoNivel: nivelFilho,
            nivel: atividade.nivelNumerico + 1,
            isExibeIncluirSubAtividade: true,
            nivelNumerico: atividade.nivelNumerico + 1,
            nivelAtividadePrincipal: atividade.nivelAtividadePrincipal,
            nivelIrmao: atividade.prazos.length + 1,
            situacaoDiaUtil: true
        });

        this.accordionsAtividades[nivelFilho] = true;
    }

    /**
     * Verifica se o Usuário desabilitou o toggle de 'Dia Útil', caso sim apresenta modal de configuração com o Usuário.
     * 
     * @param enable 
     * @param prazo 
     */
    public onValueChangeDiaUtil(enable: boolean, prazo: any): void {
        if (!enable) {
            this.messageService.addConfirmYesNo('MSG_DESABILITAR_DIAS_UTEIS', () => { }, () => {
                prazo.situacaoDiaUtil = !enable;
            });
        }
    }

    /**
     * Reprocessa a Arvore.
     *
     * @param nivelAtividade
     * @param nivelNumerico
     * @param nivelAtividadePrincipal
     */
    public reprocessarArvore(nivelAtividade: any, nivelNumerico: any, nivelAtividadePrincipal: any) {
        if (Array.isArray(nivelAtividade)) {
            nivelAtividade = nivelAtividade.filter((data) => {
                return data.nivelAtividadePrincipal == nivelAtividadePrincipal;
            });
            nivelAtividade = nivelAtividade.shift();
        }

        if (nivelAtividade.nivelNumerico == 1) {
            if (nivelAtividade.prazos == undefined || nivelAtividade.prazos.length == 0) {
                nivelAtividade.isExibeIncluirSubAtividade = true;
            }
        }

        nivelAtividade = nivelAtividade.prazos;

        nivelAtividade.forEach((nivel, i) => {

            let resultado = nivel.descricaoNivel.split(".");
            var nivelParcial = resultado.slice(0, -1);
            nivelParcial.push(i + 1);

            nivel.descricaoNivel = nivelParcial.join(".");
            nivel.nivelIrmao = i + 1;

            let nivelAtividadeGeral = this.atividades.filter((data) => {
                return data.nivelAtividadePrincipal == nivelAtividadePrincipal;
            });

            this.desabilitarBotaoIncluirSubAtividade(nivelAtividadeGeral, nivelNumerico, nivelAtividadePrincipal, nivel.nivelIrmao);

            if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                this.reprocessarArvore(nivel, nivelNumerico, nivelAtividadePrincipal);
            }
        });

    }

    /**
     * Busca em toda a Estrutura de arvore pelo nivelNumerico informado.
     *
     * @param nivel
     * @param nivelNumerico
     * @param nivelAtividadePrincipal
     * @param nivelIrmao
     */
    public desabilitarBotaoIncluirSubAtividade(nivel: any, nivelNumerico: any, nivelAtividadePrincipal: any, nivelIrmao: any) {
        let niveisAnteriores = nivel.filter((data) => {
            return data.nivelNumerico <= nivelNumerico - 1;
        });

        niveisAnteriores.forEach(nivel => {
            if (nivel.nivelNumerico != 2 && nivel.nivelIrmao == nivelIrmao) {
                nivel.isExibeIncluirSubAtividade = false;
            }

            if (nivel.prazos == undefined || nivel.prazos.length == 0) {
                nivel.isExibeIncluirSubAtividade = true;
            }

            if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                this.desabilitarBotaoIncluirSubAtividade(nivel.prazos, nivelNumerico, nivelAtividadePrincipal, nivelIrmao);
            }
        });
    }

    /**
     * Remove a Atividade de acordo com Nivel e NivelAtividade informado.
     *
     * @param atividades
     * @param nivelNumerico
     * @param nivelAtividadePrincipal
     * @param nivelIrmao
     * @param nivelPai
     */
    public removerAtividade(atividades: any, nivelNumerico: any, nivelAtividadePrincipal: any, nivelIrmao: any, nivelPai: any, exibeValidacao: boolean) {
        if (exibeValidacao) {
            this.messageService.addConfirmYesNo('LABEL_EXCLUIR_SUB_ATIVIDADE', () => {
                this.removerSubAtividade(atividades, nivelNumerico, nivelAtividadePrincipal, nivelIrmao, nivelPai);
            });
        } else {
            this.removerSubAtividade(atividades, nivelNumerico, nivelAtividadePrincipal, nivelIrmao, nivelPai);
        }
    }

    /**
     * Remove sub-atividade da lista de prazos.
     *
     * @param atividades
     * @param nivelNumerico
     * @param nivelAtividadePrincipal
     * @param nivelIrmao
     * @param nivelPai
     */
    private removerSubAtividade(atividades: any, nivelNumerico: any, nivelAtividadePrincipal: any, nivelIrmao: any, nivelPai: any): void {
        let nivelAtividade = atividades.filter((data) => {
            return data.nivelAtividadePrincipal == nivelAtividadePrincipal;
        });

        nivelAtividade.forEach((nivel, i) => {
            if (nivel.nivelNumerico == nivelNumerico) {

                let niveisIrmao = [];

                if (nivel.prazos && nivel.prazos.length > 0) {
                    niveisIrmao = nivelPai.prazos.filter((data) => {
                        if (data.nivelIrmao == nivelIrmao) {
                            nivel.duracao = '';
                            nivel.descricaoAtividade = '';
                        }

                        return true;
                    });
                } else {
                    niveisIrmao = nivelPai.prazos.filter((data) => {
                        return data.nivelIrmao != nivelIrmao;
                    });
                }

                let prazoRemover = nivelPai.prazos.filter((data) => {
                    return data.nivelIrmao == nivelIrmao;
                });

                prazoRemover = prazoRemover.shift();
               
                if (prazoRemover != undefined && prazoRemover.id != undefined && !this.prazosRemovidos.some(idPrazo => idPrazo == prazoRemover.id)) {
                    this.prazosRemovidos.push(prazoRemover.id);
                }

                nivelPai.prazos = niveisIrmao;
            } else if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                this.removerAtividade(nivel.prazos, nivelNumerico, nivelAtividadePrincipal, nivelIrmao, nivel, false);
            }
        });

        this.reprocessarArvore(nivelAtividade, nivelNumerico, nivelAtividadePrincipal);
    }
}
