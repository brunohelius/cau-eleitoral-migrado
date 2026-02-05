import { Injectable } from '@angular/core';

/**
 * Classe service responsável pelas constantes da aplicação.
 *
 * @author Squadra Tecnologia
 */
@Injectable()
export class Constants {

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
  public static CAUBR_ID: number = 165;
  public static IES_ID: number = 1000;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Declaração
   * |--------------------------------------------------------------------------
   */

  public static PERMITE_PDF: number = 1;
  public static PERMITE_DOC: number = 1;
  public static PERMITE_UPLOAD: number = 1;
  public static UPLOAD_OBRIGATORIO: number = 1;
  public static TIPO_RESPOSTA_UNICA: number = 2;
  public static TIPO_RESPOSTA_MULTIPLA: number = 1;
  public static TIPO_PROCESSO_ORDINARIO: number = 1;
  public static TIPO_PROCESSO_EXTRAORDINARIO: number = 2;

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

  public static TAMANHO_LIMITE_2000 = 2000;
  public static TAMALHO_MAXIMO_NARRACAO_FATOS_DENUNCIA = 2000;
  public static TAMALHO_MAXIMO_DESCRICAO_PLATAFORMA_CHAPA = 2000;
  public static TAMALHO_MAXIMO_DESCRICAO_DESIGNACAO_RELATOR = 2000;
  public static TAMALHO_MAXIMO_DESCRICAO_RECURSO_RECONSIDERACAO = 2000;

  public static CODIGO_ERRO_CPF_NOME_NAO_ENCONTRADO = 'MSG-060';
  public static CODIGO_ERRO_NUMERO_REGISTRO_NOME_NAO_ENCONTRADO = 'MSG-105';

  /**
  * |--------------------------------------------------------------------------
  * | permissões de acesso
  * |--------------------------------------------------------------------------
  */
  public static ROLE_ACESSOR_CEN: string = "01602009";
  public static ROLE_ACESSOR_CE: string = '01602010';
  public static ROLE_ACOMPANHAR_CHAPA: string = '01601024';

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
   * | Constantes de Tipo Status Chapa
   * |--------------------------------------------------------------------------
   */
  public static STATUS_CHAPA_EM_ANDAMENTO = 1;
  public static STATUS_CHAPA_CONCLUIDO = 2;

  public static STATUS_CHAPA_JULG_FINAL_ANDAMENTO = 1;
  public static STATUS_CHAPA_JULG_FINAL_DEFERIDO = 2;
  public static STATUS_CHAPA_JULG_FINAL_INDEFERIDO = 3;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de validação de arquivos
  * |--------------------------------------------------------------------------
  */
  public static TAMANHO_LIMITE_ARQUIVO: number = 10485760;
  public static ARQUIVO_TAMANHO_10_MEGA = 1;
  public static ARQUIVO_TAMANHO_15_MEGA = 3;
  public static ARQUIVO_TIPOS_DIVERSOS_TAMANHO_25_MEGA = 2;

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

  /**
  * |--------------------------------------------------------------------------
  * | Constantes currículo membro chapa.
  * |--------------------------------------------------------------------------
  */
  public static TAMANHO_MAXIMO_DESCRICAO_CURRICULO_MEMBRO_CHAPA = 2000;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de tipo de declaração para Chapa Eleitoral.
  * |--------------------------------------------------------------------------
  */
  public static TIPO_DECLARACAO_CADASTRO_CHAPA_UF = 1;
  public static TIPO_DECLARACAO_CADASTRO_CHAPA_IES = 2;
  public static TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA = 3;
  public static TIPO_DECLARACAO_REPRESENTATIVIDADE = 8;

  public static TIPO_RESPOSTA_DECLARACAO_MULTIPLA = 1;
  public static TIPO_RESPOSTA_DECLARACAO_UNICA = 2;

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
  public static SITUACAO_DENUNCIA_AGUARDANDO_RELATOR = 4;
  public static SITUACAO_DENUNCIA_EM_JULGAMENTO_ADMITIDA = 7;
  public static SITUACAO_DENUNCIA_AGUARDANDO_DEFESA = 12;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Análise de Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_ANALISE_ADMISSIBILIDADE = 3;

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
   * | Constantes de Julgamento Recruso de Admissibilidade
   * |--------------------------------------------------------------------------
   */
  public static ABA_JULGAMENTO_RECURSO_ADMISSIBILIDADE = 888;


  

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Defesa
   * |--------------------------------------------------------------------------
   */
  public static ABA_DEFESA = 4;
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
   * | Constantes de Parecer Final da Denúncia
   * |--------------------------------------------------------------------------
   */
  public static TIPO_JULGAMENTO_PROCEDENTE = 1;
  public static TIPO_JULGAMENTO_IMPROCEDENTE = 2;
  public static TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA = 1;
  public static TIPO_SENTENCA_JULGAMENTO_SUSPENSAO_PROPAGANDA = 2;
  public static TIPO_SENTENCA_JULGAMENTO_CASSACAO_REGISTRO_CANDIDATURA = 3;
  public static TIPO_SENTENCA_JULGAMENTO_MULTA = 4;
  public static TIPO_SENTENCA_JULGAMENTO_OUTRAS_ADEQ_PROPORC_GRAU_INFRAC_COMETIDA = 5;

