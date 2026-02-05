import * as _ from "lodash";
import * as moment from 'moment';
import * as deepEqual from "deep-equal";
import { NgForm } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap/modal';
import { MessageService } from '@cau/message';
import { Component, OnInit, Input, TemplateRef, EventEmitter, Output, ViewChild, ElementRef, AfterViewInit } from '@angular/core';
import { CalendarioClientService } from '../../../../../client/calendario-client/calendario-client.service';


/**
 * Componente de formulário de Cadastro de periodo de calendário.
 *
 * @author Squadra Tecnologia
 */
@Component({
    selector: 'form-cadastro-periodo-calendario',
    templateUrl: './form-cadastro-periodo-calendario.component.html'
})

export class FormCadastroPeriodoCalendarioComponent implements OnInit {

    public arquivo: any;
    public calendario: any;
    public tipoProcesso: any;
    public accordionsAtividades;
    public modalRef: BsModalRef;
    public atividades: any = [];
    public calendarioAnos: any[];
    public ativExcluirId: number;
    public anoReplicar: any[] = [];
    public ativSubExcluirId: number;
    public resolucaoExcluir: any = {};
    public arquivosExcluidos: any[] = [];
    public indicesAtividadesDuplicadas = [];
    public ativarCalendario: boolean = false;
    public isDisableIesFlag: boolean = false;
    public indicesSubatividadesDuplicadas = [];
    public isInsercaoAtividades: boolean = false;
    public isCalendarioReplicado: boolean = false;
    public atividadesPrincipaisExcluidas: any[] = [];
    public flagAcionarItem1Atividades: boolean = false;
    public subAtividadesPrincipaisExcluidas: any[] = [];

    /**responsavel pelo ng2 Select */
    public dropdownList = [];
    public selectedItems = [];
    public dropdownSettings = {};

    public aba: string;
    public justificativa: any;
    public submitted: boolean;
    public atividadesPeriodo: any;
    public periodo: boolean = false;
    public atividadesPrazoAlteracao: any;
    public atividadesPeriodoAlteracao: any;
    public concluirCalendario: boolean = false;
    public preenchimento: string = "Em preenchimento";

    @Input() inputCalendario;
    @Input() calendarioClone;
    @Input() acaoSistema;
    @Input() isAbaPrazo;
    @Output() next: EventEmitter<any>;
    @Output() periodoSalvo: EventEmitter<any>;
    @Output('updateClone') onUpdateClone: EventEmitter<any>;

    public isDropUfMultSelectUf: boolean = false;
    @ViewChild('cardBodyFormCalendario', { static: false }) cardBodyFormCalendario: ElementRef;

    /**
     * Construtor da classe.
     *
     * @param route
     * @param calendarioClientService
     * @param messageService
     * @param modalService
     */
    constructor(route: ActivatedRoute,
        private calendarioClientService: CalendarioClientService,
        private messageService: MessageService,
        private modalService: BsModalService,
        private router: Router) {
        this.next = new EventEmitter();
        this.periodoSalvo = new EventEmitter();
        this.onUpdateClone = new EventEmitter();
    }

    /**
     * Inicializa o Component.
     */
    ngOnInit() {
        this.accordionsAtividades = [];

        if (this.inputCalendario.atividadesPrincipais === undefined) {
            this.isInsercaoAtividades = true;
        }

        if (this.inputCalendario.atividadesPrincipais !== undefined && this.inputCalendario.atividadesPrincipais.length === 0) {
            this.atividades = [];
            this.isInsercaoAtividades = true;
        }

        this.buscarCauUfsServiceAPI();
        this.carregarUfs();
        this.getListaAnos();
        this.getListaTipoProcessoEleicao();
        this.setStatusEleicaoAtivoOuInativo();
        this.acaoIncluirCriarObjCalendario();

        this.atividadesPeriodo = _.cloneDeep(this.inputCalendario.atividadesPrincipais);
        this.arquivosExcluidos = [];

        if (this.inputCalendario && this.inputCalendario.id) {

            if (this.inputCalendario.atividadesPrincipais && this.inputCalendario.atividadesPrincipais.length > 0) {
                let key = 0;
                this.inputCalendario.atividadesPrincipais.forEach(atividade => {
                    this.accordionsAtividades[key++] = true;
                });
            } else {
                this.criarAtividade();
            }
        }
        this.onUpdateClone.emit(_.cloneDeep(this.calendario));
    }

    public acaoIncluirCriarObjCalendario() {
        let testIncluir = this.isAcaoIncluir()
        testIncluir ? this.calendarioClone = { "id": 0, "ativo": true, "tipoprocesso": { "descricao": "" } } : false;
    }

    public carregarUfs() {
        this.selectedItems = [];
        this.dropdownSettings = {
            singleSelection: false,
            idField: 'id',
            textField: 'prefixo',
            selectAllText: 'Selecione Todos',
            unSelectAllText: 'Remove Todos',
            itemsShowLimit: 5,
            allowSearchFilter: true,
            searchPlaceholderText: 'Buscar',
            maxHeight: 145, // Para que todas as opções estejam visíveis na tela
        };
    }

