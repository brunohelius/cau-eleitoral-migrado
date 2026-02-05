<?php
/*
 * Auth.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */
namespace App\Security\Middleware;

use App\Config\Constants;
use App\Exceptions\Message;
use App\Security\AuthProviderInterface;
use App\Util\Utils;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Implementação de 'Middleware' responsável por interceptar as 'Requests' a recursos 'rest', verificando
 * se o 'client' está autenticado e possui permissões necessárias para continuar com a execução do recurso.
 *
 * @package App\Security\Middleware
 * @author Squadra Tecnologia
 */
class Auth
{

    private $auth;

    /**
     * Construtor da classe.
     *
     * @param AuthProviderInterface $auth
     */
    public function __construct(AuthProviderInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Verifica se o Usuário responsável pela requisição ao recurso 'rest' está Autenticação e possui permissão de
     * acesso.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $roles
     * @return null
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $response = null;
        $token = $request->header(Constants::PARAM_AUTHORIZATION);

        if (empty($token)) {
            $response = response(Response::$statusTexts[Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
        } else {

            if (Utils::startsWith($token, Constants::PARAM_BEARER)) {
                $token = str_replace(Constants::PARAM_BEARER, '', $token);
                $token = trim($token);

                if ($this->auth->isTokenValid($token)) {

                    if ($roles != null && ! $this->auth->hasRoles($token, $roles)) {
                        $response = response(Response::$statusTexts[Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
                    }
                } else {
                    $response = response(Response::$statusTexts[Response::HTTP_FORBIDDEN], Response::HTTP_FORBIDDEN);
                }
            } else {
                $response = response(Message::$descriptions[Message::TOKEN_INVALIDO], Response::HTTP_FORBIDDEN);
            }
        }

        if ($response == null) {
            $response = $next($request);
        }

        return $response;
    }
}
