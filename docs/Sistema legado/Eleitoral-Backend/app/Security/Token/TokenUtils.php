<?php

namespace App\Security\Token;

use App\Config\Constants;
use Illuminate\Http\Request;
use stdClass;

/**
 * Utilitário de manipulação de tokens.
 *
 * @author Squadra Tecnologia S/A.
 */
class TokenUtils
{

    /**
     * Retorna o 'Token' de autorização recuperado da 'Request'.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getAppToken(Request $request)
    {
        $appToken = $request->header(Constants::PARAM_AUTHORIZATION);

        if (!empty($appToken)) {
            $appToken = str_replace(Constants::PARAM_BEARER, '', $appToken);
            $appToken = trim($appToken);
        }

        return $appToken;
    }

    /**
     * Retorna os parâmetros recuperados do Token JWT.
     *
     * @param string $appToken
     * @return null|stdClass
     */
    public static function getTokenParams($appToken)
    {
        $params = null;
        $tokenBuilder = TokenUtils::getTokenContext()->createTokenBuilder();

        if ($tokenBuilder->isValid($appToken)) {
            $params = $tokenBuilder->decode($appToken)->data;
        }
        return $params;
    }

    /**
     * Retorna a instância de 'TokenContext'.
     *
     * @return TokenContext
     */
    private static function getTokenContext()
    {
        return app()->make(TokenContext::class);
    }
}