  /**
 * |--------------------------------------------------------------------------
 * | Constantes de Recurso Denunciate
 * |--------------------------------------------------------------------------
 */
  public static ABA_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 6;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Recurso Denunciate
  * |--------------------------------------------------------------------------
  */
  public static ABA_RECURSO_DENUNCIANTE = 7;
  public static ABA_RECURSO_DENUNCIADO = 8;
  public static TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE = 1;
  public static  TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO = 2;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Julgamento Recurso Segunda Instância
  * |--------------------------------------------------------------------------
  */
  public static ABA_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 9;

  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Recurso e Contrarrazão
  * |--------------------------------------------------------------------------
  */
  public static  PRAZO_PADRAO_QUANTIDADE_DIAS_RECURSO_DENUNCIA = 5;

  /**
   * |--------------------------------------------------------------------------
   * | Constantes de Atividade do Calendário
   * |--------------------------------------------------------------------------
   */
  public static TIPO_ATIVIDADE_PRINCIPAL_DENUNCIA = 4;
  public static TIPO_ATIVIDADE_SECUNDARIA_DENUNCIA = 1;
  public static TIPO_ATIVIDADE_PRINCIPAL_IMPUGNACAO_RESULTADO = 6;
  public static TIPO_ATIVIDADE_SECUNDARIA_CADASTRO_IMPUGNACAO_RESULTADO = 1;
  public static TIPO_ATIVIDADE_SECUNDARIA_ALEGACAO_IMPUGNACAO_RESULTADO = 2;
  /**
   * |--------------------------------------------------------------------------
   * | Constantes do Membro Chapa.
   * |--------------------------------------------------------------------------
   */
  public static STATUS_COM_PENDENCIA = 1;
  public static STATUS_SEM_PENDENCIA = 2;

  public static STATUS_A_CONFIRMAR = 1;
  public static STATUS_CONFIRMADO = 2;

  public static POSICAO_TITULAR = 0;

  public static TAMANHO_MAXIMO_JUSTIFICATIVA_SUBSTITUICAO = 1000;

  public static ARQUIVO_TAMANHO_1_MEGA = 1;

  public static MEMBRO_CHAPA_RESPONSAVEL = 1;
  /**
  * |--------------------------------------------------------------------------
  * | Constantes de Chapa Eleição.
  * |--------------------------------------------------------------------------
  */
  public static IES = 'IES';
  public static ID_IES = 0;
  public static ID_CAUBR = 165;
  public static PREFIXO_CAUBR = 'CAU/BR';
  public static ID_STATUS_CHAPA_PENDENTE = 1;

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
  * | Constantes de Situações da recurso substituição dos membros.
  * |----------------------------------------------------------------------------
  */
  public static STATUS_RECURSO_DEFERIDO = 1;
  public static STATUS_RECURSO_INDEFERIDO = 2;
  /**
  * |----------------------------------------------------------------------------
  * | Constantes de Situações do julgamento final.
  * |----------------------------------------------------------------------------
  */
 public static STATUS_JULGAMENTO_DEFERIDO = 1;
 public static STATUS_JULGAMENTO_INDEFERIDO = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes de Situação da Denuncia.
   * |----------------------------------------------------------------------------
   */
  public static SITUACAO_ANALISE_ADMISSIBILIDADE: number = 1;
  /**
   * |----------------------------------------------------------------------------
   * | Shared component.
   * |----------------------------------------------------------------------------
   */
  public static TAMANHO_MAXIMO_PADRAO_EDITOR_TEXTO = 2000;
  public static TAMANHO_MINIMO_PADRAO_EDITOR_TEXTO = 0;

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
  public static ABA_DETALHAR_JULGAMENTO_SEGUNDA_INSTANCIA = 6;
  public static ABA_SUBSTITUICAO_IMPUGNADO = 7;

