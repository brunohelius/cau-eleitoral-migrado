import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { SecurityService } from '@cau/security';
import { Constants } from 'src/app/constants.service';
import { Router, ActivatedRoute } from '@angular/router';
import { BsModalService, BsModalRef } from 'ngx-bootstrap';
import { Component, OnInit, EventEmitter, Input, Output, TemplateRef, ViewChild } from '@angular/core';

import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'app-modal-cadastrar-recurso-substituicao',
    templateUrl: './modal-cadastrar-recurso-substituicao.component.html',
    styleUrls: ['./modal-cadastrar-recurso-substituicao.component.scss']
})
export class ModalCadastrarRecursoSubstituicaoComponent implements OnInit {

    @Input() isIes: any;
    @Input() substituicao: any;
    @Input() idSubstituicao: any;
    @Input() recursoReconsideracao: any;

    public modalRef: BsModalRef;
    public modalRecurso: BsModalRef;

    public user: any;
    public recurso: any;

    public submitted = false;

    @Output() voltarAba: EventEmitter<any> = new EventEmitter();
    @Output() redirecionarAposSalvamento = new EventEmitter<any>();

    @ViewChild('templateConfirmacao', { static: true }) private templateConfirmacao: any;

    /**
     * Construtor da classe.
     */
    constructor(
        private route: ActivatedRoute,
        private modalService: BsModalService,
        private messageService: MessageService,
        private securtyService: SecurityService,
        private julgamentoFinalService: JulgamentoFinalClientService
    ) {
        this.user = this.securtyService.credential.user;
    }

    /**
     * Inicialização das dependências do componente.
     */
    ngOnInit() {
        this.inicializarRecurso();
    }

    /**
     * Inicializar recurso de julgamento de substituição.
     */
    public inicializarRecurso(): void {
        this.recurso = {
        descricao: '',
        arquivos: [],
        idJulgamentoSegundaInstanciaSubstituicao: this.idSubstituicao,
        idProfissional: this.user.idProfissional,
        indicacoes: [],
        };
    }

    /**
     * Exibe modal de cadastro de recurso/reconsideracao.
     */
    public abrirModalRecurso(template: TemplateRef<any>): void {
        this.cancelarRecurso();
        this.modalRecurso = this.modalService.show(template, Object.assign({}, { class: 'modal-xl' }));
    }

    /**
     * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
     */
    public labelModalconfirmacao() {
        return this.isIes ? 'LABEL_DESCRICAO_RECONSIDERACAO_PEDIDO_SUBSTITUICAO' : 'LABEL_DESCRICAO_RECURSO_PEDIDO_SUBSTITUICAO';
    }

    /**
     * Responsavel por mudar o recurso ou a reconsideração de acordo com se e IES ou não.
     */
    public labelORecursoAReconsideracao() {
        return this.messageService.getDescription(this.isIes ? 'LABEL_A_RECONSIDERACAO' : 'LABEL_O_RECURSO');
    }

    /**
     * Responsavel pelo titulo do modal de confirmação.
     */
    public titleModalConfirmacao() {
        return this.messageService.getDescription('TITLE_CONFIRMAR_RECURSO_RECONSIDERACAO_SUBSTITUICAO', [this.recursoReconsideracao]);
    }

    /**
     * Responsavel por mudar o recurso ou reconsideração de acordo com se e IES ou não.
     */
    public msgConfirmacao() {
      return this.messageService.getDescription('MSG_PREZADO_RECURSO_RECONSIDERACAO_PEDIDO_SUBSTIUTICAO_CADASTRADO', [this.labelORecursoAReconsideracao()]);
    }

    /**
     * Responsavel por apagar todo o conteudo do modal.
     */
    public cancelarRecurso(): any {
        this.inicializarRecurso();
        this.submitted = false;
    }

    /**
     * Responsavel por apagar tudo que foi colocado no recurso quando sai do modal.
     */
    public fecharRecurso(): any {
        this.modalRecurso.hide();
    }

    /**
     * Responavel por salvar o recurso.
     */
    public salvarRecurso(): any {
        this.submitted = true;
        if (this.hasDescricao()) {
            this.julgamentoFinalService.salvarRecursoJulgamentoSubstituicao(this.recurso).subscribe(
                data => {
                    this.recurso = data;
                    this.modalRecurso.hide();
                    this.modalRef = this.modalService.show(
                        this.templateConfirmacao,
                        Object.assign({}, { class: 'modal-lg modal-dialog-centered' })
                    );
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }
    }

    /**
     * Redireciona o usuário para a tela de visualizar pedido de substituicao
     */
    public redirecionarVisualizarRecurso(event: any) {
        this.modalRef.hide();
        this.redirecionarAposSalvamento.emit(this.recurso);
    }

    /**
     * Baixar download de recurso.
     */
    public downloadModalRecurso(download: any): any {
        download.evento.emit(download.arquivo);
    }

    /**
     * Resoponsavel por adicionar a descricao que fora submetido no compomente editor de texto.
     */
    public adicionarDescricao(descricao: string): void {
      this.recurso.descricao = descricao;
    }

    /**
     * Resoponsavel por salvar o arquivo que foi submetido no componete arquivo.
     */
    public salvarArquivos(arquivos: any): void {
        this.recurso.arquivos = arquivos;
    }

    /**
     * Verifica se a discricao foi preencida.
     */
    public hasDescricao(): boolean {
        return this.recurso.descricao;
    }
}