<?php
/*
 * Constants.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Config;

use phpDocumentor\Reflection\Types\Self_;

/**
 * Classe de constantes da aplicação.
 *
 * @package App\Config
 * @author Squadra Tecnologia
 */
class Constants
{
    /*
     * |--------------------------------------------------------------------------
     * | Ambientes
     * |--------------------------------------------------------------------------
     */
    const DEV = "dev";
    const HMG = "hmg";
    const PRD = "prd";
    const TEST = "test";
    const TEST1 = "test1";
    const TEST2 = "test2";
    const TEST3 = "test3";
    const HMG1 = "hmg1";


    /*
     * |--------------------------------------------------------------------------
     * | MODULOS
     * |--------------------------------------------------------------------------
     */
    const MODULO_ELEITORAL = '3';

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Auth
     * |--------------------------------------------------------------------------
     */
    const PARAM_BEARER = "Bearer";
    const PARAM_AUTHORIZATION = "Authorization";

    const PERMISSAO_ACESSOR_CEN = '01602009';
    const PERMISSAO_ACESSOR_CE_UF = '01602010';
    const PUBLICAR_COMISSAO_ELEITORAL = '01601023';
    const PERMISSAO_PARAMETRIZAR_CALENDARIO = '1602011';

    const ROLE_ACESSOR_CE_UF = 'ROLE_ACESSOR_CE_UF';
    const ROLE_ACESSOR_CEN = 'ROLE_ACESSOR_CEN';
    const ROLE_PROFISSIONAL = 'ROLE_PROFISSIONAL';
    const ROLE_PARAMETRIZAR_CALENDARIO = 'ROLE_PARAMETRIZAR_CALENDARIO';
    const ROLE_PUBLICAR_COMISSAO_ELEITORAL = 'ROLE_PUBLICAR_COMISSAO_ELEITORAL';

    const EXTENSAO_ARQUIVO_BANDEIRAS = 'jpg';

    const SIGLA_CAU_BR = "CAU/BR";
    const SIGLA_BRASIL = "BR";

    const ID_CAU_BR = 165;
    const ID_CAUBR_IES = 1000;

    const SITUACAO_PROFISSIONAL_ATIVO = 8;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Numéricas
     * |--------------------------------------------------------------------------
     */
    const VALOR_ZERO = 0;

    /*
     * |--------------------------------------------------------------------------
     * | Tipo filiais
     * |--------------------------------------------------------------------------
     */
    const TIPO_SEDE = 7;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Arquivo
     * |--------------------------------------------------------------------------
     */
    const TAMANHO_LIMITE_ARQUIVO = 10485760;

    const TAMANHO_LIMITE_ARQUIVO_2MB = 2097152;

    const EXTENCAO_PDF = "pdf";

    const EXTENCAO_PNG = 'png';

    const EXTENCAO_JPG = 'jpg';

    const EXTENCAO_JPEG = 'jpeg';

    const EXTENCAO_DOC = "doc";

    const EXTENCAO_DOCX = "docx";

    const EXTENCAO_BMP = 'bmp';

    const QUANTIDADE_MAX_ARQUIVO_RESOLUCAO = 2;

    const QUANTIDADE_RECURSO_RECONSIDERACAO = 2;

    const PATH_STORAGE_ARQUIVO_RESOLUCAO = "eleitoral/resolucao";

    const PATH_STORAGE_ARQUIVO_ELEICAO = "eleitoral/eleicao";

    const PATH_STORAGE_ARQUIVO_REPRESENTATIVIDADE = "eleitoral/representatividade";

    const PATH_STORAGE_ARQUIVO_DENUNCIA = "eleitoral/denuncia";

    const PATH_STORAGE_ARQUIVO_ASSINATURA = "eleitoral/assinatura";

    const PATH_STORAGE_ARQUIVO_JULGAMENTO_ADMISSIBILIDADE = "eleitoral/julgamento_admissibilidade";

    const PATH_STORAGE_ARQUIVO_RECURSO_JULGAMENTO_ADMISSIBILIDADE = "eleitoral/recurso_julgamento_admissibilidade";

    const PATH_STORAGE_ARQUIVO_JULGAMENTO_RECURSO_ADMISSIBILIDADE = "eleitoral/julgamento_recurso_admissibilidade";

    const PATH_STORAGE_ARQUIVO_DENUNCIA_DEFESA = "eleitoral/denuncia_defesa";

    const PATH_STORAGE_ARQUIVO_DENUNCIA_PROVAS = "eleitoral/denuncia_provas";

    const PATH_STORAGE_ARQUIVO_RECURSO_CONTRARRAZAO_DENUNCIA = "eleitoral/recurso_denuncia";

    const PATH_STORAGE_ARQUIVO_DENUNCIA_AUDIENCIA_INSTRUCAO = "eleitoral/denuncia_audiencia_instrucao";

    const PATH_STORAGE_ARQUIVO_EMAIL = "eleitoral/email";

    const PATH_STORAGE_ARQUIVO_EXTRATO_CHAPA = "eleitoral/extrato_chapa";

    const PATH_STORAGE_ARQUIVO_EXTRATO_PROFISSIONAIS = "eleitoral/extrato_profissionais";

    const PATH_STORAGE_BANDEIRAS_FILIAIS = "eleitoral/bandeiras";

    const PATH_STORAGE_ARQUIVO_DENUNCIA_ADMITIDA = "eleitoral/denuncia_admitida";

    const NAME_ARQUIVO_EXTRATO_CHAPA = "extratoChapa.json";

    const STATUS_EXTRATO_CHAPA_TODOS = 0;

    const STATUS_EXTRATO_CHAPA_COM_PENDENCIA = 1;

    const STATUS_EXTRATO_CHAPA_CONCLUIDA = 2;

    const PATH_DEFAULT_CABECALHO = 'images' . DIRECTORY_SEPARATOR . 'bannermailsiccau.jpg';

    const PATH_DEFAULT_RODAPE = 'images' . DIRECTORY_SEPARATOR . 'rodapemailsiccau.jpg';

    const PATH_M_USER = 'images' . DIRECTORY_SEPARATOR . 'm-user.jpg';

    const PATH_F_USER = 'images' . DIRECTORY_SEPARATOR . 'f-user.jpg';

    const PATH_STORAGE_SINTESE_CURRICULO = "eleitoral/sintese_curriculo";

    const PATH_STORAGE_DOCS_COMPROBATORIOS_SINTESE_CURRICULO = "eleitoral/documentos_comprobatorios";

    const REFIXO_DOC_RESOLUCAO = "doc_resolucao";

    const REFIXO_DOC_DENUNCIA = "doc_denuncia";

    const REFIXO_DOC_DENUNCIA_INADMITIDA = "doc_denuncia_inadmitida";

    const PREFIXO_DOC_DENUNCIA_ADMITIDA = 'doc_denuncia_admitida';

    const PATH_STORAGE_ARQ_DENUNCIA_ADMITIDA = 'eleitoral/denuncia_admitida';

    const PREFIXO_DOC_DENUNCIA_RECURSO = "doc_denuncia_recurso";

    const REFIXO_DOC_ELEICAO = "doc_eleicao";

    const PREFIXO_DOC_DENUNCIA_DEFESA = "doc_denuncia_defesa";

    const PREFIXO_DOC_ENCAMINHAMENTO_DENUNCIA = "doc_ecaminhamento";

    const PREFIXO_DOC_DENUNCIA_PROVAS = "doc_denuncia_provas";

    const PREFIXO_DOC_AUDIENCIA_INSTRUCAO = "doc_audiencia_instrucao";

    const NOME_ARQUIVO_CABECALHO = 'cabecalho';

    const NOME_ARQUIVO_RODAPE = 'rodape';

    const LARGURA_MAXIMA_IMAGE_CABECALHO = 610;

    const ALTURA_MAXIMA_IMAGEM_CABECALHO = 600;

    public static $extensoesImagem = [
        self::EXTENCAO_JPEG,
        self::EXTENCAO_JPG,
        self::EXTENCAO_PNG
    ];

    public static $extensoesFotoSinteseCurriculo = [
        self::EXTENCAO_JPEG,
        self::EXTENCAO_JPG,
        self::EXTENCAO_PNG,
        self::EXTENCAO_BMP
    ];

    const TAMANHO_LIMITE_ARQUIVO_DECLARACAO = 41943040;
    const QUANTIDADE_MAX_ARQUIVO_RESOLUCAO_DECLARACAO = 10;
    const REFIXO_DOC_DECLARACAO = "doc_declaracao";
    const QUANTIDADE_MAX_ARQUIVO_DECLARACAO_CHAPA = 2;
    const TAMANHO_LIMITE_ARQUIVO_DECLARACAO_CHAPA = 10485760;

    const QTD_MAXIMA_ARQUIVOS_PEDIDO_IMPUGNACAO = 5;

    const TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB = 1;
    const TP_VALIDACAO_ARQUIVO_FORMATOS_DIVERSOS_MAIXIMO_25MB = 2;
    const TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB = 3;