  public static STATUS_IMPUGNACAO_EM_ANALISE = 1;
  public static STATUS_IMPUGNACAO_PROCEDENTE = 2;
  public static STATUS_IMPUGNACAO_IMPROCEDENTE = 3;
  public static STATUS_IMPUGNACAO_RECURSO_EM_ANALISE = 4;
  public static STATUS_IMPUGNACAO_RECURSO_PROCEDENTE = 5;
  public static STATUS_IMPUGNACAO_RECURSO_IMPROCEDENTE = 6;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes de status do pedido de impugnação
   * |----------------------------------------------------------------------------
   */
  public static STATUS_JULGAMENTO_PROCEDENTE = 1;
  public static STATUS_JULGAMENTO_IMPROCEDENTE = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Julgamento Final.
   * |----------------------------------------------------------------------------
   */
  public static ABA_JULGAMENTO_FINAL_PRINCIPAL = 0;
  public static ABA_JULGAMENTO_FINAL_DETALHAR = 1;
  public static ABA_JULGAMENTO_FINAL_RECRUSO = 2;
  public static ABA_JULGAMENTO_FINAL_SUBSTITUICAO = 3;
  public static ABA_PRINCIPAL = 0;
  public static ABA_RODRIGO = 1;
  public static ABA_JOAO = 2;
  public static ABA_CHAPA = 3;
  public static ABA_JULGAMENTO = 5;
  public static ABA_RECRUSO_JULGAMENTO = 4;
  public static ABA_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA = 6;
  public static ABA_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA = 7;
  public static E_RECURSO = "RECURSO";

  /**
   * |----------------------------------------------------------------------------
   * | Status dos pedidos de Impugnação.
   * |----------------------------------------------------------------------------
   */

  public static IMPUGNACAO_EM_ANALISE = 1;
  public static IMPUGNACAO_PROCEDENTE = 2;
  public static IMPUGNACAO_IMPROCEDENTE = 3;

  /**
   * |----------------------------------------------------------------------------
   * | Tipos de profissionais para controle de rota
   * |----------------------------------------------------------------------------
   */
  public static TIPO_PROFISSIONAL = 'profissional';
  public static TIPO_PROFISSIONAL_CHAPA = 'membroChapa';
  public static TIPO_PROFISSIONAL_COMISSAO = 'membroComissao';
  public static TIPO_PROFISSIONAL_COMISSAO_CE = 'membroComissaoCe';

  /**
   * |----------------------------------------------------------------------------
   * | Constantes de abas de julgamento de substituição de profissionais
   * |----------------------------------------------------------------------------
   */
  public static ABA_RECURSO = 3;
  public static TITLE_RECURSO = 1;
  public static ID_TIPO_RESPONSAVEL = 1;
  public static ID_TIPO_IMPUGNANTE = 2;
  public static ABA_PEDIDO_SUBSTITUICAO = 1;
  public static ABA_JULGAMENTO_PRIMEIRA_INSTANCIA = 2;
  public static ABA_JULGAMENTO_SEGUNDA_INSTANCIA = 4;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes de sentenças de julgamento
   * |----------------------------------------------------------------------------
   */
  public static ID_TIPO_SENTENCA_ADVERTENCIA = 1;
  public static ID_TIPO_SENTENCA_SUSPENSAO = 2;
  public static ID_TIPO_SENTENCA_CASSACAO = 3;
  public static ID_TIPO_SENTENCA_MULTA = 4;
  public static ID_TIPO_SENTENCA_OUTRAS = 5;

  /**
   * |----------------------------------------------------------------------------
   * | Constantes do Recurso do Julgamento final.
   * |----------------------------------------------------------------------------
   */
  public static IS_RECURSO = 'Recurso';
  public static IS_RECONSIDERACAO = 'Reconsideração';

  /**
   * |----------------------------------------------------------------------------
   * | Constantes do Impugnação de Resultado.
   * |----------------------------------------------------------------------------
   */
  public static ABA_ALEGACAO = 1;
  public static HAS_ALEGACAO = 1;
  public static ABA_IMPUGNACAO = 0;
  public static ABA_JULGAMENTO_IMPUGNACAO_RESULTADO = 2;
  public static ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 3;
  public static ABA_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO_IMPUGNADO = 4;
  public static ABA_JULGAMENTO_SEGUNDA_INSTANCIA_IMPUGNACAO_RESULTADO = 5;
  public static STATUS_IMPUGNACAO_RESULTADO_PROCEDENTE = 1;
  public static STATUS_IMPUGNACAO_RESULTADO_IMPROCEDENTE = 2;
  public static ID_STATUS_AGUARDANDO_ALEGACOES_IMPUGNACAO_RESULTADO = 1;

  public static TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO = 1;
  public static TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 2;

  /**
   * |----------------------------------------------------------------------------
   * | Itens declaração Representatividade
   * |----------------------------------------------------------------------------
   */
  public static TIPO_PESSOA_FORMACAO_10_ANOS = 77;
  public static TIPO_PESSOA_FORMACAO_INTERIOR = 78;
}
