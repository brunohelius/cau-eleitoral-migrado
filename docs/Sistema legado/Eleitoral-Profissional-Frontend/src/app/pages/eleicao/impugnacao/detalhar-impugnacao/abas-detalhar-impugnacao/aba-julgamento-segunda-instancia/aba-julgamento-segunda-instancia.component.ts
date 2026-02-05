import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { DomSanitizer } from '@angular/platform-browser';
import { Component, OnInit, Input, TemplateRef, ViewChild, Output, EventEmitter } from '@angular/core';

import { JulgamentoImpugnacaoService } from 'src/app/client/julgamento-impugnacao-client.service.ts/julgamento-impugnacao-client.service';

@Component({
    selector: 'aba-julgamento-segunda-instancia',
    templateUrl: './aba-julgamento-segunda-instancia.component.html',
    styleUrls: ['./aba-julgamento-segunda-instancia.component.scss']
})
export class AbaJulgamentoSegundaInstanciaComponent implements OnInit {

    @Input() isIES: any;
    @Input() julgamento: any;
    @Input() impugnacao: any;
    @Input() isRecursoCadastrado: any;
    @Input() substituicaoImpugnado: any;
    @Input() isSubstituicaoCadastrada: boolean;

    public isRecurso: boolean = false;
    public isConselheiro: boolean =  false;
    
    public msgConfirmacao: any;
    public idTipoSolicitacao: any;

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
        
    }

    /**
     * Realiza download de arquivo de defesa de impugnação.
     */
    public downloadArquivo(download: any): void {
        this.julgamentoService.getArquivoJulgamentoSegundaInstancia(this.julgamento.id).subscribe(
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
    * Validação para apresentar o botão Solicitar Recurso.
    */
    public validaBotao(): boolean {
        return (
            this.isResponsavelChapa() && 
            !this.substituicaoImpugnado &&
            !this.isSubstituicaoCadastrada &&
             this.impugnacao.isIniciadoAtividadeSubstituicao &&
            !this.impugnacao.isFinalizadoAtividadeSubstituicao &&
            this.impugnacao.statusPedidoImpugnacao.id == Constants.STATUS_IMPUGNACAO_RECURSO_PROCEDENTE
        );
    }

    /**
     * Retorna se e recurso ou reconsideração
     */
    public isRecursoReconsideracao(): any {
        return this.messageService.getDescription((this.isIES)? 'LABEL_RECONSIDERACAO': 'LABEL_RECURSO');
    }


    /**
     * Método responsável por abrir o modal de cadastro de substituição
     */
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
    * Método responsável por fazer o redirecionamento  pra tela de visualizar substituição
    * @param dados 
    */
    public redirecionarVisualizarSubstituicao(dados: any): void {
        this.redirecionarAposSalvamentoSubstituicao.emit(dados);
    }
}

