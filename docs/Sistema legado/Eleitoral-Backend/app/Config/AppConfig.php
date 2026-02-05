<?php
/*
 * AppConfig.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Config;

use App\Service\ArquivoService;

/**
 * Classe para centralizar configurações necessárias para aplicação.
 *
 * @package App\Config
 * @author Squadra Tecnologia
 */
class AppConfig
{

    /**
     * Construtor privado para garantir o singleto.
     */
    private function __construct()
    {
    }

    /**
     * Retorna o aplicação está no ambiente vigente.
     *
     * @return string
     */
    public static function getEnv()
    {
        return getenv('APP_ENV');
    }

    /**
     * Verifica se a aplicação está no ambiente de 'Produção'.
     *
     * @return boolean
     */
    public static function isEnvPrd()
    {
        return AppConfig::getEnv() == Constants::PRD;
    }

    /**
     * Verifica se a aplicação está no ambiente de 'Local'.
     *
     * @return boolean
     */
    public static function isEnvTesteCAU()
    {
        return (AppConfig::getEnv() == Constants::TEST || AppConfig::getEnv() == Constants::TEST1
            || AppConfig::getEnv() == Constants::TEST2 || AppConfig::getEnv() == Constants::TEST3
            || AppConfig::getEnv() == Constants::HMG1);
    }

    /**
     * Verifica se a aplicação está no ambiente de 'Local'.
     *
     * @return boolean
     */
    public static function isEnvHmg()
    {
        return AppConfig::getEnv() == Constants::HMG;
    }

    /**
     * Verifica se a aplicação está no ambiente de 'Desenvolvimento'.
     *
     * @return boolean
     */
    public static function isEnvDev()
    {
        return AppConfig::getEnv() == Constants::DEV;
    }

    /**
     * Retorna o caminho do repositório considerando o 'directory' informado.
     * Caso o parâmetro 'filename' seja infromado será considerando no retorno.
     *
     * @param string $directory
     * @param string $filename
     * @return string
     */
    public static function getRepositorio($directory = null, $filename = null)
    {
        $path = getenv('SICCAU_STORAGE_PATH');
        $separator = substr($path, -1);

        if ($separator !== '/' && $separator !== '\\') {
            $path .= DIRECTORY_SEPARATOR;
        }

        if ($directory != null) {
            $path .= $directory . DIRECTORY_SEPARATOR;
            ArquivoService::criarDiretorio($path);
        }

        if ($filename != null) {
            $path .= $filename;
        }

        return $path;
    }

    /**
     * Verifica se a aplicação está no ambiente de 'Desenvolvimento'.
     *
     * @return string
     */
    public static function getUrlServicoComplementoProfissionais()
    {
        return getenv('URL_SERVICO_COMPLEMENTO_PROFISSIONAIS');
    }

    /**
     * Retorna URL ACESSO
     *
     * @param string $relativeUrl
     * @return string
     */
    public static function getUrlAcesso(string $relativeUrl = ""): string
    {
        return env('URL_ACESSO') . DIRECTORY_SEPARATOR . $relativeUrl;
    }

    /**
     * Retorna URL do sistema Plataforma.
     *
     * @param string $relativeUrl
     * @return string
     */
    public static function getUrlPlataforma(string $relativeUrl = ""): string
    {
        return env('URL_PLATAFORMA') . DIRECTORY_SEPARATOR . $relativeUrl;
    }

    /**
     * Retorna o arquivo conforme o nome e diretório informados.
     *
     * @param $nome
     * @param $diretorio
     * @return string
     */
    private static function getArquivo($nome, $diretorio)
    {
        $arquivo = null;

        if (!empty($nome) && !empty($diretorio)) {
            $path = getenv($diretorio);
            $separator = substr($path, -1);

            if ($separator !== '/' && $separator !== '\\') {
                $path .= DIRECTORY_SEPARATOR;
            }
            $arquivo = $path . $nome;
        }

        return $arquivo;
    }

    /**
     * Retorna os destinatários pré-configurados que irão receber os emails nos ambientes 'Local',
     * 'desenvolvimento' e 'homologação'.
     *
     * @return array
     */
    public static function getEmailsDestinariosTeste()
    {
        $emails = getenv('CAU_EMAIL_DESTINATARIOS_TESTE');
        return explode(";", $emails);
    }

    /**
     * Retorna o template de e-mail conforme o 'nome' informado.
     *
     * @param string $nome
     * @return string
     */
    public static function getTemplateEmail($nome)
    {
        $html = '';

        if (!empty($nome)) {
            $path = base_path(Constants::PATH_RESOURCES_EMAILS . '/' . $nome);
            $html = file_get_contents($path);
        }

        return $html;
    }

    /**
     * Retorna a url que sera utilizada para consumir a api do calendario.com.br
     * @return string
     */
    public static function getCalendarioUrlApi() {
        return getenv('CALENDARIO_URL_API');
    }

    /**
     * Retorna a token que sera utilizada para consumir a api do calendario.com.br
     * @return string
     */
    public static function getCalendarioTokenApi() {
        return getenv('CALENDARIO_TOKEN_API');
    }

    /**
     * Retorna o middleware de rota.
     *
     * @param string $middleware
     * @return array
     */
    public static function getMiddleware(string $middleware = 'default'): array
    {
        $middlewares = [
            'default' => ['middleware' => ['auth:usuarios|pessoas']],
            'usuarios' => ['middleware' => 'auth:usuarios'],
            'pessoas' => ['middleware' => 'auth:pessoas'],
        ];

        //return ConfigService::isEnvLocal() ? [] : $middlewares[$middleware];
        return $middlewares[$middleware];
    }
}
