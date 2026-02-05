import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, TemplateRef, ViewChild } from '@angular/core';

import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';
import { formatDate } from '@angular/common';
import { AcompanharRecursoSubstituicaoClientService } from 'src/app/client/acompanhar-recurso-substituicao-client/acompanhar-recurso-substituicao-client.service';


/**
 * Componente responsável pela apresentação de listagem de Chapas por Eleição.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'acompanhar-substituicao-detalhamento',
    templateUrl: './acompanhar-substituicao-detalhamento.component.html',
    styleUrls: ['./acompanhar-substituicao-detalhamento.component.scss']
})

export class AcompanharSubstituicaoDetalhamento implements OnInit {

    public isIES: boolean;
    public cauUfs: any = [];
    private permissoes = [];
    public solicitacoesSubstituicao: any = [];

    public submitted: boolean;

    public pedido: any = {};
    public julgamento: any = {};
    public dadosServicoRecurso: any;
    public atividadeSecundaria: any;
    public configuracaoCkeditor: any = {};
    public membroSubstitutoTitular: any = {};
    public membroSubstituidoTitular: any = {};
    public membroSubstitutoSuplente: any = {};
    public membroSubstituidoSuplente: any = {};

    public usuario;
    private iesRoute: any;
    public deferimento: any;
    private calendarioId: any;
    public membroChapaSelecionado: any;
    public julgamentoSegundaInstancia: any;

    public modalRef: BsModalRef;
    public modalJulgamento: BsModalRef;
    public modalPendeciasMembro: BsModalRef;

    public tabs: any;

    public julgamentoSubstituicaoCadastrado: any;

    isCarregadoDadosAbaRecurso: boolean = false

    @ViewChild('templateConfirmacao', null) templateConfirmacao: TemplateRef<any>;
    public tituloConfirmacaoJulgamento: string;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private securityService: SecurityService,
        private substituicaoChapaService: SubstiuicaoChapaClientService,
        private acompanharRecursoSubstituicaoClientService: AcompanharRecursoSubstituicaoClientService

    ) {
        this.iesRoute = route.snapshot.params.isIES;
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.calendarioId = route.snapshot.params.calendarioId;
        this.usuario = this.securityService.credential["_user"];
        this.atividadeSecundaria = route.snapshot.data["atividadeSecundaria"];
        this.julgamentoSegundaInstancia = route.snapshot.data["julgamentoSegundaInstancia"];
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
        this.inicializartabs();
        this.pedido = this.route.snapshot.data["pedido"];
        this.julgamentoSubstituicaoCadastrado = this.route.snapshot.data["julgamentoSubstituicao"];
        this.getPermissao();
        this.inicializaMembros();
        this.inicializaIconeTitulo();
        this.inicializaConfiguracaoCkeditor();
        this.isIES = this.pedido.chapaEleicao.tipoCandidatura.descricao;
    }

    /**
     * Inicializa os objetos com os membros.
     */
    public inicializaMembros(): void {
        this.membroSubstitutoTitular = this.pedido.membroSubstitutoTitular;
        this.membroSubstituidoTitular = this.pedido.membroSubstituidoTitular;
        this.membroSubstitutoSuplente = this.pedido.membroSubstitutoSuplente;
        this.membroSubstituidoSuplente = this.pedido.membroSubstituidoSuplente;
    }

    /**
     * Inicializa ícone e título do header da página.
    */
    public inicializaIconeTitulo() {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-user',
            description: this.messageService.getDescription('Pedido de Substituição')
        });
    }

    /**
     * Inicializa a configuração do ckeditor.
     */
    private inicializaConfiguracaoCkeditor(): void {
        this.configuracaoCkeditor = {
            toolbar: [
                { name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo'] },
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            ],
            title: 'Justificativa'
        };
    }

    /**
    * Métodos de retorno do status da solicitação de substituição de membros
    */
    public getStatusConfirmado(id: any) {
        return id == Constants.STATUS_CONFIRMADO;
    }

    /**
     * Responsavel por retornar a rota.
     */
    public voltar(): void {
        let isIES = this.pedido.chapaEleicao.tipoCandidatura.descricao;
        if (isIES === Constants.IES) {
            this.router.navigate([
                `/eleicao/acompanhar-substituicao-uf/0/calendario/${this.calendarioId}`
            ]);
        } else {
            this.router.navigate([
                `/eleicao/acompanhar-substituicao-uf/${this.pedido.chapaEleicao.idCauUf}/calendario/${this.calendarioId}`
            ]);
        }
    }

    /**
    * Verifica se o membro é  responsáveç.
    * @param id
    */
    public isResponsavel(membro?: any): boolean {
        let validacao = false;
        if (membro) {
            validacao = membro.situacaoResponsavel == true;
        }
        return validacao;
    }

    /**
     * Responsavel por voltar a aba para a principal.
     */
    public voltarAbaPrincipal(): any {
        this.mudarAbaSelecionada(Constants.ABA_PRINCIAPAL_NOME);
    }

    /**
     * Verifica o status de Validação do Membro.
     *
     * @param membro
     */
    public statusValidacao(membro?: any): boolean {
        let validacao = false;
        if (membro) {
            validacao = membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
        }
        return validacao;
    }

    public salvarJulgamentoEvent(julgamento: any): void {
        this.julgamentoSegundaInstancia = julgamento;
        this.julgamentoSubstituicaoCadastrado = julgamento;
        this.mudarAbaSelecionada(this.tabs.julgamentoSegundaInstancia.nome);
        this.recarregarPedido();
    }

    public recarregarPedido() {
        this.substituicaoChapaService.getPedidoSubstituicaoChapa(this.pedido.id).subscribe(
            data => {
                this.pedido = data;
                this.inicializaMembros();
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Exibe modal de listagem de pendencias do profissional selecionado.
     *
     * @param template
     * @param element
     */
    public abrirModalPendeciasMembro(template: TemplateRef<any>, element: any): void {
        this.membroChapaSelecionado = element;
        this.modalPendeciasMembro = this.modalService.show(template, Object.assign({}, { class: 'my-modal' }));
    }

    /**
     * Exibe modal de listagem de pendencias do profissional selecionado.
     *
     * @param template
     * @param element
     */
    public abrirModalJulgamento(template: TemplateRef<any>): void {

        this.substituicaoChapaService.getAtividadeSecundariaCadastro(this.pedido.id).subscribe(
            data => {
                let dataAtual = new Date();
                dataAtual.setHours(0, 0, 0, 0);

                let dataInicio = new Date(data.dataInicio);
                dataInicio.setDate(dataInicio.getDate() + 1);
                dataInicio.setHours(0, 0, 0, 0);

                let dataFim = new Date(data.dataFim);
                dataFim.setDate(dataFim.getDate() + 1);
                dataFim.setHours(0, 0, 0, 0);

                if (dataAtual < dataInicio) {
                    this.messageService.addConfirmYesNo('MSG_INICIO_JULGAMENTO_SUBSTITUICAO', () => {
                        this.showModalJulgamento(template);
                    });
                } else if (dataAtual > dataFim) {
                    this.messageService.addConfirmYesNo('MSG_FIM_JULGAMENTO_SUBSTITUICAO', () => {
                        this.showModalJulgamento(template);
                    });
                } else {
                    this.showModalJulgamento(template);
                }
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Método auxiliar para abrir modal e resetar o julgametno antes de abrir
     * @param template
     */
    private showModalJulgamento(template: TemplateRef<any>): void {
        this.submitted = false;
        this.resetJulgamento();
        this.modalJulgamento = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
    }

    /**
     * Recupera o arquivo conforme a entidade 'resolucao' informada.
     *
     * @param event
     * @param resolucao
     */
    public download(event: EventEmitter<any>, resolucao: any): void {
        this.substituicaoChapaService.getDocumentoSubstituicao(resolucao.id).subscribe((data: Blob) => {
            event.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Realiza download de documento em formato PDF de substituição do membro da chapa.
     */
    public downloadDocumentoPedidoSubstituicaoMembro(event: EventEmitter<any>): void {
        this.substituicaoChapaService.getDocumentoPedidoSubstituicaoMembro(this.pedido.id).subscribe((data: Blob) => {
            event.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
    * Retorna as permissões do usuário logado
    */
    public getPermissao() {
        const regras = this.usuario.roles;
        regras.forEach(element => {
            if (element == Constants.ROLE_ACESSOR_CEN || element == Constants.ROLE_ACESSOR_CE) {
                this.permissoes.push(element);
            }
        });

    }

    /**
 * Verifica se é acessr CEN, caso não seja abilita
 * apenas a ação de visualizar de sua UF
 */
    public isMostraJulgamento() {
        return this.pedido.isPermissaoJulgamento && !this.isPedidoJulgado();
    }

    /**
     * Resoponsavel por adicionar o parecer que fora submetido no compomente editor de texto.
     *
     * @param parecer
     */
    public adicionarParecerJulgamento(parecer): any {
        this.julgamento.parecer = parecer;
    }

    /**
     * Resoponsavel por salvar os arquivos que foram submetidos no componete arquivo.
     *
     * @param arquivos
     */
    public salvarArquivoJulgamento(arquivos): void {
        this.julgamento.nomeArquivo = arquivos[0].nome;
        this.julgamento.arquivo = arquivos[0].arquivo;
        this.julgamento.tamanho = arquivos[0].tamanho;
    }

    /**
     * Reseta o julgamento
     */
    public resetJulgamento(): void {
        this.julgamento.parecer = '';
        this.julgamento.nomeArquivo = '';
        this.julgamento.arquivo = undefined;
        this.julgamento.tamanho = undefined;
    }

    /**
     * Resoponsavel por validar o julgamento.
     *
     * @param arquivos
     */
    public validarJulgamento(deferimento): void {
        this.submitted = true;
        this.deferimento = deferimento;

        if (this.hasDescricao()) {

            this.julgamento.idPedidoSubstituicaoChapa = this.pedido.id;
            this.julgamento.idStatusJulgamentoSubstituicao = deferimento;

            this.tituloConfirmacaoJulgamento = "TITLE_CONFIRMAR_INDEFERIMENTO_SUBSTITUICAO";
            if (deferimento == Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO) {
                this.tituloConfirmacaoJulgamento = "TITLE_CONFIRMAR_DEFERIMENTO_SUBSTITUICAO";
            }


            this.abrirModalConfirmacao(this.templateConfirmacao);
        }
    }

    /**Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO
     * Resoponsavel por salvar  o julgamento.
     *
     * @param arquivos
     */
    public salvarJulgamento(): void {

        this.substituicaoChapaService.salvarJulgamento(this.julgamento).subscribe(
            data => {
                if (this.julgamento.idStatusJulgamentoSubstituicao == Constants.STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO) {
                    this.messageService.addMsgSuccess('MSG_PEDIDO_DEFERIMENTO_SUCESSO');
                } else {
                    this.messageService.addMsgSuccess('MSG_PEDIDO_INDEFERIMENTO_SUCESSO');
                }
                this.modalRef.hide();
                this.modalJulgamento.hide();
                this.resetJulgamento();
                this.julgamentoSubstituicaoCadastrado = data;
                this.pedido = data.pedidoSubstituicaoChapa;
                this.inicializaMembros()
                this.mudarAbaSelecionada(this.tabs.julgamento.nome);

            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    public downloadArquivoJulgamento(download: any): void {
        download.evento.emit(download.arquivo);
    }

    /**
     * Exibe modal de confirmação da substituição.
     *
     * @param template
     */
    public abrirModalConfirmacao(template: TemplateRef<any>) {
        let config = {
            backdrop: true,
            ignoreBackdropClick: true,
            class: 'modal-lg modal-dialog-centered'
        };
        this.modalRef = this.modalService.show(template, config);
    }

    /**
     * Verifica se existe ao menos um arquivo submetido.
     */
    public hasArquivos(): any {
        return this.julgamento.arquivo;
    }

    /**
     * Verifica se a discricao foi preencida.
     */
    public hasDescricao(): any {
        return this.julgamento.parecer;
    }

    /**
     * Inicializa abas.
     */
    public inicializartabs(): void {
        this.tabs = {
            principal: { ativo: true, nome: 'principal' },
            julgamento: { ativo: false, nome: 'julgamento' },
            julgamentoSegundaInstancia: { ativo: false, nome: 'julgamentoSegundaInstancia' },
            recursoSubstituicao: { ativo: false, nome: 'RecursoSubstituicao' }
        };
    }

    /**
     * Método responsável por atualizar a aba.
     *
     * @param nomeAba
     */
    public mudarAbaSelecionada(nomeAba): void {
        this.tabs.principal.ativo = this.tabs.principal.nome == nomeAba;
        this.tabs.julgamento.ativo = this.tabs.julgamento.nome == nomeAba;
        this.tabs.recursoSubstituicao.ativo = this.tabs.recursoSubstituicao.nome == nomeAba;
        this.tabs.julgamentoSegundaInstancia.ativo = this.tabs.julgamentoSegundaInstancia.nome == nomeAba;

        if(this.tabs.recursoSubstituicao.ativo) {
            this.getDadosInterposicaoRecurso(this.pedido.id);
        }
    }


    /**
     * Método retorna se pedido foi julgado
     */
    public isPedidoJulgado(): boolean {
        return this.julgamentoSubstituicaoCadastrado != undefined && this.julgamentoSubstituicaoCadastrado;
    }

    /**
     * Método retorna se pedido foi julgado
     */
    public isSecundoPedidoJulgado(): boolean {
        return this.julgamentoSegundaInstancia != undefined && this.julgamentoSegundaInstancia;
    }

    /**
     * Verifica se aba de recurso deve ser mostrada.
     */
    public isMostrarAbaRecurso(): boolean {

        return (
            this.pedido.statusSubstituicaoChapa.id >= 4 // se tem recurso cadastrado)
            ||
            (
                this.isFinalizadoAtividadeRecurso()
                && this.pedido.statusSubstituicaoChapa.id >= 3 // se o pedido de substituição foi ao menos indeferido
            )
        );
    }

    /**
     * Retorna se o prazo da atividade 2.5 é vigente
     */
    public isFinalizadoAtividadeRecurso(): boolean {

        let dataFim = new Date(this.atividadeSecundaria.dataFim);
        dataFim.setHours(23, 59, 59, 999);
        dataFim.setDate(dataFim.getDate() + 1);

        let hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        if (hoje > dataFim) {
            return true;
        }
        return false;
    }



    /**
     * Retorna os dados de interposicao de recurso
     * @param id
     * @param identificadorAba
     */
    public getDadosInterposicaoRecurso(id: number): void {
        if(!this.isCarregadoDadosAbaRecurso) {
            this.acompanharRecursoSubstituicaoClientService.recursoSubstituicao(id).subscribe(
                data => {
                    this.dadosServicoRecurso = data;
                    this.isCarregadoDadosAbaRecurso = true;
                }
            );
        }
    }



        /**
     * Verifica se a data da Atividade secundária está dentro do período de vigência.
     */
    private isVigente(dataInicio, dataFim): number {
        dataFim = new Date(this.atividadeSecundaria.dataFim);
        dataFim.setHours(23, 59, 59, 999);
        dataFim.setDate(dataFim.getDate() + 1);

        dataInicio = new Date(this.atividadeSecundaria.dataInicio);
        dataInicio.setHours(0, 0, 0, 0);
        dataInicio.setDate(dataInicio.getDate() + 1);

        let hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        if (hoje <= dataFim && hoje >= dataInicio) {
            return 0;
        }
        return hoje > dataFim ? 1 : -1;
    }



}
