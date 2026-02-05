<?php
/*
 * AuthService.php
 * Copyright (c) CAU/BR.
 *
 * Este software é confidencial e propriedade da CAU/BR.
 * Não é permitida sua distribuição ou divulgação do seu conteúdo sem expressa autorização da CAU/BR.
 * Este arquivo contém informações proprietárias.
 */

namespace App\Service;

use App\Config\AppConfig;
use App\Exceptions\Message;
use App\Exceptions\NegocioException;
use App\Security\Token\TokenContext;

/**
 * Classe de serviço responsável pela autenticação do 'Usuario'.
 *
 * @package App\Service
 * @author Squadra Tecnologia S/A.
 */
class AuthService extends AbstractService
{
    /**
     * @var \App\Security\Token\TokenContext
     */
    private $tokenContext;

    /**
     * Construtor da classe.
     */
    public function __construct()
    {
        $this->tokenContext = app()->make(TokenContext::class);
    }

    /**
     * Autentica o 'Usuário' conforme a 'chaveAcesso' informada.
     *
     * @return array
     */
    public function autenticar()
    {
        return $this->getCredential([]);
    }

    /**
     * @param array $credencial
     * @return array
     */
    private function getCredential(array $credencial): array
    {
        $builder = $this->tokenContext->createTokenBuilder($credencial);
        $token = $builder->encode();

        $credencial['expiresIn'] = $token['expiresIn'];
        $credencial['accessToken'] = $token['accessToken'];
        $credencial['refreshToken'] = $token['accessToken'];

        return $credencial;
    }

    /**
     * Retorna o usuário pelo token informado.
     *
     * @param $token
     * @return mixed
     * @throws NegocioException
     */
    public function getAuthenticatedUserByToken($token)
    {
        if (empty($token)) {
            throw new NegocioException(Message::VALIDACAO_CAMPOS_OBRIGATORIOS);
        }

        $url = AppConfig::getUrlAcesso('api/auth/user');

        if (str_contains($url, '\\')) {
            $url = str_replace('\\', '/', $url);
        }

        $authUser = json_decode($this->getRestClient()->sendPost($url, ['token' => $token]));

        if (!empty($authUser->error)) {
            throw new NegocioException($authUser->error);
        }

        return $authUser;
    }
}
