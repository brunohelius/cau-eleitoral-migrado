import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { BsModalService } from 'ngx-bootstrap';
import { SecurityService } from '@cau/security';
import { Component, OnInit } from '@angular/core';
import { StringService } from 'src/app/string.service';
import { Router, ActivatedRoute } from '@angular/router';
import { format } from 'url';
import { NgForm } from '@angular/forms';
import { ImpugnacaoResultadoClientService } from 'src/app/client/impugnacao-resultado-client/impugnacao-resultado-client.service';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { Constants } from 'src/app/constants.service';
import { constants } from 'os';


@Component({
  selector: 'app-cadastro-impugnacao-resultado',
  templateUrl: './cadastro-impugnacao-resultado.component.html',
  styleUrls: ['./cadastro-impugnacao-resultado.component.scss']
})
export class CadastroImpugnacaoResultadoComponent implements OnInit {

    public atividade: any;
    public impugnacao: any;
    public profissional: any;
    public ufsCalendario: any;

    public cauUfs: any = [];

    public submitted: boolean;
    public atividadeVigente: boolean;

    /**
     * Construtor da classe.
     */
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private securtyService: SecurityService,
        private eleicaoService: EleicaoClientService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
        ) {
        this.ufsCalendario = this.route.snapshot.data["cauUfs"];
        this.atividade = this.route.snapshot.data["atividade"];
        this.profissional = this.securtyService.credential.user;
    }

    /**
     * Quando o componente inicializar.
     */
    ngOnInit() {
        this.submitted = false;
        this.inicializaImpugnacao();
        this.inicializaIconeTitulo();
    }

    /**
     * Inicializa ícone e título do header da página .
     */
    private inicializaIconeTitulo(): void {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-fw fa-list',
            description: this.messageService.getDescription('LABEL_IMPUGANACAO_RESULTADO_ELEICAO')
        });
    }

    /**
     * Responsavel pela inicialização da Impuganação do Resultado.
     */
    public inicializaImpugnacao(): void {
        this.impugnacao = {
            narracao: '',
            idCauBR: undefined,
            cauBR: undefined,
            idProfissional: parseInt(this.profissional.idProfissional),
            idCalendario: undefined,
            arquivos: []
        };
    }

    /**
     * retorna o título da aba de substituição com quebra de linha
     */
    public getTitulo(): any {
        return  this.messageService.getDescription('TITLE_ACOMPANHAR_IMPUGNACAO_RESULTADO', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * Retorna o registro com a mascara.
     *
     * @param string
     */
    public getRegistroComMask(string): any {
        return StringService.maskRegistroProfissional(string);
    }

    /**
     * Resoponsavel por salvar os arquivos que foram submetidos no componete arquivo.
     *
     * @param arquivos
     */
    public salvarArquivos(arquivos): void {
        this.impugnacao.arquivos = arquivos;
    }

    /**
     * Resoponsavel por adicionar a narracao que fora submetido no compomente editor de texto.
     *
     * @param narracao
     */
    public adicionarDescricao(narracao): void {
        this.impugnacao.narracao = narracao;
    }

    /**
     * Sai da tela para a tela inicial do sistema.
     */
    public sair(): void {

        this.messageService.addConfirmYesNo('MSG_DADOS_INFORMADOS_SERAO_PERDIDOS_CANCELAR', () => {
            this.router.navigate(['/']);
        });
    }

    /**
     * Responsavel por fazer o download do arquivo.
     */
    public downloadArquivo(params: any): void {
        params.evento.emit(params.arquivo);
    }

    /**
     * Responsavel por validar a impugnacao.
     */
    public validarImpugnacao(): any {
        this.submitted = true;

        if (this.validarCauBR() && this.validarNarracao()) {
            this.impugnacao.idCalendario = this.impugnacao.cauBR.calendario.id;
            this.impugnacao.idCauBR = this.impugnacao.cauBR.uf.id;
            this.impugnacaoService.verificarDuplicidade(this.impugnacao).subscribe(
                data => {
                    this.validarPedidosAnteriores(data);
                }
            );
        }
    }

    /**
     * Valida se existe algum pedido anterior de acordo com as regras.
     * @param data
     */
    public validarPedidosAnteriores(data): any {
        if (data == undefined || (data && !data.tipoValidacao)) {
            this.salvar();
        } else {
            let msg;

            if (data.tipoValidacao == 1) {
                msg = 'MSG_VALIDACAO_IMPUGNACAO_RESULTADO_TIPO_1';
            } else if (data.tipoValidacao == 2) {
                msg = 'MSG_VALIDACAO_IMPUGNACAO_RESULTADO_TIPO_2';
            } else if (data.tipoValidacao == 3) {
                msg = 'MSG_VALIDACAO_IMPUGNACAO_RESULTADO_TIPO_3';
            }

            this.messageService.addConfirmYesNo(msg, () => {
                this.salvar();
            }, () => {
                this.router.navigate(['/']);
            });
        }
    }

    /**
     * Servico responsavel por salvar.
     */
    public salvar(): any {
        this.impugnacaoService.salvar(this.impugnacao).subscribe(
            data => {
                let msg_protocolo = this.messageService.getDescription(
                    'MSG_CONFIRMACAO_IMPUGNACAO_RESULTADO_NUMERO_PROTOCOLO',
                    this.maskNumero(data.numero)
                );
                this.messageService.addMsgSuccess(msg_protocolo);
                const idCauBR = data.idCauBR ? data.idCauBR : Constants.ID_IES;
                this.router.navigate([
                    'eleicao',
                    'impugnacao-resultado',
                    idCauBR,
                    'profissional',
                    'visualizar',
                    data.id
                ]);
                this.submitted = false;
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Responsavel por validar o CAUBR.
     */
    public validarCauBR(): boolean {
        return (this.impugnacao.cauBR) ? true: false;
    }

    /**
     * Responsavel por validar o CAUBR.
     */
    public validarNarracao(): boolean {
        return this.impugnacao.narracao != '';
    }

    /**
     * Retorna texto de hint da narracao.
     */
    public getHintNarracao(): any {
        return ({
            msg: this.messageService.getDescription('MSG_HINT_IMPUGANCAO_RESULTADO_NARRACAO'),
            icon: "fa fa-info-circle fa-2x pointer"
        });
    }

    /**
     * Retorna texto de hint do arquivo.
     */
    public getHintArquivo(): any {
        return ({
            msg: this.messageService.getDescription('MSG_HINT_IMPUGNACAO_RESULTADO_ARQUIVO'),
            icon: "fa fa-info-circle fa-2x pointer"
        });
    }

    /*
    * Retorna 0 a esquerda caso não tenha mascara tratada no back
    * @param param 
    */
    public maskNumero(param: string): string {    
        return param.length < 2 ? `0${param}`: param;
    }

    /**
     * Verifica se a atividade secundaria esta valida.
     */
    public validarAtividade(): any {

        const principal = Constants.TIPO_ATIVIDADE_PRINCIPAL_IMPUGNACAO_RESULTADO;
        const secundaria = Constants.TIPO_ATIVIDADE_SECUNDARIA_CADASTRO_IMPUGNACAO_RESULTADO;

        this.eleicaoService.getAtividadeSecundariaVigente({ principal, secundaria }).subscribe(
            data => {
            if (data == undefined) {
                this.messageService.addMsgDanger(
                    this.messageService.getDescription('MGS_IMPEDITIVA_ATIVIDADE_SECUNDARIA_CADASTRO_IMPUGNACAO_RESULTADO')
                    );
                    this.router.navigate(['/']);
            } else {
                this.validarImpugnacao();
            }
        });
    }
}
