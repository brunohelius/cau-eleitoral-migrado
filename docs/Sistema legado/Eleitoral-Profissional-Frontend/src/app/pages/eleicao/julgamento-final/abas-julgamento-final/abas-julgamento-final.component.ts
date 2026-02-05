import { TipoCandidatura } from './../../cadastro-chapa/abas-cadastro-chapa/aba-visao-geral/aba-visao-geral.component';

import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { ActivatedRoute, Router } from '@angular/router';
import { Constants } from 'src/app/constants.service';
import { Component, OnInit, EventEmitter } from '@angular/core';
import { JulgamentoFinalClientService } from 'src/app/client/julgamento-final/julgamento-final-client.service';
import { switchMap } from 'rxjs/operators';

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

  public pedido: any = {};
  public tabs: any;
  public julgamentoFinal: any;
  public recursoJulgamento: any;
  public recursoReconsideracao: any;
  public dadosChapa: any = [];
  public substituicaoJulgamento: any;
  public recursoSegundaInstancia: any;
  public substituicaoSegundaInstancia: any;
  public julgamentoFinalSegundaInstancia: any;

  public isIes: boolean;
  public isCarregadoDadosAbaRecurso = false;
  public isCarregadoDadosAbaSubstituicao = false;
  public isCarregadoDadosAbaJulgamento = false;
  public hasRecursoSegundaInstancia = false;
  public hasSubstituicaoSegundaInstancia = false;

  public tipoProfissional: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private messageService: MessageService,
    private layoutsService: LayoutsService,
    private julgamentoFinalClientService: JulgamentoFinalClientService
  ) {
    this.dadosChapa = route.snapshot.data.dadosChapa;
  }

  ngOnInit() {
    this.inicializaIes();
    this.inicializaTipoProfissional();
    this.getTituloPagina();
    this.inicializartabs();
    this.getJulgamentoFinalSegundaInstancia();
    this.recursoReconsideracao = this.messageService.getDescription(this.isIes ? 'LABEL_RECONSIDERACAO' : 'LABEL_RECURSO');
  }

  /**
   * Inicia a variável tipoProfissional com o valor que vem como parâmetro interno na rota
   */
  private inicializaTipoProfissional(): void {
    this.tipoProfissional = this.getValorParamDoRoute('tipoProfissional');
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
   * Responsavel por trazer as informações referentes ao Recurso do Julgamento Final.
   */
  public inicializaRecurso(idChapa): any {
    if (!this.isCarregadoDadosAbaRecurso) {
      this.julgamentoFinalClientService.getRecursoJulgamentoFinal(idChapa).subscribe(
        data => {
          this.recursoJulgamento = data;
          this.isCarregadoDadosAbaRecurso = true;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_RECRUSO);
        }
      );
    } else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_RECRUSO);
    }
  }

  public isMostrarAbaJulgamento(): boolean {
    return this.dadosChapa.isCadastradoJulgamentoFinal || this.julgamentoFinal !== undefined;
  }

  /**
   * Método para voltar a página anterior
   */
  public voltar(): void {
      // tslint:disable-next-line: align
      if ( this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
        this.router.navigate([`/`]);
      } else {
        const id = this.isIes ? 0 : this.dadosChapa.idCauUf;
        const param = this.tipoProfissional == Constants.TIPO_PROFISSIONAL_COMISSAO ? `/${id}` : '';
        this.router.navigate([`/eleicao/julgamento-final/acompanhar-uf${param}`,
        {
            isIES: this.isIes
        }]);
      }
  }

  /**
   * método resposnável por inicializar o objeto de abas do módulo
   */
  public inicializartabs(): void {
    this.tabs = {
      acompanharChapa: { ativo: true, id: Constants.ABA_JULGAMENTO_FINAL_PRINCIPAL },
      julgamentoPrimeiraInstancia: { ativo: false, id: Constants.ABA_JULGAMENTO_FINAL_DETALHAR },
      recursoJulgamento: { ativo: false, id: Constants.ABA_JULGAMENTO_FINAL_RECRUSO },
      julgamentoSubstituicaoSegundaInstancia: { ativo: false, id: Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA },
      julgamentoRecursoSegundaInstancia: { ativo: false, id: Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA },
      pedidoSubstituicao: { ativo: false, id: Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO },
    };
  }

  /**
   * Método responsável por mudar o status da aba de acordo com a aba selecioanda.
   * @param nomeAba
   */
  public mudarAbaSelecionada(identificador): void {
    if ( identificador == Constants.ABA_JULGAMENTO_FINAL_PRINCIPAL ) {
      this.controleEstadoAba(identificador);
    }
    if ( identificador == Constants.ABA_JULGAMENTO_FINAL_DETALHAR ) {
      this.getDadosJulgamento();
    }
    if ( identificador == Constants.ABA_JULGAMENTO_FINAL_RECRUSO ) {
      this.inicializaRecurso(this.dadosChapa.id);
    }
    if ( identificador == Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO ) {
      this.getDadosSubstituicaoJulgamento();
    }
    if ( identificador == Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA ) {
      this.carregaDadosJulgamentoRecursoSegundaInstancia();
    }
    if ( identificador == Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA ) {
      this.carregaDadosJulgamentoSubstituicaoSegundaInstancia();
    }
  }

  public controleEstadoAba(identificador): void {
    this.tabs.acompanharChapa.ativo = this.tabs.acompanharChapa.id == identificador;
    this.tabs.julgamentoPrimeiraInstancia.ativo = this.tabs.julgamentoPrimeiraInstancia.id == identificador;
    this.tabs.recursoJulgamento.ativo = this.tabs.recursoJulgamento.id == identificador;
    this.tabs.julgamentoSubstituicaoSegundaInstancia.ativo = this.tabs.julgamentoSubstituicaoSegundaInstancia.id == identificador;
    this.tabs.julgamentoRecursoSegundaInstancia.ativo = this.tabs.julgamentoRecursoSegundaInstancia.id == identificador;
    this.tabs.pedidoSubstituicao.ativo = this.tabs.pedidoSubstituicao.id == identificador;
  }

  public getDadosJulgamento(): void {
    if (!this.isCarregadoDadosAbaJulgamento) {
      if (this.tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
        this.getDadosJulgamentoResponsavel();
      } else {
        this.getDadosJulgamentoComissao();
      }
    } else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_DETALHAR);
    }
  }

  private getDadosJulgamentoResponsavel(): void {
    this.julgamentoFinalClientService.getJulgamentoChapaResponsavel(this.dadosChapa.id).subscribe(
      data => {
          this.julgamentoFinal = data;
          this.isCarregadoDadosAbaJulgamento = true;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_DETALHAR);
      }
  );

  }
  private getDadosJulgamentoComissao(): void {
    this.julgamentoFinalClientService.getJulgamentoChapaComissao(this.dadosChapa.id).subscribe(
      data => {
          this.julgamentoFinal = data;
          this.isCarregadoDadosAbaJulgamento = true;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_DETALHAR);
      }
  );
  }

  /**
   * Responsavel por traser os dados da aba Recurso Julgamento.
   */
  public getDadosRecursoJulgamento(): void {
    if (!this.isCarregadoDadosAbaRecurso) {
      this.julgamentoFinalClientService.getMembrosComPendencia(this.dadosChapa.id).subscribe(
        data => {
          this.recursoJulgamento = data;
          this.isCarregadoDadosAbaRecurso = true;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_RECRUSO);
        }
      );
    }
  }

  /**
   * Responsavel por traser os dados da aba Julgamento do Recurso de Segunda Instância.
   */
  public carregaDadosJulgamentoRecursoSegundaInstancia(): void {
    if(this.julgamentoFinal == undefined) {
      const tipoProfissional = this.getValorParamDoRoute('tipoProfissional');
      if ( tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
        this.julgamentoFinalClientService.getJulgamentoChapaResponsavel(this.dadosChapa.id)
          .subscribe(data => { 
              this.julgamentoFinal = data;
              this.isCarregadoDadosAbaJulgamento = true;
              this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
            }
          );
        ;
      } else {
        this.julgamentoFinalClientService.getJulgamentoChapaComissao(this.dadosChapa.id)
          .subscribe(data => { 
            this.julgamentoFinal = data;
            this.isCarregadoDadosAbaJulgamento = true;
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
          });
      }
    }
    else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
    }
  }

  public getDadosJulgamentoRecursoSegundaInstancia(): void {
    this.julgamentoFinalClientService.getJulgamentoRecursoSegundaInstancia(this.julgamentoFinal.id).subscribe(
      data => {
          this.recursoSegundaInstancia = data;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA);
      }
    );
  }

  /**
   * Responsavel por traser os dados da aba Julgamento da Substituição de Segunda Instância.
   */
  public carregaDadosJulgamentoSubstituicaoSegundaInstancia(): void {
    if(this.julgamentoFinal == undefined) {
      const tipoProfissional = this.getValorParamDoRoute('tipoProfissional');
      if ( tipoProfissional == Constants.TIPO_PROFISSIONAL_CHAPA) {
        this.julgamentoFinalClientService.getJulgamentoChapaResponsavel(this.dadosChapa.id)
          .subscribe(data => { 
              this.julgamentoFinal = data;
              this.isCarregadoDadosAbaJulgamento = true;
              this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
            }
          );
        ;
      } else {
        this.julgamentoFinalClientService.getJulgamentoChapaComissao(this.dadosChapa.id)
          .subscribe(data => { 
            this.julgamentoFinal = data;
            this.isCarregadoDadosAbaJulgamento = true;
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
          });
      }
    }
    else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
    }
  }

  public getDadosJulgamentoSubstituicaoSegundaInstancia(): void {
    this.julgamentoFinalClientService.getJulgamentoSubstituicaoSegundaInstancia(this.julgamentoFinal.id).subscribe(
      data => {
          this.substituicaoSegundaInstancia = data;
          this.controleEstadoAba(Constants.ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA);
      }
    );
  }

  private getJulgamentoFinalSegundaInstancia(): void {
    if(this.julgamentoFinalSegundaInstancia == undefined) {
      this.julgamentoFinalClientService.getJulgamentoSegundaInstanciaPorChapa(this.dadosChapa.id).subscribe(
        data => {
            this.julgamentoFinalSegundaInstancia = data;

            if(this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia.length > 0){
              this.recursoSegundaInstancia = this.julgamentoFinalSegundaInstancia.recursoSegundaInstancia[0];
              this.hasRecursoSegundaInstancia = true;
            }

            if(this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia.length > 0){
              this.substituicaoSegundaInstancia = this.julgamentoFinalSegundaInstancia.substituicaoSegundaInstancia;
              this.hasSubstituicaoSegundaInstancia = true;
            }
        }
      );
    }    
  }

  /**
   * Busca dados da aba Substituição julgamento final.
   */
  public getDadosSubstituicaoJulgamento(): void {
    if(this.substituicaoJulgamento == undefined) {
      this.julgamentoFinalClientService.getSubstituicaoJulgamentoPorChapa(this.dadosChapa.id).subscribe(
          data => {
            this.substituicaoJulgamento = data;
            this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
          }
      );
    }
    else {
      this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
    }
  }

  /**
  * retorna a label da aba de acompanhar chapa com quebra de linha
  */
  public getTituloAbaAcompanharChapa(): any {
    return this.messageService.getDescription('LABEL_ACOMPANHAR_CHAPA', ['<div>', '</div><div>', '</div>']);
  }

  /**
  * retorna a label da aba de acompanhar chapa com quebra de linha
  */
  public getTituloAbaJulgamentoPrimeiraInstancia(): any {
    return  this.messageService.getDescription('LABEL_JULGAMENTO_FINAL_PRIMEIRA_INSTANCIA', ['<div>', '</div><div>', '</div>']);
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
   * Retorna um valor de parâmetro passado na rota
   * @param nameParam
   */
  private getValorParamDoRoute(nameParam) {
    const data = this.route.snapshot.data;

    let valor;

    for (const index of Object.keys(data)) {
      const param = data[index];

      if (param !== null && typeof param === 'object' && param[nameParam] !== undefined) {
        valor = param[nameParam];
        break;
      }
    }
    return valor;
  }

  /**
   * retorna o título da aba de Recurso Responsavel com quebra de linha
   */
  public getTituloAbaRecursoResponsavel(): any {
    const label = this.isIes ? 'TITLE_ABA_RECONSIDERACAO_RESPONSAVEL' : 'TITLE_ABA_RECURSO_RESPONSAVEL';
    return  this.messageService.getDescription(label, ['<div>', '</div><div>', '</div>']);
  }

  /**
   * Retorna título da aba de pedido de substiuição.
   */
  public getTituloPedidoSubstituicao(): string {
    return this.messageService.getDescription('TITLE_PEDIDO_SUBSTITUICAO');
  }

  public inicializaIes(): void {
    this.isIes = this.dadosChapa.tipoCandidatura.id == Constants.TIPO_CONSELHEIRO_IES;
  }

  /**
   * Responsavel por redirecionar para a aba de recurso apos o salvamento do msm.
   */
  public redirecionarAbaRecurso(event): void {
    this.recursoJulgamento = event;
    this.isCarregadoDadosAbaRecurso = true;
    this.dadosChapa.isCadastradoRecursoJulgamentoFinal = true;
    this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_RECRUSO);
  }

  public redirecionarAbaSubstituicao(event: any, isSegundaInstancia?: boolean): void{

    this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal = true;

    event.sequencia = '01';
    this.substituicaoJulgamento = [event];

    this.controleEstadoAba(Constants.ABA_JULGAMENTO_FINAL_SUBSTITUICAO);
  }

  public redirecionarAbaSubstAposCadSegundaInstancia(event): void {
    this.dadosChapa.statusChapaJulgamentoFinal.id = Constants.STATUS_CHAPA_JULG_FINAL_ANDAMENTO;
    this.substituicaoJulgamento = undefined;
    this.getDadosSubstituicaoJulgamento();
  }

  /**
   * Verifica se aba de recurso deve ser mostrada.
   */
  public isMostrarAbaRecurso(): boolean {
    return (
      this.dadosChapa.isCadastradoRecursoJulgamentoFinal ||
      this.dadosChapa.isFinalizadoAtivRecursoJulgamentoFinal ||
      this.dadosChapa.isCadastradoSubstituicaoJulgamentoFinal
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

}