    public static $configValidacaoArquivo = [
        self::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_10MB => [
            'tamanho_limite' => 10485760,
            'extensoes_aceitas' => ['pdf'],
            'msg_tamanho_limite' => 'MSG-053',
            'msg_extensoes_aceitas' => 'MSG-008',
        ],
        self::TP_VALIDACAO_ARQUIVO_PDF_MAIXIMO_15MB => [
            'tamanho_limite' => 15728640,
            'extensoes_aceitas' => ['pdf'],
            'msg_tamanho_limite' => 'MSG-132',
            'msg_extensoes_aceitas' => 'MSG-008',
        ],
        self::TP_VALIDACAO_ARQUIVO_FORMATOS_DIVERSOS_MAIXIMO_25MB => [
            'tamanho_limite' => 26214400,
            'extensoes_aceitas' => [
                'pdf', 'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'mp4', 'avi', 'wmv', 'mp3', 'wav', 'jpg', 'jpeg', 'png'
            ],
            'msg_tamanho_limite' => 'MSG-098',
            'msg_extensoes_aceitas' => 'MSG-098',
        ]
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Limites de anos do Calendário
     * |--------------------------------------------------------------------------
     */
    const LIMITE_MIN_ANO = 2012;
    const LIMITE_MAX_ANO = 3000;

    /*
     * |--------------------------------------------------------------------------
     * | Situações do Calendario
     * |--------------------------------------------------------------------------
     */
    const SITUACAO_CALENDARIO_EM_PREENCHIMENTO = 1;
    const SITUACAO_CALENDARIO_CONCLUIDO = 2;
    const SITUACAO_CALENDARIO_INATIVADO = 3;

    /*
     * |--------------------------------------------------------------------------
     * | Situações da Eleição
     * |--------------------------------------------------------------------------
     */
    const SITUACAO_ELEICAO_EM_ANDAMENTO = 1;
    const SITUACAO_ELEICAO_INATIVADA = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Membros da Comissão
     * |--------------------------------------------------------------------------
     */
    const SITUACAO_MEMBRO_ACONFIRMAR = 1;
    const SITUACAO_MEMBRO_CONFIRMADO = 2;
    const SITUACAO_MEMBRO_REJEITADO = 3;

    /*
     * |--------------------------------------------------------------------------
     * | Situação Denuncia.
     * |--------------------------------------------------------------------------
     */
    const SITUACAO_DENUNCIA_ANALISE_ADMISSIBILIDADE = 1;
    const SITUACAO_DENUNCIA_EM_JULGAMENTO = 2;
    const SITUACAO_DENUNCIA_EM_RELATORIA = 3;
    const SITUACAO_DENUNCIA_AGUARDANDO_RELATOR = 4;
    const SITUACAO_DENUNCIA_EM_RECURSO = 5;
    const SITUACAO_DENUNCIA_TRANSITO_JULGADO = 9;
    const SITUACAO_JULGAMENTEO_RECURSO_ADMISSIBILIDADE = 11;
    const  ACAO_RECURSO_JULGAMENTO_ADMISSIBILIDADE = 'Recurso de admissibilidade';

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Julgamento.
     * |--------------------------------------------------------------------------
     */
    const TIPO_JULGAMENTO_ADMISSIBILIDADE_PROVIMENTO = 1;
    const TIPO_JULGAMENTO_ADMISSIBILIDADE_IMPROVIMENTO = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Atividade Secundária
     * |--------------------------------------------------------------------------
     */
    const STATUS_ATIVIDADE_REGRAS_NAO_DEFINIDAS = 0;
    const STATUS_ATIVIDADE_AGUARDANDO_PARAMETRIZACAO = 1;
    const STATUS_ATIVIDADE_AGUARDANDO_DOCUMENTO = 2;
    const STATUS_ATIVIDADE_PARAMETRIZACAO_CONCLUIDA = 3;
    const STATUS_ATIVIDADE_AGUARDANDO_ATUALIZACAO = 4;
    const STATUS_ATIVIDADE_ATUALIZACAO_CONCLUIDA = 5;

    /*
    * |--------------------------------------------------------------------------
    * | Idade de Calendario
    * |--------------------------------------------------------------------------
    */
    const LIMITE_MIN_IDADE = 0;
    const LIMITE_MAX_IDADE = 999;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Histórico Calendário
     * |--------------------------------------------------------------------------
     */
    const ACAO_CALENDARIO_INSERIR = 1;
    const ACAO_CALENDARIO_ALTERAR = 2;
    const ACAO_CALENDARIO_EXCLUIR = 3;
    const ACAO_CALENDARIO_INATIVAR = 4;
    const ACAO_CALENDARIO_EXCLUIR_ATV_PRINCIPAL = 5;
    const ACAO_CALENDARIO_EXCLUIR_ATV_SECUNDARIA = 6;
    const ACAO_CALENDARIO_INSERIR_PRAZO = 7;
    const ACAO_CALENDARIO_ALTERAR_PRAZO = 8;
    const ACAO_CALENDARIO_EXCLUIR_PRAZO = 9;
    const ACAO_CALENDARIO_CONCLUIR = 10;
    const ACAO_CALENDARIO_INSERIR_ATV_PRINCIPAL = 11;

    const DESC_ABA_PERIODO = 'Período';
    const DESC_ABA_PRAZO = 'Prazo';
    const DESC_ABA_PERIODO_PRAZO = 'Período/Prazo';

    const ID_USUARIO_MOCK = 1134;

    /**
     * Define as descrições das ações do histórico de calendário.
     *
     * @var array
     */
    public static $acoesHistoricoCalendario = [
        self::ACAO_CALENDARIO_INSERIR => 'Inclusão do Cadastro',
        self::ACAO_CALENDARIO_ALTERAR => 'Alteração das Informações Iniciais',
        self::ACAO_CALENDARIO_INSERIR_PRAZO => 'Prazos Definidos',
        self::ACAO_CALENDARIO_CONCLUIR => 'Calendário Concluído',
        self::ACAO_CALENDARIO_INSERIR_ATV_PRINCIPAL => 'Atividades Definidas',
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Processo Calendário
     * |--------------------------------------------------------------------------
     */
    const TIPO_PROCESSO_ORDINARIO = 1;
    const TIPO_PROCESSO_EXTRAORDINARIO = 2;

    const TIPO_VALIDACAO_USARIO_CADASTRO = 1;
    const TIPO_VALIDACAO_CHAPA_CADASTRO = 2;
    const TIPO_VALIDACAO_CADASTRO = 3;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Participacao Membro
     * |--------------------------------------------------------------------------
     */
    const TIPO_PARTICIPACAO_COORDENADOR = 1;
    const TIPO_PARTICIPACAO_COORDENADOR_SUBSTITUTO = 2;
    const TIPO_PARTICIPACAO_COORDENADOR_ADJUNTO = 3;
    const TIPO_PARTICIPACAO_MEMBRO = 4;
    const TIPO_PARTICIPACAO_SUBSTITUTO = 5;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Historico Informação Comissão Membro
     * |--------------------------------------------------------------------------
     */
    const ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR = 1;
    const ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR = 2;
    const ACAO_HISTORICO_INFORMACAO_COMISSAO_CONCLUIR = 3;
    const ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR_MEMBROS = 4;
    const ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR_MEMBROS = 5;

    /**
     * Define as descrições das ações do histórico de Informação de Comissão.
     *
     * @var array
     */
    public static $acoesHistoricoInformacaoComissaoMembro = [
        self::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR => 'Inclusão de informação de comissão de membros',
        self::ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR => 'Alteração de informação de comissão de membros',
        self::ACAO_HISTORICO_INFORMACAO_COMISSAO_CONCLUIR => 'Conclusão de informação de comissão de membros',
        self::ACAO_HISTORICO_INFORMACAO_COMISSAO_INSERIR_MEMBROS => 'Inclusão de membros',
        self::ACAO_HISTORICO_INFORMACAO_COMISSAO_ALTERAR_MEMBROS => 'Alteração de membros'
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Historico Alteração Informação Comissão Membro
     * |--------------------------------------------------------------------------
     */
    const INCLUSAO_CAMPO_MAJORIETARIO = 'Inclusão dos dados do campo Majorietário';
    const INCLUSAO_CAMPO_COORDENADOR_ADJUNTO = 'Inclusão dos dados do campo Coordenador Adjunto';
    const INCLUSAO_CAMPO_QUANTIDADE_MINIMA_MEMBROS = 'Inclusão dos dados do campo Qnt Mínima Membros';
    const INCLUSAO_CAMPO_QUANTIDADE_MAXIMA_MEMBROS = 'Inclusão dos dados do campo Qnt Máxima Membros';
    const INCLUSAO_CAMPO_PARAMETRIZAR_CABECALHO = 'Inclusão dos dados do campo Parametrizar Cabeçalho';
    const INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_INICIAL = 'Inclusão dos dados do campo Parametrizar Texto Inicial';
    const INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_FINAL = 'Inclusão dos dados do campo Parametrizar Texto Final';
    const INCLUSAO_CAMPO_PARAMETRIZAR_TEXTO_RODAPE = 'Inclusão dos dados do campo Parametrizar Rodapé';
    const INCLUSAO_CAMPO_ATIVAR_CABECALHO = 'Inclusão dos dados do campo Ativar Cabeçalho';
    const INCLUSAO_CAMPO_ATIVAR_TEXTO_INICIAL = 'Inclusão dos dados do campo Ativar Texto Inicial';
    const INCLUSAO_CAMPO_ATIVAR_TEXTO_FINAL = 'Inclusão dos dados do campo Ativar Texto Final';
    const INCLUSAO_CAMPO_ATIVAR_TEXTO_RODAPE = 'Inclusão dos dados do campo Ativar Rodapé';

    const ALTERACAO_CAMPO_MAJORIETARIO = 'Alteração dos dados do campo Majorietário';
    const ALTERACAO_CAMPO_COORDENADOR_ADJUNTO = 'Alteração dos dados do campo Coordenador Adjunto';
    const ALTERACAO_CAMPO_QUANTIDADE_MINIMA_MEMBROS = 'Alteração dos dados do campo Qnt Mínima Membros';
    const ALTERACAO_CAMPO_QUANTIDADE_MAXIMA_MEMBROS = 'Alteração dos dados do campo Qnt Máxima Membros';
    const ALTERACAO_CAMPO_PARAMETRIZAR_CABECALHO = 'Alteração dos dados do campo Parametrizar Cabeçalho';
    const ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_INICIAL = 'Alteração dos dados do campo Parametrizar Texto Inicial';
    const ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_FINAL = 'Alteração dos dados do campo Parametrizar Texto Final';
    const ALTERACAO_CAMPO_PARAMETRIZAR_TEXTO_RODAPE = 'Alteração dos dados do campo Parametrizar Rodapé';
    const ALTERACAO_CAMPO_ATIVAR_CABECALHO = 'Alteração dos dados do campo Ativar Cabeçalho';
    const ALTERACAO_CAMPO_ATIVAR_TEXTO_INICIAL = 'Alteração dos dados do campo Ativar Texto Inicial';
    const ALTERACAO_CAMPO_ATIVAR_TEXTO_FINAL = 'Alteração dos dados do campo Ativar Texto Final';
    const ALTERACAO_CAMPO_ATIVAR_TEXTO_RODAPE = 'Alteração dos dados do campo Ativar Rodapé';

    /*
     * |--------------------------------------------------------------------------
     * | Número Membros da Comissão.
     * |--------------------------------------------------------------------------
     */
    const PREFIXO_CAU_BR = 'CAU/BR';
    const COMISSAO_MEMBRO_CAU_BR_ID = 165;
    const PREFIXO_CONSELHO_ELEITORAL = 'CE';
    const PREFIXO_CONSELHO_ELEITORAL_NACIONAL = 'CEN';
    const DESCRICAO_CAU_BR = 'CONSELHO DE ARQUITETURA E URBANISMO DO BRASIL';
    const IES_ID = 1000;

    /*
     * |--------------------------------------------------------------------------
     * | Cabeçalho do E-mail.
     * |--------------------------------------------------------------------------
     */
    const CABECALHO_EMAIL_DIRETORIO_UPLOAD_IMAGENS = 'cabecalho_email';

    /*
     * |--------------------------------------------------------------------------
     * | Emails
     * |--------------------------------------------------------------------------
     */
    const PARAMETRO_EMAIL_CORPO = "{{corpo}}";
    const PARAMETRO_EMAIL_RODAPE = "{{rodape}}";
    const PATH_RESOURCES_EMAILS = "resources/emails";
    const PARAMETRO_EMAIL_CABECALHO = "{{cabecalho}}";
    const PARAMETRO_EMAIL_IMG_RODAPE = "{{imagemRodape}}";
    const PARAMETRO_EMAIL_IMG_CABECALHO = "{{imagemCabecalho}}";

    const PARAMETRO_EMAIL_ANO_ELEICAO = "{{anoEleicao}}";
    const PARAMETRO_EMAIL_NM_MEMBRO = "{{nomeMembro}}";
    const PARAMETRO_EMAIL_NM_RESPONSAVEL = "{{nomeResponsavel}}";
    const PARAMETRO_EMAIL_POSICAO_MEMBRO = "{{posicao}}";
    const PARAMETRO_EMAIL_DS_TITULAR = "{{descricaoTitular}}";
    const PARAMETRO_EMAIL_DS_SUPLENTE = "{{descricaoSuplente}}";
    const PARAMETRO_EMAIL_NM_TITULAR = "{{nomeTitular}}";
    const PARAMETRO_EMAIL_NM_SUPLENTE = "{{nomeSuplente}}";

    const PARAMETRO_EMAIL_NUMERO_SEQUENCIAL = "{{numeroSequencia}}";
    const PARAMETRO_EMAIL_PROCESSO_ELEITORAL = "{{anoEleicao}}";
    const PARAMETRO_EMAIL_TIPO_DENUNCIA = "{{tipoDecuncia}}";
    const PARAMETRO_EMAIL_NOME_DENUNCIADO = "{{nomeDenunciado}}";
    const PARAMETRO_EMAIL_NUMERO_CHAPA = "{{numeroChapa}}";
    const PARAMETRO_EMAIL_UF = "{{uf}}";
    const PARAMETRO_EMAIL_DESC_FATOR = "{{descFatos}}";
    const PARAMETRO_EMAIL_TESTEMUNHAS = "{{testemunhas}}";
    const PARAMETRO_EMAIL_STATUS = "{{status}}";
    const PARAMETRO_EMAIL_TIPO_ENCAMINHAMENTO = "{{tipoEncaminhamento}}";
    const PARAMETRO_EMAIL_DESCRICAO_ENCAMINHAMENTO = "{{descricaoEncaminhamento}}";
    const PARAMETRO_EMAIL_AGENDAMENTO_ENCAMINHAMENTO = "{{agendamentoEncaminhamento}}";

    const PARAMETRO_EMAIL_ACEITE_CONVITE = "{{pendenciaAceiteConvite}}";
    const PARAMETRO_EMAIL_PENDENCIA_DECORRER_PROCESSO = "{{pendenciaDecorrerProcessoEleitoral}}";

    const DS_PARAMETRO_EMAIL_POSICAO_MEMBRO = "Posição nº %s";

    const PARAMETRO_EMAIL_NM_PROTOCOLO = "{{numeroProtocolo}}";
    const PARAMETRO_EMAIL_NM_SUBSTITUIDO_TITULAR = "{{nomeSubstituidoTitular}}";
    const PARAMETRO_EMAIL_NM_SUBSTITUIDO_SUPLENTE = "{{nomeSubstituidoSuplente}}";
    const PARAMETRO_EMAIL_NM_SUBSTITUTO_TITULAR = "{{nomeSubstitutoTitular}}";
    const PARAMETRO_EMAIL_NM_SUBSTITUTO_SUPLENTE = "{{nomeSubstitutoSuplente}}";

    const PARAMETRO_EMAIL_DS_ELEICAO = "{{descricaoEleicao}}";
    const PARAMETRO_EMAIL_NOME_CANDIDATO = "{{nomeCandidato}}";
    const PARAMETRO_EMAIL_NUM_CHAPA = "{{numeroChapa}}";
    const PARAMETRO_EMAIL_PREFIXO_UF = "{{prefixoUf}}";
    const PARAMETRO_EMAIL_JUSTIFICATIVA = "{{justificativa}}";

    const PARAMETRO_EMAIL_JULGAMENTO_PARECER = "{{parecer}}";
    const PARAMETRO_EMAIL_JULGAMENTO_DESICAO = "{{decisao}}";

    const PARAMETRO_EMAIL_DS_DEFESA = "{{descricaoDefesa}}";

    const PARAMETRO_EMAIL_DS_PROVAS = "{{descricaoProvasApresentadas}}";
    const PARAMETRO_EMAIL_ENCAMINHAMENTO = "{{encaminhamento}}";
    const PARAMETRO_EMAIL_DESCRICAO = "{{descricao}}";
    const PARAMETRO_EMAIL_DATA_HORA_AUDIENCIA_INSTRUCAO = "{{dataHoraAudienciaInstrucao}}";

    const PARAMETRO_EMAIL_LABEL_INTERPOR_RECURSO = "{{labelInterporRecurso}}";
    const PARAMETRO_EMAIL_DS_INTERPOR_RECURSO = "{{descricaoInterporRecurso}}";

    const PARAMETRO_EMAIL_LABEL_RECURSO = "{{labelRecurso}}";
    const PARAMETRO_EMAIL_DS_RECURSO = "{{descricaoRecurso}}";

    public static $paramsEmailPendenciasMembroChapa = [
        self::PENDENCIA_REGISTRO_INADIPLENTE => "{{pendenciaInadiplente}}",
        self::PENDENCIA_REGISTRO_NAO_ATIVO => "{{pendenciaRegistroInativo}}",
        self::PENDENCIA_REGISTRO_PROVISORIO => "{{pendenciaRegistroProvisorio}}",
        self::PENDENCIA_REGISTRO_DATA_VALIDADE_EXPIRADA => "{{pendenciaRegistroExpirado}}",
        self::PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL => "{{pendenciaDecorrerProcessoEleitoral}}",
        self::PENDENCIA_MULTA_FISCALIZACAO => "{{pendenciaMultaFiscalizacao}}",
        self::PENDENCIA_MULTA_ETICA => "{{pendenciaMultaEtica}}",
        self::PENDENCIA_SANCAO_ETICO_DISCIPLINAR => "{{pendenciaSancaoEticoDisciplinar}}",
        self::PENDENCIA_INFRACAO_ETICO_DISCIPLINAR => "{{pendenciaInfracaoEticoDisciplinar}}",
        self::PENDENCIA_MULTA_ELEITORAL => "{{pendenciaMultaEleitoral}}",
        self::PENDENCIA_CONSELHEIRO_MANDATO_SUBSEQUENTE => "{{pendenciaConselheiroMandatoSubsequente}}",
        self::PENDENCIA_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS => "{{pendenciaPerdeuMandatoUltimosCincoAnos}}",
    ];

    const TEMPLATE_EMAIL_PADRAO = "emailPadrao.html";
    const TEMPLATE_EMAIL_PENDENCIAS_MEMBRO_CHAPA = "emailPendenciasMembroChapa.html";
    const TEMPLATE_EMAIL_MEMBRO_INCLUIDO_CHAPA = "emailInformativoMembroIncluidoChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_MEMBRO_CHAPA = "emailDenunciaMembroChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_MEMBRO_COMISSAO = "emailDenunciaMembroComissao.html";
    const TEMPLATE_EMAIL_DENUNCIA_CHAPA = "emailDenunciaChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_OUTROS = "emailDenunciaOutros.html";

    const TEMPLATE_EMAIL_PEDIDO_SUBSTITUICAO_CHAPA = "emailCadastroPedidoSubstituicaoChapa.html";
    const TEMPLATE_EMAIL_PEDIDO_IMPUGNACAO = "emailCadastroPedidoImpugnacao.html";
    const TEMPLATE_EMAIL_DEFESA_IMPUGNACAO = "emailCadastroPedidoImpugnacao.html";
    const TEMPLATE_EMAIL_JULGAMENTO_SUBSTITUICAO = "emailJulgamentoSubstituicao.html";
    const TEMPLATE_EMAIL_JULGAMENTO_IMPUGNACAO = "emailJulgamentoImpugnacao.html";
    const TEMPLATE_EMAIL_RECURSO_JULGAMENTO_SUBST = "emailRecursoJulgamentoSubstituicao.html";
    const TEMPLATE_EMAIL_RECURSO_JULGAMENTO_IMPUGNACAO = "emailRecursoJulgamentoImpugnacao.html";

    const TEMPLATE_EMAIL_ENCAMINHAMENTO_CHAPA = "emailEncaminhamentoChapa.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_OUTROS = "emailEncaminhamentoOutros.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_CHAPA = "emailEncaminhamentoMembroChapa.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_COMISSAO = "emailEncaminhamentoMembroComissao.html";

    const TEMPLATE_EMAIL_ENCAMINHAMENTO_CHAPA_AUDIENCIA_INSTRUCAO = "emailEncaminhamentoChapaAudienciaInstrucao.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_OUTROS_AUDIENCIA_INSTRUCAO = "emailEncaminhamentoOutrosAudienciaInstrucao.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_CHAPA_AUDIENCIA_INSTRUCAO = "emailEncaminhamentoMembroChapaAudienciaInstrucao.html";
    const TEMPLATE_EMAIL_ENCAMINHAMENTO_MEMBRO_COMISSAO_AUDIENCIA_INSTRUCAO = "emailEncaminhamentoMembroComissaoAudienciaInstrucao.html";

    const TEMPLATE_EMAIL_DENUNCIA_DEFESA_CHAPA = "emailDenunciaDefesaChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_DEFESA_MEMBRO_CHAPA = "emailDenunciaDefesaMembroChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_DEFESA_MEMBRO_COMISSAO = "emailDenunciaDefesaMembroComissao.html";

    const TEMPLATE_EMAIL_DENUNCIA_PROVAS_CHAPA = "emailDenunciaProvasChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_PROVAS_OUTROS = "emailDenunciaProvasOutros.html";
    const TEMPLATE_EMAIL_DENUNCIA_PROVAS_MEMBRO_CHAPA = "emailDenunciaProvasMembroChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_PROVAS_MEMBRO_COMISSAO = "emailDenunciaProvasMembroComissao.html";

    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_CHAPA = "emailDenunciaAudienciaInstrucaoChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_OUTROS = "emailDenunciaAudienciaInstrucaoOutros.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_MEMBRO_CHAPA = "emailDenunciaAudienciaInstrucaoMembroChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_MEMBRO_COMISSAO = "emailDenunciaAudienciaInstrucaoMembroComissao.html";

    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_CHAPA = "emailDenunciaAudienciaInstrucaoPendenteChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_OUTROS = "emailDenunciaAudienciaInstrucaoPendenteOutros.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_MEMBRO_CHAPA = "emailDenunciaAudienciaInstrucaoPendenteMembroChapa.html";
    const TEMPLATE_EMAIL_DENUNCIA_AUDIENCIA_INSTRUCAO_PENDENTE_MEMBRO_COMISSAO = "emailDenunciaAudienciaInstrucaoPendenteMembroComissao.html";


    const QUEBRA_LINHA_TEMPLATE_EMAIL = '<br />';

    /*
     * |--------------------------------------------------------------------------
     * | Incisos da Lei 12378, art 32
     * |--------------------------------------------------------------------------
     */
    const INCISO_I = 1;
    const INCISO_II = 2;
    const INCISO_III = 3;
    const INCISO_IV = 4;

    public static $incisosLei = [
        self::INCISO_I => 'I - até 499',
        self::INCISO_II => 'II - de 500 a 1.000',
        self::INCISO_III => 'III - de 1.001 a 3.000',
        self::INCISO_IV => 'IV - acima de 3.000'
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Historico Genérico
     * |--------------------------------------------------------------------------
     */
    const PARAM_CONSELHEIRO_DESC_ALT_REALIZADA = 'Alteração Realizada';
    const PARAM_CONSELHEIRO_DESC_ATUALIZACAO_REALIZADA = 'Atualização Realizada';
    const PARAM_CONSELHEIRO_DESC_EXTRATO_GERADO = 'Extrato Gerado';
    const PARAM_CONSELHEIRO_NOME_RESP_SISTEMA = 'Sistema Eleitoral';

    const HISTORICO_TIPO_REFERENCIA_PARAMETRO_CONSELHEIRO = 1;
    const HISTORICO_ID_REFERENCIA_PARAMETRO_CONSELHEIRO = 1;

    const HISTORICO_ACAO_INSERIR = 1;
    const HISTORICO_ACAO_ALTERAR = 2;
    const HISTORICO_ACAO_EXCLUIR = 3;

    const HISTORICO_DESCRICAO_ACAO_INSERIR = 'Inclusão Realizada';
    const HISTORICO_DESCRICAO_ACAO_ALTERAR = 'Alteração Realizada';
    const HISTORICO_DESCRICAO_ACAO_EXCLUIR = 'Exclusão Realizada';

    const HISTORICO_ID_TIPO_CABECALHO_EMAIL = 2;
    const HISTORICO_ID_TIPO_CORPO_EMAIL = 3;

    const HISTORICO_ID_TIPO_PARAMET_DECLARACOES_EMAILS = 4;

    const HISTORICO_ID_TIPO_JULGAMENTO_SUBSTITUICAO = 5;

    const HISTORICO_ID_TIPO_JULGAMENTO_IMPUGNACAO = 6;

    const HISTORICO_ID_TIPO_JUGAMENTO_RECURSO_SUBST = 7;

    const HISTORICO_ID_TIPO_JUGAMENTO_RECURSO_IMPUGNACAO = 8;

    const HISTORICO_ID_TIPO_JUGAMENTO_FINAL = 9;

    const HISTORICO_ID_TIPO_JUGAMENTO_SEGUNDA_INSTANCIA = 10;

    const HISTORICO_ID_TIPO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = 9;

    const HISTORICO_ALEGACAO_IMPUGNACAO_RESULTADO = 11;

    const HISTORICO_ID_TIPO_JULG_RECURSOS_IMPUGNACAO_RESULTADO = 12;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Historico profissional Genérico
     * |--------------------------------------------------------------------------
     */

    const HISTORICO_PROF_ACAO_INSERIR = 1;
    const HISTORICO_PROF_ACAO_ALTERAR = 2;
    const HISTORICO_PROF_ACAO_EXCLUIR = 3;

    const HISTORICO_PROF_DESCRICAO_ACAO_INSERIR = 'Inclusão Realizada';
    const HISTORICO_PROF_DESCRICAO_ACAO_ALTERAR = 'Alteração Realizada';
    const HISTORICO_PROF_DESCRICAO_ACAO_EXCLUIR = 'Exclusão Realizada';

    const HISTORICO_PROF_TIPO_PEDIDO_SUBSTITUICAO = 1;

    const HISTORICO_PROF_TIPO_PEDIDO_IMPUGNACAO = 2;

    const HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_SUBSTITUICAO = 3;

    const HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_IMPUGNACAO = 4;

    const HISTORICO_PROF_TIPO_SUBSTITUICAO_IMPUGNACAO = 5;

    const HISTORICO_PROF_TIPO_RECURSO_JUGAMENTO_FINAL = 6;

    const HISTORICO_PROF_TIPO_SUBSTITUICAO_JUGAMENTO_FINAL = 7;

    const HISTORICO_PROF_TIPO_RECURSO_SEGUNDO_JUGAMENTO_SUBSTITUICAO = 9;

    const HISTORICO_PROF_TIPO_CONTRARRAZAO_IMPUGNACAO_RESULTADO = 10;

    const HISTORICO_PROF_TIPO_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO = 11;

    /**
     * |--------------------------------------------------------------------------
     * | Publicação Documento Comissão Membro
     * |--------------------------------------------------------------------------
     */
    const PREFIXO_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO = 'ComissaoEleitoral_';

    const PATH_STORAGE_DOCUMENTO_PUBLICACAO_COMISSAO_MEMBRO = "eleitoral/publicacao_comissao_membro";

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Chapa
     * |--------------------------------------------------------------------------
     */
    const ORIGEM_PROFISSIONAL = 'Profissional';
    const ORIGEM_CORPORATIVO = 'Corporativo';
    const PREFIXO_IES = 'IES';
    const MEMBRO_CHAPA_RESPONSAVEL = 1;
    const QUANTIDADE_DIAS_LIMITE_ALERTA_CONVITES_A_CONFIRMAR = 5;

    const ETAPA_PLATAF_ELEITORAL_REDES_SOCIAIS_INCLUIDA = 1;
    const ETAPA_MEMBROS_CHAPA_INCLUIDA = 2;
    const ETAPA_CRIACAO_CHAPA_CONFIRMADA = 3;

    const SITUACAO_CHAPA_PENDENTE = 1;
    const SITUACAO_CHAPA_CONCLUIDA = 2;
    const DS_SITUACAO_CHAPA_PENDENTE = 'Com Pendência';
    const DS_SITUACAO_CHAPA_CONCLUIDA = 'Concluída';

    public static $statusChapa = [
        self::SITUACAO_CHAPA_PENDENTE => self::DS_SITUACAO_CHAPA_PENDENTE,
        self::SITUACAO_CHAPA_CONCLUIDA => self::DS_SITUACAO_CHAPA_CONCLUIDA,
    ];

    const STATUS_CHAPA_JULG_FINAL_AGUARDANDO = 1;
    const STATUS_CHAPA_JULG_FINAL_DEFERIDO = 2;
    const STATUS_CHAPA_JULG_FINAL_INDEFERIDO = 3;

    const STATUS_PARTICIPACAO_MEMBRO_ACONFIRMAR = 1;
    const STATUS_PARTICIPACAO_MEMBRO_CONFIRMADO = 2;
    const STATUS_PARTICIPACAO_MEMBRO_REJEITADO = 3;

    const ST_MEMBRO_CHAPA_CADASTRADO = 1;
    const ST_MEMBRO_CHAPA_SUBSTITUIDO = 2;
    const ST_MEMBRO_CHAPA_SUBSTITUTO_ANDAMENTO = 3;
    const ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO = 4;
    const ST_MEMBRO_CHAPA_SUBSTITUTO_INDEFERIDO = 5;

    public static $situacaoMembroAtual = [
        self::ST_MEMBRO_CHAPA_CADASTRADO,
        self::ST_MEMBRO_CHAPA_SUBSTITUTO_DEFERIDO,
    ];

    const STATUS_VALIDACAO_MEMBRO_PENDENTE = 1;
    const STATUS_VALIDACAO_MEMBRO_SEM_PENDENCIA = 2;

    const TIPO_CANDIDATURA_CONSELHEIROS_UF_BR = 1;
    const TIPO_CANDIDATURA_IES = 2;

    const TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL = 1;
    const TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL = 2;
    const TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES = 3;

    const DS_REPRESENTACAO_FEDERAL = 'Federal';
    const DS_REPRESENTACAO_ESTADUAL = 'Estadual';

    public static $representacaoConselheiro = [
        self::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES => self::DS_REPRESENTACAO_FEDERAL,
        self::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL => self::DS_REPRESENTACAO_FEDERAL,
        self::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL => self::DS_REPRESENTACAO_ESTADUAL,
    ];

    const TIPO_PARTICIPACAO_CHAPA_TITULAR = 1;
    const TIPO_PARTICIPACAO_CHAPA_SUPLENTE = 2;

    public static $descricaoTipoParicipacao = [
        self::TIPO_PARTICIPACAO_CHAPA_TITULAR => 'Titular',
        self::TIPO_PARTICIPACAO_CHAPA_SUPLENTE => 'Suplente',
    ];

    const TIPO_DOCUMENTO_COMPROB_COMPROVANTE = 1;
    const TIPO_DOCUMENTO_COMPROB_CARTA_INDICACAO = 2;

    const TIPO_RESPOSTA_DECLARACAO_MULTIPLA = 1;
    const TIPO_RESPOSTA_DECLARACAO_UNICA = 2;

    const ABA_PLATAFORMA_ELEITORAL = "Plataforma Eleitoral";
    const ABA_MEMBROS_DA_CHAPA = "Membros da Chapa";

    const HISTORICO_INCLUSAO_DADOS_ABA = "Inclusão dos dados da aba %s";
    const HISTORICO_CONFIRMACAO_CRIACAO_CHAPA = "Confirmação da Criação da Chapa, com a Plataforma Eleitoral.";
    const HISTORICO_INCLUSAO_CPF_CHAPA = "Inclusão do CPF %s";
    const HISTORICO_SUBSTITUICAO_CPF_CHAPA = "Substituição do CPF %s para o CPF %s";
    const HISTORICO_INCLUSAO_RESPONSAVEL_CHAPA = "Inclusão do responsável de CPF %s";
    const HISTORICO_EXCLUSAO_RESPONSAVEL_CHAPA = "Exclusão do responsável de CPF %s";
    const HISTORICO_REJEICAO_PARTICIPACAO_CHAPA = "Rejeitar participação para chapa eleitoral";
    const HISTORICO_CONFIRMACAO_PARTICIPACAO_CHAPA = "Confirmar participação para chapa eleitoral";
    const HISTORICO_ALTERACAO_STATUS_CONFIRMACAO_CPF = "Alteração do Status de Confirmação do CPF %s";
    const HISTORICO_ALTERACAO_STATUS_VALIDACAO_CPF = "Alteração do Status de Validação do CPF %s";
    const HISTORICO_ALTERACAO_PLATAFORMA_ELEITORAL = "Alteração do texto da Plataforma Eleitoral";
    const HISTORICO_ENVIO_EMAIL_PENDENCIA_MEMBRO_CHAPA = "Envio de e-mail de pendência para o CPF %s";
    const HISTORICO_EXCLUSAO_MEMBRO_CHAPA = "Exclusão do membro de CPF %s";

    const PENDENCIA_REGISTRO_NAO_ATIVO = 1;
    const PENDENCIA_REGISTRO_DATA_VALIDADE_EXPIRADA = 2;
    const PENDENCIA_REGISTRO_PROVISORIO = 3;
    const PENDENCIA_REGISTRO_INADIPLENTE = 4;
    const PENDENCIA_MEMBRO_CHAPA_DECORRER_PROCESSO_ELEITORAL = 5;
    const PENDENCIA_MULTA_FISCALIZACAO = 6;
    const PENDENCIA_MULTA_ETICA = 7;
    const PENDENCIA_SANCAO_ETICO_DISCIPLINAR = 8;
    const PENDENCIA_INFRACAO_ETICO_DISCIPLINAR = 9;
    const PENDENCIA_MULTA_ELEITORAL = 10;
    const PENDENCIA_CONSELHEIRO_MANDATO_SUBSEQUENTE = 11;
    const PENDENCIA_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS = 12;

    const DS_PENDENCIA_ACEITE_CONVITE = 'No sistema Eleitoral Nacional – CAU, existe convite pendente de aceite.';

    const SITUACAO_REGISTRO_PROFISSIONAL_ATIVO = 8;

    const TITULO_PROFISSIONAL_ARQUITETO_URBANISTA = 15;

    const PATH_STORAGE_ARQ_RESP_DECLARACAO_CHAPA = "eleitoral/declaracoes_chapas";

    const PREFIXO_ARQ_RESPOSTA_DECLARACAO_CHAPA = "doc_declaracao_chapa";

    public static $descricoesTiposMembrosChapa = [
        self::TIPO_MEMBRO_CHAPA_CONSELHEIRO_FEDERAL => [
            self::TIPO_PARTICIPACAO_CHAPA_TITULAR => 'Conselheiro Titular Federal',
            self::TIPO_PARTICIPACAO_CHAPA_SUPLENTE => 'Conselheiro Suplente Federal'
        ],
        self::TIPO_MEMBRO_CHAPA_CONSELHEIRO_ESTADUAL => [
            self::TIPO_PARTICIPACAO_CHAPA_TITULAR => 'Conselheiro Titular Estadual',
            self::TIPO_PARTICIPACAO_CHAPA_SUPLENTE => 'Conselheiro Suplente Estadual'
        ],
        self::TIPO_MEMBRO_CHAPA_REPRESENTANTE_IES => [
            self::TIPO_PARTICIPACAO_CHAPA_TITULAR => 'Representante Titular IES',
            self::TIPO_PARTICIPACAO_CHAPA_SUPLENTE => 'Representante Suplente IES'
        ]
    ];

    const ID_STATUS_PEDIDO_CHAPA_EN_ANALISE = 1;
    const ID_STATUS_PEDIDO_CHAPA_DEFERIDO = 2;
    const ID_STATUS_PEDIDO_CHAPA_INDEFERIDO = 3;
    const ID_STATUS_PEDIDO_CHAPA_PROCEDENTE = 2;
    const ID_STATUS_PEDIDO_CHAPA_IMPROCEDENTE = 3;
    const DS_STATUS_PEDIDO_CHAPA_EN_ANALISE = 'Em análise';
    const DS_STATUS_PEDIDO_CHAPA_DEFERIDO = 'Deferido';
    const DS_STATUS_PEDIDO_CHAPA_INDEFERIDO = 'Indeferido';
    const DS_STATUS_PEDIDO_CHAPA_PROCEDENTE = 'Procedente';
    const DS_STATUS_PEDIDO_CHAPA_IMPROCEDENTE = 'Improcedente';

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Tipo Rede Social Chapa
     * |--------------------------------------------------------------------------
     */
    const TIPO_REDE_SOCIAL_FACEBOOK = 1;
    const TIPO_REDE_SOCIAL_INSTAGRAM = 2;
    const TIPO_REDE_SOCIAL_LINKEDIN = 3;
    const TIPO_REDE_SOCIAL_TWITTER = 4;
    const TIPO_REDE_SOCIAL_OUTROS = 5;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Parametrizacao E-mails e Declarações Chapa
     * |--------------------------------------------------------------------------
     */
    const HISTORICO_DS_INCLUSAO_EMAIL = "Inclusão do registro %s da Seleção E-Mail";
    const HISTORICO_DS_INCLUSAO_DECLARACAO = "Inclusão do registro %s da Seleção Declaração";
    const HISTORICO_DS_ALTERACAO_EMAIL = "Alteração do registro %s da Seleção E-mail";
    const HISTORICO_DS_ALTERACAO_DECLARACAO = "Alteração do registro %s da Seleção Declaração";
    const HISTORICO_DS_ALTERACAO_REGRA_ENVIO = "Alteração do registro %s da Regra de Envio";
    const HISTORICO_DS_ALTERACAO_REGRA_DECLARACAO = "Alteração do registro %s da Regra da Declaração";

    /*
     * |--------------------------------------------------------------------------
     * | Tipo alteração
     * |--------------------------------------------------------------------------
     */
    const TP_ALTERACAO_AUTOMATICO = 1;
    const TP_ALTERACAO_MANUAL = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Parametro Conselheiro
     * |--------------------------------------------------------------------------
     */
    const ID_IES_LISTA_CONSELHEIROS = 1000;
    const NU_PROP_IES_LISTA_CONSELHEIROS = 2;
    const DESC_IES_LISTA_CONSELHEIROS = 'IES';

    /*
     * |--------------------------------------------------------------------------
     * | Constantes configuração de definição de e-mails
     * |--------------------------------------------------------------------------
     */
    const EMAIL_INFORMACAO_MEMBRO = 1;
    const EMAIL_INCLUIR_MEMBRO = 2;
    const EMAIL_ALTERAR_MEMBRO = 3;
    const EMAIL_DEFINIR_NUMERO_MEMBROS = 4;

    const EMAIL_ACEITAR_COMISSAO = 5;
    const EMAIL_RECUSAR_COMISSAO = 6;
    const EMAIL_24H_ENCERRAMENTO = 7;

    const EMAIL_CHAPA_CRIADA_CENBR_CEUF = 8;
    const EMAIL_MEMBRO_INCLUIDO_CHAPA = 9;
    const EMAIL_MEMBRO_CHAPA_COM_PENDENCIA = 10;
    const EMAIL_CHAPA_CRIADA_RESPONSAVEIS = 11;
    const EMAIL_CHAPA_NAO_CONFIRMADA = 12;
    const EMAIL_CHAPA_COM_PENDENCIA = 13;
    const EMAIL_MEMBRO_CHAPA_ACEITOU_CONVITE = 14;
    const EMAIL_MEMBRO_CHAPA_REJEITOU_CONVITE = 15;
    const EMAIL_MEMBRO_CHAPA_CONVITE_A_CONFIRMAR = 16;

    const EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_UF = 17;
    const EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_UF = 18;
    const EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_UF = 19;
    const EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF = 20;
    const EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_IES = 21;
    const EMAIL_SUBST_MEMBRO_CHAPA_PARA_ASSESSOR_CEN_E_CEUF = 22;
    const EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_IES = 23;
    const EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_IES = 24;

    const EMAIL_RESPONSAVEL_CADASTRO_DENUNCIA = 51;
    const EMAIL_COMISSAO_ELEITORAL_CADASTRO_DENUNCIA = 52;
    const EMAIL_ASSESSOR_ELEITORAL_CADASTRO_DENUNCIA = 53;

    const EMAIL_ADMISSAO_DENUNCIA_RESP_CHAPA_MEMBRO_DENUNCIADOS = 36;
    const EMAIL_ADMISSAO_DENUNCIA_DENUNCIANTE = 37;
    const EMAIL_RELATOR_INDICADOR_DENUNCIA = 38;
    const EMAIL_ADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN = 39;
    const EMAIL_ADMISSAO_DENUNCIA_ASSESSORES_CE_CEN = 40;
    const EMAIL_INADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN = 41;
    const EMAIL_INADMISSAO_DENUNCIA_ASSESSORES_CE_CEN = 42;

    const EMAIL_IMPUGNACAO_RESPONSAVEL_CADASTRO = 28;
    const EMAIL_IMPUGNACAO_COMISSAO_ELEITORAL = 29;
    const EMAIL_IMPUGNACAO_ASSESSOR_CEN_E_CE = 30;

    const EMAIL_DEFESA_RESPONSAVEIS_CHAPA_ALERTA = 31;
    const EMAIL_DEFESA_RESPONSAVEIS_CHAPA_NOTIFICACAO_CADASTRO = 32;
    const EMAIL_DEFESA_COMISSAO_ELEITORAL_NOTIFICACAO_CADASTRO = 33;
    const EMAIL_DEFESA_ASSESSOR_CEN_E_CE_NOTIFICACAO_CADASTRO = 34;
    const EMAIL_DEFESA_IMPUGNANTE_NOTIFICACAO_CADASTRO_DEFESA = 35;

    const EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO = 43;
    const EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_DEFERIDO = 44;
    const EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_INDEFERIDO = 45;
    const EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO = 46;

    const EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO = 47;
    const EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_PROCEDENTE = 48;
    const EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_INPROCEDENTE = 49;
    const EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO = 50;

    const EMAIL_RECURSO_SUBSTITUICAO = 63;

    const EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO = 64;
    const EMAIL_JULGAMENTO_RECURSO_SUBST_DEFERIDO = 65;
    const EMAIL_JULGAMENTO_RECURSO_SUBST_INDEFERIDO = 66;
    const EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO = 67;

    const EMAIL_RECURSO_IMPUGNACAO = 68;
    const EMAIL_RECONSIDERACAO_IMPUGNACAO = 69;

    const EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO = 79;
    const EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_PROCEDENTE = 80;
    const EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_IMPROCEDENTE = 81;
    const EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO = 82;

    const EMAIL_CONTRARRAZAO_RESPONSAVEL_CHAPA = 83;
    const EMAIL_CONTRARRAZAO_IMPUGNANTE = 84;

    const EMAIL_APRESENTACAO_DEFESA_RELATOR_ATUAL = 54;
    const EMAIL_APRESENTACAO_DEFESA_ASSESSORES = 55;
    const EMAIL_APRESENTACAO_DEFESA_DENUNCIADO = 56;

    const EMAIL_CADASTRO_ENCAMINHAMENTO_RELATOR_ATUAL = 57;
    const EMAIL_CADASTRO_ENCAMINHAMENTO_ASSESSORES = 58;
    const EMAIL_CADASTRO_ENCAMINHAMENTO_DENUNCIADO = 59;
    const EMAIL_CADASTRO_ENCAMINHAMENTO_COORDENADORES = 60;

    const EMAIL_CADASTRO_PROVAS_RELATOR_ATUAL = 61;
    const EMAIL_CADASTRO_PROVAS_DESTINATARIO_ENCAMINHAMENTO = 62;

    const EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_RELATOR_ATUAL = 70;
    const EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_DENUNCIANTE = 71;
    const EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_DENUNCIADO = 72;
    const EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_ASSESSORES_CEN_CE = 73;

    const EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_RELATOR_ATUAL = 74;
    const EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_DENUNCIANTE = 75;
    const EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_DENUNCIADO = 76;
    const EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_ASSESSORES_CEN_CE = 77;
    const EMAIL_APOS_VINTE_QUATRO_HRS_AUDIE_INST_PEND_RELATOR_ATUAL = 78;

    const EMAIL_SOLICITACAO_ALEGACOES_FINAIS_RELATOR_ATUAL = 85;
    const EMAIL_SOLICITACAO_ALEGACOES_FINAIS_DESTINATARIO = 86;
    const EMAIL_SOLICITACAO_ALEGACOES_FINAIS_ASSESSORES_CE_CEN = 87;

    const EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_ATUAL = 88;
    const EMAIL_CADASTRO_ALEGACOES_FINAIS_ASSESSORES_CEN_CE = 89 ;
    const EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_DENUNCIA_APOS_ENCERRADO_PRAZO = 90;
    const EMAIL_CADASTRO_ALEGACOES_FINAIS_DESTINATARIO_APOS_ENCERRADO_PRAZO = 91;
    const EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_SAIBA_PRAZO_CONDICOES_PARECER_FINAL = 92;

    const EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA = 93;
    const EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN = 94;
    const EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE = 95;

    const EMAIL_CADASTRO_PARECER_FINAL_RELATOR_ATUAL = 96;
    const EMAIL_CADASTRO_PARECER_FINAL_DENUNCIANTE = 97;
    const EMAIL_CADASTRO_PARECER_FINAL_DENUNCIADO = 98;
    const EMAIL_CADASTRO_PARECER_FINAL_COORDENADOR_CE_CEN = 99;
    const EMAIL_CADASTRO_PARECER_FINAL_ASSESSORES_CEN_CE = 100;

    const EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 101;
    const EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 102;
    const EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 103;
    const EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 104;
    const EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 105;

    const EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_RECURSO_RECONSIDERACAO = 106;
    const EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_RECURSO_RECONSIDERACAO = 107;
    const EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_RECURSO_RECONSIDERACAO = 108;
    const EMAIL_INFORMATIVO_ASSESSOR_CEN_RECURSO_RECONSIDERACAO = 109;
    const EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO = 110;
    const EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO = 111;
    const EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO = 112;
    const EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_RECURSO_RECONSIDERACAO = 113;

    const EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_CONTRARAZAO = 114;
    const EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_CONTRARRAZAO = 115;
    const EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_CADASTRO_CONTRARRAZAO = 116;
    const EMAIL_INFORMATIVO_ASSESSOR_CEN_CADASTRO_CONTRARRAZAO = 117;
    const EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_CONTRARRAZAO = 118;
    const EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_CONTRARRAZAO = 119;
    const EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_CONTRARRAZAO = 120;
    const EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_PRAZO_CONTRARRAZAO = 121;

    const EMAIL_JULGAMENTO_FINAL_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO = 163;
    const EMAIL_JULGAMENTO_FINAL_DEFERIDO = 164;
    const EMAIL_JULGAMENTO_FINAL_INDEFERIDO = 165;
    const EMAIL_JULGAMENTO_FINAL_ASSESSORES_PERIODO_SERA_FECHADO = 166;

    const EMAIL_RECURSO_JULGAMENTO_FINAL = 167;
    const EMAIL_RECONSIDERACAO_JULGAMENTO_FINAL = 168;

    const EMAIL_SUBST_JULGAMENTO_FINAL_RESPONSAVEIS_CHAPA = 169;
    const EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUIDO = 170;
    const EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUTO = 171;
    const EMAIL_SUBST_JULGAMENTO_FINAL_PARA_CONSELHEIROS_CEN_E_CEUF = 172;
    const EMAIL_SUBST_JULGAMENTO_FINAL_PARA_ASSESSOR_CEN_E_CEUF = 173;

    const EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_ABERTO = 152;
    const EMAIL_JULGAMENTO_FINAL_RECURSO_DEFERIDO = 153;
    const EMAIL_JULGAMENTO_FINAL_RECURSO_INDEFERIDO = 154;
    const EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_DEFERIDO = 155;
    const EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_INDEFERIDO = 156;
    const EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_FECHADO = 157;

    const EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_RESPONSAVEL_CHAPA = 158;
    const EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_SUBSTITUIDO = 159;
    const EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_SUBSTITUTO = 160;
    const EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_COMISSAO = 161;
    const EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_ASSESSORES = 162;

    const EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 146;
    const EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 147;
    const EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 148;
    const EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 149;
    const EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA = 150;
    const EMAIL_INFORMATIVO_ASSESSORES_CEN_SOBRE_TEMPO_DENUNCIA_AGUARDANDO_SEGUNDA_INSTANCIA = 151;

    const EMAIL_USUARIO_CADASTRO_DENUNCIA = 122;
    const EMAIL_ACESSOR_CEN = 123;
    const EMAIL_COORDENADORES_CEN = 124;
    const EMAIL_COORDENAROR_CE_ATUA_DENUNCIA = 125;
    const EMAIL_ACESSOR_CE_UF_DENUNCIADO = 126;
    const EMAIL_DENUNCIANTE = 127;

    const EMAIL_COORDENADOR_CE_CEN = 128;
    const EMAIL_ASSCESSOR_CEN = 129;
    const EMAIL_ASSCESSOR_CEN_ROTINA = 130;
    const EMAIL_DENUNCIANTE_DENUNCIA_ADMITIDA = 131;
    const EMAIL_DENUNCIADO_DENUNCIA_ADMITIDA = 132;
    const EMAIL_COORDENADOR_CEN_DENUNCIA_JULGADA = 133;
    const EMAIL_ASSESSOR_CEN_DENUNCIA_JULGADA = 134;
    const EMAIL_DENUNCIA_CADATRADA_IMPROVIMENTO = 135;

    const EMAIL_INFORMATIVO_COORDENADOR_CE_DENUNCIA_JULGADA = 136;
    const EMAIL_INFORMATIVO_ASSESSOR_ADMISSIBILIDADE_DENUNCIA_JULGADA = 137;
    const EMAIL_INFORMATIVO_ASSESSOR_COORDENADOR_24H_SEM_RELATOR = 138;
    const EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_JULGADA_PROVIDA = 139;
    const EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_JULGADA_PROVIDA = 140;
    const EMAIL_INFORMATIVO_COORDENADOR_DENUNCIA_JULGADA_IMPROVIDA = 141;
    const EMAIL_INFORMATIVO_ASSESSOR_DENUNCIA_JULGADA_IMPROVIDA = 142;
    const EMAIL_INFORMATIVO_QUEM_CADASTROU_DENUNCIA_JULGADA_IMPROVIDA = 143;
    const EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_ADMITIDA = 144;
    const EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_ADMITIDA = 145;

    const EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 174;
    const EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNADO = 175;
    const EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_COORD_ADJUNTOS_CE_CEN = 176;
    const EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_ASSESSORES = 177;

    const EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 179;
    const EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO = 178;
    const EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN = 180;
    const EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES = 181;

    const EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO = 182;
    const EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 183;
    const EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN = 184;
    const EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES = 185;

    const EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNADO_CADASTROU = 186;
    const EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNANTE_CADASTROU = 187;
    const EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN = 188;
    const EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES = 189;

    const EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_CONTRARRAZAO = 190;
    const EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_RECURSO = 191;
    const EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN = 192;
    const EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES = 193;

    const EMAIL_JULG_RECURSO_IMPUG_RESULT_CHAPA = 194;
    const EMAIL_JULG_RECURSO_IMPUG_RESULT_IMPUGNANTE = 195;
    const EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN = 196;
    const EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES = 197;

    const ID_EMAIL_PADRAO_DEFINIR_NUMERO_MEMBROS = 1;

    const TAMANHO_MAXIMO_CARACTERES_REGRA = 1000;

    /**
     * Listagem dos tipos e-mails atividade secundária
     * @var array
     */
    public static $tiposEmailAtividadeSecundaria = [
        1 => [
            1 => [
                self::EMAIL_DEFINIR_NUMERO_MEMBROS
            ],
            2 => [
                self::EMAIL_INFORMACAO_MEMBRO,
                self::EMAIL_INCLUIR_MEMBRO,
                self::EMAIL_ALTERAR_MEMBRO
            ],
            4 => [
                self::EMAIL_ACEITAR_COMISSAO,
                self::EMAIL_RECUSAR_COMISSAO,
                self::EMAIL_24H_ENCERRAMENTO
            ],
        ],
        2 => [
            1 => [
                1 => self::EMAIL_CHAPA_CRIADA_CENBR_CEUF,
                2 => self::EMAIL_MEMBRO_INCLUIDO_CHAPA,
                3 => self::EMAIL_MEMBRO_CHAPA_COM_PENDENCIA,
                4 => self::EMAIL_CHAPA_CRIADA_RESPONSAVEIS,
                5 => self::EMAIL_CHAPA_NAO_CONFIRMADA,
                6 => self::EMAIL_CHAPA_COM_PENDENCIA,
            ],
            2 => [
                1 => self::EMAIL_MEMBRO_CHAPA_ACEITOU_CONVITE,
                2 => self::EMAIL_MEMBRO_CHAPA_REJEITOU_CONVITE,
                3 => self::EMAIL_MEMBRO_CHAPA_CONVITE_A_CONFIRMAR,
            ],
            3 => [
                1 => self::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_UF,
                2 => self::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_UF,
                3 => self::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_UF,
                4 => self::EMAIL_SUBST_MEMBRO_CHAPA_PARA_CONSELHEIROS_CEN_E_CEUF,
                5 => self::EMAIL_SUBST_MEMBRO_CHAPA_RESPONSAVEIS_IES,
                6 => self::EMAIL_SUBST_MEMBRO_CHAPA_PARA_ASSESSOR_CEN_E_CEUF,
                7 => self::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUIDO_IES,
                8 => self::EMAIL_SUBST_MEMBRO_CHAPA_SUBSTITUTO_IES,
            ],
            4 => [
                1 => self::EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_DEFERIDO,
                3 => self::EMAIL_JULGAMENTO_SUBST_COMISSAO_ELEITORAL_PEDIDO_INDEFERIDO,
                4 => self::EMAIL_JULGAMENTO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO,
            ],
            5 => [
                1 => self::EMAIL_RECURSO_SUBSTITUICAO,
            ],
            6 => [
                1 => self::EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_RECURSO_SUBST_DEFERIDO,
                3 => self::EMAIL_JULGAMENTO_RECURSO_SUBST_INDEFERIDO,
                4 => self::EMAIL_JULGAMENTO_RECURSO_SUBST_ASSESSORES_PERIODO_SERA_FECHADO,
            ],
        ],
        3 => [
            1 => [
                1 => self::EMAIL_IMPUGNACAO_RESPONSAVEL_CADASTRO,
                2 => self::EMAIL_IMPUGNACAO_COMISSAO_ELEITORAL,
                3 => self::EMAIL_IMPUGNACAO_ASSESSOR_CEN_E_CE
            ],
            2 => [
                1 => self::EMAIL_DEFESA_RESPONSAVEIS_CHAPA_ALERTA,
                2 => self::EMAIL_DEFESA_RESPONSAVEIS_CHAPA_NOTIFICACAO_CADASTRO,
                3 => self::EMAIL_DEFESA_COMISSAO_ELEITORAL_NOTIFICACAO_CADASTRO,
                4 => self::EMAIL_DEFESA_ASSESSOR_CEN_E_CE_NOTIFICACAO_CADASTRO,
                5 => self::EMAIL_DEFESA_IMPUGNANTE_NOTIFICACAO_CADASTRO_DEFESA
            ],
            3 => [
                1 => self::EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_PROCEDENTE,
                3 => self::EMAIL_JULGAMENTO_IMPUGNACAO_COMISSAO_ELEITORAL_PEDIDO_INPROCEDENTE,
                4 => self::EMAIL_JULGAMENTO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO,
            ],
            4 => [
                1 => self::EMAIL_RECURSO_IMPUGNACAO,
                2 => self::EMAIL_RECONSIDERACAO_IMPUGNACAO,
            ],
            5 => [
                1 => self::EMAIL_CONTRARRAZAO_RESPONSAVEL_CHAPA,
                2 => self::EMAIL_CONTRARRAZAO_IMPUGNANTE,
            ],
            6 => [
                1 => self::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_PROCEDENTE,
                3 => self::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_IMPROCEDENTE,
                4 => self::EMAIL_JULGAMENTO_RECURSO_IMPUGNACAO_ASSESSORES_PERIODO_SERA_FECHADO,
            ]
        ],
        4 => [
            1 => [
                1 => self::EMAIL_RESPONSAVEL_CADASTRO_DENUNCIA,
                2 => self::EMAIL_COMISSAO_ELEITORAL_CADASTRO_DENUNCIA,
                3 => self::EMAIL_ASSESSOR_ELEITORAL_CADASTRO_DENUNCIA,
            ],
            2 => [
                1 => self::EMAIL_ADMISSAO_DENUNCIA_RESP_CHAPA_MEMBRO_DENUNCIADOS,
                2 => self::EMAIL_ADMISSAO_DENUNCIA_DENUNCIANTE,
                3 => self::EMAIL_RELATOR_INDICADOR_DENUNCIA,
                4 => self::EMAIL_ADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN,
                5 => self::EMAIL_ADMISSAO_DENUNCIA_ASSESSORES_CE_CEN,
                6 => self::EMAIL_INADMISSAO_DENUNCIA_COORD_ADJUNTOS_CE_CEN,
                7 => self::EMAIL_INADMISSAO_DENUNCIA_ASSESSORES_CE_CEN,
            ],
            3 => [
                1 => self::EMAIL_APRESENTACAO_DEFESA_RELATOR_ATUAL,
                2 => self::EMAIL_APRESENTACAO_DEFESA_ASSESSORES,
                3 => self::EMAIL_APRESENTACAO_DEFESA_DENUNCIADO,
            ],
            4 => [
                1 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_RELATOR_ATUAL,
                2 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_ASSESSORES,
                3 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_DENUNCIADO,
            ],
            5 => [
                1 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_RELATOR_ATUAL,
                2 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_ASSESSORES,
                3 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_DENUNCIADO,
                4 => self::EMAIL_CADASTRO_ENCAMINHAMENTO_COORDENADORES,
            ],
            6 => [
                1 => self::EMAIL_CADASTRO_PROVAS_RELATOR_ATUAL,
                2 => self::EMAIL_CADASTRO_PROVAS_DESTINATARIO_ENCAMINHAMENTO,
            ],
            7 => [
                1 => self::EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_RELATOR_ATUAL,
                2 => self::EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_DENUNCIANTE,
                3 => self::EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_DENUNCIADO,
                4 => self::EMAIL_AGENDAMENTO_AUDIENCIA_INSTRUCAO_ASSESSORES_CEN_CE,
            ],
            8 => [
                1 => self::EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_RELATOR_ATUAL,
                2 => self::EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_DENUNCIANTE,
                3 => self::EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_DENUNCIADO,
                4 => self::EMAIL_REALIZACAO_AUDIENCIA_INSTRUCAO_ASSESSORES_CEN_CE,
                5 => self::EMAIL_APOS_VINTE_QUATRO_HRS_AUDIE_INST_PEND_RELATOR_ATUAL,
            ],
            9 => [
                1 => self::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_RELATOR_ATUAL,
                2 => self::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_DESTINATARIO,
                3 => self::EMAIL_SOLICITACAO_ALEGACOES_FINAIS_ASSESSORES_CE_CEN,
            ],
            10 =>[
                1 => self::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_ATUAL,
                2 => self::EMAIL_CADASTRO_ALEGACOES_FINAIS_ASSESSORES_CEN_CE,
                3 => self::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_DENUNCIA_APOS_ENCERRADO_PRAZO,
                4 => self::EMAIL_CADASTRO_ALEGACOES_FINAIS_DESTINATARIO_APOS_ENCERRADO_PRAZO,
                5 => self::EMAIL_CADASTRO_ALEGACOES_FINAIS_RELATOR_SAIBA_PRAZO_CONDICOES_PARECER_FINAL,
            ],
            11 =>[
                1 => self::EMAIL_CADASTRO_PARECER_FINAL_RELATOR_ATUAL,
                2 => self::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIANTE,
                3 => self::EMAIL_CADASTRO_PARECER_FINAL_DENUNCIADO,
                4 => self::EMAIL_CADASTRO_PARECER_FINAL_COORDENADOR_CE_CEN,
                5 => self::EMAIL_CADASTRO_PARECER_FINAL_ASSESSORES_CEN_CE,
            ],
            12 =>[
                1 => self::EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
                2 => self::EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
                3 => self::EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
                4 => self::EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
                5 => self::EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA,
            ],
            13 => [
                1 => self::EMAIL_INFORMATIVO_COORDENADOR_CE_DENUNCIA_JULGADA,
                2 => self::EMAIL_INFORMATIVO_ASSESSOR_ADMISSIBILIDADE_DENUNCIA_JULGADA,
                3 => self::EMAIL_INFORMATIVO_ASSESSOR_COORDENADOR_24H_SEM_RELATOR,
                4 => self::EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_JULGADA_PROVIDA,
                5 => self::EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_JULGADA_PROVIDA,
                6 => self::EMAIL_INFORMATIVO_COORDENADOR_DENUNCIA_JULGADA_IMPROVIDA,
                7 => self::EMAIL_INFORMATIVO_ASSESSOR_DENUNCIA_JULGADA_IMPROVIDA,
                8 => self::EMAIL_INFORMATIVO_QUEM_CADASTROU_DENUNCIA_JULGADA_IMPROVIDA,
            ],

            14 => [
                1 => self::EMAIL_USUARIO_CADASTRO_DENUNCIA,
                2 => self::EMAIL_ACESSOR_CEN,
                3 => self::EMAIL_COORDENADORES_CEN,
                4 => self::EMAIL_COORDENAROR_CE_ATUA_DENUNCIA,
                5 => self::EMAIL_ACESSOR_CE_UF_DENUNCIADO,
                6 => self::EMAIL_DENUNCIANTE
            ],
            15 => [
                1 => self::EMAIL_COORDENADOR_CE_CEN,
                2 => self::EMAIL_ASSCESSOR_CEN,
                3 => self::EMAIL_ASSCESSOR_CEN_ROTINA,
                4 => self::EMAIL_DENUNCIANTE_DENUNCIA_ADMITIDA,
                5 => self::EMAIL_DENUNCIADO_DENUNCIA_ADMITIDA,
                6 => self::EMAIL_COORDENADOR_CEN_DENUNCIA_JULGADA,
                7 => self::EMAIL_ASSESSOR_CEN_DENUNCIA_JULGADA,
                8 => self::EMAIL_DENUNCIA_CADATRADA_IMPROVIMENTO
            ],

            16 =>[
                1 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA,
                2 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN,
                3 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE,
            ],
            17 =>[
                1 => self::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_RECURSO_RECONSIDERACAO,
                2 => self::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_RECURSO_RECONSIDERACAO,
                3 => self::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_RECURSO_RECONSIDERACAO,
                4 => self::EMAIL_INFORMATIVO_ASSESSOR_CEN_RECURSO_RECONSIDERACAO,
                5 => self::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
                6 => self::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
                7 => self::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_RECURSO_RECONSIDERACAO,
                8 => self::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_RECURSO_RECONSIDERACAO,
            ],
            18 =>[
                1 => self::EMAIL_INFORMATIVO_DENUNCIANTE_CADASTRO_CONTRARAZAO,
                2 => self::EMAIL_INFORMATIVO_DENUNCIADO_CADASTRO_CONTRARRAZAO,
                3 => self::EMAIL_INFORMATIVO_COORDENADOR_CEN_ADJUNTO_CEN_CADASTRO_CONTRARRAZAO,
                4 => self::EMAIL_INFORMATIVO_ASSESSOR_CEN_CADASTRO_CONTRARRAZAO,
                5 => self::EMAIL_INFORMATIVO_DENUNCIANTE_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
                6 => self::EMAIL_INFORMATIVO_DENUNCIADO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
                7 => self::EMAIL_INFORMATIVO_COORDENADOR_CE_CEN_ADJUNTO_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
                8 => self::EMAIL_INFORMATIVO_ACESSOR_CE_CEN_ENCERRAMENTO_PRAZO_CONTRARRAZAO,
            ],
            19 => [
                1 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_DENUNCIA,
                2 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_COORDENADORES_CE_CEN,
                3 => self::EMAIL_INFORMATIVO_NOVO_RELATOR_ASSESSORES_CEN_CE,
                4 => self::EMAIL_INFORMATIVO_DENUNCIANTE_DENUNCIA_ADMITIDA,
                5 => self::EMAIL_INFORMATIVO_DENUNCIADO_DENUNCIA_ADMITIDA,
            ],
            20 =>[
                1 => self::EMAIL_INFORMATIVO_DENUNCIANTE_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
                2 => self::EMAIL_INFORMATIVO_DENUNCIADO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
                3 => self::EMAIL_INFORMATIVO_MEMBROS_COMISSAO_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
                4 => self::EMAIL_INFORMATIVO_COORDENADORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
                5 => self::EMAIL_INFORMATIVO_ASSESSORES_JULGAMENTO_DENUNCIA_SEGUNDA_INSTANCIA,
                6 => self::EMAIL_INFORMATIVO_ASSESSORES_CEN_SOBRE_TEMPO_DENUNCIA_AGUARDANDO_SEGUNDA_INSTANCIA,
            ],
        ],
        5 => [
            1 => [
                1 => self::EMAIL_JULGAMENTO_FINAL_IMPUGNACAO_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_FINAL_DEFERIDO,
                3 => self::EMAIL_JULGAMENTO_FINAL_INDEFERIDO,
                4 => self::EMAIL_JULGAMENTO_FINAL_ASSESSORES_PERIODO_SERA_FECHADO,
            ],
            2 => [
                1 => self::EMAIL_RECURSO_JULGAMENTO_FINAL,
                2 => self::EMAIL_RECONSIDERACAO_JULGAMENTO_FINAL,
            ],
            3 => [
                1 => self::EMAIL_SUBST_JULGAMENTO_FINAL_RESPONSAVEIS_CHAPA,
                2 => self::EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUIDO,
                3 => self::EMAIL_SUBST_JULGAMENTO_FINAL_SUBSTITUTO,
                4 => self::EMAIL_SUBST_JULGAMENTO_FINAL_PARA_CONSELHEIROS_CEN_E_CEUF,
                5 => self::EMAIL_SUBST_JULGAMENTO_FINAL_PARA_ASSESSOR_CEN_E_CEUF,
            ],
            4 => [
                1 => self::EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_ABERTO,
                2 => self::EMAIL_JULGAMENTO_FINAL_RECURSO_DEFERIDO,
                3 => self::EMAIL_JULGAMENTO_FINAL_RECURSO_INDEFERIDO,
                4 => self::EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_DEFERIDO,
                5 => self::EMAIL_JULGAMENTO_FINAL_SUBSTITUICAO_INDEFERIDO,
                6 => self::EMAIL_JULGAMENTO_FINAL_SEG_INSTANCIA_ASSESSORES_PERIODO_SERA_FECHADO,
            ],
            5 => [
                1 => self::EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_RESPONSAVEL_CHAPA,
                2 => self::EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_SUBSTITUIDO,
                3 => self::EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_SUBSTITUTO,
                4 => self::EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_COMISSAO,
                5 => self::EMAIL_RECURSO_SUBSTITUICAO_JULGAMENTO_FINAL_ASSESSORES,
            ],
        ],
        6 => [
            1 => [
                1 => self::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNANTE,
                2 => self::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_IMPUGNADO,
                3 => self::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_COORD_ADJUNTOS_CE_CEN,
                4 => self::EMAIL_PEDIDO_IMPUGNACAO_RESULTADO_ASSESSORES,
            ],
            2 => [
                1 => self::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO,
                2 => self::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE,
                3 => self::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
                4 => self::EMAIL_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES,
            ],
            3 => [
                1 => self::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNADO,
                2 => self::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_IMPUGNANTE,
                3 => self::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
                4 => self::EMAIL_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES,
            ],
            4 => [
                1 => self::EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNADO_CADASTROU,
                2 => self::EMAIL_RECURSO_JULG_ALEGACAO_IMPUG_RESULT_IMPUGNANTE_CADASTROU,
                3 => self::EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
                4 => self::EMAIL_RECURSO_JULG_ALEGACAO_IMPUGNACAO_RESULTADO_ASSESSORES,
            ],
            5 => [
                1 => self::EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_CONTRARRAZAO,
                2 => self::EMAIL_CONTRARRAZAO_RECURSO_IMPUG_RESULT_CADASTROU_RECURSO,
                3 => self::EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
                4 => self::EMAIL_CONTRARRAZAO_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES,
            ],
            6 => [
                1 => self::EMAIL_JULG_RECURSO_IMPUG_RESULT_CHAPA,
                2 => self::EMAIL_JULG_RECURSO_IMPUG_RESULT_IMPUGNANTE,
                3 => self::EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_COMISSAO_CE_CEN,
                4 => self::EMAIL_JULG_RECURSO_IMPUGNACAO_RESULTADO_ASSESSORES
            ]
        ]
    ];

    /**
     * Configuração de definição de e-mails
     * @var array $tiposEmailAtividadeSecundaria
     */
    public static $configuracaoDefinicaoEmails = [
        1 => [ // Nível da atividade principal
            "possuiAuditoria" => [4], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => []
        ],
        2 => [ // Nível da atividade principal
            "possuiAuditoria" => [1, 2, 3, 4, 5, 6], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => [1, 2, 3, 4, 5, 6]
        ],
        3 => [ // Nível da atividade principal
            "possuiAuditoria" => [1, 2, 3, 4, 5, 6], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => [1, 2, 3, 4, 5, 6] // Nível da atividade secundária
        ],
        4 => [ // Nível da atividade principal
            "possuiAuditoria" => [13, 19], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => [13, 14, 15, 19] // Nível da atividade secundária
        ],
        5 => [ // Nível da atividade principal
            "possuiAuditoria" => [1,2,3,4,5], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => [1,2,3,4,5] // Nível da atividade secundária
        ],
        6 => [// Nível da atividade principal
            "possuiAuditoria" => [1,2,3,4,5,6], // Nível da atividade secundária
            "permiteAlteracaoDeTipos" => [1,2,3,4,5,6] // Nível da atividade secundária
        ]
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Constantes configuração de definição de declarações
     * |--------------------------------------------------------------------------
     */
    const TIPO_DECLARACAO_CADASTRO_CHAPA_UF = 1;
    const TIPO_DECLARACAO_CADASTRO_CHAPA_IES = 2;
    const TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA = 3;
    const TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_COMISSAO = 4;
    const TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_UM = 5;
    const TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_DOIS = 6;
    const TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_TRES = 7;
    const TIPO_DECLARACAO_REPRESENTATIVIDADE = 8;

    public static $tiposDeclaracoesAtividadeSecundaria = [
        1 => [
            4 => [
                1 => self::TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_COMISSAO
            ],
        ],
        2 => [
            1 => [
                1 => self::TIPO_DECLARACAO_CADASTRO_CHAPA_UF,
                2 => self::TIPO_DECLARACAO_CADASTRO_CHAPA_IES,
            ],
            2 => [
                1 => self::TIPO_DECLARACAO_MEMBRO_PARA_PARTICIPAR_CHAPA,
                2 => self::TIPO_DECLARACAO_REPRESENTATIVIDADE,
            ],
        ],
        3 => [
            1 => [
                1 => self::TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_UM,
                2 => self::TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_DOIS,
                3 => self::TIPO_DECLARACAO_FUNDAMENTACAO_PEDIDO_IMPUGNACAO_TRES,
            ]
        ]
    ];

    /*
     * |--------------------------------------------------------------------------
     * | Denuncia
     * |--------------------------------------------------------------------------
     */
    const TAMANHO_LIMITE_ARQUIVO_DENUNCIA_25MB = 26214400;
    const QUANTIDADE_MAX_ARQUIVO_DENUNCIA = 5;
    const STATUS_EM_ANALISE_ADMISSIBILIDADE = 1;
    const STATUS_DENUNCIA_EM_JULGAMENTO = 2;
    const STATUS_DENUNCIA_EM_JULGAMENTO_PRIMEIRA_INSTANCIA = 7;
    const STATUS_DENUNCIA_EM_RELATORIA = 3;
    const STATUS_DENUNCIA_JULG_PRIMEIRA_INSTANCIA = 6;
    const STATUS_DENUNCIA_AGUARDANDO_CONTRARRAZAO = 8;
    const STATUS_DENUNCIA_TRANSITADO_EM_JULGADO = 9;
    const STATUS_DENUNCIA_JULG_SEGUNDA_INSTANCIA = 10;
    const STATUS_DENUNCIA_AGUARDANDO_DEFESA = 12;
    const STATUS_PENDENTE_ANALISE = 0;
    const STATUS_DENUNCIA_ADMITIDA = 1;
    const STATUS_DENUNCIA_INADMITIDA = 2;
    const TIPO_DENUNCIA_CHAPA = 'Chapa';
    const TIPO_DENUNCIA_MEMBRO_CHAPA = 'Membro Chapa';
    const TIPO_DENUNCIA_MEMBRO_COMISSAO = 'Membro Comissão';
    const TIPO_DENUNCIA_OUTROS = 'Outros';
    const QUANTIDADE_MAX_ARQUIVO_DENUNCIA_DEFESA = 5;
    const QUANTIDADE_MAX_ARQUIVO = 5;

    /*
* |--------------------------------------------------------------------------
* | Constantes Tipos de Denuncia
* |--------------------------------------------------------------------------
*/
    const TIPO_CHAPA = 1;
    const TIPO_MEMBRO_CHAPA = 2;
    const TIPO_MEMBRO_COMISSAO = 3;
    const TIPO_OUTROS = 4;

    const NIVEL_ATIVIDADE_PRINCIPAL_DENUNCIA = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA = 1;

    const NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO_COMISSAO = 1;
    const NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO_COMISSAO = 1;

    const NIVEL_ATIVIDADE_PRINCIPAL_MEMBRO = 1;
    const NIVEL_ATIVIDADE_SECUNDARIA_MEMBRO = 1;

    const NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_FINAL = 5;
    const NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL = 1;

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Pedido Substituição Chapa
     * |--------------------------------------------------------------------------
     */
    const STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO = 1;
    const STATUS_SUBSTITUICAO_CHAPA_DEFERIDO = 2;
    const STATUS_SUBSTITUICAO_CHAPA_INDEFERIDO = 3;
    const STATUS_SUBSTITUICAO_CHAPA_RECURSO_EM_ANDAMENTO = 4;
    const STATUS_SUBSTITUICAO_CHAPA_RECURSO_DEFERIDO = 5;
    const STATUS_SUBSTITUICAO_CHAPA_RECURSO_INDEFERIDO = 6;

    public static $statusSubstituicaoChapaEmAndamento= [
        self::STATUS_SUBSTITUICAO_CHAPA_EM_ANDAMENTO,
        self::STATUS_SUBSTITUICAO_CHAPA_RECURSO_EM_ANDAMENTO,
    ];

    public static $statusSubstituicaoChapaDeferido = [
        self::STATUS_SUBSTITUICAO_CHAPA_DEFERIDO,
        self::STATUS_SUBSTITUICAO_CHAPA_RECURSO_DEFERIDO,
    ];

    public static $statusSubstituicaoChapaIndeferido = [
        self::STATUS_SUBSTITUICAO_CHAPA_INDEFERIDO,
        self::STATUS_SUBSTITUICAO_CHAPA_RECURSO_INDEFERIDO,
    ];

    const PATH_STORAGE_ARQ_PEDIDO_SUBSTITUICAO_CHAPA = "eleitoral/pedidos_substituicoes_chapas";
    const PREFIXO_ARQ_PEDIDO_SUBSTITUICAO_CHAPA = "doc_pedido_substituicao_chapa";


    /*
     * |--------------------------------------------------------------------------
     * | Constantes Pedido Impugnacao
     * |--------------------------------------------------------------------------
     */
    const STATUS_IMPUGNACAO_EM_ANALISE = 1;
    const STATUS_IMPUGNACAO_PROCEDENTE = 2;
    const STATUS_IMPUGNACAO_IMPROCEDENTE = 3;
    const STATUS_IMPUGNACAO_RECURSO_EM_ANALISE = 4;
    const STATUS_IMPUGNACAO_RECURSO_PROCEDENTE = 5;
    const STATUS_IMPUGNACAO_RECURSO_IMPROCEDENTE = 6;

    public static $statusImpugnacaoChapaEmAndamento= [
        self::STATUS_IMPUGNACAO_EM_ANALISE,
        self::STATUS_IMPUGNACAO_RECURSO_EM_ANALISE,
    ];

    public static $statusImpugnacaoChapaProcedente = [
        self::STATUS_IMPUGNACAO_PROCEDENTE,
        self::STATUS_IMPUGNACAO_RECURSO_PROCEDENTE,
    ];

    public static $statusImpugnacaoChapaImprocedente = [
        self::STATUS_IMPUGNACAO_IMPROCEDENTE,
        self::STATUS_SUBSTITUICAO_CHAPA_RECURSO_INDEFERIDO,
    ];

    const PATH_STORAGE_ARQ_PEDIDO_IMPUGNACAO = "eleitoral/pedidos_impugnacao";
    const PATH_STORAGE_ARQ_JULGAMENTO_DENUNCIA = "eleitoral/julgamento_denuncia";
    const PREFIXO_ARQ_PEDIDO_IMPUGNACAO = "doc_pedido_impugnacao";
    const PREFIX_ARQ_JULGAMENTO_ADMISSIBILIDADE = 'doc_julgamento_admissibilidade';
    const PREFIX_ARQ_RECURSO_JULGAMENTO_ADMISSIBILIDADE = 'doc_recurso_julgamento_admissibilidade';

    /*
     * |--------------------------------------------------------------------------
     * | Constantes Pedido Impugnacao Resultado
     * |--------------------------------------------------------------------------
     */
    const STATUS_IMPUG_RESULTADO_AGUARDANDO_ALEGACOES = 1;
    const STATUS_IMPUG_RESULTADO_ALEGACAO_INSERIDA = 2;
    const STATUS_IMPUG_RESULTADO_JUGADO_1_INSTANCIA = 3;
    const STATUS_IMPUG_RESULTADO_EM_RECURSO = 4;
    const STATUS_IMPUG_RESULTADO_EM_CONTRARRAZAO = 5;
    const STATUS_IMPUG_RESULTADO_EM_JULGAMENTO_2_INSTANCIA = 6;
    const STATUS_IMPUG_RESULTADO_TRANSITADO_JULGADO = 7;
    const STATUS_IMPUG_RESULTADO_AGUARDANDO_HOMOLOGACAO = 8;

    const UF_CAU_BR = "165";
    const ID_IES_ELEITORAL = "0";

    /*
     * |--------------------------------------------------------------------------
     * | Membro Comissao
     * |--------------------------------------------------------------------------
     */
    const TIPO_MEMBRO_COMISSAO_CONSELHEIRO_CEN_BR = 1;
    const TIPO_MEMBRO_COMISSAO_CONSELHEIRO_CE_UF = 2;
    const DESCRICAO_MEMBRO_COMISSAO_CONSELHEIRO_CEN_BR = 'Conselheiro CEN/BR';
    const DESCRICAO_MEMBRO_COMISSAO_CONSELHEIRO_CE_UF = 'Conselheiro CE/UF';

    /*
     * |--------------------------------------------------------------------------
     * | Defesa impugnação
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_DEFESA_IMPUGNACAO = "eleitoral/defesas_impugnacao";
    const PREFIXO_ARQ_DEFESA_IMPUGNACAO = "doc_defesa_impugnacao";

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento substituição
     * |--------------------------------------------------------------------------
     */
    const STATUS_JULGAMENTO_DEFERIDO = 1;
    const STATUS_JULGAMENTO_INDEFERIDO = 2;

    const PATH_STORAGE_ARQ_JULGAMENTO_SUBSTITUICAO = "eleitoral/julgamentos_substituicoes";
    const PREFIXO_ARQ_JULGAMENTO_SUBSTITUICAO = "doc_julgamento_substituicao";

    /*
    * |--------------------------------------------------------------------------
    * | Denuncia Defesa
    * |--------------------------------------------------------------------------
    */

    const ACAO_HISTORICO_DENUNCIA_DEFESA = 'Cadastro da defesa';
    const PRAZO_DEFESA_DENUNCIA_DIAS = 3;
    const PRAZO_DEFESA_RECURSO_DENUNCIA_DIAS = 3;
    const PRAZO_DEFESA_CONTRARRAZAO_RECURSO_DENUNCIA_DIAS = 6;
    const NAO_HOUVE_APRESENTACAO_DEFESA_DENUNCIA = 'Não houve apresentação de defesa para esta denúncia.';
    const ACAO_HISTORICO_PRAZO_ENCERRADO_DEFESA = 'Prazo de defesa transcorrido';

    /*
    * |--------------------------------------------------------------------------
    * | Denuncia Provas
    * |--------------------------------------------------------------------------
    */
    const ACAO_HISTORICO_DENUNCIA_PROVAS = 'Inserção de provas';

    /*
   * |--------------------------------------------------------------------------
   * | Denuncia Audiencia Instrução
   * |--------------------------------------------------------------------------
   */
    const ACAO_HISTORICO_DENUNCIA_AUDIENCIA_INSTRUCAO = 'Realização de Audiência';

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento impugnação
     * |--------------------------------------------------------------------------
     */
    const STATUS_JULG_IMPUGNACAO_PROCEDENTE = 1;
    const STATUS_JULG_IMPUGNACAO_IMPROCEDENTE = 2;

    const PATH_STORAGE_ARQ_JULGAMENTO_IMPUGNACAO = "eleitoral/julgamentos_impugnacoes";
    const PREFIXO_ARQ_JULGAMENTO_IMPUGNACAO = "doc_julgamento_impugnacao";

    /*
     * |--------------------------------------------------------------------------
     * | Recurso julgamento substituição 1ª instância
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_SUBST = "eleitoral/recursos_julgamentos_substituicoes";
    const PREFIXO_ARQ_RECURSO_JULGAMENTO_SUBST = "doc_recurso_julgamento_substituicao";

    const LABEL_INTERPOR_RECURSO = "Interpor Recurso";
    const LABEL_INTERPOR_RECONSIDERACAO = "Interpor Reconsideração";

    /*
     * |--------------------------------------------------------------------------
     * | Substituição julgamento Final 1ª instância
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_SUBSTITUICOES_JULGAMENTOS_FINAIS = "eleitoral/substituicoes_julgamentos_finais";
    const PREFIXO_ARQ_SUBSTITUICOES_JULGAMENTOS_FINAIS = "doc_substituicao_julgamento_final";
    const HISTORICO_PROF_DS_ACAO_INSERIR_SUBSTITUICAO_JULG_FINAL = 'Incluir pedido de substituição';

    /*
     * |--------------------------------------------------------------------------
     * | Recurso julgamento impugnação 1ª instância
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_IMPUG = "eleitoral/recursos_julgamentos_impugnacoes";
    const PREFIXO_ARQ_RECURSO_JULGAMENTO_IMPUG = "doc_recurso_julgamento_impugnacao";

    const TP_SOLICITACAO_RECURSO_RESPONSAVEL_CHAPA = 1;
    const TP_SOLICITACAO_RECURSO_IMPUGNANTE = 2;

    const HISTORICO_INCLUSAO_RECURSO_IMPUGNACAO = "Inclusão %s do Julgamento Impugnação – 1ª instância";

    const LABEL_RECURSO = "Recurso";
    const LABEL_RECONSIDERACAO = "Reconsideração";

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso substituição (2ª instância)
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_SUBST = "eleitoral/julgamentos_recursos_substituicoes";
    const PREFIXO_ARQ_JULGAMENTO_RECURSO_SUBST = "doc_julgamento_recurso_substituicao";

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso substituição (2ª instância)
     * |--------------------------------------------------------------------------
     */
    const HISTORICO_INCLUSAO_SUBSTITUICAO_IMPUGNACAO = "Incluir pedido de substituição";

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso impugnaçao (2ª instância)
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_IMPUGN = "eleitoral/julgamentos_recursos_impugnacoes";
    const PREFIXO_ARQ_JULGAMENTO_RECURSO_IMPUGN = "doc_julgamento_recurso_impugnacao";
    const HISTORICO_INCLUSAO_JULG_RECURSO_IMPUGNACAO = "inclusão Julgamento do recurso da Impugnação – 2ª instância";

    /*
     * |--------------------------------------------------------------------------
     * | Contrarrazao do Recurso Impugnacao
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_CONTRARRAZAO_RECURSO_IMPUGN = "eleitoral/contrarrazao_recursos_impugnacoes";
    const PREFIXO_ARQ_CONTRARRAZAO_RECURSO_IMPUGN = "doc_contrarrazao_recurso_impugnacao";

    /*
     * |--------------------------------------------------------------------------
     * | Impugnação de Resultado.
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_IMPUGN_RESULTADO = "eleitoral/impugnacao_resultados";
    const PREFIXO_ARQ_IMPUGN_RESULTADO = "doc_impugnacao_resultado";
    const DESCRICAO_TIPO_IMPUGNACAO_DE_RESULTADO = "Impugnação de Resultado";


    /*
     * |--------------------------------------------------------------------------
     * | Alegação da Impugnação de Resultado.
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_ALEGACAO_IMPUGNACAO_RESULTADO = "eleitoral/alegacao_impugnacao_resultados";
    const PREFIXO_ARQ_ALEGACAO_IMPUGN_RESULTADO = "doc_alegacao_impugnacao_resultado";

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Encaminhamento
     * |--------------------------------------------------------------------------
     */
    const TIPO_ENCAMINHAMENTO_IMPEDIMENTO_SUSPEICAO = 1;
    const TIPO_ENCAMINHAMENTO_PRODUCAO_PROVAS = 2;
    const TIPO_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO = 3;
    const TIPO_ENCAMINHAMENTO_ALEGACOES_FINAIS = 4;
    const TIPO_ENCAMINHAMENTO_PARECER_FINAL = 5;

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Situação Encaminhamento
     * |--------------------------------------------------------------------------
     */
    const TIPO_SITUACAO_ENCAMINHAMENTO_PENDENTE = 1;
    const TIPO_SITUACAO_ENCAMINHAMENTO_TRANSCORRIDO = 2;
    const TIPO_SITUACAO_ENCAMINHAMENTO_FECHADO = 3;
    const TIPO_SITUACAO_ENCAMINHAMENTO_CONCLUIDO = 4;

    const JUSTIFICATIVA_ENCAMINHAMENTO_AUDIENCIA_INSTRUCAO_FECHADO = "Este encaminhamento foi fechado porque houve registro da audiência em outro encaminhamento.";

    /*
     * |--------------------------------------------------------------------------
     * | Recurso da Denuncia
     * |--------------------------------------------------------------------------
     */
    const TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIANTE = 1;
    const TIPO_RECURSO_CONTRARRAZAO_DENUNCIA_DENUNCIADO = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Denuncia
     * |--------------------------------------------------------------------------
     */
    const NIVEL_JULGAMENTO_DENUNCIA_PRIMEIRA_INSTANCIA = 1;
    const ACAO_HISTORICO_JULGAMENTO_DENUNCIA = 'Julgado em 1ª instância';
    const ACAO_HISTORICO_RETIFICACAO_JULGAMENTO_DENUNCIA = 'Alteração de Julgamento 1ª Instância';
    const QUANTIDADE_MAX_ARQUIVO_JULG_DENUNCIA = 5;
    const NIVEL_ATIVIDADE_PRINCIPAL_JULGAMENTO_DENUNCIA = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_DENUNCIA = 12;
    const NIVEL_ATIVIDADE_SECUNDARIA_DENUNCIA_JULGAMENTO_SEGUNDA_INSTANCIA = 20;
    const PRAZO_JULGAMENTO_RECURSO_DENUNCIA_DIAS = 10;

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Julgamento
     * |--------------------------------------------------------------------------
     */
    const TIPO_JULGAMENTO_PROCEDENTE = 1;
    const TIPO_JULGAMENTO_IMPROCEDENTE = 2;
    const PREFIXO_ARQ_JULGAMENTO_DENUNCIA = "doc_julgamento_denuncia";
    const PREFIXO_ARQ_JULGAMENTO_RECURSO  = "doc_recurso_julgamento";

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Sentença Julgamento
     * |--------------------------------------------------------------------------
     */
    const TIPO_SENTENCA_JULGAMENTO_ADVERTENCIA = 1;
    const TIPO_SENTENCA_JULGAMENTO_SUSP_PROPAGANDA = 2;
    const TIPO_SENTENCA_JULGAMENTO_CASSAC_REGIST_CANDIDATURA = 3;
    const TIPO_SENTENCA_JULGAMENTO_MULTA = 4;
    const TIPO_SENTENCA_JULGAMENTO_OUTRA_PROPORC_INFRAC_COMENTIDA = 5;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade 4.2 Análise de Admissibilidade
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_SECUNDARIA_ANALISE_ADMISSIBILIDADE = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade 4.3 Apresentar Defesa
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_PRINCIPAL_APRESENTAR_DEFESA = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_APRESENTAR_DEFESA = 3;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade para Encaminhamento Denuncia.
     * |--------------------------------------------------------------------------
     */

    const NIVEL_ATIVIDADE_PRINCIPAL_IMPEDIMENTO_SUSPENSAO = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_IMPEDIMENTO_SUSPENSAO = 5;

    const NIVEL_ATIVIDADE_PRINCIPAL_PRODUCAO_PROVAS = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_PRODUCAO_PROVAS = 4;

    const NIVEL_ATIVIDADE_PRINCIPAL_AUDIENCIA_INSTRUCAO = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_AUDIENCIA_INSTRUCAO = 7;

    const NIVEL_ATIVIDADE_PRINCIPAL_ALEGACOES_FINAIS = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_ALEGACOES_FINAIS = 9;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_ALEGACOES_FINAIS = 10;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade para Inserir Novo Relator.
     * |--------------------------------------------------------------------------
     */

    const NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_NOVO_RELATOR = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_NOVO_RELATOR = 16;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade 4.6 Inserir Provas
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_PROVAS = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_PROVAS = 6;

    /*
     * |--------------------------------------------------------------------------
     * | Niveis Atividade 4.6 Audiencia Instrução
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_PRINCIPAL_REGISTRAR_AUDIENCIA_INSTRUCAO = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_REGISTRAR_AUDIENCIA_INSTRUCAO = 8;

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Admissibilidade 4.13
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_SECUNDARIA_JULGAMENTO_ADMISSIBILIDADE = 13;

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Admissibilidade 4.19
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_RELATOR = 19;

    /*
     * |--------------------------------------------------------------------------
     * | Alegação final de Encaminhamento da Denúncia
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_ALEGACAO_FINAL_ENCAMINHAMENTO = "eleitoral/alegacao_final_encaminhamento";
    const PREFIXO_ARQ_ALEGACAO_FINAL_ENCAMINHAMENTO = "doc_alegacao_final_encaminhamento";
    const QUANTIDADE_MAX_ARQUIVO_ALEGACAO_FINAL = 5;
    const ACAO_HISTORICO_ALEGACAO_FINAL = 'Inserção de Alegações Finais';

    /*
     * |--------------------------------------------------------------------------
     * | Parecer final da Denúncia
     * |--------------------------------------------------------------------------
     */
    const ACAO_HISTORICO_PARECER_FINAL = 'Parecer Final';
    const PATH_STORAGE_ARQ_PARECER_FINAL_ENCAMINHAMENTO = "eleitoral/parecer_final_encaminhamento";
    const PREFIXO_ARQ_PARECER_FINAL_ENCAMINHAMENTO = "doc_parecer_final_encaminhamento";
    const NIVEL_ATIVIDADE_PRINCIPAL_INSERIR_PARECER_FINAL = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_PARECER_FINAL = 11;

    /*
     * |--------------------------------------------------------------------------
     * | Recurso Contrarrazão Denúncia
     * |--------------------------------------------------------------------------
     */
    const NIVEL_ATIVIDADE_PRINCIPAL_RECURSO_CONTRARRAZAO_DENUNCIA = 4;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_RECURSO_DENUNCIA = 17;
    const NIVEL_ATIVIDADE_SECUNDARIA_INSERIR_CONTRARRAZAO_DENUNCIA = 18;
    const PREFIXO_ARQ_CONTRARRAZAO_RECURSO = "doc_contrarrazao_recurso";
    const ACAO_HISTORICO_CONTRARRAZAO_RECURSO = 'Cadastro de contrarrazão do recurso';
    const ACAO_HISTORICO_PRAZO_ENCERRADO_RECURSO_DENUNCIA = 'Prazo de recurso ou reconsideração transcorrido';
    const ACAO_HISTORICO_JULGAMENTO_RECURSO_DENUNCIA = 'Julgado em 2ª Instância';

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Final
     * |--------------------------------------------------------------------------
     */
    const STATUS_JULG_FINAL_DEFERIDO = 1;
    const STATUS_JULG_FINAL_INDEFERIDO = 2;

    const PATH_STORAGE_ARQ_JULGAMENTO_FINAL = "eleitoral/julgamentos_finais";
    const PREFIXO_ARQ_JULGAMENTO_FINAL = "doc_julgamento_final";

    const PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_FINAL = "eleitoral/recurso_julgamentos_finais";
    const PREFIXO_ARQ_RECURSO_JULGAMENTO_FINAL = "doc_recurso_julgamento_final";

    const HISTORICO_INCLUSAO_JULG_FINAL = 'Inclusão Julgamento Final – 1ª instância';
    const HISTORICO_INCLUSAO_REC_JULG = 'Recurso do Julgamento Final';

    const DS_LABEL_SEM_INCLUSAO_MEMBRO = 'Sem inclusão de membro';

    const PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA = "eleitoral/julgamentos_recurso_segunda_instancia";
    const PREFIXO_ARQ_JULGAMENTO_RECURSO_SEGUNDA_INSTANCIA = "doc_julgamento_recurso_segunda_instancia";
    const HISTORICO_INCLUSAO_JULG_SEGUNDA_INSTANCIA = 'Inclusão Julgamento Final – 2ª instância';
    const HISTORICO_ALTERACAO_JULG_SEGUNDA_INSTANCIA = 'Alteração Julgamento Final – 2ª instância';

    const PATH_STORAGE_ARQ_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA = "eleitoral/julgamentos_substituicao_segunda_instancia";
    const PREFIXO_ARQ_JULGAMENTO_SUBSTITUICAO_SEGUNDA_INSTANCIA = "doc_julgamento_substituicao_segunda_instancia";

    const PATH_STORAGE_ARQ_JULGAMENTO_RECURSO_DA_SUBST_FINAL = "eleitoral/julgamentos_recursos_subsituicoes";
    const PREFIXO_ARQ_JULGAMENTO_RECURSO_DA_SUBST_FINAL = "doc_julgamento_recurso_substituicao";

    /*
     * |--------------------------------------------------------------------------
     * | Recurso Julgamento Segunda Instancia Substituicao
     * |--------------------------------------------------------------------------
     */

    const PATH_STORAGE_ARQ_RECURSO_SEGUNDO_JULGAMENTO = "eleitoral/recurso_julgamento_segunda_instancia_substituicao";
    const PREFIXO_ARQ_RECURSO_SEGUNDO_JULGAMENTO = "doc_recurso_julgamento_segunda_instancia_substituicao";
    const HISTORICO_INCLUSAO_REC_JULG_SUB = 'Recurso do Julgamento Segunda Instancia Substituição';


    /*
     * |--------------------------------------------------------------------------
     * | Atividade Secundaria - Julgamento Final – 2ª Instancia
     * |--------------------------------------------------------------------------
     */

    const ATIVIDADE_PRIMARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA = 5;
    const ATIVIDADE_SECUNDARIA_JULGAMENTO_FINAL_SEGUNDA_INSTANCIA = 4;

    /*
    * |--------------------------------------------------------------------------
    * | Substituição Julgamento Final
    * |--------------------------------------------------------------------------
    */
    const SUBSTITUICAO_JULGAMENTO_TO_TIPO_SUBSTITUICAO = 'SUBSTITUICAO';
    const SUBSTITUICAO_JULGAMENTO_TO_TIPO_RECURSO = 'RECURSO';

    /*
    * |--------------------------------------------------------------------------
    * | Utils
    * |--------------------------------------------------------------------------
    */

    const FERIADOS_NACIONAIS = 1;
    const FERIADOS_FACULTATIVOS = 4;

    /*
    * |--------------------------------------------------------------------------
    * | Impugnação de Resultado Julgamento
    * |--------------------------------------------------------------------------
    */
    const HISTORICO_INCLUSAO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = 'Inclusão Julgamento Alegação de Impugnação de resultado – 1ª instância';
    const PATH_STORAGE_ARQ_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = "eleitoral/julgamentos_primeira_instancia_elegacao_inpugnacao_resultado";

    const NIVEL_ATIVIDADE_PRINCIPAL_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = 6;
    const NIVEL_ATIVIDADE_INCLUSAO_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = 3;

    const STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = 3;

    const STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_IMPUGNACAO_RESULTADO_PROCEDENTE = 1;
    const STATUS_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_IMPUGNACAO_RESULTADO_IMPROCEDENTE = 2;

    const PREFIXO_ARQ_JUGAMENTO_PRIMEIRA_INSTANCIA_ALEGACAO_INPUGNACAO_RESULTADO = "doc_julgamentos_primeira_inst_eleg_inpug_resu";

    /*
     * |--------------------------------------------------------------------------
     * | Alegação Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    const HISTORICO_INCLUSAO_ALEGACAO_IMPUGNACAO_RESULTADO = 'Cadastro de alegação';

    const NIVEL_ATIVIDADE_PRINCIPAL_ALEGACAO_INPUGNACAO_RESULTADO = 6;
    const NIVEL_ATIVIDADE_INCLUSAO_ALEGACAO_INPUGNACAO_RESULTADO = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Contrarrazão Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_CONTRARRAZAO_IMPUGNACAO_RESULTADO = "eleitoral/contrarrazoes_impugnacao_resultado";
    const PREFIXO_ARQ_CONTRARRAZAO_IMPUGNACAO_RESULTADO = "doc_contrarrazao_impugnacao_resultado";
    const HISTORICO_PROF_DS_ACAO_INSERIR_CONTRARRAZAO_IMPUG_RESULT = 'Cadastro de Contrarrazão';

    /*
     * |--------------------------------------------------------------------------
     * | Recurso de Julgamento de Alegaçoes de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    const PATH_STORAGE_ARQ_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO = "eleitoral/recursos_julgamento_impugnacao_resultado";
    const PREFIXO_ARQ_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO = "doc_recurso_julgamento_impugnacao_resultado";
    const HISTORICO_PROF_DS_ACAO_INSERIR_RECURSO_JULGAMENTO_IMPUGNACAO_RESULTADO = 'Cadastro de Recurso';
    const TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNADO = 1;
    const TIPO_RECURSO_IMPUGNACAO_RESULTADO_IMPUGNANTE = 2;

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento 2ª instância do Recurso de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */

    const NIVEL_ATIVIDADE_PRINCIPAL_CADASTRAR_JULG_RECURSO_IMPUG_RESULTADO = 6;
    const NIVEL_ATIVIDADE_SECUNDARIA_CADASTRAR_JULG_RECURSO_IMPUG_RESULTADO = 6;

    const STATUS_JULG_RECURSO_IMPUG_RESULTADO_PROCEDENTE = 1;
    const STATUS_JULG_RECURSO_IMPUG_RESULTADO_IMPROCEDENTE = 2;

    public static $statusJulgRecursoImpugResultado = [
        self::STATUS_JULG_RECURSO_IMPUG_RESULTADO_PROCEDENTE,
        self::STATUS_JULG_RECURSO_IMPUG_RESULTADO_IMPROCEDENTE
    ];

    const PATH_STORAGE_ARQ_JULG_RECURSO_IMPUG_RESULT = "eleitoral/julgamentos_recursos_impug_resultado";
    const PREFIXO_ARQ_JULG_RECURSO_IMPUG_RESULT = "doc_julgamento_recursos_impug_resultado";

    const HISTORICO_INCLUSAO_JULG_RECURSO_IMPUG_RESULTADO = 'Cadastro de Julgamento em 2ª Instancia';

    const TIPO_DIPLOMA = 1;
    const TIPO_TERMO_POSSE = 2;

    const ORDENAR_POR_UF = 1;

    const PREFIXO_ELEITORAL_ARQUIVO = 'doc_assinatura';

    const ID_DECLARACAO_DIPLOMA = 43;
    const ID_DECLARACAO_TERMO = 42;
    /**
     * Construtor privado para garantir o singleton.
     */
    private function __construct()
    {
    }
}
