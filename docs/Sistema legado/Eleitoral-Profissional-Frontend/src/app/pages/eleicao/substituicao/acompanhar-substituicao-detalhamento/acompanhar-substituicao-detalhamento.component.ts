import { julgamentoSegundaInstanciaResolve } from 'src/app/client/impugnacao-candidatura-client/julgamento-segunda-instancia-client.resolve';
import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { ActivatedRoute, Router } from "@angular/router";
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, TemplateRef, ViewChild } from '@angular/core';
import { SubstiuicaoChapaClientService } from 'src/app/client/substituicao-chapa-client/substituicao-chapa-client.module';
import { AcompanharRecursoSubstituicaoClientService } from 'src/app/client/acompanhar-recurso-substituicao-client/acompanhar-recurso-substituicao-client.service';
import { AcompanharJulgamentoSubstituicaoClient } from 'src/app/client/acompanhar-julgamento-substituicao-client/acompanhar-julgamento-substituicao-client.service';
import { StringService } from 'src/app/string.service';


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

    public cauUfs: any = [];
    public pedido: any = {};
    public tipoProfissional: any;
    public membroChapaSelecionado: any;
    public configuracaoCkeditor: any = {};
    public modalPendeciasMembro: BsModalRef;
    public membroSubstitutoTitular: any = {};
    public membroSubstituidoTitular: any = {};
    public membroSubstitutoSuplente: any = {};
    public solicitacoesSubstituicao: any = [];
    public membroSubstituidoSuplente: any = {};
    public julgamentoSegundaInstancia: any = {};

    public abas: any = {
        abaRecurso: false,
        abaPedidoSubstituicao: true,
        abaJulgamentoPrimeiraInstancia: false,
        abaJulgamentoSegundaInstancia: false
    }

    public carregamentoDadosAbas: any = {
        dadosRecurso: false,
        dadosJulgamentoSegundaInstancia: false,
        dadosJulgamentoPrimeiraInstancia: false
    }

    public isProcessaDadosJulgamento = true;
    public dadosServicoJulgamento: any = [];
    public dadosServicoJulgamentoSegundaInstancia: any = [];

    public isProcessaDadosRecurso = true;
    public dadosServicoRecurso: any;

    public atividadeSecundaria: any;
    public isPossuiRecursoCadastrado: boolean;

    public isProcessaDadosJulgamentoSegundaInstancia = true;
    public isIES: boolean;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private substituicaoChapaService: SubstiuicaoChapaClientService,
        private acompanharRecursoSubstituicaoClientService: AcompanharRecursoSubstituicaoClientService,
        private acompanharJulgamentoSubstituicaoClient: AcompanharJulgamentoSubstituicaoClient

    ) {
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.atividadeSecundaria = route.snapshot.data["atividadeSecundaria"];
    }

    /**
     * Inicialização dos dados do campo
    */
    ngOnInit() {
        this.pedido = this.route.snapshot.data["pedido"];
        this.tipoProfissional = this.getValorParamDoRoute('tipoProfissional');
        this.inicializaMembros();
        this.inicializaIconeTitulo();
        this.inicializaConfiguracaoCkeditor();
        this.isPossuiRecursoCadastrado = this.pedido.statusSubstituicaoChapa.id > 3;
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
     * Retorna o registro com a mascara 
     * @param str 
     */
    public getRegistroComMask(str) {
        return StringService.maskRegistroProfissional(str);
    }

    /**
     * Método que redireciona o usuário para a página anterior ao ser acionado
     */
    public voltar(): void {
        let tipoCandidatura = this.pedido.chapaEleicao.tipoCandidatura.descricao;

        if (this.tipoProfissional != 'membroChapa') {

            if (tipoCandidatura === Constants.IES) {
                this.router.navigate([`/eleicao/substituicao/acompanhar-uf/${0}`]);
            } else {
                this.router.navigate([`/eleicao/substituicao/acompanhar-uf/${this.pedido.chapaEleicao.idCauUf}`]);
            }
        } else {
            this.router.navigate([`/eleicao/substituicao/acompanhar-responsavel-chapa`]);
        }
    }

    /**
     * Muda variavel de controle de recurso 'isPossuiRecursoCadastrado', Quando o usuário salvar o recurso.
     * 
     * @param recurso 
     */
    public salvarRecurso(recurso: any): void {
        this.dadosServicoRecurso = recurso;
        this.isPossuiRecursoCadastrado = true;
        this.mudarAba(Constants.ABA_RECURSO);
    }

    /**
    * Verifica se o membro é  responsável.
    * @param id 
    */
    public isResponsavel(membro: any): boolean {
        if (membro) {
            return membro.situacaoResponsavel == true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se aba de recurso deve ser mostrada.
     */
    public isMostrarAbaRecurso(): boolean {
        let vigencia: number = this.isVigente(this.atividadeSecundaria.dataInicio, this.atividadeSecundaria.dataFim);
        return this.isPossuiRecursoCadastrado || (vigencia == 1 && this.isJulgamentoImdeferido());
    }

    /**
     * Verifica se aba de julgamento deve ser mostrada.
     */
    public isMostrarAbaJulgamento(): boolean {
        return this.pedido.isIniciadoAtividadeRecurso && this.pedido.statusSubstituicaoChapa.id != 1;
    }

    /**
     * Verifica se aba de julgamento 2ª instância deve ser mostrada.
     */
    public isMostrarAbaJulgamentoSegundaInstancia(): boolean {
        return this.pedido.isFinalizadoAtividadeJulgamentoRecurso && this.pedido.statusSubstituicaoChapa.id > 4;
    }

    /**
     * Verifica se o status do pedido de subtituição é igual a indeferido.
     */
    public isJulgamentoImdeferido(): boolean {
        return this.pedido.statusSubstituicaoChapa.id == Constants.INDEFERIDO;
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

    /**
     * Verifica o status de Validação do Membro.
     * 
     * @param membro 
     */
    public statusValidacao(membro): boolean {
        if (membro) {
            return membro.statusValidacaoMembroChapa.id == Constants.STATUS_SEM_PENDENCIA;
        } else {
            return false;
        }
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
     * Retorna um valor de parâmetro passado na rota
     * @param nameParam 
     */
    private getValorParamDoRoute(nameParam) {
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
     * Método responsável por controlar as abas e chamas os serviços de dados
     * @param identificadorAba 
     */
    public mudarAba(identificadorAba: number): void {
        if (identificadorAba === Constants.ABA_PEDIDO_SUBSTITUICAO) {
            this.controleEstadoAba(identificadorAba);
        }
        if (identificadorAba === Constants.ABA_JULGAMENTO_PRIMEIRA_INSTANCIA) {

            if (this.tipoProfissional === Constants.TIPO_PROFISSIONAL_COMISSAO) {
                this.getDadosJulgamentoPrimeiraInstanciaComissao(this.pedido.id, identificadorAba);
            }

            if (this.tipoProfissional === Constants.TIPO_PROFISSIONAL_CHAPA) {
                this.getDadosJulgamentoPrimeiraInstanciaResponsávelChapa(this.pedido.id, identificadorAba);
            }
        }
        /**
         * Identificadores de abas segunda instância
         */
        if (identificadorAba === Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA) {
            if (this.tipoProfissional === Constants.TIPO_PROFISSIONAL_COMISSAO) {
                this.getDadosJulgamentoSegundaInstanciaComissao(this.pedido.id, identificadorAba);
            }

            if (this.tipoProfissional === Constants.TIPO_PROFISSIONAL_CHAPA) {
                this.getDadosJulgamentoSegundaInstanciaResponsávelChapa(this.pedido.id, identificadorAba);
            }
        }

        if (identificadorAba === Constants.ABA_RECURSO) {
            this.getDadosInterposicaoRecurso(this.pedido.id, identificadorAba);
        }
    }

    /**
     * Método responsável por controlar os estados das abas do componente
     * @param identificadorAba 
     */
    public controleEstadoAba(identificadorAba: number): void {
        this.abas.abaRecurso = identificadorAba === Constants.ABA_RECURSO;
        this.abas.abaPedidoSubstituicao = identificadorAba === Constants.ABA_PEDIDO_SUBSTITUICAO;
        this.abas.abaJulgamentoPrimeiraInstancia = identificadorAba === Constants.ABA_JULGAMENTO_PRIMEIRA_INSTANCIA;
        this.abas.abaJulgamentoSegundaInstancia = identificadorAba === Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA;
    }


    /**
     * Método responsável por mudar de página de acordo com o ID da aba 
     * informado no componente filho.
     * O evento é acionado ao clicar no botão voltar do componente filho
     * @param evento 
     */
    public mudarAbaEventFilho(evento: any): void {
        this.mudarAba(evento);
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------------
     * -----------------------------------------  Julgamento de 1ª Instância ---------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------
     */


    /**
     * Retorna os dados do julgamento de primeira instância para membros da comissão e muda de aba
     * @param id 
     * @param identificadorAba 
     */
    public getDadosJulgamentoPrimeiraInstanciaComissao(id: number, identificadorAba: number): void {
        if (this.isProcessaDadosJulgamento) {
            this.acompanharJulgamentoSubstituicaoClient.julgamentoSubstituicaoComissao(id).subscribe(
                data => {
                    this.dadosServicoJulgamento = data;
                    this.isProcessaDadosJulgamento = false;
                    this.controleEstadoAba(identificadorAba);
                }
            );
        } else {
            this.controleEstadoAba(identificadorAba);
        }
    }

    /**
     * Retorna os dados do julgamento de primeira instância para responsaveis da chapa
     * @param id 
     * @param identificadorAba 
     */
    public getDadosJulgamentoPrimeiraInstanciaResponsávelChapa(id: number, identificadorAba: number): void {
        if (this.isProcessaDadosJulgamento) {
            this.acompanharJulgamentoSubstituicaoClient.julgamentoSubstituicaoChapa(id).subscribe(
                data => {
                    this.dadosServicoJulgamento = data;
                    this.isProcessaDadosJulgamento = false;
                    this.controleEstadoAba(identificadorAba);
                }
            );
        } else {
            this.controleEstadoAba(identificadorAba);
        }
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------------
     * -----------------------------------------  Julgamento de 2ª Instância ---------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------
     */

    /**
     * Retorna os dados do julgamento de segunda instância para membros da comissão e muda de aba
     * @param id 
     * @param identificadorAba 
     */
    public getDadosJulgamentoSegundaInstanciaComissao(id: number, identificadorAba: number): void {
        if (this.isProcessaDadosJulgamentoSegundaInstancia) {
            this.acompanharJulgamentoSubstituicaoClient.julgamentoSubstituicaoComissaoSegundaInstancia(id).subscribe(
                data => {
                    this.dadosServicoJulgamentoSegundaInstancia = data;
                    this.isProcessaDadosJulgamentoSegundaInstancia = false;
                    this.controleEstadoAba(identificadorAba);
                }
            );
        } else {
            this.controleEstadoAba(identificadorAba);
        }
    }
    /**
     * Retorna os dados do julgamento de segunda instância para responsaveis da chapa
     * @param id
     * @param identificadorAba
     */
    public getDadosJulgamentoSegundaInstanciaResponsávelChapa(id: number, identificadorAba: number): void {
        if (this.isProcessaDadosJulgamentoSegundaInstancia) {
            this.acompanharJulgamentoSubstituicaoClient.julgamentoSubstituicaoChapaSegundaInstancia(id).subscribe(
                data => {
                    this.dadosServicoJulgamentoSegundaInstancia = data;
                    this.isProcessaDadosJulgamentoSegundaInstancia = false;
                    this.controleEstadoAba(identificadorAba);
                }
            );
        } else {
            this.controleEstadoAba(identificadorAba);
        }
    }


    // Será removido posteriormente
    public mudarAbaJulgamento(evento: any): void {
        this.mudarAba(evento);
    }


    /**
     * -------------------------------------------------------------------------------------------------------------------------
     * ---------------------------------------------------  Recurso  -----------------------------------------------------------
     * -------------------------------------------------------------------------------------------------------------------------
     */


    /**
     * Retorna os dados de interposicao de recurso
     * @param id 
     * @param identificadorAba 
     */
    public getDadosInterposicaoRecurso(id: number, identificadorAba: number): void {
        if (this.isProcessaDadosRecurso) {
            this.acompanharRecursoSubstituicaoClientService.recursoSubstituicao(id).subscribe(
                data => {
                    this.dadosServicoRecurso = data;
                    this.isProcessaDadosRecurso = false;
                    this.controleEstadoAba(identificadorAba);
                }
            );
        } else {
            this.controleEstadoAba(identificadorAba);
        }
    }
}
