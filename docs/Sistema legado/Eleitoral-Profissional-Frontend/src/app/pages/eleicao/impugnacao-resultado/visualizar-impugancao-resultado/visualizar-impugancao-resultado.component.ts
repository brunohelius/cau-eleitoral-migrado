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
import { Constants } from 'src/app/constants.service';
import { EleicaoClientService } from 'src/app/client/eleicao-client/eleicao-client.service';
import { UtilsService } from 'src/app/utils.service';


@Component({
    selector: 'app-visualizar-impugancao-resultado',
    templateUrl: './visualizar-impugancao-resultado.component.html',
    styleUrls: ['./visualizar-impugancao-resultado.component.scss']
})
export class VisualizarImpugnacaoResultadoComponent implements OnInit {

    public tabs: any;
    public cauUf: any;
    public alegacao: any;
    public impugnacao: any;
    public julgamento: any;
    public julgamentoSegunda: any;
    public validacaoAlegacaoData: any = {};
    public imagemBandeira = {
        imagem: '',
        sigla: ''
    };
    public recursosImpugnado: any;
    public isCarregadoRecursoImpugnado: boolean = false;
    public recursoImpugnante: any;
    public dadosRecursoImpugnante: any;
    public isCarregadoRecursoImpugnante: boolean = false;

    public isCarregadoJulgamento: boolean = false;
    public isCarregadoJulgamentoSegundaInstancia: boolean = false;
    public isCarregadoDadosAbaAlegacao: boolean = false;
    public tipoProfissional: any;

