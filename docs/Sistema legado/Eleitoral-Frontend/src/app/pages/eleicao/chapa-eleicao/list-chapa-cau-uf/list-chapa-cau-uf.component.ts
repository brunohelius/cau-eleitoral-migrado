import { ActivatedRoute, Router } from "@angular/router";
import { MessageService } from '@cau/message';
import { Component, OnInit, TemplateRef, EventEmitter } from '@angular/core';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-client/chapa-eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { BsModalRef, BsModalService } from 'ngx-bootstrap';
import { NgForm } from '@angular/forms';
import { SecurityService } from '@cau/security';
import { LayoutsService } from '@cau/layout';

/**
 * Componente responsável pela apresentação de listagem de Chapas por CAUUF.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'list-chapa-cau-uf',
    templateUrl: './list-chapa-cau-uf.component.html',
    styleUrls: ['./list-chapa-cau-uf.component.scss']
})
export class ListChapaCauUfComponent implements OnInit {

    public search: string;
    public chapas: Array<any>;
    public _chapas: Array<any>;
    private cauUfs: Array<any>;
    public cauUf: any;
    public exclusao: any;
    public alteraracaoStatus: any;
    public submitted: boolean;
    public chapaSelecionada: any;
    public statusExtrato: number;
    public isAlterarNumerosChapas: boolean;
    private chapasComNumerosAlterados: Array<number> = [];

    public modalRef: BsModalRef;

    private idCalendario: number;
    private idCauUf: string;

    public limitesPaginacao: Array<any>;
    public limitePaginacao: number;

    public  STATUS_CHAPA_CONCLUIDO: number;
    public  STATUS_CHAPA_EM_ANDAMENTO: number;

    /**
     * Construtor da classe.
     *
     * @param route
     * @param messageService
     * @param chapaEleicaoService
     */
    constructor(
        private route: ActivatedRoute,
        private router: Router,
        private layoutsService: LayoutsService,
        private messageService: MessageService,
        private chapaEleicaoService: ChapaEleicaoClientService,
        private securityService: SecurityService,
        private modalService: BsModalService,
    ) {
        this.chapas = route.snapshot.data["chapas"];
        this._chapas = this.chapas;
        this.cauUfs = route.snapshot.data["cauUfs"];
        this.idCalendario = route.snapshot.params.id;
        this.idCauUf = route.snapshot.params.idCauUf;
        this.STATUS_CHAPA_CONCLUIDO = Constants.STATUS_CHAPA_CONCLUIDO;
        this.STATUS_CHAPA_EM_ANDAMENTO = Constants.STATUS_CHAPA_EM_ANDAMENTO;
        this.statusExtrato = 0;
    }

    /**
     * Inicialização dos dados do campo
     */
    ngOnInit() {
        this.limitePaginacao = 10;
        this.limitesPaginacao = [10, 15, 20, 50];
        this.inicializaCauUf();
        this.exclusao = { chapa: {}, justificativa: "" };
        this.alteraracaoStatus = { idChapaEleicao: undefined, idStatusChapa: undefined, justificativa: undefined };
        this.isAlterarNumerosChapas = false;
    }

    /**
    * Busca imagem de bandeira do estado do CAUUF.
    *
    * @param idCauUf
    */
    public getImagemBandeira(idCauUf): String {
        let imagemBandeira = undefined;
        this.cauUfs.forEach(cauUf => {
            if (idCauUf == cauUf.id) {
                imagemBandeira = cauUf.imagemBandeira;
            } else if (idCauUf == 0 && cauUf.id == Constants.ID_CAUBR) {
                imagemBandeira = cauUf.imagemBandeira;
            }
        });
        return imagemBandeira;
    }

    /**
     * Verifica se a visualização de chapa é IES.
     */
    public isIES(): boolean {
        return this.idCauUf == '0';
    }

    /**
     * Verifica se a ação de excluir chapa está desabilitada.
     */
    public isDisabledAcaoExcluirChapa(): boolean {
        return !this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    }

    /**
     * Verifica se a ação de Alterar Status da Chapa está desabilitada.
     */
    public isDisabledAcaoAlterarStatusChapa(): boolean {
        return !this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
    }

    /**
     * Verifica se o usuário tem permissão para acessar o extrato da chapa.
     */
    public isMostrarExtratoChapa(): boolean {
        if (this.isIES()) {
            return this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
        }
        else {
            return (this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
                || (this.idCauUf == this.securityService.credential.user.cauUf.id
                && this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE])));
        }
    }

        /**
     * Verifica se o usuário tem permissão para alterar o número da chapa.
     */
    public isMostrarAlterarNumeroChapa(): boolean {
        if (this.isIES()) {
            return this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN]);
        }
        else {
            return (this.securityService.hasRoles([Constants.ROLE_ACESSOR_CEN])
                || (this.idCauUf == this.securityService.credential.user.cauUf.id
                && this.securityService.hasRoles([Constants.ROLE_ACESSOR_CE])));
        }
    }

    /**
     * Excluir chapa.
     *
     * @param chapa
     */
    public excluirChapa(template: TemplateRef<any>, chapa: any): void {
        this.exclusao.justificativa = '';
        this.messageService.addConfirmYesNo('MSG_DESEJA_REALMENTE_EXCLUIR_CHAPA_SELECIONADA', () => {
            this.chapaSelecionada = chapa;
            this.mostrarModalJustificativa(template);
        });
    }

    /**
     * Apresenta modal para justificar alteração do status da chapa.
     *
     * @param template
     * @param chapa
     */
    public alterarStatusChapa(template: TemplateRef<any>, chapa: any): void {
        this.alteraracaoStatus.idChapaEleicao = chapa.idChapaEleicao;
        this.alteraracaoStatus.idStatusChapa = chapa.idStatusChapa;
        this.alteraracaoStatus.justificativa = '';
        this.mostrarModalAlterarStatusChapa(template);
    }

    /**
     * Habilita alteração de números da chapa.
     */
    public alterarNumerosChapas(): void {
        this.chapasComNumerosAlterados = [];
        this.isAlterarNumerosChapas = !this.isAlterarNumerosChapas;
    }

    /**
     * Habilitar e desabilitar edição do número da chapa.
     *
     * @param chapa
     */
    public isAlterarNumerosChapa(chapa: any): boolean {
        return this.isAlterarNumerosChapas && this.chapasComNumerosAlterados.find(
            idChapa =>  chapa.idChapaEleicao == idChapa
        ) == undefined;
    }

    /**
     * Atualizar número de chapa.
     *
     * @param numeroChapa
     */
    public updateNumeroChapa(chapa: any, event): void {
        if(event.target.value.toString().trim()) {
            this.chapaEleicaoService.salvarNumeroChapa({ id: chapa.idChapaEleicao, numero: event.target.value }).subscribe(
                data => {
                    //this.messageService.addMsgSuccess(data);
                    chapa.numeroChapa =  event.target.value;
                    this.chapasComNumerosAlterados.push(chapa.idChapaEleicao);
                },
                error => {
                    this.messageService.addMsgDanger(error);
                }
            );
        }

    }

    public getRotaAcessarChapa(chapa: any) {
        this.router.navigate(['eleicao', 'chapa', chapa.idChapaEleicao,'visualizar-chapa']);
    }

    /**
     * Salva justificativa para exclusão de chapa.
     *
     * @param form
     */
    public salvarJustificativaExclusao(form: NgForm): void {
        this.submitted = true;

        if (form.valid) {
            this.chapaEleicaoService.excluirChapa(this.chapaSelecionada.idChapaEleicao, this.exclusao).subscribe(data => {
                this.submitted = false;
                this.messageService.addMsgSuccess('MSG_CHAPA_EXCLUIDA_COM_SUCESSO');
                this.removerChapaPorId(this.chapaSelecionada.idChapaEleicao);
            }, error => {
                this.messageService.addMsgDanger(error);
            });

            this.modalRef.hide();
        }
    }

    /**
     * Salva justificativa para alteração de status chapa.
     *
     * @param form
     */
    public salvarJustificativaAlteraracaoStatusChapa(form: NgForm): void {
        this.submitted = true;

        if (form.valid) {
            this.chapaEleicaoService.alteraracaoStatusChapa(this.alteraracaoStatus.idChapaEleicao, this.alteraracaoStatus).subscribe(
                data => {
                    this.submitted = false;
                    this.alterarStatusChapaEleicao(this.alteraracaoStatus.idChapaEleicao, this.alteraracaoStatus.idStatusChapa);
                }, error => {
                    this.messageService.addMsgDanger(error);
                }
            );
            this.modalRef.hide();
        }
    }

    /**
     * Remove a chapa selecionada no array de chapas.
     *
     * @param idChapaEleicao
     */
    private removerChapaPorId(idChapaEleicao: number): void {
        this.chapas = this.chapas = this.chapas.filter(chapa => {
            return chapa.idChapaEleicao != idChapaEleicao;
        });
    }

    /**
     * Altera o status da chapa selecionado no array de chapas.
     *
     * @param idChapaEleicao
     * @param statusChapa
     */
    private alterarStatusChapaEleicao(idChapaEleicao: number, statusChapa: number): any {
        let chapa: any = this.chapas.find(chapa => {
            return chapa.idChapaEleicao == idChapaEleicao;
        });

        if (chapa != undefined) {
            chapa.idStatusChapa = statusChapa;
        }

        return chapa;
    }

    /**
     * Exibir modal de seleção de status de chapas do extrato.
     *
     * @param template
     */
    public mostrarModalStatusExtrato(template: TemplateRef<any>): void {
        this.statusExtrato = 0;
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: 'modal-lg' }));
    }

    /**
     * Baixar extrato de listagem dos membros das chapas.
     *
     * @param form
     */
    public confirmarStatusExtrato(event: EventEmitter<any>, form: NgForm): void {
        let filtro: any = {
            idCauUf: this.idCauUf,
            idStatus: this.statusExtrato,
        };
        this.chapaEleicaoService.gerarExtratoChapa(this.idCalendario, filtro).subscribe(
            (data: Blob) => {
                event.emit(data);
                this.modalRef.hide();
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Gera arquivo json utilizada no extrato navegável de chapa.
     */
    public gerarExtratoChapaNavegavel(): void {
        let filtro: any = {
            idCauUf: this.idCauUf,
            idStatus: this.statusExtrato,
            idCalendario: this.idCalendario
        };
        this.chapaEleicaoService.gerarExtratoChapaNavegavel(filtro).subscribe(
            (data) => {
                this.modalRef.hide();
                window.open('/publico/extrato-chapa/'+this.idCauUf, '_blank');
            }, error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Mostra modal para justificar exclusão da chapa.
     *
     * @param template
     * @param idEmail
     */
    private mostrarModalJustificativa(template: TemplateRef<any>) {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: ' ' }));
    }

    /**
     * Mostra modal para alteração do status da chapa.
     *
     * @param template
     */
    private mostrarModalAlterarStatusChapa(template: TemplateRef<any>): void {
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: ' ' }));
    }

    /**
     * Mostra modal para justificar alteração do status da chapa.
     *
     * @param template
     * @param chapa
     */
    public mostrarModalJustificativaAlterarStatusChapa(template: TemplateRef<any>): void {
        this.modalRef.hide();
        this.modalRef = this.modalService.show(template, Object.assign({}, { class: '' }));
    }

    /**
    * Filtra os dados da grid conforme o valor informado na variável search.
    *
    * @param search
    */
    public filter(search) {
        let filterItens = this._chapas.filter((data) => {
            let textSearch = this.getSeachArray(data).join().toLowerCase();
            return textSearch.indexOf(search.toLowerCase()) !== -1
        });
        this.chapas = filterItens;
    }

    /**
     * Cria array utilizado para buscar de termos na listagem de chapas.
     *
     * @param obj
     */
    private getSeachArray(obj: any): Array<any> {
        let values: Array<any> = [];
        values.push(this.getNumeroChapa(obj.numeroChapa));;
        values.push(obj.membrosResponsaveis);
        values.push(obj.quantidadeTotalMembrosChapa);
        values.push(obj.quantidadeMembrosConfirmados);
        values.push(this.getDescricaoStatusChapa(obj.idStatusChapa));
        return values;
    }

    /**
     * Retorna descrição do status da chapa.
     *
     * @param idChapa
     */
    private getDescricaoStatusChapa(idChapa: number): string {
        return this.isChapaPendente(idChapa) ? this.messageService.getDescription('LABEL_COM_PENDENCIA') : this.messageService.getDescription('LABEL_CONCLUIDA');
    }

    /**
     * Retorna número da chapa.
     *
     * @param numeroChapa
     */
    public getNumeroChapa(numeroChapa: string): string {
        return numeroChapa ? numeroChapa.toString().padStart(2,'0') : this.messageService.getDescription('LABEL_NAO_APLICADO');
    }

    /**
     * Verifica se o status da chapa é pendente.
     *
     * @param idStatusChapa
     */
    public isChapaPendente(idStatusChapa: number): boolean {
        return idStatusChapa == Constants.ID_STATUS_CHAPA_PENDENTE;
    }

    /**
     * Inicializa lista de CAU UF.
     */
    public inicializaCauUf() {
        let cauUfEncontrado = undefined;
        this.cauUfs.forEach(cauUf => {
            if (this.idCauUf == cauUf.id) {
                cauUfEncontrado = cauUf;
            } else if (this.idCauUf == '0' && cauUf.id == Constants.ID_CAUBR) {
                cauUfEncontrado = cauUf;
                cauUfEncontrado.prefixo = Constants.IES;
            }
        });
        this.cauUf = cauUfEncontrado;
    }

    /**
     * Volta a pagina anterior.
     */
    public voltar() {
        this.router.navigate(['eleicao', this.idCalendario, 'chapa', 'listar']);
    }

    /**
     * Volta a pagina inicial.
     */
    public inicio() {
        this.router.navigate(['/']);
    }

    /**
     *
     * @param limitePaginacao
     */
    public onChangeLimitePaginacao(limitePaginacao: any): void {

    }

    /**
   * Retorna texto de hint de informativo do número da chapa.
   */
  public getHintInformativoNumeroChapa = () => {
    return this.messageService.getDescription('MSG_HINT_INFORMATIVO_NUMERO_CHAPA');
  }
}