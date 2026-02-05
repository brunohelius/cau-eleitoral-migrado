import { Component, Renderer2, ElementRef, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { Constants } from 'src/app/constants.service';
import { MessageService } from '@cau/message';
import { Router } from '@angular/router';
import { Observable } from 'rxjs';
import * as deepEqual from "deep-equal";
import * as _ from 'lodash';
import { ChapaEleicaoClientService } from 'src/app/client/chapa-eleicao-client/chapa-eleicao-client.service';


@Component({
    selector: 'aba-membros-chapa',
    templateUrl: './aba-membros-chapa.component.html',
    styleUrls: ['./aba-membros-chapa.component.scss']
})
export class AbaMembrosChapaComponent implements OnInit {

    @Input() eleicao: any;
    @Input() chapaEleicao: any;
    @Output() avancar: EventEmitter<any> = new EventEmitter();
    @Output() retroceder: EventEmitter<any> = new EventEmitter();
    @Output() cancelar: EventEmitter<any> = new EventEmitter();
    @Output() voltar: EventEmitter<any> = new EventEmitter();

    public pesquisa: any;
    public selected: any;
    public membrosChapas: any[] = [];
    private _memembrosChapas: any[] = [];
    public nomeMembrosChapa: any[] = [];

    public limitePaginacao: number;
    public limiteResponsaveis: number;
    public limitesPaginacao: Array<number>;

    private renderer: Renderer2;
    private el: ElementRef;


    /**
     * Construtor da classe.
     *
     * @param router
     * @param messageService
     * @param chapaEleicaoService
     */
    constructor(
        private router: Router,
        private messageService: MessageService,
        private chapaEleicaoService: ChapaEleicaoClientService
    ) {

    }

    /**
     * Executado ao inicializar o componente.
     */
    ngOnInit() {
        this.inicializarMembrosChapa();
        this.limitePaginacao = 10;
        this.limiteResponsaveis = 3;
        this.limitesPaginacao = [10, 20, 30, 50, 100];
        if (this.el.nativeElement) {
            this.renderer.selectRootElement(this.el.nativeElement).focus();
        }
    }

    /**
     * Função chamada quando o método anterior é chamado.
     */
    public anterior(): void {
        let controle: any = { isAlterado: this.isCamposAlterados(), aba: Constants.ABA_PLATAFORMA_ELEITORAL_REDES_SOCIAIS };
        this.retroceder.emit(controle);
    }

    /**
     * Enviar e valida dados dos membros da chapa.
     */
    public avancarDeclaracao(): void {
        let avancarDeclaracaoData: Array<any> = this.getavancarDeclaracaoData();
        this.chapaEleicaoService.salvarMembros(this.chapaEleicao.id, avancarDeclaracaoData).subscribe(
            data => {
                this.avancar.emit(Constants.ABA_DECLARACAO);
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }


    /**
     * Retorna dados para validação da aba de membros da chapa.
     */
    private getavancarDeclaracaoData(): Array<any> {
        let avancarDeclaracaoData: Array<any> = [];
        this.membrosChapas.forEach(
            membro => {
                if (membro.titular.id) {
                    avancarDeclaracaoData.push({ id: membro.titular.id, situacaoResponsavel: membro.titular.situacaoResponsavel });
                }

                if (membro.suplente.id) {
                    avancarDeclaracaoData.push({ id: membro.suplente.id, situacaoResponsavel: membro.suplente.situacaoResponsavel });
                }
            }
        );
        return avancarDeclaracaoData;
    }

    /**
    * Incluir membro chapa selecionado.
    *
    * @param event
    */
    public adicionarMembroChapa(membro: any, membroAtual: any, codTipoMembroChapa?: number): void {
        this.chapaEleicaoService.incluirMembro(this.chapaEleicao.id,
            {
                idProfissional: membro.profissional.id,
                idTipoParticipacao: membro.membroChapa.tipoParticipacaoChapa.id,
                idTipoMembroChapa: membro.membroChapa.tipoMembroChapa.id,
                numeroOrdem: membro.membroChapa.numeroOrdem
            }).subscribe(
                data => {
                    this.substituirMembroChapa(data);
                    this.inicializarMembrosChapa();
                },
                error => {

                    if (codTipoMembroChapa == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR) {
                        membroAtual.titular_txtPesquisa = '';
                    }

                    if (codTipoMembroChapa == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE) {
                        membroAtual.suplente_txtPesquisa = '';
                    }

                    this.messageService.addMsgDanger(error);

                });
    }

    /**
     * Substituir membro chapa em array local da Chapa eleitoral.
     *
     * @param novoMembro
     */
    private substituirMembroChapa(novoMembro: any): void {
        let naoEncontrado: boolean = true;
        this.chapaEleicao.membrosChapa = this.chapaEleicao.membrosChapa.map(membro => {
            if (membro.numeroOrdem == novoMembro.numeroOrdem && membro.tipoParticipacaoChapa.id == novoMembro.tipoParticipacaoChapa.id) {
                naoEncontrado = false;
                return novoMembro;
            }
            return membro;
        });

        if (naoEncontrado) {
            this.chapaEleicao.membrosChapa.push(novoMembro);
        }
    }

    /**
     * Valida responsáveis pela chapa.
     */
    public validarResponsavel(): void {
        if (this.getNumeroResponsaveis() > this.limiteResponsaveis) {
            this.messageService.addMsgWarning('MSG_AVISO_NUMERO_MAXIMO_RESPONSAVEIS', [this.limiteResponsaveis]);
        }
    }

    /**
     * Retorna o total de responsáveis pela chapa.
     */
    private getNumeroResponsaveis(): number {
        let total: number = 0;
        this.membrosChapas.forEach(
            membro => {
                total += (membro.titular.situacaoResponsavel ? 1 : 0);
                total += (membro.suplente.situacaoResponsavel ? 1 : 0);
            }
        );
        return total;
    }

    /**
     * Retorna placeholder utilizado no autocomplete de profissional.
     *
     * @param membro
     */
    public getPlaceholderAutoCompleteProfissional(membro): string {
        let msg: string;
        if (this.isConselheiroUfBR()) {
            if (membro.numeroOrdem == 0) {
                msg = (membro.tipoParticipacaoChapa.id == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR) ? 'LABEL_INSIRA_CPF_NOME_CONSELHEIRO_FEDERAL' : 'LABEL_INSIRA_CPF_NOME_SUPLENTE';
            } else {
                msg = (membro.tipoParticipacaoChapa.id == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR) ? 'LABEL_INSIRA_CPF_NOME_MEMBRO' : 'LABEL_INSIRA_CPF_NOME_SUPLENTE';
            }
        } else {
            msg = (membro.tipoParticipacaoChapa.id == Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR) ? 'LABEL_INSIRA_CPF_NOME_TITULAR_IES' : 'LABEL_INSIRA_CPF_NOME_SUPLENTE_IES';
        }
        return this.messageService.getDescription(msg);
    }

    /**
     * Retorna lista de Conselheiros federais da chapa eleitoral.
     *
     * @param membrosChapas
     */
    public getConselheirosFederais(membrosChapas: Array<any>): Array<any> {
        return membrosChapas.slice(0, 1);
    }

    /**
     * Retorna lista de Conselheiros estaduais da chapa eleitoral.
     *
     * @param membrosChapas
     */
    public getConselheirosEstaduais(membrosChapas: Array<any>): Array<any> {
        return membrosChapas.slice(1);
    }

    /**
     * Retorna lista de representantes IES da chapa eleitoral.
     *
     * @param membrosChapas
     */
    public getRepresentantesIES(membrosChapas: Array<any>): Array<any> {
        return membrosChapas;
    }

    /**
     * Válida se houve alteração em algum campo do formulário.
     */
    public isCamposAlterados(): boolean {
        return !deepEqual(this.membrosChapas, this._memembrosChapas);
    }

    /**
     * Válida se é "Conselheiro UF-BR".
     */
    public isConselheiroUfBR(): boolean {
        return this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR;
    }

    /**
     * Válida se é "IES".
     */
    public isConselheiroIES(): boolean {
        return this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
    }

    /**
     * Verifica se o membro da chapa é do tipo Conselheiro Estadual.
     *
     * @param id
     */
    public isConselheirosEstaduais(id: number): boolean {
        return id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL;
    }

    /**
    * Verifica se o membro da chapa é do tipo Conselheiro Federal.
    *
    * @param id
    */
    public isConselheirosFederais(id: number): boolean {
        return id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL;
    }

    /**
    * Válida se a chapa está concluída.
    */
    public isChapaConcluida(): boolean {
        return this.chapaEleicao.idEtapa == Constants.STATUS_CHAPA_ETAPA_CONCLUIDO;
    }

    /**
    * Verifica se o membro da chapa confirmou participação.
    *
    * @param idStatus
    */
    public isStatusParticipacaoChapaConfirmado(idStatus: number): boolean {
        return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO;
    }

    /**
     * Verifica se o membro da chapa rejeitou participação.
     *
     * @param idStatus
     */
    public isStatusParticipacaoChapaRejeitado(idStatus: number): boolean {
        return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO;
    }

    /**
     * Verifica se o membro da chapa tem participação pendente.
     *
     * @param idStatus
     */
    public isStatusParticipacaoChapaPendente(idStatus: number): boolean {
        return idStatus === Constants.STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO;
    }

    /**
     * Verifica se o membro da chapa tem status de validação igual a valido.
     *
     * @param idStatus
     */
    public isStatusValidacaoMembroChapaValido(idStatus: number): boolean {
        return idStatus == Constants.STATUS_MEMBRO_CHAPA_VALIDO;
    }

    /**
     * Verifica se o membro da chapa tem status de validação igual a pendente.
     *
     * @param idStatus
     */
    public isStatusValidacaoMembroChapaPendente(idStatus: number): boolean {
        return idStatus == Constants.STATUS_MEMBRO_CHAPA_PENDENTE;
    }

    /**
     * Verifica se o campo de responsável está desabilitado.
     *
     * @param membroChapa
     */
    public isInputResponsavelDesabilitado(membroChapa: any): boolean {
        return this.chapaEleicao.idProfissionalInclusao == membroChapa.profissional.id || this.isChapaConcluida();
    }

    /**
     * Retorna texto de hint apresentado na tela de cadastro da chapa eleitoral.
     */
    public getHintCadastroChapa(): string {
        return this.messageService.getDescription('MSG_HINT_CADASTRO_MEMBROS_CHAPA_ELEITORAL');
    }

    /**
     * Retorna o código do membro chapa titular.
     */
    public getCodigoTitular(): number {
        return Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR;
    }

    /**
     * Retorna o código do membro chapa suplente.
     */
    public getCodigoSuplente(): number {
        return Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE;
    }

    /**
     * Inicializa os membros chapa.
     */
    private inicializarMembrosChapa(): void {
        this.membrosChapas = [];
        this.chapaEleicao.membrosChapa = this.chapaEleicao.membrosChapa ? this.chapaEleicao.membrosChapa : [];

        if (this.chapaEleicao.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_UF_BR) {
            let totalMembrosChapa = this.chapaEleicao.numeroProporcaoConselheiros;

            for (var i = 0; i <= totalMembrosChapa; i++) {
                let titular: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, i);
                let suplente: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, i);

                this.membrosChapas.push({
                    titular: titular,
                    titular_txtPesquisa: titular.profissional ? titular.profissional.cpf : '',
                    suplente_txtPesquisa: suplente.profissional ? suplente.profissional.cpf : '',
                    suplente: suplente
                });
            }

        } else {
            let titular: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, 0);
            let suplente: any = this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, 0);

            this.membrosChapas = [
                {
                    titular: this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR, 0),
                    titular_txtPesquisa: titular.profissional ? titular.profissional.cpf : '',
                    suplente_txtPesquisa: suplente.profissional ? suplente.profissional.cpf : '',
                    suplente: this.findMembroChapa(Constants.TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE, 0)
                }
            ];
        }
        this._memembrosChapas = _.cloneDeep(this.membrosChapas);
    }

    /**
     * Retorna membro da chapa por posição tipo Participação.
     */
    private findMembroChapa(tipoParticipacao: number, posicao: number): any {
        let membroChapa = this.chapaEleicao.membrosChapa.find(membro => {
            if (posicao == 0) {
                return membro.tipoParticipacaoChapa.id == tipoParticipacao &&
                    (membro.tipoMembroChapa.id == Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL
                        || membro.tipoMembroChapa.id == Constants.TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES);
            }
            return membro.tipoParticipacaoChapa.id == tipoParticipacao && membro.numeroOrdem == posicao;
        });

        if (membroChapa == undefined) {
            membroChapa = {
                numeroOrdem: posicao,
                tipoParticipacaoChapa: { id: tipoParticipacao },
                tipoMembroChapa: { id: posicao == 0 ? Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL : Constants.TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL },
            };
        }

        return membroChapa;
    }

}
