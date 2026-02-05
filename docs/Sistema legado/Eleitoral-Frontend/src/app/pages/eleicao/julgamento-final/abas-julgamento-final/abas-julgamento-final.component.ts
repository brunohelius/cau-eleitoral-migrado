import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';

import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, EventEmitter } from '@angular/core';

/**
 * Componente responsável pela apresentação da listagem de eleições concluídas.
 *
 * @author Squadra Tecnologia.
 */
@Component({
    selector: 'abas-julgamento-final',
    templateUrl: './abas-julgamento-final.component.html',
    styleUrls: ['./abas-julgamento-final.component.scss']
})
export class AbasJulgamentoFinalComponent implements OnInit {

    public dadosChapa: any = [];

    public idUf: any;
    public tabs: any;
    public isIES: any;
    public recurso: any;
    public calendarioId: any;
    public julgamentoFinal: any;
    public membrosPorSituacao: any;
    public recursoReconsideracao: any;
    public substituicaoJulgamento: any;
    public recursoSegundaInstancia: any;
    public substituicaoSegundaInstancia: any;
    public julgamentoFinalSegundaInstancia: any;

    private isCarregadoDadosAbaRecurso = false;
    private isCarregadoDadosAbaJulgamento = false;

    public hasRecursoSegundaInstancia = false;
    public hasSubstituicaoSegundaInstancia = false;

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private messageService: MessageService,
        private layoutsService: LayoutsService,
        private julgamentoFinalClientService: JulgamentoFinalClientService,
    ) {
        this.idUf = route.snapshot.params.idUf;
        this.recurso = route.snapshot.data.recurso;
        this.dadosChapa = route.snapshot.data.dadosChapa;
        this.calendarioId = route.snapshot.params.idCalendario;
        this.membrosPorSituacao = route.snapshot.data.membros;
        this.julgamentoFinalSegundaInstancia = route.snapshot.data.dadosJulgamentoFinalSegundaInstancia
    }

    ngOnInit() {
        this.getTituloPagina();
        this.inicializartabs();
        this.inicializaIes();
        this.getJulgamentoFinalSegundaInstancia();
    }

    /**
    * retona o título do módulo de julgamento final
    */
    public getTituloPagina(): void {
        this.layoutsService.onLoadTitle.emit({
            icon: 'fa fa-wpforms',
            description: this.messageService.getDescription('TITLE_JULGAMENTO')
        });
    }

    /**
     * Responsavel por inicalizar o IsIES.
     */
    public inicializaIes(): void {
        this.isIES = this.dadosChapa.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
        this.recursoReconsideracao = this.messageService.getDescription(this.isIES ? 'LABEL_RECONSIDERACAO' : 'LABEL_RECURSO');
    }

    /**
     * Método para voltar a página anterior
     */
    public voltar(): void {
        let ufRetorno;
        if (this.dadosChapa.tipoCandidatura.id == 2) {
            ufRetorno = 0;
        } else {
            ufRetorno = this.dadosChapa.idCauUf;
        }
        this.router.navigate([`/eleicao/julgamento-final/acompanhar-uf/${ufRetorno}/calendario/${this.calendarioId}`]);
    }

    /**
     * método resposnável por inicializar o objeto de abas do módulo
     */
    public inicializartabs(): void {
        this.tabs = {
            acompanharChapa: { ativo: true, nome: Constants.ABA_ACOMPANHAR_CHAPA },
            julgamentoPrimeiraInstancia: { ativo: false, nome: Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA },
            recursoJulgamento: { ativo: false, nome: Constants.ABA_RECURSO_JULGAMENTO_FINAL },
            pedidoSubstituicao: { ativo: false, nome: Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO },
            julgamentoSubstituicaoSegundaInstancia: { ativo: false, nome: Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA },
            julgamentoRecursoSegundaInstancia: { ativo: false, nome: Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA },
        };
    }

    /**
     * Método responsável por mudar o status da aba de acordo com a aba selecioanda.
     * @param nomeAba
     */
    public mudarAbaSelecionada(nomeAba,  recarregar: boolean = false): void {
        if ( nomeAba ==  Constants.ABA_ACOMPANHAR_CHAPA ) {
            this.controleEstadoAba(nomeAba);
        }
        if ( nomeAba == Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA ) {
            this.getDadosJulgamento(nomeAba, recarregar);
        }
        if ( nomeAba == Constants.ABA_RECURSO_JULGAMENTO_FINAL ) {
            this.getDadosRecurso();
        }
        if ( nomeAba == Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO ) {
            this.getDadosSubstituicaoJulgamento(recarregar);
        }
        if ( nomeAba == Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA ) {
            this.carregaDadosJulgamentoRecursoSegundaInstancia(recarregar);
          }
        if ( nomeAba == Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA ) {
            this.carregaDadosJulgamentoSubstituicaoSegundaInstancia(recarregar);
          }
    }

    public controleEstadoAba(nomeAba): void {
        this.tabs.acompanharChapa.ativo = this.tabs.acompanharChapa.nome == nomeAba;
        this.tabs.julgamentoPrimeiraInstancia.ativo = this.tabs.julgamentoPrimeiraInstancia.nome == nomeAba;
        this.tabs.recursoJulgamento.ativo = this.tabs.recursoJulgamento.nome == nomeAba;
        this.tabs.pedidoSubstituicao.ativo = this.tabs.pedidoSubstituicao.nome == nomeAba;
        this.tabs.julgamentoSubstituicaoSegundaInstancia.ativo = this.tabs.julgamentoSubstituicaoSegundaInstancia.nome == nomeAba;
        this.tabs.julgamentoRecursoSegundaInstancia.ativo = this.tabs.julgamentoRecursoSegundaInstancia.nome == nomeAba;
    }

  /**
   * Responsavel por traser os dados da aba Julgamento do Recurso de Segunda Instância.
   */
  public carregaDadosJulgamentoRecursoSegundaInstancia(recarregar: boolean = false): void {
    if (this.julgamentoFinal == undefined || recarregar) {
      this.julgamentoFinalClientService.getJulgamentoFinalPrimeira(this.dadosChapa.id).subscribe(
          data => {
              this.julgamentoFinal = data;
              this.isCarregadoDadosAbaJulgamento = true;
              this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
          }
      );
    } else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
    }
  }

  /**
   * Responsavel por traser os dados da aba Julgamento da Substituição de Segunda Instância.
   */
  public carregaDadosJulgamentoSubstituicaoSegundaInstancia(recarregar: boolean = false): void {
    if (this.julgamentoFinal == undefined || recarregar) {
      this.julgamentoFinalClientService.getJulgamentoFinalPrimeira(this.dadosChapa.id).subscribe(
        data => {
            this.julgamentoFinal = data;
            this.isCarregadoDadosAbaJulgamento = true;
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
        }
      );
    } else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
    }
  }

  private getJulgamentoFinalSegundaInstancia(): void {
    if (this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia.length > 0) {
        this.recursoSegundaInstancia = this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia[0];
        this.hasRecursoSegundaInstancia = true;
    }
    if (this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia.length > 0) {
        this.substituicaoSegundaInstancia = this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia;
        this.hasSubstituicaoSegundaInstancia = true;
    }
  }

  /**
   * Carrega o dado e redireciona para aba recurso
   */
  public getDadosRecurso(): void {
      if (this.recurso == undefined) {
          this.julgamentoFinalClientService.getRecursoJulgamentoFinal(this.dadosChapa.id).subscribe(
              data => {
                  this.recurso = data;
                  this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_FINAL);
              }
          );
      } else {
          this.controleEstadoAba(Constants.ABA_RECURSO_JULGAMENTO_FINAL);
      }
  }

  /**
   * Busca dados da aba Substituição julgamento final.
   */
  public getDadosSubstituicaoJulgamento(recarregar: boolean = false): void {
    if (this.substituicaoJulgamento == undefined || recarregar) {
      this.julgamentoFinalClientService.getSubstituicaoJulgamentoPorChapa(this.dadosChapa.id).subscribe(
        data => {
          this.substituicaoJulgamento = data;

          this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
        }
      );
    } else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
    }
  }

    public getDadosJulgamento(nomeAba, recarregar: boolean = false): void {
        if (!this.isCarregadoDadosAbaJulgamento || recarregar ) {
            this.julgamentoFinalClientService.getJulgamentoFinalPrimeira(this.dadosChapa.id).subscribe(
                data => {
                    this.julgamentoFinal = data;
                    this.isCarregadoDadosAbaJulgamento = true;
                    this.controleEstadoAba(nomeAba);
                }
            );
        } else {
            this.controleEstadoAba(nomeAba);
        }
    }

    public isMostrarAbaJulgamento(): boolean {
        return this.dadosChapa.isCadastradoJulgamentoFinal || this.julgamentoFinal !== undefined;
    }

    /**
    * retorna a label da aba de acompanhar chapa com quebra de linha
    */
    public getTituloAbaAcompanharChapa(): any {
        return this.messageService.getDescription('TITLE_ABA_ACOMPANHAR_CHAPA_QUEBRA_LINHA', ['<div>', '</div><div>', '</div>']);
    }

    /**
    * retorna a label da aba de acompanhar chapa com quebra de linha
    */
    public getTituloAbaJulgamentoPrimeiraInstancia(): any {
        return this.messageService.getDescription('TITLE_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA', ['<div>', '</div><div>', '</div>']);
    }

    /**
    * retorna a label da aba de acompanhar chapa com quebra de linha
    */
    public getTituloAbaRecursoJulgamento(): any {
        return this.messageService.getDescription('TITLE_ABA_JULGAMENTO_PRIMEIRA_INSTANCIA', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * retorna o título da aba de Recurso Responsavel com quebra de linha
     */
    public getTituloAbaRecursoResponsavel(): any {
        return  this.messageService.getDescription('TITLE_ABA_RECURSO_RECONSIDERACAO_RESPONSAVEL',
        ['<div>', this.recursoReconsideracao, '</div><div>', '</div>']);
    }

      /**
     * retorna a label da aba de julgamento da substituição com quebra de linha
     */
    public getTituloAbaJulgamentoSubstituicaoSegundaInstancia(): any {
        return  this.messageService.getDescription('LABEL_JULGAMENTO_SUBSTITUICAO', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * retorna a label da aba de julgamento do recurso com quebra de linha
     */
    public getTituloAbaJulgamentoRecursoSegundaInstancia(): any {
        return  this.messageService.getDescription('LABEL_JULGAMENTO_RECURSO', [
            '<div>', '</div><div>', this.recursoReconsideracao, '</div>'
        ]);
    }

    /**
     * Retorna título da aba de pedido de substiuição.
     */
    public getTituloPedidoSubstituicao(): string {
        return this.messageService.getDescription('TITLE_PEDIDO_SUBSTITUICAO', ['<div>', '</div><div>', '</div>']);
    }

    /**
     * Verifica se aba de recurso deve ser mostrada.
     */
    public isMostrarAbaRecurso(): boolean {
        return (
            this.dadosChapa.isCadastradoJulgamentoFinal &&
            this.dadosChapa.isJulgamentoFinalIndeferido &&
            (this.dadosChapa.isCadastradoRecursoJulgamentoFinal ||
            this.dadosChapa.isFinalizadoAtivRecursoJulgamentoFinal ||
            this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal)
          );
    }

    /**
   * Verifica se aba de Pedido substituição deve ser mostrada.
   */
    public isMostrarAbaPedidoSubstituicao(): boolean {
        return (
            (
                !this.dadosChapa.isCadastradoRecursoJulgamentoFinal &&
                this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal
            ) ||
            (
                this.dadosChapa.isCadastradoJulgamentoFinal &&
                this.dadosChapa.isJulgamentoFinalIndeferido &&
                !this.dadosChapa.isCadastradoRecursoJulgamentoFinal &&
                !this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal &&
                this.dadosChapa.isFinalizadoAtivRecursoJulgamentoFinal &&
                this.dadosChapa.isFinalizadoAtivSubstituicaoJulgFinal
            )||
            (
                this.dadosChapa.isJulgamentoFinalIndeferido &&
                this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal
            )
        );
    }

    /**
     * Redireciona para a aba de visualizar julgamento final
     */
    public redirecionaAbaVisualizarJulgamentoPrimeira(response: any): void {
        this.isCarregadoDadosAbaJulgamento = true;
        this.julgamentoFinal = response;
        this.dadosChapa.isCadastradoJulgamentoFinal = true;
        this.mudarAbaSelecionada(Constants.ABA_JULGAMENTO_FINAL_PRIMEIRA);
    }


    public redirecionarAposSalvarJulgamento(response: any, isAbaRecurso = false): void {
      this.julgamentoFinalClientService.getJulgamentoSegundaInstanciaPorChapa(this.dadosChapa.id).subscribe(
          data => {
              this.julgamentoFinalSegundaInstancia = data;

              if (this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia.length > 0) {
                  this.recursoSegundaInstancia = this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia[0];
                  this.hasRecursoSegundaInstancia = true;

                  if (response.reload != undefined && isAbaRecurso) {
                    response.reload(this.recursoSegundaInstancia);
                }
              }

              if (this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia.length > 0) {
                  this.substituicaoSegundaInstancia = this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia;
                  this.hasSubstituicaoSegundaInstancia = true;

                  if (response.reload != undefined && !isAbaRecurso) {
                    response.reload(this.substituicaoSegundaInstancia);
                }
              }

              if (isAbaRecurso) {
                  this.mudarAbaSelecionada(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
                  this.recurso = undefined;
              } else {
                  this.substituicaoJulgamento = undefined;
                  this.mudarAbaSelecionada(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
              }
          }
      );
  }

}
