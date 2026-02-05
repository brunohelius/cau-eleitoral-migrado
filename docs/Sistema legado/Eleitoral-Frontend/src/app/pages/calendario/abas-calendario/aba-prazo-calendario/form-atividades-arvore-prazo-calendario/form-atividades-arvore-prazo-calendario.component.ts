import { ActivatedRoute } from '@angular/router';
import { Component, Input, OnInit, TemplateRef, Output, EventEmitter } from '@angular/core';
import { MessageService } from '@cau/message';
import { AcaoSistema } from 'src/app/app.acao';
import { CalendarioClientService } from 'src/app/client/calendario-client/calendario-client.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import * as _ from "lodash";
import { NgForm } from '@angular/forms';
import { PrazoControl } from '../prazo/prazo.component';

import * as moment from 'moment';

/**
 * Componente do formulário de Cadastro de atividades do calendário.
 *
 * @author Squadra Tecnologia
 */
@Component({
    selector: 'form-atividades-arvore-prazo-calendario',
    templateUrl: './form-atividades-arvore-prazo-calendario.component.html',
    styleUrls: ['./form-atividades-arvore-prazo-calendario.component.scss']
})
export class FormAtividadesArvorePrazoCalendarioComponent implements OnInit {

    @Input() calendario: any;
    @Output() periodoSalvo: EventEmitter<any>;
    @Output('updateClone') onUpdateClone: EventEmitter<any>;

    public submitted: boolean;
    public modalRef: BsModalRef;
    public accordionsAtividades;
    public atividades: any = [];
    public acaoSistema: AcaoSistema;
    public prazosRemovidos: any = [];
    public atividadePrincipal: any = [];
    public atividadesVigentes: any = [];
    public justificativaAlteracao: any = [];
    public tipoAtividadePrincipal: any = undefined;
    public atividadesPrinciapisSelecionadas: any = [];
    public prazoControl: PrazoControl = new PrazoControl();

    public preenchimento: string = "Em preenchimento";
    public isConcluirCalendario: boolean = false;

    /**
     * Construtor da classe.
     *
     * @param route
     * @param calendarioClientService
     * @param messageService
     * @param modalService
     */
    constructor(route: ActivatedRoute, private calendarioClientService: CalendarioClientService, private messageService: MessageService, private modalService: BsModalService) {
        this.acaoSistema = new AcaoSistema(route);
        this.periodoSalvo = new EventEmitter();
        this.onUpdateClone = new EventEmitter();
        this.accordionsAtividades = [];
    }

    /**
     * Inicializa o Component.
     */
    ngOnInit() {
        this.atividades = this.calendario.atividadesPrincipais;
        this.atividadePrincipal = _.cloneDeep(this.calendario.atividadesPrincipais);

        if (this.atividades.length == 0) {
            this.criarAtividade();
        }
        this.ordenarAtividades();
        this.processoArvore(this.atividades);
        this.reprocessaTiposAtividadePrincipal();
        this.onUpdateClone.emit(_.cloneDeep(this.calendario));
    }

    /**
     * Cria uma nova atividade ao calendário.
     */
    public criarAtividade() {
        let atividade = {
            "descricaoNivel": this.atividades.length + 1 + "",
            "prazos": [],
            "isExibeIncluirSubAtividade": true,
            "nivel": 1,
            "nivelNumerico": 1,
            "nivelAtividadePrincipal": this.atividades.length + 1,
            "nivelIrmao": this.atividades.length + 1,
        };

        this.atividades.push(atividade);
    }

