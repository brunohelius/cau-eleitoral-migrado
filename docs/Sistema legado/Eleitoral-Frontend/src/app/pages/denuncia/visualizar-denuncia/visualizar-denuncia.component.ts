import { Router, ActivatedRoute } from '@angular/router';
import { Component, OnInit } from '@angular/core';

import { LayoutsService } from '@cau/layout';
import { MessageService } from '@cau/message';
import { Constants } from 'src/app/constants.service';
import { DenunciaClientService } from 'src/app/client/denuncia-client/denuncia-client.service';

@Component({
  selector: 'app-visualizar-denuncia',
  templateUrl: './visualizar-denuncia.component.html',
  styleUrls: ['./visualizar-denuncia.component.scss']
})
export class VisualizarDenunciaComponent implements OnInit {

  public abas: any = {};
  public dadosFormulario: any;
  public tipoDenuncia: any;
  public encaminhamentosCarregados: any;
  public retificacoesPrimeiraInstanciaCarregadas: any;
  public retificacoesSegundaInstanciaCarregadas: any;

  public denuncia: any;
  public tipoMembroComissao: any;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private layoutsService: LayoutsService,
    private messageService: MessageService,
    private denunciaService: DenunciaClientService
  ) {

  }

  ngOnInit() {
    this.layoutsService.onLoadTitle.emit({
      icon: 'fa fa-user',
      description: this.messageService.getDescription('LABEL_DENUNCIA')
    });

    this.dadosFormulario = this.route.snapshot.data["denunciaResolve"];
    this.tipoDenuncia = this.dadosFormulario.denuncia.id_tipo_denuncia;
    this.dadosFormulario = this.getEstruturaDadosFormulario();
    this.dadosFormulario.condicao = this.route.snapshot.data["condicaoResolve"];

    this.inicializaAbas();
  }

  /**
   * Retorna a estrutura de dados do formulário atualizado.
   */
  private getEstruturaDadosFormulario = () => {
    let dadosDenuncia = this.dadosFormulario;
    let registroNacional = dadosDenuncia.denuncia.registro_nacional
      ? dadosDenuncia.denuncia.registro_nacional.replace(/^0+/, '')
      : "-";

    let estruturaDadosFormulario = {
      recursoDenunciado: undefined,
      recursoDenunciante: undefined,
      contrarrazaoDenunciado: undefined,
      contrarrazaoDenunciante: undefined,
      idTipoDenuncia: this.tipoDenuncia,
      encaminhamentosDenuncia: undefined,
      arquivos: dadosDenuncia.arquivoDenuncia,
      idCauUf: dadosDenuncia.denuncia.id_cau_uf,
      isSigiloso: dadosDenuncia.denuncia.sigiloso,
      testemunhas: dadosDenuncia.testemunhaDenuncia,
      narracaoFatos: dadosDenuncia.denuncia.ds_fatos,
      idDenuncia: dadosDenuncia.denuncia.id_denuncia,
      dataHoraDenuncia: dadosDenuncia.denuncia.dt_denuncia,
      isAssessorCEN: dadosDenuncia.denuncia.is_assessor_cen,
      isRelatorAtual: dadosDenuncia.denuncia.is_relator_atual,
      isAssessorCEUf: dadosDenuncia.denuncia.is_assessor_ce,
      numeroDenuncia: dadosDenuncia.denuncia.numero_sequencial,
      narracaoFatosSimpleText: dadosDenuncia.denuncia.ds_fatos,
      defesaApresentada: dadosDenuncia.denuncia.denuncia_defesa,
      hasEleicaoVigente: dadosDenuncia.denuncia.has_eleicao_vigente,
      idSituacaoDenuncia: dadosDenuncia.denuncia.id_situacao_denuncia,
      julgamento_denuncia : dadosDenuncia.denuncia.julgamento_denuncia,
      prazoRecursoDenuncia: dadosDenuncia.denuncia.has_prazo_recurso_denuncia,
      hasDefesaPrazoEncerrado: dadosDenuncia.denuncia.has_defesa_prazo_encerrado,
      analiseAdmissibilidade: this.getAnaliseAdmissibilidadeDenuncia(dadosDenuncia.denuncia),
      hasContrarrazaoDenunciadoDentroPrazo: dadosDenuncia.denuncia.has_contrarrazao_denunciado_dentro_prazo,
      hasContrarrazaoDenuncianteDentroPrazo: dadosDenuncia.denuncia.has_contrarrazao_denunciante_dentro_prazo,
      julgamentoAdmissibilidade: dadosDenuncia.denuncia.julgamento_admissibilidade,
      denunciado: {
        uf: {
          prefixo: dadosDenuncia.denuncia.prefixo
        }
      },
      denunciante: {
        registroNacional,
        email: dadosDenuncia.denuncia.email,
        name: dadosDenuncia.denuncia.nome_denunciante,
      },
      impedimentoSuspeicao: dadosDenuncia.denuncia.impedimentoSuspeicao,
    };

    if(dadosDenuncia.denuncia.coordenadorComissao) {
      estruturaDadosFormulario['coordenadorComissao'] = {
        name: dadosDenuncia.denuncia.coordenadorComissao.profissional.nome
      }
    }

    if (this.tipoDenuncia == Constants.TIPO_DENUNCIA_CHAPA) {
      estruturaDadosFormulario.denunciado['chapa'] = {
        numeroChapa: dadosDenuncia.denuncia.nome_denunciado
      }
    }

    if (this.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_CHAPA
      || this.tipoDenuncia == Constants.TIPO_DENUNCIA_MEMBRO_COMISSAO) {
      estruturaDadosFormulario.denunciado['membro'] = {
        nome: dadosDenuncia.denuncia.nome_denunciado
      }
    }

    if (dadosDenuncia.denuncia.encaminhamentos_denuncia.length > 0) {
      estruturaDadosFormulario.encaminhamentosDenuncia = dadosDenuncia.denuncia.encaminhamentos_denuncia;
    }

    if (dadosDenuncia.denuncia.recursos_denuncia) {
      estruturaDadosFormulario.recursoDenunciado = this.getRecursoDenunciado(dadosDenuncia.denuncia.recursos_denuncia);
      estruturaDadosFormulario.recursoDenunciante = this.getRecursoDenunciante(dadosDenuncia.denuncia.recursos_denuncia);

      if (estruturaDadosFormulario.recursoDenunciado && estruturaDadosFormulario.recursoDenunciado.contrarrazao) {
        estruturaDadosFormulario.contrarrazaoDenunciante =  estruturaDadosFormulario.recursoDenunciado.contrarrazao;
      }

      if (estruturaDadosFormulario.recursoDenunciante && estruturaDadosFormulario.recursoDenunciante.contrarrazao) {
        estruturaDadosFormulario.contrarrazaoDenunciado =  estruturaDadosFormulario.recursoDenunciante.contrarrazao;
      }
    }

    return estruturaDadosFormulario;
  }

    /**
   * Retorna o Recurso do Denunciado.
   */
  public getRecursoDenunciado = (recursosDenuncia: any) => {
    let recursoDenunciado = recursosDenuncia.filter((recursoDenuncia) => {
      return recursoDenuncia.tpRecurso == Constants.TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO;
    });
    return recursoDenunciado[0] || undefined;
  }

   /**
   * Retorna o Recurso do Denunciante.
   */
  public getRecursoDenunciante = (recursosDenuncia: any) => {
    let recursoDenunciante = recursosDenuncia.filter((recursoDenuncia) => {
      return recursoDenuncia.tpRecurso == Constants.TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE;
    });
    return recursoDenunciante[0] || undefined;
  }

  /**
   * Inicializa o objeto de abas.
   */
  private inicializaAbas(): void {
    let idAbaAtiva = Constants.ABA_CADASTRO_DENUNCIA;
    this.abas = {
      abaVisaoGeral: { id: Constants.ABA_VISAO_GERAL, nome: 'visaoGeral', ativa: false },
      abaCadastroDenuncia: {
        id: Constants.ABA_CADASTRO_DENUNCIA,
        nome: 'cadastroDenuncia'
      }
    };

    if (this.dadosFormulario.analiseAdmissibilidade != undefined) {
      this.abas.abaAnaliseAdmissibilidade = {
        id: Constants.ABA_ANALISE_ADMISSIBILIDADE,
        nome: 'analiseAdmissibilidade'
      };
      idAbaAtiva = Constants.ABA_ANALISE_ADMISSIBILIDADE;
    }

    if (this.isJulgamentoAdmissibilidadeAtivo()) {
      this.abas.abaJulgamentoAdmissibilidade = {
        id: Constants.ABA_JULGAMENTO_ADMISSIBILIDADE,
        nome: 'julgamentoAdmissibilidade',
      };
      idAbaAtiva = Constants.ABA_JULGAMENTO_ADMISSIBILIDADE;
    }


    if (this.isRecursoJulgamentoAdmissibilidadeAtivo()) {

      this.abas.abaRecursoJulgamentoAdmissibilidade = {
        id: Constants.ABA_RECURSO_ADMISSIBILIDADE,
        nome: 'recursoAdmissibilidade'
      }
      idAbaAtiva = Constants.ABA_RECURSO_ADMISSIBILIDADE
    }

    if (this.hasJulgamentoRecursoAdmissilidadeAtivo()) {
      this.abas.abaJulgamentoRecursoAdmissibilidade = {
        id: Constants.ABA_JULGAMENTO_RECURSO_ADMISSIBILIDADE,
        nome: 'recursoAdmissibilidade'
      }
      idAbaAtiva = Constants.ABA_JULGAMENTO_RECURSO_ADMISSIBILIDADE
    }

    if (this.abas.abaAnaliseAdmissibilidade != undefined && this.isAbaDefesaAtiva()) {
      this.abas.abaDefesaApresentada = {
        id: Constants.ABA_DEFESA,
        nome: 'apresentacaoDefesa'
      };
      idAbaAtiva = Constants.ABA_DEFESA;
    }

    if (this.abas.abaDefesaApresentada != undefined && this.dadosFormulario.encaminhamentosDenuncia != undefined) {
      this.abas.abaParecerDenuncia = {
        id: Constants.ABA_PARECER,
        nome: 'parecerDenuncia'
      };
      idAbaAtiva = Constants.ABA_PARECER;
    }

    if (this.abas.abaParecerDenuncia && this.dadosFormulario.julgamento_denuncia) {
      this.abas.abaJulgamentoPrimeiraInstancia = {
        id: Constants.ABA_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
        nome: 'julgamentoDenuncia'
      };
      idAbaAtiva = Constants.ABA_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA;
    }

    if (this.abas.abaJulgamentoPrimeiraInstancia != undefined && (this.dadosFormulario.recursoDenunciante != undefined
      || (this.dadosFormulario.recursoDenunciante == undefined && !this.dadosFormulario.prazoRecursoDenuncia))) {
      this.abas.abaRecursoDenunciante = {
        id: Constants.ABA_RECURSO_DENUNCIANTE,
        nome: 'recursoDenunciante'
      };
      idAbaAtiva = Constants.ABA_RECURSO_DENUNCIANTE;
    }

    if (this.abas.abaJulgamentoPrimeiraInstancia != undefined && (this.dadosFormulario.recursoDenunciado != undefined
      || (this.dadosFormulario.recursoDenunciado == undefined && !this.dadosFormulario.prazoRecursoDenuncia))) {
      this.abas.abaRecursoDenunciado = {
        id: Constants.ABA_RECURSO_DENUNCIADO,
        nome: 'recursoDenunciado'
      };
      idAbaAtiva = Constants.ABA_RECURSO_DENUNCIADO;
    }

  if ((this.abas.abaRecursoDenunciado != undefined || this.abas.abaRecursoDenunciante != undefined)
    && (this.dadosFormulario.recursoDenunciado != undefined && this.dadosFormulario.recursoDenunciado.julgamentoRecurso != undefined)
      || this.abas.abaRecursoDenunciante != undefined && (this.dadosFormulario.recursoDenunciante != undefined && this.dadosFormulario.recursoDenunciante.julgamentoRecurso != undefined)
  ) {
    this.abas.abaJulgamentoSegundaInstancia = {
      id: Constants.ABA_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
      nome: 'julgamentoRecurso'
    };
    idAbaAtiva = Constants.ABA_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA;
  }

  this.mudarAba(idAbaAtiva);
  }

  public hasJulgamentoRecursoAdmissilidadeAtivo() {
    return this.isRecursoJulgamentoAdmissibilidadeAtivo() &&

      this.dadosFormulario
        .julgamentoAdmissibilidade
        .recursoJulgamentoAdmissibilidade !== undefined &&

      this.dadosFormulario
        .julgamentoAdmissibilidade
        .recursoJulgamentoAdmissibilidade.
        julgamentoRecurso !== undefined;
  }

  public isJulgamentoAdmissibilidadeAtivo() {
    return !!this.dadosFormulario.julgamentoAdmissibilidade;
  }


  public isRecursoJulgamentoAdmissibilidadeAtivo() {

    if (this.dadosFormulario.condicao.recurso_admissibilidade.parazoRecurso === undefined) {
      return false;
    }

    let prazoRecurso = this.dadosFormulario.condicao.recurso_admissibilidade.parazoRecurso;

    let recurso = this.dadosFormulario.julgamentoAdmissibilidade !== undefined
      && this.dadosFormulario.julgamentoAdmissibilidade.recursoJulgamentoAdmissibilidade !== undefined;

    if (prazoRecurso && recurso) {
      return true;
    }

    if (!prazoRecurso && recurso) {
      return true;
    }

    if (!prazoRecurso && !recurso) {
      return true;
    }

    return false;
  }

  /**
   * Muda a aba selecionada de acordo com a seleção.
   *
   * @param aba
   */
  public mudarAbaSelecionada(aba: number): void {
    if (aba == Constants.ABA_VISAO_GERAL) {
      this.router.navigate(['/']);
    } else {
      this.mudarAba(aba);
    }
  }

  /**
   * Seta os valores de controle dos parametros referente ao botao de analise de defesa na aba de parecer.
   */
  public setEncaminhamentosCarregadosParecer = (encaminhamentosCarregados) => {
    if (this.encaminhamentosCarregados == undefined) {
      this.encaminhamentosCarregados = encaminhamentosCarregados;
    }
  }

  /**
   * Seta os valores de controle dos parametros referente as retificações do julgamento 1ª instancia.
   */
  public setRetificacoesCarregadasPrimeiraInstancia = (retificacoes) => {
    if (this.retificacoesPrimeiraInstanciaCarregadas == undefined) {
      this.retificacoesPrimeiraInstanciaCarregadas = retificacoes;
    }
  }

  /**
   * Seta os valores de controle dos parametros referente as retificações do julgamento 2ª instancia.
   */
  public setRetificacoesCarregadasSegundaInstancia = (retificacoes) => {
    if (this.retificacoesSegundaInstanciaCarregadas == undefined) {
      this.retificacoesSegundaInstanciaCarregadas = retificacoes;
    }
  }

  /**
   * Muda a aba para a aba selecionada.
   */
  private mudarAba(aba: number): void {
    if (this.abas) {
      for (let tab in this.abas) {
        this.abas[tab].ativa = this.abas[tab].id === aba ? true : false;
      }
    }
  }

  /**
   * Retorna a análise da admissibilidade da denuncia de acordo com a situação da admissibilidade.
   *
   * @param denuncia
   * @return any
   */
  private getAnaliseAdmissibilidadeDenuncia(denuncia: any) {
    let analiseAdmissibilidade: any = {
      admissao: undefined,
      inadmissao: undefined,
    };

    let denunciaAdmitida = denuncia.denuncia_admitida;
    if (denunciaAdmitida !== undefined) {
      analiseAdmissibilidade.admissao = {
        admitida: true,
        dataHora: denunciaAdmitida.dataAdmissao,
        relator: denunciaAdmitida.membroComissao,
        despacho: denunciaAdmitida.descricaoDespacho,
      };
    }

    let denunciaInadmitida = denuncia.denuncia_inadmitida;
    if (denunciaInadmitida !== undefined) {
      analiseAdmissibilidade.inadmissao = {
        inadmitida: true,
        arquivos: denunciaInadmitida.arquivos,
        dataHora: denunciaInadmitida.dataInadmissao,
        despacho: denunciaInadmitida.descricaoInadmissao,
      };
    }

    return analiseAdmissibilidade.admissao || analiseAdmissibilidade.inadmissao
      ? analiseAdmissibilidade
      : undefined;
  }

  /**
   * Retorna se a aba defesa deve estar ativa.
   */
  public isAbaDefesaAtiva = () => {
    return this.dadosFormulario.hasDefesaPrazoEncerrado || this.dadosFormulario.defesaApresentada
    || (Constants.SITUACAO_DENUNCIA_AGUARDANDO_DEFESA === this.dadosFormulario.idSituacaoDenuncia);
  }

  /**
   * Volta para a Tela anterior
   */
  public voltar() {
    window.history.back();
  }
}