    /**
     * Construtor da classe.
     */
    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private securtyService: SecurityService,
        private eleicaoClientService: EleicaoClientService,
        private impugnacaoService: ImpugnacaoResultadoClientService,
    ) {
        this.cauUf = this.route.snapshot.data["cauUf"];
        this.impugnacao = this.route.snapshot.data["impugnacao"];
        this.setImagemBandeira();
    }

    /**
     * Quando o componente inicializar.
     */
    ngOnInit() {
        this.inicializaIconeTitulo();
        this.inicializartabs();
        this.tipoProfissional = UtilsService.getValorParamDoRoute('tipoProfissional', this.route);
        this.inicializaValidacaoAlegacaoData();

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
     * Redireciona para tela inicial
     */
    public voltarTelaInicio(): void {
        this.router.navigate([`/`]);
    }

    /**
   * sai da tela para a tela inicial do sistema.
   */
    public sair(): void {
        const idCauUf = this.cauUf.id == Constants.CAUBR_ID ? Constants.ID_IES : this.cauUf.id;
        if (this.tabs.impugncao.ativo == true) {

            if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
                this.router.navigate([`/eleicao/impugnacao-resultado/acompanhar-chapa`]);

            } else if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO) {
                this.router.navigate([`/eleicao/impugnacao-resultado/acompanhar/${idCauUf}`]);
            } else {
                this.router.navigate([`/eleicao/impugnacao-resultado/acompanhar-profissional/${idCauUf}`]);
            }

        } else if (this.tabs.alegacao.ativo == true) {
            this.mudarAbaSelecionada(Constants.ABA_IMPUGNACAO)
        } else if (this.tabs.julgamento.ativo) {
            this.mudarAbaSelecionada(Constants.ABA_ALEGACAO)
        } else if (this.tabs.recursoImpugnante.ativo) {
            this.mudarAbaSelecionada(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO)
        } else if (this.tabs.recursoImpugnado.ativo) {
            if (this.hasRecursoJulgamentoImpugnante()) {
                this.mudarAbaSelecionada(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
            } else {
                this.mudarAbaSelecionada(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO);
            }
        } else if (this.tabs.julgamentoSegunda.ativo) {
            if (this.hasRecursoJulgamentoImpugnado()) {
                this.mudarAbaSelecionada(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO);
            } else {
                this.mudarAbaSelecionada(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
            }
        }
    }

    /**
     * retorna o título da aba de impugnacao com quebra de linha.
     */
    public getTituloAbaImpugnacao(): any {
        return this.messageService.getDescription('TITLE_ACOMPANHAR_IMPUGNACAO_RESULTADO', ['<div>', '</div><div>', '</div>']);
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
     * retorna o título da aba de impugnacao com quebra de linha.
     */
    public getTituloAbaAlegacao(): any {
        return this.messageService.getDescription('TITLE_ALEGACOES');
    }

    /**
     * retorna a label da aba de Julgamento 1ª Instância com quebra de linha
     */
    public getTituloAbaJulgamentoPrimeiraInstancia(): any {
        return this.messageService.getDescription('LABEL_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * Verifica se o julgamento é IES ou não.
     * @param id
     */
    public isIES(): boolean {
        let id = this.impugnacao.cauBR ? this.impugnacao.cauBR.id : undefined;
        return (id === Constants.ID_CAUBR) || (id === Constants.ID_IES) || (id === undefined);
    }

    /**
     * retorna a label da aba de recurso IMPUGNANTE com quebra de linha.
     */
    public getTituloAbaRecursoJulgamentoImpugnante(): any {
        if (this.isIES()) {
            return this.messageService.getDescription('LABEL_ABA_RECONSIDERACAO_JULGAMENTO_IMPUGNANTE', ['<div>', '</div><div>', '</div>']);
        }
        if (!this.isIES()) {
            return this.messageService.getDescription('LABEL_ABA_RECURSO_JULGAMENTO_IMPUGNANTE', ['<div>', '</div><div>', '</div>']);
        }
    }



    /**
     * Recupera o arquivo conforme a entidade 'resolucao' informada.
     */
    public downloadArquivo(event: any): void {
        this.impugnacaoService.getDocumento(this.impugnacao.id).subscribe((data: Blob) => {
            event.evento.emit(data);
        }, error => {
            this.messageService.addMsgDanger(error);
        });
    }

    /**
     * Busca imagem de bandeira do estado do CAUUF.
     *
     * @param idCauUf
     */
    public setImagemBandeira(): void {
        this.imagemBandeira.imagem = this.cauUf.imagemBandeira;
        this.imagemBandeira.sigla = this.cauUf.descricao;

        if (this.cauUf.id === Constants.ID_CAUBR) {
            this.imagemBandeira.sigla = Constants.IES;
        }
    }

    /**
     * método resposnável por inicializar o objeto de abas do módulo
     */
    public inicializartabs(): void {
        this.tabs = {
            impugncao: { ativo: true, id: Constants.ABA_IMPUGNACAO },
            alegacao: { ativo: false, id: Constants.ABA_ALEGACAO },
            julgamento: { ativo: false, id: Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO },
            recursoImpugnante: { ativo: false, id: Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE },
            recursoImpugnado: { ativo: false, id: Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO },
            julgamentoSegunda: {ativo: false, id: Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA_IMPUGNACAO_RESULTADO}
        };
    }

    /**
     * Método responsável por mudar o status da aba de acordo com a aba selecioanda.
     *
     * @param identificador
     */
    public mudarAbaSelecionada(identificador, isRecarregar: boolean = false): void {
        if (identificador == Constants.ABA_IMPUGNACAO) {
            this.controleEstadoAba(identificador);
        }
        if (identificador == Constants.ABA_ALEGACAO) {
            this.getDadosAlegacao(isRecarregar);
        }
        if (identificador == Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO) {
            this.getDadosJulgamento(isRecarregar);
        }
        if (identificador == Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
            this.getDadosRecursoImpugnante(isRecarregar);
        }
        if (identificador == Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO) {
            this.getDadosRecursosImpugnado(isRecarregar);
        }
        if (identificador == Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA_IMPUGNACAO_RESULTADO) {
            this.getDadosJulgamentoSegundaInstancia(isRecarregar);
        }
    }

    /**
     * Verifica se a aba de recurso/reconsideração IMPUGNANATE deve ser mostrada.
     */
    public hasRecursoJulgamentoImpugnante(): boolean {
        return (
            this.impugnacao.hasRecursoJulgamentoImpugnante ||
            this.impugnacao.isFinalizadoAtividadeRecursoJulgamento
            && this.impugnacao.hasJulgamento
        );
    }

    /**
     * retorna a label da aba de recurso IMPUGNADO com quebra de linha.
     */
    public getTituloAbaRecursoJulgamentoImpugnado(): any {
        if (this.isIES()) {
            return this.messageService.getDescription('LABEL_ABA_RECONSIDERACAO_JULGAMENTO_IMPUGNADO', ['<div>', '</div><div>', '</div>']);
        }
        if (!this.isIES()) {
            return this.messageService.getDescription('LABEL_ABA_RECURSO_JULGAMENTO_IMPUGNADO', ['<div>', '</div><div>', '</div>']);
        }
    }

     /**
     * retorna a label da aba de julgamento segunda instância com quebra de linha.
     */
    public getTituloAbaJulgamentoSegundaInstancia(): any {
        return  this.messageService.getDescription('LABEL_ABA_JULGAMENTO_SEGUNDA_INSTANCIA',['<div>','</div><div>','</div>']);
    }

    /**
     * Verifica se a aba de recurso/reconsideração IMPUGNADO deve ser mostrada.
     */
    public hasRecursoJulgamentoImpugnado(): boolean {
        return (
            this.impugnacao.hasRecursoJulgamentoImpugnado ||
            this.impugnacao.isFinalizadoAtividadeRecursoJulgamento
            && this.impugnacao.hasJulgamento
        );
    }

    /**
     * Responsavel por identificar a aba ativa.
     *
     * @param identificador
     */
    public controleEstadoAba(identificador): void {
        this.tabs.impugncao.ativo = this.tabs.impugncao.id == identificador;
        this.tabs.alegacao.ativo = this.tabs.alegacao.id == identificador;
        this.tabs.julgamento.ativo = this.tabs.julgamento.id == identificador;
        this.tabs.recursoImpugnante.ativo = this.tabs.recursoImpugnante.id == identificador;
        this.tabs.recursoImpugnado.ativo = this.tabs.recursoImpugnado.id == identificador;
        this.tabs.julgamentoSegunda.ativo = this.tabs.julgamentoSegunda.id == identificador;
    }

    /**
     * Responsavel por carregar os dados referentes a alegação.
     */
    public getDadosAlegacao(isRecarregar: boolean = false): void {
        if (!this.isCarregadoDadosAbaAlegacao || isRecarregar) {
            this.impugnacaoService.getAlegacaPorIdImpugnacao(this.impugnacao.id).subscribe(
                data => {
                    if (data != undefined) {
                        this.alegacao = data;
                        this.isCarregadoDadosAbaAlegacao = true;
                        this.controleEstadoAba(Constants.ABA_ALEGACAO);
                    }
                }
            );
        } else {
            this.controleEstadoAba(Constants.ABA_ALEGACAO);
        }
    }

    /**
     * valida se a aba de alegação existe ou não.
     */
    public isAbaAlegacao(): boolean {
        return this.impugnacao.isFinalizadoAtividadeAlegacao || this.impugnacao.hasAlegacao == true;
    }

    /**
     * Responsavel por carregar os dados referentes ao Julgamento.
     */
    public getDadosJulgamento(isRecarregar: boolean = false): void {
        if (!this.isCarregadoJulgamento || isRecarregar) {
            this.impugnacaoService.getJulgamentoAlegacaoImpugnacaoResultado(this.impugnacao.id).subscribe(
                data => {
                    if (data != undefined) {
                        this.julgamento = data;
                        this.isCarregadoJulgamento = true;
                        this.controleEstadoAba(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO);
                    }
                }
            );
        } else {
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_IMPUGNACAO_RESULTADO);
        }
    }

    /**
     * Carrega dados do recurso de julgamento de 1º instância.
     *
     * @param identificador
     */
    public getDadosRecursosImpugnado( isRecarregar: boolean = false): void {
        if (!this.isCarregadoRecursoImpugnado || isRecarregar) {
            this.impugnacaoService.getRecursoJulgamentoPorIdImpugnacao(this.impugnacao.id,
            Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO).subscribe(
              data => {
                if (data != undefined) {
                  this.recursosImpugnado = data;
                  this.isCarregadoRecursoImpugnado = true;
                  this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO);
                }
              }
            );
          } else {
            this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO);
          }
    }

     /**
     * Carrega dados do recurso de julgamento de 1º instância.
     *
     * @param identificador
     */
    public getDadosRecursoImpugnante( isRecarregar: boolean = false ): void {
        if (!this.isCarregadoRecursoImpugnante || isRecarregar) {
            this.impugnacaoService.getRecursoJulgamentoPorIdImpugnacao(this.impugnacao.id,
            Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE ).subscribe(
              data => {
                this.dadosRecursoImpugnante = data.shift();
                this.isCarregadoRecursoImpugnante = true;
                this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
              }
            );
          } else {
            this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
          }
    }

    public hasJulgamento(): boolean {
        return this.impugnacao.hasJulgamento == true;
    }

    /**
     * Responsavel por carregar os dados referentes ao Julgamento.
     */
    public getDadosJulgamentoSegundaInstancia(isRecarregar: boolean = false): void {
        if (!this.isCarregadoJulgamentoSegundaInstancia || isRecarregar) {
            this.impugnacaoService.getJulgamentoSegundaInstanciaImpugnacaoResultado(this.impugnacao.id).subscribe(
                data => {
                    if (data != undefined) {
                        this.julgamentoSegunda = data;
                        this.isCarregadoJulgamentoSegundaInstancia = true;
                        this.controleEstadoAba(Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA_IMPUGNACAO_RESULTADO);
                    }
                }
            );
        } else {
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_SEGUNDA_INSTANCIA_IMPUGNACAO_RESULTADO);
        }
    }

    public isAbaJulgamentoSegunda(): boolean {
        return this.impugnacao.hasJulgamentoRecurso;
      }

    /**
     * Redireciona para a aba de alegação após salvar os dados
     */
    public redirecionaAposCadastro(): void {
        this.isCarregadoDadosAbaAlegacao = false;
        this.impugnacao.hasAlegacao = true;
        this.mudarAbaSelecionada(Constants.ABA_ALEGACAO);
    }

    /**
     * Inicializa dados para validação de  cadastro alegação.
     */
    public inicializaValidacaoAlegacaoData(): void {
        this.impugnacaoService.validacao(this.impugnacao.id).subscribe(
            data => {
                this.validacaoAlegacaoData = data;
            },
            error => {
                this.messageService.addMsgDanger(error);
            }
        );
    }

    /**
     * Redireciona para a aba de alegação após salvar os dados
     */
    public redirecionaAposCadastroRecurso(tipoRecurso): void {

        if (tipoRecurso == Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO) {
            this.isCarregadoRecursoImpugnado = false;
            this.tabs.recursoImpugnado.ativo == true;
            this.mudarAbaSelecionada(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO);
        }
        if (tipoRecurso == Constants.TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE) {
            this.isCarregadoRecursoImpugnante = false;
            this.tabs.recursoImpugnante.ativo == true;
            this.mudarAbaSelecionada(Constants.ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE);
        }
    }
}
