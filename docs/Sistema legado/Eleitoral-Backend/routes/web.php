<?php
set_time_limit(0);
ini_set("pcre.backtrack_limit", "1000000000");

use App\Config\AppConfig;
use App\Util\Utils;

/*
 * |--------------------------------------------------------------------------
 * | Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It is a breeze. Simply tell Lumen the URIs it should respond to
 * | and give it the Closure to call when that URI is requested.
 * |
 */

app()->router->get('/', function () {
    return app()->router->app->version();
});

/*
 * |--------------------------------------------------------------------------
 * | Cors options
 * |--------------------------------------------------------------------------
 */
app()->router->options('{all}', function () {
    $response = response()->make('');
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->header('Access-Control-Allow-Headers',
        'Access-Control-Allow-Headers, Authorization, X-Token, X-Requested-With, Content-type');
    $response->header('Access-Control-Max-Age', '3600');
    return $response;
});

/* Rotas públicas */
/*
 * |--------------------------------------------------------------------------
 * | Auth
 * |--------------------------------------------------------------------------
 */

app()->router->post('auth/login', 'AuthController@login');

app()->router->get('chapas/dadosExtratoChapaJson/{idCauUf}', [
    //
    'uses' => 'ChapaEleicaoController@getDadosExtratoChapaJson'
]);

app()->router->get('filiais/{id}/bandeira', [
    'uses' => 'FilialController@getFilialComBandeira'
]);
/*
 * |--------------------------------------------------------------------------
 * | Membros Comissão
 * |--------------------------------------------------------------------------
 */
app()->router->get('membroComissao/validaMembroComissaoEleicaoVigente', [

    'uses' => 'MembroComissaoController@validaMembroComissaoEleicaoVigente'
]);

app()->router->get('termo/imprimir/{id}', [
    'uses' => 'TermoDePosseController@imprimir'
]);

app()->router->get('diploma/imprimir/{id}', [
    'uses' => 'DiplomaEleitoralController@imprimir'
]);

/*
 * |--------------------------------------------------------------------------
 * | Membros Chapa
 * |--------------------------------------------------------------------------
 */
app()->router->get('membrosChapa/documentoRepresentatividade/{idMembro}/download', [

    'uses' => 'MembroChapaController@downloadDocumentoRepresentatividade'
]);

app()->router->get('atualizarFoto', [
    'uses' => 'MembroChapaController@atualizarFoto'
]);

