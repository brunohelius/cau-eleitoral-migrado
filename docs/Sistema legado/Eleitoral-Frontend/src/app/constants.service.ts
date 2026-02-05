import { Injectable } from '@angular/core';

/**
 * Classe service responsável pelas constantes da aplicação.
 *
 * @author Squadra Tecnologia
 */
@Injectable()
export class Constants {

  public static TIMEZONE = 'UTC';

  public static TAMANHO_LIMITE_ARQUIVO: number = 10485760;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Profissional
   * |--------------------------------------------------------------------------
   */
  public static REGISTRO_ATIVO = 'ATIVO';

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Eleição
   * |--------------------------------------------------------------------------
   */
  public static ELEICAO_CONCLUIDA: number = 2;
  public static ELEICAO_INATIVA: number = 3;
  public static TIPO_NUMERO_MEMBROS_A: number = 2;
  public static PADRAO_MEMBROS_COORDENADORES: number = 2;
  public static TIPO_COORDENADOR: number = 1;
  public static TIPO_COORDENADOR_SUBSTITUTO: number = 2;
  public static TIPO_COORDENADOR_MEMBRO_SUBSTITUTO: number = 3;
  public static TIPO_MEMBRO: number = 4;
  public static TIPO_SUBSTITUTO: number = 5;
  public static SITUACAO_MEMBRO_PENDENTE: number = 1;
  public static SITUACAO_MEMBRO_CONFIRMADO: number = 2;
  public static SITUACAO_MEMBRO_REJEITADO: number = 3;
  public static ID_IES = 0;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Cabeçalho de E-mail.
   * |--------------------------------------------------------------------------
   */
  public static CABECALHO_EMAIL_NOME_CAMPO_RODAPE_ATIVO: string = 'toggleParametrizarIsRodapeAtivo';
  public static CABECALHO_EMAIL_NOME_CAMPO_CABECALHO_ATIVO: string = 'toggleParametrizarIsCabecalhoAtivo';

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Corpo de E-mail.
   * |--------------------------------------------------------------------------
   */
  public static CORPO_EMAIL_NOME_CAMPO_DESCRICAO = 'descricao';
  public static CORPO_EMAIL_ID_DAFAULT = 1;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Chapa Eleição.
   * |--------------------------------------------------------------------------
   */
  public static IES = "IES";
  public static ID_CAUBR = 165;
  public static ID_STATUS_CHAPA_PENDENTE = 1;

  /**
  * |--------------------------------------------------------------------------
  * | permissões de acesso
  * |--------------------------------------------------------------------------
  */
  public static ROLE_ACESSOR_CEN: string = '01602009';
  public static ROLE_ACESSOR_CE: string = '01602010';
  public static ROLE_ACOMPANHAR_CHAPA: string = '01601024';

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Calendário.
   * |--------------------------------------------------------------------------
   */
  public static CALENDARIO_SITUACAO_EM_PREENCHIMENTO: number = 1;
  public static CALENDARIO_SITUACAO_CONCLUIDO: number = 2;
  public static CALENDARIO_SITUACAO_INATIVADO: number = 3;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Informação Comissão.
   * |--------------------------------------------------------------------------
   */
  public static INFORMACAO_COMISSAO_DUAS_OPCOES: number = 1;
  public static INFORMACAO_COMISSAO_VARIAS_OPCOES: number = 2;
  public static INFORMACAO_COMISSAO_CAU_BR_ID = 165;

  /**
   * |--------------------------------------------------------------------------
   * | Atividades Principais
   * |--------------------------------------------------------------------------
   */
  public static PRIMEIRA_ATIVIDADE_PRINCIPAL: number = 1;
  public static SEGUNDA_ATIVIDADE_PRINCIPAL: number = 2;
  public static TERCEIRA_ATIVIDADE_PRINCIPAL: number = 3;
  public static QUARTA_ATIVIDADE_PRINCIPAL: number = 4;

