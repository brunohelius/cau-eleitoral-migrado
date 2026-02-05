import { NgForm } from '@angular/forms';
import { MessageService } from '@cau/message';
import { ActivatedRoute } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, Input, ViewChild, TemplateRef } from '@angular/core';

import { JulgamentoImpugnacaoService } from 'src/app/client/julgamento-impugnacao-client.service.ts/julgamento-impugnacao-client.service';

@Component({
    selector: 'aba-recurso-responsavel',
    templateUrl: './aba-recurso-responsavel.component.html',
    styleUrls: ['./aba-recurso-responsavel.component.scss']
})
export class AbaRecursoResponsavelComponent implements OnInit {

    @Input() isIes: any;
    @Input() recurso: any;
    @Input() julgamento: any;
    @Input() recursoImpugnante: any;
    @Input() isFinalizadoAtividadeContrarrazao: any;

    
    @ViewChild('templateCadastroContrarrazao', { static: true }) private templateCadastroContrarrazao: TemplateRef<any>;
    public templateCadastroContrarrazaoModalRef: BsModalRef;

    @ViewChild('templateContrarrazaoCadastrado', { static: true }) private templateContrarrazaoCadastrado: TemplateRef<any>;
    public templateContrarrazaoCadastradoModalRef: BsModalRef;

    public hasRecurso: boolean;
    public isContrarrazaoAberto: boolean = false;
    public isContrarrazaoSubmetido: boolean = false;
    public isRecursoImpugnanteAberto: boolean = false;

    public contrarrazao: any;

    /**
     * Método contrutor da classe
     */
    constructor(
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private messageService: MessageService,
        private recursoJulgamentoService: JulgamentoImpugnacaoService,
    ) {

    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.hasRecurso = this.recurso != undefined;
    }

    /**
     * Verifica se sub-aba de recurso de Impugnante deve ser exibida.
     */
    public isMostrarRecursoImpugnante(): boolean {
        return this.recursoImpugnante;
    }

    /**
     * Abri ou fechar aba de recurso de Responsável.
     */
    public abrirRecursoImpugnante(): void {
        this.isRecursoImpugnanteAberto = !this.isRecursoImpugnanteAberto;
    }

    public abrirContrarrazao(): void {
        this.isContrarrazaoAberto = !this.isContrarrazaoAberto;
    }

    /**
      * Cancela Cadasto de contrarrazão.
      * 
      * @param form 
      */
    public cancelarContrarrazao(form: NgForm): void {
        this.inicializarContrarrazao();
        this.isContrarrazaoSubmetido = false;
        this.templateCadastroContrarrazaoModalRef.hide();
    }

    /**
     * Cadasta recurso para julgamento de substituição.
     * 
     * @param form 
     */
    public salvarContrarrazao(form: NgForm): void {
        this.isContrarrazaoSubmetido = true;
        if (form.valid) {
            this.recursoJulgamentoService.salvarContrarrazaoRecursoImpugnacao(this.contrarrazao).subscribe(
                data => {
                    this.contrarrazao = data;
                    this.templateCadastroContrarrazaoModalRef.hide();
                    this.templateContrarrazaoCadastradoModalRef = this.modalService.show(this.templateContrarrazaoCadastrado, Object.assign({}, { class: 'modal-lg modal-dialog-centered', focus: false }));
                    this.recursoImpugnante.contrarrazaoRecursoImpugnacao = data;
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
     * Confirmar cadastro de Contrarrazao.
     */
    public concluirCadastroContrarrazao(): void {
        this.templateCadastroContrarrazaoModalRef.hide();
        this.templateContrarrazaoCadastradoModalRef.hide();
    }

    /**
     * Abrir Modal para cadastro de Contrarrazao.
     */
    public cadastrarContrarrazao(): void {
        this.inicializarContrarrazao();
        this.templateCadastroContrarrazaoModalRef = this.modalService.show(this.templateCadastroContrarrazao, Object.assign({}, { class: 'modal-xl modal-dialog-centered', focus: false }));
    }

    /**
     * Retorna a label certa de acordo com o tipo de recurso/reconsideração.
     */
    public labelDescricao(): any {
        return this.isIes ? 'LABEL_RECONSIDERACAO_PEDIDO_IMPUGNACAO_1_INSTANCIA' : 'LABEL_RECURSO_PEDIDO_IMPUGNACAO_1_INSTANCIA'
    }

    /**
    * Inicializar Contrarrazão.
    */
    public inicializarContrarrazao(): void {
        this.contrarrazao = {
            descricao: '',
            arquivos: [],
            idRecursoImpugnacao: this.recursoImpugnante.id,
            idJulgamentoImpugnacao: this.julgamento.id
        };
    }

    /**
     * Realiza download de arquivo de recurso de impugnação.
     */
    public downloadArquivoRecurso(download: any, id: number): void {
        this.recursoJulgamentoService.getArquivoRecurso(id).subscribe(
            data => {
                download.evento.emit(data);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
   * Baixar arquivo de Contrarrazao.
   * 
   * @param arquivo 
   */
    public downloadArquivoContrarrazao(download: any, id?: number) {
        if (id != undefined) {
            this.recursoJulgamentoService.getArquivoContrarrazao(id).subscribe(
                data => {
                    download.evento.emit(data);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        } else {
            download.evento.emit(download.arquivo);
        }
    }

    /**
     * Retorna mensagem de quantidade maxima de arquivos.
     */
    public getMsgMaxArquivos(): string {
        return this.messageService.getDescription('MSG_PERMISSAO_UPLOAD_N_ARQUIVOS', ['um', 1]);
    }

    /**
   * Verifica se pode ou não mostrar a contrarrazão.
   */
    public isMostrarContrarrazao(): boolean {
        return this.recursoImpugnante.contrarrazaoRecursoImpugnacao != undefined || this.isFinalizadoContrarrazao();
    }

    /**
   * Verifica se pode ou não mostrar a contrarrazão.
   */
    public isContrarrazaoCadastada(): boolean {
        return this.recursoImpugnante.contrarrazaoRecursoImpugnacao != undefined;
    }

    /**
   * Verifica se pode ou não mostrar o botão da contrarrazão.
   */
    public isMostrarBotaoContrarrazao(): boolean {
        return this.isResponsavelChapa() && 
        this.recursoImpugnante.podeCadastrarContrarrazao &&
        this.recursoImpugnante.contrarrazaoRecursoImpugnacao == undefined;
    }

    /**
    * Verifica se o usuário logado é responsavel pela chapa.
    */
    public isResponsavelChapa(): boolean {
        return this.getValorParamDoRoute('tipoProfissional') === Constants.TIPO_PROFISSIONAL_CHAPA;
    }

    /**
     * Retorna um valor de parâmetro passado na rota
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
     * Verifica se deve ou não mostrar a mensagem que n ouve contrarrazão.
     */
    public isFinalizadoContrarrazao():boolean {
      return this.isFinalizadoAtividadeContrarrazao &&
      this.recursoImpugnante.contrarrazaoRecursoImpugnacao == undefined;
    }
}