/* Rotas protegidas */
app()->router->group(AppConfig::getMiddleware(), function () {
    /*
     * |--------------------------------------------------------------------------
     * | Calendario
     * |--------------------------------------------------------------------------
     */
    app()->router->get('calendarios', [

        'uses' => 'CalendarioController@getCalendariosPorFiltro'
    ]);

    app()->router->get('calendarios/anos', [

        'uses' => 'CalendarioController@getAnos'
    ]);

    app()->router->get('calendarios/membroComissao', [

        'uses' => 'CalendarioController@getPorMembroComissaoLogado'
    ]);

    app()->router->post('calendarios/anos/filtro', [

        'uses' => 'CalendarioController@getAnosPorFiltro'
    ]);

    app()->router->get('calendarios/concluidos/anos', [

        'uses' => 'CalendarioController@getCalendariosConcluidosAnos'
    ]);

    app()->router->get('calendarios/eleicoes', [

        'uses' => 'CalendarioController@getEleicoes'
    ]);

    app()->router->get('calendarios/tiposProcessos', [

        'uses' => 'CalendarioController@getTipoProcesso'
    ]);

    app()->router->get('calendarios/{id}', [

        'uses' => 'CalendarioController@getPorId'
    ]);

    app()->router->get('calendarios/atividadeSecundaria/{idAtividadeSecundaria}', [

        'uses' => 'CalendarioController@getPorAtividadeSecundaria'
    ]);

    app()->router->get('calendarios/{id}/eleicao', [

        'uses' => 'CalendarioController@getCalendarioEleicaoPorId'
    ]);

    app()->router->post('calendarios/filtro', [

        'uses' => 'CalendarioController@getCalendariosPorFiltro'
    ]);

    app()->router->post('calendarios/salvar', [

        'uses' => 'CalendarioController@salvar'
    ]);

    app()->router->delete('calendarios/{id}/excluir', [

        'uses' => 'CalendarioController@excluir'
    ]);

    app()->router->post('calendarios/inativar', [

        'uses' => 'CalendarioController@inativar'
    ]);

    app()->router->post('calendarios/concluir', [

        'uses' => 'CalendarioController@concluir'
    ]);

    app()->router->get('calendarios/{idCalendario}/prazos', [

        'uses' => 'CalendarioController@getPrazosPorCalendario'
    ]);

    app()->router->get('calendarios/{id}/historico', [

        'uses' => 'CalendarioController@getHistorico'
    ]);

    app()->router->get('calendarios/{id}/numeroMembros', [

        'uses' => 'CalendarioController@getAgrupamentoNumeroMembros'
    ]);

    app()->router->get('calendarios/{idSituacao}/total', [

        'uses' => 'CalendarioController@getTotalCalendariosPorSituacao'
    ]);

    app()->router->get('calendarios/validacao/chapas', [

        'uses' => 'CalendarioController@validarQuantidadeCalendariosComChapa'
    ]);

    app()->router->get('calendarios/comissaoEleitoral/publicacao', [

        'uses' => 'CalendarioController@getCalendariosPublicacaoComissaoEleitoral'
    ]);

    app()->router->get('calendarios/comissaoEleitoral/publicacao/anos', [

        'uses' => 'CalendarioController@getAnosCalendarioPublicacaoComissaoEleitoral'
    ]);

    app()->router->post('calendarios/comissaoEleitoral/publicacao/filtro', [

        'uses' => 'CalendarioController@getCalendarioPublicacaoComissaoEleitoralPorFiltro'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Atividade Principal Calendario.
     * |--------------------------------------------------------------------------
     */

    app()->router->post('calendarios/{idCalendario}/atividadesPrincipais/filtro', [

        'uses' => 'AtividadePrincipalCalendarioController@getAtividadePrincipalPorCalendarioComFiltro'
    ]);
    app()->router->get('calendarios/{idCalendario}/atividadesPrincipais', [

        'uses' => 'AtividadePrincipalCalendarioController@getAtividadePrincipalPorCalendario'
    ]);
    app()->router->get('atividadesPrincipais', [

        'uses' => 'AtividadePrincipalCalendarioController@getAtividadePrincipal'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Atividade Secundaria Calendario.
     * |--------------------------------------------------------------------------
     */
    app()->router->post('atividadesSecundarias/profissionaisPorCpfNome', [

        'uses' => 'AtividadeSecundariaCalendarioController@getProfissionaisPorCpfNome'
    ]);
    app()->router->get('atividadesSecundarias/{id}', [

        'uses' => 'AtividadeSecundariaCalendarioController@getPorId'
    ]);
    app()->router->get('atividadesSecundarias/{id}/total-resposta-declaracoes', [

        'uses' => 'AtividadeSecundariaCalendarioController@getQuantidadeRespostaDeclaracaoPorAtividade'
    ]);
    app()->router->get('atividadesSecundarias/{idAtividadeSecundaria}/ufs-calendarios', [

        'uses' => 'AtividadeSecundariaCalendarioController@getUfsCalendariosPorAtividadeSecundaria'
    ]);
    app()->router->get('atividadesSecundarias/atividadesDeclaracao/{idDeclaracao}', [

        'uses' => 'AtividadeSecundariaCalendarioController@getQtdDeclaracoesDefinidasPorDeclaracao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Definição de E-mails e/ou Declaração por Atividade Secundária .
     * |--------------------------------------------------------------------------
     */
    app()->router->get('atividadesSecundarias/{id}/definicao-emails', [

        'uses' => 'AtividadeSecundariaCalendarioController@getInformacaoDefinicaoEmails'
    ]);

    app()->router->get('atividadesSecundarias/{id}/definicao-declaracoes', [

        'uses' => 'AtividadeSecundariaCalendarioController@getInformacaoDefinicaoDeclaracoes'
    ]);

    app()->router->post('atividadesSecundarias/definir-emails-declaracoes', [

        'uses' => 'AtividadeSecundariaCalendarioController@definirEmailsDeclaracoesPorAtividadeSecundaria'
    ]);
    app()->router->get('atividadesSecundarias/{id}/declaracao-definida-por-tipo/{idTipoDeclaracao}', [

        'uses' => 'AtividadeSecundariaCalendarioController@getDeclaracaoPorAtividadeSecundariaTipo'
    ]);
    app()->router->get('atividadesSecundarias/{id}/emails', [

        'uses' => 'CorpoEmailController@getEmailsPorAtividadeSecundaria'
    ]);
    app()->router->get('atividadesSecundarias/{id}/declaracoes-atividade', [

        'uses' => 'AtividadeSecundariaCalendarioController@getDeclaracoesPorAtividadeSecundaria'
    ]);

    app()->router->get('emailAtividadeSecundaria/{id}/hasDefinicao', [

        'uses' => 'EmailAtividadeSecundariaController@hasDefinicaoEmail'
    ]);


    /*
     * |--------------------------------------------------------------------------
     * | E-mails.
     * |--------------------------------------------------------------------------
     */
    app()->router->get('email/send', [

        'uses' => 'EmailAtividadeSecundariaController@enviarEmailsAgendados'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Cabeçalho de E-mails.
     * |--------------------------------------------------------------------------
     */
    app()->router->get('cabecalhoEmail/{id}', [

        'uses' => 'CabecalhoEmailController@getPorId'
    ]);
    app()->router->post('cabecalhoEmail', [

        'uses' => 'CabecalhoEmailController@salvar'
    ]);
    app()->router->get('uf', [

        'uses' => 'CabecalhoEmailController@getUfs'
    ]);
    app()->router->get('cabecalhoEmail/{idCabecalhoEmail}/corpoEmails/total', [

        'uses' => 'CabecalhoEmailController@getTotalCorpoEmailVinculado'
    ]);
    app()->router->post('cabecalhoEmail/filtro', [

        'uses' => 'CabecalhoEmailController@getCabecalhoEmailPorFiltro'
    ]);


    /*
     * |--------------------------------------------------------------------------
     * | Corpos de E-mails.
     * |--------------------------------------------------------------------------
     */
    app()->router->get('corpoEmail/{id}', [

        'uses' => 'CorpoEmailController@getPorId'
    ]);
    app()->router->get('corpoEmail', [

        'uses' => 'CorpoEmailController@getCorposEmail'
    ]);
    app()->router->post('corpoEmail/filtro', [

        'uses' => 'CorpoEmailController@getCorposEmailPorFiltro'
    ]);
    app()->router->post('corpoEmail', [

        'uses' => 'CorpoEmailController@salvar'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Informação Comissão Membro
     * |--------------------------------------------------------------------------
     */
    app()->router->post('informacaoComissaoMembro', [

        'uses' => 'InformacaoComissaoMembroController@salvar'
    ]);
    app()->router->post('informacaoComissaoMembro/concluir', [

        'uses' => 'InformacaoComissaoMembroController@concluir'
    ]);
    app()->router->get('informacaoComissaoMembro/{idCalendario}', [

        'uses' => 'InformacaoComissaoMembroController@getPorCalendario'
    ]);
    app()->router->get('informacaoComissaoMembro/{idInformacaoComissao}/historico', [

        'uses' => 'InformacaoComissaoMembroController@getHistoricoPorInformacaoComissao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Documento Comissão Membro
     * |--------------------------------------------------------------------------
     */
    app()->router->post('documentoComissaoMembro', [

        'uses' => 'DocumentoComissaoMembroController@salvar'
    ]);
    app()->router->get('documentoComissaoMembro/{id}', [

        'uses' => 'DocumentoComissaoMembroController@getPorId'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Membros Comissão
     * |--------------------------------------------------------------------------
     */
    app()->router->get('membroComissao/tipoConselheiroProfissionalLogado', [

        'uses' => 'MembroComissaoController@getTipoConselheiroProfissionalLogado'
    ]);
    app()->router->get('membroComissao/situacaoConvite/{idPessoa}', [

        'uses' => 'MembroComissaoController@getStatusMembroComissaoPorIdProfissional'
    ]);
    app()->router->post('membroComissao/aceitarConvite', [

        'uses' => 'MembroComissaoController@aceitarConvite'
    ]);

    app()->router->get('membroComissao/ufs', 'MembroComissaoController@getUfsComissao');

    app()->router->get('membroComissao/convitesPendentes', [

        'uses' => 'MembroComissaoController@enviarEmailsConvitesPendentes'
    ]);
    app()->router->get('membroComissao/{idPessoa}/informacaoComissaoMembro', [

        'uses' => 'MembroComissaoController@getInformacaoPorMembro'
    ]);
    app()->router->get('membroComissao/tipoParticipacao', [

        'uses' => 'MembroComissaoController@getTipoParticipacao'
    ]);
    app()->router->post('membroComissao/salvar', [

        'uses' => 'MembroComissaoController@salvar'
    ]);
    app()->router->get('membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/cauUf/{idCauUf}', [

        'uses' => 'MembroComissaoController@getPorInformacaoComissaoCauUf'
    ]);
    app()->router->post('membroComissao/filtro', [

        'uses' => 'MembroComissaoController@getPorFiltro'
    ]);
    app()->router->get('membroComissao/informacaoComissaoMembro/{idInformacaoComissao}', [

        'uses' => 'MembroComissaoController@getPorInformacaoComissao'
    ]);
    app()->router->get('membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/resumo', [

        'uses' => 'MembroComissaoController@getPorInformacaoComissaoResumo'
    ]);
    app()->router->get('membroComissao/quantidadePorCauUf/{idCauUf}/informacaoComissao/{idInformacaoComissao}', [

        'uses' => 'MembroComissaoController@getTotalMembrosPorCauUf'
    ]);
    app()->router->get('membroComissao/{idMembroComissao}', [

        'uses' => 'MembroComissaoController@getPorId'
    ]);
    app()->router->get('membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/pdf', [

        'uses' => 'MembroComissaoController@gerarDocumentoRelacaoMembros'
    ]);
    app()->router->get('membroComissao/informacaoComissaoMembro/{idInformacaoComissao}/doc', [

        'uses' => 'MembroComissaoController@gerarDocumentoRelacaoMembros'
    ]);
    app()->router->get('membroComissao/declaracao/{idPessoa}', [

        'uses' => 'MembroComissaoController@getDeclaracaoPorIdProfissional'
    ]);
    app()->router->get('membroComissao/{id}/lista', [

        'uses' => 'MembroComissaoController@getListaMembrosPorMembro'
    ]);
    app()->router->get('membroComissao/{id}/cauUf/{idCauUf}/lista', [

        'uses' => 'MembroComissaoController@getListaMembrosPorMembroCauUf'
    ]);

    app()->router->get('membroComissao/validacao/calendario/{idCalendario}/usuario', [

        'uses' => 'MembroComissaoController@validarMembrosComissaoExistentePorCalendarioUsuario'
    ]);

    app()->router->get('membroComissao/coordenadores/chapa/{idChapa}', [

        'uses' => 'MembroComissaoController@getCoordenadoresPorChapaId'
    ]);

    app()->router->get('membroComissao/assessores/chapa/{idChapa}', [

        'uses' => 'MembroComissaoController@getAssessoresPorChapaId'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Arquivo
     * |--------------------------------------------------------------------------
     */
    app()->router->post('arquivo/validacao/pdf', [

        'uses' => 'ArquivoController@validarResolucaoPDF'
    ]);
    app()->router->post('arquivo/validacao/cabecalhoEmail', [

        'uses' => 'ArquivoController@validarAriquivosCabecalhoEmail'
    ]);
    app()->router->post('arquivo/validacao/fotoSinteseCurriculo', [

        'uses' => 'ArquivoController@validarFotoSinteseCurriculo'
    ]);
    app()->router->get('calendarios/arquivo/{idArquivo}/download', [

        'uses' => 'CalendarioController@download'
    ]);
    app()->router->post('declaracao/arquivo/validacao', [

        'uses' => 'ArquivoController@validarArquivoViaDeclaracao'
    ]);
    app()->router->post('arquivo/validarArquivoRespostaDeclaracaoChapa', [

        'uses' => 'ArquivoController@validarArquivoDeclaracaoChapa'
    ]);

    app()->router->post('arquivo/validarArquivo', [

        'uses' => 'ArquivoController@validarArquivo'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Prazos
     * |--------------------------------------------------------------------------
     */
    app()->router->post('calendarios/prazos/salvar', [

        'uses' => 'CalendarioController@salvarPrazos'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Publicações de Comissão Membro
     * |--------------------------------------------------------------------------
     */
    app()->router->post('publicacao/comissaoMembro', [

        'uses' => 'PublicacaoDocumentoController@salvar'
    ]);
    app()->router->get('publicacao/comissaoMembro/{idDocumentoComissao}/gerarPDF', [

        'uses' => 'PublicacaoDocumentoController@gerarPDF'
    ]);

    app()->router->get('publicacao/{id}/download', [

        'uses' => 'PublicacaoDocumentoController@downloadPdf'
    ]);

    app()->router->get('publicacao/comissaoMembro/{idDocumentoComissao}/pdf', [

        'uses' => 'PublicacaoDocumentoController@gerarPDF'
    ]);

    app()->router->get('publicacao/comissaoMembro/{idDocumentoComissao}/doc', [

        'uses' => 'PublicacaoDocumentoController@gerarDocumento'
    ]);
    /*
     * |--------------------------------------------------------------------------
     * | Documentos Eleição
     * |--------------------------------------------------------------------------
     */
    app()->router->get('documentosEleicoes/{idEleicao}', [

        'uses' => 'DocumentoEleicaoController@getDocumentosPorEleicao'
    ]);
    app()->router->post('documentosEleicoes', [

        'uses' => 'DocumentoEleicaoController@salvar'
    ]);
    app()->router->get('documentosEleicoes/{idDocumentoEleicao}/download', [

        'uses' => 'DocumentoEleicaoController@download'
    ]);
    app()->router->post('documentosEleicoes/arquivo/validacao', [

        'uses' => 'ArquivoController@validarArquivoEleicao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * |  Testes de ambiente
     * |--------------------------------------------------------------------------
     */
    app()->router->get('chamarComandoSchedule', 'ArtisanController@chamarComandoSchedule');
    app()->router->get('verificaAgendamentos', 'ArtisanController@verificaAgendamentos');
    app()->router->get('dataAtual', function () {
        dump(Utils::getData());
    });
    app()->router->get('verificaMenusPortal', 'ArtisanController@verificaMenusPortal');
    app()->router->get('recuperaInformacaoTabela/{tabela}', 'ArtisanController@recuperaInformacaoTabela');

    /*
     * |--------------------------------------------------------------------------
     * | Histórico Extrato
     * |--------------------------------------------------------------------------
     */
    app()->router->get('conselheiros/extrato/{idHistoricoExtrato}/gerarZIP', [

        'uses' => 'HistoricoExtratoConselheiroController@gerarDocumentoZIPListaConselheiros'
    ]);
    app()->router->get('conselheiros/extrato/{idHistoricoExtrato}/gerarPDF', [

        'uses' => 'HistoricoExtratoConselheiroController@gerarDocumentoListaConselheiros'
    ]);
    app()->router->get('conselheiros/extrato/{idHistoricoExtrato}/gerarXLS', [

        'uses' => 'HistoricoExtratoConselheiroController@gerarDocumentoXSLListaConselheiros'
    ]);
    app()->router->get('conselheiros/extrato/{idAtividadeSecundaria}', 'HistoricoExtratoConselheiroController@getPorAtividadeSecundaria');


    /*
     * |--------------------------------------------------------------------------
     * | Profissionais/Conselheiros
     * |--------------------------------------------------------------------------
     */
    app()->router->post('conselheiros/total/atualizar', [

        'uses' => 'ParametroConselheiroController@atualizarNumeroConselheiros'
    ]);
    app()->router->post('conselheiros/total/filtro', [

        'uses' => 'ParametroConselheiroController@getParametroConselheiroPorFiltro'
    ]);
    app()->router->post('conselheiros/salvar', [

        'uses' => 'ParametroConselheiroController@salvar'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Chapas Eleição
     * |--------------------------------------------------------------------------
     */

    app()->router->get('chapas/informacaoChapaEleicao/{idCalendario}', [

        'uses' => 'ChapaEleicaoController@getChapasPorCalendario'
    ]);

    app()->router->get('chapas/informacaoChapaEleicao/{idCalendario}/cauUf/{idCauUf}', [

        'uses' => 'ChapaEleicaoController@getChapasQuantidadeMembrosPorCalendarioCauUf'
    ]);

    app()->router->get('chapas/ufs', 'ChapaEleicaoController@getUfsDeChapas');

    app()->router->get('chapas/{idChapaEleicao}/validacaoCauUfConvidado', [

        'uses' => 'ChapaEleicaoController@validarCauUfProfissionalConvidadoChapa'
    ]);

    app()->router->get('chapas/{id}/eleicao', [

        'uses' => 'ChapaEleicaoController@getEleicaoPorChapa'
    ]);

    app()->router->get('chapas/eleicaoVigente', [

        'uses' => 'ChapaEleicaoController@getEleicaoChapaVigente'
    ]);

    app()->router->get('chapas/cadastro', [

        'uses' => 'ChapaEleicaoController@getPorProfissionalLogadoInclusao'
    ]);

    app()->router->get('chapas/acompanhar', [

        'uses' => 'ChapaEleicaoController@getPorMembroProfissionalLogado'
    ]);

    app()->router->get('chapas/substituicao', [

        'uses' => 'ChapaEleicaoController@getChapaParaSubstituicao'
    ]);

    app()->router->get('chapas/{id}', [

        'uses' => 'ChapaEleicaoController@getPorId'
    ]);

    app()->router->get('chapas/{id}/comMembros', [

        'uses' => 'ChapaEleicaoController@getPorIdComMembros'
    ]);


    app()->router->post('chapas/salvar', [

        'uses' => 'ChapaEleicaoController@salvar'
    ]);

    app()->router->post('chapas/alterarPlataforma', [

        'uses' => 'ChapaEleicaoController@alterarPlataforma'
    ]);

    app()->router->get('chapas/retificacoesPlataforma/{idChapa}', [

        'uses' => 'ChapaEleicaoController@getRetificacoesPlataforma'
    ]);

    app()->router->get('chapas/verificaRetificacoesPlataforma/{idChapa}', [

        'uses' => 'ChapaEleicaoController@verificaRetificacoesPlataforma'
    ]);

    app()->router->post('chapas/salvarNumeroChapa', [

        'uses' => 'ChapaEleicaoController@salvarNumeroChapa'
    ]);

    app()->router->post('chapas/{id}/salvarMembros', [

        'uses' => 'ChapaEleicaoController@salvarMembros'
    ]);

    app()->router->post('chapas/{id}/buscaSubstituto', [

        'uses' => 'ChapaEleicaoController@buscaSubstituto'
    ]);

    app()->router->post('chapas/{id}/confirmarChapa', [

        'uses' => 'ChapaEleicaoController@confirmarChapa'
    ]);

    app()->router->post('chapas/{id}/atualizarChapa', [

        'uses' => 'ChapaEleicaoController@atualizarChapa'
    ]);

    app()->router->delete('chapas/{id}/excluir', [

        'uses' => 'ChapaEleicaoController@excluir'
    ]);

    app()->router->post('chapas/{idChapaEleicao}/excluirComJustificativa', [

        'uses' => 'ChapaEleicaoController@excluirComJustificativa'
    ]);

    app()->router->post('chapas/{id}/incluirMembro', [

        'uses' => 'ChapaEleicaoController@incluirMembro'
    ]);

    app()->router->post('chapas/{idChapaEleicao}/alterarStatus', [

        'uses' => 'ChapaEleicaoController@alterarStatus'
    ]);

    app()->router->get('chapas/{idCalendario}/historico', [

        'uses' => 'ChapaEleicaoController@getHistorico'
    ]);

    app()->router->get('chapas/{idCalendario}/gerarPDFExtratoQuantidadeChapa', [

        'uses' => 'ChapaEleicaoController@gerarDocumentoPDFExtratoQuantidadeChapa'
    ]);

    app()->router->get('membros/download-foto/{idMembro}', [
        'uses' => 'ChapaEleicaoController@downloadFotoMembro'
    ]);

    app()->router->get('chapas/{idCalendario}/gerarXMLChapas/{statusChapaJulgamentoFinal}', [

        'uses' => 'ChapaEleicaoController@gerarXMLChapas'
    ]);

    app()->router->get('chapas/{idCalendario}/gerarCSVChapas/{statusChapaJulgamentoFinal}', [

        'uses' => 'ChapaEleicaoController@gerarCSVChapas'
    ]);

    app()->router->get('chapas/{idCalendario}/gerarCSVChapasPorUf/{idCauUf}', [
        'uses' => 'ChapaEleicaoController@gerarCSVChapasPorUf'
    ]);

    app()->router->get('chapas/{idCalendario}/gerarCSVChapasTrePorUf/{idCauUf}', [
        'uses' => 'ChapaEleicaoController@gerarCSVChapasTrePorUf'
    ]);

    app()->router->post('chapas/{idCalendario}/gerarDocumentoPDFExtratoChapa', [

        'uses' => 'ChapaEleicaoController@gerarDocumentoPDFExtratoChapa'
    ]);

    app()->router->post('chapas/{idCalendario}/dadosParaExtratoChapa', [

        'uses' => 'ChapaEleicaoController@getDadosParaExtratoChapa'
    ]);

    app()->router->post('chapas/gerarDadosExtratoChapaJson', [

        'uses' => 'ChapaEleicaoController@gerarDadosExtratoChapaJson'
    ]);

    app()->router->get('chapas/gerarDadosExtratoChapaJson/teste', [

        'uses' => 'ChapaEleicaoController@teste'
    ]);

    app()->router->get('chapas/quantidades/{idCalendario}', [

        'uses' => 'ChapaEleicaoController@getQuantidadeChapasPorCalendario'
    ]);

    app()->router->get('chapas/membroComissao/quantidades', [

        'uses' => 'ChapaEleicaoController@getQuantidadeChapasPorMembroComissao'
    ]);

    app()->router->get('chapas/{idChapa}/pedidosSolicitados', [

        'uses' => 'ChapaEleicaoController@getPedidosSolicitadosPorIdChapa'
    ]);

    app()->router->get('chapas/{idChapa}/infoPlataformaChapa', [

        'uses' => 'ChapaEleicaoController@getPlataformaAndMeiosPropaganda'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Membros Chapa
     * |--------------------------------------------------------------------------
     */
    app()->router->get('membrosChapa/convitesRecebidos', [

        'uses' => 'MembroChapaController@getConvitesPorUsuarioLogado'
    ]);

    app()->router->get('membrosChapa/{id}/enviarEmailPendencias', [

        'uses' => 'MembroChapaController@enviarEmailPendencias'
    ]);

    app()->router->post('membrosChapa/aceitarConvite', [

        'uses' => 'MembroChapaController@aceitarConvite'
    ]);

    app()->router->post('membrosChapa/alterarDadosCurriculo', [

        'uses' => 'MembroChapaController@alterarDadosCurriculo'
    ]);

    app()->router->post('membrosChapa/rejeitarConvite', [

        'uses' => 'MembroChapaController@rejeitarConvite'
    ]);

    app()->router->get('membrosChapa/{id}/reenviarConvite', [

        'uses' => 'MembroChapaController@reenviarConvite'
    ]);

    app()->router->post('membrosChapa/{id}/excluir', [

        'uses' => 'MembroChapaController@excluir'
    ]);

    app()->router->post('membrosChapa/responsavel/excluirMembro/{id}', [

        'uses' => 'MembroChapaController@excluirByResponsavelChapa'
    ]);

    app()->router->post('membrosChapa/{id}/alterarSituacaoResponsavel', [

        'uses' => 'MembroChapaController@alterarSituacaoResponsavel'
    ]);

    app()->router->post('membrosChapa/{idMembroChapa}/alterarStatusConvite', [

        'uses' => 'MembroChapaController@alterarStatusConvite'
    ]);

    app()->router->post('membrosChapa/{idMembroChapa}/alterarStatusValidacao', [

        'uses' => 'MembroChapaController@alterarStatusValidacao'
    ]);

    app()->router->get('membrosChapa/documentoComprobatorio/{idDocumento}/download', [

        'uses' => 'MembroChapaController@downloadDocumentoComprobatorio'
    ]);

    app()->router->get('membrosChapa/{id}/detalhar', [

        'uses' => 'MembroChapaController@detalhar'
    ]);

    app()->router->get('membrosChapa/busca-substituicao/{idProfissional}', [

        'uses' => 'MembroChapaController@getMembrosParaSubstituicao'
    ]);

    app()->router->get('responsaveisChapa/{idChapa}', [

        'uses' => 'MembroChapaController@getResponsaveisChapaPorIdChapa'
    ]);

    app()->router->post('membrosChapa/setStatusEleito', [
        'uses' => 'MembroChapaController@setStatusEleito'
    ]);

    app()->router->post('membrosChapa/getEleitoByFilter', [
        'uses' => 'MembroChapaController@getEleitoByFilter'
    ]);

    app()->router->post('membrosChapa/getPresidenteUf', [
        'uses' => 'MembroChapaController@getPresidenteUf'
    ]);
    
    /*
     * |--------------------------------------------------------------------------
     * | Parametro Conselheiro
     * |--------------------------------------------------------------------------
     */
    app()->router->get('atividadesSecundarias/atualizar/conselheiros', [

        'uses' => 'ParametroConselheiroController@atualizarConselheiroAutomatico'
    ]);
    app()->router->post('profissionais/total/filtro', [

        'uses' => 'ParametroConselheiroController@getParametroConselheiroPorFiltro'
    ]);
    app()->router->get('conselheiros/atividadeSecundaria/{id}/historico', [

        'uses' => 'ParametroConselheiroController@getHistorico'
    ]);
    app()->router->get('conselheiros/atividadeSecundaria/{id}/gerarXLS', [

        'uses' => 'ParametroConselheiroController@gerarDocumentoXSLListaConselheiros'
    ]);
    app()->router->get('conselheiros/atividadeSecundaria/{id}/gerarPDF', [

        'uses' => 'ParametroConselheiroController@gerarDocumentoPDFListaConselheiros'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Gerar Documento da Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->post('denuncia/gerarDocumento', [

        'uses' => 'DenunciaController@gerarDocumento'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->get('tiposDenuncia', 'DenunciaController@getTiposDenuncia');
    app()->router->post('membroComissao/informacaoComissaoMembro/filtro', 'MembroComissaoController@getComissaoPorFiltro');
    app()->router->post('membrosChapa/filtro', 'MembroChapaController@getMembrosChapasPorFiltro');
    app()->router->get('atividadesSecundarias/nivelPrincipal/{nivelPrincipal}/nivelSecundaria/{nivelSecundaria}', 'AtividadeSecundariaCalendarioController@getAtividadeSecundariaAtivaPorNivel');
    app()->router->get('chapas/cauUf/{idCauUf}', 'ChapaEleicaoController@getPorCauUf');
    app()->router->post('denuncia/salvar', 'DenunciaController@salvar');
    app()->router->post('arquivo/validarArquivoDenuncia', 'ArquivoController@validarArquivoDenuncia');
    app()->router->get('filiais/ies', 'FilialController@getFiliaisIES');

//Denúncias agrupadas por UF
    app()->router->get('denuncias/pessoa/{idPessoa}/agrupadoUf', 'DenunciaController@getAgrupadaDenunciaPorPessoaUF');
    app()->router->get('denuncias/pessoa/{idPessoa}/cauUf/{idCauUf}', 'DenunciaController@getDenunciaLista');
    app()->router->get('denuncia/arquivo/{idArquivo}/download', 'DenunciaController@download');
    app()->router->get('denuncia/inadmitida/arquivo/{idArquivo}/download', 'DenunciaController@downloadInadmitida');

    app()->router->get('denunciaChapa/totalDenunciaAgrupadoUf/{idPessoa}', 'DenunciaChapaController@getTotalDenunciaChapaPorUF');
    app()->router->get('denunciaMembroChapa/totalDenunciaAgrupadoUf/{idPessoa}', 'DenunciaMembroChapaController@getTotalDenunciaMembroChapaPorUF');
    app()->router->get('denunciaMembroComissao/totalDenunciaAgrupadoUf/{idPessoa}', 'DenunciaMembroComissaoController@getTotalDenunciaMembroChapaPorUF');

//Lista de Denúncias por UF
    app()->router->get('denunciaChapa/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}', 'DenunciaChapaController@getListaDenunciaChapaPorUF');
    app()->router->get('denunciaMembroChapa/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}', 'DenunciaMembroChapaController@getListaDenunciaMembroChapaPorUF');
    app()->router->get('denunciaMembroComissao/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}', 'DenunciaMembroComissaoController@getListaDenunciaMembroComissaoPorUF');

//Dados da Denúncias
    app()->router->get('denunciaMembroChapa/dadosDenuncia/{idPessoa}/tipoDenuncia/{idTipoDenuncia}', 'DenunciaMembroChapaController@getDadosDenunciaMembroChapa');
    app()->router->get('denunciaChapa/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}', 'DenunciaChapaController@getListaDenunciaChapaPorUF');
    app()->router->get('denunciaMembroComissao/listaDenunciaAgrupadoUf/{idPessoa}/cauUf/{idCauUf}', 'DenunciaMembroComissaoController@getListaDenunciaMembroComissaoPorUF');

//GERAL
    app()->router->get('denuncia/listaDenunciaPessoaUF/{idPessoa}/cauUF/{idCauUF}', 'DenunciaController@getListaDenunciaPessoaUF');

    app()->router->get('denuncia/profissional', 'DenunciaController@getPorProfissional');
    app()->router->get('denuncia/{idDenuncia}', 'DenunciaController@getVisualizarDenuncia');
    app()->router->get('denuncia/{idDenuncia}/condicao', 'DenunciaController@getCondicaoDenuncia');
    app()->router->get('denuncia/{idDenuncia}/abasDisponiveis', 'DenunciaController@getAbasDisponiveisByIdDenuncia');
    app()->router->get('denuncia/{idDenuncia}/validaProfissionalLogado', 'DenunciaController@validaProfissionalLogadoPorIdDenuncia');

    app()->router->get('teste/email/{idDenuncia}', 'DenunciaController@enviarEmailResponsaveis');

//Denúncias UF por Atividade Secundaria
    app()->router->get('denuncias/calendario/{idCalendario}/agrupamentoUf', 'DenunciaController@getAgrupamentoDenunciaUfPorCalendario');
    app()->router->get('denuncias/cauUf/{idCauUf}/detalhamentoDenunciaUF', 'DenunciaController@getDenunciasPorCauUf');
    app()->router->get('denuncias/calendario/{idCalendario}/cauUf/{idCauUf}/detalhamentoDenunciaUF', 'DenunciaController@getDenunciasPorCalendarioCauUf');
    app()->router->get('denuncias/cauUf/{idCauUf}/detalhamentoDenunciaUfPessoa', 'DenunciaController@getDenunciasPorCauUfPessoa');

//Denuncias Comissao
    app()->router->get('denuncias/detalhamentoDenunciaRelatoriaProfissional', 'DenunciaController@getDenunciasRelatoriaPorProfissional');
    app()->router->get('denuncias/comissao/agrupadoUf', 'DenunciaController@getAgrupadaDenunciaComissaoUF');
    app()->router->get('denuncias/comissao/cauUf/{idCauUf}/detalhamentoDenunciaUfPessoa', 'DenunciaController@getDenunciasComissaoPorCauUfPessoa');
    app()->router->get('denuncias/comissao/admissibilidade', 'DenunciaController@getDenunciaComissaoAdmissibilidade');

    app()->router->get('denuncias/comissao/membroComissao', 'DenunciaController@getMembroComissaoDenunciaPorUsuario');

    /*
     * |--------------------------------------------------------------------------
     * | Pedido Substituição Chapa
     * |--------------------------------------------------------------------------
     */
    app()->router->get('pedidosSubstituicaoChapa/quantidadePedidosParaCadaUf[/{idCalendario}]', [

        'uses' => 'PedidoSubstituicaoChapaController@getQuantidadePedidosParaCadaUf'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/eleicoes', [

        'uses' => 'PedidoSubstituicaoChapaController@getEleicoesComPedido'
    ]);
    app()->router->post('pedidosSubstituicaoChapa/salvar', [

        'uses' => 'PedidoSubstituicaoChapaController@salvar'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/calendario/{idCalendario}/pedidosPorUf[/{idCauUF}]', [

        'uses' => 'PedidoSubstituicaoChapaController@getPedidosPorCalendarioUf'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/pedidosPorUf[/{idCauUF}]', [

        'uses' => 'PedidoSubstituicaoChapaController@getPedidosPorUf'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/pedidosChapaPorResponsavelChapa', [
        //
        'uses' => 'PedidoSubstituicaoChapaController@getPedidosChapaPorResponsavelChapa'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/{id}/download', [

        'uses' => 'PedidoSubstituicaoChapaController@downloadDocumento'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/{id}', [
        //
        'uses' => 'PedidoSubstituicaoChapaController@getPorId'
    ]);
    app()->router->get('pedidosSubstituicaoChapa/{idPedidoSubstituicao}/pdf', [

        'uses' => 'PedidoSubstituicaoChapaController@getDocumentoPDFPedidoSubstituicaoMembro'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Filial
     * |--------------------------------------------------------------------------
     */
    app()->router->get('filiais/bandeira', [
        'uses' => 'FilialController@getFiliaisComBandeiras'
    ]);

    app()->router->get('filiais/bandeira/calendario/{idCalendario}', [
        'uses' => 'FilialController@getFiliaisComBandeirasPorCalendario'
    ]);
    app()->router->get('filiais/calendario/{idCalendario}/comissao', [
        'uses' => 'FilialController@getFiliaisMembrosNaoCadastradosPorCalendario'
    ]);
    app()->router->get('profissionais/testeDownloadZipExtrato', [
        'uses' => 'ProfissionalController@getDownloadZipExtrato'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Profissionais
     * |--------------------------------------------------------------------------
     */
    app()->router->post('profissionais/filtro', [

        'uses' => 'ProfissionalController@getProfissionaisPorFiltro'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Admitir / Inadimitir
     * |--------------------------------------------------------------------------
     */
    app()->router->post('denuncia/admitir', 'DenunciaController@admitir');
    app()->router->post('denuncia/inadmitir', 'DenunciaController@inadmitir');
    app()->router->get('membroComissao/uf/{idCauUf}/denuncia/{denuncia}', 'MembroComissaoController@getMembrosComissaoPorUf');
    app()->router->post('denuncia/relator', 'DenunciaController@relator');
    app()->router->post('denuncia/recurso', 'DenunciaController@recurso');

    /*
     * |--------------------------------------------------------------------------
     * | Validar Acesso Denuncia.
     * |--------------------------------------------------------------------------
     */
    app()->router->get('membroComissao/denuncia/{idDenuncia}/validarAcessoMembro', 'MembroComissaoController@validarMembroComissaoPorDenuncia');
    app()->router->get('denuncias/denuncia/{idDenuncia}/validarAcessoAcompanhar', 'DenunciaController@validarAcessoDenunciaPorDenuncia');
    app()->router->get('denuncia/{idDenuncia}/validarAcessoAcompanharCorporativo', 'DenunciaController@validarAcessoDenunciaCorporativoPorDenuncia');

    /*
     * |--------------------------------------------------------------------------
     * | Defender Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->post('denuncia/defender', 'DenunciaController@defesaDenuncia');
    app()->router->get('denunciaDefesa/denunciado', 'DenunciaDefesaController@getPorDenunciado');
    app()->router->get('denunciaDefesa/arquivo/{idArquivo}/download', 'DenunciaDefesaController@download');

    app()->router->get('denunciaDefesa/validaPrazoDefesaDenuncia', 'DenunciaDefesaController@validaPrazoDefesaDenuncia');

//Julgar Admissibilidade
    app()->router->post('denuncia/{idDenuncia}/julgar_admissibilidade', 'JulgamentoAdmissibilidadeController@salvar');
    app()->router->get('denuncia/julgamento_admissibilidade/arquivo/{idArquivo}/download', 'JulgamentoAdmissibilidadeController@download');

//Recurso Jugamento Admissibilidade
    app()->router->post('denuncia/recurso-julgamento-admissibilidade', 'RecursoJulgamentoAdmissibilidadeController@salvar');
    app()->router->get('denuncia/recurso/validar-prazo-recurso-julgamento-admissibilidade/{idJulgamentoAdmissibilidade}', 'RecursoJulgamentoAdmissibilidadeController@validarPrazo');
    app()->router->get('denuncia/recurso/visualizar/{idRecurso}', 'RecursoJulgamentoAdmissibilidadeController@visualizar');
    app()->router->get('denuncia/recurso/arquivo/{idArquivo}', 'RecursoJulgamentoAdmissibilidadeController@download');

//Jugamento Recurso Admissibilidade
    app()->router->post('denuncia/julgamento-recurso-admissibilidade', 'JulgamentoRecursoAdmissibilidadeController@salvar');
    app()->router->get('denuncia/julgamento-recurso-admissibilidade/arquivo/{idArquivo}', 'JulgamentoRecursoAdmissibilidadeController@download');

//Inserir Relator
    app()->router->post('denuncia/{idDenuncia}/relator', 'DenunciaAdmitidaController@inserirRelator');
    app()->router->get('denuncia/{idDenuncia}/relator/create', 'DenunciaAdmitidaController@createRelator');

    /*
     * |--------------------------------------------------------------------------
     * | Enviar E-mail
     * |--------------------------------------------------------------------------
     */
    app()->router->get('email/admitir/{idDenuncia}/tipoDenuncia/{idTipoDenuncia}', 'DenunciaController@emailSend');

    /*
     * |--------------------------------------------------------------------------
     * | Pedido impugnacao
     * |--------------------------------------------------------------------------
     */

    app()->router->get('pedidosImpugnacao/atividadeSecundaria', [

        'uses' => 'PedidoImpugnacaoController@getAtividadeSecundariaCadastroPedidoImpugnacao'
    ]);
    app()->router->get('pedidosImpugnacao/consultarMembroChapa/{idProfissional}', [

        'uses' => 'MembroChapaController@getMembroParaImpugnacao'
    ]);
    app()->router->post('pedidosImpugnacao/salvar', [

        'uses' => 'PedidoImpugnacaoController@salvar'
    ]);
    app()->router->get('pedidosImpugnacao/documento/{idArquivoPedidoImpugnacao}/download', [

        'uses' => 'PedidoImpugnacaoController@downloadDocumento'
    ]);
    app()->router->get('pedidosImpugnacao/quantidadePedidosParaCadaUf[/{idCalendario}]', [

        'uses' => 'PedidoImpugnacaoController@getQuantidadePedidosParaCadaUf'
    ]);
    app()->router->get('pedidosImpugnacao/quantidadePedidosParaCadaUfSolicitante[/{idCalendario}]', [

        'uses' => 'PedidoImpugnacaoController@getQuantidadePedidosParaCadaUfSolicitante'
    ]);

    app()->router->get('pedidosImpugnacao/pedidosPorUf/{idCauUF}', [

        'uses' => 'PedidoImpugnacaoController@getPedidosPorUf'
    ]);

    app()->router->get('pedidosImpugnacao/calendario/{idCalendario}/pedidosPorUf[/{idCauUF}]', [

        'uses' => 'PedidoImpugnacaoController@getPedidosPorCalendarioUf'
    ]);

    app()->router->get('pedidosImpugnacao/pedidosPorResponsavelChapa', [

        'uses' => 'PedidoImpugnacaoController@getPedidosPorResponsavelChapa'
    ]);

    app()->router->get('pedidosImpugnacao/pedidosPorProfissionalSolicitante/{idCauUf}', [

        'uses' => 'PedidoImpugnacaoController@getPedidosPorProfissionalSolicitante'
    ]);

    app()->router->get('pedidosImpugnacao/eleicoes', [

        'uses' => 'PedidoImpugnacaoController@getEleicoesComPedido'
    ]);
    app()->router->get('pedidosImpugnacao/{id}', [

        'uses' => 'PedidoImpugnacaoController@getPorId'
    ]);
    app()->router->get('pedidosImpugnacao/{id}/chapaEleicao', [

        'uses' => 'PedidoImpugnacaoController@getChapaEleicapPorPedido'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Defesa Pedido Impugnacao
     * |--------------------------------------------------------------------------
     */
    app()->router->post('defesaImpugnacao/salvar', [

        'uses' => 'DefesaImpugnacaoController@salvar'
    ]);
    app()->router->get('defesaImpugnacao/{id}', [

        'uses' => 'DefesaImpugnacaoController@getPorId'
    ]);
    app()->router->get('defesaImpugnacao/pedidosImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'DefesaImpugnacaoController@getPorPedidoImpugnacao'
    ]);
    app()->router->get('defesaImpugnacao/documento/{idArquivoDefesaImpugnacao}/download', [

        'uses' => 'DefesaImpugnacaoController@downloadDocumento'
    ]);
    app()->router->get('defesaImpugnacao/pedidosImpugnacao/{idPedidoImpugnacao}/validarProfissional', [

        'uses' => 'DefesaImpugnacaoController@getDefesaImpugnacaoValidacaoAcessoProfissional'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Pedido de Substituição
     * |--------------------------------------------------------------------------
     */
    app()->router->get('julgamentosSubstituicao/{id}/download', [

        'uses' => 'JulgamentoSubstituicaoController@download'
    ]);
    app()->router->get('julgamentosSubstituicao/{idPedidoSubstituicao}/atividadeSecundariaCadastro', [

        'uses' => 'JulgamentoSubstituicaoController@getAtividadeSecundariaCadastroJulgamento'
    ]);
    app()->router->post('julgamentosSubstituicao/salvar', [

        'uses' => 'JulgamentoSubstituicaoController@salvar'
    ]);
    app()->router->get('julgamentosSubstituicao/{id}/pdf', [

        'uses' => 'JulgamentoSubstituicaoController@getDocumentoPDFJulgamentoSubstituicao'
    ]);
    app()->router->get('julgamentosSubstituicao/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoSubstituicaoController@getPorPedidoSubstituicao'
    ]);

    app()->router->get('julgamentosSubstituicao/responsavelChapa/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoSubstituicaoController@getPorPedidoSubstituicaoResponsavelChapa'
    ]);

    app()->router->get('julgamentosSubstituicao/membroComissao/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoSubstituicaoController@getPorPedidoSubstituicaoMembroComissao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Pedido de Impugnação
     * |--------------------------------------------------------------------------
     */
    app()->router->get('julgamentosImpugnacao/{id}/download', [

        'uses' => 'JulgamentoImpugnacaoController@download'
    ]);

    app()->router->get('julgamentosImpugnacao/{idPedidoImpugnacao}/atividadeSecundariaCadastro', [

        'uses' => 'JulgamentoImpugnacaoController@getAtividadeSecundariaCadastroJulgamento'
    ]);

    app()->router->get('julgamentosImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoImpugnacaoController@getPorPedidoImpugnacao'
    ]);

    app()->router->get('julgamentosImpugnacao/responsavel/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoImpugnacaoController@getPorPedidoImpugnacaoResponsavel'
    ]);

    app()->router->get('julgamentosImpugnacao/membroComissao/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoImpugnacaoController@getPorPedidoImpugnacaoMembroComissao'
    ]);

    app()->router->post('julgamentosImpugnacao/salvar', [

        'uses' => 'JulgamentoImpugnacaoController@salvar'
    ]);
    app()->router->get('julgamentosImpugnacao/{id}/pdf', [

        'uses' => 'JulgamentoImpugnacaoController@getDocumentoPDFJulgamentoImpugnacao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Recurso do julgamento substituição 1ª instância
     * |--------------------------------------------------------------------------
     */
    app()->router->get('recursosSubstituicao/{id}/download', [

        'uses' => 'RecursoSubstituicaoController@download'
    ]);

    app()->router->post('recursosSubstituicao/salvar', [

        'uses' => 'RecursoSubstituicaoController@salvar'
    ]);

    app()->router->get('recursosSubstituicao/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'RecursoSubstituicaoController@getPorPedidoSubstituicao'
    ]);

    app()->router->get('recursosSubstituicao/{idPedidoSubstituicao}/atividadeSecundariaRecurso', [

        'uses' => 'RecursoSubstituicaoController@getAtividadeSecundariaRecursoPorPedidoSubstituicao'
    ]);
    /*
     * |--------------------------------------------------------------------------
     * | Recurso do julgamento impugnacao 1ª instância
     * |--------------------------------------------------------------------------
     */
    app()->router->post('recursosImpugnacao/salvar', [

        'uses' => 'RecursoImpugnacaoController@salvar'
    ]);

    app()->router->get('recursosImpugnacao/{id}/download', [

        'uses' => 'RecursoImpugnacaoController@download'
    ]);

    app()->router->get('recursosImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}/tipoSolicitacao/{idTipoSolicitacao}', [

        'uses' => 'RecursoImpugnacaoController@getPorPedidoImpugnacaoAndTipoSolicitacao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso Substituição (Julgamento 2ª instância)
     * |--------------------------------------------------------------------------
     */
    app()->router->get('julgamentosRecursoSubstituicao/{id}/download', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@download'
    ]);
    app()->router->get('julgamentosRecursoSubstituicao/{idPedidoSubstituicao}/atividadeSecundariaJulgamento', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@getAtividadeSecundariaJulgamentoRecurso'
    ]);
    app()->router->post('julgamentosRecursoSubstituicao/salvar', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@salvar'
    ]);
    app()->router->get('julgamentosRecursoSubstituicao/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@getPorPedidoSubstituicao'
    ]);
    app()->router->get('julgamentosRecursoSubstituicao/membroComissao/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@getPorPedidoSubstituicaoMembroComissao'
    ]);
    app()->router->get('julgamentosRecursoSubstituicao/responsavelChapa/pedidoSubstituicao/{idPedidoSubstituicao}', [

        'uses' => 'JulgamentoRecursoSubstituicaoController@getPorPedidoSubstituicaoResponsavelChapa'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Substituição Impugnacão
     * |--------------------------------------------------------------------------
     */

    app()->router->post('substituicaoImpugnacao/buscaSubstituto', [

        'uses' => 'SubstituicaoImpugnacaoController@buscaSubstituto'
    ]);

    app()->router->get('substituicaoImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'SubstituicaoImpugnacaoController@getPorPedidoImpugnacao'
    ]);

    app()->router->post('substituicaoImpugnacao/salvar', [

        'uses' => 'SubstituicaoImpugnacaoController@salvar'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso Impugnação (Julgamento 2ª instância)
     * |--------------------------------------------------------------------------
     */
    app()->router->get('julgamentosRecursoImpugnacao/{id}/download', [

        'uses' => 'JulgamentoRecursoImpugnacaoController@download'
    ]);
    app()->router->post('julgamentosRecursoImpugnacao/salvar', [

        'uses' => 'JulgamentoRecursoImpugnacaoController@salvar'
    ]);
    app()->router->get('julgamentosRecursoImpugnacao/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoRecursoImpugnacaoController@getPorPedidoImpugnacao'
    ]);
    app()->router->get('julgamentosRecursoImpugnacao/responsavel/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoRecursoImpugnacaoController@getPorPedidoImpugnacaoResponsavel'
    ]);
    app()->router->get('julgamentosRecursoImpugnacao/membroComissao/pedidoImpugnacao/{idPedidoImpugnacao}', [

        'uses' => 'JulgamentoRecursoImpugnacaoController@getPorPedidoImpugnacaoMembroComissao'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso (Julgamento 2ª instância)
     * |--------------------------------------------------------------------------
     */
    app()->router->post('julgamentoRecurso/salvar', [

        'uses' => 'JulgamentoRecursoDenunciaController@salvar'
    ]);
    app()->router->get('julgamentoRecurso/denuncia/{id}', [

        'uses' => 'JulgamentoRecursoDenunciaController@getRecursoRetificadoPorId'
    ]);
    app()->router->get('retificacaoJulgamentoRecurso/{idRetificado}', [

        'uses' => 'JulgamentoRecursoDenunciaController@getRetificadoPorId'
    ]);
    app()->router->get('julgamentoRecurso/{id}/download', [

        'uses' => 'JulgamentoRecursoDenunciaController@download'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Contrarrazao do Recurso da Impugnação
     * |--------------------------------------------------------------------------
     */
    app()->router->get('contrarrazaoRecursoImpugnacao/{id}/download', [

        'uses' => 'ContrarrazaoRecursoImpugnacaoController@download'
    ]);
    app()->router->post('contrarrazaoRecursoImpugnacao/salvar', [

        'uses' => 'ContrarrazaoRecursoImpugnacaoController@salvar'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Encaminhamento Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->get('denuncia/{idDenuncia}/validarAudienciaInstrucaoPendente', [

        'uses' => 'EncaminhamentoDenunciaController@validarAudienciaInstrucaoPendente'
    ]);
    app()->router->get('denuncia/{idDenuncia}/getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente', [

        'uses' => 'EncaminhamentoDenunciaController@getEncaminhamentosProducaoProvasAudienciaInstrucaoPendente'
    ]);
    app()->router->get('encaminhamentoDenuncia/{idEncaminhamento}/testeEmail', [

        'uses' => 'EncaminhamentoDenunciaController@testeEmailEncaminar'
    ]);
    app()->router->post('encaminhamentoDenuncia/salvar', [

        'uses' => 'EncaminhamentoDenunciaController@salvar'
    ]);
    app()->router->get('encaminhamentoDenuncia/tiposEncaminhamento', [

        'uses' => 'EncaminhamentoDenunciaController@getTiposEncaminhamento'
    ]);
    app()->router->get('encaminhamentoDenuncia/tiposEncaminhamento/denuncia/{idDenuncia}', [

        'uses' => 'EncaminhamentoDenunciaController@getTiposEncaminhamentoPorDenuncia'
    ]);
    app()->router->get('encaminhamentosDenuncia/denuncia/{idDenuncia}', [

        'uses' => 'EncaminhamentoDenunciaController@listarEncaminhamentos'
    ]);
    app()->router->get('impedimentoSuspeicao/encaminhamentosDenuncia/{idDenuncia}', [
        //
        'uses' => 'EncaminhamentoDenunciaController@getImpedimentoSuspeicaoPorEncaminhamentoDenuncia'
    ]);

    app()->router->get('denuncia/admitida/arquivo/{idArquivo}/download', [

        'uses' => 'EncaminhamentoDenunciaController@downloadDenunciaAdmitida'
    ]);

    app()->router->post('impedimentoSuspeicao/buscarPorEncaminhamentoEDenuncia', [

        'uses' => 'EncaminhamentoDenunciaController@getImpedimentoSuspeicao'
    ]);
    app()->router->get('denuncia/{idDenuncia}/validarAudienciaInstrucaoPendente', [

        'uses' => 'EncaminhamentoDenunciaController@validarAudienciaInstrucaoPendente'
    ]);

    app()->router->get('encaminhamentosDenuncia/encaminhamento/{idEncaminhamento}', [
        //  
        'uses' => 'EncaminhamentoDenunciaController@getAudienciaInstrucaoEncaminhamento'
    ]);

    app()->router->post('encaminhamentosDenuncia/alegacaoFinal/validarArquivo', [

        'uses' => 'AlegacaoFinalController@validarArquivo'
    ]);

    app()->router->post('encaminhamentosDenuncia/alegacaoFinal/salvar', [

        'uses' => 'AlegacaoFinalController@salvar'
    ]);
    app()->router->post('encaminhamentosDenuncia/alegacaoFinal/validarArquivo', [

        'uses' => 'AlegacaoFinalController@validarArquivo'
    ]);

    app()->router->get('encaminhamentosDenuncia/alegacaoFinal/arquivo/{idArquivo}/download', [

        'uses' => 'AlegacaoFinalController@download'
    ]);

    app()->router->get('encaminhamentosDenuncia/alegacaoFinal/{idEncaminhamento}', [
        //
        'uses' => 'AlegacaoFinalController@getPorEncaminhamento'
    ]);

    app()->router->get('encaminhamentoDenuncia/visualizarProvas/{idEncaminhamento}', ['uses' => 'EncaminhamentoDenunciaController@getProvasEncaminhamento']);
    app()->router->get('encaminhamentoDenuncia/arquivo/{idArquivo}/download', ['uses' => 'EncaminhamentoDenunciaController@download']);
    app()->router->get('denunciaProvas/arquivo/{idArquivo}/download', ['uses' => 'DenunciaProvasController@download']);

    /*
     * |--------------------------------------------------------------------------
     * | Provas Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->post('denuncia/provas/salvar', [

        'uses' => 'DenunciaProvasController@salvar'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * |  Audiencia de instrução
     * |--------------------------------------------------------------------------
     */
    app()->router->get('audienciainstrucao/{id}', [
        // 
        'uses' => 'DenunciaAudienciaInstrucaoController@getPorId'
    ]);

    app()->router->post('denuncia/audienciaInstrucao/salvar', [
        //
        'uses' => 'DenunciaAudienciaInstrucaoController@salvar'
    ]);

    app()->router->get('audienciaInstrucao/documento/{idArquivoAudienciaInstrucao}/download', [

        'uses' => 'DenunciaAudienciaInstrucaoController@download'
    ]);

    app()->router->post('encaminhamentosDenuncia/parecerFinal/salvar', [

        'uses' => 'ParecerFinalController@salvar'
    ]);
    app()->router->get("encaminhamentosDenuncia/parecerFinal/{idEncaminhamento}", [

        'uses' => 'ParecerFinalController@getParecerFinalPorEncaminhamento'
    ]);

    app()->router->get('encaminhamentoDenuncia/parecerFinal/arquivo/{idArquivo}/download', [

        'uses' => 'ParecerFinalController@download'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento da Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->post('julgamentoDenuncia/salvar', [

        'uses' => 'JulgamentoDenunciaController@salvar'
    ]);
    app()->router->get('julgamentoDenuncia/{id}/download', [

        'uses' => 'JulgamentoDenunciaController@download'
    ]);
    app()->router->get('julgamentoDenuncia/tiposJulgamento', [

        'uses' => 'JulgamentoDenunciaController@getTiposJulgamento'
    ]);
    app()->router->get('julgamentoDenuncia/tiposSentencaJulgamento', [

        'uses' => 'JulgamentoDenunciaController@getTiposSentencaJulgamento'
    ]);

    app()->router->get('retificacaoJulgamentoDenuncia/{idRetificacao}', [

        'uses' => 'JulgamentoDenunciaController@getRetificacaoPorId'
    ]);
    app()->router->get('retificacoesJulgamentoDenuncia/denuncia/{idDenuncia}', [

        'uses' => 'JulgamentoDenunciaController@listarRetificacoes'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Contrarrazao do Recurso da Denuncia
     * |--------------------------------------------------------------------------
     */
    app()->router->get('contrarrazaoRecursoDenuncia/{id}/download', [

        'uses' => 'ContrarrazaoRecursoDenunciaController@download'
    ]);
    app()->router->post('contrarrazaoRecursoDenuncia/salvar', [

        'uses' => 'ContrarrazaoRecursoDenunciaController@salvar'
    ]);
    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Final
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'julgamentosFinais'], function () {

        app()->router->get('chapas/calendario/{idCalendario}/cauUf/{idCauUf}', [
            'uses' => 'ChapaEleicaoController@getChapasEleicaoJulgamentoFinalPorCalendarioCauUf'
        ]);

        app()->router->get('chapas/membroComissao[/{idCauUf}]', [
            'uses' => 'ChapaEleicaoController@getChapasEleicaoJulgamentoFinalPorMembroComissao'
        ]);

        app()->router->get('chapaEleicao/responsavelChapa', [
            'uses' => 'ChapaEleicaoController@getChapaEleicaoJulgamentoFinalPorResponsavelChapa'
        ]);

        app()->router->get('chapaEleicao/{idChapa}', [
            'uses' => 'ChapaEleicaoController@getChapaEleicaoJulgamentoFinalPorIdChapa'
        ]);

        app()->router->get('chapaEleicao/membroComissao/{idChapa}', [
            'uses' => 'ChapaEleicaoController@getChapaJulgFinalVerificacaoMembroComissaoPorIdChapa'
        ]);

        app()->router->post('salvar', [
            'uses' => 'JulgamentoFinalController@salvar'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'JulgamentoFinalController@download'
        ]);

        app()->router->get('julgamento/chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoFinalController@getPorChapaEleicao'
        ]);

        app()->router->get('julgamento/responsavelChapa/chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoFinalController@getPorChapaEleicaoResponsavelChapa'
        ]);

        app()->router->get('julgamento/membroComissao/chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoFinalController@getPorChapaEleicaoMembroComissao'
        ]);

        app()->router->get('membrosChapaParaIndicacao/chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoFinalController@getMembrosChapaParaJulgamentoFinal'
        ]);

        app()->router->get('julgamentoSegundaInstancia/chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoFinalController@getJulgamentoSegundaInstanciaPorChapaEleicao'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Substituição Julgamento Final
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'substituicaoJulgamentoFinal'], function () {

        app()->router->post('membroSubstituto', [
            'uses' => 'SubstituicaoJulgamentoFinalController@getMembroChapaSubstituto'
        ]);

        app()->router->post('salvar', [
            'uses' => 'SubstituicaoJulgamentoFinalController@salvar'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'SubstituicaoJulgamentoFinalController@download'
        ]);

        app()->router->get('recurso/{id}/download', [
            'uses' => 'SubstituicaoJulgamentoFinalController@downloadRecurso'
        ]);

        app()->router->get('chapa/{idChapa}', [
            'uses' => 'SubstituicaoJulgamentoFinalController@getPorChapa'
        ]);

        app()->router->post('recurso/salvar', [
            'uses' => 'SubstituicaoJulgamentoFinalController@salvarRecurso'
        ]);

        app()->router->get('{id}', [
            'uses' => 'SubstituicaoJulgamentoFinalController@getPorId'
        ]);

    });

    /*
     * |--------------------------------------------------------------------------
     * | Recurso Julgamento Final
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'recursoJulgamentoFinal'], function () {

        app()->router->post('salvar', [
            'uses' => 'RecursoJulgamentoFinalController@salvar'
        ]);

        app()->router->get('chapa/{idChapaEleicao}', [
            'uses' => 'RecursoJulgamentoFinalController@getRecursoJulgamentoFinalPorChapaEleicao'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'RecursoJulgamentoFinalController@download'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Recurso Julgamento Segunda Instancia Subistituicao
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'recursoSegundoJulgamentoSubstituicao'], function () {

        app()->router->post('salvar', [
            'uses' => 'RecursoSegundoJulgamentoSubstituicaoController@salvar'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'RecursoSegundoJulgamentoSubstituicaoController@download'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Recurso Segunda Instancia
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'julgamentoRecursoSegundaInstancia'], function () {

        app()->router->get('julgamentoFinal/{idJulgamentoFinal}', [
            'uses' => 'JulgamentoSegundaInstanciaRecursoController@getPorJulgamentoFinal'
        ]);

        app()->router->get('chapa/{idChapaEleicao}', [
            'uses' => 'JulgamentoSegundaInstanciaRecursoController@getPorChapaEleicao'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'JulgamentoSegundaInstanciaRecursoController@download'
        ]);

        app()->router->post('salvar', [
            'uses' => 'JulgamentoSegundaInstanciaRecursoController@salvar'
        ]);

        app()->router->get('retificacoes/{idRecursoJulgamentoFinal}', [
            'uses' => 'JulgamentoSegundaInstanciaRecursoController@getRetificacoes'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Substituição Segunda Instancia
     * |--------------------------------------------------------------------------
     */

    app()->router->group(['prefix' => 'julgamentoSubstituicaoSegundaInstancia'], function () {

        app()->router->get('julgamentoFinal/{idJulgamentoFinal}', [
            'uses' => 'JulgamentoSegundaInstanciaSubstituicaoController@getPorJulgamentoFinal'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'JulgamentoSegundaInstanciaSubstituicaoController@download'
        ]);

        app()->router->post('salvar', [
            'uses' => 'JulgamentoSegundaInstanciaSubstituicaoController@salvar'
        ]);

        app()->router->get('retificacoes/{idSubstituicaoJulgamentoFinal}', [
            'uses' => 'JulgamentoSegundaInstanciaSubstituicaoController@getRetificacoes'
        ]);

    });

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Segunda Instancia Recurso do Pedido de Substituição
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'julgamentoRecursoPedidoSubstituicao'], function () {

        app()->router->post('salvar', [
            'uses' => 'JulgamentoRecursoPedidoSubstituicaoController@salvar'
        ]);

        app()->router->get('{id}/download', [
            'uses' => 'JulgamentoRecursoPedidoSubstituicaoController@download'
        ]);

        app()->router->get('retificacoes/{idRecursoPedidoSubstituicao}', [
            'uses' => 'JulgamentoRecursoPedidoSubstituicaoController@getRetificacoes'
        ]);
    });

    app()->router->get('atualizarSintese', [
        'uses' => 'MembroChapaController@atualizarSintese'
    ]);

    /*
     * |--------------------------------------------------------------------------
     * | Impugnacao de Resultado
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'impugnacaoResultado'], function () {

        app()->router->get('cauUf', [
            'uses' => 'ImpugnacaoResultadoController@getCauUf'
        ]);

        app()->router->get('eleicoes', [
            'uses' => 'ImpugnacaoResultadoController@getEleicoesComPedidoImpugnacaoResultado'
        ]);

        app()->router->get('quantidadeParaCadaUf[/{idCalendario}]', [
            'uses' => 'ImpugnacaoResultadoController@getQuantidadeImpugnacaoResultadoParaCadaUf'
        ]);

        app()->router->get('comissao/quantidadeParaCadaUf', [
            'uses' => 'ImpugnacaoResultadoController@getQuantidadeImpugnacaoResultParaCadaUfPorMembroComissao'
        ]);

        app()->router->get('comissao/quantidadeParaCadaUf/impugnante', [
            'uses' => 'ImpugnacaoResultadoController@getQtdImpugnacaoResultadoParaCadaUfPorImpugnante'
        ]);

        app()->router->get('acompanhar/profissional/{idUf}', [
            'uses' => 'ImpugnacaoResultadoController@acompanharParaProfissional'
        ]);

        app()->router->get('acompanhar/chapa', [
            'uses' => 'ImpugnacaoResultadoController@acompanharParaChapa'
        ]);

        app()->router->get('acompanhar/membroComissao/{idUf}', [
            'uses' => 'ImpugnacaoResultadoController@acompanharParaMembroComissao'
        ]);

        app()->router->get(
            'acompanhar/corporativo/calendario/{idCalendario}/uf/{idUf}', [
            'uses' => 'ImpugnacaoResultadoController@acompanharParaCorporativo'
        ]);

        app()->router->get('documento/{id}/download', [
            'uses' => 'ImpugnacaoResultadoController@downloadDocumento'
        ]);

        app()->router->post('salvar', [
            'uses' => 'ImpugnacaoResultadoController@salvar'
        ]);

        app()->router->post('verificacaoDuplicidade', [
            'uses' => 'ImpugnacaoResultadoController@getVerificacaoDuplicidadePedido'
        ]);

        app()->router->get('{uf}', [
            'uses' => 'ImpugnacaoResultadoController@getPorUf'
        ]);

        app()->router->get('{id}/impugnacao', [
            'uses' => 'ImpugnacaoResultadoController@getImpugnacaoPorId'
        ]);

        app()->router->get('comissao/{idImpugnacao}/impugnacao', [
            'uses' => 'ImpugnacaoResultadoController@getImpugnacaoComVerificacaoComissaoPorId'
        ]);

        app()->router->get('chapa/{idImpugnacao}/impugnacao', [
            'uses' => 'ImpugnacaoResultadoController@getImpugnacaoComVerificacaoChapaPorId'
        ]);

        app()->router->get('impugnante/{idImpugnacao}/impugnacao', [
            'uses' => 'ImpugnacaoResultadoController@getImpugnacaoComVerificacaoImpugnantePorId'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Alegação do Pedido de Impugnação
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'alegacaoImpugnacaoResultado'], function () {

        app()->router->post('salvar', [
            'uses' => 'AlegacaoImpugnacaoResultadoController@salvar'
        ]);

        app()->router->get('{idImpugnacao}/validacao', [
            'uses' => 'AlegacaoImpugnacaoResultadoController@getValidacaoCadastroAlegacaoTO'
        ]);

        app()->router->get('{idImpugnacao}/alegacao', [
            'uses' => 'AlegacaoImpugnacaoResultadoController@getAlegacaoPorIdImpugnacao'
        ]);

        app()->router->get('documento/{idAlegacao}/download', [
            'uses' => 'AlegacaoImpugnacaoResultadoController@downloadDocumento'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento Alegação do Pedido de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'julgamentoAlegacaoImpugnacaoResultado'], function () {

        app()->router->get('{idImpugnacao}', [
            'uses' => 'JulgamentoAlegacaoImpugResultadoController@getJulgamentoAlegacaoPorImpugnacaoResultado'
        ]);

        app()->router->post('salvar', [
            'uses' => 'JulgamentoAlegacaoImpugResultadoController@salvar'
        ]);

        app()->router->get('documento/{idJulgamento}/download', [
            'uses' => 'JulgamentoAlegacaoImpugResultadoController@downloadDocumento'
        ]);
    });

    app()->router->get('/corrigirStatusMembro', function () {
        /** @var \App\Business\MembroChapaBO $bo */
        $bo = app(\App\Business\MembroChapaBO::class);
        $bo->corrigirStatusParticipacaoMembros(2911);
        $bo->corrigirStatusParticipacaoMembros(2912);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Contrarrazão do Recurso do Julgamento Alegação do Pedido de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'contrarrazoesImpugnacaoResultado'], function () {

        app()->router->post('salvar', [
            'uses' => 'ContrarrazaoRecursoImpugnacaoResultadoController@salvar'
        ]);

        app()->router->get('documento/{idContrarrazao}/download', [
            'uses' => 'ContrarrazaoRecursoImpugnacaoResultadoController@download'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Recurso do Julgamento do Pedido de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'recursoJulgamentoImpugnacaoResultado'], function () {

        app()->router->post('salvar', [
            'uses' => 'RecursoImpugnacaoResultadoController@salvar'
        ]);
        app()->router->get('{idImpugnacao}/recurso/{idTipoRecurso}', [
            'uses' => 'RecursoImpugnacaoResultadoController@getRecursoJulgamentoPorIdImpugnacao'
        ]);
        app()->router->get('documento/{idRecursoJulgamento}/download', [
            'uses' => 'RecursoImpugnacaoResultadoController@downloadDocumento'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Julgamento do Recurso de Impugnação de Resultado
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'julgamentosRecursosImpugResultado'], function () {

        app()->router->post('salvar', [
            'uses' => 'JulgamentoRecursoImpugResultadoController@salvar'
        ]);

        app()->router->get('impugnacaoResultado/{idImpugResultado}', [
            'uses' => 'JulgamentoRecursoImpugResultadoController@getPorImpugnacaoResultado'
        ]);

        app()->router->get('impugnacaoResultado/{idArquivo}/download', [
            'uses' => "JulgamentoRecursoImpugResultadoController@getArquivoJulgamentoRecursoPorId"
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Tipo Finalização de Mandato
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'tipoFinalizacaoMandato'], function () {
        app()->router->post('', [
            'uses' => 'TipoFinalizacaoMandatoController@store'
        ]);
        app()->router->get('{id}', [
            'uses' => 'TipoFinalizacaoMandatoController@show'
        ]);
        app()->router->put('{id}', [
            'uses' => 'TipoFinalizacaoMandatoController@update'
        ]);
        app()->router->post('filter', [
            'uses' => 'TipoFinalizacaoMandatoController@index'
        ]);
        app()->router->delete('{id}', [
            'uses' => 'TipoFinalizacaoMandatoController@delete'
        ]);
    });

     /*
     * |--------------------------------------------------------------------------
     * | Diploma eleitoral
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'diploma'], function () {
        app()->router->post('', [
            'uses' => 'DiplomaEleitoralController@store'
        ]);
        app()->router->get('{id}', [
            'uses' => 'DiplomaEleitoralController@show'
        ]);
        app()->router->put('{id}', [
            'uses' => 'DiplomaEleitoralController@update'
        ]);
    });

    /*
     * |--------------------------------------------------------------------------
     * | Diploma eleitoral
     * |--------------------------------------------------------------------------
     */
    app()->router->group(['prefix' => 'termo'], function () {
        app()->router->post('', [
            'uses' => 'TermoDePosseController@store'
        ]);
        app()->router->get('{id}', [
            'uses' => 'TermoDePosseController@show'
        ]);
        app()->router->put('{id}', [
            'uses' => 'TermoDePosseController@update'
        ]);
    });
});