  /**
   * |--------------------------------------------------------------------------
   * | Atividades Secundarias
   * |--------------------------------------------------------------------------
   */
  public static PRIMEIRA_ATIVIDADE_SECUNDARIA: number = 1;
  public static SEGUNDA_ATIVIDADE_SECUNDARIA: number = 2;
  public static TERCEIRA_ATIVIDADE_SECUNDARIA: number = 3;
  public static QUARTA_ATIVIDADE_SECUNDARIA: number = 4;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Arquivos
   * |--------------------------------------------------------------------------
   */
  public static EXTENSAO_PDF = 'pdf';
  public static EXTENSAO_DOC = 'doc';
  public static EXTENSAO_DOCX = 'docx';

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Atividades Secundárias.
   * |--------------------------------------------------------------------------
   */
  public static ATIVIDADE_SECUNDARIA_UM_PONTO_DOIS_QUANTIDADE_EMAILS: number = 3;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Chapa
   * |--------------------------------------------------------------------------
   */
  public static TIPO_DECLARACAO_CADASTRO_CHAPA_UF = 1;
  public static TIPO_DECLARACAO_CADASTRO_CHAPA_IES = 2;
  public static TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA = 3;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Tipo Status Chapa
   * |--------------------------------------------------------------------------
   */
  public static STATUS_CHAPA_EM_ANDAMENTO = 1;
  public static STATUS_CHAPA_CONCLUIDO = 2;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Tipo de Redes Sociais
   * |--------------------------------------------------------------------------
   */
  public static TIPO_REDE_SOCIAL_FACEBOOK = 1;
  public static TIPO_REDE_SOCIAL_INSTAGRAM = 2;
  public static TIPO_REDE_SOCIAL_LINKEDIN = 3;
  public static TIPO_REDE_SOCIAL_TWITTER = 4;
  public static TIPO_REDE_SOCIAL_OUTROS = 5;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Cadastro de Chapa da Eleição
   * |--------------------------------------------------------------------------
   */
  public static TIPO_CONSELHEIRO_UF_BR: number = 1;
  public static TIPO_CONSELHEIRO_IES: number = 2;
  public static ABA_VISAO_GERAL = 1;
  public static ABA_PLATAFORMA_ELEITORAL_REDES_SOCIAIS = 2;
  public static ABA_MEMBROS_CHAPA = 3;
  public static ABA_DECLARACAO = 4;


  public static TAMALHO_MAXIMO_DESCRICAO_PLATAFORMA_CHAPA = 2000;
  public static TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA = 2000;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Membro de Chapa
   * |--------------------------------------------------------------------------
   */
  public static STATUS_CHAPA_ETAPA_REDES_SOCIAIS_INCLUIDA = 1;
  public static STATUS_CHAPA_ETAPA_MEMBROS_CHAPA_INCLUIDA = 2;
  public static STATUS_CHAPA_ETAPA_CONCLUIDO = 3;

  public static STATUS_MEMBRO_CHAPA_VALIDO = 2;
  public static STATUS_MEMBRO_CHAPA_PENDENTE = 1;

  public static STATUS_MEMBRO_CHAPA_PARTICIPACAO_ACONFIRMADO = 1;
  public static STATUS_MEMBRO_CHAPA_PARTICIPACAO_CONFIRMADO = 2;
  public static STATUS_MEMBRO_CHAPA_PARTICIPACAO_REJEITADO = 3;

  public static TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL = 1;
  public static TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL = 2;
  public static TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES = 3;

  public static TIPO_PARTICIPACAO_MEMBRO_CHAPA_TITULAR = 1;
  public static TIPO_PARTICIPACAO_MEMBRO_CHAPA_SUPLENTE = 2;

  public static TIPO_DOCUMENTO_COMPROB_COMPROVANTE = 1;
  public static TIPO_DOCUMENTO_COMPROB_CARTA_INDICACAO = 2;

