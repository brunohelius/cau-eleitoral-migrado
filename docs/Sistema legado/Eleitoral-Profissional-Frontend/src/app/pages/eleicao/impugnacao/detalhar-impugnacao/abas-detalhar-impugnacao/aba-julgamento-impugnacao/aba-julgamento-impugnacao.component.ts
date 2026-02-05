import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { DomSanitizer } from '@angular/platform-browser';
import { Component, OnInit, Input, TemplateRef, ViewChild, Output, EventEmitter } from '@angular/core';

import { JulgamentoImpugnacaoService } from 'src/app/client/julgamento-impugnacao-client.service.ts/julgamento-impugnacao-client.service';

@Component({
    selector: 'aba-julgamento-impugnacao',
    templateUrl: './aba-julgamento-impugnacao.component.html',
    styleUrls: ['./aba-julgamento-impugnacao.component.scss']
})
export class AbaJulgamentoImpugnacaoComponent implements OnInit {

    @Input() julgamento: any;
    @Input() impugnacao: any;
    @Input() isIES: any;
    @Input() isRecursoCadastrado: any;
    @Input() isSubstituicaoCadastrada: boolean;


    public recurso: any = {};
    public arquivos: any = [];
    public isRecurso: boolean = false;
    public submitted: boolean =  false;
    public isSubmetido: boolean = false;
    public isConselheiro: boolean =  false;
    
    public titleModal: any;
    public titleDescricao: any;
    public msgConfirmacao: any;
    public idTipoSolicitacao: any;

    public modalRecurso: BsModalRef;
    public modalConfirmacao: BsModalRef;
    public modalSubstituicao: BsModalRef;
    
    @Output() redirecionarAposSalvamento: EventEmitter<any> = new EventEmitter();
    @Output() redirecionarAposSalvamentoSubstituicao: EventEmitter<any> = new EventEmitter();

    @ViewChild('templateConfirmacao', { static: true }) templateConfirmacao: TemplateRef<any>;
    @ViewChild('templateSubstituicao', { static: true }) templateSubstituicao: TemplateRef<any>;

    /**
     * Método contrutor da classe
     */
    constructor(
        private route: ActivatedRoute,
        public domSanitizer: DomSanitizer,
        private modalService: BsModalService,
        private messageService: MessageService,
        private julgamentoService: JulgamentoImpugnacaoService

    ) {
        
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializaArquivo();
        this.inicializaIdTipoSolicitacao();
        this.recurso = {idJulgamentoImpugnacao: 0, descricao: '', idTipoSolicitacaoRecursoImpugnacao: ''};
    }
    
