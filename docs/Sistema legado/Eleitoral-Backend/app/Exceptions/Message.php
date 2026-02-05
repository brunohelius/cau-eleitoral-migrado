<?php
/*
 * Message.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Exceptions;

use App\Util\JsonUtils;
use JMS\Serializer\Annotation as JMS;

/**
 * Classe de representação de mensagem de exceção.
 *
 * @package App\Exceptions
 * @author Squadra Tecnologia
 */
class Message
{
    const APLICACAO_ENCONTROU_ERRO_INESPERADO = 'MSG-001';
    const TOKEN_INVALIDO = 'MSG-002';
    const NENHUM_CALENDARIO_ENCONTRADO = 'MSG-003';
    const NENHUM_MEMBRO_ENCONTRADO = 'MSG-003';
    const NENHUM_REGISTRO_ENCONTRADO = 'MSG-003';
    const VALIDACAO_DATA_INICIAL_FINAL = 'MSG-004';
    const VALIDACAO_CAMPOS_OBRIGATORIOS = 'MSG-005';
    const VALIDACAO_ANO_CALENDARIO = 'MSG-006';
    const MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO = 'MSG-007';
    const MSG_ANEXO_EXTENSAO_PDF = 'MSG-008';
    const VALIDACAO_IDADE_CALENDARIO = 'MSG-009';
    const MSG_QTD_ARQUIVOS_RESOLUCAO = 'MSG-010';
    const MSG_DATA_INI_DIVERGENTE_VIGENCIA = 'MSG-011';
    const MSG_DATA_FIM_DIVERGENTE_VIGENCIA = 'MSG-012';
    const MSG_FALHA_HTTP = 'MSG-013';
    const MSG_URL_NAO_INFORMADA = 'MSG-014';
    const MSG_FAVOR_INFORMAR_CAMPOS_OBRIGATORIOS = 'MSG-015';
    const MSG_DATA_INI_DIVERGENTE_VIGENCIA_ATV_SEC = 'MSG-016';
    const MSG_DATA_FIM_DIVERGENTE_VIGENCIA_ATV_SEC = 'MSG-017';
    const MSG_CAMPOS_PREENCHIMENTO_OBRIGATORIO = 'MSG-018';
    const MSG_NAO_FOI_DENIFIDO_PARAMETRO = 'MSG-019';
    const MSG_DOCUMENTO_ELEICAO_LIMITE_TAMANHO = 'MSG-020';
    const MSG_JA_EXISTE_EMAIL_PARAMETRIZADO_PARA_ESSE_TIPO = 'MSG-021';
    const MSG_ARQUIVO_NAO_ENCONTRADO = 'MSG-026';
    const MSG_MAXIMO_TAMANHO_PERMITIDO_ARQUIVOS_CABECALHO_RODAPE = 'MSG-022';
    const MSG_TAMANHO_MAXIMO_PERMITIDO_FOTO_SINTESE_CURRICULO = 'MSG-040';
    const MGS_LARGURA_MAXIMA_PERMITIDA = 'MSG-023';
    const MSG_ALTURA_MAXIMA_PERMITIDA = 'MSG-024';
    const MSG_FORMATO_ARQUIVO_INVALIDO_SOMENTE = 'MSG-025';
    const MSG_FORMATO_ARQUIVO_INVALIDO_SINTESE_CURRICULO = 'MSG-041';
    const VALIDACAO_FILTRO_OBRIGATORIO = 'MSGI-026';
    const MSG_PROFISSIONAL_JA_CONFIRMOU_PART_CHAPA = 'MSG-027';
    const MSG_PROFISSIONAL_POSSUI_CONVITE_PART_CHAPA_A_CONFIRMAR = 'MSG-028';
    const MSG_SEM_PERIODO_VIGENTE_INCLUIR_CHAPA = 'MSG-029';
    const MSG_CPF_NAO_ENCONTRADO_SICCAU = 'MSG-030';
    const MSG_CPF_ACEITOU_PARTIC_OUTRA_CHAPA = 'MSG-031';
    const MSG_CPF_JA_INCLUIDO_CHAPA = 'MSG-032';
    const MSG_CPF_FOI_ELEITO_MANDATO_SUBSEQUENTE = 'MSG-033';
    const MSG_CPF_PERDEU_MANDATO_ULTIMOS_CINCO_ANOS = 'MSG-034';
    const MSG_CPF_POSSUI_SANCAO_ETICO_DISCIPLINAR = 'MSG-035';
    const MSG_CPF_SANCIONADO_INFRACAO_ETICO_DISCIPLINAR = 'MSG-036';
    const MSG_UF_CHAPA_DIFERENTE_PROFISSIONAL = 'MSG-037';
    const MSG_ELEICAO_NAO_POSSUI_CHAPAS_CRIADAS = 'MSG-038';
    const MSG_RESPONSAVEL_NAO_PROFISSIONAL = 'MSG-039';
    const MSG_PENDENCIA_INCLUIR_MEMBRO_CHAPA = 'MSG-042';
    const MSG_UF_CPF_DIFERENTE_UF_CHAPA = 'MSG-043';
    const MSG_CPF_CRIADOR_CHAPA_NAO_CONSTA_NOS_MEMBROS = 'MSG-044';
    const MSG_PERMITIDO_ATE_TRES_RESPONSAVEIS_CHAPA = 'MSG-045';
    const MSG_CRIADOR_CHAPA_IES_DEVE_SER_TITULAR = "MSG-046";
    const MSG_CRIACAO_CHAPA_DEVE_TER_NO_MIN_UM_MEMBRO = "MSG-047";
    const MSG_PERMISSAO_DOIS_UPLOADS = "MSG-048";
    const MSG_UF_SEM_ELEICAO_ATIVA = "MSG-049";
    const MSG_ANEXO_EXTENSAO_DOC_DECLARACAO = 'MSG-050';
    const MSG_ANEXO_EXTENSAO_PDF_DECLARACAO = 'MSG-051';
    const MSG_ANEXO_EXTENSAO_PDF_DOC_DECLARACAO = 'MSG-052';
    const MSG_LIMITE_TAMANHO_ARQUIVO_DECLARACAO_CHAPA = 'MSG-053';
    const MSG_CPF_DEVEDOR_MULTA_PROCESSO_ELEITORAL = 'MSG-054';
    const MSG_CHAPA_DEVE_TER_MINIMO_UM_RESPONSAVEL = 'MSG-055';
    const MSG_ANEXO_RESOLUCAO_LIMITE_TAMANHO_DECLARACAO = 'MSG-056';
    const MSG_QTD_ARQUIVOS_RESOLUCAO_DECLARACAO = 'MSG-057';
    const MSG_ANEXO_CALENDARIO_EXTENSAO = 'MSG-058';
    const MSG_ANEXO_DECLARACAO_DOC_E_PDF = 'MSG-059';
    const MSG_CPF_NOME_NAO_ENCONTRADO_SICCAU = 'MSG-060';
    const MSG_PROFISSIONAL_VISUALIZAR_POSSUI_CONVITE_PART_CHAPA_A_CONFIRMAR = 'MSG-061';
    const MSG_PERIODO_CONVITE_CHAPA_NOT_VIGENTE = 'MSG-062';
    const MSG_CPF_SEM_REGISTRO_CAU_COMPLETO = 'MSG-063';
    const MSG_ARQUIVO_INVALIDO = 'MSG-098';
    const MSG_DENUNCIA_MESMO_MEMBRO = 'MSG-099';
    const MSG_DENUNCIA_PRAZO_EXPIRADO = 'MSG-100';
    const MSG_DENUNCIA_NAO_ENOONTRADA_PARA_ELEICAO = 'MSG-101';
    const MSG_DENUNCIA_NAO_ENCONTRADA_ATUAL_RELATOR = 'MSG-084';
    const MSG_ATIV_SELECIONADA_SEM_VIGENCIA = 'MSG-064';
    const MSG_VISUALIZACAO_ATIV_APENAS_MEMBROS_RESPONSAVEIS_CHAPA = 'MSG-065';
    const MSG_SUBSTITUICAO_APENAS_PROFISSIONAL_DA_CHAPA_DO_RESPONSAVEL = 'MSG-066';
    const MSG_NUMERO_CHAPA_JA_CADASTRADO_PARA_UF = 'MSG-067';
    const MSG_CAMPO_SUBSTITUTO_CHAPA_OBRIGATORIO = 'MSG-068';
    const MSG_DESABILTOU_TODOS_RESPONSAVEL_CHAPA = 'MSG-069';
    const MSG_SUBSTITUICAO_CHAPA_RESPONSAVEL_SEM_PEDIDOS = 'MSG-070';
    const MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO= 'MSG-071';
    const MSG_SUBSTITUICAO_MEMBRO_COMISSAO_CEUF_SEM_PEDIDOS= 'MSG-072';
    const MSG_SUBSTITUICAO_MEMBRO_COMISSAO_CENBR_SEM_PEDIDOS= 'MSG-073';
    const MSG_MEMBRO_CHAPA_JA_TEM_PEDIDO_SUBSTITUICAO = 'MSG-074';
    const MSG_VISUALIZACAO_PEDIDOS_APENAS_MEMBROS_COMISSAO_CEN_BR = 'MSG-075'; // Não alterar o código dessa mensagem
    const MSG_ELEICOES_SEM_PEDIDOS_SUBSTITUICAO = 'MSG-076';
    const MSG_VISUALIZACAO_PEDIDOS_APENAS_ASSESSOR = 'MSG-077';
    const MSG_NUMERO_CHAPA_JA_CADASTRADO = 'MSG-078';
    const MSG_HISTORICO_EXTRATO_CONSELHEIROS_EM_GERACAO = 'MSG-079';
    const MSG_SEM_MERMISSAO_VISUALIZACAO_ATIV_SELECIONADA = 'MSG-077';
    const MSG_PROFISSIONAL_NAO_PARTICIPA_NH_CHAPA__PROCESSO_ELEITORAL = 'MSG-080';
    const MSG_PERIODO_CADASTRO_IMPUGNACAO_NAO_VIGENTE_PROCESSO_ELEITORAL = 'MSG-081';
    const MSG_PERIODO_CADASTRO_IMPUGNACAO_NAO_VIGENTE = 'MSG-082';
    const MSG_SELECIONE_NO_MINIMO_UM_ITEM_DECLARACOES = 'MSG-083';
    const MSG_MINIMO_UM_DOC_COMPROBATORIO = 'MSG-084';
    const MSG_PERMISSAO_UPLOAD_MAXIMO = 'MSG-085';
    const MSG_SEM_PEDIDOS_IMPUGNACAO = 'MSG-086';
    const MSG_PEDIDOS_IMPUGNACAO_APENAS_MEMBROS_COMISSAO = 'MSG-087';
    const MSG_PEDIDOS_IMPUGNACAO_APENAS_RESPONSAVEL_CHAPA = 'MSG-088';
    const MSG_TAMANHO_MAXIMO_N_CARACTERES_EXCEDIDAS = 'MSG-089';
    const MSG_VISUALIZACAO_APENAS_ARQUITETOS_URBANISTAS = 'MSG-090';
    const MSG_DUPLICACAO_PEDIDO_IMPUGNACAO = 'MSG-091';
    const MSG_DEFESA_IMPUGNACAO_APENAS_RESPONSAVEL_CHAPA = 'MSG-092';
    const MSG_DEFESA_IMPUGNACAO_NECESSARIO_PEDIDO_IMPUGNACAO_EM_ANALISE = 'MSG-093';
    const MSG_DEFESA_IMPUGNACAO_NECESSARIO_PEDIDO_IMPUGNACAO = 'MSG-094';
    const MSG_DEFESA_IMPUGNACAO_PERIODO_CADASTRO_NAO_ESTA_VIGENTE = 'MSG-095';
    const MSG_PERIODO_VIGENTE_ELEICAO_FECHADO = 'MSG-096';
    const MSG_PERIODO_JULGAMENTO_FINALIZADO = 'MSG-097';
    const MSG_PERIODO_VIGENCIA_FECHADO_PARA_RESPONSAVEL = 'MSG-111';
    const MSG_VISUALIZACAO_NAO_PERMITIDA_ATIV_SELECIONADA = 'MSG-112';
    const MSG_PERIODO_VIGENCIA_FECHADO_ELEICAO_SELECIONADA = 'MSG-113';
    const MSG_SEM_ACESSO_ATIV_SELECIONADA = 'MSG-114';
    const MSG_CADASTRO_COMISSAO_UF_INFORMADA_JA_FOI_CRIADO = 'MSG-115';
    const MSG_CADASTRO_COMISSAO_UF_INFORMADA_NAO_FOI_REALIZADO = 'MSG-116';
    const MSG_CADASTRO_COMISSAO_UFS_CEN_JA_FORAM_CRIADOS = 'MSG-117';
    const MSG_NAO_EXISTE_ELEICAO_VIGENTE = 'MSG-102';
    const MSG_NAO_EXISTE_DENUNCIA_PARA_ELEICAO_VIGENTE = 'MSG-103';
    const MSG_NAO_EXISTE_DEFESA_DENUNCIA = 'MSG-104';
    const MSG_NAO_E_RELATOR_DENUNCIA = 'MSG-105';
    const MSG_ENCAMINHAMENTO_DENUNCIA_PENDENTE = 'MSG-106';
    const MSG_EXISTEM_PROVAS_DESTINATARIO_DENUNCIA = 'MSG-107';
    const MSG_DATA_PASSADA_AUDIENCIA_INSTRUCAO = 'MSG-108';
    const MSG_DATA_INVALIDA_AGENDAMENTO = 'MSG-109';
    const MSG_PRAZO_DEFESA_ENCERRADO = 'MSG-110';
    const MSG_PRAZO_ENVIO_ALEGACOES_FINAIS_ENCERRADO = 'MSG-118';
    const MSG_PRAZO_PRODUCAO_PROVAS_ENCERRADO = 'MSG-119';
    const MSG_DATA_HORA_INVALIDA = 'MSG-120';
    const MSG_DATA_HORA_FUTURA = 'MSG-121';
    const MSG_EXISTE_SOLIC_ALEGACOES_FINAIS_PENDENTES_RESP_DESTINATARIOS = 'MSG-122';
    const MSG_NAO_POSSIVEL_INCLUIR_ENCAMINHAMENTO_ALEGACAO_FINAL_JA_RESPONDIDA_PELAS_PARTES = 'MSG-123';
    const MSG_JULGAMENTO_DENUNCIA_JA_INSERIDO = 'MSG-124';
    const MSG_NAO_POSSO_JULGAR = 'MSG-125';
    const MSG_DENUNCIA_NAO_EXISTE = 'MSG-126';
    const MSG_TIPO_JULGAMENTO_ADMISSIBILIDADE_NAO_EXISTE = 'MSG-127';
    const MSG_NAO_POSSO_INSERIR_RELATOR = 'MSG-128';
    const MSG_MEMBRO_COMISSAO_INVALIDO = 'MSG-129';
    const MSG_DENUNCIA_INVALIDA = 'MSG-130';
    const MSG_NAO_EXISTE_PEDIDO_IMPUGNACAO_RESULTADO = 'MSG-131';
    const MSG_TAMANHO_MAXIMO_PERMITIDO_15_MB = 'MSG-132';
    const MSG_PERIODO_FORA_DE_VIGENCIA_GENERICO =   'MSG-133';
    const MSG_POSSUI_JULGAMENTO_ALEGACAO_CADASTRADO =   'MSG-134';
    const MSG_NENHUM_REGISTRO_ENCONTRADO = 'Nenhum registro encontrado.';
    