  public static STATUS_A_CONFIRMAR = 1;
  public static STATUS_CONFIRMADO = 2;
  public static STATUS_SEM_PENDENCIA = 2;
  public static STATUS_REJEITADO = 3;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Status Atividade
   * |--------------------------------------------------------------------------
   */
  public static STATUS_ATIVIDADE_REGRAS_NAO_DEFINIDAS = 0;
  public static STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO = 1;
  public static STATUS_ATIVIDADE_AGUARDANDO_DOCUMENTO = 2;
  public static STATUS_ATIVIDADE_PARAMETRIZACAO_CONCLUIDA = 3;
  public static STATUS_ATIVIDADE_AGUARDANDO_ATUALIZACAO = 4;
  public static STATUS_ATIVIDADE_ATUALIZACAO_CONCLUIDA = 5;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Cadastro de Denuncia
   * |--------------------------------------------------------------------------
   */
  public static ABA_CADASTRO_DENUNCIA = 2;
  public static TIPO_DENUNCIA_CHAPA = 1;
  public static TIPO_DENUNCIA_OUTROS = 4;
  public static TIPO_DENUNCIA_MEMBRO_CHAPA = 2;
  public static TIPO_DENUNCIA_MEMBRO_COMISSAO = 3;
  public static SITUACAO_DENUNCIA_EM_ANALISE = 1;
  public static SITUACAO_DENUNCIA_EM_JULGAMENTO = 2;
  public static SITUACAO_DENUNCIA_EM_RELATORIA = 3;
  public static SITUACAO_DENUNCIA_EM_JULGAMENTO_DENUNCIA_ADMITIDA = 7;
  public static SITUACAO_DENUNCIA_TRANSITADO_EM_JULGADO = 9;
  public static SITUACAO_DENUNCIA_EM_JULGAMENTO_SEGUNDA_INSTANCIA = 10;
  public static SITUACAO_DENUNCIA_TRANSITADA_JULGADO = 9;
  public static STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA = 6;
  public static STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO = 8;
  public static SITUACAO_DENUNCIA_AGUARDANDO_DEFESA = 12;
  public static TAMANHO_LIMITE_2000 = 2000;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Análise de Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_ANALISE_ADMISSIBILIDADE = 3;


  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Recurso Denunciate
  * |--------------------------------------------------------------------------
  */
  public static ABA_RECURSO_DENUNCIANTE = 7;
  public static ABA_RECURSO_DENUNCIADO = 8;
  public static TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE = 1;
  public static TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO = 2;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Julgamento Recurso Segunda Instância
  * |--------------------------------------------------------------------------
  */
  public static ABA_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 9;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Julgamento de Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_JULGAMENTO_ADMISSIBILIDADE = 10;
  public static TIPO_JULGAMENTO_ADMISSIBILIDADE_PROVIDO = 1;
  public static TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIDO = 2;
  public static TIPO_JULGAMENTO_RECURSO_ADMISSIBILIDADE_PROVIDO = 1;
  public static TIPO_JULGAMENTO_RECURSO_ADMISSIBILIDADE_IMPROVIDO = 2;

  public static ABA_ADMISSAO = 11;
  public static ABA_INADMISSAO = 12;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Recurso Julgamento de Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_RECURSO_ADMISSIBILIDADE = 999;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Julgamento Recurso Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_JULGAMENTO_RECURSO_ADMISSIBILIDADE = 888;


   /**
   * |--------------------------------------------------------------------------
   * | Constantes de Defesa
   * |--------------------------------------------------------------------------
   */
  public static PRAZO_PADRAO_ENCAMINHAMENTO_DEFESA = 2;
  public static TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO = 1;
  public static TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS = 2;
  public static TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO = 3;
  public static TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS = 4;
  public static TIPO_ENCAMINHAMENTO_PARECER_FINAL = 5;
  public static STATUS_ENCAMINHAMENTO_PENDENTE = 1;
  public static STATUS_ENCAMINHAMENTO_TRANSCORRIDO = 2;
  public static STATUS_ENCAMINHAMENTO_FECHADO = 3;
  public static STATUS_ENCAMINHAMENTO_CONCLUIDO = 4;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Parecer
   * |--------------------------------------------------------------------------
   */
  public static ABA_PARECER = 5;