    public inicializaArquivo() {
        let arquivo = { nome: ''};
        arquivo.nome = this.julgamento.nomeArquivo;
        this.arquivos.push(arquivo);
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     */
    public downloadArquivoDefesaImpugnacao(download: any): void {
        this.julgamentoService.getArquivoJulgamento(this.julgamento.id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     */
    public downloadArquivoRecurso(download: any): void {
        download.evento.emit(download.arquivo);
    }

    /**
     * Verifica se o usuário logado é membro da comissao é conselheiro CEN ou CE_UF.
     */
    public isConcelheiro(): boolean {
        return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL_COMISSAO;
    } 

    /**
    * Verifica se o usuário logado é responsavel pela chapa.
    */
    public isResponsavelChapa(): boolean {
        return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL_CHAPA;
    }

    /**
    * Verifica se o usuário logado é o impugnante.
    */
    public isImpugante(): boolean {
        return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL;
    }
    
    /**
     * Retorna um valor de parâmetro passado na rota.
     * @param nameParam 
     */
    private getValorParamDoRoute(nameParam):any {
        let data = this.route.snapshot.data;

        let valor = undefined;

        for (let index of Object.keys(data)) {
            let param = data[index];

            if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
                valor = param[nameParam];
                break;
            }
        }
        return valor;
    }
    
    /**
     * Validação da finalização do recurso.
     */
    private isConcluidoRecurso(): boolean {
        return (this.isResponsavelChapa() && this.julgamento.isConcluidoRecursoResponsavel) ||
            (this.isImpugante() && this.julgamento.isConcluidoRecursoImpugnante) || this.isRecursoCadastrado;
    }

    private isJulgamentoProcedente(): boolean {
        return this.julgamento.statusJulgamentoImpugnacao.id === Constants.STATUS_JULGAMENTO_PROCEDENTE;
    }

    /**
     * 
     */
    public isConcluidoSubstituicao(): boolean {
        return this.isResponsavelChapa() && (this.julgamento.isConcluidoSubstituicao || this.isSubstituicaoCadastrada);
    }

    /**
    * Validação para apresentar o botão Solicitar Recurso.
    */
    public validaSolicitarRecurso(): boolean {
        return (
            !this.isConcelheiro()
            && !this.isIES
            && !this.isConcluidoRecurso()
            && !this.isConcluidoSubstituicao()
            && !this.julgamento.isFinalizadoAtividadeRecurso
        );
    }

    /**
    * Validação para apresentar o botão Solicitar Substiuicao
    */
    public validaSolicitarSubstiuicao(): boolean {
        return (
            this.isResponsavelChapa()
            && !this.isConcluidoRecurso()
            && this.isJulgamentoProcedente()
            && !this.isConcluidoSubstituicao()
            && !this.julgamento.isFinalizadoAtividadeRecurso
        );
    }

    /**
    * Validação para apresentar o botão Solicitar Reconsideracao
    */
    public validaSolicitarReconsideracao(): boolean {
        return (
            !this.isConcelheiro()
            && this.isIES 
            && !this.isConcluidoRecurso()
            && !this.isConcluidoSubstituicao()
            && !this.julgamento.isFinalizadoAtividadeRecurso
        );
    }
    
    /**
     * Exibe modal de cadastro de recurso/reconsideracao.
     * 
     * @param template 
     * @param element 
     */
    public abrirModalRecurso(template: TemplateRef<any>, element: any): void {
        this.isRecurso = (element == Constants.TITLE_RECURSO);
        this.titleDescricao = this.isRecurso? 'LABEL_RECURSO_DO_JULGAMENTO' : 'LABEL_RECONSIDERACAO_JULGAMENTO';
        this.titleModal = this.isRecurso? 'TITLE_INTERPOR_RECURSO_IMPUGNACAO' : 'TITLE_INTERPOR_RECONSIDERACAO_IMPUGNACAO';

        this.modalRecurso = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
    }

    /**
     * Exibe modal de confirmação.
     * 
     * @param template 
     */
    public abrirModalConfirmacao(): void {
        this.modalConfirmacao = this.modalService.show(this.templateConfirmacao, Object.assign({}, { class: 'modal-lg modal-dialog-centered' }));
    }
    
    /**
     * Salvar arquivo de recurso de impugnação.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.recurso.arquivos = arquivos;
    }
    
    /**
     * Excluir arquivo de defesa de impugnação.
     * 
     * @param arquivo 
     */
    public excluirArquivo(arquivo): void {
        if(arquivo.id) {
            this.recurso.idArquivosRemover.push(arquivo.id);
        }
    }
    
    /**
     * Verifica se a discricao foi preencida.
     */
    public hasDescricao(): any {
        return this.recurso.descricao;
    }
    
    /**
     * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
     *
     * @param descricao
     */
    public adicionarDescricao(descricao): void {
        this.recurso.descricao = descricao;
    }

    /**
     * Salva o recurso/reconsideracao do pedido de impugnacao
     */
    public salvarRecursoReconsideracao(): any {
        this.submitted = true;

        if (this.hasDescricao()){
            this.montarSalvarRecurso();
            this.julgamentoService.salvarRecurso(this.recurso).subscribe(
                data => {
                    this.recurso = data;
                    this.modalRecurso.hide();
                    this.abrirModalConfirmacao();
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            
            );
        }
    }

    /**
     * Responsavel por fazer todos os pre-requisitos para o salvamento.
     */
    public montarSalvarRecurso(): any {
        let msg = this.isRecurso? 'MSG_CONFIRMACAO_RECURSO': 'MSG_CONFIRMACAO_RECONSIDERACAO';
        this.msgConfirmacao = this.messageService.getDescription(msg);
        this.recurso.idJulgamentoImpugnacao = this.julgamento.id;

        if (this.isResponsavelChapa()) {
            this.recurso.idTipoSolicitacaoRecursoImpugnacao = Constants.ID_TIPO_RESPONSAVEL;
        } else if (this.isImpugante()){
            this.recurso.idTipoSolicitacaoRecursoImpugnacao = Constants.ID_TIPO_IMPUGNANTE;
        }
    }

    /**
     * Cancela o modal de recurso.
     */
    public cancelarRecurso():any {
        this.submitted = false;
        this.modalRecurso.hide();
        this.recurso.descricao = '';
    }

    /**
     * Responsavel por redirecionar a aba apos o salvamento do recurso.
     */
    public redirecionarAbaRecurso(): any {
        let recurso;
        this.modalConfirmacao.hide();
        recurso = {
            recurso: this.recurso,
            idTipoSolicitacao: this.idTipoSolicitacao
        }
        this.redirecionarAposSalvamento.emit(recurso);
    }
    
    /**
     * inicializa o idTipoSolicitacao.
     */
    public inicializaIdTipoSolicitacao(): any {
        if (this.isResponsavelChapa()) {
        this.idTipoSolicitacao = Constants.ID_TIPO_RESPONSAVEL;
        } else if (this.isImpugante()) {
        this.idTipoSolicitacao = Constants.ID_TIPO_IMPUGNANTE;
        }
    }



    public solicitarSubstituicao(): void {
        this.modalSubstituicao = this.modalService.show(
            this.templateSubstituicao ,
            Object.assign(
                {
                    ignoreBackdropClick: true
                }, 
                {
                    class: 'modal-xl' 
                }
            )
        );
    }

    /**
   * Método responsável por fechar o modal de cadastro de substituição
   * @param evento 
   */
    public fecharModalSubstituicao(): void {
        this.modalSubstituicao.hide()
    }


    /**
     * 
     * @param dados 
     */
    public redirecionarVisualizarSubstituicao(dados: any): void {
        this.redirecionarAposSalvamentoSubstituicao.emit(dados);
    }
}

