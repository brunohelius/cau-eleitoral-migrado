<?php
/*
 * Controller.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Http\Controllers;

use App\Config\Constants;
use App\Exceptions\NegocioException;
use App\Security\Token\TokenContext;
use App\Security\Token\TokenUtils;
use App\Service\AuthService;
use App\To\ArquivoTO;
use App\Util\JsonUtils;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use stdClass;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 *
 */

class Controller extends BaseController
{
    /**
     * Retorna o 'Objeto' serializado em 'json'.
     *
     * @param object $object
     * @return string
     */
    protected function toJson($object)
    {
        $response = response(JsonUtils::toJson($object));
        $response->header('Content-Type', 'application/json');
        $response->header('charset', 'utf-8');
        return $response;
    }

    /**
     * Retorna o 'arquivo' na 'response' considerando as suas propriedades.
     *
     * @param ArquivoTO $arquivoTO
     * @return Response
     */
    protected function toFile(ArquivoTO $arquivoTO)
    {
        $response = response($arquivoTO->file, 200);
        $response->header('Content-Type', $arquivoTO->type);
        $response->header('Content-disposition', 'attachment; filename="' . $arquivoTO->name . '"');
        return $response;
    }

    /**
     * Retorna a response 'OK'.
     *
     * @return Response
     */
    protected function ok()
    {
        $response = response()->make('');
        $response->setStatusCode(200);
        $response->setContent('OK');
        return $response;
    }

    /**
     * Retorna a response 'OK'.
     *
     * @param ArquivoTO $arquivoTO
     * @return Response
     */
    protected function jsonContents(ArquivoTO $arquivoTO)
    {
        $response = response($arquivoTO->file, 200);
        $response->header('Content-Type', 'application/json');
        $response->header('charset', 'utf-8');
        return $response;
    }

    /**
     * Retorna a instância do 'Usuário Logado'.
     *
     * @param Request $request
     * @return mixed
     * @throws NegocioException
     */
    protected function getUsuarioLogado(Request $request)
    {
        $token = $this->getAppToken($request);

        /** @var AuthService $authService */
        $authService = app()->make(AuthService::class);
        return $authService->getAuthenticatedUserByToken($token);
    }

    /**
     * Retorna o 'Token' de autorização recuperado da 'Request'.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getAppToken(Request $request)
    {
        $appToken = $request->header(Constants::PARAM_AUTHORIZATION);

        if (!empty($appToken)) {
            $appToken = str_replace(Constants::PARAM_BEARER, '', $appToken);
            $appToken = trim($appToken);
        }
        return $appToken;
    }

    /**
     * Retorna a instância de 'TokenContext'.
     *
     * @return TokenContext
     */
    private function getTokenContext()
    {
        return app()->make(TokenContext::class);
    }
}