    /**
     * Exclui uma atividade.
     *
     * @param atividadePrincipal
     */
    public excluirAtividade(atividadePrincipal: any) {
        if (atividadePrincipal != undefined) {
            this.messageService.addConfirmYesNo('LABEL_EXCLUIR_ATIVIDADE', () => {

                let atividadeRemovida = this.atividades.filter(data => {
                    return data.nivelAtividadePrincipal == atividadePrincipal.nivelAtividadePrincipal;
                })

                if (atividadePrincipal.nivelAtividadePrincipal == this.atividades.length) {
                    if (atividadeRemovida.id != undefined) {
                        this.prazosRemovidos.push(atividadeRemovida);
                    }

                    this.atividades = this.atividades.filter(data => {
                        return data.nivelAtividadePrincipal != atividadePrincipal.nivelAtividadePrincipal;
                    });
                } else {
                    atividadePrincipal.prazos = [];
                    atividadePrincipal.id = undefined;
                    atividadePrincipal.dataFim = undefined;
                    atividadePrincipal.dataInicio = undefined;
                    atividadePrincipal.isExibeIncluirSubAtividade = true;
                }

                this.reprocessaTiposAtividadePrincipal();
                this.reprocessarArvore(this.atividades);
            });
        }
    }

    /**
     * Reprocessa a arvore, atualizando os niveis.
     *
     * @param atividades
     * @param atividadePai
     * @param atividadeAvo
     * @param isAtividadePrincipal
     */
    public processoArvore(atividades: any, atividadePai?: any, atividadeAvo?: any, isAtividadePrincipal: boolean = true): any {    
        if (atividades) {
            atividades.forEach((nivel, i) => {        
                if (isAtividadePrincipal) {
                    nivel.descricaoNivel = i + 1;
                    nivel.nivelIrmao = i + 1;
                    nivel.nivelAtividadePrincipal = i + 1;
                    nivel.nivelNumerico = 1;
                    nivel.isExibeIncluirSubAtividade = true;

                    /** Adiciona prazo a cada atividade.*/
                    if((!nivel.prazos || nivel.prazos.length == 0) && atividadePai == undefined){
                        nivel.prazos = [];
                        this.addPorAtividade(nivel);
                    }
                    
                } else {
                    nivel.descricaoNivel = atividadePai.descricaoNivel + '.' + (i + 1);
                    nivel.nivelIrmao = i + 1;
                    nivel.nivelAtividadePrincipal = atividadePai.nivelAtividadePrincipal;
                    nivel.nivelNumerico = atividadePai.nivelNumerico + 1;
                    nivel.isExibeIncluirSubAtividade = true;
                }
                
                if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                    let prazos = [];
                    //Converte o Objeto em Array.
                    Object.keys(nivel).forEach(key => {
                        if (key == 'prazos') {
                            let objPrazos = nivel[key];
                            Object.values(objPrazos).forEach(prazo => {
                                prazos.push(prazo)
                            });

                        }
                    });

                    nivel.prazos = prazos;
                    this.processoArvore(nivel.prazos, nivel, atividadePai, false);
                }

                if (nivel.nivelNumerico == 2 && atividadePai) {
                    atividadePai.isExibeIncluirSubAtividade = false;
                }

                if (atividadeAvo != undefined) {
                    atividadeAvo.isExibeIncluirSubAtividade = false;
                }
            });

            this.atividadesVigentes = JSON.parse(JSON.stringify(this.atividades));
        }
    }

    /**
     * Reprocessa a arvore, atualizando os niveis.
     *
     * @param atividades
     * @param nivelAtividadePrincipal
     */
    public reprocessarArvore(atividades: any, nivelAtividadePrincipal?: any): any {

        atividades.forEach((nivel, i) => {
            let resultado = nivel.descricaoNivel.toString().split(".");
            var nivelParcial = resultado.slice(0, -1);

            if (nivelAtividadePrincipal != undefined) {
                nivelParcial[0] = nivelAtividadePrincipal;
            }

            nivelParcial.push(i + 1);

            nivel.descricaoNivel = nivelParcial.join(".");
            nivel.nivelIrmao = i + 1;

            if (nivel.nivelNumerico == 1) {
                nivel.nivelAtividadePrincipal = i + 1;
            } else {
                nivel.nivelAtividadePrincipal = nivelAtividadePrincipal
            }

            if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                this.reprocessarArvore(nivel.prazos, nivel.nivelAtividadePrincipal);
            }
        });

    }

    /**
     * Adiciona um subNivel de acordo com Prazo informado.
     *
     * @param atividade
     */
    public addPorAtividade(atividade: any) {
        atividade.prazos = atividade.prazos || [];
        atividade.prazos.push({
            descricaoNivel: atividade.descricaoNivel + '.' + (atividade.prazos.length + 1),
            nivel: atividade.nivelNumerico + 1,
            isExibeIncluirSubAtividade: true,
            nivelNumerico: atividade.nivelNumerico + 1,
            nivelAtividadePrincipal: atividade.descricaoNivel,
            nivelIrmao: atividade.prazos.length + 1,
            situacaoDiaUtil: true
        });

        if (atividade.nivelNumerico == 1 && (atividade.prazos != undefined && atividade.prazos.length != 0)) {
            atividade.isExibeIncluirSubAtividade = false;
        }
    }

    /**
     * Atribui os valores da atividade principal para a estrutura de arvore.
     *
     * @param idTipoAtividadePrincipal
     * @param atividade
     */
    public selectTipoAtividadePrincipal(idTipoAtividadePrincipal: any, atividade: any): any {

        if (idTipoAtividadePrincipal) {

            let tipoAtividadePrincipal = this.atividadePrincipal.filter((data) => {
                return data.id == idTipoAtividadePrincipal;
            });

            tipoAtividadePrincipal = tipoAtividadePrincipal.shift();

            atividade.id = tipoAtividadePrincipal.id;
            atividade.dataInicio = tipoAtividadePrincipal.dataInicio;
            atividade.dataFim = tipoAtividadePrincipal.dataFim;
            atividade.descricao = tipoAtividadePrincipal.descricao;

            this.reprocessaTiposAtividadePrincipal();

            /**
             * Exibir prazos ao selecionar Atividade principal se o prazo não está em exibição.
             */
            if(atividade.isExibeIncluirSubAtividade){        
                this.addPorAtividade(atividade);
            }            
        } else {
            atividade.dataFim = undefined;
            atividade.dataInicio = undefined;
            atividade.selecionada = false;
            this.reprocessaTiposAtividadePrincipal();
        }
    }

    /**
     * Reprocessa as Atividades Principais de acordo com as atividades Selecionadas.
     */
    public reprocessaTiposAtividadePrincipal(): any {
        this.atividadePrincipal.forEach(tipoAtividade => {
            let atividade = this.atividades.filter((data) => {
                return data.id == tipoAtividade.id;
            });

            tipoAtividade.selecionada = true;

            if (atividade.length == 0) {
                tipoAtividade.selecionada = false;
            }
        });
    }

    /**
     * Salva as Atividades.
     *
     * @param hasJustificativa
     */
    public salvarDados(hasJustificativa?: any) {
        let calendarioTO = {
            id: this.calendario.id,
            atividadesPrincipais: this.atividades,
            prazosExcluidos: this.prazosRemovidos,
            justificativa: this.justificativaAlteracao
        };

        if (this.isSituacaoEmPreenchimento()) {
            if (this.isPeriodoVigencia()) {
                if (hasJustificativa) {
                    this.modalRef.hide();
                }
                this.messageService.addConfirmYesNo('LABEL_PERIODO_VIGENCIA_MESSAGE', () => {
                    this.isConcluirCalendario = true;
                    this.gravarDados(calendarioTO, hasJustificativa);
                });
            } else {
                this.gravarDados(calendarioTO, hasJustificativa);
            }
        } else {
            this.gravarDados(calendarioTO, hasJustificativa);
        }
    }

    /**
     * Grava o dados efetivamente no banco de dados.
     *
     * @param calendarioTO
     * @param hasJustificativa
     */
    public gravarDados(calendarioTO: any, hasJustificativa?: any) {    
        this.calendarioClientService.salvarPrazos(calendarioTO).subscribe(() => {
            this.calendarioClientService.getCalendarioPorId(this.calendario.id).subscribe(calendario => {
                this.periodoSalvo.emit(calendario);
                this.messageService.addMsgSuccess('LABEL_DADOS_ALTERADOS_SUCESSO');
                this.reloadAtividadesPrazo();                
            });

            if (hasJustificativa && this.modalRef) {
                this.modalRef.hide();
            }

            if (this.isConcluirCalendario) {
                this.calendarioClientService.concluir(this.calendario).subscribe(data => {
                    this.calendario = data;
                    this.isConcluirCalendario = false;
                })
            }
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Recarrega os Prazos.
     */
    public reloadAtividadesPrazo(): any {
        this.calendarioClientService.getPrazosPorCalendario(this.calendario.id).subscribe(data => {
            this.atividades = data.atividadesPrincipais;
            this.processoArvore(this.atividades);
            /** Limpando array de prazos excluídos. */
            this.prazosRemovidos = [];
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**addPorAtividade
     *  Compara se teve alteração em algum registro da arvore, para apresentar modal de justificativa.
     *
     * @param atividades
     */
    public filterArvorePrazo(atividades: any): any {

        atividades.forEach(nivel => {

            if (nivel.id != undefined) {

                let nivelVigente = this.filtraArvorePrazoPorNiveis(nivel.nivelNumerico, nivel.nivelAtividadePrincipal, nivel.nivelIrmao, this.atividadesVigentes, undefined);

                if (nivelVigente != undefined) {

                    if (nivel.nivelNumerico == 1) {

                        if (nivelVigente.id != nivel.id) {
                            this.justificativaAlteracao.push({
                                'descricao': 'Alteração da ' + nivel.descricaoNivel + 'Atividade Principal',
                                'justificativa': ''
                            });
                        }

                    } else {

                        if (nivelVigente.descricaoAtividade != nivel.descricaoAtividade) {
                            this.justificativaAlteracao.push({
                                'descricao': 'Alteração do nome da ' + nivel.descricaoNivel + ' Atividade',
                                'justificativa': ''
                            });
                        }

                        if (nivelVigente.duracao != nivel.duracao) {
                            this.justificativaAlteracao.push({
                                'descricao': 'Alteração na duração da ' + nivel.descricaoNivel + ' Atividade',
                                'justificativa': ''
                            });
                        }

                        if (nivelVigente.situacaoDiaUtil != nivel.situacaoDiaUtil) {
                            this.justificativaAlteracao.push({
                                'descricao': 'Alteração no dia útil da ' + nivel.descricaoNivel + ' Atividade',
                                'justificativa': ''
                            });
                        }

                    }

                }
            }

            if (nivel.prazos != undefined && nivel.prazos.length != 0) {
                this.filterArvorePrazo(nivel.prazos);
            }
        });
    }

    /**
     * Busca pelo id na arvore informada.
     *
     * @param nivelNumerico
     * @param nivelAtividadePrincipal
     * @param nivelIrmao
     * @param prazos
     * @param prazoVigente
     */
    public filtraArvorePrazoPorNiveis(nivelNumerico: any, nivelAtividadePrincipal: any, nivelIrmao: any, prazos: any, prazoVigente: any): any {
        prazos.forEach(prazo => {
            if (prazoVigente == undefined) {
                if (prazo.nivelNumerico == nivelNumerico && prazo.nivelAtividadePrincipal == nivelAtividadePrincipal &&
                    prazo.nivelIrmao == nivelIrmao) {
                    prazoVigente = prazo;
                } else if (prazo.prazos != undefined && prazo.prazos.length != 0) {
                    prazoVigente = this.filtraArvorePrazoPorNiveis(nivelNumerico, nivelAtividadePrincipal, nivelIrmao, prazo.prazos, prazoVigente);
                }
            }
        });

        return prazoVigente;
    };

    /**
     * Abre a Modal de Justificativa.
     *
     * @param template
     */
    openModal(template: TemplateRef<any>) {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }))
    }

    /**
     * Salva a arvore de prazos.
     *
     * @param templateModal
     * @param form
     */
    public salvar(templateModal: any, form: NgForm): any {
        this.submitted = true;
        this.prazoControl.onSubmit.emit(this.submitted);

        if (form.valid && this.prazoControl.valid) {
            this.submitted = false;
            this.justificativaAlteracao = [];
            this.filterArvorePrazo(this.atividades);

            if (this.justificativaAlteracao.length != 0 && this.calendario.calendarioConcluido) {
                this.openModal(templateModal);
            } else {
                this.salvarDados();
            }
        }
    }

    /**
     * Método para verficar se a situação vigente do calendário é 'Em Preenchimento'.
     */
    public isSituacaoEmPreenchimento(): boolean {
        return this.calendario.situacaoVigente === undefined || this.calendario.situacaoVigente.descricao == this.preenchimento;
    }


    /**
     * Válida se o período do calendário está vigente.
     */
    public isPeriodoVigencia() {
        let agora = moment().toDate();
        let dataInicioVigencia = moment(this.calendario.dataInicioVigencia, 'YYYY-MM-DD').toDate();
        let dataFimVigencia = moment(this.calendario.dataFimVigencia, 'YYYY-MM-DD').toDate();
        return dataInicioVigencia <= agora && agora <= dataFimVigencia ? true : false;
    }

    /**
     * Método responsável por concluir o calendário.
     *
     * @param calendario
     * @param form
     */
    public concluirCalendario(calendario: any, form: NgForm): void {
        this.submitted = true;
        this.prazoControl.onSubmit.emit(this.submitted);

        if (form.valid && this.prazoControl.valid) {
            this.submitted = false;
            this.calendarioClientService.salvarPrazos(calendario).subscribe(() => {
                this.calendarioClientService.concluir(calendario).subscribe((calendarioConcluido) => {
                    calendarioConcluido.prazosDefinidos = true;
                    calendarioConcluido.cadastroRealizado = true;
                    calendarioConcluido.atividadesDefinidas = true;
                    calendarioConcluido.calendarioConcluido = true;

                    this.calendario = calendarioConcluido;
                    this.periodoSalvo.emit(calendarioConcluido);
                    this.messageService.addMsgSuccess('MSG_CALENDARIO_CONCLUIDO');
                }, error => {
                    this.messageService.addMsgDanger(error);
                });
            }, error => {
                this.messageService.addMsgDanger(error);
            });
        }
    }

    /**
     * Retorna se o calendário é inativo.
     */
    public isCalendarioInativo(): boolean {
        return !this.calendario.ativo;
    }

    /**
     * Verifica se o período de vigencia está ativo.
     *
     * @param idAtividade
     */
    public isPeriodoObedeceVigencia(idAtividade): boolean {
        let atividadeSelecionada = this.atividadePrincipal.filter(atividade => {
            return atividade.id == idAtividade;
        })

        atividadeSelecionada = atividadeSelecionada.shift();
        return atividadeSelecionada != undefined && !atividadeSelecionada.obedeceVigencia;
    }

    private ordenarAtividades(){
        if (Array.isArray(this.atividades)) {            
            for(let i= 0; i < this.atividades.length; i++){
                let o = this.atividades[i];
                if (Array.isArray(o.prazos)) {
                    o.prazos = o.prazos.sort((a, b) => this.compare(a,b));
                }
            }
        }        
    }

    private compare(a, b){
        let a1 = a.descricaoNivel.toString().split('.');
        let a2 = b.descricaoNivel.toString().split('.');
        let v1 = parseInt(a1[1]);
        let v2 = parseInt(a2[1]);
        return (v1 > v2) ? 1 : -1;
    }

}