    /**
     *
     * @var string
     */
    private $code;

    /**
     *
     * @var array
     *
     * @JMS\Exclude();
     */
    public static $descriptions = array(
        'MSG-001' => "A aplicação encontrou um erro inesperado. Favor contactar o Administrador. Descrição do erro: {0}.",
        'MSG-002' => "Token inválido!",
        'MSG-003' => "Nenhum registro encontrado!",
        'MSG-004' => "Data inicial deve ser menor que a data final!",
        'MSG-005' => "O(s) campo(s) destacado(s) é (são) de preenchimento obrigatório!",
        'MSG-006' => "Ano inválido!",
        'MSG-007' => "Para o arquivo “Resolução” o tamanho máximo permitido é  de 10 MB.",
        'MSG-008' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de PDF!",
        'MSG-009' => "Idade inválida!",
        'MSG-010' => "Permissão de Upload: dois (2) arquivos!",
        'MSG-011' => "Data de Início está divergente com o Período de Vigência do processo eleitoral!",
        'MSG-012' => "Data Fim está divergente com o Período de Vigência do processo eleitoral!",
        'MSG-013' => "Falha de comunicação com a chamada HTTP.",
        'MSG-014' => "A URL deve ser informado!",
        'MSG-015' => "Favor informar os campos obrigatórios.",
        'MSG-016' => "Data de Início está divergente com a data da Atividade Principal!",
        'MSG-017' => "Data Fim está divergente com a data de Vigência!",
        'MSG-018' => "Campo de preenchimento obrigatório",
        'MSG-019' => "Não foi definido parâmetro para esta ação",
        'MSG-020' => "Para o arquivo de publicação, o tamanho máximo permitido é de 10 MB.",
        'MSG-021' => "Já existe E-mail parametrizado para esse tipo.",
        'MSG-022' => "Para os arquivos do Cabeçalho ou do Rodapé, o tamanho máximo permitido é de 10 MB.",
        'MSG-023' => "Largura máxima permitida: {0} px",
        'MSG-024' => "Altura máxima permitida: {0} px",
        'MSG-025' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de png,jpg e jpeg!",
        'MSGI-001' => 'Favor informar pelo menos um filtro de pesquisa.',
        'MSG-026' => "Arquivo não encontrado.",
        'MSG-027' => "Prezado(a) Arquiteto(a), o sr. (a) já confirmou participação em outra Chapa, entre em contato com o responsável pela Chapa, para que sua participação possa ser retirada!",
        'MSG-028' => "Prezado(a) Arquiteto(a), o sr. (a) já foi indicado para participação em uma outra Chapa. Por gentileza, recusar o convite para possibilitar a criação de um nova Chapa!",
        'MSG-029' => "Não existe período vigente para cadastro de chapa.",
        'MSG-030' => "CPF não encontrado na Base de Dados do SICCAU!",
        'MSG-031' => "CPF informado já aceitou participação em outra chapa!",
        'MSG-032' => "O CPF informado já foi incluído nessa Chapa!",
        'MSG-033' => "O CPF informado já foi eleito conselheiro ou suplente de conselheiro do CAU/BR ou de CAU/UF em mandato subsequente!",
        'MSG-034' => "O CPF informado perdeu o mandato de conselheiro do CAU/BR ou de CAU/UF, inclusive na condição de suplente, nos últimos 5 (cinco) anos!",
        'MSG-035' => "O CPF informado possui sanção ético-disciplinar aplicada por decisão transitada em julgado pendente de reabilitação!",
        'MSG-036' => "O CPF informado foi sancionado por infração ético-disciplinar no CAU/UF ou no CAU/BR, desde a reabilitação da sanção até o transcurso do prazo de 3 (três) anos.",
        'MSG-037' => "Prezado (a) arquiteto (a) e urbanista, essa chapa foi cadastrada para uma UF diferente da sua UF de cadastro, portanto, o senhor(a) não poderá fazer parte dessa chapa.",
        'MSG-038' => "O Sistema Eleitoral não possui eleição com chapas criadas!",
        'MSG-039' => "Apenas profissionais podem acessar essa funcionalidade.",
        'MSG-040' => "O tamanho máximo permitido para o arquivo é de 2 MB!",
        'MSG-041' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de .jpg .jpeg .png e .bmp!",
        'MSG-042' => "{0}",
        'MSG-043' => "CPF informado pertence a um membro de UF diferente da chapa! Só será aceito membro pertencente a mesma UF da chapa criada.",
        'MSG-044' => "Prezado(a) Arquiteto(a), ao criar uma Chapa, o sr. (a) deverá obrigatoriamente ser um dos membros! Por gentileza, incluir o seu CPF na lista de membros!",
        'MSG-045' => "Só é permitido a inclusão de até 3 responsáveis!",
        'MSG-046' => "O criador da Chapa IES deve obrigatoriamente ser o Titular. Por gentileza, incluir o seu CPF como titular da Chapa!",
        'MSG-047' => "Para criação de uma chapa, obrigatoriamente terá que ter ao menos um (1) membro para compor a chapa!",
        'MSG-048' => "Permissão de Upload: dois (2) arquivos!",
        'MSG-049' => "A sua UF não possui eleição Ativa, no exato momento!",
        'MSG-050' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato DOC/DOCX!",
        'MSG-051' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato PDF!",
        'MSG-052' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de PDF e DOC/DOCX!",
        'MSG-053' => "O tamanho máximo permitido para cada arquivo é de 10 MB.",
        'MSG-054' => "O CPF informado é devedor de multa referente a processo eleitoral do CAU!",
        'MSG-055' => "Caro(a) Sr.(a), informe ao menos um (1) responsável pela chapa!",
        'MSG-056' => "Para o arquivo, o tamanho máximo permitido é de 40 MB.",
        'MSG-057' => "Tamanho máximo de 10 arquivos excedido!",
        'MSG-058' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de .PDF, .DOC e .DOCX!",
        'MSG-059' => "Formato do arquivo inválido. Somente serão aceitos arquivos no formato de doc/docx, pdf!",
        'MSG-060' => "CPF ou Nome informado, não foi encontrado na Base de Dados do SICCAU!",
        'MSG-061' => 'Prezado (a) arquiteto (a) e urbanista, para ter acesso ao item “Visualizar Chapa” é necessário que o senhor aceite um convite recebido através do menu “Confirmar Participação” para participar de uma chapa eleitoral.',
        'MSG-062' => 'O período de cadastro de chapa não está vigente para nenhum processo eleitoral.',
        'MSG-063' => 'Registro consta como incompleto no SICCAU.',
        'MSG-064' => 'Caro(a) sr. (a), a atividade selecionada está fora do prazo de vigência',
        'MSG-065' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros RESPONSÁVEIS da chapa.',
        'MSG-066' => 'CPF informado não pertence a nenhum dos membros a qual o sr. (a) é um membro RESPONSÁVEL! Pedidos de Substituição serão permitidos apenas para membros que compõem a sua chapa!',
        'MSG-067' => 'Número informado já foi atribuído para outra chapa de mesma UF!',
        'MSG-068' => 'Caro(a) sr(a), o preenchimento dos dois cargos (Titular e Suplente) são de preenchimento obrigatório! Caso o(a) senhor(a), queira substituir apenas 1 dos membros, por gentileza, repetir/informar novamente o CPF do membro que não deve ser substituído.',
        'MSG-069' => 'Caro(a) sr(a), foi verificado que o senhor(a) desabilitou todos os responsáveis da chapa! Por gentileza, informar ao menos 1 responsável.',
        'MSG-070' => 'Caro(a) sr. (a), não encontramos nenhum pedido de substituição a qual o senhor é responsável pela chapa!',
        'MSG-071' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros da Comissão Eleitoral!',
        'MSG-072' => 'Caro(a) sr.(a), não existe pedido de substituição cadastrado para a UF, a qual o senhor é um conselheiro CE/UF!',
        'MSG-073' => 'Caro(a) sr.(a), não existe pedido de substituição cadastrado para a eleição que o senhor é um conselheiro CEN/BR!',
        'MSG-074' => 'Já existe uma solicitação de substituição para o membro informado!',
        'MSG-075' => 'Caro(a) sr. (a), a visualização da atividade selecionada só está disponível para membros da Comissão Eleitoral CEN/BR!',
        'MSG-076' => 'O Sistema Eleitoral não possui eleição com Pedido de Substituição!',
        'MSG-077' => 'Caro(a) sr. (a), você não tem permissão de acesso para visualização da atividade selecionada!',
        'MSG-078' => 'Número informado já foi atribuído para outra chapa!',
        'MSG-079' => 'A geração do extrato ainda está em execução!',
        'MSG-080' => 'O profissional {0} não está participando de nenhuma chapa do processo eleitoral {1}',
        'MSG-081' => 'O período de cadastro de impugnações não está vigente para nenhum processo eleitoral.',
        'MSG-082' => 'Não existe período vigente para cadastro de impugnação.',
        'MSG-083' => 'Selecione no mínimo um item de declarações.',
        'MSG-084' => 'Inclua no mínimo um documento comprobatório.',
        'MSG-085' => 'Permissão de Upload: ({0}) arquivo(s)!',
        'MSG-086' => 'Não há pedidos de impugnação cadastrados.',
        'MSG-087' => 'Para visualizar os pedidos de impugnação é necessário ser membro de comissão na eleição vigente.',
        'MSG-088' => 'Para visualizar os pedidos de impugnação é necessário ser responsável de chapa na eleição vigente.',
        'MSG-089' => 'Prezado(a) tamanho máximo de ({0}) caracteres excedidos.',
        'MSG-090' => 'Prezado(a) somente arquitetos e urbanistas podem cadastrar pedidos de impugnação.',
        'MSG-091' => 'Não é possível duplicar um pedido de impugnação para o mesmo candidato.',
        'MSG-092' => 'Somente o responsável de chapa tem permissão para cadastrar defesa.',
        'MSG-093' => "Para cadastrar uma defesa é necessário que exista um pedido de impugnação em análise.",
        'MSG-094' => "Só é possível cadastrar uma defesa para um pedido de impugnação cadastrado.",
        'MSG-095' => "O período de cadastro de defesa não está vigente para nenhum processo eleitoral.",
        'MSG-096' => 'O período de vigência da ELEIÇÃO está fechado!',
        'MSG-097' => 'Caro(a) sr. (a),  o período da atividade de julgamento foi finalizada!',
        'MSG-098' => 'Arquivo inválido.',
        'MSG-099' => 'Não é possível cadastrar um pedido de denúncia para o mesmo denunciado na eleição atual.',
        'MSG-100' => 'Prezado (a) Arquiteto (a) e Urbanista, foi expirado o prazo para o cadastramento de denúncia.',
        'MSG-101' => 'Não foram encontradas denúncias na eleição selecionada.',
        'MSG-102' => 'Não existe eleição vigente.',
        'MSG-103' => 'Não foram encontradas denúncias na eleição vigente.',
        'MSG-104' => 'Não houve apresentação de defesa para esta denúncia.',
        'MSG-105' => 'O Usuário logado não é o relator desta denúncia.',
        'MSG-106' => 'Este Encaminhamento está Pendente.',
        'MSG-107' => 'Prezado(a), existem pendências na produção de provas do(s) destinatário(s) que impedem o cadastro.',
        'MSG-108' => 'Prezado(a), não é possível agendar data e hora passadas para a audiência de instrução.',
        'MSG-109' => 'Prezado(a), informe uma data e/ou hora válida para o agendamento.',
        'MSG-110' => 'Não foi possível realizar o cadastro porque o prazo de apresentação de defesa encerrou.',
        'MSG-111' => "Caro(a) sr. (a), o período de vigência encontra-se fechado, para eleição a qual o senhor é um dos responsáveis",
        'MSG-112' => "Caro(a) sr. (a), visualização não permitida para a atividade selecionada!",
        'MSG-113' => 'Caro(a) sr. (a), o período de vigência encontra-se fechado, para eleição selecionada.',
        'MSG-114' => 'Caro(a) sr. (a), você não tem permissão de acesso para visualização da atividade selecionada!',
        'MSG-115' => 'O cadastro para a comissão da UF informada, já foi criado!',
        'MSG-116' => 'O cadastro da comissão da UF informada, não foi realizado!',
        'MSG-117' => "O cadastro para as comissões de todas as UF's e CEN já foram criados!",
        'MSG-118' => "Prezado(a), o prazo de envio das alegações finais foi encerrado. Aguarde pela próxima solicitação",
        'MSG-119' => "Prezado(a), o prazo de inserção de provas está encerrado.",
        'MSG-120' => "Prezado(a), informe uma data e/ou hora válida.",
        'MSG-121' => "Prezado(a), a data e hora de registro da audiência não podem ser futuras.",
        'MSG-122' => 'Prezado(a), já existe solicitação de alegações finais pendente de resposta pelo(s) destinatário(s).',
        'MSG-123' => 'Prezado(a), não é possível incluir o encaminhamento porque as alegações finais foram respondidas pelas partes.',
        'MSG-124' => 'Prezado(a), não é possível incluir porque já existe um julgamento.',
        'MSG-125' => 'Não é possível julgar essa denúncia',
        'MSG-126' => 'A denuncia não existe.',
        'MSG-127' => 'O julgamento selecionado é inválido.',
        'MSG-128' => 'Não é possível inserir um relator para essa denúncia.',
        'MSG-129' => 'Membro da comissão é inválido.',
        'MSG-130' => 'Denuncia inválida.',
        'MSG-131' => 'Não existe Pedido de Impugnação de Resultado.',
        'MSG-132' => 'Tamanho máximo permitido 15 MB.',
        'MSG-133' => 'Prezado(a), o período de vigência da atividade no qual o sr. (a) deseja realizar o cadastro está fora de vigência.',
        'MSG-134' => 'Prezado(a), já possui julgamento cadastrado para a impugnação de resultado no qual o sr. (a) deseja realizar o cadastro de alegação.'
    );

    /**'
     *
     * @var array
     */
    private $params;

    /**
     *
     * @var string
     */
    private $stackTrace;

    /**
     *
     * @var string
     */
    private $message;

    /**
     * Construtor da classe.
     *
     * @param
     *            $code
     * @param
     *            $description
     */
    private function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Fábrica de instância de Message.
     *
     * @param
     *            $message
     * @param null $code
     * @param null $params
     * @return Message
     */
    public static function newInstance($message, $code = null, $params = null)
    {
        $message = new Message($code, $message);

        if (!empty($params)) {
            if (is_array($params)) {
                $message->setParams($params);
            } else {
                $message->addParam($params);
            }
        }

        return $message;
    }

    /**
     * Adiciona um novo parâmetro a instância de Message.
     *
     * @param
     *            $param
     */
    public function addParam($param)
    {
        if (empty($this->params)) {
            $this->params = [];
        }

        $this->params[] = $param;
    }

    /**
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     *
     * @return string
     */
    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    /**
     *
     * @param string $stackTrace
     */
    public function setStackTrace($stackTrace)
    {
        $this->stackTrace = $stackTrace;
    }

    /**
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @return mixed|string
     */
    public function __toString()
    {
        return JsonUtils::toJson($this);
    }
}