    /**
  * |--------------------------------------------------------------------------
  * | Constantes de Recurso e Contrarrazão
  * |--------------------------------------------------------------------------
  */
  public static  PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA = 5;

    /**
   * |----------------------------------------------------------------------------
   * | Constantes de Situações da substituição dos membros.
   * |----------------------------------------------------------------------------
   */
  public static EM_ANDAMENTO = 1;
  public static DEFERIDO = 2;
  public static INDEFERIDO = 3;
  public static SUBSTITUICAO_EM_ANDAMENTO_RECURDO = 4;
  public static SUBSTITUICAO_DEFERIDO_RECURSO = 5;
  public static SUBSTITUICAO_INDEFERIDO_RECURSO = 6;


  /**
   * |----------------------------------------------------------------------------
   * | Pedido de Impugnação.
   * |----------------------------------------------------------------------------
   */
  public static ABA_DETALHAR_IMPUGNACAO_VISAO_GERAL = 0;
  public static ABA_DETALHAR_IMPUGNACAO_PEDIDO = 1;
  public static ABA_DETALHAR_IMPUGNACAO_DEFESA = 2;
  public static ABA_DETALHAR_IMPUGNACAO_JULGAMENTO = 3;
  public static ABA_DETALHAR_RECURSO_RESPONSAVEL = 4;
  public static ABA_DETALHAR_RECURSO_IMPUGNANTE = 5;
  public static ABA_DETALHAR_JULGAMENTO_2_INSTANCIA = 6;
  public static ABA_SUBSTITUICAO_IMPUGNADO = 7;

  public static ARQUIVO_TAMANHO_10_MEGA = 1;
  public static ARQUIVO_TAMANHO_15_MEGA = 3;
  public static ARQUIVO_TIPOS_DIVERSOS_TAMANHO_25_MEGA = 2;

  public static STATUS_IMPUGNACAO_EM_ANALISE = 1;
  public static STATUS_IMPUGNACAO_PROCEDENTE = 2;
  public static STATUS_IMPUGNACAO_IMPROCEDENTE = 3;
  public static STATUS_IMPUGNACAO_RECURSO_EM_ANALISE = 4;
  public static STATUS_IMPUGNACAO_RECURSO_PROCEDENTE = 5;
  public static STATUS_IMPUGNACAO_RECURSO_IMPROCEDENTE = 6;

  /**
   * |----------------------------------------------------------------------------
   * | Pedido de Impugnação Resultado.
   * |----------------------------------------------------------------------------
   */
  public static ABA_DETALHAR_IMPUGNACAO_RESULTADO_VISAO_GERAL = 0;
  public static ABA_DETALHAR_IMPUGNACAO_RESULTADO_DETALHAR = 1;
  public static ABA_DETALHAR_IMPUGNACAO_RESULTADO_ALEGACAO = 2;
  public static ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO = 3;
  public static ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 4;
  public static ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO = 5;
  public static ABA_DETALHAR_IMPUGNACAO_RESULTADO_JULGAMENTO_SEGUNDA = 6;
  public static STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE = 1;
  public static STATUS_IMPUGNACAO_RESULTADO_IMPROCEDENTE = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Shared component.
   * |----------------------------------------------------------------------------
   */
  public static TAMANHO_MAXIMO_PADRAO_EDITOR_TEXTO = 2000;
  public static TAMANHO_MINIMO_PADRAO_EDITOR_TEXTO = 0;

  /**
   * |----------------------------------------------------------------------------
   * | Acompanhar o pedido de Substuicao.
   * |----------------------------------------------------------------------------
   */
  public static TIPO_JULGAMENTO_DEFERIDO = 1;
  public static TIPO_JULGAMENTO_INDEFERIDO = 2;
  public static ABA_PRINCIAPAL_NOME = 'principal';