    public buscarCauUfsServiceAPI() {
        this.calendarioClientService.buscarCauUfsServiceAPI().subscribe(data => {
            this.dropdownList = _.orderBy(data, ['prefixo'], ['asc']);
            this.getCauUF(this.inputCalendario.cauUf);
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * Verificar qual status esta o calendario ativo ou inativo
     */
    public setStatusEleicaoAtivoOuInativo() {
        this.inputCalendario.ativo = this.inputCalendario.id ? this.inputCalendario.ativo : true;
    }

    /**
     * Ao buscar as cauf os mesmo tem que coloca-los em um objecto chamado
     * selectedItems array
     * @param cauUf array cauf
     */
    public getCauUF(cauUf: any): void {
        let escopo = this;
        escopo.selectedItems = [];
        _.forEach(cauUf, function (arrayUf) {
            _.forEach(escopo.dropdownList, function (listUf) {
                if (arrayUf.idCauUf === listUf.id) {
                    escopo.selectedItems.push(listUf);
                }
            });
        });
    }

    /**
     * Carrega nos calendarios as UF selecionadas em uma lista
     * que sera consumida via API
     * @param arraySelectCauUf array
     * @param calendario array
     */
    public getCarregarCauUfSelecionadas(arraySelectCauUf: any): void {
        let escopo = this;
        this.inputCalendario.cauUf == undefined ? this.inputCalendario.cauUf = [] : false;
        _.forEach(arraySelectCauUf, function (arraySelectCauUf) {
            _.forEach(escopo.dropdownList, function (listUf) {
                if (arraySelectCauUf.idCauUf === listUf.id) {
                    this.inputCalendario.cauUf.push(listUf);
                }
            });
        });
    }

    /**
     * Busca Lista Anos de calendarios
     */
    public getListaAnos(): void {
        this.calendarioClientService.getCalendariosAno().subscribe(data => {
            data = _.orderBy(data, ['eleicao'], ['desc']);
            this.calendarioAnos = data;
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
     * Metodo de start da importação de um calendario ja criado
     * anteriorment
     * @param event event acionado
     */
    public replicarCalendario(event) {
        if (this.anoReplicar.length > 0) {
            if (this.isCamposCalendarioPreechidos()) {
                this.messageService.addConfirmYesNo('LABEL_REPLICAR_CALENDARIO_MESSAGE', () => {
                    this.acaoEfetivaReplicarCalendario();
                });
            } else {
                this.acaoEfetivaReplicarCalendario();
            }
        } else {
            this.messageService.addMsgWarning('LABEL_SELECIONAR_EXCECAO');
        }
    }

    /**
     * Quando não ha mudanças essa ação e disparada diretamente
     * Ao chamar o replicar ou importar como esta na view
     * o mesmo valida os elementos da tela e caso tenha mudança
     * o mesmo aciona um confirm ao confirmar este metodo sera
     * ativado para efetivar a importacao
     */
    public acaoEfetivaReplicarCalendario() {
        let obj = this.calendarioAnos.filter((data) => JSON.stringify(data).toLowerCase().indexOf(this.anoReplicar.toString().toLowerCase()) !== -1);
        undefined !== obj ? this.getCalendarioReplicar(obj[0].id) : false;
    }

    /**
     * Válida se algum campo do calendário foi preenchido.
     */
    public isCamposCalendarioPreechidos(): boolean {
        let isCamposPreenchidos = false;

        if (this.inputCalendario.ano !== undefined && this.inputCalendario.ano !== '' && this.inputCalendario.ano !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.dataFimMandato !== undefined && this.inputCalendario.dataFimMandato !== '' && this.inputCalendario.dataFimMandato !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.dataFimVigencia !== undefined && this.inputCalendario.dataFimVigencia !== '' && this.inputCalendario.dataFimVigencia !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.dataInicioMandato !== undefined && this.inputCalendario.dataInicioMandato !== '' && this.inputCalendario.dataInicioMandato !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.dataInicioVigencia !== undefined && this.inputCalendario.dataInicioVigencia !== '' && this.inputCalendario.dataInicioVigencia !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.tipoProcesso !== undefined && this.inputCalendario.tipoProcesso !== "undefined" && this.inputCalendario.tipoProcesso !== '' && this.inputCalendario.tipoProcesso !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.idadeInicio !== undefined && this.inputCalendario.idadeInicio !== '' && this.inputCalendario.idadeInicio !== null) {
            isCamposPreenchidos = true;
        }

        if (this.inputCalendario.idadeFim !== undefined && this.inputCalendario.idadeFim !== '' && this.inputCalendario.idadeFim !== null) {
            isCamposPreenchidos = true;
        }

        if (this.arquivo !== undefined && this.arquivo !== '' && this.arquivo !== null) {
            isCamposPreenchidos = true;
        }

        if (this.selectedItems !== undefined && this.selectedItems.length > 0) {
            isCamposPreenchidos = true;
        }

        return isCamposPreenchidos;
    }

    /**
     * Pelo Id do calendario o sistema deve buscar o
     * calendario referente a esse Id
     * @param id number
     */
    public getCalendarioReplicar(id: number) {
        this.calendarioClientService.getCalendarioPorId(id).subscribe(data => {
            this.inputCalendario = data;
            this.inputCalendario.idadeFim = data.idadeFim.toString();
            this.inputCalendario.idadeInicio = data.idadeInicio.toString();
            this.inputCalendario.situacaoVigente.descricao = this.preenchimento;

            delete this.inputCalendario.id;
            delete this.inputCalendario.prazosDefinidos;
            delete this.inputCalendario.cadastroRealizado;
            delete this.inputCalendario.atividadesDefinidas;
            delete this.inputCalendario.calendarioConcluido;

            this.carregarUfs();
            this.getCauUF(data.cauUf);

            let key = 0;
            this.inputCalendario.atividadesPrincipais.forEach((atividade: any) => {
                delete atividade.id;
                atividade.dataFim = '';
                atividade.dataInicio = '';
                this.accordionsAtividades[key++] = true;

                if (atividade.prazos) {
                    atividade.prazos.map((prazo: any) => {
                        prazo.id = undefined;
                    });
                }

                if (atividade.atividadesSecundarias) {
                    atividade.atividadesSecundarias.forEach(atividadeSecundaria => {
                        delete atividadeSecundaria.id;
                        delete atividadeSecundaria.corpoEmails;
                        delete atividadeSecundaria.informacaoComissaoMembro;
                        atividadeSecundaria.dataInicio = '';
                        atividadeSecundaria.dataFim = '';
                    });
                }
            });
            this.isCalendarioReplicado = true;
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * Ao selecionar o ativar o mesmo deve exibir um confirm
     * para evitar erros ou um clique
     * errado , assim o mesmo deve confirmar se deseja mesmo desativar
     * o calendario em ação na view
     * @param template
     * @param event
     */
    public onAtivarDesativarCalendario(event: any) {
        this.messageService.addConfirmYesNo('LABEL_INATIVAR_CALENDARIO', () => {
            this.confirmInativar()
        }, () => {
            this.inputCalendario.ativo = true
        });
    }

    /** mantido para acoes posteriores */
    public onItemSelect(item: any) {
        !this.inputCalendario.cauUf ? this.inputCalendario.cauUf = [] : false;
        let cauUf = { 'idCauUf': item.id };
        this.inputCalendario.cauUf.push(cauUf)
    }

    public onRemoveItemSelect(item: any) {
        this.inputCalendario.cauUf = _.remove(this.inputCalendario.cauUf, function (cau) {
            return cau['idCauUf'] !== item.id
        });
    }

    /**
     * Define a ação que será executada quando selecionar o campo todos do dropdown.
     */
    public onSelectAll(): void {
        let escopo = this;

        if (escopo.inputCalendario.cauUf == undefined) {
            escopo.inputCalendario.cauUf = [];
        }

        _.forEach(this.dropdownList, function (listUf) {
            let cauUf = { 'idCauUf': listUf.id };
            escopo.inputCalendario.cauUf.push(cauUf)
        });
    }

    public onDeSelectAll() {
        this.inputCalendario.cauUf = []
    }

    /**
     * metodo criado para a validação do
     * arquivo ao fazer o Upload
     * @param arquivoEvent
     * @param calendario
     */
    public uploadDocumento(arquivoEvent): void {
        let arquivoTO = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size };
        let arquivoUpload = { "nome": arquivoEvent.name, "tamanho": arquivoEvent.size, 'arquivo': arquivoEvent }
        !this.inputCalendario.arquivos ? this.inputCalendario.arquivos = [] : false;

        if (this.inputCalendario.arquivos.length < 2) {
            this.calendarioClientService.validarAnexoPDF(arquivoTO).subscribe(() => {
                this.inputCalendario.arquivos.push(arquivoUpload);
                this.arquivo = arquivoEvent.name;
            }, error => {
                this.messageService.addMsgDanger(error);
            });
        } else {
            this.messageService.addMsgDanger('LABEL_QUANTIDADE_MAX_PERMITIDA_ARQUIVOS');
        }
    }

    /**
    * Recupera o arquivo conforme a entidade 'resolucao' informada.
    *
    * @param event
    * @param resolucao
    */
    public downloadResolucao(event: EventEmitter<any>, resolucao: any): void {
        if (resolucao && resolucao.id > 0) {
            this.calendarioClientService.downloadArquivo(resolucao.id).subscribe((data: Blob) => {
                event.emit(data);
            }, error => {
                this.messageService.addMsgDanger(error);
            });
        } else {
            event.emit(resolucao);
        }
    }

    /**
     * Ao  acionar o select de tipo de processo o mesmo e ativao
     * sua função e buscar o proce
     * @param event
     */
    public changeProcesso(event: any) {
        if (this.inputCalendario.eleicao.tipoProcesso && this.inputCalendario.eleicao.tipoProcesso.id) {
            let array = this.tipoProcesso.filter((data) => JSON.stringify(data).toLowerCase().indexOf(this.inputCalendario.eleicao.tipoProcesso.id.toString().toLowerCase()) !== -1);
            this.inputCalendario.eleicao.tipoProcesso.id = array[0].id;
            this.inputCalendario.eleicao.tipoProcesso.descricao = array[0].descricao;
            array[0].id === 1 ? this.desabilitarCauUfAndIes(true) : this.desabilitarCauUfAndIes(false);
        } else {
            this.desabilitarCauUfAndIes(false)
        }
    }

    /**
     * Experiemental o mesmo foi testado
     * porem resolvido em view
     * @param event event
     */
    public changeSituacaoIes(event: any) {
        this.selectedItems = [];
        this.inputCalendario.cauUf = [];
        let bool = event.target.value === 'true' ? true : false;
    }

    /**
     * Caso o usuario selecione o Processo o mesmo
     * desabilita ou habilita e
     * chama no caso de true chama o preenchimento da
     * UFS no select de UF
     * @param flag boolean
     */
    public desabilitarCauUfAndIes(flag: boolean) {
        this.isDisableIesFlag = flag;
        flag ? this.preencherCauUFAndIes() : this.retirarCauUFAndIes();
    }

    /**metodo criado quando o usuario clicar em tipo de processo ExtraOrdinario o mesmo
    */
    public retirarCauUFAndIes() {
        this.inputCalendario.situacaoIES = undefined;
        this.selectedItems = [];
        this.inputCalendario.cauUf = [];

    }
    /**metodo criado quando o usuario clicar em tipo de processo ordinario o mesmo
     * preenche SIM na situaçãoc e desativa cauf totalmente preenchido
     */
    public preencherCauUFAndIes() {
        this.inputCalendario.situacaoIES = true;
        let escopo = this;

        //"cauUf": [{"idCauUf":8}, {"idCauUf":9}, {"idCauUf":10}],
        this.inputCalendario.cauUf = [];

        /**
         * prrenchendo o select com as UFs e carregando obj com os mesmo para
         * enviar para API
         */
        this.selectedItems = [];
        _.forEach(this.dropdownList, function (listUf) {
            escopo.selectedItems.push(listUf);
            let cauUf = { 'idCauUf': listUf.id };
            escopo.inputCalendario.cauUf.push(cauUf)
        });
    }

    /* elementos que busca pela acao do sistema qual esta vigente*/
    public isAcaoVisualizar() {
        return this.acaoSistema !== undefined ? this.acaoSistema.acao === 'visualizar' : false;
    }

    public isAcaoAlterar() {
        return this.acaoSistema !== undefined ? this.acaoSistema.acao === 'alterar' : false;
    }

    public isAcaoIncluir() {
        return this.acaoSistema !== undefined ? this.acaoSistema.acao === 'incluir' : false;
    }

    public isStatusCalendario() {
        if (!this.inputCalendario.id) {
            return false;
        } else {
            return !this.inputCalendario.ativo;
        }
    }
    /** fim acao sistema */


    /**
     * Confirmacao do Inativar Calendario
     * uma vez chamado esse metodo não ha possibilidade de ativação do mesmo
     * o botao sera desativado em view
     */
    public confirmInativar() {
        this.calendarioClientService.inativarCalendarioPorId(this.inputCalendario).subscribe(data => {
            this.inputCalendario = data;
            this.messageService.addMsgSuccess('LABEL_DADOS_ALTERADOS_SUCESSO');
        }, error => {
            this.messageService.addMsgDanger(error);
        })
    }

    /**
     * caso o usuario desista de inativar um calendario
    */
    public decline() {
        this.ativarCalendario = true;
        this.modalRef.hide()
    }

    /**
     * desabilitar o select de Situações IES
     * ao selecionar no campo tipo de Processo
     * o tipo de processo Ordinario
     */
    public isDisableIes() {
        if (this.isStatusCalendario()) {
            return true;
        } else {
            if (this.isAcaoVisualizar()) {
                return true;
            } else {
                if (this.inputCalendario.eleicao.tipoProcesso && this.inputCalendario.eleicao.tipoProcesso.id === 2) {
                    return false;
                } else
                    if (this.inputCalendario.eleicao.tipoProcesso && this.inputCalendario.eleicao.tipoProcesso.id === 1) {
                        return true;
                    } else {
                        return (this.inputCalendario.situacaoIES || this.inputCalendario.situacaoIES === 'true') === "true" ? true : false;
                    }
            }
        }
    }


    /**
     * desabilitar o select de Cau UFs ou Estados
     * ao selecionar no campo tipo de Processo
     * o tipo de processo Ordinario
     */
    public isDisableCauUf() {
        if (this.isStatusCalendario()) {
            return true;
        } else {
            if (this.isAcaoVisualizar()) {
                return true;
            } else {
                if (!this.inputCalendario.id && !this.inputCalendario.eleicao.tipoProcesso) {
                    return false;
                } else {
                    if (this.inputCalendario.eleicao.tipoProcesso && this.inputCalendario.eleicao.tipoProcesso.id === 2) {
                        return String(this.inputCalendario.situacaoIES || this.inputCalendario.situacaoIES === 'true') === 'true';
                    } else
                        if (this.inputCalendario.eleicao.tipoProcesso && this.inputCalendario.eleicao.tipoProcesso.id === 1) {
                            return true;
                        } else {
                            return (this.inputCalendario.situacaoIES || this.inputCalendario.situacaoIES === 'true') === "false" ? false : true;
                        }
                }
            }
        }
    }

    public deleteArquivo(resolucao) {
        this.resolucaoExcluir = {};
        this.resolucaoExcluir = resolucao;
        this.messageService.addConfirmYesNo('LABEL_EXCLUIR_ARQUIVO', () => { this.confirmExcluirArquivo(), () => { this.declineExcluirArquivo() } })
    }

    public confirmExcluirArquivo() {
        let escopo = this;

        if (this.resolucaoExcluir.id) {
            this.inputCalendario.arquivos = _.remove(this.inputCalendario.arquivos, function (listFile) {
                return listFile['id'] !== escopo.resolucaoExcluir.id
            });
            this.arquivosExcluidos.push(this.resolucaoExcluir);
        } else {
            this.inputCalendario.arquivos = _.remove(this.inputCalendario.arquivos, function (listFile) {
                return listFile['nome'] !== escopo.resolucaoExcluir.nome
            });
        }

        this.arquivo = '';
    }

    public declineExcluirArquivo() {
        this.resolucaoExcluir = {}
    }

    /**Controladores das atividades */
    /**
     * Cria uma nova atividade ao calendário.
     */
    public criarAtividade() {
        /**Nao permitir que o atividades  de erro por undefined */
        if (!this.inputCalendario.atividadesPrincipais) {
            this.inputCalendario.atividadesPrincipais = [];
        }
        this.accordionsAtividades[this.inputCalendario.atividadesPrincipais.length] = true;
        let nivel = this.inputCalendario.atividadesPrincipais.length + 1

        let atividade = {
            "idCalendario": this.inputCalendario.id,
            "nivel": nivel,
            "obedeceVigencia": true,
            "atividadesSecundarias": []
        };

        this.inputCalendario.atividadesPrincipais.push(atividade);
    }

    /**
     * Exclui uma atividade por key informada no click.
     *
     * @param keyAtiv
     */
    public excluirAtividade(keyAtiv) {
        this.ativExcluirId = keyAtiv;
        this.messageService.addConfirmYesNo('LABEL_EXCLUIR_ATIVIDADE', () => { this.confirmExcluirAtividade() })
    }

    /**
     * cria uma nova atividade secundária a uma atividade.
     *
     * @param keyAtiv
     */
    public criarAtividadeSecundaria(event, keyAtiv) {
        if (!this.inputCalendario.atividadesPrincipais[keyAtiv].atividadesSecundarias) {
            this.inputCalendario.atividadesPrincipais[keyAtiv].atividadesSecundarias = []
        }

        let atividadeSecundaria = {
            "nivel": this.inputCalendario.atividadesPrincipais[keyAtiv].atividadesSecundarias.length + 1
        };
        this.inputCalendario.atividadesPrincipais[keyAtiv].atividadesSecundarias.push(atividadeSecundaria);
    }

    /**
     * Exclui uma Atividade Secundária de uma Atividade.
     * 
     * @param ativ_key
     * @param sub_key
     */
    public excluirAtividadeSecundaria(ativ_key: number, sub_key: number) {
        this.ativExcluirId = ativ_key;
        this.ativSubExcluirId = sub_key;
        this.messageService.addConfirmYesNo('LABEL_EXCLUIR_SUB_ATIVIDADE', () => { this.confirmExcluirSubAtividade() })
    }

    /**
     * metodo SIM do confirmDialog
     * o mesmo refere-se a exclusao de uma Atvidade
     */
    confirmExcluirAtividade(): void {
        let sizeAtividades = this.inputCalendario.atividadesPrincipais.length;
        let atividade = this.inputCalendario.atividadesPrincipais[this.ativExcluirId];

        // Quando for o último elemento
        if (this.ativExcluirId == sizeAtividades - 1) {

            if (atividade.atividadesSecundarias) {
                atividade.atividadesSecundarias.forEach(atividadeSecundaria => {
                    this.subAtividadesPrincipaisExcluidas.push(atividadeSecundaria);
                });
            }

            this.atividadesPrincipaisExcluidas.push(atividade);
            this.inputCalendario.atividadesPrincipais.pop();

        } else { // Quando não for o último elemento
            atividade.dataFim = "";
            atividade.descricao = "";
            atividade.dataInicio = "";

            atividade.atividadesSecundarias.forEach(atividadeSecundaria => {
                this.subAtividadesPrincipaisExcluidas.push(atividadeSecundaria);
            });

            atividade.atividadesSecundarias = [];
        }
    }

    /**
    * metodo SIM do confirmDialog
    * o mesmo refere-se a exclusao de uma Atvidade
    */
    confirmExcluirSubAtividade(): void {
        let sizeSubAtividades = this.inputCalendario.atividadesPrincipais[this.ativExcluirId].atividadesSecundarias.length;
        let subAtividade = this.inputCalendario.atividadesPrincipais[this.ativExcluirId].atividadesSecundarias[this.ativSubExcluirId];

        // Quando for o último elemento
        if (this.ativSubExcluirId == sizeSubAtividades - 1) {

            if (subAtividade.id) {
                this.subAtividadesPrincipaisExcluidas.push(subAtividade);
            }
            this.inputCalendario.atividadesPrincipais[this.ativExcluirId].atividadesSecundarias.pop();

        } else { // Quando não for o último elemento
            subAtividade.descricao = "";
            subAtividade.dataInicio = "";
            subAtividade.dataFim = "";
        }

        this.limparExcluirAtividades();
    }

    public limparExcluirAtividades() {
        this.ativExcluirId = -1;
        this.ativSubExcluirId = -1;
    }

    /**
     * Método responsável por salvar o cadastro da primeira estapa do peridodo.
     *
     * @param template
     * @param form
     * @param calendario
     */
    public salvarCadastroPeriodo(template: TemplateRef<any>, form: NgForm, calendario: any, rascunho: boolean = false) {
        this.submitted = true;

        if (rascunho || form.valid) {
            this.inputCalendario.rascunho = rascunho;

            if (this.inputCalendario.id) {
                this.submitted = false;
                if (this.isSituacaoEmPreenchimento()) {

                    if (this.isPeriodoVigencia()) {
                        this.messageService.addConfirmYesNo('LABEL_PERIODO_VIGENCIA_MESSAGE', () => {
                            this.concluirCalendario = true;
                            this.validarAtividadesESubatividadesRepetidas();
                        });
                    } else {
                        this.validarAtividadesESubatividadesRepetidas();
                    }

                } else {
                    this.atividadesPeriodoAlteracao = [];
                    let alterouInformacoesIniciaisAlteradas = this.isInformacoesIniciaisAlteradas();
                    let alterouAtividades = this.isVerificarAlteracaoExclusaoPeriodoAtividades();
                    if (alterouInformacoesIniciaisAlteradas || alterouAtividades) {
                        this.aba = this.messageService.getDescription('LABEL_ABA_PERIODO');
                        this.modalRef = this.modalService.show(
                            template,
                            Object.assign({}, { class: 'modal-calendario' })
                        );
                    } else if (this.isPeriodoVigencia() && !this.isInformacoesIniciaisAlteradas() && !this.isVerificarAlteracaoExclusaoPeriodoAtividades()) {
                        this.messageService.addConfirmYesNo('LABEL_PERIODO_VIGENCIA_MESSAGE', () => {
                            this.validarAtividadesESubatividadesRepetidas();
                        });
                    } else {
                        this.validarAtividadesESubatividadesRepetidas();
                    }

                }

            } else if (this.inputCalendario.atividadesPrincipais && this.inputCalendario.atividadesPrincipais.length > 0) {
                // Caso o usuário tenha importado os dados de outro calendário e existam 'Atividades Principais/Secundárias'.
                this.validarAtividadesESubatividadesRepetidas();
            } else {
                this.save();
            }
        }
    }

    /**
     * Válida se as informações iniciais do calendário foram alteradas.
     */
    public isInformacoesIniciaisAlteradas(): boolean {
        let nivel = 0;

        if (!deepEqual(this.calendarioClone.ano, this.inputCalendario.ano)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Ano da Eleição" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.dataInicioVigencia, this.inputCalendario.dataInicioVigencia)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Período de Vigência - Data Início" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.dataFimVigencia, this.inputCalendario.dataFimVigencia)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Período de Vigência - Data Fim" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.tipoProcesso, this.inputCalendario.tipoProcesso)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Tipo de Processo" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.idadeInicio, this.inputCalendario.idadeInicio)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Idade do Voto Obrigatório - Início" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.idadeFim, this.inputCalendario.idadeFim)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Idade do Voto Obrigatório - Fim" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.dataInicioMandato, this.inputCalendario.dataInicioMandato)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Período de Mandato - Data Início" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.dataFimMandato, this.inputCalendario.dataFimMandato)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Período de Mandato - Data Fim" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.situacaoIES, this.inputCalendario.situacaoIES)) {
            let infoInicial = { "nivel": nivel++, "descricao": "IES" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.arquivos, this.inputCalendario.arquivos)) {
            let infoInicial = { "nivel": nivel++, "descricao": "Resolução" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }
        if (!deepEqual(this.calendarioClone.cauUf, this.inputCalendario.cauUf)) {
            let infoInicial = { "nivel": nivel++, "descricao": "CAU/UF" };
            this.atividadesPeriodoAlteracao.push(infoInicial);
        }

        return this.atividadesPeriodoAlteracao.length === 0 ? false : true;
    }

    /**
     * Método para verficar se a situação vigente do calendário é 'Em Preenchimento'.
     */
    public isSituacaoEmPreenchimento(): boolean {
        return this.inputCalendario.situacaoVigente === undefined || this.inputCalendario.situacaoVigente.descricao == this.preenchimento;
    }

    /**
     * Válida se o período do calendário está vigente.
     */
    public isPeriodoVigencia() {
        let agora = moment().startOf('day').toDate();
        let dataInicioVigencia = moment(this.inputCalendario.dataInicioVigencia, 'YYYY-MM-DD').toDate();
        let dataFimVigencia = moment(this.inputCalendario.dataFimVigencia, 'YYYY-MM-DD').toDate();

        return dataInicioVigencia <= agora && agora <= dataFimVigencia ? true : false;
    }

    /**
     * Exibe a mensagem de sucesso quando o cadastro foi realizado.
     *
     * @param flagAcionarItem1Atividades
     * @param eleicaoDescricao
     */
    public exibirMessagemSalvamento(flagAcionarItem1Atividades, eleicaoDescricao: string) {
        flagAcionarItem1Atividades ?
            this.messageService.addMsgSuccess('LABEL_DADOS_INCLUIDOS_SUCESSO_ELEICAO', [eleicaoDescricao]) :
            this.messageService.addMsgSuccess('LABEL_DADOS_ALTERADOS_SUCESSO');
    }


    /**
     * Verificar se houve mudança nas Atividades e SubAtividades
    */
    public isVerificarAlteracaoExclusaoPeriodoAtividades(): boolean {
        if (!deepEqual(this.atividadesPeriodo, this.inputCalendario.atividadesPrincipais)) {
            if (this.atividadesPrincipaisExcluidas.length > 0 || this.subAtividadesPrincipaisExcluidas.length > 0) {
                this.incluirExcluidosLista()
            }
            this.incluirAlteradosLista();
        } else {
            return false;
        }

        this.atividadesPeriodoAlteracao = this.atividadesPeriodoAlteracao.length > 0 ? _.sortBy(this.atividadesPeriodoAlteracao, [{ 'nivel': 'desc' }, { 'descricao': 'desc' }]) : this.atividadesPeriodoAlteracao;
        return this.atividadesPeriodoAlteracao.length === 0 ? false : true;
    }

    /**
     * Adiciona os elementos excluidos na lista de justificativas.
     */
    public incluirExcluidosLista() {
        let subAtividadesAlteracao = [];

        this.atividadesPeriodo.forEach(atividadePeriodo => {
            this.atividadesPrincipaisExcluidas.forEach(atividadeExcluida => {
                if (atividadePeriodo.id === atividadeExcluida) {
                    let objAtividadeRemovida = {
                        justificativa: '',
                        descricao: 'Exclusão da ' + atividadeExcluida.nivel + ' Atividade Principal - ' + atividadeExcluida.descricao,
                    };

                    this.atividadesPeriodoAlteracao.push(objAtividadeRemovida);
                }
            });
        });

        this.inputCalendario.atividadesPrincipais.forEach(function (atividade) {
            subAtividadesAlteracao.push(...atividade.atividadesSecundarias);
        });

        subAtividadesAlteracao.forEach(subAtividade => {
            this.subAtividadesPrincipaisExcluidas.forEach(subAtividadeRemovida => {
                if (subAtividade.id === subAtividadeRemovida) {

                    let objSubAtividadeRemovida = {
                        justificativa: '',
                        descricao: 'Exclusão da ' + subAtividadeRemovida.nivel + ' Atividade Secundária - ' + subAtividadeRemovida.descricao,
                    };

                    this.atividadesPeriodoAlteracao.push(objSubAtividadeRemovida);
                }
            });
        });
    }

    /**
     * Adiciona as alterações que aconteceram na lista de atividades e subatividades.
     */
    public incluirAlteradosLista() {
        let subAtividades = [];
        let subAtividadesAlteracao = [];

        if (this.atividadesPeriodo) {
            this.atividadesPeriodo.forEach(function (atividade) {
                if (atividade.atividadesSecundarias) {
                    subAtividades.push(...atividade.atividadesSecundarias);
                }
            });
        }

        this.inputCalendario.atividadesPrincipais.forEach(function (atividade) {
            if (atividade.atividadesSecundarias) {
                subAtividadesAlteracao.push(...atividade.atividadesSecundarias);
            }
        });

        if (this.atividadesPeriodo) {
            this.atividadesPeriodo.forEach(atividade => {
                this.inputCalendario.atividadesPrincipais.forEach(atividadeAtualizacao => {
                    if (atividade.id === atividadeAtualizacao.id) {
                        this.addAtividadePeriodoAlteracao(atividade, atividadeAtualizacao, 'Atividade Principal');
                    }
                });
            });
        }

        subAtividades.forEach(subAtividade => {
            subAtividadesAlteracao.forEach(subAtividadeAlteracao => {
                if (subAtividade.id === subAtividadeAlteracao.id && !deepEqual(subAtividade, subAtividadeAlteracao)) {
                    this.addAtividadePeriodoAlteracao(subAtividade, subAtividadeAlteracao, 'Atividade Secundaria');
                }
            });
        });
    }

    /**
     * Percorrer o array de Atividades que houve mudanca
     * e criar o objecto que sera persistido
     * @param calendario
     */
    public salvarJustificativa(form: NgForm) {
        this.submitted = true;
        this.periodo = false;
        this.justificativa = []

        let escopo = this;
        if (this.atividadesPeriodoAlteracao !== []) {
            _.forEach(this.atividadesPeriodoAlteracao, function (valueA) {
                let just = {
                    'descricao': valueA.descricao,
                    'justificativa': valueA.justificativa
                };

                escopo.justificativa.push(just);
            });
        }
        this.inputCalendario.justificativa = [];
        this.inputCalendario.justificativa = this.justificativa;
        if (form.valid) {
            this.modalRef.hide();
            if (this.isPeriodoVigencia()) {
                this.messageService.addConfirmYesNo('LABEL_PERIODO_VIGENCIA_MESSAGE', () => {
                    this.save();
                });
            } else {
                this.save();
            }
        }
    }

    /**
     * Método responsável por salvar os dados da aba de 'periodo' de calendário.
     */
    public save() {
        this.flagAcionarItem1Atividades = !this.inputCalendario.id ? true : false;
        let exibirMensagemInserir = !this.inputCalendario.id || this.isInsercaoAtividades ? true : false;

        this.inputCalendario.arquivosExcluidos = this.arquivosExcluidos;
        this.inputCalendario.isCalendarioReplicado = Number(this.isCalendarioReplicado);
        this.inputCalendario.atividadesPrincipaisExcluidas = this.atividadesPrincipaisExcluidas;
        this.inputCalendario.subAtividadesPrincipaisExcluidas = this.subAtividadesPrincipaisExcluidas;

        if (this.validarArquivoObrigatorio()) {
            this.calendarioClientService.salvarDadosPeriodo(this.inputCalendario).subscribe(data => {
                this.exibirMessagemSalvamento(exibirMensagemInserir, data.eleicao.descricao);

                if (!this.inputCalendario.id) {
                    this.router.navigate(['calendario', data.id, 'alterar']);
                } else {
                    this.calendarioClientService.getCalendarioPorId(data.id).subscribe(data => {
                        this.inputCalendario = data;

                        if (this.concluirCalendario) {
                            this.calendarioClientService.concluir(this.inputCalendario).subscribe(data => {
                                this.inputCalendario = data;
                                this.concluirCalendario = false;
                                this.indicesAtividadesDuplicadas = [];
                                this.indicesSubatividadesDuplicadas = [];
                            })
                        }
                    });
                }

                this.arquivosExcluidos = [];
            }, error => {
                this.messageService.addMsgDanger(error);
            });
        }
    }

    /**
     * Verificar se o arquivo que e obrigatorio foi inserido
     * no calendario a ser submetido
    */
    public validarArquivoObrigatorio() {
        let arquivoExists = false;
        arquivoExists = (this.inputCalendario.arquivos && this.inputCalendario.arquivos.length > 0);

        if (!arquivoExists) {
            this.messageService.addMsgDanger('LABEL_VALIDAR_ARQUIVO_OBRIGATORIO');
        }

        return arquivoExists;
    }

    /**
     * Ao clicar no salvar o objecto e criado e retorna o id
     * porem a regra me obriga a criar uma atividade vazia no
     * cadastro de atvidades
     * @param flagAcionarItem1Atividades boolean
     */
    public renovarAtividadesSubatividadesLimpezaVariaveis(flagAcionarItem1Atividades) {

        if (flagAcionarItem1Atividades) {
            this.inputCalendario.atividadesPrincipais = [];
            let ativ = { "dataInicio": '', "dataFim": '', "descricao": '', "obedeceVigencia": true, "nivel": 1 }
            this.inputCalendario.atividadesPrincipais.push(ativ);
        } else {
            this.atividadesPeriodo = _.cloneDeep(this.inputCalendario.atividadesPrincipais); //clone do Obj
        }

        flagAcionarItem1Atividades = false;
    }

    /**
     * @param tipoProcesso1
     * @param tipoProcesso2
     */
    public compareTipoProcesso(tipoProcesso1: any, tipoProcesso2: any): boolean {
        return tipoProcesso1 && tipoProcesso2 ? tipoProcesso1.id == tipoProcesso2.id : tipoProcesso1 == tipoProcesso2;
    }

    /**
     * @param form
     */
    public avancar(form: NgForm): void {
        this.submitted = true;
        if (form.valid) {
            this.inputCalendario.arquivosExcluidos = this.arquivosExcluidos;
            this.inputCalendario.atividadesPrincipaisExcluidas = this.atividadesPrincipaisExcluidas;
            this.inputCalendario.subAtividadesPrincipaisExcluidas = this.subAtividadesPrincipaisExcluidas;

            if (this.validarArquivoObrigatorio()) {
                this.calendarioClientService.salvarDadosPeriodo(this.inputCalendario).subscribe(data => {
                    this.inputCalendario = data;
                    this.next.emit();
                }, error => {
                    this.messageService.addMsgDanger(error);
                });
            }
        }
    }

    /**
     * Válida se foram informadas atividades principais repetidas.
     */
    public validarAtividadesESubatividadesRepetidas(): void {
        let isAtividadeRepetida = false;
        let atividadesSecundarias = [];
        let isSubAtividadeRepetida = false;
        let atividadesPrincipais = this.inputCalendario.atividadesPrincipais;

        atividadesPrincipais.forEach(atividade => {
            if (atividade.atividadesSecundarias && atividade.atividadesSecundarias.length > 0) {
                atividadesSecundarias.push(...atividade.atividadesSecundarias);
            }
        });

        atividadesPrincipais.forEach((atividade, indexAtividadePrincipal) => {
            atividadesPrincipais.forEach((atividadePrincipalSub, indexAtividadePrincipalSub) => {
                if (indexAtividadePrincipal != indexAtividadePrincipalSub && atividade.descricao == atividadePrincipalSub.descricao) {
                    isAtividadeRepetida = true;
                }
            });
        });

        atividadesSecundarias.forEach((atividade, index) => {
            atividadesSecundarias.forEach((atividadeSub, indexSub) => {
                if (index != indexSub && atividade.descricao == atividadeSub.descricao) {
                    isSubAtividadeRepetida = true;
                }
            });
        });

        if (isAtividadeRepetida || isSubAtividadeRepetida) {
            this.messageService.addConfirmYesNo('MSG_ATIVIDADE_SUBATIVIDADE_COM_MESMO_NOME', () => {
                this.save();
            });
        } else {
            this.save();
        }
    }

    /**
     * Valida se o nome da atividade informada não está duplicada
     * @param index 
     */
    public validarDescricaoAtividade(index: number): void {
        let isAtividadeRepetida = false;
        this.indicesAtividadesDuplicadas = [];
        let atividadesPrincipais = this.inputCalendario.atividadesPrincipais;

        let atividade = atividadesPrincipais[index];

        atividadesPrincipais.forEach((atividadePrincipal, indexAtividadePrincipal) => {
            if (index != indexAtividadePrincipal && atividade.descricao == atividadePrincipal.descricao) {
                this.indicesAtividadesDuplicadas.push(indexAtividadePrincipal);
                isAtividadeRepetida = true;
            }
        });

        if (isAtividadeRepetida) {
            this.indicesAtividadesDuplicadas.push(index);
            this.messageService.addConfirmYesNo('MSG_ATIVIDADE_SUBATIVIDADE_COM_MESMO_NOME',
                () => { },
                () => {
                    this.inputCalendario.atividadesPrincipais[index].descricao = '';
                    this.indicesAtividadesDuplicadas = [];
                });
        }
    }

    /**
     * Retorna se atividade está duplicada
     * @param index 
     */
    public isAtividadeDuplicada(index): boolean {
        return (this.indicesAtividadesDuplicadas && this.indicesAtividadesDuplicadas.indexOf(index) != -1);
    }

    /**
     * Verifica se a subatividade está com a descrição duplicada
     * @param ativKey 
     * @param sub_key 
     */
    public validarDescricaoSubAtividade(ativKey, sub_key) {
        let atividadesSecundariasVerificacao = [];
        let isSubAtividadeRepetida = false;
        this.indicesSubatividadesDuplicadas = [];

        let atividadeSecundariaVerificacao: any;

        this.inputCalendario.atividadesPrincipais.forEach((atividade, indexPrincipal) => {
            if (atividade.atividadesSecundarias && atividade.atividadesSecundarias.length > 0) {
                atividade.atividadesSecundarias.forEach((atividadeSecundaria, indexSecundaria) => {
                    let verificacao = { indexPrincipal, indexSecundaria, descricao: atividadeSecundaria.descricao };

                    if (indexPrincipal == ativKey && indexSecundaria == sub_key) {
                        atividadeSecundariaVerificacao = verificacao
                    } else {
                        atividadesSecundariasVerificacao.push(verificacao);
                    }
                });
            }
        });

        atividadesSecundariasVerificacao.forEach(atividadeSeundaria => {
            if (atividadeSecundariaVerificacao.descricao && atividadeSecundariaVerificacao.descricao == atividadeSeundaria.descricao) {
                isSubAtividadeRepetida = true;

                this.indicesSubatividadesDuplicadas.push({
                    indexPrincipal: atividadeSeundaria.indexPrincipal,
                    indexSecundaria: atividadeSeundaria.indexSecundaria
                });
            }
        });

        if (isSubAtividadeRepetida) {
            this.indicesSubatividadesDuplicadas.push({
                indexPrincipal: atividadeSecundariaVerificacao.indexPrincipal,
                indexSecundaria: atividadeSecundariaVerificacao.indexSecundaria
            });
            this.messageService.addConfirmYesNo('MSG_ATIVIDADE_SUBATIVIDADE_COM_MESMO_NOME',
                () => { },
                () => {
                    this.indicesSubatividadesDuplicadas = [];
                    this.inputCalendario.atividadesPrincipais[ativKey].atividadesSecundarias[sub_key].descricao = '';
                });
        }
    }

    /**
     * Retorna se a subatividade está duplicada
     * @param ativKey 
     * @param sub_key 
     */
    public isSubatividadeDuplicada(ativKey, sub_key): boolean {
        let isDuplicada = false;

        if (this.indicesSubatividadesDuplicadas) {
            this.indicesSubatividadesDuplicadas.forEach(indices => {
                if (indices.indexPrincipal == ativKey && indices.indexSecundaria == sub_key) {
                    isDuplicada = true;
                }
            })
        }
        return isDuplicada;
    }

    public detectarPosicao() {
        this.isDropUfMultSelectUf = this.cardBodyFormCalendario.nativeElement.offsetHeight < 750;
    }

    /**
     * Adiciona uma nova atividade para a alteração do período.
     *
     * @param atividade
     * @param atividadeAtualizacao
     */
    private addAtividadePeriodoAlteracao(atividade: any, atividadeAtualizacao: any, descricaoNivel: string): void {

        if (!deepEqual(atividade.descricao, atividadeAtualizacao.descricao)) {
            let objJustificativaHist = { descricao: '', justificativa: '' };
            objJustificativaHist.descricao = 'Alteração do nome da ' + atividadeAtualizacao.nivel + ' ' + descricaoNivel;
            this.atividadesPeriodoAlteracao.push(objJustificativaHist);
        }

        if (!deepEqual(atividade.dataInicio, atividadeAtualizacao.dataInicio)) {
            let objJustificativaHist = { descricao: '', justificativa: '' };
            objJustificativaHist.descricao = 'Alteração da data de início da ' + atividadeAtualizacao.nivel + ' ' + descricaoNivel;
            this.atividadesPeriodoAlteracao.push(objJustificativaHist);
        }

        if (!deepEqual(atividade.dataFim, atividadeAtualizacao.dataFim)) {
            let objJustificativaHist = { descricao: '', justificativa: '' };
            objJustificativaHist.descricao = 'Alteração da data de fim da ' + atividadeAtualizacao.nivel + ' ' + descricaoNivel;
            this.atividadesPeriodoAlteracao.push(objJustificativaHist);
        }

        if (!deepEqual(atividade.obedeceVigencia, atividadeAtualizacao.obedeceVigencia)) {
            let objJustificativaHist = { descricao: '', justificativa: '' };
            objJustificativaHist.descricao = 'Alteração do obedecer vigência ' + atividadeAtualizacao.nivel + ' ' + descricaoNivel;
            this.atividadesPeriodoAlteracao.push(objJustificativaHist);
        }
    }

}