  /**
   * |----------------------------------------------------------------------------
   * | Julgamento do pedido de Substuicao.
   * |----------------------------------------------------------------------------
   */
  public static STATUS_DEFERIMENTO_PEDIDO_SUBSITUICAO = 1;
  public static STATUS_INDEFERIMENTO_PEDIDO_SUBSITUICAO = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Recurso do Julgamento do Pedido de Substuicao.
   * |----------------------------------------------------------------------------
   */
  public static ID_TIPO_RESPONSAVEL = 1;
  public static ID_TIPO_IMPUGNANTE =  2;

  /**
   * |----------------------------------------------------------------------------
   * | Recurso do Julgamento do Pedido de Impugnação.
   * |----------------------------------------------------------------------------
   */
  public static ID_TIPO_PROCEDENTE = 1;
  public static ID_TIPO_IMPROCEDENTE =  2;

   /**
   * |--------------------------------------------------------------------------
   * | Constantes de Julgamento de Denúncia
   * |--------------------------------------------------------------------------
   */
  public static TIPO_JULGAMENTO_PROCEDENTE = 1;
  public static TIPO_JULGAMENTO_IMPROCEDENTE = 2;
  public static TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA = 1;
  public static TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA = 2;
  public static TIPO_SENTENCA_JULGAMENTO_CASSACAO_REGISTRO_CANDIDATURA = 3;
  public static TIPO_SENTENCA_JULGAMENTO_MULTA = 4;
  public static TIPO_SENTENCA_JULGAMENTO_OUTRAS_ADEQ_PROPORC_GRAU_INFRAC_COMETIDA = 5;

  public static ABA_ATUAL = 6;
  public static ABA_RETIFICACAO = 7;



  public static TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR = 2000;
  /**
   * |----------------------------------------------------------------------------
   * | Julgamento Final das chapas
   * |----------------------------------------------------------------------------
   */
  public static ID_STATUS_JULGAMENTO_FINAL_DEFERIDO = 1;
  public static ID_STATUS_JULGAMENTO_FINAL_INDEFERIDO = 2;
  public static ABA_ACOMPANHAR_CHAPA = 'acompanhar-chapa';
  public static ABA_JULGAMENTO_FINAL_PRIMEIRA = 'julgamento-primeira-instancia';
  public static IS_RECURSO = "Recurso";
  public static E_RECURSO = "RECURSO";
  public static ABA_RECURSO_JULGAMENTO_FINAL = "recurso-julgamento";
  public static ABA_JULGAMENTO_FINAL_SUBSTITUICAO = 3;
  public static ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA = 'julgamento-substituicao-segunda-instancia';
  public static ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA = 'julgamento-recurso-segunda-instancia';
  public static OPCAO_ALTERAR_DECISAO_JULGAMENTO = 1;
  public static OPCAO_ALTERAR_CONTEUDO_JULGAMENTO = 2;
  public static ID_SELECAO_OPCAO_DECISAO = 2;
  public static ID_SELECAO_OPCAO_CONTEUDO = 1;



  /**
   * |----------------------------------------------------------------------------
   * | Constantes denuncia
   * |----------------------------------------------------------------------------
   */
  public static ABA_DEFESA = 4;
  public static ABA_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 6;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes denuncia
   * |----------------------------------------------------------------------------
   */
  public static TIPO_CANDIDATURA_UF_BR = 1;
  public static TIPO_CANDIDATURA_IES = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes do Impugnação de Resultado.
   * |----------------------------------------------------------------------------
   */
  public static ID_STATUS_AGUARDANDO_ALEGACOES_IMPUGNACAO_RESULTADO = 1;
  public static TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO = 1;
  public static TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 2;

  /**
   * |--------------------------------------------------------------------------
   * | Título
   * |--------------------------------------------------------------------------
   */
   public static TITULO_ARQUITETO_URBANISTA = 15;
   public static TITULO_ENGENHEIRO_SEGURANCA_TRABALHO = 162;
}
